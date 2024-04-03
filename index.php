<?php
if ($_SERVER["HTTPS"] != "on") {
	header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
	exit();
}
header("Content-Type: text/html; charset=utf-8");
ini_set(default_charset, "");


session_start();
include_once("inc/func.inc.php");
include_once("inc/config.inc.php");
include_once("class/db.class.php");
$db = new db;

include_once("class/main.class.php");
$main = new main;

if ($HTTP_VARS['REMOTE_ADDR'] == '77.22.70.196') exit;
if ($HTTP_VARS['REMOTE_ADDR'] == '77.177.165.238') exit;

if ($_GET['p'] != "reg") {
	unset($_SESSION['ud']);
	unset($_SESSION['step']);
}

switch ($_GET['p']) {
	case "m":
		$inc = "main/main.php";
		break;
	case "reg":
		$inc = "main/register.php";
		break;
	case "story":
		$inc = "main/story.php";
		break;
	case "act":
		$inc = "main/actcode.php";
		break;
	case "imp":
		$inc = "main/impressum.php";
		break;
	case "rules":
		$inc = "main/todo.php";
		break;
	case "chat":
		$inc = "main/chat.html";
		break;
	default:
		$inc = "main/main.php";
}










?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

<head>
    <title>Star Trek Universe</title>

    <link rel="STYLESHEET" type="text/css" href=gfx/css/main.css>
    <link rel="shortcut icon" href="favicon.ico" />
    <meta NAME="description" content="Star Trek Universe - Ein Star Trek Browsergame">
    <meta NAME="keywords" content="star trek, star, trek, spiel, browsergame, rollenspiel, rpg, online">
    <meta NAME="author" content="Daniel Jakob">
    <meta NAME="robots" content="index">
    <meta NAME="robots" content="follow">
    <meta NAME="publisher" content="Changeme">
    <meta NAME="language" content="de">
    <meta NAME="Copyright" content="Changeme">
    <meta NAME="Page-Topic" content="star trek, star, trek, rollenspiel, online, rpg, spiel, browsergame">
    <style>
    </style>
    <link rel="alternate" type="application/atom+xml" title="Atom-Datei"
        href="http://www.stuniverse.de/static/kn.xml" />


</head>

