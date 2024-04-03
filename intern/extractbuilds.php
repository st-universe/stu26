<?php
	include_once("../inc/func.inc.php");
	include_once("../inc/config.inc.php");
	include_once($global_path."/class/db.class.php");
	$db = new db;

	
	$pathadjust = "../";
	
	
	echo "<html>
<head>
	<meta HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=iso-8859-1\">
	<title>Star Trek Universe</title>

<link rel=\"STYLESHEET\" type=\"text/css\" href=".$pathadjust."gfx/css/6.css>

</head>
<body bgcolor=\"#000000\" style=\"margin-top: 0px;\">";

$op .= "<style type=\"text/css\">
div.header {
   position: fixed;
   left: 140px;
   right: 5px;
   top: 0px;
   z-index: 900;
}
div.colinfo {
   position: fixed;
   left: 5px;
   top: 350px;
   width: 130px;
}
td.pages {
	text-align: center;
	width: 20px;
	border: 1px groove #8897cf;
}
td.pages:hover
{
	background: #262323;
}
#info {
visibility: hidden;
position: absolute;
top: 10px;
left: 10px;
z-index: 1;
width:300px;
    padding-top: 5px;
    padding-bottom: 5px;
    padding-left: 5px;
background-color: #000000;
border: 1px solid #8897cf;
}
#content {
background-color: #000000;

}</style>";
	
	
	
	
	$op .= "<script type=\"text/javascript\" language=\"JavaScript\">
	var e;
	
function planetOccurrance(id) {

	switch(id) {
		case 1: 	return ['201','202','203'];
		case 2: 	return ['201','202','203'];
		case 3: 	return ['202','205'];
		case 4: 	return ['203'];
		case 5: 	return ['202','203'];
		case 6: 	return ['201','204','205','209'];
		case 7: 	return ['201','206'];
		case 8: 	return ['204'];
		case 9: 	return ['204'];
		case 10: 	return ['210'];
		case 11: 	return ['208'];
		case 12: 	return ['210'];
		case 13: 	return ['208'];
		case 14: 	return ['209'];
		case 16: 	return ['209'];
		case 17: 	return ['208'];
		case 18: 	return ['205'];
		case 19: 	return ['206'];
		case 20: 	return ['209'];
		case 31: 	return ['201','202','203','205'];
		case 32: 	return ['206'];
		case 33: 	return ['209'];
		case 34: 	return ['204'];
		case 35: 	return ['208'];
		case 36: 	return ['210'];
		case 40: 	return ['201','203'];
		case 41: 	return ['203'];
		case 42: 	return ['203'];
		case 44: 	return ['203'];
		case 45: 	return ['203'];
		case 47: 	return ['206'];
		case 201: 	return ['207'];
		case 65: 	return ['203'];
		case 67: 	return ['203'];
		case 68: 	return ['203'];
		
		case 81: 	return ['201','202','203','204','205','206','207','209','210'];
		case 82: 	return ['201','202','203','204','205','206','207','209','210'];
		case 83: 	return ['201','202','203','204','205','206','207','209'];
		case 84: 	return ['201','202','203','206','209'];
		
		case 100: 	return ['201','202','203','204','205','206','207','209','210'];
		
		case 200: 	return ['207'];
		case 203: 	return ['207'];
		case 204: 	return ['207'];
		case 205: 	return ['207'];
		case 223: 	return ['207'];
		case 225: 	return ['207'];
		case 226: 	return ['207'];
		case 229: 	return ['207'];
		case 230: 	return ['207'];
		case 232: 	return ['207'];
		case 241: 	return ['207'];
		case 242: 	return ['207'];
		case 243: 	return ['207'];
		case 244: 	return ['207'];
		
		
		default:	return [];
	} 
}	
	
