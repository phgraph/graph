<?php

namespace Tests;

use PHGraph\Graph;
use PHGraph\Vertex;
use PHPUnit\Framework\TestCase;
use UnderflowException;
use UnexpectedValueException;

class GraphTest extends TestCase
{
    /**
     * @covers PHGraph\Graph::getDegree
     *
     * @return void
     */
    public function testGetDegree(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdgeTo($v2);

        $this->assertEquals(1, $graph->getDegree());
    }

    /**
     * @covers PHGraph\Graph::getDegree
     *
     * @return void
     */
    public function testGetDegreeEmpty(): void
    {
        $this->expectException(UnderflowException::class);

        $graph = new Graph;

        $graph->getDegree();
    }

    /**
     * @covers PHGraph\Graph::getDegree
     *
     * @return void
     */
    public function testGetDegreeIrregular(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdgeTo($v2);
        $v1->createEdge($v1);

        $graph->getDegree();
    }

    /**
     * @covers PHGraph\Graph::getDegreeMin
     *
     * @return void
     */
    public function testGetDegreeMin(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdge($v2);

        $this->assertEquals(1, $graph->getDegreeMin());
    }

    /**
     * @covers PHGraph\Graph::getDegreeMin
     *
     * @return void
     */
    public function testGetDegreeMinEmpty(): void
    {
        $this->expectException(UnderflowException::class);

        $graph = new Graph;

        $graph->getDegreeMin();
    }

    /**
     * @covers PHGraph\Graph::getDegreeMax
     *
     * @return void
     */
    public function testGetDegreeMax(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdgeTo($v2);

        $this->assertEquals(1, $graph->getDegreeMax());
    }

    /**
     * @covers PHGraph\Graph::getDegreeMax
     *
     * @return void
     */
    public function testGetDegreeMaxEmpty(): void
    {
        $this->expectException(UnderflowException::class);

        $graph = new Graph;

        $graph->getDegreeMax();
    }

    /**
     * @covers PHGraph\Graph::getVertices
     *
     * @return void
     */
    public function testGetVerticesIsArray(): void
    {
        $graph = new Graph;

        $this->assertIsArray($graph->getVertices());
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

        $this->assertContains($vertex, $graph->getVertices());
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

        $this->assertContains($vertex, $graph->getVertices());
    }

    /**
     * @covers PHGraph\Graph::removeVertex
     *
     * @return void
     */
    public function testRemoveVertex(): void
    {
        $graph = new Graph;
        $vertex = $graph->newVertex();
        $graph->removeVertex($vertex);

        $this->assertNotContains($vertex, $graph->getVertices());
    }

    /**
     * @covers PHGraph\Graph::getEdges
     *
     * @return void
     */
    public function testGetEdgesIsArray(): void
    {
        $graph = new Graph;

        $this->assertIsArray($graph->getEdges());
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

        $this->assertEqualsCanonicalizing([$edge], $graph->getEdges());
    }

    /**
     * @covers PHGraph\Graph::newFromEdges
     *
     * @return void
     */
    public function testNewFromEdgesIsGraph(): void
    {
        $graph = new Graph;

        $this->assertInstanceOf(Graph::class, $graph->newFromEdges([]));
    }

    /**
     * @covers PHGraph\Graph::newFromEdges
     *
     * @return void
     */
    public function testNewFromEdgesIsNotSameGraph(): void
    {
        $graph = new Graph;

        $this->assertNotSame($graph, $graph->newFromEdges([]));
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

        $this->assertSame('test', $graph->newFromEdges([])->getAttribute('testing'));
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

        $new_graph_edges = $graph->newFromEdges([$edge])->getEdges();

        $this->assertNotSame($edge, reset($new_graph_edges));
    }

    /**
     * @covers PHGraph\Graph::newFromVertices
     *
     * @return void
     */
    public function testNewFromVerticessIsGraph(): void
    {
        $graph = new Graph;

        $this->assertInstanceOf(Graph::class, $graph->newFromVertices([]));
    }

    /**
     * @covers PHGraph\Graph::newFromVertices
     *
     * @return void
     */
    public function testNewFromVerticessIsNotSameGraph(): void
    {
        $graph = new Graph;

        $this->assertNotSame($graph, $graph->newFromVertices([]));
    }

