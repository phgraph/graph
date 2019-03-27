<?php

namespace PHGraph\GraphViz;

use PHGraph\Edge;
use PHGraph\Graph;
use PHGraph\Vertex;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use UnexpectedValueException;

/**
 * Create GraphViz related things.
 */
class GraphViz
{
    /** @var string */
    private $format;
    /** @var string */
    private $executable;
    /** @var \PHGraph\Graph */
    protected $graph;

    /**
     * instantiate new graphviz wrapper.
     *
     * @param \PHGraph\Graph $graph      Graph to operate on
     * @param string         $format     @see `dot -T`
     * @param string         $executable program to call
     *
     * @return void
     */
    public function __construct(Graph $graph, string $format = 'png', string $executable = 'dot')
    {
        $this->graph = $graph;
        $this->format = $format;
        $this->executable = $executable;
    }

    /**
     * create and display image for this graph.
     *
     * @return void
     */
    public function display(): void
    {
        $temporary_file = $this->createImageFile();

        $executableFinder = new ExecutableFinder;
        $executable = $executableFinder->find('xdg-open') ?? $executableFinder->find('open');

        // probably windows based system
        if ($executable === null) {
            $executable = $executableFinder->find('start');
            $process = new Process([$executable, '', $temporary_file]);
            $process->run();

            return;
        }

        $process = new Process([$executable, $temporary_file]);
        $process->run();
    }

    /**
     * create image file data contents for this graph.
     *
     * @return string
     */
    public function createImageData(): string
    {
        $file = $this->createImageFile();
        $data = file_get_contents($file);
        unlink($file);

        return $data ?: '';
    }

    /**
     * create image file for this graph
     *
     * @throws UnexpectedValueException
     *
     * @return string
     */
    public function createImageFile(): string
    {
        $script = $this->createScript();

        $temporary_file = tempnam(sys_get_temp_dir(), 'graphviz');
        if ($temporary_file === false) {
            // @codeCoverageIgnoreStart
            throw new UnexpectedValueException('Unable to get temporary file name for graphviz script');
            // @codeCoverageIgnoreEnd
        }

        if (false === file_put_contents($temporary_file, $script, LOCK_EX)) {
            // @codeCoverageIgnoreStart
            throw new UnexpectedValueException('Unable to write graphviz script to temporary file');
            // @codeCoverageIgnoreEnd
        }

        $process = new Process([
            $this->executable,
            '-T',
            $this->format,
            $temporary_file,
            '-o',
            $temporary_file . '.' . $this->format,
        ]);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new UnexpectedValueException("Unable to invoke `$this->executable` to create image file.");
        }

        unlink($temporary_file);