function fieldIsTerraform(id) {

		switch(id) {
			case 16: return true;
			case 44: return true;
			case 67: return true;
			case 68: return true;
			case 69: return true;
			case 81: return true;
			case 82: return true;
			case 83: return true;
			case 84: return true;
			case 203: return true;
			case 204: return true;
			case 205: return true;
			default: return false;
		
	}


}	
	
function fieldName(id) {

		switch(id) {
			case  1: return 'Wiese';
			case  2: return 'Wald';
			case  3: return 'Nadelwald';
			case  5: return 'See';
			// case  4: return 'Seichtes Wasser';
			case 40: return 'Ozean';
			case 41: return 'Schelfmeer';
			case 42: return 'Korallenriff';
			case 44: return 'Abgetragenes Korallenriff';
			case 45: return 'Aufgeschüttetes Land';
			case 20: return 'Eisdecke';
			case  7: return 'Wüste';
			case  8: return 'Wüste';
			case 48: return 'Sanddünen';
			case 49: return 'Sanddünen';
			case 50: return 'Sanddünen';
			case  6: return 'Eis';
			case 14: return 'Eisformationen';
			case 16: return 'Eiswasser';
			case  9: return 'Ödland';
			case 18: return 'Tundra';
			case 15: return 'Felsformationen';
			case 47: return 'Felsspalten';
			case 31:
			case 32:
			case 33:
			case 34:
			case 36:
			case 35: return 'Berge';
			case 19: return 'Fels';
			case 10: return 'Fels';
			case 12: return 'Krater';

			case 79: return 'Fels';
			case 70: return 'Untergrund';
			case 71: return 'Untergrund';
			case 72: return 'Untergrund';
			case 73: return 'Untergrund';
			case 74: return 'Untergrund';

			
			case 81: return 'Untergrund-Fels';
			case 82: return 'Erzader';
			case 83: return 'Magmaeinschluss';
			case 84: return 'Untergrund-Wasser';
			
			
			case 100: return 'Weltraum';
			case 110:
			case 111:
			case 112:
			case 113:
			case 114:
			case 115:
			case 116:
			case 117:
			case 118:
			case 119: return 'Weltraum';
			
			
			case 200: return 'Lavagestein';
			case 201: return 'Berge';
			case 202: return 'Erloschener Vulkan';
			case 211:
			case 212:
			case 213:
			case 214:
			case 215:
			case 216:
			case 217: return 'Aktiver Vulkan';
			case 205: return 'Versiegelter Lavastrom';
			case 203: return 'Abgetragener Vulkan';
			case 204: return 'Kontrollierter Vulkan';
			
			case 223: 
			case 225: 
			case 226: 
			case 229: 
			case 230: 
			case 232: 
			case 241: 
			case 242: 
			case 243: 
			case 244: return 'Lavastrom';
			case 245: 
			case 246: 
			case 247: 
			case 248: return 'Lavasee';
			case  11: return 'Ebene';
			case  12:
			case  13: return 'Krater';
			case  17: return 'Aktiver Vulkan';
			case  28: return 'Erloschener Vulkan';
			
			case 1000: return 'Unfertig';
			default: return '???';
		
	}


}	
	
