<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../inc/func.inc.php");
include_once("../inc/config.inc.php");
include_once("../class/db.class.php");
$db = new db;


@session_start();

if ($_SESSION["login"] != 1) exit;
if (!$_GET["s"] || !check_int($_GET["s"])) exit;

$gfx = $_SESSION[gfx_path];
// if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "../gfx/";
if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "gfx/";

	$activecolor = "#ffffff";
	$inactivecolor = "#000000";
	$numbersize = 10;
	
	$fromx = (floor($_GET["s"] / 10)-1) * 40;
	$fromy = (($_GET["s"] % 10)-1) * 40;
	
	
	$fields = $db->query("SELECT m.*,s.systems_id FROM stu_map as m LEFT JOIN stu_systems as s ON s.cx=m.cx AND s.cy = m.cy WHERE m.cx > ".$fromx." AND m.cx <= ".($fromx+40)." AND m.cy > ".$fromy." AND m.cy <= ".($fromy+40)." ORDER BY m.cy, m.cx");
// <table  bgcolor=#262323 cellspacing=1 cellpadding=1 style=\"height:810px; width:730px;\" oncontextmenu=\"loadActualMap();hideInfo();return false;\">
	echo "<table  bgcolor=#262323 cellspacing=1 cellpadding=1 style=\"height:810px; width:730px;\" oncontextmenu=\"loadActualMap();hideInfo();return false;\">
		<tr>
			<td style=\"text-align:center;height:30px;\"colspan=3><b>Karte des Deenia-Sektors</b></td>
		</tr>
		<tr>
			<th style=\"height:26px;width:200px;text-align:center;\"><a href=#>Astrometrisch</a></th>
			<td style=\"height:26px;width:200px;text-align:center;\" onclick=\"loadPoliticalMapZoom(".$_GET["s"].");\"><a href=#>Politisch</a></td>
			<td style=\"height:26px;width:200px;text-align:center;\" onclick=\"loadEventMapZoom(".$_GET["s"].");\"><a href=#>Ereigniskarte</a></td>
		</tr>
		<tr>
			<td colspan=3 style=\"text-align:center;\"><center>
				<table  bgcolor=#000000 cellspacing=1 cellpadding=0 style=\"background: url('".$gfx."/starmap/actual/zoom".$_GET['s'].".png') top no-repeat;\">";
				
					
					echo "<tr><td rowspan=2 colspan=2></td>";
					for ($i = $fromx+1; $i <= $fromx+40; $i++) {
						echo "<th rowspan=2 style=\"font-size:".$numbersize."px;text-align:center;height:31px;width:16px;\"><div class='rotate' style=\"width:16px;height:16px;text-align:left;\">".($i)."</div></th>";
					}
					echo "</tr>";
					echo "<tr></tr>";
				
					echo "<tr><th colspan=2 style=\"font-size:".$numbersize."px;text-align:center;width:31px;\">".($fromy+1)."</th>";
					$currenty = 0;
					while ($field = mysql_fetch_assoc($fields)) {
						if ($currenty == 0) $currenty = $field[cy];
						
						if ($currenty != $field[cy]) {  
							$currenty = $field[cy];
							echo "</tr><tr>";
							echo "<th colspan=2 style=\"font-size:".$numbersize."px;text-align:center;width:31px;\">".$field[cy]."</th>";
						}
						
						if ($field['systems_id'] > 0) {
							echo "<td style=\"background: transparent;width:16px;height:16px;\"><a href=#><div class=helper onmouseover=\"this.style.border = '1px solid ".$activecolor."';showInfoField(this,".$field[cx].",".$field[cy].");\" onmouseout=\"this.style.border = 'none';hideInfo();\" onclick=\"showSystem(".$field[systems_id].",".$_GET[s].");\"></div></a></td>";
						} else {
							echo "<td style=\"background: transparent;width:16px;height:16px;\"><div class=helper onmouseover=\"this.style.border = '1px solid ".$activecolor."';showInfoField(this,".$field[cx].",".$field[cy].");\" onmouseout=\"this.style.border = 'none';hideInfo();\"></div></td>";							
						}
						
					}
				
				
					echo "</tr>";
				
echo "				</table>
</center>
			</td>
		</tr>
		<tr><th colspan=3 style=\"height:26px;text-align:center;\" onclick=\"loadActualMap();hideInfo();\"><a href=#>Zurück</a></th></tr>
	</table>
	";

	

	
?>