<?php
// Released to Public Domain. Created by Nexii Malthus.

namespace PathFinder;

define('PATHFINDER_STATUS_UNTOUCHED',	0);
define('PATHFINDER_STATUS_OPEN',		1);
define('PATHFINDER_STATUS_CLOSED',		2);


class PathFinder {
	private $graph;
	private $limit = 750;
	private $cache;
	private $debug;
	
	function __construct(&$graph) {
		$this->graph = &$graph;
	}
	
	function find($nodeStart, $nodeEnd) {
		$queue = new PriorityQueue(); // Open Nodes ordered based on F cost
		$queue->setExtractFlags(PriorityQueue::EXTR_DATA);
		
		$closed = 0;
		$found = FALSE;
		$this->debug = '';
		
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
				$this->debug = 'Hit limit. ('.$this->limit.')';
				return NULL;
			}
			
			$neighbours = $this->graph->neighbours($node);
			foreach($neighbours as $neighbour) {
				$G = $this->cache[$node]['G'] + $this->graph->G($node, $neighbour);
				
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
		$this->debug = 'Path not found, ran out of open nodes.';
		return NULL;
	}

    public function getCache()
    {
        return $this->cache;
    }

    public function getDebug()
    {
        return $this->debug;
    }
}
