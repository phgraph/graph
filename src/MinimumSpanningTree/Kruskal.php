<?php

namespace PHGraph\MinimumSpanningTree;

use PHGraph\Contracts\MinimumSpanningTree;
use PHGraph\Graph;
use PHGraph\Support\EdgeCollection;
use PHGraph\Support\VertexCollection;
use SplPriorityQueue;
use UnexpectedValueException;

/**
 * Kruskal’s minimum spanning tree algorithm.
 *
 * @see https://en.wikipedia.org/wiki/Kruskal%27s_algorithm
 */
class Kruskal implements MinimumSpanningTree
{
    /** @var \PHGraph\Graph */
    protected $graph;

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
     * @return \PHGraph\Support\EdgeCollection<\PHGraph\Edge>
     */
    public function getEdges(): EdgeCollection
    {
        $edge_queue = new SplPriorityQueue();

        /** @var \PHGraph\Edge $edge */
        foreach ($this->graph->getEdges() as $edge) {
            if (!$edge->isLoop()) {
                $edge_queue->insert($edge, -$edge->getAttribute('weight', 0));
            }
        }

        $edges = new EdgeCollection();
        /** @var \PHGraph\Support\VertexCollection[] $forests */
        $forests = [];

        while ($edge_queue->count()) {
            /** @var \PHGraph\Edge $edge */
            $edge = $edge_queue->extract();

            $use_forest = null;
            foreach ($forests as $key => $marked) {
                $from = $edge->getFrom();
                $to = $edge->getTo();
                if ($marked->contains($from) && $marked->contains($to)) {
                    continue 2;
                }
                if ($marked->contains($from) || $marked->contains($to)) {
                    foreach ($forests as $merge_key => $forest) {
                        if (
                            $marked !== $forest
                            && ($forest->contains($from) || $forest->contains($to))
                        ) {
                            unset($forests[$merge_key]);
                            $forests[$key] = $marked->merge($forest);
                            break;
                        }
                    }
                    $use_forest = $forests[$key];
                    break;
                }
            }
            if ($use_forest === null) {
                $use_forest = new VertexCollection();
                $forests[] = $use_forest;
            }

            $edges[] = $edge;
            $use_forest->add($edge->getFrom());
            $use_forest->add($edge->getTo());
        }

        if ($edges->count() !== (count($this->graph->getVertices()) - 1)) {
            throw new UnexpectedValueException('Graph is not connected');
        }

        return $edges;
    }
}
