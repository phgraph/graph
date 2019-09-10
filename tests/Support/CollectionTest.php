<?php

namespace Tests\Support;

use ArrayIterator;
use PHGraph\Support\Collection;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    /**
     * @covers PHGraph\Support\Collection::__construct
     *
     * @return void
     */
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(Collection::class, new Collection);
    }

    /**
     * @covers PHGraph\Support\Collection::__construct
     *
     * @return void
     */
    public function testInstantiationWithItems(): void
    {
        $this->assertInstanceOf(Collection::class, new Collection([1, 2, 3]));
    }

    /**
     * @covers PHGraph\Support\Collection::add
     *
     * @return void
     */
    public function testAdd(): void
    {
        $c = new Collection([1, 2, 3]);
        $c->add(4);

        $this->assertEquals([1, 2, 3, 4], $c->items());
    }

    /**
     * @covers PHGraph\Support\Collection::all
     *
     * @return void
     */
    public function testAllStripsKeys(): void
    {
        $c = new Collection([5 => 1, 'baz' => 2, 'foo' => 3]);

        $this->assertEquals([1, 2, 3], $c->all());
    }

    /**
     * @covers PHGraph\Support\Collection::contains
     *
     * @return void
     */
    public function testContainsTrue(): void
    {
        $c = new Collection([1, 2, 3]);

        $this->assertTrue($c->contains(2));
    }

    /**
     * @covers PHGraph\Support\Collection::contains
     *
     * @return void
     */
    public function testContainsFalse(): void
    {
        $c = new Collection([1, 2, 3]);

        $this->assertFalse($c->contains(4));
    }

    /**
     * @covers PHGraph\Support\Collection::count
     *
     * @return void
     */
    public function testCount(): void
    {
        $c = new Collection([1, 2, 3]);

        $this->assertEquals(3, $c->count());
    }

    /**
     * @covers PHGraph\Support\Collection::diff
     *
     * @return void
     */
    public function testDiff(): void
    {
        $c = new Collection(['id' => 1, 'first_word' => 'Hello']);

        $this->assertEquals(['id' => 1], $c->diff(new Collection(['first_word' => 'Hello', 'last_word' => 'World']))->items());
    }

    /**
     * @covers PHGraph\Support\Collection::filter
     *
     * @return void
     */
    public function testFilterWithCallback(): void
    {
        $c = new Collection([['id' => 1, 'name' => 'Hello'], ['id' => 2, 'name' => 'World']]);
        $this->assertEquals([1 => ['id' => 2, 'name' => 'World']], $c->filter(function ($item) {
            return $item['id'] == 2;
        })->items());
    }

    /**
     * @covers PHGraph\Support\Collection::filter
     *
     * @return void
     */
    public function testFilterNoCallback(): void
    {
        $c = new Collection(['', 'Hello', '', 'World']);
        $this->assertEquals(['Hello', 'World'], $c->filter()->values()->all());
    }

    /**
     * @covers PHGraph\Support\Collection::filter
     *
     * @return void
     */
    public function testFilterWithCallbackWithKeys(): void
    {
        $c = new Collection(['id' => 1, 'first' => 'Hello', 'second' => 'World']);
        $this->assertEquals(['first' => 'Hello', 'second' => 'World'], $c->filter(function ($item, $key) {
            return $key != 'id';
        })->items());
    }

    /**
     * @covers PHGraph\Support\Collection::first
     *
     * @return void
     */
    public function testFirstReturnsFirstItemInCollection(): void
    {
        $c = new Collection(['foo', 'bar']);
        $this->assertEquals('foo', $c->first());
    }

    /**
     * @covers PHGraph\Support\Collection::first
     *
     * @return void
     */
    public function testFirstWithCallback(): void
    {
        $data = new Collection(['foo', 'bar', 'baz']);
        $result = $data->first(function ($value) {
            return $value === 'bar';
        });
        $this->assertEquals('bar', $result);
    }

    /**
     * @covers PHGraph\Support\Collection::first
     *
     * @return void
     */
    public function testFirstWithCallbackAndDefault(): void
    {
        $data = new Collection(['foo', 'bar']);
        $result = $data->first(function ($value) {
            return $value === 'baz';
        }, 'default');
        $this->assertEquals('default', $result);
    }

    /**
     * @covers PHGraph\Support\Collection::first
     *
     * @return void
     */
    public function testFirstWithDefaultAndWithoutCallback(): void
    {
        $data = new Collection;
        $result = $data->first(null, 'default');
        $this->assertEquals('default', $result);
    }

    /**
     * @covers PHGraph\Support\Collection::items
     *
     * @return void
     */
    public function testItems(): void
    {
        $c = new Collection([1, 2, 3]);

        $this->assertEquals([1, 2, 3], $c->items());
    }

    /**
     * @covers PHGraph\Support\Collection::merge
     *
     * @return void
     */
    public function testMergeEmpty()
    {
        $c = new Collection(['name' => 'Hello']);
        $this->assertEquals(['name' => 'Hello'], $c->merge([])->items());
    }

    /**
     * @covers PHGraph\Support\Collection::merge
     *
     * @return void
     */
    public function testMergeArray()
    {
        $c = new Collection(['name' => 'Hello']);
        $this->assertEquals(['name' => 'Hello', 'id' => 1], $c->merge(['id' => 1])->items());
    }

    /**
     * @covers PHGraph\Support\Collection::merge
     *
     * @return void
     */
    public function testMergeCollection()
    {
        $c = new Collection(['name' => 'Hello']);
        $this->assertEquals(['name' => 'World', 'id' => 1], $c->merge(new Collection(['name' => 'World', 'id' => 1]))->items());
    }

    /**
     * @covers PHGraph\Support\Collection::remove
     *
     * @return void
     */
    public function testRemove()
    {
        $c = new Collection([1, 2, 3]);
        $c->remove(2);

        $this->assertEquals([1, 3], $c->values()->items());
    }

    /**
     * @covers PHGraph\Support\Collection::reverse
     *
     * @return void
     */
    public function testReverseNoKeys()
    {
        $data = new Collection(['foo', 'bar']);
        $reversed = $data->reverse();
        $this->assertSame([1 => 'bar', 0 => 'foo'], $reversed->items());
    }

    /**
     * @covers PHGraph\Support\Collection::reverse
     *
     * @return void
     */
    public function testReverseWithKeys()
    {
        $data = new Collection(['name' => 'baz', 'option' => 'quux']);
        $reversed = $data->reverse();
        $this->assertSame(['option' => 'quux', 'name' => 'baz'], $reversed->items());
    }

    /**
     * @covers PHGraph\Support\Collection::sortBy
     *
     * @return void
     */
    public function testSortBy()
    {
        $data = new Collection(['foo', 'bar']);
        $data = $data->sortBy(function ($x) {
            return $x;
        });
        $this->assertEquals(['bar', 'foo'], array_values($data->items()));
    }

    /**
     * @covers PHGraph\Support\Collection::sortBy
     *
     * @return void
     */
    public function testSortByAlwaysReturnsAssocativeString()
    {
        $data = new Collection(['a' => 'foo', 'b' => 'bar']);
        $data = $data->sortBy(function ($x) {
            return $x;
        });
        $this->assertEquals(['b' => 'bar', 'a' => 'foo'], $data->items());
    }

    /**
     * @covers PHGraph\Support\Collection::sortBy
     *
     * @return void
     */
    public function testSortByAlwaysReturnsAssocativeNumeric()
    {
        $data = new Collection(['foo', 'bar']);
        $data = $data->sortBy(function ($x) {
            return $x;
        });
        $this->assertEquals([1 => 'bar', 0 => 'foo'], $data->items());
    }

    /**
     * @covers PHGraph\Support\Collection::sortByDesc
     *
     * @return void
     */
    public function testSortByDesc()
    {
        $data = new Collection(['bar', 'foo']);
        $data = $data->sortByDesc(function ($x) {
            return $x;
        });
        $this->assertEquals(['foo', 'bar'], array_values($data->items()));
    }

    /**
     * @covers PHGraph\Support\Collection::values
     *
     * @return void
     */
    public function testValuesHasSameData(): void
    {
        $c = new Collection([1, 2, 3]);
        $c2 = $c->values();

        $this->assertEquals($c->items(), $c2->items());
    }

    /**
     * @covers PHGraph\Support\Collection::values
     *
     * @return void
     */
    public function testValuesCreatesNewObject(): void
    {
        $c = new Collection([1, 2, 3]);
        $c2 = $c->values();

        $this->assertNotSame($c, $c2);
    }

    /**
     * @covers PHGraph\Support\Collection::offsetExists
     *
     * @return void
     */
    public function testArrayAccessOffsetExistsZeroIndex(): void
    {
        $c = new Collection(['foo', 'bar']);

        $this->assertTrue($c->offsetExists(0));
    }

    /**
     * @covers PHGraph\Support\Collection::offsetExists
     *
     * @return void
     */
    public function testArrayAccessOffsetExistsHigherIndex(): void
    {
        $c = new Collection(['foo', 'bar']);

        $this->assertTrue($c->offsetExists(1));
    }

    /**
     * @covers PHGraph\Support\Collection::offsetExists
     *
     * @return void
     */
    public function testArrayAccessOffsetExistsUndefinedIndex(): void
    {
        $c = new Collection(['foo', 'bar']);

        $this->assertFalse($c->offsetExists(1000));
    }

    /**
     * @covers PHGraph\Support\Collection::offsetGet
     *
     * @return void
     */
    public function testArrayAccessOffsetGetZeroIndex(): void
    {
        $c = new Collection(['foo', 'bar']);

        $this->assertEquals('foo', $c->offsetGet(0));
    }

    /**
     * @covers PHGraph\Support\Collection::offsetGet
     *
     * @return void
     */
    public function testArrayAccessOffsetGetHigherIndex(): void
    {
        $c = new Collection(['foo', 'bar']);

        $this->assertEquals('bar', $c->offsetGet(1));
    }

    /**
     * @covers PHGraph\Support\Collection::offsetSet
     *
     * @return void
     */
    public function testArrayAccessOffsetSetWithValue(): void
    {
        $c = new Collection(['foo', 'foo']);
        $c->offsetSet(1, 'bar');

        $this->assertEquals('bar', $c[1]);
    }

    /**
     * @covers PHGraph\Support\Collection::offsetSet
     *
     * @return void
     */
    public function testArrayAccessOffsetSetWithNull(): void
    {
        $c = new Collection(['foo', 'foo']);
        $c->offsetSet(null, 'qux');

        $this->assertEquals('qux', $c[2]);
    }

    /**
     * @covers PHGraph\Support\Collection::offsetUnset
     *
     * @return void
     */
    public function testArrayAccessOffsetUnset(): void
    {
        $c = new Collection(['foo', 'bar']);
        $c->offsetUnset(1);

        $this->assertFalse(isset($c[1]));
    }

    /**
     * @covers PHGraph\Support\Collection::getIterator
     *
     * @return void
     */
    public function testIterableProperClass(): void
    {
        $c = new Collection(['foo']);

        $this->assertInstanceOf(ArrayIterator::class, $c->getIterator());
    }

    /**
     * @covers PHGraph\Support\Collection::getIterator
     *
     * @return void
     */
    public function testIterableCopyMatches(): void
    {
        $c = new Collection(['foo']);

        $this->assertEquals(['foo'], $c->getIterator()->getArrayCopy());
    }
}
