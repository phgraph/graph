<?php

namespace Tests\MinimumSpanningTree;

use PHGraph\Graph;
use PHGraph\MinimumSpanningTree\Prim;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

class PrimTest extends TestCase
{
    /**
     * @covers PHGraph\MinimumSpanningTree\Prim::__construct
     *
     * @return void
     */
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(Prim::class, new Prim(new Graph));
    }


    /**
     * @covers PHGraph\MinimumSpanningTree\Prim::__construct
     *
     * @return void
     */
    public function testInstantiationWithVertices(): void
    {
        $g = new Graph;
        $g->newVertex();

        $this->assertInstanceOf(Prim::class, new Prim($g));
    }

    /**
     * @covers PHGraph\MinimumSpanningTree\Prim::__construct
     *
     * @return void
     */
    public function testInstantiationThrowsExceptionOnDirectedGraph(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $graph = new Graph();
        $graph->newVertex()->createEdgeTo($graph->newVertex());

        new Prim($graph);
    }

    /**
     * @covers PHGraph\MinimumSpanningTree\Prim::createGraph
     *
     * @return void
     */
    public function testCreateGraphEmptyGraphThrowsException(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $prim = new Prim(new Graph);

        $prim->createGraph();
    }

    /**
     * @covers PHGraph\MinimumSpanningTree\Prim::getEdges
     *
     * @return void
     */
    public function testGetEdgesWithEmptyGraphThrowsException(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $prim = new Prim(new Graph);

        $prim->getEdges();
    }

    /**
     * @covers PHGraph\MinimumSpanningTree\Prim::getEdges
     *
     * @return void
     */
    public function testGetEdgesWithDisconnectedGraphThrowsException(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $graph = new Graph;
        $graph->newVertex();
        $graph->newVertex();

        $prim = new Prim($graph);

        $prim->getEdges();
    }

    /**
     * @covers PHGraph\MinimumSpanningTree\Prim::getEdges
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

        $prim = new Prim($graph);

        $this->assertCount(4, $prim->getEdges());
    }

    /**
     * @covers PHGraph\MinimumSpanningTree\Prim::getEdges
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

        $prim = new Prim($graph);

        $edges = $prim->getEdges();
        $sum_weight = array_sum(array_map(function ($edge) {
            return $edge->getAttribute('weight');
        }, $edges));

        $this->assertEquals(39, $sum_weight);
    }

    /**
     * @covers PHGraph\MinimumSpanningTree\Prim::getEdges
     *
     * @return void
     */
    public function testFindingCheapestEdge()
    {
        $graph = new Graph;

        $v1 = $graph->newVertex();
        $v2 = $graph->newVertex();

        $v1->createEdge($v2, ['weight' => 4]);
        $v1->createEdge($v2, ['weight' => 3]);
        $v1->createEdge($v2, ['weight' => 5]);

        $prim = new Prim($graph);

        $edges = $prim->getEdges();
        $this->assertEquals(3, reset($edges)->getAttribute('weight'));
    }
}
