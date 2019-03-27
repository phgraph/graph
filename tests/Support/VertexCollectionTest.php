<?php

namespace Tests\Support;

use InvalidArgumentException;
use PHGraph\Graph;
use PHGraph\Support\VertexCollection;
use PHGraph\Vertex;
use PHPUnit\Framework\TestCase;

class VertexCollectionTest extends TestCase
{
    /**
     * @covers PHGraph\Support\VertexCollection::__construct
     *
     * @return void
     */
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(VertexCollection::class, new VertexCollection);
    }

    /**
     * @covers PHGraph\Support\VertexCollection::__construct
     *
     * @return void
     */
    public function testInstantiationThrowsOnNonVertex(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $vertices = new VertexCollection([1, 2, 3]);
    }

    /**
     * @covers PHGraph\Support\VertexCollection::contains
     *
     * @return void
     */
    public function testContainsTrue(): void
    {
        $vertex = new Vertex(new Graph);

        $c = new VertexCollection([$vertex]);

        $this->assertTrue($c->contains($vertex));
    }

    /**
     * @covers PHGraph\Support\VertexCollection::contains
     *
     * @return void
     */
    public function testContainsFalse(): void
    {
        $vertex = new Vertex(new Graph);

        $c = new VertexCollection;

        $this->assertFalse($c->contains($vertex));
    }

    /**
     * @covers PHGraph\Support\VertexCollection::contains
     *
     * @return void
     */
    public function testContainsFalseOnNonVertex(): void
    {
        $c = new VertexCollection;

        $this->assertFalse($c->contains(1));
    }

    /**
     * @covers PHGraph\Support\VertexCollection::containsVertex
     *
     * @return void
     */
    public function testContainsVertexTrue(): void
    {
        $vertex = new Vertex(new Graph);

        $c = new VertexCollection([$vertex]);

        $this->assertTrue($c->containsVertex($vertex));
    }

    /**
     * @covers PHGraph\Support\VertexCollection::containsVertex
     *
     * @return void
     */
    public function testContainsVertexFalse(): void
    {
        $vertex = new Vertex(new Graph);

        $c = new VertexCollection;

        $this->assertFalse($c->containsVertex($vertex));
    }

    /**
     * @covers PHGraph\Support\VertexCollection::remove
     *
     * @return void
     */
    public function testRemove()
    {
        $vertex_a = new Vertex(new Graph);
        $vertex_b = new Vertex(new Graph);

        $c = new VertexCollection([$vertex_a, $vertex_b]);
        $c->remove($vertex_a);

        $this->assertEquals([$vertex_b], $c->values()->all());
    }

    /**
     * @covers PHGraph\Support\VertexCollection::offsetSet
     *
     * @return void
     */
    public function testArrayAccessOffsetSetIgnoresOffset(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);

        $c = new VertexCollection([$vertex_a]);
        $c->offsetSet(1, $vertex_b);
        $this->assertArrayNotHasKey(1, $c);
    }

    /**
     * @covers PHGraph\Support\VertexCollection::offsetSet
     *
     * @return void
     */
    public function testArrayAccessOffsetSetThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $c = new VertexCollection;
        $c->offsetSet(1, 'foo');
    }
}
