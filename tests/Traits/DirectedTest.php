<?php

namespace Tests\Traits;

use PHGraph\Graph;
use PHGraph\Support\EdgeCollection;
use PHGraph\Traits\Directed;
use PHGraph\Vertex;
use PHPUnit\Framework\TestCase;

class DirectedTest extends TestCase
{
    /**
     * @return void
     */
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(DirectedClass::class, new DirectedClass(new EdgeCollection));
    }

    /**
     * @covers PHGraph\Traits\Directed::hasDirected
     *
     * @return void
     */
    public function testHasDirectedTrue(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $edge = $v1->createEdgeTo($v2);

        $directed = new DirectedClass(new EdgeCollection([$edge]));

        $this->assertTrue($directed->hasDirected());
    }

    /**
     * @covers PHGraph\Traits\Directed::hasDirected
     *
     * @return void
     */
    public function testHasDirectedFalse(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $edge = $v1->createEdge($v2);

        $directed = new DirectedClass(new EdgeCollection([$edge]));

        $this->assertFalse($directed->hasDirected());
    }

    /**
     * @covers PHGraph\Traits\Directed::hasUndirected
     *
     * @return void
     */
    public function testHasUndirectedTrue(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $edge = $v1->createEdge($v2);

        $directed = new DirectedClass(new EdgeCollection([$edge]));

        $this->assertTrue($directed->hasUndirected());
    }

    /**
     * @covers PHGraph\Traits\Directed::hasUndirected
     *
     * @return void
     */
    public function testHasUndirectedFalse(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $edge = $v1->createEdgeTo($v2);

        $directed = new DirectedClass(new EdgeCollection([$edge]));

        $this->assertFalse($directed->hasUndirected());
    }

    /**
     * @covers PHGraph\Traits\Directed::isMixed
     *
     * @return void
     */
    public function testIsMixedTrue(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $edge1 = $v1->createEdgeTo($v2);
        $edge2 = $v1->createEdge($v2);

        $directed = new DirectedClass(new EdgeCollection([$edge1, $edge2]));

        $this->assertTrue($directed->isMixed());
    }

    /**
     * @covers PHGraph\Traits\Directed::isMixed
     *
     * @return void
     */
    public function testIsMixedFalseDirected(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $edge = $v1->createEdgeTo($v2);

        $directed = new DirectedClass(new EdgeCollection([$edge]));

        $this->assertFalse($directed->isMixed());
    }

    /**
     * @covers PHGraph\Traits\Directed::isMixed
     *
     * @return void
     */
    public function testIsMixedFalseUndirected(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $edge = $v1->createEdge($v2);

        $directed = new DirectedClass(new EdgeCollection([$edge]));

        $this->assertFalse($directed->isMixed());
    }
}

/**
 * Stub class for testing the Trait: Directed.
 */
class DirectedClass
{
    use Directed;

    /** \PHGraph\Support\EdgeCollection */
    protected $edges;

    /**
     * @param \PHGraph\Support\EdgeCollection $edges
     *
     * @return void
     */
    public function __construct(EdgeCollection $edges)
    {
        $this->edges = $edges;
    }

    /**
     * get the edges in the graph.
     *
     * @return \PHGraph\Support\EdgeCollection
     */
    public function getEdges(): EdgeCollection
    {
        return $this->edges;
    }
}
