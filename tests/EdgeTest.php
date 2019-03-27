<?php

namespace Tests;

use PHGraph\Graph;
use PHGraph\Edge;
use PHGraph\Vertex;
use PHPUnit\Framework\TestCase;

class EdgeTest extends TestCase
{
    /** @var \PHGraph\Graph */
    private $graph;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->graph = new Graph;
    }

    /**
     * @covers PHGraph\Edge::__construct
     *
     * @return void
     */
    public function testInstantiation(): void
    {
        $edge = new Edge(new Vertex($this->graph), new Vertex($this->graph));

        $this->assertInstanceOf(Edge::class, $edge);
    }

    /**
     * @covers PHGraph\Edge::__construct
     *
     * @return void
     */
    public function testInstantiationCrossGraphException(): void
    {
        $this->expectException(\Exception::class);

        $edge = new Edge(new Vertex($this->graph), new Vertex(new Graph));
    }

    /**
     * @covers PHGraph\Edge::__construct
     *
     * @return void
     */
    public function testNewNotifiesVerticesOfEdgeDirected(): void
    {
        $vertex_a = new Vertex($this->graph);
        $vertex_b = new Vertex($this->graph);
        $edge = new Edge($vertex_a, $vertex_b, Edge::DIRECTED);

        $this->assertEquals([$edge], $vertex_a->getEdges()->all());
    }

    /**
     * @covers PHGraph\Edge::__construct
     *
     * @return void
     */
    public function testNewNotifiesVerticesOfEdgeUndirected(): void
    {
        $vertex_a = new Vertex($this->graph);
        $vertex_b = new Vertex($this->graph);
        $edge = new Edge($vertex_a, $vertex_b, Edge::UNDIRECTED);

        $this->assertEquals([$edge], $vertex_a->getEdges()->all());
    }

    /**
     * @covers PHGraph\Edge::getId
     *
     * @return void
     */
    public function testGetIdUnique(): void
    {
        $vertex_a = new Vertex(new Graph);
        $vertex_b = clone $vertex_a;
        $edge_a = $vertex_a->createEdgeTo($vertex_b);
        $edge_b = clone $edge_a;

        $this->assertNotEquals($edge_a->getId(), $edge_b->getId());
    }

    /**
     * @covers PHGraph\Edge::getFrom
     *
     * @return void
     */
    public function testGetFrom(): void
    {
        $vertex_a = new Vertex($this->graph);
        $vertex_b = new Vertex($this->graph);
        $edge = new Edge($vertex_a, $vertex_b);

        $this->assertSame($vertex_a, $edge->getFrom());
    }

    /**
     * @covers PHGraph\Edge::getTo
     *
     * @return void
     */
    public function testGetTo(): void
    {
        $vertex_a = new Vertex($this->graph);
        $vertex_b = new Vertex($this->graph);
        $edge = new Edge($vertex_a, $vertex_b);

        $this->assertSame($vertex_b, $edge->getTo());
    }

    /**
     * @covers PHGraph\Edge::getAdjacentVertex
     *
     * @return void
     */
    public function testGetAdjacentVertexForward(): void
    {
        $vertex_a = new Vertex($this->graph);
        $vertex_b = new Vertex($this->graph);
        $edge = new Edge($vertex_a, $vertex_b, Edge::DIRECTED);

        $this->assertSame($vertex_b, $edge->getAdjacentVertex($vertex_a));
    }

    /**
     * @covers PHGraph\Edge::getAdjacentVertex
     *
     * @return void
     */
    public function testGetAdjacentVertexBackwards(): void
    {
        $vertex_a = new Vertex($this->graph);
        $vertex_b = new Vertex($this->graph);
        $edge = new Edge($vertex_a, $vertex_b, Edge::DIRECTED);

        $this->assertSame($vertex_a, $edge->getAdjacentVertex($vertex_b));
    }

    /**
     * @covers PHGraph\Edge::getAdjacentVertex
     *
     * @return void
     */
    public function testGetAdjacentVertexLoop(): void
    {
        $vertex_a = new Vertex($this->graph);
        $edge = new Edge($vertex_a, $vertex_a, Edge::DIRECTED);

        $this->assertSame($vertex_a, $edge->getAdjacentVertex($vertex_a));
    }

    /**
     * @covers PHGraph\Edge::getTargets
     *
     * @return void
     */
    public function testGetTargetsDirected(): void
    {
        $vertex_a = new Vertex($this->graph);
        $vertex_b = new Vertex($this->graph);
        $edge = new Edge($vertex_a, $vertex_b, Edge::DIRECTED);

        $this->assertSame([$vertex_b], $edge->getTargets()->all());
    }

    /**
     * @covers PHGraph\Edge::getTargets
     *
     * @return void
     */
    public function testGetTargetsUndirected(): void
    {
        $vertex_a = new Vertex($this->graph);
        $vertex_b = new Vertex($this->graph);
        $edge = new Edge($vertex_a, $vertex_b, Edge::UNDIRECTED);

        $this->assertEquals([$vertex_a, $vertex_b], $edge->getTargets()->all());
    }

    /**
     * @covers PHGraph\Edge::getVertices
     *
     * @return void
     */
    public function testGetVertices(): void
    {
        $vertex_a = new Vertex($this->graph);
        $vertex_b = new Vertex($this->graph);
        $edge = new Edge($vertex_a, $vertex_b);

        $this->assertEqualsCanonicalizing([$vertex_a, $vertex_b], $edge->getVertices()->all());
    }

    /**
     * @covers PHGraph\Edge::replaceVerticesFromMap
     *
     * @return void
     */
    public function testReplaceVerticesFromMap(): void
    {
        $vertex_a = new Vertex($this->graph);
        $vertex_b = new Vertex($this->graph);
        $edge = new Edge($vertex_a, $vertex_b);

        $vertex_c = new Vertex($this->graph);
        $vertex_d = new Vertex($this->graph);

        $replacement_map = [
            $vertex_a->getId() => $vertex_c,
            $vertex_b->getId() => $vertex_d,
        ];

        $edge->replaceVerticesFromMap($replacement_map);

        $this->assertEquals([$vertex_c, $vertex_d], $edge->getVertices()->all());
    }

    /**
     * @covers PHGraph\Edge::directed
     *
     * @return void
     */
    public function testDirectedTrue(): void
    {
        $vertex_a = new Vertex($this->graph);
        $vertex_b = new Vertex($this->graph);
        $edge = new Edge($vertex_a, $vertex_b, Edge::DIRECTED);

        $this->assertTrue($edge->directed());
    }

    /**
     * @covers PHGraph\Edge::directed
     *
     * @return void
     */
    public function testDirectedFalse(): void
    {
        $vertex_a = new Vertex($this->graph);
        $vertex_b = new Vertex($this->graph);
        $edge = new Edge($vertex_a, $vertex_b, Edge::UNDIRECTED);

        $this->assertFalse($edge->directed());
    }

    /**
     * @covers PHGraph\Edge::loop
     *
     * @return void
     */
    public function testLoopTrue(): void
    {
        $vertex = new Vertex($this->graph);
        $edge = new Edge($vertex, $vertex);

        $this->assertTrue($edge->loop());
    }

    /**
     * @covers PHGraph\Edge::loop
     *
     * @return void
     */
    public function testLoopFalse(): void
    {
        $vertex_a = new Vertex($this->graph);
        $vertex_b = new Vertex($this->graph);
        $edge = new Edge($vertex_a, $vertex_b);

        $this->assertFalse($edge->loop());
    }
}
