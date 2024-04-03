<?php
if (!is_object($db)) exit;
include_once("class/ally.class.php");
$ally = new ally;
switch($_GET[s])
{
	default:
		if ($_GET['a'] == "decj" && $_SESSION['allys_id'] == 0 && check_int($_GET['id'])) $result = $ally->decInvitation($_GET['id']);
		if ($_SESSION['allys_id'] > 0) $v = "allyview";
		else $v = "allylist";
	case "ma":
		if ($_GET['a'] == "aj")
		{
			if ($_SESSION['allys_id'] > 0 || !check_int($_GET['id']) || $_GET['id'] == 0)
			{
				$v = "allyview";
				break;
			}
			$result = $ally->joinally($_GET['id']);
			$v = "allyview";
			$ally->loadally($_GET['id']);
			if ($ally->data == 0) die(show_error(902));
			$ally->getmembers($_GET['id']);
		}
		else
		{
			if ($_SESSION['allys_id'] > 0) $v = "allyview";
			else $v = "allylist";
		}
		break;
	case "al":
		$v = "allylist";
		break;
	case "na":
		$v = "newallianz";
		break;
	case "de":
		$v = "allianzdetails";
		$ally->loadally($_GET['id']);
		if ($ally->data == 0) die(show_error(902));
		$ally->getmembers($_GET['id']);
		break;
	case "ao":
		$v = "allianzoptionen";
		$ally->loadally($_SESSION['allys_id']);
		if ($ally->data == 0) die(show_error(902));
		if ($ally->data['praes_user_id'] != $_SESSION['uid'] && $ally->data['vize_user_id'] != $_SESSION['uid']) die(show_error(902));
		$ally->getmembers($_SESSION['allys_id']);
		break;
	case "la":
		$v = "allianzlöschen";
		if ($_SESSION['allys_id'] == 0) die(show_error(902));
		$ally->loadally($_SESSION['allys_id']);
		break;
	case "dp":
		$ally->loadally($_SESSION['allys_id']);
		if ($ally->data == 0) die(show_error(902));
		if ($_SESSION['uid'] != $ally->data['praes_user_id'] && $_SESSION['uid'] != $ally->data['vize_user_id'] && $_SESSION['uid'] != $ally->data['auss_user_id']) die(show_error(902));
		$v = "diplomatie";
		break;
	case "in":
		$ally->loadally($_SESSION['allys_id']);
		if ($ally->data == 0) die(show_error(902));
		if ($ally->data['praes_user_id'] != $_SESSION['uid'] && $ally->data['vize_user_id'] != $_SESSION['uid']) die(show_error(902));
		$v = "informationen";
		break;
	case "dsw":
		$ally->loadally($_SESSION['allys_id']);
		if ($ally->data == 0) die(show_error(902));
		if ($ally->data['praes_user_id'] != $_SESSION['uid'] && $ally->data['vize_user_id'] != $_SESSION['uid']) die(show_error(902));
		$v = "detailshipview";
		break;
	case "ab":
		if (!check_int($_GET['id'])) die(show_error(902));
		$ally->loadremrelationships($_GET['id']);
		$v = "beziehungen";
		break;
	case "am":
		$ally->loadally($_SESSION['allys_id']);
		if ($ally->data == 0) die(show_error(902));
		if ($ally->data['praes_user_id'] != $_SESSION['uid'] && $ally->data['vize_user_id'] != $_SESSION['uid']) die(show_error(902));
		$ally->get_member_data_by_id($_SESSION['allys_id']);
		$v = "mitglieder";
		break;
	case "sai":
		$ally->loadally($_SESSION['allys_id']);
		if ($ally->data == 0) die(show_error(902));
		if ($ally->data['praes_user_id'] != $_SESSION['uid'] && $ally->data['vize_user_id'] != $_SESSION['uid'] && $_SESSION['uid'] != $ally->data['auss_user_id']) die(show_error(902));
		$ally->check_alliance($_GET['id']);
		$ally->data = $ally->get_ally_by_id($_GET['id']);
		$ally->get_member_data_by_id($_GET['id']);
		$v = "bndinfo";
}
if ($v == "allyview")
{
	$ally->loadally($_SESSION["allys_id"]);
	if ($ally->data == 0)
	{
		$_SESSION['allys_id'] = 0;
		die(meldung("Die Allianz wurde gelöscht"));
	}
	if ($_SESSION['uid'] == $ally->data['praes_user_id'] || $_SESSION['uid'] == $ally->data['vize_user_id'] || $_SESSION['uid'] == $ally->data['auss_user_id'])
	{
		if ($_GET["a"] == "ivu" && check_int($_GET[uid]) && $_GET[uid] > 0) $result = $ally->inviteuser($_GET[uid]);
		if ($_GET["a"] == "deli" && check_int($_GET[uid]) && $_GET[uid] > 0) $result = $ally->delinvitation($_GET[uid]);
	}
	$_SESSION['uid'] == $ally->data['praes_user_id'] || $_SESSION['uid'] == $ally->data['vize_user_id'] ? $ap = 1 : $ap = 0;
	$ally->getmembers($_SESSION["allys_id"]);
	pageheader("/ <b>Allianzschirm</b> (".stripslashes($ally->data[name]).")");
	if ($result) meldung($result);
	echo "<table cellpadding=0 cellspacing=0>
	<tr>
	<td valign=top width=400>
	<table class=tcal>
	<tr><th>Präsident</th><td>".stripslashes($ally->data[user])."</td></tr>
	<tr><th>Vize</th><td>".($ally->data['vize_user_id'] > 0 ? stripslashes($db->query("SELECT user FROM stu_user WHERE id=".$ally->data['vize_user_id'],1)) : "Unbesetzt")."</td></tr>
	<tr><th>Außenminister</th><td>".($ally->data['auss_user_id'] > 0 ? stripslashes($db->query("SELECT user FROM stu_user WHERE id=".$ally->data['auss_user_id'],1)) : "Unbesetzt")."</td></tr>
	</table><br>
	<table class=tcal><th>Beschreibung</th>
	<tr><td>".nl2br(stripslashes($ally->data[descr]))."</td></tr>
	<tr><td><a href=\"?p=ally&s=ab&id=".$_SESSION["allys_id"]."\">Diplomatische Beziehungen</a></td></tr></table><br>
	<table class=tcal>
	<th>Aktionen</th>
	<tr><td>".(strlen($ally->data[homepage]) > 0 ? "<a href=out.php?ai=".$_SESSION['allys_id']." target=_blank>Homepage aufrufen</a><br>" : "")."
	<a href=?p=ally&s=al>Allianzliste anzeigen</a><br><br>
	<a href=?p=ally&s=la><font color=#FF0000>Allianz verlassen</font></a></td></tr></table>";
	if ($_SESSION['uid'] == $ally->data['praes_user_id'] || $_SESSION['uid'] == $ally->data['vize_user_id'] || $_SESSION['uid'] == $ally->data['auss_user_id'])
	{
		echo "<br><table class=tcal>
		<form action=main.php method=get><input type=hidden name=p value=ally><input type=hidden name=a value=ivu>
		<th>Administratives</th>
		<tr>
		<td><a href=?p=ally&s=dp>Diplomatie</a>";
		if ($_SESSION['uid'] == $ally->data['praes_user_id'] || $_SESSION['uid'] == $ally->data['vize_user_id'])
			echo "<br><a href=?p=ally&s=in>Informationen</a><br><a href=?p=ally&s=ao>Einstellungen</a><br><a href=?p=ally&s=am>Mitgliederverwaltung</a><br>";
		$result = $ally->getinvitations($_SESSION["allys_id"]);
		echo "<br>Spieler einladen: ID <input type=text class=text size=3 name=uid> <input type=submit class=button value=einladen>";
		if (mysql_num_rows($result) > 0)
		{
			echo "<br><br><table width=\"100%\" class=tcal><th colspan=2>Bestehende Einladungen</th>";
			while($data=mysql_fetch_assoc($result)) echo "<tr><td>".stripslashes($data[user])."</td><td><a href=?p=ally&a=deli&uid=".$data[user_id]."><img src=".$gfx."/buttons/x1.gif name=di".$data[user_id]." border=0 title=\"Angebot an ".ftit($data[user])." löschen\"</a></td></tr>";
			echo "</table>";
		}
		echo "</td></tr>
		</form>
		</table>";
	}
	echo "</td>
	<td width=5></td>
	<td width=400 valign=top>
	<table class=tcal>
	<th colspan=2>Mitglieder (".mysql_num_rows($ally->ar).")</th>";
	while($data=mysql_fetch_assoc($ally->ar)) echo "<tr><td><img src=".$gfx."/rassen/".($data['subrace'] ? $data['race']."_".$data['subrace'] : $data['race'])."s.gif> <img src=".$gfx."/buttons/alert".($data['date'] < time()-300 ? "3.gif title=\"Offline. Letzter Login: ".date("d.m.Y H:i",$data['date'])."\"" : "1.gif title=\"Online\"")."> ".stripslashes($data['user'])."</td><td width=40><a href=\"javascript:void(0);\" onClick=\"opensi(".$data['id'].")\" ".getonm("id".$data['id'],"buttons/info")."><img src=".$gfx."/buttons/info1.gif name=id".$data['id']." border=0 title='Spielerprofil'></a> <a href=\"?p=comm&s=nn&recipient=".$data['id']."\" onmouseover=cp('ap".$data['id']."','buttons/msg2') onmouseout=cp('ap".$data[id]."','buttons/msg1')><img src=\"".$gfx."/buttons/msg1.gif\" name=\"ap".$data['id']."\" border=\"0\" title=\"PM an ".ftit($data['user'])." (".$data['id'].") schreiben\"></a></td></tr>";
	echo "</table></td>
	</tr>
	</table><br><br>".$ally->getcolemergencies($_SESSION["allys_id"])."";
}
if ($v == "allylist")
{
	pageheader("/ <a href=?p=ally&s=ma>Allianzschirm</a> / <b>Allianzliste</b>");
	if ($_GET['a'] == "da")
	{
		$ally->loadally($_SESSION['allys_id']);
		if ($ally->data == 0 || $ally->data['praes_user_id'] != $_SESSION['uid']) die;
		$result = $ally->delally();
		$_SESSION['allys_id'] = 0;
	}
	if ($_GET['a'] == "la")
	{
		if ($_SESSION['allys_id'] == 0) die;
		$ally->loadally($_SESSION['allys_id']);
		if ($_SESSION['uid'] == $ally->data['praes_user_id'] && $ally->data['vize_user_id'] == 0) die;
		if ($_SESSION['uid'] == $ally->data['praes_user_id']) $db->query("UPDATE stu_allylist SET praes_user_id=vize_user_id,vize_user_id=0 WHERE allys_id=".$_SESSION['allys_id']);
		if ($_SESSION['uid'] == $ally->data['vize_user_id']) $db->query("UPDATE stu_allylist SET vize_user_id=0 WHERE allys_id=".$_SESSION['allys_id']);
		if ($_SESSION['uid'] == $ally->data['auss_user_id']) $db->query("UPDATE stu_allylist SET auss_user_id=0 WHERE allys_id=".$_SESSION['allys_id']);
		$db->query("UPDATE stu_user SET allys_id=0 WHERE id=".$_SESSION['uid']);
		$_SESSION['allys_id'] = 0;
		$result = "Du hast die Allianz verlassen";
	}
	if ($result) meldung($result);
	$ally->getallylist();
	if ($_SESSION['allys_id'] == 0) meldung("<a href=?p=ally&s=na>Allianz gründen</a>");
	if (mysql_num_rows($ally->ar) == 0) meldung("Keine Allianzen vorhanden");
	else
	{
		echo "<table class=tcal cellpadding=1 cellspacing=1>
		<tr><th width=50%>Name</th><th>Präsident</th><th>Mitglieder</th><th>Details</th></tr>";
		while($data=mysql_fetch_assoc($ally->ar))
		{
			$i++;
			if ($i == 2)
			{
				$trc = " style=\"background-color: #171616\"";
				$i = 0;
			}
			echo "<tr><td".$trc."><a href=\"?p=ally&s=de&id=".$data['allys_id']."\">".stripslashes($data['name'])."</a> (".$data['allys_id'].")</td><td".$trc.">".stripslashes($data['user'])."</td><td".$trc.">".$data['mc']."</td><td".$trc."><a href=\"?p=ally&s=de&id=".$data['allys_id']."\" onmouseover=cp('ad".$data['allys_id']."','buttons/info2') onmouseout=cp('ad".$data['allys_id']."','buttons/info1')><img src=\"".$gfx."/buttons/info1.gif\" name=\"ad".$data['allys_id']."\" border=\"0\" title=\"Details\"></a></td></tr>";
			$trc = "";
		}
		echo "</table>";
	}
}

