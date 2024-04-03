<?php
if (!is_object($db)) exit;
include_once("class/game.class.php");
$game = new game;
switch($_GET[s])
{
	default:
		$v = "main";
	case "ma":
		$v = "main";
		break;
	case "sl":
		$v = "slist";
		break;
	case "gbl":
		$v = "gblist";
		break;
	case "wis":
		$v = "wirtstats";
		break;
	case "shi":
		$v = "shipstats";
		break;
	case "wo":
		$v = "wichtigeorte";
		break;
	case "kn":
		$v = "knstats";
		break;
	case "ka":
		$v = "kfelder";
		break;
	case "sta":
		$v = "stats";
		break;
	case "fl":
		$v = "traffic";
		break;
	case "po":
		$v = "popucol";
		break;
	case "sp":
		$v = "popuuser";
		break;
	case "lat":
		$v = "latuser";
		break;
	case "fo":
		$v = "research";
		break;
	case "alc":
		$v = "allycount";
		break;
	case "als":
		$v = "allyships";
		break;
	case "alw":
		$v = "allywirt";
		break;
	case "alp":
		$v = "allypopu";
		break;
	case "sr":
		$v = "shiprump";
		break;
	case "pl":
		$v = "planetclasses";
		break;
	case "wl":
		$v = "goodlist";
		break;
	case "lak":
		$v = "allywars";
		break;
	case "swa":
		$v = "showwars";
		break;
	case "gbg":
		$v = "gbuildings";
		break;
	case "mods":
		$v = "modslist";
		break;
	case "gsh":
		$v = "gships";
		break;
	case "goh":
		$v = "goodhist";
		break;
	case "trh":
		$v = "tradehist";
		break;
	case "uaw":
		$v = "userawards";
		break;
	case "tboneistsuper":
		$v = "stationclasses";
		break;
}

