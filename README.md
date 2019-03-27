[![Build Status](https://travis-ci.org/phgraph/graph.svg?branch=master)](https://travis-ci.org/phgraph/graph)
[![Coverage Status](https://coveralls.io/repos/github/phgraph/graph/badge.svg?branch=master)](https://coveralls.io/github/phgraph/graph?branch=master)
[![StyleCI](https://github.styleci.io/repos/176066306/shield?branch=master)](https://github.styleci.io/repos/176066306)

# phgraph/graph
PHGraph is a modern mathematical graph/network library written in PHP.

## Installation

You can install the package via composer:

```bash
composer require phgraph/graph
```

## Usage

### Creation and Search

```php
use PHGraph\Graph;
use PHGraph\Search\BreadthFirst;
…

$graph = new Graph;
$columbus = $graph->newVertex([
    'name' => 'Columbus',
]);
$cleveland = $graph->newVertex([
    'name' => 'Cleveland',
]);
$cincinnati = $graph->newVertex([
    'name' => 'Cincinnati',
]);

$columbus->createEdge($cleveland);
$columbus->createEdge($cincinnati);

$search = new BreadthFirst($cincinnati);
if ($search->hasVertex($cleveland)) {
    echo "We can get from Cincinnati to Cleveland\n";
} else {
    echo "We can't get from Cincinnati to Cleveland\n";
}
```

### Graph drawing

This library has support for visualizing graphs as images using [GraphViz](http://www.graphviz.org/) "Graph Visualization Software". You will need GraphViz installed on your system for this to work.

```php
use PHGraph\Graph;
use PHGraph\GraphViz\GraphViz;
…

$graph = new Graph;
$columbus = $graph->newVertex([
    'name' => 'Columbus',
]);
$cleveland = $graph->newVertex([
    'name' => 'Cleveland',
]);
$cincinnati = $graph->newVertex([
    'name' => 'Cincinnati',
]);
$columbus->createEdge($cleveland);
$columbus->createEdge($cincinnati);

$graphviz = new GraphViz($graph);
// open the image on your system
$graphviz->display();
```

output:

![display output](/example/simple.png?raw=true)

### Algorithms

A graph library is rather boring without the ability to use algorithms on it, here is a list of the currently supported ones:

* [Search](https://en.wikipedia.org/wiki/Graph_traversal)
    * [Depth first](https://en.wikipedia.org/wiki/Depth-first_search)
    * [Breadth first](https://en.wikipedia.org/wiki/Breadth-first_search)
* [Shortest path](https://en.wikipedia.org/wiki/Shortest_path_problem)
    * [Dijkstra](https://en.wikipedia.org/wiki/Dijkstra%27s_algorithm)
    * [Moore-Bellman-Ford](https://en.wikipedia.org/wiki/Bellman%E2%80%93Ford_algorithm)
    * [Least Hops](https://en.wikipedia.org/wiki/Best-first_search)

# Development

## Installing dependencies
You will need [Composer](https://getcomposer.org/) for the development dependencies. Once you have that, run the following

```bash
$ composer install
```

## Running tests
You can run the current test suite with the following command

```bash
$ composer test
```

For static analysis of the code run the following

```bash
$ composer analyse
```

## Bug Reports
Bug reports for the current release version can be opened in this repository's [issue tracker](https://github.com/phgraph/graph/issues).

## Thanks

this was heavily inspired by [graphp/graph](https://github.com/graphp/graph).
