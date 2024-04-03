<?php
include_once("../inc/func.inc.php");
include_once("../inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;

function usercolor($i) {

	if ($i == 1) return "#4444cc";
	if ($i == 2) return "#00aa00";
	if ($i == 3) return "#999900";
	if ($i == 4) return "#660066";
	if ($i == 5) return "#006666";
	if ($i == 0) return "#cccccc";
}




function ucol($i,$us) {
	$k = 0;
	foreach($us as $u) {
		$k++;
		if ($u == $i) return $k;
	}
	return 0;
}


function blacol($i,$us) {
	$num = ucol($i,$us);

	$r = "<font color=".usercolor($num).">";
	$r .= "(".$i.") ";
	$r .= "</font>";
	
	return $r;
}



	$users = array(11,12,13,14);


	
	
	
	
	
	
	
	echo "PM-Verkehr für:";
	
	echo "<br>";
	
	$searchrec = "(";
	$searchsen = "(";
	
	// $searchrec .= "recip_user = ";
	// $searchrec .= "send_user = ";
	
	for ($i = 0; $i < count($users); $i++) {
		echo "<br>";
		
		echo "<font color=".usercolor($i+1).">";
		
		echo "(".$users[$i].") ";
		$name = $db->query("SELECT search_user FROM stu_user WHERE id =".$users[$i]." LIMIT 1",1);
		echo $name;
		echo "</font>";
		
		if ($i != 0) $searchrec .= " OR ";
		if ($i != 0) $searchsen .= " OR ";
		$searchrec .= "recip_user = ".$users[$i];
		$searchsen .= "send_user = ".$users[$i];
		
		$backsearch[$users[$i]] = $i;
	}
	
	$searchrec .= ")";
	$searchsen .= ")";
	

	$search = "(".$searchrec." OR ".$searchsen.") AND type='1'";
	

	echo "<br><br>";
	
	$result = $db->query("SELECT *,UNIX_TIMESTAMP(date) as dt FROM stu_pms WHERE ".$search." ORDER BY date DESC");
	while($data=mysql_fetch_assoc($result)) {

		echo "<br>".$data[date]." : ".blacol($data[send_user],$users)."->".blacol($data[recip_user],$users)."";
		echo "<br>".$data[text];
		echo "<br>";
		
		if ($data[dt] < time()-3*86400) break;
	}
	














?>