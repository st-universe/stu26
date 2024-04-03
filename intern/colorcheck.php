<?php

    include_once("../inc/func.inc.php");
    include_once("../inc/config.inc.php");
    include_once($global_path."/class/db.class.php");
    $db = new db;

	echo "<body bgcolor=black><font color=#5050aa>";

	$test = "<b><font color=BB1100>[<font color=#ffffff>CMB</font>] </font><font color=0b0b0b>Qugh</font></b>";



	function isolatecolors($test){
		$new = $test;
		$new = str_replace(" ","",$new);
		$new = str_replace("'","",$new);
		$new = str_replace('"',"",$new);
		$new = str_replace("#","",$new);

		$old = $new;

		$new = strtolower($new);

			
		$counter = 0;

		for ($i = 0; $i < 10; $i++) {
			$p = strpos($new,"color");
			if ($p == 0) break;
			$temp = substr($old,$p+6,6);
			$ishex = true;
			for ($k = 0; $k < 6; $k++) {
				$c = substr($temp,$k,1);
				if (($c != "0") && (hexdec($c) == 0)) {
					$ishex = false;
					break;
				}
			}
			if ($ishex) {
				$fonts[$counter] = $temp;
				$counter++;
			}
			$new = substr($new,$p+12);
			$old = substr($old,$p+12);
		}
		$fonts[count] = $counter;
		return $fonts;
	}

	function getcolorvalues($test){

		for ($i = 0; $i < $test[count]; $i++) {
			$res[$i][r] = hexdec(substr($test[$i],0,2));
			$res[$i][g] = hexdec(substr($test[$i],2,2));
			$res[$i][b] = hexdec(substr($test[$i],4,2));
			$res[$i][s] = $res[$i][r]+$res[$i][g]+$res[$i][b];
			$res[$i][m] = max($res[$i][r],$res[$i][g],$res[$i][b]);
			$res[$i][orig] = $test[$i];
			$res[count] = $test[count];
		}
		
		return $res;
	}

	function checkcolors($test) {
		for ($i = 0; $i < $test[count]; $i++) {
			if ($test[$i][m] < 140) {
				$add = ceil((140-$test[$i][m]));
				if ($test[$i][s] == 0) $test[$i][s] = 1;
				if ($test[$i][r] == 0) $fac = 1;
				else $fac = $test[$i][r];
				$test[$i][r] += ceil(($fac/$test[$i][s]) * $add);
				if ($test[$i][r] > 255) $test[$i][r] = 255;

				if ($test[$i][g] == 0) $fac = 1;
				else $fac = $test[$i][g];
				$test[$i][g] += ceil(($fac/$test[$i][s]) * $add);
				if ($test[$i][g] > 255) $test[$i][g] = 255;

				if ($test[$i][b] == 0) $fac = 1;
				else $fac = $test[$i][b];
				$test[$i][b] += ceil(($fac/$test[$i][s]) * $add);
				if ($test[$i][b] > 255) $test[$i][b] = 255;

				$test[$i][replace] = 1;
			
				if ($test[$i][r] < 16) $test[$i][n] = "0";
				$test[$i][n] .= dechex($test[$i][r]);
				if ($test[$i][g] < 16) $test[$i][n] .= "0";
				$test[$i][n] .= dechex($test[$i][g]);
				if ($test[$i][b] < 16) $test[$i][n] .= "0";
				$test[$i][n] .= dechex($test[$i][b]);
			}
			else {
				$test[$i][replace] = 0;

			}
		}
		return $test;
	}

	function replacecolors($test,$orig) {
		for ($i = 0; $i < $test[count]; $i++) {
			if ($test[$i][replace] == 1) {
				$orig = str_replace($test[$i][orig],$test[$i][n],$orig);
			}

		}
		return $orig;
	}

	function colorcorrection($test) {
		return replacecolors(checkcolors(getcolorvalues(isolatecolors($test))),$test);

	}

	$res = $db->query("SELECT user FROM stu_user WHERE 1 order by id asc");

	while($data=mysql_fetch_assoc($res))
	{

		echo $data[user];
		echo "<br>";
		echo colorcorrection($data[user]);
		echo "<br><br>";

	}


?>
