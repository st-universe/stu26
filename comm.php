<?php
if (!is_object($db)) exit;
include_once($global_path."class/comm.class.php");
$comm = new comm;
switch($_GET['s'])
{
	default:
		$v = "main";
	case "ma":
		$v = "main";
		break;
	case "kn":
		$v = "kommnet";
		if ($_GET['t']) $t = $_GET['t'];
		else $t = "official";
		break;
	case "knu":
		$v = "kommnetuser";
		break;
	case "akn":
		if ($_SESSION['allys_id'] == 0) die(show_error(902));
		$v = "allykommnet";
		break;
	case "rkn":
		if ($comm->checkfactionkn() != 1 && $_SESSION['uid'] > 100) die(show_error(902));
		$v = "factionkommnet";
		break;
	case "bs":
		$v = "writekn";
		break;
	case "abs":
		if ($_SESSION['allys_id'] == 0) die(show_error(902));
		$v = "awritekn";
		break;
	case "rbs":
		if ($comm->checkfactionkn() != 1 && $_SESSION['uid'] > 100) die(show_error(902));
		$v = "rwritekn";
		break;
	case "sb":
		$v = "searchkn";
		break;
	case "eb":
		$v = "editkn";
		break;
	case "asb":
		if ($_SESSION['allys_id'] == 0) die(show_error(902));
		$v = "asearchkn";
		break;
	case "aeb":
		if ($_SESSION['allys_id'] == 0) die(show_error(902));
		$v = "aeditkn";
		break;
	case "rsb":
		if ($comm->checkfactionkn() != 1 && $_SESSION['uid'] > 100) die(show_error(902));
		$v = "rsearchkn";
		break;
	case "reb":
		if ($comm->checkfactionkn() != 1 && $_SESSION['uid'] > 100) die(show_error(902));
		$v = "reditkn";
		break;
	case "pe":
		$v = "postein";
		break;
	case "pes":
		$v = "postein_search";
		if ($_GET['ss']) $_SESSION['ss'] = $_GET['ss'];
		break;
	case "pei":
		$v = "postein_searchuser";
		if ($_GET['si']) $_SESSION['si'] = $_GET['si'];
		break;
	case "pa":
		$v = "postaus";
		break;
	case "nn":
		$v = "neuena";
		break;
	case "nz":
		$v = "notes";
		break;
	case "ec":
		$v = "editcontacts";
		break;
	case "nr":
		$v = "notrufe";
		if (check_int($_GET['ds'])) $result = $comm->delnr();
		$comm->loadecalls();
		break;
	case "qe":
		$v = "quests";
		include_once("class/quest.class.php");
		$quest = new quest;
		$quest->loadquestsbyuser();
		break;
	case "il":
		$v = "ignorelist";
		break;
	case "rpu":
		$v = "rpguser";
		break;
}


function translateType($type) {
	
	
	switch($type) {
		
		case "story": return "Hintergrundgeschichte";
		case "official": return "Offizielle Bekanntmachung";
		case "alliance": return "Allianz-Bekanntmachung";
		case "informal": return "Informelle Nachricht";
		case "shiplog": return "Logbuch-Eintrag";
		default: return "";
	}
}

function composeEntry($i,$data) {
	global $gfx, $comm;
	$knp = "
	<table cellspacing=1 cellpadding=1 width=90% class=\"kn_".$data['type']." suppressMenuColors\"> 
		<tr>
			<th style=\"width:150px;text-align:center;vertical-align:middle;\"><img src=".$gfx."/icons/kn/".$data['type']."_l.png border=0 height=30 width=150 title='".translateType($data['type'])."'></th>
			<th style=\"text-align:center;vertical-align:middle;height:30px;\">".stripslashes($data['titel'])."</th>
			<th style=\"width:150px;text-align:center;vertical-align:middle;\"><img src=".$gfx."/icons/kn/".$data['type']."_r.png border=0 height=30 width=150 title='".translateType($data['type'])."'></th>
		</tr>
		<tr>
			<td colspan=2 style=\"vertical-align:middle;height:30px;\">
				<table>
					<tr><td style=\"vertical-align:middle;height:30px;\"><img src=".$gfx."/rassen/".(!$data['ruser_id'] ? 9 : ($data['race']."".($data[subrace] != 0 ? "_".$data[subrace] : "")))."kn.png width=25 height=25 title='".addslashes(getracename($data['race'],$data['subrace']))."'></td>
					<td class=\"kn_text\" style=\"vertical-align:middle;height:30px;\">".stripslashes($data['username'])." (".$data['user_id'].($data['user_id'] < 100 ? " <b>NPC</b>" : "").")</td></tr>
				</table>
			</td>
			<th style=\"width:150px;text-align:center;vertical-align:middle;height:30px;\">".date("d.m.",$data['date']).setyear(date("Y",$data['date'])).date(" H:i",$data['date'])."</th>
		</tr>
		<tr>
			<td width=120 style=\"vertical-align:top;\"><div class=\"kn_text\" style=\"text-align: center;vertical-align:middle;\">".(!$data['ruser_id'] ? "<font color=gray>Gelöscht</font>" : "")."<br>".(strlen($data['propic']) > 10 ? "<img src=\"".$data['propic']."\" width=100 height=100>" : "<img src=".$gfx."/rassen/".(!$data['race'] ? 9 : $data['race'])."kn.png>")."<br>".($data['id'] > $_SESSION['kn_lez'] ? "<font size=-3 color=#ff0000>".$data['id']."</font>" : "<font size=-3>".$data['id']."</font>")."</font></div><br>
	".parse_knrefs($data['refe']).$comm->get_other_kn_posts($data['user_id'],$data['id'])."</td>
			<td colspan=2 class=kn_text>".nl2br(stripslashes(knparser($data['text'])))."</td>
		</tr>
		<tr>
			<td style=\"text-align:center;\">
			<form action=main.php method=get><input type=hidden name=p value=comm><input type=hidden name=s value=kn><input type=hidden name=m value=".$_GET["m"]."><input type=hidden name=id value=".$data[id]."><input type=hidden name=a value=v>

				<table>
					<tr>";
					
						if ($data['date'] > time()-600 && $data['user_id'] == $_SESSION['uid']) 
							$knp .= "<td width=26 style=\"width:28px;text-align:center;\"><a href=?p=comm&s=eb&id=".$data['id']." ".getHover("ke".$i,"active/n/list","hover/w/list")."><img src=".$gfx."/buttons/active/n/list.gif name=ke".$i." border=0 title='Beitrag ".$data['id']." editieren'></a></td>";					
						else
							$knp .= "<td width=26 style=\"width:28px;text-align:center;\">&nbsp;</td>";
						
						$knp .= "<td width=26 style=\"width:28px;text-align:center;\"><a href=?p=comm&s=kn&a=slz&lz=".$data['id']." ".getHover("lz".$i,"active/n/yes","hover/w/yes")."><img src=".$gfx."/buttons/active/n/yes.gif name=lz".$i." border=0 title='Lesezeichen bei Beitrag ".$data['id']." setzen'></a></td>";					

						if ($data['user_id'] != 1 && $data['ruser_id']) {
							$knp .= "<td width=26 style=\"width:28px;text-align:center;\"><a href=?p=comm&s=nn&recipient=".$data['user_id']." ".getHover("msg".$i,"active/n/mail","hover/w/mail")."><img src=".$gfx."/buttons/active/n/mail.gif name=msg".$i." border=0 title=\"".ftit("PM an ".$data['username']." schicken")."\"></a></td>";											
							$knp .= "<td width=26 style=\"width:28px;text-align:center;\"><a href=\"javascript:void(0);\" onClick=\"opensi(".$data['user_id'].")\" ".getHover("id".$data['id'],"active/n/info","hover/w/info")."><img src=".$gfx."/buttons/active/n/info.gif name=id".$data['id']." border=0 title='Spielerprofil'></a></td>";											
						} else {
							$knp .= "<td width=26 style=\"width:28px;text-align:center;\">&nbsp;</td>";
							$knp .= "<td width=26 style=\"width:28px;text-align:center;\">&nbsp;</td>";
						}
						

						
$knp .= "				</tr>
				</table>
			
			</form>
			</td>
			<td colspan=2>&nbsp;</td>
		</tr>
		
		
		
	</table>";
	return $knp;
}


function composeLogbookEntry($i,$data) {
	global $gfx, $comm;
	$knp = "
	<table cellspacing=1 cellpadding=1 width=90% class=\"kn_".$data['type']." suppressMenuColors\"> 
		<tr>
			<th style=\"width:150px;text-align:center;vertical-align:middle;\"><img src=".$gfx."/icons/kn/".$data['type']."_l.png border=0 title='".translateType($data['type'])."'></th>
			<th style=\"text-align:center;vertical-align:middle;height:30px;\">".stripslashes($data['titel'])."</th>
			<th style=\"width:150px;text-align:center;vertical-align:middle;\"><img src=".$gfx."/icons/kn/".$data['type']."_r.png border=0 title='".translateType($data['type'])."'></th>
		</tr>
		<tr>
			<td colspan=2 style=\"vertical-align:middle;height:30px;\">
				<table>
					<td class=\"kn_text\" style=\"vertical-align:middle;height:30px;\">Logbuch der <b>".stripslashes($data['shipname'])."</b> von ".stripslashes($data['username'])." (".$data['user_id'].($data['user_id'] < 100 ? " <b>NPC</b>" : "").")</td></tr>
				</table>
			</td>
			<th style=\"width:150px;text-align:center;vertical-align:middle;height:30px;\">".date("d.m.",$data['date']).setyear(date("Y",$data['date'])).date(" H:i",$data['date'])."</th>
		</tr>
		<tr>
			<td width=120 style=\"vertical-align:top;\"><div class=\"kn_text\" style=\"text-align: center;vertical-align:middle;\">".(!$data['ruser_id'] ? "<font color=gray>Gelöscht</font>" : "")."<br>"."<img src=\"gfx/ships/".$data['shipclass'].".gif"."\" class=\"kn_shiplog\">"."<br>".($data['id'] > $_SESSION['kn_lez'] ? "<font size=-3 color=#ff0000>".$data['id']."</font>" : "<font size=-3>".$data['id']."</font>")."</font></div><br>
	".parse_knrefs($data['refe']).$comm->get_other_kn_posts($data['user_id'],$data['id'])."</td>
			<td colspan=2 class=kn_text>".nl2br(stripslashes(knparser($data['text'])))."</td>
		</tr>
		<tr>
			<td style=\"text-align:center;\">
			<form action=main.php method=get><input type=hidden name=p value=comm><input type=hidden name=s value=kn><input type=hidden name=m value=".$_GET["m"]."><input type=hidden name=id value=".$data[id]."><input type=hidden name=a value=v>

				<table>
					<tr>";
					
						if ($data['date'] > time()-600 && $data['user_id'] == $_SESSION['uid']) 
							$knp .= "<td width=26 style=\"width:28px;text-align:center;\"><a href=?p=comm&s=eb&id=".$data['id']." ".getHover("ke".$i,"active/n/list","hover/w/list")."><img src=".$gfx."/buttons/active/n/list.gif name=ke".$i." border=0 title='Beitrag ".$data['id']." editieren'></a></td>";					
						else
							$knp .= "<td width=26 style=\"width:28px;text-align:center;\">&nbsp;</td>";
						
						$knp .= "<td width=26 style=\"width:28px;text-align:center;\"><a href=?p=comm&s=kn&a=slz&lz=".$data['id']." ".getHover("lz".$i,"active/n/yes","hover/w/yes")."><img src=".$gfx."/buttons/active/n/yes.gif name=lz".$i." border=0 title='Lesezeichen bei Beitrag ".$data['id']." setzen'></a></td>";					

						if ($data['user_id'] != 1 && $data['ruser_id']) {
							$knp .= "<td width=26 style=\"width:28px;text-align:center;\"><a href=?p=comm&s=nn&recipient=".$data['user_id']." ".getHover("msg".$i,"active/n/mail","hover/w/mail")."><img src=".$gfx."/buttons/active/n/mail.gif name=msg".$i." border=0 title=\"".ftit("PM an ".$data['username']." schicken")."\"></a></td>";											
							$knp .= "<td width=26 style=\"width:28px;text-align:center;\"><a href=\"javascript:void(0);\" onClick=\"opensi(".$data['user_id'].")\" ".getHover("id".$data['id'],"active/n/info","hover/w/info")."><img src=".$gfx."/buttons/active/n/info.gif name=id".$data['id']." border=0 title='Spielerprofil'></a></td>";											
						} else {
							$knp .= "<td width=26 style=\"width:28px;text-align:center;\">&nbsp;</td>";
							$knp .= "<td width=26 style=\"width:28px;text-align:center;\">&nbsp;</td>";
						}
						

						
$knp .= "				</tr>
				</table>
			
			</form>
			</td>
			<td colspan=2>&nbsp;</td>
		</tr>
		
		
		
	</table>";
	return $knp;
}


