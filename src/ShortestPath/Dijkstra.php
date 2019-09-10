<?php

namespace PHGraph\ShortestPath;

use OutOfBoundsException;
use PHGraph\Contracts\ShortestPath;
use PHGraph\Graph;
use PHGraph\Support\EdgeCollection;
use PHGraph\Support\VertexCollection;
use PHGraph\Vertex;
use PHGraph\Walk;
use SplPriorityQueue;
use UnexpectedValueException;

/**
 * For a given source node in the graph, the algorithm finds the shortest path
 * between that node and every other. This should be considered immutable on the
 * graph as we will be caching edges when getEdges is called.
 */
class Dijkstra implements ShortestPath
{
    /** @var \PHGraph\Vertex */
    protected $vertex;
    /** @var \PHGraph\Support\EdgeCollection */
    protected $edges;

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
     * @throws OutOfBoundsException if there's no path to the given end vertex
     *
     * @return \PHGraph\Walk
     */
    public function getWalkTo(Vertex $vertex): Walk
    {
        return new Walk($this->vertex, $this->getEdgesTo($vertex));
    }

    /**
     * checks whether there's a path from this start vertex to given end vertex.
     *
     * @param \PHGraph\Vertex $vertex
     *
     * @return bool
     */
    public function hasVertex(Vertex $vertex): bool
    {
        try {
            $this->getEdgesTo($vertex);
        } catch (OutOfBoundsException $e) {
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
     * @throws OutOfBoundsException if there's no path to given end vertex
     *
     * @return float
     */
    public function getDistance(Vertex $vertex): float
    {
        return $this->getEdgesTo($vertex)->sumAttribute('weight');
    }

    /**
     * get a map of all vertices to.
     *
     * @param \PHGraph\Vertex $vertex vertex we are walking to
     *
     * @throws OutOfBoundsException
     *
     * @return \PHGraph\Support\EdgeCollection
     */
    public function getEdgesTo(Vertex $vertex): EdgeCollection
    {
        $current_vertex = $vertex;
        $path = new EdgeCollection;

        if ($vertex === $this->vertex) {
            return $path;
        }

        $edges = $this->getEdges();

        do {
            $pre = null;

            foreach ($edges as $edge) {
                if ($path->contains($edge)) {
                    continue;
                }

                if (!$edge->isDirected() && $edge->getFrom() === $current_vertex) {
                    $path->add($edge);
                    $pre = $edge->getTo();

                    break;
                }

                if ($edge->getTo() === $current_vertex) {
                    $path->add($edge);
                    $pre = $edge->getFrom();

                    break;
                }
            }

            if ($pre === null) {
                throw new OutOfBoundsException('No edge leading to vertex');
            }

            $current_vertex = $pre;
        } while ($current_vertex !== $this->vertex);

        return $path->reverse();
    }

    /**
     * get map of vertex IDs to distance.
     *
     * @return array
     */
    public function getDistanceMap(): array
    {
        $ret = [];
        foreach ($this->vertex->getGraph()->getVertices() as $vertex) {
            try {
                $ret[$vertex->getId()] = $this->getEdgesTo($vertex)->sumAttribute('weight');
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
     * @return \PHGraph\Support\EdgeCollection
     */
    public function getEdges(): EdgeCollection
    {
        if ($this->edges !== null) {
            return $this->edges;
        }

        $vertices = $this->vertex->getGraph()->getVertices();

        $cost_to = [
            $this->vertex->getId() => INF,
        ];

        $vertex_queue = new SplPriorityQueue;
        $vertex_queue->insert($this->vertex, 1);

        $lowest_cost_vertex_to = [
            $this->vertex->getId() => $this->vertex,
        ];

        $used_vertices = new VertexCollection;

        for ($i = 0; $i < count($vertices); $i++) {
            if ($vertex_queue->isEmpty()) {
                break;
            }

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

            $used_vertices->add($current_vertex);
        }

        if ($cost_to[$this->vertex->getId()] === INF) {
            unset($lowest_cost_vertex_to[$this->vertex->getId()]);
        }

        $edges = new EdgeCollection;
        foreach ($vertices as $vid => $vertex) {
            if ($lowest_cost_vertex_to[$vid] ?? false) {
                $closest_edge = $lowest_cost_vertex_to[$vid]->getEdgesOut()->filter(function ($edge) use ($vertex) {
                    return $edge->getTo() === $vertex || (!$edge->isDirected() && $edge->getFrom() === $vertex);
                })->sortBy(function ($edge) {
                    return $edge->getAttribute('weight', 0);
                })->first();

                $edges[] = $closest_edge;
            }
        }

        $this->edges = $edges;

        return $this->edges;
    }
}