<body style='background: url(gfx/stars.jpg) repeat fixed top left #000000;'>

    <script LANGUAGE='JavaScript' type='text/javascript'>
    function cp(objekt, datei) {
        document.images[objekt].src = 'gfx/' + datei + '.gif';
    }

    function mov(e, c) {
        e.classList.remove(c);
        e.classList.add('menuColorActive');
    }

    function mot(e, c) {
        e.classList.add(c);
        e.classList.remove('menuColorActive');
    }
    </script>
    <?php




	$gfx = "/gfx";


	function getrtype($t, $s)
	{
		switch ($t . $s) {
			case "05":
				return "Frachter";
			case "06":
				return "Großraumfrachter";
			case "07":
				return "Transporter";
			case "11":
				return "Jäger";
			case "21":
				return "Eskortschiff";
			case "29":
				return "Bird of Prey";
			case "24":
				return "Erkundungsschiff";
			case "31":
				return "Zerstörer";
			case "39":
				return "Artillerieschiff";
			case "41":
				return "Kreuzer";
			case "44":
				return "Forschungsschiff";
			case "52":
				return "Warbird";
			case "54":
				return "Forschungsschiff";

			default:
				return "Unbekannt";
		}
	}

	$days  = date_create('now')->diff(date_create('0000-00-00'))->format('%a');


	$shipOfToday = array();
	array_push($shipOfToday, array('id' => 2101, 'rc' => 1, 'tp' => 2, 'sp' => 1, 'nm' => 'Saber'));
	array_push($shipOfToday, array('id' => 2102, 'rc' => 2, 'tp' => 2, 'sp' => 1, 'nm' => 'Ocala'));
	array_push($shipOfToday, array('id' => 2103, 'rc' => 3, 'tp' => 2, 'sp' => 9, 'nm' => 'B\'Rel'));
	array_push($shipOfToday, array('id' => 2104, 'rc' => 1, 'tp' => 2, 'sp' => 1, 'nm' => 'Miranda'));
	array_push($shipOfToday, array('id' => 2105, 'rc' => 2, 'tp' => 2, 'sp' => 1, 'nm' => 'Shrike'));

	array_push($shipOfToday, array('id' => 2201, 'rc' => 1, 'tp' => 2, 'sp' => 1, 'nm' => 'Defiant'));
	array_push($shipOfToday, array('id' => 2202, 'rc' => 2, 'tp' => 2, 'sp' => 1, 'nm' => 'Rhien'));
	array_push($shipOfToday, array('id' => 2203, 'rc' => 3, 'tp' => 2, 'sp' => 9, 'nm' => 'K\'vort\'cha'));

	array_push($shipOfToday, array('id' => 3401, 'rc' => 1, 'tp' => 2, 'sp' => 4, 'nm' => 'Nova'));
	array_push($shipOfToday, array('id' => 3402, 'rc' => 2, 'tp' => 2, 'sp' => 4, 'nm' => 'Talon'));
	array_push($shipOfToday, array('id' => 3403, 'rc' => 3, 'tp' => 2, 'sp' => 4, 'nm' => 'Haj'));

	array_push($shipOfToday, array('id' => 4101, 'rc' => 1, 'tp' => 3, 'sp' => 1, 'nm' => 'Norway'));
	array_push($shipOfToday, array('id' => 4102, 'rc' => 2, 'tp' => 3, 'sp' => 1, 'nm' => 'Eagle'));
	array_push($shipOfToday, array('id' => 4103, 'rc' => 3, 'tp' => 3, 'sp' => 1, 'nm' => 'K\'t\'inga'));

	array_push($shipOfToday, array('id' => 4901, 'rc' => 1, 'tp' => 3, 'sp' => 9, 'nm' => 'Steamrunner'));
	array_push($shipOfToday, array('id' => 4902, 'rc' => 2, 'tp' => 3, 'sp' => 9, 'nm' => 'Hawk'));
	array_push($shipOfToday, array('id' => 4903, 'rc' => 3, 'tp' => 3, 'sp' => 9, 'nm' => 'Koloth'));

	array_push($shipOfToday, array('id' => 5101, 'rc' => 1, 'tp' => 4, 'sp' => 1, 'nm' => 'Akira'));
	array_push($shipOfToday, array('id' => 5102, 'rc' => 2, 'tp' => 4, 'sp' => 1, 'nm' => 'Raptor'));
	array_push($shipOfToday, array('id' => 5103, 'rc' => 3, 'tp' => 4, 'sp' => 1, 'nm' => 'Vor\'cha'));
	array_push($shipOfToday, array('id' => 5104, 'rc' => 1, 'tp' => 4, 'sp' => 1, 'nm' => 'Excelsior'));
	array_push($shipOfToday, array('id' => 5105, 'rc' => 2, 'tp' => 4, 'sp' => 1, 'nm' => 'Shadow'));

	array_push($shipOfToday, array('id' => 5201, 'rc' => 1, 'tp' => 4, 'sp' => 1, 'nm' => 'Prometheus'));
	array_push($shipOfToday, array('id' => 5202, 'rc' => 2, 'tp' => 4, 'sp' => 1, 'nm' => 'Mogai'));
	array_push($shipOfToday, array('id' => 5203, 'rc' => 3, 'tp' => 4, 'sp' => 1, 'nm' => 'Fek\'lhr'));

	array_push($shipOfToday, array('id' => 5401, 'rc' => 1, 'tp' => 4, 'sp' => 4, 'nm' => 'Intrepd'));
	array_push($shipOfToday, array('id' => 5402, 'rc' => 2, 'tp' => 4, 'sp' => 4, 'nm' => 'Ar\'kif'));
	array_push($shipOfToday, array('id' => 5403, 'rc' => 3, 'tp' => 4, 'sp' => 4, 'nm' => 'Tor\'Kaht'));


	$selectedShip = $shipOfToday[$days % count($shipOfToday)];
	//$selectedShip = $shipOfToday[rand(0, count($shipOfToday))];

	$shipname = $selectedShip['nm'];
	if ($selectedShip['rc'] == 1) {
		$shipname = "<span style=\"color: #197DCB; font-weight: bold;\">" . $shipname . "-Klasse</span>";
		$shiprace = "Föderation";
	}
	if ($selectedShip['rc'] == 2) {
		$shipname = "<span style=\"color: #107D08; font-weight: bold;\">" . $shipname . "-Klasse</span>";
		$shiprace = "Romulaner";
	}
	if ($selectedShip['rc'] == 3) {
		$shipname = "<span style=\"color: #D01412; font-weight: bold;\">" . $shipname . "-Klasse</span>";
		$shiprace = "Klingonen";
	}

	$todaysShip = "
	<div style=\"width: 100%;\">
		<div style=\"text-align: center; width: 100%;\">
			<img src=gfx/ships/" . $selectedShip['id'] . ".gif border=0 style=\"padding: 8px;\">
			<br>" . $shipname . "
		</div>
		<div style=\"padding-left:8px; padding-right: 8px; margin-top: 8px; text-align: left; width: 100%; display: inline-block;\">
			<div style=\"width: 45%; text-align: left; display: inline-block;\">Rasse: </div>
			<div style=\"width: 45%; text-align: right; display: inline-block;\">" . $shiprace . "</div>
		</div>
		<div style=\"padding-left:8px; padding-right: 8px; margin-top: 8px; margin-bottom: 8px; text-align: left; width: 100%; display: inline-block;\">
			<div style=\"width: 45%; text-align: left; display: inline-block;\">Typ: </div>
			<div style=\"width: 45%; text-align: right; display: inline-block;\">" . getrtype($selectedShip['tp'], $selectedShip['sp']) . "</div>
		</div>	
	</div>";




	$statusname = "<div style=\"padding:8px;text-align:center;height:14px;\"><b>Online</b></div>";
	$statuscolor = "#66ff66";
	if ($main->state == 'Tick') {
		$statusname = "<div style=\"padding:8px;text-align:center;height:14px;\"><b>Tick</b></div>";
		$statuscolor = "#ffff66";
	}
	if ($main->state == 'Wartung') {
		$statusname = "<div style=\"padding:8px;text-align:center;height:14px;\"><b>Wartungsmodus</b></div>";
		$statuscolor = "#ff6666";
	}



	function nav($id, $text, $pic, $a, $fg = 0, $bg = 0)
	{
		return "<li class=\"menuColor1 mainmenu\" style=\"width:175px;height:20px;margin-left:5px;margin-right:5px;float:left;vertical-align:middle;padding:2px;\" onmouseover=\"mov(this,'menuColor1');cp('" . $id . "','buttons/hover/w/" . $pic . "');\" onmouseout=\"mot(this,'menuColor1');cp('" . $id . "','buttons/inactive/n/" . $pic . "');\"><a " . $a . " style=\"width:100%;height:100%;\"><img src=gfx/buttons/inactive/n/" . $pic . ".gif border=0 name=" . $id . "> " . $text . "</a></li>";
	}


	echo "<center><table width=1200 class=tablelayout style=\"background:none;\">";

	echo "<tr>";
	echo "<td class=tablelayout colspan=3 style=\"text-align:center;background:none;\"><img src=main/gfx/banner.png border=0 title=\"Star Trek Universe\"></td>";
	echo "</tr>";

	$nav .= "<div id=\"navi\" style=\"height:30px;\"><ul>";
	$nav .= nav("mnews", "News", "maindesk", "href='index.php'");
	$nav .= nav("mstor", "Story", "time", "href='?p=story'");
	$nav .= nav("mregi", "Registrieren", "yes", "href='?p=reg'");

	$nav .= nav("mform", "Forum", "list", "href='http://forum.stuniverse.de' target='_blank'");
	$nav .= nav("mimpr", "Impressum", "mail", "href='?p=imp'");
	$nav .= "</ul></div>";




	echo "<tr>";
	echo "<td colspan=2 class=tablelayout width=1000 style=\"background:none;\">" . fixedPanel(1, "Navigation", "mnav", $gfx . "/buttons/icon/warp.gif", $nav) . "</td>";
	echo "<td class=tablelayout width=200 style=\"background:none;\">" . coloredSimplePanel($statuscolor, "Spielstatus", "mstatus", $gfx . "/buttons/icon/info.gif", $statusname) . "</td>";
	echo "</tr>";



	$leftstyle = "padding-left:0px;padding-top:4px;padding-bottom:4px;padding-right:8px;margin:0px;vertical-align:top;";
	$rightstyle = "padding-left:0px;padding-top:4px;padding-bottom:8px;padding-right:0px;margin:0px;vertical-align:top;background:none;";


	$login = "<form action=\"main.php\" method=\"post\" name=\"f\" style=\"margin-bottom:0px;\"><table width=100% height=100%>";
	$login .= "<tr><td width=50% style=\"text-align:right;padding-right:8px;\">Username</td><td style=\"width:50%;text-align:center;\"><input type=\"text\" size=\"16\" class=\"text\" name=\"login\" id=\"txbox\"></td></tr>";
	$login .= "<tr><td width=50% style=\"text-align:right;padding-right:8px;\">Passwort</td><td style=\"width:50%;text-align:center;\"><input type=\"password\" size=\"16\" class=\"text\" name=\"pass\" id=\"txbox\"></td></tr>";
	$login .= "<tr><td colspan=2 style=\"text-align:center;\"><input type=submit value=Login class=button style=\"width:80px;\"></td></tr></table></form>";


	$indev = "<div style=\"padding:4px;min-height:200px;\"><b><font color=\"#55ff55\">Zur Zeit in Arbeit:</font></b><br>- Überarbeitung Style & Aussehen
