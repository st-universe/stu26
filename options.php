<?php
if (!is_object($db)) exit;

switch($_GET['s'])
{
	case "op":
		$v = "options";
		break;
	case "pr":
		$v = "profile";
		break;
	default:
		$v = "options";
}

if ($v == "options")
{
	echo "<script language=\"Javascript\">
	function profilePage()
	{
		window.location.href = 'http://www.stuniverse.de/main.php?p=opt&s=pr';
	}
	</script>";
	pageheader("/ <b>Einstellungen</b>");
	if ($_GET["sent"] == 1)
	{
		if ($_GET["uname"] != "" && stripslashes($_GET["uname"]) != $_SESSION["user"])
		{
			$vl = addslashes(format_string(str_replace("\"","",$_GET["uname"])));
			if (!check_html_tags($vl))
			{
				$chg[] = "Die HTML-Tags beim Usernamen wurden nicht korrekt gesetzt. HTML wurde gefiltert";
				$vl = strip_tags($vl);
			}
			if (strlen($value > 255))
			{
				$chg[] = "Der Name darf mitsamt HTML-Tags nur 255 Zeichen lang sein. HTML wurde gefiltert";
				$vl = strip_tags($value);
			}
			$filter = new InputFilter(array("font","b","i"), array("color"), 0, 0);
			$vl = $filter->process($vl);
			$vl = str_replace("\"","",$vl);
			$u->updatedata("user",$vl);
			$u->updatedata("search_user",strip_tags(str_replace("'","",stripslashes($vl))));
			$_SESSION["user"] = stripslashes($vl);
			$chg[] = "Spielername geändert";
		}
		if ($_GET["pass"] != "" && $_GET["pass2"] != "" && $_GET["pass2"] != $_GET["pass"]) $chg[] = "Die Passwörter stimmen nicht überein";
		if ($_GET["pass"] != "" && $_GET["pass2"] != "" && $_GET["pass2"] == $_GET["pass"])
		{
			$vl = str_replace("\"","",str_replace("'","",$_GET["pass"]));
			$vl = strip_tags($vl);
			if (strlen($vl) < 6) $chg[] = "Das neue Passwort muss aus mindestens 6 Zeichen bestehen";
			else
			{
				$u->updatedata("pass",md5($vl));
				$chg[] = "Das Passwort wurde geändert";
			}
		}
		if ($_GET["propi"] != $_SESSION["propic"])
		{
			$vl = str_replace("\"","",str_replace("'","",$_GET["propi"]));
			$vl = strip_tags($vl);
			$u->updatedata("propic",$vl);
			$_SESSION["propic"] = $vl;
			$chg[] = "Das Profilbild wurde geändert";
		}
		if ($_GET["sk"] != "" && $_GET["sk"] != $_SESSION["skin"] && !$_GET["sa"] && !$_GET["sr"])
		{
			if ($_GET["sk"] == "Rassenskin" && $_SESSION["skin"] != $_SESSION["race"])
			{
				$u->updatedata("skin",$_SESSION["race"]);
				$chg[] = "Skin geändert";
				if ($_SESSION['skin'] == 10) $_SESSION['gfx_path'] = $db->query("SELECT gfx_path FROM stu_user WHERE id=".$_SESSION['uid']." LIMIT 1",1);
				$_SESSION["skin"] = $_SESSION["race"];
			}
			if ($_GET["sk"] > 5 && $_GET["sk"] < 10)
			{
				$u->updatedata("skin",$_GET["sk"]);
				$chg[] = "Skin geändert";
				if ($_SESSION['skin'] == 10) $_SESSION['gfx_path'] = $db->query("SELECT gfx_path FROM stu_user WHERE id=".$_SESSION['uid']." LIMIT 1",1);
				$_SESSION["skin"] = $_GET["sk"];
			}
		}


		if (stripslashes($_GET["rpgte"]) != stripslashes($_SESSION["description"]))
		{
			$vl =addslashes(str_replace("style","",strip_tags($_GET["rpgte"],"<font></font><b></b><i></i><u></u><img><div></div>")));
			$filter = new InputFilter(array("font","b","i","u","br","img"), array("color","src"), 0, 0);
			$vl = $filter->process($vl);
			$u->updateprofile("description",$vl);
			$_SESSION["description"] = stripslashes($vl);
			$chg[] = "Beschreibung geändert";
		}
		if ($_GET[ema] == 1 && $_SESSION["email_not"] == 0)
		{
			$u->updatedata("email_not",1);
			$chg[] = "Emailbenachrichtigung aktiviert";
			$_SESSION["email_not"] = 1;
		}
		if ($_GET[ema] == "" && $_SESSION["email_not"] == 1)
		{
			$u->updatedata("email_not",0);
			$chg[] = "Emailbenachrichtigung deaktiviert";
			$_SESSION["email_not"] = 0;
		}
		if ($_GET['lav'] == 1 && $_SESSION['lav_not'] == 0)
		{
			$u->updatedata("lav_not",1);
			$chg[] = "Lagerbenachrichtigung aktiviert";
			$_SESSION['lav_not'] = 1;
		}
		if ($_GET['lav'] == "" && $_SESSION['lav_not'] == 1)
		{
			$u->updatedata("lav_not",0);
			$chg[] = "Lagerbenachrichtigung deaktiviert";
			$_SESSION['lav_not'] = 0;
		}
		
		if ($_GET['stars'] == 1 && $_SESSION['disable_background'] == 0)
		{
			$u->updatedata("disable_background",1);
			$chg[] = "Hintergrundbild deaktiviert";
			$_SESSION['disable_background'] = 1;
		}
		if ($_GET['stars'] == "" && $_SESSION['disable_background'] == 1)
		{
			$u->updatedata("disable_background",0);
			$chg[] = "Hintergrundbild aktiviert";
			$_SESSION['disable_background'] = 0;
		}		
	}

	if ($_GET[delmark] == 1)
	{
		$u->senddelmail();
		$chg[] = "Eine Accountlöschung wurde angefordert.<br>Du erhälst in Kürze eine Email um die Löschung zu bestätigen. Die Mail ist bis zum nächsten Tick gültig.";
	}
	// if ($_GET[delfo] == 1)
	// {
		// $u->delforschungen();
		// $chg[] = "Alle Forschungen wurden zurückgesetzt";
	// }
	if (is_array($chg))
	{
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1>
		<th>Meldung</th>
		<tr>
		<td>";
		foreach($chg as $key) echo $key."<br>";
		echo "</td>
		</tr></table><br>";
	}
	?>
	<script language="Javascript">
	function confirmreset()
	{
		document.getElementById("rfo").innerHTML = "<br><table bgcolor=#262323 cellspacing=1 cellpadding=1 width=400><th>Forschungen zurücksetzen</th><tr><td>Willst du wirklich alle Forschungen resetten? <a href=?p=opt&delfo=1 style='color: #FF0000;'>Ja</a></td></tr></table>";
	}
	</script>
	<?php
	echo "<form action=main.php method=post>
	<input type=hidden name=p value=opt><input type=hidden name=sent value=1><input type=hidden name=op value=1>
	<table class=tcal>
	<tr>
		<td>Spielername</td>
		<td><input type=text size=30 name=uname value=\"".stripslashes($_SESSION["user"])."\" class=text maxlength=255></td>
		<td>Aktuell: ".stripslashes($_SESSION["user"])." (HTML font,b,i / max 255 Zeichen)</td>
	</tr>
	<tr>
		<td>Passwort</td>
		<td><input type=password size=10 name=pass class=text></td>
		<td>wiederholen <input type=password size=10 name=pass2 class=text></td>
	</tr>
	<tr>
		<td>Profil-Bild</td>
		<td><input type=text size=40 name=propi value=\"".$_SESSION["propic"]."\" class=text></td>
		<td>URL zu deinem Profilbild (u.a. für das KN / 100px*100px)</td>
	</tr>";
	if ($_SESSION['skin'] != 6) {
		echo "<tr>
			<td>Skin</td>
			<td><select name=sk><option value=6>Standard<option value=Rassenskin>Rassenskin</select> <input type=submit name=sb value=ändern class=button></td>
			<td><font color=red>Warnung: Dieses feature wird nicht mehr unterstützt. Wechseln ist nur noch auf standard-skin möglich.</font></td>
		</tr>";
	}
	echo "<tr>
		<td>Benachrichtigung</td>
		<td><input type=checkbox name=ema value=1 ".($_SESSION["email_not"] == 1 ? "CHECKED" : "")."></td>
		<td>Emailbenachrichtigung bei privaten (!) PMs</td>
	</tr>
	<tr>
		<td>Lager-Meldung</td>
		<td><input type=checkbox name=lav value=1 ".($_SESSION['lav_not'] == 1 ? "CHECKED" : "")."></td>
		<td>PM bei vollen Lagern zum Tick
	</tr>
	<tr>
		<td>Hintergrundbild deaktivieren</td>
		<td><input type=checkbox name=stars value=1 ".($_SESSION['disable_background'] == 1 ? "CHECKED" : "")."></td>
		<td>Sternen-Hintergrundbild nicht mehr anzeigen
	</tr>	
	<tr>
		<td>Beschreibung</td>
		<td colspan=2>Hier kannst Du Informationen zu Deinem RPG Charakter oder sonstige (öffentliche) Notizen unterbringen</td>
	</tr>
	<tr>
		<td colspan=3><textarea name=rpgte rows=20 cols=90>".stripslashes($_SESSION["description"])."</textarea></td>
	</tr>
	</table><br>
	<input type=submit value=Ändern name=sa class=button>&nbsp;&nbsp;<input type=reset value=Reset class=button>&nbsp;&nbsp;<input type=\"button\" class=\"button\" value=\"Profil ansehen\" onClick=\"profilePage();\"></form>
	<table class=tcal>
	<form action=main.php method=get>
	<input type=hidden name=p value=opt><input type=hidden name=delmark value=1>
	<tr>
		<td>Accountlöschung</td><td><input type=submit class=button value=Löschen></td><td>Um eine Löschung durchzuführen muss diese bestätigt werden. Dies geschieht via eines Links in einer Email</td>
	</tr>
	</form>
	<form action=main.php method=get>
	<input type=hidden name=p value=opt><input type=hidden name=vac value=1>
	<tr>
		<td>Urlaubsmodus</td><td><input type=submit class=button value=Aktivieren></td><td>Noch ".(!check_int($_SESSION[vac_possible]) ? 0 : $_SESSION[vac_possible])." mal aktivierbar. <a href=http://wiki.stuniverse.de/index.php/Urlaubsmodus target=_blank>Wiki-Artikel</a> zum Urlaubsmodus</td>
	</tr>
	</table><br>
	</form>
	<div id=\"rfo\"></div>";
}
if ($v == "profile")
{
	pageheader("/ <a href=?p=opt>Einstellungen</a> / <b>Profilvorschau</b>");
	$data = $db->query("SELECT a.user,UNIX_TIMESTAMP(a.lastaction) as la,a.race,a.propic,a.allys_id,b.name as aname,c.description,c.icq FROM stu_user as a LEFT JOIN stu_allylist as b USING(allys_id) LEFT JOIN stu_user_profiles as c ON a.id=c.user_id WHERE a.id=".$_SESSION['uid']." LIMIT 1",4);
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=700>
	<th>Avatar</th><th>Infos</th><th>Auszeichnungen</th>
	<tr>
	<td rowspan=2 width=70 align=center valign=top>".(strlen($data[propic])>10 ? "<img src=".$data[propic]." width=100 height=100>" : "<img src=".$gfx."/rassen/".$data[race]."kn.gif>")."</td>
	<td>".stripslashes($data['user'])." (".$_SESSION['uid'].")</td>
	<td rowspan=2 width=40 align=center valign=top>";
	$result = $db->query("SELECT award_id FROM stu_user_awards WHERE user_id=".$_SESSION['uid']);
	if (mysql_num_rows($result) == 0) echo "Keine";
	else while($dat=mysql_fetch_assoc($result)) echo "<img src=gfx/awards/".$dat['award_id'].".gif title=\"".getawardname($dat['award_id'])."\"><br><br>";
	echo "</td>
	</tr>
	<tr>
	<td>Allianz: ".($data['allys_id'] == 0 ? "Keine" : stripslashes($data['aname']))."</td>
	</tr>
	<tr><td colspan=3>".nl2br(stripslashes($data[description]))."</td></tr>
	</table>";
}
?>
