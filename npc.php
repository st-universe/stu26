<?php
if (!is_object($db)) exit;
switch($_GET['s'])
{
	case "cl":
		$v = "contactlist";
		break;
	case "gs":
		$v = "getship";
		break;
	case "ss":
		$v = "showships";
		break;
	case "srkn":
		$v = "showrkn";
		break;
	default:
		$v = "main";
}
if ($_SESSION["npc_type"] == 3) $v = "sl";

if ($v == "main")
{
	pageheader("/ NPC-Menü");
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=300>
	<tr>
	<td>- <a href=?p=npc&s=ss>RPG Schiffe anzeigen</a><br />
	- <a href=?p=npc&s=cl>Kontaktliste</a><br>";
	if ($_SESSION['uid'] <= 14 && $_SESSION['uid'] >= 10) echo "- <a href=?p=npc&s=srkn>RKN-Sticky bearbeiten</a><br />";
	if ($_SESSION["npc_type"] == 2) echo "- <a href=?p=npc&s=gs>Schiff erstellen</a></td>";
	echo "</tr>
	</table>";
}
if ($v == "getship")
{
	pageheader("/ NPC-Menü / <b>Schiff erstellen</b>");
	if ($_GET[a] == "es" && $_SESSION[npc_type] == 2) $result = $npc->getship($_GET[pid],$_GET[cx],$_GET[cy]);
	$npc->loadbuildplans();
	if ($result) meldung($result);
	echo "<table class=tcal>
	<th colspan=5>Verfügbare Baupläne</th>
	<form action=main.php method=get><input type=hidden name=p value=npc><input type=hidden name=s value=gs><input type=hidden name=a value=es><input type=hidden name=pid value=-1>
	<tr>
		<td><img src=../gfx/ships/9.gif></td>
		<td>Konstrukt</td>
		<td>NPC: ";
		if ($_SESSION[npc_type] == 3)
		{
			echo "<select name=uid>";
			$result = $db->query("SELECT id,user FROM stu_user WHERE npc_typ='1' ORDER BY id");
			while($dat=mysql_fetch_assoc($result)) echo "<option value=".$dat[id].">".$dat[user];
			echo "</select>";
		}
		else echo $_SESSION[user];
	echo "</td><td>x|y: <input type=text size=3 class=text name=cx>|<input type=text class=text size=3 name=cy></td><td><input type=submit class=button value=Erstellen></td></tr></form>
	</tr>";
	while($data=mysql_fetch_assoc($npc->result))
	{
		echo "<form action=main.php method=get><input type=hidden name=p value=npc><input type=hidden name=s value=gs><input type=hidden name=a value=es><input type=hidden name=pid value=".$data[plans_id].">
		<tr><td><img src=../gfx/ships/".$data[rumps_id].".gif></td><td>".$data[name]."<br>";
		$i = 1;
		while($i<=11)
		{
			if ($data["m".$i] == 0)
			{
				$i++;
				continue;
			}
			echo "<img src=".$gfx."/goods/".$data["m".$i].".gif>&nbsp;";
			$i++;
		}
		echo "</td><td>NPC: ";
		if ($_SESSION[npc_type] == 3)
		{
			echo "<select name=uid>";
			$result = $db->query("SELECT id,user FROM stu_user WHERE npc_typ='1' ORDER BY id");
			while($dat=mysql_fetch_assoc($result)) echo "<option value=".$dat[id].">".$dat[user];
			echo "</select>";
		}
		else echo $_SESSION[user];
		echo "</td><td>x|y: <input type=text size=3 class=text name=cx>|<input type=text class=text size=3 name=cy></td><td><input type=submit class=button value=Erstellen></td></tr></form>";
	}
	echo "</table>";
}
if ($v == "sl")
{
	pageheader("/ NPC-Menü / <b>Spielleiter</b>");
	if (!$_GET["uid"])
	{
		echo "<b>NPC auswählen</b><br><br>";
		$result = $db->query("SELECT id,user FROM stu_user WHERE npc_type='1' OR npc_type='2' OR npc_type='3'");
		while($data=mysql_fetch_assoc($result)) echo "<a href=?p=npc&uid=".$data[id].">".$data[user]."</a><br>";
	}
	else
	{
		if ($_GET['a'] == "es" && $_SESSION[npc_type] == 3) $result = $npc->getship($_GET[pid],$_GET[cx],$_GET[cy],$_GET[uid]);
		if ($_GET['a'] == "gg" && $_SESSION[npc_type] == 3 && $_GET['goods'] && $_GET['count']) $result = $npc->addgoods($_GET['goods'],$_GET['count'],$_GET[uid]);
		if ($result) meldung($result);
		$result = $db->query("SELECT a.plans_id,a.rumps_id,a.name,a.m1,a.m2,a.m3,a.m4,a.m5,a.m6,a.m7,a.m8,a.m9,a.m10,a.m11,COUNT(b.id)as idc FROM stu_ships_buildplans as a LEFT JOIN stu_ships as b ON a.plans_id=b.plans_id AND b.user_id=".$_GET[uid]." LEFT JOIN stu_rumps as c ON c.rumps_id=a.rumps_id WHERE a.user_id=".$_GET[uid]." GROUP BY a.plans_id ORDER BY a.rumps_id,a.plans_id");
		echo "<table class=tcal>
		<th colspan=5>Verfügbare Baupläne</th>
		<form action=main.php method=get><input type=hidden name=p value=npc><input type=hidden name=a value=es><input type=hidden name=pid value=-1><input type=hidden name=uid value=".$_GET[uid].">
		<tr>
			<td><img src=../gfx/ships/9.gif></td>
			<td>Konstrukt</td>
			<td>NPC: ".$db->query("SELECT user FROM stu_user WHERE id=".$_GET['uid'],1)."</td><td>x|y: <input type=text size=3 class=text name=cx>|<input type=text class=text size=3 name=cy></td><td><input type=submit class=button value=Erstellen></td></tr></form>";
		while($data=mysql_fetch_assoc($result))
		{
			echo "<form action=main.php method=get><input type=hidden name=p value=npc><input type=hidden name=a value=es><input type=hidden name=pid value=".$data[plans_id]."><input type=hidden name=uid value=".$_GET[uid].">
			<tr><td><img src=../gfx/ships/".$data[rumps_id].".gif></td><td>".$data[name]."<br>";
			$i = 1;
			while($i<=11)
			{
				if ($data["m".$i] == 0)
				{
					$i++;
					continue;
				}
				echo "<img src=".$gfx."/goods/".$data["m".$i].".gif>&nbsp;";
				$i++;
			}
			echo "</td><td>NPC: ".$db->query("SELECT user FROM stu_user WHERE id=".$_GET['uid'],1)."</td><td>x|y: <input type=text size=3 class=text name=cx>|<input type=text class=text size=3 name=cy></td><td><input type=submit class=button value=Erstellen></td></tr></form>";
		}
		echo "</table><br>";
		$result = $db->query("SELECT * FROM stu_goods ORDER BY sort");
		echo "<form action=main.php method=get><input type=hidden name=p value=npc><input type=hidden name=a value=gg><input type=hidden name=uid value=".$_GET['uid'].">
		<table class=tcal>
		<th colspan=16>Waren erstellen</th><tr>
		<tr><td><select name=goods>";
		while($data=mysql_fetch_assoc($result))
		{
			echo "<option value=".$data['goods_id'].">".stripslashes($data['name'])."</option>";
		}
		echo "</select> <input type=text size=5 name=count></td></tr><tr><td colspan=16><input type=submit class=button value=Erstellen></td></tr></table></form><br /><br />
		<table class=tcal><th colspan=5>Schiffe des NPCs</th>
		<tr>
			<td></td><td>Name</td><td>H&uuml;lle</td><td>Schilde</td><td>Energie</td>
		</tr>";
		$result = $db->query("SELECT a.id,a.rumps_id,a.name,a.huelle,a.max_huelle,a.schilde,a.max_schilde,a.schilde_status,a.eps,a.max_eps FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) WHERE a.user_id=".$_GET['uid']." ORDER BY b.slots DESC,a.fleets_id DESC,a.id DESC");	
		while ($data=mysql_fetch_assoc($result))
		{
			echo "<tr>
				<td><img src=".$gfx."/ships/".$data['rumps_id'].".gif></td>
				<td>".stripslashes($data['name'])." (".$data['id'].")</td>
				<td>".$data['huelle']."/".$data['max_huelle']."</td>
				<td>".($data['schilde_status'] == 1 ? "<font color=cyan>".$data['schilde']."/".$data['max_schilde']."</font>" : $data['schilde']."/".$data['max_schilde'])."</td>
				<td>".$data['eps']."/".$data['max_eps']."</td>
			</tr>";
		}
		echo "</table>";
	}
}
if ($v == "contactlist")
{
	pageheader("/ NPC-Menü / <b>Kontaktlisten</b>");
	if ($_GET["sent"] == 1)
	{
		if (is_array($_GET["os"]))
		{
			foreach($_GET["os"] as $key => $value)
			{
				if ($_GET["de"][$key] == 1 && is_numeric($key) && $key > 0)
				{
					$npc->delcontact($key);
					$t .= "Beziehung zu ID ".$key." wurde gelöscht<br>";
					$key = 0;
				}
				else
				{
					$_GET['rkn'][$key] == "on" ? $_GET['rkn'][$key] = 1 : $_GET['rkn'][$key] = "";
					$_GET['hp'][$key] == "on" ? $_GET['hp'][$key] = 1 : $_GET['hp'][$key] = "";
					$npc->changecontact($key,$_GET['rkn'][$key],$_GET['hp'][$key]);
				}
			}
		}
		if (check_int($_GET["nc"])) $npc->setcontact($_GET["nc"]);
	}
	if ($t) meldung($t);
	echo "<form action=main.php method=get><input type=hidden name=p value=npc><input type=hidden name=s value=cl><input type=hidden name=sent value=1>
	<table bgcolor=#262323 cellspacing=1 cellpadding=1>
	<tr><th>Kontakt hinzufügen</th></tr>
	<tr><td>User-ID <input type=text name=nc class=text size=4> <input type=submit value=Hinzufügen class=button></td></tr></table></form>";
	$cl = $npc->getcontacts();
	if (mysql_num_rows($cl) == 0) meldung("Keine Kontakte vorhanden");
	else
	{
		echo "<form action=main.php method=get><input type=hidden name=p value=npc><input type=hidden name=s value=cl><input type=hidden name=sent value=1>
		<table bgcolor=#262323 cellspacing=1 cellpadding=1>
		<tr><th>Siedler</th><th>RKN</th><th>HP-Verbot</th><th>Löschen</th><th>RKN-P</th></tr>";
		while($d=mysql_fetch_assoc($cl))
		{
			echo "<input type=hidden name=os[".$d[recipient]."] value=".$d['rkn']."><tr><td><img src=".$gfx."/rassen/".$d['race']."s.gif> ".stripslashes($d['user'])." (".$d['recipient'].") ".($d['vac_active'] == 1 ? " <font color=Yellow>*</font>" : "")."</td><td><input type=checkbox name=rkn[".$d['recipient']."]".($d['rkn'] == 1 ? " CHECKED" : "")."></td><td><input type=checkbox name=hp[".$d['recipient']."]".($d['deny_hp'] == 1 ? " CHECKED" : "")."></td><td align=center><input type=checkbox name=de[".$d[recipient]."] value=1></td><td>".(!$d['rid'] ? 0 : $d['rid'])."</td></tr>";
		}
		echo "<tr><td colspan=5><input type=submit class=button value=Ändern> <input type=reset value=Reset class=button></td></tr></table>";
	}
}
if ($v == "showships")
{
	pageheader("/ NPC-Menü / <b>RPG Schiffe</b>");
	$result = $npc->getrpgships();
	echo "<script language=\"Javascript\">
	got = 0;
	function showMap()
	{
		if (got == 1)
		{
			document.getElementById(elt).innerHTML = '<img src=\"backend/npcmap.php\" />';
			return;
		}
		got = 1;
		elt = 'shipposition';
		return overlib('<div id=shipposition onClick=\"cl_win();\"><img src=\"backend/npcmap.php\" /></div>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, EXCLUSIVE, ABOVE, LEFT, STICKY, DRAGGABLE, ALTCUT, WIDTH, 485);
	}
	function showPosition(cx,cy)
	{
		if (got == 1)
		{
			document.getElementById(elt).innerHTML = '<img src=\"backend/npcmap.php?cx='+ cx +'&cy='+ cy +'\" />';
			return;
		}
		got = 1;
		elt = 'shipposition';
		return overlib('<div id=shipposition onClick=\"cl_win();\"><img src=\"backend/npcmap.php?cx='+ cx +'&cy='+ cy +'\" /></div>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, EXCLUSIVE, ABOVE, LEFT, STICKY, DRAGGABLE, ALTCUT, WIDTH, 485);
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
			echo "<tr><th colspan=\"7\">Flotte</th></tr>";
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
			<td>".$data['cx']."|".$data['cy']."</td>
			<td>".($data['stype'] ? "<img src=".$gfx."/map/".$data['stype'].".gif width=10 height=10> ".stripslashes($data['sname'])."-System ".$data['sx']."|".$data['sy'] : "")."</td>
			<td><a href=\"javascript:void(0);\" onClick=\"showPosition(".$data['cx'].",".$data['cy'].");\">anzeigen</a></td>
		</tr>";
	}
	echo "</table>";
}
if ($v == "showrkn")
{
	pageheader("/ NPC-Menü / <b>RKN-Sticky</b>");
	if ($_GET['sent'] == 1)
	{
		$u->updateprofile('rkn_text',addslashes($_GET['st']));
		$_SESSION['rkn_text'] = $_GET['st'];
		meldung("Nachricht aktualisiert");
	}
	echo "<form action=main.php method=post><input type=hidden name=p value=npc><input type=hidden name=s value=srkn><input type=hidden name=sent value=1>
	<table bgcolor=#262323 cellspacing=1 cellpadding=1>
	<tr><th>RKN Sticky ändern</th></tr>
	<tr>
		<td><textarea cols=\"80\" rows=\"30\" name=\"st\">".stripslashes($_SESSION['rkn_text'])."</textarea></td>
	</tr>
	<tr>
		<td><input type=submit class=button value=Ändern></td>
	</tr>
	</table>
	</form>";
}
?>