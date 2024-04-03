<?php
include_once("/srv/www/stu_sys/webroot/inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;

$result = $db->query("SELECT a.goods_id,a.sort,b.count FROM stu_goods as a LEFT JOIN stu_trade_stats as b USING(goods_id) WHERE a.view='1'");
while($data=mysql_fetch_assoc($result))
{
	$goods[$data['goods_id']]['gi'] = $data['goods_id'];
	$goods[$data['goods_id']]['id'] = $data['sort'];
	$goods[$data['goods_id']]['c'] += $data['count'];
}

// Waren sortieren
function cmp ($a, $b) { return strnatcmp($a['id'],$b['id']); }
@usort($goods, "cmp");

// History öffnen
$result = @file($global_path."/intern/data/tradehist.dat");
foreach($result as $key => $dat)
{
	//echo $dat."<br>";
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
@unlink($global_path."/intern/data/tradehist.dat");

// Historydatei schreiben
foreach($data as $key => $value)
{
	$fw .= $key."|".$value[0]."|".$value[1]."|".$value[2]."|".$value[3]."|".$value[4]."\n";
}
$fp = @fopen($global_path."/intern/data/tradehist.dat","a+");
fwrite($fp,$fw);
fclose($fp);

//History aus der DB löschen
$db->query("DELETE FROM stu_trade_stats");
?>
