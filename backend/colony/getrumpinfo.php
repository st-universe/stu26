<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../../inc/func.inc.php");
include_once("../../inc/config.inc.php");
include_once("../../class/db.class.php");
$db = new db;
@session_start();

if ($_SESSION['login'] != 1) exit;
if (!check_int($_GET['id'])) exit;

$gfx = $_SESSION['gfx_path'];
if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "../../gfx/";

$data = $db->query("SELECT rumps_id,name,eps_cost,storage,min_crew,bussard,erz,evade_val,slots,reaktor,replikator,warpable,cloakable,wp,buildtime,maintaintime,m1c,m1minlvl,m1maxlvl,m2c,m2minlvl,m2maxlvl,m3c,m3minlvl,m3maxlvl,m4c,m4minlvl,m4maxlvl,m5c,m5minlvl,m5maxlvl,m6c,m6minlvl,m6maxlvl,m7c,m7minlvl,m7maxlvl,m8c,m8minlvl,m8maxlvl,m9c,m9minlvl,m9maxlvl,m10c,m10minlvl,m10maxlvl,m11c,m11minlvl,m11maxlvl FROM stu_rumps WHERE rumps_id='".$_GET['id']."' LIMIT 1",4);
if ($data == 0) exit;

if ($db->query("SELECT user_id FROM stu_rumps_user WHERE user_id=".$_SESSION['uid']." AND rumps_id='".$_GET['id']."' LIMIT 1",1) == 0) exit;

echo "Benötigt: ";
if ($data['m1c'] > 0) echo "<img src=".$gfx."/buttons/modul_1.gif title=\"".getmodtypedescr(1)." - Level: ".$data['m1minlvl']." => ".$data['m1maxlvl']."\"> ".$data['m1c']."&nbsp;&nbsp;";
if ($data['m2c'] > 0) echo "<img src=".$gfx."/buttons/modul_2.gif title=\"".getmodtypedescr(2)." - Level: ".$data['m2minlvl']." => ".$data['m2maxlvl']."\"> ".$data['m2c']."&nbsp;&nbsp;";
if ($data['m3c'] > 0) echo "<img src=".$gfx."/buttons/modul_3.gif title=\"".getmodtypedescr(3)." - Level: ".$data['m3minlvl']." => ".$data['m3maxlvl']."\"> ".$data['m3c']."&nbsp;&nbsp;";
if ($data['m4c'] > 0) echo "<img src=".$gfx."/buttons/modul_4.gif title=\"".getmodtypedescr(4)." - Level: ".$data['m4minlvl']." => ".$data['m4maxlvl']."\"> ".$data['m4c']."&nbsp;&nbsp;";
if ($data['m5c'] > 0) echo "<img src=".$gfx."/buttons/modul_5.gif title=\"".getmodtypedescr(5)." - Level: ".$data['m5minlvl']." => ".$data['m5maxlvl']."\"> ".$data['m5c']."&nbsp;&nbsp;";
if ($data['m6c'] > 0) echo "<img src=".$gfx."/buttons/modul_6.gif title=\"".getmodtypedescr(6)." - Level: ".$data['m6minlvl']." => ".$data['m6maxlvl']."\"> ".$data['m6c']."&nbsp;&nbsp;";
if ($data['m7c'] > 0) echo "<img src=".$gfx."/buttons/modul_7.gif title=\"".getmodtypedescr(7)." - Level: ".$data['m7minlvl']." => ".$data['m7maxlvl']."\"> ".$data['m7c']."&nbsp;&nbsp;";
if ($data['m8c'] > 0) echo "<img src=".$gfx."/buttons/modul_8.gif title=\"".getmodtypedescr(8)." - Level: ".$data['m8minlvl']." => ".$data['m8maxlvl']."\"> ".$data['m8c']."&nbsp;&nbsp;";
if ($data['m9c'] > 0) echo "<img src=".$gfx."/buttons/modul_9.gif title=\"".getmodtypedescr(9)." - Level: ".$data['m9minlvl']." => ".$data['m9maxlvl']."\"> ".$data['m9c']."&nbsp;&nbsp;";
if ($data['m10c'] > 0) echo "<img src=".$gfx."/buttons/modul_10.gif title=\"".getmodtypedescr(10)." - Level: ".$data['m10minlvl']." => ".$data['m10maxlvl']."\"> ".$data['m10c']."&nbsp;&nbsp;";
if ($data['m11c'] > 0) echo "<img src=".$gfx."/buttons/modul_11.gif title=\"".getmodtypedescr(11)." - Level: ".$data['m11minlvl']." => ".$data['m11maxlvl']."\"> ".$data['m11c']."&nbsp;&nbsp;";
?>