if ($v == "main")
{
	$lzc = $comm->getlzcount();
	if ($_SESSION['allys_id'] > 0) $alzc = $comm->agetlzcount();
	$rkn = $comm->checkfactionkn();
	if ($rkn == 1 || $_SESSION['uid'] < 100) $rlzc = $comm->rgetlzcount();
	$npm = $comm->getnewpmcount();
	pageheader("/ <b>Kommunikation</b>");
	
	
	
	function formWrap($text,$p,$s) {
		return "<form action=main.php style=\"margin: 0;\"><input type=hidden name=p value=".$p."><input type=hidden name=s value=".$s.">".$text."</form>";
	}
	
	$logships = $comm->getAllLogShips();
	
	$content = array();
	array_push($content,dropDownMenuOption(
		"<img src=".$gfx."/buttons/icon/text.gif title=\"Beiträge\" border=0> Lesen",
		"<a href=?p=comm&s=kn ".getHover("read","inactive/n/text","hover/w/text")."><img src=".$gfx."/buttons/inactive/n/text.gif title='Alle lesen' name=read> Alle Beiträge anzeigen</a>",
		0,"100","400"
	));	
	if ($lzc > 0) {
		array_push($content,dropDownMenuOption(
			"",
			"<a href=?p=comm&s=kn&lez=".$_SESSION["kn_lez"]." ".getHover("readlz","inactive/n/yes","hover/w/yes")."><img src=".$gfx."/buttons/inactive/n/yes.gif title='Alle ab Lesezeichen' name=readlz> Alle ab Lesezeichen (".$lzc.")</a>",
			0,"100","400"
		));	
	}	
	// array_push($content,dropDownMenuOption(
		// "",
		// "<a href=?p=comm&s=kn ".getHover("reado","inactive/n/exclamation","hover/w/exclamation")."><img src=".$gfx."/buttons/inactive/n/exclamation.gif title='Nur Bekanntmachungen' name=reado> Nur Bekanntmachungen anzeigen</a>",
		// 0,"100","400"
	// ));		
	array_push($content,"<tr style=\"height:10px;\"><td colspan=2></td></tr>");	
	array_push($content,dropDownMenuOption(
		"<img src=".$gfx."/buttons/icon/pm_out.gif border=0 title=\"Beitrag schreiben\"> Schreiben",
		"<a href=?p=comm&s=bs ".getHover("write","inactive/n/text","hover/w/text")."><img src=".$gfx."/buttons/inactive/n/text.gif title='Alle lesen' name=write> Neuen Beitrag verfassen</a>",
		0,"100","400"
	));	
	array_push($content,"<tr style=\"height:10px;\"><td colspan=2></td></tr>");		
	array_push($content,dropDownMenuOption(
		"<img src=".$gfx."/buttons/icon/info.gif border=0 title=\"Suche\"> Suche",
		formWrap("Beitrags-ID<br><input type=text size=5 name=sbn class=text style=\"width:300px;\"> <input type=submit value=Suchen class=button>","comm","sb"),
		0,"100","400"
	));		
	array_push($content,dropDownMenuOption(
		"",
		formWrap("Spieler-ID<br><input type=text size=5 name=snu class=text style=\"width:300px;\"> <input type=submit value=Suchen class=button>","comm","sb"),		
		0,"100","400"
	));	
	array_push($content,dropDownMenuOption(
		"",
		formWrap("Text<br><input type=text size=5 name=sbs class=text style=\"width:300px;\"> <input type=submit value=Suchen class=button>","comm","sb"),
		0,"100","400"
	));	
	
	
	$ships = "<select style=\"width:300px;\">";
	foreach($logships as $ls) {
		$ships .= "<option value=\"".$ls['id']."\">".$ls['name']."</option>";
	}
	$ships .= "</select><input type=submit value=Anzeigen class=button>";
	

	$menu['skn'] = fixedPanel(3,"Sektor-Kommunikations-Netzwerk","mskn",$gfx."/buttons/icon/maindesk.gif",$content);
	
	if ($_SESSION['allys_id'] > 0)
	{	
		$content = array();
		array_push($content,dropDownMenuOption(
			"<img src=".$gfx."/buttons/icon/text.gif title=\"Beiträge\" border=0> Lesen",
			"<a href=?p=comm&s=akn ".getHover("aread","inactive/n/text","hover/w/text")."><img src=".$gfx."/buttons/inactive/n/text.gif title='Alle lesen' name=aread> Alle Beiträge anzeigen</a>",
			0,"100","400"
		));	
		if ($alzc > 0) {
			array_push($content,dropDownMenuOption(
				"",
				"<a href=?p=comm&s=akn&lez=".$_SESSION["akn_lez"]." ".getHover("areadlz","inactive/n/yes","hover/w/yes")."><img src=".$gfx."/buttons/inactive/n/yes.gif title='Alle ab Lesezeichen' name=areadlz> Alle ab Lesezeichen (".$alzc.")</a>",
				0,"100","400"
			));	
		}	
		array_push($content,"<tr style=\"height:10px;\"><td colspan=2></td></tr>");	
		array_push($content,dropDownMenuOption(
			"<img src=".$gfx."/buttons/icon/pm_out.gif border=0 title=\"Beitrag schreiben\"> Schreiben",
			"<a href=?p=comm&s=abs ".getHover("awrite","inactive/n/text","hover/w/text")."><img src=".$gfx."/buttons/inactive/n/text.gif title='Alle lesen' name=awrite> Neuen Beitrag verfassen</a>",
			0,"100","400"
		));	
		$menu['akn'] = fixedPanel(2,"Allianz-Kommunikations-Netzwerk","makn",$gfx."/buttons/icon/nodes.gif",$content);
	}
	
	$content = array();
	array_push($content,dropDownMenuOption(
		"<img src=".$gfx."/buttons/icon/text.gif title=\"Beiträge\" border=0> Lesen",
		"<a href=?p=comm&s=rkn ".getHover("rread","inactive/n/text","hover/w/text")."><img src=".$gfx."/buttons/inactive/n/text.gif title='Alle lesen' name=rread> Alle Beiträge anzeigen</a>",
		0,"100","400"
	));	
	if ($rlzc > 0) {
		array_push($content,dropDownMenuOption(
			"",
			"<a href=?p=comm&s=rkn&lez=".$_SESSION["rkn_lez"]." ".getHover("rreadlz","inactive/n/yes","hover/w/yes")."><img src=".$gfx."/buttons/inactive/n/yes.gif title='Alle ab Lesezeichen' name=rreadlz> Alle ab Lesezeichen (".$rlzc.")</a>",
			0,"100","400"
		));	
	}	
	array_push($content,"<tr style=\"height:10px;\"><td colspan=2></td></tr>");	
	array_push($content,dropDownMenuOption(
		"<img src=".$gfx."/buttons/icon/pm_out.gif border=0 title=\"Beitrag schreiben\"> Schreiben",
		"<a href=?p=comm&s=rbs ".getHover("rwrite","inactive/n/text","hover/w/text")."><img src=".$gfx."/buttons/inactive/n/text.gif title='Alle lesen' name=rwrite> Neuen Beitrag verfassen</a>",
		0,"100","400"
	));	
	$menu['rkn'] = fixedPanel(1,"Rassen-Kommunikations-Netzwerk","mrkn",$gfx."/buttons/icon/r".$_SESSION['race'].".gif",$content);
	
	
	
	
	
	
	
	
	$content = array();
	array_push($content,dropDownMenuOption(
		"<img src=".$gfx."/buttons/icon/data.gif title=\"Postfach\" border=0> Postfach",
		"<a href=?p=comm&s=pe ".getHover("pin","inactive/n/pm_in","hover/w/pm_in")."><img src=".$gfx."/buttons/inactive/n/pm_in.gif title='Posteingang' name=pin> Empfangen ".($npm > 0 ? "(".$npm.")" : "")."</a>",
		0,"100","300"
	));	
	array_push($content,dropDownMenuOption(
		"",
		"<a href=?p=comm&s=pa ".getHover("pout","inactive/n/pm_out","hover/w/pm_out")."><img src=".$gfx."/buttons/inactive/n/pm_out.gif title='Postausgang' name=pout> Gesendet</a>",
		0,"100","300"
	));		
	array_push($content,"<tr style=\"height:10px;\"><td colspan=2></td></tr>");		
	array_push($content,dropDownMenuOption(
		"<img src=".$gfx."/buttons/icon/info.gif border=0 title=\"Suche\"> Suche",
		formWrap("Spieler-ID<br><input type=text size=5 name=si class=text style=\"width:200px;\"> <input type=submit value=Suchen class=button>","comm","pei"),
		0,"100","300"
	));		
	array_push($content,dropDownMenuOption(
		"",
		formWrap("Text<br><input type=text size=5 name=ss class=text style=\"width:200px;\"> <input type=submit value=Suchen class=button>","comm","pes"),
		0,"100","300"
	));	
	array_push($content,"<tr style=\"height:10px;\"><td colspan=2></td></tr>");		
	array_push($content,dropDownMenuOption(
		"<img src=".$gfx."/buttons/icon/options.gif title=\"Kontakte\" border=0> Kontakte",
		"<a href=?p=comm&s=ec ".getHover("clist","inactive/n/people","hover/w/people")."><img src=".$gfx."/buttons/inactive/n/people.gif title='Kontaktliste' name=clist> Kontaktliste bearbeiten</a>",
		0,"100","300"
	));	
	array_push($content,dropDownMenuOption(
		"",
		"<a href=?p=comm&s=il ".getHover("cigno","inactive/n/no","hover/w/no")."><img src=".$gfx."/buttons/inactive/n/no.gif title='Ignore-Liste' name=cigno> Ignore-Liste bearbeiten</a>",
		0,"100","300"
	));			
	$menu['pm'] = fixedPanel(3,"Private Nachrichten","mpm",$gfx."/buttons/icon/mail.gif",$content);
	
	
	
	
	$content = array();
	array_push($content,dropDownMenuOption(
		"<img src=".$gfx."/buttons/icon/exclamation.gif title=\"Notrufe\" border=0> Notrufe",
		"<a href=?p=comm&s=nr ".getHover("nruf","inactive/n/exclamation","hover/w/exclamation")."><img src=".$gfx."/buttons/inactive/n/exclamation.gif title='Notrufe' name=nruf> Notrufe anzeigen</a>",
		0,"100","300"
	));	
	array_push($content,dropDownMenuOption(
		"<img src=".$gfx."/buttons/icon/nodes.gif title=\"Links\" border=0> Links",
		"<a href=http://forum.stuniverse.de target=_blank ".getHover("forum","inactive/n/text","hover/w/text")."><img src=".$gfx."/buttons/inactive/n/text.gif title='Forum' name=forum> STU-Forum (Neuer tab)</a>",
		0,"100","300"
	));		
	$menu['other'] = fixedPanel(1,"Sonstiges","mot",$gfx."/buttons/icon/info.gif",$content);
		
	
	echo "<table  class=tablelayout>";
		echo "<tr>";
			echo "<td rowspan=1 class=tablelayout style=\"width:500px;\">".$menu['skn']."</td>";
			echo "<td rowspan=3 class=tablelayout style=\"width:400px;vertical-align:top;\">".$menu['pm']."</td>";
			echo "<td rowspan=3 class=tablelayout style=\"width:400px;vertical-align:top;\">".$menu['other']."</td>";
		echo "</tr>";
		echo "<tr>";
			echo "<td rowspan=1 class=tablelayout style=\"width:500px;\">".$menu['akn']."</td>";
		echo "</tr>";		
		echo "<tr>";
			echo "<td rowspan=1 class=tablelayout style=\"width:500px;\">".$menu['rkn']."</td>";
		echo "</tr>";		
	echo "</table>";
	
	
	
	
	

	
	
	
	// echo "<table cellspacing=0 cellpadding=0>
	// <tr>
		// <td width=250 valign=top>
		// <table class=tcal>
		// <form action=main.php><input type=hidden name=p value=comm><input type=hidden name=s value=sb>
		// <th><img src=".$gfx."/buttons/menu_ally0.gif title=\"Sektor\"> Sektor-KN</th>
		// <tr>
		// <td>- <a href=?p=comm&s=kn>Beiträge lesen</a><br>";
		// if ($lzc > 0) echo "- <a href=?p=comm&s=kn&lez=".$_SESSION["kn_lez"].">Beiträge ab Lesezeichen</a>: ".$lzc."<br>";
		// echo "- <a href=?p=comm&s=bs>Beitrag schreiben</a><br>
		// - Beitrag ID <input type=text size=5 name=sbn class=text> <input type=submit value=Anzeigen class=button><br>
		// - Volltextsuche <input type=text size=10 name=sbs class=text> <input type=submit value=Suche class=button><br>
		// - Suche nach ID <input type=text size=4 name=snu class=text> <input type=submit value=Anzeigen class=button><br><br></td>
		// </tr></form>";
		
		
		

		
		
		// if ($_SESSION['allys_id'] > 0)
		// {
			// echo "<form action=main.php><input type=hidden name=p value=comm><input type=hidden name=s value=asb>
			// <th><img src=".$gfx."/buttons/menu_ally1.gif title=\"Allianz\"> Allianz-KN</th>
			// <tr>
			// <td>- <a href=?p=comm&s=akn>Beiträge lesen</a><br>";
			// if ($alzc > 0) echo "- <a href=?p=comm&s=akn&lez=".$_SESSION["akn_lez"].">Beiträge ab Lesezeichen</a>: ".$alzc."<br>";
			// echo "- <a href=?p=comm&s=abs>Beitrag schreiben</a><br>
			// - Beitrag ID <input type=text size=5 name=sbn class=text> <input type=submit value=Anzeigen class=button><br>
			// - Volltextsuche <input type=text size=10 name=sbs class=text> <input type=submit value=Suche class=button><br><br></td>
			// </tr></form>";
		// }
		// if ($rkn == 1 || $_SESSION['uid'] < 100)
		// {
			// echo "<form action=main.php><input type=hidden name=p value=comm><input type=hidden name=s value=rsb>
			// <th><img src=".$gfx."/rassen/".$_SESSION['race']."s.gif title=\"Rasse\"> Rassen-KN</th>
			// <tr>
			// <td>- <a href=?p=comm&s=rkn>Beiträge lesen</a><br>";
			// if ($rlzc > 0) echo "- <a href=?p=comm&s=rkn&lez=".$_SESSION['rkn_lez'].">Beiträge ab Lesezeichen</a>: ".$rlzc."<br>";
			// echo "- <a href=?p=comm&s=rbs>Beitrag schreiben</a><br>
			// - Beitrag ID <input type=text size=5 name=sbn class=text> <input type=submit value=Anzeigen class=button><br>
			// - Volltextsuche <input type=text size=10 name=sbs class=text> <input type=submit value=Suche class=button></td></td>
			// </tr></form>";
		// }
		// echo "</table>
		// </td>
		// <td width=25>&nbsp;</td>
		// <td width=250 valign=top>
		// <table class=tcal>
		// <th><img src=".$gfx."/buttons/msg1.gif title=\"Private Nachrichten\"> Private Nachrichten</th>
		// <tr><td>
		// <a href=?p=comm&s=nn>Neue Nachricht schreiben</a><br><br />
		// <b>Posteingang</b><br />
		// - <a href=?p=comm&s=pe>anzeigen</a> (".($npm > 0 ? "<span style=\"color: #FF0000; text-decoration: blink;\">".$npm."</span>" : 0).")<br>
		// <form action=\"main.php\" method=\"post\" style=\"margin: 0; padding: 0;\"><input type=hidden name=\"p\" value=\"comm\"><input type=hidden name=\"s\" value=\"pes\">- <input type=\"text\" name=\"ss\" class=\"text\" size=\"10\"> <input type=\"submit\" value=\"durchsuchen\" class=\"button\"></form>
		// <form action=\"main.php\" method=\"post\" style=\"margin: 0; padding: 0;\"><input type=hidden name=\"p\" value=\"comm\"><input type=hidden name=\"s\" value=\"pei\">- <input type=\"text\" name=\"si\" class=\"text\" size=\"10\"> <input type=\"submit\" value=\"nach Id suchen\" class=\"button\"></form><br>
		// <b>Postausgang</b><br />
		// - <a href=?p=comm&s=pa>anzeigen</a><br><br />
		// <b>Kontakte</b><br />
		// - <a href=?p=comm&s=ec>editieren</a><br>
		// - <a href=?p=comm&s=il>Ignore-Liste</a><br><br>
		// - <a href=?p=log>Logbücher</a> (Beta)
		// </table>
		// </td>
		// <td width=25>&nbsp;</td>
		// <td width=250 valign=top>
		// <table class=tcal>
		// <th><img src=".$gfx."/buttons/notiz1.gif title=\"Sonstiges\"> Sonstiges</th>
		// <tr><td>
		// <b>Ingame</b><br>
		// - <a href=?p=comm&s=nr>Notrufe</a><br>
		// - <a href=?p=comm&s=rpu>Rassen-RPG Spieler</a><br><br>
		// <b>Extern</b><br>
		// - <a href=http://forum.stuniverse.de target=_blank>Forum</a><br>
		// - <a href=http://wiki.stuniverse.de target=_blank>Hilfe-Wiki</a><br/><br/>
		// <b>Chat</b><br>
		// - <a href=irc://irc.euirc.net/stu>irc.euirc.net #stu</a><br>
		// - <a href=http://www.stuniverse.de/?p=chat target=_blank>Javachat</a>
		// </table>
		// </td>
	// </tr>
	// </table>";
}
if ($v == "kommnetold")
{
	$m = "";
	if ($_GET['a'] == "slz" && is_numeric($_GET['lz']) && $_GET['lz'] > 0) $return = $comm->setlz($_GET['lz']);
	if ($_GET['a'] == "v" && check_int($_GET['id']) && check_int($_GET['rat'])) $comm->knvote($_GET['id'],$_GET['rat']);
	if (check_int($_GET['lez']))
	{
		$lzp = $db->query("SELECT COUNT(id) FROM stu_kn WHERE id<=".$_GET['lez'],1);
		$_GET['m'] = floor($lzp/5)*5;
	}
	if ((check_int($_GET['m']) || $_GET['m'] == 0) && $_GET['m'] >= 0) $m = $_GET['m'];
	$knc = $comm->getknposts();
	if (!is_numeric($m) || $m > $knc-5) $m = $knc-5;
	if ($m < 0) $m = 0;
	$result = $comm->getknbymark($m,$t);

	echo "<script language=\"Javascript\">
	var elt;
	function getknvotings(knid)
	{	
		elt = 'knv';
		sendRequest('backend/knvotes.php?PHPSESSID=".session_id()."&id=' + knid);
		return overlib('<div id=knv></div>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER, STICKY, RELX, 300, RELY, 115, WIDTH, 300);
	}
	</script>";
	
	
	echo "<style>
	
	</style>";	
	

	
	pageheader("/ <a href=?p=comm>Kommunikation</a> / <b>Kommunikations Netzwerk</b>");
	if ($return != "") meldung($return);
	if (mysql_num_rows($result) == 0) meldung("Es sind keine Beiträge vorhanden");
	else
	{
		while($data=mysql_fetch_assoc($result))
		{
			$i++;
			if ($data['le'] > 0) $data['text'] .= "<br><br><br><div style=\"font-size: 7pt; color: gray;\">Zuletzt editiert: ".date("d.m.Y H:i",$data['le'])."</div>";

			if ($data['type'] == 'shiplog') $knp = composeLogbookEntry($i,$data);
			else							$knp = composeEntry($i,$data);
			$knp .= "<br><br>";
			$kn = $knp.$kn;
		}
		$in = ceil(($m+5)/5);
		$i = $in-2;
		$j = $in+2;
		$ceiled_knc = ceil($knc/5);
		if ($i > 1) $pe = "<td><a href=?p=comm&s=kn&m=0>1</a></td>";
		if ($j < $ceiled_knc) $ps = "<td><a href=?p=comm&s=kn&m=".$knc.">".$ceiled_knc."</a></td>";
		if ($j > $ceiled_knc) $j = $ceiled_knc;
		if ($i < 1) $i = 1;
		while($i<=$j)
		{
			$pages .= "<td><a href=?p=comm&s=kn&m=".(($j-1)*5).">".($j == $in ? "<div style=\"font-weight : bold;\">&nbsp;".$j."&nbsp;</div>" : $j)."</a></td>";
			$j--;
		}
		$i = $in-2;
		$j = $in+2;
		$pages = $ps.($ceiled_knc > $j+1 ? "<td>... </td>" : "").$pages.($i > 2 ? "<td>...</td>" : "").$pe;
		
		function getzu($v) { global $gfx,$m; return "<td><a href=?p=comm&s=kn&m=".($m-5 < 0 ? 0 : $m-5)." onmouseover=cp('zu".$v."','buttons/b_to2') onmouseout=cp('zu".$v."','buttons/b_to1')><img src=".$gfx."/buttons/b_to1.gif name=zu".$v." border=0 title=Zurückblättern></a></td>"; }
		function getvor($v) { global $gfx,$m; return "<td><a href=?p=comm&s=kn&m=".($m+5)." onmouseover=cp('vo".$v."','buttons/b_from2') onmouseout=cp('vo".$v."','buttons/b_from1')><img src=".$gfx."/buttons/b_from1.gif name=vo".$v." border=0 title=Vorblättern></a></td>"; }
		function getbs($v) { global $gfx; return "<td><a href=?p=comm&s=bs onmouseover=cp('nb".$v."','buttons/knedit2') onmouseout=cp('nb".$v."','buttons/knedit1')><img src=".$gfx."/buttons/knedit1.gif name=nb".$v." border=0 title='Neuen Beitrag schreiben'></a></td>"; }
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr>".getbs(1)."&nbsp;".getvor(1)."&nbsp;".$pages."&nbsp;".getzu(1)."</tr></table><br>".$kn."<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr>".getbs(2)."&nbsp;".getvor(2)."&nbsp;".$pages."&nbsp;".getzu(2)."</tr></table>";
	}
}



function composeKN($networkName, $totalPostCount, $currentPostCount, $postData, $returnMessage) {
	pageheader("/ <a href=?p=comm>Kommunikation</a> / <b>".$networkName."</b>");
	
	
	
}






if ($v == "kommnet")
{
	$networkName = "Sektor-Kommunikations-Netzwerk";
	
	
	
	$m = "";
	if ($_GET['a'] == "slz" && is_numeric($_GET['lz']) && $_GET['lz'] > 0) $return = $comm->setlz($_GET['lz']);
	if ($_GET['a'] == "v" && check_int($_GET['id']) && check_int($_GET['rat'])) $comm->knvote($_GET['id'],$_GET['rat']);
	if (check_int($_GET['lez']))
	{
		$lzp = $db->query("SELECT COUNT(id) FROM stu_kn WHERE id<=".$_GET['lez'],1);
		$_GET['m'] = floor($lzp/5)*5;
	}
	if ((check_int($_GET['m']) || $_GET['m'] == 0) && $_GET['m'] >= 0) $m = $_GET['m'];
	$knc = $comm->getknposts();
	if (!is_numeric($m) || $m > $knc-5) $m = $knc-5;
	if ($m < 0) $m = 0;
	$result = $comm->getknbymark($m,$t);

	echo "<script language=\"Javascript\">
	var elt;
	function getknvotings(knid)
	{	
		elt = 'knv';
		sendRequest('backend/knvotes.php?PHPSESSID=".session_id()."&id=' + knid);
		return overlib('<div id=knv></div>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER, STICKY, RELX, 300, RELY, 115, WIDTH, 300);
	}
	</script>";
	
	
	echo "<style>
	
	</style>";	
	

	echo composeKN($networkName, $totalPostCount, $currentPostCount, $postData, $returnMessage);
	
	echo "<br><br>";
	
	// pageheader("/ <a href=?p=comm>Kommunikation</a> / <b>Kommunikations Netzwerk</b>");
	if ($return != "") meldung($return);
	if (mysql_num_rows($result) == 0) meldung("Es sind keine Beiträge vorhanden");
	else
	{
		while($data=mysql_fetch_assoc($result))
		{
			$i++;
			if ($data['le'] > 0) $data['text'] .= "<br><br><br><div style=\"font-size: 7pt; color: gray;\">Zuletzt editiert: ".date("d.m.Y H:i",$data['le'])."</div>";

			if ($data['type'] == 'shiplog') $knp = composeLogbookEntry($i,$data);
			else							$knp = composeEntry($i,$data);
			$knp .= "<br><br>";
			$kn = $knp.$kn;
		}
		$in = ceil(($m+5)/5);
		$i = $in-2;
		$j = $in+2;
		$ceiled_knc = ceil($knc/5);
		if ($i > 1) $pe = "<td><a href=?p=comm&s=kn&m=0>1</a></td>";
		if ($j < $ceiled_knc) $ps = "<td><a href=?p=comm&s=kn&m=".$knc.">".$ceiled_knc."</a></td>";
		if ($j > $ceiled_knc) $j = $ceiled_knc;
		if ($i < 1) $i = 1;
		while($i<=$j)
		{
			$pages .= "<td><a href=?p=comm&s=kn&m=".(($j-1)*5).">".($j == $in ? "<div style=\"font-weight : bold;\">&nbsp;".$j."&nbsp;</div>" : $j)."</a></td>";
			$j--;
		}
		$i = $in-2;
		$j = $in+2;
		$pages = $ps.($ceiled_knc > $j+1 ? "<td>... </td>" : "").$pages.($i > 2 ? "<td>...</td>" : "").$pe;
		
		function getzu($v) { global $gfx,$m; return "<td><a href=?p=comm&s=kn&m=".($m-5 < 0 ? 0 : $m-5)." onmouseover=cp('zu".$v."','buttons/b_to2') onmouseout=cp('zu".$v."','buttons/b_to1')><img src=".$gfx."/buttons/b_to1.gif name=zu".$v." border=0 title=Zurückblättern></a></td>"; }
		function getvor($v) { global $gfx,$m; return "<td><a href=?p=comm&s=kn&m=".($m+5)." onmouseover=cp('vo".$v."','buttons/b_from2') onmouseout=cp('vo".$v."','buttons/b_from1')><img src=".$gfx."/buttons/b_from1.gif name=vo".$v." border=0 title=Vorblättern></a></td>"; }
		function getbs($v) { global $gfx; return "<td><a href=?p=comm&s=bs onmouseover=cp('nb".$v."','buttons/knedit2') onmouseout=cp('nb".$v."','buttons/knedit1')><img src=".$gfx."/buttons/knedit1.gif name=nb".$v." border=0 title='Neuen Beitrag schreiben'></a></td>"; }
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr>".getbs(1)."&nbsp;".getvor(1)."&nbsp;".$pages."&nbsp;".getzu(1)."</tr></table><br>".$kn."<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr>".getbs(2)."&nbsp;".getvor(2)."&nbsp;".$pages."&nbsp;".getzu(2)."</tr></table>";
	}
}

if ($v == "kommnetuser")
{
	pageheader("/ <a href=?p=comm>Kommunikation</a> / <b>Kommunikations Netzwerk sortiert nach Siedler-ID ".$_GET[ukn]."</b>");

	if (!check_int($_GET[ukn]))
	{
		meldung("Es wurde keine Sieder-ID angegeben");
		exit;
	}
	$m = "";
	if ($_GET[a] == "slz" && is_numeric($_GET[lz]) && $_GET[lz] > 0) $return = $comm->setlz($_GET[lz]);
	if ($_GET["a"] == "v" && check_int($_GET["id"]) && check_int($_GET["rat"])) $comm->knvote($_GET["id"],$_GET["rat"]);
	if (check_int($_GET[lez]))
	{
		$lzp = $db->query("SELECT COUNT(id) FROM stu_kn WHERE user_id=".$_GET[ukn]." AND id<=".$_GET[lez],1);
		$_GET[m] = floor($lzp/5)*5;
	}
	if ((check_int($_GET["m"]) || $_GET["m"] == 0) && $_GET["m"] >= 0) $m = $_GET["m"];
	$knc = $comm->getuknposts($_GET[ukn]);
	if (!is_numeric($m) || $m > $knc-5) $m = $knc-5;
	if ($m < 0) $m = 0;
	$result = $comm->getknbyuser($_GET[ukn],$m);

	if (mysql_num_rows($result) == 0)
	{
		meldung("Keine Beiträge vorhanden");
		exit;
	}

	echo "<script language=\"Javascript\">
	var elt;
	function getknvotings(knid)
	{	
		elt = 'knv';
		sendRequest('backend/knvotes.php?PHPSESSID=".session_id()."&id=' + knid);
		return overlib('<div id=knv></div>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER, STICKY, RELX, 300, RELY, 115, WIDTH, 300);
	}
	</script>";

	if ($return != "") meldung($return);
	if (mysql_num_rows($result) == 0) meldung("Es sind keine Beiträge vorhanden");
	else
	{
		while($data=mysql_fetch_assoc($result))
		{
			$i++;
			$knp = composeEntry($i,$data);
			$knp .= "<br><br>";
			$kn = $knp.$kn;
		}
		$in = ceil(($m+5)/5);
		$i = $in-2;
		$j = $in+2;
		if ($i > 1) $pe = "<td><a href=?p=comm&s=knu&ukn=".$_GET[ukn]."&m=0>1</a></td>";
		if ($j < ceil($knc/5)) $ps = "<td><a href=?p=comm&s=knu&ukn=".$_GET[ukn]."&m=".$knc.">".ceil($knc/5)."</a></td>";
		if ($j > ceil($knc/5)) $j = ceil($knc/5);
		if ($i < 1) $i = 1;
		while($i<=$j)
		{
			$pages .= "<td><a href=?p=comm&s=knu&ukn=".$_GET[ukn]."&m=".(($j-1)*5).">".($j == $in ? "<div style=\"font-weight : bold;\">&nbsp;".$j."&nbsp;</div>" : $j)."</a></td>";
			$j--;
		}
		$i = $in-2;
		$j = $in+2;
		$pages = $ps.(ceil($knc/5) > $j+1 ? "<td>... </td>" : "").$pages.($i > 2 ? "<td>...</td>" : "").$pe;
		
		function getzu($v) { global $gfx,$m; return "<td><a href=?p=comm&s=knu&ukn=".$_GET[ukn]."&m=".($m-5 < 0 ? 0 : $m-5)." onmouseover=cp('zu".$v."','buttons/b_to2') onmouseout=cp('zu".$v."','buttons/b_to1')><img src=".$gfx."/buttons/b_to1.gif name=zu".$v." border=0 title=Zurückblättern></a></td>"; }
		function getvor($v) { global $gfx,$m; return "<td><a href=?p=comm&s=knu&ukn=".$_GET[ukn]."&m=".($m+5)." onmouseover=cp('vo".$v."','buttons/b_from2') onmouseout=cp('vo".$v."','buttons/b_from1')><img src=".$gfx."/buttons/b_from1.gif name=vo".$v." border=0 title=Vorblättern></a></td>"; }
		function getbs($v) { global $gfx; return "<td><a href=?p=comm&s=bs onmouseover=cp('nb".$v."','buttons/knedit2') onmouseout=cp('nb".$v."','buttons/knedit1')><img src=".$gfx."/buttons/knedit1.gif name=nb".$v." border=0 title='Neuen Beitrag schreiben'></a></td>"; }
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr>".getbs(1)."&nbsp;".getvor(1)."&nbsp;".$pages."&nbsp;".getzu(1)."</tr></table><br>".$kn."<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr>".getbs(2)."&nbsp;".getvor(2)."&nbsp;".$pages."&nbsp;".getzu(2)."</tr></table>";
	}
}
if ($v == "writekn")
{
	$added = false;
	pageheader("/ <a href=?p=comm>Kommunikation</a> / <a href=?p=comm&s=kn>Kommunikations Netzwerk</a> / <b>Beitrag schreiben</b>");
	if ($_GET[sent] == 1 && $_GET[ab] == "Hinzufügen")
	{
		if (!$_GET[text]) meldung("Es wurde kein Text eingegeben");
		if (!$_GET[kntype]) meldung("Es wurde keine Kategorie gewählt");
		elseif ($_GET[refe] && !eregi("[0-9],",$_GET[refe]) && !check_int($_GET[refe])) meldung("Die KN-Bezüge sind fehlerhaft");
		else
		{
			$ret = $comm->addknmsg(format_string($_GET[titel]),strip_tags(format_string($_GET[text]),"<b></b><i></i><u></u>"),$_GET[refe],$_GET[rpg],$_GET['lz'],$_GET['kntype']);
			// if ($ret == -1) meldung("Kommunikationsnetzwerk gesperrt");
			// else meldung("Beitrag hinzugefügt");
			meldung($ret);
			$added = true;
		}
	}
	echo "<form action=main.php method=post><input type=hidden name=kn value=1>
	<input type=hidden name=p value=comm><input type=hidden name=s value=bs><input type=hidden name=sent value=1>";
	if ($_GET['ab'] == "Vorschau")
	{
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=500><th>Vorschau: ".str_replace("\"","",format_string(stripslashes($_GET['titel'])))."</th><tr><td>".nl2br(format_string(stripslashes($_GET['text'])))."</td></tr><tr><td><input type=submit name=ab class=button value=Hinzufügen></td></tr></table><br>";
	}//Handelt es sich um einen RPG-Beitrag? <input type=checkbox name=rpg value=1".($_GET[rpg] == 1 ? " CHECKED" : "").">
	
	
	$knTypeOptions = "<option value=\"0\">--- Bitte Kategorie wählen ---</option><option value=\"story\">Hintergrundgeschichte</option><option value=\"official\">Bekanntmachung</option>";
	
	if ($comm->canWriteAllyPost()) $knTypeOptions .= "<option value=\"alliance\">Allianz-Bekanntmachung</option>";
	if ($_SESSION[uid] < 100) $knTypeOptions .= "<option value=\"race\">Offizielle Story</option>";

	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=500>
	<tr><td class=m align=center>Titel (optional)</td></tr>
	<tr><td><input type=text size=60 name=titel class=text value=\"".str_replace("\"","",format_string(stripslashes($_GET['titel'])))."\" style=\"width:490px;\"></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>
 <select id=\"kntype\" name=\"kntype\" style=\"width:490px;\" onchange=\"knTypeChanged();\">
  ".$knTypeOptions."´
</select>
	</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td class=m align=center>Beitrag</td></tr>
	<tr><td><textarea cols=60 rows=20 name=text style=\"width:490px;\">".format_string(stripslashes($_GET['text']))."</textarea></td></tr>
	<tr><td>&nbsp;</td></tr>

	<tr><td>Lesezeichen setzen? <input type=checkbox name=lz value=1".($_GET['lz'] == 1 ? " CHECKED" : "")."></td></td>
	<tr><td>Auf KN-Postings beziehen <input type=text size=20 class=text name=refe value=".$_GET[refe]."> (max 7, mit Komma trennen)</td></tr>
	<tr><td>&nbsp;</td></tr>";
	//if ($_GET[ab] != "Hinzufügen") echo "<tr><td><input type=submit value=Hinzufügen name=ab class=button>&nbsp;<input type=submit name=ab value=Vorschau class=button></td></tr>";
	if (!$added) echo "<tr><td><input type=submit value=Hinzufügen name=ab class=button>&nbsp;<input type=submit name=ab value=Vorschau class=button></td></tr>";
	echo "</form></table>";
}
if ($v == "awritekn")
{
	pageheader("/ <a href=?p=comm>Kommunikation</a> / <a href=?p=comm&s=akn>Allianz Kommunikations Netzwerk</a> / <b>Beitrag schreiben</b>");
	if ($_GET[sent] == 1 && $_GET[ab] == "Hinzufügen")
	{
		if (!$_GET[text]) meldung("Es wurde kein Text eingegeben");
		else
		{
			$ret = $comm->aaddknmsg(format_string($_GET[titel]),format_string($_GET[text]),$_GET['lz']);
			meldung($ret);
		}
	}
	echo "<form action=main.php method=post><input type=hidden name=kn value=1>
	<input type=hidden name=p value=comm><input type=hidden name=s value=abs><input type=hidden name=sent value=1>";
	if ($_GET[ab] == "Vorschau")
	{
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=500><th>Vorschau: ".str_replace("\"","",format_string(stripslashes($_GET['titel'])))."</th><tr><td>".nl2br(format_string(stripslashes($_GET[text])))."</td></tr><tr><td><input type=submit name=ab class=button value=Hinzufügen></td></tr></table><br>";
	}
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=500>
	<tr><td class=m align=center>Titel (optional)</td></tr>
	<tr><td><input type=text size=60 name=titel class=text value=\"".str_replace("\"","",format_string(stripslashes($_GET['titel'])))."\"></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td class=m align=center>Beitrag</td></tr>
	<tr><td><textarea cols=60 rows=20 name=text>".format_string(stripslashes($_GET['text']))."</textarea></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>Lesezeichen setzen? <input type=checkbox name=lz value=1".($_GET['lz'] == 1 ? " CHECKED" : "")."></td></td>
	<tr><td>&nbsp;</td></tr>";
	if ($_GET[ab] != "Hinzufügen") echo "<tr><td><input type=submit value=Hinzufügen name=ab class=button>&nbsp;<input type=submit name=ab value=Vorschau class=button></td></tr>";
	echo "</form></table>";
}
if ($v == "rwritekn")
{
	pageheader("/ <a href=?p=comm>Kommunikation</a> / <a href=?p=comm&s=rkn>Rassen Kommunikations Netzwerk</a> / <b>Beitrag schreiben</b>");
	if ($_GET[sent] == 1 && $_GET[ab] == "Hinzufügen")
	{
		if (!$_GET[text]) meldung("Es wurde kein Text eingegeben");
		else
		{
			$ret = $comm->arddknmsg(format_string($_GET[titel]),format_string($_GET[text]),$_GET['lz']);
			meldung($ret);
		}
	}
	echo "<form action=main.php method=post><input type=hidden name=kn value=1>
	<input type=hidden name=p value=comm><input type=hidden name=s value=rbs><input type=hidden name=sent value=1>";
	if ($_GET[ab] == "Vorschau")
	{
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=500><th>Vorschau: ".str_replace("\"","",format_string(stripslashes($_GET['titel'])))."</th><tr><td>".nl2br(format_string(stripslashes($_GET['text'])))."</td></tr><tr><td><input type=submit name=ab class=button value=Hinzufügen></td></tr></table><br>";
	}
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=500>
	<tr><td class=m align=center>Titel (optional)</td></tr>
	<tr><td><input type=text size=60 name=titel class=text value=\"".str_replace("\"","",format_string(stripslashes($_GET['titel'])))."\"></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td class=m align=center>Beitrag</td></tr>
	<tr><td><textarea cols=60 rows=20 name=text>".format_string(stripslashes($_GET['text']))."</textarea></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>Lesezeichen setzen? <input type=checkbox name=lz value=1".($_GET['lz'] == 1 ? " CHECKED" : "")."></td></td>
	<tr><td>&nbsp;</td></tr>";
	if ($_GET[ab] != "Hinzufügen") echo "<tr><td><input type=submit value=Hinzufügen name=ab class=button>&nbsp;<input type=submit name=ab value=Vorschau class=button></td></tr>";
	echo "</form></table>";
}
if ($v == "searchkn")
{
	pageheader("/ <a href=?p=comm>Kommunikation</a> / <a href=?p=comm&s=kn>Kommunikations Netzwerk</a> / <b>Beitrag suchen</b>");
	if ($_GET[sbn] > 0 && is_numeric($_GET[sbn])) $result = $comm->getknmsgbyid($_GET[sbn]);
	if ($_GET[sbs] != "" && !$_GET[sbn]) $result = $comm->getknmsgbystring(strip_tags($_GET[sbs]));
	if (mysql_num_rows($result) == 0) meldung("Es wurde kein Beitrag gefunden");
	else
	{
		$i = 0;
		while($data=mysql_fetch_assoc($result))
		{
			$i++;
			$knp = composeEntry($i,$data);
			$knp .= "<br><br>";
			$kn = $knp.$kn;
			echo $kn;
		}
	}
}
if ($v == "asearchkn")
{
	pageheader("/ <a href=?p=comm>Kommunikation</a> / <a href=?p=comm&s=akn>Allianz Kommunikations Netzwerk</a> / <b>Beitrag suchen</b>");
	if ($_GET[sbn] > 0 && check_int($_GET[sbn])) $result = $comm->agetknmsgbyid($_GET[sbn]);
	if ($_GET[sbs] != "" && !$_GET[sbn]) $result = $comm->agetknmsgbystring(strip_tags($_GET[sbs]));
	if (mysql_num_rows($result) == 0) meldung("Es wurde kein Beitrag gefunden");
	else
	{
		while($data=mysql_fetch_assoc($result))
		{
			echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=750>
			<tr><td class=m colspan=3>".stripslashes($data[titel])."</td></tr>
			<tr><td colspan=2 width=620><img src=".$gfx."/rassen/".($data['race']."".($data[subrace] != 0 ? "_".$data[subrace] : ""))."s.gif  title='".addslashes(getracename($data['race'],$data['subrace']))."'> ".stripslashes($data[username])." (".$data[user_id].") <a href=?p=comm&s=akn&a=slz&lz=".$data[id]." onmouseover=cp('lz".$i."','buttons/lese2') onmouseout=cp('lz".$i."','buttons/lese1')><img src=".$gfx."/buttons/lese1.gif name=lz".$i." border=0></a></td>
			<td class=m width=130>".date("d.m.Y H:i",$data['date'])."</td></tr></table>
			<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=750>
			<tr><td width=120 valign=top><div style=\"text-align: center\">".(strlen($data[propic]) > 10 ? "<img src=\"".$data[propic]."\" width=100 height=100>" : "<img src=".$gfx."/rassen/".$data[race]."kn.png>")."<br>".($data[id] > $_SESSION["akn_lez"] ? "<font size=-3 color=#ff0000>".$data[id]."</font>" : "<font size=-3>".$data[id]."</font>")."</font></div></td>
			<td width=580 valign=top colspan=2>".nl2br(stripslashes($data[text]))."</td></tr>
			</table><br>";
		}
	}
}
if ($v == "rsearchkn")
{
	pageheader("/ <a href=?p=comm>Kommunikation</a> / <a href=?p=comm&s=rkn>Rassen Kommunikations Netzwerk</a> / <b>Beitrag suchen</b>");
	if ($_GET[sbn] > 0 && is_numeric($_GET[sbn])) $result = $comm->rgetknmsgbyid($_GET[sbn]);
	if ($_GET[sbs] != "" && !$_GET[sbn]) $result = $comm->rgetknmsgbystring(strip_tags($_GET[sbs]));
	if (mysql_num_rows($result) == 0) meldung("Es wurde kein Beitrag gefunden");
	else
	{
		while($data=mysql_fetch_assoc($result))
		{
			echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=750>
			<tr><td class=m colspan=3>".stripslashes($data[titel])."</td></tr>
			<tr><td colspan=2 width=620><img src=".$gfx."/rassen/".($data['race']."".($data[subrace] != 0 ? "_".$data[subrace] : ""))."s.gif  title='".addslashes(getracename($data['race'],$data['subrace']))."'> ".stripslashes($data[username])." (".$data[user_id].") <a href=?p=comm&s=rkn&a=slz&lz=".$data[id]." onmouseover=cp('lz".$i."','buttons/lese2') onmouseout=cp('lz".$i."','buttons/lese1')><img src=".$gfx."/buttons/lese1.gif name=lz".$i." border=0></a></td>
			<td class=m width=130>".date("d.m.Y H:i",$data['date'])."</td></tr></table>
			<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=750>
			<tr><td width=120 valign=top><div style=\"text-align: center\">".(strlen($data[propic]) > 10 ? "<img src=\"".$data[propic]."\" width=100 height=100>" : "<img src=".$gfx."/rassen/".$data[race]."kn.png>")."<br>".($data[id] > $_SESSION["rkn_lez"] ? "<font size=-3 color=#ff0000>".$data[id]."</font>" : "<font size=-3>".$data[id]."</font>")."</font></div></td>
			<td width=580 valign=top colspan=2>".nl2br(stripslashes($data[text]))."</td></tr>
			</table><br>";
		}
	}
}
if ($v == "editkn")
{
	$_GET[beitrag][titel] = stripslashes($_GET[beitrag][titel]);
	$_GET[beitrag][text] = stripslashes($_GET[beitrag][text]);
	pageheader("/ <a href=?p=comm>Kommunikation</a> / <a href=?p=comm&s=kn>Kommunikations Netzwerk</a> / <b>Beitrag editieren</b>");
	if (!$_GET[id] || !is_numeric($_GET[id])) exit;
	else
	{
		$result = $comm->getknmsgbyid($_GET[id]);
		if (mysql_num_rows($result) == 0) exit;
		$data = mysql_fetch_assoc($result);
		if ($data['date'] < time()-600 || $data[user_id] != $_SESSION[uid])
		{
			meldung("Dieser Beitrag ist nicht editierbar");
			exit;
		}
		if (is_array($_GET[beitrag]) && $_GET[kn] == 1)
		{
			if (!check_int($_GET[id])) exit;
			elseif ($_GET[refe] && !eregi("[0-9],",$_GET[refe]) && !check_int($_GET['refe'])) meldung("Die KN-Bezüge sind fehlerhaft");
			if (!$_GET[beitrag][text]) meldung("Es wurde kein Text eingegeben");
			else
			{
				$comm->editknmsg(str_replace("\"","",format_string($_GET[beitrag][titel])),format_string($_GET[beitrag][text]),$_GET[refe],$_GET[id]);
				meldung("Beitrag editiert");
				$data = mysql_fetch_assoc($comm->getknmsgbyid($_GET[id]));
			}
		}
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=500>
		<form action=main.php method=post><input type=hidden name=kn value=1>
		<input type=hidden name=p value=comm><input type=hidden name=s value=eb><input type=hidden name=id value=".$_GET[id].">
		<tr><td class=m align=center>Titel (optional)</td></tr>
		<tr><td><input type=text size=60 name=beitrag[titel] class=text value=\"".stripslashes($data[titel])."\"></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td class=m align=center>Beitrag</td></tr>
		<tr><td><textarea cols=60 rows=20 name=beitrag[text]>".stripslashes($data[text])."</textarea></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>Auf KN-Postings beziehen <input type=text size=20 class=text name=refe value=".$data[refe]."> (max 7, mit Komma trennen)</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td><input type=submit value=Editieren class=button></td></tr>
		</form></table>";
	}
}
if ($v == "aeditkn")
{
	$_GET[beitrag][titel] = stripslashes($_GET[beitrag][titel]);
	$_GET[beitrag][text] = stripslashes($_GET[beitrag][text]);
	pageheader("/ <a href=?p=comm>Kommunikation</a> / <a href=?p=comm&s=akn>Allianz Kommunikations Netzwerk</a> / <b>Beitrag editieren</b>");
	if (!$_GET[id] || !is_numeric($_GET[id])) exit;
	else
	{
		$result = $comm->agetknmsgbyid($_GET[id]);
		if (mysql_num_rows($result) == 0) exit;
		$data = mysql_fetch_assoc($result);
		if ($data['date'] < time()-600 || $data[user_id] != $_SESSION[uid])
		{
			meldung("Dieser Beitrag ist nicht editierbar");
			exit;
		}
		if (is_array($_GET[beitrag]) && $_GET[kn] == 1)
		{
			if (!check_int($_GET[id])) exit;
			if (!$_GET[beitrag][text]) meldung("Es wurde kein Text eingegeben");
			else
			{
				$comm->aeditknmsg(str_replace("\"","",format_string($_GET[beitrag][titel])),format_string($_GET[beitrag][text]),$_GET[id]);
				meldung("Beitrag editiert");
				$data = mysql_fetch_assoc($comm->agetknmsgbyid($_GET[id]));
			}
		}
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=500>
		<form action=main.php method=post><input type=hidden name=kn value=1>
		<input type=hidden name=p value=comm><input type=hidden name=s value=aeb><input type=hidden name=id value=".$_GET[id].">
		<tr><td class=m align=center>Titel (optional)</td></tr>
		<tr><td><input type=text size=60 name=beitrag[titel] class=text value=\"".stripslashes($data[titel])."\"></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td class=m align=center>Beitrag</td></tr>
		<tr><td><textarea cols=60 rows=20 name=beitrag[text]>".stripslashes($data[text])."</textarea></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td><input type=submit value=Editieren class=button></td></tr>
		</form></table>";
	}
}
if ($v == "reditkn")
{
	$_GET[beitrag][titel] = stripslashes($_GET[beitrag][titel]);
	$_GET[beitrag][text] = stripslashes($_GET[beitrag][text]);
	pageheader("/ <a href=?p=comm>Kommunikation</a> / <a href=?p=comm&s=rkn>Rassen Kommunikations Netzwerk</a> / <b>Beitrag editieren</b>");
	if (!$_GET[id] || !is_numeric($_GET[id])) exit;
	else
	{
		$result = $comm->rgetknmsgbyid($_GET[id]);
		if (mysql_num_rows($result) == 0) exit;
		$data = mysql_fetch_assoc($result);
		if ($data['date'] < time()-600 || $data[user_id] != $_SESSION[uid])
		{
			meldung("Dieser Beitrag ist nicht editierbar");
			exit;
		}
		if (is_array($_GET[beitrag]) && $_GET[kn] == 1)
		{
			if (!check_int($_GET[id])) exit;
			if (!$_GET[beitrag][text]) meldung("Es wurde kein Text eingegeben");
			else
			{
				$comm->reditknmsg(str_replace("\"","",format_string($_GET[beitrag][titel])),format_string($_GET[beitrag][text]),$_GET[id]);
				meldung("Beitrag editiert");
				$data = mysql_fetch_assoc($comm->rgetknmsgbyid($_GET[id]));
			}
		}
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=500>
		<form action=main.php method=post><input type=hidden name=kn value=1>
		<input type=hidden name=p value=comm><input type=hidden name=s value=reb><input type=hidden name=id value=".$_GET[id].">
		<tr><td class=m align=center>Titel (optional)</td></tr>
		<tr><td><input type=text size=60 name=beitrag[titel] class=text value=\"".stripslashes($data[titel])."\"></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td class=m align=center>Beitrag</td></tr>
		<tr><td><textarea cols=60 rows=20 name=beitrag[text]>".stripslashes($data[text])."</textarea></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td><input type=submit value=Editieren class=button></td></tr>
		</form></table>";
	}
}
if ($v == "postein")
{
	!$_GET['cat'] || !is_numeric($_GET['cat']) ? $cat = 1 : $cat = $_GET['cat'];
	pageheader("/ <a href=?p=comm>Kommunikation</a> / <b>Posteingang</b>");
	if ($_GET['a'] == "dpm" && is_array($_GET['dpm']) && $_GET['del'])
	{
		foreach ($_GET['dpm'] as $key => $value) $comm->delpm($value);
		meldung("Nachrichten gelöscht");
	}
	if ($_GET['a'] == "dap")
	{
		$comm->delapm($cat);
		meldung("Alle Nachrichten wurden gelöscht");
	}
	if ($_GET['a'] == "mar")
	{
		$comm->markallasread($cat);
		meldung("Alle Nachrichten der Kategorie wurden als gelesen markiert");
	}
	if ($_GET['mau'])
	{
		foreach ($_GET['dpm'] as $key => $value) $comm->markasunread($value);
		meldung("Nachrichten als ungelesen markiert");
	}
	!check_int($_GET['pa']) || $_GET['pa'] == 0 || !check_int($_GET['pa']) ? $pa = 1 : $pa = $_GET['pa'];
	$_GET['cat'] >= 1 && $_GET['cat'] <= 5 ? $result = $comm->getpms($_GET['cat'],$pa) : $result = $comm->getpms(1,1);
	echo "<script>
	function markmessages()
	{
		for(var i=0;i<document.pms.length;++i)
		{
			document.forms.pms.elements[i].checked = true;
		}
	}
	</script>
	<table cellspacing=1 cellpadding=1 width=850>
	<form action=main.php method=get name=\"pms\"><input type=hidden name=p value=comm><input type=hidden name=s value=pe><input type=hidden name=a value=dpm>
	<input type=hidden name=cat value=".$cat."><input type=hidden name=m value=".$m.">
	<tr>
	<td valign=top width=550>";
	if (mysql_num_rows($result) == 0) meldung("Keine Nachrichten vorhanden");
	else
	{
		// Seiten erzeugen
		$in = $pa;
		$i = $in-2;
		$j = $in+2;
		$ceiled_knc = ceil($comm->getpmccat($cat)/10);
		$ps0 = "<td>Seite: <a href=?p=comm&s=pe&cat=".$cat."&pa=1><<</a> <a href=?p=comm&s=pe&cat=".$cat."&pa=".($pa == 1 ? 1 : $pa-1)."><</a></td>";
		if ($i > 1) $ps = "<td class=\"pages\"><a href=?p=comm&s=pe&cat=".$cat."&pa=1>1</a></td>";
		if ($j < $ceiled_knc) $pe = "<td class=\"pages\"><a href=?p=comm&s=pe&cat=".$cat."&pa=".$ceiled_knc.">".$ceiled_knc."</a></td>";
		if ($j > $ceiled_knc) $j = $ceiled_knc;
		if ($i < 1) $i = 1;
		while($i<=$j)
		{
			$pages .= "<td class=\"pages\"><a href=?p=comm&s=pe&cat=".$cat."&pa=".$i.">".($i == $in ? "<div style=\"font-weight : bold; color: Yellow;\">".$i."</div>" : $i)."</a></td>";
			$i++;
		}
		$i = $in-2;
		$j = $in+2;
		$pages = $ps.($i > 2 ? "<td style=\"width: 20px; text-align: center;\">...</td>" : "").$pages.($ceiled_knc > $j+1 ? "<td style=\"width: 20px; text-align: center;\">... </td>" : "").$pe;
		$pe0 = "<td><a href=?p=comm&s=pe&cat=".$cat."&pa=".($pa == $ceiled_knc ? 1 : $pa+1).">></a>&nbsp;<a href=?p=comm&s=pe&cat=".$cat."&pa=".$ceiled_knc.">>></a></td>";

		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr>".$ps0.$pages.$pe0."</tr></table><br />";
		while ($data=mysql_fetch_assoc($result))
		{
			$i++;
			if ($data['new'] == 1) $comm->markasread($data['id']);
			echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=650>
			<tr>
				<th colspan=\"3\">".stripslashes($data['user'])." (".$data['send_user'].")</th>
			</tr>
			<tr>
				<td valign=\"top\" width=\"100\" rowspan=\"2\" align=\"center\">".(strlen($data['propic']) > 10 ? "<img src=\"".$data['propic']."\" width=100 height=100>" : "<img src=".$gfx."/rassen/".(!$data['race'] ? 9 : $data['race'])."kn.png>")."<br />
				 <a href=\"javascript:void(0);\" onClick=\"opensi(".$data['send_user'].");\" ".getonm("id".$i,"buttons/info")."><img src=".$gfx."/buttons/info1.gif name=id".$i." border=0 title='Spielerprofil'></a> <a href=?p=comm&s=nn&recipient=".$data['send_user']."&rpl=".$data['id']." onmouseover=cp('re".$i."','buttons/msg2') onmouseout=cp('re".$i."','buttons/msg1')><img src=".$gfx."/buttons/msg1.gif name=re".$i." title='".ftit($data['user'])." antworten' border=0></a></td>
				<td style=\"width: 400px; height: 20px;\">".($data['replied'] == 1 ? " <img src=".$gfx."/buttons/pm_reply.gif title=\"Diese Nachricht wurde bereits beantwortet\">" : "")." ".($data['new'] == 1 ? "<img src=".$gfx."/buttons/postein1.gif title=\"Neu\"> " : "")."</td>
				<td>".date("d.m.",$data['date']).setyear(date("Y",$data['date'])).date(" H:i",$data['date'])." <input type=checkbox name=dpm[] value=".$data['id']."></td>
			</tr>
			<tr>
				<td colspan=\"2\" valign=\"top\">".nl2br(stripslashes($data['text']))."</td>
			</tr>
			</table><br>";
		}
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr>".$ps0.$pages.$pe0."</tr></table>";
	}
	$c1n = $comm->getnewpmccat(1);
	$c2n = $comm->getnewpmccat(2);
	$c3n = $comm->getnewpmccat(3);
	$c4n = $comm->getnewpmccat(4);
	$c5n = $comm->getnewpmccat(5);
	$c1 = $comm->getpmccat(1);
	$c2 = $comm->getpmccat(2);
	$c3 = $comm->getpmccat(3);
	$c4 = $comm->getpmccat(4);
	$c5 = $comm->getpmccat(5);
	echo "</td><td class=d width=20>&nbsp;</td>
	<td valign=top>
	<a href=?p=comm&s=pe&a=mar&cat=".$cat." onmouseover=cp('ar','buttons/lese2') onmouseout=cp('ar','buttons/lese1')><img src=".$gfx."/buttons/lese1.gif name=ar border=0 title='Alle als gelesen markieren'></a>
	<a href=?p=comm&s=nn onmouseover=cp('msg','buttons/msg2') onmouseout=cp('msg','buttons/msg1')><img src=".$gfx."/buttons/msg1.gif name=msg border=0 title='Neue Nachricht schreiben'></a>
	<a href=?p=comm&s=pa onmouseover=cp('pa','buttons/postaus2') onmouseout=cp('pa','buttons/postaus1')><img src=".$gfx."/buttons/postaus1.gif name=pa border=0 title='Postausgang'></a>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=?p=comm&s=pe&a=dap&cat=".$cat." onmouseover=cp('da','buttons/x2') onmouseout=cp('da','buttons/x1')><img src=".$gfx."/buttons/x1.gif name=da border=0 title='Alle Nachrichten löschen'></a>
	<br><br>
	".($cat == 1 ? "<font color=#ffff00>Privat</font>" : "<a href=?p=comm&s=pe&cat=1>Privat</a>")." (".($c1n > 0 ? "<font color=FF0000>".$c1n."</font>" : 0)."/".$c1.")<br>
	".($cat == 2 ? "<font color=#ffff00>Handel</font>" : "<a href=?p=comm&s=pe&cat=2>Handel</a>")." (".($c2n > 0 ? "<font color=FF0000>".$c2n."</font>" : 0)."/".$c2.")<br>
	".($cat == 3 ? "<font color=#ffff00>Schiffe</font>" : "<a href=?p=comm&s=pe&cat=3>Schiffe</a>")." (".($c3n > 0 ? "<font color=FF0000>".$c3n."</font>" : 0)."/".$c3.")<br>
	".($cat == 4 ? "<font color=#ffff00>Kolonien</font>" : "<a href=?p=comm&s=pe&cat=4>Kolonien</a>")." (".($c4n > 0 ? "<font color=FF0000>".$c4n."</font>" : 0)."/".$c4.")<br>
	".($cat == 5 ? "<font color=#ffff00>Stationen</font>" : "<a href=?p=comm&s=pe&cat=5>Stationen</a>")." (".($c5n > 0 ? "<font color=FF0000>".$c5n."</font>" : 0)."/".$c5.")<br>
	<br><input type=button class=button value=\"Alle markieren\" onClick=\"markmessages();\">
	<br><input type=submit class=button value=\"Ungelesen\" name=mau>
	<br><input type=submit class=button value=löschen name=del></td></tr></form>
	</table>";
}
if ($v == "postein_search")
{
	pageheader("/ <a href=?p=comm>Kommunikation</a> / <a href=?p=comm&s=pe>Posteingang</a> / <b>Suche</b>");
	if ($_GET['a'] == "dpm" && is_array($_GET['dpm']) && $_GET['del'])
	{
		foreach ($_GET['dpm'] as $key => $value) $comm->delpm($value);
		meldung("Nachrichten gelöscht");
	}
	if ($_GET['mau'])
	{
		foreach ($_GET['dpm'] as $key => $value) $comm->markasunread($value);
		meldung("Nachrichten als ungelesen markiert");
	}
	!check_int($_GET['pa']) || $_GET['pa'] == 0 || !check_int($_GET['pa']) ? $pa = 1 : $pa = $_GET['pa'];
	$result = $comm->getsearchpms($_SESSION['ss'],$pa);
	echo "<script>
	function markmessages()
	{
		for(var i=0;i<document.pms.length;++i)
		{
			document.forms.pms.elements[i].checked = true;
		}
	}
	</script>
	<table cellspacing=1 cellpadding=1 width=850>
	<form action=main.php method=get name=\"pms\"><input type=hidden name=p value=comm><input type=hidden name=s value=pe><input type=hidden name=a value=dpm>
	<input type=hidden name=cat value=".$cat."><input type=hidden name=m value=".$m.">
	<tr>
	<td valign=top width=550>";
	if ($result == 0 || mysql_num_rows($result) == 0) meldung("Es wurden keine Nachrichten mit diesen Begriffen gefunden<br />Der Suchbegriff muss mindestens 3 Zeichen lang sein");
	else
	{
		// Seiten erzeugen
		$in = $pa;
		$i = $in-2;
		$j = $in+2;
		$ceiled_knc = ceil($comm->spmc/10);
		$ps0 = "<td>Seite: <a href=?p=comm&s=pes&cat=".$cat."&pa=1><<</a> <a href=?p=comm&s=pe&cat=".$cat."&pa=".($pa == 1 ? 1 : $pa-1)."><</a></td>";
		if ($i > 1) $ps = "<td class=\"pages\"><a href=?p=comm&s=pes&cat=".$cat."&pa=1>1</a></td>";
		if ($j < $ceiled_knc) $pe = "<td class=\"pages\"><a href=?p=comm&s=pes&cat=".$cat."&pa=".$ceiled_knc.">".$ceiled_knc."</a></td>";
		if ($j > $ceiled_knc) $j = $ceiled_knc;
		if ($i < 1) $i = 1;
		while($i<=$j)
		{
			$pages .= "<td class=\"pages\"><a href=?p=comm&s=pes&cat=".$cat."&pa=".$i.">".($i == $in ? "<div style=\"font-weight : bold; color: Yellow;\">".$i."</div>" : $i)."</a></td>";
			$i++;
		}
		$i = $in-2;
		$j = $in+2;
		$pages = $ps.($i > 2 ? "<td style=\"width: 20px; text-align: center;\">...</td>" : "").$pages.($ceiled_knc > $j+1 ? "<td style=\"width: 20px; text-align: center;\">... </td>" : "").$pe;
		$pe0 = "<td><a href=?p=comm&s=pes&cat=".$cat."&pa=".($pa == $ceiled_knc ? 1 : $pa+1).">></a>&nbsp;<a href=?p=comm&s=pes&cat=".$cat."&pa=".$ceiled_knc.">>></a></td>";

		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr>".$ps0.$pages.$pe0."</tr></table><br />";
		while ($data=mysql_fetch_assoc($result))
		{
			$i++;
			if ($data['new'] == 1) $comm->markasread($data['id']);
			echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=650>
			<tr>
				<th colspan=\"3\">".stripslashes($data['user'])." (".$data['send_user'].")</th>
			</tr>
			<tr>
				<td valign=\"top\" width=\"100\" rowspan=\"2\" align=\"center\">".(strlen($data['propic']) > 10 ? "<img src=\"".$data['propic']."\" width=100 height=100>" : "<img src=".$gfx."/rassen/".(!$data['race'] ? 9 : $data['race'])."kn.png>")."<br />
				 <a href=\"javascript:void(0);\" onClick=\"opensi(".$data['send_user'].");\" ".getonm("id".$i,"buttons/info")."><img src=".$gfx."/buttons/info1.gif name=id".$i." border=0 title='Spielerprofil'></a> <a href=?p=comm&s=nn&recipient=".$data['send_user']."&rpl=".$data['id']." onmouseover=cp('re".$i."','buttons/msg2') onmouseout=cp('re".$i."','buttons/msg1')><img src=".$gfx."/buttons/msg1.gif name=re".$i." title='".ftit($data['user'])." antworten' border=0></a></td>
				<td style=\"width: 400px; height: 20px;\">".($data['replied'] == 1 ? " <img src=".$gfx."/buttons/pm_reply.gif title=\"Diese Nachricht wurde bereits beantwortet\">" : "")." ".($data['new'] == 1 ? "<img src=".$gfx."/buttons/postein1.gif title=\"Neu\"> " : "")."</td>
				<td>".date("d.m.",$data['date']).setyear(date("Y",$data['date'])).date(" H:i",$data['date'])." <input type=checkbox name=dpm[] value=".$data['id']."></td>
			</tr>
			<tr>
				<td colspan=\"2\" valign=\"top\">".nl2br(stripslashes($data['text']))."</td>
			</tr>
			</table><br>";
		}
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr>".$ps0.$pages.$pe0."</tr></table>";
	}
	$c1n = $comm->getnewpmccat(1);
	$c2n = $comm->getnewpmccat(2);
	$c3n = $comm->getnewpmccat(3);
	$c4n = $comm->getnewpmccat(4);
	$c1 = $comm->getpmccat(1);
	$c2 = $comm->getpmccat(2);
	$c3 = $comm->getpmccat(3);
	$c4 = $comm->getpmccat(4);
	echo "</td><td class=d width=20>&nbsp;</td>
	<td valign=top>
	<a href=?p=comm&s=pe&a=mar&cat=".$cat." onmouseover=cp('ar','buttons/lese2') onmouseout=cp('ar','buttons/lese1')><img src=".$gfx."/buttons/lese1.gif name=ar border=0 title='Alle als gelesen markieren'></a>
	<a href=?p=comm&s=nn onmouseover=cp('msg','buttons/msg2') onmouseout=cp('msg','buttons/msg1')><img src=".$gfx."/buttons/msg1.gif name=msg border=0 title='Neue Nachricht schreiben'></a>
	<a href=?p=comm&s=pa onmouseover=cp('pa','buttons/postaus2') onmouseout=cp('pa','buttons/postaus1')><img src=".$gfx."/buttons/postaus1.gif name=pa border=0 title='Postausgang'></a>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=?p=comm&s=pe&a=dap&cat=".$cat." onmouseover=cp('da','buttons/x2') onmouseout=cp('da','buttons/x1')><img src=".$gfx."/buttons/x1.gif name=da border=0 title='Alle Nachrichten löschen'></a>
	<br><br>
	".($cat == 1 ? "<font color=#ffff00>Privat</font>" : "<a href=?p=comm&s=pe&cat=1>Privat</a>")." (".($c1n > 0 ? "<font color=FF0000>".$c1n."</font>" : 0)."/".$c1.")<br>
	".($cat == 2 ? "<font color=#ffff00>Handel</font>" : "<a href=?p=comm&s=pe&cat=2>Handel</a>")." (".($c2n > 0 ? "<font color=FF0000>".$c2n."</font>" : 0)."/".$c2.")<br>
	".($cat == 3 ? "<font color=#ffff00>Schiffe</font>" : "<a href=?p=comm&s=pe&cat=3>Schiffe</a>")." (".($c3n > 0 ? "<font color=FF0000>".$c3n."</font>" : 0)."/".$c3.")<br>
	".($cat == 4 ? "<font color=#ffff00>Kolonien</font>" : "<a href=?p=comm&s=pe&cat=4>Kolonien</a>")." (".($c4n > 0 ? "<font color=FF0000>".$c4n."</font>" : 0)."/".$c4.")<br>
	<br /><font color=#ffff00>Ergebnisse: ".$comm->spmc."</font><br />
	<br><input type=button class=button value=\"Alle markieren\" onClick=\"markmessages();\">
	<br><input type=submit class=button value=\"Ungelesen\" name=mau>
	<br><input type=submit class=button value=löschen name=del></td></tr></form>
	</table>";
}
if ($v == "postein_searchuser")
{
	pageheader("/ <a href=?p=comm>Kommunikation</a> / <a href=?p=comm&s=pe>Posteingang</a> / <b>Korrespondenz mit ID</b>");
	if ($_GET['a'] == "dpm" && is_array($_GET['dpm']) && $_GET['del'])
	{
		foreach ($_GET['dpm'] as $key => $value) $comm->delpm($value);
		meldung("Nachrichten gelöscht");
	}
	if ($_GET['mau'])
	{
		foreach ($_GET['dpm'] as $key => $value) $comm->markasunread($value);
		meldung("Nachrichten als ungelesen markiert");
	}
	!check_int($_GET['pa']) || $_GET['pa'] == 0 || !check_int($_GET['pa']) ? $pa = 1 : $pa = $_GET['pa'];
	$result = $comm->getupms($_SESSION['si'],$pa);
	echo "<script>
	function markmessages()
	{
		for(var i=0;i<document.pms.length;++i)
		{
			document.forms.pms.elements[i].checked = true;
		}
	}
	</script>
	<table cellspacing=1 cellpadding=1 width=850>
	<form action=main.php method=get name=\"pms\"><input type=hidden name=p value=comm><input type=hidden name=s value=pe><input type=hidden name=a value=dpm>
	<input type=hidden name=cat value=".$cat."><input type=hidden name=m value=".$m.">
	<tr>
	<td valign=top width=550>";
	if ($result == 0 || mysql_num_rows($result) == 0) meldung("Es wurde keine Korrespondenz mit diesem Siedler gefunden");
	else
	{
		// Seiten erzeugen
		$in = $pa;
		$i = $in-2;
		$j = $in+2;
		$ceiled_knc = ceil($comm->spmc/10);
		$ps0 = "<td>Seite: <a href=?p=comm&s=pei&cat=".$cat."&pa=1><<</a> <a href=?p=comm&s=pe&cat=".$cat."&pa=".($pa == 1 ? 1 : $pa-1)."><</a></td>";
		if ($i > 1) $ps = "<td class=\"pages\"><a href=?p=comm&s=pei&cat=".$cat."&pa=1>1</a></td>";
		if ($j < $ceiled_knc) $pe = "<td class=\"pages\"><a href=?p=comm&s=pei&cat=".$cat."&pa=".$ceiled_knc.">".$ceiled_knc."</a></td>";
		if ($j > $ceiled_knc) $j = $ceiled_knc;
		if ($i < 1) $i = 1;
		while($i<=$j)
		{
			$pages .= "<td class=\"pages\"><a href=?p=comm&s=pei&cat=".$cat."&pa=".$i.">".($i == $in ? "<div style=\"font-weight : bold; color: Yellow;\">".$i."</div>" : $i)."</a></td>";
			$i++;
		}
		$i = $in-2;
		$j = $in+2;
		$pages = $ps.($i > 2 ? "<td style=\"width: 20px; text-align: center;\">...</td>" : "").$pages.($ceiled_knc > $j+1 ? "<td style=\"width: 20px; text-align: center;\">... </td>" : "").$pe;
		$pe0 = "<td><a href=?p=comm&s=pei&cat=".$cat."&pa=".($pa == $ceiled_knc ? 1 : $pa+1).">></a>&nbsp;<a href=?p=comm&s=pei&cat=".$cat."&pa=".$ceiled_knc.">>></a></td>";

		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr>".$ps0.$pages.$pe0."</tr></table><br />";
		while ($data=mysql_fetch_assoc($result))
		{
			$i++;
			if ($data['new'] == 1) $comm->markasread($data['id']);
			echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=650>
			<tr>
				<th colspan=\"3\">".stripslashes($data['user'])." (".$data['send_user'].")</th>
			</tr>
			<tr>
				<td valign=\"top\" width=\"100\" rowspan=\"2\" align=\"center\">".(strlen($data['propic']) > 10 ? "<img src=\"".$data['propic']."\" width=100 height=100>" : "<img src=".$gfx."/rassen/".(!$data['race'] ? 9 : $data['race'])."kn.png>")."<br />
				 <a href=\"javascript:void(0);\" onClick=\"opensi(".$data['send_user'].");\" ".getonm("id".$i,"buttons/info")."><img src=".$gfx."/buttons/info1.gif name=id".$i." border=0 title='Spielerprofil'></a> <a href=?p=comm&s=nn&recipient=".$data['send_user']."&rpl=".$data['id']." onmouseover=cp('re".$i."','buttons/msg2') onmouseout=cp('re".$i."','buttons/msg1')><img src=".$gfx."/buttons/msg1.gif name=re".$i." title='".ftit($data['user'])." antworten' border=0></a></td>
				<td style=\"width: 400px; height: 20px;\">".($data['send_user'] == $_SESSION['uid'] ? "Gesendet" : "Empfangen")." ".($data['replied'] == 1 ? " <img src=".$gfx."/buttons/pm_reply.gif title=\"Diese Nachricht wurde bereits beantwortet\">" : "")." ".($data['new'] == 1 ? "<img src=".$gfx."/buttons/postein1.gif title=\"Neu\"> " : "")."</td>
				<td>".date("d.m.",$data['date']).setyear(date("Y",$data['date'])).date(" H:i",$data['date'])." <input type=checkbox name=dpm[] value=".$data['id']."></td>
			</tr>
			<tr>
				<td colspan=\"2\" valign=\"top\">".nl2br(stripslashes($data['text']))."</td>
			</tr>
			</table><br>";
		}
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr>".$ps0.$pages.$pe0."</tr></table>";
	}
	$c1n = $comm->getnewpmccat(1);
	$c2n = $comm->getnewpmccat(2);
	$c3n = $comm->getnewpmccat(3);
	$c4n = $comm->getnewpmccat(4);
	$c1 = $comm->getpmccat(1);
	$c2 = $comm->getpmccat(2);
	$c3 = $comm->getpmccat(3);
	$c4 = $comm->getpmccat(4);
	echo "</td><td class=d width=20>&nbsp;</td>
	<td valign=top>
	<a href=?p=comm&s=pe&a=mar&cat=".$cat." onmouseover=cp('ar','buttons/lese2') onmouseout=cp('ar','buttons/lese1')><img src=".$gfx."/buttons/lese1.gif name=ar border=0 title='Alle als gelesen markieren'></a>
	<a href=?p=comm&s=nn onmouseover=cp('msg','buttons/msg2') onmouseout=cp('msg','buttons/msg1')><img src=".$gfx."/buttons/msg1.gif name=msg border=0 title='Neue Nachricht schreiben'></a>
	<a href=?p=comm&s=pa onmouseover=cp('pa','buttons/postaus2') onmouseout=cp('pa','buttons/postaus1')><img src=".$gfx."/buttons/postaus1.gif name=pa border=0 title='Postausgang'></a>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=?p=comm&s=pe&a=dap&cat=".$cat." onmouseover=cp('da','buttons/x2') onmouseout=cp('da','buttons/x1')><img src=".$gfx."/buttons/x1.gif name=da border=0 title='Alle Nachrichten löschen'></a>
	<br><br>
	".($cat == 1 ? "<font color=#ffff00>Privat</font>" : "<a href=?p=comm&s=pe&cat=1>Privat</a>")." (".($c1n > 0 ? "<font color=FF0000>".$c1n."</font>" : 0)."/".$c1.")<br>
	".($cat == 2 ? "<font color=#ffff00>Handel</font>" : "<a href=?p=comm&s=pe&cat=2>Handel</a>")." (".($c2n > 0 ? "<font color=FF0000>".$c2n."</font>" : 0)."/".$c2.")<br>
	".($cat == 3 ? "<font color=#ffff00>Schiffe</font>" : "<a href=?p=comm&s=pe&cat=3>Schiffe</a>")." (".($c3n > 0 ? "<font color=FF0000>".$c3n."</font>" : 0)."/".$c3.")<br>
	".($cat == 4 ? "<font color=#ffff00>Kolonien</font>" : "<a href=?p=comm&s=pe&cat=4>Kolonien</a>")." (".($c4n > 0 ? "<font color=FF0000>".$c4n."</font>" : 0)."/".$c4.")<br>
	<br /><font color=#ffff00>Ergebnisse: ".$comm->spmc."</font><br />
	<br><input type=button class=button value=\"Alle markieren\" onClick=\"markmessages();\">
	<br><input type=submit class=button value=\"Ungelesen\" name=mau>
	<br><input type=submit class=button value=löschen name=del></td></tr></form>
	</table>";
}
if ($v == "postaus")
{
	!$_GET['cat'] || !is_numeric($_GET['cat']) ? $cat = 1 : $cat = $_GET['cat'];
	!$_GET['m'] || !is_numeric($_GET['m']) ? $m = 0 : $m = $_GET['m'];
	pageheader("/ <a href=?p=comm>Kommunikation</a> / <b>Postausgang</b>");
	if ($_GET['a'] == "dpm" && is_array($_GET['dpm']))
	{
		foreach ($_GET['dpm'] as $key => $value) $comm->delspm($value);
		meldung("Nachrichten gelöscht");
	}
	if ($_GET['a'] == "dap")
	{
		$comm->delaspm($cat);
		meldung("Alle Nachrichten wurden gelöscht");
	}
	$pmc = $db->query("SELECT COUNT(id) FROM stu_pms WHERE type='".$cat."' AND send_user=".$_SESSION['uid']." AND send_del='0'",1);
	if ($m >= $pmc) $m = 0;
	$_GET['cat'] >= 1 && $_GET['cat'] <= 4 ? $result = $comm->getsendpms($_GET['cat'],$m) : $result = $comm->getsendpms(1,$m);
	
	echo "<script>
	function markmessages()
	{
		for(var i=0;i<document.pms.length;++i)
		{
			document.forms.pms.elements[i].checked = true;
		}
	}
	</script>
	<table cellspacing=1 cellpadding=1 width=720>
	<form action=main.php method=get name=\"pms\"><input type=hidden name=p value=comm><input type=hidden name=s value=pa><input type=hidden name=a value=dpm>
	<input type=hidden name=cat value=".$cat."><input type=hidden name=m value=".$m.">
	<tr>
	<td valign=top width=550>";
	if (mysql_num_rows($result) == 0) meldung("Keine Nachrichten vorhanden");
	else
	{
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr><td><a href=?p=comm&s=pa&m=".($m-10 < 1 ? 1 : $m-10)."&cat=".$cat." onmouseover=cp('vor','buttons/b_from2') onmouseout=cp('vor','buttons/b_from1')><img src=".$gfx."/buttons/b_from1.gif name=vor border=0 title='Vorblättern'></a></td>";
		for($i=1;$i<=ceil($pmc/10);$i++)
		{
			$b = floor(($m+10)/10);
			$nv .= "<td width=10>".($b == $i ? "<b>".$i."</b>" : "<a href=?p=comm&s=pa&cat=".$cat."&m=".(($i*10)-10).">".$i."</a>")."</td>";
		}
		echo $nv."<td><a href=?p=comm&s=pa&m=".($m+10)."&cat=".$cat." onmouseover=cp('zu','buttons/b_to2') onmouseout=cp('zu','buttons/b_to1')><img src=".$gfx."/buttons/b_to1.gif name=zu border=0 title='Zurückblättern'></a></td></tr></table><br>";
		while ($data=mysql_fetch_assoc($result))
		{
			$i++;
			echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=550>
			<tr><td width=420>an: ".stripslashes($data['user'])." (".$data['recip_user'].") 
			<a href=?p=comm&s=nn&recipient=".$data['recip_user']." onmouseover=cp('re".$i."','buttons/msg2') onmouseout=cp('re".$i."','buttons/msg1')><img src=".$gfx."/buttons/msg1.gif name=re".$i." title='Nachricht an ".ftit($data['user'])." schreiben' border=0></a> <a href=\"javascript:void(0);\" onClick=\"opensi(".$data['recip_user'].");\" ".getonm("id".$i,"buttons/info")."><img src=".$gfx."/buttons/info1.gif name=id".$i." border=0 title='Spielerprofil'></a>
			</td><td class=m width=130>".date("d.m.",$data['date']).setyear(date("Y",$data['date'])).date(" H:i",$data['date'])."</td><td><input type=checkbox name=dpm[] value=".$data['id']."></td></tr>
			<tr><td colspan=3 width=550>".nl2br(stripslashes($data['text']))."</td></tr>
			</table><br>";
		}
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr><td><a href=?p=comm&s=pa&m=".($m-10 < 1 ? 1 : $m-10)."&cat=".$cat." onmouseover=cp('vord','buttons/b_from2') onmouseout=cp('vord','buttons/b_from1')><img src=".$gfx."/buttons/b_from1.gif name=vord border=0 title='Vorblättern'></a></td>".$nv."<td><a href=?p=comm&s=pa&m=".($m+10)."&cat=".$cat." onmouseover=cp('zud','buttons/b_to2') onmouseout=cp('zud','buttons/b_to1')><img src=".$gfx."/buttons/b_to1.gif name=zud border=0 title='Zurückblättern'></a></td></tr></table>";
	}
	$c1 = $comm->getapmccat(1);
	$c2 = $comm->getapmccat(2);
	$c3 = $comm->getapmccat(3);
	$c4 = $comm->getapmccat(4);
	echo "</td><td class=d width=20>&nbsp;</td>
	<td valign=top>
	<a href=?p=comm&s=nn onmouseover=cp('msg','buttons/msg2') onmouseout=cp('msg','buttons/msg1')><img src=".$gfx."/buttons/msg1.gif name=msg border=0 title='Neue Nachricht schreiben'></a>
	<a href=?p=comm&s=pe onmouseover=cp('pa','buttons/postein2') onmouseout=cp('pa','buttons/postein1')><img src=".$gfx."/buttons/postein1.gif name=pa border=0 title='Posteingang'></a>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=?p=comm&s=pa&a=dap&cat=".$cat." onmouseover=cp('da','buttons/x2') onmouseout=cp('da','buttons/x1')><img src=".$gfx."/buttons/x1.gif name=da border=0 title='Alle Nachrichten löschen'></a>
	<br><br>
	".($cat == 1 ? "<font color=#ffff00>Privat</font>" : "<a href=?p=comm&s=pa&cat=1>Privat</a>")." (".$c1.")<br>
	".($cat == 2 ? "<font color=#ffff00>Handel</font>" : "<a href=?p=comm&s=pa&cat=2>Handel</a>")." (".$c2.")<br>
	".($cat == 3 ? "<font color=#ffff00>Schiffe</font>" : "<a href=?p=comm&s=pa&cat=3>Schiffe</a>")." (".$c3.")<br>
	".($cat == 4 ? "<font color=#ffff00>Kolonien</font>" : "<a href=?p=comm&s=pa&cat=4>Kolonien</a>")." (".$c4.")<br>
	<br><input type=button class=button value=\"Alle markieren\" onClick=\"markmessages();\">
	<br><input type=submit class=button value=löschen></td></tr></form>
	</table>";
}
if ($v == "neuena")
{
	pageheader("/ <a href=?p=comm>Kommunikation</a> / <a href=?p=comm&s=pe>Posteingang</a> / <b>Neue Nachricht</b>");
	if ($_GET['recipient'] && check_int($_GET['recipient']) && $_GET['text'])
	{
		if ($db->query("SELECT id FROM stu_user WHERE id=".$_GET['recipient'],1) == 0) meldung("User existiert nicht");
		elseif ($db->query("SELECT user_id FROM stu_ignorelist WHERE user_id=".$_GET['recipient']." AND recipient=".$_SESSION['uid']." LIMIT 1",1) > 0) meldung("Der Empfäger ignoriert dich");
		else
		{
			$comm->sendpm($_SESSION['uid'],$_GET['recipient'],stripslashes($_GET['text']),1,$_GET['rpl']);
			meldung("Nachricht gesendet");
			$sent = 1;
		}
	}
	if (check_int($_GET['ext1']) && check_int($_GET['ext2'])) $string = $comm->getpmprefix($_GET['ext1'],$_GET['ext2']);
	else $string = "";
	$result = $comm->getcontacts();
	echo "<script language=\"Javascript\">
			function ausgabe() {
				var number=document.pm.rl.selectedIndex;
 					if ((number<0)||(number>=document.pm.rl.options.length)) {
					document.pm.recipient.value=\"\";
				} else {
					var Text=document.pm.rl.options[number].value;
					document.pm.recipient.value=Text;
				}
			}
			</script>";
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=650>
	<form action=main.php method=post name=pm><input type=hidden name=pm value=1>
	<input type=hidden name=p value=comm><input type=hidden name=s value=nn>".($_GET['rpl'] ? "<input type=hidden name=rpl value=".$_GET['rpl'].">" : "")."
	<tr><td class=m>Empfänger</td></tr>
	<tr><td>ID <input type=text size=5 name=recipient class=text value=".(check_int($_GET['recipient']) ? $_GET['recipient'] : "")."> ".(is_numeric($_GET['recipient']) && $_GET['recipient'] > 0 ? stripslashes($u->gf("user",$_GET['recipient'])) : "")." <select name=rl onChange=ausgabe();><option value=>-------------";
	if (mysql_num_rows($result) != 0) while($data=mysql_fetch_assoc($result)) echo "<option value=".$data['recipient'].">".stripslashes(strip_tags($data['user']))." (".$data['recipient'].")";
	echo "</select></td></tr>
	<tr><td class=m align=center>Nachricht</td></tr>
	<tr><td><textarea cols=80 rows=20 name=text>".$string.stripslashes($_GET['text'])."</textarea></td></tr>";
	if ($sent != 1) echo "<tr><td>&nbsp;</td></tr><tr><td><input type=submit value=Senden class=button></td></tr>";
	echo "</form></table>";
	if (check_int($_GET['rpl']))
	{
		$data = $comm->getpmbyid($_GET['rpl']);
		if ($data == 0) exit;
		$i++;
		echo "<br /><table bgcolor=#262323 cellspacing=1 cellpadding=1 width=650>
		<tr>
			<th colspan=\"3\">".stripslashes($data['user'])." (".$data['send_user'].")</th>
		</tr>
		<tr>
			<td valign=\"top\" width=\"100\" rowspan=\"2\" align=\"center\">".(strlen($data['propic']) > 10 ? "<img src=\"".$data['propic']."\" width=100 height=100>" : "<img src=".$gfx."/rassen/".(!$data['race'] ? 9 : $data['race'])."kn.png>")."<br />
			 <a href=\"javascript:void(0);\" onClick=\"opensi(".$data['send_user'].");\" ".getonm("id".$i,"buttons/info")."><img src=".$gfx."/buttons/info1.gif name=id".$i." border=0 title='Spielerprofil'></a></td>
			<td style=\"width: 400px; height: 20px;\">Originalnachricht</td>
			<td>".date("d.m.",$data['date']).setyear(date("Y",$data['date'])).date(" H:i",$data['date'])."</td>
		</tr>
		<tr>
			<td colspan=\"2\" valign=\"top\">".nl2br(stripslashes($data['text']))."</td>
		</tr>
		</table>";
	}
}
if ($v == "notes")
{
	if ($_GET["sent"] == 1)
	{
		if ($_GET["ndd"] == "Löschen") $u->setnotes("");
		elseif (strlen($_GET["nd"]) > 0) $u->setnotes($_GET["nd"]);
	}
	$n = $u->getnotes();
	$n == 0 && is_numeric($n) ? $t = "" : $t = stripslashes($n);
	pageheader("/ <b>Notizen</b>");
	echo "<form action=main.php method=get><input type=hidden name=p value=comm><input type=hidden name=s value=nz><input type=hidden name=sent value=1>
	<table bgcolor=#262323 cellspacing=1 cellpadding=1>
	<tr><td><textarea name=nd rows=40 cols=110>".$t."</textarea></td></tr>
	<tr><td><input type=submit class=button value=Speichern></td></tr>
	</table>
	</form>";
}
if ($v == "editcontacts")
{
	pageheader("/ <a href=?p=comm>Kommunikation</a> / <b>Kontakte editieren</b>");
	if ($_GET["sent"] == 1)
	{
		if (is_array($_GET["us"]))
		{
			foreach($_GET["us"] as $key => $value)
			{
				$comm->update_contact_comment($key,$_GET['ko'.$key]);
				if ($_GET["de"][$key] == 1 && is_numeric($key) && $key > 0)
				{
					$comm->delcontact($key);
					$t .= "Beziehung zu ID ".$key." wurde gelöscht<br>";
					$key = 0;
				}
				if ($_GET["os"][$key] != $value && $value < 4 && $value > 0 && is_numeric($key) && $key > 0)
				{
					$comm->setcontact($key,$value);
					if ($value == 1) $at = "Freund";
					if ($value == 2) $at = "Neutral";
					if ($value == 3) $at = "Feind";
					$t .= "Beziehung zu ID ".$key." geändert - Neuer Status: ".$at."<br>";
				}
			}
		}
		if (is_numeric($_GET["nc"]) && $_GET["nc"] > 0 && $_GET["mode"] > 0 && $_GET["mode"] < 4)
		{
			$r = $comm->setcontact($_GET["nc"],$_GET["mode"]);
			if ($r == 1) $t = "Kontakt erstellt";
		}
	}
	if ($t) meldung($t);
	echo "<form action=main.php method=get><input type=hidden name=p value=comm><input type=hidden name=s value=ec><input type=hidden name=sent value=1><table bgcolor=#262323 cellspacing=1 cellpadding=1>
	<tr><th>Kontakt hinzufügen</th></tr>
	<tr><td>User-ID <input type=text name=nc class=text size=4 value=".(check_int($_GET['recipient']) ? $_GET['recipient'] : "")."> <select name=mode><option value=1>Freund<option value=2>Neutral<option value=3>Feind</select> <input type=submit value=Hinzufügen class=button></td></tr></table></form>";
	$cl = $comm->getcontacts();
	if (mysql_num_rows($cl) == 0) meldung("Keine Kontakte vorhanden");
	else
	{
		echo "<style>
		#tab
		{
			background-color: #171616;
		}
		</style>
		<form action=main.php method=post><input type=hidden name=p value=comm><input type=hidden name=s value=ec><input type=hidden name=sent value=1><input type=hidden name=cl value=1>
		<table bgcolor=#262323 cellspacing=1 cellpadding=1>
		<tr><th><img src=".$gfx."/buttons/x1.gif title=\"Löschen\"></th><th>Siedler</th><th>Status</th><th>Kommentar</th></tr>";
		while($d=mysql_fetch_assoc($cl))
		{
			$j++;
			if ($j == 2)
			{
				$trc = " id=\"tab\"";
				$j = 0;
			}
			else $trc = "";
			echo "<input type=hidden name=os[".$d['recipient']."] value=".$d['mode']."><tr><td align=center".$trc."><input type=checkbox name=de[".$d['recipient']."] value=1></td><td".$trc.">".stripslashes($d['user'])." (".$d['recipient'].")</td><td".$trc."><input type=radio name=us[".$d['recipient']."] value=1".($d['mode'] == 1 ? " CHECKED" : "")."><font color=green>Freund</font> <input type=radio name=us[".$d['recipient']."] value=2".($d['mode'] == 2 ? " CHECKED" : "")."><font color=#FFFFFF>Neutral</font> <input type=radio name=us[".$d['recipient']."] value=3".($d['mode'] == 3 ? " CHECKED" : "")."><font color=#FF0000>Feind</font></td><td".$trc."><input type=text size=50 maxlength=50 class=text name=ko".$d['recipient']." value=\"".stripslashes($d['comment'])."\"></td></tr>";
		}
		echo "<tr><td colspan=4><input type=submit class=button value=Ändern> <input type=reset value=Reset class=button></td></tr></table>";
		mysql_free_result($cl);
	}
	$result = $comm->getenemyuser();
	echo "</form><table bgcolor=#262323 cellspacing=1 cellpadding=1 width=400>
	<th colspan=2>Mir gegenüber feindlich gesinnte Siedler</th>";
	if (mysql_num_rows($result) == 0) echo "<tr><td colspan=2>Keine Einträge vorhanden</td></tr>";
	else
	{
		while($data=mysql_fetch_assoc($result))
		{
			$i++;
			echo "<tr><td>".stripslashes($data['user'])." (".$data['user_id'].")</td><td><a href=?p=comm&s=nn&recipient=".$data[user_id]." ".getonm("pm".$i,"buttons/msg")."><img src=".$gfx."/buttons/msg1.gif name=pm".$i." border=0 title=\"Nachricht an ".ftit($data['user'])." senden\"</a></td></tr>";
		}
	}
	$result = $comm->getfriendlyuser();
	echo "</form></table><br /><table bgcolor=#262323 cellspacing=1 cellpadding=1 width=400>
	<th colspan=2>Mir gegenüber freundlich gesinnte Siedler</th>";
	if (mysql_num_rows($result) == 0) echo "<tr><td colspan=2>Keine Einträge vorhanden</td></tr>";
	else
	{
		while($data=mysql_fetch_assoc($result))
		{
			$i++;
			echo "<tr><td>".stripslashes($data['user'])." (".$data['user_id'].")</td><td><a href=?p=comm&s=nn&recipient=".$data[user_id]." ".getonm("pm".$i,"buttons/msg")."><img src=".$gfx."/buttons/msg1.gif name=pm".$i." border=0 title=\"Nachricht an ".ftit($data['user'])." senden\"</a></td></tr>";
		}
	}
	echo "</table>";
}
if ($v == "notrufe")
{
	pageheader("/ <a href=?p=comm>Kommunikation</a> / <b>Notrufe</b>");
	if ($result) meldung($result);
	if (mysql_num_rows($comm->result) == 0) meldung ("Keine Notrufe vorhanden");
	else
	{
		echo "<table class=tcal cellpadding=1 cellspacing=1>
		<tr>
			<th>User</th>
			<th>Schiff</th>
			<th>Beschreibung</th>
			<th>Datum</th>
			<td></td>
		</tr>";
		while($data=mysql_fetch_assoc($comm->result))
		{
			$i++;
			echo "<tr>
				<td>".stripslashes($data['user'])."</td>
				<td>".stripslashes($data['name'])."</td>
				<td>".stripslashes($data['text'])."</td>
				<td>".date("d.m H:i",$data['date'])."</td>
				<td><a href=?p=comm&s=nn&recipient=".$data['user_id']." ".getonm("msg".$i,"buttons/msg")."><img src=".$gfx."/buttons/msg1.gif name=msg".$i." border=0 title=\"PM an ".ftit($data['user'])." senden\"></a>
				".($_SESSION['uid'] == $data['user_id'] ? "&nbsp;<a href=?p=comm&s=nr&ds=".$data['ships_id']." ".getonm("del".$i,"buttons/x")."><img src=".$gfx."/buttons/x1.gif name=del".$i." border=0 title=\"Notruf löschen\"></a>" : "")."</td>
			</tr>";
		}
		echo "</table>";
	}
}
if ($v == "allykommnet")
{
	$pra = $comm->checkallypresident($_SESSION['uid'],$_SESSION['allys_id']);
	$m = "";
	if ($_GET['a'] == "slz" && is_numeric($_GET['lz']) && $_GET['lz'] > 0) $return = $comm->asetlz($_GET['lz']);
	if ($_GET['a'] == "dem" && check_int($_GET['did']) && $pra > 0) $return = $comm->delaknmsg($_GET['did'],$_SESSION['allys_id']);
	if (check_int($_GET['lez']))
	{
		$lzp = $db->query("SELECT COUNT(id) FROM stu_ally_kn WHERE allys_id=".$_SESSION['allys_id']." AND id<=".$_GET['lez'],1);
		$_GET['m'] = floor($lzp/5)*5;
	}
	if ((check_int($_GET['m']) || $_GET['m'] == 0) && $_GET['m'] >= 0) $m = $_GET['m'];
	$knc = $comm->getaknposts();
	if (!is_numeric($m) || $m > $knc-5) $m = $knc-5;
	if ($m < 0) $m = 0;
	$result = $comm->agetknbymark($m);
	pageheader("/ <a href=?p=comm>Kommunikation</a> / <b>Allianz Kommunikations Netzwerk</b>");
	if ($return != "") meldung($return);
	if (mysql_num_rows($result) == 0) meldung("Es sind keine Beiträge vorhanden");
	else
	{
		while($data=mysql_fetch_assoc($result))
		{
			$i++;
			if ($data['le'] > 0) $data['text'] .= "<br><br><br><div style=\"font-size: 7pt; color: gray;\">Zuletzt editiert: ".date("d.m.Y H:i",$data['le'])."</div>";
			$knp = "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=750>
			<tr><td class=m colspan=2 width=750>".stripslashes($data['titel'])."</td></tr>
			<tr><td width=620><img src=".$gfx."/rassen/".(!$data['ruser_id'] ? 9 : ($data['race']."".($data[subrace] != 0 ? "_".$data[subrace] : "")))."s.gif  title='".addslashes(getracename($data['race'],$data['subrace']))."'> ".stripslashes($data['username'])." (".$data['user_id'].($data['user_id'] < 100 ? " <b>NPC</b>" : "").")</td>
			<td class=m width=130>".date("d.m.",$data['date']).setyear(date("Y",$data['date'])).date(" H:i",$data['date'])."</td></tr></table>
			<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=750>
			<tr><td width=120 valign=top><div style=\"text-align: center;\">".(!$data['ruser_id'] ? "<font color=gray>Gelöscht</font>" : ($data['lastaction'] < time()-300 ? "<font color=#ff0000>Offline</font>" : "<font color=#3F923D>Online</font>"))."<br>".(strlen($data['propic']) > 10 ? "<img src=\"".$data['propic']."\" width=100 height=100>" : "<img src=".$gfx."/rassen/".(!$data['race'] ? 9 : $data['race'])."kn.png>")."<br>".($data['id'] > $_SESSION['akn_lez'] ? "<font size=-3 color=#ff0000>".$data['id']."</font>" : "<font size=-3>".$data['id']."</font>")."</font></div><br>
			<div align=left><a href=?p=comm&s=akn&a=slz&lz=".$data['id']." onmouseover=cp('lz".$i."','buttons/lese2') onmouseout=cp('lz".$i."','buttons/lese1')><img src=".$gfx."/buttons/lese1.gif name=lz".$i." border=0 title='Lesezeichen bei Beitrag ".$data[id]." setzen'></a>";
			if ($data['date'] > time()-600 && $data['user_id'] == $_SESSION['uid']) $knp .= " <a href=?p=comm&s=aeb&id=".$data['id']." onmouseover=cp('ke".$i."','buttons/knedit2') onmouseout=cp('ke".$i."','buttons/knedit1')><img src=".$gfx."/buttons/knedit1.gif name=ke".$i." border=0 title='Beitrag ".$data['id']." editieren'></a>";
			if ($data['user_id'] != 1 && $data['ruser_id']) $knp .= " <a href=?p=comm&s=nn&recipient=".$data['user_id']." ".getonm("msg".$i,"buttons/msg")."><img src=".$gfx."/buttons/msg1.gif name=msg".$i." border=0 title=\"".ftit("PM an ".$data[username]." schicken")."\"></a>&nbsp;<a href=\"javascript:void(0);\" onClick=\"opensi(".$data['user_id'].")\" ".getonm("id".$data['id'],"buttons/info")."><img src=".$gfx."/buttons/info1.gif name=id".$data['id']." border=0 title='Spielerprofil'></a>";
			if ($pra > 0) $knp .= "&nbsp;<a href=?p=comm&s=akn&m=".$m."&a=dem&did=".$data['id']." ".getonm("del".$i,"buttons/x")."><img src=".$gfx."/buttons/x1.gif title=\"Beitrag löschen\" border=0 name=del".$i."></a>";
			$knp .= "</div></td><td width=580 valign=top colspan=2>".nl2br(stripslashes($data['text']))."</td></tr>
			</table><br>";
			$kn = $knp.$kn;
		}
		
		$in = ceil(($m+5)/5);
		$i = $in-2;
		$j = $in+2;
		if ($i > 1) $pe = "<td><a href=?p=comm&s=akn&m=0>1</a></td>";
		if ($j < ceil($knc/5)) $ps = "<td><a href=?p=comm&s=akn&m=".$knc.">".ceil($knc/5)."</a></td>";
		if ($j > ceil($knc/5)) $j = ceil($knc/5);
		if ($i < 1) $i = 1;
		while($i<=$j)
		{
			$pages .= "<td><a href=?p=comm&s=akn&m=".(($j-1)*5).">".($j == $in ? "&nbsp;<b>".$j."</b>&nbsp;" : $j)."</a></td>";
			$j--;
		}
		$i = $in-2;
		$j = $in+2;
		$pages = $ps.(ceil($knc/5) > $j+1 ? "<td>...</td>" : "").$pages.($i > 2 ? "<td>...</td>" : "").$pe;
		
		function getzu($v) { global $gfx,$m; return "<td><a href=?p=comm&s=akn&m=".($m-5 < 0 ? 0 : $m-5)." onmouseover=cp('zu".$v."','buttons/b_to2') onmouseout=cp('zu".$v."','buttons/b_to1')><img src=".$gfx."/buttons/b_to1.gif name=zu".$v." border=0 title=Zurückblättern></a>"; }
		function getvor($v) { global $gfx,$m; return "<td><a href=?p=comm&s=akn&m=".($m+5)." onmouseover=cp('vo".$v."','buttons/b_from2') onmouseout=cp('vo".$v."','buttons/b_from1')><img src=".$gfx."/buttons/b_from1.gif name=vo".$v." border=0 title=Vorblättern></a>"; }
		function getbs($v) { global $gfx; return "<td><a href=?p=comm&s=abs onmouseover=cp('nb".$v."','buttons/knedit2') onmouseout=cp('nb".$v."','buttons/knedit1')><img src=".$gfx."/buttons/knedit1.gif name=nb".$v." border=0 title='Neuen Beitrag schreiben'></a>"; }
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr>".getbs(1)."&nbsp;".getvor(1)."&nbsp;".$pages."&nbsp;".getzu(1)."</tr></table><br>".$kn."<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr>".getbs(2)."&nbsp;".getvor(2)."&nbsp;".$pages."&nbsp;".getzu(2)."</tr></table>";
	}
}
if ($v == "factionkommnet")
{
	$m = "";
	if ($_GET[a] == "slz" && is_numeric($_GET[lz]) && $_GET[lz] > 0) $return = $comm->rsetlz($_GET[lz]);
	if (check_int($_GET[lez]))
	{
		$lzp = $db->query("SELECT COUNT(id) FROM stu_faction_kn WHERE faction='".$_SESSION["race"]."' AND id<=".$_GET[lez],1);
		$_GET[m] = floor($lzp/5)*5;
	}
	if ((check_int($_GET["m"]) || $_GET["m"] == 0) && $_GET["m"] >= 0) $m = $_GET["m"];
	$knc = $comm->getrknposts();
	if (!is_numeric($m) || $m > $knc-5) $m = $knc-5;
	if ($m < 0) $m = 0;
	$result = $comm->rgetknbymark($m);
	pageheader("/ <a href=?p=comm>Kommunikation</a> / <b>Rassen Kommunikations Netzwerk</b>");
	if ($return != "") meldung($return);
	if (mysql_num_rows($result) == 0) meldung("Es sind keine Beiträge vorhanden");
	else
	{
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=750>
		<th>Informationen vom NPC</th>
		<tr>
			<td>".stripslashes(nl2br($db->query("SELECT rkn_text FROM stu_user_profiles WHERE user_id=".($_SESSION['race']+9)." LIMIT 1",1)))."</td>
		</tr>
		</table>";
		while($data=mysql_fetch_assoc($result))
		{
			$i++;
			if ($data[le] > 0) $data[text] .= "<br><br><br><div style=\"font-size: 7pt; color: gray;\">Zuletzt editiert: ".date("d.m.Y H:i",$data[le])."</div>";
			$knp = "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=750>
			<tr><td class=m colspan=2 width=750>".stripslashes($data[titel])."</td></tr>
			<tr><td width=620><img src=".$gfx."/rassen/".(!$data[ruser_id] ? 9 : ($data['race']."".($data[subrace] != 0 ? "_".$data[subrace] : "")))."s.gif  title='".addslashes(getracename($data['race'],$data['subrace']))."'> ".stripslashes($data[username])." (".$data[user_id].($data[user_id] < 100 ? " <b>NPC</b>" : "").")</td>
			<td class=m width=130>".date("d.m.",$data['date']).setyear(date("Y",$data['date'])).date(" H:i",$data['date'])."</td></tr></table>
			<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=750>
			<tr><td width=120 valign=top><div style=\"text-align: center;\">".(!$data[ruser_id] ? "<font color=gray>Gelöscht</font>" : ($data[lastaction] < time()-300 ? "<font color=#ff0000>Offline</font>" : "<font color=#3F923D>Online</font>"))."<br>".(strlen($data[propic]) > 10 ? "<img src=\"".$data[propic]."\" width=100 height=100>" : "<img src=".$gfx."/rassen/".(!$data[race] ? 9 : $data[race])."kn.png>")."<br>".($data[id] > $_SESSION["rkn_lez"] ? "<font size=-3 color=#ff0000>".$data[id]."</font>" : "<font size=-3>".$data[id]."</font>")."</font></div><br>
			<div align=left><a href=?p=comm&s=rkn&a=slz&lz=".$data[id]." onmouseover=cp('lz".$i."','buttons/lese2') onmouseout=cp('lz".$i."','buttons/lese1')><img src=".$gfx."/buttons/lese1.gif name=lz".$i." border=0 title='Lesezeichen bei Beitrag ".$data[id]." setzen'></a>";
			if ($data['date'] > time()-600 && $data[user_id] == $_SESSION[uid]) $knp .= " <a href=?p=comm&s=reb&id=".$data[id]." onmouseover=cp('ke".$i."','buttons/knedit2') onmouseout=cp('ke".$i."','buttons/knedit1')><img src=".$gfx."/buttons/knedit1.gif name=ke".$i." border=0 title='Beitrag ".$data[id]." editieren'></a>";
			if ($data[user_id] != 1 && $data[ruser_id]) $knp .= " <a href=?p=comm&s=nn&recipient=".$data[user_id]." ".getonm("msg".$i,"buttons/msg")."><img src=".$gfx."/buttons/msg1.gif name=msg".$i." border=0 title=\"".ftit("PM an ".$data[username]." schicken")."\"></a>&nbsp;<a href=\"javascript:void(0);\" onClick=\"opensi(".$data[user_id].")\" ".getonm("id".$data[id],"buttons/info")."><img src=".$gfx."/buttons/info1.gif name=id".$data[id]." border=0 title='Spielerprofil'></a>";
			$knp .= "</div></td><td width=580 valign=top colspan=2>".nl2br(stripslashes($data[text]))."</td></tr>
			</table><br>";
			$kn = $knp.$kn;
		}
		
		$in = ceil(($m+5)/5);
		$i = $in-2;
		$j = $in+2;
		if ($i > 1) $pe = "<td><a href=?p=comm&s=rkn&m=0>1</a></td>";
		if ($j < ceil($knc/5)) $ps = "<td><a href=?p=comm&s=rkn&m=".$knc.">".ceil($knc/5)."</a></td>";
		if ($j > ceil($knc/5)) $j = ceil($knc/5);
		if ($i < 1) $i = 1;
		while($i<=$j)
		{
			$pages .= "<td><a href=?p=comm&s=rkn&m=".(($j-1)*5).">".($j == $in ? "&nbsp;<b>".$j."</b>&nbsp;" : $j)."</a></td>";
			$j--;
		}
		$i = $in-2;
		$j = $in+2;
		$pages = $ps.(ceil($knc/5) > $j+1 ? "<td>...</td>" : "").$pages.($i > 2 ? "<td>...</td>" : "").$pe;
		
		function getzu($v) { global $gfx,$m; return "<td><a href=?p=comm&s=rkn&m=".($m-5 < 0 ? 0 : $m-5)." onmouseover=cp('zu".$v."','buttons/b_to2') onmouseout=cp('zu".$v."','buttons/b_to1')><img src=".$gfx."/buttons/b_to1.gif name=zu".$v." border=0 title=Zurückblättern></a></td>"; }
		function getvor($v) { global $gfx,$m; return "<td><a href=?p=comm&s=rkn&m=".($m+5)." onmouseover=cp('vo".$v."','buttons/b_from2') onmouseout=cp('vo".$v."','buttons/b_from1')><img src=".$gfx."/buttons/b_from1.gif name=vo".$v." border=0 title=Vorblättern></a></td>"; }
		function getbs($v) { global $gfx; return "<td><a href=?p=comm&s=rbs onmouseover=cp('nb".$v."','buttons/knedit2') onmouseout=cp('nb".$v."','buttons/knedit1')><img src=".$gfx."/buttons/knedit1.gif name=nb".$v." border=0 title='Neuen Beitrag schreiben'></a></td>"; }
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr>".getbs(1)."&nbsp;".getvor(1)."&nbsp;".$pages."&nbsp;".getzu(1)."</tr></table><br>".$kn."<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr>".getbs(2)."&nbsp;".getvor(2)."&nbsp;".$pages."&nbsp;".getzu(2)."</tr></table>";
	}
}
if ($v == "quests")
{
	pageheader("/ <a href=?p=comm>Kommunikation</a> / <b>Quests</b>");
	if (mysql_num_rows($quest->result) == 0) meldung("Keine Quests vorhanden");
	else
	{
		echo "<table class=\"tcal\">";
		while ($data=mysql_fetch_assoc($quest->result)) echo "<tr><td><font color=#FFFF00>?</font></td><td><a href=?p=comm&s=qed&q=".$data[quest_id].">".stripslashes($data[titel])."</a></td><td>".gen_time(($data[user_time]+$data[maxtime])-time())."</td></tr>";
		echo "</table>";
	}
}
if ($v == "ignorelist")
{
	if ($_GET["sent"] == 1)
	{
		if (is_array($_GET["us"]))
		{
			foreach($_GET["us"] as $key => $value)
			{
				if ($_GET["de"][$key] == 1 && check_int($key))
				{
					$comm->delignore($key);
					$t .= "Siedler ID ".$key." wurde von der Liste gelöscht<br>";
					$key = 0;
				}
			}
		}
		if (check_int($_GET["nc"]))
		{
			$r = $comm->setignore($_GET["nc"]);
			if ($r == 1) $t = "Siedler auf die Ignoreliste gesetzt";
		}
	}
	pageheader("/ <a href=?p=comm>Kommunikation</a> / <b>Ignore-Liste</b>");
	if ($t) meldung($t);
	echo "<form action=main.php method=get><input type=hidden name=p value=comm><input type=hidden name=s value=il><input type=hidden name=sent value=1><table bgcolor=#262323 cellspacing=1 cellpadding=1>
	<tr><th>Siedler hinzufügen</th></tr>
	<tr><td>User-ID <input type=text name=nc class=text size=4 value=".(check_int($_GET[recipient]) ? $_GET[recipient] : "")."> <input type=submit value=Hinzufügen class=button></td></tr></table></form>
	<table bgcolor=#262323 cellspacing=1 cellpadding=1>
	<th colspan=2>Siedler auf der Ignore-Liste</th>";
	$result = $comm->getignorelist();
	if (mysql_num_rows($result) == 0) echo "<tr><td colspan=2>Keine Einträge vorhanden</td></tr>";
	else
	{
		echo "<form action=main.php method=get><input type=hidden name=p value=comm><input type=hidden name=s value=il><input type=hidden name=sent value=1>
		<tr><td>Löschen</td><td>Siedler</td></tr>";
		while($d=mysql_fetch_assoc($result))
		{
			echo "<input type=hidden name=us[".$d[recipient]."] value=".$d[mode]."><tr><td align=center><input type=checkbox name=de[".$d[recipient]."] value=1></td><td>".stripslashes($d[user])." (".$d[recipient].")</td></tr>";
		}
		echo "<tr><td colspan=2><input type=submit class=button value=Löschen> <input type=reset value=Reset class=button></td></tr>";
	}
	echo "</table><br>";
	$result = $comm->getignoreuser();
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1>
	<th>Siedler die mich ignorieren</th>";
	if (mysql_num_rows($result) == 0) echo "<tr><td>Keine Einträge vorhanden</td></tr>";
	else
	{
		while($data=mysql_fetch_assoc($result))
		{
			$i++;
			echo "<tr><td>".stripslashes($data['user'])." (".$data['user_id'].")</td></tr>";
		}
	}
	echo "</table>";
}
if ($v == "rpguser")
{
	$result = $comm->get_rpg_players();
	pageheader("/ <a href=?p=comm>Kommunikation</a> / <b>Rassen-RPG Spieler</b>");
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1>
	<th>Siedler</th><th></th>";
	$arr = array(1 => "<font color=#0088FF><b>Vereinte Föderation der Planeten</b></font> (".$comm->get_rpg_player_count(10)." Spieler)",
	2 => "<font color=green><b>Romulanisches Sternenimperium</b></font> (".$comm->get_rpg_player_count(11)." Spieler)",
	3 => "<b><font color=red>Klingonisches Imperium</font></b> (".$comm->get_rpg_player_count(12)." Spieler)",
	4 => "<font color=yellow><b>Cardassianische Union</b></font> (".$comm->get_rpg_player_count(13)." Spieler)",
	5 => "<b><font color=#CC4010>Ferengi Allianz</font></b> (".$comm->get_rpg_player_count(14)." Spieler)");
	while($data = mysql_fetch_assoc($result))
	{
		$i++;
		if ($data['race'] != $lc)
		{
			$lc = $data['race'];
			echo "<tr><td style=\"background-color: #171616\" colspan=\"2\">".$arr[$lc]."</td></tr>";
			$j = 0;
		}
		$j++;
		if ($j == 2)
		{
			$trc = " style=\"background-color: #171616\"";
			$j = 0;
		}
		else $trc = "";
		echo "<tr><td".$trc."><img src=".$gfx."/rassen/".($data['race']."".($data[subrace] != 0 ? "_".$data[subrace] : ""))."s.gif  title='".addslashes(getracename($data['race'],$data['subrace']))."'> ".stripslashes($data['user'])." (".$data['recipient'].")</td>
		<td".$trc."><a href=?p=comm&s=nn&recipient=".$data['recipient']." ".getonm("pm".$i,'buttons/msg')."><img src=".$gfx."/buttons/msg1.gif name=pm".$i." border=0></a></td></tr>";
	}
	echo "</table>";
}
?>
