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
