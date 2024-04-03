<?php

    include_once("../inc/func.inc.php");
    include_once("../inc/config.inc.php");
    include_once($global_path."/class/db.class.php");
    $db = new db;

	$gfx = "http://www.stuniverse.de/gfx/";
	



		$result = $db->query("SELECT a.*,b.baseshield,b.baseeps,b.basebev,b.basearmor,b.basewk,b.baselager FROM stu_stations as a LEFT OUTER JOIN stu_stations_classes as b on a.stations_classes_id = b.stations_classes_id WHERE a.stations_classes_id != 99");
		while($data=mysql_fetch_assoc($result))
		{

			$m = "";

			if ($data[max_armor] != $data[basearmor]) $m .= " | ARM";	
			$armor = $data[basearmor];

			$eps = $db->query("SELECT sum(b.eps) FROM stu_stations_fielddata as a LEFT OUTER JOIN stu_station_components as b ON a.component_id = b.component_id WHERE a.stations_id = ".$data[id]." LIMIT 1",1) + $data[baseeps];
			if ($eps != $data[max_eps]) $m .= " | EPS: ".$eps."<-".$data[max_eps];

			$shd = $db->query("SELECT sum(b.schilde) FROM stu_stations_fielddata as a LEFT OUTER JOIN stu_station_components as b ON a.component_id = b.component_id WHERE a.stations_id = ".$data[id]." AND a.aktiv = 1 LIMIT 1",1) + $data[baseshield];
			if ($shd != $data[max_schilde]) $m .= " | SHD: ".$shd."<-".$data[max_schilde];			
			//$sto = $db->query("SELECT sum(b.lager) FROM stu_stations_fielddata as a LEFT OUTER JOIN stu_station_components as b ON a.component_id = b.component_id WHERE a.stations_id = ".$data[id]." LIMIT 1",1) + $data[baselager];
			//if ($sto != $data[max_storage]) $m .= " | STO: ".$sto."<-".$data[max_storage];

			//$bev = $db->query("SELECT sum(b.bev_pro) FROM stu_stations_fielddata as a LEFT OUTER JOIN stu_station_components as b ON a.component_id = b.component_id WHERE a.stations_id = ".$data[id]." AND a.aktiv = 1 LIMIT 1",1) + $data[basebev];
			//if ($bev != $data[bev_max]) $m .= " | BEV: ".$bev."<-".$data[bev_max];

			//$wkl = $db->query("SELECT sum(b.wk_proc) FROM stu_stations_fielddata as a LEFT OUTER JOIN stu_station_components as b ON a.component_id = b.component_id WHERE a.stations_id = ".$data[id]." LIMIT 1",1) + $data[basewk];
			//if ($wkl != $data[wkload_max]) $m .= " | WKL: ".$wkl."<-".$data[wkload_max];			
			if ($m != "") $db->query("UPDATE stu_stations set max_schilde = ".$shd.",max_armor=".$armor." WHERE id = ".$data[id]."");
			
			if ($m != "") {
				echo "<br>".$data[name]." (".$data[id].")";
				echo $m."<br><br>";
			}
		}







?>
