<?php

namespace Tests;

use Exception;
use PHGraph\Edge;
use PHGraph\Graph;
use PHGraph\Vertex;
use PHPUnit\Framework\TestCase;

class VertexTest extends TestCase
{
    /**
     * @covers PHGraph\Vertex::__construct
     *
     * @return void
     */
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(Vertex::class, new Vertex(new Graph));
    }

    /**
     * @covers PHGraph\Vertex::getId
     *
     * @return void
     */
    public function testGetIdUnique(): void
    {
        $vertex_a = new Vertex(new Graph);
        $vertex_b = clone $vertex_a;

        $this->assertNotEquals($vertex_a->getId(), $vertex_b->getId());
    }

    /**
     * @covers PHGraph\Vertex::setGraph
     *
     * @return void
     */
    public function testSetGraph(): void
    {
        $vertex = new Vertex(new Graph);
        $graph = new Graph;
        $vertex->setGraph($graph);

        $this->assertSame($graph, $vertex->getGraph());
    }

    /**
     * @covers PHGraph\Vertex::setGraph
     *
     * @return void
     */
    public function testSetGraphAlreadySet(): void
    {
        $graph = new Graph;
        $vertex = new Vertex($graph);
        $vertex->setGraph($graph);

        $this->assertSame($graph, $vertex->getGraph());
    }

    /**
     * @covers PHGraph\Vertex::getGraph
     *
     * @return void
     */
    public function testGetGraphIsGraph(): void
    {
        $vertex = new Vertex(new Graph);

        $this->assertInstanceOf(Graph::class, $vertex->getGraph());
    }

    /**
     * @covers PHGraph\Vertex::getEdges
     *
     * @return void
     */
    public function testGetEdges(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_c = new Vertex($graph);
        $vertex_d = new Vertex($graph);

        $edge_a = $vertex_a->createEdge($vertex_a);
        $edge_b = $vertex_b->createEdgeTo($vertex_a);
        $edge_c = $vertex_a->createEdge($vertex_c);
        $edge_d = $vertex_d->createEdgeTo($vertex_a);

        $this->assertEquals([$edge_a, $edge_b, $edge_c, $edge_d], $vertex_a->getEdges()->all());
    }

    /**
     * @covers PHGraph\Vertex::getEdgesIn
     *
     * @return void
     */
    public function testGetEdgesIn(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_c = new Vertex($graph);
        $vertex_d = new Vertex($graph);

        $edge_a = $vertex_a->createEdge($vertex_b);
        $edge_b = $vertex_a->createEdge($vertex_c);
        $edge_c = $vertex_d->createEdgeTo($vertex_a);

        $this->assertEquals([$edge_a, $edge_b, $edge_c], $vertex_a->getEdgesIn()->all());
    }

    /**
     * @covers PHGraph\Vertex::getEdgesIn
     *
     * @return void
     */
    public function testGetEdgesInWithLoop(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_c = new Vertex($graph);
        $vertex_d = new Vertex($graph);

        $edge_a = $vertex_a->createEdge($vertex_a);
        $edge_b = $vertex_a->createEdge($vertex_b);
        $edge_c = $vertex_a->createEdge($vertex_c);
        $edge_d = $vertex_d->createEdgeTo($vertex_a);

        $this->assertEquals([$edge_a, $edge_b, $edge_c, $edge_d], $vertex_a->getEdgesIn()->all());
    }

    /**
     * @covers PHGraph\Vertex::getEdgesOut
     *
     * @return void
     */
    public function testGetEdgesOut(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_c = new Vertex($graph);
        $vertex_d = new Vertex($graph);

        $edge_a = $vertex_a->createEdge($vertex_b);
        $edge_b = $vertex_a->createEdge($vertex_c);
        $edge_c = $vertex_d->createEdgeTo($vertex_a);

        $this->assertEquals([$edge_a, $edge_b], $vertex_a->getEdgesOut()->all());
    }

    /**
     * @covers PHGraph\Vertex::getEdgesOut
     *
     * @return void
     */
    public function testGetEdgesOutWithLoop(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_c = new Vertex($graph);
        $vertex_d = new Vertex($graph);

        $edge_a = $vertex_a->createEdge($vertex_a);
        $edge_b = $vertex_a->createEdge($vertex_b);
        $edge_c = $vertex_a->createEdge($vertex_c);
        $edge_d = $vertex_d->createEdgeTo($vertex_a);

        $this->assertEquals([$edge_a, $edge_b, $edge_c], $vertex_a->getEdgesOut()->all());
    }

    /**
     * @covers PHGraph\Vertex::getVerticesFrom
     *
     * @return void
     */
    public function testGetVerticesFrom(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_c = new Vertex($graph);
        $vertex_d = new Vertex($graph);

        $vertex_a->createEdge($vertex_b);
        $vertex_a->createEdge($vertex_c);
        $vertex_d->createEdgeTo($vertex_a);

        $this->assertEquals([$vertex_b, $vertex_c, $vertex_d], $vertex_a->getVerticesFrom()->all());
    }

    /**
     * @covers PHGraph\Vertex::getVerticesFrom
     *
     * @return void
     */
    public function testGetVerticesFromWithLoop(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_c = new Vertex($graph);
        $vertex_d = new Vertex($graph);

        $vertex_a->createEdge($vertex_a);
        $vertex_a->createEdge($vertex_b);
        $vertex_a->createEdge($vertex_c);
        $vertex_d->createEdgeTo($vertex_a);

        $this->assertEquals([$vertex_a, $vertex_b, $vertex_c, $vertex_d], $vertex_a->getVerticesFrom()->all());
    }

    /**
     * @covers PHGraph\Vertex::getVerticesTo
     *
     * @return void
     */
    public function testGetVerticesTo(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_c = new Vertex($graph);
        $vertex_d = new Vertex($graph);

        $vertex_a->createEdge($vertex_b);
        $vertex_a->createEdge($vertex_c);
        $vertex_d->createEdgeTo($vertex_a);

        $this->assertEquals([$vertex_b, $vertex_c], $vertex_a->getVerticesTo()->all());
    }

    /**
     * @covers PHGraph\Vertex::getVerticesTo
     *
     * @return void
     */
    public function testGetVerticesToWithLoop(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_c = new Vertex($graph);
        $vertex_d = new Vertex($graph);

        $vertex_a->createEdge($vertex_a);
        $vertex_a->createEdge($vertex_b);
        $vertex_a->createEdge($vertex_c);
        $vertex_d->createEdgeTo($vertex_a);

        $this->assertEquals([$vertex_a, $vertex_b, $vertex_c], $vertex_a->getVerticesTo()->all());
    }

    /**
     * @covers PHGraph\Vertex::createEdge
     *
     * @return void
     */
    public function testCreateUndirectedEdge(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);

        $edge = $vertex_a->createEdge($vertex_b);

        $this->assertFalse($edge->isDirected());
    }

    /**
     * @covers PHGraph\Vertex::createEdgeTo
     *
     * @return void
     */
    public function testCreateDirectedEdge(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);

        $edge = $vertex_a->createEdgeTo($vertex_b);

        $this->assertTrue($edge->isDirected());
    }

    /**
     * @covers PHGraph\Vertex::addEdgeIn
     *
     * @return void
     */
    public function testAddEdgeInDirected(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);

        $edge = new Edge($vertex_a, $vertex_b, Edge::DIRECTED);

        $vertex_b->addEdgeIn($edge);

        $this->assertEquals([$edge], $vertex_b->getEdgesIn()->all());
    }

    /**
     * @covers PHGraph\Vertex::addEdgeIn
     *
     * @return void
     */
    public function testAddEdgeInUndirected(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);

        $edge = new Edge($vertex_a, $vertex_b, Edge::UNDIRECTED);

        $vertex_a->addEdgeIn($edge);

        $this->assertEquals([$edge], $vertex_a->getEdgesIn()->all());
    }

    /**
     * @covers PHGraph\Vertex::addEdgeIn
     *
     * @return void
     */
    public function testAddEdgeInNotValid(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);

        $edge = new Edge($vertex_a, $vertex_b, Edge::DIRECTED);

        $vertex_a->addEdgeIn($edge);

        $this->assertEquals([], $vertex_a->getEdgesIn()->all());
    }

    /**
     * @covers PHGraph\Vertex::addEdgeIn
     *
     * @return void
     */
    public function testAddEdgeInNotValidUnrelated(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_c = new Vertex($graph);

        $edge_a = new Edge($vertex_a, $vertex_b, Edge::UNDIRECTED);
        $edge_b = new Edge($vertex_a, $vertex_c, Edge::UNDIRECTED);

        $vertex_b->addEdgeIn($edge_b);

        $this->assertEquals([$edge_a], $vertex_b->getEdgesIn()->all());
    }

    /**
     * @covers PHGraph\Vertex::addEdgeOut
     *
     * @return void
     */
    public function testAddEdgeOutDirected(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);

        $edge = new Edge($vertex_a, $vertex_b, Edge::DIRECTED);

        $vertex_a->addEdgeOut($edge);

        $this->assertEquals([$edge], $vertex_a->getEdgesOut()->all());
    }

    /**
     * @covers PHGraph\Vertex::addEdgeOut
     *
     * @return void
     */
    public function testAddEdgeOutUndirected(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);

        $edge = new Edge($vertex_a, $vertex_b, Edge::UNDIRECTED);

        $vertex_a->addEdgeOut($edge);

        $this->assertEquals([$edge], $vertex_a->getEdgesOut()->all());
    }

    /**
     * @covers PHGraph\Vertex::addEdgeOut
     *
     * @return void
     */
    public function testAddEdgeOutNotValid(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);

        $edge = new Edge($vertex_a, $vertex_b, Edge::DIRECTED);

        $vertex_b->addEdgeOut($edge);

        $this->assertEquals([], $vertex_b->getEdgesOut()->all());
    }

    /**
     * @covers PHGraph\Vertex::addEdgeOut
     *
     * @return void
     */
    public function testAddEdgeOutNotValidUnrelated(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_c = new Vertex($graph);

        $edge_a = new Edge($vertex_a, $vertex_b, Edge::UNDIRECTED);
        $edge_b = new Edge($vertex_a, $vertex_c, Edge::UNDIRECTED);

        $vertex_b->addEdgeOut($edge_b);

        $this->assertEquals([$edge_a], $vertex_b->getEdgesOut()->all());
    }

    /**
     * @covers PHGraph\Vertex::removeEdge
     *
     * @return void
     */
    public function testRemoveEdge(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_c = new Vertex($graph);

        $edge_a = new Edge($vertex_a, $vertex_b, Edge::UNDIRECTED);
        $edge_b = new Edge($vertex_a, $vertex_c, Edge::UNDIRECTED);

        $vertex_b->removeEdge($edge_b);

        $this->assertEquals([$edge_a], $vertex_b->getEdges()->all());
    }

    /**
     * @covers PHGraph\Vertex::degree
     *
     * @return void
     */
    public function testDegreeEmpty(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);

        $this->assertEquals(0, $v1->degree());
    }

    /**
     * @covers PHGraph\Vertex::degree
     *
     * @return void
     */
    public function testDegree(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdge($v2);

        $this->assertEquals(1, $v1->degree());
    }

    /**
     * @covers PHGraph\Vertex::degree
     *
     * @return void
     */
    public function testDegreeLoop(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdge($v2);
        $v1->createEdge($v1);

        $this->assertEquals(3, $v1->degree());
    }

    /**
     * @covers PHGraph\Vertex::degreeIn
     *
     * @return void
     */
    public function testDegreeIn(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdge($v2);

        $this->assertEquals(1, $v1->degreeIn());
    }

    /**
     * @covers PHGraph\Vertex::degreeIn
     *
     * @return void
     */
    public function testDegreeInWithLoop(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdge($v2);
        $v1->createEdge($v1);

        $this->assertEquals(3, $v1->degreeIn());
    }

    /**
     * @covers PHGraph\Vertex::degreeOut
     *
     * @return void
     */
    public function testDegreeOut(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdge($v2);

        $this->assertEquals(1, $v1->degreeOut());
    }

    /**
     * @covers PHGraph\Vertex::degreeOut
     *
     * @return void
     */
    public function testDegreeOutWithLoop(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdge($v2);
        $v1->createEdge($v1);

        $this->assertEquals(3, $v1->degreeOut());
    }

    /**
     * @covers PHGraph\Vertex::isIsolated
     *
     * @return void
     */
    public function testIsolatedTrue(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);

        $this->assertTrue($v1->isIsolated());
    }

    /**
     * @covers PHGraph\Vertex::isIsolated
     *
     * @return void
     */
    public function testIsolatedFalse(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdgeTo($v2);

        $this->assertFalse($v1->isIsolated());
    }

    /**
     * @covers PHGraph\Vertex::isSink
     *
     * @return void
     */
    public function testSinkTrue(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdgeTo($v2);

        $this->assertTrue($v2->isSink());
    }

    /**
     * @covers PHGraph\Vertex::isSink
     *
     * @return void
     */
    public function testSinkFalse(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdgeTo($v2);

        $this->assertFalse($v1->isSink());
    }

    /**
     * @covers PHGraph\Vertex::isSource
     *
     * @return void
     */
    public function testSourceTrue(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdgeTo($v2);

        $this->assertTrue($v1->isSource());
    }

    /**
     * @covers PHGraph\Vertex::isSource
     *
     * @return void
     */
    public function testSourceFalse(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdgeTo($v2);

        $this->assertFalse($v2->isSource());
    }

    /**
     * @covers PHGraph\Vertex::destroy
     *
     * @return void
     */
    public function testDestroyRemovesEdges(): void
    {
        $graph = new Graph;

        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $edge = new Edge($vertex_a, $vertex_b);

        $vertex_a->destroy();

        $this->assertEmpty($vertex_a->getEdges());
    }

    /**
     * @covers PHGraph\Vertex::destroy
     *
     * @return void
     */
    public function testDestroyRemovesGraph(): void
    {
        $this->expectException(Exception::class);

        $graph = new Graph;

        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $edge = new Edge($vertex_a, $vertex_b);

        $vertex_b->destroy();

        $vertex_b->getGraph();
    }

    /**
     * @covers PHGraph\Vertex::__clone
     *
     * @return void
     */
    public function testCloneRemovesEdges(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);

        $edge = $vertex_a->createEdgeTo($vertex_b);

        $new_vertex = clone $vertex_a;

        $this->assertEmpty($new_vertex->getEdges());
    }

    /**
     * @covers PHGraph\Vertex::__get
     *
     * @return void
     */
    public function testGetWithInvalidThrowsException(): void
    {
        $this->expectException(Exception::class);

        $graph = new Graph;

        $vertex_a = new Vertex($graph);

        $vertex_a->destroy();

        $vertex_a->getGraph();
    }
}
