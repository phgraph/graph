<?php

namespace PHGraph;

use PHGraph\Contracts\Attributable;
use PHGraph\Contracts\Directable;
use PHGraph\Support\EdgeCollection;
use PHGraph\Support\VertexCollection;
use PHGraph\Support\VertexReplacementMap;
use PHGraph\Traits\Attributes;
use UnderflowException;
use UnexpectedValueException;

/**
 * Representation of a Mathematical Graph.
 */
class Graph implements Attributable, Directable
{
    use Attributes;

    /** @var \PHGraph\Support\VertexCollection */
    protected $vertices;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->vertices = new VertexCollection;
    }

    /**
     * Degree: get degree for k-regular-graph (only if each vertex has the same
     * degree).
     *
     * @throws UnderflowException       if graph is empty
     * @throws UnexpectedValueException if graph is not regular
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
     * Degree: get minimum degree of vertices.
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
     * Degree: get maximum degree of vertices.
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
     * get the vertices in the graph.
     *
     * @return \PHGraph\Support\VertexCollection
     */
    public function getVertices(): VertexCollection
    {
        return $this->vertices;
    }

    /**
     * add Vertex to Graph.
     *
     * @param \PHGraph\Vertex $vertex vertex to add
     *
     * @return void
     */
    public function addVertex(Vertex $vertex): void
    {
        $this->vertices->add($vertex);

        if ($this !== $vertex->getGraph()) {
            $vertex->setGraph($this);
        }
    }

    /**
     * create a new Vertex in this Graph.
     *
     * @param array $attributes attributes for the vertex
     *
     * @return \PHGraph\Vertex
     */
    public function newVertex(array $attributes = []): Vertex
    {
        $vertex = new Vertex($this, $attributes);

        $this->vertices->add($vertex);

        return $vertex;
    }

    /**
     * remove Vertex from Graph.
     *
     * @param \PHGraph\Vertex $vertex vertex to remove
     *
     * @return void
     */
    public function removeVertex(Vertex $vertex): void
    {
        $this->vertices->remove($vertex);

        $vertex->destroy();
    }

    /**
     * get the edges in the graph.
     *
     * @return \PHGraph\Support\EdgeCollection
     */
    public function getEdges(): EdgeCollection
    {
        $edges = new EdgeCollection;

        foreach ($this->vertices as $vertex) {
            $edges = $edges->merge($vertex->getEdges());
        }

        return $edges;
    }

    /**
     * Create a copy of this graph with only the supplied edges.
     *
     * @param \PHGraph\Support\EdgeCollection $edges edges to use
     *
     * @return \PHGraph\Graph
     */
    public function newFromEdges(EdgeCollection $edges): Graph
    {
        $new_graph = new static;

        $vertex_replacement_map = new VertexReplacementMap;

        foreach ($edges->getVertices() as $vertex) {
            $new_vertex = clone $vertex;
            $new_vertex->setGraph($new_graph);

            $vertex_replacement_map[$vertex] = $new_vertex;
        }

        foreach ($edges as $edge) {
            $new_edge = clone $edge;

            $new_edge->replaceVerticesFromMap($vertex_replacement_map);
        }

        return $new_graph;
    }

    /**
     * Grouped: count total number of different groups assigned to vertices.
     *
     * @return int
     */
    public function getNumberOfGroups(): int
    {
        return count($this->getGroups());
    }

    /**
     * Grouped: checks whether the input graph's vertex groups are a valid bipartition.
     *
     * @return bool
     */
    public function isBipartit(): bool
    {
        if ($this->getNumberOfGroups() !== 2) {
            return false;
        }

        foreach ($this->vertices as $vertex) {
            $group = $vertex->getAttribute('group');

            foreach ($vertex->getVerticesTo() as $vertex_neighbor) {
                if ($vertex_neighbor->getAttribute('group') === $group) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Grouped: get vector of all group numbers.
     *
     * @return array
     */
    public function getGroups(): array
    {
        $groups = [];

        foreach ($this->vertices as $vertex) {
            $groups[$vertex->getAttribute('group')] = true;
        }

        return array_keys($groups);
    }

    /**
     * Grouped: get set of all Vertices in the given group.
     *
     * @param int $group
     *
     * @return \PHGraph\Support\VertexCollection
     */
    public function getVerticesGroup(int $group): VertexCollection
    {
        return $this->vertices->filter(function ($vertex) use ($group) {
            return $vertex->getAttribute('group') === $group;
        });
    }

    /**
     * Directed: checks whether the graph has any directed edges.
     *
     * @return bool
     */
    public function hasDirected(): bool
    {
        foreach ($this->getEdges() as $edge) {
            if ($edge->isDirected()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Directed: checks whether the graph has any undirected edges.
     *
     * @return bool
     */
    public function hasUndirected(): bool
    {
        foreach ($this->getEdges() as $edge) {
            if (!$edge->isDirected()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Directed: checks whether this is a mixed graph.
     *
     * @return bool
     */
    public function isMixed(): bool
    {
        return $this->hasDirected() && $this->hasUndirected();
    }

    /**
     * Degree: checks whether the indegree of every vertex equals its outdegree.
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

    /**
     * Degree: checks whether this graph is regular, i.e. each vertex has the
     * same indegree/outdegree.
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
}
