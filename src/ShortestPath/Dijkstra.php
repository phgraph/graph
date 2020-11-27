<?php

declare(strict_types=1);

namespace PHGraph\ShortestPath;

use OutOfBoundsException;
use PHGraph\Contracts\ShortestPath;
use PHGraph\Edge;
use PHGraph\Graph;
use PHGraph\Vertex;
use PHGraph\Walk;
use SplObjectStorage;
use SplPriorityQueue;
use UnexpectedValueException;

/**
 * For a given source node in the graph, the algorithm finds the shortest path
 * between that node and every other. This should be considered immutable on the
 * graph as we will be caching edges when getEdges is called.
 *
 * @see https://en.wikipedia.org/wiki/Dijkstra%27s_algorithm
 */
final class Dijkstra implements ShortestPath
{
    private Vertex $vertex;
    /** @var \PHGraph\Edge[] */
    private array $edges;

    /**
     * instantiate new algorithm.
     *
     * @param \PHGraph\Vertex $vertex Vertex to operate on
     *
     * @return void
     */
    public function __construct(Vertex $vertex)
    {
        $this->vertex = $vertex;

        /** @var \PHGraph\Edge $edge */
        foreach ($this->vertex->getGraph()->getEdges() as $edge) {
            if ($edge->getAttribute('weight', 0) < 0) {
                throw new UnexpectedValueException('Djkstra not supported for negative weights');
            }
        }
    }

    /**
     * get walk (path) from start vertex to given end vertex.
     *
     * @param \PHGraph\Vertex $vertex
     *
     * @throws OutOfBoundsException if there’s no path to the given end vertex
     *
     * @return \PHGraph\Walk
     */
    public function getWalkTo(Vertex $vertex): Walk
    {
        return new Walk($this->vertex, $this->getEdgesTo($vertex));
    }

    /**
     * checks whether there’s a path from this start vertex to given end vertex.
     *
     * @param \PHGraph\Vertex $vertex
     *
     * @return bool
     */
    public function hasVertex(Vertex $vertex): bool
    {
        try {
            $this->getEdgesTo($vertex);
        } catch (OutOfBoundsException $exception) {
            return false;
        }

        return true;
    }

    /**
     * create new resulting graph with only edges on shortest path.
     *
     * @return \PHGraph\Graph
     */
    public function createGraph(): Graph
    {
        return $this->vertex->getGraph()->newFromEdges($this->getEdges());
    }

    /**
     * get distance between start vertex and given end vertex.
     *
     * @param \PHGraph\Vertex $vertex
     *
     * @throws OutOfBoundsException if there’s no path to given end vertex
     *
     * @return float
     */
    public function getDistance(Vertex $vertex): float
    {
        return array_sum(array_map(static function ($vertex) {
            return $vertex->getAttribute('weight', 0);
        }, $this->getEdgesTo($vertex)));
    }

    /**
     * get a map of all vertices to.
     *
     * @param \PHGraph\Vertex $vertex vertex we are walking to
     *
     * @throws OutOfBoundsException
     *
     * @return \PHGraph\Edge[]
     */
    public function getEdgesTo(Vertex $vertex): array
    {
        if ($vertex === $this->vertex) {
            return [];
        }

        $edges = $this->getEdges();
        /** @var \PHGraph\Edge[] $path */
        $path = [];
        $current_vertex = $vertex;

        do {
            $previous_vertex = null;

            foreach ($edges as $edge) {
                if (isset($path[$edge->getId()])) {
                    continue;
                }

                if (!$edge->isDirected() && $edge->getFrom() === $current_vertex) {
                    $path[$edge->getId()] = $edge;
                    $previous_vertex = $edge->getTo();

                    break;
                }

                if ($edge->getTo() === $current_vertex) {
                    $path[$edge->getId()] = $edge;
                    $previous_vertex = $edge->getFrom();

                    break;
                }
            }

            if ($previous_vertex === null) {
                throw new OutOfBoundsException('No edge leading to vertex');
            }

            $current_vertex = $previous_vertex;
        } while ($current_vertex !== $this->vertex);

        return array_reverse($path);
    }

