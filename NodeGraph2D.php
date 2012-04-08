<?php
// Released to Public Domain. Created by Nexii Malthus.

namespace PathFinder;

class NodeGraph2D implements NodeGraph {
	// This is an example class of a node graph.
	// We implement the interface as defined by NodeGraph
	// In this Graph representation, Nodes are in a fixed 2D tile grid of a known size.
	// The grid is spaced at 1.0 unit apart. Creating a distance of sqrt(2) for diagonal tiles.
	// $Tiles is a two-dimensional array (Array[X][Y])
	// storing one float which is used for G cost or to define an obstruction.

    protected $tiles;
	private $tilesLookup;
    private $tilesLookupReversed;
	private $sizeX;
	private $sizeY;

    private $directions = Array(
		Array( 0,-1), Array( 1, 0), Array( 0, 1), Array(-1, 0),
		Array( 1,-1), Array( 1, 1), Array(-1, 1), Array(-1,-1)
	);

    private $movementHorizontally = 1.0;
	private $movementDiagonally = M_SQRT2;

	/// Methods for basics
	public function __construct($sizeX, $sizeY) {
		$this->sizeX = $sizeX;
		$this->sizeY = $sizeY;

        $this->initTiles();

        foreach ($this->tiles as $x => $col) {
            foreach ($col as $y => $row) {
                $this->tilesLookup[] = array($x, $y);
                $this->tilesLookupReversed[$x][$y] = count($this->tilesLookup) - 1;
            }
        }
	}

    public function initTiles()
    {
        $this->tiles = array_fill(0, $this->sizeX, array_fill(0, $this->sizeY, 0.0));
    }

    public function isOccupied($x, $y)
    {
        if (is_object($this->tiles[$x][$y])) {
            return $this->tiles[$x][$y]->isOccupied();
        }
        return false;
    }
	
	public function XY2Node($X, $Y) {
        return $this->tilesLookupReversed[$X][$Y];
	}
	
	public function node2XY($lookup) {
        return $this->tilesLookup[$lookup];
	}

	/// Methods for debugging-ish
	public function set($newTiles) {
		foreach($newTiles as $newTile)
			$this->tiles[$newTile[0]][$newTile[1]] = $newTile[2];
	}
	
	public function random() {
		$X = rand(1, $this->sizeX-1);
		$Y = rand(1, $this->sizeY-1);
		if ($this->isOccupied($X, $Y)) return $this->random();
		return $this->XY2Node($X, $Y);
	}
	
	public function direction($nodeFrom, $nodeTo) {
		list($FX, $FY) = $this->node2XY($nodeFrom);
		list($TX, $TY) = $this->node2XY($nodeTo);
		$dirs = array(
			-1 => array(-1 => 7, 0 => 3, 1 => 6),
			 0 => array(-1 => 0, 0 => 'C', 1 => 2),
			 1 => array(-1 => 4, 0 => 1, 1 => 5)
		);
		return $dirs[$TX-$FX][$TY-$FY];
	}

	/// Pathfinding-related stuff
	function neighbours($node, $ignoreOccupied = null) {
		list($X, $Y) = $this->node2XY($node);
		
		$neighbours = array();
		for($i = 0; $i < 8; ++$i) {
			$neighbourX = $X + $this->directions[$i][0];
			$neighbourY = $Y + $this->directions[$i][1];

			if($neighbourX < 0 || $neighbourY < 0 || $neighbourX >= $this->sizeX || $neighbourY >= $this->sizeY)
				continue;

            $neighbour = $this->XY2Node($neighbourX, $neighbourY);

			if($this->isOccupied($neighbourX, $neighbourY) && $ignoreOccupied !== $neighbour)
				continue;

			$neighbours[] = $neighbour;
		}
		
		return $neighbours;
	}
	
	function G($nodeFrom, $nodeTo, $movementModifier = 1) {
		// Assumes we are being given a neighbour, as expected.
		list($FX, $FY) = $this->node2XY($nodeFrom);
		list($TX, $TY) = $this->node2XY($nodeTo);

        $tile = $this->tiles[$TX][$TY];
        if (is_object($tile)) {
            $G = $tile->getMovementValue();
        } else {
		    $G = $this->tiles[$TX][$TY] * $movementModifier;
        }
		if($FX == $TX || $FY == $TY) {
			 $G += $this->movementHorizontally;
        } else {
            $G += $this->movementDiagonally;
        }
		return $G;
	}
	
	function H($nodeFrom, $nodeTo) {
		list($FX, $FY) = $this->node2XY($nodeFrom);
		list($TX, $TY) = $this->node2XY($nodeTo);
		return sqrt(pow($TX - $FX, 2) + pow($TY - $FY, 2));
	}

    public function getTiles()
    {
        return $this->tiles;
    }

    public function getTile($x, $y)
    {
        return $this->tiles[$x][$y];
    }

    public function getSizeX()
    {
        return $this->sizeX;
    }

    public function getSizeY()
    {
        return $this->sizeY;
    }
}
