<?php

namespace PHGraph;

use Exception;
use PHGraph\Contracts\Attributable;
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

    /** @var string */
    protected $id;
    /** @var \PHGraph\Vertex */
    protected $from;
    /** @var \PHGraph\Vertex */
    protected $to;
    /** @var int */
    protected $direction;
    /** @var bool */
    protected $enabled = true;

    /**
     * @param \PHGraph\Vertex $from       source vertex
     * @param \PHGraph\Vertex $to         target vertex
     * @param int             $direction  directed
     * @param mixed[]         $attributes attributes to add to this
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

        $this->id = spl_object_hash($this);
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
        return $this->id;
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
     * @return \PHGraph\Vertex[]
     */
    public function getTargets(): array
    {
        return $this->isDirected()
            ? [$this->to->getId() => $this->to]
            : [$this->to->getId() => $this->to, $this->from->getId() => $this->from];
    }

    /**
     * Get the vertices on this edge.
     *
     * @return \PHGraph\Vertex[]
     */
    public function getVertices(): array
    {
        return [$this->to->getId() => $this->to, $this->from->getId() => $this->from];
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
        $this->from->removeEdge($this);
        unset($this->to);
        unset($this->from);
    }

    /**
     * Get enabled status.
     *
     * @return bool
     */
    public function enabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Enable this edge. Only useful if the edge was previously disabled.
     *
     * @return void
     */
    public function enable(): void
    {
        $this->enabled = true;

        $this->to->enableEdge($this);
        $this->from->enableEdge($this);
    }

    /**
     * Disable this edge. It should not be traversed, but may be renabled later.
     *
     * @return void
     */
    public function disable(): void
    {
        $this->enabled = false;

        $this->to->disableEdge($this);
        $this->from->disableEdge($this);
    }

    /**
     * Handle PHP native clone call. We reset id.
     *
     * @return void
     */
    public function __clone()
    {
        $this->id = spl_object_hash($this);
    }

    /**
     * Handle unset properties.
     *
     * @param string  $property  dynamic property to get
     *
     * @throws \Exception always
     *
     * @return void
     */
    public function __get(string $property)
    {
        throw new Exception('Undefined Property: ' . $property);
    }
}
