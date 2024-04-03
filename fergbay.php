<?php
if (!is_object($db)) exit;
include_once("class/trade.class.php");
$trade = new trade;

switch ($_GET['s'])
{
	default:
		$v = "mainpage";
	case "ma":
		$v = "mainpage";
		break;
	case "sgc":
		$v = "showgoodcat";
		if (!check_int($_GET['id']))
		{
			$v = "mainpage";
			break;
		}
		$good = $db->query("SELECT name FROM stu_goods WHERE view='1' AND goods_id<200 AND goods_id=".$_GET['id'],1);
		if ($good === 0) $v = "mainpage";
		break;
	case "sc":
		if ($_GET['cat'] == 1) $v = "showcat1";
		if ($_GET['cat'] == 2) $v = "showcat2";
		if ($_GET['cat'] == 3) $v = "showcat3";
		if ($_GET['cat'] == 4) $v = "showcat4";
		break;
	case "smt":
		$v = "showmodcat";
		if (!check_int($_GET['id'])) $v = "mainpage";
		if ($_GET['id'] < 1 || $_GET['id'] > 11) $v = "mainpage";
		break;
	case "sl":
		$v = "showlast";
		break;
}
if (!$_GET['pa'] || !check_int($_GET['pa']) || $_GET['pa'] == 0) $_GET['pa'] = 1;

function getsites($cnt,$padd="")
{
	global $_GET;
	$in = $_GET['pa'];
	$pa = $_GET['pa'];
	$i = $in-2;
	$j = $in+2;
	$ceiled_knc = ceil($cnt/50);
	$ps0 = "<td>Seite: <a href=?p=fergb&pa=1>|<</a> <a href=?p=fergb".$padd."&pa=".($pa == 1 ? 1 : $pa-1)."><</a></td>";
	if ($i > 1) $ps = "<td class=\"pages\"><a href=?p=fergb&pa=1".$padd.">1</a></td>";
	if ($j < $ceiled_knc) $pe = "<td class=\"pages\"><a href=?p=fergb".$padd."&pa=".$ceiled_knc.">".$ceiled_knc."</a></td>";
	if ($j > $ceiled_knc) $j = $ceiled_knc;
	if ($i < 1) $i = 1;
	while($i<=$j)
	{
		$pages .= "<td class=\"pages\"><a href=?p=fergb".$padd."&pa=".$i.">".($i == $in ? "<div style=\"font-weight : bold; color: Yellow;\">".$i."</div>" : $i)."</a></td>";
		$i++;
	}
	$i = $in-2;
	$j = $in+2;
	$pages = $ps.($i > 2 ? "<td style=\"width: 20px; text-align: center;\">...</td>" : "").$pages.($ceiled_knc > $j+1 ? "<td style=\"width: 20px; text-align: center;\">... </td>" : "").$pe;
	$pe0 = "<td><a href=?p=fergb".$padd."&pa=".($pa == $ceiled_knc ? 1 : $pa+1).">></a>&nbsp;<a href=?p=fergb".$padd."&pa=".$ceiled_knc.">>|</a> (".$cnt." Angebote)</td>";
	return $ps0.$pages.$pe0;
}

