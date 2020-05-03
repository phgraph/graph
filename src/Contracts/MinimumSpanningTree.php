<?php

namespace PHGraph\Contracts;

use PHGraph\Graph;

interface MinimumSpanningTree
{
    /**
     * create new resulting graph with only edges in the minimum spanning tree.
     *
     * @return \PHGraph\Graph
     */
    public function createGraph(): Graph;

    /**
     * Get all the edges in the minimum spanning tree.
     *
     * @return \PHGraph\Edge[]
     */
    public function getEdges(): array;
}
