<?php

namespace PHGraph\Contracts;

use PHGraph\Support\VertexCollection;

interface Search
{
    /**
     * @return \PHGraph\Support\VertexCollection<\PHGraph\Vertex>
     */
    public function getVertices(): VertexCollection;
}
