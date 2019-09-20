<?php

namespace PHGraph\TravelingSalesman;

use PHGraph\Contracts\TravelingSalesman;
use PHGraph\Graph;
use PHGraph\Support\EdgeCollection;
use PHGraph\Support\VertexCollection;
use PHGraph\Vertex;
use RuntimeException;
use SplPriorityQueue;
use UnexpectedValueException;

/**
 * Nearest neighbor traveling salesman problem algorithm.
 *
 * @see https://en.wikipedia.org/wiki/Nearest_neighbour_algorithm
 */
class NearestNeighbor implements TravelingSalesman
{
    /** @var \PHGraph\Graph */
    protected $graph;
    /** @var \PHGraph\Vertex */
    protected $start_vertex;

    /**
     * instantiate new algorithm. A starting vertex is chosen at random per the
     * algorithm, but this may be overriden.
     *
     * @param \PHGraph\Graph $graph Graph to operate on
     *
     * @throws UnexpectedValueException if graph has directed edges
     *
     * @return void
     */
    public function __construct(Graph $graph)
    {
        $this->graph = $graph;
        $this->start_vertex = $graph->getVertices()->random();
    }

    /**
     * Set a particular starting vertex for the algorithm.
     *
     * @param \PHGraph\Vertex $vertex starting vertex
     *
     * @return void
     */
    public function setStartVertex(Vertex $vertex): void
    {
        $this->start_vertex = $vertex;
    }

    /**
     * create new resulting graph with only edges in the path.
     *
     * @throws UnexpectedValueException if the Graph is not connected
     *
     * @return \PHGraph\Graph
     */
    public function createGraph(): Graph
    {
        return $this->graph->newFromEdges($this->getEdges());
    }

    /**
     * Get all the edges in the path.
     *
     * @throws UnexpectedValueException if the Graph is not connected
     *
     * @return \PHGraph\Support\EdgeCollection
     */
    public function getEdges(): EdgeCollection
    {
        if ($this->start_vertex === null) {
            throw new UnexpectedValueException('Graph is empty');
        }

        $edges = new EdgeCollection();

        $vertex_current = $this->start_vertex;
        $marked = new VertexCollection();

        $itterations = $this->graph->getVertices()->count() - 1;

        for ($i = 0; $i < $itterations; $i++) {
            $marked->add($vertex_current);

            $edge_queue = new SplPriorityQueue();

            /** @var \PHGraph\Edge $edge */
            foreach ($vertex_current->getEdgesOut() as $edge) {
                if (!$edge->isLoop()) {
                    $edge_queue->insert($edge, -$edge->getAttribute('weight', 0));
                }
            }

            do {
                try {
                    /** @var \PHGraph\Edge $cheapest_edge */
                    $cheapest_edge = $edge_queue->extract();
                } catch (RuntimeException $e) {
                    throw new UnexpectedValueException('Graph has more than one component', 0, $e);
                }
            } while ($marked->contains($cheapest_edge->getFrom()) && $marked->contains($cheapest_edge->getTo()));

            $edges[] = $cheapest_edge;

            if ($marked->contains($cheapest_edge->getFrom())) {
                $vertex_current = $cheapest_edge->getTo();
            } else {
                $vertex_current = $cheapest_edge->getFrom();
            }
        }

        // try to connect back to start vertex
        if ($vertex_current->getVertices()->contains($this->start_vertex)) {
            $edge_queue = new SplPriorityQueue();
            /** @var \PHGraph\Edge $edge */
            foreach ($vertex_current->getEdgesOut() as $edge) {
                if (!$edge->isLoop() && !$edges->contains($edge)) {
                    $edge_queue->insert($edge, -$edge->getAttribute('weight', 0));
                }
            }

            do {
                /** @var \PHGraph\Edge $cheapest_edge */
                $cheapest_edge = $edge_queue->extract();
            } while (!$cheapest_edge->getVertices()->contains($this->start_vertex));

            $edges[] = $cheapest_edge;
        }

        if ($edges->count() !== (count($this->graph->getVertices()))) {
            throw new UnexpectedValueException('Graph is not connected');
        }

        return $edges;
    }
}
