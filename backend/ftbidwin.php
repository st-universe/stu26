<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../inc/func.inc.php");
include_once("../inc/config.inc.php");
include_once("../class/db.class.php");

@session_start();

if ($_SESSION["login"] != 1 || !check_int($_GET['id'])) exit;

$db = new db;
include_once("../class/comm.class.php");
$comm = new comm;

$gfx = $_SESSION[gfx_path];
if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "../gfx/";

$trade = $db->query("SELECT a.trade_id,a.user_id,a.give_good,a.give_count,UNIX_TIMESTAMP(a.date) as date_tsp,a.want_good1,a.want_good2,a.want_good,a.want_count,a.want_user_id,b.name,c.user FROM stu_trade_ferg as a LEFT JOIN stu_goods as b ON b.goods_id=a.give_good LEFT JOIN stu_user as c ON c.id=a.user_id WHERE a.trade_id=".$_GET['id']." LIMIT 1",4);
if ($trade == 0) exit;

if ($trade['date_tsp']+259200 < time())
{
	echo "<table class=\"tcal\" style=\"border: 1px groove #8897cf;\"><th>Angebotsende</th><tr><td>Das Angebot ist bereits ausgelaufen</td></tr><tr><td><input type=\"button\" value=\"Schließen\" class=\"button\" onClick=\"cClick();\"></td></tr></table>";
	exit;
}

