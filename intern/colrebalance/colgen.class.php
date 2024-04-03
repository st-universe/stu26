<?php

	
class ColonyGenerator{

	private $datapath = "coldata/";

	function __construct() {
		$this->datapath = "coldata/";
	}
	
	
	
	
	function encode($colfields) {
	
		$s = "";
		ksort($colfields);
		foreach($colfields as $k => $v) {
		
			if ($v < 16) $c = "0";
			else $c = "";
			
			$c .= dechex($v);
			
			$s .= $c;
		}
		
		return $s;
	}
	
	
	
	
	
	
	
	function weighteddraw($a,$fragmentation=0)
	{
		for ($i = 0; $i < count($a);$i++)
		{
			$a[$i][weight] = rand(1,ceil($a[$i][baseweight] + $fragmentation));
		}
		usort($a,	function ($a, $b)
					{
						if ($a[weight] < $b[weight]) return +1;
						if ($a[weight] > $b[weight]) return -1;
						return (rand(1,3)-2);
					});
					
		return $a[0];
	}
	
	function madd($arr,$ele,$cnt) {
		for ($i = 0; $i < $cnt; $i++) {
			array_push($arr,$ele);
		}
		shuffle($arr);
		return $arr;
	}
	

	function shadd($arr,$fld,$bonus) {

		array_push($arr[from],	$fld);
		array_push($arr[to],	$fld.$bonus);

		return $arr;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function getweightinglist($colfields,$mode,$from,$to,$adjacent,$noadjacent,$noadjacentlimit=0,$adjdirection=0)
	{

		$w = $colfields[w]; //count($colfields);		
		$h = count($colfields[0]);
		$c = 0;
		for ($i = 0; $i < $h; $i++)
		{
			for ($j = 0; $j < $w; $j++)
			{
				$skip = 1;
				for ($k = 0; $k < count($from); $k++)
				{
					if ($colfields[$j][$i] == $from[$k]) $skip = 0;
				}
				if ($skip == 1) continue;
	
				$bw = 1;
				if ((($mode == "polar") || ($mode == "strict polar")) && ($i == 0 || $i == $h-1)) $bw += 1;
				if (($mode == "polar seeding north") && ($i == 0)) $bw += 2;
				if (($mode == "polar seeding south") && ($i == $h-1)) $bw += 2;

				if (($mode == "equatorial") && (($i == 2 && $h == 5) || (($i == 2 || $i == 3) && $h == 6))) $bw += 1;
				
				if ($mode != "nocluster" && $mode != "forced adjacency" && $mode != "forced rim"  && $mode != "polar seeding north" && $mode != "polar seeding south") 
				{
					for ($k = 0; $k < count($to); $k++)
					{
						if ($colfields[$j-1][$i] == $to[$k]) $bw += 1;
						if ($colfields[$j+1][$i] == $to[$k]) $bw += 1;
						if ($colfields[$j][$i-1] == $to[$k]) $bw += 1;
						if ($colfields[$j][$i+1] == $to[$k]) $bw += 1;
						if ($colfields[$j-1][$i-1] == $to[$k]) $bw += 0.5;
						if ($colfields[$j+1][$i+1] == $to[$k]) $bw += 0.5;
						if ($colfields[$j+1][$i-1] == $to[$k]) $bw += 0.5;
						if ($colfields[$j-1][$i+1] == $to[$k]) $bw += 0.5;
					}
				}

				if (($mode == "polar seeding north") && ($i == 0)) 
				{
					for ($k = 0; $k < count($to); $k++)
					{
						if ($colfields[$j-1][$i] == $to[$k]) $bw += 2;
						if ($colfields[$j+1][$i] == $to[$k]) $bw += 2;
					}
				}
				if ((($mode == "polar seeding south") && ($i == $h-1))) 
				{
					for ($k = 0; $k < count($to); $k++)
					{
						if ($colfields[$j-1][$i] == $to[$k]) $bw += 2;
						if ($colfields[$j+1][$i] == $to[$k]) $bw += 2;
					}
				}
				
				if ($adjacent[0]) {
				for ($k = 0; $k < count($adjacent); $k++)
				{
					if ($colfields[$j-1][$i] == $adjacent[$k]) $bw += 1;
					if ($colfields[$j+1][$i] == $adjacent[$k]) $bw += 1;
					if ($colfields[$j][$i-1] == $adjacent[$k]) $bw += 1;
					if ($colfields[$j][$i+1] == $adjacent[$k]) $bw += 1;
					if ($colfields[$j-1][$i-1] == $adjacent[$k]) $bw += 0.5;
					if ($colfields[$j+1][$i+1] == $adjacent[$k]) $bw += 0.5;
					if ($colfields[$j+1][$i-1] == $adjacent[$k]) $bw += 0.5;
					if ($colfields[$j-1][$i+1] == $adjacent[$k]) $bw += 0.5;
				}}

				if ($noadjacent[0]) {
				for ($k = 0; $k < count($noadjacent); $k++)
				{
					$ad = 0;
					if ($colfields[$j-1][$i] == $noadjacent[$k]) $ad += 1;
					if ($colfields[$j+1][$i] == $noadjacent[$k]) $ad += 1;
					if ($colfields[$j][$i-1] == $noadjacent[$k]) $ad += 1;
					if ($colfields[$j][$i+1] == $noadjacent[$k]) $ad += 1;
					if ($colfields[$j-1][$i-1] == $noadjacent[$k]) $ad += 0.5;
					if ($colfields[$j+1][$i+1] == $noadjacent[$k]) $ad += 0.5;
					if ($colfields[$j+1][$i-1] == $noadjacent[$k]) $ad += 0.5;
					if ($colfields[$j-1][$i+1] == $noadjacent[$k]) $ad += 0.5;
					
					if ($mode == "large noadjacent") {
						if ($colfields[$j-2][$i] == $noadjacent[$k]) $ad += 1;
						if ($colfields[$j+2][$i] == $noadjacent[$k]) $ad += 1;
						if ($colfields[$j][$i-2] == $noadjacent[$k]) $ad += 1;
						if ($colfields[$j][$i+2] == $noadjacent[$k]) $ad += 1;
					}
					if ($ad > $noadjacentlimit) $bw = 0;					
				}}

				if (($mode == "forced adjacency") && ($bw < 2)) $bw = 0;
				if (($mode == "forced rim") && ($bw < 1.5)) $bw = 0;
				
				if (($mode == "polar") && ($i > 1) && ($i < $h-2)) $bw = 0;
				if (($mode == "strict polar") && ($i > 0) && ($i < $h-1)) $bw = 0;
				
				
				// if ($mode == "polar seeding north" && ($i > 1)) $bw = 0;
				// if ($mode == "polar seeding south" && ($i < $h-2)) $bw = 0;		
				if ($mode == "polar seeding north" && ($i > 0)) $bw = 0;
				if ($mode == "polar seeding south" && ($i < $h-1)) $bw = 0;	

				
				if (($mode == "equatorial") && (($i < 2) || ($i > 3)) && ($h == 6)) $bw = 0;
				if (($mode == "equatorial") && (($i < 2) || ($i > 3)) && ($h == 5)) $bw = 0;
				
				if (($mode == "lower orbit") && ($i != 1)) $bw = 0;
				if (($mode == "upper orbit") && ($i != 0)) $bw = 0;

				if (($mode == "tidal seeding") && ($j != 0)) $bw = 0;
				
				if (($mode == "right") && ($colfields[$j-1][$i] != $adjacent[0])) $bw = 0;
				if (($mode == "below") && ($colfields[$j][$i-1] != $adjacent[0])) $bw = 0;
				if (($mode == "crater seeding") && (($j == $w-1) || ($i == $h-1))) {
					$bw = 0;
				}
				
				
				if (($mode == "river") || ($mode == "river end")) {
					$bw = 0;
					unset($ssto);					
					$ssto = array();
					$res[$c][to] = array();

					for ($k = 0; $k < count($to); $k++)
					{
					// ($colfields,$mode,$from,$to,$adjacent,$noadjacent,$noadjacentlimit=0,$adjdirection=0)
						if ($from[$k] != $colfields[$j][$i]) continue;
						if (($adjdirection[$k] == "we") && ($colfields[$j+1][$i+0] == $adjacent[$k])) {
							$bw = 2;
							array_push($ssto,$to[$k]);
						}
						if (($adjdirection[$k] == "ea") && ($colfields[$j-1][$i+0] == $adjacent[$k])) {
							$bw = 2;
							array_push($ssto,$to[$k]);
						}
						if (($adjdirection[$k] == "so") && ($colfields[$j+0][$i-1] == $adjacent[$k])) {
							$bw = 2;
							array_push($ssto,$to[$k]);
						}
						if (($adjdirection[$k] == "no") && ($colfields[$j+0][$i+1] == $adjacent[$k])) {
							$bw = 2;
							array_push($ssto,$to[$k]);
						}
					

					}
					$res[$c][to] = $ssto;
				}
					
					
					
					
				if ($bw > 0)
				{
					//echo "<br>".$bw." - ".$j."|".$i;
					$res[$c][x] = $j;
					$res[$c][y] = $i;
					$res[$c][baseweight] = $bw;
					$c++;
				}
			}
		}

		return $res;
	}

	function dophase($p,$phase,$colfields)
	{
		if ($phase[$p]['mode'] == "fullsurface") {
			$k = 0;
			for ($ih = 0; $ih < $colfields[h]; $ih++) {
				for ($iw = 0; $iw < $colfields[w]; $iw++) {

					$k++;
		
					$colfields[$iw][$ih] = $phase[$p][type] * 100 + $k;
				}
			}
		
		} else if ($phase[$p]['mode'] == "connecting") {
			$k = 0;
			
// ("221","222","223","224","225","226","231","233","235","237")	
			$streamfields = array();
			for ($ih = 0; $ih < $colfields[h]; $ih++) {
				for ($iw = 0; $iw < $colfields[w]; $iw++) {
				
					if ($colfields[$iw][$ih] > 300) $streamfields[$colfields[$iw][$ih]-300] = array($iw,$ih);
				
				}
			}
			
			foreach($phase[$p]['origin'] as $o) {
				$ccx = $streamfields[1][0] + 1;
				$ccy = $streamfields[1][1] + 0;
				if ($colfields[$ccx][$ccy] == $o) {
					$streamfields[0] = array($ccx,$ccy);
					break;
				}
				
				$ccx = $streamfields[1][0] + 0;
				$ccy = $streamfields[1][1] + 1;
				if ($colfields[$ccx][$ccy] == $o) {
					$streamfields[0] = array($ccx,$ccy);
					break;
				}
				
				$ccx = $streamfields[1][0] - 1;
				$ccy = $streamfields[1][1] + 0;
				if ($colfields[$ccx][$ccy] == $o) {
					$streamfields[0] = array($ccx,$ccy);
					break;
				}
				
				$ccx = $streamfields[1][0] + 0;
				$ccy = $streamfields[1][1] - 1;
				if ($colfields[$ccx][$ccy] == $o) {
					$streamfields[0] = array($ccx,$ccy);
					break;
				}				
			}

			$transformations = array();
			ksort($streamfields);
			foreach($streamfields as $k => $fd) {
				if ($k == 0) continue;
				
				$cntr = 0;
				if ($k+1 < count($streamfields)){
					if ($streamfields[$k+1][0] == $fd[0]+1) $cntr += 2;
					if ($streamfields[$k+1][0] == $fd[0]-1) $cntr += 8;
					if ($streamfields[$k+1][1] == $fd[1]-1) $cntr += 1;
					if ($streamfields[$k+1][1] == $fd[1]+1) $cntr += 4;
				}
				if ($streamfields[$k-1][0] == $fd[0]+1) $cntr += 2;
				if ($streamfields[$k-1][0] == $fd[0]-1) $cntr += 8;
				if ($streamfields[$k-1][1] == $fd[1]-1) $cntr += 1;
				if ($streamfields[$k-1][1] == $fd[1]+1) $cntr += 4;		

				// echo "<br>".$cntr;
					
				if ($cntr == 1) $cntr = 21;
				if ($cntr == 2) $cntr = 22;
				if ($cntr == 4) $cntr = 23;
				if ($cntr == 8) $cntr = 24;
				$transformations[$k] = 220 + $cntr;
			}
			// print_r($streamfields);
			// print_r($transformations);
			
			foreach($streamfields as $k => $fd) {
				if ($k == 0) continue;
				// echo "<br>".$transformations[$k]."<img src='fields/".$transformations[$k].".gif'>";
				$colfields[$fd[0]][$fd[1]] = $transformations[$k];
			}
		} else {
	
			for ($i = 0; $i < $phase[$p][num]; $i++)
			{
				$arr = $this->getweightinglist($colfields,$phase[$p][mode],$phase[$p][from],$phase[$p][to],$phase[$p][adjacent],$phase[$p][noadjacent],$phase[$p][noadjacentlimit],$phase[$p][adjacencydir]);
				if (count($arr) == 0) { 
					break;
				}
				
				$field = $this->weighteddraw($arr,$phase[$p][fragmentation]);
				$ftype = $colfields[$field[x]][$field[y]];

				$t = 0;
				unset($ta);	
				if ($phase[$p][mode] == "river") {				
					$ta = array_unique($field[to]);
					shuffle($ta);

					$t = count($ta);
				} else if ($phase[$p][mode] == "river end") {
					$ta = array_unique($field[to]);
					shuffle($ta);

					$t = count($ta);
				}
				else {
					for ($c = 0; $c < count($phase[$p][from]); $c++)
					{
							if ($ftype == $phase[$p][from][$c]) {
								$ta[$t] = $phase[$p][to][$c];
								$t++;
							}
					}
				}
				if ($t > 0) $colfields[$field[x]][$field[y]] = $ta[rand(0,$t-1)];
			}
			
		}
		return $colfields;
	}

	
	function combine($col,$orb,$gnd)
	{

		$q = 0;
		for ($i = 0; $i < $orb[h]; $i++)
		{
			for ($j = 0; $j < $orb[w]; $j++)
			{
				$res[$q] = $orb[$j][$i];
				$q++;
			}
		}
		
		for ($i = 0; $i < $col[h]; $i++)
		{
			for ($j = 0; $j < $col[w]; $j++)
			{
				$res[$q] = $col[$j][$i];
				$q++;
			}
		}
		
		for ($i = 0; $i < $gnd[h]; $i++)
		{
			for ($j = 0; $j < $gnd[w]; $j++)
			{
				$res[$q] = $gnd[$j][$i];
				$q++;
			}
		}

		//$res[length] = $q;
	
		return $res;
	
	}
	
	function generateColony($id,$bonusfields=2)
	{
		
		if (!file_exists($this->datapath.$id.".php")) return false;

		$bonusdata = array();
		
		include($this->datapath.$id.".php");
		
		$log = "";
				
		$h = $data[sizeh];
		$w = $data[sizew];

		for ($i = 0; $i < $h; $i++)
		{
			for ($j = 0; $j < $w; $j++)
			{
				$colfields[$j][$i] = $data[basefield];
			}
		}
		$colfields[h] = $h;
		$colfields[w] = $w;
		
		for ($i = 0; $i < 2; $i++)
		{
			for ($j = 0; $j < $w; $j++)
			{
				$orbfields[$j][$i] = $odata[basefield];
			}
		}
		$orbfields[h] = 2;
		$orbfields[w] = $w;
		
		$gndfields[h] = 0;
		if ($hasground) 
		{
			
			if ($colfields[h] > 5) $gndfields[h] = 2;
			else $gndfields[h] = 1;
			
			for ($i = 0; $i < $gndfields[h]; $i++)
			{
				for ($j = 0; $j < $w; $j++)
				{
					$gndfields[$j][$i] = $udata[basefield];
				}
			}	

			$gndfields[w] = $w;
			
			// print_r($gndfields);
		}

		for ($i = 0; $i < $phases; $i++)
		{
			$log = $log ."<br>".$phase[$i][description];

			$colfields = $this->dophase($i,$phase,$colfields);
		}
		
		for ($i = 0; $i < $ophases; $i++)
		{
			$log = $log ."<br>".$ophase[$i][description];

			$orbfields = $this->dophase($i,$ophase,$orbfields);
		}
		
		for ($i = 0; $i < $uphases; $i++)
		{
			$log = $log ."<br>".$uphase[$i][description];

			$gndfields = $this->dophase($i,$uphase,$gndfields);
		}
		
		for ($i = 0; $i < $bphases; $i++)
		{
			$log = $log ."<br>".$bphase[$i][description];

			$colfields = $this->dophase($i,$bphase,$colfields);
		}
		
		$cphase[0][mode] = "normal";
		$cphase[0][description] = "Ground Cloaking";
		$cphase[0][num] = 21;
		$cphase[0][from] = array(81,82,83,84);
		$cphase[0][to]   = array(71,72,73,74);
		$cphase[0][adjacent] = 0;
		$cphase[0][noadjacent] = 0;
		$cphase[0][noadjacentlimit] = 0;
		$cphase[0][fragmentation] = 0;	
		
		$gndfields = $this->dophase(0,$cphase,$gndfields);
		
	
		return $this->combine($colfields,$orbfields,$gndfields);
	}

}


?>