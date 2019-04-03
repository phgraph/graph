<?php

namespace Tests\Support;

use ArrayIterator;
use InvalidArgumentException;
use PHGraph\Graph;
use PHGraph\Support\VertexReplacementMap;
use PHGraph\Vertex;
use PHPUnit\Framework\TestCase;

class VertexReplacementMapTest extends TestCase
{
    /** @var \PHGraph\Graph */
    private $graph;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->graph = new Graph;
    }

    /**
     * @covers PHGraph\Support\VertexReplacementMap::count
     *
     * @return void
     */
    public function testCount(): void
    {
        $c = new VertexReplacementMap;
        $c[$this->graph->newVertex()] = $this->graph->newVertex();

        $this->assertEquals(1, $c->count());
    }

    /**
     * @covers PHGraph\Support\VertexReplacementMap::offsetExists
     *
     * @return void
     */
    public function testArrayAccessOffsetExistsIndex(): void
    {
        $v1 = $this->graph->newVertex();
        $c = new VertexReplacementMap;
        $c[$v1] = $this->graph->newVertex();

        $this->assertTrue($c->offsetExists($v1));
    }

    /**
     * @covers PHGraph\Support\VertexReplacementMap::offsetExists
     *
     * @return void
     */
    public function testArrayAccessOffsetExistsNonVertex(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $c = new VertexReplacementMap;
        $c->offsetExists('test');
    }

    /**
     * @covers PHGraph\Support\VertexReplacementMap::offsetExists
     *
     * @return void
     */
    public function testArrayAccessOffsetExistsUndefinedIndex(): void
    {
        $c = new VertexReplacementMap;

        $this->assertFalse($c->offsetExists($this->graph->newVertex()));
    }

    /**
     * @covers PHGraph\Support\VertexReplacementMap::offsetGet
     *
     * @return void
     */
    public function testArrayAccessOffsetGetZeroIndex(): void
    {
        $c = new VertexReplacementMap;
        $v1 = $this->graph->newVertex();
        $v2 = $this->graph->newVertex();
        $c[$v1] = $v2;

        $this->assertEquals($v2, $c->offsetGet($v1));
    }

    /**
     * @covers PHGraph\Support\VertexReplacementMap::offsetGet
     *
     * @return void
     */
    public function testArrayAccessOffsetGetNonVertex(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $c = new VertexReplacementMap;

        $c->offsetGet(1);
    }

    /**
     * @covers PHGraph\Support\VertexReplacementMap::offsetSet
     *
     * @return void
     */
    public function testArrayAccessOffsetSetWithValue(): void
    {
        $c = new VertexReplacementMap;
        $v1 = $this->graph->newVertex();
        $v2 = $this->graph->newVertex();
        $c->offsetSet($v1, $v2);

        $this->assertEquals($v2, $c[$v1]);
    }

    /**
     * @covers PHGraph\Support\VertexReplacementMap::offsetSet
     *
     * @return void
     */
    public function testArrayAccessOffsetSetWithNonVertex(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $c = new VertexReplacementMap;

        $c[1] = $this->graph->newVertex();
    }

    /**
     * @covers PHGraph\Support\VertexReplacementMap::offsetUnset
     *
     * @return void
     */
    public function testArrayAccessOffsetUnset(): void
    {
        $c = new VertexReplacementMap;
        $v1 = $this->graph->newVertex();
        $v2 = $this->graph->newVertex();
        $c[$v1] = $v2;

        $c->offsetUnset($v1);

        $this->assertFalse(isset($c[$v1]));
    }

    /**
     * @covers PHGraph\Support\VertexReplacementMap::offsetUnset
     *
     * @return void
     */
    public function testArrayAccessOffsetUnsetNonVertex(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $c = new VertexReplacementMap;

        $c->offsetUnset('string');
    }
}
