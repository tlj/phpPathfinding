<?php
// Released to Public Domain. Created by Nexii Malthus.

namespace PathFinder;

class PriorityQueue extends \SplPriorityQueue {
    public function compare($a, $b) { // Reversed to favor lowest costs!
        if($a < $b) return 1;
        if($a > $b) return -1;
        return 0;
    }
}
