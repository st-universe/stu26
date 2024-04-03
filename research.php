<?php
if (!is_object($db)) exit;
include_once("class/research.class.php");
$research = new research;

switch($_GET[s])
{
	default:
		$v = "main";
	case "ma":
		$v = "main";
		break;
	// case "red":
		// $v = "researchdetail";
		// if (!check_int($_GET["rid"])) die(show_error(902));
		// $research->loadresearch($_GET["rid"]);
		// if ($research->research == 0) die(show_error(902));
		// break;
}

if ($v == "main")
{
	
	echo "<script type=\"text/javascript\" language=\"JavaScript\">
	var e;
	
	
function subtypename(id) {
		switch(id) {
			case 1: return \"Test\";
			default: return \"Default\";
	}
}	
		
	
function getPos(el) {
    // yay readability
    for (var lx=0, ly=0;
         el != null;
         lx += el.offsetLeft, ly += el.offsetTop, el = el.offsetParent);
    return {x: lx,y: ly};
}

function showRump(rel,id)
{
		elt = 'infodiv';
		sendRequest('backend/infodisplay/rump.php?PHPSESSID=".session_id()."&rid='+id);
		positionElement(rel,document.getElementById('infodiv'),510);
}
function showTerra(rel,id)
{
		elt = 'infodiv';
		sendRequest('backend/infodisplay/terraform.php?PHPSESSID=".session_id()."&id='+id);
		positionElement(rel,document.getElementById('infodiv'),210);
}
function showResMods(rel,id)
{
		elt = 'infodiv';
		sendRequest('backend/infodisplay/resmods.php?PHPSESSID=".session_id()."&rid='+id);
		positionElement(rel,document.getElementById('infodiv'),380);
}

</script>
";	


	if ($_GET["a"] == "research" && check_int($_GET["rid"])) $return = $research->doresearch($_GET["rid"]);
	if ($_GET["a"] == "abort" && check_int($_GET["rid"])) $return = $research->breakresearch($_GET["rid"]);
	if ($_GET["a"] == "remove" && check_int($_GET["rid"])) $return = $research->deleteresearch($_GET["rid"]);
	pageheader("/ <b>Forschung</b>");
	if ($return) meldung($return);
	// $research->generateresearchtree();
	$reslist = $research->createResearchList();
	
	
	function showResearch($res,$activeres,$width=1000) {
		global $gfx;
		$m = "";
	
		$m .= "<tr height=30>";
		
		if ($res[active] == 1) $m .= "<td width=26 rowspan=2><img src='".$gfx."/icons/forsch.gif' title='Aktiv'></td>";
		else if ($res[done] == 1) $m .= "<td width=26><img src='".$gfx."/icons/haken.gif' title='Erforscht'></td>";
		else $m .= "<td width=26></td>";
		
		
		$m .= "<td width=30 style=\"text-align:center;\">";
		
		if ($res[rumps_id] > 0) 
			$m .= "<img src='".$gfx."/icons/frage.gif' title='Infos' onMouseOver=\"showRump(this,'".$res[rumps_id]."');\" onMouseOut=\"hideInfo();\">";
		if ($res[build_id] > 0) 
			$m .= "<img src='".$gfx."/icons/frage.gif' title='Infos' onMouseOver=\"showBuild(this,'".$res[build_id]."');\" onMouseOut=\"hideInfo();\">";
		if ($res[terraform_id] > 0) 
			$m .= "<img src='".$gfx."/icons/frage.gif' title='Infos' onMouseOver=\"showTerra(this,'".$res[terraform_id]."');\" onMouseOut=\"hideInfo();\">";			
		if ($res[mod_id] != "") 
			$m .= "<img src='".$gfx."/icons/frage.gif' title='Infos' onMouseOver=\"showResMods(this,'".$res[research_id]."');\" onMouseOut=\"hideInfo();\">";
			
		$m .= "</td>";
		
		
		$m .= "<td>".$res[name]."</td>";
	
		
		
		if ($res[active] == 1) {
			$m .= "<td width=100>";
			$m .= "<img src='".$gfx."/icons/".$res[effecttype].".gif' title='".geteffectname($res[effecttype])."'>".$res[progress]."/".$res[total];
			$m .= "</td>";		
		} else if ($res[done] == 1) {
			$m .= "<td width=100></td>";		
		} else {
			$m .= "<td width=100>";
			if ($res[cost]>0) $m .= "<img src='".$gfx."/icons/".$res[effecttype].".gif' title='".geteffectname($res[effecttype])."'>".$res[cost];
			$m .= "</td>";	
		}

		if ($res[done] == 1) {
			if ($res[removable] && !$activeres) $m .= "<td width=80 style=\"text-align:center;\"><a href=?p=research&rid=".$res[research_id]."&a=remove><font color=Red>Entfernen</font></a></td>";
			else if ($res[removable] && $res[research_id] == $activeres) $m .= "<td width=80 style=\"text-align:center;\"><a href=?p=research&rid=".$res[research_id]."&a=abort><font color=Red>Abbrechen</font></a></td>";
			else $m .= "<td width=80 style=\"text-align:center;\"></td>";
		} else {
			if ($activeres > 0) {
				if ($res[research_id] != $activeres) {
					$m .= "<td width=80 style=\"text-align:center;\"></td>";
				} else {
					$m .= "<td width=80 style=\"text-align:center;\"><a href=?p=research&rid=".$res[research_id]."&a=abort><font color=Red>Abbrechen</font></a></td>";
				}
			} else {
				$m .= "<td width=80 style=\"text-align:center;\"><a href=?p=research&rid=".$res[research_id]."&a=research>Erforschen</a></td>";
			}
		}
		
		
		$m .= "</tr>";
		
		if ($res[active] == 1) {
		
			$m .= "<tr>";
			$m .= "<td></td><td colspan=3>".bigdarkuniversalstatusbar($res[progress],$res[total],"cya",0,550)."</td>";
			$m .= "</tr>";
		
		
		}
		
		return $m;
	}
	
	$cunt = 1;
	function showCategory($cat,$activeres,$width=1000) {
		global $gfx, $cunt;
		
		$m = $cat['name'];
	
		// if ($cat[numdone] >= count($cat[researches])) 	$m .= "<img src='".$gfx."/icons/haken.gif' title='Erledigt'></span>";
		// elseif (($cat[limit] > 0) && ($cat[numdone] >= $cat[limit])) 			$m .= "<img src='".$gfx."/icons/hakenrot.gif' title='Limit erreicht'></span>";
		
		$m .= " (".$cat[numdone]."/".(($cat[limit] > 0) ? $cat[limit] : count($cat[researches])).")";

		$reses = array();
		foreach($cat[researches] as $res) {
			array_push($reses,showResearch($res,$activeres,800));
		}

		$pic = "/buttons/icon/research.gif";
		if ($cat[numdone] >= count($cat[researches])) 				$pic = "/buttons/icon/yes.gif";
		if (($cat[limit] > 0) && ($cat[numdone] >= $cat[limit]))	$pic = "/buttons/icon/yes.gif";
		
		$cunt++;
		return dropDownMenu(1+($cunt%4),$m,"cat".$cat['id'],$gfx.$pic,$reses,$cat['open'],0);
	}	
	
	
	echo "<div style=\"margin:16px;width:800px;\"><tr><td width=20></td><td>";
	
	$activeID = $db->query("SELECT research_id FROM stu_research_active WHERE user_id = ".$research->uid."",1);
	
	foreach($reslist as $cat) {
		if (count($cat[researches]) == 0) continue;
		echo showCategory($cat,$activeID,800);
		echo "<br>";
	}
	
	
	echo "</div>";
	
	
	
	
	// echo showCategory($cat,1000);
	
	echo "<br><br><br>";
	// print_r($reslist);
	
	
	// if (is_array($research->var_a))
	// {
		// echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=600>
		// <tr>
			// <td><b>Offene Forschungen</b></td>
			// <td><img src=".$gfx."/goods/41.gif title='Forschungspunkte Verarbeitung'></td>
			// <td><img src=".$gfx."/goods/42.gif title='Forschungspunkte Technik'></td>
			// <td><img src=".$gfx."/goods/43.gif title='Forschungspunkte Konstruktion'></td>";
			
			// if ($research->showdominion != 0) echo "<td><img src=".$gfx."/goods/44.gif title='Forschungspunkte Dominion'></td>";
			
		// echo "</tr>";
		// foreach ($research->var_a as $key => $data)
		// {
			// if ($data == 1) continue;
			// $i++;
			// if ($i == 2)
			// {
				// $trc = " style=\"background-color: #171616\"";
				// $i = 0;
			// }
			// echo "<tr>
			// <td".$trc."><a href=?p=research&s=red&rid=".$data[research_id].">".($data[points_v] > $_SESSION[r_verarbeitung] || $data[points_t] > $_SESSION[r_technik] || $data[points_k] > $_SESSION[r_konstruktion] ? "<font color=gray>".stripslashes($data[name])."</font>" : stripslashes($data[name]))."</a></td>
			// <td".$trc.">".($data[points_v] > 500 ? "---" : $data[points_v])."</td>
			// <td".$trc.">".($data[points_t] > 500 ? "---" : $data[points_t])."</td>
			// <td".$trc.">".($data[points_k] > 500 ? "---" : $data[points_k])."</td>";
			
			// if ($research->showdominion != 0) echo "<td".$trc.">".($data[points_d] > 500 ? "---" : $data[points_d])."</td>";
			
			// echo "</tr>";
			// $trc = "";
		// }
		// echo "</table>";
	// }
	// if (is_array($research->var_b))
	// {
		// echo "<br><table bgcolor=#262323 cellspacing=1 cellpadding=1 width=600>
		// <tr>
			// <td><b>Bereits getätigte Forschungen</b> (".count($research->var_b).")</td>
			// <td><img src=".$gfx."/goods/41.gif title='Forschungspunkte Verarbeitung'></td>
			// <td><img src=".$gfx."/goods/42.gif title='Forschungspunkte Technik'></td>
			// <td><img src=".$gfx."/goods/43.gif title='Forschungspunkte Konstruktion'></td>";
			
			// if ($research->showdominion != 0) echo "<td><img src=".$gfx."/goods/44.gif title='Forschungspunkte Dominion'></td>";
			
		// echo "</tr>";
		// foreach ($research->var_b as $key => $data)
		// {
			// if ($data == 1) continue;
			// $i++;
			// if ($i == 2)
			// {
				// $trc = " style=\"background-color: #171616\"";
				// $i = 0;
			// }
			// echo "<tr>
			// <td".$trc."><a href=?p=research&s=red&rid=".$data[research_id].">".$data[name]."</a></td>
			// <td".$trc.">".$data[points_v]."</td>
			// <td".$trc.">".$data[points_t]."</td>
			// <td".$trc.">".$data[points_k]."</td>";
			
			// if ($research->showdominion != 0) echo "<td".$trc.">".$data[points_d]."</td>";
			
			// echo "</tr>";
			// $trc = "";
		// }
		// echo "</table>";
	// }
}
if ($v == "researchdetail")
{
	echo "<script language=\"Javascript\">

	</script><style>table.tmodo {
	background-color: #262323;
	border-collapse: separate;
	border-spacing: 1px;
}
table.tsec {
	background-color: #000000;
	width: 100%;
	border-collapse: separate;
	border-spacing: 0px;
}
td.mml {
	background-color : #262323;
	color : #8897cf;
	font-size : 9pt;
	margin-left : 3px;
	margin-bottom : 3px;
	margin-right : 3px;
	margin-top : 3px;
	height : 20px;
}
</style>";
	pageheader("/ <a href=?p=research>Forschung</a> / <b>".stripslashes($research->research["name"])."</b>");
	
	$activeID = $db->query("SELECT research_id FROM stu_research_active WHERE user_id = ".$_SESSION["uid"]."",1);
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=750>
	<tr>";
		// if ($research->research["build_id"] != 0) echo "<td colspan=6>".$research->getbinfo($research->research["build_id"])."<br><br>".nl2br(stripslashes($research->research["description"]))."<br><br></td>";
		// elseif ($research->research["mod_id"] != 0) echo "<td colspan=6>".$research->getminfo($research->research["mod_id"])."</td>";
		// else 
			echo "<td colspan=6>".nl2br(stripslashes($research->research["description"]))."</td>";
	echo "</tr>
	<tr>";
		// <td><img src=".$gfx."/goods/41.gif title='Forschungspunkte Verarbeitung'> ".$_SESSION["r_verarbeitung"]."/".($research->research["points_v"] > 500 ? "---" : ($_SESSION["r_verarbeitung"] < $research->research["points_v"] ? "<font color=#FF0000>" : "").$research->research["points_v"].($_SESSION["r_verarbeitung"] < $research->research["points_v"] ? "</font>" : ""))."</td>
		// <td><img src=".$gfx."/goods/42.gif title='Forschungspunkte Technik'> ".$_SESSION["r_technik"]."/".($research->research["points_t"] > 500 ? "---" : ($_SESSION["r_technik"] < $research->research["points_t"] ? "<font color=#FF0000>" : "").$research->research["points_t"].($_SESSION["r_technik"] < $research->research["points_t"] ? "</font>" : ""))."</td>
		// <td><img src=".$gfx."/goods/43.gif title='Forschungspunkte Konstruktion'> ".$_SESSION["r_konstruktion"]."/".($research->research["points_k"] > 500 ? "---" : ($_SESSION["r_konstruktion"] < $research->research["points_k"] ? "<font color=#FF0000>" : "").$research->research["points_k"].($_SESSION["r_konstruktion"] < $research->research["points_k"] ? "</font>" : ""))."</td>
		
		if ($research->research["user_id"] == $_SESSION["uid"])
			echo "<td align=\"center\" colspan=2><font color=Green><b>Erforscht</b></font></td>";
		else if ($_GET["rid"] == $activeID)
			echo "<td align=\"center\" colspan=2><a href=?p=research&rid=".$_GET["rid"]."&a=abort><font color=Red>Abbrechen</font></a></td>";
		else
			echo "<td align=\"center\" colspan=2><a href=?p=research&rid=".$_GET["rid"]."&a=research>Erforschen</a></td>";
		
		
		
		echo "<td width=30%>&nbsp;</td>
	</tr>
	</table>";
}
?>