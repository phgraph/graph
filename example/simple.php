<?php

$cwd = dirname(__FILE__);

require_once $cwd . '/../vendor/autoload.php';

use PHGraph\Graph;
use PHGraph\GraphViz\GraphViz;

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

$file_location = $graphviz->createImageFile();

rename($file_location, $cwd . DIRECTORY_SEPARATOR . 'simple.png');
