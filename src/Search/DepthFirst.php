<?php

declare(strict_types=1);

namespace PHGraph\Search;

use PHGraph\Contracts\Search;
use PHGraph\Vertex;
use SplQueue;

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
        /** @var \PHGraph\Vertex[] $visited */
        $visited = [];
        $queue = new SplQueue();
        $queue->enqueue($this->vertex);

        while (!$queue->isEmpty()) {
            /** @var \PHGraph\Vertex $vertex */
            $vertex = $queue->dequeue();

            if (isset($visited[$vertex->getId()])) {
                continue;
            }

            $visited[$vertex->getId()] = $vertex;
            foreach (array_reverse($vertex->getVerticesTo()) as $next_vertex) {
                $queue->enqueue($next_vertex);
            }
        }

        return $visited;
    }
}
