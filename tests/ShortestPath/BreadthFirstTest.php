<?php

namespace Tests\ShortestPath;

use OutOfBoundsException;
use PHGraph\Graph;
use PHGraph\ShortestPath\BreadthFirst;
use PHGraph\Vertex;
use PHGraph\Walk;
use PHPUnit\Framework\TestCase;

class BreadthFirstTest extends TestCase
{
    /**
     * @covers PHGraph\ShortestPath\BreadthFirst::__construct
     *
     * @return void
     */
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(BreadthFirst::class, new BreadthFirst(new Vertex(new Graph)));
    }

    /**
     * @covers PHGraph\ShortestPath\BreadthFirst::getWalkTo
     *
     * @return void
     */
    public function testGetWalkToReturnsWalk(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_a->createEdgeTo($vertex_b);

        $bf = new BreadthFirst($vertex_a);
        $this->assertInstanceOf(Walk::class, $bf->getWalkTo($vertex_b));
    }

    /**
     * @covers PHGraph\ShortestPath\BreadthFirst::hasVertex
     *
     * @return void
     */
    public function testHasVertexTrue(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_a->createEdgeTo($vertex_b);

        $bf = new BreadthFirst($vertex_a);
        $this->assertTrue($bf->hasVertex($vertex_b));
    }

    /**
     * @covers PHGraph\ShortestPath\BreadthFirst::hasVertex
     *
     * @return void
     */
    public function testHasVertexFalse(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_b->createEdgeTo($vertex_a);

        $bf = new BreadthFirst($vertex_a);
        $this->assertFalse($bf->hasVertex($vertex_b));
    }

    /**
     * @covers PHGraph\ShortestPath\BreadthFirst::createGraph
     *
     * @return void
     */
    public function testCreateGraphReturnsGraph(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_a->createEdgeTo($vertex_b);

        $bf = new BreadthFirst($vertex_a);
        $this->assertInstanceOf(Graph::class, $bf->createGraph());
    }

    /**
     * @covers PHGraph\ShortestPath\BreadthFirst::getDistance
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
                $vertices[$i]->createEdge($vertices[$i - 1]);
            }
        }

        $bf = new BreadthFirst($vertices[0]);

        $this->assertEquals(45, $bf->getDistance($vertices[45]));
    }

    /**
     * @covers PHGraph\ShortestPath\BreadthFirst::getDistance
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

        $bf = new BreadthFirst($vertices[0]);

        $bf->getDistance($vertices[51]);
    }

    /**
     * @covers PHGraph\ShortestPath\BreadthFirst::getEdgesMap
     *
     * @return void
     */
    public function testGetEdgesMap(): void
    {
        $vertices = [];
        $graph = new Graph;
        for ($i = 0; $i < 50; $i++) {
            $vertices[$i] = new Vertex($graph);
            if ($i !== 0) {
                $vertices[$i]->createEdge($vertices[$i - 1]);
            }
        }

        $bf = new BreadthFirst($vertices[0]);

        $this->assertEquals(45, count($bf->getEdgesMap()[$vertices[45]->getId()]));
    }

    /**
     * @covers PHGraph\ShortestPath\BreadthFirst::getEdgesTo
     *
     * @return void
     */
    public function testGetEdgesToThrowsOutOfBoundsIfVerticesOnDifferentGraphs(): void
    {
        $this->expectException(OutOfBoundsException::class);

        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex(new Graph);

        $bf = new BreadthFirst($vertex_a);

        $bf->getEdgesTo($vertex_b);
    }

    /**
     * @covers PHGraph\ShortestPath\BreadthFirst::getEdgesTo
     *
     * @return void
     */
    public function testGetEdgesToThrowsOutOfBoundsIfVerticesArentConnected(): void
    {
        $this->expectException(OutOfBoundsException::class);

        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);

        $bf = new BreadthFirst($vertex_a);

        $bf->getEdgesTo($vertex_b);
    }

    /**
     * @covers PHGraph\ShortestPath\BreadthFirst::getEdgesTo
     *
     * @return void
     */
    public function testGetEdgesIsEdgeCollection(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_a->createEdgeTo($vertex_b);

        $bf = new BreadthFirst($vertex_a);

        $this->assertIsArray($bf->getEdgesTo($vertex_b));
    }

    /**
     * @covers PHGraph\ShortestPath\BreadthFirst::getDistanceMap
     *
     * @return void
     */
    public function testGetDistanceMap(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_a->createEdgeTo($vertex_b);

        $bf = new BreadthFirst($vertex_a);

        $this->assertEquals([
            $vertex_a->getId() => 0,
            $vertex_b->getId() => 1,
        ], $bf->getDistanceMap());
    }

    /**
     * @covers PHGraph\ShortestPath\BreadthFirst::getEdges
     *
     * @return void
     */
    public function testGetEdges(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $edge = $vertex_a->createEdgeTo($vertex_b);

        $bf = new BreadthFirst($vertex_a);

        $this->assertEquals([$edge], $bf->getEdges());
    }
}
