<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../inc/func.inc.php");
include_once("../inc/config.inc.php");
include_once("../class/db.class.php");

@session_start();

if (!check_int($_GET[id]) || $_SESSION["login"] != 1) exit;

$db = new db;

$gfx = $_SESSION[gfx_path];
if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "../gfx/";

$data = $db->query("SELECT a.user,UNIX_TIMESTAMP(a.lastaction) as la,a.race,a.propic,a.allys_id,b.name as aname,c.description,c.icq FROM stu_user as a LEFT JOIN stu_allylist as b USING(allys_id) LEFT JOIN stu_user_profiles as c ON a.id=c.user_id WHERE a.id=".$_GET[id],4);
if ($data == 0) die(show_error(902));
$cl = $db->query("SELECT mode FROM stu_contactlist WHERE recipient=".$_GET[id]." AND user_id=".$_SESSION["uid"],1);
switch ($cl)
{
	case 0:
		$i = "Nicht eingetragen";
		break;
	case 1:
		$i = "Freund";
		break;
	case 2:
		$i = "Neutral";
		break;
	case 3:
		$i = "Feind";
		break;
}

echo "<SCRIPT LANGUAGE='JavaScript'>
function cp(objekt,datei) { document.images[objekt].src = \"".$gfx."/\" + datei + \".gif\"}
</script>
<table class=Tcal style=\"border: 1px groove #8897cf;\">
<th colspan=2 onMouseOver=\"switch_drag_on();\" onMouseOut=\"switch_drag_off();\">Spielerprofil</th>
<tr>
<td rowspan=".(check_int($data[icq]) && $data['icq'] > 0 ? 7 : 6)." width=70 align=center valign=top>".(strlen($data[propic])>10 ? "<img src=".$data[propic]." width=100 height=100>" : "<img src=".$gfx."/rassen/".$data[race]."kn.png>")."</td>
<td>ID: ".$_GET[id]." <a href=main.php?p=comm&s=nn&recipient=".$_GET[id]." target=_parent ".getonm("pm","buttons/msg")."><img src=".$gfx."/buttons/msg1.gif border=0 name=pm title=\"PM schreiben\"></a></td>
</tr>
<tr><td>".stripslashes($data[user])."</td></tr>
<tr><td>Allianz: ".($data[allys_id] == 0 ? "Keine" : "<a href=main.php?p=ally&s=de&id=".$data[allys_id]." target=main>".stripslashes($data[aname])."</a>")."</td></tr>
<tr></td></td>
<tr><td>Status: ".($data[la] < time()-300 ? "<font color=FF0000>offline</font>" : "<font color=Green>online</font>")."</td></tr>";
if ($data[icq] > 0) echo "<tr><td>ICQ: ".$data[icq]." <img src=http://wwp.icq.com/scripts/online.dll?icq=".$data[icq]."&img=5></td></tr>";
echo "<tr><td>Kontakt: ".$i." <a href=main.php?p=comm&s=ec&recipient=".$_GET[id]." target=main ".getonm("ec","buttons/clist")."><img src=".$gfx."/buttons/clist1.gif name=ec border=0 title=\"Kontaktliste editieren\"></a></td></tr>
<th colspan=\"2\">Auszeichnungen</th>
<tr><td colspan=\"2\">";
$result = $db->query("SELECT award_id FROM stu_user_awards WHERE user_id=".$_GET['id']);
if (mysql_num_rows($result) == 0) echo "Keine";
else while($dat=mysql_fetch_assoc($result)) echo "<img src=gfx/awards/".$dat['award_id'].".gif title=\"".getawardname($dat['award_id'])."\">&nbsp;";
echo "</td></tr>
<tr><td colspan=2>".nl2br(stripslashes($data[description]))."</td></tr>
<tr><td colspan=2><input type=button class=button onClick=\"cClick();\" value=Schließen></td></tr>
</table>";
?>