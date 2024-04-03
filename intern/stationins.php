<?php

    include_once("../inc/func.inc.php");
    include_once("../inc/config.inc.php");
    include_once($global_path."/class/db.class.php");
    $db = new db;

	$gfx = "http://www.stuniverse.de/gfx/";
	
	function arrayadd($arr,$ins)
	{
		$arr[count]++;
		$arr[$arr[count]] = $ins;
		return $arr;
	}
		
	function setstation($id,$class)
	{
	global $db,$gfx;
        unset($arr);
	if ($class == 11) {

$arr = arrayadd($arr,2);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,5);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,5);
$arr = arrayadd($arr,5);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,3);
$arr = arrayadd($arr,3);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,5);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,5);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,5);
$arr = arrayadd($arr,5);
$arr = arrayadd($arr,5);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,3);
$arr = arrayadd($arr,1);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,6);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,1);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,1);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,6);
$arr = arrayadd($arr,6);
$arr = arrayadd($arr,1);
$arr = arrayadd($arr,1);
$arr = arrayadd($arr,6);
$arr = arrayadd($arr,6);
$arr = arrayadd($arr,6);
$arr = arrayadd($arr,6);
$arr = arrayadd($arr,6);

	}
	elseif ($class == 12)
	{



$arr = arrayadd($arr,6);
$arr = arrayadd($arr,6);

$arr = arrayadd($arr,5);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,3);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,5);

$arr = arrayadd($arr,5);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,1);
$arr = arrayadd($arr,1);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,5);

$arr = arrayadd($arr,6);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,6);

$arr = arrayadd($arr,6);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,3);
$arr = arrayadd($arr,3);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,6);

$arr = arrayadd($arr,5);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,1);
$arr = arrayadd($arr,1);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,5);

$arr = arrayadd($arr,5);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,1);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,5);

$arr = arrayadd($arr,6);
$arr = arrayadd($arr,6);


















	} 
	elseif ($class == 13)
	{



$arr = arrayadd($arr,5);
$arr = arrayadd($arr,1);
$arr = arrayadd($arr,1);
$arr = arrayadd($arr,5);

$arr = arrayadd($arr,5);
$arr = arrayadd($arr,3);
$arr = arrayadd($arr,3);
$arr = arrayadd($arr,3);
$arr = arrayadd($arr,5);

$arr = arrayadd($arr,5);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,5);


$arr = arrayadd($arr,5);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,1);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,5);

$arr = arrayadd($arr,1);

$arr = arrayadd($arr,2);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,2);

$arr = arrayadd($arr,2);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,2);

$arr = arrayadd($arr,6);
$arr = arrayadd($arr,1);
$arr = arrayadd($arr,6);

$arr = arrayadd($arr,6);
$arr = arrayadd($arr,6);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,6);
$arr = arrayadd($arr,6);

$arr = arrayadd($arr,6);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,6);



	}
	elseif ($class == 14)
	{

$arr = arrayadd($arr,2);

$arr = arrayadd($arr,5);
$arr = arrayadd($arr,1);
$arr = arrayadd($arr,1);
$arr = arrayadd($arr,1);
$arr = arrayadd($arr,5);

$arr = arrayadd($arr,5);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,5);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,5);

$arr = arrayadd($arr,6);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,6);

$arr = arrayadd($arr,2);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,2);

$arr = arrayadd($arr,1);
$arr = arrayadd($arr,3);
$arr = arrayadd($arr,6);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,3);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,6);
$arr = arrayadd($arr,3);
$arr = arrayadd($arr,1);

$arr = arrayadd($arr,4);
$arr = arrayadd($arr,6);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,6);
$arr = arrayadd($arr,4);

$arr = arrayadd($arr,6);
$arr = arrayadd($arr,6);

$arr = arrayadd($arr,5);
$arr = arrayadd($arr,5);
$arr = arrayadd($arr,5);

$arr = arrayadd($arr,4);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,4);





	}
	elseif ($class == 15)
	{

$arr = arrayadd($arr,5);
$arr = arrayadd($arr,5);

$arr = arrayadd($arr,5);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,5);

$arr = arrayadd($arr,5);
$arr = arrayadd($arr,1);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,3);
$arr = arrayadd($arr,1);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,1);
$arr = arrayadd($arr,5);

$arr = arrayadd($arr,6);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,6);

$arr = arrayadd($arr,2);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,2);

$arr = arrayadd($arr,3);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,3);

$arr = arrayadd($arr,5);
$arr = arrayadd($arr,1);
$arr = arrayadd($arr,1);
$arr = arrayadd($arr,5);

