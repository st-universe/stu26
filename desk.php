<?php
if (!is_object($db)) exit;
include_once($global_path."class/game.class.php");
$game = new game;
include_once($global_path."class/user.class.php");
$user = new user;
include_once($global_path."class/comm.class.php");
$comm = new comm;

switch($_GET['s'])
{
	default:
		$v = "main";
	case "main":
		$v = "main";
		break;
	case "gs":
		$v = "getship";
		break;
}
if ($v == "main")
{
	pageheader("/ <b>Maindesk</b>");
	if ($_GET['a'] == "go" && check_int($_GET['hp'])) meldung($game->colonize($_GET['hp']));
	$ecalls = $comm->get_ecall_count($_SESSION['uid']);
	
	
	if ($_GET['a'] == "gnl") meldung($game->getnextlevel());	
	
	$deskcontent .= "<div style=\"border:none; padding-top: 50px;vertical-align:top;display:block;height:250px;\">";
	// echo "<div align=center><img src=".$gfx."/rassen/".$_SESSION['race'].".png></div>";
	
	$deskcontent .= "Updates seit letztem Login (".date("d.m H:i",$_SESSION['lastaction'])." Uhr):";
			
			$deskcontent .= "<br>";
	$lzc = $comm->getlzcount();
	if ($lzc > 0) $deskcontent .= "<br><a href=?p=comm&s=kn&lez=".$_SESSION['kn_lez'].">Neue Kommunikationsbeiträge (Sektor-KN): ".$lzc."</a>";
	$lzc = $comm->agetlzcount();
	if ($lzc > 0) $deskcontent .= "<br><a href=?p=comm&s=akn&lez=".$_SESSION['akn_lez'].">Neue Kommunikationsbeiträge (Allianz-KN): ".$lzc."</a>";
	$lzc = $comm->rgetlzcount();
	if ($lzc > 0 && $comm->checkfactionkn() == 1) $deskcontent .= "<br><a href=?p=comm&s=rkn&lez=".$_SESSION['rkn_lez'].">Neue Kommunikationsbeiträge (Rassen-KN): ".$lzc."</a>";			
			
			$deskcontent .= "<br><br><a href=?p=comm&s=nr>Notrufe: ".(!$ecalls ? 0 : $ecalls)."</a>";
	if ($_SESSION["vacmsg"])
	{
		$deskcontent .= "<br>".$_SESSION['vacmsg'];
		unset($_SESSION['vacmsg']);
	}
	$deskcontent .= "</div>";
	
	echo "<br><br><br><br><br><center><div style=\"width:80%;\">";
	echo "<table width=100% class=tablelayout>";
	echo "<tr>";
	echo "<td class=tablelayout>";
	echo fixedPanel(1,"Willkommen, ".stripslashes($_SESSION['user'])."! (ID ".$_SESSION["uid"].")","mdesk",$gfx."/buttons/icon/maindesk.gif",$deskcontent);	
	echo "</td>";
	echo "<td class=tablelayout style=\"width:400px;\">";
	if ($_SESSION['race'] == 1) echo fixedPanel(2,"Emblem der Föderation","mcolcs",$gfx."/buttons/icon/r1.gif","<center><img src=".$gfx."/rassen/".$_SESSION['race'].".png width=300 height=300 /></center>");	
	if ($_SESSION['race'] == 2) echo fixedPanel(2,"Emblem des Imperiums","mcolcs",$gfx."/buttons/icon/r2.gif","<center><img src=".$gfx."/rassen/".$_SESSION['race'].".png width=300 height=300 /></center>");	
	if ($_SESSION['race'] == 3) echo fixedPanel(2,"Emblem des Reichs","mcolcs",$gfx."/buttons/icon/r3.gif","<center><img src=".$gfx."/rassen/".$_SESSION['race'].".png width=300 height=300 /></center>");
	echo "</td>";	
	echo "</tr></tr>";
	echo "<td class=tablelayout>";
	echo fixedPanel(3,"Level-Informationen","mlvl",$gfx."/buttons/icon/options.gif",$user->getLevelInfo());	
	echo "</td>";
	echo "<td class=tablelayout style=\"width:400px;\">";
	echo fixedPanel(4,"Mögliche Kolonien","mcolcs",$gfx."/buttons/icon/planet.gif",$user->getColonyInfo());	
	echo "</td>";
	echo "</tr></table></div></center>";
	
	
	
	// echo "</tr><tr><td>Eingeloggt als ".stripslashes($_SESSION['user'])."<br>
	// ID: ".$_SESSION["uid"]."<br>
	// Kolonisationslevel: ".$_SESSION['level'].($_SESSION['level'] < 6 ? " (<a href=http://wiki.stuniverse.de/index.php/Beginner:Level_".$_SESSION['level']." target=_blank>Hilfe zu Level ".$_SESSION['level']."</a>)" : "")."<br>
	// Eingeloggt seit: ".date("d.m H:i",$_SESSION['logintime'])." Uhr<br>
	// Letzte Aktion: ".date("d.m H:i",$_SESSION['lastaction'])." Uhr<br>
	// <a href=?p=comm&s=nr>Notrufe: ".(!$ecalls ? 0 : $ecalls)."</a>";
	// if ($_SESSION["vacmsg"])
	// {
		// echo "<br>".$_SESSION['vacmsg'];
		// unset($_SESSION['vacmsg']);
	// }
	// $lzc = $comm->getlzcount();
	// if ($lzc > 0) echo "<br><a href=?p=comm&s=kn&lez=".$_SESSION['kn_lez'].">Neue Kommunikationsbeiträge (Sektor-KN): ".$lzc."</a>";
	// $lzc = $comm->agetlzcount();
	// if ($lzc > 0) echo "<br><a href=?p=comm&s=akn&lez=".$_SESSION['akn_lez'].">Neue Kommunikationsbeiträge (Allianz-KN): ".$lzc."</a>";
	// $lzc = $comm->rgetlzcount();
	// if ($lzc > 0 && $comm->checkfactionkn() == 1) echo "<br><a href=?p=comm&s=rkn&lez=".$_SESSION['rkn_lez'].">Neue Kommunikationsbeiträge (Rassen-KN): ".$lzc."</a>";
	// if ($_SESSION['wpo'] > 0) echo "<br><br>Schiffbau und -Wartung sind noch für ".$_SESSION['wpo']." Runde(n) gesperrt";
	// $rd = $game->getcurrentround();
	// echo "<br><br>Aktuelle Runde: ".$rd['runde']." (läuft seit dem ".date("d.m.Y H:i",$rd['start'])." Uhr)";
	// if ($_SESSION['npc_type'] == 1 || $_SESSION['npc_type'] == 2) echo "<br><br><a href=?p=npc>NPC-Menü</a>";
	// if ($_SESSION['npc_type'] == 3) echo "<br><br><a href=?p=npc>Spielleiter-Menü</a>";
	// echo "</td>
	// </tr></table>";


	
	// if (strstr($gfx,"http://"))
	// {
		// $file = @fgets(@fopen($gfx."/version", "r"),5);
		// $ve = explode(".",$file);
		// $gfx_v = $db->query("SELECT value FROM stu_game_vars WHERE var='gfx_v' LIMIT 1",1);
		// $ave = explode(".",$gfx_v);
		// if ($file != $gfx_v)
		// {
			// echo "<br><table align=center bgcolor=#262323 cellspacing=1 cellpadding=1 width=500>
			// <th>Achtung!</th>
			// <tr><td>Das von Dir genutzte Grafikpack ist veraltet!<br>
			// <id style=\"color: #FF0000;\">Deine Version: ".$file."</id><br>
			// <id style=\"color: Green;\">Aktuelle Version: ".$gfx_v."</id><br><br>";
			// if ($ave[0] != $ve[0]) echo "Es gab ein großes Update des Grafikpacks. Das bedeutet, dass einige Bilder gelöscht oder ersetzt wurden und es daher zu einigen Anzeigefehlern kommen könnte.<br>Ein Update wird dringend empfohlen!";
			// elseif ($ave[0] == $ve[0]) echo "Es gab ein kleineres Update des Grafikpacks. Das bedeutet, es könnten vereinzelt Bilder gelöscht oder ersetzt worden sein. Daher könnte es sein, dass die ein oder andere Grafik nicht angezeigt werden kann.<br>Ein Update wird empfohlen.";
			// echo "<br><a href=gfx/images.zip>Grafikpack downloaden</a></td></tr>
			// </table>";
		// }
	// }
	
	

	
	// if ($_SESSION['level'] < 5) echo $user->getLevelInfo();
	
	// if ($_SESSION['level'] < 6 && $_SESSION['level'] > 1)
	// {
		// $ld = getnextlevel();
		// $wp = $db->query("SELECT SUM(lastrw) FROM stu_colonies WHERE user_id=".$_SESSION['uid'],1);
		// $ship = $db->query("SELECT COUNT(a.id) FROM stu_ships as a LEFT JOIN stu_ships_buildplans as b USING(plans_id) WHERE a.user_id=".$_SESSION["uid"]." AND b.user_id=".$_SESSION['uid'],1);
		// $cols = $db->query("SELECT COUNT(a.id) FROM stu_colonies as a LEFT JOIN stu_colonies_classes as b USING(colonies_classes_id) WHERE ISNULL(b.is_moon) AND a.user_id=".$_SESSION['uid'],1);
		// $moon = $db->query("SELECT COUNT(a.id) FROM stu_colonies as a LEFT JOIN stu_colonies_classes as b USING(colonies_classes_id) WHERE b.is_moon='1' AND a.user_id=".$_SESSION['uid'],1);
		// $work = $db->query("SELECT SUM(bev_work) FROM stu_colonies WHERE user_id=".$_SESSION['uid'],1);
		// echo "<br><table align=center bgcolor=#262323 cellspacing=1 cellpadding=1 width=600>
		// <tr>
			// <th colspan=3>Voraussetzung für Kolonisationslevel ".$ld['lvl']."</th>
		// </tr>
		// <tr>
			// <td></td>
			// <td>benötigt</td>
			// <td>Status</td>
		// </tr>";
		// if ($ld['ship'] > 0)
		// {
			// echo "<tr>
			// <td>Selbst gebaute Schiffe</td>
			// <td>".$ld['ship']."</td>
			// <td>".($ship < $ld['ship'] ? "<font color=#FF0000>Nicht erfüllt</font> (".$ship.")" : "<font color=Lime>erfüllt</font> (".$ship.")")."</td>
			// </tr>";
		// }
		// if ($ld['wp'] > 0)
		// {
			// echo "<tr>
			// <td>Erreichte Wirtschaftspunkte</td>
			// <td>".$ld['wp']."</td>
			// <td>".($wp < $ld['wp'] ? "<font color=#FF0000>Nicht erfüllt</font> (".$wp.")" : "<font color=Lime>erfüllt</font> (".$wp.")")."</td>
			// </tr>";
		// }
		// if ($ld['cols'] > 0)
		// {
			// echo "<tr>
			// <td>Kolonien auf Planeten</td>
			// <td>".$ld['cols']."</td>
			// <td>".($cols < $ld['cols'] ? "<font color=#FF0000>Nicht erfüllt</font> (".$cols.")" : "<font color=Lime>erfüllt</font> (".$cols.")")."</td>
			// </tr>";
		// }
		// if ($ld['moon'] > 0)
		// {
			// echo "<tr>
			// <td>Kolonien auf Monden</td>
			// <td>".$ld['moon']."</td>
			// <td>".($moon < $ld['moon'] ? "<font color=#FF0000>Nicht erfüllt</font> (".$moon.")" : "<font color=Lime>erfüllt</font> (".$moon.")")."</td>
			// </tr>";
		// }
		// if ($ld['work'] > 0)
		// {
			// echo "<tr>
			// <td>Arbeiter</td>
			// <td>".$ld['work']."</td>
			// <td>".($work < $ld['work'] ? "<font color=#FF0000>Nicht erfüllt</font> (".$work.")" : "<font color=Lime>erfüllt</font> (".$work.")")."</td>
			// </tr>";
		// }
		// if ($ld['fo'] > 0)
		// {
			// echo "<tr>
			// <td>Forschungspunkte</td>
			// <td>".$ld['fo']." pro Gebiet</td>
			// <td><img src=".$gfx."/goods/41.gif title=\"Forschungspunkte Verarbeitung\"> ".($_SESSION["r_verarbeitung"] < $ld['fo'] ? "<font color=#FF0000>Nicht erfüllt</font> (".$_SESSION["r_verarbeitung"].")" : "<font color=Lime>erfüllt</font> (".$_SESSION["r_verarbeitung"].")")."&nbsp;
			// <br><img src=".$gfx."/goods/42.gif title=\"Forschungspunkte Technik\"> ".($_SESSION["r_technik"] < $ld['fo'] ? "<font color=#FF0000>Nicht erfüllt</font> (".$_SESSION["r_technik"].")" : "<font color=Lime>erfüllt</font> (".$_SESSION["r_technik"].")")."&nbsp;
			// <br><img src=".$gfx."/goods/43.gif title=\"Forschungspunkte Konstruktion\"> ".($_SESSION["r_konstruktion"] < $ld['fo'] ? "<font color=#FF0000>Nicht erfüllt</font> (".$_SESSION["r_konstruktion"].")" : "<font color=Lime>erfüllt</font> (".$_SESSION["r_konstruktion"].")")."</td>
			// </tr>";
		// }
		// if ($ship >= $ld['ship'] && $wp >= $ld['wp'] && $_SESSION["r_konstruktion"] >= $ld['fo'] && $_SESSION["r_technik"] >= $ld['fo'] && $_SESSION["r_verarbeitung"] >= $ld['fo'] && $moon >= $ld['moon'] && $work >= $ld['work'])
		// {
			// echo "<tr>
			// <td colspan=3><a href=?p=main&a=gnl>Kolonisationslevel ".$ld['lvl']." beantragen</a></td>
			// </tr>";
		// }
		// echo "</table>";
	// }
}
if ($v == "getship")
{
	pageheader("/ <a href=?>Maindesk</a> / <b>Start-Kolonie gründen</b>");
	
	
	$planilist = "";
	$planilist .= "<form action=main.php method=get>
	<input type=hidden name=p value=main>
	<input type=hidden name=s value=main>
	<input type=hidden name=a value=go>
	<table class=tcal cellpadding=1 cellspacing=1 style=\"width:100%\">
	<tr></tr>";
	$game->loadtradeposts(gethpbyfaction($_SESSION['race']));
	
	$posts = array();
	while($data=mysql_fetch_assoc($game->result))
	{
		array_push($posts,$data);
	}
	
	function cmp($a, $b)
	{
		if ($a[dist] == $b[dist]) {
			return 0;
		}
		return ($a[dist] > $b[dist]);
	}	
	
	$colonies = array();
	foreach($posts as $p) {
		$game->loadClosestColonies($p[cx],$p[cy]);
	
		while($data=mysql_fetch_assoc($game->result))
		{
			array_push($colonies,$data);
		}
	}
	usort($colonies,'cmp');
	
	for ($i = 0; ($i < 15 && $i < count($colonies)); $i++) {
		$data = $colonies[$i];
	
		if ($i % 2 == 0) $trc = " style=\"background-color: #171616\"";
		else $trc = "";
					// <td width=50><img src=".$gfx."/systems/".$data[system_type]."m.png></td>
		$planilist .= "<tr>
			<td".$trc." width=20><input type=\"radio\" name=\"hp\" value=\"".$data['id']."\"></td>

			<td width=100><center><img src=".$gfx."/planets/".$data[colonies_classes_id].".gif title=\"".$data[classname]."\"></center></td>
			<td".$trc.">".stripslashes($data['planet_name'])."</td>
			<td width=100><img src=".$gfx."/systems/".$data[system_type]."m.png width=40 height=40> ".$data[cx]."|".$data[cy]."</td>
			
		</tr>";
	
		
	}
	$planilist .= "<tr><td colspan=5><center><input type=submit class=button value=\"Kolonie gründen\"></center></td></tr></table></form>";	
	
	echo "<div style=\"width: 600px;\">".fixedPanel(3,"Startkolonie wählen","mcolcs",$gfx."/buttons/icon/planet.gif",$planilist)."</div>";	
}
?>