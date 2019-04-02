<?php

namespace Tests\Support;

use InvalidArgumentException;
use PHGraph\Edge;
use PHGraph\Graph;
use PHGraph\Support\EdgeCollection;
use PHGraph\Vertex;
use PHPUnit\Framework\TestCase;

class EdgeCollectionTest extends TestCase
{
    /**
     * @covers PHGraph\Support\EdgeCollection::__construct
     *
     * @return void
     */
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(EdgeCollection::class, new EdgeCollection);
    }

    /**
     * @covers PHGraph\Support\EdgeCollection::__construct
     *
     * @return void
     */
    public function testInstantiationThrowsOnNonVertex(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $vertices = new EdgeCollection([1, 2, 3]);
    }

    /**
     * @covers PHGraph\Support\EdgeCollection::contains
     *
     * @return void
     */
    public function testContainsTrue(): void
    {
        $graph = new Graph;
        $edge = new Edge(new Vertex($graph), new Vertex($graph));

        $c = new EdgeCollection([$edge]);

        $this->assertTrue($c->contains($edge));
    }

    /**
     * @covers PHGraph\Support\EdgeCollection::contains
     *
     * @return void
     */
    public function testContainsFalse(): void
    {
        $graph = new Graph;
        $edge = new Edge(new Vertex($graph), new Vertex($graph));

        $c = new EdgeCollection;

        $this->assertFalse($c->contains($edge));
    }

    /**
     * @covers PHGraph\Support\EdgeCollection::contains
     *
     * @return void
     */
    public function testContainsFalseOnNonVertex(): void
    {
        $c = new EdgeCollection;

        $this->assertFalse($c->contains(1));
    }

    /**
     * @covers PHGraph\Support\EdgeCollection::containsEdge
     *
     * @return void
     */
    public function testContainsVertexTrue(): void
    {
        $graph = new Graph;
        $edge = new Edge(new Vertex($graph), new Vertex($graph));

        $c = new EdgeCollection([$edge]);

        $this->assertTrue($c->containsEdge($edge));
    }

    /**
     * @covers PHGraph\Support\EdgeCollection::containsEdge
     *
     * @return void
     */
    public function testContainsVertexFalse(): void
    {
        $graph = new Graph;
        $edge = new Edge(new Vertex($graph), new Vertex($graph));

        $c = new EdgeCollection;

        $this->assertFalse($c->containsEdge($edge));
    }

    /**
     * @covers PHGraph\Support\EdgeCollection::getVertices
     *
     * @return void
     */
    public function testGetVertices()
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_c = new Vertex($graph);
        $vertex_d = new Vertex($graph);
        $edge_a = new Edge($vertex_a, $vertex_b);
        $edge_b = new Edge($vertex_c, $vertex_d);

        $c = new EdgeCollection([$edge_a, $edge_b]);

        $this->assertEqualsCanonicalizing([$vertex_a, $vertex_b, $vertex_c, $vertex_d], $c->getVertices()->all());
    }

    /**
     * @covers PHGraph\Support\EdgeCollection::ordered
     *
     * @return void
     */
    public function testOrdered()
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_c = new Vertex($graph);
        $vertex_d = new Vertex($graph);
        $edge_a = new Edge($vertex_a, $vertex_b);
        $edge_b = new Edge($vertex_c, $vertex_d);

        $c = new EdgeCollection([$edge_a, $edge_b, $edge_b, $edge_a]);

        $this->assertEquals([$edge_a, $edge_b, $edge_b, $edge_a], $c->ordered()->all());
    }

    /**
     * @covers PHGraph\Support\EdgeCollection::remove
     *
     * @return void
     */
    public function testRemove()
    {
        $graph = new Graph;
        $edge_a = new Edge(new Vertex($graph), new Vertex($graph));
        $edge_b = new Edge(new Vertex($graph), new Vertex($graph));

        $c = new EdgeCollection([$edge_a, $edge_b]);
        $c->remove($edge_a);

        $this->assertEquals([$edge_b], $c->values()->all());
    }

    /**
     * @covers PHGraph\Support\EdgeCollection::sumAttribute
     *
     * @return void
     */
    public function testSumAttributeEmpty()
    {
        $c = new EdgeCollection;

        $this->assertEquals(0, $c->sumAttribute('test'));
    }

    /**
     * @covers PHGraph\Support\EdgeCollection::sumAttribute
     *
     * @return void
     */
    public function testSumAttributeDefault()
    {
        $graph = new Graph;
        $edge_a = new Edge(new Vertex($graph), new Vertex($graph));
        $edge_b = new Edge(new Vertex($graph), new Vertex($graph));

        $c = new EdgeCollection([$edge_a, $edge_b]);

        $this->assertEquals(2.5, $c->sumAttribute('test', 1.25));
    }

    /**
     * @covers PHGraph\Support\EdgeCollection::sumAttribute
     *
     * @return void
     */
    public function testSumAttribute()
    {
        $graph = new Graph;
        $edge_a = new Edge(new Vertex($graph), new Vertex($graph));
        $edge_b = new Edge(new Vertex($graph), new Vertex($graph));
        $edge_a->setAttribute('weight', 3);
        $edge_b->setAttribute('weight', 2.3);

        $c = new EdgeCollection([$edge_a, $edge_b]);

        $this->assertEquals(5.3, $c->sumAttribute('weight'));
    }

    /**
     * @covers PHGraph\Support\EdgeCollection::offsetSet
     *
     * @return void
     */
    public function testArrayAccessOffsetSetIgnoresOffset(): void
    {
        $graph = new Graph;
        $edge_a = new Edge(new Vertex($graph), new Vertex($graph));
        $edge_b = new Edge(new Vertex($graph), new Vertex($graph));

        $c = new EdgeCollection([$edge_a]);
        $c->offsetSet(1, $edge_b);
        $this->assertArrayNotHasKey(1, $c);
    }

    /**
     * @covers PHGraph\Support\EdgeCollection::offsetSet
     *
     * @return void
     */
    public function testArrayAccessOffsetSetThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $c = new EdgeCollection;
        $c->offsetSet(1, 'foo');
    }

    /**
     * @covers PHGraph\Support\EdgeCollection::offsetUnset
     *
     * @return void
     */
    public function testArrayAccessOffsetUnset(): void
    {
        $graph = new Graph;
        $edge_a = new Edge(new Vertex($graph), new Vertex($graph));
        $edge_b = new Edge(new Vertex($graph), new Vertex($graph));

        $c = new EdgeCollection([$edge_a, $edge_b]);
        $c->offsetUnset($edge_a->getId());

        $this->assertFalse(isset($c[$edge_a->getId()]));
    }
}
