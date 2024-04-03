<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../inc/func.inc.php");
include_once("../inc/config.inc.php");
include_once("../class/db.class.php");
$db = new db;


@session_start();

// if ($_SESSION["login"] != 1) exit;
if (!$_GET["x"] || !check_int($_GET["x"])) exit;
if (!$_GET["y"] || !check_int($_GET["y"])) exit;
if (!$_GET["s"] || !check_int($_GET["s"])) exit;

$isKnown = $db->query("SELECT infotype FROM stu_systems_user WHERE systems_id = ".$_GET["s"]." AND user_id = ".$_SESSION[uid].";",1);

if (!$isKnown || $isKnown != "map") exit;

$gfx = $_SESSION[gfx_path];
if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "../gfx/";
if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "gfx/";

	$fieldinfo = $db->query("SELECT a.*,b.* FROM stu_sys_map as a LEFT JOIN stu_map_ftypes as b on a.type = b.type WHERE a.sx = ".$_GET["x"]." AND a.sy = ".$_GET["y"]." AND a.systems_id = ".$_GET["s"].";",4);


		
	echo "<div style=\"border:1px solid #8897cf;background:#000000;padding:5px;\"><img src=\"".$gfx."/map/".$fieldinfo[type].".gif\" /> ".$_GET["x"]."|".$_GET["y"].": ".$fieldinfo[name]."</div>";	
	

	
?>