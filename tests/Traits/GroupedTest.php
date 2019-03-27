<?php

namespace Tests\Traits;

use PHGraph\Graph;
use PHGraph\Support\VertexCollection;
use PHGraph\Traits\Grouped;
use PHGraph\Vertex;
use PHPUnit\Framework\TestCase;

class GroupedTest extends TestCase
{
    /**
     * @return void
     */
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(GroupedClass::class, new GroupedClass(new VertexCollection));
    }

    /**
     * @covers PHGraph\Traits\Grouped::getNumberOfGroups
     *
     * @return void
     */
    public function testGetNumberOfGroupsEmpty(): void
    {
        $grouped = new GroupedClass(new VertexCollection);

        $this->assertEquals(0, $grouped->getNumberOfGroups());
    }

    /**
     * @covers PHGraph\Traits\Grouped::getNumberOfGroups
     *
     * @return void
     */
    public function testGetNumberOfGroups(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v1->setAttribute('group', 0);
        $v2->setAttribute('group', 1);

        $grouped = new GroupedClass(new VertexCollection([$v1, $v2]));

        $this->assertEquals(2, $grouped->getNumberOfGroups());
    }

    /**
     * @covers PHGraph\Traits\Grouped::isBipartit
     *
     * @return void
     */
    public function testIsBipartitTrue(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v3 = new Vertex($graph);
        $v4 = new Vertex($graph);
        $v1->setAttribute('group', 0);
        $v3->setAttribute('group', 0);
        $v2->setAttribute('group', 1);
        $v4->setAttribute('group', 1);
        $v1->createEdge($v2);
        $v1->createEdge($v4);
        $v2->createEdge($v3);

        $grouped = new GroupedClass(new VertexCollection([$v1, $v2]));

        $this->assertTrue($grouped->isBipartit());
    }

    /**
     * @covers PHGraph\Traits\Grouped::isBipartit
     *
     * @return void
     */
    public function testIsBipartitFalse(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v3 = new Vertex($graph);
        $v4 = new Vertex($graph);
        $v1->setAttribute('group', 0);
        $v3->setAttribute('group', 1);
        $v2->setAttribute('group', 1);
        $v4->setAttribute('group', 1);
        $v1->createEdge($v2);
        $v1->createEdge($v4);
        $v2->createEdge($v3);

        $grouped = new GroupedClass(new VertexCollection([$v1, $v2, $v3, $v4]));

        $this->assertFalse($grouped->isBipartit());
    }

    /**
     * @covers PHGraph\Traits\Grouped::isBipartit
     *
     * @return void
     */
    public function testIsBipartitFalseMoreThanTwoGroups(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v3 = new Vertex($graph);
        $v4 = new Vertex($graph);
        $v1->setAttribute('group', 0);
        $v3->setAttribute('group', 0);
        $v2->setAttribute('group', 1);
        $v4->setAttribute('group', 3);
        $v1->createEdge($v2);
        $v1->createEdge($v4);
        $v2->createEdge($v3);

        $grouped = new GroupedClass(new VertexCollection([$v1, $v2, $v3, $v4]));

        $this->assertFalse($grouped->isBipartit());
    }

    /**
     * @covers PHGraph\Traits\Grouped::getGroups
     *
     * @return void
     */
    public function testGetGroups(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v3 = new Vertex($graph);
        $v4 = new Vertex($graph);
        $v1->setAttribute('group', 0);
        $v3->setAttribute('group', 0);
        $v2->setAttribute('group', 1);
        $v4->setAttribute('group', 3);
        $v1->createEdge($v2);
        $v1->createEdge($v4);
        $v2->createEdge($v3);

        $grouped = new GroupedClass(new VertexCollection([$v1, $v2, $v3, $v4]));

        $this->assertEquals([0, 1, 3], $grouped->getGroups());
    }

    /**
     * @covers PHGraph\Traits\Grouped::getVerticesGroup
     *
     * @return void
     */
    public function testGetVerticesGroup(): void
    {
        $graph = new Graph;
        $v1 = new Vertex($graph);
        $v2 = new Vertex($graph);
        $v3 = new Vertex($graph);
        $v4 = new Vertex($graph);
        $v1->setAttribute('group', 0);
        $v3->setAttribute('group', 0);
        $v2->setAttribute('group', 1);
        $v4->setAttribute('group', 3);
        $v1->createEdge($v2);
        $v1->createEdge($v4);
        $v2->createEdge($v3);

        $grouped = new GroupedClass(new VertexCollection([$v1, $v2, $v3, $v4]));

        $this->assertEquals([$v1, $v3], $grouped->getVerticesGroup(0)->all());
    }
}

/**
 * Stub class for testing the Trait: Grouped.
 */
class GroupedClass
{
    use Grouped;

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
