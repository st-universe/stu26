<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../../inc/func.inc.php");
include_once("../../inc/config.inc.php");
include_once("../../class/db.class.php");
$db = new db;

@session_start();

if ($_SESSION["login"] != 1) exit;

$gfx = $_SESSION[gfx_path];
if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "gfx/";

if (!check_int($_GET[id]) || $_GET[id] == 0) exit;

$data = $db->query("SELECT * FROM stu_terraforming WHERE terraforming_id = ".$_GET[id]." LIMIT 1",4);
if (!$data[terraforming_id]) exit;

function getrace($r) {
	switch($r)
	{
		case "1": return "Föderation";
		case "2": return "Romulaner";
		case "3": return "Klingonen";
		case "4": return "Cardassianer";
		case "5": return "Ferengi";
		
		default: return "Unbekannt";
	}
}












function displayTerraform($data) {
	global $gfx,$db;
	$op = "";

	
	$cost = $db->query("SELECT a.goods_id,a.count,b.name FROM stu_terraforming_cost as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.terraforming_id=".$data[terraforming_id]." ORDER BY b.sort");
	
	
	$op .= "<td valign=top><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>".stripslashes($data[name])."</th>
	</tr>
	<tr>
		<td><div align=center><img src=".$gfx."fields/".$data[v_feld].".gif> <img src=".$gfx."terraforming/arrow.gif> <img src=".$gfx."fields/".$data[z_feld].".gif></div><br>";
		$j = 0;
		
			$op .= "</td>
	</tr>";
	$op .= "<tr><td>";


	$op .= "</td>
	</tr>";
	

		$op .= "<tr>
		<td><u>Kosten</u><br>";
		$op .= "<img src=".$gfx."goods/0.gif> ".$data[ecost]."</font><br>";
		while($g=mysql_fetch_assoc($cost)) $op .= "<img src=".$gfx."goods/".$g[goods_id].".gif> ".($g['count'])."</font><br>";
		$op .= "</td>
		</tr>";
	
	$op .= "</table>";
	return $op;
}







echo "<table class=tcal style=\"border: 1px groove #8897cf;\">";



echo "<tr>";




// $i = 0;





	echo "<td valign=top>";



	

	echo  displayTerraform($data);









echo "</td></tr>";




echo "</table>";

// print_r($res);



?>