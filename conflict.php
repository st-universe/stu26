<?php
if (!is_object($db)) exit;
include_once("class/game.class.php");
$game = new game;
switch($_GET[s])
{
	default:
		$v = "main";
	// case "ma":
		// $v = "main";
		// break;
}

if ($v == "main")
{
	pageheader("/ <b>Globaler Rassenkonflikt</b>");

	
	
	
	
	function rendersector($sector) {
		global $gfx;
		
		
		$content .= "<table width=100%>";
		
		
		
		
		$content .= "<tr>";
		
		$content .= "<td width=80><img src='".$gfx."/starmap/political/sector".$sector['id'].".png' width=100 height=100></td>";		
		
		$content .= "<td style=\"width:125px;padding:4px;text-align:left;vertical-align:middle;color:#ef962d;\">".renderSectorState(sectorStateString($sector['status'],$sector['counter'], $sector['faction'], $sector['attacker'], $_SESSION['race']))."</td>";			
		
		
		$content .= "<td width=150>";
		foreach($sector['systems'] as $sys) {
			$content .= "<img src='".$gfx."/systems/".$sys['type']."ms.png' height=20 width=20 style=\"vertical-align:middle;\"> ".$sys['name']."<br>";
		}
		$content .= "</td>";
		
		
		$content .= "<td width=450><table style=\"width:100%;height:100%\">";
		
		
		$dominant = 0;
		if (($sector['ships'][1] > 90) && ($sector['ships'][1] > (1.5 * ($sector['ships'][2]+$sector['ships'][3])))) $dominant = 1;
		if (($sector['ships'][2] > 90) && ($sector['ships'][2] > (1.5 * ($sector['ships'][1]+$sector['ships'][3])))) $dominant = 2;
		if (($sector['ships'][3] > 90) && ($sector['ships'][3] > (1.5 * ($sector['ships'][1]+$sector['ships'][2])))) $dominant = 3;
			
		if ($sector['ships'][$_SESSION['race']] > 0) {
					
			if ($dominant == $_SESSION['race']) $cll = "valueplus";
			else								$cll = "valueminus";
			
			if ($dominant == 1) $content .= "<tr><td style=\"width:100%;height:30px;\"><span class=\"".$cll."\"><img src='".$gfx."/buttons/icon/r1.gif' height=20 width=20 style=\"vertical-align:middle;\"><b> ".$sector['ships'][1]." Flottenpunkte</b></span></td></tr>";
			else				$content .= "<tr><td style=\"width:100%;height:30px;\"><span class=\"valuenull\"><img src='".$gfx."/buttons/icon/r1.gif' height=20 width=20 style=\"vertical-align:middle;\"> ".$sector['ships'][1]." Flottenpunkte</span></td></tr>";
			
			if ($dominant == 2) $content .= "<tr><td style=\"width:100%;height:30px;\"><span class=\"".$cll."\"><img src='".$gfx."/buttons/icon/r2.gif' height=20 width=20 style=\"vertical-align:middle;\"><b> ".$sector['ships'][2]." Flottenpunkte</b></span></td></tr>";
			else				$content .= "<tr><td style=\"width:100%;height:30px;\"><span class=\"valuenull\"><img src='".$gfx."/buttons/icon/r2.gif' height=20 width=20 style=\"vertical-align:middle;\"> ".$sector['ships'][2]." Flottenpunkte</span></td></tr>";

			if ($dominant == 3) $content .= "<tr><td style=\"width:100%;height:30px;\"><span class=\"".$cll."\"><img src='".$gfx."/buttons/icon/r3.gif' height=20 width=20 style=\"vertical-align:middle;\"><b> ".$sector['ships'][3]." Flottenpunkte</b></span></td></tr>";
			else				$content .= "<tr><td style=\"width:100%;height:30px;\"><span class=\"valuenull\"><img src='".$gfx."/buttons/icon/r3.gif' height=20 width=20 style=\"vertical-align:middle;\"> ".$sector['ships'][3]." Flottenpunkte</span></td></tr>";

			} else {
			$content .= "<tr><td style=\"width:100%;height:30px;\"></td></tr>";
			$content .= "<tr><td style=\"width:100%;height:30px;\"><img src='".$gfx."/buttons/icon/info.gif' height=20 width=20 style=\"vertical-align:middle;\"> Flottenstatus unbekannt</td></tr>";
			$content .= "<tr><td style=\"width:100%;height:30px;\"></td></tr>";			
		}
		
		$content .= "</table></td>";
		
		
		$content .= "<td width=* style=\"vertical-align:top;\"><table  style=\"width:100%;text-align:center;border:none;\"><tr><td colspan=2 style=\"width:100%;text-align:center;border:none;\"><b>Änderung bei nächstem Tick:</b></td></tr>";

		$change = sectorStateChange($sector['status'], $sector['counter'], $sector['faction'], $sector['attacker'], $dominant);
		
		$isChanged = false;
		if ($change['status'] != $sector['status'])		$isChanged = true;
		if ($change['counter'] != $sector['counter'])	$isChanged = true;
		if ($change['faction'] != $sector['faction'])	$isChanged = true;
		if ($change['attacker'] != $sector['attacker'])	$isChanged = true;

		if ($isChanged) {
			
			if ($change['faction'] != $sector['faction'])
				$content .= "<tr><td width=50%>Besitzer:</td><td><img src='".$gfx."/rassen/".$change['faction']."kn.png' style=\"vertical-align:middle;height:20px;width:20px;\"> <b>".getFormatedFactionName($change['faction'])."</b></font></td></tr>";
			
			if ($change['status'] != $sector['status'])		
				$content .= "<tr><td width=50%>Status:</td><td>".renderSectorState($change['status'])."</td></tr>";
			
			if ($change['counter'] != $sector['counter'] && $change['counter'] > 0)
				$content .= "<tr><td width=50%>Ticks bis Statusänderung:</td><td><font color='#a5a5a5'><img src='".$gfx."/icons/conflict-clock.png' style=\"vertical-align:middle;height:20px;width:20px;\"> <b>".$change['counter']." Ticks</b></font></td></tr>";
			

		} else {
			$content .= "<tr><td colspan=2>Keine Änderung</td></tr>";
		}
		
		// $content .= "<br>".print_r($change,true);
		$content .= "</table></td>";
		
		
		// $content .= "<td width=*>";
		// $content .= "<br><br>a ".print_r($sector['neighbours'],true);
		// $content .= "<br><br>b ".print_r($sector['systems'],true);
		// $content .= "</td>";

		
		
		$content .= "</tr>";
		$content .= "</table>";
		
		if ($sector['faction'] == 0) $icon = $gfx."/buttons/icon/moon.gif";
		else						 $icon = $gfx."/buttons/icon/r".$sector['faction'].".gif";
		
		
		
		return fixedPanel($sector['faction'],$sector['name'],"msector".$sector['id'],$icon,$content,"conflict");
		// return coloredSimplePanel("#ffffff",$sector['name'],"msector".$sector['id'],$gfx."/buttons/icon/r".$sector['faction'].".gif",$content);
	}
	
	
	
	
	
	$sectors = array();
	
	


	$sectors = $game->getSectors();
	
	echo "<table width=100% class=tablelayout>";
	
	
	$content = "<img src='".$gfx."/starmap/political/faction".$_SESSION['race'].".png'>";
	
	echo "<tr><td class=tablelayout></td><td class=tablelayout width=500>".fixedPanel(0,"Aktuelle Sektorkarte","msector".$sector['id'],$gfx."/buttons/icon/conflict.gif",$content,"conflict")."</td><td class=tablelayout></td></tr>";
	
	foreach($sectors as $sector) {
		echo "<tr><td class=tablelayout colspan=3>".rendersector($sector)."</td></tr>";
	}
	echo "</table>";
	
}

?>
