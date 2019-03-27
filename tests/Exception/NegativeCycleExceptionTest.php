<?php

namespace Tests\Exception;

use PHGraph\Exception\NegativeCycleException;
use PHGraph\Graph;
use PHGraph\Vertex;
use PHGraph\Walk;
use PHPUnit\Framework\TestCase;

class NegativeCycleExceptionTest extends TestCase
{
    /**
     * @covers PHGraph\Exception\NegativeCycleException::__construct
     *
     * @return void
     */
    public function testInstantiation(): void
    {
        $graph = new Graph;
        $vertex = new Vertex($graph);
        $walk = new Walk($vertex);
        $exception = new NegativeCycleException('', 0, null, $walk);

        $this->assertInstanceOf(NegativeCycleException::class, $exception);
    }

    /**
     * @covers PHGraph\Exception\NegativeCycleException::getCycle
     *
     * @return void
     */
    public function testGetCycleReturnsWalk(): void
    {
        $graph = new Graph;
        $vertex = new Vertex($graph);
        $walk = new Walk($vertex);
        $exception = new NegativeCycleException('', 0, null, $walk);

        $this->assertInstanceOf(Walk::class, $exception->getCycle());
    }
}
