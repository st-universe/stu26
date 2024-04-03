<?php

function deactivate_ship()
{
	global $data,$db,$dropintosystem;
	if ($data['schilde_status'] > 1) $sd = $data['schilde_status'];
	else $sd = 0;
	//$db->query("UPDATE stu_ships SET crew=".$data['crew'].",warp='0',cloak='0',schilde_status=".$sd.",lss=NULL,nbs=NULL,still=0 WHERE id=".$data['id']." LIMIT 1");
	//if ($data[traktormode] == 1) $db->query("UPDATE stu_ships SET traktor=0,traktormode=NULL WHERE traktor=".$data['id']." OR id=".$data['id']." LIMIT 2");
	return 1;
}

// Zieh die müllenden Schweinehunde und ihre Bojen ins System
function dropintosystem()
{
	global $data,$db;
	if ($data['traktormode'] == 2) return 0;
	if ($data['fleets_id'] > 0) return 0;
	$sys = $db->query("SELECT systems_id,sr,sr,type,name FROM stu_systems WHERE cx=".$data['cx']." AND cy=".$data['cy']." LIMIT 1",4);
	if ($sys == 0) return 0;
	if (!$data['direction']) $data['direction'] = rand(1,4);
	if ($data['direction'] == 1) { $sx = $sys[sr]; $sy = rand(round($sys['sr']/2)-round($sys['sr']/5),round($sys['sr']/2)+round($sys['sr']/5)); }
	if ($data['direction'] == 2) { $sx = 1; $sy = rand(round($sys['sr']/2)-round($sys['sr']/5),round($sys['sr']/2)+round($sys['sr']/5)); }
	if ($data['direction'] == 3) { $sx = rand(round($sys['sr']/2)-round($sys['sr']/5),round($sys['sr']/2)+round($sys['sr']/5)); $sy = $sys['sr']; }
	if ($data['direction'] == 4) { $sx = rand(round($sys['sr']/2)-round($sys['sr']/5),round($sys['sr']/2)+round($sys['sr']/5)); $sy = 1; }
	$ftype = $db->query("SELECT type FROM stu_sys_map WHERE systems_id=".$sys['systems_id']." AND sx=".$sx." AND sy=".$sy,1);
	//$db->query("UPDATE stu_ships SET sx=".$sx.",sy=".$sy.",systems_id=".$sys['systems_id'].",warp='0',cfield=".$ftype." WHERE id=".$data['id']);
	//$db->query("INSERT INTO stu_pms (send_user,recip_user,text,date,type) VALUES ('1','".$data[user_id]."','Die ".addslashes($data[name])." kann die Position nicht halten und wird durch Gravitation in das nahe ".addslashes($sys[name])."-System gezogen.',NOW(),'3')");
	echo "<br>Die ".addslashes($data['name'])." kann die Position nicht halten und wird durch Gravitation in das nahe ".$sys['name']."-System gezogen.";
	echo "<br>Direction: ".$data['direction'];
	return 1;
}
	
	
include_once("../inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;

echo "hallo";
// Liste der Schiffe holen *fump* <- Ich tu ma noch Coords und so dazu, wegen der Bojen. BOJEN! AAARGH!
$result = $db->query("SELECT a.id,a.rumps_id,a.user_id,a.name,a.warp,a.warpcore,a.warpable,a.cloak,a.eps,a.max_eps,a.schilde_status,a.dock,a.crew,a.min_crew,a.lss,a.nbs,a.replikator,a.cx,a.cy,a.direction,a.fleets_id,a.systems_id,b.reaktor,b.m5,c.reaktor as dreaktor,c.replikator as creplikator FROM stu_ships as a LEFT JOIN stu_ships_buildplans as b USING(plans_id) LEFT JOIN stu_rumps as c ON a.rumps_id=c.rumps_id LEFT JOIN stu_user as d ON a.user_id=d.id WHERE a.user_id>100 AND ISNULL(d.vac_active)");
while($data=mysql_fetch_assoc($result))
{
	// Prüfen ob das Schiff zu deaktivieren ist
	if ($data[crew] == 0 && $data[rumps_id] != 10)
	{
		deactivate_ship();
		// Ich hasse Bojen. Ich HASSE sie. HASS! TOD ALLEN BOJEN! AAAARGH!
	    if (($data['systems_id'] == 0) && ($data['rumps_id'] != 8)) dropintosystem();
		continue;
	}
	
}



?>
