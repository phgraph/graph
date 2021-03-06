<?php

declare(strict_types=1);

namespace PHGraph\ShortestPath;

use OutOfBoundsException;
use PHGraph\Contracts\ShortestPath;
use PHGraph\Graph;
use PHGraph\Vertex;
use PHGraph\Walk;

/**
 * Breadth first (least hops) shortest path algorithm.
 *
 * @see https://en.wikipedia.org/wiki/Best-first_search
 */
final class BreadthFirst implements ShortestPath
{
    private Vertex $vertex;

    /**
     * instantiate new algorithm.
     *
     * @param Vertex $vertex Vertex to operate on
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
        return count($this->getEdgesTo($vertex));
    }

    /**
     * get array of edges on the walk for each vertex (vertex ID => array of walk edges).
     *
     * @return array<string, \PHGraph\Edge[]>
     */
    public function getEdgesMap(): array
    {
        $vertex_queue = [];
        $vertex_current = $this->vertex;
        $edges = [
            $vertex_current->getId() => [],
        ];

        do {
            foreach ($vertex_current->getEdgesOut() as $edge) {
                $vertex_target = $edge->isDirected() ? $edge->getTo() : $edge->getAdjacentVertex($vertex_current);
                $vid = $vertex_target->getId();

                if (!isset($edges[$vid])) {
                    $vertex_queue[] = $vertex_target;
                    $edges[$vid] = array_merge($edges[$vertex_current->getId()], [$edge]);
                }
            }

            $vertex_current = array_shift($vertex_queue);
        } while ($vertex_current);

        return $edges;
    }

    /**
     * Get a path to the given vertex.
     *
     * @param \PHGraph\Vertex $vertex vertex we are walking to
     *
     * @throws OutOfBoundsException
     *
     * @return \PHGraph\Edge[]
     */
    public function getEdgesTo(Vertex $vertex): array
    {
        if ($vertex->getGraph() !== $this->vertex->getGraph()) {
            throw new OutOfBoundsException();
        }

        $map = $this->getEdgesMap();

        if (isset($map[$vertex->getId()])) {
            return $map[$vertex->getId()];
        }

        throw new OutOfBoundsException();
    }

    /**
     * get map of vertex IDs to distance.
     *
     * @return int[]
     */
    public function getDistanceMap(): array
    {
        return array_map(static function ($edges) {
            return count($edges);
        }, $this->getEdgesMap());
    }

    /**
     * Get all the edges that were mapped out.
     *
     * @return \PHGraph\Edge[]
     */
    public function getEdges(): array
    {
        $all_edges = [];

        foreach ($this->getEdgesMap() as $edges) {
            $all_edges = array_merge($all_edges, $edges);
        }

        return $all_edges;
    }
}