<br>
<br><b><font color=\"#ffff55\">Danach:</font></b>
<br>- Sektoreroberung
<br>
<br><b><font color=\"#ff5555\">Später:</font></b>
<br>- Änderungen an Rumpfklassen und Schiffsmodulen
<br>- Kolonieverteidigung und Beamschutz</div>";

	$links = "<div style=\"padding:4px;\"><a href=\"http://www.usox.org\"><img src=\"gfx/usox.ico\" align=\"absmiddle\" />usoX Bierblog</a></div>";

	$copyright = "<div style=\"padding:4px;\">Star Trek is a registered trademark of Paramount Pictures.<br /><br />This site is strictly non-profit. No copyright infringement is intended.<br /></div>";

	echo "<tr>";
	echo "<td class=tablelayout rowspan=2 width=900 style=\"padding-top:16px;padding-right:4px;vertical-align:top;background:none;\">";
	include_once($inc);
	echo "</td>";
	echo "<td colspan=2 class=tablelayout width=300 style=\"padding-top:16px;vertical-align:top;background:none;\">";
	echo "<table style=\"border:none;border-spacing:0px;border-collapse:separate;background:none;\" width=100%>";
	echo "<tr><td style=\"" . $rightstyle . "\">" . fixedPanel(1, "Login", "mlogin", $gfx . "/buttons/icon/key.gif", $login) . "</td></tr>";

	echo "</table>";
	echo "</td>";
	echo "</tr>";

	echo "<tr>";
	echo "<td colspan=2 class=tablelayout width=300 style=\"padding-top:0px;vertical-align:bottom;background:none;\">";
	echo "<table style=\"border:none;border-spacing:0px;border-collapse:separate;background:none;\" width=100%>";
	echo "<tr><td style=\"" . $rightstyle . "\">" . fixedPanel(1, "Schiff des Tages", "mlks", $gfx . "/buttons/icon/ship.gif", $todaysShip) . "</td></tr>";
	echo "<tr><td style=\"" . $rightstyle . "\">" . fixedPanel(1, "Befreundete Seiten", "mlks", $gfx . "/buttons/icon/super.gif", $links) . "</td></tr>";
	echo "<tr><td style=\"" . $rightstyle . "\">" . fixedPanel(1, "Copyright", "mcrt", $gfx . "/buttons/icon/r1.gif", $copyright) . "</td></tr>";
	echo "</table>";
	echo "</td>";
	echo "</tr>";

	echo "</table></center>";




	?>
</body>

</html>