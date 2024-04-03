<?php
##########################################
#       starmap.php                      #
# STU Sternenkartenscript                #
# Verwaltung der Sternenkarte und        #
# System-Karten                          #
# 26.04.05 - wolverine                   #
##########################################

// Konfigurationsdatei und Funktionen laden
include_once("../inc/config.inc.php");
include_once("../inc/func.inc.php");

// Einbinden und laden der Klassen
include_once("../class/db.class.php");
$db = new db;
include_once("starclass.php");
$map = new starmap;

// Seitenüberprüfung
switch($_GET["p"])
{
	default:
		$v = "main";
		$map->getblocks();
	case "main":
		$v = "main";
		$map->getblocks();
		break;
	case "sb":
		$v = "showblock";
		if ($_GET[nf] && $map->fieldissystem($_GET[nf]))
		{
			if (!$_GET[system_set])
			{
				$v = "newsystem";
				break;
			}
			else $map->newsystem();
		}
		if ($_GET[a] == "ef") $map->setnewfield("block");
		if ($_GET[a] == "sfa") $map->setfaction($_GET["fac"],$_GET["bo"]);
		if ($_GET[a] == "ssy") $map->setsystem($_GET[sys],$_GET[cx],$_GET[cy]);
		if (check_int($_GET["x"]) && check_int($_GET["y"])) $map->loadmap();
		if ($map->mf == 0) exit;
		break;
	case "ss":
		$v = "showsystem";
		if ($_GET[a] == "esf")
		{
			$ct = $map->fieldiscolony($_GET[nf]);
			if ($ct > 0) $map->addcolony($ct);
			else $map->setnewfield("sys");
		}
		if (check_int($_GET["id"])) $map->loadsystem();
		if ($map->mf == 0) exit;
		break;
	case "esf":
		$v = "editsysfield";
		if (check_int($_GET["sx"]) && check_int($_GET["sy"])) $map->loadfield("sys");
		if ($map->fd == 0) exit;
		if (check_int($_GET["id"])) $map->loadsystem();
		$map->loadpossiblefields();
		break;
	case "ef":
		$v = "editfield";
		if (check_int($_GET["cx"]) && check_int($_GET["cy"])) $map->loadfield("block");
		if ($map->fd == 0) exit;
		$map->loadpossiblefields();
		break;
	case "ov":
		$map->loadoverallmap();
		$v = "overall";
		break;
}

// HTML-Header
 echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
	<title>STU Starmap</title>
</head>
<link rel=\"STYLESHEET\" type=\"text/css\" href=../gfx/css/6.css>
<body>";

// Hauptbereich mit Karten-Blöcken
if ($v == "main")
{
	// Fand eine Aktion statt?
	if ($_POST[a] == "ss" && check_int($_POST[gx]) && check_int($_POST[gy]))
	{
		$tx = 1;
		$ty = 1;
		while($ty<=$_POST[gy])
		{
			while($tx<=$_POST[gx])
			{
				if ($db->query("SELECT cx FROM stu_map WHERE cx=".$tx." AND cy=".$ty,4) == 0) $db->query("INSERT INTO stu_map (cx,cy,type) VALUES ('".$tx."','".$ty."','1')");
				$tx++;
			}
			$tx = 1;
			$ty++;
		}
	}
	
	// Variablen initialisieren
	$bt = 20;
	$i = 1;
	$j = 1;
	$k = 1;
	
	// Seitenkopf ausgeben
	echo "<table class=tcal><tr><th>/ Sternenkarte</th></tr></table><br>";
	echo "<table><tr>";
	
	// Schleife für die Blöcke
	while($j<=ceil(ceil($map->blocks[cy]/$mapfields[max_x])/20))
	{
		while($i<=ceil(ceil($map->blocks[cx]/$mapfields[max_y])/20))
		{
			echo "<td><a href=?p=sb&x=".$i."&y=".$j.">Block ".$k."</a></td>";
			$i++;
			$k++;
		}
		$i = 1;
		$j++;
		echo "</tr><tr>";
	}
	echo "</table><br/><br/>
	<a href=?p=ov>Gesamtansicht</a><br/><br/><b>Größe ändern:</b><br/>
	<form action=starmap.php method=post>
	<input type=hidden name=a value=ss>
	<input type=text size=3 name=gx class=text> | <input type=text size=3 name=gy class=text> <input type=submit value=ändern class=button></form>";
}

