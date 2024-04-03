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
$result = $db->query("SELECT a.id,a.user,UNIX_TIMESTAMP(lastaction) as lastact,COUNT(c.id) as cpm FROM stu_user as a LEFT JOIN stu_contactlist as b ON a.id=b.recipient AND b.user_id=".$_SESSION["uid"]." LEFT JOIN stu_pms as c ON a.id=c.send_user AND c.recip_user=".$_SESSION["uid"]." AND c.new='1' AND c.type='1' WHERE ((a.allys_id>0 AND a.allys_id=".$_SESSION["allys_id"].") OR (b.mode<3 AND !ISNULL(mode))) AND a.id!=".$_SESSION["uid"]." GROUP BY a.id ORDER BY a.lastaction DESC,a.id");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>STU Kontakte</title>
<SCRIPT LANGUAGE='JavaScript'>
var Win = null;
function cp(objekt,datei) { <?php echo "document.images[objekt].src = \"".$gfx."/\" + datei + \".gif\"";?> }
function pmwin(rec)
{
        str = "pmwin.php?s=sp&rec=" + rec;
        Win = window.open(str,'PmWin','width=400,height=200,resizeable=no,scrollbars=yes');
        window.open(str,'PmWin','width=400,height=200');
        Win.opener = self;
}
function npm(rec)
{
        str = "pmwin.php?s=wp&rec=" + rec;
        Win = window.open(str,'PmWin','width=400,height=200,resizeable=no,scrollbars=yes');
        window.open(str,'PmWin','width=400,height=200');
        Win.opener = self;
}
</script>
<?php echo '<link rel="STYLESHEET" type="text/css" href=gfx/css/'.$_SESSION["skin"].'.css>';?>
<meta http-equiv="REFRESH" content="60; url=folist.php">
</head>
<body>
<?php
echo "<a href=folist.php ".getonm("lz","buttons/lese")."><img src=".$gfx."/buttons/lese1.gif name=lz border=0 title=\"Aktualisieren\"></a>&nbsp;<a href=javascript:window.close() ".getonm("x","buttons/x")."><img src=".$gfx."/buttons/x1.gif name=x border=0 title=\"Fenster schließen\"></a>";
if (mysql_num_rows($result) == 0) meldung("Keine Einträge vorhanden");
else
{
	echo "<table class=tcal cellpadding=1 cellspacing=1>";
	while ($data=mysql_fetch_assoc($result))
	{
		if (!$la && $data[lastact] > time()-300) { echo "<tr><th colspan=3>Online</th></tr>"; $la = 1; }
		if (($la == 1 || !$la) && $data[lastact] < time()-300) { echo "<tr><th colspan=3>Offline</th></tr>"; $la = 2; }
		echo "<tr><td title=\"Zuletzt online am ".date("d.m",$data[lastact])."\">".stripslashes($data[user])."</td><td title=\"Neue Nachrichten: ".$data[cpm]."\">".($data[cpm] > 0 ? "<a href=javascript:pmwin(".$data[id].") style=\"color: #FF0000; text-decoration: blink;\">".$data[cpm]."</a>" : 0)."</td><td><a href=javascript:npm(".$data[id].") ".getonm("mg".$data[id],"buttons/msg")."><img src=".$gfx."/buttons/msg1.gif name=mg".$data[id]." border=0></a></td></tr>";
	}
	echo "</table>";
}
?>
</body>
</html>