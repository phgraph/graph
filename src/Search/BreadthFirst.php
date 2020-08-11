<?php

declare(strict_types=1);

namespace PHGraph\Search;

use PHGraph\Contracts\Search;
use PHGraph\Vertex;
use SplObjectStorage;

/**
 * Breadth First search algorithm.
 *
 * @see https://en.wikipedia.org/wiki/Breadth-first_search
 */
final class BreadthFirst implements Search
{
    /** @var \PHGraph\Vertex */
    private $vertex;

    /**
     * instantiate new algorithm.
     *
     * @param \PHGraph\Vertex $vertex Vertex to operate on
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
        $queue = [$this->vertex];
        $marked = new SplObjectStorage();
        $marked->attach($this->vertex);
        $visited = [];

        do {
            /** @var \PHGraph\Vertex $current_vertex */
            $current_vertex = array_shift($queue);
            $visited[$current_vertex->getId()] = $current_vertex;
            $children = $current_vertex->getVerticesTo();

            /** @var \PHGraph\Vertex $vertex */
            foreach ($children as $vertex) {
                if (!$marked->contains($vertex)) {
                    $queue[] = $vertex;
                    $marked->attach($vertex);
                }
            }
        } while (count($queue));

        return $visited;
    }
}
