<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../inc/func.inc.php");
include_once("../inc/config.inc.php");
include_once("../class/db.class.php");
$db = new db;


@session_start();

if ($_SESSION["login"] != 1) exit;
if (!$_GET["x"] || !check_int($_GET["x"])) exit;
if (!$_GET["y"]) exit;

$gfx = $_SESSION[gfx_path];
// if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "../gfx/";
if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "gfx/";

	$fieldinfo = $db->query("SELECT a.*,b.* FROM stu_map as a LEFT JOIN stu_map_ftypes as b on a.type = b.type WHERE a.cx = ".$_GET["x"]." AND a.cy = ".$_GET["y"].";",4);
	// $fieldinfo = mysql_fetch_assoc($field);

	$systeminfo = $db->query("SELECT * FROM stu_systems WHERE cx = ".$_GET["x"]." AND cy = ".$_GET["y"].";",4);
	$systemadd = "<br>";
	if ($systeminfo[name]) {
		
		$cartographystate =  $db->query("SELECT infotype FROM stu_systems_user WHERE systems_id = ".$systeminfo['systems_id']." AND user_id = ".$_SESSION[uid]." LIMIT 1;",1);
		$allymap = $db->query("SELECT su.infotype FROM stu_user as u LEFT JOIN stu_systems_user as su ON u.id = su.user_id WHERE su.systems_id = ".$systeminfo['systems_id']." AND u.allys_id > 0 AND u.allys_id = ".$_SESSION[allys_id]." AND su.infotype = 'map' LIMIT 1;",1);
			
		if (!$cartographystate) {
			if ($allymap && ($allymap == "map")) {
				$systemadd = "<br><br><img src=".$gfx."/buttons/icon/star.gif> <font color=#ffff99>".$systeminfo[name]."-System</font><br><img src=".$gfx."/buttons/icon/map.gif> <font color=#ff9999>nicht kartographiert</font>";
				$systemadd .= "<br><img src=".$gfx."/buttons/icon/alliance.gif> <font color=#99ff99>Allianz-Karte verfügbar</font>";
			} else {
				$systemadd = "<br><br><img src=".$gfx."/buttons/icon/star.gif> <font color=#ff9999>Unbekanntes System</font><br><img src=".$gfx."/buttons/icon/map.gif> <font color=#ff9999>nicht kartographiert</font>";				
			}
		} else {
			if ($cartographystate && ($cartographystate == "map")) {
				$systemadd = "<br><br><img src=".$gfx."/buttons/icon/star.gif> <font color=#99ff99>".$systeminfo[name]."-System</font><br><img src=".$gfx."/buttons/icon/map.gif> <font color=#99ff99>kartographiert</font>";
				
			}
			if ($cartographystate && ($cartographystate == "name")) {
				
				if ($allymap && ($allymap == "map")) {
					$systemadd = "<br><br><img src=".$gfx."/buttons/icon/star.gif> <font color=#99ff99>".$systeminfo[name]."-System</font><br><img src=".$gfx."/buttons/icon/map.gif> <font color=#ff9999>nicht kartographiert</font>";
					$systemadd .= "<br><img src=".$gfx."/buttons/icon/alliance.gif> <font color=#99ff99>Allianz-Karte verfügbar</font>";
				} else {
					$systemadd = "<br><br><img src=".$gfx."/buttons/icon/star.gif> <font color=#99ff99>".$systeminfo[name]."-System</font><br><img src=".$gfx."/buttons/icon/map.gif> <font color=#ff9999>nicht kartographiert</font>";
				}
			}	
		}
			

		
	
		

	} else {
		
		if (!$fieldinfo['is_passable'])
			$systemadd .= "<br><img src=".$gfx."/buttons/icon/no.gif> Unpassierbar";
		if ($fieldinfo['ecost'] > 0)
			$systemadd .= "<br><img src=".$gfx."/buttons/icon/guard.gif> Deflektorkosten: ".$fieldinfo['ecost']."<img src=".$gfx."/buttons/icon/energy.gif>";
		
	}
		
	echo "<div style=\"border:1px solid #8897cf;background:#000000;padding:5px;\"><img src=\"".$gfx."/map/".$fieldinfo[type].".gif\" /> ".$_GET["x"]."|".$_GET["y"].": ".$fieldinfo[name]."".$systemadd."</div>";	
	

	
?>