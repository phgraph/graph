<?php

namespace PHGraph\Traits;

trait Directed
{
    /**
     * checks whether the graph has any directed edges.
     *
     * @return bool
     */
    public function hasDirected(): bool
    {
        foreach ($this->getEdges() as $edge) {
            if ($edge->directed()) {
                return true;
            }
        }

        return false;
    }

    /**
     * checks whether the graph has any undirected edges.
     *
     * @return bool
     */
    public function hasUndirected(): bool
    {
        foreach ($this->getEdges() as $edge) {
            if (!$edge->directed()) {
                return true;
            }
        }

        return false;
    }

    /**
     * checks whether this is a mixed graph.
     *
     * @return bool
     */
    public function isMixed(): bool
    {
        return $this->hasDirected() && $this->hasUndirected();
    }
}
