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
	
	
	
	$wetternahrung = 2;
	
	
	
	
	$res = $db->query("SELECT * FROM stu_buildings WHERE 1 ORDER BY level,name");
	
	
	
	while($data=mysql_fetch_assoc($res))
	{
	
		$builds[$data[buildings_id]] = $data;
	
		$goodres = $db->query("SELECT * FROM stu_buildings_goods WHERE buildings_id = ".$data[buildings_id]." ");
		
		
		$builds[$data[buildings_id]]['goods'] = array();
			while($gdata=mysql_fetch_assoc($goodres)) {
				$builds[$data[buildings_id]]['goods'][$gdata[goods_id]] = $gdata['count']; ;
				
			}
	}
	
	// print_r($builds);
	
	$circuits = array();
	
	function newCircuit() {
		global $circuits,$builds,$wetternahrung;
	  
		$circuit[type] = func_get_arg(1);
		$circuit[name] = func_get_arg(0);
		$circuit[builds] = array();
		$circuit[goods] = array();
		$circuit[energy] = 0;
		$circuit[priority] =  func_get_arg(2);
		$circuit[bev] = 0;
		$circuit[bev_tot] = 0;
		for($i = 3 ; $i < func_num_args(); $i++) {
			array_push($circuit[builds],func_get_arg($i));
			
			foreach($builds[func_get_arg($i)][goods] as $k => $v) {
				$circuit['goods'][$k] += $v;
				if (func_get_arg($i) == 2) $circuit['goods'][1] += $wetternahrung;
			}
			

			
			
			$circuit['goods'][1] -= $builds[func_get_arg($i)][bev_pro] / 10;
			$circuit[energy] += $builds[func_get_arg($i)][eps_proc];
			$circuit[bev_tot] += $builds[func_get_arg($i)][bev_pro];
			$circuit[bev] += $builds[func_get_arg($i)][bev_pro];
			$circuit[bev] -= $builds[func_get_arg($i)][bev_use];
		}
			foreach($circuit['goods'] as $k => $v) {
				if ($v == 0) unset($circuit['goods'][$k]);
			}
	  array_push($circuits,$circuit);

	  
  }
	// function newCircuit()
	
	
	newCircuit("Zentrale","none",'9',1);
	
	newCircuit("Siedlung G","bev",'9',30,2,2);
	newCircuit("Siedlung M","bev",'9',30,41,41,41);
	
	newCircuit("Kuppeln","bev",'9',22,22,23,23,23);
	
	newCircuit("Turbinen","energy",'3',21,21,21);
	newCircuit("Solarkomplex","energy",'3',10,10,10);
	newCircuit("Solarzelle","energy",'1',16,14);
	newCircuit("MiniFusion N","energy",'3',16,14);
	newCircuit("Fusion N","energy",'4',17,14,14);
	
	newCircuit("Warpkern N","energy",'9',38,20,25,14,14);
	
	newCircuit("Warpkern","energy",'9',38,20,25);
	
	newCircuit("Baumaterialfabrik","industry",'1',6);
	newCircuit("Aluminiumwerk","industry",'2',13,12);
	newCircuit("Antimaterie N","industry",'3',25,14);
	newCircuit("Duraanlage N","industry",'5',7,15,15,15);
	
	newCircuit("Isos","industry",'5',31,12);
	
	// echo phpversion();
	
	
	
	
  // print_r($circuits);
	
	
	// echo "Known Circuits:";
	// foreach($circuits as $c) {
		
		// echo "<br><img src=\"../gfx/buildings/".$c[builds][0]."/0.png\"/> ".$c[name]." : <img src=\"../gfx/icons/storage.gif\"/>".count($c[builds])." <img src=\"../gfx/goods/0.gif\"/>".$c[energy]." <img src=\"../gfx/icons/crew.gif\"/>".$c[bev]." <img src=\"../gfx/icons/crewspace.gif\"/>".$c[bev_tot];
			// foreach($c['goods'] as $k => $v) {
				// echo " <img src=\"../gfx/goods/".$k.".gif\"/>".$v;
			// }
	// }
	
	
	
	function addCircuitByID($selection,$circ) {
		array_push($selection['circuits'],$circ);
		
		foreach($circ[builds] as $b) {
			array_push($selection['builds'],$b);
		}
		return $selection;
	}
	
	function addCircuit($selection,$name) {
		global $circuits;
		foreach($circuits as $c) {
			if ($c[name] == $name) {
				return addCircuitByID($selection,$c);
			}
		}
	}
	
	function addBuilding($selection,$b) {
		array_push($selection['builds'],$b);
		return $selection;
	}
	
	function displaySelection($selection) {
		global $builds, $wetternahrung;
		
		
		$val[bev] = 0;
		$val[bev_tot] = 0;
		$val[goods] = array();
		$val[energy] = 0;
		
		foreach($selection[builds] as $b) {
			foreach($builds[$b][goods] as $k => $v) {
				$val['goods'][$k] += $v;
				if ($b == 2) $val['goods'][1] += $wetternahrung;
			}
			
			$val['goods'][1] -= $builds[$b][bev_pro] / 10;
			$val[energy] += $builds[$b][eps_proc];
			$val[bev_tot] += $builds[$b][bev_pro];
			$val[bev] += $builds[$b][bev_pro];
			$val[bev] -= $builds[$b][bev_use];
			$cb[$b]++;
		}
		
		
		// print_r($val);
		echo "<br><br>";
		echo "<br><img src=\"../gfx/icons/storage.gif\"/>".count($selection[builds])." <img src=\"../gfx/goods/0.gif\"/>".$val[energy]." <img src=\"../gfx/icons/crew.gif\"/>".$val[bev]." <img src=\"../gfx/icons/crewspace.gif\"/>".$val[bev_tot];
			foreach($val['goods'] as $k => $v) {
				echo " <img src=\"../gfx/goods/".$k.".gif\"/>".$v;
			}	
			echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;";
			foreach($cb as $k => $v) {
				
				echo "<img style=\"border: 1px solid #8897cf; width:20px;\" src=\"../gfx/buildings/".$k."/0.png\"/>".$v."&nbsp;&nbsp;";
			}
	}
	

