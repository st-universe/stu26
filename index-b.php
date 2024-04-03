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
		$tp = "Hauptseite";
		$inc = "main/main.php";
		break;
	case "reg":
		$tp = "Registrierung";
		$inc = "main/register.php";
		// $inc = "main/main.php";
		break;
	case "help":
		$tp = "Hilfe";
		$inc = "main/help.html";
		break;
	case "act":
		$tp = "Aktivierung";
		$inc = "main/actcode.php";
		break;
	case "imp":
		$tp = "Impressum";
		$inc = "main/impressum.html";
		break;
	case "stu3":
		$tp = "STU3";
		$inc = "main/stu3.html";
		break;
	case "chat":
		$tp = "Chat";
		$inc = "main/chat.html";
		break;
	default:
		$tp = "Hauptseite";
		$inc = "main/main.php";
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

<head>
    <title>Star Trek Universe</title>
    <link rel="STYLESHEET" type="text/css" href=main/gfx/style.css>
    <link rel="shortcut icon" href="favicon.ico" />
    <meta NAME="description" content="Star Trek Universe - Ein Star Trek Browsergame">
    <meta NAME="keywords" content="star trek, star, trek, spiel, browsergame, rollenspiel, rpg, online">
    <meta NAME="author" content="Changeme">
    <meta NAME="robots" content="index">
    <meta NAME="robots" content="follow">
    <meta NAME="publisher" content="Changeme">
    <meta NAME="language" content="de">
    <meta NAME="Copyright" content="Changeme">
    <meta NAME="Page-Topic" content="star trek, star, trek, rollenspiel, online, rpg, spiel, browsergame">
    <style>
    #link {
        background: #000000;
        text-align: center;
        width: 90px;
        border: 1px solid #2d3243;
        display: block;
        text-decoration: none;
        padding: 2px 2px 2px 4px;
    }

    #chlog_link {
        background: #000000;
        text-align: center;
        width: 100%;
        border-bottom: 1px solid #404760;
        display: block;
        text-decoration: none;
    }

    #culink {
        background: #262323;
        text-align: center;
        width: 100px;
        border: 1px solid #2d3243;
    }

    #link:hover {
        background: #262323;
    }

    #chlog_link:hover {
        background: #262323;
    }

    #banner {
        position: absolute;
        top: 0px;
        left: 0px;
        text-align: center;
        width: 956px;
    }

    #nav {
        position: absolute;
        top: 100px;
        left: 10px;
        width: 100%;
    }

    #gamestate_Online {
        background: #277e00;
        color: #FFFFFF;
        text-align: center;
        width: 90px;
        border: 1px solid #2d3243;
        text-decoration: none;
        padding: 2px 2px 2px 4px;
        position: absolute;
        top: 0px;
        left: 860px;
    }

    #gamestate_Tick {
        background: #dfdc00;
        color: #000000;
        text-align: center;
        width: 90px;
        border: 1px solid #2d3243;
        text-decoration: none;
        padding: 2px 2px 2px 4px;
        position: absolute;
        top: 0px;
        left: 860px;
    }

    #gamestate_Wartung {
        background: #c30000;
        color: #FFFFFF;
        text-align: center;
        width: 90px;
        border: 1px solid #2d3243;
        text-decoration: none;
        padding: 2px 2px 2px 4px;
        position: absolute;
        top: 0px;
        left: 860px;
    }

    #prenav_main {
        position: absolute;
        top: 80px;
        left: 0px;
        width: 494px;
        height: 26px;
        text-align: left;
        vertical-align: top;
        font-size: 7pt;
        font-weight: bold;
        padding-left: 2px;
        padding-top: 2px;
        padding-bottom: 2px;
        background: #262323;
        border: 1px solid #2d3243;
    }

    #prenav_out {
        position: absolute;
        top: 80px;
        left: 530px;
        width: 294px;
        height: 26px;
        text-align: left;
        vertical-align: top;
        font-size: 7pt;
        font-weight: bold;
        padding-left: 2px;
        padding-top: 2px;
        padding-bottom: 2px;
        background: #262323;
        border: 1px solid #2d3243;
    }

    #prenav_state {
        position: absolute;
        top: 80px;
        left: 860px;
        width: 94px;
        height: 26px;
        text-align: left;
        vertical-align: top;
        font-size: 7pt;
        font-weight: bold;
        padding-left: 2px;
        padding-top: 2px;
        padding-bottom: 2px;
        background: #262323;
        border: 1px solid #2d3243;
    }

    #box0_head {
        position: absolute;
        top: 140px;
        left: 0px;
        padding: 2px 2px 2px 2px;
        width: 660px;
        border: 1px solid #2d3243;
        height: 26px;
        text-align: left;
        vertical-align: top;
        font-size: 7pt;
        font-weight: bold;
        background: #262323;
    }

    #box0_content {
        position: absolute;
        top: 160px;
        left: 10px;
        padding: 2px 2px 2px 2px;
        width: 660px;
        border-top: 1px solid #2d3243;
        border-bottom: 1px solid #2d3243;
        border-left: 1px solid #2d3243;
        border-right: 1px solid #2d3243;
        background: #000000;
    }

    #box1_head {
        position: absolute;
        top: 140px;
        left: 710px;
        width: 246px;
        border: 1px solid #2d3243;
        padding: 2px 2px 2px 2px;
        height: 26px;
        text-align: left;
        vertical-align: top;
        font-size: 7pt;
        font-weight: bold;
        background: #262323;
    }

    #box1_content {
        background: #000000;
        position: absolute;
        top: 160px;
        left: 720px;
        width: 246px;
        border: 1px solid #2d3243;
        padding: 2px 2px 2px 2px;
    }

    #box2_head {
        position: absolute;
        top: 250px;
        left: 710px;
        width: 246px;
        border: 1px solid #2d3243;
        padding: 2px 2px 2px 2px;
        height: 26px;
        text-align: left;
        vertical-align: top;
        font-size: 7pt;
        font-weight: bold;
        background: #262323;
    }

    #box2_content {
        background: #000000;
        position: absolute;
        top: 270px;
        left: 720px;
        width: 246px;
        border: 1px solid #2d3243;
        padding: 2px 2px 2px 2px;
    }

    #box3_head {
        position: absolute;
        top: 360px;
        left: 710px;
        width: 246px;
        border: 1px solid #2d3243;
        padding: 2px 2px 2px 2px;
        height: 26px;
        text-align: left;
        vertical-align: top;
        font-size: 7pt;
        font-weight: bold;
        background: #262323;
    }

    #box3_content {
        background: #000000;
        position: absolute;
        top: 380px;
        left: 720px;
        height: 195px;
        width: 246px;
        border: 1px solid #2d3243;
        padding: 2px 2px 2px 2px;
    }

    #box4_head {
        position: absolute;
        top: 600px;
        left: 710px;
        width: 246px;
        border: 1px solid #2d3243;
        padding: 2px 2px 2px 2px;
        height: 26px;
        text-align: left;
        vertical-align: top;
        font-size: 7pt;
        font-weight: bold;
        background: #262323;
    }

    #box4_content {
        background: #000000;
        position: absolute;
        top: 620px;
        left: 720px;
        width: 246px;
        border: 1px solid #2d3243;
        padding: 2px 2px 2px 2px;
    }

    #box5_head {
        position: absolute;
        top: 725px;
        left: 710px;
        width: 246px;
        border: 1px solid #2d3243;
        padding: 2px 2px 2px 2px;
        height: 26px;
        text-align: left;
        vertical-align: top;
        font-size: 7pt;
        font-weight: bold;
        background: #262323;
    }

    #box5_content {
        background: #000000;
        position: absolute;
        top: 745px;
        left: 720px;
        width: 246px;
        border: 1px solid #2d3243;
        padding: 2px 2px 2px 2px;
    }

    #box6_head {
        position: absolute;
        top: 850px;
        left: 710px;
        width: 246px;
        border: 1px solid #2d3243;
        padding: 2px 2px 2px 2px;
        height: 26px;
        text-align: left;
        vertical-align: top;
        font-size: 7pt;
        font-weight: bold;
        background: #262323;
    }

    #box6_content {
        background: #000000;
        position: absolute;
        top: 865px;
        left: 720px;
        width: 246px;
        border: 1px solid #2d3243;
        padding: 2px 2px 2px 2px;
    }

    #txbox {
        position: absolute;
        left: 80px;
    }
    </style>
    <link rel="alternate" type="application/atom+xml" title="Atom-Datei" href="http://www.stuniverse.de/static/kn.xml">
