<?php
// Released to Public Domain. Created by Nexii Malthus.
?><html>
<head>
    <style type="text/css">
        * {
            margin: 0px; padding: 0px;
        }
        body {
            margin: 13px 0px 0px 0px;
            background: #000;
            color: #FFF;
        }

        table {
            margin: 0px auto;
        }

        td::selection {
            background: rgba(0,0,0,0.3);
            color: #FFF;
        }

        td {
            width: 20px;
            height: 20px;
            text-align: center;
            color: rgba(0,0,0,0);
        }

        .Start, .End, .Path, .Open, .WasOpen, .Close {
            background-image: url('http://chromebackend.net/_img/NodesC.png');
            background-position: 40px 40px;
        }

        .Start		{ background-color: #66F; }
        .End		{ background-color: #F33; }
        .Path		{ background-color: #9F9; }
        .Open		{ background-color: #252; }
        .Close		{ background-color: #4A4; }
        .Floor		{ background-color: #333; }
        .Wall		{ background-color: #F99; }

        .Dir0		{ background-position: 40px 60px; } /* 0,-1*/
        .Dir1		{ background-position: 20px 40px; } /* 1, 0*/
        .Dir2		{ background-position: 40px 20px; } /* 0, 1*/
        .Dir3		{ background-position: 60px 40px; } /*-1, 0*/
        .Dir4		{ background-position: 20px 60px; } /* 1,-1*/
        .Dir5		{ background-position: 20px 20px; } /* 1, 1*/
        .Dir6		{ background-position: 60px 20px; } /*-1, 1*/
        .Dir7		{ background-position: 60px 60px; } /*-1,-1*/
    </style>
    <title>/s/ervices</title>
</head>

<body>
<?php

function __autoload($classname) {
    $file = str_replace('PathFinder\\', '', $classname);
    include $file . '.php';
}

use PathFinder\NodeGraph2D;
use PathFinder\PathFinder;

$NodeGraph = new NodeGraph2D(43, 22);
$NodeGraph->set(Array(
    Array(20, 4, 255),
    Array(20, 5, 255),
    Array(20, 6, 255),
    Array(20, 7, 255),
    Array(20, 8, 255),
    Array(20, 10, 255),
    Array(20, 12, 255),
    Array(21, 4, 255),
    Array(21, 10, 255),
    Array(21, 12, 255),
    Array(22, 4, 255),
    Array(22, 5, 255),
    Array(22, 6, 255),
    Array(22, 7, 255),
    Array(22, 9, 255),
    Array(22, 10, 255),
    Array(22, 12, 255),
    Array(23, 4, 255),
    Array(23, 10, 255),
    Array(23, 12, 255),
    Array(24, 4, 255),
    Array(24, 10, 255),
    Array(24, 12, 255),
    Array(25, 4, 255),
    Array(25, 5, 255),
    Array(25, 6, 255),
    Array(25, 7, 255),
    Array(25, 8, 255),
    Array(25, 9, 255),
    Array(25, 10, 255),
    Array(25, 12, 255),
    Array(25, 13, 255),
    Array(25, 14, 255),
    Array(25, 15, 255),
    Array(25, 16, 255),
    Array(25, 17, 255),
    Array(25, 18, 255),
    Array(25, 19, 255),
    Array(25, 20, 255),
));

$Start = $NodeGraph->random();
$End = $NodeGraph->random();
while($NodeGraph->H($Start, $End) < 25)
    $End = $NodeGraph->random();


$t0 = microtime(true);
$PathFinder = new PathFinder($NodeGraph);
$t1 = microtime(true);
$Path = $PathFinder->find($Start, $End);
$t2 = microtime(true);


if($Path) {
    $Rendered = Array();
    $Cache = $PathFinder->getCache();
    foreach($Cache as $Node => $Caching) {
        list($X, $Y) = $NodeGraph->node2XY($Node);
        if($Caching['Status'] == 1)
            $Rendered[$X][$Y] = 'Open Dir'.$NodeGraph->direction($Caching['Parent'], $Node);
        else if($Caching['Status'] == 2)
            $Rendered[$X][$Y] = 'Close Dir'.$NodeGraph->direction($Caching['Parent'], $Node);
    }

    foreach($Path as $Node) {
        list($X, $Y) = $NodeGraph->node2XY($Node);
        $Rendered[$X][$Y] = 'Path Dir'.$NodeGraph->direction($Cache[$Node]['Parent'], $Node);
    }

    list($X, $Y) = $NodeGraph->node2XY($Start);
    $Rendered[$X][$Y] = 'Start';
    list($X, $Y) = $NodeGraph->node2XY($End);
    $Rendered[$X][$Y] = 'End Dir'.$NodeGraph->direction($Cache[$Node]['Parent'], $End);

    ?><table><?php
    $Table = Array();
    $Tiles = $NodeGraph->getTiles();
    foreach($Tiles as $X => $Rows) {
        foreach($Rows as $Y => $Value) {
            if(isset($Rendered[$X][$Y]))
                $Table[$Y][$X] = $Rendered[$X][$Y];
            else $Table[$Y][$X] = $Value == 255 ? 'Wall' : 'Floor';
        }
    }

    foreach($Table as $Y => $Cols) {
        ?><tr><?php
            foreach($Cols as $X => $Class) {
                ?><td class="<?php echo $Class?>"></td><?php
            }
            ?></tr><?php
    }
    ?></table><?php
    $t3 = microtime(true);

    echo "<p>Pathfinding in ".round(($t2-$t1)*1000)."msec</p></br>";

    foreach($Path as &$P) $P = implode(' x ',$NodeGraph->node2XY($P));// For easier debugging.
    echo '<pre>Path = '.print_r($Path, TRUE).'</pre></br>';
    //echo '<pre>$PathFinder = '.print_r($PathFinder->Cache, TRUE).'</pre></br>';
} else {
    ?><p>Path not found.</p></br><p><?php echo $PathFinder->debug?></p><?php
    echo '<pre>'.print_r($PathFinder, TRUE).'</pre>';
}
?>

</body>
</html>