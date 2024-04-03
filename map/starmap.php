<?php
##########################################
#       starmap.php                      #
# STU Sternenkartenscript                #
# Verwaltung der Sternenkarte und        #
# System-Karten                          #
# 26.04.05 - wolverine                   #
##########################################

@session_start();

// Konfigurationsdatei und Funktionen laden
include_once("config.inc.php");
include_once("func.inc.php");

// Einbinden und laden der Klassen
include_once("db.class.php");
$db = new db;
include_once("starclass.php");
$map = new starmap;

// Seitenüberprüfung
switch($_GET["p"])
{
	default:
		$v = "main";
		if ($_GET["a"] == "unb") $map->setunb($_GET["id"]);
		if ($_GET[system_set] == 1)
		{
			$_GET[id] = $map->newsystem();
			$v = "showsystem";
			$map->loadsystem();
			$_SESSION[sys] = $_GET[id];
			break;
		}
	case "main":
		$v = "main";
		if ($_GET["a"] == "unb") $map->setunb($_GET["id"]);
		if ($_GET[system_set] == 1)
		{
			$_GET[id] = $map->newsystem();
			$v = "showsystem";
			$map->loadsystem();
			$_SESSION[sys] = $_GET[id];
			break;
		}
		break;
	case "ns":
		$v = "newsystem";
		break;
	case "ss":
		$v = "showsystem";
		if (check_int($id)) $map->loadsystem();
		if ($_GET[nf]) $map->setnewfield("sys");
		if (check_int($id)) $map->loadsystem();
		if ($map->mf == 0) exit;
		$_SESSION[sys] = $_GET[id];
		break;
	case "esf":
		if (check_int($_GET["id"])) $map->loadsystem();
		$v = "editsysfield";
		if (check_int($_GET["sx"]) && check_int($_GET["sy"])) $map->loadfield("sys");
		if ($map->fd == 0) exit;
		if (check_int($_GET["id"])) $map->loadsystem();
		$map->loadpossiblefields();
		break;
	case "ssf":
		if ($_GET[nf]) $map->setnewfield("sys");
		$v = "setsystemfield";
		break;
}

// HTML-Header
 echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
	<title>STU Starmap</title>
<script type=\"text/javascript\" src=\"gfx/overlib.js\"><!-- overLIB (c) Erik Bosrup --></script>
</head>
<link rel=\"STYLESHEET\" type=\"text/css\" href=gfx/css/6.css>
<body>";

// Hauptbereich
if ($v == "main")
{
	// Variablen initialisieren
	$bt = 20;
	$i = 0;
	$j = 1;
	$k = 1;
	
	// Seitenkopf ausgeben
	pageheader("<b>Sternenkarte</b>");
	
	if ($_GET[block] == 1)
	{
		$db->query("UPDATE stu_systems SET blocked='1' WHERE systems_id=".$_SESSION[sys]);
		meldung("Gepeichert.");
	}
	if ($_GET[ready] == 1)
	{
		$db->query("UPDATE stu_systems SET ready='1' WHERE systems_id=".$_SESSION[sys]);
		meldung("Fertig!");
	}
	// Stats holen
	$map->getcolstats();
	while ($data=mysql_fetch_assoc($map->stl))
	{
		echo "<img src=gfx/map/".$data[type].".gif title=\"".$data[name]."\"> ".$data[cc]."&nbsp;&nbsp;";
		$i++;
		if ($i%6 == 0) echo "<br/>";
	}
	echo "<br/><br/>";
	$map->loadsystems();
	echo "<b>Liste bereits erstellter Systeme (".$db->query("SELECT COUNT(systems_id) FROM stu_systems WHERE ready='1'",1)." fertig)</b><br>";
	// Systeme anzeigen
	while($data=mysql_fetch_assoc($map->sl))
	{
		$i++;
		echo "<img src=gfx/map/".$data[type].".gif> <a href=?p=ss&id=".$data[systems_id].">".($data[ready] == 1 ? "<font color=Green>" : "")."System ".stripslashes($data[name])." von ".stripslashes($data[autor])."".($data[ready] == 1 ? "</font>" : "")."</a>  --  <a href=?a=unb&id=".$data[systems_id].">Bäh!</a><br>";
	}
	echo "<br><br><b>System erstellen</b><br>";
	
	$i = 0;
	$map->loadpossiblesystems();
	// Neue System erstellen
	while($data=mysql_fetch_assoc($map->sf))
	{
		$i++;
		echo "<a href=?p=ns&nf=".$data[type]."><img src=gfx/map/".$data[type].".gif border=0 title=\"".$data[name]."\"></a>&nbsp;&nbsp;";
		if ($i%6 == 0) echo "<br>";
	}

	echo "</table>";
}

