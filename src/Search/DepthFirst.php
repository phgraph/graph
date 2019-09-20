<?php

namespace PHGraph\Search;

use PHGraph\Contracts\Search;
use PHGraph\Support\VertexCollection;
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
     * @return \PHGraph\Support\VertexCollection
     */
    public function getVertices(): VertexCollection
    {
        $visited = new VertexCollection;
        $queue = [$this->vertex];
        while ($vertex = array_shift($queue)) {
            if (!$visited->contains($vertex)) {
                $visited->add($vertex);
                foreach ($vertex->getVerticesTo()->reverse() as $nextVertex) {
                    $queue[] = $nextVertex;
                }
            }
        }

        return $visited;
    }
}
