<?php
if (!is_object($db)) exit;
include_once("class/trade.class.php");
$t = new trade;
// include_once("inc/lists/goods.php");
switch($_GET['s'])
{

	case "net":
		$v = "network";
		break;
	case "no":
		$v = "newoffer";
		break;
	case "so":
		$v = "showoffer";
		break;
	case "po":
		$v = "payout";
		break;
	case "sp":
		$v = "showprices";
		break;
	default:
		$v = "main";
		break;		
}
// $v = "override";



		
	echo "<script type=\"text/javascript\" language=\"JavaScript\">
	var e;
	
function showGood(rel,id,isLeft)
{
		var pos = getPosition(rel);
		var y = pos.y + 0;
		//var x = pos.x + 30;
		
		if (isLeft) x = pos.x - 625;
		else		x = pos.x + 27;
		
		elt = 'infodiv';
		
		sendRequest('backend/infodisplay/goods.php?PHPSESSID=".session_id()."&gid='+id);
		
        document.getElementById('infodiv').style.left = x+'px';
        document.getElementById('infodiv').style.top = y+'px';
        document.getElementById('infodiv').style.visibility = \"visible\";
}		

function getPosition(element) {
    var xPosition = 0;
    var yPosition = 0;
  
    while(element) {
        xPosition += (element.offsetLeft - element.scrollLeft + element.clientLeft);
        yPosition += (element.offsetTop - element.scrollTop + element.clientTop);
        element = element.offsetParent;
    }
    return { x: xPosition, y: yPosition };
}
function getPos(el) {
    // yay readability
    for (var lx=0, ly=0;
         el != null;
         lx += el.offsetLeft, ly += el.offsetTop, el = el.offsetParent);
    return {x: lx,y: ly};
}
</script>";



if ($v == "override") {
	pageheader("/ <b>Warenbörse</b>");
	
	echo "<br><br><b>Die Warenbörse befindet sich zur Zeit im Umbau!</b>";
}

