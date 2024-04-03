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
	
	
	// $isKnown = $db->query("SELECT infotype FROM stu_systems_user WHERE systems_id = ".$_GET["s"]." AND user_id = ".$_SESSION[allys_id].";",1);
	// $isKnown = $db->query("SELECT su.infotype FROM stu_user as u LEFT JOIN stu_systems_user as su ON u.id = su.user_id WHERE su.systems_id = ".$_GET["s"]." AND u.allys_id = ".$_SESSION[allys_id]." AND su.infotype = 'map' LIMIT 1;",1);
	$isKnown = false;

		
	$cartographystate =  $db->query("SELECT infotype FROM stu_systems_user WHERE systems_id = ".$_GET["s"]." AND user_id = ".$_SESSION[uid]." LIMIT 1;",1);
	$allymap = $db->query("SELECT su.infotype FROM stu_user as u LEFT JOIN stu_systems_user as su ON u.id = su.user_id WHERE su.systems_id = ".$_GET["s"]." AND u.allys_id > 0 AND u.allys_id = ".$_SESSION[allys_id]." AND su.infotype = 'map' LIMIT 1;",1);
			
	
		if (!$cartographystate) {
			if ($allymap && ($allymap == "map")) {
				$isKnown = true;
			} else {
				$isKnown = false;
			}
		} else {
			if ($cartographystate && ($cartographystate == "map")) {
				$isKnown = true;			
			}
			if ($cartographystate && ($cartographystate == "name")) {
				
				if ($allymap && ($allymap == "map")) {
					$isKnown = true;
				} else {
					$isKnown = false;
				}
			}	
		}
	
	
	
	
	
	if (!$isKnown) {
		
		$sys = $db->query("SELECT * FROM stu_systems WHERE systems_id = ".$_GET["s"].";",4);
echo "
	<table  bgcolor=#262323 cellspacing=1 cellpadding=1 style=\"height:810px; width:730px;\" oncontextmenu=\"loadActualMapZoom(".$_GET[f].");hideInfo();return false;\">
		<tr>
			<td style=\"text-align:center;height:30px;\"colspan=3><b>Karte des Deenia-Sektors</b></td>
		</tr>
			<tr>
				<th style=\"height:26px;width:200px;text-align:center;\"><a href=#>Astrometrisch</a></th>
				<td style=\"height:26px;width:200px;text-align:center;\" onclick=\"loadPoliticalMapZoom(".$_GET[f].");\"><a href=#>Politisch</a></td>
				<td style=\"height:26px;width:200px;text-align:center;\" onclick=\"loadEventMapZoom(".$_GET[f].");\"><a href=#>Ereigniskarte</a></td>
			</tr>
			<tr>
				<td colspan=3 style=\"text-align:center;\"><center>
					<b>System-Karte nicht verfügbar</b>
<table  bgcolor=#000000 cellspacing=1 cellpadding=0>";
					
						
						echo "<tr><td rowspan=2 colspan=2></td>";
						for ($i = 1; $i <= $sys['sr']; $i++) {
							echo "<th rowspan=2 style=\"font-size:".$numbersize."px;text-align:center;height:31px;width:22px;\"><div class='rotate' style=\"width:22px;height:22px;text-align:left;\">".($i)."</div></th>";
						}
						echo "</tr>";
						echo "<tr></tr>";
					
						for ($j = 1; $j <= $sys['sr']; $j++) {
							echo "<tr>
									<th colspan=2 style=\"font-size:".$numbersize."px;text-align:center;width:31px;height:22px;\">".($j)."</th>";
									for ($i = 1; $i <= $sys['sr']; $i++) {
										echo "<td style=\"background: url('".$gfx."/map/blur.gif') top repeat-y;background-size: 22px;width:22px;height:22px;\"></td>";	
									}
						
							echo "</tr>";
						}
	echo "				</table>					
	</center>
				</td>
			</tr>
			<tr><th colspan=3 style=\"height:26px;text-align:center;\" onclick=\"loadActualMapZoom(".$_GET[f].");\"><a href=#>Zurück</a></th></tr>
		</table>
		";
		
		
	} else {

		$sys = $db->query("SELECT * FROM stu_systems WHERE systems_id = ".$_GET["s"].";",4);
		$fields = $db->query("SELECT * FROM stu_sys_map WHERE systems_id = ".$_GET["s"]." ORDER BY sy, sx");

		echo "
	<table  bgcolor=#262323 cellspacing=1 cellpadding=1 style=\"height:810px; width:730px;\" oncontextmenu=\"loadActualMapZoom(".$_GET[f].");hideInfo();return false;\">
		<tr>
			<td style=\"text-align:center;height:30px;\"colspan=3><b>Karte des Deenia-Sektors</b></td>
		</tr>
			<tr>
				<th style=\"height:26px;width:200px;text-align:center;\"><a href=#>Astrometrisch</a></th>
				<td style=\"height:26px;width:200px;text-align:center;\" onclick=\"loadPoliticalMapZoom(".$_GET[f].");\"><a href=#>Politisch</a></td>
				<td style=\"height:26px;width:200px;text-align:center;\" onclick=\"loadEventMapZoom(".$_GET[f].");\"><a href=#>Ereigniskarte</a></td>
			</tr>
			<tr>
				<td colspan=3 style=\"text-align:center;\"><center>
					<b>".$sys[name]."-System (".$sys[cx]."|".$sys[cy].")</b>
					<table  bgcolor=#000000 cellspacing=1 cellpadding=0>";
					
						
						echo "<tr><td rowspan=2 colspan=2></td>";
						for ($i = 1; $i <= $sys['sr']; $i++) {
							echo "<th rowspan=2 style=\"font-size:".$numbersize."px;text-align:center;height:31px;width:18px;\"><div class='rotate' style=\"width:18px;height:18px;text-align:left;\">".($i)."</div></th>";
						}
						echo "</tr>";
						echo "<tr></tr>";
					
						echo "<tr><th colspan=2 style=\"font-size:".$numbersize."px;text-align:center;width:31px;\">1</th>";
						$currenty = 0;
						while ($field = mysql_fetch_assoc($fields)) {
							if ($currenty == 0) $currenty = $field[sy];
							
							if ($currenty != $field[sy]) {
								$currenty = $field[sy];
								echo "</tr><tr>";
								echo "<th colspan=2 style=\"font-size:".$numbersize."px;text-align:center;width:31px;\">".$field[sy]."</th>";
							}
							
							echo "<td style=\"background: url('".$gfx."/map/".$field[type].".gif') top repeat-y;background-size: 22px;width:22px;height:22px;\"><div class=helpersys onmouseover=\"this.style.border = '1px solid ".$activecolor."';showInfoFieldSystem(this,".$field[sx].",".$field[sy].",".$sys[systems_id].");\" onmouseout=\"this.style.border = 'none';hideInfo();\"></div></td>";							
							
						}
					
					
						echo "</tr>";
					
	echo "				</table>
	</center>
				</td>
			</tr>
			<tr><th colspan=3 style=\"height:26px;text-align:center;\" onclick=\"loadActualMapZoom(".$_GET[f].");\"><a href=#>Zurück</a></th></tr>
		</table>
		";

	
	}
	
?>