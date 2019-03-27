<?php

namespace Tests\Search;

use PHGraph\Vertex;
use PHGraph\Graph;
use PHGraph\Search\DepthFirst;
use PHPUnit\Framework\TestCase;

class DepthFirstTest extends TestCase
{
    /**
     * @covers PHGraph\Search\DepthFirst::__construct
     *
     * @return void
     */
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(DepthFirst::class, new DepthFirst(new Vertex(new Graph)));
    }

    /**
     * @covers PHGraph\Search\DepthFirst::getVertices
     *
     * @return void
     */
    public function testArrayAccessOffsetSetIgnoresOffset(): void
    {
        $vertex = [];
        $graph = new Graph;
        for ($i = 0; $i < 50; $i++) {
            $vertex[$i] = new Vertex($graph);
            if ($i !== 0) {
                $vertex[$i]->createEdge($vertex[$i - 1]);
            }
        }

        $df = new DepthFirst($vertex[0]);

        $this->assertEquals($vertex, $df->getVertices()->all());
    }
}
