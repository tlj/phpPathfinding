<?php
// Released to Public Domain. Created by Nexii Malthus.

namespace PathFinder;

define('PATHFINDER_STATUS_UNTOUCHED',	0);
define('PATHFINDER_STATUS_OPEN',		1);
define('PATHFINDER_STATUS_CLOSED',		2);


class PathFinder {
	private $graph;
	private $limit = 1500;
	private $cache;

	function __construct(&$graph) {
		$this->graph = &$graph;
	}
	
	function find($nodeStart, $nodeEnd, $movementModifier = 1) {
		$queue = new PriorityQueue(); // Open Nodes ordered based on F cost
		$queue->setExtractFlags(PriorityQueue::EXTR_DATA);
		
		$closed = 0;
		$found = FALSE;

		$this->cache = Array( // Open and Closed Nodes. Stores calculated costs and parent nodes.
			$nodeStart => Array(
				'G' => 0,
				'F' => 0,
				'Parent' => $nodeStart,
				'Status' => PATHFINDER_STATUS_OPEN
			)
		);
		$queue->insert($nodeStart, $this->cache[$nodeStart]['F']);

		while(!$queue->isEmpty()) {
			$node = $queue->extract();
			
			if($this->cache[$node]['Status'] == PATHFINDER_STATUS_CLOSED)
				continue;
			
			if($node == $nodeEnd) {
				$this->cache[$node]['Status'] = PATHFINDER_STATUS_CLOSED;
				$found = TRUE;
				break;
			}
			
			if($closed > $this->limit) {
                throw new PathFinderException('Hit path limit.');
            }
			
			$neighbours = $this->graph->neighbours($node, $nodeEnd);
			foreach($neighbours as $neighbour) {
				$G = $this->cache[$node]['G'] + $this->graph->G($node, $neighbour, $movementModifier);
				
				if(	isset($this->cache[$neighbour])
					&& $this->cache[$neighbour]['Status']
					&& $this->cache[$neighbour]['G'] <= $G
				) continue;
				
				$F = $G + $this->graph->H($neighbour, $nodeEnd);
				
				$this->cache[$neighbour] = Array(
					'G' => $G,
					'F' => $F,
					'Parent' => $node,
					'Status' => PATHFINDER_STATUS_OPEN
				);
				$queue->insert($neighbour, $F);
			}
			++$closed;
			$this->cache[$node]['Status'] = PATHFINDER_STATUS_CLOSED;
		}
		
		if($found) {
			$path = array();
			$node = $nodeEnd;
			while($nodeStart != $node) {
				$path[] = $node;
				$node = $this->cache[$node]['Parent'];
			}
			$path[] = $node;
			return array_reverse($path);
		}
        throw new PathFinderException('Path not found, ran out of open nodes.');
	}

    public function getCache()
    {
        return $this->cache;
    }

}
