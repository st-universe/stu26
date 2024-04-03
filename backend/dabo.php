<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../inc/func.inc.php");
include_once("../inc/config.inc.php");
include_once("../class/db.class.php");

@session_start();

if ($_SESSION['login'] != 1 || !check_int($_GET['id']) || !check_int($_GET['cn']) || $_GET['cn'] < 1 || $_GET['cn'] > 28) exit;

$db = new db;

$data = $db->query("SELECT id,cx,cy,systems_id FROM stu_ships WHERE id=".$_GET['id']." AND user_id=".$_SESSION['uid']." LIMIT 1",4);
if ($data == 0) exit;

if ($data['systems_id'] > 0) exit;
if ($db->query("SELECT id FROM stu_ships WHERE cx=".$data['cx']." AND cy=".$data['cy']." AND (rumps_id=9105 OR rumps_id=9205)",1) == 0) exit;

if ($db->query("SELECT bid FROM stu_ferg_dabo WHERE user_id=".$_SESSION['uid']." LIMIT 1",1) > 0) exit;

$cn = $db->query("SELECT count FROM stu_trade_goods WHERE offer_id=0 AND goods_id=8 AND user_id=".$_SESSION['uid']." LIMIT 1",1);
if ($cn < 5)
{
	echo "<table><th colspan=\"4\">Tipp abgeben</th><tr>";
	$i=1;
	while($i<=28)
	{
		echo "<td class=\"pages\"><a href=\"javascript:void(0);\" onClick=\"registerbid(".$i.");\"><img src=gfx/dabo/".$i.".gif title=\"".get_pre($i)." ".get_post($i)."\" border=\"0\"></a></td>";
		if ($i%4 == 0) echo "</tr><tr>";
		$i++;
	}
	echo "</tr>
	<tr><td colspan=\"4\">Es ist nicht genügend Dilithium zum setzen vorhanden</td></tr></table>";
	exit;
}

if ($cn == 5) $db->query("DELETE FROM stu_trade_goods WHERE goods_id=8 AND offer_id=0 AND user_id=".$_SESSION['uid']." LIMIT 1");
else $db->query("UPDATE stu_trade_goods SET count=count-5 WHERE goods_id=8 AND offer_id=0 AND user_id=".$_SESSION['uid']." LIMIT 1");
$db->query("UPDATE stu_game_vars SET value=value+4 WHERE var='dabo_jackpot'");
$db->query("UPDATE stu_ferg_dabo SET bid=".$_GET['cn']." WHERE user_id=".$_SESSION['uid']." LIMIT 1");

function get_pre($i)
{
	$di = floor($i/4);
	$re = $i-($di*4);
	if ($re == 1) return "Single";
	if ($re == 2) return "Double";
	if ($re == 3) return "Triple";
	if ($re == 0) return "Quadruple";
}

function get_post($i)
{
	$di = ceil($i/4);
	switch ($di)
	{
		case 1:
			return "Top";
		case 2:
			return "Higher";
		case 3:
			return "Over";
		case 4:
			return "Middle";
		case 5:
			return "Under";
		case 6:
			return "Lower";
		case 7:
			return "Bottom";
	}
}

echo "Abgegebener Tipp: ".get_pre($_GET['cn'])." ".get_post($_GET['cn']);
?>