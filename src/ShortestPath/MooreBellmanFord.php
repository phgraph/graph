<?php

declare(strict_types=1);

namespace PHGraph\ShortestPath;

use OutOfBoundsException;
use PHGraph\Contracts\ShortestPath;
use PHGraph\Exception\NegativeCycle;
use PHGraph\Graph;
use PHGraph\Vertex;
use PHGraph\Walk;
use SplObjectStorage;
use UnderflowException;

/**
 * For a given source node in the graph, the algorithm finds the shortest path
 * between that node and every other. This should be considered immutable on the
 * graph as we will be caching edges when getEdges is called.
 *
 * @see https://en.wikipedia.org/wiki/Bellman%E2%80%93Ford_algorithm
 */
final class MooreBellmanFord implements ShortestPath
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
        $current_vertex = $vertex;
        $path = [];

        if ($vertex === $this->vertex) {
            return $path;
        }

        $edges = $this->getEdges();

        do {
            $pre = null;

            foreach ($edges as $edge) {
                if (!$edge->isDirected() && $edge->getFrom() === $current_vertex) {
                    $path[$edge->getId()] = $edge;
                    $pre = $edge->getTo();

                    break;
                }

                if ($edge->getTo() === $current_vertex) {
                    $path[$edge->getId()] = $edge;
                    $pre = $edge->getFrom();

                    break;
                }
            }

            if ($pre === null) {
                throw new OutOfBoundsException('No edge leading to vertex');
            }

            $current_vertex = $pre;
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
     * @throws \PHGraph\Exception\NegativeCycle
     *
     * @return \PHGraph\Edge[]
     */
    public function getEdges(): array
    {
        if (isset($this->edges)) {
            return $this->edges;
        }

        $vertices = $this->vertex->getGraph()->getVertices();
        $edges = $this->vertex->getGraph()->getEdges();
        $change_vertex = null;

        $cost_to = [
            $this->vertex->getId() => INF,
        ];

        $lowest_cost_vertex_to = [
            $this->vertex->getId() => $this->vertex,
        ];

        $vertex_count = count($vertices);
        for ($i = 0; $i < $vertex_count; $i++) {
            $change_vertex = null;

            foreach ($edges as $edge) {
                foreach ($edge->getTargets() as $to_vertex) {
                    $from_vertex = $edge->getAdjacentVertex($to_vertex);

                    if (!isset($cost_to[$from_vertex->getId()])) {
                        continue;
                    }

                    $new_cost = $cost_to[$from_vertex->getId()] + $edge->getAttribute('weight', 0);
                    if ($new_cost === INF) {
                        $new_cost = $edge->getAttribute('weight', 0);
                    }

                    if (!isset($cost_to[$to_vertex->getId()]) || $cost_to[$to_vertex->getId()] > $new_cost) {
                        $change_vertex = $to_vertex;
                        $cost_to[$to_vertex->getId()] = $new_cost;
                        $lowest_cost_vertex_to[$to_vertex->getId()] = $from_vertex;
                    } else {
                        // not changed
                        break;
                    }
                }
            }
        }

        if ($cost_to[$this->vertex->getId()] === INF) {
            unset($lowest_cost_vertex_to[$this->vertex->getId()]);
        }

        $edges = [];
        foreach ($vertices as $vid => $vertex) {
            if (isset($lowest_cost_vertex_to[$vid])) {
                /** @var \PHGraph\Edge[] $closest_edges */
                $closest_edges = array_filter(
                    $lowest_cost_vertex_to[$vid]->getEdgesOut(),
                    static function ($edge) use ($vertex) {
                        return $edge->getTo() === $vertex || (!$edge->isDirected() && $edge->getFrom() === $vertex);
                    }
                );

                if (count($closest_edges) === 0) {
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

        // if a change vertex is set we have a negative cycle.
        if ($change_vertex) {
            foreach ($edges as $edge) {
                foreach ($edge->getTargets() as $to_vertex) {
                    $from_vertex = $edge->getAdjacentVertex($to_vertex);

                    $new_cost = $cost_to[$from_vertex->getId()] + $edge->getAttribute('weight', 0);

                    if (!isset($cost_to[$to_vertex->getId()]) || $cost_to[$to_vertex->getId()] > $new_cost) {
                        $cost_to[$to_vertex->getId()] = $new_cost;
                        $lowest_cost_vertex_to[$to_vertex->getId()] = $from_vertex;
                    }
                }
            }

            $edges = [];
            $cycle_vertices = new SplObjectStorage();
            $current_vertex = $change_vertex;
            do {
                $predecessor_vertex = $lowest_cost_vertex_to[$current_vertex->getId()];

                /** @var \PHGraph\Edge[] $closest_edges */
                $closest_edges = array_filter(
                    $current_vertex->getEdgesIn(),
                    static function ($edge) use ($predecessor_vertex) {
                        return $edge->getFrom() === $predecessor_vertex
                            || (!$edge->isDirected() && $edge->getTo() === $predecessor_vertex);
                    }
                );

                if (count($closest_edges) === 0) {
                    // @codeCoverageIgnoreStart
                    continue;
                    // @codeCoverageIgnoreEnd
                }

                uasort($closest_edges, static function ($left, $right) {
                    return $left->getAttribute('weight', 0) - $right->getAttribute('weight', 0);
                });

                $closest_edge = end($closest_edges);

                $edges[$closest_edge->getId()] = $closest_edge;
                $cycle_vertices->attach($current_vertex);
                $current_vertex = $predecessor_vertex;
            } while ($change_vertex !== $current_vertex && !$cycle_vertices->contains($current_vertex));

            $cycle = new Walk($change_vertex, array_reverse($edges));
            throw new NegativeCycle('Negative cycle found', 0, null, $cycle);
        }

        $this->edges = $edges;

        return $this->edges;
    }

    /**
     * get negative cycle.
     *
     * @throws UnderflowException if there’s no negative cycle
     *
     * @return \PHGraph\Walk
     */
    public function getCycleNegative(): Walk
    {
        try {
            $this->getEdges();
        } catch (NegativeCycle $exception) {
            return $exception->getCycle();
        }

        throw new UnderflowException('No cycle found');
    }
}
