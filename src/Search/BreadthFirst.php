<?php

namespace PHGraph\Search;

use PHGraph\Contracts\Search;
use PHGraph\Support\VertexCollection;
use PHGraph\Vertex;

/**
 * Breadth First searcher.
 */
class BreadthFirst implements Search
{
    /** @var \PHGraph\Vertex */
    protected $vertex;

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
     * @return \PHGraph\Support\VertexCollection
     */
    public function getVertices(): VertexCollection
    {
        $queue = [$this->vertex];
        $marked = new VertexCollection([$this->vertex]);
        $visited = new VertexCollection;

        do {
            $current_vertex = array_shift($queue);
            $visited->add($current_vertex);
            $children = $current_vertex->getVerticesTo();

            foreach ($children as $vertex) {
                if (!$marked->contains($vertex)) {
                    $queue[] = $vertex;
                    $marked->add($vertex);
                }
            }
        } while (count($queue));

        return $visited;
    }
}
