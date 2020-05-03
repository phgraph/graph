<?php

namespace Tests\ShortestPath;

use OutOfBoundsException;
use PHGraph\Exception\NegativeCycleException;
use PHGraph\Graph;
use PHGraph\ShortestPath\MooreBellmanFord;
use PHGraph\Vertex;
use PHGraph\Walk;
use PHPUnit\Framework\TestCase;
use UnderflowException;

class MooreBellmanFordTest extends TestCase
{
    /**
     * @covers PHGraph\ShortestPath\MooreBellmanFord::__construct
     *
     * @return void
     */
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(MooreBellmanFord::class, new MooreBellmanFord(new Vertex(new Graph)));
    }

    /**
     * @covers PHGraph\ShortestPath\MooreBellmanFord::getWalkTo
     *
     * @return void
     */
    public function testGetWalkToReturnsWalk(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_a->createEdgeTo($vertex_b);

        $bf = new MooreBellmanFord($vertex_a);
        $this->assertInstanceOf(Walk::class, $bf->getWalkTo($vertex_b));
    }

    /**
     * @covers PHGraph\ShortestPath\MooreBellmanFord::hasVertex
     *
     * @return void
     */
    public function testHasVertexTrue(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_a->createEdgeTo($vertex_b);

        $bf = new MooreBellmanFord($vertex_a);
        $this->assertTrue($bf->hasVertex($vertex_b));
    }

    /**
     * @covers PHGraph\ShortestPath\MooreBellmanFord::hasVertex
     *
     * @return void
     */
    public function testHasVertexFalse(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_b->createEdgeTo($vertex_a);

        $bf = new MooreBellmanFord($vertex_a);
        $this->assertFalse($bf->hasVertex($vertex_b));
    }

    /**
     * @covers PHGraph\ShortestPath\MooreBellmanFord::createGraph
     *
     * @return void
     */
    public function testCreateGraphReturnsGraph(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_a->createEdgeTo($vertex_b);

        $bf = new MooreBellmanFord($vertex_a);
        $this->assertInstanceOf(Graph::class, $bf->createGraph());
    }

    /**
     * @covers PHGraph\ShortestPath\MooreBellmanFord::getDistance
     *
     * @return void
     */
    public function testGetDistance(): void
    {
        $vertices = [];
        $graph = new Graph;
        for ($i = 0; $i < 50; $i++) {
            $vertices[$i] = new Vertex($graph);
            if ($i !== 0) {
                $edge = $vertices[$i]->createEdge($vertices[$i - 1]);
                $edge->setAttribute('weight', 1);
            }
        }

        $bf = new MooreBellmanFord($vertices[0]);

        $this->assertEquals(45, $bf->getDistance($vertices[45]));
    }

    /**
     * @covers PHGraph\ShortestPath\MooreBellmanFord::getDistance
     *
     * @return void
     */
    public function testGetDistanceThrowsOutOfBoundsIfVertexNotReachable(): void
    {
        $this->expectException(OutOfBoundsException::class);

        $vertices = [];
        $graph = new Graph;
        for ($i = 0; $i < 50; $i++) {
            $vertices[$i] = new Vertex($graph);
            if ($i !== 0) {
                $vertices[$i]->createEdge($vertices[$i - 1]);
            }
        }
        $vertices[51] = new Vertex($graph);

        $bf = new MooreBellmanFord($vertices[0]);

        $bf->getDistance($vertices[51]);
    }

    /**
     * @covers PHGraph\ShortestPath\MooreBellmanFord::getEdgesTo
     *
     * @return void
     */
    public function testGetEdgesToThrowsOutOfBoundsIfVerticesOnDifferentGraphs(): void
    {
        $this->expectException(OutOfBoundsException::class);

        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex(new Graph);

        $bf = new MooreBellmanFord($vertex_a);

        $bf->getEdgesTo($vertex_b);
    }

    /**
     * @covers PHGraph\ShortestPath\MooreBellmanFord::getEdgesTo
     *
     * @return void
     */
    public function testGetEdgesToThrowsOutOfBoundsIfVerticesArentConnected(): void
    {
        $this->expectException(OutOfBoundsException::class);

        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);

        $bf = new MooreBellmanFord($vertex_a);

        $bf->getEdgesTo($vertex_b);
    }

    /**
     * @covers PHGraph\ShortestPath\MooreBellmanFord::getEdgesTo
     *
     * @return void
     */
    public function testGetEdgesIsArray(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_a->createEdgeTo($vertex_b);

        $bf = new MooreBellmanFord($vertex_a);

        $this->assertIsArray($bf->getEdgesTo($vertex_b));
    }

    /**
     * @covers PHGraph\ShortestPath\MooreBellmanFord::getEdgesTo
     *
     * @return void
     */
    public function testGetEdgesTakesShorterPath(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_c = new Vertex($graph);
        $vertex_d = new Vertex($graph);
        $vertex_e = new Vertex($graph);

        $edge_a = $vertex_a->createEdge($vertex_b);
        $edge_b = $vertex_e->createEdge($vertex_b);

        $vertex_a->createEdge($vertex_c);
        $vertex_d->createEdge($vertex_c);
        $vertex_d->createEdge($vertex_e);

        $bf = new MooreBellmanFord($vertex_a);

        $this->assertEquals([
            $edge_a->getId() => $edge_a,
            $edge_b->getId() => $edge_b,
        ], $bf->getEdgesTo($vertex_e));
    }

    /**
     * @covers PHGraph\ShortestPath\MooreBellmanFord::getEdgesTo
     *
     * @return void
     */
    public function testGetEdgesToSameVertexIsEmpty(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);

        $bf = new MooreBellmanFord($vertex_a);

        $this->assertEmpty($bf->getEdgesTo($vertex_a));
    }

    /**
     * @covers PHGraph\ShortestPath\MooreBellmanFord::getDistanceMap
     *
     * @return void
     */
    public function testGetDistanceMap(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $edge = $vertex_a->createEdgeTo($vertex_b);
        $edge->setAttribute('weight', 1);

        $bf = new MooreBellmanFord($vertex_a);

        $this->assertEquals([
            $vertex_a->getId() => 0.0,
            $vertex_b->getId() => 1.0,
        ], $bf->getDistanceMap());
    }

    /**
     * @covers PHGraph\ShortestPath\MooreBellmanFord::getDistanceMap
     *
     * @return void
     */
    public function testGetDistanceMapIgnoresUnreachableVertices(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_c = new Vertex($graph);
        $edge = $vertex_a->createEdgeTo($vertex_b);
        $edge->setAttribute('weight', 1);

        $bf = new MooreBellmanFord($vertex_a);

        $this->assertEquals([
            $vertex_a->getId() => 0.0,
            $vertex_b->getId() => 1.0,
        ], $bf->getDistanceMap());
    }

    /**
     * @covers PHGraph\ShortestPath\MooreBellmanFord::getEdges
     *
     * @return void
     */
    public function testGetEdges(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $edge = $vertex_a->createEdgeTo($vertex_b);

        $bf = new MooreBellmanFord($vertex_a);

        $this->assertEquals([
            $edge->getId() => $edge,
        ], $bf->getEdges());
    }

    /**
     * @covers PHGraph\ShortestPath\MooreBellmanFord::getEdges
     *
     * @return void
     */
    public function testGetEdgesCached(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_a->createEdgeTo($vertex_b);

        $bf = new MooreBellmanFord($vertex_a);

        $edges = $bf->getEdges();

        $vertex_c = new Vertex($graph);
        $vertex_a->createEdgeTo($vertex_c);

        $this->assertEquals($edges, $bf->getEdges());
    }

    /**
     * @covers PHGraph\ShortestPath\MooreBellmanFord::getEdges
     *
     * @return void
     */
    public function testGetEdgesPicksSmallerWeight(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $edge_a = $vertex_a->createEdgeTo($vertex_b);
        $edge_b = $vertex_a->createEdgeTo($vertex_b);

        $edge_a->setAttribute('weight', 1);
        $edge_b->setAttribute('weight', 0.5);

        $bf = new MooreBellmanFord($vertex_a);

        $this->assertEquals([
            $edge_b->getId() => $edge_b,
        ], $bf->getEdges());
    }

    /**
     * @covers PHGraph\ShortestPath\MooreBellmanFord::getEdges
     *
     * @return void
     */
    public function testGetEdgesPicksSmallerWeightMultiConnected(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_c = new Vertex($graph);
        $edge_a = $vertex_a->createEdgeTo($vertex_b);
        $edge_b = $vertex_a->createEdgeTo($vertex_b);
        $edge_c = $vertex_a->createEdge($vertex_c);
        $edge_d = $vertex_b->createEdge($vertex_c);

        $edge_a->setAttribute('weight', 1);
        $edge_b->setAttribute('weight', 0.5);
        $edge_c->setAttribute('weight', 4);
        $edge_d->setAttribute('weight', 5);

        $bf = new MooreBellmanFord($vertex_a);

        $this->assertEqualsCanonicalizing([$edge_b, $edge_c], $bf->getEdges());
    }

    /**
     * @covers PHGraph\ShortestPath\MooreBellmanFord::getEdges
     *
     * @return void
     */
    public function testGetEdgesPicksSmallerWeightUnreachableVertices(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_c = new Vertex($graph);
        $edge_a = $vertex_a->createEdgeTo($vertex_b);
        $edge_b = $vertex_a->createEdgeTo($vertex_b);

        $edge_a->setAttribute('weight', 1);
        $edge_b->setAttribute('weight', 0.5);

        $bf = new MooreBellmanFord($vertex_a);

        $this->assertEquals([
            $edge_b->getId() => $edge_b,
        ], $bf->getEdges());
    }

    /**
     * @covers PHGraph\ShortestPath\MooreBellmanFord::getEdges
     *
     * @return void
     */
    public function testGetEdgesThrowsNegativeCycleException(): void
    {
        $this->expectException(NegativeCycleException::class);

        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_c = new Vertex($graph);
        $vertex_d = new Vertex($graph);
        $vertex_e = new Vertex($graph);

        $vertex_a->createEdgeTo($vertex_b, ['weight' => 2]);
        $vertex_b->createEdgeTo($vertex_e, ['weight' => 2]);
        $vertex_b->createEdgeTo($vertex_c, ['weight' => -2]);
        $vertex_b->createEdgeTo($vertex_c, ['weight' => -3]);
        $vertex_c->createEdgeTo($vertex_d, ['weight' => 2]);
        $vertex_d->createEdgeTo($vertex_b, ['weight' => -2]);

        $bf = new MooreBellmanFord($vertex_a);

        $bf->getEdges();
    }

    /**
     * @covers PHGraph\ShortestPath\MooreBellmanFord::getCycleNegative
     *
     * @return void
     */
    public function testGetCycleNegativeReturnsWalk(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_c = new Vertex($graph);
        $vertex_d = new Vertex($graph);
        $vertex_e = new Vertex($graph);
        $edge_a = $vertex_a->createEdgeTo($vertex_b);
        $edge_b = $vertex_b->createEdgeTo($vertex_e);
        $edge_c = $vertex_b->createEdgeTo($vertex_c);
        $edge_d = $vertex_c->createEdgeTo($vertex_d);
        $edge_e = $vertex_d->createEdgeTo($vertex_b);

        $edge_a->setAttribute('weight', 2);
        $edge_b->setAttribute('weight', 2);
        $edge_c->setAttribute('weight', -2);
        $edge_d->setAttribute('weight', 2);
        $edge_e->setAttribute('weight', -2);

        $bf = new MooreBellmanFord($vertex_a);

        $this->assertInstanceOf(Walk::class, $bf->getCycleNegative());
    }

    /**
     * @covers PHGraph\ShortestPath\MooreBellmanFord::getCycleNegative
     *
     * @return void
     */
    public function testGetCycleNegativeThrowsUnderflowException(): void
    {
        $this->expectException(UnderflowException::class);

        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_c = new Vertex($graph);
        $vertex_d = new Vertex($graph);
        $vertex_e = new Vertex($graph);
        $edge_a = $vertex_a->createEdgeTo($vertex_b);
        $edge_b = $vertex_b->createEdgeTo($vertex_e);
        $edge_c = $vertex_b->createEdgeTo($vertex_c);
        $edge_d = $vertex_c->createEdgeTo($vertex_d);
        $edge_e = $vertex_d->createEdgeTo($vertex_b);

        $edge_a->setAttribute('weight', 2);
        $edge_b->setAttribute('weight', 2);
        $edge_c->setAttribute('weight', 2);
        $edge_d->setAttribute('weight', 2);
        $edge_e->setAttribute('weight', 2);

        $bf = new MooreBellmanFord($vertex_a);

        $bf->getCycleNegative();
    }
}
