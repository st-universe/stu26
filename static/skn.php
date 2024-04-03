<?php
include_once("../inc/func.inc.php");
include_once("../inc/config.inc.php");
include_once("../class/db.class.php");
$db = new db;

if (!check_int($_GET["id"])) exit;
$data = $db->query("SELECT a.id,a.titel,a.text,UNIX_TIMESTAMP(a.date) as date,a.username,a.user_id,a.rating,a.votes as rv,b.race,UNIX_TIMESTAMP(b.lastaction) as lastaction,ROUND(SUM(c.rating)/COUNT(c.kn_id)) as rat,COUNT(c.kn_id) as votes FROM stu_kn as a LEFT JOIN stu_user as b ON a.user_id=b.id LEFT JOIN stu_kn_rating as c ON a.id=c.kn_id WHERE a.id=".$_GET["id"]." GROUP BY a.id",4);
if ($data == 0) exit;

$gfx = "../gfx/";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>Star Trek Universe</title>
<link rel="STYLESHEET" type="text/css" href=../gfx/css/6.css>
<link rel="alternate" type="application/atom+xml" title="Atom-Datei" href="http://stuv2.sith-lords.info/static/atom100---Star-Trek-Universe-KN-Feed.xml">
</head>
<body bgcolor="#000000">
<table class=tcal>
<th>/ Kommunikations Netzwerk / Beitrag <?php echo $_GET["id"]; ?> anzeigen</th>
</table><br>
<?php
$knp = "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=750>
			<tr><td class=m colspan=2 width=750>".stripslashes($data[titel])."</td></tr>
			<tr><td width=620><img src=".$gfx."/rassen/".$data[race]."s.gif> ".stripslashes($data[username])." (".$data[user_id].")</td>
			<td class=m width=130>".date("d.m.Y H:i",$data['date'])."</td></tr></table>
			<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=750>
			<tr><td align=center width=120 valign=top>".($data[lastaction] < time()-300 ? "<font color=#ff0000>Offline</font>" : "<font color=#3F923D>Online</font>")."<br>".(strlen($data[propic]) > 10 ? "<img src=\"".$data[propic]."\" width=100 height=100>" : "<img src=".$gfx."/rassen/".$data[race]."kn.gif>")."<br>".($data[id] > $_SESSION["kn_lez"] ? "<font size=-3 color=#ff0000>".$data[id]."</font>" : "<font size=-3>".$data[id]."</font>")."</font><br>";
			$knp .= "</td><td width=580 valign=top colspan=2>".nl2br(stripslashes($data[text]))."</td></tr>
			<tr><td>";
			$knp .= "</td><td><font size=1>Wertung ";
			if ((!$data[rat] || $data[rat] == 0) && !$data[rating]) $knp .= "<img src=".$gfx."/buttons/stern2.gif> <img src=".$gfx."/buttons/stern2.gif> <img src=".$gfx."/buttons/stern2.gif> <img src=".$gfx."/buttons/stern2.gif> <img src=".$gfx."/buttons/stern2.gif>";
			else
			{
				if ($data[rating])
				{
					$data[rat] = $data[rating];
					$data[votes] = $data[rv];
				}
				$j = 0;
				while($j<$data[rat]) { $knp .= "<img src=".$gfx."/buttons/stern1.gif> "; $j++; }
				if ($j < 5) while($j<5) { $knp .= "<img src=".$gfx."/buttons/stern2.gif> "; $j++; }
			}
			$knp .= " - ".(!$data[votes] ? "0" : $data[votes])." Votes</font></td></tr>
			</table><br>";
echo $knp;
?>
</body>
</html>