$arr = arrayadd($arr,6);
$arr = arrayadd($arr,6);

$arr = arrayadd($arr,4);
$arr = arrayadd($arr,4);

$arr = arrayadd($arr,6);
$arr = arrayadd($arr,6);

$arr = arrayadd($arr,6);
$arr = arrayadd($arr,6);

$arr = arrayadd($arr,4);
$arr = arrayadd($arr,4);



	}

	elseif ($class == 16)
	{

$arr = arrayadd($arr,5);
$arr = arrayadd($arr,5);
$arr = arrayadd($arr,5);
$arr = arrayadd($arr,5);

$arr = arrayadd($arr,2);
$arr = arrayadd($arr,3);
$arr = arrayadd($arr,2);

$arr = arrayadd($arr,5);
$arr = arrayadd($arr,3);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,1);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,3);
$arr = arrayadd($arr,5);

$arr = arrayadd($arr,1);
$arr = arrayadd($arr,1);

$arr = arrayadd($arr,5);
$arr = arrayadd($arr,5);

$arr = arrayadd($arr,2);
$arr = arrayadd($arr,1);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,1);
$arr = arrayadd($arr,2);

$arr = arrayadd($arr,4);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,4);

$arr = arrayadd($arr,2);
$arr = arrayadd($arr,2);

$arr = arrayadd($arr,4);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,4);

$arr = arrayadd($arr,6);
$arr = arrayadd($arr,6);

$arr = arrayadd($arr,6);
$arr = arrayadd($arr,6);
$arr = arrayadd($arr,6);
$arr = arrayadd($arr,6);
$arr = arrayadd($arr,6);
$arr = arrayadd($arr,6);
	}
	elseif ($class == 4) {


$arr = arrayadd($arr,5);
$arr = arrayadd($arr,5);

$arr = arrayadd($arr,1);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,1);

$arr = arrayadd($arr,5);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,3);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,5);

$arr = arrayadd($arr,4);
$arr = arrayadd($arr,4);

$arr = arrayadd($arr,1);
$arr = arrayadd($arr,1);

$arr = arrayadd($arr,6);
$arr = arrayadd($arr,5);
$arr = arrayadd($arr,6);
$arr = arrayadd($arr,5);
$arr = arrayadd($arr,6);

$arr = arrayadd($arr,2);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,1);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,2);

$arr = arrayadd($arr,1);
$arr = arrayadd($arr,1);

$arr = arrayadd($arr,4);

$arr = arrayadd($arr,4);

$arr = arrayadd($arr,6);
$arr = arrayadd($arr,6);

	}

	elseif ($class == 3) {


$arr = arrayadd($arr,1);
$arr = arrayadd($arr,1);
$arr = arrayadd($arr,2);

$arr = arrayadd($arr,5);
$arr = arrayadd($arr,1);
$arr = arrayadd($arr,1);
$arr = arrayadd($arr,5);

$arr = arrayadd($arr,6);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,6);

$arr = arrayadd($arr,5);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,4);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,5);


$arr = arrayadd($arr,1);
$arr = arrayadd($arr,1);


	}

	elseif ($class == 2) {


$arr = arrayadd($arr,5);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,5);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,5);

$arr = arrayadd($arr,4);

$arr = arrayadd($arr,6);
$arr = arrayadd($arr,6);
$arr = arrayadd($arr,6);

$arr = arrayadd($arr,4);

$arr = arrayadd($arr,6);
$arr = arrayadd($arr,6);
$arr = arrayadd($arr,6);

$arr = arrayadd($arr,4);

$arr = arrayadd($arr,6);
$arr = arrayadd($arr,1);
$arr = arrayadd($arr,2);
$arr = arrayadd($arr,1);
$arr = arrayadd($arr,6);

$arr = arrayadd($arr,6);
$arr = arrayadd($arr,1);
$arr = arrayadd($arr,1);
$arr = arrayadd($arr,6);


	}
	elseif ($class == 1) {


$arr = arrayadd($arr,9);
$arr = arrayadd($arr,9);
$arr = arrayadd($arr,9);
$arr = arrayadd($arr,9);
$arr = arrayadd($arr,9);



	}




	echo "blubber: ".$class." (".$arr[count]." Fields)";


	$db->query("DELETE FROM stu_stations_fielddata WHERE stations_id = ".$id."");
	for ($i = 1; $i <= $arr[count]; $i++) {
		echo "<br>".$i.": ".$arr[$i];
		$db->query("INSERT INTO stu_stations_fielddata (stations_id,field_id,type) VALUES ('".$id."','".$i."','".$arr[$i]."')");
	}


	}


?>
