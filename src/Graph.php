<?php

declare(strict_types=1);

namespace PHGraph;

use PHGraph\Contracts\Attributable;
use PHGraph\Contracts\Directable;
use PHGraph\Support\VertexReplacementMap;
use PHGraph\Traits\Attributes;
use UnderflowException;
use UnexpectedValueException;

/**
 * Representation of a Mathematical Graph.
 */
final class Graph implements Attributable, Directable
{
    use Attributes;

    /** @var \PHGraph\Vertex[] */
    protected $vertices = [];

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
        if (count($this->vertices) === 0) {
            throw new UnderflowException('Graph is empty');
        }

        $degree = reset($this->vertices)->degree();

        foreach ($this->vertices as $vertex) {
            $i = $vertex->degree();

            if ($i !== $degree) {
                throw new UnexpectedValueException('Graph is not k-regular');
            }
        }

        return $degree;
    }

    /**
     * Degree: get minimum degree of vertices.
     *
     * @throws UnderflowException if graph is empty
     *
     * @return int
     */
    public function getDegreeMin(): int
    {
        if (count($this->vertices) === 0) {
            throw new UnderflowException('Graph is empty');
        }

        return min(array_map(static function ($item) {
            return $item->degree();
        }, $this->vertices));
    }

    /**
     * Degree: get maximum degree of vertices.
     *
     * @throws UnderflowException if graph is empty
     *
     * @return int
     */
    public function getDegreeMax(): int
    {
        if (count($this->vertices) === 0) {
            throw new UnderflowException('Graph is empty');
        }

        return max(array_map(static function ($item) {
            return $item->degree();
        }, $this->vertices));
    }

    /**
     * get the vertices in the graph.
     *
     * @return \PHGraph\Vertex[]
     */
    public function getVertices(): array
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
        $this->vertices[$vertex->getId()] = $vertex;

        if ($this !== $vertex->getGraph()) {
            $vertex->setGraph($this);
        }
    }

    /**
     * create a new Vertex in this Graph.
     *
     * @param mixed[] $attributes attributes for the vertex
     *
     * @return \PHGraph\Vertex
     */
    public function newVertex(array $attributes = []): Vertex
    {
        $vertex = new Vertex($this, $attributes);

        $this->vertices[$vertex->getId()] = $vertex;

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
        unset($this->vertices[$vertex->getId()]);

        $vertex->destroy();
    }

    /**
     * get the edges in the graph.
     *
     * @return \PHGraph\Edge[]
     */
    public function getEdges(): array
    {
        $edges = [];

        /** @var \PHGraph\Vertex $vertex */
        foreach ($this->vertices as $vertex) {
            foreach ($vertex->getEdges() as $edge) {
                $edges[$edge->getId()] = $edge;
            }
        }

        return $edges;
    }

    /**
     * Create a copy of this graph with only the supplied edges.
     *
     * @param \PHGraph\Edge[] $edges edges to use
     *
     * @return \PHGraph\Graph
     */
    public function newFromEdges(array $edges): Graph
    {
        $new_graph = new static();
        $new_graph->attributes = $this->attributes;

        $vertex_replacement_map = new VertexReplacementMap();

        $vertices = [];
        foreach ($edges as $edge) {
            foreach ($edge->getVertices() as $vertex) {
                $vertices[$vertex->getId()] = $vertex;
            }
        }

        /** @var \PHGraph\Vertex $vertex */
        foreach ($vertices as $vertex) {
            $new_vertex = clone $vertex;
            $new_vertex->setGraph($new_graph);

            $vertex_replacement_map[$vertex] = $new_vertex;
        }

        /** @var \PHGraph\Edge $edge */
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
     * Grouped: checks whether the input graph’s vertex groups are a valid
     * bipartition.
     *
     * @return bool
     */
    public function isBipartit(): bool
    {
        if ($this->getNumberOfGroups() !== 2) {
            return false;
        }

        /** @var \PHGraph\Vertex $vertex */
        foreach ($this->vertices as $vertex) {
            $group = $vertex->getAttribute('group');

            /** @var \PHGraph\Vertex $vertex_neighbor */
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
     * @return array<int>
     */
    public function getGroups(): array
    {
        $groups = [];

        /** @var \PHGraph\Vertex $vertex */
        foreach ($this->vertices as $vertex) {
            $groups[(int) $vertex->getAttribute('group')] = true;
        }

        return array_keys($groups);
    }

    /**
     * Grouped: get set of all Vertices in the given group.
     *
     * @param int $group
     *
     * @return \PHGraph\Vertex[]
     */
    public function getVerticesGroup(int $group): array
    {
        return array_filter($this->vertices, static function ($vertex) use ($group) {
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
     * Complete: checks whether this graph is complete.
     *
     * @return bool
     */
    public function isComplete(): bool
    {
        foreach ($this->vertices as $vertex_a) {
            $connected_vertices = $vertex_a->getVertices();
            foreach ($this->vertices as $vertex_b) {
                if (
                    $vertex_a !== $vertex_b
                    && !isset($connected_vertices[$vertex_b->getId()])
                ) {
                    return false;
                }
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
