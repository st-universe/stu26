<?php
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
	case "spe":
		$tp = "Spenden";
		$inc = "main/spenden.html";
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
    <meta NAME="author" content="Daniel Jakob">
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
        top: 680px;
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
        top: 700px;
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
        <img src=main/gfx/banner.png border=0 title="Star Trek Universe">
    </div>

    <div id="prenav_main">Navigation</div>
    <div id="prenav_out">Externe Links</div>
    <div id="prenav_state">Spielstatus</div>

    <div id="nav">
        <a href="index.php" id="link" style="position:absolute;top:0px;left:0px;">Hauptseite</a>
        <a href="?p=reg" id="link" style="position:absolute;top:0px;left:100px;">Registrieren</a>
        <a href="?p=help" id="link" style="position:absolute;top:0px;left:200px;">Hilfe</a>
        <a href="?p=spe" id="link" style="position:absolute;top:0px;left:300px;">Spenden</a>
        <a href="?p=imp" id="link" style="position:absolute;top:0px;left:400px;">Impressum</a>

        <a href="http://forum.stuniverse.de" target="_blank" id="link"
            style="position:absolute;top:0px;left:530px;">Forum</a>
        <a href="http://wiki.stuniverse.de" target="_blank" id="link"
            style="position:absolute;top:0px;left:630px;">Wiki</a>
        <a href="http://bugs.stuniverse.de" target="_blank" id="link"
            style="position:absolute;top:0px;left:730px;">Bugtracker</a>
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
      Runde: " . $main->cr . "<br />
      Leute im Chat: " . $main->chatc . " (<a href=\"?p=chat\">zum Chat</a>)";
		?>
    </div>

    <div id="box3_head">Newsletter</div>
    <div id="box3_content">
        <iframe frameborder="0" scrolling="no" style="width: 245px; height: 195px;"
            src="http://scnem.com/art_resource.php?sid=kxni.2679sc3"></iframe>
    </div>

    <div id="box4_head">News-Feeds</div>
    <div id="box4_content">
        <img src="gfx/rss.gif" title="Newsfeed"> <a href="http://www.stuniverse.de/static/kn.xml"
            target="_blank">Kommunikations-Netzwerk</a><br />
        <img src="gfx/rss.gif" title="Newsfeed"> <a
            href="http://wiki.stuniverse.de/index.php?title=Spezial:Recentchanges&feed=rss" target="_blank">Wiki
            �nderungslog</a>
    </div>

    <div id="box5_head">Copyright</div>
    <div id="box5_content">
        Star Trek is a registered trademark of Paramount Pictures.<br /><br />
        This site is strictly non-profit.<br />
        No copyright infringement is intended.<br />
        &copy; 2001-2008 STU-Team</div>
</body>

</html>