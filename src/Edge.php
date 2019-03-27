<?php

namespace PHGraph;

use Exception;
use PHGraph\Contracts\Attributable;
use PHGraph\Support\VertexCollection;
use PHGraph\Traits\Attributes;

/**
 * Directed connectors of vertices.
 */
class Edge implements Attributable
{
    use Attributes;

    /** @var int */
    const DIRECTED = 0;
    /** @var int */
    const UNDIRECTED = 1;

    /** @var \PHGraph\Vertex */
    protected $from;
    /** @var \PHGraph\Vertex */
    protected $to;
    /** @var int */
    protected $direction;

    /**
     * @param \PHGraph\Vertex $from      source vertex
     * @param \PHGraph\Vertex $to        target vertex
     * @param int             $direction directed
     *
     * @throws Exception if vertices are on different graphs
     *
     * @return void
     */
    public function __construct(Vertex $from, Vertex $to, int $direction = self::DIRECTED)
    {
        if ($from->getGraph() !== $to->getGraph()) {
            throw new Exception('trying to create an edge cross graph');
        }

        $this->from = $from;
        $this->to = $to;
        $this->direction = $direction;

        $this->to->addEdgeIn($this);
        $this->from->addEdgeOut($this);

        if (!$this->directed()) {
            $this->to->addEdgeOut($this);
            $this->from->addEdgeIn($this);
        }
    }

    /**
     * Get a unique id for this edge.
     *
     * @return string
     */
    public function getId(): string
    {
        return spl_object_hash($this);
    }

    /**
     * Get edge from vertex.
     *
     * @return \PHGraph\Vertex
     */
    public function getFrom(): Vertex
    {
        return $this->from;
    }

    /**
     * Get edge to vertex.
     *
     * @return \PHGraph\Vertex
     */
    public function getTo(): Vertex
    {
        return $this->to;
    }

    /**
     * Get the other vertex on this edge.
     *
     * @param \PHGraph\Vertex $vertex given vertex
     *
     * @return \PHGraph\Vertex
     */
    public function getAdjacentVertex(Vertex $vertex): Vertex
    {
        return ($this->to === $vertex) ? $this->from : $this->to;
    }

    /**
     * Get target vertices of this edge.
     *
     * @return \PHGraph\Support\VertexCollection
     */
    public function getTargets(): VertexCollection
    {
        return new VertexCollection($this->directed() ? [$this->to] : [$this->to, $this->from]);
    }

    /**
     * Get the vertices on this edge.
     *
     * @return \PHGraph\Support\VertexCollection
     */
    public function getVertices(): VertexCollection
    {
        return new VertexCollection([$this->to, $this->from]);
    }

    /**
     * Given a vertex replacement map replace the vertices pointed to by this edge.
     *
     * @todo make vertex_map a proper well-defined support object
     *
     * @param array $vertex_map map of ids to vertices
     *
     * @return void
     */
    public function replaceVerticesFromMap(array $vertex_map): void
    {
        $this->to = $vertex_map[$this->to->getId()];
        $this->from = $vertex_map[$this->from->getId()];

        $this->to->addEdgeOut($this);
        $this->from->addEdgeIn($this);
    }

    /**
     * Determine if this is a directed edge.
     *
     * @return bool
     */
    public function directed(): bool
    {
        return $this->direction === self::DIRECTED;
    }

    /**
     * Determine if this is a loop.
     *
     * @return bool
     */
    public function loop(): bool
    {
        return $this->from === $this->to;
    }
}
