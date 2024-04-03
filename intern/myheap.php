<?php

class ElementOfInt
{
	var $val = 0;
	function ElementOfInt($new) { $this->val = $new; }
	function toString() { return $this->val; }
	function getValue() { return $this->val; }
}

class MinHeap
{
	
	var $heap = array();
	
	function MinHeap() 
	{
	}
	
	function show() 
	{
    	foreach($this->heap as $element) 
		{
    		echo $element->toString()." ";
    	}
    	echo "<br>";
	}
	
	function showstring($v = 1) 
	{
		$s = $v.": ";
    	foreach($this->heap as $element) 
		{
    		$s .= $element->toString()." ";
    	}
    	return $s;
	}
	
	function getValue($element)
	{
		return $element->getValue();
	}
	
	function is_empty() 
	{ 
		return (count($this->heap) == 0); 
	}
		
	function push($element)
	{
		array_push($this->heap,$element);
		
		$this->upHeap(count($this->heap)-1);
	}
	
	
	function pop() {
		$this->swap(0,count($this->heap)-1);
		$res = array_pop($this->heap);
		
		$this->downHeap(0);
		return $res;
	}
	
	function swap($indexa, $indexb)
	{
		if ($indexa == $indexb) return;
		$temp = $this->heap[$indexa];
		$this->heap[$indexa] = $this->heap[$indexb];
		$this->heap[$indexb] = $temp;
	}
		
	function upHeap($index)
	{
		$findex = ($index % 2 == 1) ? ($index - 1) / 2 : ($index - 2) / 2;
		if ($findex < 0) return;
		
		$actual = $this->heap[$index];
		$father = $this->heap[$findex];
		if ($father->getValue() > $actual->getValue())
		{
			$this->swap($index,$findex);
			$this->upHeap($findex);
		}
	}
	
	function downHeap($index)
	{
		$actual = $this->heap[$index];
		$leftindex = 2*$index + 1;
		$rightindex = 2*$index + 2;
		$left = $this->heap[$leftindex];
		$right = $this->heap[$rightindex];
		
		if ($leftindex >= count($this->heap)) return;
		elseif ($rightindex >= count($this->heap))
		{
			if ($left->getValue() >= $actual->getValue()) return;
			else
			{
				$this->swap($index,$leftindex);
				$this->downHeap($leftindex);
			}
		}
		else
		{
			if (($left->getValue() >= $actual->getValue()) && ($right->getValue() >= $actual->getValue())) return;
			elseif (($left->getValue() >= $actual->getValue()) && ($right->getValue() < $actual->getValue()))
			{
				$this->swap($index,$rightindex);
				$this->downHeap($rightindex);
			}
			elseif (($left->getValue() < $actual->getValue()) && ($right->getValue() >= $actual->getValue()))
			{
				$this->swap($index,$leftindex);
				$this->downHeap($leftindex);
			}
			else
			{
				if ($left->getValue() < $right->getValue())
				{
					$this->swap($index,$leftindex);
					$this->downHeap($leftindex);
				}
				else
				{
					$this->swap($index,$rightindex);
					$this->downHeap($rightindex);
				}
			}
		}
	
	}
}

?>
