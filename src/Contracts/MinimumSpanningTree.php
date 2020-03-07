<?php

namespace PHGraph\Contracts;

use PHGraph\Graph;
use PHGraph\Support\EdgeCollection;

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
     * @return \PHGraph\Support\EdgeCollection<\PHGraph\Edge>
     */
    public function getEdges(): EdgeCollection;
}
