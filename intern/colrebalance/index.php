<?php

	$showhidden = 1;

	function basefieldnames($id) {
	
		switch($id) {
			case  1: return "Wiese";
			case  2: return "Laubwald";
			case  3: return "Nadelwald";
			case  5: return "See";
			case  4: return "Seichtes Wasser";
			case 65: return "Korallenriff";
			case 20: return "Eisdecke";
			case  7: return "Wüste";
			case  8: return "Wüste";
			case 47: return "Felsspalten";
			case 48: return "Sanddünen";
			case 49: return "Sanddünen";
			case 50: return "Sanddünen";
			case  6: return "Eis";
			case 14: return "Eisformationen";
			case  9: return "Ödland";
			case 18: return "Tundra";
			case 15: return "Felsformationen";
			case 31:
			case 32:
			case 33:
			case 34:
			case 36:
			case 35: return "Berge";
			case 19: return "Fels";
			case 10: return "Fels";
			case 12: return "Krater";

			case 40: return "Ozean";
			case 41: return "Schelfmeer";
			case 42: return "Korallenriff";
			
			case 46: return "Dschungel";
			
			case 79: return "Fels";
			case 81: return "Untergrund-Fels";
			case 82: return "Erzader";
			case 83: return "Magmaeinschluss";
			case 84: return "Untergrund-Wasser";
			case 78: return "Magma";
			case 74: return "Eis";
			case 100: return "Weltraum";
			case 110:
			case 111:
			case 112:
			case 113:
			case 114:
			case 115:
			case 116:
			case 117:
			case 118:
			case 119: return "Weltraum";
			
			
			case 200: return "Lavagestein";
			case 201: return "Berge";
			case 202: return "Erloschener Vulkan";
			case 211:
			case 212:
			case 213:
			case 214:
			case 215:
			case 216:
			case 217: return "Aktiver Vulkan";
			
			case 223: 
			case 225: 
			case 226: 
			case 229: 
			case 230: 
			case 232: 
			case 241: 
			case 242: 
			case 243: 
			case 244: return "Lavastrom";
			case 245: 
			case 246: 
			case 247: 
			case 248: return "Lavasee";
			case  11: return "Ebene";
			case  12:
			case  13: return "Krater";
			case  17: return "Aktiver Vulkan";
			case  28: return "Erloschener Vulkan";
			
			case 1000: return "Unfertig";
			default: return "???";
		}
	}
	
	function bonusnames($id) {
	
		switch($id) {
		
			case "01": return "(nährstoffreich: +Nahrung für Farm)";
			case "02": return "(nährstoffreich: +Nahrung für Wasserfarm)";
			case "03": return "(großflächig: +Wohnraum für Häuser)";
			case "04": return "(unterirdische Höhlen: +Wohnraum für Kuppeln)";
			case "31": return "(starke Sonneneinstrahlung: +Energie für Solarkomplex)";
			case "32": return "(starke Strömung: +Energie für Turbinen)";
			case "11": return "(reiche Deuteriumvorkommen: doppelte Produktion)";
			case "12": return "(reiche Erzvorkommen: doppelte Produktion)";
			case "21": return "(reiche Dilithiumvorkommen: verbraucht kein Vorkommen)";
			case "22": return "(reiche Tritaniumvorkommen:: verbraucht kein Vorkommen)";
		
			default: return "???";
		}
	}
	
	function colnames($id) {
	
	
		switch($id % 100) {
		
			case  1: return "Klasse M: Erdähnlich";
			case  2: return "Klasse L: Wald";
			case  3: return "Klasse O: Ozean";
			case  6: return "Klasse H: Wüste";
			case  7: return "Klasse X: Lava";
			case  4: return "Klasse K: Ödland";
			case  8: return "Klasse X: Lava";
			case  9: return "Klasse P: Eis";
			case 10: return "Klasse D: Fels";
			case  5: return "Klasse G: Tundra";

			default: return "???";
		}
	}
	
	echo "<html><head>
	<title>Koloniegenerator</title>
	<link rel='STYLESHEET' type='text/css' href='stu.css'>
	</head><body  bgcolor=#000000><font color=white>"; 

	function tablecol($allfields,$c) {

		echo "<br><br><br><table width=100%><tr><td width=50%>";
		echo "<center><table class=\"use\" style=\"border: 0; margin: 0; padding: 0 ! important;\">";
		$phase = 0;

		$w = 1;
		if (count($allfields) % 10 == 0) $w = 10;
		if (count($allfields) % 9 == 0) $w = 9;
		if (count($allfields) % 8 == 0) $w = 8;
		if (count($allfields) % 7 == 0) $w = 7;
		// else if (count($allfields) == 80) $w = 10;

		
		echo "<tr><td colspan=".$w." class=\"cfd\" style=\"border: 0px;\"><br><center>".colnames($c)."</center><br></td></tr>";
		
		foreach ($allfields as $key => $f) {
			if ($phase % $w == 0) echo "<tr>";
			
			$extrastr = "";
			if ($f > 300) $extrastr = $f-300;
			if ($f >= 10000) {
				$basef = floor($f / 100);
				
				$bonusid = substr($f,3);
				
				
				$bonustext = bonusnames($bonusid);

				echo "<td style=\"width: 30px; height: 30px;\" class=\"cfd\"><div style=\"background-image: url(fields/".$f.".gif); width: 30px; height: 30px;\" title=\"".basefieldnames($basef)." ".$bonustext."\">".$extrastr."</div></td>";				
			} else {
				echo "<td style=\"width: 30px; height: 30px;\" class=\"cfd\"><div style=\"background-image: url(fields/".$f.".gif); width: 30px; height: 30px;\" title=\"".basefieldnames($f)."\">".$extrastr."</div></td>";
			}
			if ($phase % $w == $w-1) echo "</tr>";
			$phase++;
		}

		echo "</center></table></td><td width=50%></td></tr></table>";
	}
	
	include("colgen.class.php");

	
	$c = $_GET['c'];
	if (!$c) $c = 0;
	
	$lastid = 0;
	$k = 0;





	echo "<br>";



	

	echo "<a href=index.php?c=201><img src=planets/201.gif title='' border=0></a>  "; 
	echo "<a href=index.php?c=202><img src=planets/202.gif title='' border=0></a>  "; 
	echo "<a href=index.php?c=203><img src=planets/203.gif title='' border=0></a>  "; 
	echo "<a href=index.php?c=204><img src=planets/204.gif title='' border=0></a>  "; 
	echo "<a href=index.php?c=205><img src=planets/205.gif title='' border=0></a>  "; 
	echo "<a href=index.php?c=206><img src=planets/206.gif title='' border=0></a>  "; 
	echo "<a href=index.php?c=207><img src=planets/207.gif title='' border=0></a>  "; 
	echo "<a href=index.php?c=209><img src=planets/209.gif title='' border=0></a>  "; 
	echo "<a href=index.php?c=210><img src=planets/210.gif title='' border=0></a>  "; 

	echo "<br>";
	
	echo "<a href=index.php?c=301><img src=planets/301.gif title='' border=0></a>  "; 
	echo "<a href=index.php?c=302><img src=planets/302.gif title='' border=0></a>  "; 
	echo "<a href=index.php?c=303><img src=planets/303.gif title='' border=0></a>  "; 
	echo "<a href=index.php?c=304><img src=planets/304.gif title='' border=0></a>  "; 
	echo "<a href=index.php?c=305><img src=planets/305.gif title='' border=0></a>  "; 
	echo "<a href=index.php?c=306><img src=planets/306.gif title='' border=0></a>  "; 
	echo "<a href=index.php?c=307><img src=planets/307.gif title='' border=0></a>  "; 
	echo "<a href=index.php?c=309><img src=planets/309.gif title='' border=0></a>  "; 
	echo "<a href=index.php?c=310><img src=planets/310.gif title='' border=0></a>  "; 

	
	$mycolgen = new ColonyGenerator();
	
	
	if ($c != 0)
	{
		$arr = $mycolgen->generateColony($c,3);
		// print_r($arr);
		echo "<br>";
		tablecol($arr,$c);
		
		// echo "<br><br><br><br>".$mycolgen->encode($arr);
		
		
		
		
	}
	
	echo "</font></body></html>";


?>