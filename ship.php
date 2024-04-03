<?php


if (!is_object($db)) exit;
include_once("class/map.class.php");
$map = new map;
include_once("class/ship.class.php");
$ship = new ship;
include_once("class/fleet.class.php");
$fleet = new fleet;
switch($_GET['s'])
{
	default:
		$v = "main";
	case "ma":
		$v = "main";
		break;
	case "ss":
		$v = "showship";
		break;
	case "be":
		$v = "beam";
		break;
	case "bec":
		$v = "beamc";
		break;
	case "bewb":
		$v = "beamwb";
		break;		
	case "bes":
		$v = "beams";
		break;
	case "sc":
		$v = "scan";
		break;
	case "csc":
		$v = "cscan";
		break;
	case "cac":
		$v = "caction";
		$scanned=0;
		break;
	case "cacs":
		$v = "caction";
		$scanned=1;
		break;
	case "col":
		$v = "colonize";
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
		if ($ship->map['sensoroff'] == 1 || $ship->map['type'] == 8)
		{
			if ($ship->is_shuttle == 0)
			{
				$v = "showship";
				$sresult = "Dieser Sektor kann nicht gescant werden";
				break;
			}
			else
			{
				$sresult = $ship->nebula_scan();
				$v = "showship";
				break;
			}
		}
		$result = $ship->sectorscan();
		if (!is_array($result))
		{
			if ($ship->hud == 1) $v = "showship";
			if ($ship->hud == 2) $v = "showshipfight";
			$sresult = $result;
			break;
		}
		$v = "sectorscan";
		break;
	case "las":
		if ($ship->is_shuttle != 1) die(show_error(902));
		$v = "landshuttle";
		break;
}
if ($v == "main")
{
	if ($_GET['a'] == "nf" && $_GET['fs'] > 0 && check_int($_GET['fs'])) $result = $fleet->newfleet($_GET['fs']);
	if ($_GET['a'] == "atf" && $_GET['id'] > 0 && check_int($_GET['id'])) $result = $fleet->addtofleet($_GET['id'],$_GET['fid']);
	if ($_GET['a'] == "lf" && $_GET['id'] > 0 && check_int($_GET['id'])) $result = $fleet->leavefleet($_GET['id']);
	if ($_GET['a'] == "df" && $_GET['id'] > 0 && check_int($_GET['id'])) $result = $fleet->delfleet($_GET['id']);
	if ($_GET['a'] == "rf" && $_GET['fid'] > 0 && check_int($_GET['fid']) && $_GET['name']) $result = $fleet->renamefleet($_GET['fid'],$_GET['name']);
	if ($_GET['a'] == "chso" && $_GET['sm'] && $_GET['sw']) $result = $ship->changesorting($_GET['sm'],$_GET['sw']);
	if ($_GET['a'] == "cd" && check_int($_GET['m']) && ((check_int($_GET['c']) && $_GET['c'] > 0) || $_GET['c'] == "max") && !$_GET['fld']) $result = $ship->bussard($_GET['id'],$_GET['m'],$_GET['c']);
	if ($_GET['a'] == "ce" && ((check_int($_GET['c']) && $_GET['c'] > 0) || $_GET['c'] == "max") && !$_GET['fld']) $result = $ship->collect($_GET['id'],$_GET['c']);
	if ($_GET['a'] == "swts" && check_int($_GET['id'])) $result = $ship->switchtoship($_GET['id']);
	if ($_GET['a'] == "swtst" && check_int($_GET['id'])) $result = $ship->switchtostation($_GET['id']);
	if ($_GET['a'] == "assf" && $_GET['sid'] > 0 && check_int($_GET['sid'])  && $_GET['id'] > 0 && check_int($_GET['id'])) $result = $ship->transferfreighter($_GET['id'],$_GET['sid']);
	
	if ($_GET['a'] == "joinconflict" && $_GET['id'] > 0 && check_int($_GET['id'])) $result = $fleet->joinconflict($_GET['id']);
	if ($_GET['a'] == "leaveconflict" && $_GET['id'] > 0 && check_int($_GET['id'])) $result = $fleet->leaveconflict($_GET['id']);
	if ($_GET['a'] == "cancelconflict" && $_GET['id'] > 0 && check_int($_GET['id'])) $result = $fleet->cancelconflict($_GET['id']);
	
	
	if ($_SESSION['szcode'] && $_GET['sc']) $result = $ship->selfdestruct($_GET['id'],$_GET['sc']);
	echo "<script language=\"Javascript\">
	function getshipinfo(id)
	{
		elt = 'shinfo';
		openJsWin(elt,600,200,150);
		sendRequest('backend/shinfo.php?PHPSESSID=".session_id()."&id=' + id + '');
	}
function getPos(el) {
    // yay readability
    for (var lx=0, ly=0;
         el != null;
         lx += el.offsetLeft, ly += el.offsetTop, el = el.offsetParent);
    return {x: lx,y: ly};
}

function showInfo(el,text) {
	var bodyRect = document.body.getBoundingClientRect();
    elemRect = el.getBoundingClientRect();
	
	var w=window,d=document,e=d.documentElement,g=d.getElementsByTagName('body')[0],wx=w.innerWidth||e.clientWidth||g.clientWidth,wy=w.innerHeight||e.clientHeight||g.clientHeight;

    y   = Math.min(elemRect.top - bodyRect.top, wy - 250);	
	x   = elemRect.left - bodyRect.left;
	
	document.getElementById('infodiv').innerHTML = text;
	positionElement(el,document.getElementById('infodiv'),document.getElementById('infodiv').offsetHeight);
}	
	
	</script>";
	pageheader("/ <b>Schiffe</b>");
	if (is_string($result)) meldung($result);
	
			$t = time();
	// $crewmax = (100+floor($db->query("SELECT SUM(bev_free+bev_work) FROM stu_colonies WHERE user_id=".$_SESSION['uid'],1)/10));
	
	
	$pc = $ship->getAllPoints("pcrew");
	$pv = $ship->getAllPoints("psupply");
	$pw = $ship->getAllPoints("pmaintain");
	
	$cf = $ship->getCurrentFleetPoints();
	$cc = $ship->getCurrentCivilianCount();
	
	$stepFleetPoints = stepFleetLimit($pc,$pv,$pw);
	
	$fleetdata = "<table cellspacing=\"0\" cellpadding=\"0\" border=0 style=\"width:100%;border-style: none;\"><tr style=\"height:25px;\"><td colspan=2><b>Flottenstufe ".$stepFleetPoints['level'].":</b></td></tr>";
	$fleetdata .= "<tr><td><img src=".$gfx."/icons/fleet.gif border=0 title='Flottenpunkte'> Flottenpunkte:</td><td style=\"text-align:center;\">".(($cf <=  $stepFleetPoints['battleships']) ? "<font color='green'>" : "<font color='red'>").$cf."/".$stepFleetPoints['battleships']."</font></td></tr>";
	$fleetdata .= "<tr><td><img src=".$gfx."/icons/storage.gif border=0 title='Zivile Schiffe'> Zivile Schiffe:</td><td style=\"text-align:center;\">".(($cc <=  $stepFleetPoints['civilianships']) ? "<font color='green'>" : "<font color='red'>").$cc."/".$stepFleetPoints['civilianships']."</font></td></tr>";
	
	
	$fleetdata .= "<tr style=\"height:25px;\"><td colspan=2><b>Für Stufe ".($stepFleetPoints['level']+1).":</b></td></tr>";
	$fleetdata .= "<tr><td><img src=".$gfx."/icons/pcrew.gif border=0 title='Crewpunkte'> Crewpunkte:</td><td style=\"text-align:center;\">".(($pc >=  $stepFleetPoints['step']) ? "<font color='green'>" : "<font color='yellow'>").$pc."/".$stepFleetPoints['step']."</font></td></tr>";
	$fleetdata .= "<tr><td><img src=".$gfx."/icons/pmaintain.gif border=0 title='Wartungspunkte'> Wartungspunkte:</td><td style=\"text-align:center;\">".(($pw >=  $stepFleetPoints['step']) ? "<font color='green'>" : "<font color='yellow'>").$pw."/".$stepFleetPoints['step']."</font></td></tr>";
	$fleetdata .= "<tr><td><img src=".$gfx."/icons/psupply.gif border=0 title='Versorgungspunkte'> Versorgungspunkte:</td><td style=\"text-align:center;\">".(($pv >=  $stepFleetPoints['step']) ? "<font color='green'>" : "<font color='yellow'>").$pv."/".$stepFleetPoints['step']."</font></td></tr>";
	
	
	$fleetdata .= "</table>";
	
	$pointmax = $ship->getAllFleetPoints();
	
	$points = $db->query("SELECT SUM(b.fleetpoints) FROM stu_ships AS a LEFT JOIN stu_rumps AS b ON a.rumps_id = b.rumps_id WHERE a.user_id=".$_SESSION['uid'],1);
	$pts = "0";
	$pts = "<font color=#".fractionalColor(min($points/$pointmax,1)).">".$points." / ".$pointmax."</font>";
	// if ($points > 0) $crews = "<font color=green>".$points." / ".$crewmax."</font>";
	// if (($points > 0) && ($points >= $crewmax)) $crews = "<font color=red>".$points." / ".$crewmax."</font>";
	
	// $crewpic = "<img src=".$gfx."/icons/crew".$_SESSION[race].".gif border=0 title='Schiffscrews'>";
	// $crewpic = "<img src=".$gfx."/bev/crew/".$_SESSION[race]."m.png width=35 title='Crew'><img src=".$gfx."/bev/crew/".$_SESSION[race]."f.png width=35 title='Crew'>";
	$crewpic = "";
	
	$edemand = $db->query("SELECT SUM(c.eps_drain) FROM stu_ships as s LEFT JOIN stu_rumps as c ON s.rumps_id = c.rumps_id WHERE s.user_id=".$_SESSION['uid'],1);
	
	$wkloads = ceil($edemand/50);
	$gdemand = "";
	$gdemand .= "&nbsp;<img src=".$gfx."/goods/8.gif border=0 title='Dilithium'> ".$wkloads;
	$gdemand .= "&nbsp;<img src=".$gfx."/goods/5.gif border=0 title='Deuterium'> ".(2*$wkloads);
	$gdemand .= "&nbsp;<img src=".$gfx."/goods/6.gif border=0 title='Antimaterie'> ".(2*$wkloads);
	

	// $fleetdata = "20 + Minimum von ".$pc." ".$pm." ".$ps."&nbsp;<br><img src=".$gfx."/icons/fleet.gif border=0 title='Flottenpunkte'> ".$pts;
	$demanddata = "<img src=".$gfx."/icons/energy.gif border=0 title='Grund-Verbrauch'> ".$edemand." = ".$gdemand;
	
	function sortingLine() {
		global $gfx;
		
		return "<tr>
		<td style=\"text-align:center;width:150px;\"><a href=?p=ship&a=chso&sm=ru&sw=up><img src=".$gfx."/buttons/pup.gif border=0 title=\"Nach Rumpf aufsteigend sortieren\"></a> Klasse <a href=?p=ship&a=chso&sm=ru&sw=do><img src=".$gfx."/buttons/pdown.gif border=0 title=\"Nach Rumpf absteigend sortieren\"></a></td>
		<td>Name</td>
		<td style=\"text-align:center;width:120px;\">x|y</td>
		<td style=\"text-align:center;width:140px;\"><a href=?p=ship&a=chso&sm=hu&sw=up><img src=".$gfx."/buttons/pup.gif border=0 title=\"Nach Hüllenwert aufsteigend sortieren\"></a> Hülle <a href=?p=ship&a=chso&sm=hu&sw=do><img src=".$gfx."/buttons/pdown.gif border=0 title=\"Nach Hüllenwert absteigend sortieren\"></a></td>
		<td style=\"text-align:center;width:140px;\"><a href=?p=ship&a=chso&sm=sh&sw=up><img src=".$gfx."/buttons/pup.gif border=0 title=\"Nach Schildwert aufsteigend sortieren\"></a> Schilde <a href=?p=ship&a=chso&sm=sh&sw=do><img src=".$gfx."/buttons/pdown.gif border=0 title=\"Nach Schildwert absteigend sortieren\"></a></td>
		
		<td style=\"text-align:center;width:140px;\"><a href=?p=ship&a=chso&sm=ep&sw=up><img src=".$gfx."/buttons/pup.gif border=0 title=\"Nach Energie aufsteigend sortieren\"></a> Energie <a href=?p=ship&a=chso&sm=ep&sw=do><img src=".$gfx."/buttons/pdown.gif border=0 title=\"Nach Energie absteigend sortieren\"></a></td>
		<td style=\"text-align:center;width:140px;\">Warpantrieb</td>
		<td style=\"text-align:center;width:140px;\">Warpkern</td>
		<td style=\"text-align:center;width:140px;\"><a href=?p=ship&a=chso&sm=cr&sw=up><img src=".$gfx."/buttons/pup.gif border=0 title=\"Nach Crew aufsteigend sortieren\"></a> Crew <a href=?p=ship&a=chso&sm=cr&sw=do><img src=".$gfx."/buttons/pdown.gif border=0 title=\"Nach Crew absteigend sortieren\"></a></td>
		<td title=\"Aktivierte Sensoren: NBS|LSS\" style=\"text-align:center;width:40px;\">Sen</td>
		
		<td style=\"text-align:center;width:16px;\" title=\"Alarmstatus des Schiffes\">A</td>
		<td style=\"text-align:center;width:60px;\"><img src=".$gfx."/buttons/icon/torpedo.gif title=\"Anzeige des geladenen Torpedo-Typs\"></td>
		<td style=\"text-align:center;width:16px;\"></td></tr>";		
	}
	
	$shipstring = "";
	
	$slist = $ship->getshiplist();
	// $aslist = $ship->getashiplist();
	$timestamp = time();
	if (mysql_num_rows($slist) == 0) meldung("Keine Schiffe vorhanden");
	else
	{
		
		while($data=mysql_fetch_assoc($slist))
		{
			if ($data['maintaintime'] != 0 && $_SESSION['uid'] > 100)
			{
				if ($data['lastmaintainance']+$data['maintaintime'] > $timestamp+86400 && $data['lastmaintainance']+$data['maintaintime'] <= $timestamp+432000) $sco = "Yellow";
				elseif ($data['lastmaintainance']+$data['maintaintime'] <= $timestamp+86400) $sco = "#FF0000";
				else $sco = "Green";
			}
			else $sco = "#000000";
			$vb = 0;
			if ($lf != $data['fleets_id'] && $data['fleets_id'] != 0)
			{
				if ($lf > 0) $shipstring .= "</table>";
				$lf = $data['fleets_id'];
				$actiondata = $ship->getfleetactionstring($data['fleets_id']);
				if ($actiondata != 0) {
					$fadd = "<img src=".$gfx."/planets/".$actiondata['coltype'].".gif title=Kolonieaktion border=0>";
					if ($actiondata['code'] == 1) $fadd .= "<img src=".$gfx."/buttons/guard2.gif title=Verteidigung border=0> ";
					elseif ($actiondata['code'] == 2) $fadd .= "<img src=".$gfx."/buttons/x1.gif title=Blockade border=0> ";
					else $fadd .= "<img src=".$gfx."/buttons/leavecol2.gif title=Angriff border=0> ";
					$fadd .= $actiondata['text'];
					$fadd = "<tr><td colspan=14>".$fadd."</td></tr>";
				}
				else $fadd = "";
				$fdata = $db->query("SELECT * FROM stu_fleets WHERE fleets_id=".$lf." LIMIT 1",4);
				$shipstring .= "";

				
				if ($fdata['faction'] == 0) {
					$shipstring .= "<table class=\"suppressMenuColors race".$fdata['faction']."\">";
					$shipstring .= "<tr>";
					$shipstring .= "<th colspan=2 style=\"height:32px;vertical-align:middle;\"><table cellspacing=0 cellpadding=0><tr><th style=\"height:32px;vertical-align:middle;\"></th><th width=265 style=\"vertical-align:middle;\">&nbsp;<a href=?p=ship&s=ss&id=".$fdata['ships_id'].">".stripslashes($fdata['name'])."</a> (".$fleet->getTotalPoints($data['fleets_id'])."/60)</th></table></th>";	
					$shipstring .= "<form action=main.php method=get><th colspan=3 style=\"height:32px;text-align:center;vertical-align:middle;\"><input type=hidden name=p value=ship><input type=hidden name=fid value=".$data['fleets_id']."><input type=hidden name=a value=rf><input type=text class=race".$fdata['faction']." size=40 name=name value=\"".stripslashes($fdata['name'])."\"  style=\"height:22px;\">&nbsp;<input type=submit value=umbenennen style=\"height:22px;\"></th></form>";	
					$shipstring .= "<form action=main.php method=get><th colspan=3 style=\"height:32px;text-align:center;vertical-align:middle;\"><input type=hidden name=p value=ship><input type=hidden name=a value=atf><input type=hidden name=fid value=".$data['fleets_id']."><select name=id>".$fleet->getjoinlist($data,$fdata['ships_id'])."</select> <input type=submit value=Hinzufügen  style=\"height:22px;\"></th></form>";	

					if ($fdata['faction_change_time'] > 0) {
						$shipstring .= "<th colspan=6 style=\"height:32px;text-align:center;vertical-align:middle;\"><a href=?p=ship&a=cancelconflict&id=".$fdata['fleets_id']." style=\"font-weight: normal;\" ".getHover("rconf".$i,"active/n/exclamation","hover/r/exclamation")."><img src=".$gfx."/buttons/active/n/exclamation.gif border=0 title='Änderung abbrechen' name=rconf".$i."> Änderung abbrechen</a></th>";	
					} else {
						$shipstring .= "<th colspan=6 style=\"height:32px;text-align:center;vertical-align:middle;\"><a href=?p=ship&a=joinconflict&id=".$fdata['fleets_id']." style=\"font-weight: normal;\" ".getHover("rconf".$i,"active/n/r".$_SESSION['race'],"hover/g/r".$_SESSION['race'])."><img src=".$gfx."/buttons/active/n/r".$_SESSION['race'].".gif border=0 title='Flotte bereitstellen' name=rconf".$i."> Flotte bereitstellen</a></th>";	
					}
					
					$shipstring .= "</tr>";													
				} else {
					$shipstring .= "<table class=\"suppressMenuColors race".$fdata['faction']."\">";
					$shipstring .= "<tr>";
					$shipstring .= "<th colspan=2 style=\"height:32px;vertical-align:middle;\"><table cellspacing=0 cellpadding=0><tr><th width=35 style=\"height:32px;vertical-align:middle;\"><img src=".$gfx."/rassen/".$fdata['faction']."kn.png width=32 height=32></th><th width=100% style=\"vertical-align:middle;\">&nbsp;<a href=?p=ship&s=ss&id=".$fdata['ships_id'].">".stripslashes($fdata['name'])."</a> (".$fleet->getTotalPoints($data['fleets_id'])."/60)</th></table></th>";	
					$shipstring .= "<th colspan=3 style=\"height:32px;text-align:center;vertical-align:middle;\"></th>";	
					$shipstring .= "<th colspan=3 style=\"height:32px;text-align:center;vertical-align:middle;\"></th>";	
					
					if ($fdata['faction_change_time'] > 0) {
						$shipstring .= "<th colspan=6 style=\"height:32px;text-align:center;vertical-align:middle;\"><a href=?p=ship&a=cancelconflict&id=".$fdata['fleets_id']." style=\"font-weight: normal;\" ".getHover("rconf".$i,"active/w/exclamation","hover/r/exclamation")."><img src=".$gfx."/buttons/active/w/exclamation.gif border=0 title='Änderung abbrechen' name=rconf".$i."> Änderung abbrechen</a></th>";	
					} else {
						$shipstring .= "<th colspan=6 style=\"height:32px;text-align:center;vertical-align:middle;\"><a href=?p=ship&a=leaveconflict&id=".$fdata['fleets_id']." style=\"font-weight: normal;\" ".getHover("rconf".$i,"active/w/r".$_SESSION['race'],"hover/r/r".$_SESSION['race'])."><img src=".$gfx."/buttons/active/w/r".$_SESSION['race'].".gif border=0 title='Bereitstellung aufheben' name=rconf".$i."> Bereitstellung aufheben</a></th>";	
						// $shipstring .= "<th colspan=6 style=\"height:32px;text-align:center;vertical-align:middle;\"></th>";	
					}
					
					$shipstring .= "</tr>";						
				}
				$shipstring .= "</th></tr>";
				$shipstring .= $fadd;
				
				$shipstring .= sortingLine();

			}

			if ($lf != $data['fleets_id'] && $data['fleets_id'] == 0 && $data['slots'] == 0)
			{
				if ($lf > 0) $shipstring .= "</table>";
				$shipstring .= "<table class=\"suppressMenuColors black\">";
				$shipstring .= sortingLine();				
				$lf = $data['fleets_id'];
			}

			
			$i++;
			if ($data['m5'] == 0 || $data['warpcore'] == 0)
			{
				$deut = $db->query("SELECT count FROM stu_ships_storage WHERE goods_id=5 AND ships_id=".$data['id']." LIMIT 1",1);
				$rr = @floor($deut/$data['creaktor']);
				if ($deut == 0) $data['creaktor'] = 0;
				$reaktor = $data['creaktor'];
			}
			else
			{
				$rr = round($data['warpcore']/$data['rreaktor']);
				$reaktor = $data['rreaktor'];
			}
			if ($data['lss'] == 1) $vb++;
			if ($data['nbs'] == 1) $vb++;
			if ($data['wea_phaser'] == 1) $vb++;
			if ($data['wea_torp'] == 1) $vb++;
			if ($data['schilde_status'] == 1) $vb++;
			if ($data['creplikator'] == 0)
			{
				$rn = @floor($db->query("SELECT count FROM stu_ships_storage WHERE goods_id=1 AND ships_id=".$data['id']." LIMIT 1",1)/ceil($data['crew']/5));
			}
			else
			{
				if ($data['replikator'] == 1 && $data['eps']+($reaktor-$vb) > 0)
				{
					$vb += ceil($data['crew']/5);
					$rn = "*";
				}
				else
				{
					$nahr = $db->query("SELECT count FROM stu_ships_storage WHERE goods_id=1 AND ships_id=".$data['id']." LIMIT 1",1);
					if ($nahr == 0 && $data['eps']+($reaktor-$vb) > 0)
					{
						$vb += ceil($data['crew']/5);
						$rn = "*";
					}
					else
					{
						$cv = ceil($data['crew']/5);
						if ($cv > $nahr) $rn = 0;
						else $rn = @floor($nahr/$cv);
					}
				}
			}

			$tarnkosten = tarnkosten($data['rumps_id']);
			if ($data['cloak'] == 1) {
				$vb += $tarnkosten;
				$reaktor -= $tarnkosten*2;
			}
			$reaktor-$vb > $data['max_eps']-$data['eps'] ? $ee = $data['max_eps']-$data['eps'] : $ee = $reaktor-$vb;
			if ($data['m5'] > 0 && $data['warpcore'] > 0)
			{
				if ($vb+$ee < 1) $rr = "*";
				else $rr = @floor(@$data['warpcore']/($vb+$ee > $data['rreaktor'] ? $data['rreaktor'] : $vb+$ee));
			}
			else
			{
				if ($vb+$ee < 1) $rr = "*";
				else $rr = @floor(@$deut/($vb+$ee > $data['creaktor'] ? $data['creaktor'] : $vb+$ee));
			}
			if ($data['crew'] < $data['min_crew'])
			{
				$reaktor = 0;
				$ee = 0;
				$vb = 0;
				$rn = "-";
				$rr = "<font color=#FF0000>!</font>";
			}
			if ($data['crew'] == 0)
			{
				$rn = "-";
				$rr = "-";
			}
			if (check_int($rr) && $rr < 6 && $rr > 1) $rr = "<font color=Yellow>".$rr."</font>";
			if (check_int($rr) && $rr <= 1) $rr = "<font color=#FF0000>".$rr."</font>";
			
			if (check_int($rn) && $rn < 6 && $rn > 1) $rn = "<font color=Yellow>".$rn."</font>";
			if (check_int($rn) && $rn <= 1) $rn = "<font color=#FF0000>".$rn."</font>";
			
			$data['slots'] > 0 ? $link = "stat" : $link = "ship";
		$shipstring .= "<tr style=\"height:40px;\">";
			
			
			$shippicpathadd = "";
			if ($data['cloak'] == 1) $shippicpathadd = "cloak/";
			
		$shipstring .= "<td style=\"text-align:center;\"><a href=?p=".$link."&s=ss&id=".$data['id']."><img src=".$gfx."/ships/".$shippicpathadd.$data['rumps_id'].".gif title=\"".stripslashes($data['cname'])."\" border=0></a></td>";
			$shipstring .= "<td><a href=?p=".$link."&s=ss&id=".$data['id'].">".stripslashes($data['name'])."</a> (".($data['cloak'] == 1 ? "<font color=gray><a href=\"javascript:void(0);\" onClick=\"getshipinfo(".$data['id'].");\">".$data['id']."</a></font>" : "<a href=\"javascript:void(0);\" onClick=\"getshipinfo(".$data['id'].");\">".$data['id']."</a>").")</td>";
			
			$shipstring .= "<td>".($data['systems_id'] > 0 ? "<img src=".$gfx."/systems/".$data['type']."ms.png width=24 height=24 title=\"".$data['sysname']."-System (".$data['cx']."|".$data['cy'].")\">" : "<img src=".$gfx."/map/0.gif width=24 height=24 title=\"Freier Weltraum\">")."&nbsp;<img src=".$gfx."/map/".$data['cfield'].".gif width=24 height=24> ".($data['systems_id'] > 0 ? "<span style=\"display: inline-block;text-align:center;width:60px;\">".$data['sx']."|".$data['sy']."</span>" : "<span style=\"display: inline-block;text-align:center;width:60px;\">".$data['cx']."|".$data['cy']."</span>")." </td>";

			$hullcolor = "gre";
			if (($data['huelle'] / $data['max_huelle']) < 0.6) $hullcolor = "yel";
			if (($data['huelle'] / $data['max_huelle']) < 0.4) $hullcolor = "org";
			if (($data['huelle'] / $data['max_huelle']) < 0.2) $hullcolor = "red";
			
			if ($data['schilde_status'] == 1) 	$shieldcolor = "cya";
			else								$shieldcolor = "cyd";
	
			if ($data['schilde_status'] == 0 && ($data['schilde'] < $data['max_schilde'])) {
				$plan =  $db->query("SELECT * FROM stu_ships_buildplans WHERE plans_id = ".$data[plans_id]."",4);
				$regsum = $db->query("SELECT SUM(value) FROM stu_modules_special WHERE type = 'shieldreg' AND modules_id IN (".$plan[m1].", ".$plan[m2].", ".$plan[m3].", ".$plan[m4].", ".$plan[m5].", ".$plan[w1].", ".$plan[w2].", ".$plan[s1].", ".$plan[s2].")",1);

				$regsum = min($data['max_schilde'] - $data['schilde'],$regsum);
		
				$regt = max(max(ceil((($data['lasthit'] + 1800) - $t)/60),0), max(ceil((($data['lastshieldreg'] + 600) - $t)/60),0));
			} else $regsum = 0;
		
			if ($data['cloak'] == 1) $regsum = 0;
		
			$overtext = "<div style=\\'background:#000000;border:1px solid #8897cf;padding:15px;\\'><img src=".$gfx."buttons/icon/armor.gif />&nbsp;".$data['huelle']."/".$data['max_huelle']."</div>";
			$shipstring .= "<td><div><p style=\"display: inline;\"><img src=\"".$gfx."/icons/frage.gif\"/ style=\"vertical-align:middle;\" onMouseOver=\"showInfo(this,'".$overtext."');\" onMouseOut=\"hideInfo();\"></p><p style=\"display: inline;vertical-align:middle;\">".darkuniversalstatusbar($data['huelle'],$data['max_huelle'],$hullcolor,0,100)."</p></div></td>";

			$overtext = "<div style=\\'background:#000000;border:1px solid #8897cf;padding:15px;\\'><img src=".$gfx."buttons/icon/shield.gif />&nbsp;".$data['schilde']."/".$data['max_schilde']."".($regsum > 0 ? "<br><img src=".$gfx."buttons/icon/shieldplus.gif />&nbsp;+".$regsum." in ".$regt." min" : "")."<br>".(($data['schilde_status'] == 1) ? "<img src=".$gfx."buttons/icon/yes.gif /> aktiviert" : "<img src=".$gfx."buttons/icon/no.gif /> deaktiviert")."</div>";
			$shipstring .= "<td><div><p style=\"display: inline;\"><img src=\"".$gfx."/icons/frage.gif\"/ style=\"vertical-align:middle;\" onMouseOver=\"showInfo(this,'".$overtext."');\" onMouseOut=\"hideInfo();\"></p><p style=\"display: inline;vertical-align:middle;\">".darkuniversalstatusbar($data['schilde'],$data['max_schilde'],$shieldcolor,$regsum,100)."</p></div></td>";
			
			unset($eplus);
			unset($eminus);
			unset($etemp);
			$shipvals = getShipValuesForBuildplan($data['plans_id']);
			
			// $eplus += min($shipvals['reaktor'],$data['warpcore']);
			
			$estring = "Reaktorleistung:<br>";

			
			$eplus += min($shipvals['reaktor'],$data['warpcore']);
			$estring .= "<img src=".$gfx."buttons/icon/energy.gif />&nbsp;<font color=#00ff00>+".$shipvals['reaktor']."</font> (Warpkern)";

			
			if ($data['cloak'] == 1) {
				$estring .= "<br><img src=".$gfx."buttons/icon/cloak.gif />&nbsp;<font color=#ff0000>-".($shipvals['reaktor']-floor($shipvals['reaktor']*0.7))."</font> (Tarnvorrichtung)";
				$shipvals['reaktor']  = floor($shipvals['reaktor']*0.7);
			}	
			
			if ($data['warpcore']<$shipvals['reaktor']) {
				$draw = $shipvals['reaktor'] - $data[warpcore];
				$estring .= "<br><img src=".$gfx."buttons/icon/warpcore.gif />&nbsp;<font color=#ff0000>-".($draw)."</font> (Fehlende Warpkernladung)";
			}
			
			$estring .= "<br><br>Laufende Kosten:";
			
			if ($data['cloak'] == 1) {
				$etemp = 3;
				$eminus += $etemp;
				$estring .= "<br><img src=".$gfx."buttons/icon/cloak.gif />&nbsp;<font color=#ff0000>-".$etemp."</font> (Tarnvorrichtung)";
				$eplus -= $etemp;
			}	
			
			$etemp = $shipvals['eps_drain'];
			$eminus += $etemp;
			$eplus  -= $etemp;
			$estring .= "<br><img src=".$gfx."buttons/icon/replicator.gif />&nbsp;<font color=#ff0000>-".$etemp."</font> (Lebenserhaltung)";
			
			if ($data['schilde_status'] == 1) {
				$etemp = 1;
				$eminus += $etemp;
				$estring .= "<br><img src=".$gfx."buttons/icon/shield.gif />&nbsp;<font color=#ff0000>-".$etemp."</font> (Schilde)";
				$eplus -= $etemp;
			}		
			if ($data['warp'] == 1) {
				$etemp = 1;
				$eminus += $etemp;
				$estring .= "<br><img src=".$gfx."buttons/icon/warp.gif />&nbsp;<font color=#ff0000>-".$etemp."</font> (Warpantrieb)";
				$eplus -= $etemp;
			}				
			if ($data['nbs'] || $data['lss']) {
				$etemp = $data['nbs'] + $data['lss'];
				$eminus += $etemp;
				$estring .= "<br><img src=".$gfx."buttons/icon/scan.gif />&nbsp;<font color=#ff0000>-".$etemp."</font> (Sensoren)";
				$eplus -= $etemp;
			}

			
			if ($data['wea_phaser']) {
				$etemp = $data['w1_lvl'];
				$eminus += $etemp;
				$estring .= "<br><img src=".$gfx."buttons/icon/phaser.gif />&nbsp;<font color=#ff0000>-".$etemp."</font> (Primärwaffe)";
				$eplus -= $etemp;
			}
			if ($data['wea_torp']) {
				$etemp = $data['w2_lvl'];
				$eminus += $etemp;
				$estring .= "<br><img src=".$gfx."buttons/icon/torpedo.gif />&nbsp;<font color=#ff0000>-".$etemp."</font> (Sekundärwaffe)";
				$eplus -= $etemp;
			}

			
			$estring = "<img src=".$gfx."buttons/icon/energy.gif />&nbsp;".$data['eps']."/".$data['max_eps']."<br><img src=".$gfx."buttons/icon/energyplus.gif />&nbsp;<font color=".($eplus < 0 ? "#ff0000>" : "#00ff00>+").$eplus."</font><br><br>".$estring;
			
			$overtext = "<div style=\\'background:#000000;border:1px solid #8897cf;padding:15px;\\'>".$estring."</div>";
			$shipstring .= "<td><div><p style=\"display: inline;\"><img src=\"".$gfx."/icons/frage.gif\"/ style=\"vertical-align:middle;\" onMouseOver=\"showInfo(this,'".$overtext."');\" onMouseOut=\"hideInfo();\"></p><p style=\"display: inline;vertical-align:middle;\">".darkuniversalstatusbar($data['eps'],$data['max_eps'],'yel',$eplus,100)."</p></div></td>";

			
			$warpplus = min(min($shipvals['warpfield_regen'],max(0,$data['warpcore'] - $shipvals['reaktor'])),$data['max_warpfields'] - $data['warpfields']);
			
			$overtext = "<div style=\\'background:#000000;border:1px solid #8897cf;padding:15px;\\'><img src=".$gfx."buttons/icon/warp.gif />&nbsp;".$data['warpfields']."/".$data['max_warpfields']."<br><img src=".$gfx."buttons/icon/energyplus.gif />&nbsp;<font color=#00ff00>+".$warpplus."</font><br>".(($data['warp'] == 1) ? "<img src=".$gfx."buttons/icon/yes.gif /> aktiviert" : "<img src=".$gfx."buttons/icon/no.gif /> deaktiviert")."</div>";
			$shipstring .= "<td><div><p style=\"display: inline;\"><img src=\"".$gfx."/icons/frage.gif\"/ style=\"vertical-align:middle;\" onMouseOver=\"showInfo(this,'".$overtext."');\" onMouseOut=\"hideInfo();\"></p><p style=\"display: inline;vertical-align:middle;\">".darkuniversalstatusbar($data['warpfields'],$data['max_warpfields'],'whi',$warpplus,100)."</p></div></td>";

			$coredrain_w = 0;
			$coredrain_e = $eminus + min($shipvals['reaktor']-$eminus,$data['max_eps'] - $data['eps']);
			// $coredrain_e = $eminus;
			$warptext = "<br><img src=".$gfx."buttons/icon/energy.gif />&nbsp;<font color=#ff0000>-".$coredrain_e."</font> (Reaktor)";
			
			if ($data['warpcore'] > $coredrain_e) {
				$coredrain_w = min($shipvals['warpfield_regen'],$data['max_warpfields'] - $data['warpfields']);
				$warptext .= "<br><img src=".$gfx."buttons/icon/warp.gif />&nbsp;<font color=#ff0000>-".$coredrain_w."</font> (Warpantrieb)";
			}
			
			$overtext = "<div style=\\'background:#000000;border:1px solid #8897cf;padding:15px;\\'><img src=".$gfx."buttons/icon/warpcore.gif />&nbsp;".$data['warpcore']."/".$data['max_warpcore'].$warptext."</div>";
			$shipstring .= "<td class=suppressMenuColors><div><p style=\"display: inline;\"><img src=\"".$gfx."/icons/frage.gif\"/ style=\"vertical-align:middle;\" onMouseOver=\"showInfo(this,'".$overtext."');\" onMouseOut=\"hideInfo();\"></p><p style=\"display: inline;vertical-align:middle;\">".darkuniversalstatusbar($data['warpcore'],$data['max_warpcore'],'org',0-($coredrain_w+$coredrain_e),100)."</p></div></td>";
			
			
			$shipstring .= "<td class=suppressMenuColors>".$data['crew']."/".$data['max_crew']." (".$data['min_crew'].")</td><td>".($data['nbs'] == 1 ? "<img src=".$gfx."/buttons/icon/yes.gif>" : "<img src=".$gfx."/buttons/icon/no.gif>")."|".($data['lss'] == 1 ? "<img src=".$gfx."/buttons/icon/yes.gif>" : "<img src=".$gfx."/buttons/icon/no.gif>")."</td>";
			
			if ($data['alvl'] == 1)	$shipstring .= "<td><img src=".$gfx."/buttons/icon/green.gif></td>";	
			if ($data['alvl'] == 2)	$shipstring .= "<td><img src=".$gfx."/buttons/icon/yellow.gif></td>";	
			if ($data['alvl'] == 3)	$shipstring .= "<td><img src=".$gfx."/buttons/icon/red.gif></td>";	
			
			$torps = $db->query("SELECT a.*,b.name FROM stu_ships_storage as a LEFT JOIN stu_goods as b ON a.goods_id = b.goods_id WHERE a.ships_id = ".$data['id']." AND a.goods_id IN (81,82,83) ORDER BY a.goods_id DESC LIMIT 1;",4);
			
			$shipstring .= "<td align=center>".($torps['count'] > 0 ? "<img src=".$gfx."/goods/".($torps['goods_id']).".gif title='".$torps['name']."'> ".$torps['count'] : "-")."</td>";

			if ($data['slots'] == 0 && $data['is_shuttle'] == 0 && $data['dock'] == 0)
			{
				if ($data['fleets_id'] == 0) $shipstring .= "<td><a href=?p=ship&a=nf&fs=".$data['id']." ".getHover("nf".$i,"active/n/ships","hover/g/ships")."><img src=".$gfx."/buttons/active/n/ships.gif border=0 title='Neue Flotte gründen' name=nf".$i."></a></td></tr>";
				else
				{
					if ($fdata['faction'] > 0) {
						$shipstring .= "<td style=\"text-align:center;\">-</td>";
					} else {
						if ($fdata['ships_id'] == $data['id']) $shipstring .= "<td><a href=?p=ship&a=df&id=".$data['id']." ".getHover("df".$i,"active/n/ships","hover/r/ships")."><img src=".$gfx."/buttons/active/n/ships.gif border=0 title='Flotte auflösen' name=df".$i."></a></td>";
						else $shipstring .= "<td><a href=?p=ship&a=lf&id=".$data['id']." ".getHover("lf".$i,"active/n/ship","hover/r/ship")."><img src=".$gfx."/buttons/active/n/ship.gif border=0 title='Flotte verlassen' name=lf".$i."></a></td>";
					}
				}
			}
			else $shipstring .= "<td></td>";
			$rn = "";
			$rr = "";
			if ($data['still'] > 0) $shipstring .= "<tr><td colspan=\"14\">&nbsp;&nbsp;&nbsp;<img src=".$gfx."/buttons/icon/map.gif> Kartographierung des ".$data['sysname']."-Systems. Dauert an bis ".date("d.m. H:i",$data['still'])." Uhr</th></tr>";
			// if ($data['maintain'] > 0) $shipstring .= "<tr><td colspan=\"14\"><img src=".$gfx."/buttons/b_to1.gif> Wird gewartet. Dauert an bis ".date("d.m. H:i",$data['maintain'])." Uhr</th></tr>";
			if ($data['schilde_status'] > 1) $shipstring .= "<tr><td colspan=\"14\">&nbsp;&nbsp;&nbsp;<img src=".$gfx."/buttons/icon/shield.gif> Schilde werden depolarisiert. Dauert an bis ".date("d.m. H:i",$data['schilde_status'])." Uhr</th></tr>";
			if (($data['cloak'] > 1) && (time() < $data['cloak'])) $shipstring .= "<tr><td colspan=\"14\">&nbsp;&nbsp;&nbsp;<img src=".$gfx."/buttons/icon/cloak.gif> Tarnung baut Chronitonen ab. Dauert an bis ".date("d.m. H:i",$data['cloak'])." Uhr</th></tr>";

		}

		$shipstring .= "</table>";
		
		
		echo "<table width=100% class=tablelayout><tr>";
		echo "<td  class=tablelayout style=\"width:300px; vertical-align:top;\">";
		echo fixedPanel(4,"Flottenpunkte","slist",$gfx."/buttons/icon/fleet.gif",$fleetdata);		
		echo "</td>";
		echo "<td class=tablelayout style=\"width:300px; vertical-align:top;\">";
		echo fixedPanel(3,"Grundverbrauch","slist",$gfx."/buttons/icon/energysystems.gif",$demanddata."<br>&nbsp;");	
		echo "</td><td></td>";
		echo "</tr><tr>";
		echo "<td class=tablelayout colspan=3>".fixedPanel(2,"Schiffsliste","slist",$gfx."/buttons/icon/ships.gif",$shipstring)."</td>";
		echo "</tr></table>";
		
		
		
		
	}

}
if ($v == "showship")
{
	unset($result);
	if ($ship->destroyed == 1 || $ship->dsships[$_GET['id']] == 1 || $fleet->dsships[$_GET['id']] == 1)
	{
		pageheader("/ <a href=?p=ship>Schiffe</a> / <b>Schiffsdetails</b>");
		meldung("Du bist nicht Besitzer dieses Schiffes");
		exit;
	}
	if ($ship->maintain > 0)
	{
		pageheader("/ <a href=?p=ship>Schiffe</a> / <b>".stripslashes($ship->name)."</b>");
		meldung("Das Schiff wird zur Zeit gewartet - Abschluß: ".date("d.m.Y H:i",$ship->maintain));
		exit;
	}
	if (isset($_GET['a']))
	{
		// neuer Flottenbefehl-Typ
		if (isset($_GET['fleetex'])) $fleetex = $_GET['fleetex'];
		else $fleetex = 0;
	
	
	
	
		// Flottenbefehle zuerst
		// if ($ship->fleets_id > 0 && $_GET['id'] == $ship->fsf)
		// {
			// if ($_GET['a'] == "flas") $result = $fleet->activate_shields();
			// if ($_GET['a'] == "flac") $result = $fleet->activate_cloak();
			// if ($_GET['a'] == "flds") $result = $fleet->deactivate_shields();
			// if ($_GET['a'] == "fldc") $result = $fleet->deactivate_cloak();
			// if ($_GET['a'] == "flaw") $result = $fleet->activate_warp();
			// if ($_GET['a'] == "fldw") $result = $fleet->deactivate_warp();
			if ($_GET['a'] == "fleetal" && check_int($_GET['l'])) $result = $fleet->changealvl($_GET['l']);
			// if ($_GET['a'] == "flawp") $result = $fleet->activate_phaser();
			// if ($_GET['a'] == "flawt") $result = $fleet->activate_torps();
			// if ($_GET['a'] == "fldwp") $result = $fleet->deactivate_phaser();
			// if ($_GET['a'] == "fldwt") $result = $fleet->deactivate_torps();
			// if ($_GET['a'] == "flsl" && $ship->systems_id > 0) $result = $ship->leavesystem();
			// if ($_GET['a'] == "flse" && $ship->systems_id == 0) $result = $ship->entersystem();
			// if ($_GET['a'] == "flaks") $result = $fleet->activate_kss();
			// if ($_GET['a'] == "fldks") $result = $fleet->deactivate_kss();
			// if ($_GET['a'] == "att" && check_int($_GET['t']) && $_SESSION['preps'] == $_GET['ps']) $result = $fleet->attack($_GET['t'],$ship->fleets_id);
			// if ($_GET['a'] == "atts" && check_int($_GET['t'])) $result = $fleet->attackstation($ship->fleets_id,$_GET['t']);
			// if ($_GET['a'] == "int" && check_int($_GET['t']) && $_GET['t'] > 0) $result = $fleet->intercept($_GET['t']);
			// if ($_GET['a'] == "cd" && check_int($_GET['m']) && ((check_int($_GET['c']) && $_GET['c'] > 0) || $_GET['c'] == "max") && $_GET["fld"] == 1) $result = $fleet->bussard($_GET['m'],$_GET['c']);
			// if ($_GET['a'] == "ce" && ((check_int($_GET['c']) && $_GET['c'] > 0) || $_GET['c'] == "max") && $_GET["fld"] == 1) $result = $fleet->collect($_GET['c']);
			// if ($_GET['a'] == "afrkn") $result = $fleet->addtorkn();
			// if ($result) unset($_GET['a']);
		// }
		
		// Flottenbefehl-Modifier
		// if ($fleetex) {
			// if ($_GET['a'] == "av" && $_GET['d'] == "re") $result = "Flottenbefehl TODO";//$fleet->activate_repli();
			// if ($_GET['a'] == "dv" && $_GET['d'] == "re") $result = "Flottenbefehl TODO";//$fleet->deactivate_repli();
		
			// if ($_GET['a'] == "awp") $result = $fleet->activate_phaser();
			// if ($_GET['a'] == "awt") $result = $fleet->activate_torps();
			// if ($_GET['a'] == "dwp") $result = $fleet->deactivate_phaser();
			// if ($_GET['a'] == "dwt") $result = $fleet->deactivate_torps();
			
			
			
			
			if ($_GET['a'] == "fleetav" && $_GET['d']) $result = $fleet->av($_GET['d']);
			if ($_GET['a'] == "fleetdv" && $_GET['d']) $result = $fleet->dv($_GET['d']);
			
			
			
		// } else {
			if ($_GET['a'] == "av" && $_GET['d']) $result = $ship->av($_GET['d']);
			if ($_GET['a'] == "dv" && $_GET['d']) $result = $ship->dv($_GET['d']);
		
			if ($_GET['a'] == "awp") $result = $ship->av("wp");
			if ($_GET['a'] == "awt") $result = $ship->av("wt");
			if ($_GET['a'] == "dwp") $result = $ship->dv("wp");
			if ($_GET['a'] == "dwt") $result = $ship->dv("wt");
			
			
			
			
			
			
			
			
			
		// }
		
		
		
		
		
		// Schiffsfunktionen
		// if ($_GET['a'] == "Aktivieren" && $ship->warp == 0 && $ship->warpable == 1) $result = $ship->warpon();
		// if ($_GET['a'] == "Deaktivieren" && $ship->warp == 1 && $ship->warpable == 1) $result = $ship->warpoff();

		if ($_GET['a'] == "mv" && $_SESSION['preps'] == $_GET['ps'])
		{
			if (!check_int($_GET['c']) || abs($_GET['c']) > 10 || abs($_GET['c']) < 1) $_GET['c'] = 1;
			if ($_GET['l'] > 0) $result = $ship->newmove($_GET['c'],"l",0,0);
			if ($_GET['r'] > 0) $result = $ship->newmove($_GET['c'],"r",0,0);
			if ($_GET['u'] > 0) $result = $ship->newmove($_GET['c'],"u",0,0);
			if ($_GET['d'] > 0) $result = $ship->newmove($_GET['c'],"d",0,0);
			// $result = $ship->move($_GET['id'],$_GET['c'],$_GET['l'],$_GET['r'],$_GET['u'],$_GET['d']);
		}	



		
		if ($_GET['a'] == "es" && $ship->map['is_system'] == 1 && $ship->systems_id == 0) $result = $ship->entersystem($id);
		if ($_GET['a'] == "ls" && $ship->systems_id > 0) $result = $ship->leavesystem($_GET['id']);
		if ($_GET['a'] == "lwk" && ($_GET['c'] == "laden" || $_GET['c'] == "max") && !$_GET['flwk']) $result = $ship->loadwarpcore($_GET['c']);


		if ($_GET['a'] == "clsn") $result = $ship->cloakscan();
		
		if ($_GET['a'] == "sensormode") $result = $ship->switchLSSMode($_GET['m']);
		if ($_GET['a'] == "enterworm") $result = $ship->enterWormhole();
		
		if ($_GET['a'] == "nr") $result = $ship->notruf();
		if ($_GET['a'] == "dcl") $result = $ship->stopCollect();
		if ($_GET['a'] == "acl") $result = $ship->startCollect();
		if ($_GET['a'] == "tr" && check_int($_GET['t']) && $_GET['t'] > 0) $result = $ship->activatetraktor($_GET['t']);
		if ($_GET['a'] == "dt") $result = $ship->deactivatetraktor();
		if ($_GET['a'] == "ca" && check_int($_GET['m'])) $result = $ship->changealvl($_GET['m']);
		// if ($_GET['a'] == "lsh" && ((check_int($_GET['c']) && $_GET['c'] > 0) || $_GET['c'] == "max")) $result = $ship->loadshields($_GET['c']);
		// if ($_GET['a'] == "cd" && check_int($_GET['m']) && ((check_int($_GET['c']) && $_GET['c'] > 0) || $_GET['c'] == "max") && !$_GET['fld']) $result = $ship->bussard($_GET['id'],$_GET['m'],$_GET['c']);
		// if ($_GET['a'] == "ce" && ((check_int($_GET['c']) && $_GET['c'] > 0) || $_GET['c'] == "max") && !$_GET['fld']) $result = $ship->collect($_GET['id'],$_GET['c']);
		if ($_GET['a'] == "att" && check_int($_GET['t']) && $_GET['t'] > 0 && $_SESSION['preps'] == $_GET['ps']) $result = $ship->fleetBattle($_GET['t']);
		if ($_GET['a'] == "col" && check_int($_GET['t']) && check_int($_GET['cf'])) $result = $ship->colonize($_GET['t'],$_GET['cf']);
		if ($_GET['a'] == "eb" && (check_int($_GET['c']) || $_GET['c'] == "max")) $result = $ship->ebatt($_GET['c']);
		if ($_GET['a'] == "kat" && $ship->canMap() == 1 && $ship->still == 0 && $ship->systems_id > 0) $result = $ship->kartographie();
		if ($_GET['a'] == "skat" && $ship->canMap() == 1 && $ship->still != 0 && $ship->systems_id > 0) $result = $ship->stopkartographie();
		if ($_GET['a'] == "hud") $result = $ship->changeshiphud();
		if ($_GET['a'] == "int" && check_int($_GET['t']) && $_GET['t'] > 0) $result = $ship->intercept($_GET['t']);
		if ($_GET['a'] == "bko") $result = $ship->setkonstrukt();
		if ($_GET['a'] == "doc" && check_int($_GET['t'])) $result = $ship->dock($_GET['t']);
		if ($_GET['a'] == "ddoc" && $ship->dock > 0) $result = $ship->undock();
		if ($_GET['a'] == "laush" && check_int($_GET['shur'])) $result = $ship->launchshuttle($_GET['shur']);
		if ($_GET['a'] == "lansh" && check_int($_GET['t'])) $result = $ship->landshuttle($_GET['t']);
		if ($_GET['a'] == "warsh" && check_int($_GET['shur'])) $result = $ship->maintainshuttle($_GET['shur']);
		if ($_GET['a'] == "sfbi" && $ship->traktormode == 2) $result = $ship->feedbackimpulse();
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

		if ($_GET['a'] == "bes" && check_int($_GET['t']) && $_GET['m'] == "to")
		{
			if (is_array($_GET['good']) && is_array($_GET['count'])) $result = $ship->beamtosta($_GET['t'],$_GET['good'],$_GET['count']);
			if (check_int($_GET['crew']) && $_GET['crew'] > 0 && $ship->stop_trans != 1)
	
			{
				if ($result) $result .= "<br>";
				$result .= $ship->transfercrewsta($_GET['t'],$_GET['crew'],$_GET['m']);
			}
		}
		if ($_GET['a'] == "bes" && check_int($_GET['t']) && $_GET['m'] == "fr")
		{
			if (is_array($_GET['good']) && is_array($_GET['count'])) $result = $ship->beamfromsta($_GET['t'],$_GET['good'],$_GET['count']);
			if (check_int($_GET['crew']) && $_GET['crew'] > 0 && $ship->stop_trans != 1)
			{
				if ($result) $result .= "<br>";
				$result .= $ship->transfercrewsta($_GET['t'],$_GET['crew'],$_GET['m']);
			}
		}
		
		if ($_GET['a'] == "bewb" && check_int($_GET['t']) && $_GET['m'] == "to") {
			if (is_array($_GET['good']) && is_array($_GET['count'])) $result = $ship->beamtowb($_GET['t'],$_GET['good'],$_GET['count']);
		}
		if ($_GET['a'] == "bewb" && check_int($_GET['t']) && $_GET['m'] == "fr") {
			if (is_array($_GET['good']) && is_array($_GET['count'])) $result = $ship->beamfromwb($_GET['t'],$_GET['good'],$_GET['count']);
		}
		// if ($_GET['a'] == "bwr" && check_int($_GET['t']) && check_int($_GET['id']))
		// {
			// $result = $ship->domwreckage($_GET['id'], $_GET['t']);
		// }
		
		
		
		
		
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
		if ($_GET['a'] == "et" && ((check_int($_GET['count']) && $_GET['count'] > 0) || $_GET['count'] == "max") && check_int($_GET['t'])) $result = $ship->etransfer($_GET['t'],$_GET['count']);
		if ($_GET['a'] == "etc" && ((check_int($_GET['count']) && $_GET['count'] > 0) || $_GET['count'] == "max") && check_int($_GET['t'])) $result = $ship->etransferc($_GET['t'],$_GET['count']);
		if ($_GET['a'] == "ett" && ((check_int($_GET['count']) && $_GET['count'] > 0) || $_GET['count'] == "max") && check_int($_GET['t'])) $result = $ship->etransfers($_GET['t'],$_GET['count']);
		if ($_GET['a'] == "chn" && strlen($_GET['nname']) > 1) $result = $ship->changename($_GET['nname']);
	}
	if ($result || $sresult)
	{
		if ($sresult) $result = $sresult;
		if (is_array($ship->logbook)) $ship->write_logbook();
		if ($ship->destroyed == 0 && !$ship->dsships[$_GET['id']] && !$fleet->dsships[$_GET['id']]  && $_GET['a'] != "col" && $ship->colonized != 1) $ship = new ship;
		pageheader("/ <a href=?p=ship>Schiffe</a> / <b>".stripslashes($ship->name)."</b> <a href=#sh>^</a>");
		meldung($result);
		if ($ship->colonized == 1) die;
	}
	else pageheader("/ <a href=?p=ship>Schiffe</a> / <b>".stripslashes($ship->name)."</b>");
	if ($ship->destroyed == 1 || $ship->dsships[$_GET['id']] == 1 || $fleet->dsships[$_GET['id']] == 1)
	{
		meldung("Du bist nicht Besitzer dieses Schiffes");
		exit;
	}

	
	$shipvals = getShipValuesForBuildplan($ship->plans_id);
	
	
	// debugHelper($shipvals);
	
	if ($ship->cloak == 1) {
		$shipvals['reaktor']  = floor($shipvals['reaktor']*0.7);
		$reaktor = min($shipvals['reaktor'],$ship->warpcore);
	} else {
		$reaktor = min($shipvals['reaktor'],$ship->warpcore);		
	}

	$warpgain = min($shipvals['warpfield_regen'],$ship->warpcore);
	$warpgain = min($ship->max_warpfields - $ship->warpfields,$warpgain);

	
	$vb = $ship->eps_drain;
	if ($ship->lss == 1) $vb++;
	if ($ship->nbs == 1) $vb++;
	if ($ship->wea_phaser == 1) $vb++;
	if ($ship->wea_torp == 1) $vb++;
	if ($ship->schilde_status == 1) $vb++;
	if ($ship->cloak == 1) $vb+=3;


	
	
	// if ($ship->porep == 1)
	// {
		// if ($ship->replikator == 1 || $db->query("SELECT count FROM stu_ships_storage WHERE goods_id=1 AND ships_id=".$ship->id." LIMIT 1",1) < ceil($ship->crew/5)) $vb += ceil($ship->crew/5);
	// }

	// $tarnkosten = tarnkosten($ship->rumps_id);
	// if ($ship->cloak == 1) {
		// $vb += $tarnkosten;
		// $reaktor -= $tarnkosten*2;
	// }

	
	if ($ship->mod_w1) $w1type = $db->query("SELECT * FROM stu_weapons WHERE module_id = ".$ship->mod_w1." LIMIT 1",4);
	if ($ship->mod_w2) $w2type = $db->query("SELECT * FROM stu_weapons WHERE module_id = ".$ship->mod_w2." LIMIT 1",4);
	
	$regsum = $db->query("SELECT SUM(value) FROM stu_modules_special WHERE type = 'shieldreg' AND modules_id IN (".$ship->mod_m1.", ".$ship->mod_m2.", ".$ship->mod_m3.", ".$ship->mod_m4.", ".$ship->mod_m5.", ".$ship->mod_w1.", ".$ship->mod_w2.", ".$ship->mod_s1.", ".$ship->mod_s2.")",1);	
	$regsum = min($ship->max_schilde - $ship->schilde,$regsum);
	if ($ship->cloak == 1) $regsum = 0;
	
	
	$reaktor-$vb > $ship->max_eps-$ship->eps ? $ee = $ship->max_eps-$ship->eps : $ee = $reaktor-$vb;
	
	$coredrain = min($ee+$vb+$warpgain,$ship->warpcore);
	
	if ($ship->crew < $ship->min_crew)
	{
		$reaktor = 0;
		$ee = $vb;
		// $vb = 0;
		$warpgain = 0;
		$regsum = 0;
	}
	
	switch($w1type['wtype']) {
		case "torpedo" : 	$attackicon = "torpedo";
							break;
		case "beam":		$attackicon = $w1type['dtype'];
							break;
		default:
							switch($w2type['wtype']) {
								case "torpedo" : 	$attackicon = "torpedo";
													break;
								case "beam":		$attackicon = $w2type['dtype'];
													break;
							}
	}
	
	
	echo "<script language=\"Javascript\">
	
document.onkeydown = function(e) {
	if (e.target.name != 'nname' && e.target.name != 'tx' && e.target.name != 'count' && e.target.name != 'recipient') {
		
		switch (e.keyCode) {
			case 37:
				doFly('l',1);
				break;
			case 38:
				doFly('u',1);
				break;
			case 39:
				doFly('r',1);
				break;
			case 40:
				doFly('d',1);
				break;
		}
	}
};
	function doFly(direction, count) {
		window.location = 'main.php?p=ship&s=ss&id=".$_GET['id']."&ps=".$_SESSION['pagesess']."&a=mv&'+direction+'=1&c='+count;
	}
	function switchSensor(mode) {
		window.location = 'main.php?p=ship&s=ss&id=".$_GET['id']."&ps=".$_SESSION['pagesess']."&a=sensormode&m='+mode;
	}	
	function getshipinfo()
	{
		elt = 'shinfo';
		openPJsWin(elt,600,300,150);
		sendRequest('backend/shinfo.php?PHPSESSID=".session_id()."&id=".$_GET['id']."');
	}
	function showsysinfo(id)
	{
		elt = 'syinfo';
		openJsWin(elt,600,300,100);
		sendRequest('backend/shipsysview.php?PHPSESSID=".session_id()."&id=' + id + '');
	}
	function open_pm_window(rec,ext1,ext2)
	{
		elt = 'shipm';
		openJsWin(elt,450,200,150);
		sendRequest('backend/pmwin.php?PHPSESSID=".session_id()."&recipient=' + rec + '&ext1='+ext1+'&ext2='+ext2);
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
		openJsWin(elt,600,300,150);
		sendRequest('backend/ship/writelog.php?PHPSESSID=".session_id()."&id=".$_GET['id']."');
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
		openPJsWin(elt,300);
		sendRequest('backend/ship/setransfer.php?PHPSESSID=".session_id()."&id='+id+'&t='+t+'&e=".$ship->eps."');
	}
	function col_etransfer(id,t)
	{
		elt = 'cetrans';
		openPJsWin(elt,300);
		sendRequest('backend/ship/cetransfer.php?PHPSESSID=".session_id()."&id='+id+'&t='+t+'&e=".$ship->eps."');
	}
	function stat_etransfer(id,t)
	{
		elt = 'tetrans';
		openPJsWin(elt,300);
		sendRequest('backend/ship/tetransfer.php?PHPSESSID=".session_id()."&id='+id+'&t='+t+'&e=".$ship->eps."');
	}
	function togglecall(sid,menu)
	{
		http.open('get', 'backend/ship/togglepanel.php?PHPSESSID=".session_id()."&id='+sid+'&m='+menu);
		http.onreadystatechange = function () {};
		http.send(null);		
	}	
	</script>";
	if ($ship->maintaintime > 0 && $_SESSION['uid'] > 100)
	{
		if ($ship->lastmaintainance+$ship->maintaintime > time()+86400 && $ship->lastmaintainance+$ship->maintaintime <= time()+432000) $sco = "Yellow";
		elseif ($ship->lastmaintainance+$ship->maintaintime <= time()+86400) $sco = "#FF0000";
		else $sco = "Green";
	}
	else $sco = "#000000";
	// SCO als Variablennamen zu nehmen ist schon hart. Andererseits passts ganz gut - wenn sie denn endlich pleite sind gehts Licht aus...und dann isses da auch schwarz
	// echo "<a name=sh></a><table bgcolor=#262323 cellspacing=1 cellpadding=1 width=850><form action=main.php method=get><input type=hidden name=p value=ship><input type=hidden name=s value=ss><input type=hidden name=a value=chn><input type=hidden name=id value=".$_GET['id'].">
	// <tr><th></th><th>Klasse</th><th>x|y</th><th>Hülle</th><th>Schilde</th><th>Energie</th><th>Crew</th><th>Name</th><td rowspan=3><a href=?p=ship&s=ssz&id=".$_GET['id']." ".getonm('szst','buttons/selfdes')."><img src=".$gfx."/buttons/selfdes1.gif name=szst border=0 title=\"Selbstzerstörung einleiten\"></a></td></tr>
	// <tr><td width=1 rowspan=2 style=\"background: ".$sco."\"></td><td rowspan=2><img src=".$gfx."/ships/".vdam($ship).$ship->rumps_id.".gif title=\"".stripslashes($ship->cname)."\"></td>
	// <td>".($ship->systems_id > 0 ? $ship->sx."|".$ship->sy." (".$ship->cx."|".$ship->cy.")" : $ship->cx."|".$ship->cy)."</td>
	// <td>".renderhuellstatusbar($ship->huelle,$ship->max_huelle)." ".$ship->huelle."/".$ship->max_huelle."</td>
	// <td>".rendershieldstatusbar($ship->schilde_status,$ship->schilde,$ship->max_schilde)." ".($ship->schilde_status == 1 ? "<font color=cyan>".$ship->schilde."/".$ship->max_schilde."</font>" : $ship->schilde."/".$ship->max_schilde)."</td>
	// <td>".$ship->eps."/".$ship->max_eps." (<font color=Green>".($ee > 0 ? "+".$ee : $ee)."</font>/<font color=#FF0000>".(!$vb ? 0 : $vb)."</font>/".($ee+$vb).")</td><td>".$ship->crew."/".$ship->max_crew." (".$ship->min_crew.")</td>
	// <td><input type=text size=20 name=nname class=text value=\"".stripslashes($ship->name)."\"> <input type=submit class=button value=ändern></td></tr>
	// <tr><td><a href=?p=ship&s=ss&id=".$_GET['id']." onmouseover=cp('ref','buttons/lese2') onmouseout=cp('ref','buttons/lese1')><img src=".$gfx."/buttons/lese1.gif name=ref title='Aktualisieren' border=0></a>
	// <a href=?p=ship&s=ss&a=nr&id=".$_GET['id']." ".getonm("nr","buttons/ascan")."><img src=".$gfx."/buttons/ascan1.gif name=nr border=0 title=\"Notruf absetzen\"></a>".($ship->slots == 0 ? " <a href=?p=ship&s=ss&id=".$_GET['id']."&a=hud ".getonm('hud','buttons/hud')."><img src=".$gfx."/buttons/hud1.gif title=\"Zur Gefechts-HUD umschalten\" name=hud border=0>" : "")."</a>
	// <a href=\"javascript:void(0);\" onClick=\"open_log_window();\" ".getonm('writelog','buttons/knedit')."><img src=".$gfx."/buttons/knedit1.gif name=writelog title=\"Logbucheintrag schreiben\" border=0></a> <a href=\"javascript:void(0);\" onClick=\"getshipinfo();\" ".getonm('sin','buttons/info')."><img src=".$gfx."/buttons/info1.gif name=sin border=0 title=\"Schiffsinformationen\"></a>".($ship->is_rkn == 0 && $_SESSION['is_rkn'] == 1 ? " <a href=?p=ship&s=ss&a=".($ship->fsf == $_GET['id'] ? "afrkn" : "arkn")."&id=".$_GET['id']."><img src=".$gfx."/rassen/".$_SESSION['race']."s.gif border=0 title=\"".($ship->fsf == $_GET['id'] ? "Flotte" : "Schiff")." dem RPG bereitstellen\"></a>" : "")."
	// </td><td colspan=2>".getmodulelist()."</td><td colspan=3>".getdammodulelist()."</td></tr></form></table><br>";

	$menus = array();
	$open = explode(" ",$ship->open_menus);
	
	$shipdatacontent = array();
	
	$hullcolor = "gre";
	if (($ship->huelle / $ship->max_huelle) < 0.6) $hullcolor = "yel";
	if (($ship->huelle / $ship->max_huelle) < 0.4) $hullcolor = "org";
	if (($ship->huelle / $ship->max_huelle) < 0.2) $hullcolor = "red";
	
	if ($ship->schilde_status == 1) $shieldcolor = "cya";
	else							$shieldcolor = "cyd";
	
	$shipdatastring = "<tr><td><table bgcolor=#262323 cellspacing=1 cellpadding=2 style=\"width:100%;\"><form action=main.php method=get><input type=hidden name=p value=ship><input type=hidden name=s value=ss><input type=hidden name=a value=chn><input type=hidden name=id value=".$_GET['id'].">
	<tr><th>Klasse</th><th>x|y</th><th width=100>Hülle</th><th width=100>Schilde</th><th width=100>Energie</th><th width=100>Warpkern</th><th width=100>Antrieb</th><th width=50>Crew</th><th>Name</th></tr>
	<tr><td rowspan=2><img src=".$gfx."/ships/".shipPic($ship->rumps_id,$ship->cloak,$ship->trumfield,$ship->huelle/$ship->max_huelle).".gif title=\"".stripslashes($ship->cname)."\"></td>
	<td>".($ship->systems_id > 0 ? $ship->sx."|".$ship->sy." (".$ship->cx."|".$ship->cy.")" : $ship->cx."|".$ship->cy)."</td>
	<td style='text-align:center;width:120px;'>".darkuniversalstatusbar($ship->huelle,$ship->max_huelle,$hullcolor,0,110)."<br>".$ship->huelle."/".$ship->max_huelle."</td>
	<td style='text-align:center;width:120px;'>".darkuniversalstatusbar($ship->schilde,$ship->max_schilde,$shieldcolor,$regsum,110)."<br>".($ship->schilde_status == 1 ? "<font color=cyan>".$ship->schilde."/".$ship->max_schilde."</font>" : $ship->schilde."/".$ship->max_schilde)." ".($regsum > 0 ? "<font color=green>+".$regsum."</font>" : "")."</td>
	<td style='text-align:center;width:120px;'>".darkuniversalstatusbar($ship->eps,$ship->max_eps,"yel",$ee,110)."<br>".$ship->eps."/".$ship->max_eps." ".($ee > 0 ? "<font color=green>+".$ee."</font>" : "<font color=red>".$ee."</font>")."</td>
	<td style='text-align:center;width:120px;'>".darkuniversalstatusbar($ship->warpcore,$ship->max_warpcore,"org",$coredrain,110)."<br>".$ship->warpcore."/".$ship->max_warpcore." <font color=red>-".$coredrain."</font></td>
	<td style='text-align:center;width:120px;'>".darkuniversalstatusbar($ship->warpfields,$ship->max_warpfields,"whi",$warpgain,110)."<br>".$ship->warpfields."/".$ship->max_warpfields." <font color=green>+".$warpgain."</font></td>
	<td style='text-align:center;width:120px;'>".$ship->crew."/".$ship->max_crew."</td>
	<td><input type=text size=40 name=nname class=text value=\"".stripslashes($ship->name)."\"> <input type=submit class=button value=ändern></td></tr></form></table></td></tr>";
	
	
	array_push($shipdatacontent,$shipdatastring);
	
	
	
	
	
	$leftmenu = "";
	$menus_l = array();
	$menus_r = array();
	
	// print_r($ship);
	$content = array();
	array_push($content,dropDownMenuOption(
		"<center>".getmodulelist()."</center>"
	));	
	$dammod = 	getdammodulelist();
	if ($dammod) {
		array_push($content,dropDownMenuOption(
			"<div style=\"text-align: center;width: 400px;\">".$dammod."</div>"
		));		
	}
	array_push($content,dropDownMenuOption(
		"<img src=".$gfx."/buttons/icon/info.gif name=info title=\"Schiffswerte\" border=0> Schiffswerte",
		"<input type=button name=c value=\"anzeigen\" class=button onClick=\"getshipinfo();\">"
	));		
	$menus['mods'] = dropDownMenu(1,"Module & Werte","mmod",$gfx."/buttons/icon/modules.gif",$content,in_array("mmod",$open),"togglecall('".$ship->id."','mmod');");
	array_push($menus_l,$menus['mods']);
	

	$content = array();
	array_push($content,dropDownMenuOption(
		"<img src=".$gfx."/buttons/icon/warpcore.gif title='Warpkern'> Warpkern: ".$ship->warpcore."/".$ship->max_warpcore,
		"<input type=submit name=c value=laden class=button> <input type=submit name=c value=max class=button>",
		array('p' => "ship", "s" => "ss", "a" => "lwk", "id" => $ship->id)
	));	
	array_push($content,dropDownMenuOption(
		"<img src=".$gfx."/buttons/icon/warp.gif title='Warpantrieb'> Warpantrieb",
		($ship->warp == 1) ? "<a href=?p=ship&s=ss&a=dv&d=wrp&id=".$_GET['id']." ".getHover("wrp","active/g/warp","hover/r/ship")."><img src=".$gfx."/buttons/active/g/warp.gif border=0 title='Warpantrieb deaktivieren' name=wrp> deaktivieren</a>" : "<a href=?p=ship&s=ss&a=av&d=wrp&id=".$_GET['id']." ".getHover("wrp","inactive/n/ship","hover/g/warp")."><img src=".$gfx."/buttons/inactive/n/ship.gif border=0 title='Warpantrieb aktivieren' name=wrp> aktivieren</a>"		
	));	
	$menus['warp'] = dropDownMenu(4,"Reaktor & Antriebssysteme","megy",$gfx."/buttons/icon/energysystems.gif",$content,in_array("megy",$open),"togglecall('".$ship->id."','megy');");
	array_push($menus_l,$menus['warp']);
	

	$alvlicon = "";
	if ($ship->alvl == 1) $tmpcol = "green";
	if ($ship->alvl == 2) $tmpcol = "yellow";
	if ($ship->alvl == 3) $tmpcol = "red";
	
	
	$content = array();
	array_push($content,dropDownMenuOption(
		"<img src=".$gfx."/buttons/icon/".$tmpcol.".gif title='Alarmstufe'> Alarmstufe",
		"<a href=?p=ship&s=ss&a=ca&m=1&id=".$_GET['id']."><img name=alt1 src=".$gfx."/buttons/active/n/green.gif border=0 title=\"Grün\" ".getHover("alt1","active/n/green","hover/g/green")."></a>&nbsp;
		 <a href=?p=ship&s=ss&a=ca&m=2&id=".$_GET['id']."><img name=alt2 src=".$gfx."/buttons/active/n/yellow.gif border=0 title=\"Gelb\" ".getHover("alt2","active/n/yellow","hover/w/yellow")."></a>&nbsp;
		 <a href=?p=ship&s=ss&a=ca&m=3&id=".$_GET['id']."><img name=alt3 src=".$gfx."/buttons/active/n/red.gif border=0 title=\"Rot\" ".getHover("alt3","active/n/red","hover/r/red")."></a>&nbsp;"
	));
	
	$weaponControlString = "";
	if ($ship->mod_w1) {
		$wicon = "";
		switch($w1type['wtype']) {
			case "torpedo" : 	$wicon = "torpedo";
								$wname = "<img src=".$gfx."/buttons/icon/torpedo.gif title='Torpedo-Rampe'> Torpedo-Rampe";
								break;
			case "beam":		$wicon = $w1type['dtype'];
								$wname = "<img src=".$gfx."/buttons/icon/phaser.gif title='Strahlwaffe'> Strahlwaffe";
								break;
		}
		
		if ($ship->wea_phaser == 1) {
			$weaponControlString .= "<a href=?p=ship&s=ss&id=".$_GET['id']."&a=dwp ".getHover("wps","active/g/".$wicon,"hover/r/emitter")."><img src=".$gfx."/buttons/active/g/".$wicon.".gif border=0  name=wps title='Waffensystem deaktivieren'> deaktivieren</a>";
		} else {
			$weaponControlString .= "<a href=?p=ship&s=ss&id=".$_GET['id']."&a=awp ".getHover("wps","inactive/n/emitter","hover/g/".$wicon)."><img src=".$gfx."/buttons/inactive/n/emitter.gif border=0  name=wps title='Waffensystem aktivieren'> aktivieren</a>";			
		}
		array_push($content,dropDownMenuOption(
			$wname,
			$weaponControlString
		));	
		unset($weaponControlString);
		unset($wname);
		unset($wicon);
	}
	if ($ship->mod_w2) {
		$wicon = "";
		switch($w2type['wtype']) {
			case "torpedo" : 	$wicon = "torpedo";
								$wname = "<img src=".$gfx."/buttons/icon/torpedo.gif title='Torpedo-Rampe'> Torpedo-Rampe";
								break;
			case "beam":		$wicon = $w2type['dtype'];
								$wname = "<img src=".$gfx."/buttons/icon/phaser.gif title='Strahlwaffe'> Strahlwaffe";
								break;
		}
		
		if ($ship->wea_torp == 1) {
			$weaponControlString .= "<a href=?p=ship&s=ss&id=".$_GET['id']."&a=dwt ".getHover("wpt","active/g/".$wicon,"hover/r/emitter")."><img src=".$gfx."/buttons/active/g/".$wicon.".gif border=0  name=wpt title='Waffensystem deaktivieren'> deaktivieren</a>";
		} else {
			$weaponControlString .= "<a href=?p=ship&s=ss&id=".$_GET['id']."&a=awt ".getHover("wpt","inactive/n/emitter","hover/g/".$wicon)."><img src=".$gfx."/buttons/inactive/n/emitter.gif border=0  name=wpt title='Waffensystem aktivieren'> aktivieren</a>";			
		}
		array_push($content,dropDownMenuOption(
			$wname,
			$weaponControlString
		));	
		unset($weaponControlString);
		unset($wname);
		unset($wicon);
	}
	$menus['fight'] = dropDownMenu(3,"Gefechtskontrolle","mcbt",$gfx."/buttons/icon/ai.gif",$content,in_array("mcbt",$open),"togglecall('".$ship->id."','mcbt');");
	array_push($menus_r,$menus['fight']);	
	
	
	$content = array();
	if ($ship->fleets_id > 0) {
		array_push($content,dropDownMenuOption(
			"<img src=".$gfx."/buttons/icon/shield.gif> Flotte: Schilde",
			"<a href=?p=ship&s=ss&a=fleetav&d=sh&id=".$_GET['id']." ".getHover("fshia","inactive/n/shield","hover/g/shield")."><img src=".$gfx."/buttons/inactive/n/shield.gif border=0 title='Schilde aktivieren' name=fshia> aktivieren</a>"
		));			
		array_push($content,dropDownMenuOption(
			"",
			"<a href=?p=ship&s=ss&a=fleetdv&d=sh&id=".$_GET['id']." ".getHover("fshid","inactive/n/shield","hover/r/shield")."><img src=".$gfx."/buttons/inactive/n/shield.gif border=0 title='Schilde deaktiveren' name=fshid> deaktivieren</a>"
		));	
		
		array_push($content,dropDownMenuOption(
			"<img src=".$gfx."/buttons/icon/warp.gif> Flotte: Warpantrieb",
			"<a href=?p=ship&s=ss&a=fleetav&d=wa&id=".$_GET['id']." ".getHover("fwara","inactive/n/warp","hover/g/warp")."><img src=".$gfx."/buttons/inactive/n/warp.gif border=0 title='Warpantrieb aktivieren' name=fwara> aktivieren</a>"
		));			
		array_push($content,dropDownMenuOption(
			"",
			"<a href=?p=ship&s=ss&a=fleetdv&d=wa&id=".$_GET['id']." ".getHover("fward","inactive/n/warp","hover/r/warp")."><img src=".$gfx."/buttons/inactive/n/warp.gif border=0 title='Warpantrieb deaktiveren' name=fward> deaktivieren</a>"
		));	
		
		array_push($content,dropDownMenuOption(
			"<img src=".$gfx."/buttons/icon/phaser.gif> Flotte: Primärwaffe",
			"<a href=?p=ship&s=ss&a=fleetav&d=pw&id=".$_GET['id']." ".getHover("fpwa","inactive/n/phaser","hover/g/phaser")."><img src=".$gfx."/buttons/inactive/n/phaser.gif border=0 title='Primärwaffe aktivieren' name=fpwa> aktivieren</a>"
		));			
		array_push($content,dropDownMenuOption(
			"",
			"<a href=?p=ship&s=ss&a=fleetdv&d=pw&id=".$_GET['id']." ".getHover("fpwd","inactive/n/phaser","hover/r/phaser")."><img src=".$gfx."/buttons/inactive/n/phaser.gif border=0 title='Primärwaffe deaktiveren' name=fpwd> deaktivieren</a>"
		));	
		
		array_push($content,dropDownMenuOption(
			"<img src=".$gfx."/buttons/icon/torpedo.gif> Flotte: Sekundärwaffe",
			"<a href=?p=ship&s=ss&a=fleetav&d=sw&id=".$_GET['id']." ".getHover("fswa","inactive/n/torpedo","hover/g/torpedo")."><img src=".$gfx."/buttons/inactive/n/torpedo.gif border=0 title='Sekundärwaffe aktivieren' name=fswa> aktivieren</a>"
		));			
		array_push($content,dropDownMenuOption(
			"",
			"<a href=?p=ship&s=ss&a=fleetdv&d=sw&id=".$_GET['id']." ".getHover("fswd","inactive/n/torpedo","hover/r/torpedo")."><img src=".$gfx."/buttons/inactive/n/torpedo.gif border=0 title='Sekundärwaffe deaktiveren' name=fswd> deaktivieren</a>"
		));	
		
		array_push($content,dropDownMenuOption(
			"<img src=".$gfx."/buttons/icon/cloak.gif> Flotte: Tarnvorrichtung",
			"<a href=?p=ship&s=ss&a=fleetav&d=ck&id=".$_GET['id']." ".getHover("fclka","inactive/n/cloak","hover/g/cloak")."><img src=".$gfx."/buttons/inactive/n/cloak.gif border=0 title='Tarnvorrichtung aktivieren' name=fclka> aktivieren</a>"
		));			
		array_push($content,dropDownMenuOption(
			"",
			"<a href=?p=ship&s=ss&a=fleetdv&d=ck&id=".$_GET['id']." ".getHover("fclkd","inactive/n/cloak","hover/r/cloak")."><img src=".$gfx."/buttons/inactive/n/cloak.gif border=0 title='Tarnvorrichtung deaktiveren' name=fclkd> deaktivieren</a>"
		));	
		
		array_push($content,dropDownMenuOption(
			"<img src=".$gfx."/buttons/icon/ai.gif> Flotte: Alarmstufe",
			"<a href=?p=ship&s=ss&a=fleetal&l=1&id=".$_GET['id']." ".getHover("fgre","inactive/n/green","hover/g/green")."><img src=".$gfx."/buttons/inactive/n/green.gif border=0 title='Alarmstufe Grün' name=fgre> Grün</a>"
		));			
		array_push($content,dropDownMenuOption(
			"",
			"<a href=?p=ship&s=ss&a=fleetal&l=2&id=".$_GET['id']." ".getHover("fyel","inactive/n/yellow","hover/w/yellow")."><img src=".$gfx."/buttons/inactive/n/yellow.gif border=0 title='Alarmstufe Gelb' name=fyel> Gelb</a>"
		));	
		array_push($content,dropDownMenuOption(
			"",
			"<a href=?p=ship&s=ss&a=fleetal&l=3&id=".$_GET['id']." ".getHover("fred","inactive/n/red","hover/r/red")."><img src=".$gfx."/buttons/inactive/n/red.gif border=0 title='Alarmstufe Rot' name=fred> Rot</a>"
		));	
		$menus['fleet'] = dropDownMenu(2,"Flottenbefehle","mflt",$gfx."/buttons/icon/ships.gif",$content,in_array("mflt",$open),"togglecall('".$ship->id."','mflt');");
		array_push($menus_r,$menus['fleet']);	
	}


	
	
	$content = array();
	array_push($content,dropDownMenuOption(
		"<img src=".$gfx."/buttons/icon/shield.gif title='Schilde'> Schilde: ".$ship->schilde."/".$ship->max_schilde."",
		($ship->schilde_status == 1) ? "<a href=?p=ship&s=ss&a=dv&d=sh&id=".$_GET['id']." ".getHover("shi","active/g/shield","hover/r/shield")."><img src=".$gfx."/buttons/active/g/shield.gif border=0 title='Schilde deaktiveren' name=shi> deaktivieren</a>" : "<a href=?p=ship&s=ss&a=av&d=sh&id=".$_GET['id']." ".getHover("shi","inactive/n/shield","hover/g/shield")."><img src=".$gfx."/buttons/inactive/n/shield.gif border=0 title='Schilde aktivieren' name=shi> aktivieren</a>"
	));
	if ($ship->schilde < $ship->max_schilde) {
		$t = time();
		// $regsum = $db->query("SELECT SUM(value) FROM stu_modules_special WHERE type = 'shieldreg' AND modules_id IN (".$ship->mod_m1.", ".$ship->mod_m2.", ".$ship->mod_m3.", ".$ship->mod_m4.", ".$ship->mod_m5.", ".$ship->mod_w1.", ".$ship->mod_w2.", ".$ship->mod_s1.", ".$ship->mod_s2.")",1);
		$diff = max(max(ceil((($ship->lasthit + 1800) - $t)/60),0), max(ceil((($ship->lastshieldreg + 600) - $t)/60),0));
		// $diff = ;
		array_push($content,dropDownMenuOption(
			"<img src=".$gfx."/buttons/icon/shieldplus.gif title='Schildregeneration'> Regeneration",
			"+".$regsum." in ".$diff." min"
		));	
	}
	
	$menus['shield'] = dropDownMenu(2,"Schilde","mshd",$gfx."/buttons/icon/shield.gif",$content,in_array("mshd",$open),"togglecall('".$ship->id."','mshd');");
	array_push($menus_l,$menus['shield']);

	
	if($ship->cloakable == 1) {
	$content = array();
		array_push($content,dropDownMenuOption(
			"<img src=".$gfx."/buttons/icon/cloak.gif title='Tarnung'> Tarnung",
			(($ship->cloak > 1 && time() < $ship->cloak) ? "Baut ab bis<br>".date("d.m H:i",$ship->cloak) : ($ship->cloak == 0 || ($ship->cloak > 1 && time() > $ship->cloak) ? "<a href=?p=ship&s=ss&a=av&d=cl&id=".$_GET['id']." ".getHover("clk","inactive/n/ship","hover/g/cloak")."><img src=".$gfx."/buttons/inactive/n/ship.gif border=0 title='Tarnung aktivieren' name=clk> aktivieren</a>" : "<a href=?p=ship&s=ss&a=dv&d=cl&id=".$_GET['id']." ".getHover("clk","active/g/cloak","hover/r/ship")."><img src=".$gfx."/buttons/active/g/cloak.gif border=0 title='Tarnung deaktiveren' name=clk> deaktivieren</a>"))
		));	
		$menus['cloak'] = dropDownMenu(2,"Tarnvorrichtung","mclk",$gfx."/buttons/icon/cloak.gif",$content,in_array("mclk",$open),"togglecall('".$ship->id."','mclk');");
		array_push($menus_l,$menus['cloak']);
	}
	
	
	
	$content = array();
	
	array_push($content,dropDownMenuOption(
		"<img src=".$gfx."/buttons/icon/text.gif name=writelog title=\"Logbucheintrag schreiben\" border=0> Logbuch",
		"<input type=button name=c value=\"Eintrag erstellen\" class=button onClick=\"open_log_window();\">"
	));	
	array_push($content,dropDownMenuOption(
		"<img src=".$gfx."/buttons/icon/exclamation.gif name=nr border=0 title=\"Notruf absetzen\"> Notruf",
		"<input type=submit name=c value=absetzen class=button>",
		array('p' => "ship", "s" => "ss", "a" => "nr", "id" => $ship->id)
	));	
	array_push($content,"<tr style=\"height:10px;\"><td colspan=2></td></tr>");		
	array_push($content,dropDownMenuOption(
		"<img src=".$gfx."/buttons/icon/destruct.gif name=szst border=0 title=\"Selbstzerstörung einleiten\"> Selbstzerstörung",
		"<a href=?p=ship&s=ssz&id=".$_GET['id']." ".getHover("selfdes","inactive/n/ship","hover/r/destruct")."><img src=".$gfx."/buttons/inactive/n/ship.gif title='Tarnung' name=selfdes> einleiten</a>"
	));		
	$menus['special'] = dropDownMenu(1,"Sonderfunktionen","mspc",$gfx."/buttons/icon/hud.gif",$content,in_array("mspc",$open),"togglecall('".$ship->id."','mspc');");
	array_push($menus_r,$menus['special']);	
	

	
	
	
	if ($ship->canMap() == 1 && $ship->systems_id > 0)
	{
		$content = array();			
		if ($ship->systemIsMapped($ship->systems_id)) {
			array_push($content,dropDownMenuOption(
				"<img src=".$gfx."buttons/icon/map.gif name=cgd border=0 width=16 height=16> Astrometrie ",
				"System bereits kartographiert"
			));	
		} else {
			if ($ship->still == 0) {
				array_push($content,dropDownMenuOption(
					"<img src=".$gfx."buttons/icon/map.gif name=cgd border=0 width=16 height=16> Astrometrie ",
					"<a href=?p=ship&s=ss&id=".$_GET['id']."&a=kat ".getHover("smap","inactive/n/map","hover/w/map")."><img src=".$gfx."/buttons/inactive/n/map.gif name=smap border=0 title=\"System kartographieren\"> System kartographieren</a>"
				));	
			} else {
				array_push($content,dropDownMenuOption(
					"<img src=".$gfx."buttons/icon/map.gif name=cgd border=0 width=16 height=16> Astrometrie ",
					"Dauert an bis ca. ".date("d.m H:i",$ship->still)."<br><a href=?p=ship&s=ss&id=".$_GET['id']."&a=skat ".getHover("smap","inactive/n/map","hover/r/map")."><img src=".$gfx."/buttons/inactive/n/map.gif name=stel border=0 title=\"Kartographierung abbrechen\"> abbrechen</a>"
				));	
			}

		}
	
		$menus['labs'] = dropDownMenu(3,"Labors","mlab",$gfx."buttons/icon/research.gif",$content,in_array("mlab",$open),"togglecall('".$ship->id."','mlab');");
		array_push($menus_r,$menus['labs']);			
	}
	
	
	
	
	$collector = $ship->getCollector();
	$collectinfo = $ship->getCollectionInfo();

	$content = array();
	if (($collector == "deut") && $collectinfo['deut'] > 0) {
		array_push($content,dropDownMenuOption(
			"<img src=".$gfx."/goods/5.gif name=cgd border=0 width=16 height=16> Vorkommen: ",
			$collectinfo['curr']." / ".$collectinfo['max']
		));		
		if ($ship->assigned) {
			array_push($content,dropDownMenuOption(
				"<img src=".$gfx."/buttons/icon/storage.gif name=szst border=0 title=\"Sammeln stoppen\"> Sammeln stoppen",
				"<input type=submit name=scl value=stoppen class=button>",
				array('p' => "ship", "s" => "ss", "a" => "dcl", "id" => $ship->id)
			));		
		} else {
			array_push($content,dropDownMenuOption(
				"<img src=".$gfx."/buttons/icon/storage.gif name=szst border=0 title=\"Sammeln beginnen\"> Sammeln beginnen",
				"<input type=submit name=scl value=beginnen class=button>",
				array('p' => "ship", "s" => "ss", "a" => "acl", "id" => $ship->id)
			));		
		}
		$menus['collect'] = dropDownMenu(1,"Sammeln","mclt",$gfx."buttons/icon/asteroids.gif",$content,in_array("mclt",$open),"togglecall('".$ship->id."','mclt');");
		array_push($menus_r,$menus['collect']);	
	}	
	if ($collector == "ore" && $collectinfo['ore'] > 0) {
		array_push($content,dropDownMenuOption(
			"<img src=".$gfx."/goods/11.gif name=cgd border=0 width=16 height=16> Vorkommen: ",
			$collectinfo['curr']." / ".$collectinfo['max']
		));		
		if ($ship->assigned) {
			array_push($content,dropDownMenuOption(
				"<img src=".$gfx."/buttons/icon/storage.gif name=szst border=0 title=\"Sammeln stoppen\"> Sammeln stoppen",
				"<input type=submit name=scl value=stoppen class=button>",
				array('p' => "ship", "s" => "ss", "a" => "dcl", "id" => $ship->id)
			));		
		} else {
			array_push($content,dropDownMenuOption(
				"<img src=".$gfx."/buttons/icon/storage.gif name=szst border=0 title=\"Sammeln beginnen\"> Sammeln beginnen",
				"<input type=submit name=scl value=beginnen class=button>",
				array('p' => "ship", "s" => "ss", "a" => "acl", "id" => $ship->id)
			));		
		}
		$menus['collect'] = dropDownMenu(4,"Sammeln","mclt",$gfx."buttons/icon/asteroids.gif",$content,in_array("mclt",$open),"togglecall('".$ship->id."','mclt');");
		array_push($menus_r,$menus['collect']);			
	}		

	if ($collector == "ore" && $collectinfo['ore'] == 0) {
		array_push($content,dropDownMenuOption(
			"<img src=".$gfx."/goods/11.gif name=cgd border=0 width=16 height=16> Vorkommen: ",
			"keine"
		));		
		$menus['collect'] = dropDownMenu(4,"Sammeln","mclt",$gfx."buttons/icon/asteroids.gif",$content,in_array("mclt",$open),"togglecall('".$ship->id."','mclt');");
		array_push($menus_r,$menus['collect']);	
	}
	if ($collector == "deut" && $collectinfo['deut'] == 0) {
		array_push($content,dropDownMenuOption(
			"<img src=".$gfx."/goods/5.gif name=cgd border=0 width=16 height=16> Vorkommen: ",
			"keine"
		));		
		$menus['collect'] = dropDownMenu(4,"Sammeln","mclt",$gfx."buttons/icon/asteroids.gif",$content,in_array("mclt",$open),"togglecall('".$ship->id."','mclt');");
		array_push($menus_r,$menus['collect']);	
	}
	
	$content = array();

	if ($ship->map['is_system'] == 1 && $ship->systems_id == 0)
	{
		$sys = $map->getsystembyxy($ship->cx,$ship->cy);
		array_push($content,dropDownMenuOption(
			"<img src=".$gfx."/systems/".$sys['type']."ms.png width=15 height=15>&nbsp;".$sys['name']."-System (".$ship->map['name'].")"
		));
		array_push($content,dropDownMenuOption(
			"<img src=".$gfx."/buttons/icon/planet.gif name=planis border=0 title=\"Planeten\"> Planeten-Scan",
			"<input type=button name=c value=\"anzeigen\" class=button onClick=\"showsysinfo(".$_GET['id'].");\">"
		));	
		array_push($content,dropDownMenuOption(
			"<img src=".$gfx."/buttons/icon/star.gif name=se border=0 title='Einfliegen'> System-Einflug",
			"<a href=?p=ship&s=ss&a=es&id=".$_GET['id']." ".getHover("lvsys","inactive/n/warp","hover/w/ship")."><img src=".$gfx."/buttons/inactive/n/warp.gif border=0 title='System-Einflug' name=lvsys> durchführen</a>"			
		));	
	} elseif ($ship->systems_id > 0) {
		if (!$sys) $sys = $map->getsystembyid($ship->systems_id);
		$sys = $map->getsystembyxy($ship->cx,$ship->cy);
		array_push($content,dropDownMenuOption(
			"<img src=".$gfx."/map/".$ship->map['type'].".gif width=15 height=15>&nbsp;".$ship->map['name']." im ".$sys['name']."-System"
		));
		array_push($content,dropDownMenuOption(
			"<img src=".$gfx."/buttons/icon/planet.gif name=planis border=0 title=\"Planeten\"> Planetenscan",
			"<input type=button name=c value=\"anzeigen\" class=button onClick=\"showsysinfo(".$_GET['id'].");\">"
		));	
		array_push($content,dropDownMenuOption(
			"<img src=".$gfx."/buttons/icon/star.gif name=sl border=0 title='Verlassen'> System verlassen",
			"<a href=?p=ship&s=ss&a=ls&id=".$_GET['id']." ".getHover("lvsys","inactive/n/ship","hover/g/warp")."><img src=".$gfx."/buttons/inactive/n/ship.gif border=0 title='System verlassen' name=lvsys> verlassen</a>"
		));			
	} else {
		array_push($content,dropDownMenuOption(
			"<img src=".$gfx."/map/".$ship->map['type'].".gif width=15 height=15>&nbsp;".$ship->map['name']
		));		
	}
	array_push($content,dropDownMenuOption(
		"<img src=".$gfx."/buttons/icon/magnify.gif border=0 name=scs title=\"Sektor scannen\"> Spurenscan",
		"<a href=?p=ship&s=scs&id=".$_GET['id']." ".getHover("scscan","inactive/n/magnify","hover/g/magnify")."><img src=".$gfx."/buttons/inactive/n/magnify.gif border=0 title='Spurenscan' name=scscan> durchführen</a>"
	));			
	
	
	

	
	

	$menus['fieldinfo'] = dropDownMenu(4,"Feld-Informationen","mfld",$gfx."/buttons/icon/options.gif",$content,in_array("mfld",$open),"togglecall('".$ship->id."','mfld');");
	
	if ($ship->systems_id > 0)
	{
		$sys = $map->getsystembyid($ship->systems_id);
		$nbu = ($ship->sy-1 < 1 ? "value='-' disabled" : "value=".$ship->sx."|".($ship->sy-1));
		$nbl = ($ship->sx-1 < 1 ? "value='-' disabled" : "value=".($ship->sx-1)."|".$ship->sy);
		$nbr = ($ship->sx+1 > $sys['sr'] ? "value='-' disabled" : "value=".($ship->sx+1)."|".$ship->sy);
		$nbd = ($ship->sy+1 > $sys['sr'] ? "value='-' disabled" : "value=".$ship->sx."|".($ship->sy+1));
	}
	else
	{
		$nbu = ($ship->cy-1 < 1 ? "value='-' disabled" : "value=".$ship->cx."|".($ship->cy-1));
		$nbl = ($ship->cx-1 < 1 ? "value='-' disabled" : "value=".($ship->cx-1)."|".$ship->cy);
		$nbr = ($ship->cx+1 > $mapfields['max_x'] ? "value='-' disabled" : "value=".($ship->cx+1)."|".$ship->cy);
		$nbd = ($ship->cy+1 > $mapfields['max_y'] ? "value='-' disabled" : "value=".$ship->cx."|".($ship->cy+1));
	}
	
	

	$seb = $ship->checksectorbase();

	$nbsstring = "";
	$lf = -1;
	$fleetfaction = 0;

	if ($ship->nbs == 0) $nbsstring .= "";

	else {
		// $nbsstring .= "<table bgcolor=#262323 cellspacing=1 cellpadding=1  width=100
		
		$result = $map->nbs();
		while($ret=mysql_fetch_assoc($result)) $arr[] = $ret;
		function scmp(&$a, &$b)
		{
			global $blockade;
			if ($a['fleets_id'] == $b['fleets_id']) return 0;
			if ($a['fleets_id'] == $blockade['value']) return -1;
			if ($b['fleets_id'] == $blockade['value']) return 1;
			return ($a['fleets_id'] > $b['fleets_id']) ? -1 : 1;
		}
		@usort($arr, "scmp");
		// if ($seb != 0) {

		// }
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
					$currentfleetdata = $db->query("SELECT * FROM stu_fleets WHERE fleets_id = ".$data['fleets_id']." LIMIT 1;",4);
					
					$fleetfaction = $currentfleetdata['faction'];
					
					if ($fleetfaction > 0) {
						if ($lf != -1) $nbsstring .= "</table>";
						$nbsstring .= "<table class=\"suppressMenuColors race".$fleetfaction."\">";
						// $nbsstring .= "<th colspan=11 style=\"height:32px;vertical-align:middle;\"><table class=\"suppressMenuColors\" cellspacing=0 cellpadding=0><tr><th style=\"height:32px;width:32px;vertical-align:middle;\"><img src=".$gfx."/rassen/".$fleetfaction."kn.png width=32 height=32></th><th style=\"padding-left:10px; vertical-align:middle;\">Flotte</th></table></th>";
						// $nbsstring .= "<tr>
										// <td style=\"width:150px;\"></td>
										// <td style=\"\">Klasse</td>
										// <td style=\"width:400px;\">Rasse</td>
										// <td style=\"width:50px;text-align:center;\">ID</td>
										// <td style=\"text-align:center;\">Zustand</td>
										// <td style=\"text-align:center;\">S</td>
										// <td style=\"text-align:center;\">A</td>
										// <td style=\"text-align:center;\">Tr</td>
										// <td style=\"text-align:center;\">E</td>
										// <td style=\"text-align:center;\">Beamen</td>
										// <td style=\"text-align:center;\">N</td>
									  // </tr>";		
						$nbsstring .= "<th colspan=11 style=\"height:32px;vertical-align:middle;\"><table class=\"suppressMenuColors\" cellspacing=0 cellpadding=0><tr><th style=\"height:32px;width:32px;vertical-align:middle;\"><img src=".$gfx."/rassen/".$fleetfaction."kn.png width=32 height=32></th><th style=\"padding-left:10px; vertical-align:middle;\">".stripslashes($data['fname'])."</th></table></th>";
						$nbsstring .= "<tr>
										<th style=\"width:150px;\"></th>
										<th class=main>Name</th>
										<th style=\"width:400px;\">Besitzer</th>
										<th style=\"width:50px;text-align:center;\">ID</th>
										<th style=\"text-align:center;\">Zustand</th>
										<th style=\"text-align:center;\">S</th>
										<th style=\"text-align:center;\">A</th>
										<th style=\"text-align:center;\">Tr</th>
										<th style=\"text-align:center;\">E</th>
										<th style=\"text-align:center;\">Beamen</th>
										<th style=\"text-align:center;\">N</th>
									  </tr>";	
					} else {
						if ($lf != -1) $nbsstring .= "</table>";
						$nbsstring .= "<table class=\"suppressMenuColors race0\">";
						$nbsstring .= "<th colspan=11 style=\"padding-left:5px;height:32px;vertical-align:middle;\">".stripslashes($data['fname'])."</th>";
						$nbsstring .= "<tr>
										<th style=\"width:150px;\"></th>
										<th class=main>Name</th>
										<th style=\"width:400px;\">Besitzer</th>
										<th style=\"width:50px;text-align:center;\">ID</th>
										<th style=\"text-align:center;\">Zustand</th>
										<th style=\"text-align:center;\">S</th>
										<th style=\"text-align:center;\">A</th>
										<th style=\"text-align:center;\">Tr</th>
										<th style=\"text-align:center;\">E</th>
										<th style=\"text-align:center;\">Beamen</th>
										<th style=\"text-align:center;\">N</th>
									  </tr>";						
					}
					
					$lf = $data['fleets_id'];
				}
				
				
				
				if ($lf != $data['fleets_id'] && $data['fleets_id'] == 0 && $data['slots'] == 0)
				{
					$fleetfaction = 0;
					if ($lf != -1) $nbsstring .= "</table>";
					$nbsstring .= "<table class=\"suppressMenuColors black\" style=\"margin:10px;margin-bottom:4px;\">";
					$nbsstring .= "<tr>
									<th style=\"width:150px;\"></th>
									<th style=\"\">Name</th>
									<th style=\"width:400px;\">Besitzer</th>
									<th style=\"width:50px;text-align:center;\">ID</th>
									<th style=\"text-align:center;\">Zustand</th>
									<th style=\"text-align:center;\">S</th>
									<th style=\"text-align:center;\">A</th>
									<th style=\"text-align:center;\">Tr</th>
									<th style=\"text-align:center;\">E</th>
									<th style=\"text-align:center;\">Beamen</th>
									<th style=\"text-align:center;\">N</th>
								  </tr>";
					$lf = 0;
				}

				// if ($fleetfaction > 0) {
					// $racename = "";
					// if ($fleetfaction == 1) $racename = "<font color=#197DCB><b>Vereinte Föderation der Planeten</b></font>";
					// if ($fleetfaction == 2) $racename = "<font color=#107D08><b>Romulanisches Sternenimperium</b></font>";
					// if ($fleetfaction == 3) $racename = "<font color=#D01412><b>Klingonisches Imperium</b></font>";					
					
					// $nbsstring .= "<tr>";
					// $nbsstring .= "<td style=\"height:40px;width:150px;text-align:center;vertical-align:middle;\">".($data['user_id'] == $_SESSION['uid'] ? "<a href=?p=ship&s=ss&id=".$data['id']."><img src=".$gfx."/ships/".shipPic($data['rumps_id'],$data['cloak'],$data['trumfield'],0).".gif title=\"".ftit($data['cname'])."\" border=0></a>" : "<img src=".$gfx."/ships/".shipPic($data['rumps_id'],$data['cloak'],$data['trumfield'],0).".gif title=\"".stripslashes($data['cname'])."\">")."</td>";
					// $nbsstring .= "<td style=\"padding-left:4px;\">".stripslashes($data['cname'])."</td>";
					// $nbsstring .= "<td style=\"padding-left:4px;\">".stripslashes($racename)."</td>";					
				// } else {
					$nbsstring .= "<tr>";
					$nbsstring .= "<td style=\"height:40px;width:150px;text-align:center;vertical-align:middle;\">".($data['user_id'] == $_SESSION['uid'] ? "<a href=?p=ship&s=ss&id=".$data['id']."><img src=".$gfx."/ships/".shipPic($data['rumps_id'],$data['cloak'],$data['trumfield'],0).".gif title=\"".ftit($data['cname'])."\" border=0></a>" : "<img src=".$gfx."/ships/".shipPic($data['rumps_id'],$data['cloak'],$data['trumfield'],0).".gif title=\"".stripslashes($data['cname'])."\">")."</td>";
					$nbsstring .= "<td style=\"padding-left:4px;\">".stripslashes($data['name'])."</td>";
					$nbsstring .= "<td style=\"padding-left:4px;\">".stripslashes($data['user'])." ".($data['user_id'] < 101 ? "<b>NPC</b>" : "(".$data['user_id'].")")."</td>";
				// }

				
				

				if ($data['traktormode'] == 1) $tr = ">";
				elseif ($data['traktormode'] == 2) $tr = "<";
				else $tr = "";
				
				$nbsstring .= "<td style=\"width:50px;text-align:center;\">".$data['id']." ".$tr."</td>";
				$nbsstring .= "<td style=\"width:150px;text-align:center;\">".$data['huelle']."/".$data['max_huelle'].($data['schilde_status'] == 1 ? " (<font color=cyan>".$data['schilde']."</font>)" : "")."</td>";
				$nbsstring .= "<td style=\"width:20px;text-align:center;padding:4px;\">".($data['user_id'] == $_SESSION['uid'] && $ship->is_shuttle == 1 && $data['is_shuttle'] != 1 ? "<a href=?p=ship&s=ss&a=lansh&t=".$data['id']."&id=".$_GET['id']." ".getonm("sc".$i,"buttons/shu_l")."><img src=".$gfx."/buttons/shu_l1.gif border=0 name=sc".$i." title='Shuttle landen'></a>" : ($data['warp'] == 1 || $ship->warp == 1 || $data['cloak'] == 1 || $ship->cloak == 1 ? "-" : "<a href=?p=ship&s=sc&id=".$_GET['id']."&t=".$data['id']."&m=s ".getHover("sc".$i,"inactive/n/magnify","hover/w/magnify")."><img src=".$gfx."/buttons/inactive/n/magnify.gif border=0 name=sc".$i." title='Scan'></a>"))."</td>";

				$nbsstring .= "<td style=\"width:20px;text-align:center;padding:4px;\">".($ship->wea_phaser > 0 || $ship->wea_torp > 0 ? "<a href=?p=ship&s=ss&a=att&ps=".$_SESSION['pagesess']."&id=".$_GET['id']."&t=".$data['id']." ".getHover("ph".$i,"inactive/n/emitter","hover/r/".$attackicon)."><img src=".$gfx."/buttons/inactive/n/emitter.gif title='Angreifen' name=ph".$i." border=0></a>" : "-")."</td>";
				
				if ($data['is_hp'] || $data['cloak'] == 1) {
					$nbsstring .= "<td style=\"width:20px;text-align:center;padding:4px;\">-</td>";					
					$nbsstring .= "<td style=\"width:20px;text-align:center;padding:4px;\">-</td>";					
					$nbsstring .= "<td style=\"width:48px;text-align:center;padding:4px;\">-</td>";										
				} else {
					$nbsstring .= "<td style=\"width:20px;text-align:center;padding:4px;\">".($data['slots'] == 0 && $data['trumfield'] == 0 ? ($ship->traktor == $data['id'] && $ship->traktormode == 1 ? "<a href=?p=ship&s=ss&a=dt&id=".$_GET['id']."&t=".$data['id']." ".getHover("tr".$i,"active/g/tractor","hover/r/emitter")."><img src=".$gfx."/buttons/active/g/tractor.gif border=0 title='Traktorstrahl deaktivieren' name=tr".$i."></a>" : "<a href=?p=ship&s=ss&a=tr&id=".$_GET['id']."&t=".$data['id']." ".getHover("tr".$i,"inactive/n/emitter","hover/w/tractor")."><img src=".$gfx."/buttons/inactive/n/emitter.gif border=0 title='Traktorstrahl aktivieren' name=tr".$i."></a>") : "-")."</td>";
					$nbsstring .= "<td style=\"width:20px;text-align:center;padding:4px;\"><a href=\"javascript:void(0);\" onClick=\"ship_etransfer(".$_GET['id'].",".$data['id'].");\" ".getHover("et".$i,"inactive/n/energyplus","hover/g/energyplus")."><img src=".$gfx."/buttons/inactive/n/energyplus.gif title='Energie zur ".ftit($data['name'])." transferieren' border=0 name=et".$i."></a></td>";					
					$nbsstring .= "<td style=\"width:48px;text-align:center;padding:4px;\"><a href=?p=ship&s=be&m=to&id=".$_GET['id']."&t=".$data['id']." ".getHover("bt".$i,"inactive/n/nbeam_d","hover/w/beam_d")."><img src=".$gfx."/buttons/inactive/n/nbeam_d.gif name=bt".$i." title='Zu der ".ftit($data['name'])." beamen' border=0></a>&nbsp;&nbsp;<a href=?p=ship&s=be&m=fr&id=".$_GET['id']."&t=".$data['id']." ".getHover("bf".$i,"inactive/n/nbeam_u","hover/w/beam_u")."><img src=".$gfx."/buttons/inactive/n/nbeam_u.gif name=bf".$i." title='Von der ".ftit($data['name'])." beamen' border=0></a></td>";					
				}
				
				
				$nbsstring .= "<td style=\"width:20px;text-align:center;padding:4px;\"><a href=\"javascript:void(0);\" onClick=\"open_pm_window(".$data['user_id'].",".$_GET['id'].",".$data['id'].");\" ".getHover("pm".$i,"inactive/n/mail","hover/w/mail")."><img src=".$gfx."/buttons/inactive/n/mail.gif name=pm".$i." title='Eine PM an die ".ftit($data['name'])." schicken' border=0></a></td></tr>";
			
			
			}
			
			if ($cl > 0) {
				if (!($i > 1)) $nbsstring .= "<table class=\"suppressMenuColors black\" style=\"margin:10px;margin-bottom:4px;\">";
				$nbsstring .= "<tr><td colspan=11>Getarnte Objekte</td></tr>";
				$nbsstring .= "<tr><td></td><td>Unbekannt</td><td><b>?</b></td><td/><td/><td><center><a href=?p=ship&s=ss&id=".$_GET['id']."&a=clsn ".getHover("cloakscan","inactive/n/magnify","hover/r/magnify")."><img src=".$gfx."/buttons/inactive/n/magnify.gif border=0 name=cloakscan title='Scan'></a></center></td><td colspan=5></td></tr>";
			}
			$nbsstring .= "</table>";
		}

	}
	
	
	




	//<a href=?p=ship&s=ss&a=dv&d=nbs&id=".$_GET['id']." ".getronm("kss","buttons/kss")."><img src=".$gfx."/buttons/kss2.gif name=kss title='Nahbereichssensoren deaktivieren' border=0></a>
	$content = array();
	
	if ($ship->nbs > 0) {		
		array_push($content,dropDownMenuOption(
			$nbsstring
		));
		array_push($content,dropDownMenuOption(
			"<center><input type=hidden name=d value=nbs><input type=submit name=c value=\"Sensoren deaktivieren\" class=button> </center>",
			0,
			array('p' => "ship", "s" => "ss", "a" => "dv", "id" => $ship->id)
		));			
	} else {
		array_push($content,dropDownMenuOption(
			"<center><input type=hidden name=d value=nbs><input type=submit name=c value=\"Sensoren aktivieren\" class=button> </center>",
			0,
			array('p' => "ship", "s" => "ss", "a" => "av", "id" => $ship->id)
		));	
	}
	$menus['nbs'] = dropDownMenu(2,"Schiffe und Objekte","mnbs",$gfx."/buttons/icon/scan.gif",$content,in_array("mnbs",$open),"togglecall('".$ship->id."','mnbs');");
	
	$trademenu = "";
	$tradedata = $ship->getTradeNetwork();
	// print_r($tradedata);
	if ($tradedata['network_id'] > 0) {
	
	
		$content = array();
	
	
		
	
		$tradestring .= "<table bgcolor=#262323 cellspacing=1 cellpadding=1  width=100%>
		<tr><th></th><th>Name</th><th>Betreiber</th><th align=center>Beamen</th></tr>";

	
		$tradestring .= "<tr>";
		$tradestring .= "<td><img src=".$gfx."/ships/".$tradedata['shipdata']['rumps_id'].".gif></td>";
		$tradestring .= "<td>".$tradedata['name']."</td>";
		$tradestring .= "<td>".$tradedata['username']." ".($data['user_id'] < 101 ? "<b>NPC</b>" : "(".$data['user_id'].")")."</td>";
		$tradestring .= "<td style=\"width:100px;text-align:center;\"><a href=?p=ship&s=bewb&m=to&id=".$_GET['id']."&t=".$tradedata['network_id']." ".getHover("btwb","inactive/n/nbeam_d","hover/w/beam_d")."><img src=".$gfx."/buttons/inactive/n/nbeam_d.gif name=btwb title='Zu Warenbörse beamen' border=0></a>&nbsp;<a href=?p=ship&s=bewb&m=fr&id=".$_GET['id']."&t=".$tradedata['network_id']." ".getHover("bfwb","inactive/n/nbeam_u","hover/w/beam_u")."><img src=".$gfx."/buttons/inactive/n/nbeam_u.gif name=bfwb title='Aus Warenbörse beamen' border=0></a></td>";
		
		
		
		
		
		$tradestring .= "</tr>";
	
	
		$tradestring .= "</table>";
	
	
		array_push($content,dropDownMenuOption($tradestring));
		$menus['trade'] = dropDownMenu(1,"Warenbörse","mtrd",$gfx."/buttons/icon/trade.gif",$content,in_array("mtrd",$open),"togglecall('".$ship->id."','mtrd');");		
	
	}
	
	$special = $ship->m->getspecial($ship->cx,$ship->cy,$ship->systems_id);
	if ($special) {
		
		$content = array();
		
		$specialstring = "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=100%><tr>";
		$specialname = "Unbekanntes Phänomen";
		if ($special['type'] == "wormhole") 
			$specialname = "Stabiles Wurmloch";
		
		$specialstring .= "<td style=\"width:120px;height:55px;text-align:center;vertical-align:middle;\"><img src=".$gfx."/map/".$special['type'].".png border=0 title='".$$specialnamename."'/></td>";
		
		$specialstring .= "<td>".$specialname."</td>";
		
		if ($special['type'] == "wormhole") 
			$specialstring .= "<td width=150 style=\"text-align:center;\"><a href=?p=ship&s=ss&id=".$_GET['id']."&a=enterworm ".getHover("enterworm","inactive/n/warp","hover/g/warp")."><img src=".$gfx."/buttons/inactive/n/warp.gif border=0 title='Einfliegen' name=enterworm> einfliegen</a></td>";		
		
		$specialstring .= "</tr></table>";
		array_push($content,dropDownMenuOption(
			$specialstring
		));
		$menus['anomaly'] = dropDownMenu(2,"Anomalie","mano",$gfx."/buttons/icon/info.gif",$content,in_array("mano",$open),"togglecall('".$ship->id."','mano');");	
		
	}
	
	if ($ship->map['cid'] > 0)
	{
		// 
		// $img = "<img src=".$gfx."/planets/".$ship->map['cid'].($col['schilde_status'] == 1 ? "s" : "").".gif width=15 heigth=15 border=0 onmouseover=\"return overlib('<table class=tcal><th>Kolonieinformationen</th><tr><td>Kolonie: ".str_replace("'","",stripslashes($col['name']))." (".$col['id'].")<br>Besitzer: ".str_replace("'","",stripslashes($col['user']))." (".$col['user_id'].")</td></tr></table>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER);\" onmouseout=\"nd();\">";
		// if ($col['user_id'] != 1) $col['user_id'] == $_SESSION['uid'] ? print("<a href=?p=colony&s=sc&id=".$col['id']."&shd=".$_GET['id'].">".$img."</a>") : print($img);
		// else echo "<img src=".$gfx."/planets/".$ship->map['cid'].".gif width=15 heigth=15 title='Besitzer: Niemand'>";
		// echo " <a href=?p=ship&s=cac&id=".$_GET['id']."&t=".$col['id']." ".getonm("csc","buttons/lupe")."><img src=".$gfx."/buttons/lupe1.gif name=csc title='Kolonie-Aktionen' border=0></a>
		 // 
		 // 
		 // <a href=?p=ship&s=sht&id=".$_GET['id']."&t=".$col['id']." ".getonm("cft","buttons/fergtrade")."><img src=".$gfx."/buttons/fergtrade1.gif name=cft border=0 title=\"Warenangebot\"></a>";
		// if ($col['user_id'] == 1 && $ship->plans_id == 1) echo "<br><a href=?p=ship&s=col&id=".$_GET['id']."&cid=".$col['id']." ".getonm('colz','buttons/colo')."><img src=".$gfx."/buttons/colo1.gif title=\"Kolonisieren\" name=colz border=0> Kolonisieren</a>";
		$col = $db->query("SELECT a.id,a.name,a.user_id,a.schilde_status,a.planet_name,a.quest_involved,b.user,c.name as cname FROM stu_colonies as a LEFT JOIN stu_user as b ON a.user_id=b.id LEFT JOIN stu_colonies_classes as c ON a.colonies_classes_id=c.colonies_classes_id WHERE a.systems_id=".$ship->systems_id." AND a.sx=".$ship->sx." AND a.sy=".$ship->sy." LIMIT 1",4);
		$content = array();
		
		$colstring = "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=100%>
		<tr><th></th><th>Name</th><th>Siedler</th><th>ID</th><th align=center width=18>S</th><th align=center width=18>E</th><th align=center>Beamen</th><th align=center>N</th></tr>";
		
		$colstring .= "<tr>";
		
		if ($col['user_id'] == $_SESSION['uid']) {
			$colstring .= "<td style=\"width:120px;\"><center><a href=?p=colony&s=sc&id=".$col['id']."&shd=".$_GET['id']."><img src=".$gfx."/planets/".$ship->map['cid'].".gif title=\"".$col[cname]."\"></a></center></td>";
			$colstring .= "<td><a href=?p=colony&s=sc&id=".$col['id']."&shd=".$_GET['id'].">".$col[name]."</a></td>";			
		} else {
			if (($ship->map['cid'] == 231) || ($ship->map['cid'] == 232)  || ($ship->map['cid'] == 233) )
				$colstring .= "<td style=\"width:120px;\"><img style='background-repeat: no-repeat;background-position: center;background-image: url(\"".$gfx."/planets/".$ship->map['cid'].".gif"."\");' src=".$gfx."/planets/".$ship->map['cid']."r.png title=\"Klasse J Planet\"></td>";
			else 
				$colstring .= "<td style=\"width:120px;\"><center><img src=".$gfx."/planets/".$ship->map['cid'].".gif title=\"".$col[cname]."\"></center></td>";
			$colstring .= "<td>".$col[name]."</td>";
		}
		
		$colstring .= "<td>".$col[user]."</td>";
		$colstring .= "<td>".$col[id]."</td>";
		$colstring .= "<td><a href=?p=ship&s=cac&id=".$_GET['id']."&t=".$col['id']." ".getHover("csc","inactive/n/magnify","hover/w/magnify")."><img src=".$gfx."/buttons/inactive/n/magnify.gif name=csc title='Kolonie-Aktionen' border=0></a></td>";
		$colstring .= "<td><a href=\"javascript:void(0);\" onClick=\"col_etransfer(".$_GET['id'].",".$col['id'].");\" ".getHover("cet","inactive/n/energyplus","hover/g/energyplus")."><img src=".$gfx."/buttons/inactive/n/energyplus.gif name=cet title='Energietransfer' border=0></a></td>";
		$colstring .= "<td><a href=?p=ship&s=bec&id=".$_GET['id']."&m=to&t=".$col['id']." ".getHover("cbt","inactive/n/nbeam_d","hover/w/beam_d")."><img src=".$gfx."/buttons/inactive/n/nbeam_d.gif name=cbt title=\"Zur Kolonie beamen\" border=0></a> <a href=?p=ship&s=bec&id=".$_GET['id']."&m=fr&t=".$col['id']." ".getHover("cbf","inactive/n/nbeam_u","hover/w/beam_u")."><img src=".$gfx."/buttons/inactive/n/nbeam_u.gif name=cbf title='Von der Kolonie beamen' border=0></a></td>";
		$colstring .= "<td><a href=\"javascript:void(0);\" onClick=\"open_pm_window(".$col['user_id'].",0,0);\" ".getHover("comsg","inactive/n/mail","hover/w/mail")."><img src=".$gfx."/buttons/inactive/n/mail.gif name=comsg border=0 title=\"Eine PM an ".ftit($col['user'])." schicken\"></a></td>";
		
		
		$colstring .= "</tr>";
		
		if (($ship->mod_s1 == 7001) && ($col['user_id'] == 1) && ($ship->map['cid'] <= 220 || $ship->map['cid'] >= 240)) {
			$colstring .= "<tr><td colspan=8 style=\"height:30px;\"><center><a href=?p=ship&s=col&id=".$_GET['id']."&cid=".$col['id']." ".getHover('colz','inactive/n/planet',"hover/g/planet")."><img src=".$gfx."/buttons/inactive/n/planet.gif title=\"Kolonisieren\" name=colz border=0> Kolonisieren</a></center></td></tr>";
		}
		
		$colstring .= "</table>";
		array_push($content,dropDownMenuOption(
			$colstring
		));
		$menus['colony'] = dropDownMenu(3,"Kolonie","mcol",$gfx."/buttons/icon/planet.gif",$content,in_array("mcol",$open),"togglecall('".$ship->id."','mcol');");		
	}
		
	$lssstring = "";
	if ($ship->lss != 0) {
		// if ($_SESSION['level'] == 1) $lssstring .= "<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr><th><a href=?p=main&s=fm>Liste kolonisierbarer Planeten</a></th></tr></table><br>";
		$lssstring .= "<table class=\"mapfields suppressMenuColors\"><tr><th class=\"fieldlegend\" align=center></th>";
		// $ship->systems_id > 0 ? $result = $map->getkss($ship->sx,$ship->sy,$ship->kss_range,$ship->systems_id) : $result = $map->getlss($ship->cx,$ship->cy,$ship->lss_range);
		
		$result = $ship->getSensorData($ship->lssmode);

		for ($i=($ship->systems_id > 0 ? $ship->sx-$ship->kss_range : $ship->cx-$ship->lss_range);$i<=($ship->systems_id > 0 ? $ship->sx+$ship->kss_range : $ship->cx+$ship->lss_range);$i++) if ($i > 0 && $i <= ($ship->systems_id > 0 ? $sys['sr'] : $mapfields['max_x'])) $lssstring .= "<th class=\"fieldlegend\" align=center>".$i."</th>";
		
		foreach($result['fields'] as $field) {
			if ($field['y'] != $yd) { $lssstring .= "</tr><tr><th class=\"fieldlegend\" align=center>".$field['y']."</th>"; $yd = $field['y']; }
			if (!$yd) $yd = $field['y'];

			$lssstring .= "<td class=".$field['class']." ".($field['onclick'] ? "onclick=\"".$field['onclick']."\"" : "")." style=\"background-image:url('".$gfx."/map/".$field['type'].".gif');".($result['desaturated'] ? "background-blend-mode: luminosity;" : "")."\" >".$field['display']."</td>";
			$border = "";
			$cloak = "";
		}

		$lssstring .= "</tr></table>";
	}
	
	
	
	function modename($mode) {
		if ($mode == "ship") return "Schiffssignaturen";
		if ($mode == "subspace") return "Subraum";
		if ($mode == "chroniton") return "Chroniton";
		if ($mode == "borders") return "Politisch";
	}
	
	$content = array();
	
	if ($ship->lss > 0) {		
		array_push($content,dropDownMenuOption(
			"<center>".$lssstring."</center>"
		));
		array_push($content,dropDownMenuOption(
			"<img src=".$gfx."/buttons/icon/scanarea.gif border=0 name=scs title=\"Sensorenphalanx\"> Sensorenphalanx",
			"<a href=?p=ship&s=ss&id=".$_GET['id']."&a=dv&d=lss ".getHover("dblss","active/g/scanarea","hover/r/scanarea")."><img src=".$gfx."/buttons/active/g/scanarea.gif border=0 title='Sensorenphalanx' name=dblss> deaktivieren</a>"
		));	
		
		$scanoptions = "";
		foreach($ship->getAllowedLSSModes() as $mode) {
			$scanoptions .= "<option value=\"".$mode."\" ".($ship->lssmode == $mode ? "selected" : "").">".modename($mode)."</option>";
		}

		
		array_push($content,dropDownMenuOption(
			"<img src=".$gfx."/buttons/icon/modules.gif border=0 title=\"Sensorenphalanx\"> Scanmodus",
			"<select id=\"lssmode\" name=\"lssmode\" onchange=\"switchSensor(this.value);\">".$scanoptions."</select>"
		));			
	} else {
		
	array_push($content,dropDownMenuOption(
		"<center><table bgcolor=#262323 cellspacing=1 cellpadding=1 width=145>
	<form action=main.php method=\"post\"><input type=hidden name=ps value=".$_SESSION['pagesess']."><input type=hidden name=p value=ship><input type=hidden name=s value=ss><input type=hidden name=id value=".$_GET['id']."><input type=hidden name=a value=mv>
	  <tr>
	    <td align=center colspan=3 style=\"width:180px;height:30px;\">
	      <input type=submit name=u ".$nbu." class=button style=\"width:60px;height:30px;\">
	    </td>
	  </tr>
	  <tr>
	    <td align=center style=\"width:60px;height:30px;\">
	      <input type=submit name=l ".$nbl." class=button style=\"width:60px;height:30px;\">
	    </td>
	    <td align=center style=\"width:60px;height:30px;\">
	      <input type=text size=7 name=c class=text value=".($ship->systems_id > 0 ? $ship->sx."|".$ship->sy : $ship->cx."|".$ship->cy)." onFocus=\"javascript:if(this.value=='".($ship->systems_id > 0 ? $ship->sx."|".$ship->sy : $ship->cx."|".$ship->cy)."'){this.value=''};\" onBlur=\"javascript:if(this.value==''){this.value='".($ship->systems_id > 0 ? $ship->sx."|".$ship->sy : $ship->cx."|".$ship->cy)."'};\" style='text-align:center;width:60px;height:30px;\"'>
	    </td>
	    <td align=center style=\"width:60px;height:30px;\">
	      <input type=submit name=r ".$nbr." class=button style=\"width:60px;height:30px;\">
	    </td>
	  </tr>
	  <tr>
	    <td colspan=3 align=center style=\"width:180px;height:30px;\">
	      <input type=submit name=d ".$nbd." class=button style=\"width:60px;height:30px;\">
	    </td>
	  </tr>
	</form>
	</table></center>"
	));	
		array_push($content,dropDownMenuOption(
			"<img src=".$gfx."/buttons/icon/scanarea.gif border=0 title=\"Sensorenphalanx\"> Sensorenphalanx",
			"<a href=?p=ship&s=ss&id=".$_GET['id']."&a=av&d=lss ".getHover("dblss","inactive/n/scanarea","hover/g/scanarea")."><img src=".$gfx."/buttons/inactive/n/scanarea.gif border=0 title='Sensorenphalanx' name=dblss> aktivieren</a>"
		));	

		
	}
	if ($ship->systems_id > 0) $menus['lss'] = dropDownMenu(1,"Navigation & Kurzstreckensensoren","mlss",$gfx."/buttons/icon/map.gif",$content,in_array("mlss",$open),"togglecall('".$ship->id."','mlss');");
	else $menus['lss'] = dropDownMenu(1,"Navigation & Langstreckensensoren","mlss",$gfx."/buttons/icon/map.gif",$content,in_array("mlss",$open),"togglecall('".$ship->id."','mlss');");
	
	
	$stor = $ship->getshipstorage($ship->id);
	$storsum = $ship->getshipstoragesum($ship->id);
	$i = 0;
	$s = "<table bgcolor=#262323 cellspacing=1 cellpadding=1  width=100%>";
	
	// $s .= "<tr><td colspan=1><img src=".$gfx."/icons/storage.gif>Laderaum</td><td colspan=3>&nbsp;</td></tr>";
	
	$empty = true;
	while ($sd=mysql_fetch_assoc($stor))
	{
		$empty = false;
		if ($i == 0) $s .= "<tr>";
		$s .= "<td width=200><img src=".$gfx."/goods/".$sd['goods_id'].".gif title=\"".$sd['name']."\"> ".$sd['name']."</td>";
		
		
		$s .= "<td>&nbsp;".$sd['count']."</td>";
		$i++;
		if ($i == 2) { $s .= "</tr>"; $i = 0; }
		$cn += $sd['count'];
	}
	if ($i == 1) $s .= "<td></td></tr>";
	
	if ($empty) $s .= "<tr><td colspan=4><img src=".$gfx."/icons/storage.gif> Leer</td></tr>";
	
	if (!$cn) $cn = 0;
	$s .= "</table>";
	

	
	$content = array();
	array_push($content,dropDownMenuOption(
		$s
	));
	$menus['storage'] = dropDownMenu(1,"Laderaum (".$storsum."/".$ship->storage.")","msto",$gfx."/buttons/icon/storage.gif",$content,in_array("msto",$open),"togglecall('".$ship->id."','msto');");
	
	$menus['shipdata'] = dropDownMenu(4,"Schiffsdaten","mshp",$gfx."/buttons/icon/ship.gif",$shipdatacontent,in_array("mshp",$open),"togglecall('".$ship->id."','mshp');");
	$menus['refresh'] = dropDownMenu(3,"Aktualisieren","mref",$gfx."/buttons/icon/time.gif",array("<tr style=\"height:30px;\"><td><center><a href=?p=ship&s=ss&id=".$_GET['id'].">aktualisieren</a></center></td></tr>"),in_array("mref",$open),"togglecall('".$ship->id."','mref');");
	

	echo "<table class=tablelayout>";
		
	echo "<tr>";
		echo "<td class=tablelayout colspan=5 style=\"vertical-align:top;\">".$menus['shipdata']."</td>";
		echo "<td class=tablelayout colspan=1 style=\"vertical-align:top;\">".$menus['refresh']."</td>";
	echo "</tr>";	
	// echo "<tr><td colspan=6>&nbsp;</td></tr>";
	
	echo "<tr>";
	echo 	"<td class=tablelayout colspan=2 style='vertical-align:top;'>".$menus['mods']."</td>";
	echo 	"<td class=tablelayout colspan=2 style='vertical-align:top;'>".$menus['fight']."</td>";
	echo 	"<td class=tablelayout colspan=2 style='vertical-align:top;'>".$menus['fleet']."</td>";
	echo "</tr>";
	echo "<tr>";
	echo 	"<td class=tablelayout colspan=2 style='vertical-align:top;'>".$menus['warp']."</td>";
	echo 	"<td class=tablelayout colspan=2 style='vertical-align:top;'>".$menus['special']."</td>";
	echo 	"<td class=tablelayout colspan=2 style='vertical-align:top;'></td>";
	echo "</tr>";
	echo "<tr>";
	echo 	"<td class=tablelayout colspan=2 style='vertical-align:top;'>".$menus['shield']."</td>";
	echo 	"<td class=tablelayout colspan=2 style='vertical-align:top;'>".$menus['collect'].$menus['labs']."</td>";
	echo 	"<td class=tablelayout colspan=2 style='vertical-align:top;'></td>";
	echo "</tr>";
	
	if ($menus['cloak']) {
		echo "<tr>";
		echo 	"<td class=tablelayout colspan=2 style='vertical-align:top;'>".$menus['cloak']."</td>";
		echo 	"<td class=tablelayout colspan=2 style='vertical-align:top;'></td>";
		echo 	"<td class=tablelayout colspan=2 style='vertical-align:top;'></td>";
		echo "</tr>";
	}
	
	// echo "<tr><td colspan=6>&nbsp;</td></tr>";

	echo "<tr>";
	echo 	"<td class=tablelayout colspan=2 style='vertical-align:top;'>".$menus['lss']."</td>";
	echo 	"<td class=tablelayout colspan=2 style='vertical-align:top;'>".$menus['fieldinfo']."</td>";
	echo 	"<td class=tablelayout colspan=2 style='vertical-align:top;'></td>";
	echo "</tr>";
	
	// echo "<tr><td colspan=6>&nbsp;</td></tr>";

	if ($menus['anomaly']) {
		echo "<tr><td class=tablelayout colspan=6>".$menus['anomaly']."</td><td>&nbsp;</td></tr>"; 
		// echo "<tr><td colspan=6>&nbsp;</td></tr>";
	}
	if ($menus['trade'])
	{
		echo "<tr><td class=tablelayout colspan=6>".$menus['trade']."</td><td>&nbsp;</td></tr>";
		// echo "<tr><td colspan=6>&nbsp;</td></tr>";
	}	
	if ($menus['colony'])
	{
		echo "<tr><td class=tablelayout colspan=6>".$menus['colony']."</td></tr>";
		// echo "<tr><td colspan=6>&nbsp;</td></tr>";
	}
	
	

	echo "<tr><td class=tablelayout colspan=6>".$menus['nbs']."</td></tr>";
	// echo "<tr><td colspan=6>&nbsp;</td></tr>";
	echo "<tr><td class=tablelayout colspan=6>".$menus['storage']."</td></tr>";
	
	
	echo "<tr style=\"height:1px;margin:0px;\"><td width=250></td><td width=250></td><td width=250></td><td width=250></td><td width=250></td><td width=250></td></tr>";
	
	echo "</table>";
	
	
	
	
	
	// echo "<table width=600><tr><td valign=top>";
	// if ($ship->m5 > 0 || $ship->m11 > 0)
	// {
		// echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=300><tr><th colspan=2><img src=".$gfx."/buttons/warpsys.gif title=\"Warpsystem\"> Warp-System</th></tr>
		// <form action=main.php><input type=hidden name=p value=ship><input type=hidden name=s value=ss><input type=hidden name=a value=lwk><input type=hidden name=id value=".$_GET['id'].">";
		// if ($ship->m5 > 0) echo "<tr><td width=150><img src=".$gfx."/buttons/warpk.gif title='Warpkern'> Warpkern: ".$ship->warpcore."</td><td valign=middle><input type=submit name=c value=laden class=button> <input type=submit name=c value=max class=button></td></tr>";
		// if ($ship->m11 > 0) echo "<tr><td><img src=".$gfx."/buttons/".($ship->warp == 1 ? "warp2" : "warp1").".gif title='Warpantrieb'> Warpantrieb </td><td>".($ship->warp == 1 ? "<input type=submit class=button name=a value=Deaktivieren>" : "<input type=submit class=button name=a value=Aktivieren".($ship->systems_id > 0 ? " disabled" : "").">")."</td></tr>";
		// echo "</table></form>";
	// }
	// if ($ship->is_shuttle != 1)
	// {
		// echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=300><tr><th><img src=".$gfx."/buttons/batt.gif title='Ersatzbatterie'> Ersatzbatterie (".$ship->batt.")</th></tr><form action=main.php><input type=hidden name=p value=ship><input type=hidden name=s value=ss><input type=hidden name=a value=eb><input type=hidden name=id value=".$_GET['id'].">
		// <tr><td width=175>".($ship->batt_wait > 0 ? "Gesperrt bis: ".date("d.m.Y H:i",$ship->batt_wait)."" : "<input type=text size=2 name=c class=text".($ship->batt == 0 ? " disabled" : "")."> <input type=submit value=entladen class=button".($ship->batt == 0 ? " disabled" : "")."> <input type=submit value=max name=c class=button".($ship->batt == 0 ? " disabled" : "").">")."</td>
		// </tr></form></table>";
	// }
	// echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=300><tr><th colspan=2><img src=".$gfx."/buttons/shld.gif title='Schilde'> Schilde (".$ship->schilde."/".$ship->max_schilde.")</th></tr>
	// <form action=main.php><input type=hidden name=p value=ship><input type=hidden name=s value=ss><input type=hidden name=a value=lsh><input type=hidden name=id value=".$_GET['id'].">
	// <tr><td>".($ship->schilde_status > 1 && time() < $ship->schilde_status ? "Polarisiert bis ".date("d.m H:i",$ship->schilde_status) : ($ship->schilde_status == 0 || ($ship->schilde_status > 1 && time() > $ship->schilde_status) ? "<a href=?p=ship&s=ss&a=av&d=sh&id=".$_GET['id']." ".getonm('shi','buttons/shldac')."><img src=".$gfx."/buttons/shldac1.gif border=0 title='Schilde aktivieren' name=shi> aktivieren</a>" : "<a href=?p=ship&s=ss&a=dv&d=sh&id=".$_GET['id']." onmouseover=cp('shi','buttons/shldac1') onmouseout=cp('shi','buttons/shldac2')><img src=".$gfx."/buttons/shldac2.gif border=0 title='Schilde deaktiveren' name=shi> deaktivieren</a>"))."</td>
	// <td><input type=text size=2 name=c class=text".($ship->schilde_status == 1 ? " disabled" : "")."> <input type=submit value=laden class=button".($ship->schilde_status == 1 ? " disabled" : "")."> <input type=submit name=c value=max class=button".($ship->schilde_status == 1 ? " disabled" : "")."></td></tr></form></table>";




	// if ($ship->cloakable == 1)
	// {
		// echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=300><tr><th colspan=2><img src=".$gfx."/buttons/tarnv.gif title='Tarnung'> Tarnung</th></tr>
		// <tr><td>".($ship->cloak > 1 && time() < $ship->cloak ? "Baut Chronitonen ab bis ".date("d.m H:i",$ship->cloak) : ($ship->cloak == 0 || ($ship->cloak > 1 && time() > $ship->cloak) ? "<a href=?p=ship&s=ss&a=av&d=cl&id=".$_GET['id']." ".getonm('clo','buttons/tarn')."><img src=".$gfx."/buttons/tarn1.gif border=0 title='Tarnung aktivieren' name=clo> aktivieren</a>" : "<a href=?p=ship&s=ss&a=dv&d=cl&id=".$_GET['id']." onmouseover=cp('clo','buttons/tarn1') onmouseout=cp('clo','buttons/tarn2')><img src=".$gfx."/buttons/tarn2.gif border=0 title='Tarnung deaktiveren' name=clo> deaktivieren</a>"))."</td>
		// <td></td></tr></form></table>";
	// }
	// if ($ship->traktor > 0)
	// {
		// echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=300><tr><th colspan=2><img src=".$gfx."/buttons/trak.gif title='Traktorstrahl'> Traktorstrahl</th></tr>
		// <tr><td>".vtrak($ship->traktor)."<br>";
		// if ($ship->traktormode == 1) echo "<a href=?p=ship&s=ss&a=dt&id=".$_GET['id']." onmouseover=cp('tra','buttons/trak1') onmouseout=cp('tra','buttons/trak2')><img src=".$gfx."/buttons/trak2.gif border=0 title='Traktorstrahl deaktivieren' name=tra> deaktivieren</a>";
		// else echo "Schiff wird von Traktorstrahl gehalten<br><a href=?p=ship&s=ss&id=".$_GET['id']."&a=sfbi ".getonm('fbip','buttons/trak')."><img src=".$gfx."/buttons/trak1.gif border=0 title=\"Feedback Impuls aussenden\" name=fbip> Feedback-Impuls aussenden</a>";
		// echo "</td></tr></table>";
	// }
	// if (($ship->map['type'] == 11 || $ship->map['type'] == 12) && $ship->erz > 0 && $ship->warp != 1)
	// {
		// echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=300><form action=main.php><input type=hidden name=p value=ship><input type=hidden name=s value=ss><input type=hidden name=a value=ce><input type=hidden name=id value=".$_GET['id'].">
		// <tr><th colspan=2><img src=".$gfx."/map/".$ship->map['type'].".gif title='".$ship->map['name']."' width=15 height=15> Erze sammeln</th></tr>
		// <tr><td><select name=c".($ship->eps < 3 ? " disabled" : "").">";
		// for($i=1;$i<=floor($ship->eps/3);$i++) echo "<option value=".($i*3).">".($i*3);
		// echo "</select> <input type=submit value=sammeln class=button".($ship->eps == 0 ? " disabled" : "")."> <input type=submit name=c value=max class=button".($ship->eps == 0 ? " disabled" : "").">".($ship->fsf == $_GET['id'] ? " Flotte: <input type=\"checkbox\" name=\"fld\" value=1>" : "")."</td></tr></form></table>";
	// }
	// if ($ship->map['deut'] > 0 && $ship->bussard > 0 && $ship->warp != 1)
	// {
		// echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=300><form action=main.php><input type=hidden name=p value=ship><input type=hidden name=s value=ss><input type=hidden name=a value=cd><input type=hidden name=m value=5><input type=hidden name=id value=".$_GET['id'].">
		// <tr><th colspan=2><img src=".$gfx."/goods/5.gif title='Deuterium'> Deuterium (".$ship->bussard."/".$ship->map['deut'].")</th></tr>
		// <tr><td><input type=text size=2 name=c class=text".($ship->eps == 0 ? " disabled" : "")."> <input type=submit value=sammeln class=button".($ship->eps == 0 ? " disabled" : "")."> <input type=submit name=c value=max class=button".($ship->eps == 0 ? " disabled" : "").">".($ship->fsf == $_GET['id'] ? " Flotte: <input type=\"checkbox\" name=\"fld\" value=1>" : "")."</td></tr></form></table>";
	// }
	// if ($ship->map['type'] == 6 && $ship->bussard > 0 && $ship->warp != 1)
	// {
		// echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=300><form action=main.php><input type=hidden name=p value=ship><input type=hidden name=s value=ss><input type=hidden name=a value=cd><input type=hidden name=m value=7><input type=hidden name=id value=".$_GET['id'].">
		// <tr><th colspan=2><img src=".$gfx."/goods/7.gif title='Plasma'> Plasma (".$ship->bussard."/".round($ship->bussard/3).")</th></tr>
		// <tr><td><input type=text size=2 name=c class=text".($ship->eps == 0 ? " disabled" : "")."> <input type=submit value=sammeln class=button".($ship->eps == 0 ? " disabled" : "")."> <input type=submit name=c value=max class=button".($ship->eps == 0 ? " disabled" : "").">".($ship->fsf == $_GET['id'] ? " Flotte: <input type=\"checkbox\" name=\"fld\" value=1>" : "")."</td></tr></form></table>";
	// }
	// if (($ship->map['type'] == 11 || $ship->map['type'] == 12) && $ship->m10 == 657 && $ship->warp != 1)
	// {
		// $ed = $ship->geterzfeld();
		// echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=300>
		// <tr><th colspan=2><img src=".$gfx."/map/".$ship->map['type'].".gif title='".$ship->map['name']."' width=15 height=15> Zusammensetzung</th></tr>
		// <tr><td><img src=".$gfx."/goods/20.gif title=\"Iridium\"> ".$ed['chance_20']." <img src=".$gfx."/goods/21.gif title=\"Kelbonit\"> ".$ed['chance_21']."&nbsp;&nbsp;<img src=".$gfx."/goods/22.gif title=\"Nitrium\"> ".$ed['chance_22']."&nbsp;&nbsp;<img src=".$gfx."/goods/23.gif title=\"Magnesit\"> ".$ed['chance_23']."&nbsp;&nbsp;<img src=".$gfx."/goods/24.gif title=\"Talgonit\"> ".$ed['chance_24']."&nbsp;&nbsp;<img src=".$gfx."/goods/25.gif title=\"Galazit\"> ".$ed['chance_25']."</td></tr></table>";
	// }
	// if ($ship->stellar == 1 && $ship->systems_id > 0)
	// {
		// $check = $ship->checkIfIscartogryphed($ship->systems_id);
		// echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=300><th><img src=".$gfx."/buttons/map1.gif title=\"Kartographierung\"> Kartographierung</th>
		// <tr><td>";
		// if ($check == 0)
		// {
			// if ($ship->still == 0) echo "<a href=?p=ship&s=ss&id=".$_GET['id']."&a=kat ".getonm('stel','buttons/map')."><img src=".$gfx."/buttons/map1.gif name=stel border=0 title=\"System kartografieren\"> System kartographieren</a>";
			// else echo "Dauert an bis ca. ".date("d.m H:i",$ship->still)."<br><a href=?p=ship&s=ss&id=".$_GET['id']."&a=skat ".getronm('stel','buttons/map')."><img src=".$gfx."/buttons/map2.gif name=stel border=0 title=\"Kartographierung abbrechen\"> abbrechen</a>";
		// }
		// else echo "Dieses System wurde bereits kartographiert";
		// echo "</td></tr></table>";
	// }
	// if ($ship->porep == 1)
	// {
		// echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=300><th colspan=2><img src=".$gfx."/buttons/repli.gif title=\"Replikator\"> Replikator</th>
		// <tr>".($ship->replikator == 1 ? "<td><a href=?p=ship&s=ss&id=".$_GET['id']."&a=dv&d=re ".getronm('repi','buttons/repli')."><img src=".$gfx."/buttons/repli2.gif border=0 name=repli title=\"deaktivieren\"> deaktivieren</a></td><td><img src=".$gfx."/buttons/e_trans2.gif title=\"Energieverbrauch\"> Verbrauch: ".ceil($ship->crew/5)."</td>" : "<td colspan=2><a href=?p=ship&s=ss&id=".$_GET['id']."&a=av&d=re ".getonm('repli','buttons/repli')."><img src=".$gfx."/buttons/repli1.gif border=0 name=repli title=\"aktivieren\"> aktivieren</a></td>")."</tr>
		// </table>";
	// }

	// echo "</td><td valign=top align=left>
	// <table bgcolor=#262323 cellspacing=1 cellpadding=1 width=300>
	// <tr><th><img src=".$gfx."/buttons/gefecht.gif title='Gefechtskontrolle'> Gefechtskontrolle</th></tr>
	// <tr><td><img src=".$gfx."/buttons/alert".$ship->alvl.".gif title='Alarmstufe'> Alarmstufe ".($ship->alvl == 1 ? "" : "<a href=?p=ship&s=ss&a=ca&m=1&id=".$_GET['id']."><img src=".$gfx."/buttons/alert1.gif border=0 title=\"Grün\"></a>&nbsp;").($ship->alvl == 2 ? "" : "<a href=?p=ship&s=ss&a=ca&m=2&id=".$_GET['id']."><img src=".$gfx."/buttons/alert2.gif border=0 title=\"Gelb\"></a>").($ship->alvl == 3 ? "" : "&nbsp;<a href=?p=ship&s=ss&a=ca&m=3&id=".$_GET['id']."><img src=".$gfx."/buttons/alert3.gif border=0 title=\"Rot\"></a>")."</td></tr>
	// <tr><td>Waffen: ".($ship->wea_phaser == 1 ? "<a href=?p=ship&s=ss&id=".$_GET['id']."&a=dwp><img src=".$gfx."/buttons/act_phaser2.gif border=0 ".getronm('wps','buttons/act_phaser')." name=wps title='Waffensystem (Strahlenwaffe) deaktivieren'></a>" : "<a href=?p=ship&s=ss&id=".$_GET['id']."&a=awp><img src=".$gfx."/buttons/act_phaser1.gif border=0 ".getonm('wps','buttons/act_phaser')." name=wps title='Waffensystem (Strahlenwaffe) aktivieren'></a>")." ".($ship->wea_torp == 1 ? "<a href=?p=ship&s=ss&id=".$_GET['id']."&a=dwt><img src=".$gfx."/buttons/act_torp2.gif border=0 ".getronm('wpt','buttons/act_torp')." name=wpt title='Waffensystem (Torpedobänke) deaktivieren'></a>" : "<a href=?p=ship&s=ss&id=".$_GET['id']."&a=awt><img src=".$gfx."/buttons/act_torp1.gif border=0 ".getonm('wpt','buttons/act_torp')." name=wpt title='Waffensystem (Torpedobänke) aktivieren'></a>")."</td>";
	// echo "</td><td valign=top align=left></td></tr>";
	// if ($ship->rumps_id == 1712) echo "<tr><td><a href=?p=ship&a=swtst&id=".$_GET['id'].">Stationsmodus</td></tr>";
	// echo "</table>";
	// if ($ship->max_shuttles > 0)
	// {
		// echo "<script language=\"Javascript\">
		// function chgpic()
		// {
			// var pic = document.forms.shu.shur.value;
			// if (pic == parseInt(0))
			// {
				// document.getElementById(\"picshu\").innerHTML = '<img src=".$gfx."/buttons/info1.gif>';
				// return;
			// }
			// document.getElementById(\"picshu\").innerHTML = '<img src=".$gfx."/ships/' + pic + '.gif>';
		// }
		// </script>";
		// $res = $ship->getshuttles();
		// if (mysql_num_rows($res) > 0)
		// {
			// echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=300><tr><th colspan=2> Shuttles</th></tr>
			// <form action=main.php method=get name=shu><input type=hidden name=p value=ship><input type=hidden name=s value=ss><input type=hidden name=id value=".$_GET['id']."><input type=hidden name=a value=laush>
			// <tr><td><span id=picshu><img src=".$gfx."/buttons/info1.gif></span> <select name=shur onChange=\"chgpic();\"><option value=0>-----------";
			// while($data=mysql_fetch_assoc($res)) echo "<option value=".$data['rumps_id']."> ".$data['name'];
			// echo "</select> <input type=submit value=starten class=button></td></tr></form></table>";
		// }
		// $res = $ship->getoldshuttles();
		// if (mysql_num_rows($res) > 0)
		// {
			// echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=300><tr><th colspan=2>Gebrauchte Shuttles</th></tr>
			// <form action=main.php method=get name=shu><input type=hidden name=p value=ship><input type=hidden name=s value=ss><input type=hidden name=id value=".$_GET['id']."><input type=hidden name=a value=warsh>
			// <tr><td> <select name=shur><option value=0>-----------";
			// while($data=mysql_fetch_assoc($res)) echo "<option value=".$data['goods_id']."> ".$data['name'];
			// echo "</select> <input type=submit value=warten class=button></td></tr></form></table>";
		// }
	// }
	
	
	// JMP JumpMark Flottensteuerung
	
	// if ($ship->fsf == $_GET['id'])
	// {
		// echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=300><tr><th colspan=2><img src=".$gfx."/buttons/fleet.gif title=\"Flottensteuerung\"> Flottensteuerung</th></tr>
		// <tr><td width=150>Aktivieren</td><td>Deaktivieren</td></tr>
		//<tr><td><a href=?p=ship&s=ss&id=".$_GET['id']."&a=flaks ".getonm('flak','buttons/kss')."><img src=".$gfx."/buttons/kss1.gif border=0 name=flak title=\"Flottenbefehl: Nahbereichsensoren aktivieren\"></a> <a href=?p=ship&s=ss&id=".$_GET['id']."&a=flas ".getonm('flsh','buttons/shldac')."><img src=".$gfx."/buttons/shldac1.gif border=0 name=flsh title=\"Flottenbefehl: Schilde aktivieren\"></a> <a href=?p=ship&s=ss&id=".$_GET['id']."&a=flac ".getonm('flac','buttons/tarn')."><img src=".$gfx."/buttons/tarn1.gif name=flac border=0 title=\"Flottenbefehl: Tarnung aktivieren\"></a> <a href=?p=ship&s=ss&id=".$_GET['id']."&a=flaw ".getonm('flaw',"buttons/warp")."><img src=".$gfx."/buttons/warp1.gif border=0 name=flaw title=\"Flottenbefehl: Warpantrieb aktivieren\"></a> <a href=?p=ship&s=ss&id=".$_GET['id']."&a=flawp><img src=".$gfx."/buttons/act_phaser1.gif border=0 ".getonm('fwps','buttons/act_phaser')." name=fwps title='Flottenbefehl: Waffensystem (Strahlenwaffe) aktivieren'></a> <a href=?p=ship&s=ss&id=".$_GET['id']."&a=flawt><img src=".$gfx."/buttons/act_torp1.gif border=0 ".getonm('fwpt','buttons/act_torp')." name=fwpt title='Flottenbefehl: Waffensystem (Torpedobänke) aktivieren'></a></td>
		//<td><a href=?p=ship&s=ss&id=".$_GET['id']."&a=fldks ".getronm('fldk','buttons/kss')."><img src=".$gfx."/buttons/kss2.gif border=0 name=fldk title=\"Flottenbefehl: Nahbereichsensoren deaktivieren\"></a> <a href=?p=ship&s=ss&id=".$_GET['id']."&a=flds ".getronm('fldsh','buttons/shldac')."><img src=".$gfx."/buttons/shldac2.gif border=0 name=fldsh title=\"Flottenbefehl: Schilde deaktivieren\"></a> <a href=?p=ship&s=ss&id=".$_GET['id']."&a=fldc ".getronm('fldc','buttons/tarn')."><img src=".$gfx."/buttons/tarn2.gif name=fldc border=0 title=\"Flottenbefehl: Tarnung deaktivieren\"></a> <a href=?p=ship&s=ss&id=".$_GET['id']."&a=fldw ".getronm('fldw',"buttons/warp")."><img src=".$gfx."/buttons/warp2.gif border=0 name=fldw title=\"Flottenbefehl: Warpantrieb deaktivieren\"></a> <a href=?p=ship&s=ss&id=".$_GET['id']."&a=fldwp><img src=".$gfx."/buttons/act_phaser2.gif border=0 ".getronm('fdwps','buttons/act_phaser')." name=fdwps title='Flottenbefehl: Waffensystem (Strahlenwaffe) deaktivieren'></a> <a href=?p=ship&s=ss&id=".$_GET['id']."&a=fldwt><img src=".$gfx."/buttons/act_torp2.gif border=0 ".getronm('fdwpt','buttons/act_torp')." name=fdwpt title='Flottenbefehl: Waffensystem (Torpedobänke) deaktivieren'></a></td></tr>
		// <tr><td colspan=2><a href=?p=ship&s=ss&id=".$_GET['id']."&a=flal&lvl=1><img src=".$gfx."/buttons/alert1.gif border=0 title=\"Flottenbefehl: Alarmstufe Grün\"></a> <a href=?p=ship&s=ss&id=".$_GET['id']."&a=flal&lvl=2><img src=".$gfx."/buttons/alert2.gif border=0 title=\"Flottenbefehl: Alarmstufe Gelb\"></a> <a href=?p=ship&s=ss&id=".$_GET['id']."&a=flal&lvl=3><img src=".$gfx."/buttons/alert3.gif border=0 title=\"Flottenbefehl: Alarmstufe Rot\"></a></td></tr></table>";
	// }
	// echo "</td></tr></table>";
	
	
	
	// if ($ship->map['cid'] > 0)
	// {
		// $col = $db->query("SELECT a.id,a.name,a.user_id,a.schilde_status,a.planet_name,a.quest_involved,b.user,c.name as cname FROM stu_colonies as a LEFT JOIN stu_user as b ON a.user_id=b.id LEFT JOIN stu_colonies_classes as c ON a.colonies_classes_id=c.colonies_classes_id WHERE a.systems_id=".$ship->systems_id." AND a.sx=".$ship->sx." AND a.sy=".$ship->sy." LIMIT 1",4);
		// $img = "<img src=".$gfx."/planets/".$ship->map['cid'].($col['schilde_status'] == 1 ? "s" : "").".gif width=15 heigth=15 border=0 onmouseover=\"return overlib('<table class=tcal><th>Kolonieinformationen</th><tr><td>Kolonie: ".str_replace("'","",stripslashes($col['name']))." (".$col['id'].")<br>Besitzer: ".str_replace("'","",stripslashes($col['user']))." (".$col['user_id'].")</td></tr></table>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER);\" onmouseout=\"nd();\">";
		// if ($col['user_id'] != 1) $col['user_id'] == $_SESSION['uid'] ? print("<a href=?p=colony&s=sc&id=".$col['id']."&shd=".$_GET['id'].">".$img."</a>") : print($img);
		// else echo "<img src=".$gfx."/planets/".$ship->map['cid'].".gif width=15 heigth=15 title='Besitzer: Niemand'>";
		// echo " <a href=?p=ship&s=cac&id=".$_GET['id']."&t=".$col['id']." ".getonm("csc","buttons/lupe")."><img src=".$gfx."/buttons/lupe1.gif name=csc title='Kolonie-Aktionen' border=0></a>
		 // <a href=\"javascript:void(0);\" onClick=\"col_etransfer(".$_GET['id'].",".$col['id'].");\" ".getonm("cet","buttons/e_trans")."><img src=".$gfx."/buttons/e_trans1.gif name=cet title='Energietransfer' border=0></a> <a href=?p=ship&s=bec&id=".$_GET['id']."&m=to&t=".$col['id']." ".getonm("cbt","buttons/b_down")."><img src=".$gfx."/buttons/b_down1.gif name=cbt title=\"Zur Kolonie beamen\" border=0></a> <a href=?p=ship&s=bec&id=".$_GET['id']."&m=fr&t=".$col['id']." ".getonm("cbf","buttons/b_up")."><img src=".$gfx."/buttons/b_up1.gif name=cbf title='Von der Kolonie beamen' border=0></a>
		 // <a href=\"javascript:void(0);\" onClick=\"open_pm_window(".$col['user_id'].",0,0);\" ".getonm("comsg","buttons/msg")."><img src=".$gfx."/buttons/msg1.gif name=comsg border=0 title=\"Eine PM an ".ftit($col['user'])." schicken\"></a>
		 // <a href=?p=ship&s=sht&id=".$_GET['id']."&t=".$col['id']." ".getonm("cft","buttons/fergtrade")."><img src=".$gfx."/buttons/fergtrade1.gif name=cft border=0 title=\"Warenangebot\"></a>";
		// if ($col['user_id'] == 1 && $ship->plans_id == 1) echo "<br><a href=?p=ship&s=col&id=".$_GET['id']."&cid=".$col['id']." ".getonm('colz','buttons/colo')."><img src=".$gfx."/buttons/colo1.gif title=\"Kolonisieren\" name=colz border=0> Kolonisieren</a>";
	// }
	
	
	
	// else echo "<img src=".$gfx."/map/".$ship->map['type'].".gif width=15 height=15> ".$ship->map['name'];


	// JMP JumpMark SEB Station
	
	// if ($seb != 0) 
	// {

		// if ($seb['rumps_id'] != 0 && $seb['id'] != $_GET['id'])
		// {
			// echo "<br>".($seb['user_id'] == $_SESSION['uid'] ? "<a href=?p=stat&s=ss&id=".$seb['id'].">" : "")."<img src=".$gfx."/ships/".vdam($seb).$seb['rumps_id'].".gif width=15 heigth=15 border=0 onmouseover=\"overlib('<table class=tcal><th>Stationsinformationen</th><tr><td>".ftit($seb['cname']).": ".ftit($seb['name'])." (".$seb['id'].")</td></tr>
			// <tr><td>Besitzer: ".ftit($seb['user'])." (".$seb['user_id'].")</td></tr></table>');\" onmouseout=\"nd();\">".($seb['user_id'] == $_SESSION['uid'] ? "</a>" : "")." <a href=?p=ship&s=sc&id=".$_GET['id']."&t=".$seb['id']."&m=s ".getonm("bsc","buttons/lupe")."><img src=".$gfx."/buttons/lupe1.gif border=0 name=bsc title='Scan'></a> 
			// <a href=\"javascript:void(0);\" onClick=\"ship_etransfer(".$_GET['id'].",".$seb['id'].");\" ".getonm("bet","buttons/e_trans")."><img src=".$gfx."/buttons/e_trans1.gif name=bet title='Energietransfer' border=0></a>
			// <a href=?p=ship&s=be&m=to&t=".$seb['id']."&id=".$_GET['id']." ".getonm("bbt","buttons/b_down")."><img src=".$gfx."/buttons/b_down1.gif name=bbt title=\"Zur Basis beamen\" border=0></a> <a href=?p=ship&s=be&m=fr&t=".$seb['id']."&id=".$_GET['id']." ".getonm("bbf","buttons/b_up")."><img src=".$gfx."/buttons/b_up1.gif name=bbf title=\"Von der Basis beamen\" border=0></a>
			// <a href=\"javascript:void(0);\" onClick=\"open_pm_window(".$seb['user_id'].",".$_GET['id'].",".$seb['id'].");\" ".getonm("pm".$i,"buttons/msg")."><img src=".$gfx."/buttons/msg1.gif name=pm".$i." title='Eine PM an die ".ftit($seb['name'])." schicken' border=0></a>";
			// $ship->dock == $seb['id'] ? print(" <a href=?p=ship&s=ss&id=".$_GET['id']."&a=ddoc&t=".$seb['id']." ".getronm('dock','buttons/dock')."><img src=".$gfx."/buttons/dock2.gif name=dock border=0 title=\"Von der ".ftit($seb['name'])." abdocken\"></a>") : print(" <a href=?p=ship&s=ss&id=".$_GET['id']."&a=doc&t=".$seb['id']." ".getonm('dock','buttons/dock')."><img src=".$gfx."/buttons/dock1.gif name=dock border=0 title=\"An der ".ftit($seb['name'])." andocken\"></a>");
			// if (($seb['rumps_id'] == 9000) && ($_SESSION['uid'] == 102)) echo "&nbsp;<a href=?p=fergp&id=".$_GET['id']." ".getonm('ftr','buttons/fergtrade')."><img src=".$gfx."/buttons/fergtrade1.gif border=0 name=ftr title=\"Ferengi-Bar\"></a>";
			// if ($_SESSION['is_rkn'] == 1 && $_SESSION['race'] == $seb['is_rkn'] && ($seb['rumps_id'] == 14 || ($seb['rumps_id'] >= 9001 && $seb['rumps_id'] <= 9005))) echo "&nbsp;<a href=\"javascript:void(0);\" onClick=\"get_npc_support(".$_GET['id'].",".$seb['id'].");\"><img src=".$gfx."/rassen/".$_SESSION['race']."s.gif border=\"0\"></a>";
		// }

	// }

	
	// JMP JumpMark Konstrukt errichten
	// if ($ship->checkgood($ship->id,10) >= 1 && $seb == 0) echo "<br><a href=?p=ship&s=ss&a=bko&id=".$_GET['id']." ".getronm('kon','buttons/statio')."><img src=".$gfx."/buttons/statio2.gif name=kon title=\"Konstrukt errichten\" border=0> Konstrukt errichten</a>";
	
	
	
	
	// echo "<br><a href=?p=ship&s=scs&id=".$_GET['id']." ".getonm("scs","buttons/lupe")."><img src=".$gfx."/buttons/lupe1.gif border=0 name=scs title=\"Sektor scannen\"> Sektor scannen</a>
	// </td></tr></table></td><td valign=top>";
	// if ($ship->map['is_system'] == 1 && $ship->systems_id == 0)
	// {
		// $sys = $map->getsystembyxy($ship->cx,$ship->cy);
		// echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=200><tr><th>Systemdetails</a></th></tr>
		// <tr><td><img src=".$gfx."/map/".$sys['type'].".gif width=15 height=15> <a href=\"javascript:void(0);\" onClick=\"showsysinfo(".$_GET['id'].");\">".$sys['name']."</a>";
		// if ($ship->m11 > 0) echo "<br><a href=?p=ship&s=ss&a=es&id=".$_GET['id']." onmouseover=cp('se','buttons/sysenter2') onmouseout=cp('se','buttons/sysenter1')><img src=".$gfx."/buttons/sysenter1.gif name=se border=0 title='Einfliegen'> Einfliegen</a>";
		// echo "</td></tr></table>";
	// }
	// if ($ship->systems_id > 0)
	// {
		// if (!$sys) $sys = $map->getsystembyid($ship->systems_id);
		// echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=200><tr><th>Systemdetails</th></tr>
		// <tr><td><img src=".$gfx."/map/".$sys['type'].".gif width=15 height=15> <a href=\"javascript:void(0);\" onClick=\"showsysinfo(".$_GET['id'].");\">".$sys['name']."</a>";
		// if ($ship->m11 > 0) echo "<br><a href=?p=ship&s=ss&a=ls&id=".$_GET['id']." onmouseover=cp('sl','buttons/sysleave2') onmouseout=cp('sl','buttons/sysleave1')><img src=".$gfx."/buttons/sysleave1.gif name=sl border=0 title='Verlassen'> Verlassen</a>";
		// echo "</td></tr></table>";
	// }
	// echo "</td></tr></table>";
	//if ($ship->map['sensoroff'] == 1 || $ship->map['type'] == 8)
	//{
	//	$result = $ship->get_marked_ships();
	//	if (mysql_num_rows($result) > 0)
	//	{
	//		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1>
	//		<th colspan=2>Markierte Schiffe</th>";
	//		while($data=mysql_fetch_assoc($result))
	//		{
	//			$i++;
	//			echo "<tr>
	//				<td><img src=".$gfx."/ships/".$data['rumps_id'].".gif></td>
	//				<td>".($ship->wea_phaser == 1 || $ship->wea_torp == 1 ? "<a href=?p=ship&s=ss&a=att&ps=".$_SESSION['pagesess']."&id=".$_GET['id']."&t=".$data['id']." ".getonm("ph".$i,"buttons/phaser")."><img src=".$gfx."/buttons/phaser1.gif title='Angreifen' name=ph".$i." border=0></a>" : "-")."</td>
	//			</tr>";
	//		}
	//		echo "</table><br />";
	//	}
	//}
	
	
	
	
	
	
	
	
	
	
	



	
	echo "</table><br><br>";

}
if ($v == "beam")
{
	if (($_GET['m'] != "to" && $_GET['m'] != "fr") || !$_GET['t'] || !check_int($_GET['t'])) exit;
	$tar = $db->query("SELECT a.id,a.name,a.user_id,a.rumps_id,a.cloak,a.cx,a.cy,a.sx,a.sy,a.systems_id,a.huelle,a.max_huelle,a.crew,a.max_crew,a.trumps_id,a.is_hp,b.name as cname,b.slots,b.storage,b.trumfield FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) WHERE a.id=".$_GET['t']." LIMIT 1",4);
	if (checksector($tar) == 0) exit;
	pageheader("/ <a href=?p=ship>Schiffe</a> / <a href=?p=ship&s=ss&id=".$_GET['id'].">".stripslashes($ship->name)."</a> / <b>".($_GET['m'] == "to" ? "Zu" : "Von")." der ".stripslashes($tar['name'])." beamen</b>");
	$_GET['m'] == "to" ? $stor = $ship->getshipstorage($_GET['id']) : $stor = $ship->getshipstorage($_GET['t']);
	echo "<form action=main.php method=post><input type=hidden name=bshi value=1><input type=hidden name=p value=ship><input type=hidden name=s value=ss><input type=hidden name=a value=be>
	<input type=hidden name=t value=".$_GET["t"]."><input type=hidden name=m value=".$_GET["m"]."><input type=hidden name=id value=".$_GET["id"].">
	<table cellspacing=1 cellpadding=1>
	<tr><td valign=top>";
	if (mysql_num_rows($stor) == 0) meldung("Keine Waren auf der ".($_GET['m'] == "fr" ? stripslashes($tar['name']) : stripslashes($ship->name))." vorhanden");
	else
	{
		
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr><th colspan=4>Waren auswählen</th></tr>";
		while($sd=mysql_fetch_assoc($stor))
		{
			if ($sd['goods_id'] >= 80 && $sd['goods_id'] < 110 && $_SESSION['uid'] != $tar['user_id'] && $_GET['m'] == "fr" && $tar['is_hp'] != 1) continue;
			if ($i == 0) echo "<tr>";
			echo "<td><input type=hidden name=good[] value=".$sd['goods_id'].">
			<img src=".$gfx."/goods/".$sd['goods_id'].".gif title='".$sd['name']."'> ".$sd['count']."</td><td><input type=text size=3 name=count[] class=text></td>";
			$i++;
			$w = 1;
			if ($i == 2) { echo "</tr>"; $i = 0; }
		}
		if (!$w) echo "<tr><td colspan=2>Keine Waren auf der ".($_GET['m'] == "fr" ? stripslashes($tar['name']) : stripslashes($ship->name))." vorhanden</td></tr>";
		if ($i == 1) echo "<td colspan=2></td></tr>";
		echo "</table><br><input type=submit class=button value=Beamen>";
	}
	echo "</td><td valign=top>
	<table bgcolor=#262323 cellspacing=1 cellpadding=1>
	<tr><th colspan=3>Informationen</th></tr>
	<tr><td colspan=3>Energie: ".$ship->eps." (".$ship->batt.")<br>
	Ladung: ".($_GET['m'] == "to" ? $ship->getshipstoragesum($tar['id'])."/".$tar['storage'] : $ship->getshipstoragesum($_GET['id'])."/".$ship->storage)."</td></tr>";
	if ($_SESSION['uid'] == $tar['user_id']) echo "<tr><td colspan=3><img src=".$gfx."/buttons/crew.gif title='Crew'> <input type=text size=2 class=text name=crew> ".($_GET['m'] == "to" ? $ship->crew."/".($tar['max_crew']-$tar['crew']) : $tar['crew']."/".($ship->max_crew-$ship->crew))."</td></tr>";
	echo "<tr><th colspan=3>Modus ändern</th></tr>
	<tr><td><a href=?p=ship&s=ss&id=".$_GET['id']."><img src=".$gfx."/ships/".vdam($ship).$ship->rumps_id.".gif border=0 title=\"".$ship->cname."\"></a></td><td>";
	if ($_GET['m'] == "to") echo "<a href=?p=ship&s=be&m=fr&id=".$_GET["id"]."&t=".$tar['id']." ".getonm('bm','buttons/b_to')."><img src=".$gfx."/buttons/b_to1.gif name=bm border=0 title=\" Von der ".ftit($tar['name'])." beamen\"></a>";
	else echo "<a href=?p=ship&s=be&m=to&id=".$_GET['id']."&t=".$tar['id']." ".getonm('bm','buttons/b_from')."><img src=".$gfx."/buttons/b_from1.gif name=bm border=0 title=\" Zu der ".ftit($tar['name'])." beamen\"></a>";
	echo "</td><td>".($tar['user_id'] == $_SESSION['uid'] ? "<a href=?p=".($tar['slots'] > 0 ? "stat" : "ship")."&s=ss&id=".$tar['id']."><img src=".$gfx."/ships/".vdam($tar).($tar['trumfield'] == 1 ? $tar['trumps_id'] : $tar['rumps_id']).".gif border=0 title=\"".$tar['cname']."\"></a>" : "<img src=".$gfx."/ships/".vdam($tar).($tar['trumfield'] == 1 ? $tar['trumps_id'] : $tar['rumps_id']).".gif title=\"".$tar['cname']."\">")."</td></tr>
	<tr><td colspan=3 align=center><input type=submit class=button value=Beamen></td></tr>
	</table>
	</td></tr></table></form>";
}
if ($v == "beamc")
{
	if (($_GET['m'] != "to" && $_GET['m'] != "fr") || !$_GET['t'] || !check_int($_GET['t'])) exit;
	$tar = $db->query("SELECT id,user_id,name,colonies_classes_id as cid,sx,sy,systems_id,bev_free,bev_work,bev_max,max_storage FROM stu_colonies WHERE id=".$_GET['t']." LIMIT 1",4);
	if (checksector($tar) == 0) exit;
	pageheader("/ <a href=?p=ship>Schiffe</a> / <a href=?p=ship&s=ss&id=".$_GET['id'].">".stripslashes($ship->name)."</a> / <b>".($_GET['m'] == "to" ? "Zu" : "Von")." der Kolonie ".stripslashes($tar['name'])." beamen</b>");
	if ($tar['user_id'] != $_SESSION['uid'] && $db->query("SELECT field_id FROM stu_colonies_fielddata WHERE (buildings_id=408 OR  buildings_id=418 OR  buildings_id=428) AND aktiv=1 AND colonies_id=".$_GET['t']." LIMIT 1",1) > 0)
	{
		$scanblock = 1;
	}
	$_GET['m'] == "to" ? $stor = $ship->getshipstorage($ship->id) : $stor = $ship->getcolstorage($_GET['t']);
	echo "<form action=main.php method=post><input type=hidden name=bshc value=1><input type=hidden name=p value=ship><input type=hidden name=s value=ss><input type=hidden name=a value=bec>
	<input type=hidden name=t value=".$_GET['t']."><input type=hidden name=m value=".$_GET['m']."><input type=hidden name=id value=".$_GET['id'].">
	<table cellspacing=1 cellpadding=1>
	<tr><td valign=top>";
	if (mysql_num_rows($stor) == 0) meldung("Keine Waren auf der ".($_GET['m'] == "fr" ? stripslashes($tar['name']) : $ship->name)." vorhanden");
	else
	{
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr><th colspan=4>Waren auswählen</th></tr>";
		while($sd=mysql_fetch_assoc($stor))
		{
			if ($i == 0) echo "<tr>";
			echo "<td><input type=hidden name=good[] value=".$sd['goods_id'].">
			<img src=".$gfx."/goods/".$sd['goods_id'].".gif title='".$sd['name']."'> ".($scanblock == 1 ? "???" : $sd['count'])."</td><td><input type=text size=3 name=count[] class=text></td>";
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
	Ladung: ".($_GET['m'] == "to" ? $ship->getcolstoragesum($tar['id'])."/".$tar[max_storage] : $ship->getshipstoragesum($_GET['id'])."/".$ship->storage)."</td></tr>";
	if ($_SESSION['uid'] == $tar['user_id']) echo "<tr><td colspan=3><img src=".$gfx."/buttons/crew.gif title='Crew'> <input type=text size=2 class=text name=crew> ".($_GET['m'] == "to" ? $ship->crew."/".($tar[bev_max]-$tar[bev_free]-$tar[bev_work]) : $tar[bev_free]."/".($ship->max_crew-$ship->crew))."</td></tr>";
	echo "<tr><th colspan=3>Modus</th></tr>
	<tr><td><a href=?p=ship&s=ss&id=".$_GET['id']."><img src=".$gfx."/ships/".vdam($ship).$ship->rumps_id.".gif border=0 title=\"".$ship->cname."\"></a></td><td>";
	if ($_GET['m'] == "to") echo "<a href=?p=ship&s=bec&m=fr&id=".$_GET['id']."&t=".$tar['id']." ".getonm('bm','buttons/b_to')."><img src=".$gfx."/buttons/b_to1.gif name=bm border=0 title=\" Von der Kolonie ".stripslashes($tar['name'])." beamen\"></a>";
	else echo "<a href=?p=ship&s=bec&m=to&id=".$_GET['id']."&t=".$tar['id']." ".getonm('bm','buttons/b_from')."><img src=".$gfx."/buttons/b_from1.gif name=bm border=0 title=\" Zu der Kolonie ".stripslashes($tar['name'])." beamen\"></a>";
	echo "</td><td>".($tar['user_id'] == $_SESSION["uid"] ? "<a href=?p=colony&s=sc&id=".$tar['id']."><img src=".$gfx."/planets/".$tar[cid].($tar['schilde_status'] == 1 ? "s" : "").".gif border=0></a>" : "<img src=".$gfx."/planets/".$tar[cid].($tar['schilde_status'] == 1 ? "s" : "").".gif border=0>")."</td></tr>
	<tr><td colspan=3 align=center><input type=submit class=button value=Beamen></td></tr></table>
	</td></tr></table></form>";
}
if ($v == "beams")
{
	if (($_GET['m'] != "to" && $_GET['m'] != "fr") || !$_GET['t'] || !check_int($_GET['t'])) exit;
	$tar = $db->query("SELECT id,user_id,name,stations_classes_id as cid,sx,sy,systems_id,bev_free,bev_work,bev_max,max_storage FROM stu_stations WHERE id=".$_GET['t']." LIMIT 1",4);
	if (checksector($tar) == 0) exit;
	pageheader("/ <a href=?p=ship>Schiffe</a> / <a href=?p=ship&s=ss&id=".$_GET['id'].">".stripslashes($ship->name)."</a> / <b>".($_GET['m'] == "to" ? "Zu" : "Von")." der Station ".stripslashes($tar['name'])." beamen</b>");

	$_GET['m'] == "to" ? $stor = $ship->getshipstorage($ship->id) : $stor = $ship->getstastorage($_GET['t']);
	echo "<form action=main.php method=post><input type=hidden name=bshc value=1><input type=hidden name=p value=ship><input type=hidden name=s value=ss><input type=hidden name=a value=bes>
	<input type=hidden name=t value=".$_GET['t']."><input type=hidden name=m value=".$_GET['m']."><input type=hidden name=id value=".$_GET['id'].">
	<table cellspacing=1 cellpadding=1>
	<tr><td valign=top>";
	if (mysql_num_rows($stor) == 0) meldung("Keine Waren auf der ".($_GET['m'] == "fr" ? stripslashes($tar['name']) : $ship->name)." vorhanden");
	else
	{
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr><th colspan=4>Waren auswählen</th></tr>";
		while($sd=mysql_fetch_assoc($stor))
		{
			if ($i == 0) echo "<tr>";
			echo "<td><input type=hidden name=good[] value=".$sd['goods_id'].">
			<img src=".$gfx."/goods/".$sd['goods_id'].".gif title='".$sd['name']."'> ".($scanblock == 1 ? "???" : $sd['count'])."</td><td><input type=text size=3 name=count[] class=text></td>";
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
	Ladung: ".($_GET['m'] == "to" ? $ship->getstastoragesum($tar['id'])."/".$tar[max_storage] : $ship->getshipstoragesum($_GET['id'])."/".$ship->storage)."</td></tr>";
	if ($_SESSION['uid'] == $tar['user_id']) echo "<tr><td colspan=3><img src=".$gfx."/buttons/crew.gif title='Crew'> <input type=text size=2 class=text name=crew> ".($_GET['m'] == "to" ? $ship->crew."/".($tar[bev_max]-$tar[bev_free]-$tar[bev_work]) : $tar[bev_free]."/".($ship->max_crew-$ship->crew))."</td></tr>";
	echo "<tr><th colspan=3>Modus</th></tr>
	<tr><td><a href=?p=ship&s=ss&id=".$_GET['id']."><img src=".$gfx."/ships/".vdam($ship).$ship->rumps_id.".gif border=0 title=\"".$ship->cname."\"></a></td><td>";
	if ($_GET['m'] == "to") echo "<a href=?p=ship&s=bec&m=fr&id=".$_GET['id']."&t=".$tar['id']." ".getonm('bm','buttons/b_to')."><img src=".$gfx."/buttons/b_to1.gif name=bm border=0 title=\" Von der Kolonie ".stripslashes($tar['name'])." beamen\"></a>";
	else echo "<a href=?p=ship&s=bes&m=to&id=".$_GET['id']."&t=".$tar['id']." ".getonm('bm','buttons/b_from')."><img src=".$gfx."/buttons/b_from1.gif name=bm border=0 title=\" Zu der Kolonie ".stripslashes($tar['name'])." beamen\"></a>";
	echo "</td><td>".($tar['user_id'] == $_SESSION["uid"] ? "<a href=?p=station&s=show&id=".$tar['id']."><img src=".$gfx."/stations/".$tar[cid].".gif border=0></a>" : "<img src=".$gfx."/stations/".$tar[cid].".gif border=0>")."</td></tr>
	<tr><td colspan=3 align=center><input type=submit class=button value=Beamen></td></tr></table>
	</td></tr></table></form>";
}

if ($v == "beamwb")
{
	if (($_GET['m'] != "to" && $_GET['m'] != "fr") || !$_GET['t'] || !check_int($_GET['t'])) exit;
	$tar = $db->query("SELECT a.*,b.rumps_id FROM stu_trade_networks as a LEFT JOIN stu_ships as b on a.ships_id = b.id WHERE a.network_id=".$_GET['t']." LIMIT 1",4);

	if ($ship->cx != $tar['cx'] || $ship->cy != $tar['cy']) exit;
	
	pageheader("/ <a href=?p=ship>Schiffe</a> / <a href=?p=ship&s=ss&id=".$_GET['id'].">".stripslashes($ship->name)."</a> / <b>".($_GET['m'] == "to" ? "Zu" : "Aus")." ".stripslashes($tar['name'])." beamen</b>");

	$_GET['m'] == "to" ? $stor = $ship->getshipstorage($ship->id) : $stor = $ship->getwbstorage($_GET['t']);
	echo "<form action=main.php method=post><input type=hidden name=p value=ship><input type=hidden name=s value=ss><input type=hidden name=a value=bewb>
	<input type=hidden name=t value=".$_GET['t']."><input type=hidden name=m value=".$_GET['m']."><input type=hidden name=id value=".$_GET['id'].">
	<table cellspacing=1 cellpadding=1>
	<tr><td valign=top>";
	if (mysql_num_rows($stor) == 0) meldung("Keine Waren ".($_GET['m'] == "fr" ? "im Konto" : "auf der ".$ship->name)." vorhanden");
	else
	{
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr><th colspan=4>Waren auswählen</th></tr>";
		while($sd=mysql_fetch_assoc($stor))
		{
			if ($i == 0) echo "<tr>";
			echo "<td><input type=hidden name=good[] value=".$sd['goods_id'].">
			<img src=".$gfx."/goods/".$sd['goods_id'].".gif title='".$sd['name']."'> ".($scanblock == 1 ? "???" : $sd['count'])."</td><td><input type=text size=3 name=count[] class=text></td>";
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
	".($_GET['m'] == "to" ? "Warenkonto" : "Ladung").": ".($_GET['m'] == "to" ? $ship->getwbstoragesum($tar['network_id'])."/".$tar[max_storage] : $ship->getshipstoragesum($_GET['id'])."/".$ship->storage)."</td></tr>";
	if ($_SESSION['uid'] == $tar['user_id']) echo "<tr><td colspan=3><img src=".$gfx."/buttons/crew.gif title='Crew'> <input type=text size=2 class=text name=crew> ".($_GET['m'] == "to" ? $ship->crew."/".($tar[bev_max]-$tar[bev_free]-$tar[bev_work]) : $tar[bev_free]."/".($ship->max_crew-$ship->crew))."</td></tr>";
	echo "<tr><th colspan=3>Modus</th></tr>
	<tr><td><a href=?p=ship&s=ss&id=".$_GET['id']."><img src=".$gfx."/ships/".vdam($ship).$ship->rumps_id.".gif border=0 title=\"".$ship->cname."\"></a></td><td>";
	if ($_GET['m'] == "to") echo "<a href=?p=ship&s=bewb&m=fr&id=".$_GET['id']."&t=".$tar['network_id']." ".getonm('bm','buttons/b_to')."><img src=".$gfx."/buttons/b_to1.gif name=bm border=0 title=\" Zu ".stripslashes($tar['name'])." beamen\"></a>";
	else echo "<a href=?p=ship&s=bewb&m=to&id=".$_GET['id']."&t=".$tar['network_id']." ".getonm('bm','buttons/b_from')."><img src=".$gfx."/buttons/b_from1.gif name=bm border=0 title=\" Aus ".stripslashes($tar['name'])." beamen\"></a>";
	echo "</td><td><a href=?p=trade&s=wb&id=".$tar['network_id']."><img src=".$gfx."/ships/".$tar[rumps_id].".gif border=0></a></td></tr>
	<tr><td colspan=3 align=center><input type=submit class=button value=Beamen></td></tr></table>
	</td></tr></table></form>";
}

if ($v == "scan")
{
	if (!$_GET['t'] || !check_int($_GET['t'])) exit;
	pageheader("/ <a href=?p=ship>Schiffe</a> / <a href=?p=ship&s=ss&id=".$_GET['id'].">".stripslashes($ship->name)."</a> / <b>Scanergebnisse ".stripslashes($data['name'])."</b>");
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
	

	$db->query("INSERT IGNORE INTO stu_rumps_scans (`rumps_id` ,`user_id`) VALUES ('".$data['rumps_id']."', '".$_SESSION['uid']."');");
	
	if ($data['rumps_id'] != 99) $qpm->send_pm($_SESSION['uid'],$data['user_id'],"Die ".stripslashes($ship->name)." hat die ".stripslashes($data['name'])." in Sektor ".($ship->systems_id > 0 ? $ship->sx."|".$ship->sy." (".$ship->m->getsysnamebyid($ship->systems_id)."-System ".$ship->cx."|".$ship->cy.")" : $ship->cx."|".$ship->cy)." gescant",3);
	if ($ship->cloak == 0 && ($data['allys_id'] == 0 || $data['allys_id'] != $_SESSION['allys_id']) && $data['user_id'] != $_SESSION['uid'] && ($data['wea_phaser'] == 1 || $data['wea_torp'] == 1) && (($data['alvl'] == 3 && !isfriend($data['user_id'],$data['allys_id'],$_SESSION['uid'],$_SESSION['allys_id'])) || ($data['alvl'] == 2 && isenemy($data['user_id'],$data['allys_id'],$_SESSION['uid'],$_SESSION['allys_id']))))
	{
		if ($data['fleets_id'] == 0) $result = $ship->attack($ship->id,$data['id'],0,1,1);
		else $result = $fleet->attack($ship->id,$data['fleets_id'],1);
	}
	if ($result || $sresult)
	{
		if ($sresult) $result = $sresult;
		if (is_array($ship->logbook)) $ship->write_logbook();
		if ($ship->destroyed == 0 && !$ship->dsships[$_GET['id']] && !$fleet->dsships[$_GET['id']]  && $_GET['a'] != "col" && $ship->colonized != 1) $ship = new ship;
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
	// switch ($data['rumps_id'])
	// {
		// default:
			// $resid = 0;
			// break;
		// case 1806:
			// $resid = 743;
			// break;
		// case 2306:
			// $resid = 743;
			// break;
		// case 1810:
			// $resid = 741;
			// break;
		// case 5510:
			// $resid = 741;
			// break;
		// case 1808:
			// $resid = 742;
			// break;
		// case 5508:
			// $resid = 742;
			// break;
		// case 1811:
			// $resid = 744;
			// break;
	// }
	// if ($resid != 0 && $db->query("SELECT research_id FROM stu_researched WHERE user_id=".$_SESSION['uid']." AND research_id=".($resid-10)." LIMIT 1",1) > 0)
	// {
		// if ($db->query("SELECT research_id FROM stu_researched WHERE user_id=".$_SESSION['uid']." AND research_id=".($resid+10)." LIMIT 1",1) == 0)
		// {
			// $db->query("INSERT INTO stu_researched (research_id,user_id) VALUES ('".($resid+10)."','".$_SESSION['uid']."')");
			// $db->query("INSERT INTO stu_researched_disables (research_id,user_id) VALUES ('".($resid)."','".$_SESSION['uid']."')");
			// $db->query("INSERT INTO stu_pms (send_user,recip_user,text,type,date) VALUES ('6','".$_SESSION['uid']."','".addslashes("Die durch den Scan gewonnenen Daten kommen gerade herein. Diese sollten ausreichen, wir beginnen sofort mit der Entwicklung von Modulen.")."','1',NOW())");
		// }
	// }

	if ($data['rumps_id'] != 99) {
	
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
	} else {
		echo "<table>
		<tr>
		<td valign=\"top\">
			<table bgcolor=#262323 cellspacing=1 cellpadding=1>
			<tr><th colspan=3>".stripslashes($data['name'])."</th></tr>
			<tr>
				<td rowspan=7 valign=top><img src=".$gfx."/ships/".vdam($data).$data['rumps_id'].".gif title=\"".ftit($data['cname'])."\"><br /><br /></td>
				<td>Zustand</td>
				<td>bedenklich</td>
			</tr>
			<tr>
				<td>Schilde</td>
				<td>teilweise strukturelle<br>Integritätsfelder</td>
			</tr>
			<tr>
				<td>Energie</td>
				<td>fluktuierend</td>
			</tr>
			<tr>
				<td>Alarmbereitschaft</td>
				<td>keine</td>
			</tr>
			<tr>
				<td>Waffensysteme</td>
				<td>zerstört</td>
			</tr>
			<tr>
				<td>Sensoren</td>
				<td>zerstört</td>
			</tr>
			<tr>
				<td>Lebenszeichen</td>
				<td>keine</td>
			</tr></table>
		</td>
		<td valign=\"top\">
			<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=800>
			<th>Scanbericht</th>
			<tr><td>Das Wrack dieses abgestürzten Jem'Hadar-Schiffs scheint unkontrolliert auf einem Asteroiden aufgeschlagen zu sein. Trotz des enormen Aufpralls wirkt das Schiff teilweise intakt. Unsere Scans deuten darauf hin, dass Teile der Energieversorgung noch aktiv sind - Lebenszeichen sind jedoch keine vorhanden.";
			
			echo "<br><br>";
			
			if ($data['huelle'] > 10) {
				echo "Das Schiff ist stabil genug, um ein Außenteam herüberzubeamen. Eventuell ist es dem Team dann möglich, den Hauptcomputer anzuzapfen und an einen Datenkern des Dominion zu gelangen.";

				echo "<br><br><a href=?p=ship&s=ss&a=bwr&id=".$_GET['id']."&t=".$data['id']." onmouseover=cp('bt','buttons/b_to2') onmouseout=cp('bt','buttons/b_to1')><img src=".$gfx."/buttons/b_to1.gif name=bt title='Außenteam ".strip_tags(stripslashes($data['name']))." beamen' border=0> Außenteam herüberbeamen</a>";
			} else {
				echo "Der Zustand des Wracks hat sich drastisch verschlechtert - ein Transport ist nicht länger sicher.";
			}
			
			echo "</td></tr>";
			echo "</table>		
		</td>
		</tr>
		</table>";

	}
}
if ($v == "cscan")
{
	pageheader("/ <a href=?p=ship>Schiffe</a> / <a href=?p=ship&s=ss&id=".$_GET['id'].">".stripslashes($ship->name)."</a> / <b>Scanergebnisse ".stripslashes($data['name'])."</b>");
	if (!check_int($_GET['t'])) die(show_error(902));
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
	if ($_GET['a'] == "atc" && check_int($_GET['fid']) && $_GET['fid'] > 0) meldung($ship->attackcolfield($_GET['t'],$_GET['fid']));
	if ($ship->destroyed == 1 || $ship->dsships[$_GET['id']] == 1)
	{
		meldung("Du bist nicht Besitzer dieses Schiffes");
		exit;
	}
	include_once("class/colony.class.php");
	$col = new colony;
	echo "<table><tr><td>";
	$col->rendercolony($_GET['t'],1);
	echo "</td><td width=250 valign=top>
	<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr><td><table cellpadding=1 cellspacing=1>
	<th colspan=3>Schiffsinformationen</th>
	<tr><td colspan=3>".stripslashes($ship->name)."</td></tr>
	<tr><td>Energie</td><td>".renderepsstatusbar($ship->eps,$ship->max_eps)."</td><td>".$ship->eps."/".$ship->max_eps."<br>
	<tr><td>Hülle</td><td>".renderhuellstatusbar($ship->huelle,$ship->max_huelle)."</td><td>".$ship->huelle."/".$ship->max_huelle."</td></tr>
	<tr><td>Schilde</td><td>".rendershieldstatusbar($ship->schilde_aktiv,$ship->schilde,$ship->max_schilde)."</td><td>".$ship->schilde."/".$ship->max_schilde."</td></tr>
	<tr><td colspan=3>Torpedos: ".$db->query("SELECT b.count FROM stu_torpedo_types as a LEFT JOIN stu_ships_storage as b ON a.goods_id=b.goods_id AND b.ships_id=".$ship->id." WHERE a.torp_type=".$ship->torp_type,1)."</td></tr>
	</table></td></tr></table>
	</td></tr></table>";
}
if ($v == "caction")
{
	pageheader("/ <a href=?p=ship>Schiffe</a> / <a href=?p=ship&s=ss&id=".$_GET['id'].">".stripslashes($ship->name)."</a> / <b>Kolonie-Aktionen</b>");
	if (!check_int($_GET['t'])) die(show_error(902));
	$data = $db->query("SELECT id,sx,sy,systems_id FROM stu_colonies WHERE id=".$_GET['t']." LIMIT 1",4);
	if (!$data || $data == 0) exit;
	if (checksector($data) == 0) exit;
	if ($scanned == 1) {
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
	}	
	include_once("class/colony.class.php");
	$col = new colony;
	include_once("class/fleet.class.php");
	$fle = new fleet;
	if ($_GET['a'] == "def") meldung($fleet->startblockade(0,$ship->fleets_id,$_GET['t']));
	if ($_GET['a'] == "blo") meldung($fleet->startblockade(1,$ship->fleets_id,$_GET['t']));
	if ($_GET['a'] == "at1") meldung($fleet->startblockade(2,$ship->fleets_id,$_GET['t']));
	if ($_GET['a'] == "at2") meldung($fleet->startblockade(3,$ship->fleets_id,$_GET['t']));
	if ($_GET['a'] == "at3") meldung($fleet->startblockade(4,$ship->fleets_id,$_GET['t']));
	if ($ship->fleets_id != 0) {
		$statusbars = $fle->getfleetstatusbars($ship->fleets_id);
	}
	else {
		$statusbars = "<table cellpadding=1 cellspacing=1>
	<th colspan=3>Schiffsinformationen</th>
	<tr><td colspan=3>".stripslashes($ship->name)."</td></tr>
	<tr><td>Energie</td><td>".renderepsstatusbar($ship->eps,$ship->max_eps)."</td><td>".$ship->eps."/".$ship->max_eps."<br>
	<tr><td>Hülle</td><td>".renderhuellstatusbar($ship->huelle,$ship->max_huelle)."</td><td>".$ship->huelle."/".$ship->max_huelle."</td></tr>
	<tr><td>Schilde</td><td>".rendershieldstatusbar($ship->schilde_aktiv,$ship->schilde,$ship->max_schilde)."</td><td>".$ship->schilde."/".$ship->max_schilde."</td></tr>
	</table>";
	}
	echo "<table><tr><td>";
	if ($scanned == 1) {
		$col->rendercolony($_GET['t'],2);
	} else {
		echo "<a href=?p=ship&s=cacs&id=".$_GET['id']."&t=".$_GET['t']." ".getonm("csc","buttons/lupe")."><img src=".$gfx."/buttons/lupe1.gif name=csc title='Kolonie scannen' border=0> Kolonie scannen</a>";
	}
	echo "</td><td width=560 valign=top>
	<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr><td>".$statusbars."</td></tr></table></td></tr></table>";
	if ($ship->fleets_id != 0) {
		echo "<br>Mögliche Aktionen:<br><br>";
		echo "Defensiv:<br>";
		echo " <a href=?p=ship&s=cac&id=".$_GET['id']."&t=".$_GET['t']."&a=def ".getonm("def","buttons/guard")."><img src=".$gfx."/buttons/guard1.gif name=def title='Kolonie verteidigen' border=0> Verteidigen</a>";
		echo "<br><br>Offensiv:<br>";
		echo " <a href=?p=ship&s=cac&id=".$_GET['id']."&t=".$_GET['t']."&a=blo ".getonm("blo","buttons/x")."><img src=".$gfx."/buttons/x1.gif name=blo title='Kolonie blockieren' border=0> Blockade errichten</a>";
		echo "<br> <a href=?p=ship&s=cac&id=".$_GET['id']."&t=".$_GET['t']."&a=at1 ".getonm("at1","buttons/leavecol")."><img src=".$gfx."/buttons/leavecol1.gif name=at1 title='Militär zerstoeren' border=0> Militärische Gebäude zerstören</a>";
		echo "<br> <a href=?p=ship&s=cac&id=".$_GET['id']."&t=".$_GET['t']."&a=at2 ".getonm("at2","buttons/leavecol")."><img src=".$gfx."/buttons/leavecol1.gif name=at2 title='Produktion zerstoeren' border=0> Produktion zerstören</a>";
		echo "<br> <a href=?p=ship&s=cac&id=".$_GET['id']."&t=".$_GET['t']."&a=at3 ".getonm("at3","buttons/leavecol")."><img src=".$gfx."/buttons/leavecol1.gif name=at3 title='Kolonie zerstoeren' border=0> Kolonie vollständig zerstören</a>";
	}
}
if ($v == "colonize")
{
	include_once("class/colony.class.php");
	$col = new colony;
	$col->loadcolony($_GET["cid"]);
	if ($col->col == 0) exit;
	pageheader("/ <a href=?p=ship>Schiffe</a> / <a href=?p=ship&s=ss&id=".$_GET['id'].">".stripslashes($ship->name)."</a> / <b>Kolonisation - Feld wählen</b>");
	echo "<table>
	<tr><td valign=top><table cellpadding=1 cellspacing=1 bgcolor=#262323>";
	$i=0;
	$col->col['is_moon'] == 1 ? $t = 7 : $t=10;
	$fd = $db->query("SELECT type,field_id,buildings_id,aktiv FROM stu_colonies_fielddata WHERE colonies_id=".$_GET['cid']." AND field_id < 81 ORDER BY field_id");
	$cf = getcolonizefield($col->col['colonies_classes_id']);
	while($data = mysql_fetch_assoc($fd))
	{
		if ($i%$t==0) echo "</tr><tr>";
		if ($cf == $data['type']) $link = 1;
		echo "<td>".($link == 1 ? "<a href=?p=ship&s=ss&a=col&id=".$_GET['id']."&t=".$_GET["cid"]."&cf=".$data['field_id'].">" : "")."<img src=".$gfx."/fields/".get_dn_state($j,$chg,$col->col['dn_mode'],$data['fm'],$col->col['is_moon'],$col->col['colonies_classes_id']).$data['type'].".gif border=0>".($link == 1 ? "</a>" : "")."</td>";
		$i++;
		$link = 0;
	}
	echo "</table></td><td width=5>&nbsp;</td><td valign=top width=100><table bgcolor=#262323 cellspacing=1 cellpadding=1>
	<th>Informationen</th>
	<tr><td><img src=".$gfx."/buttons/grav.gif title='Gravitation'> ".$col->col['gravitation']."<br>
	<img src=".$gfx."/buttons/time.gif title='Rotationsdauer'> ".gen_time($col->col['rotation'])."</td></tr>
	
	</table></td></tr></table>";
}
if ($v == "showtrade")
{
	pageheader("/ <a href=?p=ship>Schiffe</a> / <a href=?p=ship&s=ss&id=".$_GET['id'].">".stripslashes($ship->name)."</a> / <b>Warenangebote der Kolonie</b>");
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=200>
	<th>Angebotene Waren</th><tr><td>";
	while($data=mysql_fetch_assoc($ship->result))
	{
		if (!$lm && $data['mode'] == 2)
		{
			$lm = 1;
			echo "</td></tr><th>Gesuchte Waren</th><tr><td>";
		}
		echo "<img src=".$gfx."/goods/".$data['goods_id'].".gif title=\"".ftit($data['name'])."\">&nbsp;";
	}
	echo "</td></tr></table>";
}
if ($v == "selbstzerst")
{
	pageheader("/ <a href=?p=ship>Schiffe</a> / <a href=?p=ship&s=ss&id=".$_GET['id'].">".stripslashes($ship->name)."</a> / <b>Selbstzerstörung</b>");
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=400>
	<tr><td>Bitte den Bestätigungscode in das Feld eintippen und bestätigen:<br>
	<font color=#ff0000>".$_SESSION["szcode"]."</font></td></tr>
	<form action=main.php method=get><input type=hidden name=p value=ship><input type=hidden name=id value=".$_GET['id'].">
	<tr><td><input type=text size=6 class=text name=sc> <input type=submit value=Bestätigung class=button></td></tr></form>
	</table>";
}
if ($v == "sectorscan")
{
	pageheader("/ <a href=?p=ship>Schiffe</a> / <a href=?p=ship&s=ss&id=".$_GET['id'].">".stripslashes($ship->name)."</a> / <b>Signaturen-Scan für Sektor ".($ship->systems_id > 0 ? $ship->sx."|".$ship->sy : $ship->cx."|".$ship->cy)."</b>");
	
	echo "<a href=?p=ship&s=scs&id=".$_GET['id']." ".getHover("scn","inactive/n/scan","hover/g/scan")."><img src=".$gfx."/buttons/inactive/n/scan.gif border=0  name=scn title='Scan wiederholen'> Scan wiederholen</a><br><br>";
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1>
	<th width=150><center>Typ</center></th><th width=30></th><th width=500><center>Details</center></th><th width=160><center>Alter</center></th>";
	
function getrtype($t) {
	global $gfx;
	switch($t)
	{
		case "0": return "Frachtschiff / Transporter";
		case "1": return "Jäger / Runabout";
		case "2": return "Eskortschiff / Bird of Prey";
		case "3": return "Zerstörer";
		case "4": return "Kreuzer";			
		default: return "Unbekannt";
	}
}
function getricon($t) {
	global $gfx;
	switch($t)
	{
		case "0": return "<img src=".$gfx."/icons/rump/civilian.gif title=\"".getrtype($t)."\">";
		case "1": return "<img src=".$gfx."/icons/rump/fighter.gif title=\"".getrtype($t)."\">";
		case "2": return "<img src=".$gfx."/icons/rump/escort.gif title=\"".getrtype($t)."\">";
		case "3": return "<img src=".$gfx."/icons/rump/destroyer.gif title=\"".getrtype($t)."\">";
		case "4": return "<img src=".$gfx."/icons/rump/cruiser.gif title=\"".getrtype($t)."\">";
		default: return "?";
	}
}
function formatTime($val) {
	global $time;

	
	$seconds = $time-$val;
	$hours = floor($seconds/3600);
	
	if ($hours < 1) return "Weniger als 1 Stunde";
	if ($hours < 3) return "<font color=#22aa22>Weniger als ".($hours+1)." Stunden</font>";
	else return "<font color=#aa2222>Weniger als ".($hours+1)." Stunden</font>";
}

	$time = time();

	foreach($result['ss'] as $data) {
		echo "<tr>";
		
		if ($data['type'] == "warp") {
			echo "<td width=150><center><font color=#88aaff>Warpspur</font></center></td>";
			echo "<td width=30>".getricon($data['rtype'])."</td>";
			
			if ($time - $data['date_tsp'] < 3600*3) echo "<td><center><img src=".$gfx."/ships/".$data['rumps_id'].".gif /> ".$data['rname']."-Klasse</center></td>";
			else									echo "<td><center>".getrtype($data['rtype'])."</center></td>";
			
			echo "<td width=160><center>".formatTime($data['date_tsp'])."</center></td>";
		}
		if ($data['type'] == "cloak") {
			echo "<td width=150><center><font color=#666666>Chroniton-Partikel</font></center></td>";
			echo "<td width=30><img src=".$gfx."/icons/rump/cloak.gif title=\"Chroniton-Partikel\"></td>";
			
			echo "<td><center>???</center></td>";
			
			echo "<td width=160><center>".formatTime($data['date_tsp'])."</center></td>";
		}
		
		echo "</tr>";
	}
	
	
	
	echo "</table>";
}
?>
