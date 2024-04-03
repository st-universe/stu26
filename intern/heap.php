<?php

//$element = array( X, Y, cost, pre x, pre y, heuristic);

class Heap {
	
	var $heap = array();

	function getValue(&$element) { return $element; }
	// print_r($element); echo "(".($element[2] + $element[5]).")"; 	
	
	function Heap() {
	}
	
	function swap($i,$j) {
		if($i==$j) return;
		$temp = $this->heap[$i];
		$this->heap[$i] = $this->heap[$j];
		$this->heap[$j] = $temp;
	}
	
	function trickle_down($i) {
		$left_son_idx = 2*$i+1;
		$left_son = $this->heap[$left_son_idx];
		$right_son_idx = 2*$i+2;
		$right_son = $this->heap[$right_son_idx];
		$father = $this->heap[$i];

		if(
			( $right_son_idx >= count($this->heap) ) || 
			( $this->getValue($right_son) > $this->getValue($father)) 
		) {
			//den linken sohn checken
			if(
				( $left_son_idx >= count($this->heap)) || 
				( $this->getValue($left_son) > $this->getValue($father))
			) {return;}
			else {
				if( $this->getValue($left_son) == $this->getValue($father)) {
						if($left_son[8]<$father[8]) {
							$this->swap($left_son_idx,$i); //ecken überprüfen
							$this->trickle_down($left_son_idx);
						}
				} else {
					$this->swap($i,$left_son_idx);
					$this->trickle_down($left_son_idx);
				}
			}
		}
		else {
			if( $this->getValue($left_son) <= $this->getValue($right_son) ) {
				//ECKEN!
				if($this->getValue($left_son) < $this->getValue($father)) {
					$this->swap($i,$left_son_idx);
					$this->trickle_down($left_son_idx);
				} else {
					if( ($this->getValue($left_son) == $this->getValue($father)) && ($left_son[8]<$father[8])) {
						$this->swap($i,$left_son_idx);
						$this->trickle_down($left_son_idx);
					}
					else {return;}
				}
			}
			else {
				//ECKEN
				if($this->getValue($right_son) < $this->getValue($father) ){
					$this->swap($i,$right_son_idx);
					$this->trickle_down($right_son_idx);
				}
				else if($this->getValue($right_son) == $this->getValue($father)) {
					if($right_son[8]<$father[8]) {
						$this->swap($i,$right_son_idx);
						$this->trickle_down($right_son_idx);
					}
					else {return;}
				}
				else {return;}
			}
		}

		return;
	}
	
	function trickle_up($i) {
		$father_idx = ($i%2==1) ? ($i-1)/2 :($i-2)/2;
		
		if($father_idx < 0) return;
		//echo "FOOBAR".$father_idx;
		
		$father = $this->heap[$father_idx];
		
		if( $this->getValue($this->heap[$i]) < $this->getValue($father)) {
			$this->swap($father_idx,$i);
			$this->trickle_up($father_idx);
		}
	}
	
	function push($element) {
		array_push($this->heap,$element);

		//trickle up
		$this->trickle_up(count($this->heap)-1);
	}
	
	function is_empty() {return count($this->heap)==0; }
	
	function pop() {
//		$last = count($this->heap);
		$this->swap(0,count($this->heap)-1);
		$res = array_pop($this->heap);
		
		//trickle down the new top element
		$this->trickle_down(0);
		
		return $res;
	}
	
	function hprint() {
		echo "{";
		foreach($this->heap as $element) {
			echo "(".$element[0].",".$element[1].")"." - (".$this->getValue($element).")      ";
		}
		echo "}";
	}
	
	function hprint2() {
    	echo "{";
    	foreach($this->heap as $element) {
    		echo "(".$element.")      ";
    	}
    	echo "}";
	}
	
	function get_array() {return$this->heap; }
}

    $a = new Heap();
    $a->push(5);
    $a->push(3);
    $a->push(7);
    $a->push(2);
    $a->push(9);
    $a->hprint2();
?>

