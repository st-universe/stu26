<?php
include_once("/srv/www/stu_sys/webroot/inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;

function getpossiblewheater($class)
{
	switch ($class)
	{
		case 1:
			$dat[temp] = rand(10,15);
			$dat[w] = array(1,2,3,4,5);
			return $dat;
		case 2:
			$dat[temp] = rand(10,15);
			$dat[w] = array(1,2,3,4,5);
			return $dat;
		case 3:
			$dat[temp] = rand(10,15);
			$dat[w] = array(1,2,3,4,5);
			return $dat;
		case 4:
			$dat[temp] = rand(25,55);
			$dat[w] = array(1,7);
			return $dat;
		case 5:
			$dat[temp] = rand(25,60)*-1;
			$dat[w] = array(1,2,5,6);
			return $dat;
		case 6:
			$dat[temp] = rand(5,10);
			$dat[w] = array(1,2,3,4,5);
			return $dat;
		case 7:
			$dat[temp] = rand(20,30);
			$dat[w] = array(1,8);
			return $dat;
		case 8:
			$dat[temp] = rand(20,30);
			$dat[w] = array(1,8);
			return $dat;
		case 9:
			$dat[temp] = rand(35,60);
			$dat[w] = array(1);
			return $dat;
		case 10:
			$dat[temp] = rand(10,15);
			$dat[w] = array(1,2,3,4);
			return $dat;
		case 20:
			$dat[temp] = rand(10,15);
			$dat[w] = array(1,2,3,4,5);
			return $dat;
		case 21:
			$dat[temp] = rand(10,15);
			$dat[w] = array(1,2,3,4,5);
			return $dat;
		case 22:
			$dat[temp] = rand(10,15);
			$dat[w] = array(1,2,3,4,5);
			return $dat;
		case 23:
			$dat[temp] = rand(25,55);
			$dat[w] = array(1,7);
			return $dat;
		case 24:
			$dat[temp] = rand(25,60)*-1;
			$dat[w] = array(1,2,5,6);
			return $dat;
		case 25:
			$dat[temp] = rand(5,10);
			$dat[w] = array(1,2,3,4,5);
			return $dat;
		case 26:
			$dat[temp] = rand(20,30);
			$dat[w] = array(1,8);
			return $dat;
		case 27:
			$dat[temp] = rand(20,30);
			$dat[w] = array(1,8);
			return $dat;
		case 28:
			$dat[temp] = rand(35,60);
			$dat[w] = array(1);
			return $dat;
		case 29:
			$dat[temp] = rand(35,60);
			$dat[w] = array(1);
			return $dat;
		case 30:
			$dat[temp] = rand(35,60);
			$dat[w] = array(1);
			return $dat;
	}
}

function getspecifictemp($w)
{
	switch ($w)
	{
		case 1:
			return rand(5,15);
		case 2:
			return rand(1,9);
		case 3:
			return rand(1,9)*-1;
		case 4:
			return rand(1,9)*-1;
		case 5:
			return rand(10,25)*-1;
		case 6:
			return rand(25,50)*-1;
		case 7:
			return rand(25,50);
		case 8:
			return rand(1,5);
	}
}

$result = $db->query("SELECT id,colonies_classes_id FROM stu_colonies");
while($data=mysql_fetch_assoc($result))
{
	$dat = getpossiblewheater($data[colonies_classes_id]);
	$index = array_rand($dat[w]);
	$dat[w] = $dat[w][$index];
	$dat[temp] += getspecifictemp($dat[w]);
	$db->query("UPDATE stu_colonies SET w_type=".$dat[w].",w_temp=".$dat[temp]." WHERE id=".$data[id]);
}
?>
