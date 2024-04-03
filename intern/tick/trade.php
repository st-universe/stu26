<?php
include_once("/srv/www/stu_sys/webroot/inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;

$userid = 0;

// $result = $db->query("SELECT a.count,CEILING((a.count/100)*5) as gc,a.goods_id,a.user_id,b.name FROM stu_trade_goods as a LEFT JOIN stu_goods as b ON b.goods_id=a.goods_id LEFT JOIN stu_user as c ON c.id=a.user_id WHERE a.offer_id=0 AND UNIX_TIMESTAMP(a.date)<".(time()-86400)." AND a.user_id>100 AND (c.vac_active='0' OR ISNULL(c.vac_active)) ORDER BY a.user_id");
// $db->query("UPDATE stu_trade_goods SET date=NOW() WHERE offer_id=0 AND UNIX_TIMESTAMP(date)<".(time()-86400));
// while($data=mysql_fetch_assoc($result))
// {
	// if ($userid == 0 || $userid != $data[user_id])
	// {
		// if ($userid != 0) $db->query("INSERT INTO stu_pms (send_user,recip_user,text,date,type) VALUES ('1','".$userid."','<b>5% Lagergebühr auf alle Waren in der Warenbörse</b>".addslashes($msg)."',NOW(),'2')");
		// $userid = $data[user_id];
		// unset($msg);
	// }

	// if ($data[gc] >= $data['count'])
	// {
		// $db->query("DELETE FROM stu_trade_goods WHERE goods_id=".$data[goods_id]." AND offer_id=0 AND user_id=".$data[user_id]);
		// $msg .= "<br>Die restlichen ".$data['count']." ".$data[name]." wurden eingezogen";
	// }
	// else
	// {
		// $db->query("UPDATE stu_trade_goods SET count=count-".$data[gc]." WHERE offer_id=0 AND goods_id=".$data[goods_id]." AND user_id=".$data[user_id]);
		// $msg .= "<br>Es wurden ".$data[gc]." ".$data[name]." abgezogen";
	// }
// }
// if ($msg)
// {
	// $db->query("INSERT INTO stu_pms (send_user,recip_user,text,date,type) VALUES ('1','".$userid."','<b>5% Lagergebühr auf alle Waren in der Warenbörse</b>".addslashes($msg)."',NOW(),'2')");
// }

// 5% Abzug
unset($msg);
$userid = 0;

$result = $db->query("SELECT offer_id,user_id,count,ggoods_id,gcount FROM stu_trade_offers WHERE UNIX_TIMESTAMP(date)<".(time()-604800)." ORDER BY user_id");
while($data=mysql_fetch_assoc($result))
{
	if ($userid == 0 || $userid != $data['user_id'])
	{
		if ($userid != 0) $db->query("INSERT INTO stu_pms (send_user,recip_user,text,date,type) VALUES ('1','".$userid."','<b>Folgende Angebote wurden aufgrund der 7 Tages Frist gelöscht</b>".$msg."',NOW(),'2')");
		$userid = $data['user_id'];
		unset($msg);
	}
	$msg .= "<br>Angebot ".$data['offer_id'];
	$res2 = $db->query("UPDATE stu_trade_goods SET count=count+".($data['gcount']*$data['count'])." WHERE goods_id=".$data['ggoods_id']." AND ISNULL(mode) AND user_id=".$data['user_id'],6);
	if ($res2 == 0) $db->query("INSERT INTO stu_trade_goods (user_id,goods_id,count,date) VALUES ('".$data['user_id']."','".$data['ggoods_id']."','".($data['gcount']*$data['count'])."',NOW())");
	$db->query("DELETE FROM stu_trade_offers WHERE offer_id=".$data['offer_id']);
	$db->query("DELETE FROM stu_trade_marks WHERe offer_id=".$data['offer_id']);
}

if ($msg)
{
	$db->query("INSERT INTO stu_pms (send_user,recip_user,text,date,type) VALUES ('1','".$userid."','<b>Folgende Angebote wurden aufgrund der 7 Tages Frist gelöscht</b>".$msg."',NOW(),'2')");
}
?>