if ($v == "newallianz")
{
	pageheader("/ <a href=?p=ally&s=ma>Allianzschirm</a> / <b>Allianz erstellen</b>");
	if ($_SESSION["allys_id"] > 0) die;
	if ($_GET[a] == "sd" && $_GET[sent] == 1)
	{
		if (!is_string($_GET[name])) $error[name] = "Der Name ist fehlerhaft";
		if (strlen(strip_tags($_GET[name],"<b></b><i></i><u></u><font></font>")) < 6) $error[name] = "Der Name muss aus mindestens 6 Zeichen bestehen";
		if (!check_html_tags($_GET[name])) $error[name] = "Die HTML-Tags sind fehlerhaft";
		if (strlen(format_string($_GET[name])) > 255) $error[name] = "Der Name ist zu lang";
		if (strlen(trim(strip_tags($_GET[desc]))) < 1) $error[desc] = "Es wurde keine Beschreibung angegeben";
		if (!is_array($error)) meldung($ally->newally());
	}
	
	echo "<table class=tcal cellpadding=1 cellspacing=1>
	<form action=main.php method=get>
	<input type=hidden name=p value=ally>
	<input type=hidden name=s value=na>
	<input type=hidden name=a value=sd>
	<input type=hidden name=sent value=1>
	<tr><th>Name</th><td><input type=text size=30 name=name value=\"".str_replace("\"","",$_GET[name])."\" class=text></td><td>min 6 / max 255 Zeichen | Erlaubte HTML-Tags: &lt;font&gt;&lt;/font&gt;</td></tr>";
	if ($error[name]) echo "<tr><td colspan=3><font color=#ff0000>".$error[name]."</font></td></tr>";
	echo "<tr><th colspan=3>Beschreibung</th></tr>
	<tr><td colspan=2 align=center><textarea cols=60 rows=15 name=desc>".strip_tags($_GET[desc])."</textarea></td><td valign=top>Text über die Allianz. Wer ihr seid, was ihr macht,etc<br>Kein HTML erlaubt</td></tr>";
	if ($error[desc]) echo "<tr><td colspan=3><font color=#ff0000>".$error[desc]."</font></td></tr>";
	echo "<tr><th>Homepage</th><td><input type=text size=40 name=homep class=text value=\"".strip_tags($_GET[homep])."\"></td><td>Seite der Allianz (optional)</td></tr>
	<tr><td colspan=3><input type=submit value=Gr&uuml;nden class=button></td></tr>
	</form>
	</table>";
}