function getFieldInfo(id) {
	
	s = '<img src=".$pathadjust."gfx/fields/'+id+'.gif> ';

	occ = planetOccurrance(id);
	v = '';
	for (i = 0; i <  occ.length; i++) {
		v += '<img src=".$pathadjust."gfx/map/'+occ[i]+'.gif>';
	}
	
	if (fieldIsTerraform(id)) t = ' <font size=\"-2\">(Terraforming)</font>';
	else t = '';
	return s+'<b>'+fieldName(id)+'</b>'+t+'<br><br><u>Möglich auf:</u> <br>'+v;
}
	
	
function goodText(id) {
	switch(id) {
		case 0: return 'Wird für fast alle Vorgänge benötigt.';
		case 1: return 'Konsumiert von Einwohnern. 10 Einwohner essen 1 Nahrung pro Tick.';
		case 2: return 'Grundlegende Materialien und Werkzeuge, die für fast alle Konstruktionen benötigt werden.';
		case 3: return 'Alleine wenig nützlich, werden diese Chemikalien meist zu anderen Waren weiterverarbeitet.';
		case 4: return 'Das Glas der Zukunft. Wird für viele Konstruktionen benötigt.';
		case 5: return 'Dieses schwere Wasserstoff-Isotop wird hauptsächlich zur Energiegewinnung in Fusions- und Warpreaktoren benötigt.';
		case 6: return 'Typischerweise Anti-Deuterium. Wird von Warpreaktoren verbraucht und für die Konstruktion von Photonentorpedos benötigt.';
		case 8: return 'Ein seltener Kristall, der die hoch-energetischen Reaktionen in Materie/Antimaterie-Auslöschungen regulieren kann.';
		case 11: return 'Gestein mit hohem Metall-Gehalt. Muss weiterverarbeitet werden.';
		case 19: return 'Gestein mit hohem Gehalt des Metalls Tritanium.';
		case 21: return 'Eine sehr widerstandsfähige Metall-Legierung, die für viele Konstruktionen benötigt wird.';
		case 29: return 'Tritanium';
		case 31: return 'Bestandteil von modernen Computer-Systemen. Wird zur Herstellung von Schiffsmodulen verwendet.';
		case 32: return 'Bestandteil von Hochenergie-Systemen. Wird zur Herstellung von Schiffsmodulen verwendet.';
		case 33: return 'Bestandteil von Schiffs-Systemen. Wird zur Herstellung von Schiffsmodulen verwendet.';
		case 34: return 'Bestandteil von Schiffs-Systemen. Wird zur Herstellung von Schiffsmodulen verwendet.';
		case 66: return 'Eine imaginäre High-Tech-Komponente, die irgendwas cooles macht.';

		default: return 'Keine Beschreibung vorhanden.';
		
	}
}	

function goodName(id) {
	switch(id) {
		case 0: return 'Energie';
		case 1: return 'Nahrung';
		case 2: return 'Baumaterial';
		case 3: return 'Chemische Komponenten';
		case 4: return 'Transparentes Aluminium';
		case 5: return 'Deuterium';
		case 6: return 'Antimaterie';
		case 8: return 'Dilithium';
		case 11: return 'Erz';
		case 19: return 'Tritanium-Erz';
		case 21: return 'Duranium';
		case 29: return 'Tritanium';
		case 31: return 'Isolinear-Chips';
		case 32: return 'Plasma';
		case 33: return 'Metaphasen-Konverter';
		case 34: return 'Subraum-Spulen';
		case 66: return 'Transphasen-Fluxkompensator';
		default: return '???';
		
	}
}	
	
	
function getGoodInfo(id) {
	
	s = '<img src=".$pathadjust."gfx/goods/'+id+'.gif> ';

	
	s += '<b>'+goodName(id)+'</b><br><br>'+goodText(id);
	
	return s;
}	
	
	
	
	
function getMiscInfo(id) {
	
	
	switch(id) {
		case 'r': return '<b>Forschungspunkte</b><br><br>Für dieses Gebäude ist eine Forschung notwendig.';
		case 'a': return '<b>Arbeiter</b><br><br>Um dieses Gebäude zu betreiben werden freie Arbeiter benötigt.';
		case 'w': return '<b>Wohnraum</b><br><br>Dieses Gebäude erhöht den Wohnraum für zusätzliche Einwohner.';
		case 'l': return '<b>Lagerraum</b><br><br>Dieses Gebäude erhöht die maximale Lagerkapazität für Waren.';
		case 'e': return '<b>Energiespeicher</b><br><br>Dieses Gebäude erhöht die maximale Energiespeicherkapazität.';
		case 'lg': return '<b>Globales Baulimit</b><br><br>Maximal baubare Anzahl pro Spieler ist begrenzt.';
		case 'lc': return '<b>Kolonie-Baulimit</b><br><br>Maximal baubare Anzahl pro Kolonie ist begrenzt.';
		case 'cr': return '<b>Crewlimit</b><br><br>Erhöht die maximale Crew.';
		case 't': return '<b>Bauzeit</b><br><br>Die Zeit benötigt bis zur Fertigstellung des Gebäudes.';
		case 'fleet': return '<b>Flottenpunkte</b><br><br>Erhöht das globale Schiffslimit.';
		case 'pcrew': return '<b>Crewpunkte</b><br><br>Repräsentiert Ausbildung und Unterhalt von Schiffscrews. Erhöht das Schiffslimit in Verbindung mit Wartungs- und Versorgungspunkten.';
		case 'pmaintain': return '<b>Wartungspunkte</b><br><br>Repräsentiert Wartung und generelle Instandhaltung von Schiffen. Erhöht das Schiffslimit in Verbindung mit Crew- und Versorgungspunkten.';
		case 'psupply': return '<b>Versorgungspunkte</b><br><br>Repräsentiert Versorgung von Schiffen. Erhöht das Schiffslimit in Verbindung mit Crew- und Wartungsspunkten.';
		case 'research': return '<b>Forschungspunkte</b><br><br>Werden zur Erforschung neuer Technologien benötigt.';

		default: return '???';
		
	}	
	
	return s;
}	
	
	
	
	
	
	
	
