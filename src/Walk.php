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
    /** @var \PHGraph\Support\VertexCollection */
    protected $vertices;
    /** @var \PHGraph\Support\EdgeCollection */
    protected $edges;

    /**
     * @todo should edges be optional for the walk?
     *
     * @param \PHGraph\Vertex                 $start_vertex vertex that the walk starts from
     * @param \PHGraph\Support\EdgeCollection $edges        optional collection of edges for the walk
     *
     * @return void
     */
    public function __construct(Vertex $start_vertex, EdgeCollection $edges = null)
    {
        $this->start_vertex = $start_vertex;
        $this->edges = $edges ?? new EdgeCollection;
        $this->vertices = new VertexCollection([$this->start_vertex]);

        if ($edges === null) {
            $this->edges = new EdgeCollection;
        }
    }
}