if ($v == "allianzdetails")
{
	pageheader("/ <a href=?p=ally&s=ma>Allianzschirm</a> / <a href=?p=ally&s=al>Allianzliste</a> / <b>Allianzdetails</b> ".stripslashes($ally->data[name])."");
	echo "<table cellpadding=0 cellspacing=0>
	<tr>
	<td valign=top width=400>
	<table class=tcal>
	<tr><th>Präsident</th><td>".stripslashes($ally->data[user])."</td></tr>
	<tr><th>Vize</th><td>".($ally->data['vize_user_id'] > 0 ? stripslashes($db->query("SELECT user FROM stu_user WHERE id=".$ally->data['vize_user_id'],1)) : "Unbesetzt")."</td></tr>
	<tr><th>Außenminister</th><td>".($ally->data['auss_user_id'] > 0 ? stripslashes($db->query("SELECT user FROM stu_user WHERE id=".$ally->data['auss_user_id'],1)) : "Unbesetzt")."</td></tr>
	</table><br>
	<table class=tcal><th>Beschreibung</th>
	<tr><td>".nl2br(stripslashes($ally->data[descr]))."</td></tr></table><br>
	<table class=tcal>
	<th>Aktionen</th>
	<tr><td>".(strlen($ally->data[homepage]) > 0 ? "<a href=out.php?ai=".$_GET[id]." target=_blank>Homepage aufrufen</a><br>" : "")."
	<a href=?p=ally&s=al>Allianzliste anzeigen</a><br>
	<a href=\"?p=ally&s=ab&id=".$_GET[id]."\">Diplomatische Beziehungen</a></td></tr></table><br></td>
	<td width=5></td>
	<td width=400 valign=top>
	<table class=tcal>
	<th colspan=2>Mitglieder (".mysql_num_rows($ally->ar).")</th>";
	while($data=mysql_fetch_assoc($ally->ar)) echo "<tr><td><img src=".$gfx."/rassen/".($data['subrace'] ? $data['race']."_".$data['subrace'] : $data['race'])."s.gif> ".stripslashes($data['user'])."</td><td width=60><a href=\"javascript:void(0);\" onClick=\"opensi(".$data['id'].");\" ".getonm("id".$data['id'],"buttons/info")."><img src=".$gfx."/buttons/info1.gif name=id".$data['id']." border=0 title='Spielerprofil'></a> <a href=\"?p=comm&s=nn&recipient=".$data['id']."\" onmouseover=cp('ap".$data['id']."','buttons/msg2') onmouseout=cp('ap".$data['id']."','buttons/msg1')><img src=\"".$gfx."/buttons/msg1.gif\" name=\"ap".$data['id']."\" border=\"0\" title=\"PM an ".ftit($data['user'])." (".$data['id'].") schreiben\"></a>".($ap == 1 ? " <a href=?p=ally&a=dm&id=".$data['id']." onmouseover=cp('dl".$data['id']."','buttons/x2') onmouseout=cp('dl".$data['id']."','buttons/x1')><img src=".$gfx."/buttons/x1.gif title='".ftit($data['user'])."' name=dl".$data['id']." border=0></a>" : "")."</td></tr>";
	echo "</table>
	</td>
	</tr>
	</table>";
}
if ($v == "allianzoptionen")
{
	if ($_GET[a] == "eo" && $_GET["sent"] == 1 && $_GET[descr])
	{
		if ($_GET[name] && format_string($_GET['name']) != stripslashes($ally->data['name']))
		{
			if (strlen($_GET[name]) > 255) $error[name] = "Der Name darf maximal 255 Zeichen groß sein";
			if (!check_html_tags($_GET[name])) $error[name] = "Fehler bei den HTML-Tags";
			if (strlen(strip_tags($_GET[name])) < 6) $error[name] = "Der Name muss (ohne HTML-Tags) mindestens 6 Zeichen lang sein";
			if (!$error[name]) {
				$filter = new InputFilter(array("font","b","i"), array("color"), 0, 0);
				$_GET['name'] = format_string($filter->process($_GET['name']));
				$ea .= ",name='".str_replace("\"","",$_GET['name'])."'";
			}
		}
		if ($_GET[hp])
		{
			if (strlen(strip_tags($_GET[hp])) < 10) $error[hp] = "Die Adresse musst mindestens 10 Zeichen lang sein";
			if (!$error[hp]) $ea .= ",homepage='".format_string(strip_tags($_GET[hp]))."'";
		}
		$filter = new InputFilter(array("font","b","i","br","img","div"), array("color","src","align"), 0, 0);
		$desc = $filter->process($_GET['descr']);
		$db->query("UPDATE stu_allylist SET descr='".str_replace("style","",addslashes(strip_tags($desc,"<b></b><u></u><i></i><font></font><a></a><img><div></div>")))."'".$ea." WHERE allys_id=".$_SESSION['allys_id']);
	}
	$ally->loadally($_SESSION['allys_id']);
	if ($_GET[a] == "cp" && check_int($_GET[np]))
	{
		if ($_GET[np] == $_SESSION['uid']) die;
		if ($db->query("SELECT allys_id FROM stu_user WHERE id=".$_GET[np],1) != $_SESSION['allys_id']) die;
		if ($_GET[np] == $ally->data['vize_user_id']) $ea = ",vize_user_id=0";
		if ($_GET[np] == $ally->data['auss_user_id']) $ea = ",auss_user_id=0";
		$db->query("UPDATE stu_allylist SET praes_user_id=".$_GET[np].$ea." WHERE allys_id=".$_SESSION['allys_id']);
		$db->query("INSERT INTO stu_ally_kn (user_id,username,titel,text,date,allys_id) VALUES ('1','Niemand','Neuer Präsident','".addslashes($db->query("SELECT user FROM stu_user WHERE id=".$_GET[np],1))." wurde zum neuen Präsidenten ernannt',NOW(),'".$_SESSION['allys_id']."')");
		$ally->loadally($_SESSION['allys_id']);
	}
	if ($_GET[a] == "cvp" && check_int($_GET[np]))
	{
		if ($_GET[np] == $_SESSION['uid']) die;
		if ($db->query("SELECT allys_id FROM stu_user WHERE id=".$_GET[np],1) != $_SESSION['allys_id'] && $_GET[np] != 0) die;
		if ($_GET[np] == $ally->data['auss_user_id']) $ea = ",auss_user_id=0";
		$db->query("UPDATE stu_allylist SET vize_user_id=".$_GET[np].$ea." WHERE allys_id=".$_SESSION['allys_id']);
		if ($_GET[np] > 0) $db->query("INSERT INTO stu_ally_kn (user_id,username,titel,text,date,allys_id) VALUES ('1','Niemand','Neuer Vize-Präsident','".addslashes($db->query("SELECT user FROM stu_user WHERE id=".$_GET[np],1))." wurde zum neuen Vize-Präsidenten ernannt',NOW(),'".$_SESSION['allys_id']."')");
		$ally->loadally($_SESSION['allys_id']);
	}
	if ($_GET[a] == "cap" && check_int($_GET[np]))
	{
		if ($_GET[np] == $_SESSION['uid']) die;
		if ($_GET[np] == $ally->data['praes_user_id']) die;
		if ($db->query("SELECT allys_id FROM stu_user WHERE id=".$_GET[np],1) != $_SESSION['allys_id'] && $_GET[np] != 0) die;
		if ($_GET[np] == $ally->data['vize_user_id']) $ea = ",vize_user_id=0";
		$db->query("UPDATE stu_allylist SET auss_user_id=".$_GET[np].$ea." WHERE allys_id=".$_SESSION['allys_id']);
		if ($_GET[np] > 0) $db->query("INSERT INTO stu_ally_kn (user_id,username,titel,text,date,allys_id) VALUES ('1','Niemand','Neuer Außenmininster','".addslashes($db->query("SELECT user FROM stu_user WHERE id=".$_GET[np],1))." wurde zum neuen Außenminister ernannt',NOW(),'".$_SESSION['allys_id']."')");
		$ally->loadally($_SESSION['allys_id']);
	}
	pageheader("/ <a href=?p=ally>Allianzschirm (".stripslashes($ally->data[name]).")</a> / <b>Optionen</b>");
	if ($_GET[a] == "eo" && $_GET["sent"] == 1) meldung("Allianzdetails aktualisiert");
	if ($_GET[a] == "cp" && check_int($_GET[np]))
	{
		meldung("Präsidentschaft abgegeben");
		die;
	}
	if ($_GET[a] == "cvp" && check_int($_GET[np])) ($_GET[np] == 0 ? meldung("Der Posten wurde freigegeben") : meldung("Vize-Präsident wurde ernannt"));
	if ($_GET[a] == "cap" && check_int($_GET[np])) ($_GET[np] == 0 ? meldung("Der Posten wurde freigegeben") : meldung("Außenminister ernannt"));
	echo "<form action=\"main.php\" method=\"get\">
	<input type=\"hidden\" name=\"p\" value=\"ally\">
	<input type=\"hidden\" name=\"s\" value=\"ao\">
	<input type=\"hidden\" name=\"a\" value=\"eo\">
	<input type=\"hidden\" name=\"sent\" value=\"1\">
	<table class=\"tcal\" cellpadding=\"1\" cellspacing=\"1\">
	<tr>
		<td>Allianzname</td>
		<td><input type=\"text\" size=\"30\" name=\"name\" class=\"text\" value=\"".stripslashes($ally->data['name'])."\"></td>
		<td>Aktuell: ".stripslashes($ally->data['name'])."</td>
	</tr>";
	if ($error[name]) echo "<tr><td colspan=3><font color=#FF0000>".$error[name]."</font></td></tr>";
	echo "<tr>
		<td>Homepage</td>
		<td><input type=\"text\" size=\"40\" name=\"hp\" class=\"text\"></td>
		<td>Aktuell: ".stripslashes($ally->data[homepage])."</td>
	</tr>";
	if ($error[hp]) echo "<tr><td colspan=3><font color=#FF0000>".$error[hp]."</font></td></tr>";
	echo "
	<tr>
		<td colspan=\"3\">Beschreibung</td>
	</tr>
	<tr>
		<td colspan=\"3\"><textarea cols=\"100\" rows=\"15\" name=\"descr\">".stripslashes($ally->data[descr])."</textarea></td>
	</tr>
	<tr>
		<td colspan=\"3\"><input type=\"submit\" class=\"button\" value=\"Editieren\"></td>
	</tr>
	</table>
	</form><br>
	<table class=\"tcal\" cellpadding=\"1\" cellspacing=\"1\">";
	$sa = "<option value=0>---------------------------";
	while ($data=mysql_fetch_assoc($ally->ar)) if ($data[id] != $_SESSION['uid'] && $data[id] != $ally->data['praes_user_id']) $sa .= "<option value=\"".$data[id]."\"> ".stripslashes(strip_tags($data[user]));
	if ($ally->data['praes_user_id'] == $_SESSION['uid'])
	{
		echo "<form action=\"main.php\" method=\"get\">
		<input type=\"hidden\" name=\"p\" value=\"ally\">
		<input type=\"hidden\" name=\"s\" value=\"ao\">
		<input type=\"hidden\" name=\"a\" value=\"cp\">
		<tr>
			<td width=15%>Präsidentschaft an</td>
			<td width=40%><select name=\"np\">".$sa."</select> <input type=\"submit\" class=\"button\" value=\"abgeben\"></td>
			<td>&nbsp;</td>
		</tr>
		</form><form action=\"main.php\" method=\"get\">
		<input type=\"hidden\" name=\"p\" value=\"ally\">
		<input type=\"hidden\" name=\"s\" value=\"ao\">
		<input type=\"hidden\" name=\"a\" value=\"cvp\">
		<tr>
			<td>Vize-Präsidentschaft an</td>
			<td><select name=\"np\">".$sa."</select> <input type=\"submit\" class=\"button\" value=\"abgeben\"></td>
			<td>Aktuell: ".($ally->data['vize_user_id'] > 0 ? stripslashes($db->query("SELECT user FROM stu_user WHERE id=".$ally->data['vize_user_id'],1)) : "Nicht vorhanden")."</td>
		</tr>
		</form>";
	}
	echo "<form action=\"main.php\" method=\"get\">
	<input type=\"hidden\" name=\"p\" value=\"ally\">
	<input type=\"hidden\" name=\"s\" value=\"ao\">
	<input type=\"hidden\" name=\"a\" value=\"cap\">
	<tr>
		<td>Außenministerium an</td>
		<td><select name=\"np\">".$sa."</select> <input type=\"submit\" class=\"button\" value=\"abgeben\"></td>
		<td>Aktuell: ".($ally->data['auss_user_id'] > 0 ? stripslashes($db->query("SELECT user FROM stu_user WHERE id=".$ally->data['auss_user_id'],1)) : "Nicht vorhanden")."</td>
	</tr>
	</form>
	<tr><td colspan=\"3\"><a href=\"?p=ally&s=al&a=da\">Allianz löschen</a></td></tr>
	</table>";
}
if ($v == "allianzlöschen")
{
	pageheader("/ <a href=?p=ally>Allianzschirm (".stripslashes($ally->data[name]).")</a> / <b>Allianz verlassen</b>");
	if ($ally->data['praes_user_id'] == $_SESSION['uid'] && $ally->data['vize_user_id'] == 0) meldung("Du bist der Präsident der Allianz!<br>Da Du keinen Vize-Präsidenten ernannt hast, wird die Allianz gelöscht<br><a href=\"?p=ally&s=al&a=da\"><font color=\"#FF0000\">Allianz löschen</font></a>");
	elseif ($ally->data['praes_user_id'] == $_SESSION['uid'] && $ally->data['vize_user_id'] > 0)meldung("Du bist der Präsident der Allianz!<br>Die Präsidentschaft wird den den Vize-Präsidenten abgegeben<br><a href=\"?p=ally&s=al&a=la\"><font color=\"#FF0000\">Allianz verlassen</font></a>");
	else meldung("Willst Du die Allianz wirklich verlassen?<br><a href=\"?p=ally&s=al&a=la\"><font color=\"#FF0000\">Allianz verlassen</font></a>");
}
if ($v == "diplomatie")
{
	pageheader("/ <a href=?p=ally>Allianzschirm (".stripslashes($ally->data[name]).")</a> / <b>Diplomatie</b>");
	if ($_GET[a] == "cb" && is_array($_GET[ad]) && is_array($_GET[nr])) $result = $ally->editrelationships($_GET[ad],$_GET[nr]);
	if ($_GET[a] == "nr" && check_int($_GET[nr]) && check_int($_GET[rt]) && $_GET[rt] > 0 && $_GET[rt] < 5) $result = $ally->editrelationship($_GET[nr],$_GET[rt]);
	if ($_GET[a] == "tr" && check_int($_GET[aid])) $result = $ally->takerelationship($_GET[aid]);
	if ($_GET[a] == "do" && check_int($_GET[aid])) $result = $ally->deloffer($_GET[aid]);
	$ally->loadrelationships();
	if ($result) meldung($result);
	if (mysql_num_rows($ally->result) == 0) meldung ("Keine Beziehungen vorhanden");
	else
	{
		echo "<form action=main.php method=get><input type=hidden name=p value=ally><input type=hidden name=s value=dp>
		<input type=hidden name=a value=cb>
		<table class=tcal cellpadding=1 cellspacing=1>
		<tr><th>Beziehung mit</th><th>Art</th><th>seit</th><th>ändern</th></tr>";
		while($data=mysql_fetch_assoc($ally->result))
		{
			if ($data[date_tsp] == 0)
			{
				$da[] = $data;
				continue;
			}
			if ($data[type] == 1) $typ = "Krieg";
			if ($data[type] == 2) $typ = "Handelsabkommen";
			if ($data[type] == 3) $typ = "Freundschaftsvertrag";
			if ($data[type] == 4) $typ = "<a href=?p=ally&s=sai&id=".($data['allys_id1'] == $_SESSION['allys_id'] ? $data['allys_id2'] : $data['allys_id1']).">Bündnis</a>";
			echo "<input type=hidden name=ad[] value=".($data[allys_id1] == $_SESSION['allys_id'] ? $data[allys_id2] : $data[allys_id1])."><tr>
			<td>".($data[allys_id1] == $_SESSION['allys_id'] ? "<a href=?p=ally&s=de&id=".$data[allys_id2].">".stripslashes($data[name2])."</a>" : "<a href=?p=ally&s=de&id=".$data[allys_id1].">".stripslashes($data[name])."</a>")."</td>
			<td>".$typ."</td>
			<td>".date("d.m.Y",$data[date_tsp])."</td>
			<td><select name=nr[]>";
			if ($data[type] == 1) echo "<option value=bla>-------------------------<option value=5>Frieden anbieten";
			else echo "<option value=bla>-------------------------<option value=del>Löschen<option value=1>Krieg erklären<option value=2>Handelsabkommen anbieten<option value=3>Freundschaftsvertrag anbieten<option value=4>Bündnis anbieten";
			echo "</select></td></tr>";
		}
		echo "<tr><td colspan=4><input type=submit value=ändern class=button></td></tr></form>";
		if (is_array($da))
		{
			echo "<tr><th>Angebote an/von</th><th>Typ</th><th>Status</th><th>Aktionen</th></tr>";
			foreach($da as $key => $value)
			{
				if ($value['type'] == 2) $typ = "Handelsabkommen";
				if ($value['type'] == 3) $typ = "Freundschaftsvertrag";
				if ($value['type'] == 4) $typ = "Bündnis";
				if ($value['type'] == 5) $typ = "Frieden";
				if ($value['allys_id1'] == $_SESSION['allys_id'])
				{
					echo "<tr><td>".stripslashes($value['name2'])."</td>
					<td>".$typ."</td><td>Warte auf Antwort</td><td></td></tr>";
				}
				if ($value['allys_id2'] == $_SESSION['allys_id'])
				{
					echo "<tr><td>".stripslashes($value['name'])."</td>
					<td>".$typ."</td><td>angeboten</td><td><a href=?p=ally&s=dp&a=tr&aid=".$value['allys_id1']." ".getonm("tr".$value[allys_id1],"buttons/fergtrade")."><img src=".$gfx."/buttons/fergtrade1.gif border=0 name=tr".$value[allys_id1]." title=\"Angebot annehmen\"></a>&nbsp;<a href=?p=ally&s=dp&a=do&aid=".$value[allys_id1]." ".getonm("dr".$value[allys_id1],"buttons/x")."><img src=".$gfx."/buttons/x1.gif border=0 name=dr".$value[allys_id1]." title=\"Angebot ablehnen\"></a></td></tr>";
				}
			}
		}
	}
	echo "</table><br><table bgcolor=#262323 cellspacing=1 cellpadding=1 width=400>
	<th>Beziehung erstellen</th>
	<form action=main.php method=get><input type=hidden name=p value=ally><input type=hidden name=s value=dp><input type=hidden name=a value=nr>
	<tr><td>Allianz-ID <input type=text size=4 name=nr class=text> <select name=rt><option value=1>Krieg<option value=2>Handelsabkommen<option value=3>Freundschaftsvertrag<option value=4>Bündnis</select> <input type=submit value=erstellen class=button></td></tr></form></table>";
}
if ($v == "informationen")
{
	$sr = $db->query("SELECT a.rumps_id,a.name,COUNT(b.id) as sc FROM stu_rumps as a LEFT JOIN stu_ships as b USING(rumps_id) LEFT JOIN stu_user as c ON b.user_id=c.id WHERE c.allys_id=".$_SESSION["allys_id"]." GROUP BY a.rumps_id ORDER BY a.sort");
	$cr = $db->query("SELECT COUNT(a.id) as cc,a.colonies_classes_id,b.name FROM stu_colonies as a LEFT JOIN stu_colonies_classes as b USING(colonies_classes_id) LEFT JOIN stu_user as c ON a.user_id=c.id WHERE c.allys_id=".$_SESSION["allys_id"]." GROUP BY a.colonies_classes_id ORDER BY a.colonies_classes_id");
	$sys = $db->query("SELECT COUNT(a.id) as idc,a.systems_id,c.cx,c.cy,c.type,c.name FROM stu_colonies as a LEFT JOIN stu_user as b ON a.user_id=b.id AND b.allys_id=".$_SESSION["allys_id"]." LEFT JOIN stu_systems as c ON a.systems_id=c.systems_id WHERE b.allys_id=".$_SESSION["allys_id"]." GROUP BY a.systems_id");
	pageheader("/ <a href=?p=ally>Allianzschirm (".stripslashes($ally->data[name]).")</a> / <b>Informationen</b>");
	echo "<table cellpadding=0 cellspacing=0>
	<tr>
		<td width=350 valign=top>
		<table class=tcal>
		<tr><th colspan=3>Schiffe (<a href=\"?p=ally&s=dsw\">Detailansicht</a>)</th></tr>";
		while($data=mysql_fetch_assoc($sr)) echo "<tr><td><img src=".$gfx."/ships/".$data[rumps_id].".gif></td><td>".$data[name]."</td><td>".$data[sc]."</td></tr>";
		echo "</table>
		</td>
		<td width=5></td>
		<td width=350 valign=top>
		<table class=tcal>
		<tr><th colspan=3>Kolonien</th></tr>";
		while($data=mysql_fetch_assoc($cr)) echo "<tr><td style=\"width: 30px;\"><img src=".$gfx."/planets/".$data[colonies_classes_id].".gif></td><td>".$data[name]."</td><td>".$data[cc]."</td></tr>";
		echo "</table>
		</td>
		<td width=5></td>
		<td width=350 valign=top>
		<table class=tcal>
		<tr><th colspan=3>Kontrollierte Systeme</th></tr>";
		while($data=mysql_fetch_assoc($sys))
		{
			$cc = $db->query("SELECT COUNT(id) FROM stu_colonies WHERE systems_id=".$data[systems_id],1);
			$res = $db->query("SELECT a.colonies_classes_id,b.id,b.user FROM stu_colonies as a LEFT JOIN stu_user as b ON b.id=a.user_id WHERE a.systems_id=".$data[systems_id]." AND b.allys_id=".$_SESSION["allys_id"]);
			while($da=mysql_fetch_assoc($res)) $onm .= "<br><img src=".$gfx."/planets/".$da[colonies_classes_id].".gif width=15 height=15> ".ftit($da[user])." (".$da[id].")";
			echo "<tr><td><img src=".$gfx."/map/".$data[type].".gif width=15 height=15 onmouseover=\"return overlib('<div width=100%><b>Ansässig</b>".$onm."</div>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER);\" onmouseout=\"nd();\"> ".$data[name]."-System (".$data[cx]."|".$data[cy].") ".$data[idc]." (".round((100/$cc)*$data[idc])."%)</td></tr>";
			$onm = "";
		}
		echo "</table>
		</td>
	</tr>
	</table>";
}
if ($v == "beziehungen")
{
	pageheader("/ <a href=?p=ally>Allianzschirm</a> / <b>Beziehungen der Allianz ".$db->query("SELECT name FROM stu_allylist WHERE allys_id=".$_GET[id],1)."</b>");
	if (mysql_num_rows($ally->result) == 0) meldung("Keine Beziehungen vorhanden");
	else
	{
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><th></th><th>Datum</th>";
		while($data=mysql_fetch_array($ally->result))
		{
			switch($data[type])
			{
				case 1:
					$tx = "Krieg";
					break;
				case 2:
					$tx = "Handelsabkommen";
					break;
				case 3:
					$tx = "Freundschaftsvertrag";
					break;
				case 4:
					$tx = "Bündnis";
					break;
			}
			echo "<tr><td>".$tx." mit ".($_GET[id] == $data[allys_id1] ? "<a href=?p=ally&s=de&id=".$data[allys_id2].">".stripslashes($data[name2])."</a>" : "<a href=?p=ally&s=de&id=".$data[allys_id1].">".stripslashes($data[name])."</a>")."</td><td>".date("d.m.",$data[date_tsp]).setyear(date("Y",$data['date_tsp']))."</td></tr>";
		}
		echo "</table>";
	}
}
if ($v == "mitglieder")
{
	pageheader("/ <a href=?p=ally>Allianzschirm</a> / <b>Mitgliederverwaltung</b>");
	if ($_GET['a'] == "dm" && check_int($_GET['id']) && $_GET['id'] != $ally->data['praes_user_id'])
	{
		$result = $ally->delfromally($_GET['id']);
		$ally->get_member_data_by_id($_SESSION['allys_id']);
		if ($result) meldung($result);
	}
	echo "<table class=\"tcal\"><th colspan=\"2\" width=40></th><th>Name</th><th>Letzte Aktion</th><th>Kolonien</th><th>Schiffe</th><th>WP</th><th colspan=2 style=\"width: 40px;\"></th><th></th>";
	while($data=mysql_fetch_assoc($ally->ar))
	{
		$data['wirt'] = $db->query("SELECT SUM(lastrw) FROM stu_colonies WHERE user_id=".$data['id'],1);
		echo "<tr>
		<td><img src=".$gfx."/buttons/".($data['lastaction'] > time()-300 ? "alert1" : "alert3").".gif></td>
		<td><img src=".$gfx."/rassen/".($data['subrace'] > 0 ? $data['race']."_".$data['subrace'] : $data['race'])."s.gif></td>
		<td>".stripslashes($data['user'])." (".$data['id'].")".($data['vac_active'] == 1 ? " <font color=Yellow>*</font>" : "")."</td>
		<td>".date("d.m.Y H:i",$data['lastaction'])."</td>
		<td>".$data['cols']."</td>
		<td>".$data['ships']."</td>
		<td>".$data['wirt']."</td>
		<td><a href=\"javascript:void(0);\" onClick=\"opensi(".$data['id'].");\" ".getonm('si'.$data['id'],'buttons/info')."><img src=".$gfx."/buttons/info1.gif name=si".$data['id']." border=0 title=\"Siedlerinfo aufrufen\"></a></td>
		<td><a href=?p=comm&s=nn&recipient=".$data['id']." ".getonm('pm'.$data['id'],'buttons/msg')."><img src=".$gfx."/buttons/msg1.gif name=pm".$data['id']." border=0 title=\"PM an ".ftit($data['user'])." senden\"</td>
		<td style=\"width: 40px; text-align: right;\"><a href=?p=ally&s=am&a=dm&id=".$data['id']." ".getonm('dl'.$data['id'],'buttons/x')."><img src=".$gfx."/buttons/x1.gif title='".ftit($data['user'])." rauswerfen' name=dl".$data[id]." border=0></a></td></tr>";
		$cols += $data['cols'];
		$ships += $data['ships'];
		$wirt += $data['wirt'];
	}
	$memc = mysql_num_rows($ally->ar);
	echo "<tr>
	<td colspan=4>Mitglieder: ".$memc."</td>
	<td>= ".$cols." (".round($cols/$memc,2).")</td>
	<td>= ".$ships." (".round($ships/$memc,2).")</td>
	<td>= ".$wirt." (".round($wirt/$memc,2).")</td>
	<td colspan=3></td>
	</tr></table>";
}
if ($v == "bndinfo")
{
	pageheader("/ <a href=?p=ally>Allianzschirm</a> / <b>Informationen über die Allianz ".stripslashes($ally->data['name'])."</b>");
	echo "<table class=\"tcal\"><th colspan=\"2\" width=40></th><th>Name</th><th>Letzte Aktion</th><th>Kolonien</th><th>Schiffe</th><th>WP</th><th colspan=2 style=\"width: 40px;\"></th>";
	while($data=mysql_fetch_assoc($ally->ar))
	{
		$data['wirt'] = $db->query("SELECT SUM(lastrw) FROM stu_colonies WHERE user_id=".$data['id'],1);
		echo "<tr>
		<td><img src=".$gfx."/buttons/".($data['lastaction'] > time()-500 ? "alert1" : "alert3").".gif></td>
		<td><img src=".$gfx."/rassen/".$data['race']."s.gif></td>
		<td>".stripslashes($data['user'])." (".$data['id'].")".($data['vac_active'] == 1 ? " <font color=Yellow>*</font>" : "")."</td>
		<td>".date("d.m.Y H:i",$data['lastaction'])."</td>
		<td>".$data['cols']."</td>
		<td>".$data['ships']."</td>
		<td>".$data['wirt']."</td>
		<td><a href=\"javascript:void(0);\" onClick=\"opensi(".$data['id'].");\" ".getonm('si'.$data['id'],'buttons/info')."><img src=".$gfx."/buttons/info1.gif name=si".$data['id']." border=0 title=\"Siedlerinfo aufrufen\"></a></td>
		<td><a href=?p=comm&s=nn&recipient=".$data['id']." ".getonm('pm'.$data['id'],'buttons/msg')."><img src=".$gfx."/buttons/msg1.gif name=pm".$data['id']." border=0 title=\"PM an ".ftit($data['user'])." senden\"</td>
		</tr>";
		$cols += $data['cols'];
		$ships += $data['ships'];
		$wirt += $data['wirt'];
	}
	$memc = mysql_num_rows($ally->ar);
	echo "<tr>
	<td colspan=4>Mitglieder: ".$memc."</td>
	<td>= ".$cols." (".round($cols/$memc,2).")</td>
	<td>= ".$ships." (".round($ships/$memc,2).")</td>
	<td>= ".$wirt." (".round($wirt/$memc,2).")</td>
	<td colspan=2></td>
	</tr></table>";
}
if ($v == "detailshipview")
{
	pageheader("/ <a href=?p=ally>Allianzschirm</a> / <a href=\"?p=ally&s=in\">Informationen über die Allianz ".stripslashes($ally->data['name'])."</a> / <b>Detailansicht (Schiffe)</b>");
	$result = $ally->getallyshipsdetails();
	echo "<script language=\"Javascript\">
	got = 0;
	function showMap()
	{
		if (got == 1)
		{
			document.getElementById(elt).innerHTML = '<img src=\"backend/ally/shipmap.php\" />';
			return;
		}
		got = 1;
		elt = 'shipposition';
		return overlib('<div id=shipposition onClick=\"cl_win();\"><img src=\"backend/ally/shipmap.php\" /></div>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, EXCLUSIVE, ABOVE, LEFT, STICKY, DRAGGABLE, ALTCUT, WIDTH, 485);
	}
	function showPosition(cx,cy)
	{
		if (got == 1)
		{
			document.getElementById(elt).innerHTML = '<img src=\"backend/ally/shipmap.php?cx='+ cx +'&cy='+ cy +'\" />';
			return;
		}
		got = 1;
		elt = 'shipposition';
		return overlib('<div id=shipposition onClick=\"cl_win();\"><img src=\"backend/ally/shipmap.php?cx='+ cx +'&cy='+ cy +'\" /></div>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, EXCLUSIVE, ABOVE, LEFT, STICKY, DRAGGABLE, ALTCUT, WIDTH, 485);
	}
	function cl_win()
	{
		got = 0;
		cClick();
	}
	</script>
	<table bgcolor=#262323 cellspacing=1 cellpadding=1>
	<tr><td>Schiffe vorhanden: ".mysql_num_rows($result)."<br />
	<a href=\"javascript:void(0);\" onClick=\"showMap();\">Gesamtansicht</a></td></tr>
	</table><br />
	<table bgcolor=#262323 cellspacing=1 cellpadding=1>
	<th></th><th>Status</th><th>Name</th><th>Siedler</th><th>Koordinaten</th><th>System</th><th></th>";
	$lf = 0;
	$sl = 0;
	while($data = mysql_fetch_assoc($result))
	{
		if ($lf != $data['fleets_id'] && $data['fleets_id'] > 0)
		{
			echo "<tr><th colspan=\"7\">Flotte: ".stripslashes($db->query("SELECT name FROM stu_fleets WHERE fleets_id=".$data['fleets_id']."",1))."</th></tr>";
			$lf = $data['fleets_id'];
		}
		if ($sl == 0 && $data['slots'] > 0)
		{
			echo "</tr><th colspan=\"7\">Stationen</th></tr>";
			$sl = $data['slots'];
		}
		if ($lf != $data['fleets_id'] && $data['fleets_id'] == 0 && $data['slots'] == 0)
		{
			echo "</tr><th colspan=\"7\">Einzelschiffe</th></tr>";
			$lf = $data['fleets_id'];
		}
		echo "<tr>
			<td><img src=".$gfx."/ships/".$data['rumps_id'].".gif></td>
			<td>".renderhuellstatusbar($data['huelle'],$data['max_huelle'])."<br/>".rendershieldstatusbar($data['schilde_status'],$data['schilde'],$data['max_schilde'])."<br />".renderepsstatusbar($data['eps'],$data['max_eps'])."</td>
			<td>".stripslashes($data['name'])."</td>
			<td>".stripslashes($data['user'])." (".$data['user_id'].") ".($data['vac_active'] == 1 ? " <font color=Yellow>*</font>" : "")."</td>
			<td style=\"text-align: center;\">".$data['cx']."|".$data['cy']."</td>
			<td>".($data['stype'] ? "<img src=".$gfx."/map/".$data['stype'].".gif width=10 height=10> ".stripslashes($data['sname'])."-System ".$data['sx']."|".$data['sy'] : "")."</td>
			<td><a href=\"javascript:void(0);\" onClick=\"showPosition(".$data['cx'].",".$data['cy'].");\">anzeigen</a></td>
		</tr>";
	}
	echo "</table>";
}
?>
