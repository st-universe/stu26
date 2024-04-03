<?php
include_once("/srv/www/stu_sys/webroot/inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;

$result = $db->query("SELECT SUM(b.count) as gc,b.goods_id,c.name,c.sort as sorti FROM stu_ships as a LEFT JOIN stu_ships_storage as b ON b.ships_id=a.id LEFT JOIN stu_goods as c ON c.goods_id=b.goods_id WHERE c.view='1' AND a.user_id>100 GROUP BY b.goods_id ORDER BY c.sort");
while($data=mysql_fetch_assoc($result))
{
	$goods[$data['goods_id']]['gi'] = $data['goods_id'];
	$goods[$data['goods_id']]['id'] = $data['sorti'];
	$goods[$data['goods_id']]['c'] += $data['gc'];
	$goods[$data['goods_id']]['n'] = $data['name'];
}
$result = $db->query("SELECT SUM(b.count) as gc,b.goods_id,c.name,c.sort as sorti FROM stu_colonies as a LEFT JOIN stu_colonies_storage as b ON b.colonies_id=a.id LEFT JOIN stu_goods as c ON c.goods_id=b.goods_id WHERE c.view='1' AND a.user_id>100 GROUP BY b.goods_id ORDER BY c.sort");
while($data=mysql_fetch_assoc($result))
{
	$goods[$data['goods_id']]['gi'] = $data['goods_id'];
	$goods[$data['goods_id']]['id'] = $data['sorti'];
	$goods[$data['goods_id']]['c'] += $data['gc'];
	$goods[$data['goods_id']]['n'] = $data['name'];
}
$result = $db->query("SELECT SUM(a.count) as gc,a.goods_id,b.name,b.sort as sorti FROM stu_trade_goods as a LEFT JOIN stu_goods as b USING(goods_id) WHERE b.view='1' AND (ISNULL(mode) OR mode='') AND a.user_id>100 GROUP BY goods_id ORDER BY b.sort");
while($data=mysql_fetch_assoc($result))
{
	$goods[$data['goods_id']]['gi'] = $data['goods_id'];
	$goods[$data['goods_id']]['id'] = $data['sorti'];
	$goods[$data['goods_id']]['c'] += $data['gc'];
	$goods[$data['goods_id']]['n'] = $data['name'];
}
$result = $db->query("SELECT SUM(a.count*a.gcount) as gc,a.ggoods_id as goods_id,b.name,b.sort as sorti FROM stu_trade_offers as a LEFT JOIN stu_goods as b ON b.goods_id=a.ggoods_id WHERE b.view='1' AND a.user_id>100 GROUP BY a.ggoods_id ORDER BY b.sort");
while($data=mysql_fetch_assoc($result))
{
	$goods[$data['goods_id']]['gi'] = $data['goods_id'];
	$goods[$data['goods_id']]['id'] = $data['sorti'];
	$goods[$data['goods_id']]['c'] += $data['gc'];
	$goods[$data['goods_id']]['n'] = $data['name'];
}

// Waren sortieren
function cmp ($a, $b) { return strnatcmp($a['id'],$b['id']); }
@usort($goods, "cmp");

// History öffnen
$result = @file($global_path."/intern/data/goodhist.dat");
foreach($result as $key => $dat)
{
	$arr = explode("|",$dat);
	$data[$arr[0]] = array($arr[2],$arr[3],$arr[4],trim($arr[5]));
}

// Warenliste verarbeiten
foreach($goods as $key => $value)
{
	//echo $value['id']." - ".$value['n']." - ".$value['c']."<br>";
	array_push($data[$value['id']],$value['c']);
}

// Historyfile löschen
@unlink($global_path."/intern/data/goodhist.dat");

// Historydatei schreiben
foreach($data as $key => $value)
{
	$fw .= $key."|".$value[0]."|".$value[1]."|".$value[2]."|".$value[3]."|".$value[4]."\n";
}
$fp = @fopen($global_path."/intern/data/goodhist.dat","a+");
fwrite($fp,$fw);
fclose($fp);

/*
@unlink($global_path."/intern/data/goodhist.dat");
$result = $db->query("SELECT goods_id FROM stu_goods WHERE view='1' ORDER BY sort");
while($data=mysql_fetch_assoc($result))
{
	$fw .= $data['goods_id']."|0|0|0|0|0\n";
}
$fp = @fopen($global_path."/intern/data/goodhist.dat","a+");
fwrite($fp,$fw);
fclose($fp);
*/
?>
