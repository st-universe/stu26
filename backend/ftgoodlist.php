<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../inc/func.inc.php");
include_once("../inc/config.inc.php");
include_once("../class/db.class.php");

@session_start();

if ($_SESSION["login"] != 1) exit;

$db = new db;

$gfx = $_SESSION[gfx_path];
if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "../gfx/";

echo "<table class=\"tcal\" style=\"border: 1px groove #8897cf;\"><th colspan=\"3\">Warenliste</th><tr>";
$result = $db->query("SELECT a.goods_id,a.name,COUNT(b.trade_id) as tc FROM stu_goods as a LEFT JOIN stu_trade_ferg as b ON b.give_good=a.goods_id WHERE a.view='1' AND a.goods_id<200 GROUP BY a.goods_id ORDER BY sort");
while($data=mysql_fetch_assoc($result))
{
	if ($i == 3)
	{
		echo "</tr><tr>";
		$i = 0;
	}
	$i++;
	echo "<td><img src=".$gfx."/goods/".$data['goods_id'].".gif title=\"".$data['name']."\"> <a href=?p=fergb&s=sgc&id=".$data['goods_id'].">".$data['name']."</a> (".(!$data['tc'] ? 0 : $data['tc']).")</td>";
}
echo "</tr><tr><td align=\"center\" colspan=3><input type=button class=button onClick=\"cClick();\" value=Schließen></td></tr></table>";
?>