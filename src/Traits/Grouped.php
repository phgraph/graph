<?php

namespace PHGraph\Traits;

use PHGraph\Support\VertexCollection;

trait Grouped
{
    /**
     * count total number of different groups assigned to vertices.
     *
     * @return int
     */
    public function getNumberOfGroups(): int
    {
        return count($this->getGroups());
    }

    /**
     * checks whether the input graph's vertex groups are a valid bipartition.
     *
     * @return bool
     */
    public function isBipartit(): bool
    {
        if ($this->getNumberOfGroups() !== 2) {
            return false;
        }

        foreach ($this->vertices as $vertex) {
            $group = $vertex->getAttribute('group');

            foreach ($vertex->getVerticesTo() as $vertex_neighbor) {
                if ($vertex_neighbor->getAttribute('group') === $group) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * get vector of all group numbers.
     *
     * @return array
     */
    public function getGroups(): array
    {
        $groups = [];

        foreach ($this->vertices as $vertex) {
            $groups[$vertex->getAttribute('group')] = true;
        }

        return array_keys($groups);
    }

    /**
     * get set of all Vertices in the given group.
     *
     * @param int $group
     *
     * @return \PHGraph\Support\VertexCollection
     */
    public function getVerticesGroup(int $group): VertexCollection
    {
        return $this->vertices->filter(function ($vertex) use ($group) {
            return $vertex->getAttribute('group') === $group;
        });
    }
}
