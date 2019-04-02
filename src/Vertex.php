<?php

namespace PHGraph;

use PHGraph\Contracts\Attributable;
use PHGraph\Support\EdgeCollection;
use PHGraph\Support\VertexCollection;
use PHGraph\Traits\Attributes;

/**
 * The fundamental unit of which graphs are formed.
 */
class Vertex implements Attributable
{
    use Attributes;

    /** @var \PHGraph\Graph */
    protected $graph;
    /** @var \PHGraph\Support\EdgeCollection */
    protected $edges_in;
    /** @var \PHGraph\Support\EdgeCollection */
    protected $edges_out;

    /**
     * @param \PHGraph\Graph $graph source graph
     *
     * @return void
     */
    public function __construct(Graph $graph, array $attributes = [])
    {
        $this->setGraph($graph);
        $this->edges_in = new EdgeCollection;
        $this->edges_out = new EdgeCollection;
        $this->setAttributes($attributes);
    }

    /**
     * Get a unique id for this vertex.
     *
     * @return string
     */
    public function getId(): string
    {
        return spl_object_hash($this);
    }

    /**
     * Set associated graph.
     *
     * @param \PHGraph\Graph $graph graph to associate vertex to
     *
     * @return void
     */
    public function setGraph(Graph $graph): void
    {
        if ($this->graph === $graph) {
            return;
        }

        $this->graph = $graph;
        $this->graph->addVertex($this);
    }

    /**
     * Get associated graph.
     *
     * @return \PHGraph\Graph
     */
    public function getGraph(): Graph
    {
        return $this->graph;
    }

    /**
     * Get all edges connected to this vertex.
     *
     * @return \PHGraph\Support\EdgeCollection
     */
    public function getEdges(): EdgeCollection
    {
        return $this->edges_in->merge($this->edges_out);
    }

    /**
     * Get the edges leading in to this vertex.
     *
     * @return \PHGraph\Support\EdgeCollection
     */
    public function getEdgesIn(): EdgeCollection
    {
        return $this->edges_in;
    }

    /**
     * Get the edges leading out from this vertex.
     *
     * @return \PHGraph\Support\EdgeCollection
     */
    public function getEdgesOut(): EdgeCollection
    {
        return $this->edges_out;
    }

    /**
     * Get the vertices that this vertex is connected from.
     *
     * @return \PHGraph\Support\VertexCollection
     */
    public function getVerticesFrom(): VertexCollection
    {
        $vertices = new VertexCollection;
        $has_loop = false;

        foreach ($this->edges_in as $edge) {
            if ($edge->isLoop()) {
                $has_loop = true;
            }

            $vertices = $vertices->merge($edge->getVertices());
        }

        if (!$has_loop) {
            $vertices->remove($this);
        }

        return $vertices;
    }

    /**
     * Get the vertices that this vertex is connected to.
     *
     * @return \PHGraph\Support\VertexCollection
     */
    public function getVerticesTo(): VertexCollection
    {
        $vertices = new VertexCollection;
        $has_loop = false;

        foreach ($this->edges_out as $edge) {
            if ($edge->isLoop()) {
                $has_loop = true;
            }

            $vertices = $vertices->merge($edge->getVertices());
        }

        if (!$has_loop) {
            $vertices->remove($this);
        }

        return $vertices;
    }

    /**
     * Create an undirected edge from this vertex the given vertex.
     *
     * @param \PHGraph\Vertex $vertex vertex to connect to
     *
     * @throws \Exception if vertices are on different graphs
     *
     * @return \PHGraph\Edge
     */
    public function createEdge(Vertex $vertex): Edge
    {
        return new Edge($this, $vertex, Edge::UNDIRECTED);
    }

    /**
     * Create a directed edge from this vertex to another vertex.
     *
     * @param \PHGraph\Vertex $vertex vertex to connect to
     *
     * @throws \Exception if vertices are on different graphs
     *
     * @return \PHGraph\Edge
     */
    public function createEdgeTo(Vertex $vertex): Edge
    {
        return new Edge($this, $vertex, Edge::DIRECTED);
    }

    /**
     * Call to associate an edge in from direction with this vertex.
     *
     * @param \PHGraph\Edge $edge edge requested to be associated
     *
     * @return void
     */
    public function addEdgeIn(Edge $edge): void
    {
        if ($edge->isDirected() && $edge->getTo() !== $this) {
            return;
        }

        if ($edge->getFrom() !== $this && $edge->getTo() !== $this) {
            return;
        }

        $this->edges_in[] = $edge;
    }

    /**
     * Call to associate an edge in to direction with this vertex.
     *
     * @param \PHGraph\Edge $edge edge requested to be associated
     *
     * @return void
     */
    public function addEdgeOut(Edge $edge): void
    {
        if ($edge->isDirected() && $edge->getFrom() !== $this) {
            return;
        }

        if ($edge->getFrom() !== $this && $edge->getTo() !== $this) {
            return;
        }

        $this->edges_out[] = $edge;
    }

    /**
     * remove references to given edge.
     *
     * @param \PHGraph\Edge $edge edge to remove
     *
     * @return void
     */
    public function removeEdge(Edge $edge): void
    {
        $this->edges_in->remove($edge);
        $this->edges_out->remove($edge);
    }

    /**
     * get degree of this vertex (total number of edges).
     *
     * vertex degree counts the total number of edges attached to this vertex
     * regardless of whether they're directed or not. loop edges are counted
     * twice as both start and end form a 'line' to the same vertex.
     *
     * @return int
     */
    public function degree(): int
    {
        $edges = $this->getEdges();

        return count($edges) + count($edges->filter(function ($edge) {
            return $edge->isLoop();
        }));
    }

    /**
     * get indegree of this vertex.
     *
     * @return int
     */
    public function degreeIn(): int
    {
        return count($this->edges_in) + count($this->edges_out->filter(function ($edge) {
            return $edge->isLoop() && !$edge->isDirected();
        }));
    }

    /**
     * get outdegree of this vertex.
     *
     * @return int
     */
    public function degreeOut(): int
    {
        return count($this->edges_out) + count($this->edges_in->filter(function ($edge) {
            return $edge->isLoop() && !$edge->isDirected();
        }));
    }

    /**
     * check whether this vertex is isolated.
     *
     * @return bool
     */
    public function isIsolated(): bool
    {
        return count($this->getEdges()) === 0;
    }

    /**
     * checks whether this vertex is a sink, i.e. its outdegree is zero.
     *
     * @return bool
     */
    public function isSink(): bool
    {
        return count($this->edges_out) === 0;
    }

    /**
     * checks whether this vertex is a source, i.e. its indegree is zero.
     *
     * @return bool
     */
    public function isSource(): bool
    {
        return count($this->edges_in) === 0;
    }

    /**
     * Handle PHP native clone call. We disassociate all edges in this case.
     *
     * @return void
     */
    public function __clone()
    {
        $this->edges_in = new EdgeCollection;
        $this->edges_out = new EdgeCollection;
    }
}