function getPos(el) {
    // yay readability
    for (var lx=0, ly=0;
         el != null;
         lx += el.offsetLeft, ly += el.offsetTop, el = el.offsetParent);
    return {x: lx,y: ly};
}
function hideInfo() {
      document.getElementById('info').style.visibility = \"hidden\";
}

function showInfo(e,Inhalte,rel)
{
		var x = rel.offsetLeft + rel.offsetParent.offsetLeft + rel.offsetParent.offsetParent.offsetLeft + rel.offsetParent.offsetParent.offsetParent.offsetLeft;
		var y = rel.offsetTop + rel.offsetParent.offsetTop + rel.offsetParent.offsetParent.offsetTop + rel.offsetParent.offsetParent.offsetParent.offsetTop+ rel.offsetParent.offsetParent.offsetParent.offsetParent.offsetTop;
        document.getElementById('info').innerHTML = Inhalte;
        document.getElementById('info').style.left = x+30+'px';
        document.getElementById('info').style.top = y+'px';
        document.getElementById('info').style.visibility = \"visible\";
}</script><div id=\"info\" style=\"z-index:10; visibility:hidden;\">&nbsp;</div>
";
	
$op .= "<table>";


$prevlevel = -1;
$i = 0;
// $res = $db->query("SELECT a.buildings_id,a.name,a.lager,a.eps_cost,a.eps,a.eps_proc,a.bev_pro,a.research_id,a.bev_use,a.level,a.integrity,a.points,a.schilde,a.bclimit,a.blimit,a.upgrade_from,a.buildtime,b.name as upname FROM stu_buildings as a LEFT JOIN stu_buildings as b ON a.upgrade_from=b.buildings_id WHERE ((a.view=1 OR a.buildings_id=1) AND (a.buildings_id < 200 OR a.buildings_id > 299) AND a.buildings_id != 99 AND a.buildings_id != 98) ORDER BY a.level,a.name");


$res = $db->query("SELECT a.buildings_id,a.name,a.lager,a.eps_cost,a.eps,a.eps_proc,a.bev_pro,a.research_id,a.bev_use,a.level,a.integrity,a.points,a.schilde,a.bclimit,a.blimit,a.upgrade_from,a.buildtime,b.name as upname,a.research_t,a.research_v,a.research_k FROM stu_buildings as a LEFT JOIN stu_buildings as b ON a.upgrade_from=b.buildings_id WHERE ((a.view=1 OR a.buildings_id=1) AND a.research_id = 0) ORDER BY a.level,a.name");

