<?php

declare(strict_types=1);

namespace PHGraph\Contracts;

use PHGraph\Graph;

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
     * @return \PHGraph\Edge[]
     */
    public function getEdges(): array;
}
