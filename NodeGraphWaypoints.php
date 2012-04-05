<?php
// Released to Public Domain. Created by Nexii Malthus.

namespace PathFinder;

class NodeGraphWaypoints implements NodeGraph {
	// This representation implements a waypoint graph that has specific one-way or two-way links between nodes.
	// For example to force traffic to drive on one side of a road in one way lanes of sort.
	
	public $nodes;
	
	
	/// Methods for basics
	function __construct($nodes) {
		$this->nodes = $nodes;
	}
	
	
	/// Other Methods
	function random() {
		$node = array_rand($this->nodes);
		return $node;
	}
	
	function pos($node) {
		return $this->nodes[$node]['Pos'];
	}
	
	function closest($posFrom) {
		$distance = 99999999.;
		$closest = NULL;
		foreach($this->nodes as $node => $contents) {
			$posTo = $contents['Pos'];
			$D = $this->vecDist($posFrom, $posTo);
			if($D < $distance) {
				$closest = $node;
				$distance = $D;
			}
		}
		return $closest;
	}
	
	function vecDist($a, $b) {
		return sqrt(pow($b[0] - $a[0], 2) + pow($b[1] - $a[1], 2) + pow($b[2] - $a[2], 2));
	}
	
	
	/// Pathfinding-related stuff
	function neighbours($node) {
		return $this->Nodes[$node]['Neighbours'];
	}
	
	function G($nodeFrom, $nodeTo) {
		$posFrom = $this->nodes[$nodeFrom]['Pos'];
		$posTo = $this->nodes[$nodeTo]['Pos'];
		return $this->vecDist($posFrom, $posTo);
	}
	
	function H($nodeFrom, $nodeTo) {
		$posFrom = $this->nodes[$nodeFrom]['Pos'];
		$posTo = $this->nodes[$nodeTo]['Pos'];
		return $this->VecDist($posFrom, $posTo);
	}
}