while($data=mysql_fetch_assoc($res))
{
	if (($i%4 != 0) && ($data[level] != $prevlevel) && ($prevlevel != -1)) {
		$op .= "</tr>";
		$i = 0;
	}
	
	if ($data[level] != $prevlevel) {
		$op .= "<tr style=\"height:10px;\"><td colspan=8></td></tr><tr><th colspan=7 style=\"height:25px;\">Ab Level ".$data[level]."</th><td></td></tr><tr style=\"height:10px;\"><td colspan=8></td></tr>";
	}
	
	if (($i%4 == 0) || ($data[level] != $prevlevel)) {
		$op .= "<tr>";
	}

	
	$field = $db->query("SELECT type FROM stu_field_build WHERE buildings_id=".$data[buildings_id]." AND type<200 ORDER BY type ASC LIMIT 1",1);
	$cost = $db->query("SELECT a.goods_id,a.count,b.name FROM stu_buildings_cost as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.buildings_id=".$data[buildings_id]." ORDER BY b.sort");
	$goods = $db->query("SELECT a.goods_id,a.count,b.name FROM stu_buildings_goods as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.buildings_id=".$data[buildings_id]." ORDER BY b.sort");
	$effects = $db->query("SELECT type,count FROM stu_buildings_effects WHERE buildings_id=".$data[buildings_id]." LIMIT 1;",4);
	
	if ($data[level] != $prevlevel) {
		$prevlevel = $data[level];
	}
	
	$op .= "<td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>".stripslashes($data[name])."</th>
	</tr>
	<tr>
		<td><div align=center><img src=".$pathadjust."gfx/buildings/".$data[buildings_id]."/0.png></div><br>";
		$j = 0;
		
		$op .= "<div  style=\"padding-left:20px;\">";
		
		$result = $db->query("SELECT type FROM stu_field_build WHERE type<300 AND buildings_id=".$data[buildings_id]." ORDER BY type ASC");
		while($dat=mysql_fetch_assoc($result))
		{
			$j++;
			$op .= "<img src=".$pathadjust."gfx/fields/".$dat[type].".gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(".$dat[type]."),this);\" onMouseOut=\"hideInfo();\">&nbsp;";
			if ($j%8 == 0) $op .= "<br>";
		}
		$op .= "</div>";
			$op .= "</td>
	</tr>
	<tr><td>";
	// Ab Level: ".$data[level]."<br>";
	if ($data[research_id] != 0) $op .= "<img src=".$pathadjust."gfx/icons/forsch1.gif onMouseOver=\"showInfo(event,getMiscInfo('r'),this);\" onMouseOut=\"hideInfo();\"> Benötigt Forschung<br>";
	if ($data[bev_pro] > 0) $op .= "<img src=".$pathadjust."gfx/bev/blank/0f.png onMouseOver=\"showInfo(event,getMiscInfo('w'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> ".$data[bev_pro]."<br>";
	if ($data[bev_use] > 0) $op .= "<img src=".$pathadjust."gfx/bev/crew/#RACE#m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> ".$data[bev_use]."<br>";
	if ($data[buildtime] > 0) $op .= "<img src=".$pathadjust."gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> ".round($data[buildtime]/3600,1)." Stunden<br>";	
	if ($data[lager] > 0) $op .= "<img src=".$pathadjust."gfx/icons/storage.gif onMouseOver=\"showInfo(event,getMiscInfo('l'),this);\" onMouseOut=\"hideInfo();\"> ".$data[lager]."<br>";
	if ($data[eps] > 0) $op .= "<img src=".$pathadjust."gfx/icons/eps.gif onMouseOver=\"showInfo(event,getMiscInfo('e'),this);\" onMouseOut=\"hideInfo();\"> ".$data[eps]."<br>";
	// if ($data[points] > 0) $op .= "<img src=".$pathadjust."gfx/buttons/points.gif title='Wirtschaft'> +".$data[points]."<br>";
	// $op .= "Integrität: ".$data[integrity]."<br>
	// <img src=".$pathadjust."gfx/buttons/time.gif title='Bauzeit'> ".gen_time($data[buildtime]);

	if ($data[blimit] > 0) $op .= "<img src=".$pathadjust."gfx/icons/stopr.gif onMouseOver=\"showInfo(event,getMiscInfo('lg'),this);\" onMouseOut=\"hideInfo();\"> Max. pro Spieler: ".$data[blimit]."<br>";
	if ($data[bclimit] > 0) $op .= "<img src=".$pathadjust."gfx/icons/stopg.gif onMouseOver=\"showInfo(event,getMiscInfo('lc'),this);\" onMouseOut=\"hideInfo();\"> Max. pro Kolonie: ".$data[bclimit]."<br>";
	if ($data[upgrade_from] > 0) $op .= "<br>Upgrade von: ".$data[upname];
	$op .= "</td>
	</tr>";
	if (mysql_num_rows($goods) > 0 || $data[eps_proc] != 0)
	{
		$op .= "<tr>
		<td><u>Produktion / Verbrauch</u><br>";
		$op .= "<img src=".$pathadjust."gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> ".($data[eps_proc] > 0 ? "<font color=\"#66ff66\"/>+".$data[eps_proc] : "<font color=\"#ff6666\"/>".$data[eps_proc])."</font><br>";
		while($g=mysql_fetch_assoc($goods)) $op .= "<img src=".$pathadjust."gfx/goods/".$g[goods_id].".gif onMouseOver=\"showInfo(event,getGoodInfo(".$g[goods_id]."),this);\" onMouseOut=\"hideInfo();\"> ".($g['count'] > 0 ? "<font color=\"#66ff66\"/>+".$g['count'] : "<font color=\"#ff6666\"/>".$g['count'])."</font><br>";
		
	
		if ($effects['type']) $op .= "<img src=".$pathadjust."gfx/icons/".$effects['type'].".gif onMouseOver=\"showInfo(event,getMiscInfo('".$effects['type']."'),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#00ff00\"/>+".$effects['count']."</font><br>";
	
		$op .= "</td>
		</tr>";
	}

	
	if ($data[buildings_id] > 1) {
		$op .= "<tr>
		<td><u>Baukosten</u><br>";
		$op .= "<img src=".$pathadjust."gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> ".$data[eps_cost]."</font><br>";
		while($g=mysql_fetch_assoc($cost)) $op .= "<img src=".$pathadjust."gfx/goods/".$g[goods_id].".gif onMouseOver=\"showInfo(event,getGoodInfo(".$g[goods_id]."),this);\" onMouseOut=\"hideInfo();\"> ".($g['count'])."</font><br>";
		$op .= "</td>
		</tr>";
	}

	// $op .= "<tr>
		// <td><u>Baukosten</u><br>
		// <img src=".$pathadjust."gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> ".$data[eps_cost]."<br>";
		// while($c=mysql_fetch_assoc($cost)) $op .= "<img src=".$pathadjust."gfx/goods/".$c[goods_id].".gif onMouseOver=\"showInfo(event,getGoodInfo(".$c[goods_id]."),this);\" onMouseOut=\"hideInfo();\"> ".$c['count']."<br>";
		// $op .= "</td>
	// </tr>";
	$op .= "</table></td><td width=40></td>";
	$i++;
	if ($i%4==0) $op .= "</tr>";
}
$op .= "</tr>";

