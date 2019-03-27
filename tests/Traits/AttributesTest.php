<?php

namespace Tests\Traits;

use PHGraph\Traits\Attributes;
use PHPUnit\Framework\TestCase;

class AttributesTest extends TestCase
{
    /**
     * @return void
     */
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(AttributesClass::class, new AttributesClass);
    }

    /**
     * @covers PHGraph\Traits\Attributes::getAttribute
     *
     * @return void
     */
    public function testGetAttributeDefault(): void
    {
        $attributable = new AttributesClass;

        $this->assertEquals(1, $attributable->getAttribute('notset', 1));
    }

    /**
     * @covers PHGraph\Traits\Attributes::getAttribute
     *
     * @return void
     */
    public function testGetAttribute(): void
    {
        $attributable = new AttributesClass(['val' => 'test']);

        $this->assertEquals('test', $attributable->getAttribute('val', 1));
    }

    /**
     * @covers PHGraph\Traits\Attributes::getAttributesWithPrefix
     *
     * @return void
     */
    public function testGetAttributesWithPrefixEmpty(): void
    {
        $attributable = new AttributesClass([
            'foo.quux' => 'test1',
            'bar.baz' => 'test2',
        ]);

        $this->assertEquals([], $attributable->getAttributesWithPrefix('blah.'));
    }

    /**
     * @covers PHGraph\Traits\Attributes::getAttributesWithPrefix
     *
     * @return void
     */
    public function testGetAttributesWithPrefix(): void
    {
        $attributable = new AttributesClass([
            'foo.quux' => 'test1',
            'bar.baz' => 'test2',
        ]);

        $this->assertEquals(['quux' => 'test1'], $attributable->getAttributesWithPrefix('foo.'));
    }

    /**
     * @covers PHGraph\Traits\Attributes::getAttributesWithPrefix
     *
     * @return void
     */
    public function testGetAttributesWithPrefixMultiple(): void
    {
        $attributable = new AttributesClass([
            'foo.quux' => 'test1',
            'foo.blah' => 'test2',
            'bar.baz' => 'test3',
        ]);

        $this->assertEquals(['quux' => 'test1', 'blah' => 'test2'], $attributable->getAttributesWithPrefix('foo.'));
    }

    /**
     * @covers PHGraph\Traits\Attributes::setAttribute
     *
     * @return void
     */
    public function testSetAttribute(): void
    {
        $attributable = new AttributesClass(['val' => 'test']);

        $attributable->setAttribute('val', 'testing');

        $this->assertEquals('testing', $attributable->getAttribute('val', 1));
    }

    /**
     * @covers PHGraph\Traits\Attributes::setAttributes
     *
     * @return void
     */
    public function testSetAttributes(): void
    {
        $attributable = new AttributesClass(['val' => 'test']);

        $attributable->setAttributes([
            'val' => 'testing',
        ]);

        $this->assertEquals('testing', $attributable->getAttribute('val', 1));
    }

    /**
     * @covers PHGraph\Traits\Attributes::removeAttribute
     *
     * @return void
     */
    public function testRemoveAttribute(): void
    {
        $attributable = new AttributesClass(['val' => 'test']);

        $attributable->removeAttribute('val');

        $this->assertEquals(1, $attributable->getAttribute('val', 1));
    }
}

/**
 * Stub class for testing the Trait: Attributes.
 */
class AttributesClass
{
    use Attributes;

    /**
     * @param array $attributes
     *
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }
}
