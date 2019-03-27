<?php

namespace PHGraph\Support;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * Class to abstract collections of similar things.
 */
class Collection implements ArrayAccess, Countable, IteratorAggregate
{
    /** @var array */
    protected $items = [];

    /**
     * @param iterable $items items that form this collection
     *
     * @return void
     */
    public function __construct(iterable $items = [])
    {
        foreach ($items as $key => $item) {
            $this[$key] = $item;
        }
    }

    /**
     * add item to collection.
     *
     * @param mixed $value value to add
     *
     * @return void
     */
    public function add($value): void
    {
        $this->offsetSet(null, $value);
    }

    /**
     * Get all of the items in the collection.
     *
     * @return array
     */
    public function all(): array
    {
        return array_values($this->items);
    }

    /**
     * Determine if an item exists in the collection.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function contains($value): bool
    {
        return in_array($value, $this->items);
    }

    /**
     * Countable: get count of items in collection.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Run a filter over each of the items.
     *
     * @param  callable|null  $callback
     *
     * @return static
     */
    public function filter(callable $callback = null): self
    {
        if ($callback) {
            return new static(array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH));
        }

        return new static(array_filter($this->items));
    }

    /**
     * Get the first item from the collection.
     *
     * @param  callable|null  $callback
     * @param  mixed  $default
     *
     * @return mixed
     */
    public function first(callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            if (empty($this->items)) {
                return $default;
            }
            foreach ($this->items as $item) {
                return $item;
            }
        }
        foreach ($this->items as $key => $value) {
            if (call_user_func($callback, $value, $key)) {
                return $value;
            }
        }

        return $default;
    }

    /**
     * Get all of the items in the collection with keys.
     *
     * @return array
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * Merge the collection with the given items.
     *
     * @param iterable $items
     *
     * @return static
     */
    public function merge(iterable $items): self
    {
        return new static(array_merge($this->items, $items instanceof self ? $items->items : $items));
    }

    /**
     * Remove item from collection.
     *
     * @param mixed $value value to remove
     *
     * @return void
     */
    public function remove($value): void
    {
        foreach ($this->items as $key => $item) {
            if ($item === $value) {
                $this->offsetUnset($key);
            }
        }
    }

    /**
     * Reverse items order.
     *
     * @return static
     */
    public function reverse(): self
    {
        return new static(array_reverse($this->items, true));
    }

    /**
     * Sort the collection using the given callback.
     *
     * @param callable $callback   sorting function
     * @param int      $options    array sort options
     * @param bool     $descending sort direction
     *
     * @return static
     */
    public function sortBy($callback, $options = SORT_REGULAR, $descending = false): self
    {
        $results = [];

        foreach ($this->items as $key => $value) {
            $results[$key] = $callback($value, $key);
        }
        $descending ? arsort($results, $options) : asort($results, $options);

        foreach (array_keys($results) as $key) {
            $results[$key] = $this->items[$key];
        }

        return new static($results);
    }

    /**
     * Sort the collection in descending order using the given callback.
     *
     * @param callable $callback   sorting function
     * @param int      $options    array sort options
     *
     * @return static
     */
    public function sortByDesc($callback, $options = SORT_REGULAR): self
    {
        return $this->sortBy($callback, $options, true);
    }

    /**
     * Reset the keys on the underlying array.
     *
     * @return static
     */
    public function values(): self
    {
        return new static(array_values($this->items));
    }

    /**
     * ArrayAccess: Determine if an item exists at an offset.
     *
     * @param mixed $offset accessor key
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->items);
    }

    /**
     * ArrayAccess: Get an item at a given offset.
     *
     * @param mixed $offset accessor key
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    /**
     * ArrayAccess: Set the item at a given offset.
     *
     * @param mixed $offset accessor key
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        if ($offset === null) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    /**
     * ArrayAccess: Unset the item at a given offset.
     *
     * @param mixed $offset accessor key
     *
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->items[$offset]);
    }

    /**
     * IteratorAggregate: Get an iterator for the items.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }
}
