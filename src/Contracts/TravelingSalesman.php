<?php

namespace PHGraph\Contracts;

use PHGraph\Graph;
use PHGraph\Support\EdgeCollection;

interface TravelingSalesman
{
    /**
     * create new resulting graph with only edges in path.
     *
     * @return \PHGraph\Graph
     */
    public function createGraph(): Graph;

    /**
     * Get all the edges in the path.
     *
     * @return \PHGraph\Support\EdgeCollection
     */
    public function getEdges(): EdgeCollection;
}
