<?php

declare(strict_types=1);

namespace PHGraph\MinimumSpanningTree;

use PHGraph\Contracts\MinimumSpanningTree;
use PHGraph\Graph;
use PHGraph\Vertex;
use RuntimeException;
use SplObjectStorage;
use SplPriorityQueue;
use UnderflowException;
use UnexpectedValueException;

/**
 * Prim’s minimum spanning tree algorithm.
 *
 * @see https://en.wikipedia.org/wiki/Prim%27s_algorithm
 */
final class Prim implements MinimumSpanningTree
{
    private Graph $graph;
    private Vertex $start_vertex;

    /**
     * instantiate new algorithm.
     *
     * @param \PHGraph\Graph $graph Graph to operate on
     *
     * @throws UnexpectedValueException if graph has directed edges
     * @throws UnderflowException if graph is empty
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

        if (count($vertices) === 0) {
            throw new UnderflowException('Graph is empty');
        }

        $this->start_vertex = $vertices[array_rand($vertices)];
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
        $marked = new SplObjectStorage();

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
                } catch (RuntimeException $exception) {
                    throw new UnexpectedValueException('Graph has more than one component', 0, $exception);
                }
            } while (!($marked->contains($cheapest_edge->getFrom()) xor $marked->contains($cheapest_edge->getTo())));

            $edges[] = $cheapest_edge;

            if ($marked->contains($cheapest_edge->getFrom())) {
                $vertex_current = $cheapest_edge->getTo();
            } else {
                $vertex_current = $cheapest_edge->getFrom();
            }
        }

        return $edges;
    }
}