$i = 0;

$res = $db->query("SELECT a.buildings_id,a.name,a.lager,a.eps_cost,a.eps,a.eps_proc,a.bev_pro,a.research_id,a.bev_use,a.level,a.integrity,a.points,a.schilde,a.bclimit,a.blimit,a.upgrade_from,a.buildtime,b.name as upname,a.research_t,a.research_v,a.research_k FROM stu_buildings as a LEFT JOIN stu_buildings as b ON a.upgrade_from=b.buildings_id WHERE ((a.view=1 OR a.buildings_id=1) AND a.research_id > 0) ORDER BY a.level,a.name");
$op .= "<tr style=\"height:10px;\"><td colspan=8></td></tr><tr><th colspan=7 style=\"height:25px;\">Nach Forschung</th><td></td></tr><tr style=\"height:10px;\"><td colspan=8></td></tr>";
	
while($data=mysql_fetch_assoc($res))
{
	if (($i%4 != 0) && ($data[level] != $prevlevel) && ($prevlevel != -1)) {
		$op .= "</tr>";
		$i = 0;
	}
	

	
	if (($i%4 == 0) || ($data[level] != $prevlevel)) {
		$op .= "<tr>";
	}

	
	$field = $db->query("SELECT type FROM stu_field_build WHERE buildings_id=".$data[buildings_id]." AND type<200 ORDER BY type ASC LIMIT 1",1);
	$cost = $db->query("SELECT a.goods_id,a.count,b.name FROM stu_buildings_cost as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.buildings_id=".$data[buildings_id]." ORDER BY b.sort");
	$goods = $db->query("SELECT a.goods_id,a.count,b.name FROM stu_buildings_goods as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.buildings_id=".$data[buildings_id]." ORDER BY b.sort");
	$effects = $db->query("SELECT type,count FROM stu_buildings_effects WHERE buildings_id=".$data[buildings_id]." LIMIT 1;",4);

	if ($data[level] != $prevlevel) {
		$prevlevel = $data[level];
	}
	
	$op .= "<td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>".stripslashes($data[name])."</th>
	</tr>
	<tr>
		<td><div align=center><img src=".$pathadjust."gfx/buildings/".$data[buildings_id]."/0.png></div><br>";
		$j = 0;
		
		$op .= "<div  style=\"padding-left:20px;\">";
		
		$result = $db->query("SELECT type FROM stu_field_build WHERE type<300 AND buildings_id=".$data[buildings_id]." ORDER BY type ASC");
		while($dat=mysql_fetch_assoc($result))
		{
			$j++;
			$op .= "<img src=".$pathadjust."gfx/fields/".$dat[type].".gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(".$dat[type]."),this);\" onMouseOut=\"hideInfo();\">&nbsp;";
			if ($j%8 == 0) $op .= "<br>";
		}
		$op .= "</div>";
			$op .= "</td>
	</tr>
	<tr><td>";

	if ($data[research_id] != 0) $op .= "<img src=".$pathadjust."gfx/icons/forsch1.gif onMouseOver=\"showInfo(event,getMiscInfo('r'),this);\" onMouseOut=\"hideInfo();\"> Benötigt Forschung<br>";

	if ($data[bev_pro] > 0) $op .= "<img src=".$pathadjust."gfx/bev/blank/0f.png onMouseOver=\"showInfo(event,getMiscInfo('w'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> ".$data[bev_pro]."<br>";
	if ($data[bev_use] > 0) $op .= "<img src=".$pathadjust."gfx/bev/crew/#RACE#m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> ".$data[bev_use]."<br>";

	
	if ($data[buildtime] > 0) $op .= "<img src=".$pathadjust."gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> ".round($data[buildtime]/3600,1)." Stunden<br>";
	
	if ($data[lager] > 0) $op .= "<img src=".$pathadjust."gfx/icons/storage.gif onMouseOver=\"showInfo(event,getMiscInfo('l'),this);\" onMouseOut=\"hideInfo();\"> ".$data[lager]."<br>";
	if ($data[eps] > 0) $op .= "<img src=".$pathadjust."gfx/icons/eps.gif onMouseOver=\"showInfo(event,getMiscInfo('e'),this);\" onMouseOut=\"hideInfo();\"> ".$data[eps]."<br>";

	if ($data[blimit] > 0) $op .= "<img src=".$pathadjust."gfx/icons/stopr.gif onMouseOver=\"showInfo(event,getMiscInfo('lg'),this);\" onMouseOut=\"hideInfo();\"> Max. pro Spieler: ".$data[blimit]."<br>";
	if ($data[bclimit] > 0) $op .= "<img src=".$pathadjust."gfx/icons/stopg.gif onMouseOver=\"showInfo(event,getMiscInfo('lc'),this);\" onMouseOut=\"hideInfo();\"> Max. pro Kolonie: ".$data[bclimit]."<br>";
	if ($data[upgrade_from] > 0) $op .= "<br>Upgrade von: ".$data[upname];
	$op .= "</td>
	</tr>";
	if (mysql_num_rows($goods) > 0 || $data[eps_proc] != 0)
	{
		$op .= "<tr>
		<td><u>Produktion / Verbrauch</u><br>";
		$op .= "<img src=".$pathadjust."gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> ".($data[eps_proc] > 0 ? "<font color=\"#66ff66\"/>+".$data[eps_proc] : "<font color=\"#ff6666\"/>".$data[eps_proc])."</font><br>";
		while($g=mysql_fetch_assoc($goods)) $op .= "<img src=".$pathadjust."gfx/goods/".$g[goods_id].".gif onMouseOver=\"showInfo(event,getGoodInfo(".$g[goods_id]."),this);\" onMouseOut=\"hideInfo();\"> ".($g['count'] > 0 ? "<font color=\"#66ff66\"/>+".$g['count'] : "<font color=\"#ff6666\"/>".$g['count'])."</font><br>";
		
		if ($effects['type']) $op .= "<img src=".$pathadjust."gfx/icons/".$effects['type'].".gif onMouseOver=\"showInfo(event,getMiscInfo('".$effects['type']."'),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#00ff00\"/>+".$effects['count']."</font><br>";
		if ($data[buildings_id] == 54) {
			$op .= "<img src=".$pathadjust."gfx/goods/1.gif onMouseOver=\"showInfo(event,getGoodInfo(1),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+2</font> pro <img src=".$pathadjust."gfx/buildings/2/0.png width=22 height=22> Farm<br>";
			$op .= "<img src=".$pathadjust."gfx/goods/1.gif onMouseOver=\"showInfo(event,getGoodInfo(1),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+2</font> pro <img src=".$pathadjust."gfx/buildings/9/0.png width=22 height=22> Algenfarm<br>";
		}
		
		$op .= "</td>
		</tr>";
	}

		$op .= "<tr>
		<td><u>Baukosten</u><br>";
		$op .= "<img src=".$pathadjust."gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> ".$data[eps_cost]."</font><br>";
		while($g=mysql_fetch_assoc($cost)) $op .= "<img src=".$pathadjust."gfx/goods/".$g[goods_id].".gif onMouseOver=\"showInfo(event,getGoodInfo(".$g[goods_id]."),this);\" onMouseOut=\"hideInfo();\"> ".($g['count'])."</font><br>";
		$op .= "</td>
		</tr>";
	
	$op .= "</table></td><td width=40></td>";
	$i++;
	if ($i%4==0) $op .= "</tr>";
}

$op .= "</tr>";

echo "</table>";

unlink($global_path."/inc/lists/gbl.html");
$fp = fopen($global_path."/inc/lists/gbl.html","a+");
fwrite($fp,str_replace("#RACE#","1",$op));
fclose($fp);

$op = str_replace("\"","\\\"",$op);
$op = str_replace("#RACE#","\".\$_SESSION['race'].\"",$op);

unlink($global_path."/inc/lists/gbl.php");
$fp = fopen($global_path."/inc/lists/gbl.php","a+");
fwrite($fp,"<?php \n echo \"".$op."\";\n ?>");
fclose($fp);

	echo $op;
	
	
	echo "</body></html>";
?>