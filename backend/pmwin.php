<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../inc/func.inc.php");
include_once("../inc/config.inc.php");
include_once("../class/db.class.php");

@session_start();

if ($_SESSION['login'] != 1) exit;

$db = new db;
include_once("../class/comm.class.php");
$comm = new comm;

echo "<div id=\"pmwinc\">";

if ($_GET['recipient'] && check_int($_GET['recipient']) && $_GET['text'])
{
	if ($db->query("SELECT id FROM stu_user WHERE id=".$_GET['recipient'],1) == 0) $td = "Dieser Siedler existiert nicht";
	elseif ($db->query("SELECT user_id FROM stu_ignorelist WHERE user_id=".$_GET['recipient']." AND recipient=".$_SESSION['uid'],1) > 0) $td = "Der Empfäger ignoriert dich";
	else
	{
		if (mb_detect_encoding($_GET['text'], 'UTF-8, ISO-8859-1') == "UTF-8") $_GET['text'] = utf8_decode($_GET['text']);
		$comm->sendpm($_SESSION['uid'],$_GET['recipient'],$_GET['text'],1);
		$td = "Nachricht gesendet";
		$sent = 1;
	}
}
if (check_int($_GET['ext1']) && check_int($_GET['ext2']) && $_GET['ext1'] > 0 && $_GET['ext2'] > 0) $string = $comm->getpmprefix($_GET['ext1'],$_GET['ext2']);
else $string = "";

echo "<form name=\"pmform\">
<table class=\"tcal\" style=\"border: 1px groove #8897cf;\">
<th colspan=\"2\" onMouseOver=\"switch_drag_on();\" onMouseOut=\"switch_drag_off();\">Neue Nachricht schreiben</th>";
if ($td) echo "<tr><td><b>".$td."</b></td></tr>";
echo "<tr><td>Empfänger</td></tr>
<tr><td>ID <input type=text size=5 name=recipient class=text value=".(check_int($_GET['recipient']) ? $_GET['recipient'] : "")."> ".(is_numeric($_GET['recipient']) && $_GET['recipient'] > 0 ? stripslashes($db->query("SELECT user FROM stu_user WHERE id=".$_GET['recipient']." LIMIT 1",1)) : "")."
<tr><td><textarea cols=60 rows=20 name=tx>".$string.stripslashes(str_replace("<br>","\n",$_GET[text]))."</textarea></td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td>".($sent != 1 ? "<input type=\"button\" value=\"Senden\" class=\"button\" onClick=\"send_pm();\">&nbsp;" : "")."<input type=\"button\" value=\"Schließen\" class=\"button\" onClick=\"cClick();\"></td></tr>
</table>
</form>
</div>";
?>