<?php

namespace Tests;

use PHGraph\Support\EdgeCollection;
use PHGraph\Graph;
use PHGraph\Vertex;
use PHGraph\Edge;
use PHGraph\Support\VertexCollection;
use PHPUnit\Framework\TestCase;

class GraphTest extends TestCase
{
    /**
     * @covers PHGraph\Graph::__construct
     *
     * @return void
     */
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(Graph::class, new Graph);
    }

    /**
     * @covers PHGraph\Graph::addVertex
     *
     * @return void
     */
    public function testAddVertex(): void
    {
        $graph = new Graph;
        $vertex = new Vertex(new Graph);

        $graph->addVertex($vertex);

        $this->assertTrue($graph->getVertices()->contains($vertex));
    }

    /**
     * @covers PHGraph\Graph::newVertex
     *
     * @return void
     */
    public function testNewVertexReturnsVertex(): void
    {
        $graph = new Graph;

        $this->assertInstanceOf(Vertex::class, $graph->newVertex());
    }

    /**
     * @covers PHGraph\Graph::newVertex
     *
     * @return void
     */
    public function testNewVertexAddsVertexToGraph(): void
    {
        $graph = new Graph;
        $vertex = $graph->newVertex();

        $this->assertTrue($graph->getVertices()->contains($vertex));
    }

    /**
     * @covers PHGraph\Graph::getVertices
     *
     * @return void
     */
    public function testGetVerticesIsVertexCollection(): void
    {
        $graph = new Graph;

        $this->assertInstanceOf(VertexCollection::class, $graph->getVertices());
    }

    /**
     * @covers PHGraph\Graph::getEdges
     *
     * @return void
     */
    public function testGetEdgesIsEdgeCollection(): void
    {
        $graph = new Graph;

        $this->assertInstanceOf(EdgeCollection::class, $graph->getEdges());
    }

    /**
     * @covers PHGraph\Graph::getEdges
     *
     * @return void
     */
    public function testGetEdgesComesFromVertices(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $edge = $vertex_a->createEdge($vertex_b);

        $this->assertEquals([$edge], $graph->getEdges()->all());
    }

    /**
     * @covers PHGraph\Graph::newFromEdges
     *
     * @return void
     */
    public function testNewFromEdgesIsGraph(): void
    {
        $graph = new Graph;

        $this->assertInstanceOf(Graph::class, $graph->newFromEdges(new EdgeCollection));
    }

    /**
     * @covers PHGraph\Graph::newFromEdges
     *
     * @return void
     */
    public function testNewFromEdgesIsNotSameGraph(): void
    {
        $graph = new Graph;

        $this->assertNotSame($graph, $graph->newFromEdges(new EdgeCollection));
    }

    /**
     * @covers PHGraph\Graph::newFromEdges
     *
     * @return void
     */
    public function testNewFromEdgesKeepsAtrributes(): void
    {
        $graph = new Graph;
        $graph->setAttribute('testing', 'test');

        $this->assertNotSame('test', $graph->newFromEdges(new EdgeCollection)->getAttribute('testing'));
    }

    /**
     * @covers PHGraph\Graph::newFromEdges
     *
     * @return void
     */
    public function testNewFromEdgesHasNewEdges(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $edge = $vertex_a->createEdge($vertex_b);

        $this->assertNotSame($edge, array_values($graph->newFromEdges(new EdgeCollection([$edge]))->getEdges()->all())[0]);
    }
}
