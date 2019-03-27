<?php

namespace PHGraph\Contracts;

use PHGraph\Support\VertexCollection;

interface Search
{
    /**
     * @return \PHGraph\Support\VertexCollection
     */
    public function getVertices(): VertexCollection;
}
