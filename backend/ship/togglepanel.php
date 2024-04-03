<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../../inc/func.inc.php");
include_once("../../inc/config.inc.php");
include_once("../../class/db.class.php");

@session_start();

if (!check_int($_GET['id']) || $_GET['id'] == 0 || !$_GET['m'] || strlen($_GET['m']) > 4 || $_SESSION["login"] != 1) exit;

$db = new db;
$id = $_GET['id'];
$m = $_GET['m'];

$gfx = $_SESSION[gfx_path];
if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "../gfx/";

$data = $db->query("SELECT open_menus FROM stu_ships WHERE id = ".$id." LIMIT 1;",1);

$new = array();
$arr = explode(" ",$data);
$found = false;
foreach($arr as $n) {
  if ($n == $m) {
	  $found = true;
  } else {
	  array_push($new,$n);
  }
}
if (!$found) array_push($new,$m);
$db->query("UPDATE stu_ships SET open_menus = '".implode(" ",$new)."' WHERE id = ".$id." LIMIT 1;");
?>