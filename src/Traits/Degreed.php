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
        $degree = $this->getDegreeVertex($this->vertices->first());

        foreach ($this->vertices as $vertex) {
            $i = $this->getDegreeVertex($vertex);

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
        return $this->getDegreeVertex($this->vertices->sortBy(function ($vertex) {
            return $this->getDegreeVertex($vertex);
        })->first());
    }

    /**
     * get maximum degree of vertices.
     *
     * @return int
     */
    public function getDegreeMax(): int
    {
        return $this->getDegreeVertex($this->vertices->sortByDesc(function ($vertex) {
            return $this->getDegreeVertex($vertex);
        })->first());
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
            if ($this->getDegreeInVertex($vertex) !== $this->getDegreeOutVertex($vertex)) {
                return false;
            }
        }

        return true;
    }

    /**
     * checks whether this vertex is a source, i.e. its indegree is zero.
     *
     * @param \PHGraph\Vertex $vertex
     *
     * @return bool
     */
    public function isVertexSource(Vertex $vertex): bool
    {
        return count($vertex->getEdgesIn()) === 0;
    }

    /**
     * checks whether this vertex is a sink, i.e. its outdegree is zero.
     *
     * @param \PHGraph\Vertex $vertex
     *
     * @return bool
     */
    public function isVertexSink(Vertex $vertex): bool
    {
        return count($vertex->getEdgesOut()) === 0;
    }

    /**
     * get degree of this vertex (total number of edges).
     *
     * vertex degree counts the total number of edges attached to this vertex
     * regardless of whether they're directed or not. loop edges are counted
     * twice as both start and end form a 'line' to the same vertex.
     *
     * @todo does this feel more like a native of Vertex?
     *
     * @param \PHGraph\Vertex $vertex
     *
     * @return int
     */
    public function getDegreeVertex(Vertex $vertex): int
    {
        $edges = $vertex->getEdges();

        return count($edges) + count($edges->filter(function ($edge) {
            return $edge->loop();
        }));
    }

    /**
     * check whether this vertex is isolated.
     *
     * @param \PHGraph\Vertex $vertex
     *
     * @return bool
     */
    public function isVertexIsolated(Vertex $vertex): bool
    {
        return count($vertex->getEdges()) === 0;
    }

    /**
     * get indegree of this vertex.
     *
     * @param \PHGraph\Vertex $vertex
     *
     * @return int
     */
    public function getDegreeInVertex(Vertex $vertex): int
    {
        return count($vertex->getEdgesIn());
    }

    /**
     * get outdegree of this vertex.
     *
     * @todo verify if loops count?
     *
     * @param \PHGraph\Vertex $vertex
     *
     * @return int
     */
    public function getDegreeOutVertex(Vertex $vertex): int
    {
        return count($vertex->getEdgesOut());
    }
}
