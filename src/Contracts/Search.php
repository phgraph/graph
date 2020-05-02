<?php

namespace PHGraph\Contracts;

interface Search
{
    /**
     * @return \PHGraph\Vertex[]
     */
    public function getVertices(): array;
}
