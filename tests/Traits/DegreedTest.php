<?php

namespace Tests\Traits;

use PHGraph\Graph;
use PHGraph\Support\VertexCollection;
use PHGraph\Traits\Degreed;
use PHGraph\Vertex;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

class DegreedTest extends TestCase
{
    /**
     * @return void
     */
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(DegreedClass::class, new DegreedClass(new VertexCollection));
    }

    /**
     * @covers PHGraph\Traits\Degreed::getDegree
     *
     * @return void
     */
    public function testGetDegree(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdgeTo($v2);

        $degreed = new DegreedClass(new VertexCollection([$v1, $v2]));

        $this->assertEquals(1, $degreed->getDegree());
    }

    /**
     * @covers PHGraph\Traits\Degreed::getDegree
     *
     * @return void
     */
    public function testGetDegreeIrregular(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdgeTo($v2);
        $v1->createEdge($v1);

        $degreed = new DegreedClass(new VertexCollection([$v1, $v2]));

        $degreed->getDegree();
    }

    /**
     * @covers PHGraph\Traits\Degreed::getDegreeMin
     *
     * @return void
     */
    public function testGetDegreeMin(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdge($v2);

        $degreed = new DegreedClass(new VertexCollection([$v1, $v2]));

        $this->assertEquals(1, $degreed->getDegreeMin());
    }

    /**
     * @covers PHGraph\Traits\Degreed::getDegreeMax
     *
     * @return void
     */
    public function testGetDegreeMax(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdgeTo($v2);

        $degreed = new DegreedClass(new VertexCollection([$v1, $v2]));

        $this->assertEquals(1, $degreed->getDegreeMax());
    }

    /**
     * @covers PHGraph\Traits\Degreed::isRegular
     *
     * @return void
     */
    public function testIsRegularEmptyTrue(): void
    {
        $degreed = new DegreedClass(new VertexCollection);

        $this->assertTrue($degreed->isRegular());
    }

    /**
     * @covers PHGraph\Traits\Degreed::isRegular
     *
     * @return void
     */
    public function testIsRegularTrue(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdgeTo($v2);
        $v1->createEdge($v2);

        $degreed = new DegreedClass(new VertexCollection([$v1, $v2]));

        $this->assertTrue($degreed->isRegular());
    }

    /**
     * @covers PHGraph\Traits\Degreed::isRegular
     *
     * @return void
     */
    public function testIsRegularFalse(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdgeTo($v2);
        $v1->createEdgeTo($v1);

        $degreed = new DegreedClass(new VertexCollection([$v1, $v2]));

        $this->assertFalse($degreed->isRegular());
    }

    /**
     * @covers PHGraph\Traits\Degreed::isBalanced
     *
     * @return void
     */
    public function testIsBalancedTrue(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdge($v2);

        $degreed = new DegreedClass(new VertexCollection([$v1, $v2]));

        $this->assertTrue($degreed->isBalanced());
    }

    /**
     * @covers PHGraph\Traits\Degreed::isBalanced
     *
     * @return void
     */
    public function testIsBalancedFalse(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdgeTo($v2);

        $degreed = new DegreedClass(new VertexCollection([$v1, $v2]));

        $this->assertFalse($degreed->isBalanced());
    }

    /**
     * @covers PHGraph\Traits\Degreed::isVertexSource
     *
     * @return void
     */
    public function testIsVertexSourceTrue(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdgeTo($v2);

        $degreed = new DegreedClass(new VertexCollection([$v1, $v2]));

        $this->assertTrue($degreed->isVertexSource($v1));
    }

    /**
     * @covers PHGraph\Traits\Degreed::isVertexSource
     *
     * @return void
     */
    public function testIsVertexSourceFalse(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdgeTo($v2);

        $degreed = new DegreedClass(new VertexCollection([$v1, $v2]));

        $this->assertFalse($degreed->isVertexSource($v2));
    }

    /**
     * @covers PHGraph\Traits\Degreed::isVertexSink
     *
     * @return void
     */
    public function testIsVertexSinkTrue(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdgeTo($v2);

        $degreed = new DegreedClass(new VertexCollection([$v1, $v2]));

        $this->assertTrue($degreed->isVertexSink($v2));
    }

    /**
     * @covers PHGraph\Traits\Degreed::isVertexSink
     *
     * @return void
     */
    public function testIsVertexSinkFalse(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdgeTo($v2);

        $degreed = new DegreedClass(new VertexCollection([$v1, $v2]));

        $this->assertFalse($degreed->isVertexSink($v1));
    }

    /**
     * @covers PHGraph\Traits\Degreed::getDegreeVertex
     *
     * @return void
     */
    public function testGetDegreeVertexEmpty(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);

        $degreed = new DegreedClass(new VertexCollection([$v1]));

        $this->assertEquals(0, $degreed->getDegreeVertex($v1));
    }

    /**
     * @covers PHGraph\Traits\Degreed::getDegreeVertex
     *
     * @return void
     */
    public function testGetDegreeVertex(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdge($v2);

        $degreed = new DegreedClass(new VertexCollection([$v1, $v2]));

        $this->assertEquals(1, $degreed->getDegreeVertex($v1));
    }

    /**
     * @covers PHGraph\Traits\Degreed::getDegreeVertex
     *
     * @return void
     */
    public function testGetDegreeVertexLoop(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdge($v2);
        $v1->createEdge($v1);

        $degreed = new DegreedClass(new VertexCollection([$v1, $v2]));

        $this->assertEquals(3, $degreed->getDegreeVertex($v1));
    }

    /**
     * @covers PHGraph\Traits\Degreed::isVertexIsolated
     *
     * @return void
     */
    public function testIsVertexIsolatedTrue(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);

        $degreed = new DegreedClass(new VertexCollection([$v1]));

        $this->assertTrue($degreed->isVertexIsolated($v1));
    }

    /**
     * @covers PHGraph\Traits\Degreed::isVertexIsolated
     *
     * @return void
     */
    public function testIsVertexIsolatedFalse(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdgeTo($v2);

        $degreed = new DegreedClass(new VertexCollection([$v1, $v2]));

        $this->assertFalse($degreed->isVertexIsolated($v1));
    }

    /**
     * @covers PHGraph\Traits\Degreed::getDegreeInVertex
     *
     * @return void
     */
    public function testGetDegreeInVertex(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdge($v2);

        $degreed = new DegreedClass(new VertexCollection([$v1, $v2]));

        $this->assertEquals(1, $degreed->getDegreeInVertex($v1));
    }

    /**
     * @covers PHGraph\Traits\Degreed::getDegreeOutVertex
     *
     * @return void
     */
    public function testGetDegreeOutVertex(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->createEdge($v2);

        $degreed = new DegreedClass(new VertexCollection([$v1, $v2]));

        $this->assertEquals(1, $degreed->getDegreeOutVertex($v1));
    }
}

/**
 * Stub class for testing the Trait: Degreed.
 */
class DegreedClass
{
    use Degreed;

    /** \PHGraph\Support\VertexCollection */
    protected $vertices;

    /**
     * @param \PHGraph\Support\VertexCollection $vertices
     *
     * @return void
     */
    public function __construct(VertexCollection $vertices)
    {
        $this->vertices = $vertices;
    }
}
