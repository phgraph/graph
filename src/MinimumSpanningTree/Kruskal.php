<?php

declare(strict_types=1);

namespace PHGraph\MinimumSpanningTree;

use PHGraph\Contracts\MinimumSpanningTree;
use PHGraph\Graph;
use SplObjectStorage;
use SplPriorityQueue;
use UnexpectedValueException;

/**
 * Kruskal’s minimum spanning tree algorithm.
 *
 * @see https://en.wikipedia.org/wiki/Kruskal%27s_algorithm
 */
final class Kruskal implements MinimumSpanningTree
{
    private Graph $graph;

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
     * @return \PHGraph\Edge[]
     */
    public function getEdges(): array
    {
        $edge_queue = new SplPriorityQueue();

        /** @var \PHGraph\Edge $edge */
        foreach ($this->graph->getEdges() as $edge) {
            if (!$edge->isLoop()) {
                $edge_queue->insert($edge, -$edge->getAttribute('weight', 0));
            }
        }

        $edges = [];
        /** @var SplObjectStorage[] $forests */
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
                if (!$marked->contains($from) && !$marked->contains($to)) {
                    continue;
                }
                foreach ($forests as $merge_key => $forest) {
                    if (
                        $marked !== $forest
                        && ($forest->contains($from) || $forest->contains($to))
                    ) {
                        $forests[$key]->addAll($forests[$merge_key]);
                        unset($forests[$merge_key]);
                        break;
                    }
                }
                $use_forest = $forests[$key];
                break;
            }
            if ($use_forest === null) {
                $use_forest = new SplObjectStorage();
                $forests[] = $use_forest;
            }

            $edges[$edge->getId()] = $edge;
            $use_forest->attach($edge->getFrom());
            $use_forest->attach($edge->getTo());
        }

        if (count($edges) !== count($this->graph->getVertices()) - 1) {
            throw new UnexpectedValueException('Graph is not connected');
        }

        return $edges;
    }
}
