<?php
include_once("inc/func.inc.php");
include_once("inc/config.inc.php");
include_once("class/db.class.php");
$db = new db;
session_start();
if ($_SESSION["login"] != 1) die(show_error(106));
include_once("class/sess.class.php");
$sess = new sess;
$gfx = $_SESSION["gfx_path"];
switch ($_GET[s])
{
	default:
		$v = "writepm";
	case "wp":
		if (!check_int($_GET[rec])) die(show_error(902));
		$name = $db->query("SELECT user FROM stu_user WHERE id=".$_GET[rec],1);
		if ($name === 0) die(show_error(902));
		$v = "writepm";
		break;
	case "sp":
		if (!check_int($_GET[rec])) die(show_error(902));
		$v = "showpm";
		break;
	case "wpd":
		if (!check_int($_GET[rec]) || !is_string($_GET[txt])) die(show_error(902));
		$v = "sendpm";
		break;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>STU PMs</title>
<SCRIPT LANGUAGE='JavaScript'>
function cp(objekt,datei) { <?php echo "document.images[objekt].src = \"".$gfx."/\" + datei + \".gif\"";?> }
</script>
<?php echo '<link rel="STYLESHEET" type="text/css" href=gfx/css/'.$_SESSION["skin"].'.css>';?>
</head>
<body>
<?php
if ($v == "writepm")
{
	echo "<table class=tcal cellpadding=1 cellspacing=1 height=100%>
	<form action=pmwin.php method=get><input type=hidden name=s value=wpd>
	<input type=hidden name=rec value=".$_GET[rec].">
	<tr><th height=8%>PM an ".stripslashes($name)." senden</th></tr>
	<tr>
		<td><textarea cols=55 rows=10 name=txt></textarea></td>
	</tr>
	<tr><td><input type=submit value=senden class=button>&nbsp;<a href=javascript:window.close() ".getonm("x","buttons/x")."><img src=".$gfx."/buttons/x1.gif name=x border=0 title=\"Fenster schlieﬂen\"></a></td></tr>
	</form></table>";

}
if ($v == "showpm")
{
	if (!$_GET[m] || !check_int($_GET[m])) $m = 0;
	else $m = $_GET[m];
	$data = $db->query("SELECT a.id,a.text,UNIX_TIMESTAMP(a.date) as date_tsp,b.user FROM stu_pms as a LEFT JOIN stu_user as b ON a.send_user=b.id WHERE a.recip_user=".$_SESSION["uid"]." AND a.send_user=".$_GET[rec]." AND a.type='1' ORDER BY date DESC LIMIT ".$m.",1",4);
	if ($data == 0)
	{
		$data = $db->query("SELECT a.id,a.text,UNIX_TIMESTAMP(a.date) as date_tsp,b.user FROM stu_pms as a LEFT JOIN stu_user as b ON a.send_user=b.id WHERE a.recip_user=".$_SESSION["uid"]." AND a.send_user=".$_GET[rec]." AND a.type='1' ORDER BY date DESC LIMIT 0,1",4);
		$m = 0;
	}
	$db->query("UPDATE stu_pms SET new='0' WHERE id=".$data[id]);
	echo "<table class=tcal cellpadding=1 cellspacing=1 height=100%>
	<tr><th height=8%>Absender: ".stripslashes($data[user])."</th></tr>
	<tr><td height=84% valign=top>".nl2br(stripslashes($data[text]))."</td></tr>
	<tr><td height=8%><a href=pmwin.php?s=sp&rec=".$_GET[rec]."&m=".($m-1)." ".getonm("bto","buttons/b_from")."><img src=".$gfx."/buttons/b_from1.gif name=bto border=0 title=\"Neuere PMs dieses Absenders\"></a>&nbsp;<a href=pmwin.php?s=wp&rec=".$_GET[rec]." ".getonm("mto","buttons/msg")."><img src=".$gfx."/buttons/msg1.gif name=mto border=0 title=\"Antwort schreiben\"></a>&nbsp;<a href=pmwin.php?s=sp&rec=".$_GET[rec]."&m=".($m+1)." ".getonm("bfr","buttons/b_to")."><img src=".$gfx."/buttons/b_to1.gif name=bfr border=0 title=\"ƒltere PMs dieses Absenders\"></a>&nbsp;<a href=javascript:window.close() ".getonm("x","buttons/x")."><img src=".$gfx."/buttons/x1.gif name=x border=0 title=\"Fenster schlieﬂen\"></a>&nbsp;(Datum: ".date("d.m.Y H:i",$data[date_tsp])." Uhr)</td></tr></table>";
}
if ($v == "sendpm")
{
	if ($db->query("SELECT user_id FROM stu_ignorelist WHERE user_id=".$_GET[rec]." AND recipient=".$_SESSION["uid"],1) > 0) meldung("Der Spieler ignoriert dich<br><br><a href=javascript:window.close()>Fenster schlieﬂen</a>");
	else {
		$db->query("INSERT INTO stu_pms (send_user,recip_user,text,type,date) VALUES ('".$_SESSION["uid"]."','".$_GET[rec]."','".addslashes($_GET[txt])."','1',NOW())");
		meldung("Nachricht gesendet<br><br><a href=javascript:window.close()>Fenster schlieﬂen</a>");
	}
}
?>
</body>
</html>