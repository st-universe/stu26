<?php
header("Content-Type: text/html; charset=utf-8");
include_once("../../inc/func.inc.php");
include_once("../../inc/config.inc.php");
include_once("../../class/db.class.php");
$db = new db;
@session_start();

if ($_SESSION['login'] != 1) exit;
if (!check_int($_GET['id'])) exit;
if (!check_int($_GET['cid'])) exit;

$gfx = $_SESSION['gfx_path'];
if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "../../gfx/";

$col = $db->query("SELECT id FROM stu_colonies WHERE id=".$_GET['cid']." AND user_id=".$_SESSION['uid']." LIMIT 1",4);
if ($col == 0) exit;


	$c = $db->query("SELECT c.id,c.planet_name,c.sx,c.sy,c.colonies_classes_id,c.user_id,l.name as cname,l.research_id,l.is_moon FROM stu_colonies AS c LEFT JOIN stu_colonies_classes as l ON c.colonies_classes_id = l.colonies_classes_id WHERE c.id=".$_GET['id'].";",4);

	$content = "<div style=\"padding:4px;\">";
	
	$content .= "Koordinaten: ".$c['sx']."|".$c['sy']."<br>";
	
	if ($c['is_moon']) {
		$content .= $c['cname']." Mond<br>";	
	} else {
		$content .= $c['cname']." Planet<br>";	
	}
	
	
	if ($c['user_id'] == 1) 	 $class = "free";
	if ($c['user_id'] != 1) 	 $class = "taken";
	if ($c['id'] == $_GET['cid']) $class = "this";
	if ($c['research_id'] != 0)  $class = "impossible";	
	
	if ($class == "free")
		$content .= "<font color=#336633>unbewohnt</font>";
	if ($class == "taken")
		$content .= "<font color=#555555>bereits kolonisiert</font>";
	if ($class == "this")
		$content .= "<font color=#ddddff>aktuelle Kolonie</font>";
	if ($class == "impossible")
		$content .= "<font color=#663333>Koloniserung nicht möglich</font>";
	
	
	$content .= "</div>";
	
	echo "<div style=\"text-align:left;width:300px;\">";
	echo floatingPanel(3,$c['planet_name'],"plist",$gfx."/buttons/icon/planet.gif",$content,0);	
	echo "</div>";

?>
