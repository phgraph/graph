<?php

namespace Tests;

use PHGraph\Graph;
use PHGraph\Support\EdgeCollection;
use PHGraph\Vertex;
use PHGraph\Walk;
use PHPUnit\Framework\TestCase;

class WalkTest extends TestCase
{
    /**
     * @covers PHGraph\Walk::__construct
     *
     * @return void
     */
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(Walk::class, new Walk(new Vertex(new Graph), []));
    }

    /**
     * @covers PHGraph\Walk::__construct
     *
     * @return void
     */
    public function testInstantiationWithPath(): void
    {
        $graph = new Graph;
        $start = $graph->newVertex();
        $start->createEdgeTo($graph->newVertex());

        $this->assertInstanceOf(Walk::class, new Walk($start, $graph->getEdges()));
    }

    /**
     * @covers PHGraph\Walk::getGraph
     *
     * @return void
     */
    public function testGetGraphIsOriginalGraph(): void
    {
        $graph = new Graph;
        $start = $graph->newVertex();
        $start->createEdgeTo($graph->newVertex());
        $walk = new Walk($start, $graph->getEdges());

        $this->assertSame($graph, $walk->getGraph());
    }

    /**
     * @covers PHGraph\Walk::createGraph
     *
     * @return void
     */
    public function testCreateGraphIsNotOriginalGraph(): void
    {
        $graph = new Graph;
        $start = $graph->newVertex();
        $start->createEdgeTo($graph->newVertex());
        $walk = new Walk($start, $graph->getEdges());

        $this->assertNotSame($graph, $walk->createGraph());
    }

    /**
     * @covers PHGraph\Walk::createGraph
     *
     * @return void
     */
    public function testCreateGraphCaches(): void
    {
        $graph = new Graph;
        $start = $graph->newVertex();
        $start->createEdgeTo($graph->newVertex());
        $walk = new Walk($start, $graph->getEdges());
        $new_graph = $walk->createGraph();

        $this->assertSame($new_graph, $walk->createGraph());
    }

    /**
     * @covers PHGraph\Walk::getEdges
     *
     * @return void
     */
    public function testGetEdges(): void
    {
        $graph = new Graph;
        $start = $graph->newVertex();
        $edge = $start->createEdgeTo($graph->newVertex());
        $walk = new Walk($start, $graph->getEdges());
        $new_graph = $walk->createGraph();

        $this->assertEqualsCanonicalizing([$edge], $walk->getEdges());
    }

    /**
     * @covers PHGraph\Walk::getVertices
     *
     * @return void
     */
    public function testGetVertices(): void
    {
        $graph = new Graph;
        $start = $graph->newVertex();
        $v2 = $graph->newVertex();
        $start->createEdgeTo($v2);
        $walk = new Walk($start, $graph->getEdges());
        $new_graph = $walk->createGraph();

        $this->assertEqualsCanonicalizing([$start, $v2], $walk->getVertices());
    }

    /**
     * @covers PHGraph\Walk::getAlternatingSequence
     *
     * @return void
     */
    public function testGetAlternatingSequence(): void
    {
        $graph = new Graph;
        $start = $graph->newVertex();
        $v2 = $graph->newVertex();
        $edge = $start->createEdgeTo($v2);
        $walk = new Walk($start, $graph->getEdges());
        $new_graph = $walk->createGraph();

        $this->assertEquals([$start, $edge, $v2], $walk->getAlternatingSequence());
    }
}
