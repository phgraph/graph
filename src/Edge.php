<?php

namespace PHGraph;

use Exception;
use PHGraph\Contracts\Attributable;
use PHGraph\Support\VertexCollection;
use PHGraph\Support\VertexReplacementMap;
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
     * @param \PHGraph\Vertex $from       source vertex
     * @param \PHGraph\Vertex $to         target vertex
     * @param int             $direction  directed
     * @param array           $attributes attributes to add to this
     *
     * @throws Exception if vertices are on different graphs
     *
     * @return void
     */
    public function __construct(Vertex $from, Vertex $to, int $direction = self::DIRECTED, array $attributes = [])
    {
        if ($from->getGraph() !== $to->getGraph()) {
            throw new Exception('trying to create an edge cross graph');
        }

        $this->from = $from;
        $this->to = $to;
        $this->direction = $direction;

        $this->to->addEdgeIn($this);
        $this->from->addEdgeOut($this);

        if (!$this->isDirected()) {
            $this->to->addEdgeOut($this);
            $this->from->addEdgeIn($this);
        }

        $this->setAttributes($attributes);
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
     * @throws Exception if this was destroyed
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
     * @throws Exception if this was destroyed
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
     * @throws Exception if this was destroyed
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
        return new VertexCollection($this->isDirected() ? [$this->to] : [$this->to, $this->from]);
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
     * @param \PHGraph\Support\VertexReplacementMap $vertex_map map of ids to vertices
     *
     * @return void
     */
    public function replaceVerticesFromMap(VertexReplacementMap $vertex_map): void
    {
        $this->to->removeEdge($this);
        $this->from->removeEdge($this);

        $this->to = $vertex_map[$this->to] ?? $this->to;
        $this->from = $vertex_map[$this->from] ?? $this->from;

        $this->to->addEdgeIn($this);
        $this->from->addEdgeOut($this);

        if (!$this->isDirected()) {
            $this->to->addEdgeOut($this);
            $this->from->addEdgeIn($this);
        }
    }

    /**
     * Determine if this is a directed edge.
     *
     * @return bool
     */
    public function isDirected(): bool
    {
        return $this->direction === self::DIRECTED;
    }

    /**
     * Determine if this is a loop.
     *
     * @return bool
     */
    public function isLoop(): bool
    {
        return $this->from === $this->to;
    }

    /**
     * destroy this edge.
     *
     * @return void
     */
    public function destroy(): void
    {
        $this->to->removeEdge($this);
        unset($this->to);

        $this->from->removeEdge($this);
        unset($this->from);
    }

    /**
     * Handle unset properties.
     */
    public function __get(string $property)
    {
        throw new Exception('Undefined Property');
    }
}