    /**
     * get map of vertex IDs to distance.
     *
     * @return float[]
     */
    public function getDistanceMap(): array
    {
        $ret = [];
        foreach ($this->vertex->getGraph()->getVertices() as $vertex) {
            try {
                $ret[$vertex->getId()] = $this->getDistance($vertex);
            } catch (OutOfBoundsException $ignore) {
                // ignore vertices that can not be reached
            }
        }

        return $ret;
    }

    /**
     * Get cheapest edges (lowest weight) for given map.
     *
     * @throws UnexpectedValueException
     *
     * @return \PHGraph\Edge[]
     */
    public function getEdges(): array
    {
        if (isset($this->edges)) {
            return $this->edges;
        }

        $vertices = $this->vertex->getGraph()->getVertices();

        /** @var float[] $cost_to */
        $cost_to = [
            $this->vertex->getId() => INF,
        ];

        $vertex_queue = new SplPriorityQueue();
        $vertex_queue->insert($this->vertex, 1);

        $lowest_cost_vertex_to = [
            $this->vertex->getId() => $this->vertex,
        ];

        $used_vertices = new SplObjectStorage();
        $vertex_count = count($vertices);
        for ($i = 0; $i < $vertex_count; $i++) {
            if ($vertex_queue->isEmpty()) {
                break;
            }

            /** @var \PHGraph\Vertex $current_vertex */
            $current_vertex = $vertex_queue->extract();

            if ($used_vertices->contains($current_vertex)) {
                $i--;

                continue;
            }

            foreach ($current_vertex->getEdgesOut() as $edge) {
                $target_vertex = $edge->isDirected()
                    ? $edge->getTo()
                    : $edge->getAdjacentVertex($current_vertex);

                if ($used_vertices->contains($target_vertex)) {
                    continue;
                }

                /** @var float $weight */
                $weight = $edge->getAttribute('weight', 0);

                $target_vertex_cost = $cost_to[$current_vertex->getId()] + $weight;
                if ($target_vertex_cost === INF) {
                    $target_vertex_cost = $weight;
                }

                if (
                    !isset($lowest_cost_vertex_to[$target_vertex->getId()])
                    || $cost_to[$target_vertex->getId()] > $target_vertex_cost
                ) {
                    $vertex_queue->insert($target_vertex, -$target_vertex_cost);

                    $cost_to[$target_vertex->getId()] = $target_vertex_cost;

                    $lowest_cost_vertex_to[$target_vertex->getId()] = $current_vertex;
                }
            }

            $used_vertices->attach($current_vertex);
        }

        if ($cost_to[$this->vertex->getId()] === INF) {
            unset($lowest_cost_vertex_to[$this->vertex->getId()]);
        }

        /** @var \PHGraph\Edge[] $edges */
        $edges = [];
        foreach ($vertices as $vid => $vertex) {
            if (isset($lowest_cost_vertex_to[$vid])) {
                /** @var \PHGraph\Edge[] $closest_edges */
                $closest_edges = array_filter(
                    $lowest_cost_vertex_to[$vid]->getEdgesOut(),
                    static function (Edge $edge) use ($vertex) {
                        return $edge->getTo() === $vertex || (!$edge->isDirected() && $edge->getFrom() === $vertex);
                    }
                );

                if (count($closest_edges) === 0) {
                    // @todo determine if this can actually happen
                    // @codeCoverageIgnoreStart
                    continue;
                    // @codeCoverageIgnoreEnd
                }

                uasort($closest_edges, static function ($left, $right) {
                    return $left->getAttribute('weight', 0) - $right->getAttribute('weight', 0);
                });

                $closest_edge = end($closest_edges);

                $edges[$closest_edge->getId()] = $closest_edge;
            }
        }

        $this->edges = $edges;

        return $this->edges;
    }
}
