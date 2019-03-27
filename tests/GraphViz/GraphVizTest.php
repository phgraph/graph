<?php

namespace Tests\GraphViz;

use Mockery;
use PHGraph\Graph;
use PHGraph\GraphViz\GraphViz;
use PHGraph\Vertex;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use UnexpectedValueException;

class GraphVizTest extends TestCase
{
    /**
     * tearDown for PHPUnit.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @covers PHGraph\GraphViz\GraphViz::__construct
     *
     * @return void
     */
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(GraphViz::class, new GraphViz(new Graph));
    }

    /**
     * @covers PHGraph\GraphViz\GraphViz::display
     *
     * @return void
     */
    public function testDisplayOpenMacos(): void
    {
        /** @var \Mockery\Mock */
        $executableFinderMock = Mockery::mock('overload:' . ExecutableFinder::class);
        $executableFinderMock->shouldReceive('find')->andReturnUsing(function ($arg) {
            return [
                'xdg-open' => null,
                'open' => '/usr/bin/open',
                'start' => null,
            ][$arg];
        });

        /** @var \Mockery\Mock */
        $processMock = Mockery::mock('overload:' . Process::class);
        $processMock->shouldReceive('__construct')->withArgs(function ($command) {
            if ($command[0] === 'dot') {
                return true;
            }

            $this->assertEquals('/usr/bin/open', $command[0]);

            return '/usr/bin/open' === $command[0];
        })->andReturnSelf();
        $processMock->shouldReceive('run');
        $processMock->shouldReceive('isSuccessful')->andReturn(true);

        $graphviz = new GraphViz(new Graph);
        $graphviz->display();
    }

    /**
     * @covers PHGraph\GraphViz\GraphViz::display
     *
     * @return void
     */
    public function testDisplayOpenLinux(): void
    {
        /** @var \Mockery\Mock */
        $executableFinderMock = Mockery::mock('overload:' . ExecutableFinder::class);
        $executableFinderMock->shouldReceive('find')->andReturnUsing(function ($arg) {
            return [
                'xdg-open' => '/usr/bin/xdg-open',
                'open' => null,
                'start' => null,
            ][$arg];
        });

        /** @var \Mockery\Mock */
        $processMock = Mockery::mock('overload:' . Process::class);
        $processMock->shouldReceive('__construct')->withArgs(function ($command) {
            if ($command[0] === 'dot') {
                return true;
            }

            $this->assertEquals('/usr/bin/xdg-open', $command[0]);

            return '/usr/bin/xdg-open' === $command[0];
        })->andReturnSelf();
        $processMock->shouldReceive('run');
        $processMock->shouldReceive('isSuccessful')->andReturn(true);

        $graphviz = new GraphViz(new Graph);
        $graphviz->display();
    }

    /**
     * @covers PHGraph\GraphViz\GraphViz::display
     *
     * @return void
     */
    public function testDisplayOpenWindows(): void
    {
        /** @var \Mockery\Mock */
        $executableFinderMock = Mockery::mock('overload:' . ExecutableFinder::class);
        $executableFinderMock->shouldReceive('find')->andReturnUsing(function ($arg) {
            return [
                'xdg-open' => null,
                'open' => null,
                'start' => 'start',
            ][$arg];
        });

        /** @var \Mockery\Mock */
        $processMock = Mockery::mock('overload:' . Process::class);
        $processMock->shouldReceive('__construct')->withArgs(function ($command) {
            if ($command[0] === 'dot') {
                return true;
            }

            $this->assertEquals('start', $command[0]);

            return 'start' === $command[0];
        })->andReturnSelf();
        $processMock->shouldReceive('run');
        $processMock->shouldReceive('isSuccessful')->andReturn(true);

        $graphviz = new GraphViz(new Graph);
        $graphviz->display();
    }

    /**
     * @covers PHGraph\GraphViz\GraphViz::createImageData
     *
     * @return void
     */
    public function testCreateImageDataEmptyGraph(): void
    {
        /** @var \Mockery\Mock */
        $processMock = Mockery::mock('overload:' . Process::class);
        $processMock->shouldReceive('__construct')->withArgs(function ($command) {
            file_put_contents($command[5], 'test');

            return true;
        })->andReturnSelf();
        $processMock->shouldReceive('run');
        $processMock->shouldReceive('isSuccessful')->andReturn(true);

        $graphviz = new GraphViz(new Graph);

        $this->assertEquals('test', $graphviz->createImageData());
    }

    /**
     * @covers PHGraph\GraphViz\GraphViz::createImageFile
     *
     * @return void
     */
    public function testCreateImageFileEmptyGraph(): void
    {
        $return_file = null;

        /** @var \Mockery\Mock */
        $processMock = Mockery::mock('overload:' . Process::class);
        $processMock->shouldReceive('__construct')->withArgs(function ($command) use (&$return_file) {
            $return_file = $command[5];

            return true;
        })->andReturnSelf();
        $processMock->shouldReceive('run');
        $processMock->shouldReceive('isSuccessful')->andReturn(true);

        $graphviz = new GraphViz(new Graph);

        $file = $graphviz->createImageFile();
        $this->assertEquals($return_file, $file);
    }

    /**
     * @covers PHGraph\GraphViz\GraphViz::createImageFile
     *
     * @return void
     */
    public function testCreateImageFileThrowsException(): void
    {
        $this->expectException(UnexpectedValueException::class);

        /** @var \Mockery\Mock */
        $processMock = Mockery::mock('overload:' . Process::class);
        $processMock->shouldReceive('__construct')->andReturnSelf();
        $processMock->shouldReceive('run');
        $processMock->shouldReceive('isSuccessful')->andReturn(false);

        $graphviz = new GraphViz(new Graph);

        $graphviz->createImageFile();
    }

    /**
     * @covers PHGraph\GraphViz\GraphViz::createScript
     *
     * @return void
     */
    public function testCreateScriptEmptyGraph(): void
    {
        $graphviz = new GraphViz(new Graph);

        $this->assertEquals("graph {\n}\n", $graphviz->createScript());
    }

    /**
     * @covers PHGraph\GraphViz\GraphViz::createScript
     *
     * @return void
     */
    public function testCreateScriptEmptyGraphWithName(): void
    {
        $graph = new Graph;
        $graphviz = new GraphViz($graph);
        $graph->setAttribute('graphviz.name', 'Test Graph');

        $this->assertEquals("graph \"Test Graph\" {\n}\n", $graphviz->createScript());
    }

    /**
     * @covers PHGraph\GraphViz\GraphViz::createScript
     *
     * @return void
     */
    public function testCreateScriptIsolatedVertices(): void
    {
        $graph = new Graph;
        $a = new Vertex($graph);
        $b = new Vertex($graph);
        $graphviz = new GraphViz($graph);
        $a->setAttribute('name', 'a');
        $b->setAttribute('name', 'b');

        $this->assertEquals("graph {\n  \"a\"\n  \"b\"\n}\n", $graphviz->createScript());
    }

    /**
     * @covers PHGraph\GraphViz\GraphViz::createScript
     *
     * @return void
     */
    public function testCreateScriptEmptyGraphWithAttributes(): void
    {
        $graph = new Graph;
        $graphviz = new GraphViz($graph);
        $graph->setAttribute('graphviz.graph.bgcolor', 'transparent');
        $graph->setAttribute('graphviz.node.color', 'blue');
        $graph->setAttribute('graphviz.edge.color', 'grey');

        $this->assertEquals("graph {\n  graph [bgcolor=\"transparent\"]\n  node [color=\"blue\"]\n  edge [color=\"grey\"]\n}\n",
            $graphviz->createScript());
    }

    /**
     * @covers PHGraph\GraphViz\GraphViz::createScript
     *
     * @return void
     */
    public function testCreateScriptEmptyGraphDiscardBadAttributes(): void
    {
        $graph = new Graph;
        $graphviz = new GraphViz($graph);
        $graph->setAttribute('graphviz.vertex.color', 'blue');
        $graph->setAttribute('graphviz.unknown.color', 'red');

        $this->assertEquals("graph {\n}\n", $graphviz->createScript());
    }

    /**
     * @covers PHGraph\GraphViz\GraphViz::createScript
     *
     * @return void
     */
    public function testCreateScriptEscapesAttributes(): void
    {
        $graph = new Graph;
        $graphviz = new GraphViz($graph);
        $graph->setAttribute('graphviz.name', 'b¹²³ is; ok\\ay, "right"?');

        $this->assertEquals("graph \"b¹²³ is; ok\\\\ay, &quot;right&quot;?\" {\n}\n", $graphviz->createScript());
    }

    /**
     * @covers PHGraph\GraphViz\GraphViz::createScript
     *
     * @return void
     */
    public function testCreateScriptDirectedEdges(): void
    {
        $graph = new Graph;
        $a = new Vertex($graph);
        $b = new Vertex($graph);
        $a->createEdgeTo($b);
        $graphviz = new GraphViz($graph);
        $a->setAttribute('name', 'a');
        $b->setAttribute('name', 'b');

        $this->assertEquals("digraph {\n  \"a\" -> \"b\"\n}\n", $graphviz->createScript());
    }

    /**
     * @covers PHGraph\GraphViz\GraphViz::createScript
     *
     * @return void
     */
    public function testCreateScriptMixedEdges(): void
    {
        $graph = new Graph;
        $a = new Vertex($graph);
        $b = new Vertex($graph);
        $c = new Vertex($graph);
        $a->createEdgeTo($b);
        $c->createEdge($b);
        $graphviz = new GraphViz($graph);
        $a->setAttribute('name', 'a');
        $b->setAttribute('name', 'b');
        $c->setAttribute('name', 'c');

        $this->assertEquals("digraph {\n  \"a\" -> \"b\"\n  \"c\" -> \"b\" [dir=\"none\"]\n}\n", $graphviz->createScript());
    }

    /**
     * @covers PHGraph\GraphViz\GraphViz::createScript
     *
     * @return void
     */
    public function testCreateScriptGroupsMixedEdges(): void
    {
        $graph = new Graph;
        $a = new Vertex($graph, ['name' => 'a', 'group' => 0]);
        $b = new Vertex($graph, ['name' => 'b', 'group' => 1, 'graphviz.foo' => 'bar']);
        $c = new Vertex($graph, ['name' => 'c', 'group' => 1]);
        $a->createEdgeTo($b);
        $c->createEdge($b);
        $graphviz = new GraphViz($graph);

        $this->assertEquals("digraph {\n  subgraph cluster_0 {\n    label = 0\n    \"a\"\n  }\n  subgraph cluster_1 {\n    label = 1\n    \"b\" [foo=\"bar\"]\n    \"c\"\n  }\n  \"a\" -> \"b\"\n  \"c\" -> \"b\" [dir=\"none\"]\n}\n", $graphviz->createScript());
    }

    /**
     * @covers PHGraph\GraphViz\GraphViz::createScript
     *
     * @return void
     */
    public function testCreateScriptUndirectedEdgesWithIsolated(): void
    {
        $graph = new Graph;
        $a = new Vertex($graph);
        $b = new Vertex($graph);
        $c = new Vertex($graph);
        $d = new Vertex($graph);
        $a->createEdge($b);
        $c->createEdge($b);
        $graphviz = new GraphViz($graph);
        $a->setAttribute('name', 'a');
        $b->setAttribute('name', 'b');
        $c->setAttribute('name', 'c');
        $d->setAttribute('name', 'd');

        $this->assertEquals("graph {\n  \"d\"\n  \"a\" -- \"b\"\n  \"c\" -- \"b\"\n}\n", $graphviz->createScript());
    }

    /**
     * @covers PHGraph\GraphViz\GraphViz::createScript
     *
     * @return void
     */
    public function testCreateScriptVertexLabelsAndAttributes(): void
    {
        $graph = new Graph;
        $a = new Vertex($graph);
        $b = new Vertex($graph);
        $c = new Vertex($graph);
        $d = new Vertex($graph);
        $graphviz = new GraphViz($graph);
        $a->setAttribute('name', 'a');
        $a->setAttribute('balance', 1);
        $b->setAttribute('name', 'b');
        $b->setAttribute('balance', 0);
        $c->setAttribute('name', 'c');
        $c->setAttribute('balance', -1);
        $d->setAttribute('name', 'test');
        $d->setAttribute('graphviz.a', 'b');

        $this->assertEquals("graph {\n  \"a (+1)\"\n  \"b (0)\"\n  \"c (-1)\"\n  \"test\" [a=\"b\"]\n}\n", $graphviz->createScript());
    }

    /**
     * @covers PHGraph\GraphViz\GraphViz::createScript
     *
     * @return void
     */
    public function testCreateScriptEdgeLabels(): void
    {
        $graph = new Graph;
        $edge_a = $graph->newVertex(['name' => 'a1'])->createEdge($graph->newVertex(['name' => 'a2']));
        $edge_b = $graph->newVertex(['name' => 'b1'])->createEdge($graph->newVertex(['name' => 'b2']));
        $edge_c = $graph->newVertex(['name' => 'c1'])->createEdge($graph->newVertex(['name' => 'c2']));
        $edge_d = $graph->newVertex(['name' => 'd1'])->createEdge($graph->newVertex(['name' => 'd2']));
        $edge_e = $graph->newVertex(['name' => 'e1'])->createEdge($graph->newVertex(['name' => 'e2']));

        $edge_b->setAttribute('graphviz.numeric', 20);
        $edge_c->setAttribute('graphviz.textual', 'forty');
        $edge_d->setAttributes(['graphviz.1' => 1, 'graphviz.2' => 2]);
        $edge_e->setAttributes(['graphviz.a' => 'b', 'graphviz.c' => 'd']);
        $graphviz = new GraphViz($graph);

        $this->assertEquals("graph {\n  \"a1\" -- \"a2\"\n  \"b1\" -- \"b2\" [numeric=20]\n  \"c1\" -- \"c2\" [textual=\"forty\"]\n  \"d1\" -- \"d2\" [1=1 2=2]\n  \"e1\" -- \"e2\" [a=\"b\" c=\"d\"]\n}\n", $graphviz->createScript());
    }

    /**
     * @covers PHGraph\GraphViz\GraphViz::createScript
     *
     * @return void
     */
    public function testCreateScriptEdgeAttributes(): void
    {
        $graph = new Graph;

        $graph->newVertex(['name' => '1a'])->createEdge($graph->newVertex(['name' => '1b']));
        $graph->newVertex(['name' => '2a'])->createEdge($graph->newVertex(['name' => '2b']))->setAttribute('weight', 20);
        $graph->newVertex(['name' => '3a'])->createEdge($graph->newVertex(['name' => '3b']))->setAttribute('capacity', 30);
        $graph->newVertex(['name' => '4a'])->createEdge($graph->newVertex(['name' => '4b']))->setAttribute('flow', 40);
        $graph->newVertex(['name' => '5a'])->createEdge($graph->newVertex(['name' => '5b']))->setAttributes([
            'flow' => 50,
            'capacity' => 60,
        ]);
        $graph->newVertex(['name' => '6a'])->createEdge($graph->newVertex(['name' => '6b']))->setAttributes([
            'flow' => 60,
            'capacity' => 70,
            'weight' => 80,
        ]);
        $graph->newVertex(['name' => '7a'])->createEdge($graph->newVertex(['name' => '7b']))->setAttributes([
            'flow' => 70,
            'graphviz.label' => 'prefixed',
        ]);

        $graphviz = new GraphViz($graph);

        $this->assertEquals("graph {\n  \"1a\" -- \"1b\"\n  \"2a\" -- \"2b\" [label=20]\n  \"3a\" -- \"3b\" [label=\"0/30\"]\n  \"4a\" -- \"4b\" [label=\"40/∞\"]\n  \"5a\" -- \"5b\" [label=\"50/60\"]\n  \"6a\" -- \"6b\" [label=\"60/70/80\"]\n  \"7a\" -- \"7b\" [label=\"prefixed 70/∞\"]\n}\n", $graphviz->createScript());
    }

    /**
     * @covers PHGraph\GraphViz\GraphViz::escape
     *
     * @return void
     */
    public function testEscapeEmpty(): void
    {
        $graphviz = new GraphViz(new Graph);

        $this->assertEquals('""', $graphviz->escape(''));
    }

    /**
     * @covers PHGraph\GraphViz\GraphViz::escape
     *
     * @return void
     */
    public function testEscapeNumeric(): void
    {
        $graphviz = new GraphViz(new Graph);

        $this->assertEquals('23', $graphviz->escape('23'));
    }

    /**
     * @covers PHGraph\GraphViz\GraphViz::escape
     *
     * @return void
     */
    public function testEscapeString(): void
    {
        $graphviz = new GraphViz(new Graph);

        $this->assertEquals('"yes"', $graphviz->escape('yes'));
    }

    /**
     * @covers PHGraph\GraphViz\GraphViz::escapeAttributes
     *
     * @return void
     */
    public function testEscapeAttributesEmpty(): void
    {
        $graphviz = new GraphViz(new Graph);

        $this->assertEquals('', $graphviz->escapeAttributes([]));
    }

    /**
     * @covers PHGraph\GraphViz\GraphViz::escapeAttributes
     *
     * @return void
     */
    public function testEscapeAttributesSingle(): void
    {
        $graphviz = new GraphViz(new Graph);

        $this->assertEquals('[hello="yes"]', $graphviz->escapeAttributes(['hello' => 'yes']));
    }

    /**
     * @covers PHGraph\GraphViz\GraphViz::escapeAttributes
     *
     * @return void
     */
    public function testEscapeAttributesMultiple(): void
    {
        $graphviz = new GraphViz(new Graph);

        $this->assertEquals('[hello="yes" foo="bar" baz="quux"]', $graphviz->escapeAttributes(['hello' => 'yes', 'foo' => 'bar', 'baz' => 'quux']));
    }

    /**
     * @covers PHGraph\GraphViz\GraphViz::getLayoutVertex
     *
     * @return void
     */
    public function testGetLayoutVertex(): void
    {
        $graph = new Graph;
        $vertex = new Vertex($graph, [
            'name' => 'a',
            'graphviz.label' => 'red',
            'balance' => 10,
            'graphviz.foo' => 'bar',
        ]);

        $graphviz = new GraphViz($graph);

        $this->assertEquals([
            'name' => 'a (+10)',
            'label' => 'red',
            'foo' => 'bar',
        ], $graphviz->getLayoutVertex($vertex));
    }
}
