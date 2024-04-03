<?php
include_once("../../inc/config.inc.php");
include_once($global_path."/inc/func.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;
// Bev
//echo "Wohnraum<br>";
$result = $db->query("SELECT id,bev_max FROM stu_colonies WHERE user_id>1");
while($data=mysql_fetch_assoc($result))
{
	$res = $db->query("SELECT SUM(b.bev_pro) FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.colonies_id=".$data[id]." AND a.aktiv=1",1);
	if ($res != $data[bev_max])
	{
		$db->query("UPDATE stu_colonies SET bev_max=".$res." WHERE id=".$data[id]);
		//echo $data[id]." - ".$data[bev_max]." - ".$res."<br>";
	}
}


// EPS
//echo "EPS<br>";
$result = $db->query("SELECT id,max_eps FROM stu_colonies WHERE user_id>1");
while($data=mysql_fetch_assoc($result))
{
	$res = $db->query("SELECT SUM(b.eps) FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.aktiv<2 AND a.colonies_id=".$data[id],1);
	if ($res != $data[max_eps])
	{
		$db->query("UPDATE stu_colonies SET max_eps=".$res." WHERE id=".$data[id]);
		//echo $data[id]." - ".$data[max_eps]." - ".$res."<br>";
	}
}


// Arbeiter
//echo "Arbeiter<br>";
$result = $db->query("SELECT id,bev_free,bev_work,bev_max FROM stu_colonies WHERE user_id>1");
while($data=mysql_fetch_assoc($result))
{
	$res = $db->query("SELECT SUM(b.bev_use) FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.colonies_id=".$data[id]." AND a.aktiv=1",1);
	if ($res != $data[bev_work])
	{
		$db->query("UPDATE stu_colonies SET bev_work=".$res." WHERE id=".$data[id]);
		//echo $data[id]." - ".$data[bev_work]." - ".$res."<br>";
	}
}

// Lager
//echo "Lager<br>";
$result = $db->query("SELECT id,max_storage FROM stu_colonies WHERE user_id>1");
while($data=mysql_fetch_assoc($result))
{
	$res = $db->query("SELECT SUM(b.lager) FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.aktiv<2 AND a.colonies_id=".$data[id],1);
	if ($res != $data[max_storage])
	{
		$db->query("UPDATE stu_colonies SET max_storage=".$res." WHERE id=".$data[id]);
		//echo $data[id]." - ".$data[max_storage]." - ".$res."<br>";
	}
}
// Schilde
//echo "Schilde<br>";
$result = $db->query("SELECT id,max_schilde,schilde FROM stu_colonies WHERE user_id>1");
while($data=mysql_fetch_assoc($result))
{
	$res = $db->query("SELECT SUM(b.schilde) FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.aktiv < 2 AND a.colonies_id=".$data[id],1);
	if ($res != $data[max_schilde])
	{
		$db->query("UPDATE stu_colonies SET max_schilde=".$res." WHERE id=".$data[id]);
		//echo $data[id]." - ".$data[max_schilde]." - ".$res."<br>";
	}
}
?>
