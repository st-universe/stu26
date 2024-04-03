<?php
include_once("../inc/func.inc.php");
include_once("../inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;

$result = $db->query("SELECT a.user_id,a.ip,a.session,a.agent,a.start,a.end,COUNT(a.user_id) as uic,b.allys_id,b.user,b.pass FROM stu_user_iptable as a LEFT JOIN stu_user as b ON b.id=a.user_id WHERE UNIX_TIMESTAMP(a.start)>=".(time()-86400)." AND a.user_id>102 GROUP BY ip");
while($data=mysql_fetch_assoc($result))
{
	$arru[$data[user_id]] = array("pass" => $data[pass],"user" => $data[user],"agent" => $data[agent],"allys_id" => $data[allys_id]);
	if ($data[uic] == 1) continue;
	$res = $db->query("SELECT a.session,a.agent,b.id,b.allys_id,b.pass,b.user FROM stu_user_iptable as a LEFT JOIN stu_user as b ON b.id=a.user_id WHERE a.ip='".$data[ip]."' AND UNIX_TIMESTAMP(a.start)>=".(time()-86400)." AND a.user_id>102 GROUP BY a.user_id ORDER BY a.session,a.user_id");
	//if (mysql_num_rows($res) > 1) echo $data[ip]."<br>";
	while($dat=mysql_fetch_assoc($res))
	{
		if (!$arru[$dat[id]]) $arru[$dat[id]] = array("pass" => $dat[pass],"user" => $dat[user],"agent" => $dat[agent],"allys_id" => $dat[allys_id]);
		if ($la == $data[agent] && $ls == $data[session])
		{
			$mf = 50;
			if ($li < $dat[id]) $multi2[$li."-".$dat[id]] += $mf;
			if ($li > $dat[id]) $multi2[$dat[id]."-".$li] += $mf;
		}
		//echo "-> ".$dat[id]." - ".substr($dat[agent],0,50)." - ".substr($dat[session],0,16)." - ".substr($dat[pass],0,16)."<br>";
		$li = $dat[id];
		$la = $dat[agent];
		$ls = $dat[session];
		$lp = $dat[pass];
	}
	$li = "";
	$la = "";
	$ls = "";
	$lp = "";
}
echo "_______________________________________<br>";

foreach($multi2 as $key => $value)
{
	$sp = explode("-",$key);
	$pms = $db->query("SELECT COUNT(id) FROM stu_pms WHERE ((recip_user=".$sp[0]." AND send_user=".$sp[1].") OR (recip_user=".$sp[1]." AND send_user=".$sp[0].")) AND type='2'",1);
	$value += $pms*10;
	if ($arru[$sp[0]][pass] == $arru[$sp[1]][pass]) $value += 100;
	if ($arru[$sp[0]][agent] == $arru[$sp[1]][agent]) $value += 20;
	if ($arru[$sp[0]][allys_id] > 0 && $arru[$sp[0]][allys_id] == $arru[$sp[1]][allys_id]) $value += 40;
	//Prüfen, ob die beiden schonmal auffielen
	$res = $db->query("SELECT COUNT(ip) as ic,ip FROM stu_user_iptable WHERE (user_id=".$sp[0]." OR user_id=".$sp[1].") AND UNIX_TIMESTAMP(start) BETWEEN ".(time()-86400)." AND ".(time()+172800)." AND UNIX_TIMESTAMP(end)>0 GROUP BY ip HAVING ic>0");
	while($data=mysql_fetch_assoc($res)) $value += $data[ic]*5;
	$mda = $db->query("SELECT UNIX_TIMESTAMP(start) as st,UNIX_TIMESTAMP(end) as en FROM stu_user_iptable WHERE user_id=".$sp[0]." AND UNIX_TIMESTAMP(end)>0 AND UNIX_TIMESTAMP(start)>".(time()-86400)." ORDER BY start LIMIT 1",4);
	$mdb = $db->query("SELECT UNIX_TIMESTAMP(start) as st,UNIX_TIMESTAMP(end) as en FROM stu_user_iptable WHERE user_id=".$sp[1]." AND UNIX_TIMESTAMP(end)>0 AND UNIX_TIMESTAMP(start)>".(time()-86400)." ORDER BY start LIMIT 1",4);
	if ($mda[en] < $mdb[st] - 21600 || $mdb[en] < $mda[st] - 21600) $value -= 50;
	if ($mda[st] < $mdb[en]+600 || $mdb[st] < $mda[en]+600) $value += 100;
	if (($mda[st] > $mdb[st] && $mda[en] < $mdb[en]) || ($mdb[st] > $mda[st] && $mdb[en] < $mda[en])) $value -= 50;
	if ($value < 200) continue;
	echo "Accounts: ".$key."<br>Faktor: ".$value."<br>PM: ".$pms."<br>";
	echo "- ".$sp[0].": ".date("d.m.Y H:i",$mda[st])." - ".date("d.m.Y H:i",$mda[en])."<br>
	- ".$sp[1].": ".date("d.m.Y H:i",$mdb[st])." - ".date("d.m.Y H:i",$mdb[en])."<br><br>";
}
?>