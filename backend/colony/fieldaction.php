<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../../inc/func.inc.php");
include_once("../../inc/config.inc.php");
include_once("../../class/db.class.php");
$db = new db;
@session_start();

if ($_SESSION['login'] != 1) exit;
if (!check_int($_GET['id']) || !check_int($_GET['fid'])) exit;

$gfx = $_SESSION['gfx_path'];
if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "../../gfx/";

$col = $db->query("SELECT id,name,gravitation,colonies_classes_id FROM stu_colonies WHERE id=".$_GET['id']." AND user_id=".$_SESSION['uid']." LIMIT 1",4);
if ($col == 0) exit;

$field = $db->query("SELECT a.field_id,a.type,a.buildings_id,a.integrity,a.aktiv,b.name,b.integrity as maxintegrity,b.is_activateable FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.field_id=".$_GET['fid']." AND a.colonies_id=".$_GET['id']." LIMIT 1",4);
if ($field == 0) exit;

$content .= "<table class=\"tcal\" style=\"width:100%;\" >
	<tr>
	<td style=\"width: 200px;\" valign=\"top\">";
	if ($field['buildings_id'] > 0)
	{
		$contentwidth = 400;
		$content .= composeBuildingInfo($field['buildings_id'],0,$col['colonies_classes_id']);
		$content .= "</td><td valign=\"top\">";
		
		
		$content .= "<table width=100%>";  
		
		$content .= "<tr><th style=\"padding:4px; text-align:center;\">Gebäudefunktionen</th></tr>";
		$content .= "<tr><td style=\"padding:4px;\"><img src=".$gfx."/buttons/icon/armor.gif border=0> Integrität: ".$field['integrity']." / ".$field['maxintegrity']."</td></tr>";

		if ($field['is_activateable'] == 1 && $field['aktiv'] == 1) 
			$content .= "<tr><td style=\"padding:4px;\"><a href=?ps=".$_SESSION['pagesess']."&p=colony&s=sc&id=".$_GET['id']."&a=db&fid=".$_GET['fid']." ".getHover("bact","inactive/n/energy","hover/r/energy")."><img src=".$gfx."/buttons/inactive/n/energy.gif name=bact title='Gebäude deaktivieren' border=0> Deaktivieren</a></td></tr>";
		if ($field['is_activateable'] == 1 && $field['aktiv'] == 0) 
			$content .= "<tr><td style=\"padding:4px;\"><a href=?ps=".$_SESSION['pagesess']."&p=colony&s=sc&id=".$_GET['id']."&a=ab&fid=".$_GET['fid']." ".getHover("bact","inactive/n/energy","hover/g/energy")."><img src=".$gfx."/buttons/inactive/n/energy.gif name=bact title='Gebäude aktivieren' border=0> Aktivieren</a></td></tr>";
		
		if ($field['maxintegrity'] > $field['integrity']) 
			$content .= "<tr><td style=\"padding:4px;\"><a href=?p=colony&s=rep&id=".$_GET['id']."&fid=".$_GET['fid']." ".getHover("brep","inactive/n/repair","hover/g/repair")."><img src=".$gfx."/buttons/inactive/n/repair.gif name=brep title='Gebäude reparieren' border=0> Reparieren</a></td></tr>";
		
		
		
		
		if ($field['buildings_id'] == 1)
		{
			$content .= "<tr><td style=\"padding:4px;\"><a href=?p=colony&s=sc&id=".$_GET['id']."&a=nmode&ps=".$_SESSION['pagesess']." ".getHover("bm1","inactive/n/options","hover/w/options")."><img src=".$gfx."/buttons/inactive/n/options.gif name=bm1 title='Modus ändern' border=0> Produktion zu Nahrung ändern</a></td></tr>";
			$content .= "<tr><td style=\"padding:4px;\"><a href=?p=colony&s=sc&id=".$_GET['id']."&a=emode&ps=".$_SESSION['pagesess']." ".getHover("bm2","inactive/n/options","hover/w/options")."><img src=".$gfx."/buttons/inactive/n/options.gif name=bm2 title='Modus ändern' border=0> Produktion zu Energie ändern</a></td></tr>";
		}
		if ($field['buildings_id'] == 101)
		{
			$content .= "<tr><td style=\"padding:4px;\"><a href=?p=colony&s=sc&id=".$_GET['id']."&a=bmode&ps=".$_SESSION['pagesess']." ".getHover("bm1","inactive/n/options","hover/w/options")."><img src=".$gfx."/buttons/inactive/n/options.gif name=bm1 title='Modus ändern' border=0> Produktion zu Baumaterial ändern</a></td></tr>";
			$content .= "<tr><td style=\"padding:4px;\"><a href=?p=colony&s=sc&id=".$_GET['id']."&a=emode&ps=".$_SESSION['pagesess']." ".getHover("bm2","inactive/n/options","hover/w/options")."><img src=".$gfx."/buttons/inactive/n/options.gif name=bm2 title='Modus ändern' border=0> Produktion zu Energie ändern</a></td></tr>";
		}
		if ($field['buildings_id'] == 102)
		{
			$content .= "<tr><td style=\"padding:4px;\"><a href=?p=colony&s=sc&id=".$_GET['id']."&a=bmode&ps=".$_SESSION['pagesess']." ".getHover("bm1","inactive/n/options","hover/w/options")."><img src=".$gfx."/buttons/inactive/n/options.gif name=bm1 title='Modus ändern' border=0> Produktion zu Baumaterial ändern</a></td></tr>";
			$content .= "<tr><td style=\"padding:4px;\"><a href=?p=colony&s=sc&id=".$_GET['id']."&a=nmode&ps=".$_SESSION['pagesess']." ".getHover("bm2","inactive/n/options","hover/w/options")."><img src=".$gfx."/buttons/inactive/n/options.gif name=bm2 title='Modus ändern' border=0> Produktion zu Nahrung ändern</a></td></tr>";
		}		
		
		
		
		
		
		
		
		if ($field['buildings_id'] == 51 && $field['aktiv'] == 1)
		{
			$content .= "<tr><td style=\"padding:4px;\"><a href=?p=colony&s=sb&id=".$_GET['id']." ".getHover("bsbl","inactive/n/ship","hover/w/ship")."><img src=".$gfx."/buttons/inactive/n/ship.gif name=bsbl title='Schiffbau' border=0> Schiffbau</a></td></tr>";
			$content .= "<tr><td style=\"padding:4px;\"><a href=?p=colony&s=resp&id=".$_GET['id']." ".getHover("bsrp","inactive/n/repair","hover/g/repair")."><img src=".$gfx."/buttons/inactive/n/repair.gif name=bsrp title='Schiffsreparatur' border=0> Schiffsreparatur</a></td></tr>";
			$content .= "<tr><td style=\"padding:4px;\"><a href=?p=colony&s=dmt&id=".$_GET['id']." ".getHover("bsdm","inactive/n/shipparts","hover/r/shipparts")."><img src=".$gfx."/buttons/inactive/n/shipparts.gif name=bsdm title='Schiffsdemontage' border=0> Schiffsdemontage</a></td></tr>";
		// $content .= "<br><table><tr><td colspan=3 style=\"border : 1px solid #262323;\"><b>Werftfunktionen</b></td></tr>
		// <tr>
		// <td style=\"border : 1px solid #262323;\"><a href=?p=colony&s=sb&id=".$_GET['id']." ".getonm('sba','buttons/builds')."><img src=".$gfx."/buttons/builds1.gif name=sba border=0 title=\"Schiffbau\"> Schiffbau</a></td>
		// <td style=\"border : 1px solid #262323;\"><a href=?p=colony&s=resp&id=".$_GET['id']." ".getonm('sre','buttons/rep')."><img src=".$gfx."/buttons/rep1.gif border=0 name=sre title=\"Schiffsreparatur\"> Schiffsreparatur</a></td>
		// <td style=\"border : 1px solid #262323;\"><a href=?p=colony&s=dmt&id=".$_GET['id']." ".getonm('dem','buttons/demship')."><img src=".$gfx."/buttons/demship1.gif border=0 name=dem title=\"Schiffsdemontage\"> Schiffsdemontage</a></td>
		// </tr><tr>
		// <td style=\"border : 1px solid #262323;\"><a href=?p=colony&s=bat&id=".$_GET['id']." ".getonm("batt","buttons/battp")."><img src=".$gfx."/buttons/battp1.gif name=batt title=\"Ersatzbatterie laden\" border=0> Ersatzbatterie</a></td>

		// <td style=\"border : 1px solid #262323;\"></td>
		// <td style=\"border : 1px solid #262323;\"></td>
		// </tr>
		// </table>";
		}
		
		if ($field['buildings_id'] == 40 && $field['aktiv'] < 2)
			$content .= "<tr><td style=\"padding:4px;\"><a href=?p=colony&s=mor&id=".$_GET['id']."&fid=".$_GET['fid']." ".getHover("bmdl","inactive/n/list","hover/g/list")."><img src=".$gfx."/buttons/inactive/n/list.gif name=bmdl title='Modulherstellung' border=0> Modulherstellung</a></td></tr>";


		if ($field['buildings_id'] == 4 && $field['aktiv'] < 2)
			$content .= "<tr><td style=\"padding:4px;\"><a href=?p=colony&s=trs&id=".$_GET['id']."&fid=".$_GET['fid']." ".getHover("bjnk","inactive/n/dooropen","hover/r/dooropen")."><img src=".$gfx."/buttons/inactive/n/dooropen.gif name=bjnk title='Abfallentsorgung' border=0> Abfallentsorgung</a></td></tr>";
		
		if ($field['buildings_id'] == 8 && $_SESSION['level'] < 5)
		{
			$content .= "<tr><td style=\"padding:4px;\"><a href=?ps=".$_SESSION['pagesess']."&p=colony&s=sc&id=".$_GET['id']."&a=bcs ".getHover("bcsp","inactive/n/ship","hover/g/ship")."><img src=".$gfx."/buttons/inactive/n/ship.gif name=bcsp title='Kolonieschiff bauen' border=0> Kolonieschiff bauen</a></td></tr>";								
			$content .= "<tr><td style=\"padding:4px;\">Kosten: ".goodPic(0)."x50 ".goodPic(2)."x50 ".goodPic(4)."x25 ".goodPic(21)."x25 ".goodPic(5)."x50</td></tr>";
		}	
		
		
		
		
		
		
		
		
		
		
		
		if (!iscolcent($col->fdd['buildings_id'])) {
			// $content .= "<td style=\"width: 130px; border : 1px solid #9C1417; margin-left: 3px;\"><a href=\"javascript:void(0);\" onClick=\"showConfirm(".$_GET['fid'].");\"><img src=".$gfx."/buttons/demont.gif border=0><font color=#FF0000> Demontieren</font></a></span>";
			// $content .= "<tr><td style=\"padding:4px;\"><a href=?p=colony&s=bm&id=".$_GET['id']."&fid=".$_GET['fid']." ".getHover("bbmn","inactive/n/text","hover/w/text")."><img src=".$gfx."/buttons/inactive/n/text.gif name=bbmn title='Baumenü' border=0> Gebäude ersetzen</a></td></tr>";
			
			if ($field['aktiv'] < 2) {
				$content .= "<tr><td style=\"padding:4px;\"><a href=\"javascript:void(0);\" onClick=\"document.getElementById('dmc').style.visibility = 'visible';this.style.visibility = 'hidden';\" ".getHover("bdmt","inactive/r/destruct","hover/r/destruct")."><img src=".$gfx."/buttons/inactive/r/destruct.gif name=bdmt title='Gebäude demontieren' border=0> Demontieren</a></td></tr>";
				$content .= "<tr><td style=\"padding:4px;\"><a id=dmc style=\"visibility:hidden;\" href=?ps=".$_SESSION['pagesess']."&p=colony&s=sc&id=".$_GET['id']."&a=dmb&fid=".$_GET['fid']." ".getHover("bdmt2","inactive/r/info","hover/r/info")."><img src=".$gfx."/buttons/inactive/r/info.gif name=bdmt2 title='Gebäude demontieren' border=0> Demontieren bestätigen</a></td></tr>";
			} else {
				$content .= "<tr><td style=\"padding:4px;\"><a id=dmc href=?ps=".$_SESSION['pagesess']."&p=colony&s=sc&id=".$_GET['id']."&a=dmb&fid=".$_GET['fid']." ".getHover("bdmt2","inactive/r/exclamation","hover/r/exclamation")."><img src=".$gfx."/buttons/inactive/r/exclamation.gif name=bdmt2 title='Bau abbrechen' border=0> Bau abbrechen</a></td></tr>";
			}
		}
		
		if ($field['aktiv'] > 1 && $field['is_activateable'] == 1) {
			
			$after = $db->query("SELECT value FROM stu_colonies_actions WHERE var='db' AND colonies_id=".$_GET['id']." AND value='".$_GET["fid"]."'",1);
			
			if ($after == 0) {
				$content .= "<tr><td style=\"padding:4px;\"><img src=".$gfx."/buttons/icon/energy.gif border=0> Aktiviert nach Bau: <b>ja</b></td></tr>";	
				$content .= "<tr><td style=\"padding:4px;\"><a href=?p=colony&s=sc&a=sam&fid=".$_GET["fid"]."&id=".$_GET['id']." ".getHover("bggh","inactive/n/energy","hover/r/energy")."><img src=".$gfx."/buttons/inactive/n/energy.gif name=bggh title='Nach Bau deaktivieren' border=0> Nach Bau deaktivieren</a></td></tr>";				
			} else {
				$content .= "<tr><td style=\"padding:4px;\"><img src=".$gfx."/buttons/icon/energy.gif border=0> Aktiviert nach Bau: <b>nein</b></td></tr>";
				$content .= "<tr><td style=\"padding:4px;\"><a href=?p=colony&s=sc&a=sam&fid=".$_GET["fid"]."&id=".$_GET['id']." ".getHover("bggh","inactive/n/energy","hover/g/energy")."><img src=".$gfx."/buttons/inactive/n/energy.gif name=bggh title='Nach Bau aktivieren' border=0> Nach Bau aktivieren</a></td></tr>";								
			}
			
			
			
		}
		
		
		

		
		
		
		
		
			// $content .= "<tr><td style=\"padding:4px;\"><a id=dmc href=?ps=".$_SESSION['pagesess']."&p=colony&s=sc&id=".$_GET['id']."&a=dmb&fid=".$_GET['fid']." ".getHover("bdmt2","inactive/r/exclamation","hover/r/exclamation")."><img src=".$gfx."/buttons/inactive/r/exclamation.gif name=bdmt2 title='Bau abbrechen' border=0> Bau abbrechen</a></td></tr>";
			// $content .= "<br><table><tr><td style=\"border : 1px solid #262323;\"><b>Aktivierungseinstellung</b></td></tr><tr><td style=\"border : 1px solid #262323;\">Aktiviert nach Bau? ".($db->query("SELECT value FROM stu_colonies_actions WHERE var='db' AND colonies_id=".$_GET['id']." AND value='".$_GET["fid"]."'",1) > 0 ? "<a href=?p=colony&s=sc&a=sam&fid=".$_GET["fid"]."&id=".$_GET['id'].">Nein</a>" : "<a href=?p=colony&s=sc&a=sam&fid=".$_GET["fid"]."&id=".$_GET['id'].">Ja</a>")."</td></tr></table>";
		
		
		
		
		
		
		
		
		
		
		
		$content .= "</table>";








		// $content .= "</tr></table><table><tr><td style=\"width: 130px; border : 1px solid #262323;\"><a href=?p=colony&s=bm&id=".$_GET['id']."&fid=".$_GET['fid']." ".getonm('bmt','buttons/notiz')."><img src=".$gfx."/buttons/notiz1.gif border=0 name=bmt title=\"Baumenü\"> Baumenü</a></td>";





		// $content .= "</tr></table>";


		// if ($field['buildings_id'] == 4) $content .= "<table><tr><td style=\"width: 130px; border : 1px solid #262323;\"><a href=?p=colony&s=trs&id=".$_GET['id']."&fid=".$_GET['fid']." ".getonm('mor','buttons/x')."><img src=".$gfx."/buttons/x1.gif name=mor border=0 title=\"Abfallentsorgung\"> Abfallentsorgung</a></td></tr></table>";
		
		
	// if ($field['buildings_id'] == 8 )
	// {
		// $content .= "<table><tr><td style=\"border : 1px solid #262323;\"><a href=?ps=".$_SESSION['pagesess']."&p=colony&s=sc&id=".$_GET['id']."&a=bcs ".getonm("bcs","buttons/builds")."><img src=".$gfx."/buttons/builds1.gif name=bcs border=0 title=\"Kolonieschiff bauen\"> Kolonieschiff bauen</a></td></tr>
		// <tr><td>&nbsp;&nbsp;Kosten: 
			// <img src=".$gfx."/icons/energy.gif border=0 title=\"Energie\">50&nbsp;
			// <img src=".$gfx."/goods/2.gif border=0 title=\"Baumaterial\">50&nbsp;
			// <img src=".$gfx."/goods/4.gif border=0 title=\"Transparentes Aluminium\">25&nbsp;
			// <img src=".$gfx."/goods/21.gif border=0 title=\"Duranium\">25&nbsp;
			// <img src=".$gfx."/goods/5.gif border=0 title=\"Deuterium\">50&nbsp;
			// <img src=".$gfx."/icons/crew".$_SESSION['race'].".gif border=0 title=\"Arbeiter\">15&nbsp;
		
		// </td></tr></table>";
	// }	
		// if ($field['buildings_id'] == 40) $content .= "<table><tr><td style=\"width: 150px; border : 1px solid #262323;\"><a href=?p=colony&s=mor&id=".$_GET['id']."&fid=".$_GET['fid']." ".getonm('mor','buttons/notiz')."><img src=".$gfx."/buttons/notiz1.gif name=mor border=0 title=\"Modulherstellung\"> Modulherstellung</a></td></tr></table>";
		// if ($field['buildings_id'] == 80) $content .= "<table><tr><td style=\"width: 150px; border : 1px solid #262323;\"><a href=?p=colony&s=tpr&id=".$_GET['id']."&fid=".$_GET['fid']." ".getonm('mor','buttons/notiz')."><img src=".$gfx."/buttons/notiz1.gif name=mor border=0 title=\"Torpedoherstellung\"> Torpedoherstellung</a></td></tr></table>";
		
		
		
		// if ($field['aktiv'] > 1 && $field['is_activateable'] == 1) $content .= "<br><table><tr><td style=\"border : 1px solid #262323;\"><b>Aktivierungseinstellung</b></td></tr><tr><td style=\"border : 1px solid #262323;\">Aktiviert nach Bau? ".($db->query("SELECT value FROM stu_colonies_actions WHERE var='db' AND colonies_id=".$_GET['id']." AND value='".$_GET["fid"]."'",1) > 0 ? "<a href=?p=colony&s=sc&a=sam&fid=".$_GET["fid"]."&id=".$_GET['id'].">Nein</a>" : "<a href=?p=colony&s=sc&a=sam&fid=".$_GET["fid"]."&id=".$_GET['id'].">Ja</a>")."</td></tr></table>";

	}
	else
	{
		$contentwidth = 600;
		$content .= "<table border=0 style=\"width:100%;\">";
	
		$content .= "<tr><th colspan=2 style=\"padding:4px;text-align:center;\"><b>Unbebaut</b></th></tr>";

		$content .= "<tr>";
		$content .= "<td colspan=2 style=\"padding:4px;text-align:center;\">".fieldPic($field['type'])."</td>";
		$content .= "</tr></table>";
		
		$content .= "</td><td valign=\"top\">";
		
		
		$content .= "<table width=100%>";
		

		$content .= "<tr><td style=\"padding:4px;\"><a href=?p=colony&s=bm&id=".$_GET['id']."&fid=".$_GET['fid']." ".getHover("bbmn","inactive/n/text","hover/w/text")."><img src=".$gfx."/buttons/inactive/n/text.gif name=bbmn title='Baumenü' border=0> Gebäude & Terraforming</a></td></tr>";
		$content .= "</table>";		
	}


	


	$content .= "</td>
</tr>
</table>";


		echo "<div style=\"width:".(210+$contentwidth)."px; text-align:left;\">";
		echo floatingPanel(3,"Feld ".$_GET['fid']." - ".getnamebyfield($field['type']),"finfo",$gfx."/buttons/icon/field.gif",$content,1);	
		echo "</div>";

?>
