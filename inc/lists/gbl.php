<?php 
 echo "<style type=\"text/css\">
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

}</style><script type=\"text/javascript\" language=\"JavaScript\">
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
	
	s = '<img src=../gfx/fields/'+id+'.gif> ';

	occ = planetOccurrance(id);
	v = '';
	for (i = 0; i <  occ.length; i++) {
		v += '<img src=../gfx/map/'+occ[i]+'.gif>';
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
		case 7: return 'Dihydrogen-Monoxid. Wird für die meisten Formen von Leben benötigt. Hydroponik und Deuteriumextraktion sind die häufigsten industriellen Anwendungen.';
		case 8: return 'Ein seltener Kristall, der die hoch-energetischen Reaktionen in Materie/Antimaterie-Auslöschungen regulieren kann.';
		case 9: return 'Sammelbegriff für Elemente, die oft nur in Spuren vorkommen, aber von industrieller Bedeutung sind.';
		case 11: return 'Gestein mit hohem Metall-Gehalt. Muss weiterverarbeitet werden.';
		case 19: return 'Tritanium in Reinform. Seine Verarbeitung zu Tritanium-Legierung ist ein hochkomplexer Prozess.';
		case 21: return 'Eine sehr widerstandsfähige Metall-Legierung, die für viele Konstruktionen benötigt wird.';
		case 29: return 'Tritanium-Legierung hält selbst extremsten Umständen stand. Es wird daher für moderne Schiffspanzerungen verwendet.';
		case 30: return 'Allgemeine Komponente in vielen einfacheren Schiffssystemen.';
		case 31: return 'Bestandteil von modernen Computer-Systemen. Wird zur Herstellung von Schiffsmodulen verwendet.';
		case 32: return 'Bestandteil von Hochenergie-Systemen. Wird zur Herstellung von Schiffsmodulen verwendet.';
		case 33: return 'Bestandteil von Schiffs-Systemen. Wird zur Herstellung von Schiffsmodulen verwendet.';
		case 34: return 'Bestandteil von Schiffs-Systemen. Wird zur Herstellung von Schiffsmodulen verwendet.';
		case 35: return 'Bestandteil von Schiffs-Systemen. Wird zur Herstellung von Schiffsmodulen verwendet.';
		case 41: return 'Seltener Rohstoff, der zur Herstellung von Isolinearen Chips benötigt wird.';
		case 42: return 'Seltener Rohstoff, der zur Herstellung von Hochenergie-Plasma benötigt wird.';
		case 43: return 'Seltener Rohstoff, der zur Herstellung von Metaphasen-Konvertern benötigt wird.';
		case 44: return 'Seltener Rohstoff, der zur Herstellung von Subraum-Spulen benötigt wird.';
		case 45: return 'Seltener Rohstoff, der zur Herstellung von Partikel-Emittern benötigt wird.';		
		case 66: return 'Eine imaginäre High-Tech-Komponente, die irgendwas cooles macht.';
		case 81: return 'Ein Torpedo. Munition für Torpedorampen.';
		case 82: return 'Ein Torpedo. Munition für Torpedorampen.';

		default: return 'Keine Beschreibung vorhanden.';
		
	}
}	

