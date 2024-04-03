<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../../inc/func.inc.php");
include_once("../../inc/config.inc.php");
include_once("../../class/db.class.php");

@session_start();

if (!check_int($_GET['gg']) || !check_int($_GET['gc']) || !check_int($_GET['wg']) || !check_int($_GET['wc']) || $_SESSION['login'] != 1) exit;
$db = new db;

$data = $db->query("SELECT SUM(gcount*count) as gc,SUM(wcount*count) as wc FROM stu_trade_offers WHERE ggoods_id=".$_GET['gg']." AND wgoods_id=".$_GET['wg'],4);

if ($data['gc'] == 0 || !$_GET['gc']) $vd = "N/A";
else $vd = @round(@$data['wc']/$data['gc'],2);
$od = @round(@$_GET['wc']/$_GET['gc'],2);

if ($vd != "N/A")
{
	$war = round((100/$vd)*$od);
	$vd = "1:".$vd;
}
if ($war >= 150) $od = "<font color=#FF0000>1:".$od."</font>";
else $od = "<font color=Green>1:".$od."</font>";

echo "<table class=tcal>
<th colspan=2>Preischeck</th>
<tr>
	<td>Dein Angebot</td>
	<td>".$od."</td>
</tr>
<tr>
	<td>Durchschnitt</td>
	<td>".$vd."</td>
</tr>
</table>";
?>