</head>

<body onload="document.f.login.focus();">

    <div id="banner">
        <img src=main/gfx/bbanner.png border=0 title="Star Trek Universe">
    </div>

    <div id="prenav_main">Navigation</div>
    <div id="prenav_out">Externe Links</div>
    <div id="prenav_state">Spielstatus</div>

    <div id="nav">
        <a href="index.php" id="link" style="position:absolute;top:0px;left:0px;">Hauptseite</a>
        <a href="?p=reg" id="link" style="position:absolute;top:0px;left:100px;">Registrierung</a>
        <a href="?p=help" id="link" style="position:absolute;top:0px;left:200px;">Hilfe</a>

        <a href="?p=imp" id="link" style="position:absolute;top:0px;left:400px;">Impressum</a>

        <a href="http://forum.stuniverse.de" target="_blank" id="link"
            style="position:absolute;top:0px;left:530px;">Forum</a>

        <?php
		// Online-Status
		echo "<div id=\"gamestate_" . $main->state . "\">" . $main->state . "</div>";
		?>
    </div>

    <div id="box0_head"><?php echo $tp; ?></div>
    <div id="box0_content">
        <?php
		// Der eigentliche Content findet hier drin seinen Platz
		include_once($inc);
		?>
    </div>

    <form action="main.php" method="post" name="f">
        <div id="box1_head">Login</div>
        <div id="box1_content">
            Siedler <input type="text" size="15" class="text" name="login" id="txbox"><br />
            Passwort <input type="password" size="15" class="text" name="pass" id="txbox"><br /><br />
            <input type=submit value=Login class=button>
        </div>
    </form>

    <div id="box2_head">Statistiken</div>
    <div id="box2_content">
        <?php
		// Spielstats
		echo "Spieler: " . $main->pc . "<br />
      Online: " . $main->opc . "<br />
	  <br>
      Runde: " . $main->cr . "<br />";
		?>
    </div>

    <div id="box3_head">Entwicklung (Stand: 02.09.2017)</div>
    <div id="box3_content">
        <br>
        <b>
            <font color="#55ff55">Zur Zeit in Arbeit:</font>
        </b>
        <br>- Überarbeitung Style & Aussehen
        <br>
        <br><b>
            <font color="#ffff55">Danach:</font>
        </b>
        <br>- Sektoreroberung
        <br>
        <br><b>
            <font color="#ff5555">Später:</font>
        </b>
        <br>- Änderungen an Rumpfklassen und Schiffsmodulen
        <br>- Kolonieverteidigung und Beamschutz




    </div>

    <div id="box4_head">Social</div>
    <div id="box4_content">
        <img src="gfx/facebook.ico" title="Twitter"> <a href="http://scnem.com/olt.php?sid=lurc.1b1fc9r,l=55805964"
            target="_blank">Facebook</a><br />
        <iframe
            src="//www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fpages%2FStar-Trek-Universe%2F209049872543929&amp;width=246&amp;height=62&amp;colorscheme=light&amp;show_faces=false&amp;border_color&amp;stream=false&amp;header=false&amp;appId=79750951070"
            scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:292px; height:62px;"
            allowTransparency="true"></iframe>
    </div>

    <div id="box5_head">Links</div>
    <div id="box5_content">
        <a href="http://www.usox.org"><img src="gfx/usox.ico" align="absmiddle" />usoX Bierblog</a>
    </div>

    <div id="box6_head">Copyright</div>
    <div id="box6_content">
        Star Trek is a registered trademark of Paramount Pictures.<br /><br />
        This site is strictly non-profit.<br />
        No copyright infringement is intended.<br />
        &copy; 2010 STU-Team</div>
</body>

</html>