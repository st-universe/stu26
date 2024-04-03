<?php

    include_once("../inc/func.inc.php");
    include_once("../inc/config.inc.php");
    include_once($global_path."/class/db.class.php");
    $db = new db;

	$gfx = "http://www.stuniverse.de/gfx/";
	

	
	function getAllFleetPoints($id) {
		global $db;
		$sum = 0;
		
		$cpoints = getAllPoints("pcrew",$id);
		$mpoints = getAllPoints("pmaintain",$id);
		$spoints = getAllPoints("psupply",$id);

		return 20+min($cpoints,min($mpoints,$spoints));
	}
	
	function getAllPoints($type,$id) {
		global $db;		
		$result = $db->query("SELECT id FROM stu_colonies WHERE user_id=".$id.";");
		
		$sum = 0;
		while($d=mysql_fetch_assoc($result)) {
			$sum += getPoints($d['id'],$type);
		}			
		
		return $sum;
	}
	
	function getPoints($cid,$type) {
		global $db;		
		return $db->query("SELECT SUM(count) FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings_effects as b on a.buildings_id = b.buildings_id WHERE a.colonies_id=".$cid." AND a.aktiv = 1 AND b.type='".$type."';",1);
	}
	
	
	function countbuildings($uid,$bid) {
		global $db;		
		return $db->query("SELECT COUNT(id) FROM stu_colonies_fielddata as a LEFT JOIN stu_colonies as c on a.colonies_id = c.id WHERE c.user_id=".$uid." AND a.aktiv < 2 AND a.buildings_id='".$bid."';",1);				
	}
	
	function countproduction($uid,$gid) {
		global $db;		
		
		$b = $db->query("SELECT SUM(b.count) FROM stu_colonies_fielddata as a 
		LEFT JOIN stu_colonies_bonus as b ON a.buildings_id = b.buildings_id 
		LEFT JOIN stu_colonies as c ON c.id = a.colonies_id 
		WHERE c.user_id=".$uid." AND a.aktiv=1 AND b.colonies_classes_id = c.colonies_classes_id  AND b.goods_id = ".$gid."
		GROUP by b.goods_id;",1);
		
		
		$a = $db->query("SELECT SUM(c.count) as pc FROM stu_colonies as a 
		LEFT JOIN stu_colonies_fielddata as b ON b.colonies_id=a.id LEFT JOIN stu_buildings_goods as c ON c.buildings_id=b.buildings_id 
		LEFT JOIN stu_goods as d ON d.goods_id=c.goods_id 
		WHERE a.user_id=".$uid." AND b.aktiv=1 AND c.goods_id = ".$gid."
		GROUP BY c.goods_id ORDER BY d.sort",1);
		
		return $a+$b;
	}	
	function cmp($a, $b)
{
        if ($a[pfleet] == $b[pfleet]) {
            return 0;
        }
        return ($a[pfleet] > $b[pfleet]) ? -1 : +1;
}
	
	echo "<body bgcolor=#000000 text=#8897cf>";
	
	
	
	
	function b(&$p,$id) {
		global $db;
		$p['b'.$id] = countbuildings($p[id],$id);
	}
	function g(&$p,$id) {
		global $db;
		$p['g'.$id] = countproduction($p[id],$id);
	}
	
	function sb($p,$id) {
		echo "<td><img src='http://www.stuniverse.de/gfx/buildings/".$id."/0.png' border=0/>".$p['b'.$id]."</td>";
	}
	function sg($p,$id) {
		echo "<td><img src='http://www.stuniverse.de/gfx/goods/".$id.".gif' border=0/>".$p['g'.$id]."</td>";
	}	
	
	
	
	
	$ps = array();

		$result = $db->query("SELECT * FROM stu_user WHERE level = '5' ORDER by id ASC;");
		while($data=mysql_fetch_assoc($result))
		{
			
			$player = array();
			$player[id] = $data[id];
			$player[name] = $data[search_user];
			$player[pcrew] =  getAllPoints("pcrew",$player[id]);
			$player[pmaintain] =  getAllPoints("pmaintain",$player[id]);
			$player[psupply] =  getAllPoints("psupply",$player[id]);
			
			$player[pfleet] = 20+min($player[pcrew],min($player[pmaintain],$player[psupply]));
			
			b($player,15);
			g($player,21);
			g($player,5);
			g($player,6);
			g($player,8);
			g($player,11);

			
			array_push($ps,$player);
			
			unset($player);
			
			// print_r($player);
			// echo "<br>";
		}


usort($ps, "cmp");


		echo "<table width=100%>";
		
		
		foreach($ps as $p) {
			
			echo "<tr>";
			
			
				echo "<td>".$p[id]."</td>";
				echo "<td>".$p[name]."</td>";
				echo "<td><img src='http://www.stuniverse.de/gfx//icons/fleet.gif' border=0/>".$p[pfleet]."</td>";
				echo "<td><img src='http://www.stuniverse.de/gfx//icons/pcrew.gif' border=0/>".$p[pcrew]."</td>";
				echo "<td><img src='http://www.stuniverse.de/gfx//icons/pmaintain.gif' border=0/>".$p[pmaintain]."</td>";
				echo "<td><img src='http://www.stuniverse.de/gfx//icons/psupply.gif' border=0/>".$p[psupply]."</td>";
				
				sb($p,15);

				sg($p,5);
				sg($p,11);
				sg($p,21);	
				sg($p,6);	
				sg($p,8);	
					
			echo "</tr>";
			
		}
		
	echo "</table>";	
		
		
		
		
		
		
		
		
		
		
	echo "</body>";



?>