if ($_GET['a'] == "bid" && $trade['user_id'] != $_SESSION['uid'] && check_int($_GET['gc']))
{
	$result = bidup($trade,$_GET['gc'],$_GET['gi']);
	$trade = $db->query("SELECT a.trade_id,a.user_id,a.give_good,a.give_count,UNIX_TIMESTAMP(a.date) as date_tsp,a.want_good1,a.want_good2,a.want_good,a.want_count,a.want_user_id,b.name,c.user FROM stu_trade_ferg as a LEFT JOIN stu_goods as b ON b.goods_id=a.give_good LEFT JOIN stu_user as c ON c.id=a.user_id WHERE a.trade_id=".$_GET['id']." LIMIT 1",4);
}
if ($_GET['a'] == "fbid" && $trade['user_id'] != $_SESSION['uid'] && check_int($_GET['gc']))
{
	$result = bidupfirst($trade,$_GET['gc'],$_GET['gi']);
	$trade = $db->query("SELECT a.trade_id,a.user_id,a.give_good,a.give_count,UNIX_TIMESTAMP(a.date) as date_tsp,a.want_good1,a.want_good2,a.want_good,a.want_count,a.want_user_id,b.name,c.user FROM stu_trade_ferg as a LEFT JOIN stu_goods as b ON b.goods_id=a.give_good LEFT JOIN stu_user as c ON c.id=a.user_id WHERE a.trade_id=".$_GET['id']." LIMIT 1",4);
}
function bidup($data,$count,$goods_id)
{
	global $db,$_SESSION,$comm;
	if ($goods_id != $data['want_good']) return;
	if ($data['want_good'] == 0) return;
	if ($data['date_tsp']+259200 < time()) return "Diese Auktion ist beendet";
	if ($count <= $data['want_count']) return "Das neue Gebot muss über dem alten liegen";
	$gc = $db->query("SELECT count FROM stu_trade_goods WHERE offer_id=0 AND user_id=".$_SESSION['uid']." AND goods_id=".$goods_id." LIMIT 1",1);
	$good = $db->query("SELECT name FROM stu_goods WHERE goods_id=".$goods_id." LIMIT 1",1);
	// Eigenes Gebot erhöhen
	if ($data['want_user_id'] == $_SESSION['uid'])
	{
		$mc = $db->query("SELECT MAX(maxcount) FROM stu_trade_ferg_history WHERE trade_id=".$data['trade_id'],1);
		if ($count <= $mc) return "Das neue Angebot muss höher als ".$mc." sein";
		if ($gc+$mc < $count) return "Es werden ".$count." ".$good." benötigt - Vorhanden sind nur ".($gc+$mc);
		$db->query("UPDATE stu_trade_ferg_history SET maxcount=".$count." WHERE trade_id=".$data['trade_id']." AND user_id=".$_SESSION['uid']." LIMIT 1");
		$result = $db->query("UPDATE stu_trade_goods SET count=count-".($count-$mc)." WHERE goods_id=".$goods_id." AND offer_id=0 AND user_id=".$_SESSION['uid']." AND count>".($count-$mc),6);
		if ($result == 0) $db->query("DELETE FROM stu_trade_goods WHERE goods_id=".$goods_id." AND offer_id=0 AND user_id=".$_SESSION['uid']);
		return "Das Angebot wurde erhöht";
	}
	if ($gc < $count) return "Es werden ".$count." ".$good." benötigt - Vorhanden sind nur ".(!$gc ? 0 : $gc);
	// Überprüfen, ob das neue Gebot über dem Maximalgebot des alten liegt
	$mc = $db->query("SELECT MAX(maxcount) FROM stu_trade_ferg_history WHERE trade_id=".$data['trade_id'],1);
	if ($mc > $count || $mc == $count)
	{
		$db->query("INSERT INTO stu_trade_ferg_history (trade_id,user_id,count,date,maxcount) VALUES ('".$data['trade_id']."','".$_SESSION['uid']."','".$count."','".date("Y-m-d H:i:s",time()-1)."','".$count."')");
		$db->query("UPDATE stu_trade_ferg_history SET date=NOW(),count=".$count." WHERE trade_id=".$data['trade_id']." AND user_id=".$data['want_user_id']." AND maxcount=".$mc." LIMIT 1");
		$db->query("UPDATE stu_trade_ferg SET want_count=".$count.",bids=bids+1 WHERE trade_id=".$data['trade_id']." LIMIT 1");
		return "Das Angebot wurde abgegeben doch der vorherige Bieter ist mitgezogen";
	}
	$res = $db->query("INSERT INTO stu_trade_ferg_history (trade_id,user_id,count,date,maxcount) VALUES ('".$data['trade_id']."','".$_SESSION['uid']."','".($mc+1)."',NOW(),'".$count."')",6);
	if (!$res || $res == 0) return;
	$db->query("UPDATE stu_trade_ferg_history SET count=".$mc." WHERE trade_id=".$data['trade_id']." AND user_id=".$data['want_user_id']." AND maxcount=".$mc." LIMIT 1");
	if ($_SESSION['uid'] != $data['want_user_id']) $comm->sendpm(1,$data['want_user_id'],"Du wurdest bei der Auktion ".$data['trade_id']." von ".$_SESSION['user']." überboten",2);
	$db->query("START TRANSACTION");
	$db->query("UPDATE stu_trade_ferg SET want_user_id=".$_SESSION['uid'].",want_count=".($mc+1).",bids=bids+1 WHERE trade_id=".$data['trade_id']." LIMIT 1");
	
	$result = $db->query("UPDATE stu_trade_goods SET count=count+".$mc." WHERE goods_id=".$goods_id." AND offer_id=0 AND user_id=".$data['want_user_id'],6);
	if ($result == 0) $db->query("INSERT INTO stu_trade_goods (user_id,goods_id,count,date) VALUES ('".$data['want_user_id']."','".$goods_id."','".$mc."',NOW())");
	
	$result = $db->query("UPDATE stu_trade_goods SET count=count-".$count." WHERE goods_id=".$goods_id." AND offer_id=0 AND user_id=".$_SESSION['uid']." AND count>".$count,6);
	if ($result == 0) $db->query("DELETE FROM stu_trade_goods WHERE goods_id=".$goods_id." AND offer_id=0 AND user_id=".$_SESSION['uid']);
	$db->query("COMMIT");
	return "Das Angebot wurde abgegeben";
}
function bidupfirst($data,$count,$goods_id)
{
	global $db,$_SESSION,$comm;
	if ($goods_id != $data['want_good1'] && $goods_id != $data['want_good2']) return;
	if ($data['date_tsp']+259200 < time()) return "Diese Auktion ist beendet";
	if ($count < 1) return "Das Gebot muss mindestens 1 sein";
	$gc = $db->query("SELECT count FROM stu_trade_goods WHERE offer_id=0 AND user_id=".$_SESSION['uid']." AND goods_id=".$goods_id." LIMIT 1",1);
	$good = $db->query("SELECT name FROM stu_goods WHERE goods_id=".$goods_id." LIMIT 1",1);
	if ($gc < $count) return "Es werden ".$count." ".$good." benötigt - Vorhanden sind nur ".(!$gc ? 0 : $gc);
	$res = $db->query("INSERT INTO stu_trade_ferg_history (trade_id,user_id,count,date,maxcount) VALUES ('".$data['trade_id']."','".$_SESSION['uid']."','1',NOW(),'".$count."')",6);
	if (!$res || $res == 0) return;
	$db->query("START TRANSACTION");
	$db->query("UPDATE stu_trade_ferg SET want_user_id=".$_SESSION['uid'].",want_good=".$goods_id.",want_count=1,bids=bids+1 WHERE trade_id=".$data['trade_id']." LIMIT 1");
	$result = $db->query("UPDATE stu_trade_goods SET count=count-".$count." WHERE goods_id=".$goods_id." AND offer_id=0 AND user_id=".$_SESSION['uid']." AND count>".$count,6);
	if ($result == 0) $db->query("DELETE FROM stu_trade_goods WHERE goods_id=".$goods_id." AND offer_id=0 AND user_id=".$_SESSION['uid']);
	$db->query("COMMIT");
	return "Das Angebot wurde abgegeben";
}

