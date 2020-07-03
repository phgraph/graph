<?php

declare(strict_types=1);

namespace PHGraph\Contracts;

interface Search
{
    /**
     * @return \PHGraph\Vertex[]
     */
    public function getVertices(): array;
}