class PlanetSetup{

	private $name = "No Name";
	private $ore = 4;
	private $deu = 4;
	private $wet = 0;
	
	private $power = array(5,5,5,5,5,5);
	
	private $builds = array();
	
	
	function __construct($n) {
		$this->name = $n;
		$this->wet = $GLOBALS['wetternahrung'];
	}
	
	function setResources($o,$d) {
		$this->ore = $o;
		$this->deu = $d;
	}
	function setPower($scell,$scomp,$water,$strom,$wind,$sat) {
		$this->power = array($scell,$scomp,$water,$strom,$wind,$sat);
	}
	
	function addBuilding($b,$num=1) {
		for ($i = 0; $i < $num; $i++)
			array_push($this->builds,$b);
	}
	
	
	function addCircuitArray($circ) {
		foreach($circ[builds] as $b) {
			array_push($this->builds,$b);
		}
	}
	
	function addCircuit($name,$num=1) {
		$circuits = $GLOBALS['circuits'];

		foreach($circuits as $c) {
			if ($c[name] == $name) {
				for ($i = 0; $i < $num; $i++)
					$this->addCircuitArray($c);
			}
		}
	}
	
	
	function balanceDeut() {
		
		$val[bev] = 0;
		$val[bev_tot] = 0;
		$val[goods] = array();
		$val[energy] = 0;
		
		$builds = $GLOBALS['builds'];
		$cb = array();
				
		foreach($this->builds as $b) {
			if ($b == 14) {
				$val['goods'][5] += $this->deu;
			} else if ($b == 15) {
				$val['goods'][11] += $this->ore;
			} else {
				foreach($builds[$b][goods] as $k => $v) {
					$val['goods'][$k] += $v;
					if ($b == 2) $val['goods'][1] += $this->wet;
				}
			}
			
			$val['goods'][1] -= $builds[$b][bev_pro] / 10;
			$val[energy] += $builds[$b][eps_proc];
			$val[bev_tot] += $builds[$b][bev_pro];
			$val[bev] += $builds[$b][bev_pro];
			$val[bev] -= $builds[$b][bev_use];
			$cb[$b]++;
		}
		
		if ($val['goods'][5] < 0) {
			$this->addBuilding(14,ceil(-1 * $val['goods'][5]) / $this->deu);
		}
	}
	
