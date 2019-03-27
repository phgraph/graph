<?php

namespace PHGraph;

use PHGraph\Contracts\Attributable;
use PHGraph\Contracts\Directable;
use PHGraph\Support\EdgeCollection;
use PHGraph\Support\VertexCollection;
use PHGraph\Traits\Attributes;
use PHGraph\Traits\Degreed;
use PHGraph\Traits\Directed;
use PHGraph\Traits\Grouped;

/**
 * Representation of a Mathematical Graph.
 */
class Graph implements Attributable, Directable
{
    use Attributes;
    use Degreed;
    use Directed;
    use Grouped;

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

        $vertex_replacement_map = array_map(function ($vertex) use ($new_graph) {
            $new_vertex = clone $vertex;
            $new_vertex->setGraph($new_graph);

            return $new_vertex;
        }, $edges->getVertices()->items());

        foreach ($edges as $edge) {
            $new_edge = clone $edge;

            $new_edge->replaceVerticesFromMap($vertex_replacement_map);
        }

        return $new_graph;
    }
}
