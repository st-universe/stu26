<?php

	include_once("myheap.php");
	include_once("../inc/config.inc.php");
	include_once($global_path."/class/db.class.php");
	$db = new db;
	$a = new MinHeap();
	
	
class ElementOfPathFinding
{
	var $x = 0;
	var $y = 0;
	var $cost = 0;
	var $xPre = 0;
	var $yPre = 0;
	var $costRem = 0;
	function ElementOfPathFinding($x,$y,$c,$xp,$yp,$cr) 
	{ 
		$this->x = $x;
		$this->y = $y;
		$this->cost = $c;
		$this->xPre = $xp;
		$this->yPre = $yp;
		$this->costRem = $cr;
	}
	function toString() { return "(".$this->x."|".$this->y.")<(".$this->xPre."|".$this->yPre.")-".$this->cost."|".$this->costRem." ||| "; }
	function getValue() { return ($this->cost + $this->costRem); }
	function getX() { return ($this->x); }
	function getY() { return ($this->y); }
	function getXPre() { return ($this->xPre); }
	function getYPre() { return ($this->yPre); }
	function getCost() { return ($this->cost); }
	function getCostRem() { return ($this->costRem); }
}


	function getDist($x,$y,$tx,$ty)	{ return 1.4*(abs($tx-$x) + abs($ty-$y)); }
	
	function findPath($map,$pre,$sx,$sy,$tx,$ty)
	{
		global $MinHeap, $ElementOfPathFinding, $ElementOfInt;
		
		$heap = new MinHeap();
		
		$heap->push(new ElementOfPathFinding($sx,$sy,0,$sx,$sy,getDist($sx,$sy,$tx,$ty)));
		
		while (true)
		{
			$ele = $heap->pop();
			$x = $ele->getX();
			$y = $ele->getY();
			$cost = $ele->getCost();
			$costRem = $ele->getCostRem();
			if ($pre[$x][$y][x] == 0)
			{
				$pre[$x][$y][x] = $ele->getXPre();
				$pre[$x][$y][y] = $ele->getYPre();
				if (($x == $tx) && ($y == $ty)) break;
				if ($x > 1) $heap->push(new ElementOfPathFinding($x-1,$y,($cost + $map[$x-1][$y][cost]),$x,$y,getDist($x-1,$y,$tx,$ty)));
				if ($x < 30) $heap->push(new ElementOfPathFinding($x+1,$y,($cost + $map[$x+1][$y][cost]),$x,$y,getDist($x+1,$y,$tx,$ty)));
				if ($y > 1) $heap->push(new ElementOfPathFinding($x,$y-1,($cost + $map[$x][$y-1][cost]),$x,$y,getDist($x,$y-1,$tx,$ty)));		
				if ($y < 30) $heap->push(new ElementOfPathFinding($x,$y+1,($cost + $map[$x][$y+1][cost]),$x,$y,getDist($x,$y+1,$tx,$ty)));
			}
		}
		return $pre;
	}
	
	function loadmap($x,$y,$x2,$y2) 
	{ 
		global $db;
		$result = $db->query("SELECT a.cx,a.cy,a.type,a.faction_id,a.is_border,c.name as fname,c.color,COUNT(b.id) as sc FROM stu_map as a LEFT JOIN stu_ships as b ON a.cx=b.cx AND a.cy=b.cy AND (b.cloak='0' OR b.cloak='' OR ISNULL(b.cloak)) AND ((b.systems_id>0 AND b.cfield!=7) OR b.systems_id=0) LEFT JOIN stu_factions as c USING(faction_id) WHERE a.cx BETWEEN ".$x." AND ".$x2." AND a.cy BETWEEN ".$y." AND ".$y2." GROUP BY a.cx,a.cy ORDER BY a.cy,a.cx");
		while($data=mysql_fetch_assoc($result))
		{
			$map[$data['cx']][$data['cy']][type] = $data['type'];
			switch ($data['type'])
			{
				case 2: $cost = 3; break;
				case 3: $cost = 5; break;
				case 4: case 5: case 6: case 7: case 8: case 9: case 10: case 15: case 16: $cost = 20; break;
				default: $cost = 1;
			}
			//if ($data['type'] <= 18) $cost += $data[sc];
			$map[$data['cx']][$data['cy']][cost] = $cost;
		} 
		
		return $map;
	}
	
	function getDirection($pre,$sx,$sy,$tx,$ty)
	{
		$x = $pre[$tx][$ty][x];
		$y = $pre[$tx][$ty][y];
		while (true)
		{
			if (($pre[$x][$y][x] == 0) || ($pre[$x][$y][y] == 0)) break;
			if (($pre[$x][$y][x] != $sx) || ($pre[$x][$y][y] != $sy))
			{
				$xnew = $pre[$x][$y][x];
				$ynew = $pre[$x][$y][y];
				$x = $xnew;
				$y = $ynew;
			}
			else break;
		}
		if ($x > $sx) return "Rechts";
		if ($y > $sy) return "Unten";
		if ($x < $sx) return "Links";
		if ($x < $sx) return "Oben";
		return "Schon da";
	}	
		
	echo "<body bgcolor=#303030><font color=white>";
	
	for ($i = 1; $i <= 30; $i++)
	{
		for ($j = 1; $j <= 30; $j++)
		{
			$pre[$i][$j][x] = 0;
			$pre[$i][$j][y] = 0;
		}
	}

	$t3 = microtime(true);
	$map = loadmap(1,1,30,30);
	$t4 = microtime(true);
		
	for ($i = 1; $i <= 30; $i++)
	{
		for ($j = 1; $j <= 30; $j++)
		{
			echo "<img src=http://www.stuniverse.de/gfx/map/".$map[$j][$i][type].".gif> ";
		}
		echo "<br>";
	}
	$t1 = microtime(true);
	$pre = findPath($map,$pre,2,2,29,29);
	$t2 = microtime(true);

	for ($i = 1; $i <= 30; $i++)
	{
		for ($j = 1; $j <= 30; $j++)
		{
			$e = ($map[$j][$i][cost] < 10 ? "0" : "").$map[$j][$i][cost]." ";
			if ($map[$j][$i][cost] == 1) $e = "<font color=#505050>01 </font>";
			echo $e;
		}
		echo "<br>";
	}
echo "<br><br>";
	for ($i = 1; $i <= 30; $i++)
	{
		for ($j = 1; $j <= 30; $j++)
		{
			$e = ($pre[$j][$i][x] < 10 ? "0" : "").$pre[$j][$i][x]."|".($pre[$j][$i][y] < 10 ? "0" : "").$pre[$j][$i][y]." ";
			if ($pre[$j][$i][x] == 0) $e = "<font color=#505050>00|00</font> ";
			echo $e;
		}
		echo "<br>";
	}
	$t5 = microtime(true);
	$d = getDirection($pre,2,2,29,29);
	$t6 = microtime(true);
	echo "<br>Fliege nach: ".$d."<br>";
	echo "<br>Daten holen:<br>".(($t4 - $t3)*1000)." ms";
	echo "<br>Pfad berechnen:<br>".(($t2 - $t1)*1000)." ms";
	echo "<br>Zurückverfolgen:<br>".(($t6 - $t5)*1000)." ms";
	echo "<br><br>Total:<br>".((($t6 - $t5)+($t2 - $t1)+($t4 - $t3))*1000)." ms";
?>