	function balanceOre() {
		
		$val[bev] = 0;
		$val[bev_tot] = 0;
		$val[goods] = array();
		$val[energy] = 0;
		
		$builds = $GLOBALS['builds'];
		$cb = array();
				
		foreach($this->builds as $b) {
			if ($b == 14) {
				$val['goods'][5] += $this->deu;
			} else if ($b == 15) {
				$val['goods'][11] += $this->ore;
			} else {
				foreach($builds[$b][goods] as $k => $v) {
					$val['goods'][$k] += $v;
					if ($b == 2) $val['goods'][1] += $this->wet;
				}
			}
			
			$val['goods'][1] -= $builds[$b][bev_pro] / 10;
			$val[energy] += $builds[$b][eps_proc];
			$val[bev_tot] += $builds[$b][bev_pro];
			$val[bev] += $builds[$b][bev_pro];
			$val[bev] -= $builds[$b][bev_use];
			$cb[$b]++;
		}
		
		if ($val['goods'][11] < 0) {
			$this->addBuilding(15,ceil(-1 * $val['goods'][11]) / $this->ore);
		}
	}
	
	function balance() {
		$this->balanceDeut();
		$this->balanceOre();
	}
		
	function __toString() {
		
		$val[bev] = 0;
		$val[bev_tot] = 0;
		$val[goods] = array();
		$val[energy] = 0;
		
		$builds = $GLOBALS['builds'];
		$cb = array();
		
		foreach($this->builds as $b) {
			if ($b == 14) {
				$val['goods'][5] += $this->deu;
			} else if ($b == 15) {
				$val['goods'][11] += $this->ore;
			} else {
				foreach($builds[$b][goods] as $k => $v) {
					$val['goods'][$k] += $v;
					if ($b == 2) $val['goods'][1] += $this->wet;
				}
			}
			
			$val['goods'][1] -= $builds[$b][bev_pro] / 10;
			
			switch($b) {
				
				case  5:	$val[energy] += $this->power[0];				break;
				case 10:	$val[energy] += $this->power[1];				break;
				case 11:	$val[energy] += $this->power[2];				break;
				case 21:	$val[energy] += $this->power[3];				break;
				case 50:	$val[energy] += $this->power[4];				break;
				
				default:  	$val[energy] += $builds[$b][eps_proc];			break;
			}
			
			
			$val[bev_tot] += $builds[$b][bev_pro];
			$val[bev] += $builds[$b][bev_pro];
			$val[bev] -= $builds[$b][bev_use];
			$cb[$b]++;
		}
		
		$r = "";
		
		$r .= "<br><br><b>".$this->name."</b>";
		$r .= "<br><img src=\"../gfx/icons/storage.gif\"/>".count($this->builds)." <img src=\"../gfx/goods/0.gif\"/>".$val[energy]." <img src=\"../gfx/icons/crew.gif\"/>".$val[bev]." <img src=\"../gfx/icons/crewspace.gif\"/>".$val[bev_tot];
			foreach($val['goods'] as $k => $v) {
				$r .= " <img src=\"../gfx/goods/".$k.".gif\"/>".$v;
			}	
			$r .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;";
			foreach($cb as $k => $v) {
				
				$r .= "<img style=\"border: 1px solid #8897cf; width:20px;\" src=\"../gfx/buildings/".$k."/0.png\"/>".$v."&nbsp;&nbsp;";
			}
	
		return $r."<br><br>";
	}
	
	
	
	
	
	
	
	
}
	
	
	
	$m = new PlanetSetup("M-Erdähnlich");
	$m->setResources(4,4);
	$m->setPower(4,8,4,8,8,4);
	$m->addBuilding(1);
	
	$m->addBuilding(6,1);			// BM
	$m->addBuilding(7,2);			// Dura
	$m->addCircuit("Aluminiumwerk");
	
	$m->addCircuit("Siedlung G");
	$m->addCircuit("Siedlung G");
	$m->addCircuit("Siedlung G");
	$m->addCircuit("Siedlung G");
	$m->addCircuit("Siedlung G");
	$m->addCircuit("Siedlung G");
	$m->addCircuit("Siedlung G");
	
	$m->addCircuit("Warpkern");
	$m->addCircuit("Isos");
	$m->addCircuit("Isos");
	$m->addCircuit("Isos");

	$m->addBuilding(18,3);			// Forschungzentrum
	// $m->addBuilding(5,1);			// Solarzellen
	$m->addBuilding(10,5);			// Solarkomplex
	$m->addBuilding(17,3);			// Fusis
	
	$m->addBuilding(25,4);			// AM
	
	$m->addBuilding(4,5);			// Lager = Tote Felder
	
	$m->balance();
	echo $m;
	
	
	
	$m = new PlanetSetup("L-Wald");
	$m->setResources(4,4);
	$m->setPower(4,8,4,8,8,4);
	$m->addBuilding(1);
	
	$m->addBuilding(6,1);			// BM
	$m->addBuilding(7,3);			// Dura
	$m->addCircuit("Aluminiumwerk");
	$m->addCircuit("Aluminiumwerk");

	
	$m->addCircuit("Siedlung G");
	$m->addCircuit("Siedlung G");
	$m->addCircuit("Siedlung G");
	$m->addCircuit("Siedlung G");
	$m->addCircuit("Siedlung G");
	$m->addCircuit("Siedlung G");
	$m->addCircuit("Siedlung G");
	
	$m->addCircuit("Warpkern");
	$m->addCircuit("Warpkern");
	$m->addCircuit("Isos");
	$m->addCircuit("Isos");
	$m->addCircuit("Isos");
	$m->addCircuit("Isos");
	// $m->addCircuit("Isos");

	$m->addBuilding(18,3);			// Forschungzentrum
	// $m->addBuilding(5,1);			// Solarzellen
	// $m->addBuilding(10,5);			// Solarkomplex
	$m->addBuilding(17,2);			// Fusis
	
	// $m->addBuilding(25,2);			// AM
	
	$m->addBuilding(4,5);			// Lager = Tote Felder
	
	$m->balance();
	echo $m;
	
	
	$m = new PlanetSetup("O-Ozean");
	$m->setResources(4,6);
	$m->setPower(4,8,5,10,8,4);
	$m->addBuilding(1);
	
	$m->addBuilding(6,1);			// BM
	$m->addBuilding(7,2);			// Dura
	$m->addCircuit("Aluminiumwerk");
	// $m->addCircuit("Aluminiumwerk");

	
	$m->addCircuit("Siedlung G");
	$m->addCircuit("Siedlung G");
	$m->addCircuit("Siedlung G");
	$m->addCircuit("Siedlung G");
	$m->addCircuit("Siedlung G");
	$m->addCircuit("Siedlung G");
	$m->addCircuit("Siedlung G");
	
	$m->addCircuit("Warpkern");
	$m->addCircuit("Isos");
	$m->addCircuit("Isos");
	$m->addCircuit("Isos");
	// $m->addCircuit("Isos");
	// $m->addCircuit("Isos");

	$m->addBuilding(18,3);			// Forschungzentrum
	// $m->addBuilding(5,1);			// Solarzellen
	$m->addBuilding(21,8);			// Solarkomplex
	$m->addBuilding(17,2);			// Fusis
	
	$m->addBuilding(25,4);			// AM
	
	$m->addBuilding(4,5);			// Lager = Tote Felder
	
	$m->balance();
	echo $m;
	
	
	echo "<br>-------------------------------------------------------------------------------------------------------------------------------------------------------------------------<br>";	
	
	
	$k = new PlanetSetup("K-Ödland");
	$k->setResources(6,4);
	$k->setPower(4,8,4,8,8,4);
	$k->addBuilding(1);
	
	$k->addBuilding(6,1);			// BM
	$k->addBuilding(7,3);			// Dura
	$k->addCircuit("Aluminiumwerk");
	$k->addCircuit("Aluminiumwerk");
	
	$k->addCircuit("Siedlung M");
	$k->addCircuit("Siedlung M");
	$k->addCircuit("Siedlung M");
	$k->addCircuit("Siedlung M");
	$k->addCircuit("Siedlung M");
	
	$k->addCircuit("Warpkern");
	$k->addCircuit("Warpkern");
	$k->addCircuit("Isos");
	$k->addCircuit("Isos");
	$k->addCircuit("Isos");
	$k->addCircuit("Isos");

	$k->addBuilding(29,2);			// Häuser
	$k->addBuilding(41,4);			// Essen
	
	// $k->addBuilding(5,1);			// Solarzellen
	$k->addBuilding(10,6);			// Solarkomplex
	$k->addBuilding(17,1);			// Fusis
	$k->addBuilding(25,2);
	
	$k->addBuilding(4,5);			// Lager = Tote Felder
	
	$k->balance();
	echo $k;	
	
	
	
	
	$k = new PlanetSetup("H-Wüste");
	$k->setResources(6,4);
	$k->setPower(5,10,4,8,8,4);
	$k->addBuilding(1);
	
	$k->addBuilding(6,1);			// BM
	$k->addBuilding(7,4);			// Dura
	$k->addCircuit("Aluminiumwerk");
	$k->addCircuit("Aluminiumwerk");
	
	$k->addCircuit("Siedlung M");
	$k->addCircuit("Siedlung M");
	$k->addCircuit("Siedlung M");
	$k->addCircuit("Siedlung M");
	$k->addCircuit("Siedlung M");
	
	$k->addCircuit("Warpkern");
	$k->addCircuit("Warpkern");
	$k->addCircuit("Isos");
	$k->addCircuit("Isos");
	$k->addCircuit("Isos");
	$k->addCircuit("Isos");

	$k->addBuilding(29,2);			// Häuser
	$k->addBuilding(41,4);			// Essen
	
	// $k->addBuilding(5,1);			// Solarzellen
	$k->addBuilding(10,12);			// Solarkomplex
	// $k->addBuilding(17,1);			// Fusis
	// $k->addBuilding(25,2);
	
	$k->addBuilding(4,5);			// Lager = Tote Felder
	
	$k->balance();
	echo $k;	
	
	
	
	$k = new PlanetSetup("G-Tundra");
	$k->setResources(4,6);
	$k->setPower(4,8,4,8,10,4);
	$k->addBuilding(1);
	
	$k->addBuilding(6,1);			// BM
	$k->addBuilding(7,2);			// Dura
	$k->addCircuit("Aluminiumwerk");
	$k->addCircuit("Aluminiumwerk");
	
	$k->addCircuit("Siedlung M");
	$k->addCircuit("Siedlung M");
	$k->addCircuit("Siedlung M");
	$k->addCircuit("Siedlung M");
	$k->addCircuit("Siedlung M");
	
	$k->addCircuit("Warpkern");
	$k->addCircuit("Warpkern");
	$k->addCircuit("Isos");
	$k->addCircuit("Isos");
	$k->addCircuit("Isos");
	// $k->addCircuit("Isos");

	$k->addBuilding(29,2);			// Häuser
	$k->addBuilding(41,4);			// Essen
	
	// $k->addBuilding(5,1);			// Solarzellen
	$k->addBuilding(26,8);			// Solarkomplex
	$k->addBuilding(17,1);			// Fusis
	$k->addBuilding(25,2);			// AM
	
	$k->addBuilding(4,5);			// Lager = Tote Felder
	
	$k->balance();
	echo $k;	
	
	
	
	echo "<br>-------------------------------------------------------------------------------------------------------------------------------------------------------------------------<br>";
	
	
	
	
	
	
	
	
	
	
	
	$d = new PlanetSetup("D-Fels");
	$d->setResources(8,4);
	$d->setPower(4,8,4,8,8,4);
	$d->addBuilding(1);
	
	// $d->addBuilding(6,1);			// BM
	$d->addBuilding(7,5);			// Dura
	$d->addCircuit("Aluminiumwerk");
	// $k->addCircuit("Aluminiumwerk");
	
	$d->addCircuit("Kuppeln");
	$d->addCircuit("Kuppeln");
	$d->addCircuit("Kuppeln");
	$d->addCircuit("Kuppeln");
	$d->addCircuit("Kuppeln");
	$d->addCircuit("Kuppeln");
	
	// $d->addCircuit("Warpkern");
	$d->addCircuit("Warpkern");
	$d->addCircuit("Warpkern");
	$d->addCircuit("Warpkern");
	$d->addCircuit("Isos");
	$d->addCircuit("Isos");
	$d->addCircuit("Isos");
	$d->addCircuit("Isos");
	$d->addCircuit("Isos");
	$d->addCircuit("Isos");
	// $d->addCircuit("Isos");


	
	// $d->addBuilding(10,5);			// Solar
	
	$d->addBuilding(4,5);			// Lager = Tote Felder
	
	$d->balanceOre();
	echo $d;
	
	
	
	
	
	
	
	
	
	$p = new PlanetSetup("P-Eis");
	$p->setResources(6,8);
	$p->setPower(4,8,4,8,8,4);
	$p->addBuilding(1);
	
	// $d->addBuilding(6,1);			// BM
	$p->addBuilding(7,3);			// Dura
	$p->addCircuit("Aluminiumwerk");
	// $k->addCircuit("Aluminiumwerk");
	
	$p->addCircuit("Kuppeln");
	$p->addCircuit("Kuppeln");
	$p->addCircuit("Kuppeln");
	$p->addCircuit("Kuppeln");
	$p->addCircuit("Kuppeln");
	$p->addCircuit("Kuppeln");
	
	// $d->addCircuit("Warpkern");
	// $p->addCircuit("Warpkern");
	// $p->addCircuit("Warpkern");
	$p->addCircuit("Isos");
	$p->addCircuit("Isos");
	$p->addCircuit("Isos");
	$p->addCircuit("Isos");
	// $p->addCircuit("Isos");
	// $p->addCircuit("Isos");

	$p->addBuilding(17,7);			// Fusis
	$p->addBuilding(25,5);			// AM
	

	
	$p->addBuilding(4,5);			// Lager = Tote Felder
	
	$p->balance();
	echo $p;
	
	
	
	
	
	
	
	
	$p = new PlanetSetup("X-Lava");
	$p->setResources(8,4);
	$p->setPower(4,8,4,8,8,4);
	$p->addBuilding(1);
	
	// $d->addBuilding(6,1);			// BM
	$p->addBuilding(7,4);			// Dura
	$p->addCircuit("Aluminiumwerk");
	// $k->addCircuit("Aluminiumwerk");
	
	$p->addCircuit("Kuppeln");
	$p->addCircuit("Kuppeln");
	$p->addCircuit("Kuppeln");
	$p->addCircuit("Kuppeln");
	$p->addCircuit("Kuppeln");
	$p->addCircuit("Kuppeln");
	
	// $d->addCircuit("Warpkern");
	$p->addCircuit("Warpkern");
	$p->addCircuit("Warpkern");
	$p->addCircuit("Isos");
	$p->addCircuit("Isos");
	$p->addCircuit("Isos");
	$p->addCircuit("Isos");
	$p->addCircuit("Isos");
	$p->addCircuit("Isos");
	
	$p->addBuilding(27,8);			// WärmeKW

	$p->addBuilding(4,5);			// Lager = Tote Felder
	
	$p->balanceOre();
	echo $p;
	
	
	
	
	
	
	
	
	
	
	
	
	
	
// $op .= "<table>";






// echo "</table>";


	
	echo "</body></html>";
?>