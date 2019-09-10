<?php

namespace Tests\ShortestPath;

use OutOfBoundsException;
use PHGraph\Graph;
use PHGraph\ShortestPath\Dijkstra;
use PHGraph\Support\EdgeCollection;
use PHGraph\Vertex;
use PHGraph\Walk;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

class DijkstraTest extends TestCase
{
    /**
     * @covers PHGraph\ShortestPath\Dijkstra::__construct
     *
     * @return void
     */
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(Dijkstra::class, new Dijkstra(new Vertex(new Graph)));
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::__construct
     *
     * @return void
     */
    public function testNegativeWeightsThrowException(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $edge = $vertex_a->createEdgeTo($vertex_b);

        $edge->setAttribute('weight', -1);

        $bf = new Dijkstra($vertex_a);
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::getWalkTo
     *
     * @return void
     */
    public function testGetWalkToReturnsWalk(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_a->createEdgeTo($vertex_b);

        $bf = new Dijkstra($vertex_a);
        $this->assertInstanceOf(Walk::class, $bf->getWalkTo($vertex_b));
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::hasVertex
     *
     * @return void
     */
    public function testHasVertexTrue(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_a->createEdgeTo($vertex_b);

        $bf = new Dijkstra($vertex_a);
        $this->assertTrue($bf->hasVertex($vertex_b));
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::hasVertex
     *
     * @return void
     */
    public function testHasVertexFalse(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_b->createEdgeTo($vertex_a);

        $bf = new Dijkstra($vertex_a);
        $this->assertFalse($bf->hasVertex($vertex_b));
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::createGraph
     *
     * @return void
     */
    public function testCreateGraphReturnsGraph(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_a->createEdgeTo($vertex_b);

        $bf = new Dijkstra($vertex_a);
        $this->assertInstanceOf(Graph::class, $bf->createGraph());
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::getDistance
     *
     * @return void
     */
    public function testGetDistance(): void
    {
        $vertices = [];
        $graph = new Graph;
        for ($i = 0; $i < 50; $i++) {
            $vertices[$i] = new Vertex($graph);
            if ($i !== 0) {
                $edge = $vertices[$i]->createEdge($vertices[$i - 1]);
                $edge->setAttribute('weight', 1);
            }
        }

        $bf = new Dijkstra($vertices[0]);

        $this->assertEquals(45, $bf->getDistance($vertices[45]));
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::getDistance
     *
     * @return void
     */
    public function testGetDistanceThrowsOutOfBoundsIfVertexNotReachable(): void
    {
        $this->expectException(OutOfBoundsException::class);

        $vertices = [];
        $graph = new Graph;
        for ($i = 0; $i < 50; $i++) {
            $vertices[$i] = new Vertex($graph);
            if ($i !== 0) {
                $vertices[$i]->createEdge($vertices[$i - 1]);
            }
        }
        $vertices[51] = new Vertex($graph);

        $bf = new Dijkstra($vertices[0]);

        $bf->getDistance($vertices[51]);
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::getEdgesTo
     *
     * @return void
     */
    public function testGetEdgesToThrowsOutOfBoundsIfVerticesOnDifferentGraphs(): void
    {
        $this->expectException(OutOfBoundsException::class);

        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex(new Graph);

        $bf = new Dijkstra($vertex_a);

        $bf->getEdgesTo($vertex_b);
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::getEdgesTo
     *
     * @return void
     */
    public function testGetEdgesToThrowsOutOfBoundsIfVerticesArentConnected(): void
    {
        $this->expectException(OutOfBoundsException::class);

        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);

        $bf = new Dijkstra($vertex_a);

        $bf->getEdgesTo($vertex_b);
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::getEdgesTo
     *
     * @return void
     */
    public function testGetEdgesIsEdgeCollection(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_a->createEdgeTo($vertex_b);

        $bf = new Dijkstra($vertex_a);

        $this->assertInstanceOf(EdgeCollection::class, $bf->getEdgesTo($vertex_b));
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::getEdgesTo
     *
     * @return void
     */
    public function testGetEdgesTakesShorterPath(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_c = new Vertex($graph);
        $vertex_d = new Vertex($graph);
        $vertex_e = new Vertex($graph);

        $edge_a = $vertex_a->createEdge($vertex_b);
        $edge_b = $vertex_e->createEdge($vertex_b);

        $vertex_a->createEdge($vertex_c);
        $vertex_d->createEdge($vertex_c);
        $vertex_d->createEdge($vertex_e);

        $bf = new Dijkstra($vertex_a);

        $this->assertEquals([$edge_a, $edge_b], $bf->getEdgesTo($vertex_e)->all());
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::getEdgesTo
     *
     * @return void
     */
    public function testGetEdgesToSameVertexIsEmpty(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);

        $bf = new Dijkstra($vertex_a);

        $this->assertEmpty($bf->getEdgesTo($vertex_a)->all());
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::getDistanceMap
     *
     * @return void
     */
    public function testGetDistanceMap(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $edge = $vertex_a->createEdgeTo($vertex_b);
        $edge->setAttribute('weight', 1);

        $bf = new Dijkstra($vertex_a);

        $this->assertEquals([
            $vertex_a->getId() => 0.0,
            $vertex_b->getId() => 1.0,
        ], $bf->getDistanceMap());
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::getDistanceMap
     *
     * @return void
     */
    public function testGetDistanceMapIgnoresUnreachableVertices(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_c = new Vertex($graph);
        $edge = $vertex_a->createEdgeTo($vertex_b);
        $edge->setAttribute('weight', 1);

        $bf = new Dijkstra($vertex_a);

        $this->assertEquals([
            $vertex_a->getId() => 0.0,
            $vertex_b->getId() => 1.0,
        ], $bf->getDistanceMap());
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::getEdges
     *
     * @return void
     */
    public function testGetEdges(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $edge = $vertex_a->createEdgeTo($vertex_b);

        $bf = new Dijkstra($vertex_a);

        $this->assertEquals([$edge], $bf->getEdges()->all());
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::getEdges
     *
     * @return void
     */
    public function testGetEdgesCached(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_a->createEdgeTo($vertex_b);

        $bf = new Dijkstra($vertex_a);

        $edges = $bf->getEdges()->all();

        $vertex_c = new Vertex($graph);
        $vertex_a->createEdgeTo($vertex_c);

        $this->assertEquals($edges, $bf->getEdges()->all());
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::getEdges
     *
     * @return void
     */
    public function testGetEdgesPicksSmallerWeight(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $edge_a = $vertex_a->createEdgeTo($vertex_b);
        $edge_b = $vertex_a->createEdgeTo($vertex_b);

        $edge_a->setAttribute('weight', 1);
        $edge_b->setAttribute('weight', 0.5);

        $bf = new Dijkstra($vertex_a);

        $this->assertEquals([$edge_b], $bf->getEdges()->all());
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::getEdges
     *
     * @return void
     */
    public function testGetEdgesPicksSmallerWeightMultiConnected(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_c = new Vertex($graph);
        $edge_a = $vertex_a->createEdgeTo($vertex_b);
        $edge_b = $vertex_a->createEdgeTo($vertex_b);
        $edge_c = $vertex_a->createEdge($vertex_c);
        $edge_d = $vertex_b->createEdge($vertex_c);

        $edge_a->setAttribute('weight', 1);
        $edge_b->setAttribute('weight', 0.5);
        $edge_c->setAttribute('weight', 4);
        $edge_d->setAttribute('weight', 5);

        $bf = new Dijkstra($vertex_a);

        $this->assertEquals([$edge_b, $edge_c], $bf->getEdges()->all());
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::getEdges
     *
     * @return void
     */
    public function testGetEdgesPicksSmallerWeightUnreachableVertices(): void
    {
        $graph = new Graph;
        $vertex_a = new Vertex($graph);
        $vertex_b = new Vertex($graph);
        $vertex_c = new Vertex($graph);
        $edge_a = $vertex_a->createEdgeTo($vertex_b);
        $edge_b = $vertex_a->createEdgeTo($vertex_b);

        $edge_a->setAttribute('weight', 1);
        $edge_b->setAttribute('weight', 0.5);

        $bf = new Dijkstra($vertex_a);

        $this->assertEquals([$edge_b], $bf->getEdges()->all());
    }

    /**
     * @covers PHGraph\ShortestPath\Dijkstra::getEdgesTo
     *
     * @return void
     */
    public function testGetEdgesToLargeGraph(): void
    {
        [$v, $e] = $this->getLargeTestGraph();

        $bf = new Dijkstra($v[250]);

        $this->assertEquals([
            $e[249],
            $e[248],
            $e[247],
            $e[246],
            $e[245],
            $e[244],
            $e[243],
            $e[242],
            $e[278],
            $e[106],
            $e[105],
            $e[104],
            $e[103],
            $e[102],
            $e[101],
            $e[340],
            $e[350],
            $e[356],
        ], $bf->getEdgesTo($v[0])->ordered()->all());
    }

    /**
     * Get a predefined larger graph to test with.
     *
     * @return array
     */
    public function getLargeTestGraph(): array
    {
        $v = [];
        $e = [];
        $graph = new Graph;

        $v[0] = $graph->newVertex(['name' => 'Karelle']);
        $v[1] = $graph->newVertex(['name' => 'Kristoffer']);
        $v[2] = $graph->newVertex(['name' => 'Frederique']);
        $v[3] = $graph->newVertex(['name' => 'Stephany']);
        $v[4] = $graph->newVertex(['name' => 'Jensen']);
        $v[5] = $graph->newVertex(['name' => 'Simone']);
        $v[6] = $graph->newVertex(['name' => 'Arvid']);
        $v[7] = $graph->newVertex(['name' => 'Carlos']);
        $v[8] = $graph->newVertex(['name' => 'Gonzalo']);
        $v[9] = $graph->newVertex(['name' => 'Roger']);
        $v[10] = $graph->newVertex(['name' => 'Oscar']);
        $v[11] = $graph->newVertex(['name' => 'Laurie']);
        $v[12] = $graph->newVertex(['name' => 'Dasia']);
        $v[13] = $graph->newVertex(['name' => 'Skylar']);
        $v[14] = $graph->newVertex(['name' => 'Vivien']);
        $v[15] = $graph->newVertex(['name' => 'Katelyn']);
        $v[16] = $graph->newVertex(['name' => 'Sonia']);
        $v[17] = $graph->newVertex(['name' => 'Ozella']);
        $v[18] = $graph->newVertex(['name' => 'Daphney']);
        $v[19] = $graph->newVertex(['name' => 'Lysanne']);
        $v[20] = $graph->newVertex(['name' => 'Justine']);
        $v[21] = $graph->newVertex(['name' => 'Ursula']);
        $v[22] = $graph->newVertex(['name' => 'Gia']);
        $v[23] = $graph->newVertex(['name' => 'Bennie']);
        $v[24] = $graph->newVertex(['name' => 'Enid']);
        $v[25] = $graph->newVertex(['name' => 'Evelyn']);
        $v[26] = $graph->newVertex(['name' => 'Kareem']);
        $v[27] = $graph->newVertex(['name' => 'Jaylen']);
        $v[28] = $graph->newVertex(['name' => 'Torrey']);
        $v[29] = $graph->newVertex(['name' => 'Rickey']);
        $v[30] = $graph->newVertex(['name' => 'Lauriane']);
        $v[31] = $graph->newVertex(['name' => 'Xander']);
        $v[32] = $graph->newVertex(['name' => 'Hayley']);
        $v[33] = $graph->newVertex(['name' => 'Jude']);
        $v[34] = $graph->newVertex(['name' => 'Maryjane']);
        $v[35] = $graph->newVertex(['name' => 'Gus']);
        $v[36] = $graph->newVertex(['name' => 'Roel']);
        $v[37] = $graph->newVertex(['name' => 'Aubrey']);
        $v[38] = $graph->newVertex(['name' => 'Chasity']);
        $v[39] = $graph->newVertex(['name' => 'Cory']);
        $v[40] = $graph->newVertex(['name' => 'Madie']);
        $v[41] = $graph->newVertex(['name' => 'Alessandra']);
        $v[42] = $graph->newVertex(['name' => 'Wilfrid']);
        $v[43] = $graph->newVertex(['name' => 'Cruz']);
        $v[44] = $graph->newVertex(['name' => 'Talia']);
        $v[45] = $graph->newVertex(['name' => 'Sydnie']);
        $v[46] = $graph->newVertex(['name' => 'Julio']);
        $v[47] = $graph->newVertex(['name' => 'Samanta']);
        $v[48] = $graph->newVertex(['name' => 'Dimitri']);
        $v[49] = $graph->newVertex(['name' => 'Winnifred']);
        $v[50] = $graph->newVertex(['name' => 'Kassandra']);
        $v[51] = $graph->newVertex(['name' => 'Warren']);
        $v[52] = $graph->newVertex(['name' => 'Amir']);
        $v[53] = $graph->newVertex(['name' => 'Marianne']);
        $v[54] = $graph->newVertex(['name' => 'Davin']);
        $v[55] = $graph->newVertex(['name' => 'Jose']);
        $v[56] = $graph->newVertex(['name' => 'Hank']);
        $v[57] = $graph->newVertex(['name' => 'Arnoldo']);
        $v[58] = $graph->newVertex(['name' => 'Ardella']);
        $v[59] = $graph->newVertex(['name' => 'Daniella']);
        $v[60] = $graph->newVertex(['name' => 'Nikolas']);
        $v[61] = $graph->newVertex(['name' => 'Genevieve']);
        $v[62] = $graph->newVertex(['name' => 'Alberto']);
        $v[63] = $graph->newVertex(['name' => 'Freida']);
        $v[64] = $graph->newVertex(['name' => 'Rosie']);
        $v[65] = $graph->newVertex(['name' => 'Ulices']);
        $v[66] = $graph->newVertex(['name' => 'Bridgette']);
        $v[67] = $graph->newVertex(['name' => 'Arthur']);
        $v[68] = $graph->newVertex(['name' => 'Al']);
        $v[69] = $graph->newVertex(['name' => 'Adan']);
        $v[70] = $graph->newVertex(['name' => 'Blanca']);
        $v[71] = $graph->newVertex(['name' => 'Miracle']);
        $v[72] = $graph->newVertex(['name' => 'Therese']);
        $v[73] = $graph->newVertex(['name' => 'Laury']);
        $v[74] = $graph->newVertex(['name' => 'Orville']);
        $v[75] = $graph->newVertex(['name' => 'Ricky']);
        $v[76] = $graph->newVertex(['name' => 'Olga']);
        $v[77] = $graph->newVertex(['name' => 'Isaiah']);
        $v[78] = $graph->newVertex(['name' => 'Shaun']);
        $v[79] = $graph->newVertex(['name' => 'Katrine']);
        $v[80] = $graph->newVertex(['name' => 'Aurore']);
        $v[81] = $graph->newVertex(['name' => 'Kelli']);
        $v[82] = $graph->newVertex(['name' => 'Destany']);
        $v[83] = $graph->newVertex(['name' => 'Rebekah']);
        $v[84] = $graph->newVertex(['name' => 'Bianka']);
        $v[85] = $graph->newVertex(['name' => 'Ottis']);
        $v[86] = $graph->newVertex(['name' => 'Mathilde']);
        $v[87] = $graph->newVertex(['name' => 'Kale']);
        $v[88] = $graph->newVertex(['name' => 'Oleta']);
        $v[89] = $graph->newVertex(['name' => 'Leonard']);
        $v[90] = $graph->newVertex(['name' => 'Kianna']);
        $v[91] = $graph->newVertex(['name' => 'Karlee']);
        $v[92] = $graph->newVertex(['name' => 'Genesis']);
        $v[93] = $graph->newVertex(['name' => 'Leonard']);
        $v[94] = $graph->newVertex(['name' => 'Jazmin']);
        $v[95] = $graph->newVertex(['name' => 'Jonatan']);
        $v[96] = $graph->newVertex(['name' => 'Mary']);
        $v[97] = $graph->newVertex(['name' => 'Rocio']);
        $v[98] = $graph->newVertex(['name' => 'Jarrett']);
        $v[99] = $graph->newVertex(['name' => 'Karlie']);
        $v[100] = $graph->newVertex(['name' => 'Torrey']);
        $v[101] = $graph->newVertex(['name' => 'Valentin']);
        $v[102] = $graph->newVertex(['name' => 'Gilbert']);
        $v[103] = $graph->newVertex(['name' => 'Aurore']);
        $v[104] = $graph->newVertex(['name' => 'Roosevelt']);
        $v[105] = $graph->newVertex(['name' => 'Zella']);
        $v[106] = $graph->newVertex(['name' => 'Jaden']);
        $v[107] = $graph->newVertex(['name' => 'Isabell']);
        $v[108] = $graph->newVertex(['name' => 'Roslyn']);
        $v[109] = $graph->newVertex(['name' => 'Carolanne']);
        $v[110] = $graph->newVertex(['name' => 'Mathilde']);
        $v[111] = $graph->newVertex(['name' => 'Saul']);
        $v[112] = $graph->newVertex(['name' => 'Lonzo']);
        $v[113] = $graph->newVertex(['name' => 'Beverly']);
        $v[114] = $graph->newVertex(['name' => 'Jarrod']);
        $v[115] = $graph->newVertex(['name' => 'Dock']);
        $v[116] = $graph->newVertex(['name' => 'Dell']);
        $v[117] = $graph->newVertex(['name' => 'Ayana']);
        $v[118] = $graph->newVertex(['name' => 'Florence']);
        $v[119] = $graph->newVertex(['name' => 'Talon']);
        $v[120] = $graph->newVertex(['name' => 'Furman']);
        $v[121] = $graph->newVertex(['name' => 'Ignacio']);
        $v[122] = $graph->newVertex(['name' => 'Hassan']);
        $v[123] = $graph->newVertex(['name' => 'Chanel']);
        $v[124] = $graph->newVertex(['name' => 'Robbie']);
        $v[125] = $graph->newVertex(['name' => 'Timmy']);
        $v[126] = $graph->newVertex(['name' => 'Drew']);
        $v[127] = $graph->newVertex(['name' => 'Annabel']);
        $v[128] = $graph->newVertex(['name' => 'Hugh']);
        $v[129] = $graph->newVertex(['name' => 'Astrid']);
        $v[130] = $graph->newVertex(['name' => 'Asha']);
        $v[131] = $graph->newVertex(['name' => 'Aliza']);
        $v[132] = $graph->newVertex(['name' => 'Lenna']);
        $v[133] = $graph->newVertex(['name' => 'Lela']);
        $v[134] = $graph->newVertex(['name' => 'Bette']);
        $v[135] = $graph->newVertex(['name' => 'Queenie']);
        $v[136] = $graph->newVertex(['name' => 'Blaise']);
        $v[137] = $graph->newVertex(['name' => 'Earnestine']);
        $v[138] = $graph->newVertex(['name' => 'Gregoria']);
        $v[139] = $graph->newVertex(['name' => 'Elenora']);
        $v[140] = $graph->newVertex(['name' => 'Connie']);
        $v[141] = $graph->newVertex(['name' => 'Yadira']);
        $v[142] = $graph->newVertex(['name' => 'Joany']);
        $v[143] = $graph->newVertex(['name' => 'Ollie']);
        $v[144] = $graph->newVertex(['name' => 'Adaline']);
        $v[145] = $graph->newVertex(['name' => 'Diamond']);
        $v[146] = $graph->newVertex(['name' => 'Joanne']);
        $v[147] = $graph->newVertex(['name' => 'Myrtice']);
        $v[148] = $graph->newVertex(['name' => 'Nellie']);
        $v[149] = $graph->newVertex(['name' => 'Demarcus']);
        $v[150] = $graph->newVertex(['name' => 'Kelvin']);
        $v[151] = $graph->newVertex(['name' => 'Karine']);
        $v[152] = $graph->newVertex(['name' => 'Braeden']);
        $v[153] = $graph->newVertex(['name' => 'Domingo']);
        $v[154] = $graph->newVertex(['name' => 'Elena']);
        $v[155] = $graph->newVertex(['name' => 'Elvie']);
        $v[156] = $graph->newVertex(['name' => 'Wallace']);
        $v[157] = $graph->newVertex(['name' => 'Dee']);
        $v[158] = $graph->newVertex(['name' => 'Toni']);
        $v[159] = $graph->newVertex(['name' => 'Mariela']);
        $v[160] = $graph->newVertex(['name' => 'Marco']);
        $v[161] = $graph->newVertex(['name' => 'Giovanna']);
        $v[162] = $graph->newVertex(['name' => 'Blaze']);
        $v[163] = $graph->newVertex(['name' => 'Agnes']);
        $v[164] = $graph->newVertex(['name' => 'Jaylon']);
        $v[165] = $graph->newVertex(['name' => 'Dedrick']);
        $v[166] = $graph->newVertex(['name' => 'Obie']);
        $v[167] = $graph->newVertex(['name' => 'Eleonore']);
        $v[168] = $graph->newVertex(['name' => 'Gavin']);
        $v[169] = $graph->newVertex(['name' => 'Ida']);
        $v[170] = $graph->newVertex(['name' => 'Javon']);
        $v[171] = $graph->newVertex(['name' => 'Giovanni']);
        $v[172] = $graph->newVertex(['name' => 'Sydnee']);
        $v[173] = $graph->newVertex(['name' => 'Brendan']);
        $v[174] = $graph->newVertex(['name' => 'Avis']);
        $v[175] = $graph->newVertex(['name' => 'Sheldon']);
        $v[176] = $graph->newVertex(['name' => 'Kasandra']);
        $v[177] = $graph->newVertex(['name' => 'Carlotta']);
        $v[178] = $graph->newVertex(['name' => 'Dimitri']);
        $v[179] = $graph->newVertex(['name' => 'Gladyce']);
        $v[180] = $graph->newVertex(['name' => 'Aletha']);
        $v[181] = $graph->newVertex(['name' => 'Rosalee']);
        $v[182] = $graph->newVertex(['name' => 'Armand']);
        $v[183] = $graph->newVertex(['name' => 'Rozella']);
        $v[184] = $graph->newVertex(['name' => 'Thalia']);
        $v[185] = $graph->newVertex(['name' => 'Lura']);
        $v[186] = $graph->newVertex(['name' => 'Ruthe']);
        $v[187] = $graph->newVertex(['name' => 'Milford']);
        $v[188] = $graph->newVertex(['name' => 'Korey']);
        $v[189] = $graph->newVertex(['name' => 'Eldon']);
        $v[190] = $graph->newVertex(['name' => 'Abigale']);
        $v[191] = $graph->newVertex(['name' => 'Jaden']);
        $v[192] = $graph->newVertex(['name' => 'Korey']);
        $v[193] = $graph->newVertex(['name' => 'Rowan']);
        $v[194] = $graph->newVertex(['name' => 'Gust']);
        $v[195] = $graph->newVertex(['name' => 'Rusty']);
        $v[196] = $graph->newVertex(['name' => 'Alana']);
        $v[197] = $graph->newVertex(['name' => 'Dean']);
        $v[198] = $graph->newVertex(['name' => 'Helen']);
        $v[199] = $graph->newVertex(['name' => 'Cordia']);
        $v[200] = $graph->newVertex(['name' => 'Ora']);
        $v[201] = $graph->newVertex(['name' => 'Wyman']);
        $v[202] = $graph->newVertex(['name' => 'Reginald']);
        $v[203] = $graph->newVertex(['name' => 'Delores']);
        $v[204] = $graph->newVertex(['name' => 'Sherman']);
        $v[205] = $graph->newVertex(['name' => 'Brisa']);
        $v[206] = $graph->newVertex(['name' => 'Horacio']);
        $v[207] = $graph->newVertex(['name' => 'Emmanuelle']);
        $v[208] = $graph->newVertex(['name' => 'Hassie']);
        $v[209] = $graph->newVertex(['name' => 'Samantha']);
        $v[210] = $graph->newVertex(['name' => 'Jaycee']);
        $v[211] = $graph->newVertex(['name' => 'Clair']);
        $v[212] = $graph->newVertex(['name' => 'Nickolas']);
        $v[213] = $graph->newVertex(['name' => 'Heather']);
        $v[214] = $graph->newVertex(['name' => 'Rasheed']);
        $v[215] = $graph->newVertex(['name' => 'Adela']);
        $v[216] = $graph->newVertex(['name' => 'Titus']);
        $v[217] = $graph->newVertex(['name' => 'Magnolia']);
        $v[218] = $graph->newVertex(['name' => 'Karley']);
        $v[219] = $graph->newVertex(['name' => 'Parker']);
        $v[220] = $graph->newVertex(['name' => 'Micheal']);
        $v[221] = $graph->newVertex(['name' => 'Hulda']);
        $v[222] = $graph->newVertex(['name' => 'Gideon']);
        $v[223] = $graph->newVertex(['name' => 'Erick']);
        $v[224] = $graph->newVertex(['name' => 'Ubaldo']);
        $v[225] = $graph->newVertex(['name' => 'Odie']);
        $v[226] = $graph->newVertex(['name' => 'Jakob']);
        $v[227] = $graph->newVertex(['name' => 'Misty']);
        $v[228] = $graph->newVertex(['name' => 'Filomena']);
        $v[229] = $graph->newVertex(['name' => 'Destin']);
        $v[230] = $graph->newVertex(['name' => 'Americo']);
        $v[231] = $graph->newVertex(['name' => 'Orie']);
        $v[232] = $graph->newVertex(['name' => 'Maeve']);
        $v[233] = $graph->newVertex(['name' => 'Charley']);
        $v[234] = $graph->newVertex(['name' => 'Clyde']);
        $v[235] = $graph->newVertex(['name' => 'Winston']);
        $v[236] = $graph->newVertex(['name' => 'Luciano']);
        $v[237] = $graph->newVertex(['name' => 'Abdiel']);
        $v[238] = $graph->newVertex(['name' => 'Shaniya']);
        $v[239] = $graph->newVertex(['name' => 'Grant']);
        $v[240] = $graph->newVertex(['name' => 'Carmel']);
        $v[241] = $graph->newVertex(['name' => 'Ford']);
        $v[242] = $graph->newVertex(['name' => 'Norris']);
        $v[243] = $graph->newVertex(['name' => 'Santina']);
        $v[244] = $graph->newVertex(['name' => 'Tomasa']);
        $v[245] = $graph->newVertex(['name' => 'Ora']);
        $v[246] = $graph->newVertex(['name' => 'Lesly']);
        $v[247] = $graph->newVertex(['name' => 'Norma']);
        $v[248] = $graph->newVertex(['name' => 'Jaclyn']);
        $v[249] = $graph->newVertex(['name' => 'Thad']);
        $v[250] = $graph->newVertex(['name' => 'Columbus']);
        $e[0] = $v[1]->createEdgeTo($v[0], ['weight' => 9]);
        $e[1] = $v[2]->createEdgeTo($v[1], ['weight' => 21]);
        $e[2] = $v[3]->createEdgeTo($v[2], ['weight' => 20]);
        $e[3] = $v[4]->createEdgeTo($v[3], ['weight' => 17]);
        $e[4] = $v[5]->createEdgeTo($v[4], ['weight' => 10]);
        $e[5] = $v[6]->createEdgeTo($v[5], ['weight' => 3]);
        $e[6] = $v[7]->createEdgeTo($v[6], ['weight' => 1]);
        $e[7] = $v[8]->createEdgeTo($v[7], ['weight' => 26]);
        $e[8] = $v[9]->createEdgeTo($v[8], ['weight' => 24]);
        $e[9] = $v[10]->createEdgeTo($v[9], ['weight' => 30]);
        $e[10] = $v[11]->createEdgeTo($v[10], ['weight' => 18]);
        $e[11] = $v[12]->createEdgeTo($v[11], ['weight' => 30]);
        $e[12] = $v[13]->createEdgeTo($v[12], ['weight' => 10]);
        $e[13] = $v[14]->createEdgeTo($v[13], ['weight' => 28]);
        $e[14] = $v[15]->createEdgeTo($v[14], ['weight' => 3]);
        $e[15] = $v[16]->createEdgeTo($v[15], ['weight' => 9]);
        $e[16] = $v[17]->createEdgeTo($v[16], ['weight' => 22]);
        $e[17] = $v[18]->createEdgeTo($v[17], ['weight' => 27]);
        $e[18] = $v[19]->createEdgeTo($v[18], ['weight' => 10]);
        $e[19] = $v[20]->createEdgeTo($v[19], ['weight' => 8]);
        $e[20] = $v[21]->createEdgeTo($v[20], ['weight' => 29]);
        $e[21] = $v[22]->createEdgeTo($v[21], ['weight' => 14]);
        $e[22] = $v[23]->createEdgeTo($v[22], ['weight' => 20]);
        $e[23] = $v[24]->createEdgeTo($v[23], ['weight' => 25]);
        $e[24] = $v[25]->createEdgeTo($v[24], ['weight' => 8]);
        $e[25] = $v[26]->createEdgeTo($v[25], ['weight' => 3]);
        $e[26] = $v[27]->createEdgeTo($v[26], ['weight' => 28]);
        $e[27] = $v[28]->createEdgeTo($v[27], ['weight' => 20]);
        $e[28] = $v[29]->createEdgeTo($v[28], ['weight' => 8]);
        $e[29] = $v[30]->createEdgeTo($v[29], ['weight' => 22]);
        $e[30] = $v[31]->createEdgeTo($v[30], ['weight' => 17]);
        $e[31] = $v[32]->createEdgeTo($v[31], ['weight' => 14]);
        $e[32] = $v[33]->createEdgeTo($v[32], ['weight' => 29]);
        $e[33] = $v[34]->createEdgeTo($v[33], ['weight' => 13]);
        $e[34] = $v[35]->createEdgeTo($v[34], ['weight' => 26]);
        $e[35] = $v[36]->createEdgeTo($v[35], ['weight' => 8]);
        $e[36] = $v[37]->createEdgeTo($v[36], ['weight' => 16]);
        $e[37] = $v[38]->createEdgeTo($v[37], ['weight' => 7]);
        $e[38] = $v[39]->createEdgeTo($v[38], ['weight' => 20]);
        $e[39] = $v[40]->createEdgeTo($v[39], ['weight' => 3]);
        $e[40] = $v[41]->createEdgeTo($v[40], ['weight' => 13]);
        $e[41] = $v[42]->createEdgeTo($v[41], ['weight' => 11]);
        $e[42] = $v[43]->createEdgeTo($v[42], ['weight' => 21]);
        $e[43] = $v[44]->createEdgeTo($v[43], ['weight' => 29]);
        $e[44] = $v[45]->createEdgeTo($v[44], ['weight' => 5]);
        $e[45] = $v[46]->createEdgeTo($v[45], ['weight' => 20]);
        $e[46] = $v[47]->createEdgeTo($v[46], ['weight' => 25]);
        $e[47] = $v[48]->createEdgeTo($v[47], ['weight' => 30]);
        $e[48] = $v[49]->createEdgeTo($v[48], ['weight' => 7]);
        $e[49] = $v[50]->createEdgeTo($v[49], ['weight' => 1]);
        $e[50] = $v[51]->createEdgeTo($v[50], ['weight' => 7]);
        $e[51] = $v[52]->createEdgeTo($v[51], ['weight' => 10]);
        $e[52] = $v[53]->createEdgeTo($v[52], ['weight' => 11]);
        $e[53] = $v[54]->createEdgeTo($v[53], ['weight' => 12]);
        $e[54] = $v[55]->createEdgeTo($v[54], ['weight' => 23]);
        $e[55] = $v[56]->createEdgeTo($v[55], ['weight' => 27]);
        $e[56] = $v[57]->createEdgeTo($v[56], ['weight' => 12]);
        $e[57] = $v[58]->createEdgeTo($v[57], ['weight' => 0]);
        $e[58] = $v[59]->createEdgeTo($v[58], ['weight' => 7]);
        $e[59] = $v[60]->createEdgeTo($v[59], ['weight' => 23]);
        $e[60] = $v[61]->createEdgeTo($v[60], ['weight' => 17]);
        $e[61] = $v[62]->createEdgeTo($v[61], ['weight' => 29]);
        $e[62] = $v[63]->createEdgeTo($v[62], ['weight' => 11]);
        $e[63] = $v[64]->createEdgeTo($v[63], ['weight' => 8]);
        $e[64] = $v[65]->createEdgeTo($v[64], ['weight' => 22]);
        $e[65] = $v[66]->createEdgeTo($v[65], ['weight' => 9]);
        $e[66] = $v[67]->createEdgeTo($v[66], ['weight' => 27]);
        $e[67] = $v[68]->createEdgeTo($v[67], ['weight' => 17]);
        $e[68] = $v[69]->createEdgeTo($v[68], ['weight' => 0]);
        $e[69] = $v[70]->createEdgeTo($v[69], ['weight' => 18]);
        $e[70] = $v[71]->createEdgeTo($v[70], ['weight' => 7]);
        $e[71] = $v[72]->createEdgeTo($v[71], ['weight' => 22]);
        $e[72] = $v[73]->createEdgeTo($v[72], ['weight' => 18]);
        $e[73] = $v[74]->createEdgeTo($v[73], ['weight' => 3]);
        $e[74] = $v[75]->createEdgeTo($v[74], ['weight' => 14]);
        $e[75] = $v[76]->createEdgeTo($v[75], ['weight' => 20]);
        $e[76] = $v[77]->createEdgeTo($v[76], ['weight' => 13]);
        $e[77] = $v[78]->createEdgeTo($v[77], ['weight' => 9]);
        $e[78] = $v[79]->createEdgeTo($v[78], ['weight' => 23]);
        $e[79] = $v[80]->createEdgeTo($v[79], ['weight' => 30]);
        $e[80] = $v[81]->createEdgeTo($v[80], ['weight' => 28]);
        $e[81] = $v[82]->createEdgeTo($v[81], ['weight' => 28]);
        $e[82] = $v[83]->createEdgeTo($v[82], ['weight' => 9]);
        $e[83] = $v[84]->createEdgeTo($v[83], ['weight' => 6]);
        $e[84] = $v[85]->createEdgeTo($v[84], ['weight' => 20]);
        $e[85] = $v[86]->createEdgeTo($v[85], ['weight' => 7]);
        $e[86] = $v[87]->createEdgeTo($v[86], ['weight' => 27]);
        $e[87] = $v[88]->createEdgeTo($v[87], ['weight' => 25]);
        $e[88] = $v[89]->createEdgeTo($v[88], ['weight' => 9]);
        $e[89] = $v[90]->createEdgeTo($v[89], ['weight' => 12]);
        $e[90] = $v[91]->createEdgeTo($v[90], ['weight' => 10]);
        $e[91] = $v[92]->createEdgeTo($v[91], ['weight' => 27]);
        $e[92] = $v[93]->createEdgeTo($v[92], ['weight' => 22]);
        $e[93] = $v[94]->createEdgeTo($v[93], ['weight' => 21]);
        $e[94] = $v[95]->createEdgeTo($v[94], ['weight' => 20]);
        $e[95] = $v[96]->createEdgeTo($v[95], ['weight' => 1]);
        $e[96] = $v[97]->createEdgeTo($v[96], ['weight' => 23]);
        $e[97] = $v[98]->createEdgeTo($v[97], ['weight' => 27]);
        $e[98] = $v[99]->createEdgeTo($v[98], ['weight' => 6]);
        $e[99] = $v[100]->createEdgeTo($v[99], ['weight' => 0]);
        $e[100] = $v[101]->createEdgeTo($v[100], ['weight' => 3]);
        $e[101] = $v[102]->createEdgeTo($v[101], ['weight' => 28]);
        $e[102] = $v[103]->createEdgeTo($v[102], ['weight' => 21]);
        $e[103] = $v[104]->createEdgeTo($v[103], ['weight' => 5]);
        $e[104] = $v[105]->createEdgeTo($v[104], ['weight' => 11]);
        $e[105] = $v[106]->createEdgeTo($v[105], ['weight' => 26]);
        $e[106] = $v[107]->createEdgeTo($v[106], ['weight' => 15]);
        $e[107] = $v[108]->createEdgeTo($v[107], ['weight' => 10]);
        $e[108] = $v[109]->createEdgeTo($v[108], ['weight' => 13]);
        $e[109] = $v[110]->createEdgeTo($v[109], ['weight' => 5]);
        $e[110] = $v[111]->createEdgeTo($v[110], ['weight' => 17]);
        $e[111] = $v[112]->createEdgeTo($v[111], ['weight' => 7]);
        $e[112] = $v[113]->createEdgeTo($v[112], ['weight' => 15]);
        $e[113] = $v[114]->createEdgeTo($v[113], ['weight' => 30]);
        $e[114] = $v[115]->createEdgeTo($v[114], ['weight' => 26]);
        $e[115] = $v[116]->createEdgeTo($v[115], ['weight' => 5]);
        $e[116] = $v[117]->createEdgeTo($v[116], ['weight' => 8]);
        $e[117] = $v[118]->createEdgeTo($v[117], ['weight' => 7]);
        $e[118] = $v[119]->createEdgeTo($v[118], ['weight' => 20]);
        $e[119] = $v[120]->createEdgeTo($v[119], ['weight' => 22]);
        $e[120] = $v[121]->createEdgeTo($v[120], ['weight' => 23]);
        $e[121] = $v[122]->createEdgeTo($v[121], ['weight' => 30]);
        $e[122] = $v[123]->createEdgeTo($v[122], ['weight' => 30]);
        $e[123] = $v[124]->createEdgeTo($v[123], ['weight' => 12]);
        $e[124] = $v[125]->createEdgeTo($v[124], ['weight' => 20]);
        $e[125] = $v[126]->createEdgeTo($v[125], ['weight' => 15]);
        $e[126] = $v[127]->createEdgeTo($v[126], ['weight' => 0]);
        $e[127] = $v[128]->createEdgeTo($v[127], ['weight' => 27]);
        $e[128] = $v[129]->createEdgeTo($v[128], ['weight' => 14]);
        $e[129] = $v[130]->createEdgeTo($v[129], ['weight' => 17]);
        $e[130] = $v[131]->createEdgeTo($v[130], ['weight' => 21]);
        $e[131] = $v[132]->createEdgeTo($v[131], ['weight' => 5]);
        $e[132] = $v[133]->createEdgeTo($v[132], ['weight' => 23]);
        $e[133] = $v[134]->createEdgeTo($v[133], ['weight' => 28]);
        $e[134] = $v[135]->createEdgeTo($v[134], ['weight' => 14]);
        $e[135] = $v[136]->createEdgeTo($v[135], ['weight' => 24]);
        $e[136] = $v[137]->createEdgeTo($v[136], ['weight' => 16]);
        $e[137] = $v[138]->createEdgeTo($v[137], ['weight' => 18]);
        $e[138] = $v[139]->createEdgeTo($v[138], ['weight' => 9]);
        $e[139] = $v[140]->createEdgeTo($v[139], ['weight' => 27]);
        $e[140] = $v[141]->createEdgeTo($v[140], ['weight' => 9]);
        $e[141] = $v[142]->createEdgeTo($v[141], ['weight' => 29]);
        $e[142] = $v[143]->createEdgeTo($v[142], ['weight' => 28]);
        $e[143] = $v[144]->createEdgeTo($v[143], ['weight' => 22]);
        $e[144] = $v[145]->createEdgeTo($v[144], ['weight' => 3]);
        $e[145] = $v[146]->createEdgeTo($v[145], ['weight' => 23]);
        $e[146] = $v[147]->createEdgeTo($v[146], ['weight' => 12]);
        $e[147] = $v[148]->createEdgeTo($v[147], ['weight' => 19]);
        $e[148] = $v[149]->createEdgeTo($v[148], ['weight' => 9]);
        $e[149] = $v[150]->createEdgeTo($v[149], ['weight' => 16]);
        $e[150] = $v[151]->createEdgeTo($v[150], ['weight' => 3]);
        $e[151] = $v[152]->createEdgeTo($v[151], ['weight' => 30]);
        $e[152] = $v[153]->createEdgeTo($v[152], ['weight' => 10]);
        $e[153] = $v[154]->createEdgeTo($v[153], ['weight' => 13]);
        $e[154] = $v[155]->createEdgeTo($v[154], ['weight' => 14]);
        $e[155] = $v[156]->createEdgeTo($v[155], ['weight' => 29]);
        $e[156] = $v[157]->createEdgeTo($v[156], ['weight' => 26]);
        $e[157] = $v[158]->createEdgeTo($v[157], ['weight' => 10]);
        $e[158] = $v[159]->createEdgeTo($v[158], ['weight' => 3]);
        $e[159] = $v[160]->createEdgeTo($v[159], ['weight' => 11]);
        $e[160] = $v[161]->createEdgeTo($v[160], ['weight' => 12]);
        $e[161] = $v[162]->createEdgeTo($v[161], ['weight' => 20]);
        $e[162] = $v[163]->createEdgeTo($v[162], ['weight' => 8]);
        $e[163] = $v[164]->createEdgeTo($v[163], ['weight' => 3]);
        $e[164] = $v[165]->createEdgeTo($v[164], ['weight' => 16]);
        $e[165] = $v[166]->createEdgeTo($v[165], ['weight' => 22]);
        $e[166] = $v[167]->createEdgeTo($v[166], ['weight' => 15]);
        $e[167] = $v[168]->createEdgeTo($v[167], ['weight' => 5]);
        $e[168] = $v[169]->createEdgeTo($v[168], ['weight' => 25]);
        $e[169] = $v[170]->createEdgeTo($v[169], ['weight' => 6]);
        $e[170] = $v[171]->createEdgeTo($v[170], ['weight' => 22]);
        $e[171] = $v[172]->createEdgeTo($v[171], ['weight' => 17]);
        $e[172] = $v[173]->createEdgeTo($v[172], ['weight' => 9]);
        $e[173] = $v[174]->createEdgeTo($v[173], ['weight' => 9]);
        $e[174] = $v[175]->createEdgeTo($v[174], ['weight' => 26]);
        $e[175] = $v[176]->createEdgeTo($v[175], ['weight' => 21]);
        $e[176] = $v[177]->createEdgeTo($v[176], ['weight' => 18]);
        $e[177] = $v[178]->createEdgeTo($v[177], ['weight' => 30]);
        $e[178] = $v[179]->createEdgeTo($v[178], ['weight' => 7]);
        $e[179] = $v[180]->createEdgeTo($v[179], ['weight' => 12]);
        $e[180] = $v[181]->createEdgeTo($v[180], ['weight' => 2]);
        $e[181] = $v[182]->createEdgeTo($v[181], ['weight' => 26]);
        $e[182] = $v[183]->createEdgeTo($v[182], ['weight' => 6]);
        $e[183] = $v[184]->createEdgeTo($v[183], ['weight' => 12]);
        $e[184] = $v[185]->createEdgeTo($v[184], ['weight' => 0]);
        $e[185] = $v[186]->createEdgeTo($v[185], ['weight' => 26]);
        $e[186] = $v[187]->createEdgeTo($v[186], ['weight' => 9]);
        $e[187] = $v[188]->createEdgeTo($v[187], ['weight' => 0]);
        $e[188] = $v[189]->createEdgeTo($v[188], ['weight' => 1]);
        $e[189] = $v[190]->createEdgeTo($v[189], ['weight' => 9]);
        $e[190] = $v[191]->createEdgeTo($v[190], ['weight' => 21]);
        $e[191] = $v[192]->createEdgeTo($v[191], ['weight' => 22]);
        $e[192] = $v[193]->createEdgeTo($v[192], ['weight' => 8]);
        $e[193] = $v[194]->createEdgeTo($v[193], ['weight' => 5]);
        $e[194] = $v[195]->createEdgeTo($v[194], ['weight' => 18]);
        $e[195] = $v[196]->createEdgeTo($v[195], ['weight' => 29]);
        $e[196] = $v[197]->createEdgeTo($v[196], ['weight' => 19]);
        $e[197] = $v[198]->createEdgeTo($v[197], ['weight' => 1]);
        $e[198] = $v[199]->createEdgeTo($v[198], ['weight' => 17]);
        $e[199] = $v[200]->createEdgeTo($v[199], ['weight' => 9]);
        $e[200] = $v[201]->createEdgeTo($v[200], ['weight' => 20]);
        $e[201] = $v[202]->createEdgeTo($v[201], ['weight' => 10]);
        $e[202] = $v[203]->createEdgeTo($v[202], ['weight' => 23]);
        $e[203] = $v[204]->createEdgeTo($v[203], ['weight' => 18]);
        $e[204] = $v[205]->createEdgeTo($v[204], ['weight' => 20]);
        $e[205] = $v[206]->createEdgeTo($v[205], ['weight' => 0]);
        $e[206] = $v[207]->createEdgeTo($v[206], ['weight' => 15]);
        $e[207] = $v[208]->createEdgeTo($v[207], ['weight' => 4]);
        $e[208] = $v[209]->createEdgeTo($v[208], ['weight' => 25]);
        $e[209] = $v[210]->createEdgeTo($v[209], ['weight' => 17]);
        $e[210] = $v[211]->createEdgeTo($v[210], ['weight' => 16]);
        $e[211] = $v[212]->createEdgeTo($v[211], ['weight' => 24]);
        $e[212] = $v[213]->createEdgeTo($v[212], ['weight' => 6]);
        $e[213] = $v[214]->createEdgeTo($v[213], ['weight' => 9]);
        $e[214] = $v[215]->createEdgeTo($v[214], ['weight' => 9]);
        $e[215] = $v[216]->createEdgeTo($v[215], ['weight' => 24]);
        $e[216] = $v[217]->createEdgeTo($v[216], ['weight' => 13]);
        $e[217] = $v[218]->createEdgeTo($v[217], ['weight' => 30]);
        $e[218] = $v[219]->createEdgeTo($v[218], ['weight' => 25]);
        $e[219] = $v[220]->createEdgeTo($v[219], ['weight' => 23]);
        $e[220] = $v[221]->createEdgeTo($v[220], ['weight' => 3]);
        $e[221] = $v[222]->createEdgeTo($v[221], ['weight' => 10]);
        $e[222] = $v[223]->createEdgeTo($v[222], ['weight' => 28]);
        $e[223] = $v[224]->createEdgeTo($v[223], ['weight' => 9]);
        $e[224] = $v[225]->createEdgeTo($v[224], ['weight' => 15]);
        $e[225] = $v[226]->createEdgeTo($v[225], ['weight' => 12]);
        $e[226] = $v[227]->createEdgeTo($v[226], ['weight' => 9]);
        $e[227] = $v[228]->createEdgeTo($v[227], ['weight' => 29]);
        $e[228] = $v[229]->createEdgeTo($v[228], ['weight' => 20]);
        $e[229] = $v[230]->createEdgeTo($v[229], ['weight' => 18]);
        $e[230] = $v[231]->createEdgeTo($v[230], ['weight' => 0]);
        $e[231] = $v[232]->createEdgeTo($v[231], ['weight' => 23]);
        $e[232] = $v[233]->createEdgeTo($v[232], ['weight' => 17]);
        $e[233] = $v[234]->createEdgeTo($v[233], ['weight' => 11]);
        $e[234] = $v[235]->createEdgeTo($v[234], ['weight' => 0]);
        $e[235] = $v[236]->createEdgeTo($v[235], ['weight' => 21]);
        $e[236] = $v[237]->createEdgeTo($v[236], ['weight' => 18]);
        $e[237] = $v[238]->createEdgeTo($v[237], ['weight' => 26]);
        $e[238] = $v[239]->createEdgeTo($v[238], ['weight' => 11]);
        $e[239] = $v[240]->createEdgeTo($v[239], ['weight' => 26]);
        $e[240] = $v[241]->createEdgeTo($v[240], ['weight' => 6]);
        $e[241] = $v[242]->createEdgeTo($v[241], ['weight' => 1]);
        $e[242] = $v[243]->createEdgeTo($v[242], ['weight' => 3]);
        $e[243] = $v[244]->createEdgeTo($v[243], ['weight' => 18]);
        $e[244] = $v[245]->createEdgeTo($v[244], ['weight' => 0]);
        $e[245] = $v[246]->createEdgeTo($v[245], ['weight' => 26]);
        $e[246] = $v[247]->createEdgeTo($v[246], ['weight' => 22]);
        $e[247] = $v[248]->createEdgeTo($v[247], ['weight' => 8]);
        $e[248] = $v[249]->createEdgeTo($v[248], ['weight' => 22]);
        $e[249] = $v[250]->createEdgeTo($v[249], ['weight' => 14]);
        $e[250] = $v[242]->createEdgeTo($v[136], ['weight' => 24]);
        $e[251] = $v[176]->createEdgeTo($v[49], ['weight' => 14]);
        $e[252] = $v[175]->createEdge($v[168], ['weight' => 11]);
        $e[253] = $v[144]->createEdge($v[118], ['weight' => 0]);
        $e[254] = $v[175]->createEdgeTo($v[10], ['weight' => 11]);
        $e[255] = $v[153]->createEdgeTo($v[19], ['weight' => 24]);
        $e[256] = $v[230]->createEdgeTo($v[119], ['weight' => 15]);
        $e[257] = $v[185]->createEdge($v[13], ['weight' => 22]);
        $e[258] = $v[199]->createEdgeTo($v[173], ['weight' => 11]);
        $e[259] = $v[70]->createEdgeTo($v[10], ['weight' => 15]);
        $e[260] = $v[163]->createEdge($v[102], ['weight' => 11]);
        $e[261] = $v[76]->createEdgeTo($v[65], ['weight' => 6]);
        $e[262] = $v[178]->createEdge($v[160], ['weight' => 18]);
        $e[263] = $v[92]->createEdge($v[15], ['weight' => 17]);
        $e[264] = $v[172]->createEdge($v[212], ['weight' => 13]);
        $e[265] = $v[224]->createEdge($v[52], ['weight' => 7]);
        $e[266] = $v[109]->createEdgeTo($v[109], ['weight' => 30]);
        $e[267] = $v[38]->createEdge($v[75], ['weight' => 22]);
        $e[268] = $v[108]->createEdge($v[177], ['weight' => 28]);
        $e[269] = $v[148]->createEdge($v[185], ['weight' => 13]);
        $e[270] = $v[82]->createEdgeTo($v[175], ['weight' => 14]);
        $e[271] = $v[110]->createEdgeTo($v[154], ['weight' => 27]);
        $e[272] = $v[3]->createEdgeTo($v[101], ['weight' => 18]);
        $e[273] = $v[198]->createEdgeTo($v[47], ['weight' => 27]);
        $e[274] = $v[134]->createEdge($v[27], ['weight' => 0]);
        $e[275] = $v[35]->createEdgeTo($v[82], ['weight' => 11]);
        $e[276] = $v[208]->createEdgeTo($v[192], ['weight' => 12]);
        $e[277] = $v[211]->createEdge($v[184], ['weight' => 10]);
        $e[278] = $v[107]->createEdge($v[242], ['weight' => 4]);
        $e[279] = $v[122]->createEdge($v[55], ['weight' => 4]);
        $e[280] = $v[231]->createEdge($v[123], ['weight' => 25]);
        $e[281] = $v[185]->createEdge($v[146], ['weight' => 19]);
        $e[282] = $v[40]->createEdge($v[179], ['weight' => 7]);
        $e[283] = $v[50]->createEdge($v[192], ['weight' => 22]);
        $e[284] = $v[83]->createEdge($v[127], ['weight' => 4]);
        $e[285] = $v[206]->createEdge($v[198], ['weight' => 15]);
        $e[286] = $v[91]->createEdge($v[152], ['weight' => 18]);
        $e[287] = $v[168]->createEdge($v[205], ['weight' => 28]);
        $e[288] = $v[2]->createEdge($v[232], ['weight' => 14]);
        $e[289] = $v[11]->createEdgeTo($v[218], ['weight' => 17]);
        $e[290] = $v[6]->createEdge($v[82], ['weight' => 11]);
        $e[291] = $v[29]->createEdge($v[176], ['weight' => 20]);
        $e[292] = $v[115]->createEdgeTo($v[82], ['weight' => 14]);
        $e[293] = $v[170]->createEdge($v[148], ['weight' => 4]);
        $e[294] = $v[158]->createEdgeTo($v[181], ['weight' => 23]);
        $e[295] = $v[87]->createEdgeTo($v[91], ['weight' => 19]);
        $e[296] = $v[208]->createEdgeTo($v[140], ['weight' => 5]);
        $e[297] = $v[21]->createEdge($v[149], ['weight' => 29]);
        $e[298] = $v[94]->createEdgeTo($v[201], ['weight' => 2]);
        $e[299] = $v[3]->createEdgeTo($v[88], ['weight' => 30]);
        $e[300] = $v[211]->createEdgeTo($v[115], ['weight' => 12]);
        $e[301] = $v[91]->createEdge($v[117], ['weight' => 10]);
        $e[302] = $v[135]->createEdgeTo($v[47], ['weight' => 28]);
        $e[303] = $v[92]->createEdge($v[58], ['weight' => 20]);
        $e[304] = $v[205]->createEdgeTo($v[38], ['weight' => 5]);
        $e[305] = $v[31]->createEdgeTo($v[0], ['weight' => 13]);
        $e[306] = $v[23]->createEdge($v[205], ['weight' => 19]);
        $e[307] = $v[33]->createEdgeTo($v[23], ['weight' => 11]);
        $e[308] = $v[108]->createEdge($v[235], ['weight' => 20]);
        $e[309] = $v[105]->createEdgeTo($v[75], ['weight' => 12]);
        $e[310] = $v[187]->createEdgeTo($v[68], ['weight' => 23]);
        $e[311] = $v[69]->createEdgeTo($v[81], ['weight' => 17]);
        $e[312] = $v[203]->createEdgeTo($v[18], ['weight' => 15]);
        $e[313] = $v[121]->createEdge($v[181], ['weight' => 3]);
        $e[314] = $v[126]->createEdgeTo($v[249], ['weight' => 3]);
        $e[315] = $v[134]->createEdge($v[5], ['weight' => 8]);
        $e[316] = $v[166]->createEdgeTo($v[111], ['weight' => 8]);
        $e[317] = $v[99]->createEdge($v[164], ['weight' => 14]);
        $e[318] = $v[221]->createEdge($v[168], ['weight' => 16]);
        $e[319] = $v[52]->createEdgeTo($v[12], ['weight' => 6]);
        $e[320] = $v[146]->createEdge($v[211], ['weight' => 0]);
        $e[321] = $v[240]->createEdge($v[181], ['weight' => 17]);
        $e[322] = $v[154]->createEdgeTo($v[119], ['weight' => 0]);
        $e[323] = $v[3]->createEdgeTo($v[27], ['weight' => 25]);
        $e[324] = $v[190]->createEdgeTo($v[113], ['weight' => 1]);
        $e[325] = $v[110]->createEdge($v[40], ['weight' => 26]);
        $e[326] = $v[163]->createEdgeTo($v[203], ['weight' => 28]);
        $e[327] = $v[195]->createEdgeTo($v[104], ['weight' => 19]);
        $e[328] = $v[63]->createEdgeTo($v[240], ['weight' => 1]);
        $e[329] = $v[133]->createEdge($v[178], ['weight' => 18]);
        $e[330] = $v[50]->createEdge($v[60], ['weight' => 30]);
        $e[331] = $v[180]->createEdge($v[98], ['weight' => 26]);
        $e[332] = $v[235]->createEdgeTo($v[101], ['weight' => 26]);
        $e[333] = $v[196]->createEdge($v[28], ['weight' => 18]);
        $e[334] = $v[213]->createEdgeTo($v[221], ['weight' => 19]);
        $e[335] = $v[166]->createEdgeTo($v[227], ['weight' => 2]);
        $e[336] = $v[70]->createEdge($v[59], ['weight' => 12]);
        $e[337] = $v[165]->createEdge($v[32], ['weight' => 6]);
        $e[338] = $v[229]->createEdge($v[71], ['weight' => 16]);
        $e[339] = $v[213]->createEdge($v[99], ['weight' => 29]);
        $e[340] = $v[84]->createEdge($v[101], ['weight' => 17]);
        $e[341] = $v[98]->createEdge($v[144], ['weight' => 0]);
        $e[342] = $v[114]->createEdgeTo($v[198], ['weight' => 3]);
        $e[343] = $v[48]->createEdgeTo($v[240], ['weight' => 28]);
        $e[344] = $v[85]->createEdge($v[123], ['weight' => 27]);
        $e[345] = $v[66]->createEdge($v[124], ['weight' => 7]);
        $e[346] = $v[16]->createEdge($v[46], ['weight' => 22]);
        $e[347] = $v[83]->createEdge($v[44], ['weight' => 1]);
        $e[348] = $v[235]->createEdgeTo($v[90], ['weight' => 6]);
        $e[349] = $v[200]->createEdgeTo($v[243], ['weight' => 4]);
        $e[350] = $v[84]->createEdgeTo($v[219], ['weight' => 3]);
        $e[351] = $v[119]->createEdge($v[136], ['weight' => 15]);
        $e[352] = $v[92]->createEdge($v[68], ['weight' => 12]);
        $e[353] = $v[243]->createEdgeTo($v[210], ['weight' => 4]);
        $e[354] = $v[42]->createEdge($v[167], ['weight' => 5]);
        $e[355] = $v[237]->createEdge($v[230], ['weight' => 20]);
        $e[356] = $v[219]->createEdge($v[0], ['weight' => 1]);
        $e[357] = $v[19]->createEdge($v[63], ['weight' => 5]);
        $e[358] = $v[34]->createEdge($v[152], ['weight' => 5]);
        $e[359] = $v[108]->createEdge($v[123], ['weight' => 9]);
        $e[360] = $v[29]->createEdgeTo($v[129], ['weight' => 21]);

        return [$v, $e];
    }
}
