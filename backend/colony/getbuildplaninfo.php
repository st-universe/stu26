<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../../inc/func.inc.php");
include_once("../../inc/config.inc.php");
include_once("../../class/db.class.php");
$db = new db;
@session_start();

if ($_SESSION['login'] != 1) exit;
if (!check_int($_GET['id']) || !check_int($_GET['c'])) exit;

$gfx = $_SESSION['gfx_path'];
if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "../../gfx/";

$col = $db->query("SELECT id FROM stu_colonies WHERE id=".$_GET['c']." AND user_id=".$_SESSION['uid']." LIMIT 1",4);
if ($col == 0) exit;
$data = $db->query("SELECT rumps_id,m1,m2,m3,m4,m5,m6,m7,m8,m9,m10,m11 FROM stu_ships_buildplans WHERE plans_id=".$_GET['id']." AND user_id=".$_SESSION['uid']." LIMIT 1",4);
if ($data == 0) exit;
$rump = $db->query("SELECT m1c,m2c,m3c,m4c,m5c,m6c,m7c,m8c,m9c,m10c,m11c FROM stu_rumps WHERE rumps_id='".$data['rumps_id']."' LIMIT 1",4);
if ($rump == 0) exit;

include_once("../../inc/lists/goods.php");

echo "Benötigt: ";
if ($data['m1'] > 0) echo "<img src=".$gfx."/goods/".$data['m1'].".gif title=\"".getgoodname($data['m1'])."\"> ".$rump['m1c']." (".$db->query("SELECT count FROM stu_colonies_storage WHERE goods_id=".$data['m1']." AND colonies_id=".$_GET['c']." LIMIT 1",1).")&nbsp;&nbsp;";
if ($data['m2'] > 0) echo "<img src=".$gfx."/goods/".$data['m2'].".gif title=\"".getgoodname($data['m2'])."\"> ".$rump['m2c']." (".$db->query("SELECT count FROM stu_colonies_storage WHERE goods_id=".$data['m2']." AND colonies_id=".$_GET['c']." LIMIT 1",1).")&nbsp;&nbsp;";
if ($data['m3'] > 0) echo "<img src=".$gfx."/goods/".$data['m3'].".gif title=\"".getgoodname($data['m3'])."\"> ".$rump['m3c']." (".$db->query("SELECT count FROM stu_colonies_storage WHERE goods_id=".$data['m3']." AND colonies_id=".$_GET['c']." LIMIT 1",1).")&nbsp;&nbsp;";
if ($data['m4'] > 0) echo "<img src=".$gfx."/goods/".$data['m4'].".gif title=\"".getgoodname($data['m4'])."\"> ".$rump['m4c']." (".$db->query("SELECT count FROM stu_colonies_storage WHERE goods_id=".$data['m4']." AND colonies_id=".$_GET['c']." LIMIT 1",1).")&nbsp;&nbsp;";
if ($data['m5'] > 0) echo "<img src=".$gfx."/goods/".$data['m5'].".gif title=\"".getgoodname($data['m5'])."\"> ".$rump['m5c']." (".$db->query("SELECT count FROM stu_colonies_storage WHERE goods_id=".$data['m5']." AND colonies_id=".$_GET['c']." LIMIT 1",1).")&nbsp;&nbsp;";
if ($data['m6'] > 0) echo "<img src=".$gfx."/goods/".$data['m6'].".gif title=\"".getgoodname($data['m6'])."\"> ".$rump['m6c']." (".$db->query("SELECT count FROM stu_colonies_storage WHERE goods_id=".$data['m6']." AND colonies_id=".$_GET['c']." LIMIT 1",1).")&nbsp;&nbsp;";
if ($data['m7'] > 0) echo "<img src=".$gfx."/goods/".$data['m7'].".gif title=\"".getgoodname($data['m7'])."\"> ".$rump['m7c']." (".$db->query("SELECT count FROM stu_colonies_storage WHERE goods_id=".$data['m7']." AND colonies_id=".$_GET['c']." LIMIT 1",1).")&nbsp;&nbsp;";
if ($data['m8'] > 0) echo "<img src=".$gfx."/goods/".$data['m8'].".gif title=\"".getgoodname($data['m8'])."\"> ".$rump['m8c']." (".$db->query("SELECT count FROM stu_colonies_storage WHERE goods_id=".$data['m8']." AND colonies_id=".$_GET['c']." LIMIT 1",1).")&nbsp;&nbsp;";
if ($data['m9'] > 0) echo "<img src=".$gfx."/goods/".$data['m9'].".gif title=\"".getgoodname($data['m9'])."\"> ".$rump['m9c']." (".$db->query("SELECT count FROM stu_colonies_storage WHERE goods_id=".$data['m9']." AND colonies_id=".$_GET['c']." LIMIT 1",1).")&nbsp;&nbsp;";
if ($data['m10'] > 0) echo "<img src=".$gfx."/goods/".$data['m10'].".gif title=\"".getgoodname($data['m10'])."\"> ".$rump['m10c']." (".$db->query("SELECT count FROM stu_colonies_storage WHERE goods_id=".$data['m10']." AND colonies_id=".$_GET['c']." LIMIT 1",1).")&nbsp;&nbsp;";
if ($data['m11'] > 0) echo "<img src=".$gfx."/goods/".$data['m11'].".gif title=\"".getgoodname($data['m11'])."\"> ".$rump['m11c']." (".$db->query("SELECT count FROM stu_colonies_storage WHERE goods_id=".$data['m11']." AND colonies_id=".$_GET['c']." LIMIT 1",1).")&nbsp;&nbsp;";
?>