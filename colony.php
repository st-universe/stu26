<?php
if (!is_object($db)) exit;
include_once("class/colony.class.php");
$col = new colony;

switch($_GET['s'])
{
	default:
		$v = "main";
	case "ma":
		$v = "main";
		break;
	case "sc":
		$v = "showcolony";
		break;
	case "ase":
		if ($_GET['sl_x'])
		{
			$v = "listorbititems";
			break;
		}
		if (!check_int($_GET['shd'])) die(show_error(902));
		$col->loadship($_GET['shd']);
		if ($col->ship == 0) exit;
		if (checkcolsector($col->ship) == 0)
		{
			$v = "showcolony";
			break;
		}
		if ($_GET['ar_x'])
		{
			if ($col->check_rbf($col->id) == 0)
			{
				$result = "Für diese Aktion wird ein aktiver Raumbahnhof benötigt";
				$v = "showcolony";
				break;
			}
			if ($col->check_werft($col->id) == 0)
			{
				$result = "Für diese Aktion wird eine Werft benötigt";
				$v = "showcolony";
				break;
			}
			if ($col->ship['user_id'] != $_SESSION['uid'])
			{
				$result = "Zur Zeit können nur eigene Schiffe ausgerüstet werden";
				$v = "showcolony";
				break;
			}
			$_GET['t'] = $col->ship['id'];
			include_once("colship.php");
			exit;
		}
		if ($_GET['bt_x']  && $_GET['shd']) $v = "beamto";
		if ($_GET['bf_x']  && $_GET['shd']) $v = "beamfrom";
		if ($_GET['et_x']  && $_GET['shd']) $v = "energietrans";
		break;
	case "ass":
		if (!check_int($_GET['shd'])) die(show_error(902));
		$col->loadstat($_GET['shd']);
		if ($col->stat == 0) die(show_error(902));
		if (checkcolsectors($col->stat) == 0)
		{
			$v = "main";
			break;
		}
		if ($_GET['m'] == "bt" && $_GET['shd']) $v = "beamtos";
		if ($_GET['m'] == "bf"  && $_GET['shd']) $v = "beamfroms";
		if ($_GET['et_x']  && $_GET['shd']) $v = "energietranss";
		//$v = "main";
		break;
	case "bm":
		if (!check_int($_GET['fid'])) exit;
		$col->loadfield($_GET['fid'],$_GET['id']);
		if ($col->fdd == 0) exit;
		$v = "buildmenu";
		break;
	case "mor":
		if (!check_int($_GET['fid'])) exit;
		$col->loadfield($_GET['fid'],$_GET['id']);
		if ($col->fdd == 0) exit;
		$v = "modrep";
		break;
	case "tpr":
		if (!check_int($_GET['fid'])) exit;
		$col->loadfield($_GET['fid'],$_GET['id']);
		if ($col->fdd == 0) exit;
		$v = "torprep";
		break;		
	case "trs":
		if (!check_int($_GET['fid'])) exit;
		$col->loadfield($_GET['fid'],$_GET['id']);
		if ($col->fdd == 0) exit;
		$v = "trash";
		break;		
	case "sf":
		if (!check_int($_GET['sx']) || !check_int($_GET['sy'])) exit;
		if ($_GET['sx'] < $col->sx-1 || $_GET['sx'] > $col->sx+1) exit;
		if ($_GET['sy'] < $col->sy-1 || $_GET['sy'] > $col->sy+1) exit;
		if ($_GET['sx'] == $col->sx && $_GET['sy'] == $col->sy)
		{
			$v = "listorbititems";
			break;
		}
		if ($col->eps == 0)
		{
			$result = "Zum Scan wird mindestens 1 Energie benötigt";
			$v = "showcolony";
			break;
		}
		$col->loadships($_GET['sx'],$_GET['sy']);
		$db->query("UPDATE stu_colonies SET eps=eps-1 WHERE id=".$col->id." LIMIT 1");
		$col->eps-=1;
		$v = "sectorscan";
		break;
	case "loi":
		$v = "listorbititems";
		break;
	case "cto":
		$v = "coltradeoffer";
		break;
	case "gse":
		$v = "geschaltung";
		break;
	case "pdl":
		$v = "prodlimit";
		break;		
	case "sb":
		if ($col->check_werft($_GET['id']) == 0) die(show_error(902));
		$v = "schiffbau";
		break;
	case "sm":
		if ($col->check_werft($_GET['id']) == 0) die(show_error(902));
		if (!check_int($_GET['rid']) || !$col->checkrump($_GET['rid'])) die(show_error(902));
		// if ($_GET['rid'] > 1200 && $_GET['rid'] < 2000 && $col->getwerfttype($_GET['id']) == 300)
		// {
			// $result = "Dieser Schifftyp kann nur in erweiterten Werften gebaut werden";
			// $v = "schiffbau";
			// break;
		// }
		$col->getrumpbyid($_GET['rid']);
		if ($col->rump == 0) die(show_error(902));
		if ($col->rump[slots] > 0) die(show_error(902));
		$v = "selectmodules";
		break;
	case "rep":
		$v = "repairbuilding";
		if (!check_int($_GET['fid'])) exit;
		$col->loadfield($_GET['fid'],$_GET['id']);
		if ($col->fdd == 0) exit;
		if ($col->fdd[maxintegrity] <= $col->fdd[integrity]) exit;
		$col->loadrepaircost();
		break;
	case "coa":
		$v = "colaufgeben";
		break;
	case "sfl":
		$v = "sectorflights";
		break;
	case "bat":
		if ($col->check_werft($_GET['id']) == 0) die(show_error(902));
		$v = "battery";
		break;
	case "tel":
		if ($db->query("SELECT field_id FROM stu_colonies_fielddata WHERE (buildings_id=403 OR buildings_id=314) AND colonies_id=".$col->id,1) == 0) die(show_error(902));
		$v = "teleskop";
		break;
	case "scn":
		if ($db->query("SELECT field_id FROM stu_colonies_fielddata WHERE (buildings_id=403 OR buildings_id=314) AND colonies_id=".$col->id,1) == 0) die(show_error(902));
		$v = "scansector";
		break;
	case "scsy":
		if (!check_int($_GET['sys']) || $_GET['sys'] < 1) die(show_error(902));
		if ($db->query("SELECT field_id FROM stu_colonies_fielddata WHERE (buildings_id=403 OR buildings_id=314) AND colonies_id=".$col->id,1) == 0) die(show_error(902));
		$v = "scansystem";
		break;
	case "resp":
		if ($col->check_werft($_GET['id']) == 0) die(show_error(902));
		$v = "repairship";
		break;
	case "war":
		if ($col->check_warwerft($_GET['id']) == 0) die(show_error(902));
		$v = "wartung";
		break;
	case "dmt":
		if ($col->check_werft($_GET['id']) == 0) die(show_error(902));
		$v = "demontship";
		break;
	case "syv":
		$v = "sysview";
		break;
}
if ($v == "main")
{
	pageheader("/ <b>Kolonien</b>");
	if (check_int($_GET["delid"]) && $_SESSION['preps'] == $_GET['ps']) meldung($col->giveupcol($_GET["delid"]));
	$list = $col->getcolonylist();
	$collist = "<table class=tcal>
	<th width=30></th><th>Name</th><th width=30></th><th>System</th><th>Sektor</th><th>Energie</th><th>Waren</th><th>Flottenunterhalt</th>";
	if (mysql_num_rows($list) == 0) $collist .= "<tr><td colspan=8 align=center>Keine Kolonien vorhanden</td></tr>";
	else
	{
		while($data=mysql_fetch_assoc($list))
		{
			$cmsg = $col->getcolattackstring($data['id']);
			$vbn += ceil(($data['bev_free']+$data['bev_work'])/10);
			$gs = $db->query("SELECT SUM(count) FROM stu_colonies_storage WHERE colonies_id=".$data['id'],1);
			$fe = $db->query("SELECT SUM(b.eps_proc) FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.aktiv=1 AND a.colonies_id=".$data['id'],1);			
			$fd = $db->query("SELECT SUM(a.count) as gc,a.goods_id,c.name FROM stu_buildings_goods as a LEFT JOIN stu_colonies_fielddata as b USING(buildings_id) LEFT JOIN stu_goods as c USING(goods_id) WHERE b.colonies_id=".$data['id']." AND b.aktiv=1 GROUP BY a.goods_id");
			
			$bresult = $db->query("SELECT b.goods_id, SUM(b.count) as gc FROM stu_colonies_fielddata as a LEFT JOIN stu_colonies_bonus as b ON a.buildings_id = b.buildings_id LEFT JOIN stu_colonies as c ON c.id = a.colonies_id WHERE a.colonies_id=".$data['id']." AND a.aktiv=1 AND b.colonies_classes_id = c.colonies_classes_id GROUP by b.goods_id;");
			
			unset($colobonus);
			while($b=mysql_fetch_assoc($bresult))
			{
				$colobonus[$b['goods_id']] = $b['gc'];
			}
			$fe += $colobonus[0];
			
			$cw = $db->query("SELECT SUM(b.points) FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.colonies_id=".$data['id']." AND (a.aktiv=1 OR (a.aktiv=0 AND (ISNULL(b.is_activateable) OR b.is_activateable='0' OR b.is_activateable='')))",1);
			if ($db->query("SELECT field_id FROM stu_colonies_fielddata WHERE buildings_id=54 AND aktiv=1 AND colonies_id=".$data['id']." LIMIT 1",1) != 0) $fc = $db->query("SELECT COUNT(field_id) FROM stu_colonies_fielddata WHERE (buildings_id=2 OR buildings_id=9) AND aktiv=1 AND colonies_id=".$data['id'],1);
			$fco += $fc;
			$collist .= "<tr><td style=\"text-align:center;\"><a href=?p=colony&s=sc&id=".$data['id']."><img src=".$gfx."/planets/".$data['colonies_classes_id'].($data['schilde_status'] == 1 ? "s" : "").".gif border=0></a></td>
			<td><a href=?p=colony&s=sc&id=".$data['id'].">".stripslashes($data['name'])."</a> (".$data['id'].")</td><td><img src=".$gfx."/systems/".$data['type']."ms.png width=50px height=50px title=\"".$data['sname']."-System (".$data['cx']."|".$data['cy'].")\"></td><td>".$data['sname']." (".$data['cx']."|".$data['cy'].")</td><td>".$data['sx']."|".$data['sy']." (<a href=?p=colony&s=sfl&id=".$data['id'].">".$col->getsectorflights($data['sx'],$data['sy'],$data['systems_id'])."</a>)</td>
			<td>".$data['eps']."/".$data['max_eps']." (".($fe >= 0 ? "<font color=#30BF00><b>+".$fe."</b></font>" : "<font color=#C40005><b>".$fe."</b></font>").")</td>
			<td>";
			if (mysql_num_rows($fd) == 0 && ($data['bev_free'] > 0 || $data['bev_work'] > 0))
			{
				$collist .= "<br><img src=".$gfx."/goods/1.gif> -".ceil(($data['bev_free']+$data['bev_work'])/10);
				$ss = $db->query("SELECT count FROM stu_colonies_storage WHERE colonies_id=".$data['id']." AND goods_id=1 LIMIT 1",1);
				$lr = floor($ss/abs(ceil(($data['bev_free']+$data['bev_work'])/10)));
				$collist .= "&nbsp;(".($lr <= 5 ? ($lr <= 2 ? "<font color=FF0000>".$lr."</font>" : "<font color=FFFF00>".$lr."</font>") : $lr)." Ticks)<br>";
			}
			while($d=mysql_fetch_assoc($fd))
			{
				$d['gc'] += $colobonus[$d['goods_id']];
				if (!$ck)
				{
					if ($d['goods_id'] == 1) {
						$d['gc']+= 2*$fc;
					}
					if ($d['goods_id'] > 1)
					{
						$collist .= "<img src=".$gfx."/goods/1.gif> <font color=#FF0000>-".ceil(($data['bev_free']+$data['bev_work'])/10)."</font>";
						$ss = $db->query("SELECT count FROM stu_colonies_storage WHERE colonies_id=".$data['id']." AND goods_id=1 LIMIT 1",1);
						$lr = floor($ss/abs(ceil(($data['bev_free']+$data['bev_work'])/10)));
						$collist .= "&nbsp;(".($lr <= 5 ? ($lr <= 2 ? "<font color=FF0000>".$lr."</font>" : "<font color=FFFF00>".$lr."</font>") : $lr)." Ticks)<br>";
						$collist .= "<br>";
					}
					else {
						$d['gc'] -= ceil(($data['bev_free']+$data['bev_work'])/10);
					}
					$ck = 1;
				}
				if ($d['gc'] > 0 || $d['gc'] < 0) $gc[$d['goods_id']] = $d['gc'];
				if ($d['gc'] < 0)
				{
					$collist .= "<img src=".$gfx."/goods/".$d['goods_id'].".gif title=\"".ftit($d['name'])."\"> <font color=#FF0000>".$d['gc']."</font>";
					$ss = $db->query("SELECT count FROM stu_colonies_storage WHERE colonies_id=".$data['id']." AND goods_id=".$d['goods_id']." LIMIT 1",1);
					$lr = @floor($ss/abs($d['gc']));
					$collist .= "&nbsp;(".($lr <= 5 ? ($lr <= 2 ? "<font color=FF0000>".$lr."</font>" : "<font color=FFFF00>".$lr."</font>") : $lr)." Ticks)";
					$collist .= "<br>";
				}
			}
			mysql_free_result($fd);
			$gres = $db->query("SELECT a.goods_id,a.name,b.count FROM stu_goods as a LEFT JOIN stu_colonies_storage as b ON b.goods_id=a.goods_id AND b.colonies_id=".$data['id']." WHERE a.goods_id<80 ORDER BY a.sort");
			$foo = 0;
			while($gd=mysql_fetch_assoc($gres))
			{
				if (!$gd['count'] && !$gc[$gd['goods_id']]) continue;
				if ($foo == 0) $tt .= "<tr>";
				$foo++;
				$tt .= "<td><img src=".$gfx."/goods/".$gd['goods_id'].".gif> ".(!$gd['count'] ? 0 : $gd['count'])."</td><td>".(!$gc[$gd['goods_id']] || $gc[$gd['goods_id']] == 0 ? 0 : ($gc[$gd['goods_id']] < 0 ? "<font color=#FF0000>".$gc[$gd['goods_id']]."</font>" : "<font color=green>+".$gc[$gd['goods_id']]."</font>"))."</td>";
				if ($foo == 2)
				{
					$tt .= "</tr>";
					$foo = 0;
				}
			}
			mysql_free_result($gres);
			$foo = 0;
			$collist .= "<img src=".$gfx."/icons/storage.gif onmouseover=\"return overlib('<table class=tcal><th colspan=4>Lager und Produktion</th>".$tt."</table>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER);\" onmouseout=\"nd();\" onmouseover=\"return escape('<b>Lager - Produktion/Verbrauch</b>".$tt."')\"> ".round(@(100/$data[max_storage])*$gs)."%</td>";
			
			// $crewmax = (floor(($data['bev_free']+$data['bev_work'])/10));
			$fleetpoints = $col->getFleetPoints($data['id']);
			$crewpic = "<img src=".$gfx."/icons/crew".$_SESSION[race].".gif border=0 title='Schiffscrew'>";		
			
			$pc = $col->getPoints($data['id'],"pcrew");
			$pm = $col->getPoints($data['id'],"pmaintain");
			$ps = $col->getPoints($data['id'],"psupply");
			
			
			$fleetinfo = "";
			if ($pc > 0) $fleetinfo .= "<img src=".$gfx."/icons/pcrew.gif border=0 title='Crewpunkte'> ".$pc."<br>";
			if ($pm > 0) $fleetinfo .= "<img src=".$gfx."/icons/pmaintain.gif border=0 title='Wartungspunkte'> ".$pm."<br>";
			if ($ps > 0) $fleetinfo .= "<img src=".$gfx."/icons/psupply.gif border=0 title='Versorgungspunkte'> ".$ps."<br>";
			
			if ($gc['8'] > 0) $fleetinfo .= "<img src=".$gfx."/goods/8.gif border=0 title='Dilithium'> ".$gc['8']."<br>";
			

			$collist .= "<td align=left>".$fleetinfo."</td></tr>";

			unset($ck);
			unset($tt);
			unset($fc);
			unset($gc);
			$nw+=$cw;
			$gw+=$data['lastrw'];
			if ($cmsg != "") $collist .= "<tr><td colspan=7>&nbsp;&nbsp;&nbsp;".$cmsg."</td></tr>";
		}
		// echo "<tr><td colspan=7></td><td align=center>= ".$nw." (".$gw.")</td></tr>";
	}
	$collist .= "</table>";
	
	$result = $col->getbuildingprogress();
	if (mysql_num_rows($result) > 0)
	{
		$buildprogress .= "<table class=tcal>";
		while($data=mysql_fetch_assoc($result))
		{
			if ($lc != $data['id'])
			{
				$buildprogress .= "<tr><td style=\"width:50px;height:50px;text-align:center;\"><a href=?p=colony&s=sc&id=".$data['id']."><img src=".$gfx."/planets/".$data['colonies_classes_id'].".gif border=0></a></td><td colspan=2><a href=?p=colony&s=sc&id=".$data['id'].">".stripslashes($data['cname'])."</a></td></tr>";
				$lc = $data['id'];
			}
			$buildprogress .= "<tr><td style=\"width:50px;text-align:center;\"></td><td width=30 class=none style=\"background-image:url(".$gfx."/fields/".$data['type'].".gif); background-repeat: no-repeat; background-position:center; width:30px;\"><img src=".$gfx."/buildings/".$data['buildings_id']."/".buildingpic($data['buildings_id'],$data['type']).".png></td><td>".stripslashes($data['name'])." auf Feld ".$data['field_id'].". Fertigstellung: ".date("d.m.Y H:i",$data['aktiv'])."</td></tr>";
			
		}
		$buildprogress .= "</table>";
	} else $buildprogress = 0;
	
	$lc = 0;
	$result = $col->getterraformprogress();
	if (mysql_num_rows($result) > 0)
	{
		$terraformprogress .= "<table class=tcal>";
		while($data=mysql_fetch_assoc($result))
		{
			if ($lc != $data['id'])
			{
				$terraformprogress .= "<tr><td style=\"width:50px;height:50px;text-align:center;\"><a href=?p=colony&s=sc&id=".$data['id']."><img src=".$gfx."/planets/".$data['colonies_classes_id'].".gif border=0></a></td><td colspan=2><a href=?p=colony&s=sc&id=".$data['id'].">".stripslashes($data['cname'])."</a></td></tr>";
				$lc = $data['id'];
			}
			$terraformprogress .= "<tr><td style=\"width:50px;text-align:center;\"></td><td width=30 style=\"background-image:url(".$gfx."/fields/".$data['v_feld'].".gif); background-repeat: no-repeat; background-position:center; width:30px;\"><img src=".$gfx."/fields/dozer.png></td><td>Vorgang: ".stripslashes($data['name'])." - Fertigstellung: ".date("d.m.Y H:i",$data['terraformtime'])."</td></tr>";
		}
		$terraformprogress .= "</table>";
	} else $terraformprogress = 0;
	
	$lc = 0;
	$result = $col->getshipbuildprogress();
	if (mysql_num_rows($result) > 0)
	{
		$shipprogress = "<table class=tcal>";
		while($data=mysql_fetch_assoc($result))
		{
			if ($lc != $data['colonies_id'])
			{
				$shipprogress .= "<tr><td style=\"width:50px;height:50px;text-align:center;\"><a href=?p=colony&s=sc&id=".$data['colonies_id']."><img src=".$gfx."/planets/".$data['colonies_classes_id'].".gif border=0></a></td><td colspan=2><a href=?p=colony&s=sc&id=".$data['colonies_id'].">".stripslashes($data['name'])."</a></td></tr>";
				$lc = $data['colonies_id'];
			}
			$shipprogress .= "<tr><td style=\"width:50px;text-align:center;\"></td><td width=30><img src=".$gfx."/ships/".$data['rumps_id'].".gif></td><td>Bauplan: ".(!$data['pname'] ? "Standard Kolonisationsschiff" : stripslashes($data['pname']))." - Fertigstellung: ".date("d.m.Y H:i",$data['buildtime'])."</td></tr>";
		}
		$shipprogress .= "</table>";
	} else $shipprogress = 0;
	
	$production .= "<table class=tcal><tr>";
	$result = $col->getprodoverview();
	$bonus = $col->getbonusoverview();
	while($data=mysql_fetch_assoc($bonus))
	{
		$bonusprod[$data['goods_id']] = $data['gc'];
	}
	$i = 0;
	while($data=mysql_fetch_assoc($result))
	{
		if ($data['goods_id'] == 1)
		{
			$data['pc'] -= $vbn;
			$data['pc'] += 2*$fco;
		}
		if ($data['pc'] == 0) continue;
		
		$data['pc'] += $bonusprod[$data['goods_id']];
		
		if ($i == 5)
		{
			$production .="</tr><tr>";
			$i = 0;
		}
		$production .= "<td style=\"width:20%;vertical-align:middle;\">&nbsp;<img src=".$gfx."/goods/".$data['goods_id'].".gif title=\"".ftit($data['name'])."\"> ".($data['pc'] < 0 ? "<font color=#FF0000>".$data['pc']."</font>" : "<font color=Green>+".$data['pc']."</font>")."</td>";
		$i++;
	}
	for ($ii = $i; $ii < 5; $ii++) $production .= "<td style=\"width:20%;\"></td>";
	$production .= "</tr></table>";


	echo "<table width=100% class=tablelayout><tr>";
	echo "<td class=tablelayout colspan=2>";
	echo fixedPanel(2,"Kolonieliste","mcoll",$gfx."/buttons/icon/planet.gif",$collist);
	echo "</td></tr><tr>";
	echo "<td class=tablelayout style=\"width:50%;\">";
	if ($buildprogress) echo fixedPanel(4,"Aktuelle Bauaufträge","mprod",$gfx."/buttons/icon/time.gif",$buildprogress)."<br>";
	if ($terraformprogress) echo fixedPanel(3,"Aktuelle Terraformingvorgänge","mprod",$gfx."/buttons/icon/time.gif",$terraformprogress)."<br>";
	if ($shipprogress) echo fixedPanel(1,"Aktuelle Schiffbauaufträge","mprod",$gfx."/buttons/icon/ship.gif",$shipprogress)."<br>";
	echo "</td>";		
	echo "<td class=tablelayout  style=\"vertical-align:top;width:50%;\">";
	echo fixedPanel(3,"Gesamt-Produktion","mprod",$gfx."/buttons/icon/storage.gif",$production);
	echo "</td>";

	echo "</tr></table>";
}
if ($v == "showcolony")
{

	if ($_GET['a'] == "cn" && strlen(strip_tags($_GET['cn'])) > 3 && $_SESSION['preps'] == $_GET['ps']) $result = $col->changename($_GET['cn']);
	if ($_GET['a'] == "bu" && check_int($_GET['bu']) && check_int($_GET['fid']) && $_SESSION['preps'] == $_GET['ps']) $result = $col->build($_GET['bu'],$_GET['fid']);
	if ($_GET['a'] == "db" && check_int($_GET['fid']) && $_SESSION['preps'] == $_GET['ps']) $result = $col->deactivatebuilding($_GET['fid']);
	if ($_GET['a'] == "ab" && check_int($_GET['fid']) && $_SESSION['preps'] == $_GET['ps']) $result = $col->activatebuilding($_GET['fid']);
	if ($_GET['a'] == "dmb" && check_int($_GET['fid']) && $_SESSION['preps'] == $_GET['ps']) $result = $col->removebuilding($_GET['fid'],$_GET['nxb']);
	if ($_GET['a'] == "dmpl" && check_int($_GET['fid']) && $_SESSION['preps'] == $_GET['ps']) $result = $col->removeplatform($_GET['fid']);
	if ($_GET['a'] == "bt" && check_int($_GET['shd']) && (is_array($_GET['good']) && is_array($_GET['value']) || check_int($_GET['cb'])) && $_SESSION['preps'] == $_GET['ps']) $result = $col->beamto($_GET['shd'],$_GET['good'],$_GET['value'],$_GET['cb']);
	if ($_GET['a'] == "bf" && check_int($_GET['shd']) && (is_array($_GET['good']) && is_array($_GET['value']) || check_int($_GET['cb'])) && $_SESSION['preps'] == $_GET['ps']) $result = $col->beamfrom($_GET['shd'],$_GET['good'],$_GET['value'],$_GET['cb']);

	if ($_GET['a'] == "bts" && check_int($_GET['shd']) && (is_array($_GET['good']) && is_array($_GET['value']) || check_int($_GET['cb'])) && $_SESSION['preps'] == $_GET['ps']) $result = $col->beamtostat($_GET['shd'],$_GET['good'],$_GET['value'],$_GET['cb']);
	if ($_GET['a'] == "bfs" && check_int($_GET['shd']) && (is_array($_GET['good']) && is_array($_GET['value']) || check_int($_GET['cb'])) && $_SESSION['preps'] == $_GET['ps']) $result = $col->beamfromstat($_GET['shd'],$_GET['good'],$_GET['value'],$_GET['cb']);

	if ($_GET['a'] == "et" && check_int($_GET['shd']) && (check_int($_GET['ec']) || $_GET['ec'] == "max")) $result = $col->etrans($_GET['shd'],$_GET['ec']);
	if ($_GET['a'] == "sam" && check_int($_GET['fid'])) $result = $col->swapactivatemode($_GET['fid']);
	if ($_GET['a'] == "upg" && check_int($_GET['fid']) && check_int($_GET['ubu']) && $_SESSION['preps'] == $_GET['ps']) $result = $col->upgradebuilding($_GET['ubu'],$_GET['fid']);
	if ($_GET['a'] == "trf" && check_int($_GET['fid']) && check_int($_GET['tofid']) && $_SESSION['preps'] == $_GET['ps']) $result = $col->terraform($_GET['fid'],$_GET['tofid']);
	if ($_GET['a'] == "crb" && check_int($_GET['fid']) && check_int($_GET['nid'])) $result = $col->cycleresearchbuildings($_GET['fid'],$_GET['nid']);
	if ($_GET['a'] == "sew" && ($_GET['ew'] == 1 || $_GET['ew'] == 0) && $_SESSION['preps'] == $_GET['ps']) $result = $col->seteinwanderung($_GET['ew']);
	if ($_GET['a'] == "sbs" && check_int($_GET['bsc']) && $_SESSION['preps'] == $_GET['ps']) $result = $col->seteinwanderungslimit($_GET['bsc']);
	if ($_GET['a'] == "rmo" && is_array($_GET['mod']) && $_SESSION['preps'] == $_GET['ps']) $result = $col->modulherstellung($_GET['mod']);
	if ($_GET['a'] == "tpo" && is_array($_GET['mod']) && $_SESSION['preps'] == $_GET['ps']) $result = $col->torpedoherstellung($_GET['mod']);
	if ($_GET['a'] == "rju" && is_array($_GET['mod']) && $_SESSION['preps'] == $_GET['ps']) $result = $col->modulinstandsetzung($_GET['mod']);
	if ($_GET['a'] == "ran" && check_int($_GET['mod']) && $_SESSION['preps'] == $_GET['ps']) $result = $col->modulanalyse($_GET['mod']);
	
	if ($_GET['a'] == "cdc" && $_SESSION['preps'] == $_GET['ps']) $result = $col->kernkopie();
	
	if ($_GET['a'] == "rto" && is_array($_GET['mod']) && $_SESSION['preps'] == $_GET['ps']) $result = $col->torpedoherstellung($_GET['mod']);
	// here
	if ($_GET['a'] == "ets" && is_array($_GET['mod']) && $_SESSION['preps'] == $_GET['ps']) $result = $col->verschrotten($_GET['mod']);
	
	if ($_GET['a'] == "rsh" && is_array($_GET['mod']) && $_SESSION['preps'] == $_GET['ps']) $result = $col->workbeeherstellung($_GET['mod']);
	// if ($_GET['a'] == "lcs" && $_SESSION['preps'] == $_GET['ps']) $result = $col->launchcolship();
	if ($_GET['a'] == "eba" && is_array($_GET['bl']) && $_SESSION['preps'] == $_GET['ps']) $result = $col->ebatt($_GET['bl']);
	if ($_GET['a'] == "lsb" && (check_int($_GET['c']) || $_GET['c'] == "m") && $_SESSION['preps'] == $_GET['ps']) $result = $col->loadshields($_GET['c']);
	if ($_GET['a'] == "met" && is_array($_GET['et']) && $_SESSION['preps'] == $_GET['ps']) $result = $col->massetrans($_GET['et']);
	
	if ($_GET['a'] == "bcs" && $_SESSION['preps'] == $_GET['ps']) $result = $col->createcolship();
	if ($_GET['a'] == "xug" && $_SESSION['preps'] == $_GET['ps']) $result = $col->digground();
	
	
	if ($_GET['a'] == "bmode" && $_SESSION['preps'] == $_GET['ps']) $result = $col->switchCenterMode(1);
	if ($_GET['a'] == "nmode" && $_SESSION['preps'] == $_GET['ps']) $result = $col->switchCenterMode(101);
	if ($_GET['a'] == "emode" && $_SESSION['preps'] == $_GET['ps']) $result = $col->switchCenterMode(102);
	
	
	pageheader("/ <a href=?p=colony>Kolonien</a> / <b>".stripslashes($col->name)."</b>");
	if ($result)
	{
		$col = new colony;
		meldung($result);
	}
	$col->loadcolstorage();
	$col->loadsysteminfo();
	$col->loadresearchpoints();

	echo "<script language=\"Javascript\">
	function showsysinfo(rel,sys)
	{
		elt = 'contentdiv';
		sendRequest('backend/colony/planetlist.php?PHPSESSID=".session_id()."&id=' + sys + '');
		positionElement(rel,document.getElementById('contentdiv'),510);		
	}
	function getterrainfo(name,statusbar,percentage,finishdate,pica,picb)
	{
		return overlib('<table class=tcal><th colspan=3>Terraforminginfo</th><tr><td colspan=3>' + name + '</td></tr><tr><td colspan=3>Status: ' + statusbar + ' ' + percentage + '%</td></tr><tr><td colspan=3>Fertigstellung: ' + finishdate + '</td></tr><tr><td align=center><img src=".$gfx."/fields/' + pica + '.gif></td><td align=center><img src=".$gfx."/buttons/b_to1.gif></td><td align=center><img src=".$gfx."/fields/' + picb + '.gif></td></tr></table>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER);
	}
	function getbuildprogressinfo(name,statusbar,percentage,finishdate,pica,picb,picc)
	{
		return overlib('<table class=tcal><th colspan=3>Bauinfo</th><tr><td colspan=3>' + name + '</td></tr><tr><td colspan=3>Status: ' + statusbar + ' ' + percentage + '%</td></tr><tr><td colspan=3>Fertigstellung: ' + finishdate + '</td></tr><tr><td align=center><img src=".$gfx."/fields/' + pica + '.gif></td><td align=center><img src=".$gfx."/buttons/b_to1.gif></td><td align=center style=\'background-image:url(".$gfx."/fields/' + pica + '.gif); background-repeat: no-repeat; background-position:center;\'><img src=".$gfx."/buildings/' + picb + '/' + picc + '.png></td></tr></table>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER);
	}
	function field_action(rel,fid)
	{
		//elt = 'fielda';
		//get_window(elt);
		//sendRequest('backend/colony/fieldaction.php?PHPSESSID=".session_id()."&fid='+fid+'&id=".$col->id."');
		
		elt = 'contentdiv';
		sendRequest('backend/colony/fieldaction.php?PHPSESSID=".session_id()."&fid='+fid+'&id=".$col->id."');
		positionElement(rel,document.getElementById('contentdiv'),510);			
		
	}
	function get_window(elt)
	{
		return overlib('<div id='+elt+'></div>', BACKGROUND, 'none', CELLPAD, 0, 0, 0, 0, CENTER, STICKY, DRAGGABLE, ALTCUT, EXCLUSIVE, WIDTH, 500);
	}
	function showConfirm(fid)
	{
		//document.getElementById(\"dmc\").innerHTML = \"Soll das Gebäude wirklich demontiert werden? <a href=?ps=".$_SESSION['pagesess']."&p=colony&s=sc&id=".$_GET['id']."&a=dmb&fid=\"+fid+\"><font color=#FF0000>Ja</font></a>\";
		//document.getElementById(\"dmc\").style.border = \"1px solid #262323\";
		document.getElementById(\"dmc\").style.visibility = \"visible\";
	}
	function showConfirmplatform(fid)
	{
		document.getElementById(\"dmc\").innerHTML = \"Soll das Gebäude wirklich zur Plattform zurückgebaut werden? <a href=?ps=".$_SESSION['pagesess']."&p=colony&s=sc&id=".$_GET['id']."&a=dmpl&fid=\"+fid+\"><font color=#FF0000>Ja</font></a>\";
		document.getElementById(\"dmc\").style.border = \"1px solid #262323\";
	}
	</script>";
	if ($col->bev_free < 0) {
		$col->bev_free = 0;
	}
	$fm = 1;
	$j=1;
	$i=1;
	$chg = $col->dn_change-time();
	$ub = $col->bev_work;
	$fb = $col->bev_free;
	$wr = $col->bev_max-$col->bev_work-$col->bev_free;
	// $faction = ($_SESSION["race"].($_SESSION["subrace"] != 0 ? "_".$_SESSION["subrace"] : ""));
	$faction = $col->faction;
	while($i<=$col->bev_max)
	{
		if ($ub > 0)
		{
			if ($ub >= 10 && $i + 10 <= $col->bev_max) { $beva .= "<img src=".$gfx."/bev/bev_used_5_".$faction.".gif border=0>"; $ub-=10; $j+=26; $i+=10; }
			else { $beva .= "<img src=".$gfx."/bev/bev_used_1_".$faction.".gif border=0>"; $ub-=1; $j+=11; $i+=1; }
		}
		if ($fb > 0 && $ub == 0)
		{
			if ($fb >= 10 && $i + 10 <= $col->bev_max) { $beva .= "<img src=".$gfx."/bev/bev_unused_5_".$faction.".gif border=0>"; $fb-=10; $j+=26; $i+=10; }
			else { $beva .= "<img src=".$gfx."/bev/bev_unused_1_".$faction.".gif border=0>"; $fb-=1; $j+=11; $i+=1; }
		}
		if ($wr > 0 && $ub == 0 && $fb == 0)
		{
			if ($wr >= 10) { $beva .= "<img src=".$gfx."/bev/bev_free_5_".$faction.".gif border=0>"; $wr-=10; $j+=26; $i+=10; }
			else { $beva .= "<img src=".$gfx."/bev/bev_free_1_".$faction.".gif border=0>"; $wr-=1; $j+=11; $i+=1; }
		}
		if ($j >= 600) { $beva.="<br>"; $j=0; }
	}
	if ($col->bev_free+$col->bev_work > $col->bev_max)
	{
		$ob = ($col->bev_free+$col->bev_work) - $col->bev_max;
		while (0 < $ob)
		{
			if ($ob >= 10) { $beva .= "<img src=".$gfx."/bev/bev_over_5_".$faction.".gif border=0>"; $ob-=10; $j+=26; }
			else { $beva .= "<img src=".$gfx."/bev/bev_over_1_".$faction.".gif border=0>"; $ob-=1; $j+=11; }
			if ($j >= 600) { $beva.="<br>"; $j=0; }
		}
	}
	$fe = $db->query("SELECT SUM(b.eps_proc) FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.aktiv=1 AND a.colonies_id=".$_GET['id'],1);
	$fe += $col->goods[0];
	
	$se = $col->eps;
	if ($fe < 0)
	{
		$se -= abs($fe);
		$em = "<img src=".$gfx."/em_t.gif width=".abs($fe)." height=9>";
	}
	if ($fe > 0 && $col->eps < $col->max_eps) $ep = "<img src=".$gfx."/ep_t.gif width=".($se+$fe > $col->max_eps ? $col->max_eps-$se : $fe)." height=9>";
	if ($se > 0) $ev = "<img src=".$gfx."/ev_t.gif width=".$se." height=9>";
	if ($se+abs($fe) < $col->max_eps) $el = "<img src=".$gfx."/el_t.gif width=".($col->max_eps-($se+abs($fe)))." height=9>";
	
	
	
	$i=0;
	$j=1;
	$chg = $col->dn_change-time();
	
	$andstring = "";
	if ($col->ground_enabled == 0 && !$col->is_moon) $andstring = "AND a.field_id < 81";
	if ($col->ground_enabled == 0 && $col->is_moon) $andstring = "AND a.field_id < 50";
	
	$fieldsstring = "<table class=\"mapfields suppressMenuColors\" style='margin-left:auto;margin-right:auto;'><tr>";
	
	$fd = $db->query("SELECT a.type,a.field_id,a.buildings_id,a.aktiv,b.name,b.buildtime,b.is_activateable FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING (buildings_id) WHERE a.colonies_id=".$_GET['id']." ".$andstring." ORDER BY a.field_id ASC LIMIT 100");
	while($ret = mysql_fetch_assoc($fd)) $arr[] = $ret;
	$ftr = $db->query("SELECT a.field_id,a.terraforming_id,a.terraformtime,b.name,b.t_time,b.v_feld,b.z_feld FROM stu_colonies_terraforming as a LEFT JOIN stu_terraforming as b USING(terraforming_id) WHERE a.colonies_id=".$col->id);
	while($data=mysql_fetch_assoc($ftr)) $ftf[$data['field_id']] = $data;
	$col->is_moon == 1 ? $t = 7 : $t = 10;

	@usort($arr, "cmp");
	foreach($arr as $key => $data)
	{
		if ($data['buildings_id'] == 54 && $data['aktiv'] == 1) $wks = 1;
		if ($wks == 1 && $data['aktiv'] == 1 && ($data['buildings_id'] == 2 || $data['buildings_id'] == 9)) $fc+=1;
		if ($i%$t==0) { $fieldsstring .= "</tr><tr>"; $j=1; }
		$tf = $ftf[$data['field_id']];
		// ".$data['type'].get_dn_state($j,$chg,$col->dn_mode,$data['field_id'],$col->is_moon,$col->colonies_classes_id).
		$buildpic = buildingpic($data['buildings_id'],$data['type']);
		$buildpicbase = buildingpic($data['buildings_id'],$data['type']);
		
		if ($data['buildings_id'] > 0) {
			if ($data['aktiv'] == 0) {
				if ($data['is_activateable']) {
					$fclass = "fielddeactivated";	
				} else {
					$fclass = "fieldactive";
				}
				
			} else {
				if ($data['aktiv'] > 1) {
					$fclass = "fieldconstruction";		
				} else {
					$fclass = "fieldactive";
				}				
			}
		} else {
			if (is_array($tf)) {
				$fclass = "fieldterraform";
			} else {
				$fclass = "fieldnormal";
			}
		}
		
		if ($data['buildings_id'] > 0) $fieldsstring .= "<td class=".$fclass." style='background-image:url(".$gfx."/fields/".$data['type'].".gif); background-repeat: no-repeat; background-position:center;'><a href=\"javascript:void(0);\" ><img onClick=\"field_action(this,".$data['field_id'].");\" src=".$gfx."/buildings/".$data['buildings_id']."/".$buildpic.".png border=0 ".($data['aktiv'] > 1 ? "onmouseover=\"getbuildprogressinfo('".stripslashes($data['name'])."','".renderstatusbar(time()-($data['aktiv']-$data['buildtime']),$data['buildtime'],"gre")."','".getpercentage(time()-($data['aktiv']-$data['buildtime']),$data['buildtime'])."','".date("d.m H:i",$data['aktiv'])."','".$data['type']."','".$data['buildings_id']."','".$buildpicbase."');\" onmouseout=\"nd();\"" : "title=\"".$data['name'].($data['is_activateable'] == 1 ? " (".($data['aktiv'] == 0 ? "deaktiviert" : "aktiviert").")" : "")." auf ".getnamebyfield($data['type'])."\"")."></a></td>";
		else {
			if (is_array($tf)) {
				$fieldsstring .= "<td class=".$fclass." style='background-image:url(".$gfx."/fields/".$data['type'].".gif); background-repeat: no-repeat; background-position:center;'><a href=\"javascript:void(0);\" ><img onClick=\"field_action(this,".$data['field_id'].");\" src=".$gfx."/fields/dozer.png border=0 title=\"Aktives Terraforming\"></a></td>";
				// $fieldsstring .= "<td class=".$fclass."><a href=\"javascript:void(0);\" ><img onClick=\"field_action(this,".$data['field_id'].");\" src=".$gfx."/".(is_array($tf) ? "terraforming/".$tf['terraforming_id'].".gif" : "fields/".$data['type'].".gif")." border=0 ".(is_array($tf) ? "onmouseover=\"getterrainfo('".stripslashes($tf['name'])."','".renderstatusbar(time()-($tf['terraformtime']-$tf['t_time']),$tf['t_time'],"gre")."','".getpercentage(time()-($tf['terraformtime']-$tf['t_time']),$tf['t_time'])."','".date("d.m H:i",$tf['terraformtime'])."','".($col->colonies_classes_id == 9 && $tf['terraforming_id'] == 99 ? 53 : $tf['v_feld'])."','".($tf['terraforming_id'] == 99 ? ($col->colonies_classes_id == 9 ? 53  : 72) : $tf['z_feld'])."');\" onmouseout=\"nd();\"" : "title=\"".getnamebyfield($data['type'])."\"")."\"></a></td>";
			} else {
				// $fieldsstring .= "<td class=".$fclass."><a href=\"javascript:void(0);\" ><img onClick=\"field_action(this,".$data['field_id'].");\" src=".$gfx."/".(is_array($tf) ? "terraforming/".$tf['terraforming_id'].".gif" : "fields/".$data['type'].".gif")." border=0 ".(is_array($tf) ? "onmouseover=\"getterrainfo('".stripslashes($tf['name'])."','".renderstatusbar(time()-($tf['terraformtime']-$tf['t_time']),$tf['t_time'],"gre")."','".getpercentage(time()-($tf['terraformtime']-$tf['t_time']),$tf['t_time'])."','".date("d.m H:i",$tf['terraformtime'])."','".($col->colonies_classes_id == 9 && $tf['terraforming_id'] == 99 ? 53 : $tf['v_feld'])."','".($tf['terraforming_id'] == 99 ? ($col->colonies_classes_id == 9 ? 53  : 72) : $tf['z_feld'])."');\" onmouseout=\"nd();\"" : "title=\"".getnamebyfield($data['type'])."\"")."\"></a></td>";
				$fieldsstring .= "<td class=".$fclass."><a href=\"javascript:void(0);\" ><img onClick=\"field_action(this,".$data['field_id'].");\" src=".$gfx."/fields/".$data['type'].".gif border=0 title=\"".getnamebyfield($data['type'])."\"></a></td>";				
			}
		}
		$i++;
		$j++;
		if ($data['buildings_id'] == 10 && $data['aktiv'] == 0) $pnp++;
	}
	
	$fieldsstring  .= "</tr></table>";

	$menus['fields'] = fixedPanel(4,"Koloniefelder","mfld",$gfx."/buttons/icon/planet.gif",array("<tr><td style='text-align:center;'>".$fieldsstring."</td></tr>"));	

	// $bevstring = "<tr><td><img src=".$gfx."/bev/bev_free_1_".$faction.".gif title='Wohnraum'> Wohnraum: ".($col->bev_max-$col->bev_free-$col->bev_work < 0 ? 0 : $col->bev_max-$col->bev_free-$col->bev_work)."/".$col->bev_max."<br>
	// <img src=".$gfx."/bev/bev_used_1_".$faction.".gif title='Arbeiter'> Arbeiter: ".$col->bev_work."<br>
	// <img src=".$gfx."/bev/bev_unused_1_".$faction.".gif title='Arbeitslose'> Arbeitslose: ".$col->bev_free."<br>
	// <img src=".$gfx."/bev/bev_over_1_".$faction.".gif title='Obdachlose'> Obdachlose: ".($col->bev_work+$col->bev_free > $col->bev_max ? ($col->bev_free+$col->bev_work)-$col->bev_max : 0)."
	// <br>&nbsp;</td></tr>
			// <tr><td>".($col->einwanderung == 0 ? "<a href=?p=colony&s=sc&id=".$_GET['id']."&a=sew&ew=1&ps=".$_SESSION['pagesess']." onmouseover=cp('ew','buttons/einwand0') onmouseout=cp('ew','buttons/einwand1')><img src=".$gfx."/buttons/einwand1.gif name=ew border=0 title='Einwanderung verbieten'> Einwanderung verbieten</a>" : "<a href=?p=colony&s=sc&id=".$_GET['id']."&a=sew&ew=0&ps=".$_SESSION['pagesess']." onmouseover=cp('ew','buttons/einwand1') onmouseout=cp('ew','buttons/einwand0')><img src=".$gfx."/buttons/einwand0.gif name=ew border=0 title='Einwanderung erlauben'> Einwanderung erlauben</a>")."</td></tr>
		// <form action=main.php method=get><input type=hidden name=p value=colony><input type=hidden name=s value=sc><input type=hidden name=a value=sbs><input type=hidden name=id value=".$_GET['id'].">
		// <tr><td>Einwanderungsgrenze <input type=text size=3 class=text name=bsc value='".$col->bevstop."'> <input type=hidden name=ps value=".$_SESSION['pagesess']."> <input type=submit class=button value=festlegen></td></tr></form>";
		
	$bevstring = "";
	// $faction = 3;
	if ($col->bev_free+$col->bev_work > $col->bev_max)
		$bevstring .= "<tr><td width=200><br>&nbsp;<img src=".$gfx."/bev/blank/0m.png width=35 title='Wohnraum'><img src=".$gfx."/bev/blank/0f.png width=35 title='Wohnraum'> Wohnraum: </td><td style=\"vertical-align: bottom;text-align:center;\"><br><font color=#ff0000>".($col->bev_free+$col->bev_work)." / ".$col->bev_max."</font></td></tr>";
	else 
		$bevstring .= "<tr><td width=200><br>&nbsp;<img src=".$gfx."/bev/blank/0m.png width=35 title='Wohnraum'><img src=".$gfx."/bev/blank/0f.png width=35 title='Wohnraum'> Wohnraum: </td><td style=\"vertical-align: bottom;text-align:center;\"><br>".($col->bev_free+$col->bev_work)." / ".$col->bev_max."</td></tr>";
	
	// if ($col->bev_work > $col->bev_max)
		// $bevstring .= "<tr><td width=200 style=\"vertical-align:bottom;\"><br>&nbsp;<img src=".$gfx."/bev/workers/".$faction."m.png width=35 title='Arbeiter'><img src=".$gfx."/bev/workers/".$faction."f.png width=35 title='Arbeiter'> Arbeiter: </td><td style=\"vertical-align: bottom;text-align:center;\"><font color=#ff0000>".($col->bev_free+$col->bev_work)." / ".$col->bev_max."</font>";
	// else 
		// $bevstring .= "<tr><td width=200 style=\"vertical-align:bottom;\"><br>&nbsp;<img src=".$gfx."/bev/workers/".$faction."m.png width=35 title='Arbeiter'><img src=".$gfx."/bev/workers/".$faction."f.png width=35 title='Arbeiter'> Arbeiter: </td><td style=\"vertical-align: bottom;text-align:center;\">".($col->bev_work)." / ".($col->bev_free+$col->bev_work)."";

	// if ($col->bev_free >= 5) 
		// $bevstring .= "<br><br><font color='#00ff00'>".$col->bev_free." frei</font></td></tr>";
	// else
		// $bevstring .= "<br><br><font color='#ffff00'>".$col->bev_free." frei</font></td></tr>";
	
	$bevstring .= "<tr><td width=200><br>&nbsp;<img src=".$gfx."/bev/workers/".$faction."m.png width=35 title='Arbeitslose'><img src=".$gfx."/bev/workers/".$faction."f.png width=35 title='Arbeitslose'> Arbeitslose: </td><td style=\"vertical-align: bottom;text-align:center;\"><br>".($col->bev_free)."</td></tr>";	
	$bevstring .= "<tr><td width=200><br>&nbsp;<img src=".$gfx."/bev/crew/".$faction."m.png width=35 title='Arbeiter'><img src=".$gfx."/bev/crew/".$faction."f.png width=35 title='Arbeiter'> Arbeiter: </td><td style=\"vertical-align: bottom;text-align:center;\"><br>".($col->bev_work)."</td></tr>";	
	

	$menus['bev'] = fixedPanel(2,"Einwohner","mbev",$gfx."/buttons/icon/alliance.gif",array($bevstring));	
		
	$coloptionstring = "<form action=main.php method=get><input type=hidden name=p value=colony><input type=hidden name=s value=sc><input type=hidden name=ps value=".$_SESSION['pagesess']."><input type=hidden name=a value=cn><input type=hidden name=id value=".$_GET['id'].">";
	
	$coloptionstring .= "<tr><td style=\"padding:2px;\">Koloniename <input type=text size=15 name=cn value=\"".$col->name."\"> <input type=submit class=button value=ändern><input type=hidden name=ps value=".$_SESSION['pagesess']."></td></tr>";
	$coloptionstring .= "<tr><td style=\"padding:2px;\"><a href=?p=colony&s=gse&id=".$_GET['id']." ".getHover("gse","inactive/n/list","hover/w/list")."><img src=".$gfx."/buttons/inactive/n/list.gif name=gse border=0> Gebäudeschaltung</a></td></tr>";




		// <tr><td><img src=".$gfx."/goods/41.gif title='Forschungspunkte Verarbeitung'> ".(!$col->research["rv"] ? "0" : "+".$col->research["rv"])."&nbsp;<img src=".$gfx."/goods/42.gif title='Forschungspunkte Technik'> ".(!$col->research["rt"] ? "0" : "+".$col->research["rt"])."&nbsp;<img src=".$gfx."/goods/43.gif title='Forschungspunkte Konstruktion'> ".(!$col->research["rk"] ? "0" : "+".$col->research["rk"])."<br></td></tr></form>	
	$coloptionstring .= "</form>";
	$coloptionstring .= "<tr><td style=\"padding:2px;\">".($col->einwanderung == 0 ? "<a href=?p=colony&s=sc&id=".$_GET['id']."&a=sew&ew=1&ps=".$_SESSION['pagesess']." ".getHover("ew","inactive/g/alliance","hover/r/alliance")."><img src=".$gfx."/buttons/inactive/g/alliance.gif name=ew border=0 title='Einwanderung verbieten'> Einwanderung verbieten</a>" : "<a href=?p=colony&s=sc&id=".$_GET['id']."&a=sew&ew=0&ps=".$_SESSION['pagesess']." ".getHover("ew","inactive/r/alliance","hover/g/alliance")."><img src=".$gfx."/buttons/inactive/r/alliance.gif name=ew border=0 title='Einwanderung erlauben'> Einwanderung erlauben</a>")."</td></tr>
		<form action=main.php method=get><input type=hidden name=p value=colony><input type=hidden name=s value=sc><input type=hidden name=a value=sbs><input type=hidden name=id value=".$_GET['id'].">
		<tr><td>Einwanderungsgrenze <input type=text size=3 class=text name=bsc value='".$col->bevstop."'> <input type=hidden name=ps value=".$_SESSION['pagesess']."> <input type=submit class=button value=festlegen></td></tr></form>";
		
	$coloptionstring .= "<tr><td>&nbsp;</td></tr><tr><td style=\"padding:2px;\"><a href=?p=colony&s=coa&id=".$_GET['id']." ".getHover("coa","inactive/n/planet","hover/r/x")."><img src=".$gfx."/buttons/inactive/n/planet.gif border=0 title='Kolonie aufgeben' name=coa> Kolonie aufgeben</a></td></tr>";		
	// </table>";
	
	
	$rowspan=2;
	if (!$col->ground_enabled && $db->query("SELECT research_id FROM stu_researched WHERE user_id=".$col->uid." AND research_id = 7001",1)) {
	
		$coloptionstring .= "<tr><td><a href=?p=colony&ps=".$_SESSION['pagesess']."&s=sc&a=xug&id=".$_GET['id']." ".getonm("xug","buttons/b_down")."><img src=".$gfx."/buttons/b_down1.gif name=xug border=0> Untergrund freilegen</a></td></tr>";
	
		$coloptionstring .=" <tr><td>Kosten: <br>
			<img src=".$gfx."/goods/0.gif title='Energie'>150
			<img src=".$gfx."/goods/2.gif title='Baumaterial'>50
			<img src=".$gfx."/goods/4.gif title='Transparentes Aluminium'>50
			<img src=".$gfx."/goods/21.gif title='Duranium'>100
			<img src=".$gfx."/goods/5.gif title='Deuterium'>100
			<img src=".$gfx."/goods/6.gif title='Antimaterie'>100
			<br></td></tr>";
	
		// $menus['ground'] = fixedPanel(1,"Untergrund","mgnd",$gfx."/buttons/icon/asteroids.gif",array($ugstring));

		$rowspan=3;
	}
	
	
	$menus['settings'] = fixedPanel(3,"Erweiterte Kolonie-Funktionen","mopt",$gfx."/buttons/icon/options.gif",array($coloptionstring));

	
	
	// $astrostring = "<tr><td valign=top><img src=".$gfx."/systems/".$col->sys['type']."ms.png width=30 height=30> ".stripslashes($col->sys[name])."-System (".$col->sys[cx]."|".$col->sys[cy].")<br>
	// <img src=".$gfx."/planets/".$col->colonies_classes_id.".gif title='".$col->planet_name."' width=20 height=20 style='padding-left:5px;padding-right:5px;'> ".stripslashes($col->planet_name)." (".$col->sx."|".$col->sy.")<br>
	// <br>
	// </td></tr>";
	

	$result = $col->getSensorDataShip();
	$result = addSensorLegend($result);
	
	$lssstring = renderSensors($result);
	
	
	
	$scanstring = "<table width=100% border=0>
	<tr><td width=100% style=\"padding:2px;\"><a href=\"javascript:void(0);\" ".getHover("sysmap","inactive/n/map","hover/w/map")." onClick=\"showsysinfo(this,".$col->id.");\"><img src=".$gfx."/buttons/inactive/n/map.gif border=0 title='Planeten in diesem System' name=sysmap> Planeten im ".stripslashes($col->sys[name])."-System</a></td></tr>
	<tr><td width=100%><center>".$lssstring."</center></td></tr></table>";
	
	// $statr = $db->query("SELECT * FROM stu_stations WHERE sx=".$col->sx." AND sy=".$col->sy." AND systems_id=".$col->systems_id."",4);
	// if ($statr != 0) {

		// echo "<br><img src=".$gfx."/stations/".$statr[stations_classes_id].".gif> ".$statr[name]."<br>";

		// if ($statr[user_id] == $_SESSION["uid"]) echo "<a href=?p=station&s=show&id=".$statr[id]."><img src=".$gfx."/buttons/lupe1.gif title=\"Zu Station wechseln\" border=0></a> ";
		// echo "<a href=?p=colony&s=ass&m=bt&shd=".$statr[id]."&id=".$_GET[id]."><img src=".$gfx."/buttons/b_up1.gif title='Zur Station beamen' border=0></a> ";
		// echo "<a href=?p=colony&s=ass&m=bf&shd=".$statr[id]."&id=".$_GET[id]."><img src=".$gfx."/buttons/b_down1.gif title='Von der Station beamen' border=0></a> ";

	// }
	
	
	$shiparr = array();
	$shipsstring = "<tr><td valign=top style=\"height:20px;display: block;\">";

	$sr = $db->query("SELECT id,name,user_id FROM stu_ships WHERE sx=".$col->sx." AND sy=".$col->sy." AND systems_id=".$col->systems_id." AND (ISNULL(cloak) OR cloak!='1' OR cloak='' OR (cloak='1' AND user_id=".$_SESSION["uid"].")) ORDER BY fleets_id DESC,id");
		
	if (mysql_num_rows($sr) == 0) $shipsstring .= "Keine";
	else {
		$shipsstring .= "<form action=main.php method=get name=wtd><input type=hidden name=p value=colony><input type=hidden name=s value=ase><input type=hidden name=id value=".$_GET['id']."><input type=hidden name=ac value=sba><select name=shd style=\"width:100%;height:20px;\">";

		
		while($data=mysql_fetch_assoc($sr)) $shipsstring .= "<option value=".$data['id'].($_GET['shd'] == $data['id'] ? " SELECTED" : "").">".stripslashes(strip_tags($data['name']))." (".$data['id'].")".($data['user_id'] == $_SESSION['uid'] ? " *" : "");
		mysql_free_result($sr);
		$shipsstring .= "</select></td></tr>";
		array_push($shiparr,$shipsstring);
		
		$shipsstring = "<tr><td style=\"text-align:center;vertical-align:middle;width:100%;\">
		<input type=image name=show src=".$gfx."/buttons/inactive/n/ship.gif title=\"Zu Schiff wechseln\" onmouseover=\"this.src='".$gfx."/buttons/hover/w/ship.gif'\" onmouseout=\"this.src='".$gfx."/buttons/inactive/n/ship.gif'\">
		<input type=image name=bt src=".$gfx."/buttons/inactive/n/nbeam_u.gif title='Zu Schiff beamen' onmouseover=\"this.src='".$gfx."/buttons/hover/w/beam_u.gif'\" onmouseout=\"this.src='".$gfx."/buttons/inactive/n/nbeam_u.gif'\">
		<input type=image name=bf src=".$gfx."/buttons/inactive/n/nbeam_d.gif title='Von Schiff beamen' onmouseover=\"this.src='".$gfx."/buttons/hover/w/beam_d.gif'\" onmouseout=\"this.src='".$gfx."/buttons/inactive/n/nbeam_d.gif'\">
		<input type=image name=et src=".$gfx."/buttons/inactive/n/energyplus.gif title='Energietransfer' onmouseover=\"this.src='".$gfx."/buttons/hover/w/energyplus.gif'\" onmouseout=\"this.src='".$gfx."/buttons/inactive/n/energyplus.gif'\">
		<input type=image name=sl src=".$gfx."/buttons/inactive/n/list.gif title=\"Schiffsliste aufrufen\" onmouseover=\"this.src='".$gfx."/buttons/hover/w/list.gif'\" onmouseout=\"this.src='".$gfx."/buttons/inactive/n/list.gif'\">
		</td></tr>
		</form>";
		array_push($shiparr,$shipsstring);
	}

	
	
		
	$menus['astro'] = fixedPanel(4,"Astrometrische Daten","mast",$gfx."/buttons/icon/star.gif",array($astrostring));	
	$menus['scan'] = fixedPanel(1,"Umgebungsscan","mscn",$gfx."/buttons/icon/map.gif",$scanstring);		
	$menus['ships'] = fixedPanel(3,"Schiffe im Orbit","mshp",$gfx."/buttons/icon/ship.gif",$shiparr);		

	
	$lagerrows = array();
	$modsrows = array();
	$j = 0;
	$mc = 0;
	while($data=mysql_fetch_assoc($col->result))
	{
		if ($data['goods_id'] == 1 && $wks == 1) $col->goods[1]+= 2*$fc;
		if ($data['goods_id'] == 1) $col->goods[1] -= ceil(($col->bev_free+$col->bev_work)/10);
		if ($data['count'] == 0 && $col->goods[$data['goods_id']] == 0) continue;
		$sg = $data['count'];
		if ($col->goods[$data['goods_id']] < 0) $sg -= abs($col->goods[$data['goods_id']]);
		if ($sg > 0)
		{
			// Berechnung der Lagerstandsanzeige
			for($i=0;$i<floor($sg/1000);$i++) $lb .= "<img src=".$gfx."/l_t.gif>";
			$sg -= floor($sg/1000)*1000;
			for($i=0;$i<floor($sg/100);$i++) $lb .= "<img src=".$gfx."/l_h.gif>";
			$sg -= floor($sg/100)*100;
			if ($data['count'] >= $sg) $lb .= "<img src=".$gfx."/l_s.gif width=".$sg." height=12>";
		}
		if ($col->goods[$data['goods_id']] > 0)
		{
			// Berechnung der Anzeige der dazukommenden Waren
			$sg = $col->goods[$data['goods_id']];
			for($i=0;$i<floor($sg/100);$i++) $lb .= "<img src=".$gfx."/l_hg.gif>";
			$sg -= floor($sg/100)*100;
			if ($sg != 0) $lb .= "<img src=".$gfx."/l_sg.gif width=".$sg." height=12>";
		}
		if ($col->goods[$data['goods_id']] < 0)
		{
			// Berechnung der Anzeige der verbrauchten Waren
			$sg = abs($col->goods[$data['goods_id']]);
			for($i=0;$i<floor($sg/100);$i++) $lb .= "<img src=".$gfx."/l_hr.gif>";
			$sg -= floor($sg/100)*100;
			if ($sg > 0) $lb .= "<img src=".$gfx."/l_sr.gif width=".$sg." height=12>";
		}
		$j++;
		if ($j == 2)
		{
			$trc = " style=\"background-color: #171616\"";
			$j = 0;
		}
		$laz .= "<tr><td".$trc."><img src=".$gfx."/goods/".$data['goods_id'].".gif title='".ftit($data['name'])."'> ".(!$data['count'] ? 0 : $data['count'])."</td><td".$trc.">".$lb."</td><td".$trc.">".(!$col->goods[$data['goods_id']] ? 0 : ($col->goods[$data['goods_id']] > 0 ? "<font color=#00ff00>+".$col->goods[$data['goods_id']]."</font>" : "<font color=#FF0000>".$col->goods[$data['goods_id']]."</font>"))."</td></tr>";
		if ($data['goods_id'] < 100) {
			array_push($lagerrows,"<tr><td style=\"width:160px;padding:2px;\">".goodPic($data['goods_id'])." ".(!$data['count'] ? 0 : $data['count'])."</td><td width=520>".bigdarkuniversalstatusbar($data['count'],$col->max_storage,"whi",$col->goods[$data['goods_id']],520)."</td><td style='text-align:right'>".plusminus($col->goods[$data['goods_id']])."&nbsp;</td></tr>");
		} else {
		
			if ($mc % 2 == 0) {
				$s = "<tr>";
			}
			$s .= "<td style=\"padding:2px;\">".goodPic($data['goods_id'])." ".(!$data['count'] ? 0 : $data['count'])."</td>";
			if ($mc % 2 == 1) {
				$s .= "</tr>";
				array_push($modsrows,$s);
			}
			$mc++;			
		}
		$row = 
		$trc = "";
		unset($lb);
		$gc += $col->goods[$data['goods_id']];
	}
	if ($mc % 2 == 1) {
		$s .= "<td></td></tr>";
		array_push($modsrows,$s);
	}


	$emenu = array();
	$energybar = "<tr><td style=\"width:160px;padding:2px;\">".infoPic("energy")." ".$col->eps." / ".$col->max_eps."</td><td width=760>".bigdarkuniversalstatusbar($col->eps,$col->max_eps,"yel",$fe,760)."</td><td style='text-align:right'>".plusminus($fe)."&nbsp;</td></tr>";
	array_push($emenu,$energybar);

	// $buildingcount = $db->query("SELECT COUNT(*) FROM stu_colonies_fielddata WHERE colonies_id=".$col->id." AND buildings_id > 0 AND aktiv < 2;",1);
	
		// if ($col->is_moon)
			// $combinedeffects['fleet'] = floor(min($buildingcount*0.5, 10));
		// else 
			// $combinedeffects['fleet'] = floor(min($buildingcount*0.5, 15));	
	$combinedeffects = array();
	$effects = $db->query("SELECT SUM(e.count) as count, e.type as type FROM stu_colonies_fielddata as a left join stu_buildings_effects as e on a.buildings_id = e.buildings_id WHERE e.count != 0 AND a.aktiv=1 AND a.colonies_id = ".$col->id." GROUP BY e.type ORDER BY count DESC;");

	while ($effect = mysql_fetch_assoc($effects)) {
		$combinedeffects[$effect['type']] += $effect['count'];
	}
	foreach ($combinedeffects as $ck => $cv) {
		array_push($emenu,"<tr><td style=\"width:160px;padding:2px;\">".infoPic($ck)." ".geteffectname($ck)."</td><td width=760></td><td style='text-align:right'>".plusminus($cv)."&nbsp;</td></tr>");
	}
	
	$menus['effects'] = fixedPanel(1,"Energie & Sonstiges","menuenergy",$gfx."/buttons/icon/energyplus.gif",$emenu);
	
	
	$goodlines = array();
	array_push($goodlines,"<tr><td style=\"width:160px;padding:2px;\"><img src='".$gfx."/icons/storage.gif' title='Gesamt'> ".$col->storage." / ".$col->max_storage."</td><td width=760>".bigdarkuniversalstatusbar($col->storage,$col->max_storage,"whi",0,760)."</td><td style='text-align:right'>".plusminus($gc)."&nbsp;</td></tr>");
	// $goodlines = array_merge($goodlines,$lagerrows);
	$menus['goods'] = fixedPanel(3,"Produzierte Waren","menugood",$gfx."/buttons/icon/storage.gif",$lagerrows);	
	$menus['modules'] = fixedPanel(1,"Schiffs-Module","menumods",$gfx."/buttons/icon/shipparts.gif",$modsrows);	
	$menus['storage'] = fixedPanel(4,"Lagerstand (".(round((@(100/$col->max_storage)*$col->storage),2))."% voll)","menulager",$gfx."/buttons/icon/storage.gif",$goodlines);	
	

	echo "<table  class=tablelayout>";

	echo "<tr>";
		echo "<td rowspan=2 class=tablelayout style=\"width:375px;\">".$menus['fields']."</td>";
		echo "<td colspan=2 class=tablelayout style=\"vertical-align:top;\">".$menus['bev']."</td>";
		echo "<td rowspan=5 class=tablelayout style=\"vertical-align:top;min-width:250px;\">".$menus['scan']."</td>";
	echo "</tr>";
	echo "<tr>";
		echo "<td class=tablelayout style=\"vertical-align:top;width:320px;\">".$menus['settings']."</td>";
		echo "<td class=tablelayout style=\"vertical-align:top;width:320px;\">".$menus['ships']."</td>";
	echo "</tr>";		
	// echo "<tr>";
		// echo "<td class=tablelayout style=\"vertical-align:top;width:320px;\">".$menus['ground']."</td>";
		// echo "<td class=tablelayout style=\"vertical-align:top;width:320px;\">".$menus['ships']."</td>";
	// echo "</tr>";

	echo "<tr>";
		echo "<td colspan=3 class=tablelayout style=\"vertical-align:top;\">".$menus['effects']."</td>";
	echo "</tr>";	
	echo "<tr>";
		echo "<td colspan=3 class=tablelayout style=\"vertical-align:top;\">".$menus['storage']."</td>";
	echo "</tr>";	
	echo "<tr>";
		echo "<td colspan=2 class=tablelayout style=\"vertical-align:top;\">".$menus['goods']."</td>";
		echo "<td class=tablelayout style=\"vertical-align:top;\">".$menus['modules']."</td>";
	echo "</tr>";		
	// echo "<tr><td colspan=6>&nbsp;</td></tr>";
	

	echo "</table>";
	
}
if ($v == "beamto")
{
	pageheader("/ <a href=?p=colony>Kolonien</a> / <a href=?p=colony&s=sc&id=".$col->id.">".ftit($col->name)."</a> / <b>Zur ".ftit($col->ship['name'])." beamen</b>");
	$result = $db->query("SELECT a.goods_id,a.count,b.name FROM stu_colonies_storage as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.colonies_id=".$col->id." ORDER BY b.sort");

	$control['mode']		= "to";
	$control['directionlink']	= "?p=colony&s=ase&ac=sbf&shd=".$_GET['shd']."&id=".$_GET['id']."&bf.x=5";
	$control['parameters']	= array();

	echo "<form action=main.php method=post><input type=hidden name=bcol value=1><input type=hidden name=p value=colony><input type=hidden name=s value=sc><input type=hidden name=id value=".$_GET['id']."><input type=hidden name=shd value=".$_GET[shd]."><input type=hidden name=a value=bt><input type=hidden name=ps value=".$_SESSION['pagesess'].">";
	beamingPage(getColonyBeamInfo($col->id),getShipBeamInfo($col->ship['id']),$control);
	echo "</form>";
}

if ($v == "beamfrom")
{
	pageheader("/ <a href=?p=colony>Kolonien</a> / <a href=?p=colony&s=sc&id=".$col->id.">".ftit($col->name)."</a> / <b>Von der ".ftit($col->ship['name'])." beamen</b>");
	$result = $db->query("SELECT a.goods_id,a.count,b.name FROM stu_ships_storage as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.ships_id=".$col->ship['id']." ORDER BY b.sort");
	
	$control['mode']		= "from";
	$control['directionlink']	= "?p=colony&s=ase&ac=sbf&shd=".$_GET['shd']."&id=".$_GET['id']."&bt.x=5";
	$control['parameters']	= array();
	
	echo "<form action=main.php method=post><input type=hidden name=bcol value=1><input type=hidden name=p value=colony><input type=hidden name=s value=sc><input type=hidden name=id value=".$_GET['id']."><input type=hidden name=shd value=".$_GET[shd]."><input type=hidden name=a value=bf><input type=hidden name=ps value=".$_SESSION['pagesess'].">";
	beamingPage(getColonyBeamInfo($col->id),getShipBeamInfo($col->ship['id']),$control);
	echo "</form>";
}

if ($v == "energietrans")
{
	pageheader("/ <a href=?p=colony>Kolonien</a> / <a href=?p=colony&s=sc&id=".$col->id.">".ftit($col->name)."</a> / <b>Energie zur ".ftit($col->ship[name])." transferieren</b>");
	$result = $db->query("SELECT a.goods_id,a.count,b.name FROM stu_ships_storage as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.ships_id=".$col->ship[id]);
	echo "<form action=main.php method=get><input type=hidden name=p value=colony><input type=hidden name=s value=sc><input type=hidden name=id value=".$_GET['id']."><input type=hidden name=shd value=".$_GET[shd]."><input type=hidden name=a value=et>
	<input type=hidden name=ps value=".$_SESSION['pagesess'].">
	<table width=350><tr><td valign=top>
	<table class=tcal cellpadding=1 cellspacing=1><tr><th>Energietransfer</th></tr>
	<tr><td><img src=".$gfx."/buttons/e_trans2.gif title='Energie'> <input type=text size=3 class=text name=ec> ".($col->eps)." <input type=submit class=button value=Transfer> <input type=submit class=button name=ec value=max></td></tr>";
	echo "</table></td><td valign=top><table class=tcal cellspacing=1 cellpadding=1>
	<tr><th>Informationen</th></tr>
	<tr><td>Energie: ".$col->eps."/".$col->max_eps."</td></tr>
	<tr><td>Schiff-EPS: ".$col->ship['eps']."/".$col->ship['max_eps']."</td></tr>
	<tr><td>Ladung: ".$db->query("SELECT SUM(count) FROM stu_ships_storage WHERE ships_id=".$col->ship['id'],1)."/".$col->ship[storage]."</td></tr>
	</table></td></tr></table></form>";
}
if ($v == "listorbititems")
{
	pageheader("/ <a href=?p=colony>Kolonien</a> / <a href=?p=colony&s=sc&id=".$col->id.">".ftit($col->name)."</a> / <b>Schiffe im Orbit</b>");
	echo "<script language=\"Javascript\">
	function getshipinfo(id)
	{
		elt = 'shinfo';
		sendRequest('backend/shinfo.php?PHPSESSID=".session_id()."&id=' + id + '');
		return overlib('<div id=shinfo></div>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER, STICKY, RELX, 300, RELY, 150, WIDTH, 400);
	}
	</script>";
	$col->loadorbititems();
	if (mysql_num_rows($col->result) == 0) meldung("Keine Schiffe im Orbit");
	else
	{
		echo "<form method=post action=main.php><input type=hidden name=p value=colony><input type=hidden name=s value=sc><input type=hidden name=id value=".$_GET['id'].">
		<input type=hidden name=a value=met>
	<input type=hidden name=ps value=".$_SESSION['pagesess'].">
		<table class=tcal cellspacing=1 cellpadding=1>
		<tr><th></th><th>Energie</td><th></th><th>Name</th><th>Besitzer</th><th>Energie</th><th>Lagerraum</th></tr>";
		while($data=mysql_fetch_assoc($col->result))
		{
			$j++;
			if ($j == 2)
			{
				$trc = " style=\"background-color: #171616\"";
				$j = 0;
			}
			else $trc = "";
			if ($data['fleets_id'] > 0 && $data['fleets_id'] != $lf)
			{
				echo "<tr><td colspan=7".$trc.">Flotte ".stripslashes($db->query("SELECT name FROM stu_fleets WHERE fleets_id=".$data['fleets_id']." LIMIT 1",1))."</td></tr>";
				$lf = $data['fleets_id'];
			}
			if ($data['fleets_id'] == 0 && $lf > 0)
			{
				echo "<tr><td colspan=7".$trc.">Einzelschiffe</td></tr>";
				$lf = 0;
			}
			echo "<tr>
				<td".$trc.">".($data['user_id'] == $_SESSION['uid'] ? "<a href=?p=".($data['slots'] > 0 ? "stat" : "ship")."&s=ss&id=".$data['id'].">" : "")."<img src=".$gfx."/ships/".vdam($data).$data['rumps_id'].".gif title=\"".$data['rname']."\" border=0>".($data['user_id'] == $_SESSION['uid'] ? "</a>" : "")."</td>
				<td align=center".$trc."><input type=text size=3 class=text name=et[".$data['id']."]></td>
				<td align=center".$trc."><a href=?p=colony&s=ase&bt.x=1&shd=".$data['id']."&id=".$_GET['id']." ".getonm("bt".$i,"buttons/b_up")."><img src=".$gfx."/buttons/b_up1.gif border=0 name=bt".$i." title=\"Zur ".ftit($data['name'])." beamen\"></a>
					<a href=?p=colony&s=ase&bf.x=1&shd=".$data['id']."&id=".$_GET['id']." ".getonm("bf".$i,"buttons/b_down")."><img src=".$gfx."/buttons/b_down1.gif border=0 name=bf".$i." title=\"Von der ".ftit($data['name'])." beamen\"></a>
					<a href=?p=comm&s=nn&recipient=".$data['user_id']." ".getonm("pm".$i,"buttons/msg")."><img src=".$gfx."/buttons/msg1.gif name=pm".$i." title=\"Nachricht an ".ftit($data['user'])." senden\" border=0></a></td>
				<td".$trc.">".stripslashes($data['name'])." (".($_SESSION['uid'] == $data['user_id'] ? "<a href=\"javascript:void(0);\" onClick=\"getshipinfo(".$data['id'].");\">".$data['id']."</a>" : $data['id']).")</td>
				<td".$trc.">".stripslashes($data['user'])." (".$data['user_id'].")</td>
				<td".$trc.">".($data['eps'] < $data['max_eps'] ? "<font color=yellow>".$data['eps']."</font>" : $data['eps'])."/".$data['max_eps']."</td>
				<td".$trc.">".(!$data['ss'] ? 0 : ($data['ss'] >= $data['storage'] ? "<font color=yellow>".$data['ss']."</font>" : $data['ss']))."/".$data['storage']."</td>
			</tr>";
			$i++;
		}
		echo "<tr>
		<td colspan=7 align=center><input type=submit value=Transfer class=button></td>
		</tr>
		</table>";
	}
}
if ($v == "sectorscan")
{
	pageheader("/ <a href=?p=colony>Kolonien</a> / <a href=?p=colony&s=sc&id=".$col->id.">".ftit($col->name)."</a> / <b>Sektorscan ".$_GET[sx]."|".$_GET[sy]."</b>");
	if (mysql_num_rows($col->result) == 0) meldung("Keine Schiffe in diesem Sektor");
	else
	{
		echo "<table class=tcal cellspacing=1 cellpadding=1>
		<tr><td></td><th>Name</th><th>Besitzer</th></tr>";
		if ($_SESSION['allys_id'] > 0)
		{
			include_once($global_path."class/ally.class.php");
			$ally = new ally;
		}
		while($data=mysql_fetch_assoc($col->result))
		{
			if ($data['cloak'] == 1 && $data['user_id'] != $_SESSION['uid'])
			{
				if ($_SESSION['allys_id'] == 0 || $data['allys_id'] == 0)
				{
					$cloaked++;
					continue;
				}
				if ($ally->checkbez($_SESSION['allys_id'],$data['allys_id']) != 4)
				{
					$cloaked++;
					continue;
				}
			}
			echo "<tr>
				<td>".($data['user_id'] == $_SESSION['uid'] ? "<a href=?p=ship&s=ss&id=".$data['id'].">" : "")."<img src=".$gfx."/ships/".vdam($data).$data['rumps_id'].".gif title=\"".ftit($data['rname'])."\" border=0>".($data['user_id'] == $_SESSION['uid'] ? "</a>" : "")."</td>
				<td>".stripslashes($data['name'])."</td>
				<td>".stripslashes($data['user'])." <a href=?p=comm&s=nn&recipient=".$data['user_id']." ".getonm("pm".$i,"buttons/msg")."><img src=".$gfx."/buttons/msg1.gif name=pm".$i." title=\"Nachricht an ".ftit($data['user'])." senden\" border=0></a></td>
			</tr>";
			$i++;
		}
		echo "</table>";
	}
}
if ($v == "coltradeoffer")
{
	pageheader("/ <a href=?p=colony>Kolonien</a> / <a href=?p=colony&s=sc&id=".$col->id.">".ftit($col->name)."</a> / <b>Warenangebot</b>");
	if (is_array($_GET['good']) && is_array($_GET['gm'])) $result = $col->changecto($_GET['good'],$_GET['gm']);
	if ($result) meldung($result);
	$col->loadcto();
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=300>
	<form action=main.php method=post>
	<input type=hidden name=wag value=1>
	<input type=hidden name=p value=colony>
	<input type=hidden name=s value=cto>
	<input type=hidden name=id value=".$_GET['id'].">
	<th width=20></th>
	<th>Vorhanden</th>
	<th>Anbieten</th>
	<th>Verlangen</th>
	<th>X</th>";
	while($data=mysql_fetch_assoc($col->result))
	{
		$i++;
		echo "<input type=hidden name=good[] value=".$data['goods_id'].">
		<tr><td><img src=".$gfx."/goods/".$data['goods_id'].".gif title=\"".ftit($data['name'])."\"></td>
		<td>".(!$data['count'] ? 0 : $data['count'])."</td>
		<td><input type=radio name=gm[".$data['goods_id']."] value=1".($data['mode'] == 1 ? " CHECKED" : "")."></td>
		<td><input type=radio name=gm[".$data['goods_id']."] value=2".($data['mode'] == 2 ? " CHECKED" : "")."></td>
		<td><input type=radio name=gm[".$data['goods_id']."] value=0".(!$data['mode'] ? " CHECKED" : "")."></td></tr>";
		if ($i%15==0) echo "<tr><td align=center colspan=5><input type=submit class=button value=Ändern></td></tr>";
	}
	echo "</form></table>";
}
if ($v == "geschaltung")
{
	pageheader("/ <a href=?p=colony>Kolonien</a> / <a href=?p=colony&s=sc&id=".$col->id.">".ftit($col->name)."</a> / <b>Gebäudeschaltung</b>");
	
	if ($_GET['av']) $result = $col->activateBuildingType($_GET['av']);
	if ($_GET['dv']) $result = $col->deactivateBuildingType($_GET['dv']);
	if ($result) meldung($result);
	
	// if (is_array($_GET['fields']) && $_SESSION['preps'] == $_GET['ps'])
	// {
		// foreach($_GET['fields'] as $key => $value)
		// {
			// $_GET['am'] == "Aktivieren" ? $res = $col->activatebuilding($value) : $res = $col->deactivatebuilding($value);
			// if ($res) $result .= $res."<br>";
			// unset($col->fdd);
		// }
	// }
	// if ($_GET['a'] == "gs" && check_int($_GET['m']) && $_SESSION['preps'] == $_GET['ps']) $result = $col->gschaltung($_GET['m']);
	// if ($result) meldung($result);
	// $col->loadgschaltung();
	// $col->loadcolstorage();
	// $fe = $db->query("SELECT SUM(b.eps_proc) FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.aktiv=1 AND a.colonies_id=".$_GET['id'],1);
	// echo "<form action=main.php method=post><input type=hidden name=p value=colony><input type=hidden name=s value=gse>
	// <input type=hidden name=ps value=".$_SESSION['pagesess'].">
	// <input type=hidden name=id value=".$_GET['id']."><table cellpadding=0 cellspacing=0>
	// <tr><td valign=top>
	// <table class=tcal>
	// <th>Gebäude im Orbit</th>
	// <tr><td><select name=fields[] size=10 multiple>";
	// while($data=mysql_fetch_assoc($col->orbit_r)) echo "<option value=".$data['field_id']."> ".stripslashes($data['name']).($data['aktiv'] == 1 ? " (an)" : " (aus)");
	// echo "</select></td></tr>
	// <tr><td><input type=submit name=am value=Aktivieren class=button> <input type=submit name=am value=Deaktivieren class=button></td></tr></table><br>
	// <table class=tcal><th>Gebäude auf der Oberfläche</th>
	// <tr><td><select name=fields[] size=10 multiple>";
	// while($data=mysql_fetch_assoc($col->field_r)) echo "<option value=".$data['field_id']."> ".stripslashes($data['name']).($data['aktiv'] == 1 ? " (an)" : " (aus)");
	// echo "</select></td></tr>
	// <tr><td><input type=submit name=am value=Aktivieren class=button> <input type=submit name=am value=Deaktivieren class=button></td></tr></table>";
	// if ($col->is_moon == 0)
	// {
		// echo "<br><table class=tcal><th>Gebäude im Untergrund</th>
		// <tr><td><select name=fields[] size=10 multiple>";
		// while($data=mysql_fetch_assoc($col->ground_r)) echo "<option value=".$data['field_id']."> ".stripslashes($data['name']).($data['aktiv'] == 1 ? " (an)" : " (aus)");
		// echo "</select></td></tr>
			// <tr><td><input type=submit name=am value=Aktivieren class=button> <input type=submit name=am value=Deaktivieren class=button></td></tr></table>";
	// }
	// echo "</form>
	// </td>
	// <td width=25></td>
	// <td valign=top width=300>
	// <table class=tcal><th>Gruppen</th>
	// <tr>
		// <td><b>Energie</b><br><br>
		// <a href=?ps=".$_SESSION['pagesess']."&p=colony&s=gse&a=gs&m=1&id=".$_GET['id'].">Produzenten aktivieren</a><br>
		// <a href=?ps=".$_SESSION['pagesess']."&p=colony&s=gse&a=gs&m=2&id=".$_GET['id'].">Produzenten deaktivieren</a><br><br>
		// <a href=?ps=".$_SESSION['pagesess']."&p=colony&s=gse&a=gs&m=3&id=".$_GET['id'].">Verbraucher aktivieren</a><br>
		// <a href=?ps=".$_SESSION['pagesess']."&p=colony&s=gse&a=gs&m=4&id=".$_GET['id'].">Verbraucher deaktivieren</a></td>
	// </tr>
	// <form action=main.php method=get><input type=hidden name=p value=colony>
	// <input type=hidden name=ps value=".$_SESSION['pagesess'].">
	// <input type=hidden name=s value=gse><input type=hidden name=a value=gs><input type=hidden name=m value=5><input type=hidden name=id value=".$_GET['id'].">
	// <tr>
		// <td><b>Waren</b><br><br>
		// Bezogen auf Ware <select name=go>";
		// $result = $db->query("SELECT goods_id,name FROM stu_goods WHERE (goods_id<=50 AND view=1) ORDER BY sort LIMIT 50");
		// while($data=mysql_fetch_assoc($result)) echo "<option value=".$data['goods_id'].">".stripslashes($data['name']);
		// echo "</select><br>
		// <input type=\"checkbox\" name=\"wpro\" value=\"1\"> Produzenten<br>
		// <input type=\"checkbox\" name=\"wver\" value=\"1\"> Verbraucher<br>
		// <input type=submit class=button name=am value=Aktivieren> <input type=submit class=button name=am value=Deaktivieren></td>
	// </tr></form>
	// <tr>
		// <td><b>Bevölkerung</b><br><br>
		// <a href=?ps=".$_SESSION['pagesess']."&p=colony&s=gse&a=gs&m=6&id=".$_GET['id'].">Wohnhäuser aktivieren</a><br>
		// <a href=?ps=".$_SESSION['pagesess']."&p=colony&s=gse&a=gs&m=7&id=".$_GET['id'].">Wohnhäuser deaktiviern</a><br><br>
		// <a href=?ps=".$_SESSION['pagesess']."&p=colony&s=gse&a=gs&m=8&id=".$_GET['id'].">Betriebe aktivieren</a><br>
		// <a href=?ps=".$_SESSION['pagesess']."&p=colony&s=gse&a=gs&m=9&id=".$_GET['id'].">Betriebe deaktivieren</a></td>
	// </tr>
	// <tr>
		// <td><b>Forschung</b><br><br>
		// <a href=?ps=".$_SESSION['pagesess']."&p=colony&s=gse&a=gs&m=10&id=".$_GET['id'].">Forschungsziel Verarbeitung</a><br>
		// <a href=?ps=".$_SESSION['pagesess']."&p=colony&s=gse&a=gs&m=11&id=".$_GET['id'].">Forschungsziel Technik</a><br>
		// <a href=?ps=".$_SESSION['pagesess']."&p=colony&s=gse&a=gs&m=12&id=".$_GET['id'].">Forschungsziel Konstruktion</a>
		// </td>
	// </tr>
	// </table></td>
	// <td width=25></td>
	// <td width=150 valign=top>
	// <table class=tcal>
	// <th colspan=3>Vorschau</th>
	// <tr>
	// <td><img src=".$gfx."/buttons/e_trans1.gif title=\"Energie\"> ".$col->eps."</td><td>".($fe < 0 ? "<font color=#FF0000>".$fe."</font>" : ($fe == 0 ? 0 : "<font color=#00FF00>+".$fe."</font>"))."</td>
	// </tr>";
	// if ($db->query("SELECT aktiv FROM stu_colonies_fielddata WHERE colonies_id=".$_GET['id']." AND buildings_id=54 LIMIT 1",1) == 1)
	// {
		// $col->goods[1] += $db->query("SELECT COUNT(*) FROM stu_colonies_fielddata WHERE colonies_id=".$_GET['id']." AND (buildings_id=2 OR buildings_id=9 OR buildings_id=102) AND aktiv=1",1);
	// }
	// while($data=mysql_fetch_assoc($col->result))
	// {
		// if ($data['goods_id'] > 50) break;
		// if ($data['goods_id'] == 1) $col->goods[1] -= ceil(($col->bev_free+$col->bev_work)/5);
		// if ($data['count'] == 0 && $col->goods[$data['goods_id']] == 0) continue;
		// echo "<tr><td><img src=".$gfx."/goods/".$data['goods_id'].".gif title='".ftit($data['name'])."'> ".(!$data['count'] ? 0 : $data['count'])."</td><td>".(!$col->goods[$data['goods_id']] ? 0 : ($col->goods[$data['goods_id']] > 0 ? "<font color=#00ff00>+".$col->goods[$data['goods_id']]."</font>" : "<font color=#FF0000>".$col->goods[$data['goods_id']]."</font>"))."</td></tr>";
	// }
	// echo "</table>
	// </td>
	// </tr></table>";
	
	
	$builds = $col->getBuildingData();
	
	$totalprod = array();
	$totaleffects = array();
	$totalbevpro = 0;
	$totalbevuse = 0;
	$totalenergy = 0;
	foreach($builds as $id => $build) {
		foreach($build['goods'] as $good => $count)
			$totalprod[$good] += $count * $build['on'];
		
		foreach($build['effects'] as $type => $count)
			$totaleffects[$type] += $count * $build['count'];		
			
		$totalenergy += $build['eps_proc'] * $build['on'];
		$totalbevpro += $build['bev_pro'] * $build['on'];
		$totalbevuse += $build['bev_use'] * $build['on'];
	}
	
	$totalprod[1] -= ceil(($col->bev_free + $col->bev_work) / 10);
	
	$stufflist = array();
	array_push($stufflist,"<td style=\"width:100px;padding:4px;\">".infoPic('energy')." ".plusminus($totalenergy)."</td>");
	array_push($stufflist,"<td style=\"width:100px;padding:4px;\">".infoPic('bev_pro')." ".plusminus($totalbevpro)."</td>");
	array_push($stufflist,"<td style=\"width:100px;padding:4px;\">".infoPic('bev_use')." ".plusminus($totalbevuse)."</td>");
	foreach($totalprod as $good => $count) {
		array_push($stufflist,"<td style=\"width:100px;padding:4px;\">".goodPic($good)." ".plusminus($count)."</td>");
	}
	foreach($totaleffects as $type => $count) {
		array_push($stufflist,"<td style=\"width:100px;padding:4px;\">".infoPic($type)." ".plusminus($count)."</td>");
	}
	
	$prodstring = "<table width=100%>";
	$i = 0;
	$rows = 3;
	foreach($stufflist as $stuff) {
		if ($i % $rows == 0) {
			$prodstring .=  "<tr>";
		}		
		$prodstring .= $stuff;
		$i++;
		if ($i % $rows == $rows) {
			$prodstring .=  "</tr>";
		}
	}
	if ($i % $rows < $rows && $i % $rows > 0) {
		$prodstring .= "<td colspan=".($rows - ($i % $rows))."></td></tr>";
	}
	$prodstring .= "</table>";
	
	
	
	
	$buildsstring = "<table width=100%>";
	
	foreach($builds as $id => $build) {
		
		$buildsstring .= "<tr>";
		

		$buildsstring .= "<td style=\"width:30px;padding:4px;\"><img src='".$gfx."/buildings/".$id."/0.png' onMouseOver=\"showBuild(this,'".$id."');\" onMouseOut=\"hideInfo();\"></td>";
		$buildsstring .= "<td width=* style=\"padding:4px;\">".$build['name']."</td>";

		if ($build[id] == 1 || !$build['activateable']) {
			$buildsstring .= "<td colspan=3 style=\"width:150px;text-align:center;\">".$build['count']."</td>";
		} else {
			if ($build['on'] > 0)
				$buildsstring .= "<td style=\"width:25px;text-align:center;\"><a href='?p=colony&s=gse&id=".$_GET['id']."&dv=".$id."' ".getHover("dv".$id,"active/n/dir_d","hover/r/dir_d")."><img name=dv".$id." src=".$gfx."/buttons/active/n/dir_d.gif border=0 title=\"Deaktivieren\" ></a></td>";
			else 
				$buildsstring .= "<td style=\"width:25px;text-align:center;\"></td>";
			
			if ($build['on'] == $build['count'])
				$buildsstring .= "<td style=\"width:100px;text-align:center;\"><span class=\"valueplus\">".$build['on']." / ".$build['count']."</span></td>";					
			else
				$buildsstring .= "<td style=\"width:100px;text-align:center;\">".$build['on']." / ".$build['count']."</td>";			
			
			
			if ($build['on'] < $build['count'])
				$buildsstring .= "<td style=\"width:25px;text-align:center;\"><a href='?p=colony&s=gse&id=".$_GET['id']."&av=".$id."' ".getHover("av".$id,"active/n/dir_u","hover/g/dir_u")."><img name=av".$id." src=".$gfx."/buttons/active/n/dir_u.gif border=0 title=\"Aktivieren\" ></a></td>";			else 
				$buildsstring .= "<td style=\"width:25px;text-align:center;\"></td>";			
		}
		
		$buildsstring .= "</tr>";
	}
	
	
	
	$buildsstring .= "</table>";	

	
	
	echo "<table class=tablelayout>";
	
	echo "<tr>";
	echo "<td class=tablelayout style=\"width:580px; vertical-align:top;\">";
	echo fixedPanel(1,"Gebäude","slist",$gfx."/buttons/icon/options.gif",$buildsstring);	
	echo "</td>";
	echo "<td class=tablelayout style=\"width:330px; vertical-align:top;\">";
	echo fixedPanel(3,"Produktions-Übersicht","slist",$gfx."/buttons/icon/storage.gif",$prodstring);	
	echo "</td>";		
	echo "</tr>";
	
	echo "</table>";
	
	
	
}
if ($v == "prodlimit")
{
	pageheader("/ <a href=?p=colony>Kolonien</a> / <a href=?p=colony&s=sc&id=".$col->id.">".ftit($col->name)."</a> / <b>Produktionslimits</b>");
	if (is_array($_GET['fields']) && $_SESSION['preps'] == $_GET['ps'])
	{
		foreach($_GET['fields'] as $key => $value)
		{
			$_GET['am'] == "Aktivieren" ? $res = $col->activatebuilding($value) : $res = $col->deactivatebuilding($value);
			if ($res) $result .= $res."<br>";
			unset($col->fdd);
		}
	}
	if ($_GET['a'] == "gs" && check_int($_GET['m']) && $_SESSION['preps'] == $_GET['ps']) $result = $col->gschaltung($_GET['m']);
	if ($result) meldung($result);
	$col->loadgschaltung();
	$col->loadcolstorage();
	$fe = $db->query("SELECT SUM(b.eps_proc) FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.aktiv=1 AND a.colonies_id=".$_GET['id'],1);
	echo "<form action=main.php method=post><input type=hidden name=p value=colony><input type=hidden name=s value=gse>
	<input type=hidden name=ps value=".$_SESSION['pagesess'].">
	<input type=hidden name=id value=".$_GET['id'].">
	
	
	<table class=tcal style=\"width:500px;\">
	<th>Produktionslimits einstellen</th>

	<form action=main.php method=get><input type=hidden name=p value=colony>
	<input type=hidden name=ps value=".$_SESSION['pagesess'].">
	<input type=hidden name=s value=gse><input type=hidden name=a value=gs><input type=hidden name=m value=5><input type=hidden name=id value=".$_GET['id'].">
	<tr>
		<td>
		Bezogen auf Ware <select name=go>";
		$result = $db->query("SELECT goods_id,name FROM stu_goods WHERE (goods_id<=50 AND view=1) ORDER BY sort LIMIT 50");
		while($data=mysql_fetch_assoc($result)) echo "<option value=".$data['goods_id'].">".stripslashes($data['name']);
		echo "</select><br>
		<input type=\"checkbox\" name=\"wpro\" value=\"1\"> Produzenten<br>
		<input type=\"checkbox\" name=\"wver\" value=\"1\"> Verbraucher<br>
		<input type=submit class=button name=am value=Aktivieren> <input type=submit class=button name=am value=Deaktivieren></td>
	</tr></form>
	</table>
	
	<table class=tcal>
	<th colspan=3>Vorschau</th>
	<tr>
	<td><img src=".$gfx."/buttons/e_trans1.gif title=\"Energie\"> ".$col->eps."</td><td>".($fe < 0 ? "<font color=#FF0000>".$fe."</font>" : ($fe == 0 ? 0 : "<font color=#00FF00>+".$fe."</font>"))."</td>
	</tr>";
	if ($db->query("SELECT aktiv FROM stu_colonies_fielddata WHERE colonies_id=".$_GET['id']." AND buildings_id=54 LIMIT 1",1) == 1)
	{
		$col->goods[1] += $db->query("SELECT COUNT(*) FROM stu_colonies_fielddata WHERE colonies_id=".$_GET['id']." AND (buildings_id=2 OR buildings_id=9) AND aktiv=1",1);
	}
	while($data=mysql_fetch_assoc($col->result))
	{
		if ($data['goods_id'] > 50) break;
		if ($data['goods_id'] == 1) $col->goods[1] -= ceil(($col->bev_free+$col->bev_work)/5);
		if ($data['count'] == 0 && $col->goods[$data['goods_id']] == 0) continue;
		echo "<tr><td><img src=".$gfx."/goods/".$data['goods_id'].".gif title='".ftit($data['name'])."'> ".(!$data['count'] ? 0 : $data['count'])."</td><td>".(!$col->goods[$data['goods_id']] ? 0 : ($col->goods[$data['goods_id']] > 0 ? "<font color=#00ff00>+".$col->goods[$data['goods_id']]."</font>" : "<font color=#FF0000>".$col->goods[$data['goods_id']]."</font>"))."</td></tr>";
	}
	echo "</table>";
}
if ($v == "schiffbau")
{
	echo "<script language=\"Javascript\">
	var elt;
	function get_window(elt,width)
	{
		return overlib('<div id='+elt+'></div>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER, STICKY, RELX, 100, RELY, 100, WIDTH, width);
	}
	function getrinfo(rid,fg)
	{	
		elt = fg;
		get_window(elt,422);
		sendRequest('backend/rinfo.php?PHPSESSID=".session_id()."&rid=' + rid);
	}
	function getbpc(pid)
	{	
		elt = 'bpc';
		get_window(elt,300);
		sendRequest('backend/comm/sendbuildplan.php?PHPSESSID=".session_id()."&id=".$_GET['id']."&pid=' + pid);
	}
	function loadinfo(rid,fg)
	{	
		elt = fg;
		sendRequest('backend/rdetail.php?PHPSESSID=".session_id()."&rid=' + rid);
	}
	function setpos(off)
	{
		elt = 'rl';
		sendRequest('backend/rlist.php?PHPSESSID=".session_id()."&off=' + off);
	}
	</script><style>
	td.pages {
		text-align: center;
		width: 20px;
		border: 1px groove #8897cf;
	}
	td.pages:hover
	{
		background: #262323;
	}
	</style>";
	pageheader("/ <a href=?p=colony>Kolonien</a> / <a href=?p=colony&s=sc&id=".$col->id.">".ftit($col->name)."</a> / <b>Schiffbau</b>");
	if ($_GET['a'] == "spn" && is_array($_GET['pn'])) $result = $col->setplanname($_GET['pn']);
	if ($_GET['a'] == "dp" && check_int($_GET['pid'])) $result = $col->delplan($_GET['pid']);
	if ($_GET['a'] == "bcs" && $_SESSION['preps'] == $_GET['ps']) $result = $col->buildcolship();
	if ($_GET['a'] == "hpl" && check_int($_GET['pid'])) $result = $col->hide_buildplan($_GET['pid']);
	if ($_GET['a'] == "uhpl" && check_int($_GET['pid'])) $result = $col->unhide_buildplan($_GET['pid']);
	if ($_GET['a'] == "sbp" && check_int($_GET['pid']) && $_SESSION['preps'] == $_GET['ps'] && check_int($_GET['rec'])) $result = $col->send_buildplan($_GET['pid'],$_GET['rec']);
	if ($result) meldung($result);
	if ($col->getwerfttype($_GET['id']) == 8) $col->loadcolshiprump();
	else $col->loadpossiblerumps();
	
	echo "<table><tr><td valign=\"top\">
	<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=260><th colspan=4>Verfügbare Schiffsrümpfe</th>";
	if ($_SESSION['level'] >=4)
	{
		$i++;
		$j = 1;
		// echo "<tr><td><a href=\"javascript:void(0)\" onClick=\"getrinfo(1,'rinfo');\"><img src=".$gfx."/ships/1.gif title=\"Kolonieschiff\" border=0></a></td><td><a href=\"http://wiki.stuniverse.de/index.php/Kolonieschiff\" target=\"_blank\">Kolonieschiff</a></td><td>-</td><td><a href=?p=colony&s=sb&id=".$_GET['id']."&a=bcs&ps=".$_SESSION['pagesess']." ".getonm('shibu'.$i,'buttons/builds')."><img src=".$gfx."/buttons/builds1.gif name=\"shibu".$i."\" border=\"0\" title=\"Kolonieschiff bauen\"></a></td></tr>";
	}
	while($data=mysql_fetch_assoc($col->result))
	{
		$i++;
		$j++;
		if ($j == 2)
		{
			$trc = " style=\"background-color: #171616\"";
			$j = 0;
		}
		echo "<tr>
		<td".$trc."><a href=\"javascript:void(0)\" onClick=\"getrinfo(".$data['rumps_id'].",'rinfo');\"><img src=".$gfx."/ships/".$data['rumps_id'].".gif title=\"".ftit($data['name'])."\" border=0></a></td>
		<td".$trc."><a href=\"http://wiki.stuniverse.de/index.php/".stripslashes($data['name'])."\" target=_blank>".stripslashes($data['name'])."</a></td>
		<td".$trc.">".(!$data['shid'] ? 0 : $data['shid'])."</td>
		<td".$trc."><a href=?p=colony&s=sm&id=".$_GET['id']."&rid=".$data['rumps_id']." ".getonm('shibu'.$i,'buttons/builds')."><img src=".$gfx."/buttons/builds1.gif name=\"shibu".$i."\" border=\"0\" title=\"".ftit($data['name'])." bauen\"></a></td></tr>";
		$trc = "";
	}
	echo "</table>
	</td><td valign=\"top\" style=\"width: 100%;\">";
	include_once("inc/shutrep.php");
	echo "</td></tr></table><br />";
	
	// Seiten berechnen
	$bpc = $col->get_buildplan_count($_SESSION['uid']);
	if (!$_GET['pa'] || !check_int($_GET['pa'])) $_GET['pa'] = 1;
	// Seiten erzeugen
	$in = $_GET['pa'];
	$i = $in-2;
	$j = $in+2;
	$ceiled_knc = ceil($bpc/10);
	$ps0 = "<td>Seite: <a href=?p=colony&id=".$_GET['id']."&s=sb&pa=1>|<</a> <a href=?p=colony&id=".$_GET['id']."&s=sb&pa=".($pa == 1 ? 1 : $pa-1)."><</a></td>";
	if ($i > 1) $ps = "<td class=\"pages\"><a href=?p=colony&id=".$_GET['id']."&s=sb&pa=1>1</a></td>";
	if ($j < $ceiled_knc) $pe = "<td class=\"pages\"><a href=?p=colony&id=".$_GET['id']."&s=sb&pa=".$ceiled_knc.">".$ceiled_knc."</a></td>";
	if ($j > $ceiled_knc) $j = $ceiled_knc;
	if ($i < 1) $i = 1;
	while($i<=$j)
	{
		$pages .= "<td class=\"pages\"><a href=?p=colony&id=".$_GET['id']."&s=sb&pa=".$i.">".($i == $in ? "<div style=\"font-weight : bold; color: Yellow;\">".$i."</div>" : $i)."</a></td>";
		$i++;
	}
	$i = $in-2;
	$j = $in+2;
	$pages = $ps.($i > 2 ? "<td style=\"width: 20px; text-align: center;\">...</td>" : "").$pages.($ceiled_knc > $j+1 ? "<td style=\"width: 20px; text-align: center;\">... </td>" : "").$pe;
	$pe0 = "<td><a href=?p=colony&id=".$_GET['id']."&s=sb&pa=".($_GET['pa'] == $ceiled_knc ? $_GET['pa'] : $_GET['pa']+1).">></a>&nbsp;<a href=?p=colony&id=".$_GET['id']."&s=sb&pa=".$ceiled_knc.">>|</a> (".$bpc." Baupläne)</td>";

	$m = ($_GET['pa']-1)*10;
	$col->loadbuildplans($m);
	// Wirtschaftspunkte errechnen
	$wp_v = $col->get_free_wpoints($_SESSION['uid']);
	echo "<form action=main.php method=get><table class=tcal><input type=hidden name=p value=colony><input type=hidden name=a value=spn>
	<input type=hidden name=s value=sb><input type=hidden name=id value=".$_GET['id']."><th colspan=6>Gespeicherte Baupläne (".$wp_v." Wirtschaftspunkte frei)</th>
	<tr>
		<td></td>
		<td><b>Rumpf</b></td>
		<td><b>Bauplan</b></td>
		<td><b>Module</b></td>
		<td><img src=".$gfx."/buttons/points.gif title=\"Wirtschaftspunkte\"></td>
		<td><b>Name</b></td>
		<td></td>
	</tr>";
	while($data=mysql_fetch_assoc($col->result))
	{
		if ($data['hidden'] == 1) $trc = " style=\"background-color: #2e2e2e;\"";
		else $trc = "";
		echo "<tr><td".$trc.">".(!$data['idc'] ? 0 : $data['idc'])."</td><td".$trc."><a href=?p=colony&s=sm&id=".$_GET['id']."&rid=".$data['rumps_id']."&m1=".$data['m1']."&m2=".$data['m2']."&m3=".$data['m3']."&m4=".$data['m4']."&m5=".$data['m5']."&m6=".$data['m6']."&m7=".$data['m7']."&m8=".$data['m8']."&m9=".$data['m9']."&m10=".$data['m10']."&m11=".$data['m11']."&sb=Vorschau><img src=".$gfx."/ships/".$data['rumps_id'].".gif title=\"".ftit($data['name'])."\" border=0></a></td><td".$trc.">".stripslashes($data['name'])."</td><td".$trc.">";
		if ($data['m1'] != 0) echo "<img src=".$gfx."/goods/".$data['m1'].".gif title=\"".$db->query("SELECT name FROM stu_modules WHERE module_id=".$data['m1'],1)."\">&nbsp;";
		if ($data['m2'] != 0) echo "<img src=".$gfx."/goods/".$data['m2'].".gif title=\"".$db->query("SELECT name FROM stu_modules WHERE module_id=".$data['m2'],1)."\">&nbsp;";
		if ($data['m3'] != 0) echo "<img src=".$gfx."/goods/".$data['m3'].".gif title=\"".$db->query("SELECT name FROM stu_modules WHERE module_id=".$data['m3'],1)."\">&nbsp;";
		if ($data['m4'] != 0) echo "<img src=".$gfx."/goods/".$data['m4'].".gif title=\"".$db->query("SELECT name FROM stu_modules WHERE module_id=".$data['m4'],1)."\">&nbsp;";
		if ($data['m5'] != 0) echo "<img src=".$gfx."/goods/".$data['m5'].".gif title=\"".$db->query("SELECT name FROM stu_modules WHERE module_id=".$data['m5'],1)."\">&nbsp;";
		if ($data['m6'] != 0) echo "<img src=".$gfx."/goods/".$data['m6'].".gif title=\"".$db->query("SELECT name FROM stu_modules WHERE module_id=".$data['m6'],1)."\">&nbsp;";
		if ($data['m7'] != 0) echo "<img src=".$gfx."/goods/".$data['m7'].".gif title=\"".$db->query("SELECT name FROM stu_modules WHERE module_id=".$data['m7'],1)."\">&nbsp;";
		if ($data['m8'] != 0) echo "<img src=".$gfx."/goods/".$data['m8'].".gif title=\"".$db->query("SELECT name FROM stu_modules WHERE module_id=".$data['m8'],1)."\">&nbsp;";
		if ($data['m9'] != 0) echo "<img src=".$gfx."/goods/".$data['m9'].".gif title=\"".$db->query("SELECT name FROM stu_modules WHERE module_id=".$data['m9'],1)."\"f>&nbsp;";
		if ($data['m10'] != 0) echo "<img src=".$gfx."/goods/".$data['m10'].".gif title=\"".$db->query("SELECT name FROM stu_modules WHERE module_id=".$data['m10'],1)."\">&nbsp;";
		if ($data['m11'] != 0) echo "<img src=".$gfx."/goods/".$data['m11'].".gif title=\"".$db->query("SELECT name FROM stu_modules WHERE module_id=".$data['m11'],1)."\">&nbsp;";
		echo "</td><td".$trc.">".($wp_v < $data['wpoints'] ? "<font color=#FF0000>".$data['wpoints']."</font>" : $data['wpoints'])."</td>
		<td".$trc."><input type=text size=15 name=pn[".$data['plans_id']."] class=text> <input type=submit value=ändern class=button></td>
		<td".$trc.">".($data['hidden'] == 0 ? "<a href=?p=colony&s=sb&id=".$_GET['id']."&pa=".$_GET['pa']."&a=hpl&pid=".$data['plans_id']." ".getonm('pl'.$data['plans_id'],'buttons/knedit')."><img src=".$gfx."/buttons/knedit1.gif border=0 name=pl".$data['plans_id']." title=\"Bauplan verschieben\"></a>" : "<a href=?p=colony&s=sb&id=".$_GET['id']."&pa=".$_GET['pa']."&a=uhpl&pid=".$data['plans_id']." ".getronm('pl'.$data['plans_id'],'buttons/knedit')."><img src=".$gfx."/buttons/knedit2.gif border=0 name=pl".$data['plans_id']." title=\"Bauplan anzeigen\"></a>")."
		&nbsp;<a href=\"javascript:void(0);\" onClick=\"getbpc(".$data['plans_id'].");\" ".getonm('bpc'.$data['plans_id'],'buttons/msg')."><img src=".$gfx."/buttons/msg1.gif name=bpc".$data['plans_id']." title=\"Bauplan verschicken\" border=\"0\"></a>
		&nbsp;".($data['idc'] > 0 ? "" : "<a href=?p=colony&s=sb&a=dp&id=".$_GET['id']."&pid=".$data['plans_id']." ".getonm("del".$data['plans_id']."","buttons/x")."><img src=".$gfx."/buttons/x1.gif border=0 title='Plan löschen' name=del".$data['plans_id']."></a>")."</td></tr>";
	}
	echo "<tr><td colspan=\"7\"><table><tr>".$ps0.$pages.$pe0."</tr></table></td></tr>
	</table></form>";
}
if ($v == "selectmodules")
{
	pageheader("/ <a href=?p=colony>Kolonien</a> / <a href=?p=colony&s=sc&id=".$col->id.">".ftit($col->name)."</a> / <a href=?p=colony&s=sb&id=".$col->id.">Schiffbau</a> / <b>Module wählen</b>");
	if ($_GET['sb'] == "Bauen" && $_SESSION['preps'] == $_GET['ps']) meldung($col->buildship());
	if ($_GET['sp'] == "Speichern" && $_SESSION['preps'] == $_GET['ps']) meldung($col->save_buildplan());
	echo "<table class=tcal><th colspan=10>Gewählter Schiffsrumpf</th>
	<tr><td rowspan=2><img src=".$gfx."/ships/".$_GET['rid'].".gif></td><td rowspan=2>".stripslashes($col->rump['name'])."</td><td>Lagerraum</td><td>Bussard</td><td>Erzkollektoren</td><td>Reaktor</td><td>Replikator</td></tr>
	<tr><td>".$col->rump['storage']."</td><td>".$col->rump['bussard']."</td><td>".$col->rump['erz']."</td><td>".$col->rump['reaktor']."</td><td>".($col->rump['replikator'] == 1 ? "Ja" : "Nein")."</td></tr></table><br>";
	$result = $col->getmods($col->rump['m1_lvl'],1);
	$result2 = $col->getmods($col->rump['m2_lvl'],2);
	echo "<form action=main.php method=get><input type=hidden name=p value=colony>
	<input type=hidden name=s value=sm><input type=hidden name=id value=".$_GET['id']."><input type=hidden name=rid value=".$_GET['rid'].">
	<input type=hidden name=ps value=".$_SESSION['pagesess'].">";
	
	function printModuleOption($mtype) {
		global $gfx,$col,$_GET;
	
		$result = $col->getmods($col->rump[$mtype[id]."_lvl"],$mtype[num]);
		echo "<tr><td width=600><b>".$mtype[name]."</b></td><td><b>Effekte</b></td></tr>";

		$options = "";
		$found = false;
		while($data=mysql_fetch_assoc($result))
		{
			$checked = false;		
			if ($_GET["m_".$mtype[id]] == $data[module_id]) $bm[] = $data;
			if (($_GET["m_".$mtype[id]] == $data[module_id]) || (!$_GET["m_".$mtype[id]] && !$found)) $checked = true;
			$found = true;		
			$options .= "<tr>
			<td width=600><input type=\"radio\" name=\"m_".$mtype[id]."\" value=\"".$data[module_id]."\"".($checked ? " CHECKED" : "")."> <img src=".$gfx."/goods/".$data[module_id].".gif title=\"".$data[name]."\"> ".$data[name]."</td>
			<td>".$col->getmodulespecial($data)."</td>
			</tr>";
		}
		
		if ($mtype[optional]) {
			$checked = false;
			if (!$found) $checked = true;
			echo "<tr>
			<td width=600><input type=\"radio\" name=\"m_".$mtype[id]."\" value=\"0\"".($checked ? " CHECKED" : "")."> Keine</td><td>Keine</td>
			</tr>";
			echo $options;
		} else {
			echo $options;
			if (!$found) echo "<tr><td colspan=2><font color=red><b>Keine Module dieses Typs vorhanden!</b></font></td></tr>";	
		}
		
	}	
	
	function printWeaponOption($mtype,$wtypes) {
		global $gfx,$col,$_GET;
	
		$result = $col->getmods($col->rump[$mtype[id]."_lvl"],$mtype[num]);
		echo "<tr><td colspan=2><b>".$mtype[name]."</b></td><td><b>Effekte</b></td></tr>";

		$options = "";
		$found = false;
		while($data=mysql_fetch_assoc($result))
		{
			if (!in_array($data['subtype'],$wtypes)) continue;
			$checked = false;		
			if ($_GET["m_".$mtype[id]] == $data[module_id]) $bm[] = $data;
			if (($_GET["m_".$mtype[id]] == $data[module_id]) || (!$_GET["m_".$mtype[id]] && !$found)) $checked = true;
			$found = true;		
			$options .= "<tr>
			<td width=300><input type=\"radio\" name=\"m_".$mtype[id]."\" value=\"".$data[module_id]."\"".($checked ? " CHECKED" : "")."> <img src=".$gfx."/goods/".$data[module_id].".gif title=\"".$data[name]."\"> ".$data[name]."</td>
			<td width=300>".weaponTypeDescription(trim($data['subtype']))."</td>
			<td>".$col->getmodulespecial($data)."</td>
			</tr>";
		}
		
		if ($mtype[optional]) {
			$checked = false;
			if (!$found) $checked = true;
			echo "<tr>
			<td colspan=2><input type=\"radio\" name=\"m_".$mtype[id]."\" value=\"0\"".($checked ? " CHECKED" : "")."> Keine</td><td>Keine</td>
			</tr>";
			echo $options;
		} else {
			echo $options;
			if (!$found) echo "<tr><td colspan=2><font color=red><b>Keine Module dieses Typs vorhanden!</b></font></td></tr>";	
		}
		
	}		
	
	
	function printSpecialOption($mtype,$wtypes,$rump) {
		global $gfx,$col,$_GET;
	
		$result = $col->getmodsnolvl($mtype[num]);
		echo "<tr><td colspan=2><b>".$mtype[name]."</b></td><td><b>Effekte</b></td></tr>";
		
		echo "<tr>
		<td colspan=2><input type=\"radio\" name=\"m_".$mtype[id]."\" value=\"0\""." CHECKED"."> Keine</td><td>Keine</td>
		</tr>";
			
		$options = "";
		$found = false;
		while($data=mysql_fetch_assoc($result))
		{
			if (!in_array($data['subtype'],$wtypes)) continue;
			$checked = false;		
			if ($_GET["m_".$mtype[id]] == $data[module_id]) $bm[] = $data;	
			$options .= "<tr>
			<td width=300><input type=\"radio\" name=\"m_".$mtype[id]."\" value=\"".$data[module_id]."\"".($checked ? " CHECKED" : "")."> <img src=".$gfx."/goods/".$data[module_id].".gif title=\"".$data[name]."\"> ".$data[name]."</td>
			<td width=300>".specialTypeDescription(trim($data['subtype']))."</td>
			<td>".$col->getmodulespecial($data)."</td>
			</tr>";
		}
		
		if ($mtype[optional]) {
			$checked = false;
			if (!$found) $checked = true;


		}
		
		
		
		if ($mtype[id] == "s1" &&($rump == 6501 || $rump == 6502 || $rump == 6503)) {
			$result = $col->getmodcolony();
		while($data=mysql_fetch_assoc($result))
		{
			$checked = false;
			echo "<tr>
			<td width=300><input type=\"radio\" name=\"m_".$mtype[id]."\" value=\"".$data[module_id]."\"".($checked ? " CHECKED" : "")."> <img src=".$gfx."/goods/".$data[module_id].".gif title=\"".$data[name]."\"> ".$data[name]."</td>
			<td width=300>".specialTypeDescription(trim($data['subtype']))."</td>
			<td>".$col->getmodulespecial($data)."</td>
			</tr>";
		}
		}
		
		echo $options;

		
	}	
	
	$w1types = array_map('trim', explode(',',$col->rump['w1_types']));
	$w2types = array_map('trim', explode(',',$col->rump['w2_types']));
	$s1types = array_map('trim', explode(',',$col->rump['s1_types']));
	$s2types = array_map('trim', explode(',',$col->rump['s2_types']));
	
	echo "<table class=tcal><tr><td colspan=3><img src=".$gfx."/buttons/sb_waffen_".$_SESSION["race"].".gif></td></tr>";	
	$mtype[id] = "w1"; $mtype[num] = "6"; $mtype[name] = "Primärwaffe"; $mtype[optional] = true;
	printWeaponOption($mtype,$w1types);
	$mtype[id] = "w2"; $mtype[num] = "6"; $mtype[name] = "Sekundärwaffe"; $mtype[optional] = true;
	printWeaponOption($mtype,$w2types);
	echo "</table><br>";	
	
	echo "<table class=tcal><tr><td colspan=2><img src=".$gfx."/buttons/sb_schilde_".$_SESSION["race"].".gif></td></tr>";	
	$mtype[id] = "m1"; $mtype[num] = "1"; $mtype[name] = "Hüllenpanzerung"; $mtype[optional] = false;
	printModuleOption($mtype);
	$mtype[id] = "m2"; $mtype[num] = "2"; $mtype[name] = "Schilde"; $mtype[optional] = false;
	printModuleOption($mtype);	
	echo "</table><br>";		
	
	echo "<table class=tcal><tr><td colspan=2><img src=".$gfx."/buttons/sb_energie_".$_SESSION["race"].".gif></td></tr>";
	$mtype[id] = "m3"; $mtype[num] = "3"; $mtype[name] = "Warpkern"; $mtype[optional] = false;
	printModuleOption($mtype);
	echo "</table><br>";
	
	echo "<table class=tcal><tr><td colspan=2><img src=".$gfx."/buttons/sb_antrieb_".$_SESSION["race"].".gif></td></tr>";	
	$mtype[id] = "m4"; $mtype[num] = "4"; $mtype[name] = "Antrieb";	$mtype[optional] = false;
	printModuleOption($mtype);
	echo "</table><br>";	
		
	echo "<table class=tcal><tr><td colspan=2><img src=".$gfx."/buttons/sb_sensor_".$_SESSION["race"].".gif></td></tr>";
	$mtype[id] = "m5"; $mtype[num] = "5"; $mtype[name] = "Sensoren"; $mtype[optional] = false;
	printModuleOption($mtype);
	echo "</table><br>";	
	
	echo "<table class=tcal><tr><td colspan=3><img src=".$gfx."/buttons/sb_support_".$_SESSION["race"].".gif></td></tr>";	
	$mtype[id] = "s1"; $mtype[num] = "7"; $mtype[name] = "Spezialmodule 1"; $mtype[optional] = true;
	printSpecialOption($mtype,$s1types,$_GET['rid']);
	$mtype[id] = "s2"; $mtype[num] = "7"; $mtype[name] = "Spezialmodule 2"; $mtype[optional] = true;
	printSpecialOption($mtype,$s2types,$_GET['rid']);	
	echo "</table><br>";	
	
	// print_r(getShipValuesWithMods(3401,array("1999")));
	
	echo "<br><input type=submit value=Vorschau class=button> <input type=submit name=sb value=Bauen class=button><br><br>";
	unset($weapon);
	// if (is_array($bm))
	// {
		// foreach($bm as $key => $data)
		// {
			// $huelle += $data[huelle]*$col->rump["m".$data[type]."c"];
			// $bz += $data[buildtime];
			// $maintain += $data[maintaintime];
			// $points += $data[points];
			// $schilde += $data[schilde]*$col->rump["m".$data[type]."c"];
			// $reaktor += $data[reaktor]*$col->rump["m".$data[type]."c"];
			// $wkkap += $data[wkkap];
			// $eps += $data[eps]*$col->rump["m".$data[type]."c"];
			// if ($data[type] == 1)
			// {
				// if ($data[special_id1] == 1)
				// {
					// $ev_mul = 1.1;
					// $hit_mul = 1.1;
				// }
				// if ($data[special_id1] == 2)
				// {
					// $ev_mul = 0.9;
					// $hit_mul = 0.9;
				// }
			// }
			// else $evade += $data[evade_val];
			// if ($data[type] == 11) $abfang = $data[warp_capability];
			// if ($data[type] == 4)
			// {
				// $lss = $data[lss]+($col->rump["m".$data[type]."c"] - 1);
				// $kss = $data[kss]+($col->rump["m".$data[type]."c"] - 1);
			// }
			// $torps += $data[torps]*$col->rump["m".$data[type]."c"];
			// $detect += $data[detect_val];
			// $cloak += $data[cloak_val];
			// $hit += $data[hit_val];
			// if ($data[type] == 6)
			// {
				// $weapon = $col->getweaponbyid($data['module_id']);
				// if ($weapon['pulse'] == 0) $weapon['strength'] = round($weapon['strength'] * get_weapon_damage($col->rump['m'.$data['type'].'c']),1);
				// else $weapon['strength'] = round($weapon['strength'],1);
			// }
		// }
		// $points = round(($col->rump[wp] * $points) / 10,1);
		// $evade += $col->rump[evade_val];
		// if (!$ev_mul) $ev_mul = 1;
		// $evade = round($evade * $ev_mul);
		// if (!$hit_mul) $hit_mul = 1;
		// $hit = round($hit * $hit_mul);
		// $bz = round($col->rump[buildtime]*$bz/1000);
		// $maintain = round($col->rump[maintaintime]*(2- ($maintain/1100)));
		// if (!$weapon) $weapon = array("strength" => 0,"varianz" => 0);
		// echo "<table style=\"width: 600px;\"><tr><td style=\"width: 250px;\">
		// <table class=tcal><th colspan=2>Vorschau</th>
		// <tr><td>Bauzeit</td><td>".gen_time($bz)."</td></tr>
		// <tr><td>Wartungsbedarf</td><td>".gen_time($maintain)."</td></tr>
		// <tr><td>Hülle</td><td>".round($huelle)."</td></tr>
		// <tr><td>Schilde</td><td>".round($schilde)."</td></tr>
		// <tr><td>Reaktor<br>(WK-Kapazität)</td><td>".$reaktor."<br>(".$wkkap.")</td></tr>
		// <tr><td>EPS</td><td>".round($eps)."</td></tr>
		// <tr><td>Ausweich-%</td><td>".$evade."</td></tr>
		// <tr><td>Warpfaktor</td><td>".$abfang."</td></tr>
		// <tr><td>Enttarnung</td><td>".$detect."</td></tr>
		// <tr><td>Tarnung</td><td>".$cloak."</td></tr>
		// <tr><td>KSS/LSS</td><td>".$kss."/".$lss."</td></tr>
		// <tr><td>Torpedos</td><td>".$torps."</td></tr>
		// <tr><td>Strahlenwaffe</td><td>".$weapon[strength]." (".$weapon[varianz]."%)</td></tr>
		// <tr><td>Trefferchance</td><td>".$hit."</td></tr>
		// <tr><td>Wirtschaftspunkte</td><td>".$points."</td></tr>
		// </table></td><td style=\"width: 350px;\" valign=top>";
		// $plan = $col->getpossiblebuildplans();
		// if ($plan != 0)
		// {
			// echo "<table class=tcal><th>Verfügbarer Bauplan</th>
			// <tr><td><img src=".$gfx."/ships/".$_GET["rid"].".gif> ".stripslashes($plan[name])."</td></tr>
			// <tr><td>".(!$plan[idc] ? 0 : $plan[idc])." Schiffe nach diesem Bauplan gebaut</td></tr></table>";
		// }
		// else
		// {
			// echo "<table class=tcal><th>Kein Bauplan gefunden - Lege neuen an</th>
			// <tr><td>Name <input type=text size=15 name=npn class=text> <input type=submit class=button name=sp value=Speichern></td></tr></table>";
		// }
		// echo "</td></tr></table></form>";
	// }
}
if ($v == "repairbuilding")
{
	pageheader("/ <a href=?p=colony>Kolonien</a> / <a href=?p=colony&s=sc&id=".$col->id.">".ftit($col->name)."</a> / <a href=?p=colony&s=bm&id=".$_GET['id']."&fid=".$_GET['fid'].">Feld ".$_GET['fid']."</a> / <b>Gebäudereparatur</b>");
	if ($_GET['a'] == "rp")
	{
		meldung($col->repairbuilding($_GET['fid']));
		$col->loadfield($_GET['fid'],$_GET['id']);
		$col->loadrepaircost();
	}
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1>
	<tr><td rowspan=2 valign=top width=30><img src=".$gfx."/buildings/".$col->fdd['buildings_id']."/".$col->fdd['type'].".gif title='".ftit($col->fdd['name'])."'></td><td class=m>".stripslashes($col->fdd['name'])."</td></tr>
	<tr><td>Integrität: ".$col->fdd['integrity']."/".$col->fdd['maxintegrity']."<br><br><u>Reparaturkosten</u><br><img src=".$gfx."/buttons/e_trans2.gif title='Energie'> ".ceil(($db->query("SELECT eps_cost FROM stu_buildings WHERE buildings_id=".$col->fdd['buildings_id']." LIMIT 1",1)/$col->fdd['maxintegrity'])*($col->fdd['maxintegrity']-$col->fdd['integrity']))."/".$col->eps."<br>";
	while($data=mysql_fetch_assoc($col->rpc))
	{
		if (!$data['vcount']) $data['vcount'] = 0;
		echo "<img src=".$gfx."/goods/".$data['goods_id'].".gif title='".ftit($data['name'])."'> ".$data['count']."/".($data['vcount'] < $data['count'] ? "<font color=#FF0000>".$data['vcount']."</font>" : $data['vcount'])."<br>";
	}
	echo "<br><a href=?p=colony&s=rep&id=".$_GET['id']."&fid=".$_GET['fid']."&a=rp><img src=".$gfx."/buttons/rep1.gif border=0 title='Reparieren' name=rep> Reparieren</a></td></tr>
	</table>";
}
if ($v == "colaufgeben")
{
	pageheader("/ <a href=?p=colony>Kolonien</a> / <a href=?p=colony&s=sc&id=".$_GET['id'].">".ftit($col->name)."</a> / <b>Kolonie aufgeben</b>");
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1>
	<tr><td>Soll die Kolonie wirklich aufgegeben werden?<br>
	<a href=?p=colony&delid=".$_GET['id']."&ps=".$_SESSION['pagesess']."><font color=#ff0000>Ja</font></a></td></tr>
	</table>";
}
if ($v == "sectorflights")
{
	pageheader("/ <a href=?p=colony>Kolonien</a> / <a href=?p=colony&s=sc&id=".$_GET['id'].">".ftit($col->name)."</a> / <b>Sektordurchflüge</b>");
	echo "<table class=tcal>
	<th></th><th>User</th><th>Datum</th>";
	$result = $col->loadsectorflights();
	if (mysql_num_rows($result) == 0) echo "<tr><td colspan=3>Keine Durchflüge registriert</td></tr>";
	else
	{
		while($data=mysql_fetch_assoc($result)) echo "<tr><td><img src=".$gfx."/ships/".$data['rumps_id'].".gif></td>
		<td>".stripslashes($data['user'])."</td>
		<td>".date("d.m.Y H:i",$data['date_tsp'])."</td></tr>";
	}
	echo "</table>";
}
if ($v == "battery")
{
	pageheader("/ <a href=?p=colony>Kolonien</a> / <a href=?p=colony&s=sc&id=".$_GET['id'].">".ftit($col->name)."</a> / <b>Ersatzbatterie laden</b>");
	$col->loadorbititems();
	echo "<form action=main.php><input type=hidden name=p value=colony><input type=hidden name=s value=sc><input type=hidden name=id value=".$_GET['id'].">
	<input type=hidden name=ps value=".$_SESSION['pagesess'].">
	<input type=hidden name=a value=eba><table class=tcal><th></th><th>Name</th><th>Energie</th><th>Batterie</th><th>Ladung</th><th>Siedler</th>";
	if (mysql_num_rows($col->result) == 0) echo "<tr><td colspan=6>Keine Schiffe im Orbit</td></tr>";
	else
	{
		while($data=mysql_fetch_assoc($col->result))
		{
			if ($data['slots'] > 0 || $data['trumfield'] == 1 || $data['is_shuttle'] == 1) continue;
			echo "<tr>
				<td><img src=".$gfx."/ships/".vdam($data).$data['rumps_id'].".gif title=\"".ftit($data['rname'])."\"></td>
				<td>".stripslashes($data['name'])."</td>
				<td>".$data['eps']."/".$data['max_eps']."</td>
				<td>".$data['batt']."/".$data['max_batt']."</td>
				<td><input type=text size=3 class=text name=bl[".$data['id']."]></td>
				<td>".stripslashes($data['user'])."</td>
			</tr>";
		}
	}
	echo "<tr><td colspan=6><input type=submit class=button value=Transfer></td></tr>
	</table></form>";
}
if ($v == "teleskop")
{
	pageheader("/ <a href=?p=colony>Kolonien</a> / <a href=?p=colony&s=sc&id=".$_GET['id'].">".ftit($col->name)."</a> / <b>Subraumteleskop</b>");
	$range = $col->gettelerange();
	$result = $col->getpossibletelesystems($range['sys']);
	$rw = $col->gettelecoords($range['norm']);
	echo "<form action=main.php method=get><input type=hidden name=p value=colony><input type=hidden name=s value=scn><input type=hidden name=id value=".$_GET['id'].">
	<table><tr>
	<td width=200 valign=top>
	<table class=tcal>
	<th colspan=3 align=center>Scanbare Systeme</th>";
	while($data=mysql_fetch_assoc($result)) echo "<tr><td width=30><img src=".$gfx."/map/".$data['type'].".gif></td><td><a href=?p=colony&s=scsy&id=".$_GET['id']."&sys=".$data['systems_id'].">".stripslashes($data['name'])."</a></td><td><a href=?p=colony&s=scn&id=".$_GET['id']."&cx=".$data['cx']."&cy=".$data['cy'].">".$data['cx']."|".$data['cy']."</a></td></tr>";
	echo "</table>
	</td>
	<td width=300 valign=top>
	<table class=tcal>
	<th>Koordinaten scannen</th>
	<tr>
		<td>x <input type=text size=3 class=text name=cx> | y <input type=text size=3 class=text name=cy> <input type=submit class=button value=Scannen><br>(Reichweite: x ".($rw['xmin'] < 1 ? 1 : $rw['xmin'])." - ".($rw['xmax'] > $mapfields['max_x'] ? $mapfields['max_x'] : $rw['xmax'])." | y ".($rw['ymin'] < 1 ? 1 : $rw['ymin'])." - ".($rw['ymax'] > $mapfields['max_y'] ? $mapfields['max_y'] : $rw['ymax']).")</td>
	</tr>
	</table>
	</td>
	</tr></table></form>";
}
if ($v == "scansector")
{
	pageheader("/ <a href=?p=colony>Kolonien</a> / <a href=?p=colony&s=sc&id=".$_GET['id'].">".ftit($col->name)."</a> / <a href=?p=colony&s=tel&id=".$_GET['id'].">Subraumteleskop</a> /  <b>Sektor scannen</b> (".$_GET['cx']."|".$_GET['cy'].")");
	$result = $col->telescan($_GET['cx'],$_GET['cy']);
	if ($result) return meldung($result);
	else
	{
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=200>
		<tr>
			<td><img src=".$gfx."/map/".$col->type['type'].".gif title=\"".$col->type['name']."\"></td><td>Gescante Signaturen: ".mysql_num_rows($col->result)."</td>
		</tr>
		</table><br>
		<table bgcolor=#262323 cellspacing=1 cellpadding=1>
		<th width=30></th><th>Name</th><th>Siedler</th>";
		if (mysql_num_rows($col->result) == 0) echo "<tr><td colspan=3>Keine Signaturen entdeckt</td></tr>";
		else
		{
			while($data=mysql_fetch_assoc($col->result))
			{
				if ($data['fleets_id'] > 0 && $data['fleets_id'] != $fid)
				{
					echo "<tr><td colspan=3>Flotte: ".stripslashes($data['fname'])."</td></tr>";
					$fid = $data['fleets_id'];
				}
				if ($fid != 0 && $data['fleets_id'] == 0)
				{
					echo "<tr><td colspan=3>Einzelschiffe</td></tr>";
					$fid = 0;
				}
				echo "<tr><td><img src=".$gfx."/ships/".$data['rumps_id'].".gif></td><td>".stripslashes($data['name'])."</td><td>".stripslashes($data['user'])."</td></tr>";
			}
		}
		echo "</table>";
	}
}
if ($v == "scansystem")
{
	pageheader("/ <a href=?p=colony>Kolonien</a> / <a href=?p=colony&s=sc&id=".$_GET['id'].">".ftit($col->name)."</a> / <a href=?p=colony&s=tel&id=".$_GET['id'].">Subraumteleskop</a> /  <b>System scannen</b>");
	$result = $col->telescansystem($_GET['sys']);
	if ($result) return meldung($result);
	else
	{
		$sys = $col->getsystembyid($_GET['sys']);
		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1>
		<tr><th colspan=".($sys['sr']+1).">".stripslashes($sys['name'])."-System</th></tr>
		<tr><td width=30 height=30></td>";
		for($i=1;$i<=$sys['sr'];$i++) echo "<th>".$i."</th>";
		echo "</tr><tr><th width=30 height=30>1</th>";
		$y = 1;
		while($data=mysql_fetch_assoc($col->result))
		{
			if ($data['sy'] != $y)
			{
				echo "</tr><tr><th>".$data['sy']."</th>";
				$y = $data['sy'];
			}
			if ($data['cid'])
			{
				if ($data['type'] == 7) $ss = "X";
				elseif ($data['type'] == 8) $ss = "&nbsp;";
				else $ss = $data['cid'];
			}
			else $ss = "&nbsp;";
			echo "<td class=lssnormal align=center width=30 height=30 background=".$gfx."/map/".$data['type'].".gif>".$ss."</td>";
		}
		echo "</tr></table>";
	}
}
if ($v == "repairship")
{
	pageheader("/ <a href=?p=colony>Kolonien</a> / <a href=?p=colony&s=sc&id=".$_GET['id'].">".ftit($col->name)."</a> / <b>Schiffsreparatur</b>");
	if ($_GET['a'] == "rep" && check_int($_GET["t"]) && $_SESSION['preps'] == $_GET['ps']) $result = $col->repairship($_GET['t']);
	if ($result) meldung($result);
	$result = $col->getdamagedships();
	if (mysql_num_rows($result) == 0) meldung("Es befinden sich keine beschädigten Schiffe im Orbit");
	else
	{
		echo "<table class=Tcal>
		<th></th><th>Name</th><th>Siedler</th><th>Status</th><th>Kosten</th><th></th>";
		while($data=mysql_fetch_assoc($result))
		{
			echo "<tr><td width=150><img src=".$gfx."/ships/".vdam($data).$data['rumps_id'].".gif title=\"".ftit($data['rname'])."\"></td>
				<td>".stripslashes($data['name'])." (".$data['id'].")</td><td>".stripslashes($data['user'])." (".$data['user_id'].") <a href=?p=comm&s=nn&recipient=".$data['user_id']." ".getonm('pm'.$data['id'],"buttons/msg")."><img src=".$gfx."/buttons/msg1.gif border=0 name=pm".$data['id']." title=\"PM an ".ftit($data['user'])." schreiben\"></a></td>
				<td>".$data['huelle']."/".$data['max_huelle']." (".round((100/$data['max_huelle'])*$data['huelle'])." %)</td><td><table cellpadding=2 cellspacing=2><tr>";
			$arr = $col->getShipRepairCost($data);
			foreach ($arr as $key => $value)
			{
				if ($i%8==0) echo "</tr><tr>";
				if ($value['goods_id'] == 0)
				{
					echo "<td><img src=".$gfx."/icons/energy.gif title=\"Energie\"> ".$value['gcount']."/".(!$value['vcount'] ? "<font color=#FF0000>0</font>" : ($value['vcount'] < $value['gcount'] ? "<font color=#FF0000>".$value['vcount']."</font>" : $value['vcount']))."</td>";
					$i++;
					continue;
				}
			 	echo "<td><img src=".$gfx."/goods/".$value['goods_id'].".gif title=\"".ftit($value['name'])."\"> ".$value['gcount']."/".(!$value['vcount'] ? "<font color=#FF0000>0</font>" : ($value['vcount'] < $value['gcount'] ? "<font color=#FF0000>".$value['vcount']."</font>" : $value['vcount']))."</td>";
				$i++;
			}
			echo "</tr></table></td><td><a href=?ps=".$_SESSION['pagesess']."&p=colony&s=resp&id=".$_GET['id']."&a=rep&t=".$data['id']." ".getonm('rep'.$data['id'],'buttons/rep')."><img src=".$gfx."/buttons/rep1.gif name=rep".$data['id']." border=0 title=\"Schiff reparieren\"></a></td></tr>"; 
		}
		echo "</table>";
	}
}
if ($v == "wartung")
{
	pageheader("/ <a href=?p=colony>Kolonien</a> / <a href=?p=colony&s=sc&id=".$_GET['id'].">".ftit($col->name)."</a> / <b>Schiffwartung</b>");
	if ($_GET['a'] == "war" && check_int($_GET['t']) && $_SESSION['preps'] == $_GET['ps']) $result = $col->maintainship($_GET['t']);
	if ($result) meldung($result);
	$result = $col->getmaintainanceships();
	if (@mysql_num_rows($result) == 0) meldung("Es befinden sich keine Schiffe im Orbit");
	else
	{
		echo "<table class=Tcal>
		<th></th><th>Name</th><th>Siedler</th><th>Wartung</th><th>Kosten</th><th></th>";
		while($data=mysql_fetch_assoc($result))
		{
			$j++;
			if ($j == 2)
			{
				$trc = " style=\"background-color: #171616\"";
				$j = 0;
			}
			else $trc = "";
			$i = 0;
			echo "<tr><td".$trc.">".($data['maintain'] > 0 ? "" : "<a href=?ps=".$_SESSION['pagesess']."&p=colony&s=war&id=".$_GET['id']."&a=war&t=".$data['id']." ".getonm('rep'.$data['id'],'buttons/rep')."><img src=".$gfx."/buttons/rep1.gif name=rep".$data['id']." border=0 title=\"Schiff warten\"></a>")."</td>
				<td".$trc."><img src=".$gfx."/ships/".vdam($data).$data['rumps_id'].".gif title=\"".ftit($data['rname'])."\"></td>
				<td".$trc.">".stripslashes($data['name'])." (".$data['id'].")</td><td>".stripslashes($data['user'])." (".$data['user_id'].") <a href=?p=comm&s=nn&recipient=".$data['user_id']." ".getonm('pm'.$data['id'],"buttons/msg")."><img src=".$gfx."/buttons/msg1.gif border=0 name=pm".$data['id']." title=\"PM an ".ftit($data['user'])." schreiben\"></a></td>
				<td".$trc.">Fällig: ".($data['lastmaintainance'] + $data['maintaintime'] < time() ? "<font color=#FF0000>".date("d.m. H:i",($data['lastmaintainance']+$data['maintaintime']))."</font>" : date("d.m. H:i",($data['lastmaintainance']+$data['maintaintime'])))."<br>Letzte: ".date("d.m. H:i",$data['lastmaintainance'])."</td><td><table cellpadding=2 cellspacing=2><tr>";
			$arr = $col->getShipMaintainanceCost($data);
			echo "<td".$trc."><img src=".$gfx."/buttons/e_trans2.gif title=\"Energie\"> ".round($data['eps_cost']/4)."/".($col->eps < round($data['eps_cost']/4) ? "<font color=#FF0000>".$col->eps."</font>" : $col->eps)."</td>";
			$i++;
			while($value = mysql_fetch_assoc($arr))
			{
				if (!$value['gcount']) continue;
				if ($i%5==0) echo "</tr><tr>";
			 	echo "<td><img src=".$gfx."/goods/".$value['goods_id'].".gif title=\"".ftit($value['name'])."\"> ".$value['gcount']."/".(!$value['vcount'] ? "<font color=#FF0000>0</font>" : ($value['vcount'] < $value['gcount'] ? "<font color=#FF0000>".$value['vcount']."</font>" : $value['vcount']))."</td>";
				$i++;
			}
			echo "</tr></table></td></tr>"; 
		}
		echo "</table>";
	}
}
if ($v == "demontship")
{
	pageheader("/ <a href=?p=colony>Kolonien</a> / <a href=?p=colony&s=sc&id=".$_GET['id'].">".ftit($col->name)."</a> / <b>Schiff demontieren</b>");
	if ($_GET['a'] == "dmt" && check_int($_GET['t']) && $_SESSION['preps'] == $_GET['ps']) $result = $col->demontship($_GET['t']);
	if ($result) meldung($result);
	$result = $col->getdemontableships();
	if (mysql_num_rows($result) == 0) meldung("Es befinden sich keine Schiffe im Orbit");
	else
	{
		echo "<table class=Tcal>
		<th></th><th>Schiff</th><th>Zustand</th><th></th>";
		while($data=mysql_fetch_assoc($result))
		{
			$j++;
			if ($j == 2)
			{
				$trc = " style=\"background-color: #171616\"";
				$j = 0;
			}
			else $trc = "";
			$i = 0;
			echo "<tr><td".$trc."><a href=?ps=".$_SESSION['pagesess']."&p=colony&s=dmt&id=".$_GET['id']."&a=dmt&t=".$data['id']." ".getonm('dem'.$data['id'],'buttons/demship')."><img src=".$gfx."/buttons/demship1.gif name=dem".$data['id']." border=0 title=\"Schiff demontieren\"></a></td>
				<td".$trc."><img src=".$gfx."/ships/".vdam($data).$data['rumps_id'].".gif title=\"".ftit($data['rname'])."\"></td>
				<td".$trc.">".stripslashes($data['name'])." (".$data['id'].")</td><td".$trc.">".renderhuellstatusbar($data['huelle'],$data['max_huelle'])." ".$data['huelle']."/".$data['max_huelle']."</td></tr>"; 
		}
		echo "</table>";
	}
}
if ($v == "buildmenu")
{
	if ($col->is_moon == 1)
	{
		if ($_GET['fid'] <= 14) $pha = "Orbit";
		if ($_GET['fid'] > 14 && $_GET["field_id"] <= 49) $pha = "Oberfläche";
		if ($_GET['fid'] > 49) $pha = "Untergrund";
	}
	else
	{
		if ($_GET['fid'] <= 20) $pha = "Orbit";
		if ($_GET['fid'] > 20 && $_GET["field_id"] <= 80) $pha = "Oberfläche";
		if ($_GET['fid'] > 80) $pha = "Untergrund";
	}
	pageheader("/ <a href=?p=colony>Kolonien</a> / <a href=?p=colony&s=sc&id=".$_GET['id'].">".ftit($col->name)."</a> / <b>Feld ".$_GET['fid']." (".$pha.")</b>");
	echo "<script language=\"Javascript\">
	function showConfirm()
	{
		document.getElementById(\"dmc\").innerHTML = \"Soll das Gebäude wirklich demontiert werden? <a href=?p=colony&s=sc&id=".$_GET['id']."&a=dmb&fid=".$_GET['fid']."><font color=#FF0000>Ja</font></a>\";
	}
	function getbinfo(fid,bid)
	{
		elt = 'binfo';
		get_window(elt,400);
		sendRequest('backend/binfo.php?PHPSESSID=".session_id()."&id=".$_GET['id']."&fid=' + fid + '&bid=' + bid + '&ccid=' + '".$col->colonies_classes_id."');
	}
	function gettinfo(zf)
	{
		elt = 'terrainf';
		get_window(elt,200)
		sendRequest('backend/terrainfo.php?PHPSESSID=".session_id()."&id=".$_GET['id']."&fid=".$_GET['fid']."&vfeld=".$col->fdd['type']."&zfeld=' + zf);
	}
	
	function get_window(elt,wwidth)
	{
		return overlib('<div id='+elt+'></div>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER, STICKY, FIXX, 300, FIXY, 150, WIDTH, wwidth);
	}
	
	function getubinfo(fid,bid)
	{
		elt = 'upgri';
		get_window(elt,400);
		sendRequest('backend/binfo.php?PHPSESSID=".session_id()."&u=1&id=".$_GET['id']."&fid=' + fid + '&bid=' + bid);
	}
	</script>
	<table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<td width=55% valign=top>Feldtyp: ".getnamebyfield($col->fdd['type'])."<br>";
		
		if ($col->fdd['buildings_id'] == 0) {
			echo "<img src=".$gfx."/".(!$col->fdd['terraforming_id'] ? "fields/".$col->fdd['type'] : "terraforming/".$col->fdd['terraforming_id']).".gif><br>";
		} else {
			echo "<div class=none style=\"background-image:url(".$gfx."/fields/".$col->fdd['type'].".gif); background-repeat: no-repeat; background-position:center; width:30px;\"><img src=".$gfx."/buildings/".$col->fdd['buildings_id']."/".buildingpic($col->fdd['buildings_id'],$col->fdd['type']).".png></div><br>";
		}

		// Terraforming
		if (!$col->fdd['terraforming_id'] && $col->fdd['buildings_id'] == 0)
		{
			$col->loadterraformingtypes();
			if (mysql_num_rows($col->result) != 0) echo "<br><b>Terraforming</b><br>";
			while($data=mysql_fetch_assoc($col->result)) echo "<a href=\"javascript:void(0);\" onClick=\"gettinfo(".$data['z_feld'].");\">".stripslashes($data['name'])."</a><br><img src=".$gfx."/fields/".$col->fdd['type'].".gif> => <img src=".$gfx."/fields/".$data['z_feld'].".gif border=0><br>";
		}
		// Läuft ein Terraforming?
		if ($col->fdd['terraforming_id'] > 0) echo "<br><table><tr><td style=\"border : 1px solid #262323;\"><b>Terraforming</b></td></tr><tr><td style=\"border : 1px solid #262323;\">Voraussichtliches Ende der Arbeiten: ".date("d.m.Y H:i",$col->fdd['terraformtime'])."</td></tr></table>";
		echo "</td><td width=45% valign=top><style>
		td.kd:hover
		{
			background: #262323;
		}
		</style>";
		// Baumenü
		if (!isColCent($col->fdd['buildings_id']) && !$col->fdd['terraforming_id'])
		{
			echo "<table class=tcal><th colspan=\"3\">".($col->fdd['buildings_id'] > 0 ? "Gebäude ersetzen" : "Mögliche Gebäude")."</th><tr>";
			// NPC-Weiche
			if ($_SESSION['uid'] < 101) $col->loadpossiblenpcbuildings();
			else $col->loadpossiblebuildings();
			$i = 0;
			while($data=mysql_fetch_assoc($col->result))
			{
				if ($data['buildings_id'] == $col->fdd['buildings_id']) continue;
				if ($i == 3)
				{
					$i = 0;
					echo "</tr><tr>";
				}
				echo "<td class=kd width=\"33%\" valign=\"middle\" height=\"60\" align=\"center\"><a href=\"javascript:void(0);\" onClick=\"getbinfo(".$_GET['fid'].",".$data['buildings_id'].");\" style=\"display: block; font-size: 10px;\">".$data['name']."<br><img src=".$gfx."/buildings/".$data['buildings_id']."/0.png width=30 heigth=30 border=0></a></td>\n";
				$i++;
			}
			if ($i == 1) echo "<td></td><td></td>";
			if ($i == 2) echo "<td></td>";
			echo "</tr></table>";
		}
		// Upgrades verfügbar?
		if ($col->fdd['buildings_id'] > 0 && $col->fdd['aktiv'] < 2)
		{
			$col->getpossibleupgrades($col->fdd['type']);
			if (mysql_num_rows($col->result) != 0)
			{
				echo "<br><table bgcolor=#262323 cellspacing=1 cellpadding=1 width=33%><th>Verfügbare Upgrades</th>";
				while($data=mysql_fetch_assoc($col->result)) echo "<tr><td class=kd width=\"100%\" valign=\"middle\" height=\"60\" align=\"center\"><a href=\"javascript:void(0);\" onClick=\"getubinfo(".$_GET['fid'].",".$data['buildings_id'].");\" style=\"display: block; height: 100%;\">".$data['name']."<br><img src=".$gfx."/buildings/".$data['buildings_id']."/".$col->fdd['type'].".gif border=0></a></td></tr>";
				echo "</table>";
			}
		}
		echo "</td>
	</tr>
	</table>";
}
if ($v == "modrep")
{
	echo "<script language=\"Javascript\">
	function select_buildplan()
	{
		plan = document.pform.bp.value;
		if (plan == 0)
		{
			document.getElementById(\"binfo\").innerHTML = '';
			return;
		}
		elt = 'binfo';
		sendRequest('backend/colony/getbuildplaninfo.php?PHPSESSID=".session_id()."&id='+plan+'&c=".$col->id."');
	}
	function select_rump()
	{
		plan = document.rform.bp.value;
		if (plan == 0)
		{
			document.getElementById(\"rinfo\").innerHTML = '';
			return;
		}
		elt = 'rinfo';
		sendRequest('backend/colony/getrumpinfo.php?PHPSESSID=".session_id()."&id='+plan+'&c=".$col->id."');
	}
	</script>";
	pageheader("/ <a href=?p=colony>Kolonien</a> / <a href=?p=colony&s=sc&id=".$_GET['id'].">".ftit($col->name)."</a> / <b>Modulherstellung</b>");
	if ($col->fdd['buildings_id'] == 40) include_once("inc/modrep2.php");
}

if ($v == "torprep")
{
	pageheader("/ <a href=?p=colony>Kolonien</a> / <a href=?p=colony&s=sc&id=".$_GET['id'].">".ftit($col->name)."</a> / <b>Torpedoherstellung</b>");
	if ($col->fdd['buildings_id'] == 80) include_once("inc/torprep.php");
}
if ($v == "trash")
{
	echo "<script language=\"Javascript\">
	function select_buildplan()
	{
		plan = document.pform.bp.value;
		if (plan == 0)
		{
			document.getElementById(\"binfo\").innerHTML = '';
			return;
		}
		elt = 'binfo';
		sendRequest('backend/colony/getbuildplaninfo.php?PHPSESSID=".session_id()."&id='+plan+'&c=".$col->id."');
	}
	function select_rump()
	{
		plan = document.rform.bp.value;
		if (plan == 0)
		{
			document.getElementById(\"rinfo\").innerHTML = '';
			return;
		}
		elt = 'rinfo';
		sendRequest('backend/colony/getrumpinfo.php?PHPSESSID=".session_id()."&id='+plan+'&c=".$col->id."');
	}
	</script>";
	pageheader("/ <a href=?p=colony>Kolonien</a> / <a href=?p=colony&s=sc&id=".$_GET['id'].">".ftit($col->name)."</a> / <b>Abfallentsorgung</b>");
	if ($col->fdd['buildings_id'] == 4) include_once("inc/trash.php");
}
?>
