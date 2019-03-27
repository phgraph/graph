<?php

namespace PHGraph\Contracts;

interface Attributable
{
    /**
     * get a single attribute with the given $name (or return $default if attribute was not found).
     *
     * @param string $name
     * @param mixed  $default to return if attribute was not found
     *
     * @return mixed
     */
    public function getAttribute(string $name, $default = null);

    /**
     * set a single attribute with the given $name to given $value.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return void
     */
    public function setAttribute(string $name, $value): void;

    /**
     * Removes a single attribute with the given $name.
     *
     * @param string $name
     *
     * @return void
     */
    public function removeAttribute(string $name): void;
}