echo "<div id=\"ftbd\">
<table class=\"tcal\" style=\"border: 1px groove #8897cf;\">
<th>Gegenstand</th><th>Anbieter</th><th>Restzeit</th><th></th>
<tr>
<td><img src=".$gfx."/goods/".$trade['give_good'].".gif title=\"".$trade['name']."\"> ".$trade['give_count']."</td>
<td>".stripslashes($trade['user'])." (".$trade['user_id'].")</td>
<td>".gen_time(($trade['date_tsp']+259200)-time())."</td>
<td><input type=button class=button value=\"Aktualisieren\" onClick=\"refreshbidwin(".$trade['trade_id'].");\"></td>
</tr>";
if ($trade['want_user_id'] > 0)
{
	echo "<tr>
	<td colspan=\"4\">Höchstbieter: ".stripslashes($db->query("SELECT user FROM stu_user WHERE id=".$trade['want_user_id']." LIMIT 1",1))." (".$trade['want_user_id'].")</td>
	</tr>";
}
echo "<th colspan=\"4\">Gebot abgeben</th>
<tr>
<td colspan=\"4\">";
if ($trade['want_user_id'] > 0)
{
	$gc = $db->query("SELECT count FROM stu_trade_goods WHERE offer_id=0 AND user_id=".$_SESSION['uid']." AND goods_id=".$trade['want_good']." LIMIT 1",1);
	if (!$gc) $gc = 0;
	if ($trade['want_user_id'] == $_SESSION['uid']) $val = $db->query("SELECT MAX(maxcount) FROM stu_trade_ferg_history WHERE trade_id=".$trade['trade_id']." LIMIT 1",1)+1;
	else $val = ($trade['want_count']+1);
	echo "<form name=bidform><input type=hidden name=bidgood value=".$trade['want_good']."><img src=".$gfx."/goods/".$trade['want_good'].".gif> <input type=text size=4 class=text name=bid value=".$val."> / ".($trade['want_count']+1 > $gc ? "<font color=#FF0000>".$gc."</font>" : $gc)." <input type=button class=button value=\"Gebot abgeben\" onClick=\"makebid(".$trade['trade_id'].");\"> <input type=button class=button onClick=\"cClick();\" value=Schließen></form>";
}
else
{
	if ($trade['want_good'] > 0)
	{
		$gc = $db->query("SELECT count FROM stu_trade_goods WHERE offer_id=0 AND user_id=".$_SESSION['uid']." AND goods_id=".$trade['want_good']." LIMIT 1",1);
		if (!$gc) $gc = 0;
		if ($trade['want_user_id'] == $_SESSION['uid']) $val = $db->query("SELECT MAX(maxcount) FROM stu_trade_ferg_history WHERE trade_id=".$trade['trade_id']." LIMIT 1",1)+1;
		else $val = ($trade['want_count']+1);
		echo "<form name=bidform><input type=hidden name=bidgood value=".$trade['want_good']."><img src=".$gfx."/goods/".$trade['want_good'].".gif> <input type=text size=4 class=text name=bid value=".$val."> / ".($trade['want_count']+1 > $gc ? "<font color=#FF0000>".$gc."</font>" : $gc)." <input type=button class=button value=\"Gebot abgeben\" onClick=\"makebid(".$trade['trade_id'].");\"> <input type=button class=button onClick=\"cClick();\" value=Schließen></form>";
	}
	else
	{
		$gc1 = $db->query("SELECT count FROM stu_trade_goods WHERE offer_id=0 AND user_id=".$_SESSION['uid']." AND goods_id=".$trade['want_good1']." LIMIT 1",1);
		if (!$gc1) $gc1 = 0;
		$gc2 = $db->query("SELECT count FROM stu_trade_goods WHERE offer_id=0 AND user_id=".$_SESSION['uid']." AND goods_id=".$trade['want_good2']." LIMIT 1",1);
		if (!$gc2) $gc2 = 0;
		echo "<form name=bidform><table><tr><td>
		<input type=\"radio\" name=\"bidgood\" value=\"".$trade['want_good1']."\" CHECKED> <img src=".$gfx."/goods/".$trade['want_good1'].".gif> ".($gc1 == 0 ? "<font color=#FF0000>".$gc1."</font>" : $gc1)."<br>
		<input type=\"radio\" name=\"bidgood\" value=\"".$trade['want_good2']."\"> <img src=".$gfx."/goods/".$trade['want_good2'].".gif> ".($gc2 == 0 ? "<font color=#FF0000>".$gc2."</font>" : $gc2)."
		</td><td width=\"25\"></td><td><input type=text size=4 class=text name=bid value=1> <input type=button class=button value=\"Gebot abgeben\" onClick=\"makefirstbid(".$trade['trade_id'].")\"> <input type=button class=button onClick=\"cClick();\" value=Schließen></td></tr></table></form>";
	}
}
echo "</td>
</tr>";
if ($result) echo "<tr><td colspan=\"4\">".$result."</td></tr>";
echo "<th colspan=4>Auktionsverlauf</th>";
$result = $db->query("SELECT a.user_id,a.count,UNIX_TIMESTAMP(a.date) as date_tsp,b.user FROM stu_trade_ferg_history as a LEFT JOIN stu_user as b ON b.id=a.user_id WHERE a.trade_id=".$trade['trade_id']." ORDER BY a.count DESC");
while($data=mysql_fetch_assoc($result))
{
	echo "<tr>
	<td>".date("d.m. H:i",$data['date_tsp'])."</td>
	<td>".stripslashes($data['user'])." (".$data['user_id'].")</td>
	<td colspan=\"2\"><img src=".$gfx."/goods/".$trade['want_good'].".gif> ".$data['count']."</td>
	</tr>";
}
echo "<tr><td align=\"center\" colspan=\"4\"><input type=button class=button onClick=\"cClick();\" value=Schließen></td></tr></table></div>";
?>