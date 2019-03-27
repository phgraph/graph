<?php

namespace Tests;

use PHGraph\Graph;
use PHGraph\Vertex;
use PHGraph\Walk;
use PHPUnit\Framework\TestCase;

class WalkTest extends TestCase
{
    /**
     * @covers PHGraph\Walk::__construct
     *
     * @return void
     */
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(Walk::class, new Walk(new Vertex(new Graph)));
    }
}
