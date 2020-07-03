<?php

namespace Tests\Exception;

use PHGraph\Exception\NegativeCycle;
use PHGraph\Graph;
use PHGraph\Vertex;
use PHGraph\Walk;
use PHPUnit\Framework\TestCase;

class NegativeCycleTest extends TestCase
{
    /**
     * @covers PHGraph\Exception\NegativeCycle::__construct
     *
     * @return void
     */
    public function testInstantiation(): void
    {
        $graph = new Graph;
        $vertex = new Vertex($graph);
        $walk = new Walk($vertex, []);
        $exception = new NegativeCycle('', 0, null, $walk);

        $this->assertInstanceOf(NegativeCycle::class, $exception);
    }

    /**
     * @covers PHGraph\Exception\NegativeCycle::getCycle
     *
     * @return void
     */
    public function testGetCycleReturnsWalk(): void
    {
        $graph = new Graph;
        $vertex = new Vertex($graph);
        $walk = new Walk($vertex, []);
        $exception = new NegativeCycle('', 0, null, $walk);

        $this->assertInstanceOf(Walk::class, $exception->getCycle());
    }
}
