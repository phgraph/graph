<?php

declare(strict_types=1);

namespace PHGraph;

/**
 * A path is a trail in which all vertices (except possibly the first and last)
 * are distinct. A trail is a walk in which all edges are distinct. A walk is an
 * alternating sequence of vertices and edges.
 */
final class Walk
{
    private Vertex $start_vertex;
    /** @var \PHGraph\Vertex[] */
    private array $vertices;
    /** @var \PHGraph\Edge[] */
    private array $edges;
    private Graph $graph;
    /** @var mixed[] */
    private array $alternating_sequence = [];

    /**
     * @param \PHGraph\Vertex $start_vertex vertex that the walk starts from
     * @param \PHGraph\Edge[] $edges collection of edges for the walk
     *
     * @return void
     */
    public function __construct(Vertex $start_vertex, array $edges)
    {
        $this->start_vertex = $start_vertex;
        $this->edges = $edges;
        $this->vertices = [$this->start_vertex->getId() => $this->start_vertex];

        // walk the edges for the alternating sequence
        $current_vertex = $start_vertex;
        $this->alternating_sequence[] = $current_vertex;
        foreach ($this->edges as $edge) {
            $current_vertex = $edge->getAdjacentVertex($current_vertex);

            $this->vertices[$current_vertex->getId()] = $current_vertex;

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
        if (isset($this->graph)) {
            return $this->graph;
        }

        $this->graph = $this->getGraph()->newFromEdges($this->edges);

        return $this->graph;
    }

    /**
     * return all edges of walk.
     *
     * @return \PHGraph\Edge[]
     */
    public function getEdges(): array
    {
        return $this->edges;
    }

    /**
     * return all vertices of walk.
     *
     * @return \PHGraph\Vertex[]
     */
    public function getVertices(): array
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
