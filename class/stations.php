<?php
if (!is_object($db)) exit;
include_once("class/stations.class.php");
$sta = new stations;
include_once("class/map.class.php");
$map = new map;
switch($_GET['s'])
{
	default:
		$v = "main";
	case "ma":
		$v = "main";
		break;
	case "kss":
		$v = "kss";
		$sys = $map->getsystembyid($_GET['sy']);
		$stasys = $map->getsystembyid($sta->systems_id);
		if ($sys == 0) $v = "main";

		if (($sys[cx] < $stasys[cx]-floor(2/3*$sta->sensor)) || ($sys[cx] > $stasys[cx]+floor(2/3*$sta->sensor))) $v = "main";
		if (($sys[cy] < $stasys[cy]-floor(2/3*$sta->sensor)) || ($sys[cy] > $stasys[cy]+floor(2/3*$sta->sensor))) $v = "main";

		break;
	case "okss":
		$v = "okss";
		$sys = $map->getsystembyid($_GET['sy']);
		$stasys = $map->getsystembyid($sta->systems_id);
		if ($sys == 0) $v = "main";

		if (($sys[cx] < $stasys[cx]-$sta->sensor) || ($sys[cx] > $stasys[cx]+$sta->sensor)) $v = "main";
		if (($sys[cy] < $stasys[cy]-$sta->sensor) || ($sys[cy] > $stasys[cy]+$sta->sensor)) $v = "main";

		break;
	case "skss":
		$v = "subkss";
		$sys = $map->getsystembyid($_GET['sy']);
		$stasys = $map->getsystembyid($sta->systems_id);
		if ($sta->subspace == 0) {
			$v = main; 
			break;
		}
		if ($sys == 0) $v = "main";

		if (($sys[cx] < $stasys[cx]-floor(2/3*$sta->sensor)) || ($sys[cx] > $stasys[cx]+floor(2/3*$sta->sensor))) $v = "main";
		if (($sys[cy] < $stasys[cy]-floor(2/3*$sta->sensor)) || ($sys[cy] > $stasys[cy]+floor(2/3*$sta->sensor))) $v = "main";

		break;
	case "lss":
		$v = "lss";
		break;
	case "slss":
		if ($sta->subspace == 0) {
			$v = main; 
			break;
		}
		$v = "sublss";
		break;
	case "show":
		$v = "showstation";
		break;
	case "bm":
		if (!check_int($_GET['fid'])) exit;
		$sta->loadfield($_GET['fid'],$_GET['id']);
		if ($sta->fdd == 0) exit;
		$v = "buildmenu";
		break;
	case "gat":
		if (!check_int($_GET['fid'])) exit;
		$sta->loadfield($_GET['fid'],$_GET['id']);
		if ($sta->fdd == 0) exit;
		$v = "gatherlss";
		break;
	case "ssz":
		$v = "selbstzerst";
		$sta->generateszcode();
		break;
	case "gak":
		if (!check_int($_GET['fid'])) exit;
		$sta->loadfield($_GET['fid'],$_GET['id']);
		if ($sta->fdd == 0) exit;
		$sys = $map->getsystembyid($_GET['sy']);
		$stasys = $map->getsystembyid($sta->systems_id);
		if ($sys == 0) $v = "main";
		$v = "gatherkss";
		break;
	case "torp":
		if (!check_int($_GET['fid'])) exit;
		$sta->loadfield($_GET['fid'],$_GET['id']);
		if ($sta->fdd == 0) exit;
		$v = "torprep";
		break;
}
if ($v == "main")
{
	if ($_SESSION['szcode'] && $_GET['sc']) $result = $sta->selfdestruct($_GET['id'],$_GET['sc']);


	pageheader("/ <b>Stationen</b>");
	if (is_string($result)) meldung($result);

	$list = $sta->getstationlist();
	echo "<table class=tcal>
	<th width=30></th><th>Name</th><th width=30></th><th>System</th><th>Sektor</th><th>Energie</th><th>Lager</th>";
	if (mysql_num_rows($list) == 0) echo "<tr><td colspan=8 align=center>Keine Stationen vorhanden</td></tr>";
	else
	{
		while($data=mysql_fetch_assoc($list))
		{

			echo "<tr><td><a href=?p=station&s=show&id=".$data['id']."><img src=".$gfx."/stations/".$data['stations_classes_id'].".gif border=0></a></td>
			<td><a href=?p=station&s=show&id=".$data['id'].">".stripslashes($data['name'])."</a> (".$data['id'].")</td><td><img src=".$gfx."/map/".$data['type'].".gif  title=\"".$data['sname']."-System (".$data['cx']."|".$data['cy'].")\"></td><td>".$data['sname']." (".$data['cx']."|".$data['cy'].")</td><td>".$data['sx']."|".$data['sy']." </a>)</td>
			<td>".$data['eps']."/".$data['max_eps']." (".($fe >= 0 ? "<font color=#30BF00><b>+".$fe."</b></font>" : "<font color=#C40005><b>".$fe."</b></font>").")</td>
			<td>";




			echo "<img src=".$gfx."/buttons/lager.gif onmouseover=\"return overlib('<table class=tcal><th colspan=4>Lager und Produktion</th>".$tt."</table>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER);\" onmouseout=\"nd();\" onmouseover=\"return escape('<b>Lager - Produktion/Verbrauch</b>".$tt."')\"> ".round(@(100/$data[max_storage])*$gs)."%</td>
			</tr>";

		}
		echo "<tr><td colspan=7></td><td align=center>= </td></tr>";
	}
	echo "</table><br><table cellpadding=0 cellspacing=0><tr><td valign=top width=600>";

	echo "</tr></table>
	</td></tr></table>";
}
if ($v == "showstation")
{
	if ($_GET['a'] == "db" && check_int($_GET['fid']) && $_SESSION['preps'] == $_GET['ps']) $result = $sta->deactivatecompo($_GET['fid']);
	if ($_GET['a'] == "ab" && check_int($_GET['fid']) && $_SESSION['preps'] == $_GET['ps']) $result = $sta->activatecompo($_GET['fid']);
	if ($_GET['a'] == "ltsmash" && check_int($_GET['fid']) && check_int($_GET['tt'])) $result = $sta->loadtorp($_GET['fid'],$_GET['tt']);
	if ($_GET['a'] == "ret" && check_int($_GET['fid'])) $result = $sta->returnfreighter($_GET['fid']);
	if ($_GET['a'] == "free" && check_int($_GET['fid'])) $result = $sta->freefreighter($_GET['fid']);
	if ($_GET['a'] == "dmb" && check_int($_GET['fid']) && $_SESSION['preps'] == $_GET['ps']) $result = $sta->removecomponent($_GET['fid'],$_GET['nxb']);
	if ($_GET['a'] == "bu" && check_int($_GET['bu']) && check_int($_GET['fid']) && $_SESSION['preps'] == $_GET['ps']) $result = $sta->build($_GET['bu'],$_GET['fid']);
	if ($_GET['a'] == "kb" && check_int($_GET['bid'])) $result = $sta->buildstation($_GET['bid']);
	if ($_GET['a'] == "wlm" && check_int($_GET['m'])) $result = $sta->setwkmode($_GET['m']);
	if ($_GET['a'] == "lsh" && check_int($_GET['c'])) $result = $sta->loadshields($_GET['c']);
	if ($_GET['a'] == "shm" && check_int($_GET['m'])) $result = $sta->setshstatus($_GET['m']);
	if ($_GET['a'] == "cn" && strlen(strip_tags($_GET['cn'])) > 3 && $_SESSION['preps'] == $_GET['ps']) $result = $sta->changename($_GET['cn']);
	if ($_GET['a'] == "stg" && check_int($_GET['fid'])&& check_int($_GET['sg'])&& check_int($_GET['x'])&& check_int($_GET['y']) ) $result = $sta->sendfreighter($_GET['fid'],$_GET['x'],$_GET['y'],$_GET['sg']);
	if ($_GET['a'] == "rto" && is_array($_GET['mod'])) $result = $sta->torpedoherstellung($_GET['mod']);

	pageheader("/ <a href=?p=station>Stationen</a> / <b>".stripslashes($sta->name)."</b>");
	if ($result)
	{
		$sta = new stations;
		meldung($result);
	}

function renderbar($val,$val2,$maxval,$size,$col)
{
	global $gfx;
	if ($val > $maxval) $val = $maxval;
	$pro = @round(($size/$maxval)*$val);
	$pro2 = abs(@round(($size/$maxval)*$val2));
	$bar = "<img src=".$gfx."/buttons/".$col.".gif height=6 width=".ceil($pro).">";
	if ($val2 >= 0) $bar .= "<img src=".$gfx."/buttons/hu_gre.gif height=6 width=".ceil($pro2).">";
	else $bar .= "<img src=".$gfx."/buttons/hu_red.gif height=6 width=".ceil($pro2).">";
	if ($pro+$pro2 < $size) $bar .= "<img src=".$gfx."/buttons/hu_grey.gif height=6 width=".floor(($size-($pro+$pro2))).">";
	return $bar;
}

	$sta->loadstastorage();
	$sta->loadenergydata();
	//$col->loadsysteminfo();
	//$col->loadresearchpoints();
	

	echo "<script language=\"Javascript\">
	function showsysinfo(sys)
	{
		elt = 'sinfo';
		sendRequest('backend/colsysview.php?PHPSESSID=".session_id()."&id=' + sys + '');
		return overlib('<div id=sinfo></div>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER, STICKY, FIXX, 300, FIXY, 100, WIDTH, 200);
	}
	function field_action(fid)
	{
		elt = 'fielda';
		get_window(elt);
		sendRequest('backend/stations/fieldaction.php?PHPSESSID=".session_id()."&fid='+fid+'&id=".$sta->id."');
	}
	function get_window(elt)
	{
		return overlib('<div id='+elt+'></div>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER, STICKY, DRAGGABLE, ALTCUT, EXCLUSIVE, WIDTH, 500);
	}
	function showConfirm(fid)
	{
		document.getElementById(\"dmc\").innerHTML = \"Soll das Modul wirklich demontiert werden? <a href=?ps=".$_SESSION['pagesess']."&p=station&s=show&id=".$_GET['id']."&a=dmb&fid=\"+fid+\"><font color=#FF0000>Ja</font></a>\";
		document.getElementById(\"dmc\").style.border = \"1px solid #262323\";
	}
	function getbuildprogressinfo(name,statusbar,percentage,finishdate,pica,picb)
	{
		return overlib('<table class=tcal><th colspan=3>Bauinfo</th><tr><td colspan=3>' + name + '</td></tr><tr><td colspan=3>Status: ' + statusbar + ' ' + percentage + '%</td></tr><tr><td colspan=3>Fertigstellung: ' + finishdate + '</td></tr><tr><td align=center><img src=".$gfx."/fieldss/' + pica + '.gif></td><td align=center><img src=".$gfx."/buttons/b_to1.gif></td><td align=center><img src=".$gfx."/components/' + picb + '_' + pica + '.gif></td></tr></table>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER);
	}
	</script>";

	if ($sta->stations_classes_id != 99)
	{
	$fm = 1;
	$j=1;
	$i=1;
	$ub = $sta->bev_work;
	$fb = $sta->bev_free;
	$wr = $sta->bev_max-$sta->bev_work-$sta->bev_free;
	$faction = ($_SESSION["race"].($_SESSION["subrace"] != 0 ? "_".$_SESSION["subrace"] : ""));
	while($i<=$sta->bev_max)
	{
		if ($ub > 0)
		{
			if ($ub >= 10 && $i + 10 <= $sta->bev_max) { $beva .= "<img src=".$gfx."/bev/bev_used_5_".$faction.".gif border=0>"; $ub-=10; $j+=26; $i+=10; }
			else { $beva .= "<img src=".$gfx."/bev/bev_used_1_".$faction.".gif border=0>"; $ub-=1; $j+=11; $i+=1; }
		}
		if ($fb > 0 && $ub == 0)
		{
			if ($fb >= 10 && $i + 10 <= $sta->bev_max) { $beva .= "<img src=".$gfx."/bev/bev_unused_5_".$faction.".gif border=0>"; $fb-=10; $j+=26; $i+=10; }
			else { $beva .= "<img src=".$gfx."/bev/bev_unused_1_".$faction.".gif border=0>"; $fb-=1; $j+=11; $i+=1; }
		}
		if ($wr > 0 && $ub == 0 && $fb == 0)
		{
			if ($wr >= 10) { $beva .= "<img src=".$gfx."/bev/bev_free_5_".$faction.".gif border=0>"; $wr-=10; $j+=26; $i+=10; }
			else { $beva .= "<img src=".$gfx."/bev/bev_free_1_".$faction.".gif border=0>"; $wr-=1; $j+=11; $i+=1; }
		}
		if ($j >= 600) { $beva.="<br>"; $j=0; }
	}
	if ($sta->bev_free+$sta->bev_work > $sta->bev_max)
	{
		$ob = ($sta->bev_free+$sta->bev_work) - $sta->bev_max;
		while (0 < $ob)
		{
			if ($ob >= 10) { $beva .= "<img src=".$gfx."/bev/bev_over_5_".$faction.".gif border=0>"; $ob-=10; $j+=26; }
			else { $beva .= "<img src=".$gfx."/bev/bev_over_1_".$faction.".gif border=0>"; $ob-=1; $j+=11; }
			if ($j >= 600) { $beva.="<br>"; $j=0; }
		}
	}

	$fe = $sta->egesamt;
	$se = $sta->eps;
	if ($fe < 0)
	{
		$se -= abs($fe);
		$em = "<img src=".$gfx."/em_t.gif width=".abs($fe)." height=9>";
	}
	if ($fe > 0 && $sta->eps < $sta->max_eps) $ep = "<img src=".$gfx."/ep_t.gif width=".($se+$fe > $sta->max_eps ? $sta->max_eps-$se : $fe)." height=9>";
	if ($se > 0) $ev = "<img src=".$gfx."/ev_t.gif width=".$se." height=9>";
	if ($se+abs($fe) < $sta->max_eps) $el = "<img src=".$gfx."/el_t.gif width=".($sta->max_eps-($se+abs($fe)))." height=9>";
	echo "<table bgcolor=#000000 border=0><tr><td><table bgcolor=#262323>
	<tr>
		<td>Energie: ".$sta->eps."/".$sta->max_eps." (".($fe > 0 ? "+" : "").$fe.")<br>
		".renderbar($se,($se+$fe > $sta->max_eps ? $sta->max_eps-$se : $fe),$sta->max_eps,300,"hu_yel")."</td>
	</tr>";

	$wkp = $sta->wkplus;



	echo "<tr>
		<td>Warpkernladung: ".$sta->wkload."/".$sta->wkload_max." (".($wkp > 0 ? "+" : "").$wkp.")<br>
		".renderbar($sta->wkload,($sta->wkload+$wkp > $sta->wkload_max ? $sta->wkload_max-$sta->wkload : $wkp),$sta->wkload_max,300,"hu_org")."</td>
	</tr>";
	echo "</table></td><td><table bgcolor=#262323>";
		echo "<tr><td>Schilde: ".$sta->schilde."/".$sta->max_schilde."<br>";

		if ($sta->schilde_status == 1) echo renderbar($sta->schilde,0,$sta->max_schilde,300,"sh_blu")."</td></tr>";
		else echo renderbar($sta->schilde,0,$sta->max_schilde,300,"sh_dblu")."</td></tr>";
	

		echo "<tr><td>Hülle: ".$sta->armor."/".$sta->max_armor."<br>";

		echo renderbar($sta->armor,0,$sta->max_armor,300,"hu_red")."</td></tr>";
	
	echo "</table></td></tr>";
	echo "<tr>
		<td>Besatzung: ".($sta->bev_work + $sta->bev_free)."/".$sta->bev_max." <br>
		".$beva."</td></tr></table>
		<br><table border=0><tr><td valign=top rowspan=2><table cellpadding=1 cellspacing=1 bgcolor=#262323><tr>";
	$i=0;
	$j=1;


	$fd = $db->query("SELECT a.type,a.field_id,a.component_id,a.aktiv,b.*,b.is_activateable FROM stu_stations_fielddata as a LEFT JOIN stu_station_components as b USING (component_id) WHERE a.stations_id=".$_GET['id']." AND a.field_id < 100 LIMIT 100");
	while($ret = mysql_fetch_assoc($fd)) $arr[] = $ret;
	
	function cmp (&$a, &$b) { return strnatcmp($a['field_id'],$b['field_id']); }
	@usort($arr, "cmp");
	$max = $sta->cols*$sta->rows;
	$q = -1;
	$rendering = explode(",",$sta->renderstring);
	$f = 0;
	$i=0;
	$j=1;

	echo "<style>
		td.sft {
			border: 1px solid transparent;
			background-color: transparent;
			background-image:http://www.stuniverse.de/gfx/fields/0.gif;
		}
		td.sfa {
			border: 1px solid transparent;
			background-color: transparent;
		}
		td.sfd {
			border: 1px solid #FF0000;
			background-color: transparent;
		}
		td.sfe {
			border: 1px solid transparent;
			background-color: transparent;
		}
		td.trans {
			background-color: transparent;
			background-image:http://www.stuniverse.de/gfx/fields/0.gif;
		}
	</style>";
	echo "<table  background='".$gfx."/stations/".$sta->stations_classes_id."msd.gif' border='1' cellspacing=0 cellpadding=0><tr><td class=trans>";
	echo "<table>";
	for($q=0; $q < $max; $q++)
	{

		if ($q%$sta->cols==0) { echo "</tr><tr>"; $j=1; }

		$j++;

		if ($rendering[$q]) {
			if ($arr[$f]['component_id'] > 0)echo "<td class=".(($arr[$f]['aktiv'] >= 1) || ($arr[$f]['is_activateable'] == 0) ? "sfa" : "sfd")."><a href=\"javascript:void(0);\" onClick=\"field_action(".$arr[$f]['field_id'].");\"><img src=".$gfx."/".($arr[$f]['aktiv'] <= 1 ? ("components/".$arr[$f]['component_id']."_".$arr[$f]['type']) : ("fieldss/10")).".gif border=0 ".($arr[$f]['aktiv'] > 1 ? "onmouseover=\"getbuildprogressinfo('".stripslashes($arr[$f]['name'])."','".renderstatusbar(time()-($arr[$f]['aktiv']-$arr[$f]['buildtime']),$arr[$f]['buildtime'],"gre")."','".getpercentage(time()-($arr[$f]['aktiv']-$arr[$f]['buildtime']),$arr[$f]['buildtime'])."','".date("d.m H:i",$arr[$f]['aktiv'])."','".$arr[$f]['type']."','".$arr[$f]['component_id']."');\" onmouseout=\"nd();\"" : "title=\"".$arr[$f]['name'].($arr[$f]['is_activateable'] == 1 ? " (".($arr[$f]['aktiv'] == 0 ? "deaktiviert" : "aktiviert").")" : "")." auf ".getsnamebyfield($arr[$f]['type'])."\"")."></a></td>";
			else echo "<td class=sfe><a href=\"javascript:void(0);\" onClick=\"field_action(".$arr[$f]['field_id'].");\"><img src=".$gfx."/fieldss/".$arr[$f]['type'].".gif border=0 title=\"".getsnamebyfield($arr[$f]['type'])."\"></a></td>";
			$f++;
		}
		else echo "<td class=sft><img src=".$gfx."/fieldss/0.gif border=0></td>";



	}
	echo "</td></tr></table>";
	echo "</table>";








	$fd2 = $db->query("SELECT a.type,a.field_id,a.component_id,a.aktiv,b.* FROM stu_stations_fielddata as a LEFT JOIN stu_station_components as b USING (component_id) WHERE a.stations_id=".$_GET['id']." AND a.field_id > 100 LIMIT 100");
	while($ret2 = mysql_fetch_assoc($fd2)) {
		$arr2[] = $ret2;
		$nrofships++;
	}
	
	@usort($arr2, "cmp");

	$q = -1;

	$f = 0;
	$i=0;
	$j=1;

	if ($arr2[0] != 0) {
	echo "<br><table   border='1' cellspacing=0 cellpadding=0><tr><td class=trans>";
	echo "<table>";
	$size = ceil($nrofships / $sta->cols) * $sta->cols;
	for($q=0; $q < $size; $q++)
	{

		if ($q%$sta->cols==0) { echo "</tr><tr>"; $j=1; }
		$j++;

		if ($arr2[$f] != 0) {
			if ($arr2[$f]['component_id'] > 0) echo "<td class=".($arr2[$f]['aktiv'] >= 1 ? "sfa" : "sfd")."><a href=\"javascript:void(0);\" onClick=\"field_action(".$arr2[$f]['field_id'].");\"><img src=".$gfx."/components/".$arr2[$f]['type']."_".$arr2[$f]['component_id'].".gif border=0 title='".getsnamebyfield($arr2[$f]['type'])." ".$arr2[$f]['name']."'></a></td>";
			else echo "<td class=sfe><a href=\"javascript:void(0);\" onClick=\"field_action(".$arr2[$f]['field_id'].");\"><img src=".$gfx."/fieldss/".$arr2[$f]['type'].".gif border=0 title=\"".getsnamebyfield($arr2[$f]['type'])."\"></a></td>";
			$f++;
		}
		else echo "<td class=sft><img src=".$gfx."/fieldss/0.gif border=0></td>";



	}
	echo "</td></tr></table>";
	echo "</table>";

	}

	echo "</td>";




	echo "<td valign=top width=500 height=200><table cellpadding=1 cellspacing=1 class=tcal>
	<tr><td class=m width=200><b>Informationen</b></td><td class=m><b>Funktionen</b></td></tr>
	<tr><td><img src=".$gfx."/stations/".$sta->stations_classes_id.".gif title=\"".$sta->cname."\"> ".stripslashes($sta->sys['name'])." ".$sta->planet_name."<br>
	".$sta->cname."<br>
	<img src=".$gfx."/bev/bev_free_1_".$faction.".gif title='Wohnraum'> Wohnraum: ".($sta->bev_max-$sta->bev_free-$sta->bev_work < 0 ? 0 : $sta->bev_max-$sta->bev_free-$sta->bev_work)."/".$sta->bev_max."<br>
	<img src=".$gfx."/bev/bev_used_1_".$faction.".gif title='Arbeiter'> Arbeiter/Frachtercrew: ".$sta->bev_work."<br>
	<img src=".$gfx."/bev/bev_unused_1_".$faction.".gif title='Arbeitslose'> Arbeitslose: ".$sta->bev_free."<br>
	<img src=".$gfx."/bev/bev_over_1_".$faction.".gif title='Obdachlose'> Obdachlose: ".($sta->bev_work+$sta->bev_free > $sta->bev_max ? ($sta->bev_free+$sta->bev_work)-$sta->bev_max : 0)."
	</td>
	<td valign=top><br><br>";

echo "<form action=main.php method=get><input type=hidden name=p value=station><input type=hidden name=s value=show><input type=hidden name=ps value=".$_SESSION['pagesess']."><input type=hidden name=a value=cn><input type=hidden name=id value=".$_GET['id'].">Name <input type=text size=15 name=cn value=\"".$sta->name."\" class=text> <input type=submit class=button value=ändern></form>";
	if ($sta->wkfull == 1) echo "<a href=?p=station&s=show&id=".$_GET['id']."&a=wlm&m=0><img src=".$gfx."/buttons/wkp2.gif title='Warpladungsmodus' border=0> Warpkernladung wird gefüllt</a><br>";
	else echo "<a href=?p=station&s=show&id=".$_GET['id']."&a=wlm&m=1><img src=".$gfx."/buttons/wkp1.gif title='Warpladungsmodus' border=0> Warpkernladung nur wenn nötig</a><br>";

	echo "<a href=?p=station&s=lss&id=".$_GET['id']."><img src=".$gfx."/buttons/lss2.gif title='Sensoren' border=0> Langstreckensensoren (Rng: ".$sta->sensor.")</a><br>";
	if ($sta->subspace == 1) echo "<a href=?p=station&s=slss&id=".$_GET['id']."><img src=".$gfx."/buttons/lss2.gif title='Sensoren' border=0> Subraumscan</a><br>";

	if ($sta->schilde_status == 1) echo "<br><a href=?p=station&s=show&id=".$_GET['id']."&a=shm&m=0><img src=".$gfx."/buttons/shldac1.gif title='Schilde deaktivieren' border=0> Schilde deaktivieren</a><br>";
	else echo "<br><a href=?p=station&s=show&id=".$_GET['id']."&a=shm&m=1><img src=".$gfx."/buttons/shldac2.gif title='Schilde aktivieren' border=0> Schilde aktivieren</a><br>";
	$reduce = $sta->getshddmgreduce();
	$rm = "";
	if ($reduce[p] != 0) $rm .= "<img src=".$gfx."/specials/wd_1.gif title='Schadensreduktion Phaser' border=0>".$reduce[p]." ";
	if ($reduce[d] != 0) $rm .= "<img src=".$gfx."/specials/wd_2.gif title='Schadensreduktion Disruptor' border=0>".$reduce[d]." ";
	if ($reduce[l] != 0) $rm .= "<img src=".$gfx."/specials/wd_4.gif title='Schadensreduktion Plasma' border=0>".$reduce[l]." ";
	if ($rm != "") echo $rm;
	echo "<form action=main.php><input type=hidden name=p value=station><img src=".$gfx."/buttons/shldp2.gif title='Schilde laden' border=0>   <input type=hidden name=s value=show><input type=hidden name=a value=lsh><input type=hidden name=id value=".$_GET['id']."><input type=text size=4 name=c class=text> <input type=submit value='Schilde laden' class=button></form>";

	echo "<br><br><a href=?p=station&s=ssz&id=".$_GET['id']."><img src=".$gfx."/buttons/selfdes2.gif title='' border=0> <font color=red>Selbstzerstörung</font></a>";
	echo "</td>
	</tr>

	</table></td></tr></table>";











	echo "<table bgcolor=\"#262323\" width=100% cellpadding=1 cellspacing=1>";
	echo "<tr valign=top><td width=* valign=top>";


		unset($result,$ret,$data,$arr);

		echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1>
		<tr><th><img src=".$gfx."/buttons/kss2.gif name=kss title='Nahbereichssensoren' border=0></th><th width=200>Name</th><th width=200>Siedler</th><th width=40>ID</th><th align=center width=50>Zustand</th><th align=center width=18>A</th><th align=center width=18>E</th><th align=center width=18>B</th></tr>";

		if ($col != 0)
		{
			$blockade = $db->query("SELECT * FROM stu_colonies_actions WHERE colonies_id =".$col['id']." AND (var='fdef' OR var='fblock' OR var='fattack')",4);
			$img = "<img src=".$gfx."/planets/".$ship->map['cid'].($col['schilde_status'] == 1 ? "s" : "").".gif border=0 title=\"".ftit($col['cname'])."\">";
			if ($col['user_id'] != 1) $col['user_id'] == $_SESSION['uid'] ? $img = "<a href=?p=colony&s=sc&id=".$col['id']."&shd=".$_GET['id'].">".$img."</a>" : $img = $img;
			echo "<tr><td>".$img."</td><td>".($col['user_id'] == 1 ? stripslashes($sys['name'])." ".$col['planet_name'] : stripslashes($col['name']))."</td><td>".stripslashes($col['user'])." (".$col['user_id'].")</td><td>".$col['id']."</td><td align=center>-</td>";
			echo "<td><a href=?p=ship&s=cac&id=".$_GET['id']."&t=".$col['id']." ".getonm("cosc","buttons/lupe")."><img src=".$gfx."/buttons/lupe1.gif name=cosc title='Kolonie-Aktionen' border=0></a></td>";
			echo "<td align=center><a href=\"javascript:void(0);\" onClick=\"col_etransfer(".$_GET['id'].",".$col['id'].");\" ".getonm("coet","buttons/e_trans")."><img src=".$gfx."/buttons/e_trans1.gif name=coet title='Energietransfer' border=0></a></td>";
			echo "<td align=center><a href=?p=ship&s=bec&id=".$_GET['id']."&m=to&t=".$col['id']." ".getonm("cobt","buttons/b_down")."><img src=".$gfx."/buttons/b_down1.gif name=cobt title=\"Zur Kolonie beamen\" border=0></a> <a href=?p=ship&s=bec&id=".$_GET['id']."&m=fr&t=".$col['id']." ".getonm("cobf","buttons/b_up")."><img src=".$gfx."/buttons/b_up1.gif name=cobf title='Von der Kolonie beamen' border=0></a></td>";
			echo "<td align=center><a href=\"javascript:void(0);\" onClick=\"open_pm_window(".$col['user_id'].",0,0);\" ".getonm("comsg","buttons/msg")."><img src=".$gfx."/buttons/msg1.gif name=comsg border=0 title=\"Eine PM an ".ftit($col['user'])." schicken\"></a></td></tr>";
		}
		$result = $sta->nbs();
		while($ret=mysql_fetch_assoc($result)) $arr[] = $ret;
		function cmp2(&$a, &$b)
		{
			global $blockade;
			if ($a['fleets_id'] == $b['fleets_id']) return 0;
			if ($a['fleets_id'] == $blockade['value']) return -1;
			if ($b['fleets_id'] == $blockade['value']) return 1;
			return ($a['fleets_id'] > $b['fleets_id']) ? -1 : 1;
		}
		@usort($arr, "cmp2");

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
					if ($data['fleets_id'] == $blockade['value']) {
						if ($blockade['var'] == "fdef") $defadd = "<img src=".$gfx."/buttons/guard2.gif title=Verteidigung border=0> ";
						elseif ($blockade['var'] == "fblock")  $defadd = "<img src=".$gfx."/buttons/x1.gif title=Blockade border=0> ";
						elseif ($blockade['var'] == "fattack")  $defadd = "<img src=".$gfx."/buttons/leavecol2.gif title=Angriff border=0> ";
						else $defadd = "";
					}
					else $defadd = "";
					echo "<td colspan=13>".($data['user_id'] == $_SESSION['uid'] ? "<a href=?p=ship&s=ss&id=".$data['fship_id'].">".$defadd."".stripslashes($data['fname'])."</a>" : stripslashes($data['fname']))."</td>";
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
					<td align=center>".($data['warp'] == 1 ? "<a href=?p=ship&s=ss&id=".$_GET['id']."&a=int&t=".$data['id']." ".getonm("inc".$i,"buttons/inc")."><img src=".$gfx."/buttons/inc1.gif title=\"Abfangen\" border=0 name=inc".$i."></a>" : "-")."</td>
					<td align=center>".($ship->phaser > 0 || $ship->torp_type > 0 ? "<a href=?p=ship&s=ss&a=att&ps=".$_SESSION['pagesess']."&id=".$_GET['id']."&t=".$data['id']." ".getonm("ph".$i,"buttons/phaser")."><img src=".$gfx."/buttons/phaser1.gif title='Angreifen' name=ph".$i." border=0></a>" : "-")."</td>
					<td colspan=3 align=center><font color=gray>getarnt</font></td><td><a href=\"javascript:void(0);\" onClick=\"open_pm_window(".$data['user_id'].",".$_GET['id'].",".$data['id'].");\" ".getonm("pm".$i,"buttons/msg")."><img src=".$gfx."/buttons/msg1.gif name=pm".$i." title='Eine PM an die ".ftit($data['name'])." schicken' border=0></a></td></tr>";
					continue;
				}
				if ($data['traktormode'] == 1) $tr = ">";
				elseif ($data['traktormode'] == 2) $tr = "<";
				else $tr = "";
				echo "<td>".$data['id']." ".$tr."</td><td align=center>".$data['huelle']."/".$data['max_huelle'].($data['schilde_status'] == 1 ? " (<font color=cyan>".$data['schilde']."</font>)" : "")."</td>";
				
				echo "<td align=center>A</td>";
				echo "<td align=center>E</td>";
				echo "<td align=center>B</td>";

			}
		}
		if ($cl > 0) echo "<tr><td colspan=12>Es befinden sich nicht scanbare Objekte in diesem Sektor</td></tr>";
		echo "</table>";






























	echo "</td>";

	echo "<td valign=top><table bgcolor=\"#262323\" width=400 cellpadding=1 cellspacing=1>";
	$j = 0;
	while($data=mysql_fetch_assoc($sta->result))
	{
		// if ($data['goods_id'] == 1) $sta->goods[1] -= ceil(($sta->bev_free+$sta->bev_work)/5);
		if ($data['goods_id'] == 5) $sta->goods[5] -= $sta->nrofloads*2;
		if ($data['goods_id'] == 6) $sta->goods[6] -= $sta->nrofloads*2;
		if ($data['count'] == 0 && $sta->goods[$data['goods_id']] == 0) continue;
		$sg = $data['count'];
		if ($sta->goods[$data['goods_id']] < 0) $sg -= abs($sta->goods[$data['goods_id']]);
		if ($sg > 0)
		{
			// Berechnung der Lagerstandsanzeige
			for($i=0;$i<floor($sg/1000);$i++) $lb .= "<img src=".$gfx."/l_t.gif>";
			$sg -= floor($sg/1000)*1000;
			for($i=0;$i<floor($sg/100);$i++) $lb .= "<img src=".$gfx."/l_h.gif>";
			$sg -= floor($sg/100)*100;
			if ($data['count'] >= $sg) $lb .= "<img src=".$gfx."/l_s.gif width=".$sg." height=12>";
		}
		if ($sta->goods[$data['goods_id']] > 0)
		{
			// Berechnung der Anzeige der dazukommenden Waren
			$sg = $sta->goods[$data['goods_id']];
			for($i=0;$i<floor($sg/100);$i++) $lb .= "<img src=".$gfx."/l_hg.gif>";
			$sg -= floor($sg/100)*100;
			if ($sg != 0) $lb .= "<img src=".$gfx."/l_sg.gif width=".$sg." height=12>";
		}
		if ($sta->goods[$data['goods_id']] < 0)
		{
			// Berechnung der Anzeige der verbrauchten Waren
			$sg = abs($sta->goods[$data['goods_id']]);
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
		$laz .= "<tr><td".$trc."><img src=".$gfx."/goods/".$data['goods_id'].".gif title='".ftit($data['name'])."'> ".(!$data['count'] ? 0 : $data['count'])."</td><td".$trc.">".$lb."</td><td".$trc.">".(!$sta->goods[$data['goods_id']] ? 0 : ($sta->goods[$data['goods_id']] > 0 ? "<font color=#00ff00>+".$sta->goods[$data['goods_id']]."</font>" : "<font color=#FF0000>".$sta->goods[$data['goods_id']]."</font>"))."</td></tr>";
		$trc = "";
		unset($lb);
		$gc += $sta->goods[$data['goods_id']];
	}
	echo "<tr><th align=left width=60><img src=".$gfx."/buttons/lager.gif title='Lager' border=0> ".$sta->storage."</th><th align=left>".round((@(100/$sta->max_storage)*$sta->storage),2)."% von ".$sta->max_storage."</th><td width=120>".($gc < 0 ? "<font color=#ff0000>".$gc."</font>" : "<font color=#00ff00>+".$gc."</font>")." (".($gc < 0 ? "<font color=#ff0000>-</font>" : "<font color=#00ff00>+</font>").round((@(100/$sta->max_storage)*abs($gc)),2)."%)</td></tr>
	".$laz."</table></td></table>";




	}
	else
	{



	$fm = 1;
	$j=1;
	$i=1;
	$ub = $sta->bev_work;
	$fb = $sta->bev_free;
	$wr = $sta->bev_max-$sta->bev_work-$sta->bev_free;

	$bob = $sta->getbuildoptions();


	$progress = $db->query("SELECT a.*,b.name FROM stu_stations_buildprogress as a LEFT JOIN stu_stations_classes as b ON a.build_id = b.stations_classes_id WHERE a.stations_id=".$sta->id." LIMIT 1",4);

	if ($progress != 0) {

	echo "<table border=0><tr>";

	echo "<td class=tba><img src=".$gfx."/stations/".$progress['build_id'].".gif title='".$progress[name]."'> ".$progress[name]." wird gebaut, Fertigstellung: ".date("d.m H:i",$progress[buildtime])."</td>";

	echo "</tr></table>";

	}
	else
	{

	$se = $sta->eps;
	if ($fe < 0)
	{
		$se -= abs($fe);
		$em = "<img src=".$gfx."/em_t.gif width=".abs($fe)." height=9>";
	}
	if ($fe > 0 && $sta->eps < $sta->max_eps) $ep = "<img src=".$gfx."/ep_t.gif width=".($se+$fe > $sta->max_eps ? $sta->max_eps-$se : $fe)." height=9>";
	if ($se > 0) $ev = "<img src=".$gfx."/ev_t.gif width=".$se." height=9>";
	if ($se+abs($fe) < $sta->max_eps) $el = "<img src=".$gfx."/el_t.gif width=".($sta->max_eps-($se+abs($fe)))." height=9>";
	echo "<table bgcolor=#000000 border=0><tr><td><table bgcolor=#262323>
	<tr>
		<td>Energie: ".$sta->eps."/".$sta->max_eps." (".($fe > 0 ? "+" : "").$fe.")<br>
		".renderbar($se,($se+$fe > $sta->max_eps ? $sta->max_eps-$se : $fe),$sta->max_eps,300,"hu_yel")."</td>
	</tr>";
	echo "</table><br><table border=0><tr><td valign=top rowspan=2><table cellpadding=1 cellspacing=1 bgcolor=#262323><tr>";
	$i=0;
	$j=1;

	echo "<style>
		td.tba {
			border: 0;
			background-color: #000000;
		}
		td.tbb {
			border: 0;
			background-color: #222222;
		}
	</style>";

	echo "<table border=0><tr>";

	for ($i = 1; $i <= $bob[count]; $i++)
	{
		echo "<td width=150 class=tba><center><img src=".$gfx."/stations/".$bob[$i]['id'].".gif title='".$bob[$i][name]."'></center></td>";
	}
	echo "</tr><tr>";
	for ($i = 1; $i <= $bob[count]; $i++)
	{
		if ($i%2 == 1) echo "<td width=150 class=tba><center>".$bob[$i][name]."</center></td>";
		else echo "<td width=150 class=tbb><center>".$bob[$i][name]."</center></td>";
	}
	echo "</tr><tr>";
	for ($i = 1; $i <= $bob[count]; $i++)
	{
		if ($i%2 == 1) echo "<td width=150 class=tba valign=top >".$bob[$i][goods]."<br><br></td>";
		else echo "<td width=150 class=tbb valign=top >".$bob[$i][goods]."<br><br></td>";
	}
	echo "</tr><tr>";
	for ($i = 1; $i <= $bob[count]; $i++)
	{
		if ($i%2 == 1) echo "<td width=150 class=tba><center><a href='main.php?p=station&s=show&id=".$sta->id."&a=kb&bid=".$bob[$i][id]."'><img src=".$gfx."/ships/5.gif title='Dies ist ein Bild' border=0> Bauen</a></center></td>";
		else echo "<td width=150 class=tbb><center><a href='main.php?p=station&s=show&id=".$sta->id."&a=kb&bid=".$bob[$i][id]."'><img src=".$gfx."/ships/5.gif title='Dies ist ein Bild' border=0> Bauen</a></center></td>";
	}
	echo "</tr></table>";
	}

	echo "<br><br>
	<table bgcolor=\"#262323\" width=600 cellpadding=1 cellspacing=1>";
	$j = 0;
	while($data=mysql_fetch_assoc($sta->result))
	{
		if ($data['count'] == 0 && $sta->goods[$data['goods_id']] == 0) continue;
		$sg = $data['count'];
		if ($sta->goods[$data['goods_id']] < 0) $sg -= abs($sta->goods[$data['goods_id']]);
		if ($sg > 0)
		{
			// Berechnung der Lagerstandsanzeige
			for($i=0;$i<floor($sg/1000);$i++) $lb .= "<img src=".$gfx."/l_t.gif>";
			$sg -= floor($sg/1000)*1000;
			for($i=0;$i<floor($sg/100);$i++) $lb .= "<img src=".$gfx."/l_h.gif>";
			$sg -= floor($sg/100)*100;
			if ($data['count'] >= $sg) $lb .= "<img src=".$gfx."/l_s.gif width=".$sg." height=12>";
		}

		$j++;
		if ($j == 2)
		{
			$trc = " style=\"background-color: #171616\"";
			$j = 0;
		}
		$laz .= "<tr><td".$trc."><img src=".$gfx."/goods/".$data['goods_id'].".gif title='".ftit($data['name'])."'> ".(!$data['count'] ? 0 : $data['count'])."</td><td".$trc.">".$lb."</td><td".$trc.">".(!$sta->goods[$data['goods_id']] ? 0 : ($sta->goods[$data['goods_id']] > 0 ? "<font color=#00ff00>+".$sta->goods[$data['goods_id']]."</font>" : "<font color=#FF0000>".$sta->goods[$data['goods_id']]."</font>"))."</td></tr>";
		$trc = "";
		unset($lb);
		$gc += $sta->goods[$data['goods_id']];
	}
	echo "<tr><th align=left width=60><img src=".$gfx."/buttons/lager.gif title='Lager' border=0> ".$sta->storage."</th><th align=left>".round((@(100/$sta->max_storage)*$sta->storage),2)."% von ".$sta->max_storage."</th><td width=120>".($gc < 0 ? "<font color=#ff0000>".$gc."</font>" : "<font color=#00ff00>+".$gc."</font>")." (".($gc < 0 ? "<font color=#ff0000>-</font>" : "<font color=#00ff00>+</font>").round((@(100/$sta->max_storage)*abs($gc)),2)."%)</td></tr>
	".$laz."</table>";





	 }







}

if ($v == "lss")
{
	pageheader("/ <a href=?p=station>Stationen</a> / <a href=?p=station&s=show&id=".$_GET['id'].">".ftit($sta->name)."</a> / <b>Langstreckensensoren</b>");


	$range = $sta->sensor;

	$sys = $map->getsystembyid($sta->systems_id);

	$result = $map->getlss($sys[cx],$sys[cy],$range);
	$result2 = $map->getsystemsinrange($sys[cx],$sys[cy],floor((2/3) *$range));
	$result3 = $map->getosystemsinrange($sys[cx],$sys[cy],$range);

	echo "<table bgcolor=#000000 border=0><tr><td>";

	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr><td class=l width=30 align=center></td>";
	
	for ($i=($sys[cx]-$range);$i<=($sys[cx]+$range);$i++) if ($i > 0 && $i <= (160)) echo "<th align=center width=30>".$i."</th>";
	while($data=mysql_fetch_assoc($result))
	{
		if ($data['cy'] != $yd) { echo "</tr><tr><th align=center>".$data['cy']."</th>"; $yd = $data['cy']; }
		if (!$yd) $yd = $data['cy'];
		if ($data['cx'] == $sys[cx] && $data['cy'] == $sys[cy]) $border = " bordercolor=#929191 style='border: 1px solid #929191'";
		$sc = ($data['sc'] == 0 || $ship->map['type'] == 7 || $ship->map['type'] == 9 ? "&nbsp;" : $data['sc']);
		if ($data['type'] == 7 && $sc > 0) $sc = "X";
		if (!$data[special]) echo "<td class=l width=30 background=".$gfx."/map/".$data['type'].".gif align=center".$border."".($sc > 0 ? " title=\"Signaturen: ".$sc."\"": "").">".$sc."</td>";
		else echo "<td class=l width=30 background=".$gfx."/map/".$data['type']."x.gif align=center".$border."".($sc > 0 ? " title=\"Signaturen: ".$sc."\"": "").">".$sc."</td>";
		$border = "";
	}
	echo "</tr></table>";
	
	echo "</td><td valign=top>";



	echo "<table bgcolor=#000000 cellspacing=1 cellpadding=1 width=300><tr><th align=center height=30>Systeme in Reichweite</th></tr>";



	echo "<tr><th align=center height=30>Kompletter Scan möglich:</th></tr>";
	while($data=mysql_fetch_assoc($result2))
	{
		echo "<tr><td><a href=?p=station&s=kss&id=".$_GET['id']."&sy=".$data['systems_id']."><img src=".$gfx."/map/".$data['type'].".gif border=0> ".$data[name]."-System (".$data[cx]."|".$data[cy].")</td></a></tr>";

	}


	echo "<tr><th align=center height=30>Optischer Scan:</th></tr>";
	while($data=mysql_fetch_assoc($result3))
	{
		echo "<tr><td><a href=?p=station&s=okss&id=".$_GET['id']."&sy=".$data['systems_id']."><img src=".$gfx."/map/".$data['type'].".gif border=0> ".$data[name]."-System (".$data[cx]."|".$data[cy].")</td></a></tr>";

	}

	echo "</table></td></tr></table>";



}

if ($v == "sublss")
{
	pageheader("/ <a href=?p=station>Stationen</a> / <a href=?p=station&s=show&id=".$_GET['id'].">".ftit($sta->name)."</a> / <b>Langstrecken-Subraumscan</b>");


	$range = $sta->sensor;

	$sys = $map->getsystembyid($sta->systems_id);

	$result = $sta->subspacescanwarp($sys[cx],$sys[cy],$range);
	$result3 = $map->getsystemsinrange($sys[cx],$sys[cy],floor((2/3) *$range));

	echo "<table bgcolor=#000000 border=0><tr><td>";

	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr><td class=l width=30 align=center></td>";

	$t = time();
	
	for ($i=($sys[cx]-$range);$i<=($sys[cx]+$range);$i++) if ($i > 0 && $i <= (160)) echo "<th align=center width=30>".$i."</th>";
	while($data=mysql_fetch_assoc($result))
	{
		if ($data['cy'] != $yd) { echo "</tr><tr><th align=center>".$data['cy']."</th>"; $yd = $data['cy']; }
		if (!$yd) $yd = $data['cy'];
		if ($data['cx'] == $sys[cx] && $data['cy'] == $sys[cy]) $border = " bordercolor=#929191 style='border: 1px solid #929191'";

		if ($data[ftime] == 0) $icon = "<img src=".$gfx."/fieldss/0.gif border=0 width=30 height=30>";
		else {
			$diff = $t - $data[ftime];
			if ($diff > 0) $icon = "<img src=".$gfx."/map/subspace4.png border=0 width=30 height=30>";
			if ($diff > 5400) $icon = "<img src=".$gfx."/map/subspace3.png border=0 width=30 height=30>";
			if ($diff > 10800) $icon = "<img src=".$gfx."/map/subspace2.png border=0 width=30 height=30>";
			if ($diff > 16200) $icon = "<img src=".$gfx."/map/subspace1.png border=0 width=30 height=30>";
			if ($diff > 21600) $icon = "<img src=".$gfx."/fieldss/0.gif border=0 width=30 height=30>";
		}
		echo "<td class=l width=30 background=".$gfx."/map/".$data['type'].".gif align=center".$border.">".$icon."</td>";
		$border = "";
	}
	echo "</tr></table>";
	
	echo "</td><td valign=top>";



	echo "<table bgcolor=#000000 cellspacing=1 cellpadding=1 width=300><tr><th align=center height=30>Systeme in Reichweite</th></tr>";


	//echo "<tr><th align=center height=30>System-Scan:</th></tr>";
	while($data=mysql_fetch_assoc($result3))
	{
		echo "<tr><td><a href=?p=station&s=skss&id=".$_GET['id']."&sy=".$data['systems_id']."><img src=".$gfx."/map/".$data['type'].".gif border=0> ".$data[name]."-System (".$data[cx]."|".$data[cy].")</td></a></tr>";

	}

	echo "</table></td></tr></table>";



}

if ($v == "kss")
{


	pageheader("/ <a href=?p=station>Stationen</a> / <a href=?p=station&s=show&id=".$_GET['id'].">".ftit($sta->name)."</a> / <a href=?p=station&s=lss&id=".$_GET['id'].">Langstreckensensoren</a> / <b>Scan des ".$sys[name]."-Systems</b>");


	$range = $sta->sensor;


	$result = $map->getkss(1,1,30,$sys[systems_id]);

	echo "<table bgcolor=#000000 border=0><tr><td>";

	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr><td class=l width=30 align=center></td>";
	
	for ($i=1;$i<=$sys[sr];$i++) echo "<th align=center width=30>".$i."</th>";
	while($data=mysql_fetch_assoc($result))
	{
		if ($data['cy'] != $yd) { echo "</tr><tr><th align=center>".$data['cy']."</th>"; $yd = $data['cy']; }
		if (!$yd) $yd = $data['cy'];

		$sc = ($data['sc'] == 0 || $ship->map['type'] == 7 || $ship->map['type'] == 9 ? "&nbsp;" : $data['sc']);
		if ($data['type'] == 7 && $sc > 0) $sc = "X";
		if (!$data[special]) echo "<td class=l background=".$gfx."/map/".$data['type'].".gif align=center".$border."".($sc > 0 ? " title=\"Signaturen: ".$sc."\"": "").">".$sc."</td>";
		else echo "<td class=l background=".$gfx."/map/".$data['type']."x.gif align=center".$border."".($sc > 0 ? " title=\"Signaturen: ".$sc."\"": "").">".$sc."</td>";
		$border = "";
	}
	echo "</tr></table>";



	
	echo "</td><td valign=top>";


	echo "</td></tr></table>";



}


if ($v == "okss")
{


	pageheader("/ <a href=?p=station>Stationen</a> / <a href=?p=station&s=show&id=".$_GET['id'].">".ftit($sta->name)."</a> / <a href=?p=station&s=lss&id=".$_GET['id'].">Langstreckensensoren</a> / <b>Optischer Scan des ".$sys[name]."-Systems</b>");


	$range = $sta->sensor;


	$result = $map->getokss(1,1,30,$sys[systems_id]);

	echo "<table bgcolor=#000000 border=0><tr><td>";

	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr><td class=l width=30 align=center></td>";
	
	for ($i=1;$i<=$sys[sr];$i++) echo "<th align=center width=30>".$i."</th>";
	while($data=mysql_fetch_assoc($result))
	{
		if ($data['cy'] != $yd) { echo "</tr><tr><th align=center>".$data['cy']."</th>"; $yd = $data['cy']; }
		if (!$yd) $yd = $data['cy'];

		$sc = ($data['sc'] == 0 || $ship->map['type'] == 7 || $ship->map['type'] == 9 ? "&nbsp;" : $data['sc']);
		if ($data['type'] == 7 && $sc > 0) $sc = "X";
		if (!$data[special]) echo "<td class=l background=".$gfx."/map/".$data['type'].".gif align=center".$border."".($sc > 0 ? " title=\"Signaturen: ".$sc."\"": "").">".$sc."</td>";
		else echo "<td class=l background=".$gfx."/map/".$data['type']."x.gif align=center".$border."".($sc > 0 ? " title=\"Signaturen: ".$sc."\"": "").">".$sc."</td>";
		$border = "";
	}
	echo "</tr></table>";



	
	echo "</td><td valign=top>";


	echo "</td></tr></table>";



}


if ($v == "subkss")
{


	pageheader("/ <a href=?p=station>Stationen</a> / <a href=?p=station&s=show&id=".$_GET['id'].">".ftit($sta->name)."</a> / <a href=?p=station&s=slss&id=".$_GET['id'].">Langstrecken-Subraumscan</a> / <b>Subraum-Scan des ".$sys[name]."-Systems</b>");


	$range = $sta->sensor;


	$result = $sta->subspacescansystem($sys[systems_id]);

	echo "<table bgcolor=#000000 border=0><tr><td>";

	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr><td class=l width=30 align=center></td>";
	
	$t = time();

	for ($i=1;$i<=$sys[sr];$i++) echo "<th align=center width=30>".$i."</th>";
	while($data=mysql_fetch_assoc($result))
	{
		if ($data['cy'] != $yd) { echo "</tr><tr><th align=center>".$data['cy']."</th>"; $yd = $data['cy']; }
		if (!$yd) $yd = $data['cy'];


		if ($data[ftime] == 0) $icon = "<img src=".$gfx."/fieldss/0.gif border=0 width=28 height=28>";
		else {
			$diff = $t - $data[ftime];
			if ($diff > 0) $icon = "<img src=".$gfx."/map/subspace4.png border=0 width=28 height=28>";
			if ($diff > 5400) $icon = "<img src=".$gfx."/map/subspace3.png border=0 width=28 height=28>";
			if ($diff > 10800) $icon = "<img src=".$gfx."/map/subspace2.png border=0 width=28 height=28>";
			if ($diff > 16200) $icon = "<img src=".$gfx."/map/subspace1.png border=0 width=28 height=28>";
			if ($diff > 21600) $icon = "<img src=".$gfx."/fieldss/0.gif border=0 width=28 height=28>";
		}
		echo "<td class=l width=30 background=".$gfx."/map/".$data['type'].".gif align=center".$border.">".$icon."</td>";
		$border = "";
	}
	echo "</tr></table>";



	
	echo "</td><td valign=top>";


	echo "</td></tr></table>";



}


if ($v == "buildmenu")
{
	pageheader("/ <a href=?p=station>Stationen</a> / <a href=?p=station&s=show&id=".$_GET['id'].">".ftit($sta->name)."</a> / <b>Feld ".$_GET['fid']." (".$pha.")</b>");
	echo "<script language=\"Javascript\">
	function showConfirm()
	{
		document.getElementById(\"dmc\").innerHTML = \"Soll das Modul wirklich demontiert werden? <a href=?p=stations&s=sc&id=".$_GET['id']."&a=dmb&fid=".$_GET['fid']."><font color=#FF0000>Ja</font></a>\";
	}
	function getcpinfo(fid,bid)
	{
		elt = 'cpinfo';
		get_window(elt,400);
		sendRequest('backend/cpinfo.php?PHPSESSID=".session_id()."&id=".$_GET['id']."&fid=' + fid + '&bid=' + bid);
	}

	function get_window(elt,wwidth)
	{
		return overlib('<div id='+elt+'></div>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER, STICKY, FIXX, 300, FIXY, 150, WIDTH, wwidth);
	}
	</script>
	<table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<td width=55% valign=top>Feldtyp: ".getsnamebyfield($sta->fdd['type'])."<br>



		<img src=".$gfx."/".($sta->fdd['component_id'] == 0 ? ("fieldss/".$sta->fdd['type']) : "components/".($sta->fdd['aktiv'] > 1 ? 0 : $sta->fdd['component_id']."_".$sta->fdd['type'])).".gif><br>";


		echo "</td><td width=45% valign=top><style>
		td.kd:hover
		{
			background: #262323;
		}
		</style>";
		// Baumenü

		echo "<table class=tcal><th colspan=\"3\">".($sta->fdd['component_id'] > 0 ? "Modul ersetzen" : "Mögliche Module")."</th><tr>";

		$sta->loadpossiblebuildings();
		$i = 0;
		while($data=mysql_fetch_assoc($sta->result))
		{
			if ($data['component_id'] == $sta->fdd['component_id']) continue;
			if ($i == 3)
			{
				$i = 0;
				echo "</tr><tr>";
			}
		echo "<td class=kd width=\"33%\" valign=\"middle\" height=\"60\" align=\"center\"><a href=\"javascript:void(0);\" onClick=\"getcpinfo(".$_GET['fid'].",".$data['component_id'].");\" style=\"display: block; font-size: 10px;\">".$data['name']."<br><img src=".$gfx."/components/".$data['component_id']."_".$sta->fdd['type'].".gif width=30 heigth=30 border=0></a></td>\n";
			$i++;
		}
		if ($i == 1) echo "<td></td><td></td>";
		if ($i == 2) echo "<td></td>";
		echo "</tr></table>";
		
		echo "</td>
	</tr>
	</table>";
}




if ($v == "gatherlss")
{
	pageheader("/ <a href=?p=station>Stationen</a> / <a href=?p=station&s=show&id=".$_GET['id'].">".ftit($sta->name)."</a> / <b>Ziel für Sammeloperation wählen</b>");

	$range = 9;
	if ($sta->fdd[type] > 20) $range = 6;
	$plus = $db->query("SELECT COUNT(field_id) FROM stu_stations_fielddata WHERE stations_id = ".$sta->id." AND component_id = 116",1);
	$range += $plus*3;
	$range = min($range,$sta->sensor);

	$sys = $map->getsystembyid($sta->systems_id);

	$result = $map->getlss($sys[cx],$sys[cy],$range);
	$result3 = $map->getosystemsinrange($sys[cx],$sys[cy],$range);

	echo "<table bgcolor=#000000 border=0><tr><td>";

	echo "<table bgcolor=#262323 cellspacing=0 cellpadding=0><tr><td class=l width=30 align=center></td>";
	
	for ($i=($sys[cx]-$range);$i<=($sys[cx]+$range);$i++) if ($i > 0 && $i <= (160)) echo "<th align=center width=30>".$i."</th>";
	while($data=mysql_fetch_assoc($result))
	{
		if ($data['cy'] != $yd) { echo "</tr><tr height=30><th align=center>".$data['cy']."</th>"; $yd = $data['cy']; }
		if (!$yd) $yd = $data['cy'];
		if ($data['cx'] == $sys[cx] && $data['cy'] == $sys[cy]) $border = " bordercolor=#929191 style='border: 1px solid #929191'";
		else $border = " bordercolor=#505050 style='border: 1px solid #505050'";
		$sc = "";
		if ($data['type'] == 2) $sc = "<img src=".$gfx."/components/py.gif border=0 title='Deuterium sammeln möglich'>";
		if ($data['type'] == 3) $sc = "<img src=".$gfx."/components/pyy.gif border=0 title='Deuterium sammeln möglich'>";
		if ($data['type'] == 59) $sc = "<img src=".$gfx."/components/pyy.gif border=0 title='Deuterium sammeln möglich'>";
		if ($data['type'] == 69) $sc = "<img src=".$gfx."/components/pyy.gif border=0 title='Deuterium sammeln möglich'>";
		if ($data['type'] == 11) $sc = "<img src=".$gfx."/components/pb.gif border=0 title='Erz sammeln möglich'>";
		if ($data['type'] == 12) $sc = "<img src=".$gfx."/components/pbb.gif border=0 title='Erz sammeln möglich'>";
		if ($data[systems_id] == "") $data[systems_id] = 0;
		if ($sc != "") $sc = "<a href=?p=station&s=show&id=".$_GET['id']."&a=stg&sg=".$data['systems_id']."&fid=".$_GET['fid']."&x=".$data[cx]."&y=".$data[cy].">".$sc."</a>";
		else $sc = "<img src=".$gfx."/fieldss/0.gif width=30 height=30 border=0 title=''>";
		echo "<td class=l width=30 background=".$gfx."/map/".$data['type'].".gif align=center".$border." title=''>".$sc."</td>";
		$border = "";
	}
	echo "</tr></table>";
	
	echo "</td><td valign=top>";



	echo "<table bgcolor=#000000 cellspacing=1 cellpadding=1 width=300><tr><th align=center height=30>Systeme in Reichweite</th></tr>";



	echo "<tr><th align=center height=30>In System sammeln:</th></tr>";
	while($data=mysql_fetch_assoc($result3))
	{
		echo "<tr><td><a href=?p=station&s=gak&id=".$_GET['id']."&sy=".$data['systems_id']."&fid=".$_GET['fid']."><img src=".$gfx."/map/".$data['type'].".gif border=0> ".$data[name]."-System (".$data[cx]."|".$data[cy].")</td></a></tr>";

	}

	echo "</table></td></tr></table>";



}


if ($v == "gatherkss")
{


	pageheader("/ <a href=?p=station>Stationen</a> / <a href=?p=station&s=show&id=".$_GET['id'].">".ftit($sta->name)."</a> / <a href=?p=station&s=gat&id=".$_GET['id']."&fid=".$_GET['fid'].">Ziel für Sammeloperation wählen</a> / <b>Sammeln im ".$sys[name]."-System</b>");

	$range = 9;
	if ($sta->fdd[type] > 20) $range = 6;
	$plus = $db->query("SELECT COUNT(field_id) FROM stu_stations_fielddata WHERE stations_id = ".$sta->id." AND component_id = 116",1);
	$range += $plus*3;
	$range = min($range,$sta->sensor);


	$result = $map->getokss(1,1,30,$sys[systems_id]);

	echo "<table bgcolor=#000000 border=0><tr><td>";

	echo "<table bgcolor=#262323 cellspacing=0 cellpadding=0><tr height=30><td class=l width=30 align=center></td>";
	
	for ($i=1;$i<=$sys[sr];$i++) echo "<th align=center width=30>".$i."</th>";
	while($data=mysql_fetch_assoc($result))
	{
		if ($data['cy'] != $yd) { echo "</tr><tr><th align=center>".$data['cy']."</th>"; $yd = $data['cy']; }
		if (!$yd) $yd = $data['cy'];
		if ($data['cx'] == $sys[cx] && $data['cy'] == $sys[cy]) $border = " bordercolor=#929191 style='border: 1px solid #929191'";
		else $border = " bordercolor=#505050 style='border: 1px solid #505050'";


		$sc = "";
		if ($data['type'] == 2) $sc = "<img src=".$gfx."/components/py.gif border=0 title='Deuterium sammeln möglich'>";
		if ($data['type'] == 3) $sc = "<img src=".$gfx."/components/pyy.gif border=0 title='Deuterium sammeln möglich'>";
		if ($data['type'] == 59) $sc = "<img src=".$gfx."/components/pyy.gif border=0 title='Deuterium sammeln möglich'>";
		if ($data['type'] == 69) $sc = "<img src=".$gfx."/components/pyy.gif border=0 title='Deuterium sammeln möglich'>";
		if ($data['type'] == 11) $sc = "<img src=".$gfx."/components/pb.gif border=0 title='Erz sammeln möglich'>";
		if ($data['type'] == 12) $sc = "<img src=".$gfx."/components/pbb.gif border=0 title='Erz sammeln möglich'>";

		if ($sc != "") $sc = "<a href=?p=station&s=show&id=".$_GET['id']."&a=stg&sg=".$sys[systems_id]."&fid=".$_GET['fid']."&x=".$data[cx]."&y=".$data[cy].">".$sc."</a>";
		else $sc = "<img src=".$gfx."/fieldss/0.gif width=30 height=30 border=0 title=''>";
		echo "<td class=l width=30 background=".$gfx."/map/".$data['type'].".gif align=center".$border." title=''>".$sc."</td>";
	}
	echo "</tr></table>";



	
	echo "</td><td valign=top>";


	echo "</td></tr></table>";



}


if ($v == "selbstzerst")
{
	pageheader("/ <a href=?p=station>Stationen</a> / <a href=?p=station&s=show&id=".$_GET['id'].">".ftit($sta->name)."</a> / <b>Selbstzerstörung</b>");
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=400>
	<tr><td>Bitte den Bestätigungscode in das Feld eintippen und bestätigen:<br>
	<font color=#ff0000>".$_SESSION["szcode"]."</font></td></tr>
	<form action=main.php method=get><input type=hidden name=p value=station><input type=hidden name=id value=".$_GET['id'].">
	<tr><td><input type=text size=6 class=text name=sc> <input type=submit value=Bestätigung class=button></td></tr></form>
	</table>";
}












if ($v == "torprep")
{

	pageheader("/ <a href=?p=station>Stationen</a> / <a href=?p=station&s=show&id=".$_GET['id'].">".ftit($sta->name)."</a> / <b>Torpedoherstellung</b>");
	if ($sta->fdd['component_id'] == 120) include_once("inc/stattorp.php");
}
?>