// Blockansicht
if ($v == "showblock")
{
	// Variablen initialisieren
	$data = array();
	$xt = 1;
	
	// Seitenkopf ausgeben
	echo "<table class=tcal><tr><th><a href=?p=main>Sternenkarte</a> / <b>Blockansicht</b></th></tr></table><br>";
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr>";
	
	// Schleife für Kartenfelder
	while($data=mysql_fetch_assoc($map->mf))
	{
		if ($xt != $data[cy])
		{
			echo "</tr><tr>";
			$xt = $data[cy];
		}
		$border = " bordercolor=#000000 style='border: 1px solid #000000'";
		if ($data[faction_id] > 0) $border = " bordercolor=".$data[darker_color]." style='border: 1px solid ".$data[darker_color]."'";
		if ($data[faction_id] > 0 && $data[is_border] == 1) $border = " bordercolor=".$data[color]." style='border: 1px solid ".$data[color]."'";
		echo "<td".$border.">".($data[systems_id] > 0 ? "<a href=?p=ss&x=".$_GET["x"]."&y=".$_GET["y"]."&id=".$data[systems_id].">" : "<a href=?p=ef&cx=".$data[cx]."&cy=".$data[cy]."&x=".$_GET[x]."&y=".$_GET[y].">")."<img src=../gfx/map/".($data[id] > 0 ? "hap" : $data[type]).".gif border=0 title=\"".$data[name]."\"></a></td>";
	}
	echo "</tr></table>";
}

// Systemansicht
if ($v == "showsystem")
{
	// Variablen initialisieren
	$data = array();
	$xt = 1;
	
	// Seitenkopf ausgeben
	echo "<table class=tcal><tr><th><a href=?p=main>Sternenkarte</a> / <a href=?p=sb&x=".$_GET["x"]."&y=".$_GET["y"].">Blockansicht</a> / <b>".$map->sd[name]."-System</b></th></tr></table><br>";
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr>";
	
	// Schleife für Kartenfelder
	while($data=mysql_fetch_assoc($map->mf))
	{
		if ($xt != $data[sy])
		{
			echo "</tr><tr>";
			$xt = $data[sy];
		}
		echo "<td><a href=?p=esf&sx=".$data[sx]."&sy=".$data[sy]."&id=".$_GET[id]."&x=".$_GET[x]."&y=".$_GET[y]."><img src=../gfx/map/".$data[type].".gif border=0 title=\"".$data[name]."\"></a></td>";
	}
	echo "</tr></table>";
}

// System-Feld editieren
if ($v == "editsysfield")
{
	// Seitenkopf ausgeben
	echo "<table class=tcal><tr><th><a href=?p=main>Sternenkarte</a> / <a href=?p=sb&x=".$_GET["x"]."&y=".$_GET["y"].">Blockansicht</a> / <a href=?p=ss&x=".$_GET["x"]."&y=".$_GET["y"]."&id=".$map->sd[systems_id].">".$map->sd[name]."-System</a> / <b>Feld editieren</b></th></tr></table><br>";
	
	// Aktuelles Feld anzeigen
	echo "<b>Aktuelles Feld</b><br/>
	<img src=../gfx/map/".$map->fd[type].".gif title=\"".$map->fd[name]."\"><br/>
	<b>Neues Feld</b><br/>";
	
	// Auswahl möglicher Felder generieren
	while($data=mysql_fetch_assoc($map->mr))
	{
		echo "<a href=?p=ss&x=".$_GET["x"]."&y=".$_GET["y"]."&sx=".$_GET[sx]."&sy=".$_GET[sy]."&id=".$_GET[id]."&a=esf&nf=".$data[type]."><img src=../gfx/map/".$data[type].".gif border=0 title=\"".$data[name]."\"></a>&nbsp;";
		$i++;
		if ($i%3 == 0) echo "<br/>";
	}
}

