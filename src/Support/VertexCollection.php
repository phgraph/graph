<?php

namespace PHGraph\Support;

use InvalidArgumentException;
use PHGraph\Vertex;

/**
 * Collection of vertices. We key them by their spl object hash for quicker
 * checks when we do graph traversal.
 */
class VertexCollection extends Collection
{
    /**
     * {@inheritdoc}
     */
    public function contains($value): bool
    {
        if (!$value instanceof Vertex) {
            return false;
        }

        return $this->containsVertex($value);
    }

    /**
     * Determine if the given Vertex is in this collection.
     *
     * @param \PHGraph\Vertex $vertex vertex to check
     *
     * @return bool
     */
    public function containsVertex(Vertex $vertex): bool
    {
        return (bool) ($this->items[$vertex->getId()] ?? false);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($value): void
    {
        $this->offsetUnset($value->getId());
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException when non-vertex supplied
     */
    public function offsetSet($offset, $value): void
    {
        if (!$value instanceof Vertex) {
            throw new InvalidArgumentException('invalid collection member');
        }

        $this->items[$value->getId()] = $value;
    }
}