if ($v == "main") {
	pageheader("/ <b>Warenbörse</b>");


	if ($result) meldung($result);
	
	$nets = $t->getTradeNetworks();

	
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1>";
	
	
	echo "<th></th><th style=\"text-align:center;\">Ort</th><th>Warenbörse</th><th>Angebote</th><th>Waren</th><th>Schiffe</th>";
	
	foreach($nets as $net) {
		echo "<tr style=\"height:100px;\">";
		
		echo "<td style=\"text-align:center;\"><a href=?p=trade&s=net&n=".$net[network_id]."><img src=".$gfx."/ships/".$net['wbpic'].".gif title=\"".$net['name']."\"></a></td>";
		echo "<td width=70 style=\"text-align:center;\">".$net['cx']."|".$net['cy']."</td>";		
		echo "<td width=400><a href=?p=trade&s=net&n=".$net[network_id]."><b>".$net['name']."</b></a><br><br>Betreiber: ".$net['wbuser']."</td>";
		
	
		// echo "<td style=\"width:250px; vertical-align: top;\">";
		echo "<td style=\"width:250px;\">";
		echo "<a href=?p=trade&s=so&n=".$net[network_id].">".($net[offercount] > 0 ? "<font color=#00ff00>Eigene Angebote: ".$net[offercount]."</font>" : "<font color=#666666>Keine eigenen Angebote</font>")."</a><br><br>";		
		echo "<a href=?p=trade&s=net&n=".$net[network_id].">Angebote insgesamt: ".$net[totaloffers]."</a>";
		echo "</td>";
		
		$sum = $net[goodsum] + $net[offergoodsum];
		
		echo "<td style=\"width:250px;\">";
		echo ($net[goodsum] > 0 ? "<a href=?p=trade&s=no&n=".$net[network_id].">Waren im Lager: ".$net[goodsum]."</a>" : "<font color=#666666>Keine Waren im Lager</font>")."<br><br>";
		echo ($net[offergoodsum] > 0 ? "<a href=?p=trade&s=so&n=".$net[network_id].">Waren in Angeboten: ".$net[offergoodsum]."</a>" : "<font color=#666666>Keine Waren in Angeboten</font>")."<br><br>";
		echo "Warenlimit: ".$sum." / ".$net[max_storage];
		echo "</td>";
		echo "<td style=\"width:250px;\">";
		echo "".($net[shipsthere] > 0 ? "<font color=green>".$net[shipsthere]." Schiffe vor Ort</font>" : "<font color=#666666>Keine Schiffe vor Ort</font>")."";
		echo "</td>";
		echo "<td style=\"text-align:center;width:100px;\">";
		echo "<a href=?p=trade&s=net&n=".$net[network_id].">Anzeigen</a>";
		echo "</td>";		
		echo "</tr>";
	}
	
	echo "</table>";
	
	
	
	
	
	
	echo "<br><br><br>";
	
	// print_r($nets);
	
}
if ($v == "network")
{
	if (!check_int($_GET['n']) || $_GET['n'] == 0) exit();
	$net = $db->query("SELECT * FROM stu_trade_networks WHERE network_id = ".$_GET['n']." LIMIT 1",4);
	if (!$net) exit();
	
	// TODO
	if ($_GET['a'] == "takeoffer" && check_int($_GET['ac']) && $_GET['ac'] > 0 && check_int($_GET['id']) && $_GET['id'] > 0) $result = $t->takeoffer($_GET['n'],$_GET['id'],$_GET['ac']);
	// if ($_GET['a'] == "payout" && is_array($_GET['good']) && is_array($_GET['count']) && is_numeric($_GET['hp']) && $_GET['hp'] > 0) $result = $t->payout($_GET['good'],$_GET['count'],$_GET['hp']);
	// if ($_GET['a'] == "dof" && $_SESSION['uid'] < 100 && check_int($_GET['oid'])) $result = $t->npcdeloffer($_GET['oid']);
	// if ($_GET['a'] == "gof" && $_SESSION['uid'] < 100 && check_int($_GET['oid'])) $result = $t->npcgetoffergoods($_GET['oid']);
	if ($_GET['a'] == "mtr" && check_int($_GET['oid'])) $result = $t->markoffer($_GET['oid']);
		

		
		
	pageheader("/ <a href=?p=trade>Warenbörse</a> / <b>".$net[name]."</b>");
	if ($result) meldung($result);
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><th>Aktionen</th><tr><td><a href=?p=trade&s=no&n=".$_GET['n'].">Angebot erstellen</a><br>
	<a href=?p=trade&s=so&n=".$_GET['n'].">Meine Angebote einsehen</a><br>
	</td></tr></table><br><form action=main.php method=post name=sel><input type=hidden name=p value=trade><input type=hidden name=s value=net><input type=hidden name=n value=".$_GET['n']."><select name=mode><option value=1".($_GET['mode'] == 1 ? " selected" : "").">bietet<option value=2".($_GET['mode'] == 2 ? " selected" : "").">verlangt</select> <select name=sor>";	
	$goods = $db->query("SELECT goods_id,name FROM stu_goods WHERE view=1 ORDER BY sort");
	while($g = mysql_fetch_assoc($goods)) echo "<option value=".$g['goods_id'].($_GET['sor'] == $g['goods_id'] ? " selected" : "")."> ".stripslashes($g['name']);
	echo "<option>----------------</option><option value=x".($_GET['sor'] == "x" ? " selected" : "").">Sonderwaren</option>
	</select> <input type=text size=5 class=text name=cou value='".($_GET['cou'] ? $_GET['cou'] : "Menge")."' onClick=\"document.sel.cou.value=''\"> <input type=submit value=Filtern class=button> <input type=submit name=sor value=\"Filter aufheben\" class=button></form>";
	!check_int($_GET['pa']) || $_GET['pa'] == 0 ? $pa = 1 : $pa = $_GET['pa'];
	$t->getofferlist($_GET['n'],$_GET['sor'],$_GET['mode'],$_GET['cou'],$pa);
	if (mysql_num_rows($t->result) == 0) meldung("Keine Angebote vorhanden");
	else
	{
		if (!$_GET['pa']) $_GET['pa'] = 1;
		// Seiten erzeugen
		$in = $_GET['pa'];
		$i = $in-2;
		$j = $in+2;
		$ceiled_knc = ceil($t->sc/40);
		$ps0 = "<td>Seite: <a href=?p=trade&s=net&n=".$_GET['n']."&sor=".$_GET['sor']."&mode=".$_GET['mode']."&cou=".$_GET['cou']."&pa=1><<</a> <a href=?p=trade&s=net&n=".$_GET['n']."&sor=".$_GET['sor']."&mode=".$_GET['mode']."&pa=".($pa == 1 ? 1 : $pa-1)."><</a></td>";
		if ($i > 1) $ps = "<td class=\"pages\"><a href=?p=trade&s=net&n=".$_GET['n']."&sor=".$_GET['sor']."&mode=".$_GET['mode']."&cou=".$_GET['cou']."&pa=1>1</a></td>";
		if ($j < $ceiled_knc) $pe = "<td class=\"pages\"><a href=?p=trade&s=net&n=".$_GET['n']."&sor=".$_GET['sor']."&mode=".$_GET['mode']."&cou=".$_GET['cou']."&pa=".$ceiled_knc.">".$ceiled_knc."</a></td>";
		if ($j > $ceiled_knc) $j = $ceiled_knc;
		if ($i < 1) $i = 1;
		while($i<=$j)
		{
			$pages .= "<td class=\"pages\"><a href=?p=trade&s=net&n=".$_GET['n']."&sor=".$_GET['sor']."&mode=".$_GET['mode']."&cou=".$_GET['cou']."&pa=".$i.">".($i == $in ? "<div style=\"font-weight : bold; color: Yellow;\">".$i."</div>" : $i)."</a></td>";
			$i++;
		}
		$i = $in-2;
		$j = $in+2;
		$pages = $ps.($i > 2 ? "<td style=\"width: 20px; text-align: center;\">...</td>" : "").$pages.($ceiled_knc > $j+1 ? "<td style=\"width: 20px; text-align: center;\">... </td>" : "").$pe;
		$pe0 = "<td><a href=?p=trade&sor=".$_GET['sor']."&mode=".$_GET['mode']."&cou=".$_GET['cou']."&pa=".($pa == $ceiled_knc ? 1 : $pa+1).">></a>&nbsp;<a href=?p=trade&sor=".$_GET['sor']."&mode=".$_GET['mode']."&pa=".$ceiled_knc.">>></a> (".$t->sc." Angebote)</td>";
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=100%>
		<tr><td colspan=6><table><tr>".$ps0.$pages.$pe0."</tr></table></td></tr>
		<tr><th width=16></th><th width=500>Siedler</td><th width=100>Anzahl</th><th width=120></th><th>bietet</td><th>verlangt</td></tr>";
		while($data=mysql_fetch_assoc($t->result))
		{
			$i++;
			$k++;
			if ($k == 2)
			{
				$trc = " style=\"background-color: #171616\"";
				if ($data['marks'] >= 15 && $_SESSION['uid'] < 100) $trc = " style=\"background-color: #171616; border: 1px #FF0000 solid;\"";
				$k = 0;
			}
			else $trc = "";
			if ($data['marks'] >= 5 && $_SESSION['uid'] < 100) $trc = " style=\"border: 1px #FF0000 solid;\"";
			if ($ld != date("d.m.Y",$data['date_t'])) { echo "<tr><th colspan=6>".date("d.m.Y",$data['date_t'])."</th></tr>"; $ld = date("d.m.Y",$data['date_t']); }
			echo "<form action=main.php method=post><input type=hidden name=p value=trade><input type=hidden name=s value=net><input type=hidden name=n value=".$_GET['n']."><input type=hidden name=a value=takeoffer>
			<input type=hidden name=id value=".$data['offer_id']."><input type=hidden name=sor value=".$_GET['sor']."><input type=hidden name=mode value=".$_GET['mode']."><input type=hidden name=pa value=".$_GET['pa'].">
			<tr>
			<td".$trc."><a href=?p=trade&s=net&n=".$_GET['n']."&sor=xs&mode=".$data['user_id']." ".getonm('oof'.$data['offer_id'],'buttons/clist')."><img src=".$gfx."/buttons/clist1.gif border=0 name=oof".$data['offer_id']." title=\"Weitere Angebote dieses Siedlers\"></a></td>
			<td".$trc.">".stripslashes($data['user'])."</td>
			<td".$trc."><input type=text size=3 class=text name=ac value=1> / ".$data['count']." (".(!$data['vcount'] ? 0 : floor($data['vcount']/$data['wcount'])).")</td>
			<td".$trc."><input type=submit class=button value=Annehmen>";
			// if ($_SESSION['uid'] < 100)
			// {
				// if ($_SESSION['uid'] == 10 && $data['race'] == 1) echo " <a href=?p=trade&a=dof&oid=".$data['offer_id']."&sor=".$_GET['sor']."&mode=".$_GET['mode']."&pa=".$_GET['pa']." ".getonm("del".$i,"buttons/x")."><img src=".$gfx."/buttons/x1.gif name=del".$i." title=\"Angebot ".$data['offer_id']." löschen\" border=0></a> <a href=?p=trade&a=gof&oid=".$data['offer_id']."&sor=".$_GET['sor']."&mode=".$_GET['mode']."&pa=".$_GET['pa']." ".getonm("delg".$i,"buttons/x")."><img src=".$gfx."/buttons/x1.gif name=delg".$i." title=\"Angebot ".$data['offer_id']." einziehen\" border=0></a>";
				// if ($_SESSION['uid'] == 11 && $data['race'] == 2) echo " <a href=?p=trade&a=dof&oid=".$data['offer_id']."&sor=".$_GET['sor']."&mode=".$_GET['mode']."&pa=".$_GET['pa']." ".getonm("del".$i,"buttons/x")."><img src=".$gfx."/buttons/x1.gif name=del".$i." title=\"Angebot ".$data['offer_id']." löschen\" border=0></a> <a href=?p=trade&a=gof&oid=".$data['offer_id']."&sor=".$_GET['sor']."&mode=".$_GET['mode']."&pa=".$_GET['pa']." ".getonm("delg".$i,"buttons/x")."><img src=".$gfx."/buttons/x1.gif name=delg".$i." title=\"Angebot ".$data['offer_id']." einziehen\" border=0></a>";
				// if ($_SESSION['uid'] == 12 && $data['race'] == 3) echo " <a href=?p=trade&a=dof&oid=".$data['offer_id']."&sor=".$_GET['sor']."&mode=".$_GET['mode']."&pa=".$_GET['pa']." ".getonm("del".$i,"buttons/x")."><img src=".$gfx."/buttons/x1.gif name=del".$i." title=\"Angebot ".$data['offer_id']." löschen\" border=0></a> <a href=?p=trade&a=gof&oid=".$data['offer_id']."&sor=".$_GET['sor']."&mode=".$_GET['mode']."&pa=".$_GET['pa']." ".getonm("delg".$i,"buttons/x")."><img src=".$gfx."/buttons/x1.gif name=delg".$i." title=\"Angebot ".$data['offer_id']." einziehen\" border=0></a>";
				// if ($_SESSION['uid'] == 13 && $data['race'] == 4) echo " <a href=?p=trade&a=dof&oid=".$data['offer_id']."&sor=".$_GET['sor']."&mode=".$_GET['mode']."&pa=".$_GET['pa']." ".getonm("del".$i,"buttons/x")."><img src=".$gfx."/buttons/x1.gif name=del".$i." title=\"Angebot ".$data['offer_id']." löschen\" border=0></a> <a href=?p=trade&a=gof&oid=".$data['offer_id']."&sor=".$_GET['sor']."&mode=".$_GET['mode']."&pa=".$_GET['pa']." ".getonm("delg".$i,"buttons/x")."><img src=".$gfx."/buttons/x1.gif name=delg".$i." title=\"Angebot ".$data['offer_id']." einziehen\" border=0></a>";
				// if ($_SESSION['uid'] == 14 && $data['race'] == 5) echo " <a href=?p=trade&a=dof&oid=".$data['offer_id']."&sor=".$_GET['sor']."&mode=".$_GET['mode']."&pa=".$_GET['pa']." ".getonm("del".$i,"buttons/x")."><img src=".$gfx."/buttons/x1.gif name=del".$i." title=\"Angebot ".$data['offer_id']." löschen\" border=0></a> <a href=?p=trade&a=gof&oid=".$data['offer_id']."&sor=".$_GET['sor']."&mode=".$_GET['mode']."&pa=".$_GET['pa']." ".getonm("delg".$i,"buttons/x")."><img src=".$gfx."/buttons/x1.gif name=delg".$i." title=\"Angebot ".$data['offer_id']." einziehen\" border=0></a>";
				// if ($_SESSION['uid'] == 2) echo " <a href=?p=trade&a=dof&oid=".$data['offer_id']."&sor=".$_GET['sor']."&mode=".$_GET['mode']."&pa=".$_GET['pa']." ".getonm("del".$i,"buttons/x")."><img src=".$gfx."/buttons/x1.gif name=del".$i." title=\"Angebot ".$data['offer_id']." löschen\" border=0></a> <a href=?p=trade&a=gof&oid=".$data['offer_id']."&sor=".$_GET['sor']."&mode=".$_GET['mode']."&pa=".$_GET['pa']." ".getonm("delg".$i,"buttons/x")."><img src=".$gfx."/buttons/x1.gif name=delg".$i." title=\"Angebot ".$data['offer_id']." einziehen\" border=0></a>";
			// }
			// else echo "&nbsp;<a href=?p=trade&a=mtr&oid=".$data['offer_id']."&sor=".$_GET['sor']."&mode=".$_GET['mode']."&pa=".$_GET['pa']."&cou=".$cou." ".getonm('td'.$data['offer_id'],'buttons/x')."><img src=".$gfx."/buttons/x1.gif name=td".$data['offer_id']." border=0 title=\"Lagerangebot melden!\"></a>";
			echo "</td>
			<td".$trc."><img src=".$gfx."/goods/".$data['ggoods_id'].".gif onMouseOver=\"showGood(this,'".$data['ggoods_id']."',false);\" onMouseOut=\"hideInfo();\"> ".$data['gcount']."</td>
			<td".$trc."><img src=".$gfx."/goods/".$data['wgoods_id'].".gif onMouseOver=\"showGood(this,'".$data['wgoods_id']."',true);\" onMouseOut=\"hideInfo();\"> ".$data['wcount']."</td>
			</tr></form>";
		}
		echo "<tr><td colspan=6><table><tr>".$ps0.$pages.$pe0."</tr></table></td></tr></table>";
	}
}
if ($v == "newoffer")
{
	echo "<script language=\"Javascript\">
	function chggopic()
	{
		var pic = document.forms.nof.go.value;
		if (pic == parseInt(0))
		{
			document.getElementById(\"picgo\").innerHTML = '<img src=".$gfx."/buttons/info1.gif>';
			return;
		}
		document.getElementById(\"picgo\").innerHTML = '<img src=".$gfx."/goods/' + pic + '.gif>';
	}
	function chgwopic()
	{
		var pic = document.forms.nof.wo.value;
		if (pic == parseInt(0))
		{
			document.getElementById(\"picwo\").innerHTML = '<img src=".$gfx."/buttons/info1.gif>';
			return;
		}
		document.getElementById(\"picwo\").innerHTML = '<img src=".$gfx."/goods/' + pic + '.gif>';
	}
	function pricecheck()
	{
		var gg = document.forms.nof.go.value;
		var wg = document.forms.nof.wo.value;
		var gc = document.forms.nof.gcount.value;
		var wc = document.forms.nof.wcount.value;
		if (gg == 0 || wg == 0) return;
		elt = 'pc';
		sendRequest('backend/trade/pricecheck.php?PHPSESSID=".session_id()."&gg=' + gg + '&gc=' + gc + '&wg=' + wg + '&wc=' + wc);
	}
	</script>";
	
	if (!check_int($_GET['n']) || $_GET['n'] == 0) exit();
	$net = $db->query("SELECT * FROM stu_trade_networks WHERE network_id = ".$_GET['n']." LIMIT 1",4);
	if (!$net) exit();	
	
	if (check_int($_GET['go']) && check_int($_GET['wo']) && check_int($_GET['gcount']) && check_int($_GET['wcount']) && check_int($_GET['acount']) && $_GET['acount'] > 0) $result = $t->newoffer($_GET['n'],$_GET['go'],$_GET['wo'],$_GET['gcount'],$_GET['wcount'],round($_GET['acount']));
	pageheader("/ <a href=?p=trade>Warenbörse</a> / <b><a href=?p=trade&s=net&n=".$_GET['n'].">".$net[name]."</a></b> / <b>Angebot erstellen</b>");	
	if ($result) meldung($result);
	$gresult = $t->getgivegoodlist($_GET['n']);
	$wresult = $t->getwantgoodlist();
	echo "<form action=main.php method=post name=nof><input type=hidden name=p value=trade><input type=hidden name=s value=no><input type=hidden name=n value=".$_GET['n']."><input type=hidden name=wb value=1>
	<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=400>
	<th>Anbieten</th>
	<tr><td><input type=text size=5 name=gcount class=text> 
	<span id=picgo><img src=".$gfx."/buttons/info1.gif></span> <select name=\"go\" onChange=\"chggopic();\"><option value=0>-------------";
	while($data = mysql_fetch_assoc($gresult)) echo "<option value=".$data['goods_id'].">".stripslashes($data['name'])." (".$data['count'].")";
	echo "</td></tr>
	<th>Verlangen</th>
	<tr><td><input type=text size=5 name=wcount class=text> <span id=picwo><img src=".$gfx."/buttons/info1.gif></span> <select name=\"wo\" onChange=\"chgwopic();\"><option value=0>-------------";
	while($data = mysql_fetch_assoc($wresult)) echo "<option value=".$data['goods_id'].">".stripslashes($data['name']);
	echo "</td></tr>
	<tr><td>Angebotszahl: <input type=text size=2 class=text name=acount value=1></td></tr>	
	<tr><td style=\"text-align:center;\"><input type=submit value=\"Angebot erstellen\" class=button>"; 
	//echo " <input type=button value=Preischeck class=button onClick=\"pricecheck();\">";
	echo "</td></tr></table></form>
	<br /><div id=pc style=\"width: 150px;\"></div>";
}
// if ($v == "payout")
// {
	// echo "<script language=\"JavaScript\">
	// function get_window(elt,width)
	// {
		// return overlib('<div id='+elt+'></div>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER, STICKY, RELX, 200, RELY, 150, WIDTH, width);
	// }
	// function goodtransfer(good)
	// {
		// elt = 'transfer';
		// get_window(elt,500);
		// sendRequest('backend/trade/transfer.php?PHPSESSID=".session_id()."&good=' + good + '');
	// }
	// function ausgabe() {
		// var number=document.tr.rl.selectedIndex;
				// if ((number<0)||(number>=document.tr.rl.options.length)) {
			// document.tr.recipient.value=\"\";
		// } else {
			// var Text=document.tr.rl.options[number].value;
			// document.tr.recipient.value=Text;
		// }
	// }
	// </script>";
	// pageheader("/ <a href=?p=trade>Warenbörse</a> / <b>Waren auszahlen</b>");
	// if ($_GET['a'] == "tr" && check_int($_GET['good']) && check_int($_GET['count'])) meldung($t->goodtransfer($_GET['recipient'],$_GET['good'],$_GET['count']));
	// $result = $db->query("SELECT a.goods_id,a.count,b.name FROM stu_trade_goods as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.user_id=".$_SESSION["uid"]." AND a.offer_id=0 ORDER BY sort");
	// if (mysql_num_rows($result) == 0) meldung("Keine Waren vorhanden");
	// else
	// {
		// echo "<form action=main.php method=post><input type=hidden name=p value=trade><input type=hidden name=a value=payout>
		// <table bgcolor=#262323 cellspacing=1 cellpadding=1 width=300><tr><td colspan=4 class=m>Waren</td></tr>";
		// $i = 1;
		// while ($data=mysql_fetch_assoc($result))
		// {
			// if ($i == 1) echo "<tr>";
			// echo "<td width=90><img src=".$gfx."/goods/".$data['goods_id'].".gif title='".ftit($data['name'])."'> ".$data['count']."</td><td><input type=hidden name=good[] value=".$data['goods_id']."><input type=text size=3 name=count[] class=text> <a href=\"javascript:void(0);\" onClick=\"goodtransfer(".$data['goods_id'].")\" ".getonm('tr'.$data['goods_id'],'buttons/b_to')."><img src=".$gfx."/buttons/b_to1.gif border=0 title=\"Überweisung\" name=tr".$data['goods_id']."></a></td>";
			// if ($i == 2)
			// {
				// echo "</tr>";
				// $i = 1;
			// }
			// else $i++;
		// }
		// if ($i == 2) echo "<td colspan=\"2\">&nbsp;</td></tr>";
		// echo "</table><br><table bgcolor=#262323 cellspacing=1 cellpadding=1><tr><td colspan=4 class=m>Handelsposten wählen</td></tr>";
		// $sc = $db->query("SELECT a.id,a.rumps_id,a.name,a.sx,a.sy,a.systems_id,a.cx,a.cy,a.schilde_status,b.name as sname FROM stu_ships as a LEFT JOIN stu_systems as b USING(systems_id) WHERE a.is_hp='1' ORDER BY a.id");
		// while ($d=mysql_fetch_assoc($sc)) 
		// {
			// if ($d['rumps_id'] != 9020) echo "<tr><td><input type=radio name=hp value=".$d['id']."></td><td><img src=".$gfx."/ships/".$d['rumps_id'].".gif></td><td>".($d['schilde_status'] == 1 ?  "<font color=cyan>".stripslashes($d['name'])."</font>" : stripslashes($d['name']))."</td><td align=center>".($d['systems_id'] > 0 ? $d['sx']."|".$d['sy']."<br>(".$d['sname']."-System)" : $d['cx']."|".$d['cy'])."</td></tr>";
			// else echo "<tr><td><input type=radio name=hp value=".$d['id']."></td><td><img src=".$gfx."/ships/".$d['rumps_id'].".gif></td><td>".($d['schilde_status'] == 1 ?  "<font color=cyan>".stripslashes($d['name'])."</font>" : stripslashes($d['name']))."</td><td align=center>??|??</td></tr>";
		// }
		// echo "<tr><td colspan=4><input type=submit class=button value=Auszahlen></td></tr></table></form>";
	// }
// }
if ($v == "showoffer")
{
	if (!check_int($_GET['n']) || $_GET['n'] == 0) exit();
	$net = $db->query("SELECT * FROM stu_trade_networks WHERE network_id = ".$_GET['n']." LIMIT 1",4);
	if (!$net) exit();
	
	pageheader("/ <a href=?p=trade>Warenbörse</a> / <b><a href=?p=trade&s=net&n=".$_GET['n'].">".$net[name]."</a></b> / <b>Angebote einsehen</b>");
	
	if ($_GET['a'] == "do" && is_numeric($_GET['id']) && $_GET['id'] > 0) $result = $t->deloffer($_GET['n'],$_GET['id']);
	if ($_GET['a'] == "cho" && check_int($_GET['id']) && check_int($_GET['ocn'])) $result = $t->change_offer_count($_GET['n'],$_GET['ocn'],$_GET['id']);
	if (is_string($result)) meldung($result);
	$result = $t->getownofferlist($_GET['n']);
	if (mysql_num_rows($result) == 0) meldung("Keine Angebote vorhanden");
	else
	{
		echo "<table class=tcal>
		<tr><td></td><td class=m>Anzahl</td><td class=m>biete</td><td class=m>verlange</td></tr>";
		$lm = 0;
		while($data=mysql_fetch_assoc($result))
		{
			if ($ld != date("d.m.Y",$data['date_t'])) echo "<tr><td colspan=5 class=m>".date("d.m.Y",$data['date_t'])."</td></tr>";
			echo "<form action=\"main.php\" method=\"get\"><input type=\"hidden\" name=\"p\" value=\"trade\"><input type=\"hidden\" name=\"s\" value=\"so\"><input type=\"hidden\" name=\"n\" value=\"".$_GET['n']."\"><input type=\"hidden\" name=\"a\" value=\"cho\"><input type=\"hidden\" name=\"id\" value=\"".$data['offer_id']."\">
			<tr>
				<td valign=top align=center>".($data['date_t']+10800 > time() ? "<img src=".$gfx."/buttons/time.gif title=\"Angebot kann frühestens am ".date("d.m.Y H:i",($data['date_t']+10800))." Uhr gelöscht werden\">" : "<a href=?p=trade&s=so&a=do&n=".$_GET['n']."&id=".$data['offer_id']." onmouseover=cp('to".$i."','buttons/x2') onmouseout=cp('to".$i."','buttons/x1')><img src=".$gfx."/buttons/x1.gif border=0 title=\"Angebot ".$data['offer_id']." löschen\" name='to".$i."'></a>")."</td>
				<td valign=top><input type=\"text\" size=\"3\" name=\"ocn\" class=\"button\" value=\"".$data['ocount']."\"> <input type=\"submit\" class=\"button\" value=\"Aktualisieren\"></td>
				<td valign=top><img src=".$gfx."/goods/".$data['ggoods_id'].".gif  onMouseOver=\"showGood(this,'".$data['ggoods_id']."',true);\" onMouseOut=\"hideInfo();\"> ".$data['gcount']."</td>
				<td valign=top><img src=".$gfx."/goods/".$data['wgoods_id'].".gif  onMouseOver=\"showGood(this,'".$data['wgoods_id']."',true);\" onMouseOut=\"hideInfo();\"> ".$data['wcount']."</td>
			</tr></form>";
			$ld = date("d.m.Y",$data['date_t']);
			$i++;
		}
	}
	echo "</table>";
}
if ($v == "showprices")
{
	echo "<script language=\"Javascript\">
	function showgraph(good)
	{
		document.getElementById('img'+good).innerHTML = \"<img src=gfx/prices/\"+good+\".jpg>\";
	}
	function hidegraph(good)
	{
		document.getElementById('img'+good).innerHTML = \"\";
	}
	</script>";
	pageheader("/ <a href=?p=trade>Warenbörse</a> / <b>Wechselkurse</b>");
	$t->getpricedate();
	$result = $t->getcurrentprices();
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1>
	<tr>
		<td>Preise vom ".date("d.m.Y H:i",$t->pricedate)."</td>
	</tr>
	</table><br />";
	while($data = mysql_fetch_assoc($result))
	{
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=710>
		<th><img src=".$gfx."/goods/".$data['goods_id'].".gif title=\"".ftit($data['name'])."\"> ".stripslashes($data['name'])."</th>
		<tr>
			<td>Verkauf pro 1 Dilithium: ".$data['price']." - Ankauf pro 1 Dilithium: ".$data['price2']." - <a href=\"javascript:void(0);\" onClick=\"showgraph(".$data['goods_id'].")\">Graph anzeigen</a></td>
		</tr>
		<tr>
			<td><div id=\"img".$data['goods_id']."\" onClick=\"hidegraph(".$data['goods_id'].");\"></div></td>
		</tr>
		</table><br />";
	}
}
?>