    /**
     * @covers PHGraph\Graph::newFromVertices
     *
     * @return void
     */
    public function testNewFromVerticessKeepsAtrributes(): void
    {
        $graph = new Graph;
        $graph->setAttribute('testing', 'test');

        $this->assertSame('test', $graph->newFromVertices([])->getAttribute('testing'));
    }

    /**
     * @covers PHGraph\Graph::newFromVertices
     *
     * @return void
     */
    public function testNewFromVerticessHasNewEdges(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $edge = $vertex_a->createEdge($vertex_b);

        $new_graph_edges = $graph->newFromVertices([$vertex_a, $vertex_b])->getEdges();

        $this->assertNotSame($edge, reset($new_graph_edges));
    }

    /**
     * @covers PHGraph\Graph::getNumberOfGroups
     *
     * @return void
     */
    public function testGetNumberOfGroupsEmpty(): void
    {
        $graph = new Graph;

        $this->assertEquals(0, $graph->getNumberOfGroups());
    }

    /**
     * @covers PHGraph\Graph::getNumberOfGroups
     *
     * @return void
     */
    public function testGetNumberOfGroups(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->setAttribute('group', 0);
        $v2->setAttribute('group', 1);

        $this->assertEquals(2, $graph->getNumberOfGroups());
    }

    /**
     * @covers PHGraph\Graph::isBipartit
     *
     * @return void
     */
    public function testIsBipartitTrue(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v3 = new Vertex($graph);
        $v4 = new Vertex($graph);
        $v1->setAttribute('group', 0);
        $v3->setAttribute('group', 0);
        $v2->setAttribute('group', 1);
        $v4->setAttribute('group', 1);
        $v1->createEdge($v2);
        $v1->createEdge($v4);
        $v2->createEdge($v3);

        $this->assertTrue($graph->isBipartit());
    }

    /**
     * @covers PHGraph\Graph::isBipartit
     *
     * @return void
     */
    public function testIsBipartitFalse(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v3 = new Vertex($graph);
        $v4 = new Vertex($graph);
        $v1->setAttribute('group', 0);
        $v3->setAttribute('group', 1);
        $v2->setAttribute('group', 1);
        $v4->setAttribute('group', 1);
        $v1->createEdge($v2);
        $v1->createEdge($v4);
        $v2->createEdge($v3);

        $this->assertFalse($graph->isBipartit());
    }

    /**
     * @covers PHGraph\Graph::isBipartit
     *
     * @return void
     */
    public function testIsBipartitFalseMoreThanTwoGroups(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v3 = new Vertex($graph);
        $v4 = new Vertex($graph);
        $v1->setAttribute('group', 0);
        $v3->setAttribute('group', 0);
        $v2->setAttribute('group', 1);
        $v4->setAttribute('group', 3);
        $v1->createEdge($v2);
        $v1->createEdge($v4);
        $v2->createEdge($v3);

        $this->assertFalse($graph->isBipartit());
    }

    /**
     * @covers PHGraph\Graph::getGroups
     *
     * @return void
     */
    public function testGetGroups(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v3 = new Vertex($graph);
        $v4 = new Vertex($graph);
        $v1->setAttribute('group', 0);
        $v3->setAttribute('group', 0);
        $v2->setAttribute('group', 1);
        $v4->setAttribute('group', 3);
        $v1->createEdge($v2);
        $v1->createEdge($v4);
        $v2->createEdge($v3);

        $this->assertEquals([0, 1, 3], $graph->getGroups());
    }

    /**
     * @covers PHGraph\Graph::getVerticesGroup
     *
     * @return void
     */
    public function testGetVerticesGroup(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v3 = new Vertex($graph);
        $v4 = new Vertex($graph);
        $v1->setAttribute('group', 0);
        $v3->setAttribute('group', 0);
        $v2->setAttribute('group', 1);
        $v4->setAttribute('group', 3);
        $v1->createEdge($v2);
        $v1->createEdge($v4);
        $v2->createEdge($v3);

        $this->assertEqualsCanonicalizing([$v1, $v3], $graph->getVerticesGroup(0));
    }

    /**
     * @covers PHGraph\Graph::hasDirected
     *
     * @return void
     */
    public function testHasDirectedTrue(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $edge = $v1->createEdgeTo($v2);

        $this->assertTrue($graph->hasDirected());
    }

