<?php

namespace PHGraph;

use PHGraph\Support\EdgeCollection;
use PHGraph\Support\VertexCollection;

/**
 * A path is a trail in which all vertices (except possibly the first and last)
 * are distinct. A trail is a walk in which all edges are distinct. A walk is an
 * alternating sequence of vertices and edges.
 */
class Walk
{
    /** @var \PHGraph\Vertex */
    protected $start_vertex;
    /** @var \PHGraph\Support\VertexCollection<\PHGraph\Vertex> */
    protected $vertices;
    /** @var \PHGraph\Support\EdgeCollection<\PHGraph\Edge> */
    protected $edges;
    /** @var \PHGraph\Graph */
    protected $graph;
    /** @var mixed[] */
    protected $alternating_sequence = [];

    /**
     * @param \PHGraph\Vertex                                $start_vertex vertex that the walk starts from
     * @param \PHGraph\Support\EdgeCollection<\PHGraph\Edge> $edges        collection of edges for the walk
     *
     * @return void
     */
    public function __construct(Vertex $start_vertex, EdgeCollection $edges)
    {
        $this->start_vertex = $start_vertex;
        $this->edges = $edges;
        $this->vertices = new VertexCollection([$this->start_vertex]);

        // walk the edges for the alternating sequence
        $current_vertex = $start_vertex;
        $this->alternating_sequence[] = $current_vertex;
        foreach ($this->edges->ordered() as $edge) {
            $current_vertex = $edge->getAdjacentVertex($current_vertex);

            $this->vertices[] = $current_vertex;

            $this->alternating_sequence[] = $edge;
            $this->alternating_sequence[] = $current_vertex;
        }
    }

    /**
     * Get the original underlying graph.
     *
     * @return \PHGraph\Graph
     */
    public function getGraph(): Graph
    {
        return $this->start_vertex->getGraph();
    }

    /**
     * Create a new deep cloned graph from this walk.
     *
     * @return \PHGraph\Graph
     */
    public function createGraph(): Graph
    {
        if ($this->graph !== null) {
            return $this->graph;
        }

        $this->graph = $this->getGraph()->newFromEdges($this->edges);

        return $this->graph;
    }

    /**
     * return all edges of walk.
     *
     * @return \PHGraph\Support\EdgeCollection<\PHGraph\Edge>
     */
    public function getEdges(): EdgeCollection
    {
        return $this->edges;
    }

    /**
     * return all vertices of walk.
     *
     * @return \PHGraph\Support\VertexCollection<\PHGraph\Vertex>
     */
    public function getVertices(): VertexCollection
    {
        return $this->vertices;
    }

    /**
     * get alternating sequence: V1, E1, V2, ... Vx.
     *
     * @return mixed[]
     */
    public function getAlternatingSequence(): array
    {
        return $this->alternating_sequence;
    }
}
