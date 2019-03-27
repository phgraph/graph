<?php

namespace Tests\Search;

use PHGraph\Graph;
use PHGraph\Search\BreadthFirst;
use PHGraph\Vertex;
use PHPUnit\Framework\TestCase;

class BreadthFirstTest extends TestCase
{
    /**
     * @covers PHGraph\Search\BreadthFirst::__construct
     *
     * @return void
     */
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(BreadthFirst::class, new BreadthFirst(new Vertex(new Graph)));
    }

    /**
     * @covers PHGraph\Search\BreadthFirst::getVertices
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

        $bf = new BreadthFirst($vertex[0]);

        $this->assertEquals($vertex, $bf->getVertices()->all());
    }
}