echo "<script language=\"Javascript\">
function showgoodlist()
{	
	elt = 'ftgl';
	get_window(elt,700);
	sendRequest('backend/ftgoodlist.php?PHPSESSID=".session_id()."');
}
function get_window(elt,width)
{
	return overlib('<div id='+elt+'></div>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER, STICKY, RELX, 400, RELY, 70, WIDTH, width);
}
function showbidwin(tid)
{	
	elt = 'ftbw';
	get_window(elt,650);
	sendRequest('backend/ftbidwin.php?PHPSESSID=".session_id()."&id=' + tid);
}
function makebid(tid)
{
	gc = document.bidform.bid.value;
	gi = document.bidform.bidgood.value;
	var elt = 'result';
	sendRequest('backend/ftbidwin.php?PHPSESSID=".session_id()."&id=' + tid + '&a=bid&gc=' + gc + '&gi=' + gi);
}
function makefirstbid(tid)
{
	gc = document.bidform.bid.value;
	if (document.bidform.bidgood[0].checked == true) gi = document.bidform.bidgood[0].value;
	else gi = document.bidform.bidgood[1].value;
	var elt = 'result';
	sendRequest('backend/ftbidwin.php?PHPSESSID=".session_id()."&id=' + tid + '&a=fbid&gc=' + gc + '&gi=' + gi);
}
function refreshbidwin(tid)
{
	elt = 'ftbd';
	sendRequest('backend/ftbidwin.php?PHPSESSID=".session_id()."&id=' + tid);
}
function startauction()
{
	elt = 'auwi';
	get_window(elt,400);
	sendRequest('backend/fergauction.php?PHPSESSID=".session_id()."');
}
function auctionsteptwo()
{
	elt = 'auwi2';
	var go = document.forms.ferga.go.value;
	var gc = document.forms.ferga.gc.value;
	sendRequest('backend/fergauction.php?PHPSESSID=".session_id()."&st=2&go=' + go + '&gc=' + gc);
}
function showauctionlist()
{	
	elt = 'aucl';
	get_window(elt,600);
	sendRequest('backend/auctionlist.php?PHPSESSID=".session_id()."&m=ac');
}
function showownauctionlist()
{	
	elt = 'aucl';
	get_window(elt,600);
	sendRequest('backend/auctionlist.php?PHPSESSID=".session_id()."&m=oac');
}
function chggopic()
{
	var pic = document.forms.ferga.go.value;
	if (pic == parseInt(0))
	{
		document.getElementById(\"picgo\").innerHTML = '<img src=".$gfx."/buttons/info1.gif>';
		return;
	}
	document.getElementById(\"picgo\").innerHTML = '<img src=".$gfx."/goods/' + pic + '.gif>';
}
</script>
<style>
td.pages {
	text-align: center;
	width: 20px;
	border: 1px groove #8897cf;
}
td.pages:hover
{
	background: #262323;
}
</style>";

include_once("inc/lists/goods.php");

if ($v == "mainpage")
{
	pageheader("/ <b>Ferengi Auktionshaus");
	if ($_GET['a'] == "sa" && check_int($_GET['go']) && check_int($_GET['gc']) && check_int($_GET['go1']) && $_GET['go1'] != $_GET['go2'] && strlen($_GET['gc']) < 10)
	{
		if ($db->query("SELECT COUNT(trade_id) FROM stu_trade_ferg WHERE user_id=".$_SESSION['uid'],1) == 15) exit;
		$res = $db->query("SELECT a.count FROM stu_trade_goods as a LEFT JOIN stu_goods as b USING(goods_id) WHERE ".$_GET['gc'].">=b.ferg_minvalue AND a.goods_id=".$_GET['go']." AND a.count>=".$_GET['gc']." AND a.user_id=".$_SESSION['uid']." AND a.offer_id=0",1);
		if ($res == 0) exit;
		if ($db->query("SELECT goods_id FROM stu_goods WHERE view='1' AND goods_id=".$_GET['go1'],1) == 0) exit;
		if (check_int($_GET['go2']) && $db->query("SELECT goods_id FROM stu_goods WHERE view='1' AND goods_id=".$_GET['go2'],1) == 0) exit;
		$good_name = $db->query("SELECT name FROM stu_goods WHERE goods_id=".$_GET['go']." LIMIT 1",1);
		if ($good_name === 0) exit;
		$db->query("START TRANSACTION");
		$result = $db->query("UPDATE stu_trade_goods SET count=count-".$_GET['gc']." WHERE goods_id=".$_GET['go']." AND offer_id=0 AND user_id=".$_SESSION['uid']." AND count>".$_GET['gc'],6);
		if ($result == 0) $db->query("DELETE FROM stu_trade_goods WHERE goods_id=".$_GET['go']." AND offer_id=0 AND user_id=".$_SESSION['uid']);
		if (check_int($_GET['go2']) && check_int($_GET['go1'])) $db->query("INSERT INTO stu_trade_ferg (user_id,give_good,give_count,date,want_good1,want_good2) VALUES ('".$_SESSION['uid']."','".$_GET['go']."','".$_GET['gc']."',NOW(),'".$_GET['go1']."','".$_GET['go2']."')");
		else $db->query("INSERT INTO stu_trade_ferg (user_id,give_good,give_count,date,want_good,want_count) VALUES ('".$_SESSION['uid']."','".$_GET['go']."','".$_GET['gc']."',NOW(),'".$_GET['go1']."','0')");
		$db->query("COMMIT");
		meldung("Die Auktion wurde erstellt und endet in 3 Tagen");
	}
	$res = $db->query("SELECT a.trade_id,a.user_id,a.give_good,a.give_count,UNIX_TIMESTAMP(a.date) as date_tsp,a.want_good1,a.want_good2,a.want_good,a.want_count,a.bids,b.name,c.user FROM stu_trade_ferg as a LEFT JOIN stu_goods as b ON b.goods_id=a.give_good LEFT JOIN stu_user as c ON c.id=a.user_id WHERE UNIX_TIMESTAMP(a.date)+259200>".time()." ORDER BY a.date LIMIT ".(($_GET['pa']-1)*50).",50");
	$pages = getsites($db->query("SELECT COUNT(a.trade_id) FROM stu_trade_ferg as a LEFT JOIN stu_goods as b ON b.goods_id=a.give_good LEFT JOIN stu_user as c ON c.id=a.user_id WHERE UNIX_TIMESTAMP(a.date)+259200>".time(),1));
	echo "<table class=\"tcal\">
	<th width=\"150\">Kategorien</th><th>Laufende Auktionen</th>
	<tr>
	<td valign=\"top\" width=\"150\"><b><a href=?p=fergb&s=sc&cat=1>Standardwaren</a></b><br>";
	$result = $db->query("SELECT a.name,a.goods_id,COUNT(b.trade_id) as tid FROM stu_goods as a LEFT JOIN stu_trade_ferg as b ON b.give_good=a.goods_id WHERE a.goods_id=2 OR a.goods_id=4 OR a.goods_id=8 OR a.goods_id=31 OR a.goods_id=33 GROUP BY a.goods_id ORDER BY a.sort");
	while($data=mysql_fetch_assoc($result))
	{
		echo "- <a href=?p=fergb&s=sgc&id=".$data['goods_id'].">".$data['name']."</a> (".(!$data['tid'] ? 0 : $data['tid']).")<br>";
	}
	echo "	- [<b><a href=\"javascript:void(0);\" onClick=\"showgoodlist();\">mehr</a></b>]<br><br>
	<b><a href=?p=fergb&s=sc&cat=2>Module</a></b><br>";
	$result = $db->query("SELECT a.type,COUNT(b.trade_id) as tid FROM stu_modules as a LEFT JOIN stu_trade_ferg as b ON b.give_good=a.module_id GROUP BY a.type");
	while($data=mysql_fetch_assoc($result))
	{
		echo "- <a href=?p=fergb&s=smt&id=".$data['type']."> ".getmodtypedescr($data['type'])."</a> (".(!$data['tid'] ? 0 : $data['tid']).")<br>";
	}
	echo "<br>
	<b><a href=?p=fergb&s=sc&cat=3>Sonderwaren</a></b><br><br>
	<b><a href=?p=fergb&s=sc&cat=4>Schrottplatz</a></b><br><br>
	<a href=?p=fergb>Aktualisieren</a><br>
	<a href=\"javascript:void(0);\" onClick=\"showauctionlist();\">Auktionsstatus</a><br><br>
	<a href=\"javascript:void(0);\" onClick=\"startauction();\">Auktion starten</a><br>
	<a href=\"javascript:void(0);\" onClick=\"showownauctionlist();\">Meine Auktionen</a><br><br>
	<a href=?p=fergb&s=sl>Letzte Auktionen</a></td>
	<td valign=\"top\"><table class=\"tcal\">
	<tr><td colspan=\"6\"><table><tr>".$pages."</tr></table></td></tr>
	<th></th><th></th><th>Anbieter</th><th>Aktuelles Gebot</th><th>Restlaufzeit</th><th></th>";
	while($data=mysql_fetch_assoc($res))
	{
		$i++;
		if ($i == 2)
		{
			$trc = " style=\"background-color: #171616\"";
			$i = 0;
		}
		echo "<tr>
		<td".$trc."><img src=".$gfx."/goods/".$data['give_good'].".gif title=\"".$data['name']."\"> ".$data['give_count']."</td>
		<td".$trc.">".$data['trade_id']."</td>
		<td".$trc.">".stripslashes($data['user'])."</td>
		<td".$trc.">".($data['want_good'] != 0 ? "<img src=".$gfx."/goods/".$data['want_good'].".gif title=\"".getgoodname($data['want_good'])."\"> ".$data['want_count']." (".$data['bids']." Gebote)" : "<img src=".$gfx."/goods/".$data['want_good1'].".gif  title=\"".getgoodname($data['want_good1'])."\"> oder <img src=".$gfx."/goods/".$data['want_good2'].".gif title=\"".getgoodname($data['want_good2'])."\">")."</td>
		<td".$trc.">".($data['date_tsp']+259200 < time()+3600 ? "<font color=#FF0000>".gen_time(($data['date_tsp']+259200)-time())."</font>" : gen_time(($data['date_tsp']+259200)-time()))."</td>
		<td".$trc."><a href=\"javascript:void(0);\" onClick=\"showbidwin(".$data['trade_id'].");\" ".getonm("gbo".$data['trade_id'],'buttons/fergtrade')."><img src=".$gfx."/buttons/fergtrade1.gif name=gbo".$data['trade_id']." border=0 title=\"Gebot abgeben\"></a></td>
		</tr>";
		$trc = "";
	}
	echo "<tr><td colspan=\"6\"><table><tr>".$pages."</tr></table></td></tr></table></td>
	</tr>
	</table>";
}
if ($v == "showgoodcat")
{
	pageheader("/ <a href=?p=fergb>Ferengi Auktionshaus</a> / <b>Warenkategorie ".$good."</b>");
	$res = $db->query("SELECT a.trade_id,a.user_id,a.give_good,a.give_count,UNIX_TIMESTAMP(a.date) as date_tsp,a.want_good1,a.want_good2,a.want_good,a.want_count,a.bids,b.name,c.user FROM stu_trade_ferg as a LEFT JOIN stu_goods as b ON b.goods_id=a.give_good LEFT JOIN stu_user as c ON c.id=a.user_id WHERE a.give_good=".$_GET['id']." AND UNIX_TIMESTAMP(a.date)+259200>".time()." ORDER BY a.date LIMIT ".(($_GET['pa']-1)*50).",50");
	$pages = getsites($db->query("SELECT COUNT(a.trade_id) FROM stu_trade_ferg as a LEFT JOIN stu_goods as b ON b.goods_id=a.give_good LEFT JOIN stu_user as c ON c.id=a.user_id WHERE a.give_good=".$_GET['id']." AND UNIX_TIMESTAMP(a.date)+259200>".time(),1),"&s=sgc&id=".$_GET['id']);
	echo "<table class=\"tcal\">
	<th width=\"150\">Kategorien</th><th>Laufende Auktionen</th>
	<tr>
	<td valign=\"top\" width=\"150\"><b><a href=?p=fergb&s=sc&cat=1>Standardwaren</a></b><br>";
	$result = $db->query("SELECT a.name,a.goods_id,COUNT(b.trade_id) as tid FROM stu_goods as a LEFT JOIN stu_trade_ferg as b ON b.give_good=a.goods_id WHERE a.goods_id=2 OR a.goods_id=4 OR a.goods_id=8 OR a.goods_id=31 OR a.goods_id=33 GROUP BY a.goods_id ORDER BY a.sort");
	while($data=mysql_fetch_assoc($result))
	{
		echo "- <a href=?p=fergb&s=sgc&id=".$data['goods_id'].">".($_GET['id'] == $data['goods_id'] ? "<font color=Yellow>".$data['name']."</font>" : $data['name'])."</a> (".(!$data['tid'] ? 0 : $data['tid']).")<br>";
	}
	echo "	- [<b><a href=\"javascript:void(0);\" onClick=\"showgoodlist();\">mehr</a></b>]<br><br>
	<b><a href=?p=fergb&s=sc&cat=2>Module</a></b><br>";
	$result = $db->query("SELECT a.type,COUNT(b.trade_id) as tid FROM stu_modules as a LEFT JOIN stu_trade_ferg as b ON b.give_good=a.module_id GROUP BY a.type");
	while($data=mysql_fetch_assoc($result))
	{
		echo "- <a href=?p=fergb&s=smt&id=".$data['type']."> ".getmodtypedescr($data['type'])."</a> (".(!$data['tid'] ? 0 : $data['tid']).")<br>";
	}
	echo "<br>
	<b><a href=?p=fergb&s=sc&cat=3>Sonderwaren</a></b><br><br>
	<b><a href=?p=fergb&s=sc&cat=4>Schrottplatz</a></b><br><br>
	<a href=?p=fergb&s=sgc&id=".$_GET['id'].">Aktualisieren</a><br>
	<a href=\"javascript:void(0);\" onClick=\"showauctionlist();\">Auktionsstatus</a><br><br>
	<a href=\"javascript:void(0);\" onClick=\"startauction();\">Auktion starten</a><br>
	<a href=\"javascript:void(0);\" onClick=\"showownauctionlist();\">Meine Auktionen</a><br><br>
	<a href=?p=fergb&s=sl>Letzte Auktionen</a></td>
	<td valign=\"top\"><table class=\"tcal\">
	<tr><td colspan=\"6\"><table><tr>".$pages."</tr></table></td></tr>
	<th></th><th></th><th>Anbieter</th><th>Aktuelles Gebot</th><th>Restlaufzeit</th><th></th>";
	while($data=mysql_fetch_assoc($res))
	{
		$i++;
		if ($i == 2)
		{
			$trc = " style=\"background-color: #171616\"";
			$i = 0;
		}
		echo "<tr>
		<td".$trc."><img src=".$gfx."/goods/".$data['give_good'].".gif title=\"".$data['name']."\"> ".$data['give_count']."</td>
		<td".$trc.">".$data['trade_id']."</td>
		<td".$trc.">".stripslashes($data['user'])."</td>
		<td".$trc.">".($data['want_good'] != 0 ? "<img src=".$gfx."/goods/".$data['want_good'].".gif title=\"".getgoodname($data['want_good'])."\"> ".$data['want_count']." (".$data['bids']." Gebote)" : "<img src=".$gfx."/goods/".$data['want_good1'].".gif  title=\"".getgoodname($data['want_good1'])."\"> oder <img src=".$gfx."/goods/".$data['want_good2'].".gif title=\"".getgoodname($data['want_good2'])."\">")."</td>
		<td".$trc.">".($data['date_tsp']+259200 < time()+3600 ? "<font color=#FF0000>".gen_time(($data['date_tsp']+259200)-time())."</font>" : gen_time(($data['date_tsp']+259200)-time()))."</td>
		<td".$trc."><a href=\"javascript:void(0);\" onClick=\"showbidwin(".$data['trade_id'].");\" ".getonm("gbo".$data['trade_id'],'buttons/fergtrade')."><img src=".$gfx."/buttons/fergtrade1.gif name=gbo".$data['trade_id']." border=0 title=\"Gebot abgeben\"></a></td>
		</tr>";
		$trc = "";
	}
	echo "<tr><td colspan=\"6\"><table><tr>".$pages."</tr></table></td></tr>
	</table></td>
	</tr>
	</table>";
}
if ($v == "showcat1")
{
	pageheader("/ <a href=?p=fergb>Ferengi Auktionshaus</a> / <b>Hauptkategorie Waren</b>");
	$res = $db->query("SELECT a.trade_id,a.user_id,a.give_good,a.give_count,UNIX_TIMESTAMP(a.date) as date_tsp,a.want_good1,a.want_good2,a.want_good,a.want_count,a.bids,b.name,c.user FROM stu_trade_ferg as a LEFT JOIN stu_goods as b ON b.goods_id=a.give_good LEFT JOIN stu_user as c ON c.id=a.user_id WHERE a.give_good<200 AND b.view='1' AND UNIX_TIMESTAMP(a.date)+259200>".time()." ORDER BY a.date LIMIT ".(($_GET['pa']-1)*50).",50");
	$pages = getsites($db->query("SELECT COUNT(a.trade_id) FROM stu_trade_ferg as a LEFT JOIN stu_goods as b ON b.goods_id=a.give_good LEFT JOIN stu_user as c ON c.id=a.user_id WHERE a.give_good<200 AND b.view='1' AND UNIX_TIMESTAMP(a.date)+259200>".time(),1),"&s=sc&cat=1");
	echo "<table class=\"tcal\">
	<th width=\"150\">Kategorien</th><th>Laufende Auktionen</th>
	<tr>
	<td valign=\"top\" width=\"150\"><b><a href=?p=fergb&s=sc&cat=1><font color=Yellow>Standardwaren</font></a></b><br>";
	$result = $db->query("SELECT a.name,a.goods_id,COUNT(b.trade_id) as tid FROM stu_goods as a LEFT JOIN stu_trade_ferg as b ON b.give_good=a.goods_id WHERE a.goods_id=2 OR a.goods_id=4 OR a.goods_id=8 OR a.goods_id=31 OR a.goods_id=33 GROUP BY a.goods_id ORDER BY a.sort");
	while($data=mysql_fetch_assoc($result))
	{
		echo "- <a href=?p=fergb&s=sgc&id=".$data['goods_id'].">".$data['name']."</a> (".(!$data['tid'] ? 0 : $data['tid']).")<br>";
	}
	echo "	- [<b><a href=\"javascript:void(0);\" onClick=\"showgoodlist();\">mehr</a></b>]<br><br>
	<b><a href=?p=fergb&s=sc&cat=2>Module</a></b><br>";
	$result = $db->query("SELECT a.type,COUNT(b.trade_id) as tid FROM stu_modules as a LEFT JOIN stu_trade_ferg as b ON b.give_good=a.module_id GROUP BY a.type");
	while($data=mysql_fetch_assoc($result))
	{
		echo "- <a href=?p=fergb&s=smt&id=".$data['type']."> ".getmodtypedescr($data['type'])."</a> (".(!$data['tid'] ? 0 : $data['tid']).")<br>";
	}
	echo "<br>
	<b><a href=?p=fergb&s=sc&cat=3>Sonderwaren</a></b><br><br>
	<b><a href=?p=fergb&s=sc&cat=4>Schrottplatz</a></b><br><br>
	<a href=?p=fergb&s=sc&cat=1>Aktualisieren</a><br>
	<a href=\"javascript:void(0);\" onClick=\"showauctionlist();\">Auktionsstatus</a><br><br>
	<a href=\"javascript:void(0);\" onClick=\"startauction();\">Auktion starten</a><br>
	<a href=\"javascript:void(0);\" onClick=\"showownauctionlist();\">Meine Auktionen</a><br><br>
	<a href=?p=fergb&s=sl>Letzte Auktionen</a></td>
	<td valign=\"top\"><table class=\"tcal\">
	<tr><td colspan=\"6\"><table><tr>".$pages."</tr></table></td></tr>
	<th></th><th></th><th>Anbieter</th><th>Aktuelles Gebot</th><th>Restlaufzeit</th><th></th>";
	while($data=mysql_fetch_assoc($res))
	{
		$i++;
		if ($i == 2)
		{
			$trc = " style=\"background-color: #171616\"";
			$i = 0;
		}
		echo "<tr>
		<td".$trc."><img src=".$gfx."/goods/".$data['give_good'].".gif title=\"".$data['name']."\"> ".$data['give_count']."</td>
		<td".$trc.">".$data['trade_id']."</td>
		<td".$trc.">".stripslashes($data['user'])."</td>
		<td".$trc.">".($data['want_good'] != 0 ? "<img src=".$gfx."/goods/".$data['want_good'].".gif title=\"".getgoodname($data['want_good'])."\"> ".$data['want_count']." (".$data['bids']." Gebote)" : "<img src=".$gfx."/goods/".$data['want_good1'].".gif  title=\"".getgoodname($data['want_good1'])."\"> oder <img src=".$gfx."/goods/".$data['want_good2'].".gif title=\"".getgoodname($data['want_good2'])."\">")."</td>
		<td".$trc.">".($data['date_tsp']+259200 < time()+3600 ? "<font color=#FF0000>".gen_time(($data['date_tsp']+259200)-time())."</font>" : gen_time(($data['date_tsp']+259200)-time()))."</td>
		<td".$trc."><a href=\"javascript:void(0);\" onClick=\"showbidwin(".$data['trade_id'].");\" ".getonm("gbo".$data['trade_id'],'buttons/fergtrade')."><img src=".$gfx."/buttons/fergtrade1.gif name=gbo".$data['trade_id']." border=0 title=\"Gebot abgeben\"></a></td>
		</tr>";
		$trc = "";
	}
	echo "<tr><td colspan=\"6\"><table><tr>".$pages."</tr></table></td></tr>
	</table></td>
	</tr>
	</table>";
}
if ($v == "showcat2")
{
	pageheader("/ <a href=?p=fergb>Ferengi Auktionshaus</a> / <b>Hauptkategorie Waren</b>");
	$res = $db->query("SELECT a.trade_id,a.user_id,a.give_good,a.give_count,UNIX_TIMESTAMP(a.date) as date_tsp,a.want_good1,a.want_good2,a.want_good,a.want_count,a.bids,b.name,c.user FROM stu_trade_ferg as a LEFT JOIN stu_goods as b ON b.goods_id=a.give_good LEFT JOIN stu_user as c ON c.id=a.user_id WHERE a.give_good>=200 AND a.give_good<=1000 AND UNIX_TIMESTAMP(a.date)+259200>".time()." ORDER BY a.date LIMIT ".(($_GET['pa']-1)*50).",50");
	$pages = getsites($db->query("SELECT COUNT(a.trade_id) FROM stu_trade_ferg as a LEFT JOIN stu_goods as b ON b.goods_id=a.give_good LEFT JOIN stu_user as c ON c.id=a.user_id WHERE a.give_good>=200 AND a.give_good<=1000 AND UNIX_TIMESTAMP(a.date)+259200>".time(),1),"&s=sc&cat=2");
	echo "<table class=\"tcal\">
	<th width=\"150\">Kategorien</th><th>Laufende Auktionen</th>
	<tr>
	<td valign=\"top\" width=\"150\"><b><a href=?p=fergb&s=sc&cat=1>Standardwaren</a></b><br>";
	$result = $db->query("SELECT a.name,a.goods_id,COUNT(b.trade_id) as tid FROM stu_goods as a LEFT JOIN stu_trade_ferg as b ON b.give_good=a.goods_id WHERE a.goods_id=2 OR a.goods_id=4 OR a.goods_id=8 OR a.goods_id=31 OR a.goods_id=33 GROUP BY a.goods_id ORDER BY a.sort");
	while($data=mysql_fetch_assoc($result))
	{
		echo "- <a href=?p=fergb&s=sgc&id=".$data['goods_id'].">".$data['name']."</a> (".(!$data['tid'] ? 0 : $data['tid']).")<br>";
	}
	echo "	- [<b><a href=\"javascript:void(0);\" onClick=\"showgoodlist();\">mehr</a></b>]<br><br>
	<b><a href=?p=fergb&s=sc&cat=2><font color=Yellow>Module</font></a></b><br>";
	$result = $db->query("SELECT a.type,COUNT(b.trade_id) as tid FROM stu_modules as a LEFT JOIN stu_trade_ferg as b ON b.give_good=a.module_id GROUP BY a.type");
	while($data=mysql_fetch_assoc($result))
	{
		echo "- <a href=?p=fergb&s=smt&id=".$data['type']."> ".getmodtypedescr($data['type'])."</a> (".(!$data['tid'] ? 0 : $data['tid']).")<br>";
	}
	echo "<br>
	<b><a href=?p=fergb&s=sc&cat=3>Sonderwaren</a></b><br><br>
	<b><a href=?p=fergb&s=sc&cat=4>Schrottplatz</a></b><br><br>
	<a href=?p=fergb&s=sc&cat=2>Aktualisieren</a><br>
	<a href=\"javascript:void(0);\" onClick=\"showauctionlist();\">Auktionsstatus</a><br><br>
	<a href=\"javascript:void(0);\" onClick=\"startauction();\">Auktion starten</a><br>
	<a href=\"javascript:void(0);\" onClick=\"showownauctionlist();\">Meine Auktionen</a><br><br>
	<a href=?p=fergb&s=sl>Letzte Auktionen</a></td>
	<td valign=\"top\"><table class=\"tcal\">
	<tr><td colspan=\"6\"><table><tr>".$pages."</tr></table></td></tr>
	<th></th><th></th><th>Anbieter</th><th>Aktuelles Gebot</th><th>Restlaufzeit</th><th></th>";
	while($data=mysql_fetch_assoc($res))
	{
		$i++;
		if ($i == 2)
		{
			$trc = " style=\"background-color: #171616\"";
			$i = 0;
		}
		echo "<tr>
		<td".$trc."><img src=".$gfx."/goods/".$data['give_good'].".gif title=\"".$data['name']."\"> ".$data['give_count']."</td>
		<td".$trc.">".$data['trade_id']."</td>
		<td".$trc.">".stripslashes($data['user'])."</td>
		<td".$trc.">".($data['want_good'] != 0 ? "<img src=".$gfx."/goods/".$data['want_good'].".gif title=\"".getgoodname($data['want_good'])."\"> ".$data['want_count']." (".$data['bids']." Gebote)" : "<img src=".$gfx."/goods/".$data['want_good1'].".gif  title=\"".getgoodname($data['want_good1'])."\"> oder <img src=".$gfx."/goods/".$data['want_good2'].".gif title=\"".getgoodname($data['want_good2'])."\">")."</td>
		<td".$trc.">".($data['date_tsp']+259200 < time()+3600 ? "<font color=#FF0000>".gen_time(($data['date_tsp']+259200)-time())."</font>" : gen_time(($data['date_tsp']+259200)-time()))."</td>
		<td".$trc."><a href=\"javascript:void(0);\" onClick=\"showbidwin(".$data['trade_id'].");\" ".getonm("gbo".$data['trade_id'],'buttons/fergtrade')."><img src=".$gfx."/buttons/fergtrade1.gif name=gbo".$data['trade_id']." border=0 title=\"Gebot abgeben\"></a></td>
		</tr>";
		$trc = "";
	}
	echo "<tr><td colspan=\"6\"><table><tr>".$pages."</tr></table></td></tr>
	</table></td>
	</tr>
	</table>";
}
if ($v == "showcat3")
{
	pageheader("/ <a href=?p=fergb>Ferengi Auktionshaus</a> / <b>Hauptkategorie Waren</b>");
	$res = $db->query("SELECT a.trade_id,a.user_id,a.give_good,a.give_count,UNIX_TIMESTAMP(a.date) as date_tsp,a.want_good1,a.want_good2,a.want_good,a.want_count,a.bids,b.name,c.user FROM stu_trade_ferg as a LEFT JOIN stu_goods as b ON b.goods_id=a.give_good LEFT JOIN stu_user as c ON c.id=a.user_id WHERE (b.view='0' OR b.view='' OR ISNULL(b.view)) AND (b.goods_id < 900 OR b.goods_id > 912) AND UNIX_TIMESTAMP(a.date)+259200>".time()." ORDER BY a.date LIMIT ".(($_GET['pa']-1)*50).",50");
	$pages = getsites($db->query("SELECT COUNT(a.trade_id) FROM stu_trade_ferg as a LEFT JOIN stu_goods as b ON b.goods_id=a.give_good LEFT JOIN stu_user as c ON c.id=a.user_id WHERE (b.view='0' OR b.view='' OR ISNULL(b.view)) AND (b.goods_id < 900 OR b.goods_id > 912) AND UNIX_TIMESTAMP(a.date)+259200>".time(),1),"&s=sc&cat=3");
	echo "<table class=\"tcal\">
	<th width=\"150\">Kategorien</th><th>Laufende Auktionen</th>
	<tr>
	<td valign=\"top\" width=\"150\"><b><a href=?p=fergb&s=sc&cat=1>Standardwaren</a></b><br>";
	$result = $db->query("SELECT a.name,a.goods_id,COUNT(b.trade_id) as tid FROM stu_goods as a LEFT JOIN stu_trade_ferg as b ON b.give_good=a.goods_id WHERE a.goods_id=2 OR a.goods_id=4 OR a.goods_id=8 OR a.goods_id=31 OR a.goods_id=33 GROUP BY a.goods_id ORDER BY a.sort");
	while($data=mysql_fetch_assoc($result))
	{
		echo "- <a href=?p=fergb&s=sgc&id=".$data['goods_id'].">".$data['name']."</a> (".(!$data['tid'] ? 0 : $data['tid']).")<br>";
	}
	echo "	- [<b><a href=\"javascript:void(0);\" onClick=\"showgoodlist();\">mehr</a></b>]<br><br>
	<b><a href=?p=fergb&s=sc&cat=2>Module</a></b><br>";
	$result = $db->query("SELECT a.type,COUNT(b.trade_id) as tid FROM stu_modules as a LEFT JOIN stu_trade_ferg as b ON b.give_good=a.module_id GROUP BY a.type");
	while($data=mysql_fetch_assoc($result))
	{
		echo "- <a href=?p=fergb&s=smt&id=".$data['type']."> ".getmodtypedescr($data['type'])."</a> (".(!$data['tid'] ? 0 : $data['tid']).")<br>";
	}
	echo "<br>
	<b><a href=?p=fergb&s=sc&cat=3><font color=Yellow>Sonderwaren</font></a></b><br><br>
	<b><a href=?p=fergb&s=sc&cat=4>Schrottplatz</a></b><br><br>
	<a href=?p=fergb&s=sc&cat=3>Aktualisieren</a><br>
	<a href=\"javascript:void(0);\" onClick=\"showauctionlist();\">Auktionsstatus</a><br><br>
	<a href=\"javascript:void(0);\" onClick=\"startauction();\">Auktion starten</a><br>
	<a href=\"javascript:void(0);\" onClick=\"showownauctionlist();\">Meine Auktionen</a><br><br>
	<a href=?p=fergb&s=sl>Letzte Auktionen</a></td>
	<td valign=\"top\"><table class=\"tcal\">
	<tr><td colspan=\"6\"><table><tr>".$pages."</tr></table></td></tr>
	<th></th><th></th><th>Anbieter</th><th>Aktuelles Gebot</th><th>Restlaufzeit</th><th></th>";
	while($data=mysql_fetch_assoc($res))
	{
		$i++;
		if ($i == 2)
		{
			$trc = " style=\"background-color: #171616\"";
			$i = 0;
		}
		echo "<tr>
		<td".$trc."><img src=".$gfx."/goods/".$data['give_good'].".gif title=\"".$data['name']."\"> ".$data['give_count']."</td>
		<td".$trc.">".$data['trade_id']."</td>
		<td".$trc.">".stripslashes($data['user'])."</td>
		<td".$trc.">".($data['want_good'] != 0 ? "<img src=".$gfx."/goods/".$data['want_good'].".gif title=\"".getgoodname($data['want_good'])."\"> ".$data['want_count']." (".$data['bids']." Gebote)" : "<img src=".$gfx."/goods/".$data['want_good1'].".gif  title=\"".getgoodname($data['want_good1'])."\"> oder <img src=".$gfx."/goods/".$data['want_good2'].".gif title=\"".getgoodname($data['want_good2'])."\">")."</td>
		<td".$trc.">".($data['date_tsp']+259200 < time()+3600 ? "<font color=#FF0000>".gen_time(($data['date_tsp']+259200)-time())."</font>" : gen_time(($data['date_tsp']+259200)-time()))."</td>
		<td".$trc."><a href=\"javascript:void(0);\" onClick=\"showbidwin(".$data['trade_id'].");\" ".getonm("gbo".$data['trade_id'],'buttons/fergtrade')."><img src=".$gfx."/buttons/fergtrade1.gif name=gbo".$data['trade_id']." border=0 title=\"Gebot abgeben\"></a></td>
		</tr>";
		$trc = "";
	}
	echo "<tr><td colspan=\"6\"><table><tr>".$pages."</tr></table></td></tr>
	</table></td>
	</tr>
	</table>";
}
if ($v == "showcat4")
{
	pageheader("/ <a href=?p=fergb>Ferengi Auktionshaus</a> / <b>Ferengi-Schrottplatz</b>");
	$res = $db->query("SELECT a.trade_id,a.user_id,a.give_good,a.give_count,UNIX_TIMESTAMP(a.date) as date_tsp,a.want_good1,a.want_good2,a.want_good,a.want_count,a.bids,b.name,c.user FROM stu_trade_ferg as a LEFT JOIN stu_goods as b ON b.goods_id=a.give_good LEFT JOIN stu_user as c ON c.id=a.user_id WHERE (b.goods_id <'912' AND b.goods_id >'900') AND UNIX_TIMESTAMP(a.date)+259200>".time()." ORDER BY a.date LIMIT ".(($_GET['pa']-1)*50).",50");
	$pages = getsites($db->query("SELECT COUNT(a.trade_id) FROM stu_trade_ferg as a LEFT JOIN stu_goods as b ON b.goods_id=a.give_good LEFT JOIN stu_user as c ON c.id=a.user_id WHERE (b.goods_id <'912' AND b.goods_id >'900') AND UNIX_TIMESTAMP(a.date)+259200>".time(),1),"&s=sc&cat=3");
	echo "<table class=\"tcal\">
	<th width=\"150\">Kategorien</th><th>Laufende Auktionen</th>
	<tr>
	<td valign=\"top\" width=\"150\"><b><a href=?p=fergb&s=sc&cat=1>Standardwaren</a></b><br>";
	$result = $db->query("SELECT a.name,a.goods_id,COUNT(b.trade_id) as tid FROM stu_goods as a LEFT JOIN stu_trade_ferg as b ON b.give_good=a.goods_id WHERE a.goods_id=2 OR a.goods_id=4 OR a.goods_id=8 OR a.goods_id=31 OR a.goods_id=33 GROUP BY a.goods_id ORDER BY a.sort");
	while($data=mysql_fetch_assoc($result))
	{
		echo "- <a href=?p=fergb&s=sgc&id=".$data['goods_id'].">".$data['name']."</a> (".(!$data['tid'] ? 0 : $data['tid']).")<br>";
	}
	echo "	- [<b><a href=\"javascript:void(0);\" onClick=\"showgoodlist();\">mehr</a></b>]<br><br>
	<b><a href=?p=fergb&s=sc&cat=2>Module</a></b><br>";
	$result = $db->query("SELECT a.type,COUNT(b.trade_id) as tid FROM stu_modules as a LEFT JOIN stu_trade_ferg as b ON b.give_good=a.module_id GROUP BY a.type");
	while($data=mysql_fetch_assoc($result))
	{
		echo "- <a href=?p=fergb&s=smt&id=".$data['type']."> ".getmodtypedescr($data['type'])."</a> (".(!$data['tid'] ? 0 : $data['tid']).")<br>";
	}
	echo "<br>
	<b><a href=?p=fergb&s=sc&cat=3>Sonderwaren</a></b><br><br>
	<b><a href=?p=fergb&s=sc&cat=4><font color=Yellow>Schrottplatz</font></a></b><br><br>
	<a href=?p=fergb&s=sc&cat=3>Aktualisieren</a><br>
	<a href=\"javascript:void(0);\" onClick=\"showauctionlist();\">Auktionsstatus</a><br><br>
	<a href=\"javascript:void(0);\" onClick=\"startauction();\">Auktion starten</a><br>
	<a href=\"javascript:void(0);\" onClick=\"showownauctionlist();\">Meine Auktionen</a><br><br>
	<a href=?p=fergb&s=sl>Letzte Auktionen</a></td>
	<td valign=\"top\"><table class=\"tcal\">
	<tr><td colspan=\"6\"><table><tr>".$pages."</tr></table></td></tr>
	<th></th><th></th><th>Anbieter</th><th>Aktuelles Gebot</th><th>Restlaufzeit</th><th></th>";
	while($data=mysql_fetch_assoc($res))
	{
		$i++;
		if ($i == 2)
		{
			$trc = " style=\"background-color: #171616\"";
			$i = 0;
		}
		echo "<tr>
		<td".$trc."><img src=".$gfx."/goods/".$data['give_good'].".gif title=\"".$data['name']."\"> ".$data['give_count']."</td>
		<td".$trc.">".$data['trade_id']."</td>
		<td".$trc.">".stripslashes($data['user'])."</td>
		<td".$trc.">".($data['want_good'] != 0 ? "<img src=".$gfx."/goods/".$data['want_good'].".gif title=\"".getgoodname($data['want_good'])."\"> ".$data['want_count']." (".$data['bids']." Gebote)" : "<img src=".$gfx."/goods/".$data['want_good1'].".gif  title=\"".getgoodname($data['want_good1'])."\"> oder <img src=".$gfx."/goods/".$data['want_good2'].".gif title=\"".getgoodname($data['want_good2'])."\">")."</td>
		<td".$trc.">".($data['date_tsp']+259200 < time()+3600 ? "<font color=#FF0000>".gen_time(($data['date_tsp']+259200)-time())."</font>" : gen_time(($data['date_tsp']+259200)-time()))."</td>
		<td".$trc."><a href=\"javascript:void(0);\" onClick=\"showbidwin(".$data['trade_id'].");\" ".getonm("gbo".$data['trade_id'],'buttons/fergtrade')."><img src=".$gfx."/buttons/fergtrade1.gif name=gbo".$data['trade_id']." border=0 title=\"Gebot abgeben\"></a></td>
		</tr>";
		$trc = "";
	}
	echo "<tr><td colspan=\"6\"><table><tr>".$pages."</tr></table></td></tr>
	</table></td>
	</tr>
	</table>";
}
if ($v == "showmodcat")
{
	pageheader("/ <a href=?p=fergb>Ferengi Auktionshaus</a> / <b>Modulkategorie ".getmodtypedescr($_GET['id'])."</b>");
	$res = $db->query("SELECT b.trade_id,b.user_id,b.give_good,b.give_count,UNIX_TIMESTAMP(b.date) as date_tsp,b.want_good1,b.want_good2,b.want_good,b.want_count,b.bids,c.name,d.user FROM stu_modules as a LEFT JOIN stu_trade_ferg as b ON b.give_good=a.module_id LEFT JOIN stu_goods as c ON c.goods_id=b.give_good LEFT JOIN stu_user as d ON d.id=b.user_id WHERE a.type=".$_GET['id']." AND UNIX_TIMESTAMP(b.date)+259200>".time()." ORDER BY b.date LIMIT ".(($_GET['pa']-1)*50).",50");
	$pages = getsites($db->query("SELECT COUNT(b.trade_id) FROM stu_modules as a LEFT JOIN stu_trade_ferg as b ON b.give_good=a.module_id LEFT JOIN stu_goods as c ON c.goods_id=b.give_good LEFT JOIN stu_user as d ON d.id=b.user_id WHERE a.type=".$_GET['id']." AND UNIX_TIMESTAMP(b.date)+259200>".time(),1),"&s=smt&id=".$_GET['id']);
	echo "<table class=\"tcal\">
	<th width=\"150\">Kategorien</th><th>Laufende Auktionen</th>
	<tr>
	<td valign=\"top\" width=\"150\"><b><a href=?p=fergb&s=sc&cat=1>Standardwaren</a></b><br>";
	$result = $db->query("SELECT a.name,a.goods_id,COUNT(b.trade_id) as tid FROM stu_goods as a LEFT JOIN stu_trade_ferg as b ON b.give_good=a.goods_id WHERE a.goods_id=2 OR a.goods_id=4 OR a.goods_id=8 OR a.goods_id=31 OR a.goods_id=33 GROUP BY a.goods_id ORDER BY a.sort");
	while($data=mysql_fetch_assoc($result))
	{
		echo "- <a href=?p=fergb&s=sgc&id=".$data['goods_id'].">".$data['name']."</a> (".(!$data['tid'] ? 0 : $data['tid']).")<br>";
	}
	echo "	- [<b><a href=\"javascript:void(0);\" onClick=\"showgoodlist();\">mehr</a></b>]<br><br>
	<b><a href=?p=fergb&s=sc&cat=2>Module</a></b><br>";
	$result = $db->query("SELECT a.type,COUNT(b.trade_id) as tid FROM stu_modules as a LEFT JOIN stu_trade_ferg as b ON b.give_good=a.module_id GROUP BY a.type");
	while($data=mysql_fetch_assoc($result))
	{
		echo "- <a href=?p=fergb&s=smt&id=".$data['type']."> ".($_GET['id'] == $data['type'] ? "<font color=Yellow>".getmodtypedescr($data['type'])."</font>" : getmodtypedescr($data['type']))."</a> (".(!$data['tid'] ? 0 : $data['tid']).")<br>";
	}
	echo "<br>
	<b><a href=?p=fergb&s=sc&cat=3>Sonderwaren</a></b><br><br>
	<b><a href=?p=fergb&s=sc&cat=4>Schrottplatz</a></b><br><br>
	<a href=?p=fergb&s=smt&id=".$_GET['id'].">Aktualisieren</a><br>
	<a href=\"javascript:void(0);\" onClick=\"showauctionlist();\">Auktionsstatus</a><br><br>
	<a href=\"javascript:void(0);\" onClick=\"startauction();\">Auktion starten</a><br>
	<a href=\"javascript:void(0);\" onClick=\"showownauctionlist();\">Meine Auktionen</a><br><br>
	<a href=?p=fergb&s=sl>Letzte Auktionen</a></td>
	<td valign=\"top\"><table class=\"tcal\">
	<tr><td colspan=\"6\"><table><tr>".$pages."</tr></table></td></tr>
	<th></th><th></th><th>Anbieter</th><th>Aktuelles Gebot</th><th>Restlaufzeit</th><th></th>";
	while($data=mysql_fetch_assoc($res))
	{
		$i++;
		if ($i == 2)
		{
			$trc = " style=\"background-color: #171616\"";
			$i = 0;
		}
		echo "<tr>
		<td".$trc."><img src=".$gfx."/goods/".$data['give_good'].".gif title=\"".$data['name']."\"> ".$data['give_count']."</td>
		<td".$trc.">".$data['trade_id']."</td>
		<td".$trc.">".stripslashes($data['user'])."</td>
		<td".$trc.">".($data['want_good'] != 0 ? "<img src=".$gfx."/goods/".$data['want_good'].".gif title=\"".getgoodname($data['want_good'])."\"> ".$data['want_count']." (".$data['bids']." Gebote)" : "<img src=".$gfx."/goods/".$data['want_good1'].".gif  title=\"".getgoodname($data['want_good1'])."\"> oder <img src=".$gfx."/goods/".$data['want_good2'].".gif title=\"".getgoodname($data['want_good2'])."\">")."</td>
		<td".$trc.">".($data['date_tsp']+259200 < time()+3600 ? "<font color=#FF0000>".gen_time(($data['date_tsp']+259200)-time())."</font>" : gen_time(($data['date_tsp']+259200)-time()))."</td>
		<td".$trc."><a href=\"javascript:void(0);\" onClick=\"showbidwin(".$data['trade_id'].");\" ".getonm("gbo".$data['trade_id'],'buttons/fergtrade')."><img src=".$gfx."/buttons/fergtrade1.gif name=gbo".$data['trade_id']." border=0 title=\"Gebot abgeben\"></a></td>
		</tr>";
		$trc = "";
	}
	echo "<tr><td colspan=\"6\"><table><tr>".$pages."</tr></table></td></tr>
	</table></td>
	</tr>
	</table>";
}
if ($v == "showlast")
{
	pageheader("/ <a href=?p=fergb>Ferengi Auktionshaus</a> / <b>Auktionen der letzten Stunde</b>");
	$res = $db->query("SELECT a.trade_id,a.user_id,a.give_good,a.give_count,UNIX_TIMESTAMP(a.date) as date_tsp,a.want_user_id,a.want_good,a.want_count,a.bids,b.user,c.user as wuser FROM stu_trade_ferg_last as a LEFT JOIN stu_user as b ON b.id=a.user_id LEFT JOIN stu_user as c ON c.id=a.want_user_id ORDER BY a.date LIMIT ".(($_GET['pa']-1)*50).",50");
	$pages = getsites($db->query("SELECT COUNT(trade_id) FROM stu_trade_ferg_last",1),"&s=sl");
	echo "<table class=\"tcal\">
	<th width=\"150\">Kategorien</th><th>Letzte Auktionen</th>
	<tr>
	<td valign=\"top\" width=\"150\"><b><a href=?p=fergb&s=sc&cat=1>Standardwaren</a></b><br>";
	$result = $db->query("SELECT a.name,a.goods_id,COUNT(b.trade_id) as tid FROM stu_goods as a LEFT JOIN stu_trade_ferg as b ON b.give_good=a.goods_id WHERE a.goods_id=2 OR a.goods_id=4 OR a.goods_id=8 OR a.goods_id=31 OR a.goods_id=33 GROUP BY a.goods_id ORDER BY a.sort");
	while($data=mysql_fetch_assoc($result))
	{
		echo "- <a href=?p=fergb&s=sgc&id=".$data['goods_id'].">".$data['name']."</a> (".(!$data['tid'] ? 0 : $data['tid']).")<br>";
	}
	echo "	- [<b><a href=\"javascript:void(0);\" onClick=\"showgoodlist();\">mehr</a></b>]<br><br>
	<b><a href=?p=fergb&s=sc&cat=2>Module</a></b><br>";
	$result = $db->query("SELECT a.type,COUNT(b.trade_id) as tid FROM stu_modules as a LEFT JOIN stu_trade_ferg as b ON b.give_good=a.module_id GROUP BY a.type");
	while($data=mysql_fetch_assoc($result))
	{
		echo "- <a href=?p=fergb&s=smt&id=".$data['type']."> ".getmodtypedescr($data['type'])."</a> (".(!$data['tid'] ? 0 : $data['tid']).")<br>";
	}
	echo "<br>
	<b><a href=?p=fergb&s=sc&cat=3>Sonderwaren</a></b><br><br>
	<b><a href=?p=fergb&s=sc&cat=4>Schrottplatz</a></b><br><br>
	<a href=?p=fergb&s=sl>Aktualisieren</a><br>
	<a href=\"javascript:void(0);\" onClick=\"showauctionlist();\">Auktionsstatus</a><br><br>
	<a href=\"javascript:void(0);\" onClick=\"startauction();\">Auktion starten</a><br>
	<a href=\"javascript:void(0);\" onClick=\"showownauctionlist();\">Meine Auktionen</a><br><br>
	<a href=?p=fergb&s=sl>Letzte Auktionen</a></td>
	<td valign=\"top\"><table class=\"tcal\">
	<tr><td colspan=\"6\"><table><tr>".$pages."</tr></table></td></tr>
	<th></th><th></th><th>Anbieter</th><th>Auktionsgewinner</th><th>Gebot</th><th>Datum</th>";
	while($data=mysql_fetch_assoc($res))
	{
		$i++;
		if ($i == 2)
		{
			$trc = " style=\"background-color: #171616\"";
			$i = 0;
		}
		echo "<tr>
		<td".$trc."><img src=".$gfx."/goods/".$data['give_good'].".gif title=\"".getgoodname($data['give_good'])."\"> ".$data['give_count']."</td>
		<td".$trc.">".$data['trade_id']."</td>
		<td".$trc.">".stripslashes($data['user'])."</td>
		<td".$trc.">".stripslashes($data['wuser'])."</td>
		<td".$trc.">".($data['want_good'] != 0 ? "<img src=".$gfx."/goods/".$data['want_good'].".gif title=\"".getgoodname($data['want_good'])."\"> ".$data['want_count']." (".$data['bids']." Gebote)" : "<img src=".$gfx."/goods/".$data['want_good1'].".gif  title=\"".getgoodname($data['want_good1'])."\"> oder <img src=".$gfx."/goods/".$data['want_good2'].".gif title=\"".getgoodname($data['want_good2'])."\">")."</td>
		<td".$trc.">".date("d.m.Y H:i",$data['date_tsp'])."</td>
		</tr>";
		$trc = "";
	}
	echo "<tr><td colspan=\"6\"><table><tr>".$pages."</tr></table></td></tr>
	</table></td>
	</tr>
	</table>";
}
?>