function goodName(id) {
	switch(id) {
		case 0: return 'Energie';
		case 1: return 'Nahrung';
		case 2: return 'Baumaterial';
		case 3: return 'Chemikalien';
		case 4: return 'Transparentes Aluminium';
		case 5: return 'Deuterium';
		case 6: return 'Antimaterie';
		case 7: return 'Wasser';
		case 8: return 'Dilithium';
		case 9: return 'Seltene Erden';
		case 11: return 'Erz';
		case 19: return 'Tritanium';
		case 21: return 'Duranium';
		case 29: return 'Tritanium-Legierung';
		case 30: return 'Einfache Schaltkreise';
		case 31: return 'Isolineare Chips';
		case 32: return 'Hochenergie-Plasma';
		case 33: return 'Metaphasen-Konverter';
		case 34: return 'Subraum-Spulen';
		case 35: return 'Partikel-Emitter';
		case 41: return 'Nitrium';
		case 42: return 'Kemocite';
		case 43: return 'Verterium';
		case 44: return 'Cortenit';
		case 45: return 'Bilitrium';
		case 66: return 'Transphasen-Fluxkompensator';
		case 81: return 'Photonentorpedo';
		case 82: return 'Plasmatorpedo';
		default: return '???';
		
	}
}	
	
	
function getGoodInfo(id) {
	
	s = '<img src=../gfx/goods/'+id+'.gif> ';

	
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
<table><tr style=\"height:10px;\"><td colspan=8></td></tr><tr><th colspan=7 style=\"height:25px;\">Ab Level 0</th><td></td></tr><tr style=\"height:10px;\"><td colspan=8></td></tr><tr><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Koloniezentrale</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/1/0.png></div><br><div  style=\"padding-left:20px;\"></div></td>
	</tr>
	<tr><td><img src=../gfx/bev/blank/0f.png onMouseOver=\"showInfo(event,getMiscInfo('w'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 50<br><img src=../gfx/icons/storage.gif onMouseOver=\"showInfo(event,getMiscInfo('l'),this);\" onMouseOut=\"hideInfo();\"> 5000<br><img src=../gfx/icons/eps.gif onMouseOver=\"showInfo(event,getMiscInfo('e'),this);\" onMouseOut=\"hideInfo();\"> 150<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+10</font><br><img src=../gfx/goods/1.gif onMouseOver=\"showInfo(event,getGoodInfo(1),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+5</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+5</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Koloniezentrale E</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/102/0.png></div><br><div  style=\"padding-left:20px;\"></div></td>
	</tr>
	<tr><td><img src=../gfx/bev/blank/0f.png onMouseOver=\"showInfo(event,getMiscInfo('w'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 50<br><img src=../gfx/icons/storage.gif onMouseOver=\"showInfo(event,getMiscInfo('l'),this);\" onMouseOut=\"hideInfo();\"> 5000<br><img src=../gfx/icons/eps.gif onMouseOver=\"showInfo(event,getMiscInfo('e'),this);\" onMouseOut=\"hideInfo();\"> 150<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+14</font><br><img src=../gfx/goods/1.gif onMouseOver=\"showInfo(event,getGoodInfo(1),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+5</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 0</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Koloniezentrale N</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/101/0.png></div><br><div  style=\"padding-left:20px;\"></div></td>
	</tr>
	<tr><td><img src=../gfx/bev/blank/0f.png onMouseOver=\"showInfo(event,getMiscInfo('w'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 50<br><img src=../gfx/icons/storage.gif onMouseOver=\"showInfo(event,getMiscInfo('l'),this);\" onMouseOut=\"hideInfo();\"> 5000<br><img src=../gfx/icons/eps.gif onMouseOver=\"showInfo(event,getMiscInfo('e'),this);\" onMouseOut=\"hideInfo();\"> 150<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+10</font><br><img src=../gfx/goods/1.gif onMouseOver=\"showInfo(event,getGoodInfo(1),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+9</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 0</font><br></td>
		</tr></table></td><td width=40></td></tr><tr style=\"height:10px;\"><td colspan=8></td></tr><tr><th colspan=7 style=\"height:25px;\">Ab Level 1</th><td></td></tr><tr style=\"height:10px;\"><td colspan=8></td></tr><tr><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Baracken</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/3/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/1.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(1),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/9.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(9),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/18.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(18),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/19.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(19),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/45.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(45),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/47.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(47),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/blank/0f.png onMouseOver=\"showInfo(event,getMiscInfo('w'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 40<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 0.8 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-1</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 25</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 25</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Baumaterialfabrik</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/6/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/1.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(1),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/6.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(6),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/7.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(7),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/8.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(8),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/9.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(9),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/10.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(10),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/18.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(18),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/19.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(19),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br><img src=../gfx/fields/45.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(45),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/47.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(47),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/81.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(81),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/82.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(82),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/83.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(83),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/200.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(200),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/203.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(203),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/205.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(205),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br></div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 15<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 1.5 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-3</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+5</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 50</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Farm</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/2/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/1.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(1),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/45.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(45),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 5<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 0.5 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-1</font><br><img src=../gfx/goods/1.gif onMouseOver=\"showInfo(event,getGoodInfo(1),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+4</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 25</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 25</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Lager</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/4/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/1.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(1),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/6.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(6),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/7.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(7),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/8.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(8),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/9.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(9),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/10.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(10),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/18.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(18),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/19.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(19),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br><img src=../gfx/fields/20.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(20),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/45.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(45),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/47.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(47),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/81.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(81),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/82.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(82),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/83.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(83),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/200.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(200),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/203.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(203),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br><img src=../gfx/fields/205.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(205),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 0.8 Stunden<br><img src=../gfx/icons/storage.gif onMouseOver=\"showInfo(event,getMiscInfo('l'),this);\" onMouseOut=\"hideInfo();\"> 4000<br><img src=../gfx/icons/eps.gif onMouseOver=\"showInfo(event,getMiscInfo('e'),this);\" onMouseOut=\"hideInfo();\"> 75<br></td>
	</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 38</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 38</font><br></td>
		</tr></table></td><td width=40></td></tr><tr><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Solarkollektoren</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/5/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/1.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(1),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/6.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(6),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/7.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(7),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/8.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(8),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/9.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(9),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/10.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(10),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/18.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(18),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/19.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(19),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br><img src=../gfx/fields/20.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(20),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/45.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(45),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/47.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(47),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/200.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(200),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/203.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(203),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/205.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(205),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 5<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 0.5 Stunden<br><img src=../gfx/icons/eps.gif onMouseOver=\"showInfo(event,getMiscInfo('e'),this);\" onMouseOut=\"hideInfo();\"> 10<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+4</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 25</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 25</font><br></td>
		</tr></table></td><td width=40></td></tr><tr style=\"height:10px;\"><td colspan=8></td></tr><tr><th colspan=7 style=\"height:25px;\">Ab Level 2</th><td></td></tr><tr style=\"height:10px;\"><td colspan=8></td></tr><tr><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Aluminiumwerk</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/13/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/1.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(1),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/6.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(6),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/7.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(7),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/8.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(8),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/9.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(9),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/10.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(10),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/18.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(18),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/19.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(19),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br><img src=../gfx/fields/45.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(45),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/81.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(81),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/82.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(82),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/83.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(83),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/200.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(200),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/203.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(203),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/205.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(205),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 25<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 1.8 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-6</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+4</font><br><img src=../gfx/goods/3.gif onMouseOver=\"showInfo(event,getGoodInfo(3),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-2</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 55</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Chemiefabrik</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/12/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/1.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(1),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/6.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(6),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/7.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(7),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/8.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(8),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/9.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(9),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/10.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(10),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/18.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(18),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/19.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(19),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br><img src=../gfx/fields/45.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(45),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/47.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(47),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/81.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(81),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/82.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(82),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/83.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(83),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/200.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(200),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/203.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(203),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/205.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(205),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br></div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 10<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 1.3 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-2</font><br><img src=../gfx/goods/3.gif onMouseOver=\"showInfo(event,getGoodInfo(3),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+2</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 45</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Deuterium-Extraktor</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/14/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/1.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(1),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/6.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(6),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/7.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(7),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/8.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(8),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/9.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(9),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/10.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(10),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/18.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(18),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/19.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(19),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br><img src=../gfx/fields/20.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(20),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/45.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(45),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/47.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(47),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/200.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(200),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/203.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(203),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/205.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(205),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 10<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 2 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-2</font><br><img src=../gfx/goods/7.gif onMouseOver=\"showInfo(event,getGoodInfo(7),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-4</font><br><img src=../gfx/goods/5.gif onMouseOver=\"showInfo(event,getGoodInfo(5),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+8</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 38</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 25</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Entsalzungsanlage</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/45/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/16.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(16),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/40.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(40),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/41.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(41),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/42.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(42),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/44.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(44),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 5<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 1.5 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-3</font><br><img src=../gfx/goods/7.gif onMouseOver=\"showInfo(event,getGoodInfo(7),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+8</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 25</font><br></td>
		</tr></table></td><td width=40></td></tr><tr><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Leichter Fusionsreaktor</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/16/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/1.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(1),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/6.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(6),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/7.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(7),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/8.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(8),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/9.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(9),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/10.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(10),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/18.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(18),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/19.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(19),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br><img src=../gfx/fields/45.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(45),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/47.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(47),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/81.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(81),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/82.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(82),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/83.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(83),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/200.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(200),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/203.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(203),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/205.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(205),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br></div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 10<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 2.5 Stunden<br><img src=../gfx/icons/eps.gif onMouseOver=\"showInfo(event,getMiscInfo('e'),this);\" onMouseOut=\"hideInfo();\"> 25<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+20</font><br><img src=../gfx/goods/5.gif onMouseOver=\"showInfo(event,getGoodInfo(5),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-4</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 63</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 38</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Wasserwerk</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/46/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/5.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(5),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 5<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 1.5 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-1</font><br><img src=../gfx/goods/7.gif onMouseOver=\"showInfo(event,getGoodInfo(7),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+8</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 25</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 10</font><br></td>
		</tr></table></td><td width=40></td></tr><tr style=\"height:10px;\"><td colspan=8></td></tr><tr><th colspan=7 style=\"height:25px;\">Ab Level 3</th><td></td></tr><tr style=\"height:10px;\"><td colspan=8></td></tr><tr><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Algenfarm</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/9/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/5.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(5),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/40.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(40),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/41.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(41),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/42.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(42),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/44.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(44),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 5<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 0.8 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-1</font><br><img src=../gfx/goods/1.gif onMouseOver=\"showInfo(event,getGoodInfo(1),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+4</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 25</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 20</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 5</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Deuterium-Synthesizer</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/19/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/1.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(1),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/6.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(6),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/7.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(7),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/8.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(8),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/9.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(9),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/10.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(10),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/18.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(18),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/19.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(19),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br><img src=../gfx/fields/20.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(20),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/45.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(45),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/47.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(47),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/200.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(200),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/203.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(203),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/205.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(205),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 5<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 2 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-2</font><br><img src=../gfx/goods/5.gif onMouseOver=\"showInfo(event,getGoodInfo(5),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+3</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 25</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 10</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Duraniumanlage</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/7/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/1.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(1),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/6.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(6),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/7.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(7),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/8.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(8),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/9.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(9),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/10.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(10),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/18.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(18),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/19.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(19),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br><img src=../gfx/fields/45.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(45),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/47.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(47),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/81.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(81),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/82.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(82),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/83.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(83),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/200.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(200),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/203.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(203),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/205.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(205),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br></div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 30<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 3 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-12</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+4</font><br><img src=../gfx/goods/11.gif onMouseOver=\"showInfo(event,getGoodInfo(11),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-12</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 100</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 50</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Eisschmelze</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/48/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/6.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(6),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/20.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(20),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 10<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 1.5 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-8</font><br><img src=../gfx/goods/7.gif onMouseOver=\"showInfo(event,getGoodInfo(7),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+8</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 60</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 75</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 75</font><br></td>
		</tr></table></td><td width=40></td></tr><tr><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Gezeitenkraftwerk</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/11/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/16.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(16),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/40.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(40),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/41.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(41),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/44.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(44),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 5<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 1.3 Stunden<br><img src=../gfx/icons/eps.gif onMouseOver=\"showInfo(event,getMiscInfo('e'),this);\" onMouseOut=\"hideInfo();\"> 10<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+4</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 25</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 20</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 5</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Häuser</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/29/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/1.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(1),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/9.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(9),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/18.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(18),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/19.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(19),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/45.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(45),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/47.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(47),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/blank/0f.png onMouseOver=\"showInfo(event,getMiscInfo('w'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 80<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 2 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-3</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 75</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 25</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 40</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Mine</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/15/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/31.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(31),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/32.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(32),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/33.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(33),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/34.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(34),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/35.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(35),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/36.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(36),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/82.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(82),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/201.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(201),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br></div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 10<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 2 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-2</font><br><img src=../gfx/goods/11.gif onMouseOver=\"showInfo(event,getGoodInfo(11),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+4</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 38</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 25</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Startrampe</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/8/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/1.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(1),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 2 Stunden<br></td>
	</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 63</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 63</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 25</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 25</font><br></td>
		</tr></table></td><td width=40></td></tr><tr style=\"height:10px;\"><td colspan=8></td></tr><tr><th colspan=7 style=\"height:25px;\">Ab Level 4</th><td></td></tr><tr style=\"height:10px;\"><td colspan=8></td></tr><tr><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Aeroponik-Station</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/52/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/100.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(100),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 1<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 3.8 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-1</font><br><img src=../gfx/goods/1.gif onMouseOver=\"showInfo(event,getGoodInfo(1),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+2</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 163</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 25</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 38</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 38</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Einfaches Gewächshaus</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/92/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/1.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(1),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/6.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(6),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/7.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(7),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/8.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(8),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/9.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(9),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/10.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(10),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/18.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(18),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/19.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(19),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br><img src=../gfx/fields/20.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(20),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/45.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(45),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/47.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(47),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 5<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 0.5 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-1</font><br><img src=../gfx/goods/1.gif onMouseOver=\"showInfo(event,getGoodInfo(1),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+2</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 25</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 10</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 10</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Hydroponik-Anlage</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/41/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/1.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(1),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/9.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(9),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/18.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(18),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/19.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(19),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/45.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(45),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/47.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(47),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 5<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 2.5 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-2</font><br><img src=../gfx/goods/1.gif onMouseOver=\"showInfo(event,getGoodInfo(1),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+5</font><br><img src=../gfx/goods/7.gif onMouseOver=\"showInfo(event,getGoodInfo(7),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-2</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 60</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 40</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Iglus</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/93/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/6.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(6),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/blank/0f.png onMouseOver=\"showInfo(event,getMiscInfo('w'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 20<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 0.4 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-1</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 25</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 15</font><br></td>
		</tr></table></td><td width=40></td></tr><tr><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Modulfabrik</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/40/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/1.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(1),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/6.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(6),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/7.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(7),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/8.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(8),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/9.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(9),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/10.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(10),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/18.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(18),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/19.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(19),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br><img src=../gfx/fields/45.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(45),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/200.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(200),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/203.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(203),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/205.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(205),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 3 Stunden<br><img src=../gfx/icons/storage.gif onMouseOver=\"showInfo(event,getMiscInfo('l'),this);\" onMouseOut=\"hideInfo();\"> 1000<br></td>
	</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 125</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 38</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 50</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Pumpenstation</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/47/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/47.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(47),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 10<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 1.5 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-6</font><br><img src=../gfx/goods/7.gif onMouseOver=\"showInfo(event,getGoodInfo(7),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+8</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 100</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 75</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Raumbahnhof</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/24/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/1.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(1),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/6.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(6),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/7.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(7),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/8.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(8),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/9.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(9),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/10.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(10),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/18.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(18),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/19.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(19),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br><img src=../gfx/fields/45.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(45),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/200.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(200),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/203.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(203),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 20<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 3 Stunden<br><img src=../gfx/icons/stopg.gif onMouseOver=\"showInfo(event,getMiscInfo('lc'),this);\" onMouseOut=\"hideInfo();\"> Max. pro Kolonie: 1<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-2</font><br><img src=../gfx/goods/5.gif onMouseOver=\"showInfo(event,getGoodInfo(5),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-4</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 125</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 63</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 25</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 25</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Schaltkreis-Fabrik</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/86/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/1.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(1),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/6.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(6),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/7.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(7),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/8.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(8),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/9.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(9),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/10.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(10),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/18.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(18),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/19.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(19),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br><img src=../gfx/fields/20.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(20),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/45.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(45),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/47.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(47),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/200.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(200),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/203.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(203),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/205.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(205),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 30<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 3 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-12</font><br><img src=../gfx/goods/9.gif onMouseOver=\"showInfo(event,getGoodInfo(9),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-4</font><br><img src=../gfx/goods/30.gif onMouseOver=\"showInfo(event,getGoodInfo(30),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+3</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 100</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 75</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 25</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 25</font><br></td>
		</tr></table></td><td width=40></td></tr><tr><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Schwerer Fusionsreaktor</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/17/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/1.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(1),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/6.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(6),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/7.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(7),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/8.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(8),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/9.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(9),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/10.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(10),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/18.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(18),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/19.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(19),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br><img src=../gfx/fields/45.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(45),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/81.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(81),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/82.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(82),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/83.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(83),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/200.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(200),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/203.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(203),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/205.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(205),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 25<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 4 Stunden<br><img src=../gfx/icons/eps.gif onMouseOver=\"showInfo(event,getMiscInfo('e'),this);\" onMouseOut=\"hideInfo();\"> 50<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+36</font><br><img src=../gfx/goods/5.gif onMouseOver=\"showInfo(event,getGoodInfo(5),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-8</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 100</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 25</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 63</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Schürfstation</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/85/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/7.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(7),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/8.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(8),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/9.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(9),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/18.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(18),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/19.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(19),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 10<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 2 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-2</font><br><img src=../gfx/goods/9.gif onMouseOver=\"showInfo(event,getGoodInfo(9),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+2</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 80</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 40</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 20</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Solarkomplex</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/10/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/7.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(7),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/8.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(8),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/12.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(12),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/19.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(19),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/47.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(47),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 10<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 1.8 Stunden<br><img src=../gfx/icons/eps.gif onMouseOver=\"showInfo(event,getMiscInfo('e'),this);\" onMouseOut=\"hideInfo();\"> 25<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+8</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 75</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 25</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 25</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Solarsatellit</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/50/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/100.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(100),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 1<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 3 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+3</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 163</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 25</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 38</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 38</font><br></td>
		</tr></table></td><td width=40></td></tr><tr><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Strömungsturbinen</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/21/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/16.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(16),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/40.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(40),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/41.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(41),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/44.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(44),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 10<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 1.8 Stunden<br><img src=../gfx/icons/eps.gif onMouseOver=\"showInfo(event,getMiscInfo('e'),this);\" onMouseOut=\"hideInfo();\"> 25<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+8</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 75</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 25</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 25</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 38</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Werft</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/51/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/100.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(100),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 30<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 7.5 Stunden<br><img src=../gfx/icons/stopr.gif onMouseOver=\"showInfo(event,getMiscInfo('lg'),this);\" onMouseOut=\"hideInfo();\"> Max. pro Spieler: 6<br><img src=../gfx/icons/stopg.gif onMouseOver=\"showInfo(event,getMiscInfo('lc'),this);\" onMouseOut=\"hideInfo();\"> Max. pro Kolonie: 1<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-10</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 200</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 125</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Windturbinen</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/26/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/18.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(18),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 10<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 1.8 Stunden<br><img src=../gfx/icons/eps.gif onMouseOver=\"showInfo(event,getMiscInfo('e'),this);\" onMouseOut=\"hideInfo();\"> 25<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+8</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 75</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 25</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 25</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 38</font><br></td>
		</tr></table></td><td width=40></td></tr><tr style=\"height:10px;\"><td colspan=8></td></tr><tr><th colspan=7 style=\"height:25px;\">Ab Level 5</th><td></td></tr><tr style=\"height:10px;\"><td colspan=8></td></tr><tr><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Bilitrium-Förderanlage</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/65/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/33.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(33),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 10<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 2 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-2</font><br><img src=../gfx/goods/45.gif onMouseOver=\"showInfo(event,getGoodInfo(45),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+1</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 50</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Cortenit-Förderanlage</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/64/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/10.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(10),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/36.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(36),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 10<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 2 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-2</font><br><img src=../gfx/goods/44.gif onMouseOver=\"showInfo(event,getGoodInfo(44),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+1</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 50</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Dilithium-Mine</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/20/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/31.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(31),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/32.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(32),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/33.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(33),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/34.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(34),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/35.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(35),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/36.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(36),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/81.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(81),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/82.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(82),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br><img src=../gfx/fields/201.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(201),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 10<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 2 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-2</font><br><img src=../gfx/goods/8.gif onMouseOver=\"showInfo(event,getGoodInfo(8),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+1</font><br><img src=../gfx/icons/psupply.gif onMouseOver=\"showInfo(event,getMiscInfo('psupply'),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#00ff00\"/>+25</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 38</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 25</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 25</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Einfacher Replikator</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/49/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/1.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(1),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/6.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(6),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/7.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(7),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/8.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(8),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/9.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(9),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/10.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(10),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/18.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(18),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/19.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(19),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br><img src=../gfx/fields/20.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(20),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/45.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(45),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/47.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(47),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/200.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(200),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/203.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(203),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/205.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(205),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 5<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 2.2 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-3</font><br><img src=../gfx/goods/1.gif onMouseOver=\"showInfo(event,getGoodInfo(1),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+6</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-2</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 70</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 75</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 75</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 75</font><br></td>
		</tr></table></td><td width=40></td></tr><tr><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Emitter-Fabrik</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/87/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/1.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(1),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/6.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(6),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/7.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(7),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/8.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(8),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/9.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(9),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/10.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(10),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/18.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(18),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/19.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(19),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br><img src=../gfx/fields/20.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(20),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/45.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(45),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/47.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(47),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/200.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(200),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/203.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(203),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/205.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(205),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 20<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 4.5 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-5</font><br><img src=../gfx/goods/11.gif onMouseOver=\"showInfo(event,getGoodInfo(11),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-12</font><br><img src=../gfx/goods/45.gif onMouseOver=\"showInfo(event,getGoodInfo(45),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-1</font><br><img src=../gfx/goods/35.gif onMouseOver=\"showInfo(event,getGoodInfo(35),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+2</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 188</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 125</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 63</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 125</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Habitat-Kuppel</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/22/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/1.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(1),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/5.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(5),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/6.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(6),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/7.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(7),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/8.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(8),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/9.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(9),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/10.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(10),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/12.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(12),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br><img src=../gfx/fields/16.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(16),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/18.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(18),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/19.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(19),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/20.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(20),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/40.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(40),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/41.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(41),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/44.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(44),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/45.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(45),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br><img src=../gfx/fields/47.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(47),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/81.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(81),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/82.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(82),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/83.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(83),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/84.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(84),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/200.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(200),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/203.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(203),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/205.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(205),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br></div></td>
	</tr>
	<tr><td><img src=../gfx/bev/blank/0f.png onMouseOver=\"showInfo(event,getMiscInfo('w'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 60<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 5 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-3</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 100</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 25</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 63</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 63</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Hydroponik-Kuppel</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/23/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/1.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(1),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/5.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(5),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/6.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(6),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/7.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(7),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/8.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(8),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/9.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(9),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/10.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(10),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/12.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(12),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br><img src=../gfx/fields/16.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(16),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/18.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(18),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/19.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(19),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/20.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(20),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/40.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(40),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/41.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(41),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/44.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(44),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/45.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(45),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br><img src=../gfx/fields/47.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(47),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/81.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(81),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/82.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(82),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/83.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(83),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/84.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(84),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/200.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(200),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/203.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(203),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/205.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(205),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br></div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 5<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 5 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-2</font><br><img src=../gfx/goods/1.gif onMouseOver=\"showInfo(event,getGoodInfo(1),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+7</font><br><img src=../gfx/goods/7.gif onMouseOver=\"showInfo(event,getGoodInfo(7),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-4</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 100</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 25</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 63</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 63</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Isolinear-Chip-Werk</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/31/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/1.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(1),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/6.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(6),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/7.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(7),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/8.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(8),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/9.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(9),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/10.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(10),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/18.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(18),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/19.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(19),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br><img src=../gfx/fields/45.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(45),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/200.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(200),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/203.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(203),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/205.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(205),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 20<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 4.5 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-5</font><br><img src=../gfx/goods/3.gif onMouseOver=\"showInfo(event,getGoodInfo(3),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-2</font><br><img src=../gfx/goods/11.gif onMouseOver=\"showInfo(event,getGoodInfo(11),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-8</font><br><img src=../gfx/goods/41.gif onMouseOver=\"showInfo(event,getGoodInfo(41),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-1</font><br><img src=../gfx/goods/31.gif onMouseOver=\"showInfo(event,getGoodInfo(31),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+2</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 188</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 125</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 63</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 125</font><br></td>
		</tr></table></td><td width=40></td></tr><tr><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Kemocite-Förderanlage</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/62/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/34.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(34),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 10<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 2 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-2</font><br><img src=../gfx/goods/42.gif onMouseOver=\"showInfo(event,getGoodInfo(42),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+1</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 50</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Metaphasen-Konverter-Fabrik</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/33/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/1.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(1),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/6.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(6),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/7.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(7),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/8.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(8),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/9.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(9),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/10.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(10),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/18.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(18),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/19.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(19),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br><img src=../gfx/fields/45.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(45),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/200.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(200),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/203.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(203),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/205.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(205),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 20<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 4.5 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-5</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-5</font><br><img src=../gfx/goods/11.gif onMouseOver=\"showInfo(event,getGoodInfo(11),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-8</font><br><img src=../gfx/goods/43.gif onMouseOver=\"showInfo(event,getGoodInfo(43),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-1</font><br><img src=../gfx/goods/33.gif onMouseOver=\"showInfo(event,getGoodInfo(33),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+2</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 188</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 125</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 63</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 125</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Nitrium-Förderanlage</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/61/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/32.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(32),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 10<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 2 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-2</font><br><img src=../gfx/goods/41.gif onMouseOver=\"showInfo(event,getGoodInfo(41),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+1</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 50</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Orbital-Habitat</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/53/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/100.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(100),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/blank/0f.png onMouseOver=\"showInfo(event,getMiscInfo('w'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 30<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 4.5 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-2</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 163</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 25</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 50</font><br></td>
		</tr></table></td><td width=40></td></tr><tr><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Photonentorpedo-Fabrik</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/80/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/1.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(1),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/6.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(6),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/7.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(7),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/8.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(8),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/9.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(9),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/10.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(10),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/18.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(18),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/19.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(19),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br><img src=../gfx/fields/45.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(45),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/200.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(200),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/203.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(203),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 25<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 3 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-8</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-4</font><br><img src=../gfx/goods/8.gif onMouseOver=\"showInfo(event,getGoodInfo(8),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-1</font><br><img src=../gfx/goods/5.gif onMouseOver=\"showInfo(event,getGoodInfo(5),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-4</font><br><img src=../gfx/goods/6.gif onMouseOver=\"showInfo(event,getGoodInfo(6),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-4</font><br><img src=../gfx/goods/30.gif onMouseOver=\"showInfo(event,getGoodInfo(30),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-3</font><br><img src=../gfx/goods/81.gif onMouseOver=\"showInfo(event,getGoodInfo(81),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+4</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 200</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 75</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 100</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 113</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Planetarer Warpkern</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/38/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/1.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(1),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/6.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(6),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/7.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(7),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/8.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(8),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/9.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(9),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/10.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(10),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/18.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(18),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/19.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(19),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br><img src=../gfx/fields/45.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(45),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/200.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(200),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/203.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(203),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 30<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 5 Stunden<br><img src=../gfx/icons/eps.gif onMouseOver=\"showInfo(event,getMiscInfo('e'),this);\" onMouseOut=\"hideInfo();\"> 100<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+100</font><br><img src=../gfx/goods/8.gif onMouseOver=\"showInfo(event,getGoodInfo(8),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-1</font><br><img src=../gfx/goods/5.gif onMouseOver=\"showInfo(event,getGoodInfo(5),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-2</font><br><img src=../gfx/goods/6.gif onMouseOver=\"showInfo(event,getGoodInfo(6),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-2</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 125</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 75</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 88</font><br><img src=../gfx/goods/8.gif onMouseOver=\"showInfo(event,getGoodInfo(8),this);\" onMouseOut=\"hideInfo();\"> 13</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Plasma-Konverter</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/32/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/1.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(1),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/6.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(6),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/7.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(7),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/8.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(8),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/9.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(9),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/10.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(10),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/18.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(18),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/19.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(19),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br><img src=../gfx/fields/45.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(45),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/200.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(200),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/203.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(203),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/205.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(205),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 20<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 4.5 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-5</font><br><img src=../gfx/goods/5.gif onMouseOver=\"showInfo(event,getGoodInfo(5),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-8</font><br><img src=../gfx/goods/3.gif onMouseOver=\"showInfo(event,getGoodInfo(3),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-2</font><br><img src=../gfx/goods/42.gif onMouseOver=\"showInfo(event,getGoodInfo(42),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-1</font><br><img src=../gfx/goods/32.gif onMouseOver=\"showInfo(event,getGoodInfo(32),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+2</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 188</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 125</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 63</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 125</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Plasmatorpedo-Fabrik</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/81/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/1.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(1),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/6.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(6),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/7.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(7),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/8.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(8),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/9.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(9),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/10.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(10),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/18.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(18),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/19.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(19),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br><img src=../gfx/fields/45.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(45),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/200.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(200),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/203.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(203),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 25<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 3 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-8</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-4</font><br><img src=../gfx/goods/8.gif onMouseOver=\"showInfo(event,getGoodInfo(8),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-1</font><br><img src=../gfx/goods/5.gif onMouseOver=\"showInfo(event,getGoodInfo(5),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-4</font><br><img src=../gfx/goods/30.gif onMouseOver=\"showInfo(event,getGoodInfo(30),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-3</font><br><img src=../gfx/goods/32.gif onMouseOver=\"showInfo(event,getGoodInfo(32),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-1</font><br><img src=../gfx/goods/82.gif onMouseOver=\"showInfo(event,getGoodInfo(82),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+4</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 200</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 75</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 100</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 113</font><br></td>
		</tr></table></td><td width=40></td></tr><tr><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Schule</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/91/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/1.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(1),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/45.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(45),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 20<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 2 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-2</font><br><img src=../gfx/icons/pcrew.gif onMouseOver=\"showInfo(event,getMiscInfo('pcrew'),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#00ff00\"/>+40</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 20</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 20</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 15</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Schwimmende Siedlung</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/55/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/5.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(5),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/40.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(40),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/41.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(41),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/44.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(44),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/blank/0f.png onMouseOver=\"showInfo(event,getMiscInfo('w'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 120<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 4.5 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-6</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 200</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 60</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 100</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Siedlung</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/30/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/1.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(1),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/9.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(9),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/18.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(18),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/19.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(19),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/45.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(45),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/47.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(47),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/blank/0f.png onMouseOver=\"showInfo(event,getMiscInfo('w'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 120<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 3.8 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-6</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 163</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 75</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 50</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Subraum-Spulen-Fabrik</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/34/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/1.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(1),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/6.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(6),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/7.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(7),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/8.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(8),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/9.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(9),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/10.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(10),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/18.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(18),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/19.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(19),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br><img src=../gfx/fields/45.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(45),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/200.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(200),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/203.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(203),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/205.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(205),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 20<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 4.5 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-5</font><br><img src=../gfx/goods/11.gif onMouseOver=\"showInfo(event,getGoodInfo(11),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-12</font><br><img src=../gfx/goods/44.gif onMouseOver=\"showInfo(event,getGoodInfo(44),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-1</font><br><img src=../gfx/goods/34.gif onMouseOver=\"showInfo(event,getGoodInfo(34),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+2</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 188</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 125</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 63</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 125</font><br></td>
		</tr></table></td><td width=40></td></tr><tr><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Teilchenbeschleuniger</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/25/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/1.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(1),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/6.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(6),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/7.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(7),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/8.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(8),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/9.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(9),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/10.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(10),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/12.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(12),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/18.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(18),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br><img src=../gfx/fields/19.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(19),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/45.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(45),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/47.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(47),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/81.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(81),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/82.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(82),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/83.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(83),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/200.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(200),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/203.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(203),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br><img src=../gfx/fields/205.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(205),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 15<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 4 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-8</font><br><img src=../gfx/goods/5.gif onMouseOver=\"showInfo(event,getGoodInfo(5),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-4</font><br><img src=../gfx/goods/6.gif onMouseOver=\"showInfo(event,getGoodInfo(6),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+2</font><br><img src=../gfx/icons/psupply.gif onMouseOver=\"showInfo(event,getMiscInfo('psupply'),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#00ff00\"/>+15</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 100</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 38</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 38</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 38</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Tritanium-Förderanlage</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/66/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/201.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(201),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 10<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 2 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-2</font><br><img src=../gfx/goods/19.gif onMouseOver=\"showInfo(event,getGoodInfo(19),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+1</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 50</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Tritaniumanlage</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/88/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/1.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(1),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/6.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(6),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/7.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(7),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/8.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(8),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/9.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(9),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/10.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(10),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/18.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(18),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/19.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(19),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br><img src=../gfx/fields/45.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(45),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/47.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(47),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/200.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(200),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/203.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(203),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/205.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(205),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 20<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 4.5 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-5</font><br><img src=../gfx/goods/11.gif onMouseOver=\"showInfo(event,getGoodInfo(11),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-12</font><br><img src=../gfx/goods/19.gif onMouseOver=\"showInfo(event,getGoodInfo(19),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-1</font><br><img src=../gfx/goods/29.gif onMouseOver=\"showInfo(event,getGoodInfo(29),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+2</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 188</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 125</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 63</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 125</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Verterium-Förderanlage</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/63/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/31.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(31),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 10<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 2 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-2</font><br><img src=../gfx/goods/43.gif onMouseOver=\"showInfo(event,getGoodInfo(43),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+1</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 50</font><br></td>
		</tr></table></td><td width=40></td></tr><tr><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Vulkan-Schmelze</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/43/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/204.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(204),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 20<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 6 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>0</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+4</font><br><img src=../gfx/goods/11.gif onMouseOver=\"showInfo(event,getGoodInfo(11),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-12</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 125</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 75</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 125</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Wartungsstation</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/56/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/100.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(100),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 10<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 6.1 Stunden<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-5</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-2</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#ff6666\"/>-3</font><br><img src=../gfx/icons/pmaintain.gif onMouseOver=\"showInfo(event,getMiscInfo('pmaintain'),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#00ff00\"/>+70</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 150</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 30</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 50</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 80</font><br></td>
		</tr></table></td><td width=40></td><td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>Wärmekraftwerk</th>
	</tr>
	<tr>
		<td><div align=center><img src=../gfx/buildings/27/0.png></div><br><div  style=\"padding-left:20px;\"><img src=../gfx/fields/205.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(205),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/223.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(223),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/225.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(225),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/226.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(226),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/229.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(229),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/230.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(230),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/232.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(232),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/241.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(241),this);\" onMouseOut=\"hideInfo();\">&nbsp;<br><img src=../gfx/fields/242.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(242),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/243.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(243),this);\" onMouseOut=\"hideInfo();\">&nbsp;<img src=../gfx/fields/244.gif width=16 height=16 onMouseOver=\"showInfo(event,getFieldInfo(244),this);\" onMouseOut=\"hideInfo();\">&nbsp;</div></td>
	</tr>
	<tr><td><img src=../gfx/bev/crew/".$_SESSION['race']."m.png onMouseOver=\"showInfo(event,getMiscInfo('a'),this);\" onMouseOut=\"hideInfo();\" style=\"width:24px;height:24px;\"> 10<br><img src=../gfx/icons/clock.gif onMouseOver=\"showInfo(event,getMiscInfo('t'),this);\" onMouseOut=\"hideInfo();\"> 3 Stunden<br><img src=../gfx/icons/eps.gif onMouseOver=\"showInfo(event,getMiscInfo('e'),this);\" onMouseOut=\"hideInfo();\"> 25<br></td>
	</tr><tr>
		<td><u>Produktion / Verbrauch</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> <font color=\"#66ff66\"/>+12</font><br></td>
		</tr><tr>
		<td><u>Baukosten</u><br><img src=../gfx/goods/0.gif onMouseOver=\"showInfo(event,getGoodInfo(0),this);\" onMouseOut=\"hideInfo();\"> 100</font><br><img src=../gfx/goods/2.gif onMouseOver=\"showInfo(event,getGoodInfo(2),this);\" onMouseOut=\"hideInfo();\"> 25</font><br><img src=../gfx/goods/4.gif onMouseOver=\"showInfo(event,getGoodInfo(4),this);\" onMouseOut=\"hideInfo();\"> 25</font><br><img src=../gfx/goods/21.gif onMouseOver=\"showInfo(event,getGoodInfo(21),this);\" onMouseOut=\"hideInfo();\"> 50</font><br></td>
		</tr></table></td><td width=40></td></tr><tr style=\"height:10px;\"><td colspan=8></td></tr><tr><th colspan=7 style=\"height:25px;\">Nach Forschung</th><td></td></tr><tr style=\"height:10px;\"><td colspan=8></td></tr></tr>";
 ?>