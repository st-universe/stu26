<?php
include_once("../inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;
echo "<html>
<head>
	<title>STU Planetennamen Testcript für System 1173</title>
</head><body>
<< <a href=index.html>Interner Bereich</a><br><br>";

	function distance ($x1,$y1,$x2,$y2) 
	{
		$x = ($x1 - $x2);
		$y = ($y1 - $y2);
		return sqrt($x*$x + $y*$y);
	}

	function getminimaldistantplanet($x,$y,$colonies)
	{
		$result[dist] = 99;
		$result[main] = 0;
		for ($i = 1; $i <= count($colonies); $i++) 
		{
			if ($colonies[$i][is_moon] == 0)
			{
		  		$dis = distance($x,$y,$colonies[$i][x],$colonies[$i][y]);
				if ($dis < $result[dist])
				{
					$result[dist] = $dis;
					$result[main] = $colonies[$i][id];
				}
			}
		
		}
		return $result;
	}

	function colcopy($colonies,$a,$b)
	{
		$t[id] = $colonies[$a][id];
		$t[x]  = $colonies[$a][x];
		$t[y]  = $colonies[$a][y];
		$t[is_moon]  = $colonies[$a][is_moon];
		$t[pclass]  = $colonies[$a][pclass];
		$t[add]  = $colonies[$a][add];
		$t[circles]  = $colonies[$a][circles];

		$colonies[$a][id] = $colonies[$b][id];
		$colonies[$a][x]  = $colonies[$b][x];
		$colonies[$a][y]  = $colonies[$b][y];
		$colonies[$a][is_moon]  = $colonies[$b][is_moon];
		$colonies[$a][pclass]  = $colonies[$b][pclass];
		$colonies[$a][add]  = $colonies[$b][add];
		$colonies[$a][circles]  = $colonies[$b][circles];

		$colonies[$b][id] = $t[id];
		$colonies[$b][x]  = $t[x];
		$colonies[$b][y]  = $t[y];
		$colonies[$b][is_moon]  = $t[is_moon];
		$colonies[$b][pclass]  = $t[pclass];
		$colonies[$b][add]  = $t[add];
		$colonies[$b][circles]  = $t[circles];

		return $colonies;
	}

	function sortbycircling($colonies)
	{
		for ($i = count($colonies); $i >= 1; $i--)
		{
			for ($j = 2; $j <= $i; $j++)
			{
				if ($colonies[$j][circles] < $colonies[($j-1)][circles])
				{
					$t = $colonies[($j-1)];
					$colonies[($j-1)] = $colonies[$j];
					$colonies[$j] = $t;
				}
			}
		}
		return $colonies;
	}

	function sortbydistance_constcircles($colonies,$circles)
	{
		$minindex = 0;
		$maxindex = 0;
		for ($i = 1; $i <= count($colonies); $i++) 
		{ 
			if (($minindex == 0) && ($colonies[$i][circles] == $circles)) $minindex = $i;
			elseif ($colonies[$i][circles] == $circles) $maxindex = $i;
		}
		for ($i = $maxindex; $i > $minindex; $i--)
		{
			for ($j = ($minindex+1); $j <= $i; $j++)
			{
				if ($colonies[$j][circles] != $circles) echo "  Fehler an Stelle:".$j;
				if (($colonies[$j][distance] < $colonies[($j-1)][distance]) && ($colonies[$j][circles] == $colonies[($j-1)][circles]))
				{
					$t = $colonies[($j-1)];
					$colonies[($j-1)] = $colonies[$j];
					$colonies[$j] = $t;
				}
			}
		}
		return $colonies;
	}

	function getadd_planet($i)
	{
		switch($i) {
 		case 1: return "I";
 		case 2: return "II";
 		case 3: return "III";
 		case 4: return "IV";
 		case 5: return "V";
 		case 6: return "VI";
 		case 7: return "VII";
 		case 8: return "VIII";
 		case 9: return "IX";
 		case 10: return "X";
 		case 11: return "XI";
 		case 12: return "XII";
 		case 13: return "XIII";
 		case 14: return "XIV";
 		case 15: return "XV";
		}
	}

	function getadd_moon($i)
	{
		switch($i) {
 		case 1: return "a";
 		case 2: return "b";
 		case 3: return "c";
 		case 4: return "d";
 		case 5: return "e";
 		case 6: return "f";
 		case 7: return "g";
 		case 8: return "h";
 		case 9: return "i";
 		case 10: return "j";
		}
	}

	function is_moon($id)
	{
		if (($id <= 11) || ($id == 29)) return 0;
		else return 1; 
	}	

	function find_id($id, $colonies)
	{
		for ($i = 1; $i <= count($colonies); $i++) 
		{
			if ($colonies[$i][id] == $id) return $i;
		}
	}

	$result = $db->query("SELECT systems_id,name,sr FROM stu_systems ORDER BY systems_id");
	while($data=mysql_fetch_assoc($result))
	{
		$k++;
		$sys[$k][name] = $data[name];		
		$sys[$k][size] = $data[sr];
		$sys[$k][id] = $data[systems_id];
	}
	
	for ($k = 1; $k <= count($sys); $k++)
{
	echo $sys[$k][name]." ".$sys[$k][size]."<br>";
	$result = $db->query("SELECT id, sx, sy, colonies_classes_id FROM stu_colonies WHERE systems_id = ".$sys[$k][id]." ORDER BY id");
	$i = 0;
	while($data=mysql_fetch_assoc($result))
	{
		$i++;
		$colonies[$i][id] = $data[id];		
		$colonies[$i][x] = $data[sx];	
		$colonies[$i][y] = $data[sy];	
		$colonies[$i][is_moon] = is_moon($data[colonies_classes_id]);
	}

	$center = $sys[$k][size] / 2 + 0.5;

	for ($i = 1; $i <= count($colonies); $i++) 
	{
		if ($colonies[$i][is_moon] == 0)
		{
	  		$colonies[$i][distance] = distance($colonies[$i][x], $colonies[$i][y], $center, $center);
			$colonies[$i][circles] = 0;
			$colonies[$i][pclass] = "Planet";
		}
		elseif ($colonies[$i][is_moon] == 1)
		{
			$min = getminimaldistantplanet($colonies[$i][x],$colonies[$i][y],$colonies);
			if ($min[dist] > 3) 
			{
				$colonies[$i][circles] = 0;
				$colonies[$i][distance] = distance($colonies[$i][x], $colonies[$i][y], $center, $center);
				$colonies[$i][pclass] = "Planetoid";
			}
			else 
			{
				$colonies[$i][distance] = $min[dist];
				$colonies[$i][circles] = $min[main];
				$colonies[$i][pclass] = "Moon";
			}
		}
	}

	$colonies = sortbycircling($colonies);
	$colonies = sortbydistance_constcircles($colonies,0);

	$i = 1;
	while (($colonies[$i][pclass] == "Planet") || ($colonies[$i][pclass] == "Planetoid"))
	{
		$colonies = sortbydistance_constcircles($colonies,$colonies[$i][id]);
		$i++;
	}

	$j = 1;
	$c = 0;
	for ($i = 1; $i <= count($colonies); $i++) 
	{
		if ($colonies[$i][pclass] != "Moon")
		{
			$colonies[$i][add] = getadd_planet($i);
		}
		else
		{
			if ($c != $colonies[$i][circles])
			{
				$c = $colonies[$i][circles];
				$j = 1;
			}
			$colonies[$i][add] = $colonies[find_id($colonies[$i][circles],$colonies)][add].getadd_moon($j);
			$j++;
		}
	}


	for ($i = 1; $i <= count($colonies); $i++) 
	{
		echo $colonies[$i][id]." (".$colonies[$i][x]."/".$colonies[$i][y].") - ".$colonies[$i][pclass];
		echo " ".$colonies[$i][add]." - ";
		if ($colonies[$i][circles] == 0) echo "  circles: Sun";
		else echo "  circles: ".$colonies[$i][circles];
		echo "<br>";
		$db->query("UPDATE stu_colonies SET planet_name='".$colonies[$i][add]."' WHERE id=".$colonies[$i][id]);
	}
	unset($colonies);
}
?>
</body></html>