// Karten-Feld editieren
if ($v == "editfield")
{
	// Seitenkopf ausgeben
	echo "<table class=tcal><tr><th><a href=?p=main>Sternenkarte</a> / <a href=?p=sb&x=".$_GET["x"]."&y=".$_GET["y"].">Blockansicht</a> / <b>Feld editieren</b></th></tr></table><br>";
	
	// Aktuelles Feld anzeigen
	echo "<b>Aktuelles Feld</b><br/>
	<img src=../gfx/map/".$map->fd[type].".gif title=\"".$map->fd[name]."\"><br/>
	<b>Neues Feld</b><br/>
	<table><tr><td>";
	
	// Auswahl möglicher Felder generieren
	while($data=mysql_fetch_assoc($map->mr))
	{
		echo "<a href=?p=sb&x=".$_GET["x"]."&y=".$_GET["y"]."&cx=".$_GET[cx]."&cy=".$_GET[cy]."&a=ef&nf=".$data[type]."><img src=../gfx/map/".$data[type].".gif border=0 title=\"".$data[name]."\"></a>&nbsp;";
		$i++;
		if ($i%3 == 0) echo "<br/>";
	}
	echo "</td><td valign=top><b>Rassengebiete</b><br><a href=?p=sb&x=".$_GET["x"]."&y=".$_GET["y"]."&cx=".$_GET[cx]."&cy=".$_GET[cy]."&a=sfa&fac=0>Keins</a><br>";
	while($data=mysql_fetch_assoc($map->fr)) echo "<a href=?p=sb&x=".$_GET["x"]."&y=".$_GET["y"]."&cx=".$_GET[cx]."&cy=".$_GET[cy]."&a=sfa&fac=".$data[faction_id].">".$data[name]."</a> - <a href=?p=sb&x=".$_GET["x"]."&y=".$_GET["y"]."&cx=".$_GET[cx]."&cy=".$_GET[cy]."&a=sfa&fac=".$data[faction_id]."&bo=1>".$data[name]." (Grenze)</a><br>";
	echo "<br><br><b>Fertige Systeme</b><br>";
	while($data=mysql_Fetch_assoc($map->fs))
	{
		$i++;
		echo "<a href=?p=sb&x=".$_GET["x"]."&y=".$_GET["y"]."&cx=".$_GET[cx]."&cy=".$_GET[cy]."&a=ssy&sys=".$data[systems_id]."><img src=../gfx/map/".$data[type].".gif border=0> ".$data[name]."</a>";
		if ($i%3==0) echo "<br>";
	}
	echo "</td></tr></table>";
}

// Neues System erstellen
if ($v == "newsystem")
{
	// Seitenkopf ausgeben
	echo "<table class=tcal><tr><th><a href=?p=main>Sternenkarte</a> / <a href=?p=sb&x=".$_GET["x"]."&y=".$_GET["y"].">Blockansicht</a> / <b>System erstellen</b></th></tr></table><br>";
	
	// Aktuelles Feld anzeigen
	echo "<form method=get action=starmap.php>
	<input type=hidden name=system_set value=1>
	<input type=hidden name=p value=sb>
	<input type=hidden name=a value=ef>
	<input type=hidden name=x value=".$_GET[x].">
	<input type=hidden name=y value=".$_GET[y].">
	<input type=hidden name=cx value=".$_GET[cx].">
	<input type=hidden name=cy value=".$_GET[cy].">
	<input type=hidden name=nf value=".$_GET[nf].">
	<b>System erstellen</b><br/>
	Name <input type=text size=10 name=sn class=text><br/>
	Größe <input type=test size=2 name=nss class=text><br/>
	<input type=submit value=erstellen class=button></form>";
}

// Gesamtansicht der Sternenkarte erstellen
if ($v == "overall")
{
	// Variablen initialisieren
	$data = array();
	$xt = 1;
	
	// Seitenkopf ausgeben
	echo "<table class=tcal><tr><th><a href=?p=main>Sternenkarte</a> / <b>Gesamtansicht</b></tr></table><br>";
	
	// Schleife für Kartenfelder
	while($data=mysql_fetch_assoc($map->mf))
	{
		if ($xt != $data[cy])
		{
			echo "<br>";
			$xt = $data[cy];
		}
		echo "<img src=../gfx/map/".($data[id] > 0 ? "hap" : $data[type]).".gif border=0 width=5 height=5>";
	}
	
	/*
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr>";
	
	// Schleife für Kartenfelder
	while($data=mysql_fetch_assoc($map->mf))
	{
		if ($xt != $data[cy])
		{
			echo "</tr><tr>";
			$xt = $data[cy];
		}
		echo "<td><img src=../gfx/map/".($data[id] > 0 ? "hap" : $data[type]).".gif border=0 width=5 height=5></td>";
	}
	echo "</tr></table>";
	*/
}
echo "</body></html>";
?>