        return $temporary_file . '.' . $this->format;
    }

    /**
     * create graphviz script representing this graph.
     *
     * @return string
     */
    public function createScript(): string
    {
        $directed = $this->graph->hasDirected();

        $name = $this->graph->getAttribute('graphviz.name');

        if ($name !== null) {
            $name = $this->escape($name) . ' ';
        }

        $script = ($directed ? 'di' : '') . 'graph ' . $name . '{' . PHP_EOL;

        foreach (['graph', 'node', 'edge'] as $part) {
            $attributes = $this->graph->getAttributesWithPrefix("graphviz.$part.");

            if (count($attributes) === 0) {
                continue;
            }

            $script .= sprintf('  %s %s%s', $part, $this->escapeAttributes($attributes), PHP_EOL);
        }

        // only append group number to vertex label if there are at least 2 different groups
        $showGroups = ($this->graph->getNumberOfGroups() > 1);

        if ($showGroups) {
            $gid = 0;
            // put each group of vertices in a separate subgraph cluster
            foreach ($this->graph->getGroups() as $group) {
                $script .= sprintf('  subgraph cluster_%s {%s', $gid++, PHP_EOL);
                $script .= sprintf('    label = %s%s', $this->escape($group), PHP_EOL);
                foreach($this->graph->getVerticesGroup($group) as $vid => $vertex) {
                    $layout = $this->getLayoutVertex($vertex);

                    $script .= sprintf('    %s', $this->escape($vertex->getAttribute('name', $vid)));
                    if ($layout) {
                        $script .= sprintf(' %s', $this->escapeAttributes($layout));
                    }
                    $script .= PHP_EOL;
                }
                $script .= sprintf('  }%s', PHP_EOL);
            }
        } else {
            foreach ($this->graph->getVertices() as $vid => $vertex) {
                if (count($vertex->getEdges()) !== 0) {
                    continue;
                }

                $layout = $this->getLayoutVertex($vertex);

                if ($layout || $this->graph->isVertexIsolated($vertex)) {
                    $script .= sprintf('  %s', $this->escape($layout['name'] ?? $vertex->getAttribute('name', $vid)));
                    unset($layout['name']);
                    if (count($layout)) {
                        $script .= sprintf(' %s', $this->escapeAttributes($layout));
                    }
                    $script .= PHP_EOL;
                }
            }
        }

        foreach ($this->graph->getEdges() as $currentEdge) {
            $currentStartVertex = $currentEdge->getFrom();
            $currentTargetVertex = $currentEdge->getTo();

            $label_from = $currentStartVertex->getAttribute('name', $currentStartVertex->getId());
            $label_to = $currentTargetVertex->getAttribute('name',$currentTargetVertex->getId());

            $script .= sprintf('  %s %s %s', $this->escape($label_from), $directed ? '->' : '--', $this->escape($label_to));

            $layout = $currentEdge->getAttributesWithPrefix('graphviz.');

            // use flow/capacity/weight as edge label
            $label = null;

            $flow = $currentEdge->getAttribute('flow');
            $capacity = $currentEdge->getAttribute('capacity');
            if ($flow !== null) {
                // null capacity = infinite capacity
                $label = $flow . '/' . ($capacity === null ? '∞' : $capacity);
                // capacity set, but not flow (assume zero flow)
            } elseif ($capacity !== null) {
                $label = '0/' . $capacity;
            }

            $weight = $currentEdge->getAttribute('weight');
            if ($weight !== null) {
                if ($label === null) {
                    $label = $weight;
                } else {
                    $label .= '/' . $weight;
                }
            }

            if ($label !== null) {
                if (isset($layout['label'])) {
                    $layout['label'] .= ' ' . $label;
                } else {
                    $layout['label'] = $label;
                }
            }

            if ($directed && !$currentEdge->directed()) {
                $layout['dir'] = 'none';
            }

            if ($layout) {
                $script .= ' ' . $this->escapeAttributes($layout);
            }

            $script .= PHP_EOL;
        }
        $script .= '}' . PHP_EOL;

        return $script;
    }

    /**
     * escape given id string and wrap in quotes if needed.
     *
     * @link http://graphviz.org/content/dot-language
     *
     * @param string $id
     *
     * @return string
     */
    public function escape(string $id): string
    {
        if (preg_match('/^(?:\-?(?:\.\d+|\d+(?:\.\d+)?))$/i', $id)) {
            return $id;
        }

        return '"' . str_replace(array('&', '<', '>', '"', "'", '\\', "\n"), array('&amp;', '&lt;', '&gt;', '&quot;', '&apos;', '\\\\', '\\l'), $id) . '"';
    }

    /**
     * get escaped attribute string for given array of (unescaped) attributes
     *
     * @param array $attrs
     *
     * @return string
     */
    public function escapeAttributes(array $attrs): string
    {
        if (count($attrs) === 0) {
            return '';
        }

        $script = [];
        foreach ($attrs as $name => $value) {
            $script[] = sprintf('%s=%s', $name, $this->escape($value));
        }

        return sprintf('[%s]', implode(' ', $script));
    }

    /**
     * Get the layout attributes for vertex.
     *
     * @param \PHGraph\Vertex $vertex vertex to get attributes for
     *
     * @return array
     */
    public function getLayoutVertex(Vertex $vertex): array
    {
        $layout = $vertex->getAttributesWithPrefix('graphviz.');

        $balance = $vertex->getAttribute('balance');
        if ($balance !== null) {
            if ($balance > 0) {
                $balance = '+' . $balance;
            }
            $layout['name'] = $vertex->getAttribute('name', $vertex->getId()) . ' (' . $balance . ')';
        }

        return $layout;
    }
}