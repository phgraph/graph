<?php

namespace PHGraph\Exception;

use Exception;
use PHGraph\Walk;

/**
 * If a graph contains a "negative cycle" (i.e. a cycle whose edges sum to a
 * negative value) that is reachable from the source, then there is no cheapest
 * path: any path that has a point on the negative cycle can be made cheaper by
 * one more walk around the negative cycle.
 */
class NegativeCycleException extends Exception
{
    /** @var \PHGraph\Walk */
    private $cycle;

    /**
     * @param string          $message  exception message
     * @param int             $code     user defined exception code
     * @param \Exception|null $previous previous exception if nested exception
     * @param \PHGraph\Walk   $cycle    cycle involved
     *
     * @return void
     */
    public function __construct($message, $code = 0, $previous = null, Walk $cycle)
    {
        parent::__construct($message, $code, $previous);

        $this->cycle = $cycle;
    }

    /**
     * @return \PHGraph\Walk
     */
    public function getCycle(): Walk
    {
        return $this->cycle;
    }
}
