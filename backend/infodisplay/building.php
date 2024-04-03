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

$data = $db->query("SELECT * FROM stu_buildings WHERE buildings_id = ".$_GET[id]." LIMIT 1",4);
if (!$data[buildings_id]) exit;



	echo "<div style=\"width:300px; text-align:left;\">";
	echo floatingPanel(4,"Gebäude-Beschreibung","slist",$gfx."/buttons/icon/info.gif","<div style=\"padding:2px;\">".composeBuildingInfo($data['buildings_id'],0,0)."</div>");	
	echo "</div>";



?>