    /**
     * @covers PHGraph\Graph::hasDirected
     *
     * @return void
     */
    public function testHasDirectedFalse(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $edge = $v1->createEdge($v2);

        $this->assertFalse($graph->hasDirected());
    }

    /**
     * @covers PHGraph\Graph::hasUndirected
     *
     * @return void
     */
    public function testHasUndirectedTrue(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $edge = $v1->createEdge($v2);

        $this->assertTrue($graph->hasUndirected());
    }

    /**
     * @covers PHGraph\Graph::hasUndirected
     *
     * @return void
     */
    public function testHasUndirectedFalse(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $edge = $v1->createEdgeTo($v2);

        $this->assertFalse($graph->hasUndirected());
    }

    /**
     * @covers PHGraph\Graph::isMixed
     *
     * @return void
     */
    public function testIsMixedTrue(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $edge1 = $v1->createEdgeTo($v2);
        $edge2 = $v1->createEdge($v2);

        $this->assertTrue($graph->isMixed());
    }

    /**
     * @covers PHGraph\Graph::isMixed
     *
     * @return void
     */
    public function testIsMixedFalseDirected(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $edge = $v1->createEdgeTo($v2);

        $this->assertFalse($graph->isMixed());
    }

    /**
     * @covers PHGraph\Graph::isMixed
     *
     * @return void
     */
    public function testIsMixedFalseUndirected(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $edge = $v1->createEdge($v2);

        $this->assertFalse($graph->isMixed());
    }

    /**
     * @covers PHGraph\Graph::isBalanced
     *
     * @return void
     */
    public function testIsBalancedTrue(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdge($v2);

        $this->assertTrue($graph->isBalanced());
    }

    /**
     * @covers PHGraph\Graph::isBalanced
     *
     * @return void
     */
    public function testIsBalancedFalse(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdgeTo($v2);

        $this->assertFalse($graph->isBalanced());
    }

    /**
     * @covers PHGraph\Graph::isComplete
     *
     * @return void
     */
    public function testIsCompleteEmptyTrue(): void
    {
        $graph = new Graph;

        $this->assertTrue($graph->isComplete());
    }

    /**
     * @covers PHGraph\Graph::isComplete
     *
     * @return void
     */
    public function testIsCompleteOneVertexTrue(): void
    {
        $graph = new Graph;
        $graph->newVertex();

        $this->assertTrue($graph->isComplete());
    }

    /**
     * @covers PHGraph\Graph::isComplete
     *
     * @return void
     */
    public function testIsCompleteMultipleVertexTrue(): void
    {
        $graph = new Graph;
        $v1 = $graph->newVertex();
        $v2 = $graph->newVertex();
        $v3 = $graph->newVertex();
        $v4 = $graph->newVertex();
        $v1->createEdge($v2);
        $v1->createEdge($v3);
        $v1->createEdge($v4);
        $v2->createEdge($v3);
        $v2->createEdge($v4);
        $v3->createEdge($v4);

        $this->assertTrue($graph->isComplete());
    }

    /**
     * @covers PHGraph\Graph::isComplete
     *
     * @return void
     */
    public function testIsCompleteMultipleVertexFalse(): void
    {
        $graph = new Graph;
        $v1 = $graph->newVertex();
        $v2 = $graph->newVertex();
        $v3 = $graph->newVertex();
        $v4 = $graph->newVertex();
        $v1->createEdge($v2);
        $v1->createEdge($v3);
        $v1->createEdge($v4);
        $v2->createEdge($v3);
        $v2->createEdge($v4);

        $this->assertFalse($graph->isComplete());
    }

    /**
     * @covers PHGraph\Graph::isRegular
     *
     * @return void
     */
    public function testIsRegularEmptyTrue(): void
    {
        $graph = new Graph;

        $this->assertTrue($graph->isRegular());
    }

    /**
     * @covers PHGraph\Graph::isRegular
     *
     * @return void
     */
    public function testIsRegularTrue(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdgeTo($v2);
        $v1->createEdge($v2);

        $this->assertTrue($graph->isRegular());
    }

    /**
     * @covers PHGraph\Graph::isRegular
     *
     * @return void
     */
    public function testIsRegularFalse(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdgeTo($v2);
        $v1->createEdgeTo($v1);

        $this->assertFalse($graph->isRegular());
    }
}
