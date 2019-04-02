<?php

namespace Tests\ShortestPath;

use OutOfBoundsException;
use PHGraph\Graph;
use PHGraph\ShortestPath\Dijkstra;
use PHGraph\Support\EdgeCollection;
use PHGraph\Vertex;
use PHGraph\Walk;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

class DijkstraTest extends TestCase
{
    /**
     * @covers PHGraph\ShortestPath\Dijkstra::__construct
     *
     * @return void
     */
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(Dijkstra::class, new Dijkstra(new Vertex(new Graph)));
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::__construct
     *
     * @return void
     */
    public function testNegativeWeightsThrowException(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $edge = $vertex_a->createEdgeTo($vertex_b);

        $edge->setAttribute('weight', -1);

        $bf = new Dijkstra($vertex_a);
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::getWalkTo
     *
     * @return void
     */
    public function testGetWalkToReturnsWalk(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_a->createEdgeTo($vertex_b);

        $bf = new Dijkstra($vertex_a);
        $this->assertInstanceOf(Walk::class, $bf->getWalkTo($vertex_b));
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::hasVertex
     *
     * @return void
     */
    public function testHasVertexTrue(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_a->createEdgeTo($vertex_b);

        $bf = new Dijkstra($vertex_a);
        $this->assertTrue($bf->hasVertex($vertex_b));
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::hasVertex
     *
     * @return void
     */
    public function testHasVertexFalse(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_b->createEdgeTo($vertex_a);

        $bf = new Dijkstra($vertex_a);
        $this->assertFalse($bf->hasVertex($vertex_b));
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::createGraph
     *
     * @return void
     */
    public function testCreateGraphReturnsGraph(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_a->createEdgeTo($vertex_b);

        $bf = new Dijkstra($vertex_a);
        $this->assertInstanceOf(Graph::class, $bf->createGraph());
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::getDistance
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

        $bf = new Dijkstra($vertices[0]);

        $this->assertEquals(45, $bf->getDistance($vertices[45]));
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::getDistance
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

        $bf = new Dijkstra($vertices[0]);

        $bf->getDistance($vertices[51]);
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::getEdgesTo
     *
     * @return void
     */
    public function testGetEdgesToThrowsOutOfBoundsIfVerticesOnDifferentGraphs(): void
    {
        $this->expectException(OutOfBoundsException::class);

        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex(new Graph);

        $bf = new Dijkstra($vertex_a);

        $bf->getEdgesTo($vertex_b);
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::getEdgesTo
     *
     * @return void
     */
    public function testGetEdgesToThrowsOutOfBoundsIfVerticesArentConnected(): void
    {
        $this->expectException(OutOfBoundsException::class);

        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);

        $bf = new Dijkstra($vertex_a);

        $bf->getEdgesTo($vertex_b);
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::getEdgesTo
     *
     * @return void
     */
    public function testGetEdgesIsEdgeCollection(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_a->createEdgeTo($vertex_b);

        $bf = new Dijkstra($vertex_a);

        $this->assertInstanceOf(EdgeCollection::class, $bf->getEdgesTo($vertex_b));
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::getEdgesTo
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

        $bf = new Dijkstra($vertex_a);

        $this->assertEquals([$edge_a, $edge_b], $bf->getEdgesTo($vertex_e)->all());
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::getEdgesTo
     *
     * @return void
     */
    public function testGetEdgesToSameVertexIsEmpty(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);

        $bf = new Dijkstra($vertex_a);

        $this->assertEmpty($bf->getEdgesTo($vertex_a)->all());
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::getDistanceMap
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

        $bf = new Dijkstra($vertex_a);

        $this->assertEquals([
            $vertex_a->getId() => 0.0,
            $vertex_b->getId() => 1.0,
        ], $bf->getDistanceMap());
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::getDistanceMap
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

        $bf = new Dijkstra($vertex_a);

        $this->assertEquals([
            $vertex_a->getId() => 0.0,
            $vertex_b->getId() => 1.0,
        ], $bf->getDistanceMap());
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::getEdges
     *
     * @return void
     */
    public function testGetEdges(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $edge = $vertex_a->createEdgeTo($vertex_b);

        $bf = new Dijkstra($vertex_a);

        $this->assertEquals([$edge], $bf->getEdges()->all());
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::getEdges
     *
     * @return void
     */
    public function testGetEdgesCached(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_a->createEdgeTo($vertex_b);

        $bf = new Dijkstra($vertex_a);

        $edges = $bf->getEdges()->all();

        $vertex_c = new Vertex($graph);
        $vertex_a->createEdgeTo($vertex_c);

        $this->assertEquals($edges, $bf->getEdges()->all());
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::getEdges
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

        $bf = new Dijkstra($vertex_a);

        $this->assertEquals([$edge_b], $bf->getEdges()->all());
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::getEdges
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

        $bf = new Dijkstra($vertex_a);

        $this->assertEquals([$edge_b, $edge_c], $bf->getEdges()->all());
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::getEdges
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

        $bf = new Dijkstra($vertex_a);

        $this->assertEquals([$edge_b], $bf->getEdges()->all());
    }
}
