<?php

namespace PHGraph\Contracts;

interface Directable
{
    /**
     * checks whether the graph has any directed edges.
     *
     * @return bool
     */
    public function hasDirected(): bool;

    /**
     * checks whether the graph has any undirected edges.
     *
     * @return bool
     */
    public function hasUndirected(): bool;

    /**
     * checks whether this is a mixed graph.
     *
     * @return bool
     */
    public function isMixed(): bool;

    /**
     * get the edges in the graph.
     *
     * @return \PHGraph\Edge[]
     */
    public function getEdges(): array;
}