// Systemansicht
if ($v == "showsystem")
{
	// Variablen initialisieren
	$data = array();
	$xt = 1;
	
	echo "<script language=\"Javascript\">
	function openfiw(sx,sy,id)
	{
		ele = 'infid';
		sendRequest('starmap.php?id='+id+'&p=esf&sx='+sx+'&sy='+sy+'&sid=".$PHPSESSID."');
		return overlib('<div id=infid></div>', BGCOLOR, '#FFFFFF', FGCOLOR, '#FFFFFF', ABOVE, HEIGHT, 150, CELLPAD, 0, 0, 0, 0, BORDER, 0, STICKY, CENTER, CLOSECLICK);
	}
	function setnewfield(sx,sy,id,type)
	{
		ele = sx+'-'+sy;
		sendRequest('starmap.php?id='+id+'&p=ssf&sx='+sx+'&sy='+sy+'&nf='+type+'&sid=".$PHPSESSID."');
		cClick();
	}
	function createRequestObject()
	{
		var ro;
		var browser = navigator.appName;
		if(browser == \"Microsoft Internet Explorer\"){
			ro = new ActiveXObject(\"Microsoft.XMLHTTP\");
		}else{
			ro = new XMLHttpRequest();
		}
	return ro;
	}
	var http = createRequestObject();
	function sendRequest(action)
	{
		http.open('get', action);
		http.onreadystatechange = handleResponse;
		http.send(null);
	}
	function handleResponse()
	{
		if(http.readyState == 4)
		{
			var response = http.responseText;
			if(response.length > 0){
				document.getElementById(ele).innerHTML = response;
			}
	}
	}
	</script>";
	
	// Seitenkopf ausgeben
	pageheader("<a href=?p=main>Sternenkarte</a> / <b>".$map->sd[name]."-System</b>");
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr>";
	
	// Schleife für Kartenfelder
	while($data=mysql_fetch_assoc($map->mf))
	{
		if ($xt != $data[sy])
		{
			echo "</tr><tr>";
			$xt = $data[sy];
		}
		echo "<td><a href=\"javascript:void(0);\" onClick=\"openfiw(".$data[sx].",".$data[sy].",".$_GET[id].");\" id=\"".$data[sx]."-".$data[sy]."\"><img src=gfx/map/".$data[type].".gif border=0 title=\"".$data[name]."\"></a></td>";
	}
	echo "</tr></table><br>
	<form action=starmap.php method=get><input type=hidden name=block value=1><input type=submit value=Sperren class=button></form>&nbsp;Für T: <form action=starmap.php method=get><input type=hidden name=ready value=1><input type=submit value=Fertig! class=button></form>";
}

// System-Feld editieren
if ($v == "editsysfield")
{
	echo "<table class=Tcal style=\"border: 1px groove #4c4c4c;\"><tr><td>";
	// Seitenkopf ausgeben
	pageheader("<b>Feld ".$_GET[sx]."|".$_GET[sy]." editieren - <a href=\"javascript:void(0);\" onClick=\"cClick();\">Schließen</a></b>");
	
	// Aktuelles Feld anzeigen
	echo "<b>Aktuelles Feld</b><br/>
	<img src=gfx/map/".$map->fd[type].".gif title=\"".$map->fd[name]."\"><br/>
	<b>Neues Feld</b><br/>";
	
	// Auswahl möglicher Felder generieren
	while($data=mysql_fetch_assoc($map->mr))
	{
		echo "<a href=\"javascript:void(0);\" onClick=\"setnewfield(".$_GET[sx].",".$_GET[sy].",".$_GET[id].",".$data[type].");\"><img src=gfx/map/".$data[type].".gif border=0 title=\"".$data[name]."\"></a>&nbsp;";
		$i++;
		if ($i%10 == 0) echo "<br/>";
	}
	echo "</td></tr></table>";
}

// Neues System erstellen
if ($v == "newsystem")
{
	// Seitenkopf ausgeben
	pageheader("<a href=?p=main>Sternenkarte</a> / <b>System erstellen</b>");
	
	// Aktuelles Feld anzeigen
	echo "<form method=get action=starmap.php>
	<input type=hidden name=system_set value=1>
	<input type=hidden name=p value=main>
	<input type=hidden name=a value=ef>
	<input type=hidden name=nf value=".$_GET[nf].">
	<b>System erstellen</b><br/>
	Nickname <select name=nn><option value=tbone>T-Bone<option value=gen>Gen<option value=Wolverine>Wolverine<option value=shini>Shinigami</select><br/>
	Name <input type=text size=10 name=sn class=text><br/>
	Größe <input type=test size=2 name=nss class=text><br/>
	<input type=submit value=erstellen class=button></form>";
}
if ($v == "setsystemfield")
{
	echo "<img src=gfx/map/".$_GET[nf].".gif border=0>";
}
echo "</body></html>";
?>