if ($v == "main")
{
	pageheader("/ <b>Datenbanken</b>");
	echo "<script type=\"text/javascript\">
	function bildklick (Ereignis)
	{
		if (!Ereignis) Ereignis = window.event;
		browser = navigator.appName;
		if(browser == \"Microsoft Internet Explorer\" || browser == \"Opera\")
		{
			showEntries(Ereignis.offsetX,Ereignis.offsetY);
		}
		else
		{
			showEntries(Ereignis.layerX,Ereignis.layerY);
		}
	}
	function showEntries(x,y)
	{
		elt = 'history';
		sendRequest('backend/history.php?PHPSESSID=".session_id()."&x=' + x + '&y=' + y);	
		return overlib('<div id=\"history\"></div>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER, STICKY, RELX, 150, RELY, 50, WIDTH, 800, DRAGGABLE, ALTCUT);
	}
	function starte_ueberwachung ()
	{
		document.images[\"karte\"].onclick = bildklick;
	}
	</script>
	
	
	<style type=\"text/css\">
	#karte
	{
		position:relative;
	}
	</style>
	<table width=100%>
	<tr><td valign=top>
	<table bgcolor=#262323 cellspacing=1 cellpadding=1><th width=350>Statistiken</th>
	<tr>
		<td valign=top><a href=?p=db&s=wis>Die 50 stärksten Wirtschaftsmächte</a><br>
		<a href=?p=db&s=shi>Die 50 Siedler mit den größten Flotten</a><br>
		<a href=?p=db&s=kn>Die 50 bestbewertesten RPG-Schreiber</a><br>
		<a href=?p=db&s=po>Die 50 Kolonien mit der größten Population</a><br>
		<a href=?p=db&s=sp>Die 50 Siedler mit der größten Population</a><br>
		<a href=?p=db&s=fo>Die 50 besten Forscher</a><br>
		<a href=?p=db&s=fl>Die 50 Siedler mit dem größten Flugverkehr</a><br>
		<a href=?p=db&s=lat>Die 10 reichsten Siedler</a><br>
		<a href=?p=db&s=uaw>Die 10 Siedler mit den meisten Auszeichnungen</a><br><br>
		<a href=?p=db&s=alc>Die 20 größten Allianzen</a><br>
		<a href=?p=db&s=als>Die 20 Allianzen mit den größten Flotten</a><br>
		<a href=?p=db&s=alw>Die 20 wirtschaftsstärksten Allianzen</a><br>
		<a href=?p=db&s=alp>Die 20 Allianzen mit der größten Population</a><br>
		<a href=?p=db&s=lak>Die 20 längsten Kriege</a><br><br>
		<a href=?p=db&s=wl>Warenübersicht</a><br>
		<a href=?p=db&s=sta>Statistiken</a></td></tr></table>
		</td>
		<td valign=top>
		<table bgcolor=#262323 cellspacing=1 cellpadding=1>
		<th width=250>Informationen</th>
		<tr>
		<td valign=top><a href=?p=db&s=sl>Siedlerliste</a><br>
		<a href=?p=db&s=gbl>Gebäudeliste</a><br>
		<a href=?p=db&s=ka>Kartenfelder</a><br>
		<a href=?p=db&s=sr>Schiffsrümpfe</a><br>
		<a href=?p=db&s=mods>Modul- und Torpedowerte</a><br>
		<a href=?p=db&s=pl>Planetenklassen</a><br>
		<a href=?p=db&s=wo>Wichtige Orte</a><br><br>
		<a href=?p=db&s=gbg>Gebaute Gebäude</a><br>
		<a href=?p=db&s=gsh>Gebaute Schiffe</a><br>
		<a href=?p=db&s=goh>Warenstatistiken</a><br>
		<a href=?p=db&s=trh>Handelsstatistik</a></td></tr></table>
		</td>
		<td valign=top>
		<table bgcolor=#262323 cellspacing=1 cellpadding=1>
		<th width=250>Ereigniskarte (Beta)</th>
		<tr>
		<td valign=top><img src=\"gfx/graph/eventmap.png\" border=\"0\" width=\"482\" height=\"482\" id=\"karte\"></td></tr></table>
		</td>
	</tr>
	</table>";
}
if ($v == "slist")
{
	if (!check_int($_GET['pa'])) $pa = 1;
	else $pa = $_GET['pa'];
	if ($_GET['so'] == "Reset")
	{
		$ses = "";
		$_GET['ses'] = "";
	}
	if ($_GET['ses'])
	{
		if (check_int($_GET['ses']))$ses = $_GET['ses'];
		else
		{
			$ses = strip_tags(str_replace("\"","",str_replace("'","",$_GET['ses'])));
			if ($ses == "") unset($set);
		}
	}
	$game->loadslist($_GET['type'],$_GET['way'],$pa,$ses);
	// Seiten erzeugen
	$pg = "Seite: <a href=?p=db&s=sl&type=".$_GET['type']."&way=".$_GET['way']."&pa=1>|<</a> <a href=?p=db&s=sl&type=".$_GET['type']."&way=".$_GET['way']."&pa=".($pa == 1 ? 1 : $pa-1)."><</a>&nbsp;";
	for($i=1;$i<=ceil($game->sc/50);$i++)
	{
		$pa != $i ? $pg .= "<a href=?p=db&s=sl&type=".$_GET['type']."&way=".$_GET['way']."&pa=".$i."&ses=".$ses.">".$i."</a>" : $pg .= "<b>".$i."</b>";
		if ($i < ceil($game->sc/50)) $pg .= "&nbsp;|&nbsp;";
	}
	$pg .= "&nbsp;<a href=?p=db&s=sl&type=".$_GET["type"]."&way=".$_GET["way"]."&pa=".($pa == ceil($game->sc/50) ? 1 : $pa+1).">></a>&nbsp;<a href=?p=db&s=sl&type=".$_GET['type']."&way=".$_GET['way']."&pa=".(ceil($game->sc/50)).">>|</a> (".$game->sc." Siedler)";
	pageheader("/ <a href=?p=db>Datenbanken</a> / <b>Siedlerliste</b>");
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1>
	<form action=main.php method=get>
	<input type=hidden name=p value=db><input type=hidden name=s value=sl>
	<tr><td>Suche nach ID/Name <input type=text size=10 class=text name=ses value=".$ses."> <input type=submit value=Suche class=button> <input type=submit value=Reset class=button name=so></td></tr>
	</form></table><br>
	<table class=tcal cellpadding=1 cellspacing=1>
	<tr><td colspan=6>".$pg."</td></tr>
	<tr>
		<th><a href=?p=db&s=sl&type=1&way=2&pa=".$pa."&ses=".$ses."><img src=".$gfx."/buttons/pdown.gif title='Absteigend' border=0></a>&nbsp;ID&nbsp;<a href=?p=db&s=sl&type=1&way=1&pa=".$pa."&ses=".$ses."><img src=".$gfx."/buttons/pup.gif title='Aufsteigend' border=0></a></th>
		<th><a href=?p=db&s=sl&type=2&way=2&pa=".$pa."&ses=".$ses."><img src=".$gfx."/buttons/pdown.gif title='Absteigend' border=0></a>&nbsp;Name&nbsp;<a href=?p=db&s=sl&type=2&way=1&pa=".$pa."&ses=".$ses."><img src=".$gfx."/buttons/pup.gif title='Aufsteigend' border=0></a></th>
		<th><a href=?p=db&s=sl&type=3&way=2&pa=".$pa."&ses=".$ses."><img src=".$gfx."/buttons/pdown.gif title='Absteigend' border=0></a>&nbsp;Allianz&nbsp;<a href=?p=db&s=sl&type=3&way=1&pa=".$pa."&ses=".$ses."><img src=".$gfx."/buttons/pup.gif title='Aufsteigend' border=0></a></th>
		<th><a href=?p=db&s=sl&type=5&way=2&pa=".$pa."&ses=".$ses."><img src=".$gfx."/buttons/pdown.gif title='Absteigend' border=0></a>&nbsp;Lvl&nbsp;<a href=?p=db&s=sl&type=5&way=&pa=".$pa."&ses=".$ses."><img src=".$gfx."/buttons/pup.gif title='Aufsteigend' border=0></a></th>
		<td><a href=?p=db&s=sl&type=6&way=2&pa=".$pa."&ses=".$ses."><img src=".$gfx."/buttons/pdown.gif title='Absteigend' border=0></a>&nbsp;&nbsp;<a href=?p=db&s=sl&type=6&way=1&pa=".$pa."&ses=".$ses."><img src=".$gfx."/buttons/pup.gif title='Aufsteigend' border=0></a></td>
		<td></td>
	</tr>";
	while($data=mysql_fetch_assoc($game->result))
	{
		$j++;
		if ($j == 2)
		{
			$trc = " style=\"background-color: #171616\"";
			$j = 0;
		}
		else $trc = "";
		echo "<tr>
			<td".$trc.">".$data['id']."</td>
			<td".$trc.">".stripslashes($data['user'])."</td>
			<td".$trc."><a href=?p=ally&s=de&id=".$data['allys_id'].">".stripslashes($data['name'])."</a></td>
			<td".$trc.">".$data['level']."</td>
			<td".$trc."><img src=".$gfx."/rassen/".$data['race']."".($data[subrace] != 0 ? "_".$data[subrace]."s.gif" : "s.gif")." title='".addslashes(getracename($data['race'],$data['subrace']))."'></td>
			<td".$trc."><a href=\"javascript:void(0);\" onClick=\"opensi(".$data['id'].");\" ".getonm("id".$data['id'],"buttons/info")."><img src=".$gfx."/buttons/info1.gif name=id".$data['id']." border=0 title='Spielerprofil'></a></td>
		</tr>";
	}
	echo "<tr><td colspan=6>".$pg."</td></tr></table>";
}
if ($v == "gblist")
{
	pageheader("/ <a href=?p=db>Datenbanken</a> / <b>Gebäudeliste</b>");
	include_once("inc/lists/gbl.html");
}
if ($v == "wirtstats")
{
	pageheader("/ <a href=?p=db>Datenbanken</a> / <b>Die 50 stärksten Wirtschaftsmächte</b>");
	$result = $game->getwirtstats();
	while($ret=mysql_fetch_assoc($result))
	{
		if (!$ret['lwp']) continue;
		$arr[] = $ret;
	}
	function cmp ($a, $b) { return strnatcmp($b['lwp'],$a['lwp']); }
	@usort($arr, "cmp");
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=400>";
	foreach($arr as $key => $data)
	{
		$i++;
		$j++;
		if ($j == 2)
		{
			$trc = " style=\"background-color: #171616\"";
			$j = 0;
		}
		else $trc = "";
		echo "<tr><td".$trc.">".$i.".</td><td".$trc."><img src=".$gfx."/rassen/".$data['race']."s.gif> ".stripslashes($data['user'])."</td></tr>";
		if ($i == 50) break;
	}
	echo "</table>";
}
if ($v == "shipstats")
{
	pageheader("/ <a href=?p=db>Datenbanken</a> / <b>Die 50 Siedler mit den größten Flotten</b>");
	$result = $game->getshipstats();
	while($ret=mysql_fetch_assoc($result))
	{
		if (!$ret['c']) continue;
		$arr[] = $ret;
	}
	function cmp ($a, $b) { return strnatcmp($b['c'],$a['c']); }
	@usort($arr, "cmp");
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=700>";
	foreach($arr as $key => $data)
	{
		$i++;
		$j++;
		if ($j == 2)
		{
			$trc = " style=\"background-color: #171616\"";
			$j = 0;
		}
		else $trc = "";
		echo "<tr><td".$trc.">".$i.".</td><td".$trc."><img src=".$gfx."/rassen/".$data['race']."s.gif> ".stripslashes($data['user'])."</td><td".$trc.">".$data['c']."</td></tr>";
		if ($i == 50) break;
	}
	echo "</table>";
}
if ($v == "wichtigeorte")
{
	pageheader("/ <a href=?p=db>Datenbanken</a> / <b>Wichtige Orte</b>");
	$result = $game->gethps();
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=700>";
	while($data=mysql_fetch_assoc($result))
	{
		$i++;
		if ($i == 2)
		{
			$trc = " style=\"background-color: #171616\"";
			$i = 0;
		}
		echo "<tr><td".$trc."><img src=".$gfx."/ships/".$data[rumps_id].".gif title=\"".ftit($data['cname'])."\"></td><td".$trc.">".stripslashes($data['name'])."</td><td".$trc.">".$data[cx]."|".$data[cy]."</td></tr>";
		$trc = "";
	}
	echo "</table>";
}
if ($v == "knstats")
{
	pageheader("/ <a href=?p=db>Datenbanken</a> / <b>Die 50 bestbewertesten RPG-Schreiber</b>");
	$result = $db->query("SELECT a.id,a.user,a.race,COUNT(b.id) as knc FROM stu_user as a LEFT JOIN stu_kn as b ON b.user_id=a.id WHERE a.id>100 AND UNIX_TIMESTAMP(b.date)>".(time()-10368000)." AND b.votes>0 AND b.official='1' GROUP BY a.id");
	while($data=mysql_fetch_assoc($result)) $ud[$data['id']] = $data;
	$result = $db->query("SELECT user_id,rating,votes FROM stu_kn WHERE user_id>100 AND UNIX_TIMESTAMP(date)>".(time()-10368000)." AND votes>0 AND official='1'");
	while($data=mysql_fetch_assoc($result))
	{
		$vd[$data['user_id']]['votes'] += $data['votes'];
		$vd[$data['user_id']]['rats'] += $data['rating'];
	}
	foreach($ud as $key => $value)
	{
		$data[''.round($vd[$key]['rats']/$value['knc'],2).''][] = array("user" => $value['user'],"race" => $value['race'],"id" => $value['id'],"rat" => round($vd[$key]['rats']/$value['knc'],2),"knc" => $value['knc'],"votes" => $vd[$key]['votes']);
	}
	krsort($data);
	meldung("Diese Statistiken werden aus allen RPG-Posting der letzten 4 Monate (ausgenommen Beiträge die noch bewertet werden können) berechnet");
	echo "<table class=tcal><th></th><th>Siedler</th><th>Wertung (0-5)</th>";
	foreach($data  as $key => $value)
	{
		@usort($value, "cmp");
		foreach($value as $key2 => $value2)
		{
			if ($value2['knc'] < 5) continue;
			$i++;
			$j++;
			if ($j == 2)
			{
				$trc = " style=\"background-color: #171616\"";
				$j = 0;
			}
			else $trc = "";
			echo "<tr><td".$trc.">".$i.".</td><td".$trc."><img src=".$gfx."/rassen/".$value2['race']."s.gif> ".stripslashes($value2['user'])." (".$value2['id'].")</td><td".$trc.">".$key." (".$value2['votes']." Stimmen | ".$value2['knc']." Beiträge)</td></tr>";
			if ($i == 50)
			{
				$lvl1break = 1;
				break;
			}
		}
		if ($lvl1break == 1) break;
	}
	echo "</table>";
}
if ($v == "kfelder")
{
	pageheader("/ <a href=?p=db>Datenbanken</a> / <b>Kartenfelder</b>");
	$result = $game->getmapfieldtypes();
	echo "<table class=Tcal>
	<th width=30></th><th></th><th>Energiekosten</th><th>Schaden bei<br>Deflektorausfall</th><th>Allgemeiner<br>Einflugschaden*</th><th>Sensorausfall</th><th>Schildausfall</th><th>Tarnungsausfall</th>";
	while($data=mysql_fetch_assoc($result))
	{
		$j++;
		if ($j == 2)
		{
			$trc = " style=\"background-color: #171616\"";
			$j = 0;
		}
		else $trc = "";
		echo "<tr><td".$trc."><img src=gfx/map/".$data[type].".gif></td><td".$trc.">".$data[name]."</td><td".$trc." align=Center>".$data[ecost]."</td><td".$trc." align=Center>".$data[damage]."</td><td".$trc." align=Center>".$data[x_damage]."</td><td".$trc." align=Center>".($data[sensoroff] == 1 ? "X" : "")."</td><td".$trc." align=Center>".($data[shieldoff] == 1 ? "X" : "")."</td><td".$trc." align=Center>".($data[cloakoff] == 1 ? "X" : "")."</td></tr>";
	}
	echo "<tr><td colspan=8><b>*</b>) Bei den Systemen (Roter Zwerg, etc) gilt dieser Einflugschaden nur beim Einflug in das entsprechende Feld <u>innerhalb</u> des Systems</td></tr></table>";
}
if ($v == "stats")
{
	$data = $game->getgamestats();
	pageheader("/ <a href=?p=db>Datenbanken</a> / <b>Statistiken</b>");
	$file = file("/proc/uptime");
	$up = split(" ",$file[0]);
	$file = file("/proc/loadavg");
	$load = split(" ",$file[0]);
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1>
	<th>Mein Account</th>
	<tr><td>Schiffe: ".($data['od']['sp']+1).". Platz (".$data['od']['schiffe'].")<br>
	Davon bemannt: ".($data['od']['spb']+1).". Platz (".$data['od']['schiffeb'].")<br>
	Wirtschaft: ".($data['od']['wi']+1).". Platz (".$data['od']['wirtschaft'].")<br>
	Bevölkerung: ".($data['od']['po']+1).". Platz (".$data['od']['population'].")</td></tr>
	</table><br>
	<table><tr><td width=300 valign=top>
	<table class=tcal>
	<th>Server</th>
	<tr><td>Uptime: ".gen_time(round($up[0]))."</td></tr>
	<tr><td>Last: 1min ".$load[0]." 5min ".$load[1]." 15min ".$load[2]."</td></tr>
	<tr><td>System: Dual Quad-Core AMD Opteron 2376, 8 GB RAM</td></tr>
	</table><br>
	<table class=tcal>
	<th>Allianzen</th>
	<tr><td>|-Anzahl: ".$data['al']['ac']."<br>
	|-<a href=?p=db&s=swa>Kriege: ".$data['al']['wa']."</a></td></tr>
	</table><br />
	<table class=tcal>
	<th>Tribbles</th>
	<tr><td>|-Anzahl: ".$data['tr']['tc']."
	</table>
	</td>
	<td valign=top width=150>
	<table class=Tcal>
	<th>Spieler</th>
	<tr><td>|-Aktiv: ".$data['player']."<br>
	|-Online: ".$data['online']."<br>
	|-im Urlaub: ".$data['urlaub']."<br>
	|-Gesperrt: ".$data['sperre']."<br>
	|-Freischaltung: ".$data['schalt']."<br><br>
	Nach Rassen<br>
	|-Föderation: ".$data['fed']."<br>
	|-Romulaner: ".$data['rom']."<br>
	|-Klingonen: ".$data['kli']."<br>
	|-Cardassianer: ".$data['car']."<br>
	|-Ferengi: ".$data['fer']."<br>
	|-Gorn: ".$data['gor']."</td></tr>
	</table>
	</td>
	<td valign=top width=200>
	<table class=Tcal>
	<th>Kolonien</th>
	<tr><td>|-Kolonisiert: ".$data['kolos']."/".$data['kolos_max']."<br>
	|-Bewohner: ".$data['popu']."<br>
	|-Wirtschaft: ".$data['wirt']."<br>
	|-&Oslash;-Wirtschaft: ".$data['wavg']."<br>
	|-Veränderung: ".$data['wchg']."%</td></tr>
	</table>
	</td>
	<td valign=top width=200>
	<table class=Tcal>
	<th>Schiffe</th>
	<tr><td>|-Vorhanden: ".$data['ships']."<br>
	|-Bemannt: ".$data['aships']."<br>
	|-Wracks: ".$data['trums']."<br>
	|-Flugverkehr (24h): ".$data['flight']."<br>
	|-Ionenstürme: ".$data['ion']."<br>
	</table>
	</td></tr></table><br>
	<table bgcolor=#262323 cellspacing=1 cellpadding=1>
	<th colspan=3>Diagramme der letzten 10 Ticks</th>
	<tr><td><table>
	<tr><td>Entwicklung der Spielerzahl</td></tr>
	<tr><td><img src=gfx/graph/usertick.jpg title=\"User nach Ticks\"></td></tr>
	</table>
	</td>
	<td><table>
	<tr><td>Entwicklung der Zahl der Schiffe</td></tr>
	<tr><td><img src=gfx/graph/shiptick.jpg title=\"Schiffe nach Ticks\"></td></tr>
	</table>
	</td>
	<td><table>
	<tr><td>Entwicklung der Wirtschaft</td></tr>
	<tr><td><img src=gfx/graph/wirttick.jpg title=\"Wirtschaft nach Ticks\"></td></tr>
	</table>
	</td></tr>
	</table>";
}
if ($v == "traffic")
{
	pageheader("/ <a href=?p=db>Datenbanken</a> / <b>Die 50 Siedler mit dem größten Flugverkehr</b>");
	$result = $game->getusertraffic();
	while($data=mysql_fetch_assoc($result)) $rs[] = $data;
	function cmp ($a, $b) { return strnatcmp($b['shid'],$a['shid']); }
	@usort($rs, "cmp");
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=700><th></th><th>Siedler</th><th>Felder</th>";
	foreach($rs as $key => $value)
	{
		$i++;
		$j++;
		if ($j == 2)
		{
			$trc = " style=\"background-color: #171616\"";
			$j = 0;
		}
		else $trc = "";
		echo "<tr><td".$trc.">".$i.".</td><td".$trc."><img src=".$gfx."/rassen/".$value['race']."s.gif> ".stripslashes($value['user'])."</td><td".$trc.">".$value['shid']."</td></tr>";
		if ($i == 50) break;
	}
	echo "</table>";
}
if ($v == "popucol")
{
	pageheader("/ <a href=?p=db>Datenbanken</a> / <b>Die 50 Kolonien mit der größten Population</b>");
	$result = $game->getpopucolonys();
	while($ret=mysql_fetch_assoc($result))
	{
		if (!$ret['bev']) continue;
		$arr[] = $ret;
	}
	function cmp ($a, $b) { return strnatcmp($b['bev'],$a['bev']); }
	@usort($arr, "cmp");
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><th></th><th>Kolonie</th><th>Bevölkerung (W/A)</th><th>Siedler</th>";
	foreach($arr as $key => $data)
	{
		$i++;
		$j++;
		if ($j == 2)
		{
			$trc = " style=\"background-color: #171616\"";
			$j = 0;
		}
		else $trc = "";
		echo "<tr><td".$trc.">".$i.".</td><td".$trc.">".stripslashes($data['name'])."</td><td".$trc.">".$data[bev]." (".$data[bev_work]."/".$data[bev_free].")</td><td".$trc."><img src=".$gfx."/rassen/".$data['race']."s.gif> ".stripslashes($data[user])."</td></tr>";
		if ($i == 50) break;
	}
	echo "</table>";
}
if ($v == "popuuser")
{
	pageheader("/ <a href=?p=db>Datenbanken</a> / <b>Die 50 Siedler mit der größten Population</b>");
	$result = $game->getpopuuser();
	while($ret=mysql_fetch_assoc($result))
	{
		if (!$ret['bev']) continue;
		$arr[] = $ret;
	}
	function cmp ($a, $b) { return strnatcmp($b['bev'],$a['bev']); }
	@usort($arr, "cmp");
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=700><th></th><th>Siedler</th><th>Bevölkerung (W/A)</th>";
	foreach($arr as $key => $data)
	{
		$i++;
		$j++;
		if ($j == 2)
		{
			$trc = " style=\"background-color: #171616\"";
			$j = 0;
		}
		else $trc = "";
		echo "<tr><td".$trc.">".$i.".</td><td".$trc."><img src=".$gfx."/rassen/".$data['race']."s.gif> ".stripslashes($data[user])."</td><td".$trc.">".$data[bev]." (".$data[bev_work]."/".$data[bev_free].")</td></tr>";
		if ($i == 50) break;
	}
	echo "</table>";
}
if ($v == "latuser")
{
	pageheader("/ <a href=?p=db>Datenbanken</a> / <b>Die 10 reichsten Siedler</b>");
	$result = $game->getrichestsettlers();
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=700><th></th><th>Siedler</th><th>Latinum</th>";
	while($data = mysql_fetch_assoc($result))
	{
		$i++;
		$j++;
		if ($j == 2)
		{
			$trc = " style=\"background-color: #171616\"";
			$j = 0;
		}
		else $trc = "";
		echo "<tr><td".$trc.">".$i.".</td><td".$trc."><img src=".$gfx."/rassen/".$data['race']."s.gif> ".stripslashes($data['user'])."</td><td".$trc.">".$data['latinum']."</td></tr>";
	}
	echo "</table>";
}
if ($v == "research")
{
	pageheader("/ <a href=?p=db>Datenbanken</a> / <b>Die 50 besten Forscher</b>");
	$result = $game->getresearchuser();
	while($ret=mysql_fetch_assoc($result))
	{
		if (!$ret['researched']) continue;
		$arr[] = $ret;
	}
	function cmp ($a, $b) { return strnatcmp($b['researched'],$a['researched']); }
	@usort($arr, "cmp");
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=700><th></th><th>Siedler</th><th>FP</th>";
	foreach($arr as $key => $data)
	{
		$i++;
		$j++;
		if ($j == 2)
		{
			$trc = " style=\"background-color: #171616\"";
			$j = 0;
		}
		else $trc = "";
		echo "<tr><td".$trc.">".$i.".</td><td".$trc."><img src=".$gfx."/rassen/".$data['race']."s.gif> ".stripslashes($data['user'])."</td><td".$trc.">".$data['researched']."</td></tr>";
		if ($i == 50) break;
	}
	echo "</table>";
}
if ($v == "allycount")
{
	pageheader("/ <a href=?p=db>Datenbanken</a> / <b>Die 20 größten Allianzen</b>");
	$result = $game->getallymembers();
	while($ret=mysql_fetch_assoc($result))
	{
		if (!$ret['c']) continue;
		$arr[] = $ret;
	}
	function cmp ($a, $b) { return strnatcmp($b['c'],$a['c']); }
	@usort($arr, "cmp");
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=700><th></th><th>Allianz</th><th>Mitglieder</th>";
	foreach($arr as $key => $data)
	{
		$i++;
		$j++;
		if ($j == 2)
		{
			$trc = " style=\"background-color: #171616\"";
			$j = 0;
		}
		else $trc = "";
		echo "<tr><td".$trc.">".$i.".</td><td".$trc.">".stripslashes($data['name'])."</td><td".$trc.">".$data['c']."</td></tr>";
		if ($i == 20) break;
	}
	echo "</table>";
}
if ($v == "allyships")
{
	pageheader("/ <a href=?p=db>Datenbanken</a> / <b>Die 20 Allianzen mit den größten Flotten</b>");
	$result = $game->getallyships();
	while($ret=mysql_fetch_assoc($result))
	{
		if (!$ret['co']) continue;
		$arr[] = $ret;
	}
	function cmp ($a, $b) { return strnatcmp($b['co'],$a['co']); }
	@usort($arr, "cmp");
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=700><th></th><th>Allianz</th><th>Schiffe</th>";
	foreach($arr as $key => $data)
	{
		$i++;
		$j++;
		if ($j == 2)
		{
			$trc = " style=\"background-color: #171616\"";
			$j = 0;
		}
		else $trc = "";
		echo "<tr><td".$trc.">".$i.".</td><td".$trc.">".stripslashes($data['name'])."</td><td".$trc.">".$data['co']."</td></tr>";
		if ($i == 20) break;
	}
	echo "</table>";
}
if ($v == "allywirt")
{
	pageheader("/ <a href=?p=db>Datenbanken</a> / <b>Die 20 wirtschaftsstärksten Allianzen</b>");
	$result = $game->getallywirt();
	while($ret=mysql_fetch_assoc($result))
	{
		if (!$ret['wi']) continue;
		$arr[] = $ret;
	}
	function cmp ($a, $b) { return strnatcmp($b["wi"],$a["wi"]); }
	@usort($arr, "cmp");
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=700><th></th><th>Allianz</th>";
	foreach($arr as $key => $data)
	{
		$i++;
		$j++;
		if ($j == 2)
		{
			$trc = " style=\"background-color: #171616\"";
			$j = 0;
		}
		else $trc = "";
		echo "<tr><td".$trc.">".$i.".</td><td".$trc.">".stripslashes($data['name'])."</td></tr>";
		if ($i == 20) break;
	}
	echo "</table>";
}
if ($v == "allypopu")
{
	pageheader("/ <a href=?p=db>Datenbanken</a> / <b>Die 20 Allianzen mit der größten Population</b>");
	$result = $game->getallypopu();
	while($ret=mysql_fetch_assoc($result))
	{
		if (!$ret['bev']) continue;
		$arr[] = $ret;
	}
	function cmp ($a, $b) { return strnatcmp($b["bev"],$a["bev"]); }
	@usort($arr, "cmp");
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=700><th></th><th>Allianz</th><th>Bevölkerung (W/A)</th>";
	foreach($arr as $key => $data)
	{
		$i++;
		$j++;
		if ($j == 2)
		{
			$trc = " style=\"background-color: #171616\"";
			$j = 0;
		}
		else $trc = "";
		echo "<tr><td".$trc.">".$i.".</td><td".$trc.">".stripslashes($data['name'])."</td><td".$trc.">".$data['bev']." (".$data['bev_work']."/".$data['bev_free'].")</td></tr>";
		if ($i == 20) break;
	}
	echo "</table>";
}
if ($v == "userawards")
{
	pageheader("/ <a href=?p=db>Datenbanken</a> / <b>Die 10 Siedler mit den meisten Auszeichnungen</b>");
	$result = $game->getuserawards();
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=750><th></th><th>Siedler</th><th>Auszeichnungen</th>";
	while($data = mysql_fetch_assoc($result))
	{
		$i++;
		$j++;
		if ($j == 2)
		{
			$trc = " style=\"background-color: #171616\"";
			$j = 0;
		}
		else $trc = "";
		echo "<tr><td".$trc.">".$i.".</td><td".$trc.">".stripslashes($data['user'])."</td><td".$trc.">";
		$res = $game->getawardsperuser($data['user_id']);
		while($dat = mysql_fetch_assoc($res))  echo "<img src=gfx/awards/".$dat['award_id'].".gif title=\"".getawardname($dat['award_id'])."\">&nbsp;";
		echo "</td></tr>";
		if ($i == 10) break;
	}
	echo "</table>";
}
if ($v == "shiprump")
{
	echo "<script language=\"Javascript\">
	var elt;
	function get_window(elt,width)
	{
		return overlib('<div id=rinfo></div>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER, STICKY, EXCLUSIVE, DRAGGABLE, ALTCUT, RELX, 400, RELY, 70, WIDTH, width);
	}
	function getrinfo(rid,fg)
	{	
		elt = fg;
		get_window(elt,422);
		sendRequest('backend/rinfo.php?PHPSESSID=".session_id()."&rid=' + rid);
	}
	function loadinfo(rid,fg)
	{	
		elt = fg;
		sendRequest('backend/rdetail.php?PHPSESSID=".session_id()."&rid=' + rid);
	}
	function setpos(off)
	{
		elt = 'rl';
		sendRequest('backend/rlist.php?PHPSESSID=".session_id()."&off=' + off);
	}
	</script>";
	pageheader("/ <a href=?p=db>Datenbanken</a> / <b>Schiffsrümpfe</b>");
	$result = $game->getrumplist();
	echo "<table class=tcal>";
	$i=1;
	while($data=mysql_fetch_assoc($result))
	{
		$j++;
		if ($j == 2)
		{
			$trc = " style=\"background-color: #171616\"";
			$j = 0;
		}
		else $trc = "";
		if ($data['sort'] != $sort)
		{
			echo "<th></th><th>Name</th><th><img src=".$gfx."/buttons/modul_1.gif title=\"Hülle\"></th><th><img src=".$gfx."/buttons/modul_2.gif title=\"Schilde\"></th><th><img src=".$gfx."/buttons/modul_3.gif title=\"Computer\"></th><th><img src=".$gfx."/buttons/modul_7.gif title=\"Impulsantrieb\"></th><th><img src=".$gfx."/buttons/modul_5.gif title=\"Warpkern\"></th><th><img src=".$gfx."/buttons/modul_11.gif title=\"Warpantrieb\"></th><th><img src=".$gfx."/buttons/modul_10.gif title=\"Torpedorampe\"></th><th><img src=".$gfx."/buttons/modul_6.gif title=\"Strahlenwaffe\"></th><th><img src=".$gfx."/buttons/modul_8.gif title=\"EPS-Leitungen\"></th><th><img src=".$gfx."/buttons/modul_4.gif title=\"Sensoren\"></th><th><img src=".$gfx."/buttons/modul_9.gif title=\"Spezial\"></th>";
			$sort = $data['sort'];
		}
		echo "<tr>
			<td".$trc."><a href=\"javascript:void(0)\" onClick=\"getrinfo(".$data['rumps_id'].",'rinfo');\"><img src=".$gfx."/ships/".$data['rumps_id'].".gif border=0 title=\"Infos zum Schiffsrumpf\"></a></td>
			<td".$trc."><a href=\"http://wiki.stuniverse.de/index.php/".stripslashes($data['name'])."\" target=_blank>".$data['name']."</a></td>
			<td".$trc.">".$data['m1c']." (<font color=Green>".$data['m1minlvl']."</font>|<font color=Lime>".$data['m1maxlvl']."</font>)</td>
			<td".$trc.">".$data['m2c']." (<font color=Green>".$data['m2minlvl']."</font>|<font color=Lime>".$data['m2maxlvl']."</font>)</td>
			<td".$trc.">".$data['m3c']." (<font color=Green>".$data['m3minlvl']."</font>|<font color=Lime>".$data['m3maxlvl']."</font>)</td>
			<td".$trc.">".$data['m7c']." (<font color=Green>".$data['m7minlvl']."</font>|<font color=Lime>".$data['m7maxlvl']."</font>)</td>
			<td".$trc.">".$data['m5c']." (<font color=Green>".$data['m5minlvl']."</font>|<font color=Lime>".$data['m5maxlvl']."</font>)</td>
			<td".$trc.">".$data['m11c']." (<font color=Green>".$data['m11minlvl']."</font>|<font color=Lime>".$data['m11maxlvl']."</font>)</td>
			<td".$trc.">".$data['m10c']." (<font color=Green>".$data['m10minlvl']."</font>|<font color=Lime>".$data['m10maxlvl']."</font>)</td>
			<td".$trc.">".$data['m6c']." (<font color=Green>".$data['m6minlvl']."</font>|<font color=Lime>".$data['m6maxlvl']."</font>)</td>
			<td".$trc.">".$data['m8c']." (<font color=Green>".$data['m8minlvl']."</font>|<font color=Lime>".$data['m8maxlvl']."</font>)</td>
			<td".$trc.">".$data['m4c']." (<font color=Green>".$data['m4minlvl']."</font>|<font color=Lime>".$data['m4maxlvl']."</font>)</td>
			<td".$trc.">".$data['m9c']." (<font color=Green>".$data['m9minlvl']."</font>|<font color=Lime>".$data['m9maxlvl']."</font>)</td>
		</tr>";
		$i++;
	}
	echo "</table>";
}
if ($v == "planetclasses")
{
	pageheader("/ <a href=?p=db>Datenbanken</a> / <b>Planetenklassen</b>");
	$result = $db->query("SELECT * FROM stu_colonies_classes WHERE colonies_classes_id<30 ORDER BY level,is_moon ASC,colonies_classes_id ASC");
	while($data=mysql_fetch_assoc($result))
	{
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=400>";
		if ($ll != $data[level])
		{
			echo "<th colspan=2>Ab Level ".$data[level]."</th>";
			$ll = $data[level];
		}
		echo "<tr><td width=30><img src=".$gfx."/planets/".$data[colonies_classes_id].".gif onmouseover=\"overlib('<table width=450><th colspan=2>Vorschau ".$data[name]."</th><tr><td><img src=".$gfx."/planets/preview/".$data[colonies_classes_id].".gif></td><td valign=top><b>Vorkommen:</b><br>Iridium: ".$data[mine20]."<br>Kelbonit: ".$data[mine21]."<br>Nitrium: ".$data[mine22]."<br>Magnesit: ".$data[mine23]."<br>Talgonit: ".$data[mine24]."<br>Galazit: ".$data[mine25]."<br>Dilithium: ".$data[mine26]."";
		if ($data[is_moon] != 1) echo "<br><br>Geothermie: ".$data[geos]."";
		echo "<br><br>Atmosphäre: ".$data[atmosphere]."</td></tr></table>',FIXX,200,FIXY,250);\" onmouseout=\"nd();\"></td><td>".$data[name]."</td></tr>";
		echo "</table>";
	}
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=400><tr><td>Weitere Informationen zu den Planetenklassen gibt es in der <a href=http://wiki.stuniverse.de/index.php/Planetenklassen_%26_Kartenfelder target=_blank>Wiki</a>.</td></tr></table>";
}
if ($v == "goodlist")
{
	pageheader("/ <a href=?p=db>Datenbanken</a> / <b>Warenübersicht</b>");
	echo "<script language=\"Javascript\">
	{
		function retrieve_goodlist(m)
		{
			elt = 'gl';
			sendRequest('backend/generategoodlist.php?PHPSESSID=".session_id()."&m='+m);
		}
		function get_window(elt,width)
		{
			return overlib('<div id=gdb></div>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER, STICKY, RELX, 200, RELY, 110, WIDTH, width);
		}
		function retrieve_good_distribution(good)
		{
			elt = 'gdb';
			get_window(elt,500);
			sendRequest('backend/generategooddist.php?PHPSESSID=".session_id()."&good='+good);
		}
	}
	</script>
	<style>
	#txa
	{
		position:absolute;
		top:0px;
		left:0px;
		width:494px;
		height: 26px;
		text-align: left;
		vertical-align: top;
		font-size: 7pt;
		font-weight: bold;
		padding-left: 2px;
		padding-top: 2px;
		padding-bottom: 2px;
		background: #262323;
		border: 1px solid #404760;
	}
	#txf
	{
		top:26px;
		left:10px;
		width: 80px;
		border: 1px solid #262323;
		padding: 2px 2px 2px 2px;
	}
	#txf a
	{
		display:block;
		text-decoration: none;
	}
	#txf a:hover
	{
		background: #262323;
	}
	</style>";
	echo "<table><tr><td id=\"txf\">Bitte wählen</td><td id=\"txf\" style=\"width: 90px; text-align: center;\"><a href=\"javascript:void(0);\" onClick=\"retrieve_goodlist(1);\">Waren</a></td><td id=\"txf\" style=\"width: 90px; text-align: center;\"><a href=\"javascript:void(0);\" onClick=\"retrieve_goodlist(2);\">Module</a></td><td></tr></table>
	<div id=\"gl\"></div>";
}
if ($v == "allywars")
{
	pageheader("/ <a href=?p=db>Datenbanken</a> / <b>Die 20 längsten Allianzkriege</b>");
	$result = $game->getallywars();
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=700><th>Parteien</th><th>seit</th>";
	while($data=mysql_fetch_assoc($result))
	{
		$i++;
		$j++;
		if ($j == 2)
		{
			$trc = " style=\"background-color: #171616\"";
			$j = 0;
		}
		else $trc = "";
		echo "<tr><td".$trc.">".stripslashes($data['name'])." vs. ".stripslashes($data['name2'])."</td><td".$trc.">".date("d.m.",$data['date_tsp']).setyear(date("Y",$data['date_tsp']))."</td></tr>";
		if ($i == 20) break;
	}
	echo "</table>";
}
if ($v == "showwars")
{
	pageheader("/ <a href=?p=db>Datenbanken</a> / <b>Alle Kriege</b>");
	$result = $game->getallywars();
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=700><th>Parteien</th><th>seit</th>";
	while($data=mysql_fetch_assoc($result))
	{
		$i++;
		$j++;
		if ($j == 2)
		{
			$trc = " style=\"background-color: #171616\"";
			$j = 0;
		}
		else $trc = "";
		echo "<tr><td".$trc.">".stripslashes($data['name'])." vs. ".stripslashes($data['name2'])."</td><td".$trc.">".date("d.m.",$data['date_tsp']).setyear(date("Y",$data['date_tsp']))."</td></tr>";
	}
	echo "</table>";
}
if ($v == "gbuildings")
{
	pageheader("/ <a href=?p=db>Datenbanken</a> / <b>Gebaute Gebäude</b>");
	$site = include("inc/lists/gbl_bui.php");
	$site = str_replace("gfx",$gfx,$site);
}
if ($v == "gships")
{
	pageheader("/ <a href=?p=db>Datenbanken</a> / <b>Gebaute Schiffe</b>");
	$site = include("inc/lists/shi_bui.php");
	$site = str_replace("gfx",$gfx,$site);
}
if ($v == "modslist")
{
	pageheader("/ <a href=?p=db>Datenbanken</a> / <b>Modul- und Torpedodatenbank</b>");
	echo "<style>table.tmodo {
	background-color: #262323;
	border-collapse: separate;
	border-spacing: 1px;
}
table.tsec {
	background-color: #000000;
	width: 100%;
	border-collapse: separate;
	border-spacing: 0px;
}
td.mml {
	background-color : #262323;
	color : #8897cf;
	font-size : 9pt;
	margin-left : 3px;
	margin-bottom : 3px;
	margin-right : 3px;
	margin-top : 3px;
	height : 20px;
}
</style><table width=840 class=tmodo ><tr>
		<td width=140><a href=?p=db&s=mods&m=1><img src=gfx/buttons/modul_1.gif border=0> Huelle</a></td>
		<td width=140><a href=?p=db&s=mods&m=2><img src=gfx/buttons/modul_2.gif border=0> Schilde</a></td>
		<td width=140><a href=?p=db&s=mods&m=3><img src=gfx/buttons/modul_3.gif border=0> Computer</a></td>
		<td width=140><a href=?p=db&s=mods&m=4><img src=gfx/buttons/modul_4.gif border=0> Sensoren</a></td>
		<td width=140><a href=?p=db&s=mods&m=5><img src=gfx/buttons/modul_5.gif border=0> Warpkern</a></td>
		<td width=140><a href=?p=db&s=mods&m=6><img src=gfx/buttons/modul_6.gif border=0> Waffen</a></td></tr><tr>
		<td width=140><a href=?p=db&s=mods&m=7><img src=gfx/buttons/modul_7.gif border=0> Impulsantrieb</a></td>
		<td width=140><a href=?p=db&s=mods&m=8><img src=gfx/buttons/modul_8.gif border=0> EPS</a></td>
		<td width=140><a href=?p=db&s=mods&m=10><img src=gfx/buttons/modul_10.gif border=0> Torpedorampen</a></td>
		<td width=140><a href=?p=db&s=mods&m=11><img src=gfx/buttons/modul_11.gif border=0> Warpantrieb</a></td>
		<td width=140><a href=?p=db&s=mods&m=9><img src=gfx/buttons/modul_9.gif border=0> Spezial</a></td>
		<td width=140><a href=?p=db&s=mods&m=12><img src=gfx/goods/81.gif border=0> Torpedos</a></td></tr></table><br><br>";
	$mod = $_GET[m];
	if ($mod != 0) include_once("inc/lists/modl".$mod.".html");
}
if ($v == "goodhist")
{
	echo "<style>
	td.li {
		vertical-align: bottom;
		height: 80px;
	}
	td.li2 {
		vertical-align: middle;
		text-align: center;
	}
	</style>";
	include_once("inc/lists/goods.php");
	pageheader("/ <a href=?p=db>Datenbanken</a> / <b>Warenstatistik</b>");
	$result = @file($global_path."/intern/data/goodhist.dat");
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=500>
	<th width=\"200\">Name</th>
	<th>Graph</th>
	<th>Entwicklung</th>";
	foreach($result as $key => $value)
	{
		$j++;
		if ($j == 2)
		{
			$trc = " style=\"background-color: #171616\"";
			$j = 0;
		}
		else $trc = "";
		$arr = explode("|",$value);
		$i = 1;
		while($i<=5)
		{
			$arr[$i] = intval($arr[$i]);
			$i++;
		}
		$tmp_arr = array($arr[1],$arr[2],$arr[3],$arr[4],$arr[5]);
		$max = max($tmp_arr);
		$min = min($tmp_arr);
		
		$rm = $max-$min;
		
		// Es muss der Unterschied zum Maximum un minimum gefunden werden!
		
		echo "<tr><td".$trc."><img src=".$gfx."/goods/".$arr[0].".gif title=\"".ftit(getgoodname($arr[0]))."\"> ".getgoodname($arr[0])."</td>
		<td".$trc." class=\"li\">
		<img src=gfx/wurst.gif width=4 height=".($rm == 0 ? 70 : (round(((100/$rm)*($arr[1]-$min))/2))+20).">
		<img src=gfx/wurst.gif width=4 height=".($rm == 0 ? 70 : (round(((100/$rm)*($arr[2]-$min))/2))+20).">
		<img src=gfx/wurst.gif width=4 height=".($rm == 0 ? 70 : (round(((100/$rm)*($arr[3]-$min))/2))+20).">
		<img src=gfx/wurst.gif width=4 height=".($rm == 0 ? 70 : (round(((100/$rm)*($arr[4]-$min))/2))+20).">
		<img src=gfx/wurst.gif width=4 height=".($rm == 0 ? 70 : (round(((100/$rm)*($arr[5]-$min))/2))+20).">
		</td>
		<td class=\"li2\">";
		if ($arr[4] < $arr[5])
		{
			$pc = "<img src=".$gfx."/buttons/b_up1.gif>";
			$vl = $arr[5]-$arr[4];
		}
		if ($arr[4] == $arr[5])
		{
			$pc = "<img src=".$gfx."/buttons/b_to1.gif>";
			$vl = 0;
		}
		if ($arr[4] > $arr[5])
		{
			$pc = "<img src=".$gfx."/buttons/b_down1.gif>";
			$vl = ($arr[4]-$arr[5])*-1;
		}
		echo $pc."<br><br>".$arr[5]." (".($vl < 0 ? $vl : "+".$vl).")</td>
		</tr>\n";
	}
	echo "</table>";
}
if ($v == "tradehist")
{
	echo "<style>
	td.li {
		vertical-align: bottom;
		height: 80px;
	}
	td.li2 {
		vertical-align: middle;
		text-align: center;
	}
	</style>";
	include_once("inc/lists/goods.php");
	pageheader("/ <a href=?p=db>Datenbanken</a> / <b>Handelsstatistik</b>");
	$result = @file($global_path."/intern/data/tradehist.dat");
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=500>
	<th width=\"200\">Name</th>
	<th>Graph</th>
	<th>Entwicklung</th>";
	foreach($result as $key => $value)
	{
		$j++;
		if ($j == 2)
		{
			$trc = " style=\"background-color: #171616\"";
			$j = 0;
		}
		else $trc = "";
		$arr = explode("|",$value);
		$i = 1;
		while($i<=5)
		{
			$arr[$i] = intval($arr[$i]);
			$i++;
		}
		$tmp_arr = array($arr[1],$arr[2],$arr[3],$arr[4],$arr[5]);
		$max = max($tmp_arr);
		$min = min($tmp_arr);
		
		$rm = $max-$min;
		
		// Es muss der Unterschied zum Maximum un minimum gefunden werden!
		
		echo "<tr><td".$trc."><img src=".$gfx."/goods/".$arr[0].".gif title=\"".ftit(getgoodname($arr[0]))."\"> ".getgoodname($arr[0])."</td>
		<td".$trc." class=\"li\">
		<img src=gfx/wurst.gif width=4 height=".($rm == 0 ? 70 : (round(((100/$rm)*($arr[1]-$min))/2))+20).">
		<img src=gfx/wurst.gif width=4 height=".($rm == 0 ? 70 : (round(((100/$rm)*($arr[2]-$min))/2))+20).">
		<img src=gfx/wurst.gif width=4 height=".($rm == 0 ? 70 : (round(((100/$rm)*($arr[3]-$min))/2))+20).">
		<img src=gfx/wurst.gif width=4 height=".($rm == 0 ? 70 : (round(((100/$rm)*($arr[4]-$min))/2))+20).">
		<img src=gfx/wurst.gif width=4 height=".($rm == 0 ? 70 : (round(((100/$rm)*($arr[5]-$min))/2))+20).">
		</td>
		<td class=\"li2\">";
		if ($arr[4] < $arr[5])
		{
			$pc = "<img src=".$gfx."/buttons/b_up1.gif>";
			$vl = $arr[5]-$arr[4];
		}
		if ($arr[4] == $arr[5])
		{
			$pc = "<img src=".$gfx."/buttons/b_to1.gif>";
			$vl = 0;
		}
		if ($arr[4] > $arr[5])
		{
			$pc = "<img src=".$gfx."/buttons/b_down1.gif>";
			$vl = ($arr[4]-$arr[5])*-1;
		}
		echo $pc."<br><br>".$arr[5]." (".($vl < 0 ? $vl : "+".$vl).")</td>
		</tr>\n";
	}
	echo "</table>";
}
if ($v == "stationclasses")
{
	pageheader("/ <a href=?p=db>Datenbanken</a> / <b>Stationsklassen</b>");
	$result = $db->query("SELECT * FROM stu_stations_classes ORDER BY stations_classes_id ASC");
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=800>";
	while($data=mysql_fetch_assoc($result))
	{
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=800>";
		echo "<tr><td><img src=".$gfx."/stations/".$data[stations_classes_id].".gif title='".$data[name]."'></td>";

		//echo "<br><br>Atmosphäre: ".$data[atmosphere]."</td></tr></table>',FIXX,200,FIXY,250);\" onmouseout=\"nd();\"></td><td>".$data[name]."</td></tr>";
		echo "</tr>";
	}
	echo "</table>";
}
?>
