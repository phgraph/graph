<?php

namespace Tests\TravelingSalesman;

use PHGraph\Graph;
use PHGraph\TravelingSalesman\NearestNeighbor;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

class NearestNeighborTest extends TestCase
{
    /**
     * @covers PHGraph\TravelingSalesman\NearestNeighbor::__construct
     *
     * @return void
     */
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(NearestNeighbor::class, new NearestNeighbor(new Graph));
    }

    /**
     * @covers PHGraph\TravelingSalesman\NearestNeighbor::setStartVertex
     *
     * @return void
     */
    public function testSetStartVertex(): void
    {
        $graph = new Graph();

        $a = $graph->newVertex(['name' => 'A']);
        $b = $graph->newVertex(['name' => 'B']);
        $c = $graph->newVertex(['name' => 'C']);

        $a->createEdgeTo($b, ['weight' => 6]);
        $a->createEdge($b, ['weight' => 10]);
        $b->createEdge($c, ['weight' => 15]);
        $c->createEdge($a, ['weight' => 15]);

        $nearest_neighbor = new NearestNeighbor($graph);
        $nearest_neighbor->setStartVertex($a);

        $this->assertEquals(36, $nearest_neighbor->getEdges()->sumAttribute('weight'));
    }

    /**
     * @covers PHGraph\TravelingSalesman\NearestNeighbor::createGraph
     *
     * @return void
     */
    public function testCreateGraphEmptyGraphThrowsException(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $nearest_neighbor = new NearestNeighbor(new Graph);

        $nearest_neighbor->createGraph();
    }

    /**
     * @covers PHGraph\TravelingSalesman\NearestNeighbor::getEdges
     *
     * @return void
     */
    public function testGetEdgesWithEmptyGraphThrowsException(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $nearest_neighbor = new NearestNeighbor(new Graph);

        $nearest_neighbor->getEdges();
    }

    /**
     * @covers PHGraph\TravelingSalesman\NearestNeighbor::getEdges
     *
     * @return void
     */
    public function testGetEdgesWithDisconnectedGraphThrowsException(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $graph = new Graph;
        $graph->newVertex();
        $graph->newVertex();

        $nearest_neighbor = new NearestNeighbor($graph);

        $nearest_neighbor->getEdges();
    }

    /**
     * @covers PHGraph\TravelingSalesman\NearestNeighbor::getEdges
     *
     * @return void
     */
    public function testSimpleGraphNotCycleThrowsException()
    {
        $this->expectException(UnexpectedValueException::class);

        $graph = new Graph;

        $v[0] = $graph->newVertex(['name' => 'Karelle']);
        $v[1] = $graph->newVertex(['name' => 'Kristoffer']);
        $v[2] = $graph->newVertex(['name' => 'Frederique']);
        $v[3] = $graph->newVertex(['name' => 'Stephany']);
        $v[4] = $graph->newVertex(['name' => 'Jensen']);

        $e[0] = $v[1]->createEdge($v[0], ['weight' => 6]);
        $e[1] = $v[2]->createEdge($v[1], ['weight' => 9]);
        $e[2] = $v[3]->createEdge($v[2], ['weight' => 7]);
        $e[3] = $v[4]->createEdge($v[3], ['weight' => 8]);

        $nearest_neighbor = new NearestNeighbor($graph);
        $nearest_neighbor->setStartVertex($v[0]);

        $nearest_neighbor->getEdges();
    }

    /**
     * @covers PHGraph\TravelingSalesman\NearestNeighbor::getEdges
     *
     * @return void
     */
    public function testSimpleGraph()
    {
        $graph = new Graph;

        $v[0] = $graph->newVertex(['name' => 'Karelle']);
        $v[1] = $graph->newVertex(['name' => 'Kristoffer']);
        $v[2] = $graph->newVertex(['name' => 'Frederique']);
        $v[3] = $graph->newVertex(['name' => 'Stephany']);
        $v[4] = $graph->newVertex(['name' => 'Jensen']);

        $e[0] = $v[1]->createEdge($v[0], ['weight' => 6]);
        $e[1] = $v[2]->createEdge($v[1], ['weight' => 9]);
        $e[2] = $v[3]->createEdge($v[2], ['weight' => 7]);
        $e[3] = $v[4]->createEdge($v[3], ['weight' => 8]);
        $e[4] = $v[0]->createEdge($v[4], ['weight' => 3]);

        $nearest_neighbor = new NearestNeighbor($graph);

        $this->assertEquals(5, $nearest_neighbor->getEdges()->count());
    }

    /**
     * @covers PHGraph\TravelingSalesman\NearestNeighbor::getEdges
     *
     * B --8-- C     G
     * | \\    |   / |
     * 7 |  7  5  9  11
     * |  \   \|/    |
     * A   9   E -8- F
     *  \   \  |    /
     *    5  | 15  6
     *      \\ | /
     *         D
     *
     * @return void
     */
    public function testComplexGraph()
    {
        $graph = new Graph;

        $a = $graph->newVertex(['name' => 'A']);
        $b = $graph->newVertex(['name' => 'B']);
        $c = $graph->newVertex(['name' => 'C']);
        $d = $graph->newVertex(['name' => 'D']);
        $e = $graph->newVertex(['name' => 'E']);
        $f = $graph->newVertex(['name' => 'F']);
        $g = $graph->newVertex(['name' => 'G']);

        $e = [
            $a->createEdge($b, ['weight' => 7]),
            $a->createEdge($d, ['weight' => 5]),
            $b->createEdge($c, ['weight' => 8]),
            $b->createEdge($d, ['weight' => 9]),
            $b->createEdge($e, ['weight' => 7]),
            $c->createEdge($e, ['weight' => 5]),
            $d->createEdge($e, ['weight' => 15]),
            $d->createEdge($f, ['weight' => 6]),
            $e->createEdge($f, ['weight' => 8]),
            $e->createEdge($g, ['weight' => 9]),
            $f->createEdge($g, ['weight' => 11]),
        ];

        $nearest_neighbor = new NearestNeighbor($graph);
        $nearest_neighbor->setStartVertex($g);

        $this->assertEquals(51, $nearest_neighbor->getEdges()->sumAttribute('weight'));
    }

    /**
     * @covers PHGraph\TravelingSalesman\NearestNeighbor::getEdges
     *
     * @return void
     */
    public function testComplexGraphFailsOnBadStartingVertex()
    {
        $this->expectException(UnexpectedValueException::class);

        $graph = new Graph;

        $a = $graph->newVertex(['name' => 'A']);
        $b = $graph->newVertex(['name' => 'B']);
        $c = $graph->newVertex(['name' => 'C']);
        $d = $graph->newVertex(['name' => 'D']);
        $e = $graph->newVertex(['name' => 'E']);
        $f = $graph->newVertex(['name' => 'F']);
        $g = $graph->newVertex(['name' => 'G']);

        $e = [
            $a->createEdge($b, ['weight' => 7]),
            $a->createEdge($d, ['weight' => 5]),
            $b->createEdge($c, ['weight' => 8]),
            $b->createEdge($d, ['weight' => 9]),
            $b->createEdge($e, ['weight' => 7]),
            $c->createEdge($e, ['weight' => 5]),
            $d->createEdge($e, ['weight' => 15]),
            $d->createEdge($f, ['weight' => 6]),
            $e->createEdge($f, ['weight' => 8]),
            $e->createEdge($g, ['weight' => 9]),
            $f->createEdge($g, ['weight' => 11]),
        ];

        $nearest_neighbor = new NearestNeighbor($graph);
        $nearest_neighbor->setStartVertex($a);

        $nearest_neighbor->getEdges();
    }

    /**
     * @covers PHGraph\TravelingSalesman\NearestNeighbor::getEdges
     *
     * @return void
     */
    public function testFindingCheapestPath()
    {
        $graph = new Graph;

        $a = $graph->newVertex(['name' => 'A']);
        $b = $graph->newVertex(['name' => 'B']);

        $a->createEdge($b, ['weight' => 4]);
        $a->createEdge($b, ['weight' => 3]);
        $a->createEdge($b, ['weight' => 5]);

        $nearest_neighbor = new NearestNeighbor($graph);

        $this->assertEquals(7, $nearest_neighbor->getEdges()->sumAttribute('weight'));
    }
}
