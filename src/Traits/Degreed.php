<?php

namespace PHGraph\Traits;

use PHGraph\Vertex;
use UnderflowException;
use UnexpectedValueException;

/**
 * The degree (or valency) of a Vertex of a Graph is the number of Edges
 * incident to the Vertex, with Loops counted twice.
 */
trait Degreed
{
    /**
     * get degree for k-regular-graph (only if each vertex has the same degree).
     *
     * @throws UnderflowException       if graph is empty
     * @throws UnexpectedValueException if graph is not regular (i.e. vertex degrees are not equal)
     *
     * @return int
     */
    public function getDegree(): int
    {
        $degree = $this->vertices->first()->degree();

        foreach ($this->vertices as $vertex) {
            $i = $vertex->degree();

            if ($i !== $degree) {
                throw new UnexpectedValueException('Graph is not k-regular (vertex degrees differ)');
            }
        }

        return $degree;
    }

    /**
     * get minimum degree of vertices.
     *
     * @return int
     */
    public function getDegreeMin(): int
    {
        return $this->vertices->sortBy(function ($vertex) {
            return $vertex->degree();
        })->first()->degree();
    }

    /**
     * get maximum degree of vertices.
     *
     * @return int
     */
    public function getDegreeMax(): int
    {
        return $this->vertices->sortByDesc(function ($vertex) {
            return $vertex->degree();
        })->first()->degree();
    }

    /**
     * checks whether this graph is regular, i.e. each vertex has the same indegree/outdegree.
     *
     * @return bool
     */
    public function isRegular(): bool
    {
        if (count($this->vertices) === 0) {
            return true;
        }

        try {
            $this->getDegree();

            return true;
        } catch (UnexpectedValueException $ignore) {
            // ignoring UnexpectedValueException
        }

        return false;
    }

    /**
     * checks whether the indegree of every vertex equals its outdegree.
     *
     * @return bool
     */
    public function isBalanced(): bool
    {
        foreach ($this->vertices as $vertex) {
            if ($vertex->degreeIn() !== $vertex->degreeOut()) {
                return false;
            }
        }

        return true;
    }
}
