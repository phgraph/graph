<?php

namespace PHGraph\Search;

use PHGraph\Contracts\Search;
use PHGraph\Vertex;

/**
 * Depth First search algorithm.
 *
 * @see https://en.wikipedia.org/wiki/Depth-first_search
 */
class DepthFirst implements Search
{
    /** @var \PHGraph\Vertex */
    protected $vertex;

    /**
     * instantiate new algorithm.
     *
     * @param Vertex $vertex Vertex to operate on
     *
     * @return void
     */
    public function __construct(Vertex $vertex)
    {
        $this->vertex = $vertex;
    }

    /**
     * @return \PHGraph\Vertex[]
     */
    public function getVertices(): array
    {
        $visited = [];
        $queue = [$this->vertex];
        while ($vertex = array_shift($queue)) {
            if (!($visited[$vertex->getId()] ?? false)) {
                $visited[$vertex->getId()] = $vertex;
                foreach (array_reverse($vertex->getVerticesTo()) as $nextVertex) {
                    $queue[] = $nextVertex;
                }
            }
        }

        return $visited;
    }
}
