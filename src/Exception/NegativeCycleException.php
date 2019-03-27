<?php

namespace PHGraph\Exception;

use Exception;
use PHGraph\Walk;

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
