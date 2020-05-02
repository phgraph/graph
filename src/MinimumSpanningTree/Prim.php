<?php

namespace PHGraph\MinimumSpanningTree;

use Exception;
use PHGraph\Contracts\MinimumSpanningTree;
use PHGraph\Graph;
use RuntimeException;
use SplObjectStorage;
use SplPriorityQueue;
use UnexpectedValueException;

/**
 * Prim’s minimum spanning tree algorithm.
 *
 * @see https://en.wikipedia.org/wiki/Prim%27s_algorithm
 */
class Prim implements MinimumSpanningTree
{
    /** @var \PHGraph\Graph */
    protected $graph;
    /** @var \PHGraph\Vertex */
    protected $start_vertex;

    /**
     * instantiate new algorithm.
     *
     * @param \PHGraph\Graph $graph Graph to operate on
     *
     * @throws UnexpectedValueException if graph has directed edges
     *
     * @return void
     */
    public function __construct(Graph $graph)
    {
        if ($graph->hasDirected()) {
            throw new UnexpectedValueException('Cannot create MST for directed graph');
        }

        $this->graph = $graph;
        $vertices = $graph->getVertices();
        $this->start_vertex = count($vertices)
            ? $vertices[array_rand($vertices)]
            : null;
    }

    /**
     * create new resulting graph with only edges in the minimum spanning tree.
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
     * Get all the edges in the minimum spanning tree.
     *
     * @throws UnexpectedValueException if the Graph is not connected
     *
     * @return \PHGraph\Edge[]
     */
    public function getEdges(): array
    {
        $edge_queue = new SplPriorityQueue();
        $edges = [];

        $vertex_current = $this->start_vertex;
        $marked = new SplObjectStorage;

        $itterations = count($this->graph->getVertices()) - 1;

        for ($i = 0; $i < $itterations; $i++) {
            $marked->attach($vertex_current);

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
            } while (!($marked->contains($cheapest_edge->getFrom()) xor $marked->contains($cheapest_edge->getTo())));

            $edges[] = $cheapest_edge;

            if ($marked->contains($cheapest_edge->getFrom())) {
                $vertex_current = $cheapest_edge->getTo();
            } else {
                $vertex_current = $cheapest_edge->getFrom();
            }
        }

        if (count($edges) !== $itterations) {
            throw new UnexpectedValueException('Graph is not connected');
        }

        return $edges;
    }
}
