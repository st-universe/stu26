<?php
if (!is_object($db)) exit;
include_once("class/map.class.php");
$map = new map;
include_once("class/station.class.php");
$ship = new station;
switch($_GET['s'])
{
	default:
		$v = "showship";
	case "ss":
		$v = "showship";
		break;
	case "be":
		$v = "beam";
		break;
	case "bec":
		$v = "beamc";
		break;
	case "sc":
		$v = "scan";
		break;
	case "csc":
		$v = "cscan";
		break;
	case "sht":
		if (!check_int($_GET['t'])) die(show_error(902));
		$tar = $db->query("SELECT id,name,user_id,sx,sy,systems_id,schilde_status FROM stu_colonies WHERE id=".$_GET['t']." LIMIT 1",4);
		if ($tar == 0) die(show_error(902));
		if (checksector($tar) == 0) die(show_error(902));
		$ship->loadcto($_GET['t']);
		$v = "showtrade";
		break;
	case "ssz":
		$v = "selbstzerst";
		$ship->generateszcode();
		break;
	case "scs":
		if ($ship->map['sensor_off'] == 1)
		{
			$v = "showship";
			$sresult = "Dieser Sektor kann nicht gescant werden";
			break;
		}
		$result = $ship->sectorscan();
		if (!is_array($result))
		{
			$v = "showship";
			$sresult = $result;
			break;
		}
		$v = "sectorscan";
		break;
	case "sda":
		$v = "sensordata";
		if ($ship->rumps_id != 10 && $ship->rumps_id != 8002 && $ship->rumps_id != 8003 && $ship->rumps_id != 1912 && $ship->rumps_id != 9507 && $ship->rumps_id != 9701) exit;
		if ($ship->rumps_id != 10 && $ship->rumps_id != 1912 && $ship->rumps_id != 9507 && $ship->rumps_id != 9701) $_GET['m'] = "l";
		if (($ship->rumps_id == 1912 || $ship->rumps_id == 9507) && $ship->systems_id == 0) $_GET['m'] = "l";
		break;
	case "ssm":
		if ($ship->rumps_id != 9) die(show_error(902));
		if ($_GET['stat'])
		{
			include_once("class/colony.class.php");
			$col = new colony;
			if (!$col->checkrump($_GET['stat'])) die(show_error(902));
			$col->getrumpbyid($_GET['stat']);
		}
		$v = "stationsbau";
		break;
	case "las":
		if ($ship->is_shuttle != 1) die(show_error(902));
		$v = "landshuttle";
		break;
	case "war":
		if ($ship->rumps_id < 9001 || $ship->rumps_id > 9005) die(show_error(902));
		$v = "wartung";
		break;
}
if ($v == "showship")
{
	unset($result);
	if ($ship->destroyed == 1 || $ship->dsships[$_GET['id']] == 1)
	{
		pageheader("/ <a href=?p=ship>Schiffe</a> / <b>Schiffsdetails</b>");
		meldung("Du bist nicht Besitzer dieses Schiffes");
		exit;
	}
	// Schiffsfunktionen

	if ($_GET['a'] == "lwk" && $ship->m5 > 0 && ($_GET['c'] == "laden" || $_GET['c'] == "max")) $result = $ship->loadwarpcore($_GET['c']);
	if ($_GET['a'] == "av" && $_GET['d']) $result = $ship->av($_GET['d']);
	if ($_GET['a'] == "dv" && $_GET['d']) $result = $ship->dv($_GET['d']);
	if ($_GET['a'] == "tr" && check_int($_GET['t'])) $result = $ship->activatetraktor($_GET['t']);
	if ($_GET['a'] == "dt") $result = $ship->deactivatetraktor();
 	if ($_GET['a'] == "ca" && check_int($_GET['m'])) $result = $ship->changealvl($_GET['m']);
	if ($_GET['a'] == "lsh" && ((check_int($_GET['c']) && $_GET['c'] > 0) || $_GET['c'] == "max")) $result = $ship->loadshields($_GET['c']);
	if ($_GET['a'] == "awp") $result = $ship->av("wp");
	if ($_GET['a'] == "awt") $result = $ship->av("wt");
	if ($_GET['a'] == "dwp") $result = $ship->dv("wp");
	if ($_GET['a'] == "dwt") $result = $ship->dv("wt");
	if ($_GET['a'] == "att" && check_int($_GET['t']) && $_GET['t'] > 0 && ($ship->phaser > 0 || $ship->torp_type > 0)) $result = $ship->attack($_GET['t'],$_GET['id']);
	if ($_GET['a'] == "nr") $result = $ship->notruf();
	if ($_GET['a'] == "cfdr") $result = $ship->dockRightFriends($_GET['fdr']);
	if ($_GET['a'] == "ndr" && check_int($_GET['ta']) && check_int($_GET['ty']) && check_int($_GET['m'])) $result = $ship->newDockRight($_GET['ta'],$_GET['ty'],$_GET['m']);
	if ($_GET['a'] == "ddr" && check_int($_GET['t']) && check_int($_GET['ty'])) $result = $ship->delDockRight($_GET['t'],$_GET['ty']);
	if ($_GET['a'] == "dedoc" && check_int($_GET['t'])) $result = $ship->dedock($_GET['t']);
	if ($_GET['a'] == "laush" && check_int($_GET['shur'])) $result = $ship->launchshuttle($_GET['shur']);
	if ($_GET['a'] == "lansh" && check_int($_GET['t'])) $result = $ship->landshuttle($_GET['t']);
	if ($_GET['a'] == "warsh" && check_int($_GET['shur'])) $result = $ship->maintainshuttle($_GET['shur']);
	if ($_GET['a'] == "repsh" && $_SESSION['uid'] < 100 && check_int($_GET['t'])) $result = $npc->repairship($_GET['t']);
	if ($_GET['a'] == "maish" && $_SESSION['uid'] < 100 && check_int($_GET['t'])) $result = $npc->maintainship($_GET['t']);
	if ($_GET['a'] == "ttd" && $ship->traktormode == 1) $result = $ship->docktraktorship();
	if ($_GET['a'] == "wlo" && $_GET['tx']) $result = $ship->write_private_logbook($_GET['tx']);
	if ($_GET['a'] == "arkn") $result = $ship->addtorkn();
	if ($_GET['a'] == "bec" && check_int($_GET['t']) && $_GET['m'] == "to")
	{
		if (is_array($_GET['good']) && is_array($_GET['count'])) $result = $ship->beamtocol($_GET['t'],$_GET['good'],$_GET['count']);
		if (check_int($_GET['crew']) && $_GET['crew'] > 0 && $ship->stop_trans != 1)
		{
			if ($result) $result .= "<br>";
			$result .= $ship->transfercrewcol($_GET['t'],$_GET['crew'],$_GET['m']);
		}
	}
	if ($_GET['a'] == "bec" && check_int($_GET['t']) && $_GET['m'] == "fr")
	{
		if (is_array($_GET['good']) && is_array($_GET['count'])) $result = $ship->beamfromcol($_GET['t'],$_GET['good'],$_GET['count']);
		if (check_int($_GET['crew']) && $_GET['crew'] > 0 && $ship->stop_trans != 1)
		{
			if ($result) $result .= "<br>";
			$result .= $ship->transfercrewcol($_GET['t'],$_GET['crew'],$_GET['m']);
		}
	}
	if ($_GET['a'] == "be" && check_int($_GET['t']) && $_GET['m'] == "to")
	{
		if (is_array($_GET['good']) && is_array($_GET['count'])) $result = $ship->beamto($_GET['t'],$_GET['good'],$_GET['count']);
		if (check_int($_GET['crew']) && $_GET['crew'] > 0 && $ship->stop_trans != 1)
		{
			if ($result) $result .= "<br>";
			$result .= $ship->transfercrew($_GET['t'],$_GET['crew'],$_GET['m']);
		}
	}
	if ($_GET['a'] == "be" && check_int($_GET['t']) && $_GET['m'] == "fr")
	{
		if (is_array($_GET['good']) && is_array($_GET['count'])) $result = $ship->beamfrom($_GET['t'],$_GET['good'],$_GET['count']);
		if (check_int($_GET['crew']) && $_GET['crew'] > 0 && $ship->stop_trans != 1)
		{
			if ($result) $result .= "<br>";
			$result .= $ship->transfercrew($_GET['t'],$_GET['crew'],$_GET['m']);
		}
	}
	if ($_GET['a'] == "et" && ((check_int($_GET['count']) && $_GET['count'] > 0) || $_GET['count'] == "max") && check_int($_GET["t"])) $result = $ship->etransfer($_GET["t"],$_GET['count']);
	if ($_GET['a'] == "etc" && ((check_int($_GET['count']) && $_GET['count'] > 0) || $_GET['count'] == "max") && check_int($_GET["t"])) $result = $ship->etransferc($_GET["t"],$_GET['count']);
	if ($_GET['a'] == "chn" && strlen($_GET["nname"]) > 1) $result = $ship->changename($_GET["nname"]);
	if ($result || $sresult)
	{
		if ($sresult) $result = $sresult;
		if (is_array($ship->logbook)) $ship->write_logbook();
		if ($ship->destroyed == 0 && !$ship->dsships[$_GET['id']]  && $_GET['a'] != "col") $ship = new station;
		pageheader("/ <a href=?p=ship>Schiffe</a> / <b>".stripslashes($ship->name)."</b>");
		meldung($result);
		if ($ship->colonized == 1) die;
	}
	else pageheader("/ <a href=?p=ship>Schiffe</a> / <b>".stripslashes($ship->name)."</b>");
	if ($ship->destroyed == 1 || $ship->dsships[$_GET['id']] == 1)
	{
		meldung("Du bist nicht Besitzer dieses Schiffes");
		exit;
	}
	if ($ship->m5 == 0  || $ship->warpcore == 0)
	{
		$deut = $db->query("SELECT count FROM stu_ships_storage WHERE goods_id=5 AND ships_id=".$ship->id." LIMIT 1",1);
		if ($deut == 0) $ship->creaktor = 0;
		$reaktor = $ship->creaktor;
	}
	else
	{
		$reaktor = $ship->rreaktor;
	}
	if ($ship->lss == 1) $vb++;
	if ($ship->nbs == 1) $vb++;
	if ($ship->wea_phaser == 1) $vb++;
	if ($ship->wea_torp == 1) $vb++;
	if ($ship->schilde_status == 1) $vb++;
	if ($ship->porep == 1)
	{
		if ($ship->replikator == 1 || $db->query("SELECT count FROM stu_ships_storage WHERE goods_id=1 AND ships_id=".$ship->id." LIMIT 1",1) == 0) $vb += ceil($ship->crew/5);
	}
	if ($ship->cloak == 1) ($reaktor-$vb-1 < 3 ? $vb += 3 : $vb += $reaktor-$vb-1);
	$reaktor-$vb > $ship->max_eps-$ship->eps ? $ee = $ship->max_eps-$ship->eps : $ee = $reaktor-$vb;
	if ($ship->crew < $ship->min_crew)
	{
		$reaktor = 0;
		$ee = 0;
		$vb = 0;
	}
	echo "<script language=\"Javascript\">
	function getshipinfo()
	{
		elt = 'shinfo';
		sendRequest('backend/shinfo.php?PHPSESSID=".session_id()."&id=".$_GET['id']."');
		return overlib('<div id=shinfo></div>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER, STICKY, FIXX, 300, FIXY, 150, WIDTH, 600);
	}
	function showsysinfo(id)
	{
		elt = 'syinfo';
		sendRequest('backend/shipsysview.php?PHPSESSID=".session_id()."&id=' + id + '');
		return overlib('<div id=syinfo></div>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER, STICKY, FIXX, 300, FIXY, 100, WIDTH, 180);
	}
	function open_pm_window(rec,ext1,ext2)
	{
		elt = 'shipm';
		sendRequest('backend/pmwin.php?PHPSESSID=".session_id()."&recipient=' + rec + '&ext1='+ext1+'&ext2='+ext2);
		return overlib('<div id=shipm></div>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER, STICKY, EXCLUSIVE, RELX, 200, RELY, 150, WIDTH, 450, DRAGGABLE, ALTCUT);
	}
	function send_pm()
	{
		elt = 'pmwinc';
		rec = document.pmform.recipient.value;
		tex = document.pmform.tx.value;
		sendRequest('backend/pmwin.php?PHPSESSID=".session_id()."&recipient=' + rec + '&text='+nl2br_js(tex));
	}
	function open_log_window()
	{
		elt = 'logwin';
		sendRequest('backend/station/writelog.php?PHPSESSID=".session_id()."&id=".$_GET['id']."');
		return overlib('<div id=logwin></div>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER, ABOVE, STICKY, DRAGGABLE, ALTCUT, FIXX, 300, FIXY, 150, WIDTH, 600);
	}
	function nl2br_js(string)
	{
		var regX =  /\\n/g;
		var replaceString = '<br>';
		return string.replace(regX, replaceString);
	}
	function ship_etransfer(id,t)
	{
		elt = 'setrans';
		sendRequest('backend/station/setransfer.php?PHPSESSID=".session_id()."&id='+id+'&t='+t+'&e=".$ship->eps."');
		return overlib('<div id=setrans></div>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, ABOVE, LEFT, STICKY, DRAGGABLE, ALTCUT, WIDTH, 300);
	}
	function col_etransfer(id,t)
	{
		elt = 'cetrans';
		sendRequest('backend/station/cetransfer.php?PHPSESSID=".session_id()."&id='+id+'&t='+t+'&e=".$ship->eps."');
		return overlib('<div id=cetrans></div>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, ABOVE, LEFT, STICKY, DRAGGABLE, ALTCUT, WIDTH, 300);
	}
	</script>
	<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=850><form action=main.php method=get><input type=hidden name=p value=stat><input type=hidden name=s value=ss><input type=hidden name=a value=chn><input type=hidden name=id value=".$_GET["id"].">
	<tr><th></th><th>Klasse</th><th>x|y</th><th>Hülle</th><th>Schilde</th><th>Energie</th><th>Crew</th><th>Name</th><td rowspan=3><a href=?p=stat&s=ssz&id=".$_GET["id"]." ".getonm('szst','buttons/selfdes')."><img src=".$gfx."/buttons/selfdes1.gif name=szst border=0 title=\"Selbstzerstörung einleiten\"></a></td></tr>
	<tr><td width=1 rowspan=2 style=\"background: ".$sco."\"></td><td rowspan=2><img src=".$gfx."/ships/".vdam($ship).$ship->rumps_id.".gif title=\"".stripslashes($ship->cname)."\"></td>
	<td>".($ship->systems_id > 0 ? $ship->sx."|".$ship->sy." (".$ship->cx."|".$ship->cy.")" : $ship->cx."|".$ship->cy)."</td>
	<td>".renderhuellstatusbar($ship->huelle,$ship->max_huelle)." ".$ship->huelle."/".$ship->max_huelle."</td>
	<td>".rendershieldstatusbar($ship->schilde_status,$ship->schilde,$ship->max_schilde)." ".($ship->schilde_status == 1 ? "<font color=cyan>".$ship->schilde."/".$ship->max_schilde."</font>" : $ship->schilde."/".$ship->max_schilde)."</td>
	<td>".$ship->eps."/".$ship->max_eps." (<font color=Green>".($ee > 0 ? "+".$ee : $ee)."</font>/<font color=#FF0000>".(!$vb ? 0 : $vb)."</font>/".($ee+$vb).")</td><td>".$ship->crew."/".$ship->max_crew." (".$ship->min_crew.")</td>
	<td><input type=text size=20 name=nname class=text value=\"".stripslashes($ship->name)."\"> <input type=submit class=button value=ändern></td></tr>
	<tr><td><a href=?p=stat&s=ss&id=".$_GET['id']." onmouseover=cp('ref','buttons/lese2') onmouseout=cp('ref','buttons/lese1')><img src=".$gfx."/buttons/lese1.gif name=ref title='Aktualisieren' border=0></a>
	<a href=?p=stat&s=ss&a=nr&id=".$_GET['id']." ".getonm("nr","buttons/ascan")."><img src=".$gfx."/buttons/ascan1.gif name=nr border=0 title=\"Notruf absetzen\"></a>".($ship->slots == 0 ? " <a href=?p=stat&s=ss&id=".$_GET['id']."&a=hud ".getonm('hud','buttons/hud')."><img src=".$gfx."/buttons/hud1.gif title=\"Zur Gefechts-HUD umschalten\" name=hud border=0>" : "")."</a>
	<a href=\"javascript:void(0);\" onClick=\"open_log_window();\" ".getonm('writelog','buttons/knedit')."><img src=".$gfx."/buttons/knedit1.gif name=writelog title=\"Logbucheintrag schreiben\" border=0></a> <a href=\"javascript:void(0);\" onClick=\"getshipinfo();\" ".getonm('sin','buttons/info')."><img src=".$gfx."/buttons/info1.gif name=sin border=0 title=\"Schiffsinformationen\"></a>".($ship->is_rkn == 0 && checkfactionkn() == 1 ? " <a href=?p=stat&s=ss&a=arkn&id=".$_GET['id']."><img src=".$gfx."/rassen/".$_SESSION['race']."s.gif border=0 title=\"Station dem RPG bereitstellen\"></a>" : "")."
	</td><td colspan=2>".getmodulelist()."</td><td colspan=3>".getdammodulelist()."</td></tr></form></table><br>
	<table width=600><tr><td valign=top>";
	if ($ship->m5 > 0 || $ship->m11 > 0)
	{
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=300><tr><th colspan=2><img src=".$gfx."/buttons/warpsys.gif title=\"Warpsystem\"> Warp-System</th></tr>
		<form action=main.php><input type=hidden name=p value=stat><input type=hidden name=s value=ss><input type=hidden name=a value=lwk><input type=hidden name=id value=".$_GET['id'].">";
		if ($ship->m5 > 0) echo "<tr><td width=150><img src=".$gfx."/buttons/warpk.gif title='Warpkern'> Warpkern: ".$ship->warpcore."</td><td valign=middle><input type=submit name=c value=laden class=button> <input type=submit name=c value=max class=button></td></tr>";
		if ($ship->m11 > 0) echo "<tr><td><img src=".$gfx."/buttons/".($ship->warp == 1 ? "warp2" : "warp1").".gif title='Warpantrieb'> Warpantrieb </td><td>".($ship->warp == 1 ? "<input type=submit class=button name=a value=Deaktivieren>" : "<input type=submit class=button name=a value=Aktivieren".($ship->systems_id > 0 ? " disabled" : "").">")."</td></tr>";
		echo "</table></form>";
	}
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=300><tr><th colspan=2><img src=".$gfx."/buttons/shld.gif title='Schilde'> Schilde (".$ship->schilde."/".$ship->max_schilde.")</th></tr>
	<form action=main.php><input type=hidden name=p value=stat><input type=hidden name=s value=ss><input type=hidden name=a value=lsh><input type=hidden name=id value=".$_GET['id'].">
	<tr><td>".($ship->schilde_status > 1 && time() < $ship->schilde_status ? "Polarisiert bis ".date("d.m H:i",$ship->schilde_status) : ($ship->schilde_status == 0 || ($ship->schilde_status > 1 && time() > $ship->schilde_status) ? "<a href=?p=stat&s=ss&a=av&d=sh&id=".$_GET['id']." ".getonm('shi','buttons/shldac')."><img src=".$gfx."/buttons/shldac1.gif border=0 title='Schilde aktivieren' name=shi> aktivieren</a>" : "<a href=?p=stat&s=ss&a=dv&d=sh&id=".$_GET['id']." onmouseover=cp('shi','buttons/shldac1') onmouseout=cp('shi','buttons/shldac2')><img src=".$gfx."/buttons/shldac2.gif border=0 title='Schilde deaktiveren' name=shi> deaktivieren</a>"))."</td>
	<td><input type=text size=2 name=c class=text".($ship->schilde_status == 1 ? " disabled" : "")."> <input type=submit value=laden class=button".($ship->schilde_status == 1 ? " disabled" : "")."> <input type=submit name=c value=max class=button".($ship->schilde_status == 1 ? " disabled" : "")."></td></tr></form></table>";
	if ($ship->cloakable == 1)
	{
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=300><tr><th colspan=2><img src=".$gfx."/buttons/tarnv.gif title='Tarnung'> Tarnung</th></tr>
		<tr><td>".($ship->cloak == 1 ? "<a href=?p=stat&s=ss&a=dv&d=cl&id=".$_GET['id']." onmouseover=cp('clo','buttons/tarn1') onmouseout=cp('clo','buttons/tarn2')><img src=".$gfx."/buttons/tarn2.gif border=0 title='Tarnung deaktivieren' name=clo> deaktivieren</a>" : "<a href=?p=stat&s=ss&a=av&d=cl&id=".$_GET['id']." onmouseover=cp('clo','buttons/tarn2') onmouseout=cp('clo','buttons/tarn1')><img src=".$gfx."/buttons/tarn1.gif border=0 title='Tarnung aktivieren' name=clo> aktivieren</a>")."</td></tr></table>";
	}
	if ($ship->traktor > 0)
	{
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=300><tr><th colspan=2><img src=".$gfx."/buttons/trak.gif title='Traktorstrahl'> Traktorstrahl</th></tr>
		<tr><td>".vtrak($ship->traktor)."<br>";
		if ($ship->traktormode == 1)
		{
			echo "<a href=?p=stat&s=ss&a=dt&id=".$_GET['id']." onmouseover=cp('tra','buttons/trak1') onmouseout=cp('tra','buttons/trak2')><img src=".$gfx."/buttons/trak2.gif border=0 title='Traktorstrahl deaktivieren' name=tra> deaktivieren</a>";
			if ($ship->slots > 0) echo "&nbsp;<a href=?p=stat&s=ss&id=".$_GET['id']."&a=ttd ".getonm('ttd','buttons/dock')."><img src=".$gfx."/buttons/dock1.gif name=ttd border=0 title=\"Schiff andocken\"> Andocken</a>";
		}
		else echo "Schiff wird von Traktorstrahl gehalten<br><a href=?p=stat&s=ss&id=".$_GET['id']."&a=sfbi ".getonm('fbip','buttons/trak')."><img src=".$gfx."/buttons/trak1.gif border=0 title=\"Feedback Impuls aussenden\" name=fbip> Feedback-Impuls aussenden</a>";
		echo "</td></tr></table>";
	}
	if (($ship->map['type'] == 11 || $ship->map['type'] == 12) && $ship->m10 == 657)
	{
		$ed = $ship->geterzfeld();
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=300>
		<tr><th colspan=2><img src=".$gfx."/map/".$ship->map['type'].".gif title='".$ship->map['name']."' width=15 height=15> Zusammensetzung</th></tr>
		<tr><td><img src=".$gfx."/goods/20.gif title=\"Iridium\"> ".$ed['chance_20']." <img src=".$gfx."/goods/21.gif title=\"Kelbonit\"> ".$ed['chance_21']."&nbsp;&nbsp;<img src=".$gfx."/goods/22.gif title=\"Nitrium\"> ".$ed['chance_22']."&nbsp;&nbsp;<img src=".$gfx."/goods/23.gif title=\"Magnesit\"> ".$ed['chance_23']."&nbsp;&nbsp;<img src=".$gfx."/goods/24.gif title=\"Talgonit\"> ".$ed['chance_24']."&nbsp;&nbsp;<img src=".$gfx."/goods/25.gif title=\"Galazit\"> ".$ed['chance_25']."</td></tr></table>";
	}
	if ($ship->porep == 1)
	{
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=300><th colspan=2><img src=".$gfx."/buttons/repli.gif title=\"Replikator\"> Replikator</th>
		<tr>".($ship->replikator == 1 ? "<td><a href=?p=stat&s=ss&id=".$_GET['id']."&a=dv&d=re ".getronm('repi','buttons/repli')."><img src=".$gfx."/buttons/repli2.gif border=0 name=repli title=\"deaktivieren\"> deaktivieren</a></td><td><img src=".$gfx."/buttons/e_trans2.gif title=\"Energieverbrauch\"> Verbrauch: ".ceil($ship->crew/5)."</td>" : "<td colspan=2><a href=?p=stat&s=ss&id=".$_GET['id']."&a=av&d=re ".getonm('repli','buttons/repli')."><img src=".$gfx."/buttons/repli1.gif border=0 name=repli title=\"aktivieren\"> aktivieren</a></td>")."</tr>
		</table>";
	}
	echo "</td><td valign=top align=left>
	<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=300>
	<tr><th><img src=".$gfx."/buttons/gefecht.gif title='Gefechtskontrolle'> Gefechtskontrolle</th></tr>
	<tr><td><img src=".$gfx."/buttons/alert".$ship->alvl.".gif title='Alarmstufe'> Alarmstufe ".($ship->alvl == 1 ? "" : "<a href=?p=stat&s=ss&a=ca&m=1&id=".$_GET['id']."><img src=".$gfx."/buttons/alert1.gif border=0 title=\"Grün\"></a>&nbsp;").($ship->alvl == 2 ? "" : "<a href=?p=stat&s=ss&a=ca&m=2&id=".$_GET['id']."><img src=".$gfx."/buttons/alert2.gif border=0 title=\"Gelb\"></a>").($ship->alvl == 3 ? "" : "&nbsp;<a href=?p=stat&s=ss&a=ca&m=3&id=".$_GET['id']."><img src=".$gfx."/buttons/alert3.gif border=0 title=\"Rot\"></a>")."</td></tr>
	<tr><td>Waffen: ".($ship->wea_phaser == 1 ? "<a href=?p=stat&s=ss&id=".$_GET['id']."&a=dwp><img src=".$gfx."/buttons/act_phaser2.gif border=0 ".getronm('wps','buttons/act_phaser')." name=wps title='Waffensystem (Strahlenwaffe) deaktivieren'></a>" : "<a href=?p=stat&s=ss&id=".$_GET['id']."&a=awp><img src=".$gfx."/buttons/act_phaser1.gif border=0 ".getonm('wps','buttons/act_phaser')." name=wps title='Waffensystem (Strahlenwaffe) aktivieren'></a>")." ".($ship->wea_torp == 1 ? "<a href=?p=stat&s=ss&id=".$_GET['id']."&a=dwt><img src=".$gfx."/buttons/act_torp2.gif border=0 ".getronm('wpt','buttons/act_torp')." name=wpt title='Waffensystem (Torpedobänke) deaktivieren'></a>" : "<a href=?p=stat&s=ss&id=".$_GET['id']."&a=awt><img src=".$gfx."/buttons/act_torp1.gif border=0 ".getonm('wpt','buttons/act_torp')." name=wpt title='Waffensystem (Torpedobänke) aktivieren'></a>")."</td></tr>";
	if ($ship->rumps_id == 1912) echo "<tr><td><a href=?p=ship&a=swts&id=".$_GET['id'].">Schiffsmodus</a></td></tr>";
	echo "</table>";
	if ($ship->max_shuttles > 0)
	{
		echo "<script language=\"Javascript\">
		function chgpic()
		{
			var pic = document.forms.shu.shur.value;
			if (pic == parseInt(0))
			{
				document.getElementById(\"picshu\").innerHTML = '<img src=".$gfx."/buttons/info1.gif>';
				return;
			}
			document.getElementById(\"picshu\").innerHTML = '<img src=".$gfx."/ships/' + pic + '.gif>';
		}
		</script>";
		$res = $ship->getshuttles();
		if (mysql_num_rows($res) > 0)
		{
			echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=300><tr><th colspan=2> Shuttles</th></tr>
			<form action=main.php method=get name=shu><input type=hidden name=p value=stat><input type=hidden name=s value=ss><input type=hidden name=id value=".$_GET['id']."><input type=hidden name=a value=laush>
			<tr><td><span id=picshu><img src=".$gfx."/buttons/info1.gif></span> <select name=shur onChange=\"chgpic();\"><option value=0>-----------";
			while($data=mysql_fetch_assoc($res)) echo "<option value=".$data['rumps_id']."> ".$data['name'];
			echo "</select> <input type=submit value=starten class=button></td></tr></form></table>";
		}
		$res = $ship->getoldshuttles();
		if (mysql_num_rows($res) > 0)
		{
			echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=300><tr><th colspan=2>Gebrauchte Shuttles</th></tr>
			<form action=main.php method=get name=shu><input type=hidden name=p value=stat><input type=hidden name=s value=ss><input type=hidden name=id value=".$_GET['id']."><input type=hidden name=a value=warsh>
			<tr><td> <select name=shur><option value=0>-----------";
			while($data=mysql_fetch_assoc($res)) echo "<option value=".$data['goods_id']."> ".$data['name'];
			echo "</select> <input type=submit value=warten class=button></td></tr></form></table>";
		}
	}
	echo "</td></tr></table><table><tr>
	<td valign=top width=160><table bgcolor=#262323 cellspacing=1 cellpadding=1 width=160><tr><th align=center>Informationen</th></tr>
	<tr><td>";
	if ($ship->map['cid'] > 0)
	{
		$col = $db->query("SELECT a.id,a.name,a.user_id,a.schilde_status,a.planet_name,a.quest_involved,b.user,c.name as cname FROM stu_colonies as a LEFT JOIN stu_user as b ON a.user_id=b.id LEFT JOIN stu_colonies_classes as c ON a.colonies_classes_id=c.colonies_classes_id WHERE a.systems_id=".$ship->systems_id." AND a.sx=".$ship->sx." AND a.sy=".$ship->sy." LIMIT 1",4);
		$img = "<img src=".$gfx."/planets/".$ship->map['cid'].($col['schilde_status'] == 1 ? "s" : "").".gif width=15 heigth=15 border=0 onmouseover=\"return overlib('<table class=tcal><th>Kolonieinformationen</th><tr><td>Kolonie: ".str_replace("'","",stripslashes($col['name']))." (".$col['id'].")<br>Besitzer: ".str_replace("'","",stripslashes($col['user']))." (".$col['user_id'].")</td></tr></table>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER);\" onmouseout=\"nd();\">";
		if ($col['user_id'] != 1) $col['user_id'] == $_SESSION['uid'] ? print("<a href=?p=colony&s=sc&id=".$col['id']."&shd=".$_GET['id'].">".$img."</a>") : print($img);
		else echo "<img src=".$gfx."/planets/".$ship->map['cid'].".gif width=15 heigth=15 title='Besitzer: Niemand'>";
		echo " <a href=?p=stat&s=csc&id=".$_GET['id']."&t=".$col['id']." ".getonm("csc","buttons/lupe")."><img src=".$gfx."/buttons/lupe1.gif name=csc title='Kolonie scannen' border=0></a>
		 <a href=\"javascript:void(0);\" onClick=\"col_etransfer(".$_GET['id'].",".$col['id'].");\" ".getonm("cet","buttons/e_trans")."><img src=".$gfx."/buttons/e_trans1.gif name=cet title='Energietransfer' border=0></a> <a href=?p=stat&s=bec&id=".$_GET['id']."&m=to&t=".$col['id']." ".getonm("cbt","buttons/b_down")."><img src=".$gfx."/buttons/b_down1.gif name=cbt title=\"Zur Kolonie beamen\" border=0></a> <a href=?p=stat&s=bec&id=".$_GET['id']."&m=fr&t=".$col['id']." ".getonm("cbf","buttons/b_up")."><img src=".$gfx."/buttons/b_up1.gif name=cbf title='Von der Kolonie beamen' border=0></a>
		 <a href=?p=comm&s=nn&recipient=".$col['user_id']." ".getonm("cmsg","buttons/msg")."><img src=".$gfx."/buttons/msg1.gif name=cmsg border=0 title=\"PM an ".ftit($col['user'])." schicken\"></a>
		 <a href=?p=stat&s=sht&id=".$_GET['id']."&t=".$col['id']." ".getonm("cft","buttons/fergtrade")."><img src=".$gfx."/buttons/fergtrade1.gif name=cft border=0 title=\"Warenangebot\"></a>";
		if ($col['user_id'] == 1 && $ship->plans_id == 1) echo "<br><a href=?p=stat&s=col&id=".$_GET["id"]."&cid=".$col['id']." ".getonm('colz','buttons/colo')."><img src=".$gfx."/buttons/colo1.gif title=\"Kolonisieren\" name=colz border=0> Kolonisieren</a>";
	}
	else echo "<img src=".$gfx."/map/".$ship->map['type'].".gif width=15 height=15> ".$ship->map['name'];
	if ($ship->rumps_id == 9) echo "<br><a href=?p=stat&s=ssm&id=".$_GET['id']." ".getronm('kon','buttons/statio')."><img src=".$gfx."/buttons/statio2.gif name=kon title=\"Station errichten\" border=0> Station errichten</a>";
	echo "<br><a href=?p=stat&s=scs&id=".$_GET["id"]." ".getonm("scs","buttons/lupe")."><img src=".$gfx."/buttons/lupe1.gif border=0 name=scs title=\"Sektor scannen\"> Sektor scannen</a>";
	if ($ship->rumps_id == 10 || $ship->rumps_id == 8002 || $ship->rumps_id == 8003 || $ship->rumps_id == 1912 || $ship->rumps_id == 9507 || $ship->rumps_id == 9701) echo "<br><a href=?p=stat&s=sda&id=".$_GET["id"]." ".getonm("sda","buttons/ascan")."><img src=".$gfx."/buttons/ascan1.gif name=sda border=0 title=\"Sensordaten\"> Sensordaten</a>";
	echo "</td></tr></table></td><td valign=top>";
	if ($ship->map['is_system'] == 1 && $ship->systems_id == 0)
	{
		$sys = $map->getsystembyxy($ship->cx,$ship->cy);
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=200><tr><th>Systemdetails</a></th></tr>
		<tr><td><img src=".$gfx."/map/".$sys['type'].".gif width=15 height=15> <a href=\"javascript:void(0);\" onClick=\"showsysinfo(".$_GET['id'].");\">".$sys['name']."</a>";
		if ($ship->m11 > 0) echo "<br><a href=?p=stat&s=ss&a=es&id=".$_GET['id']." onmouseover=cp('se','buttons/sysenter2') onmouseout=cp('se','buttons/sysenter1')><img src=".$gfx."/buttons/sysenter1.gif name=se border=0 title='Einfliegen'> Einfliegen</a>";
		echo "</td></tr></table>";
	}
	if ($ship->systems_id > 0)
	{
		if (!$sys) $sys = $map->getsystembyid($ship->systems_id);
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=200><tr><th>Systemdetails</th></tr>
		<tr><td><img src=".$gfx."/map/".$sys['type'].".gif width=15 height=15> <a href=\"javascript:void(0);\" onClick=\"showsysinfo(".$_GET['id'].");\">".$sys['name']."</a>";
		if ($ship->m11 > 0) echo "<br><a href=?p=stat&s=ss&a=ls&id=".$_GET['id']." onmouseover=cp('sl','buttons/sysleave2') onmouseout=cp('sl','buttons/sysleave1')><img src=".$gfx."/buttons/sysleave1.gif name=sl border=0 title='Verlassen'> Verlassen</a>";
		echo "</td></tr></table>";
	}
	echo "</td></tr></table>";
	$result = $db->query("SELECT a.id,a.user_id,a.rumps_id,a.name,a.huelle,a.max_huelle,b.user FROM stu_ships as a LEFT JOIN stu_user as b ON a.user_id=b.id WHERE a.dock=".$ship->id);
	if (mysql_num_rows($result) > 0)
	{
		echo "<br><table bgcolor=#262323 cellspacing=1 cellpadding=1>
		<th colspan=4>Angedockte Schiffe</th>";
		while($data=mysql_fetch_assoc($result))
		{
			$i++;
			echo "<tr><td>".($data['user_id'] == $_SESSION["uid"] ? "<a href=?p=ship&s=ss&id=".$data['id'].">" : "")."<img src=".$gfx."/ships/".vdam($data).$data[rumps_id].".gif border=0 title=\"".ftit($data['name'])."\">".($data['user_id'] == $_SESSION["uid"] ? "</a>" : "")."</td>
			<td>".stripslashes($data['name'])."</td><td>".stripslashes($data['user'])."</td><td><a href=?p=stat&s=ss&id=".$_GET['id']."&a=dedoc&t=".$data['id']." ".getonm('ddo'.$data['id'],'buttons/x')."><img src=".$gfx."/buttons/x1.gif name=ddo".$data['id']." border=0 title=\"Die ".ftit($data['name'])." abdocken\"></a>";
			if ($_SESSION['uid'] < 100)
			{
				echo "&nbsp;<a href=?p=stat&s=ss&id=".$_GET['id']."&a=repsh&t=".$data['id']." ".getonm('rep'.$data['id'],'buttons/rep')."><img src=".$gfx."/buttons/rep1.gif name=rep".$data['id']." border=0 title=\"".ftit($data['name'])." reparieren\"></a>";
				echo "&nbsp;<a href=?p=stat&s=ss&id=".$_GET['id']."&a=maish&t=".$data['id']." ".getonm('mai'.$data['id'],'buttons/maint')."><img src=".$gfx."/buttons/maint1.gif name=mai".$data['id']." border=0 title=\"".ftit($data['name'])." warten\"></a>";
			}
			echo "&nbsp;<a href=\"javascript:void(0);\" onClick=\"ship_etransfer(".$_GET['id'].",".$data['id'].");\" ".getonm("et".$i,"buttons/e_trans")."><img src=".$gfx."/buttons/e_trans1.gif title='Energie zur ".ftit($data['name'])." transferieren' border=0 name=et".$i."></a>";
			echo "&nbsp;<a href=?p=stat&s=be&m=to&id=".$_GET['id']."&t=".$data['id']." ".getonm("sbt".$i,"buttons/b_down")."><img src=".$gfx."/buttons/b_down1.gif name=sbt".$i." title='Zu der ".ftit($data['name'])." beamen' border=0></a>&nbsp;<a href=?p=stat&s=be&m=fr&id=".$_GET['id']."&t=".$data['id']." ".getonm("sbf".$i,"buttons/b_up")."><img src=".$gfx."/buttons/b_up1.gif name=sbf".$i." title='Von der ".ftit($data['name'])." beamen' border=0></a>";
			echo "&nbsp;<a href=\"javascript:void(0);\" onClick=\"open_pm_window(".$data['user_id'].",".$_GET['id'].",".$data['id'].");\" ".getonm("spm".$i,"buttons/msg")."><img src=".$gfx."/buttons/msg1.gif name=spm".$i." title='Eine PM an die ".ftit($data['name'])." schicken' border=0></a>";
			echo "</td></tr>";
		}
		if ($ship->rumps_id >= 9001 && $ship->rumps_id <= 9005) echo "<tr><td colspan=\"4\"><a href=?p=stat&s=war&id=".$_GET['id']." ".getonm('war','buttons/maint')."><img src=\"".$gfx."/buttons/maint1.gif\" name=\"war\" border=\"0\" title=\"Wartungsübersicht\"> Wartungsübersicht</a></td></tr>";
		echo "</table>";
	}
	$result = $db->query("SELECT a.id,a.type,a.mode,b.name as sname,c.user as uname,d.name as aname FROM stu_dockingrights as a LEFT JOIN stu_ships as b ON a.id=b.id LEFT JOIN stu_user as c ON a.id=c.id LEFT JOIN stu_allylist as d ON a.id=d.allys_id WHERE a.ships_id=".$ship->id." AND a.type!='4' ORDER BY a.type,a.id");
	echo "<br><table bgcolor=#262323 cellspacing=1 cellpadding=1 width=500>
	<th colspan=4>Andockrechte</th>";
	while($data=mysql_fetch_assoc($result))
	{
		echo "<tr>";
		if ($data['type'] == 1) echo "<td>Schiff</td><td>".stripslashes($data['sname'])." (".$data['id'].")</td>";
		if ($data['type'] == 2) echo "<td>Siedler</td><td>".stripslashes($data['uname'])." (".$data['id'].")</td>";
		if ($data['type'] == 3) echo "<td>Allianz</td><td>".stripslashes($data['aname'])." (".$data['id'].")</td>";
		echo "<td>".($data['mode'] == 1 ? "<font color=Green>erlauben</font>" : "<font color=#FF0000>verweigern</font>")."</td>
		<td><a href=?p=stat&s=ss&id=".$_GET['id']."&a=ddr&t=".$data['id']."&ty=".$data['type']." ".getonm('ddr'.$data['id'],'buttons/x')."><img src=".$gfx."/buttons/x1.gif name=ddr".$data['id']." border=0 title=\"Andockregel löschen\"></a></td></tr>";
	}
	$result = $db->query("SELECT ships_id FROM stu_dockingrights WHERE ships_id=".$ship->id." AND type='4' LIMIT 1",1);
	echo "<form action=main.php method=get><input type=hidden name=p value=stat><input type=hidden name=s value=ss><input type=hidden name=id value=".$_GET['id']."><input type=hidden name=a value=cfdr>
	<tr>
	<td colspan=4>Allgemeine Erlaubnis für Freunde? <input type=checkbox name=fdr value=1".($result > 0 ? " CHECKED" : "")."> <input type=submit value=ändern class=button></td>
	</tr></form>
	<form action=main.php method=get><input type=hidden name=p value=stat><input type=hidden name=s value=ss><input type=hidden name=id value=".$_GET['id']."><input type=hidden name=a value=ndr>
	<tr><td colspan=4>ID <input type=text size=3 name=ta class=text> <select name=ty><option value=1>Schiff<option value=2>Siedler<option value=3>Allianz</select> <select name=m><option value=1>erlauben<option value=2>verweigern</select> <input type=submit value=Erstellen class=button></td></tr>
	</form></table><br>";
	if ($ship->nbs == 0) echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr><th><a href=?p=stat&s=ss&a=av&d=nbs&id=".$_GET['id']." ".getonm("kss","buttons/kss")."><img src=".$gfx."/buttons/kss1.gif name=kss title='Nahbereichssensoren aktivieren' border=0> Nahbereichssensoren aktivieren</a></th></tr></table>";
	else
	{
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1>
		<tr><th><a href=?p=stat&s=ss&a=dv&d=nbs&id=".$_GET['id']." ".getronm("kss","buttons/kss")."><img src=".$gfx."/buttons/kss2.gif name=kss title='Nahbereichssensoren deaktivieren' border=0></a></th><th>Name</th><th>Siedler</th><th>ID</th><th align=center>Zustand</th><th align=center>S</th><th align=center>In</th><th align=center>A</th><th align=center>T</th><th align=center>E</th><th align=center>Beamen</th><th align=center>N</th></tr>";
		if ($col != 0)
		{
			$img = "<img src=".$gfx."/planets/".$ship->map['cid'].($col['schilde_status'] == 1 ? "s" : "").".gif border=0 title=\"".ftit($col['cname'])."\">";
			if ($col['user_id'] != 1) $col['user_id'] == $_SESSION['uid'] ? $img = "<a href=?p=colony&s=sc&id=".$col['id']."&shd=".$_GET['id'].">".$img."</a>" : $img = $img;
			echo "<tr><td>".$img."</td><td>".($col['user_id'] == 1 ? stripslashes($sys['name'])." ".$col['planet_name'] : stripslashes($col['name']))."</td><td>".stripslashes($col['user'])." (".$col['user_id'].")</td><td>".$col['id']."</td><td align=center>-</td>";
			echo "<td><a href=?p=stat&s=csc&id=".$_GET['id']."&t=".$col['id']." ".getonm("cosc","buttons/lupe")."><img src=".$gfx."/buttons/lupe1.gif name=cosc title='Kolonie scannen' border=0></a></td>";
			echo "<td align=center>-</td><td align=center>-</td><td align=center>-</td>";
			echo "<td align=center><a href=\"javascript:void(0);\" onClick=\"col_etransfer(".$_GET['id'].",".$col['id'].");\" ".getonm("coet","buttons/e_trans")."><img src=".$gfx."/buttons/e_trans1.gif name=coet title='Energietransfer' border=0></a></td>";
			echo "<td align=center><a href=?p=stat&s=bec&id=".$_GET['id']."&m=to&t=".$col['id']." ".getonm("cobt","buttons/b_down")."><img src=".$gfx."/buttons/b_down1.gif name=cobt title=\"Zur Kolonie beamen\" border=0></a> <a href=?p=stat&s=bec&id=".$_GET['id']."&m=fr&t=".$col['id']." ".getonm("cobf","buttons/b_up")."><img src=".$gfx."/buttons/b_up1.gif name=cobf title='Von der Kolonie beamen' border=0></a></td>";
			echo "<td align=center><a href=\"javascript:void(0);\" onClick=\"open_pm_window(".$col['user_id'].",0,0);\" ".getonm("comsg","buttons/msg")."><img src=".$gfx."/buttons/msg1.gif name=comsg border=0 title=\"Eine PM an ".ftit($col['user'])." schicken\"></a></td></tr>";
		}
		$result = $map->nbs();
		while($ret=mysql_fetch_assoc($result)) $arr[] = $ret;
		function cmp(&$a, &$b)
		{
			if ($a['fleets_id'] == $b['fleets_id']) return 0;
			return ($a['fleets_id'] > $b['fleets_id']) ? -1 : 1;
		}
		@usort($arr, "cmp");
		if (count($arr) > 0)
		{
			foreach($arr as $key => $data)
			{
				if (!$data['dcship_id'] && $data['cloak'] == 1 && $data['user_id'] != $_SESSION['uid'] && ($data['allys_id'] != $_SESSION['allys_id'] || $_SESSION['allys_id'] == 0) && $data['type'] != 4 && $data['mode'] != 1)
				{
					$cl++;
					continue;
				}
				$i++;
				if ($lf != $data['fleets_id'] && $data['fleets_id'] > 0)
				{
					echo "<td colspan=12>".($data['user_id'] == $_SESSION['uid'] ? "<a href=?p=ship&s=ss&id=".$data['fship_id'].">".stripslashes($data['fname'])."</a>" : stripslashes($data['fname']))."</td>";
					$lf = $data['fleets_id'];
				}
				if ($lf != $data['fleets_id'] && $data['fleets_id'] == 0 && $data['slots'] == 0)
				{
					echo "<td colspan=12>Einzelschiffe</td>";
					$lf = 0;
				}
				echo "<tr><td>".($data['user_id'] == $_SESSION['uid'] ? "<a href=?p=ship&s=ss&id=".$data['id']."><img src=".$gfx."/ships/".vdam($data).($data['trumfield'] == 1 ? $data['trumps_id'] : $data['rumps_id']).".gif title=\"".ftit($data['cname'])."\" border=0></a>" : "<img src=".$gfx."/ships/".vdam($data).($data['trumfield'] == 1 ? $data['trumps_id'] : $data['rumps_id']).".gif title=\"".stripslashes($data['cname'])."\">")."</td>
				<td>".stripslashes($data['name'])."</td><td>".($data['is_rkn'] > 0 ? "<img src=".$gfx."/rassen/".$data['is_rkn']."s.gif> " : "").stripslashes($data['user'])." ".($data['user_id'] < 101 ? "<b>NPC</b>" : "(".$data['user_id'].")")."</td>";
				if ($data['cloak'] == 1)
				{
					echo "<td>".$data['id']."</td>
					<td align=center>-</td>
					<td align=center>-</td>
					<td align=center>-</td>
					<td align=center>".($ship->phaser > 0 || $ship->torp_type > 0 ? "<a href=?p=stat&s=ss&a=att&id=".$_GET['id']."&t=".$data['id']." ".getonm("ph".$i,"buttons/phaser")."><img src=".$gfx."/buttons/phaser1.gif title='Angreifen' name=ph".$i." border=0></a>" : "-")."</td>
					<td colspan=3 align=center><font color=gray>getarnt</font></td><td><a href=?p=comm&s=nn&recipient=".$data['user_id']." ".getonm("pm".$i,"buttons/msg")."><img src=".$gfx."/buttons/msg1.gif name=pm".$i." title='Eine PM an ".ftit($data['user'])." schicken' border=0></a></td></tr>";
					continue;
				}
				if ($data['traktormode'] == 1) $tr = ">";
				elseif ($data['traktormode'] == 2) $tr = "<";
				else $tr = "";
				echo "<td>".$data['id']." ".$tr."</td><td align=center>".$data['huelle']."/".$data['max_huelle'].($data['schilde_status'] == 1 ? " (<font color=cyan>".$data['schilde']."</font>)" : "")."</td>";
				echo "<td align=center>".($data['user_id'] == $_SESSION['uid'] && $ship->is_shuttle == 1 && $data['is_shuttle'] != 1 ? "<a href=?p=stat&s=ss&a=lansh&t=".$data['id']."&id=".$_GET['id']." ".getonm("sc".$i,"buttons/shu_l")."><img src=".$gfx."/buttons/shu_l1.gif border=0 name=sc".$i." title='Shuttle landen'></a>" : ($data['warp'] == 1 ? "-" : "<a href=?p=stat&s=sc&id=".$_GET['id']."&t=".$data['id']."&m=s ".getonm("sc".$i,"buttons/lupe")."><img src=".$gfx."/buttons/lupe1.gif border=0 name=sc".$i." title='Scan'></a>"))."</td>";
				echo "<td align=center>".($data['warp'] == 1 ? "<a href=?p=stat&s=ss&id=".$_GET['id']."&a=int&t=".$data['id']." ".getonm("inc".$i,"buttons/inc")."><img src=".$gfx."/buttons/inc1.gif title=\"Abfangen\" border=0 name=inc".$i."></a>" : "-")."</td>";
				echo "<td align=center>".($ship->phaser > 0 || $ship->torp_type > 0 ? "<a href=?p=stat&s=ss&a=att&id=".$_GET['id']."&t=".$data['id']." ".getonm("ph".$i,"buttons/phaser")."><img src=".$gfx."/buttons/phaser1.gif title='Angreifen' name=ph".$i." border=0></a>" : "-")."</td>";
				echo "<td align=center>".($data['slots'] == 0 && $data['trumfield'] == 0 ? ($ship->traktor == $data['id'] && $ship->traktormode == 1 ? "<img src=".$gfx."/buttons/trak2.gif border=0 title='Dieses Schiff wird im Traktorstrahl gehalten'>" : "<a href=?p=stat&s=ss&a=tr&id=".$_GET['id']."&t=".$data['id']." ".getonm("tr".$i,"buttons/trak")."><img src=".$gfx."/buttons/trak1.gif border=0 title='Traktorstrahl aktivieren' name=tr".$i."></a>") : "-")."</td>";
				echo "<td align=center><a href=\"javascript:void(0);\" onClick=\"ship_etransfer(".$_GET['id'].",".$data['id'].");\" ".getonm("et".$i,"buttons/e_trans")."><img src=".$gfx."/buttons/e_trans1.gif title='Energie zur ".ftit($data['name'])." transferieren' border=0 name=et".$i."></a></td>";
				echo "<td align=center><a href=?p=stat&s=be&m=to&id=".$_GET['id']."&t=".$data['id']." ".getonm("bt".$i,"buttons/b_down")."><img src=".$gfx."/buttons/b_down1.gif name=bt".$i." title='Zu der ".ftit($data['name'])." beamen' border=0></a>&nbsp;<a href=?p=stat&s=be&m=fr&id=".$_GET['id']."&t=".$data['id']." ".getonm("bf".$i,"buttons/b_up")."><img src=".$gfx."/buttons/b_up1.gif name=bf".$i." title='Von der ".ftit($data['name'])." beamen' border=0></a></td>";
				echo "<td align=center><a href=\"javascript:void(0);\" onClick=\"open_pm_window(".$data['user_id'].",".$_GET['id'].",".$data['id'].");\" ".getonm("pm".$i,"buttons/msg")."><img src=".$gfx."/buttons/msg1.gif name=pm".$i." title='Eine PM an die ".ftit($data['name'])." schicken' border=0></a></td></tr>";
			}
		}
		if ($cl > 0) echo "<tr><td colspan=12>Es befinden sich nicht scanbare Objekte in diesem Sektor</td></tr>";
		echo "</table>";
	}
	echo "<br>";
	if ($ship->lss == 0) echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr><th><a href=?p=stat&s=ss&a=av&d=lss&id=".$_GET['id']." ".getonm("lss","buttons/lss")."><img src=".$gfx."/buttons/lss1.gif name=lss title='".($ship->systems_id == 0 ? "Lang" : "Kurz")."streckensensoren aktivieren' border=0> ".($ship->systems_id == 0 ? "Lang" : "Kurz")."streckensensoren aktivieren</a></th></tr></table>";
	else
	{
		if ($_SESSION["level"] == 1) echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr><th><a href=?p=main&s=fm>Liste kolonisierbarer Planeten</a></th></tr></table><br>";
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr><td class=l align=center><a href=?p=stat&s=ss&a=dv&d=lss&id=".$_GET['id']." ".getronm("lss","buttons/lss")."><img src=".$gfx."/buttons/lss2.gif name=lss title='".($ship->systems_id == 0 ? "Lang" : "Kurz")."streckensensoren deaktivieren' border=0></a></td>";
		$ship->systems_id > 0 ? $result = $map->getkss($ship->sx,$ship->sy,$ship->kss_range,$ship->systems_id) : $result = $map->getlss($ship->cx,$ship->cy,$ship->lss_range);
		for ($i=($ship->systems_id > 0 ? $ship->sx-$ship->kss_range : $ship->cx-$ship->lss_range);$i<=($ship->systems_id > 0 ? $ship->sx+$ship->kss_range : $ship->cx+$ship->lss_range);$i++) if ($i > 0 && $i <= ($ship->systems_id > 0 ? $sys['sr'] : $mapfields['max_x'])) echo "<th align=center width=30>".$i."</th>";
		while($data=mysql_fetch_assoc($result))
		{
			if ($data['cy'] != $yd) { echo "</tr><tr><th align=center>".$data['cy']."</th>"; $yd = $data['cy']; }
			if (!$yd) $yd = $data['cy'];
			if ($data['cx'] == ($ship->systems_id > 0 ? $ship->sx : $ship->cx) && $data['cy'] == ($ship->systems_id > 0 ? $ship->sy : $ship->cy)) $border = " bordercolor=#929191 style='border: 1px solid #929191'";
			if ($ship->systems_id == 0 && $data['faction_id'] > 0 && $data['is_border'] == 1) $border = " bordercolor=".$data['color']." style='border: 1px outset ".$data['color']."'";
			$sc = ($data['sc'] == 0 || $ship->map['type'] == 7 || $ship->map['type'] == 9 ? "&nbsp;" : $data['sc']);
			if ($data['type'] == 7 && $sc > 0) $sc = "X";
			if (!$data[special]) echo "<td class=l background=".$gfx."/map/".$data['type'].".gif align=center".$border."".($sc > 0 ? " title=\"Signaturen: ".$sc."\"": "").">".$sc."</td>";
			else echo "<td class=l background=".$gfx."/map/".$data['type']."x.gif align=center".$border."".($sc > 0 ? " title=\"Signaturen: ".$sc."\"": "").">".$sc."</td>";
			$border = "";
		}
		echo "</tr></table>";
	}
	$stor = $ship->getshipstorage($ship->id);
	$i = 0;
	$s = "";
	while ($sd=mysql_fetch_assoc($stor))
	{
		if ($i == 0) $s .= "<tr>";
		$s .= "<td><img src=".$gfx."/goods/".$sd['goods_id'].".gif title=\"".$sd['name']."\">&nbsp;".$sd['count']."</td>";
		$i++;
		if ($i == 2) { $s .= "</tr>"; $i = 0; }
		$cn += $sd['count'];
	}
	if ($i == 1) $s .= "<td></td></tr>";
	if (!$cn) $cn = 0;
	echo "<br><table bgcolor=#262323 cellspacing=1 cellpadding=1>
	<tr>
		<th colspan=3><img src=".$gfx."/buttons/lager.gif title='Laderaum'> ".$cn."/".$ship->storage." (".round((100/$ship->storage)*$cn,2)."%)</th>
	</tr>".$s."</table>";
}
if ($v == "beam")
{
	if (($_GET["m"] != "to" && $_GET["m"] != "fr") || !$_GET["t"] || !check_int($_GET["t"])) exit;
	$tar = $db->query("SELECT a.id,a.name,a.user_id,a.rumps_id,a.cloak,a.cx,a.cy,a.sx,a.sy,a.systems_id,a.huelle,a.max_huelle,a.crew,a.max_crew,a.trumps_id,a.is_hp,b.name as cname,b.storage,b.trumfield FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) WHERE a.id=".$_GET['t']." LIMIT 1",4);
	if (checksector($tar) == 0) exit;
	pageheader("/ <a href=?p=ship>Schiffe</a> / <a href=?p=stat&s=ss&id=".$_GET["id"].">".stripslashes($ship->name)."</a> / <b>".($_GET["m"] == "to" ? "Zu" : "Von")." der ".stripslashes($tar['name'])." beamen</b>");
	$_GET["m"] == "to" ? $stor = $ship->getshipstorage($_GET["id"]) : $stor = $ship->getshipstorage($_GET["t"]);
	echo "<form action=main.php method=post><input type=hidden name=bshi value=1><input type=hidden name=p value=stat><input type=hidden name=s value=ss><input type=hidden name=a value=be>
	<input type=hidden name=t value=".$_GET["t"]."><input type=hidden name=m value=".$_GET["m"]."><input type=hidden name=id value=".$_GET["id"].">
	<table cellspacing=1 cellpadding=1>
	<tr><td valign=top>";
	if (mysql_num_rows($stor) == 0) meldung("Keine Waren auf der ".($_GET["m"] == "fr" ? stripslashes($tar['name']) : $ship->name)." vorhanden");
	else
	{
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr><th colspan=4>Waren auswählen</th></tr>";
		while($sd=mysql_fetch_assoc($stor))
		{
			if ($sd['goods_id'] >= 80 && $sd['goods_id'] < 110 && $_SESSION['uid'] != $tar['user_id'] && $_GET["m"] == "fr" && $tar['is_hp'] != 1) continue;
			if ($i == 0) echo "<tr>";
			echo "<td><input type=hidden name=good[] value=".$sd['goods_id'].">
			<img src=".$gfx."/goods/".$sd['goods_id'].".gif title='".$sd['name']."'> ".$sd['count']."</td><td><input type=text size=3 name=count[] class=text></td>";
			$i++;
			if ($i == 2) { echo "</tr>"; $i = 0; }
		}
		if ($i == 1) echo "<td colspan=2></td></tr>";
		echo "</table><br><input type=submit class=button value=Beamen>";
	}
	echo "</td><td valign=top>
	<table bgcolor=#262323 cellspacing=1 cellpadding=1>
	<tr><th colspan=3>Informationen</th></tr>
	<tr><td colspan=3>Energie: ".$ship->eps." (".$ship->batt.")<br>
	Ladung: ".($_GET["m"] == "to" ? $ship->getshipstoragesum($tar['id'])."/".$tar[storage] : $ship->getshipstoragesum($_GET["id"])."/".$ship->storage)."</td></tr>";
	if ($_SESSION["uid"] == $tar['user_id']) echo "<tr><td colspan=3><img src=".$gfx."/buttons/crew.gif title='Crew'> <input type=text size=2 class=text name=crew> ".($_GET["m"] == "to" ? $ship->crew."/".($tar[max_crew]-$tar[crew]) : $tar[crew]."/".($ship->max_crew-$ship->crew))."</td></tr>";
	echo "<tr><th colspan=3>Modus ändern</th></tr>
	<tr><td><a href=?p=stat&s=ss&id=".$_GET['id']."><img src=".$gfx."/ships/".vdam($ship).$ship->rumps_id.".gif border=0 title=\"".$ship->cname."\"></a></td><td>";
	if ($_GET["m"] == "to") echo "<a href=?p=stat&s=be&m=fr&id=".$_GET["id"]."&t=".$tar['id']." ".getonm('bm','buttons/b_from')."><img src=".$gfx."/buttons/b_from1.gif name=bm border=0 title=\" Von der ".ftit($tar['name'])." beamen\"></a>";
	else echo "<a href=?p=stat&s=be&m=to&id=".$_GET['id']."&t=".$tar['id']." ".getonm('bm','buttons/b_to')."><img src=".$gfx."/buttons/b_to1.gif name=bm border=0 title=\" Zu der ".ftit($tar['name'])." beamen\"></a>";
	echo "</td><td>".($tar['user_id'] == $_SESSION['uid'] ? "<a href=?p=ship&s=ss&id=".$tar['id']."><img src=".$gfx."/ships/".vdam($tar).($tar[trumfield] == 1 ? $tar[trumps_id] : $tar[rumps_id]).".gif border=0 title=\"".$tar[cname]."\"></a>" : "<img src=".$gfx."/ships/".vdam($tar).($tar[trumfield] == 1 ? $tar[trumps_id] : $tar[rumps_id]).".gif title=\"".$tar[cname]."\">")."</td></tr>
	<tr><td colspan=3 align=center><input type=submit class=button value=Beamen></td></tr>
	</table>
	</td></tr></table></form>";
}
if ($v == "beamc")
{
	if (($_GET["m"] != "to" && $_GET["m"] != "fr") || !$_GET["t"] || !check_int($_GET["t"])) exit;
	$tar = $db->query("SELECT id,user_id,name,colonies_classes_id as cid,sx,sy,systems_id,bev_free,bev_work,bev_max,max_storage FROM stu_colonies WHERE id=".$_GET["t"]." LIMIT 1",4);
	if (checksector($tar) == 0) exit;
	pageheader("/ <a href=?p=ship>Schiffe</a> / <a href=?p=stat&s=ss&id=".$_GET["id"].">".stripslashes($ship->name)."</a> / <b>".($_GET["m"] == "to" ? "Zu" : "Von")." der Kolonie ".stripslashes($tar['name'])." beamen</b>");
	if ($_GET['m'] == "fr" && $tar['user_id'] != $_SESSION['uid'] && $db->query("SELECT field_id FROM stu_colonies_fielddata WHERE buildings_id=107 AND aktiv=1 AND colonies_id=".$_GET['t']." LIMIT 1",1) > 0)
	{
		meldung("Die Oberfläche der Kolonie ist nicht scanbar");
		exit;
	}
	$_GET['m'] == "to" ? $stor = $ship->getshipstorage($ship->id) : $stor = $ship->getcolstorage($_GET['t']);
	echo "<form action=main.php method=post><input type=hidden name=bshc value=1><input type=hidden name=p value=stat><input type=hidden name=s value=ss><input type=hidden name=a value=bec>
	<input type=hidden name=t value=".$_GET['t']."><input type=hidden name=m value=".$_GET['m']."><input type=hidden name=id value=".$_GET["id"].">
	<table cellspacing=1 cellpadding=1>
	<tr><td valign=top>";
	if (mysql_num_rows($stor) == 0) meldung("Keine Waren auf der ".($_GET["m"] == "fr" ? stripslashes($tar['name']) : $ship->name)." vorhanden");
	else
	{
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr><th colspan=4>Waren auswählen</th></tr>";
		while($sd=mysql_fetch_assoc($stor))
		{
			if ($i == 0) echo "<tr>";
			echo "<td><input type=hidden name=good[] value=".$sd['goods_id'].">
			<img src=".$gfx."/goods/".$sd['goods_id'].".gif title='".$sd['name']."'> ".$sd['count']."</td><td><input type=text size=3 name=count[] class=text></td>";
			$i++;
			if ($i == 2) { echo "</tr>"; $i = 0; }
		}
		if ($i == 1) echo "<td colspan=2></td></tr>";
		echo "</table><br><input type=submit class=button value=Beamen>";
	}
	echo "</td><td valign=top>
	<table bgcolor=#262323 cellspacing=1 cellpadding=1>
	<tr><th colspan=3>Informationen</th></tr>
	<tr><td colspan=3>Energie: ".$ship->eps." (".$ship->batt.")<br>
	Ladung: ".($_GET["m"] == "to" ? $ship->getcolstoragesum($tar['id'])."/".$tar[max_storage] : $ship->getshipstoragesum($_GET["id"])."/".$ship->storage)."</td></tr>";
	if ($_SESSION["uid"] == $tar['user_id']) echo "<tr><td colspan=3><img src=".$gfx."/buttons/crew.gif title='Crew'> <input type=text size=2 class=text name=crew> ".($_GET["m"] == "to" ? $ship->crew."/".($tar[bev_max]-$tar[bev_free]-$tar[bev_work]) : $tar[bev_free]."/".($ship->max_crew-$ship->crew))."</td></tr>";
	echo "<tr><th colspan=3>Modus</th></tr>
	<tr><td><a href=?p=stat&s=ss&id=".$_GET['id']."><img src=".$gfx."/ships/".vdam($ship).$ship->rumps_id.".gif border=0 title=\"".$ship->cname."\"></a></td><td>";
	if ($_GET["m"] == "to") echo "<a href=?p=stat&s=bec&m=fr&id=".$_GET["id"]."&t=".$tar['id']." onmouseover=cp('bm','buttons/b_from2') onmouseout=cp('bm','buttons/b_to1')><img src=".$gfx."/buttons/b_to1.gif name=bm border=0 title=\" Von der Kolonie ".stripslashes($tar['name'])." beamen\"></a>";
	else echo "<a href=?p=stat&s=bec&m=to&id=".$_GET["id"]."&t=".$tar['id']." onmouseover=cp('bm','buttons/b_to2') onmouseout=cp('bm','buttons/b_from1')><img src=".$gfx."/buttons/b_from1.gif name=bm border=0 title=\" Zu der Kolonie ".stripslashes($tar['name'])." beamen\"></a>";
	echo "</td><td>".($tar['user_id'] == $_SESSION["uid"] ? "<a href=?p=colony&s=sc&id=".$tar['id']."><img src=".$gfx."/planets/".$tar[cid].($tar['schilde_status'] == 1 ? "s" : "").".gif border=0></a>" : "<img src=".$gfx."/planets/".$tar[cid].($tar['schilde_status'] == 1 ? "s" : "").".gif border=0>")."</td></tr>
	<tr><td colspan=3 align=center><input type=submit class=button value=Beamen></td></tr></table>
	</td></tr></table></form>";
}
if ($v == "scan")
{
	if (!$_GET['t'] || !check_int($_GET['t'])) exit;
	pageheader("/ <a href=?p=ship>Schiffe</a> / <a href=?p=stat&s=ss&id=".$_GET['id'].">".stripslashes($ship->name)."</a> / <b>Scanergebnisse ".stripslashes($data['name'])."</b>");
	if ($ship->nbs == 0)
	{
		meldung("Zum scannen werden aktivierte ".($ship->systems_id > 0 ? "Nahbereichssensoren" : "Kurzstreckensensoren")." benötigt");
		exit;
	}
	if ($ship->eps < 1)
	{
		meldung("Zum scannen wird 1 Energie benötigt");
		exit;
	}
	$data = $db->query("SELECT a.id,a.rumps_id,a.fleets_id,a.user_id,a.name,a.sx,a.sy,a.cx,a.cy,a.systems_id,a.huelle,a.max_huelle,a.eps,a.max_eps,a.schilde,a.schilde_status,a.crew,a.alvl,a.nbs,a.lss,a.wea_phaser,a.wea_torp,b.name as cname,c.allys_id FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_user as c ON c.id=a.user_id WHERE a.id=".$_GET['t']." LIMIT 1",4);
	$db->query("UPDATE stu_ships SET eps=eps-1 WHERE id=".$ship->id." LIMIT 1");
	if (!$data || $data == 0) exit;
	if (checksector($data) == 0) exit;
	if ($data['warp'] == 1 || $ship->warp == 1) exit;
	$qpm->send_pm($_SESSION['uid'],$data['user_id'],"Die ".stripslashes($ship->name)." hat die ".stripslashes($data['name'])." in Sektor ".($ship->systems_id > 0 ? $ship->sx."|".$ship->sy." (".$ship->m->getsysnamebyid($ship->systems_id)."-System ".$ship->cx."|".$ship->cy.")" : $ship->cx."|".$ship->cy)." gescant",3);
	if ($ship->cloak == 0 && ($data['allys_id'] == 0 || $data['allys_id'] != $_SESSION['allys_id']) && $data['user_id'] != $_SESSION['uid'] && ($data['wea_phaser'] == 1 || $data['wea_torp'] == 1) && (($data['alvl'] == 3 && !isfriend($data['user_id'],$data['allys_id'],$_SESSION['uid'],$_SESSION['allys_id'])) || ($data['alvl'] == 2 && isenemy($data['user_id'],$data['allys_id'],$_SESSION['uid'],$_SESSION['allys_id']))))
	{
		if ($data['fleets_id'] == 0) $result = $ship->attack($ship->id,$data['id'],0,1,1);
		else $result = $fleet->attack($ship->id,$data['fleets_id'],1);
	}
	if ($result || $sresult)
	{
		if ($sresult) $result = $sresult;
		if (is_array($ship->logbook)) $ship->write_logbook();
		if ($ship->destroyed == 0 && !$ship->dsships[$_GET['id']] && !$fleet->dsships[$_GET['id']]  && $_GET['a'] != "col" && $ship->colonized != 1) $ship = new station;
		meldung($result);
	}
	if ($ship->destroyed == 1 || $ship->dsships[$_GET['id']] == 1 || $fleet->dsships[$_GET['id']] == 1)
	{
		meldung("Du bist nicht Besitzer dieses Schiffes");
		exit;
	}
	echo "<script language=\"Javascript\">
	function ship_etransfer(id,t)
	{
		elt = 'setrans';
		sendRequest('backend/ship/setransfer.php?PHPSESSID=".session_id()."&id='+id+'&t='+t+'&e=".$ship->eps."');
		return overlib('<div id=setrans></div>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER, STICKY, WIDTH, 300);
	}
	</script>";
	// Forschungen einfügen
	switch ($data['rumps_id'])
	{
		default:
			$resid = 0;
		case 1806:
			$resid = 743;
			break;
		case 2306:
			$resid = 743;
			break;
		case 1810:
			$resid = 741;
			break;
		case 5510:
			$resid = 741;
			break;
		case 1808:
			$resid = 742;
			break;
		case 5508:
			$resid = 742;
			break;
		case 1811:
			$resid = 744;
			break;
	}
	if ($resid != 0 && $db->query("SELECT research_id FROM stu_researched WHERE user_id=".$_SESSION['uid']." AND research_id=".($resid-10)." LIMIT 1",1) > 0)
	{
		if ($db->query("SELECT research_id FROM stu_researched WHERE user_id=".$_SESSION['uid']." AND research_id=".$resid." LIMIT 1",1) == 0)
		{
			$db->query("INSERT INTO stu_researched (research_id,user_id) VALUES ('".($resid+10)."','".$_SESSION['uid']."')");
			$db->query("INSERT INTO stu_researched_disables (research_id,user_id) VALUES ('".($resid)."','".$_SESSION['uid']."')");
			$db->query("INSERT INTO stu_pms (send_user,recip_user,text,type,date) VALUES ('6','".$_SESSION['uid']."','".addslashes("Die durch den Scan gewonnenen Daten kommen gerade herein. Diese sollten ausreichen, wir beginnen sofort mit der Entwicklung von Modulen.")."','1',NOW())");
		}
	}

	echo "<table>
	<tr>
	<td valign=\"top\">
		<table bgcolor=#262323 cellspacing=1 cellpadding=1>
		<tr><th colspan=3>".stripslashes($data['name'])."</th></tr>
		<tr>
			<td rowspan=7 valign=top><img src=".$gfx."/ships/".vdam($data).$data['rumps_id'].".gif title=\"".ftit($data['cname'])."\"><br /><br />
				<a href=\"javascript:void(0);\" onClick=\"ship_etransfer(".$_GET['id'].",".$data['id'].");\" onmouseover=cp('et','buttons/e_trans2') onmouseout=cp('et','buttons/e_trans1')><img src=".$gfx."/buttons/e_trans1.gif title='Energie zur ".stripslashes(strip_tags($data['name']))." transferieren' border=0 name=et></a>
				<a href=?p=ship&s=be&m=to&id=".$_GET['id']."&t=".$data['id']." onmouseover=cp('bt','buttons/b_to2') onmouseout=cp('bt','buttons/b_to1')><img src=".$gfx."/buttons/b_to1.gif name=bt title='Zu der ".strip_tags(stripslashes($data['name']))." beamen' border=0></a>&nbsp;<a href=?p=ship&s=be&m=fr&id=".$_GET['id']."&t=".$data['id']." onmouseover=cp('bf','buttons/b_from2') onmouseout=cp('bf','buttons/b_from1')><img src=".$gfx."/buttons/b_from1.gif name=bf title='Von der ".ftit($data['name'])." beamen' border=0></a></td>
			<td>Zustand</td>
			<td>".$data['huelle']."/".$data['max_huelle']." (".round((100/$data['max_huelle'])*$data['huelle'])."%)</td>
		</tr>
		<tr>
			<td>Schilde</td>
			<td>".($data['schilde_status'] != 1 ? "deaktiviert" : "<font color=cyan>".$data['schilde'])."</font></td>
		</tr>
		<tr>
			<td>Energie</td>
			<td>".round((100/$data['max_eps'])*$data['eps'])."%</td>
		</tr>
		<tr>
			<td>Alarmbereitschaft</td>
			<td>".($data['alvl'] > 1 ? "Ja" : "Nein")."</td>
		</tr>
		<tr>
			<td>Waffensysteme</td>
			<td>".($data['wea_phaser'] == 1 ? "<img src=".$gfx."/buttons/act_phaser2.gif title=\"Waffensystem (Phaser) ist aktiviert\">" : "<img src=".$gfx."/buttons/act_phaser1.gif title=\"Waffensystem (Phaser) ist deaktiviert\">")." ".($data['wea_torp'] == 1 ? "<img src=".$gfx."/buttons/act_torp2.gif title=\"Waffensystem (Torpedobänke) ist aktiviert\">" : "<img src=".$gfx."/buttons/act_torp1.gif title=\"Waffensystem (Torpedobänke) ist deaktiviert\">")."</td>
		</tr>
		<tr>
			<td>Sensoren</td>
			<td>".($data['nbs'] == 1 ? "*" : "-")."|".($data['lss'] == 1 ? "*" : "-")."</td>
		</tr>
		<tr>
			<td>Lebenszeichen</td>
			<td>".$data['crew']."</td>
		</tr></table>
	</td>
	<td valign=\"top\">
		<table bgcolor=#262323 cellspacing=1 cellpadding=1>
		<th>Logbuch des Schiffes</th>";
		$result = $ship->get_private_logbook($_GET['t']);
		if (mysql_num_rows($result) == 0) echo "<tr><td>Es befinden sich keine Einträge im Computerlogbuch des Schiffes</td></tr>";
		else
		{
			while($dat = mysql_fetch_assoc($result))
			{
				echo "<tr><td>Eintrag vom ".date("d.m.",$dat['date_tsp']).setyear(date("Y",$dat['date_tsp'])).date(" H:i",$dat['date_tsp'])."</td></tr>
				<tr><td>".nl2br(stripslashes($dat['text']))."</td></tr>";
			}
		}
		echo "</table>
	</td>
	</tr>
	</table>";
}
if ($v == "cscan")
{
	pageheader("/ <a href=?p=ship>Schiffe</a> / <a href=?p=stat&s=ss&id=".$_GET["id"].">".stripslashes($ship->name)."</a> / <b>Scanergebnisse ".stripslashes($data['name'])."</b>");
	if (!check_int($_GET["t"])) die(show_error(902));
	if ($ship->nbs == 0)
	{
		meldung("Zum scannen werden aktivierte Nahbereichssensoren benötigt");
		exit;
	}
	if ($ship->eps == 0)
	{
		meldung("Zum scannen wird 1 Energie benötigt");
		exit;
	}
	$db->query("UPDATE stu_ships SET eps=eps-1 WHERE id=".$ship->id." LIMIT 1");
	$ship->eps -= 1;
	$data = $db->query("SELECT id,sx,sy,systems_id FROM stu_colonies WHERE id=".$_GET['t']." LIMIT 1",4);
	if (!$data || $data == 0) exit;
	if (checksector($data) == 0) exit;
	include_once("class/colony.class.php");
	$col = new colony;
	echo "<table><tr><td>";
	$col->rendercolony($_GET["t"],2);
	echo "</td></tr></table>";
}
if ($v == "showtrade")
{
	pageheader("/ <a href=?p=ship>Schiffe</a> / <a href=?p=stat&s=ss&id=".$_GET["id"].">".stripslashes($ship->name)."</a> / <b>Warenangebote der Kolonie</b>");
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=200>
	<th>Angebotene Waren</th><tr><td>";
	while($data=mysql_fetch_assoc($ship->result))
	{
		if (!$lm && $data[mode] == 2)
		{
			$lm = 1;
			echo "</td></tr><th>Gesuchte Waren</th><tr><td>";
		}
		echo "<img src=".$gfx."/goods/".$data[goods_id].".gif title=\"".$data['name']."\">&nbsp;";
	}
	echo "</td></tr></table>";
}
if ($v == "selbstzerst")
{
	pageheader("/ <a href=?p=ship>Schiffe</a> / <a href=?p=stat&s=ss&id=".$_GET["id"].">".stripslashes($ship->name)."</a> / <b>Selbstzerstörung</b>");
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=400>
	<tr><td>Bitte den Bestätigungscode in das Feld eintippen und bestätigen:<br>
	<font color=#ff0000>".$_SESSION['szcode']."</font></td></tr>
	<form action=main.php method=get><input type=hidden name=p value=ship><input type=hidden name=id value=".$_GET['id'].">
	<tr><td><input type=text size=6 class=text name=sc> <input type=submit value=Bestätigung class=button></td></tr></form>
	</table>";
}
if ($v == "sectorscan")
{
	pageheader("/ <a href=?p=ship>Schiffe</a> / <a href=?p=stat&s=ss&id=".$_GET["id"].">".stripslashes($ship->name)."</a> / <b>Sectorscan</b>");
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1>
	<th>Ergebnisse des Sektorscans von ".($ship->systems_id > 0 ? $ship->sx."|".$ship->sy : $ship->cx."|".$ship->cy)."</th>
	<tr><td>Schiffssignaturen geortet: ".mysql_num_rows($result[ss])."<br>
	Getarnte Schiffe geortet: ".(mysql_num_rows($result[sc]) > 0 ? "Ja" : "Nein")."<br><br>
	<a href=?p=stat&s=scs&id=".$_GET["id"]." ".getonm("scs","buttons/lupe")."><img src=".$gfx."/buttons/lupe1.gif name=scs border=0 title=\"Scan wiederholen\"> Scan wiederholen</a></td></tr>
	</table><br>
	<table><tr><td claign=top>
	<table bgcolor=#262323 cellspacing=1 cellpadding=1>
	<th colspan=2>Schiffssignaturen</th>
	<tr><td>Typ</td><td>Datum</td></tr>";
	if (mysql_num_rows($result[ss]) == 0) echo "<tr><td colspan=2>Keine Signaturen vorhanden</td></tr>";
	else
	{
		while($data=mysql_fetch_assoc($result[ss])) echo "<tr><td><img src=".$gfx."/ships/".$data[rumps_id].".gif></td><td>".date("d.m.Y H:i",$data[date_tsp])."</td></tr>";
	}
	unset($data);
	echo "</table></td>
	<td valign=top>
	<table bgcolor=#262323 cellspacing=1 cellpadding=1>
	<th colspan=4>Getarnte Schiffe</th>
	<tr><td>Typ</td><td>Name</td><td>Besitzer</td><td></td></tr>";
	if (mysql_num_rows($result[sc]) == 0 || !$result[sc]) echo "<tr><td colspan=4>Keine getarnten Schiffe vorhanden</td></tr>";
	else
	{
		while($data=mysql_fetch_assoc($result[sc]))
		{
			if ($data[cloak_val] > $ship->sensor_val) $ch = 5;
			else $ch = ($ship->sensor_val-$data[cloak_val])+5;
			if (rand(1,100) > $ch && $data[mode] != 1 && $data[type] != 4 && !$data[dcship_id]) continue;
			if ($data[mode] != 1 && $data[type] != 4 && !$data[dcship_id]) $ship->setdecloaked($data['id'],$data['user_id'],$data['name']);
			echo "<tr><td><img src=".$gfx."/ships/".$data[rumps_id].".gif></td><td>".stripslashes($data['name'])." (".$data['id'].")</td><td>".stripslashes($data['user'])." (".$data['user_id'].")</td><td><a href=?p=comm&s=nn&recipient=".$data['user_id']." ".getonm("pm".$data['id'],"buttons/msg")."><img src=".$gfx."/buttons/msg1.gif border=0 name=pm".$data['id']." title=\"Nachricht an ".ftit($data[user])." senden\"></a></td></tr>";
			$i++;
		}
		if (!$i) echo "<tr><td colspan=4>Der Scan ergab keine Ergebnisse</td></tr>";
	}
	echo "</table></td>
	</tr></table>";
}
if ($v == "sensordata")
{
	pageheader("/ <a href=?p=ship>Schiffe</a> / <a href=?p=stat&s=ss&id=".$_GET['id'].">".stripslashes($ship->name)."</a> / <b>Sensordaten</b> (<a href=?p=stat&s=sda&id=".$_GET['id'].">KSS</a> / <a href=?p=stat&s=sda&id=".$_GET['id']."&m=l>LSS</a>)");
	if ($ship->lss == 0)
	{
		meldung("Die Kurzstreckensensoren sind nicht aktiviert");
		exit;
	}
	echo "<script language=\"Javascript\">
	function getsekinfo(sx,sy,scount)
	{
		return overlib('Signaturen in Sektor ' + sx + '|' + sy + ': ' + scount + '', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER);
	}
	function get_kss_sek_details(sx,sy)
	{
		elt = 'sdat';
		sendRequest('backend/ship/kss_sensordata.php?PHPSESSID=".session_id()."&id=".$ship->id."&sx=' + sx + '&sy=' + sy);
		return overlib('<div id=sdat></div>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER, STICKY, CENTER, WIDTH, 500, CLOSECLICK);
	}
	function get_lss_sek_details(sx,sy)
	{
		elt = 'sdat';
		sendRequest('backend/ship/lss_sensordata.php?PHPSESSID=".session_id()."&id=".$ship->id."&cx=' + sx + '&cy=' + sy);
		return overlib('<div id=sdat></div>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER, STICKY, CENTER, WIDTH, 500, CLOSECLICK);
	}
	</script>";
	if ($_GET['m'] == "l")
	{
		$res = $ship->get_lss_sensordata();
		if (mysql_num_rows($res) == 0) meldung("Es wurden keine Durchflüge registriert");
		else
		{
			echo "<table><th></th>";
			for($i=($ship->cx-$ship->lss_range < 1 ? 1 : $ship->cx-$ship->lss_range);$i<=($ship->cx+$ship->lss_range > $mapfields['max_x'] ? $mapfields['max_x'] : $ship->cx+$ship->lss_range);$i++) echo "<th>".$i."</th>";
			$result = $ship->get_lss_enemy_sensordata();
			while($data=mysql_fetch_assoc($result))
			{
				if ($data['cy'] != $lsy)
				{
					echo "</tr><tr><th>".$data['cy']."</th>";
					$lsy = $data['cy'];
				}
				if ($data['faction_id'] > 0 && $data['is_border'] == 1) $border = " bordercolor=".$data['color']." style='text-align:center; border: 1px outset ".$data['color']."'";
				echo "<td class=l background=".$gfx."/map/".$data['type'].".gif".$border." style=\"text-align: center;\" onmouseover=\"getsekinfo('".$data['cx']."','".$data['cy']."','".$data['sc']."');\" onmouseout=\"nd();\" onClick=\"get_lss_sek_details('".$data['cx']."','".$data['cy']."')\">".(!$data['sc'] ? "&nbsp;" : $data['sc'])."</td>";
				$border = "";
			}
			echo "</tr></table><br><table class=tcal><th></th><th>Siedler</th><th>Letzter Kontakt</th><th>Sektor</th><th>Status</th>";
			while($data=mysql_fetch_assoc($res))
			{
				$det = $ship->get_lss_sensor_detail($data['ships_id']);
				echo "<tr><td><img src=".$gfx."/ships/".$data['rumps_id'].".gif></td>
				<td>".stripslashes($data['user'])." (".$data['user_id'].")</td><td>".date("d.m.Y H:i",$det['date_tsp'])."</td><td>".$det['cx']."|".$det['cy']."</td><td>".getuserrelationship($data)."</td></tr>";
			}
			echo "</table><br><br><table><tr><td></td>";
		}
	}
	else
	{
		$res = $ship->get_kss_sensordata();
		if (mysql_num_rows($res) == 0) meldung("Es wurden keine Durchflüge registriert");
		else
		{
			echo "<table><th></th>";
			$sys = $ship->m->getsystembyid($ship->systems_id);
			for($i=($ship->sx-$ship->lss_range < 1 ? 1 : $ship->sx-$ship->lss_range);$i<=($ship->sx+$ship->lss_range > $sys['sr'] ? $sys['sr'] : $ship->sx+$ship->lss_range);$i++) echo "<th>".$i."</th>";
			$result = $ship->get_kss_enemy_sensordata();
			while($data=mysql_fetch_assoc($result))
			{
				if ($data['sy'] != $lsy)
				{
					echo "</tr><tr><th>".$data['sy']."</th>";
					$lsy = $data['sy'];
				}
				echo "<td class=l background=".$gfx."/map/".$data['type'].".gif style=\"text-align: center;\" onmouseover=\"getsekinfo('".$data[sx]."','".$data[sy]."','".$data['sc']."');\" onmouseout=\"nd();\" onClick=\"get_kss_sek_details('".$data['sx']."','".$data['sy']."')\">".(!$data['sc'] ? "&nbsp;" : $data['sc'])."</td>";
			}
			echo "</tr></table><br><table class=tcal><th></th><th>Siedler</th><th>Letzter Kontakt</th><th>Sektor</th><th>Status</th>";
			while($data=mysql_fetch_assoc($res))
			{
				$det = $ship->get_kss_sensor_detail($data['ships_id']);
				echo "<tr><td><img src=".$gfx."/ships/".$data['rumps_id'].".gif></td>
				<td>".stripslashes($data['user'])." (".$data['user_id'].")</td><td>".date("d.m.Y H:i",$det['date_tsp'])."</td><td>".($ship->systems_id > 0 ? $det['sx']."|".$det['sy'] : $det['cx']."|".$det['cy'])."</td><td>".getuserrelationship($data)."</td></tr>";
			}
			echo "</table><br><br><table><tr><td></td>";
		}
	}
}
if ($v == "stationsbau")
{
	// Ausgelagert nach inc
	include_once("inc/stationbuild.inc.php");
}
if ($v == "wartung")
{
	pageheader("/ <a href=?p=ship>Schiffe</a> / <a href=?p=stat&s=ss&id=".$_GET['id'].">".stripslashes($ship->name)."</a> / <b>Wartung</b>");
	if ($_GET['a'] == "war" && check_int($_GET['t'])) $result = $ship->maintain_ship($_GET['t']);
	if ($result) meldung($result);
	$result = $ship->get_maintainance_ships();
	if (mysql_num_rows($result) == 0) meldung("Es sind keine Schiffe am Versorgungsposten angedockt");
	else
	{
		echo "<table class=Tcal>
		<th></th><th>Name</th><th>Siedler</th><th>Wartung</th><th>Kosten</th><th></th>";
		while($data=mysql_fetch_assoc($result))
		{
			$i = 0;
			echo "<tr><td><img src=".$gfx."/ships/".vdam($data).$data['rumps_id'].".gif title=\"".ftit($data['rname'])."\"></td>
				<td>".stripslashes($data['name'])." (".$data['id'].")</td><td>".stripslashes($data['user'])." (".$data['user_id'].") <a href=?p=comm&s=nn&recipient=".$data['user_id']." ".getonm('pm'.$data['id'],"buttons/msg")."><img src=".$gfx."/buttons/msg1.gif border=0 name=pm".$data[id]." title=\"PM an ".ftit($data['user'])." schreiben\"></a></td>
				<td>Fällig: ".($data['lastmaintainance'] + $data['maintaintime'] < time() ? "<font color=#FF0000>".date("d.m. H:i",($data['lastmaintainance']+$data['maintaintime']))."</font>" : date("d.m. H:i",($data['lastmaintainance']+$data['maintaintime'])))."<br>Letzte: ".date("d.m. H:i",$data['lastmaintainance'])."</td><td><table cellpadding=2 cellspacing=2><tr>";
			$arr = $ship->get_ship_maintainance_cost($data);
			echo "<td><img src=".$gfx."/buttons/e_trans2.gif title=\"Energie\"> ".round($data['eps_cost']/4)."/".($ship->eps < round($data['eps_cost']/4) ? "<font color=#FF0000>".$ship->eps."</font>" : $ship->eps)."</td>";
			$i++;
			while($value = mysql_fetch_assoc($arr))
			{
				if (!$value['gcount']) continue;
				if ($i%5==0) echo "</tr><tr>";
			 	echo "<td><img src=".$gfx."/goods/".$value['goods_id'].".gif title=\"".ftit($value['name'])."\"> ".$value['gcount']."/".(!$value['vcount'] ? "<font color=#FF0000>0</font>" : ($value['vcount'] < $value['gcount'] ? "<font color=#FF0000>".$value['vcount']."</font>" : $value[vcount]))."</td>";
				$i++;
			}
			echo "</tr></table></td><td><a href=?p=stat&s=war&id=".$_GET['id']."&a=war&t=".$data['id']." ".getonm('rep'.$data['id'],'buttons/rep')."><img src=".$gfx."/buttons/rep1.gif name=rep".$data['id']." border=0 title=\"Schiff warten\"></a></td></tr>"; 
		}
		echo "</table>";
	}
}
?>
