<?php
include_once("/srv/www/stu_sys/webroot/inc/config.inc.php");
include_once($global_path."/inc/func.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;

$result = $db->query("SELECT ggoods_id,SUM(wcount*count) as ws,SUM(gcount*count) as gs,wgoods_id FROM stu_trade_offers WHERE ggoods_id=8 GROUP BY wgoods_id");
while($data = mysql_fetch_assoc($result))
{
	$test[$data['wgoods_id']]['go'] += $data['ws'];
	$test[$data['wgoods_id']]['dil'] += $data['gs'];
}
$result = $db->query("SELECT ggoods_id,SUM(wcount*count) as ws,SUM(gcount*count) as gs,wgoods_id FROM stu_trade_offers WHERE wgoods_id=8 GROUP BY ggoods_id");
while($data = mysql_fetch_assoc($result))
{
	$test[$data['ggoods_id']]['w']['go'] += $data['gs'];
	$test[$data['ggoods_id']]['w']['dil'] += $data['ws'];
}

foreach ($test as $key => $value)
{
	$db->query("INSERT INTO stu_trade_prices (goods_id,price,price2,date) VALUES ('".$key."','".(!$value['go'] ? 1 : round($value['go']/$value['dil'],2))."','".(!$value['w'] ? 1 : round($value['w']['go']/$value['w']['dil'],2))."',NOW())");
}

reset($test);

// Graphen erstellen (User/Tick)
include($global_path."/inc/graph/jpgraph.php");
include($global_path."/inc/graph/jpgraph_bar.php");
include($global_path."/inc/graph/jpgraph_line.php");

foreach ($test as $key => $value)
{
	unset($value['w']);
	if (!$value['go']) continue;
	$g = new Graph(700,200,"auto");
	$g->SetScale("textlin");
	$g->setcolor("white");
	$g->img->SetMargin(60,20,20,20);

	$result = $db->query("SELECT price,UNIX_TIMESTAMP(date) as date_tsp FROM stu_trade_prices WHERE goods_id=".$key." AND HOUR(date)=".date("H")." AND MINUTE(date)=30 ORDER BY date DESC LIMIT 14");
	if (mysql_num_rows($result) < 2) continue;
	while($data=mysql_fetch_assoc($result))
	{
		$daten[] = $data['price'];
		$tick[] = date("d.m",$data['date_tsp']);
	}
	$l = new LinePlot(array_reverse($daten));
	$l->SetColor("#c2b942");
	$g->Add($l);
	$g->xaxis->SetColor("#8897cf");
	$g->yaxis->SetColor("#8897cf");
	$g->yaxis->scale->SetGrace(10,10);
	$l->SetWeight(3);
	$g->xaxis->SetTickLabels(array_reverse($tick));
	@unlink($global_path."/gfx/prices/".$key.".jpg");
	$g->Stroke($global_path."/gfx/prices/".$key.".jpg");
	unset($daten);
	unset($tick);
	
}
?>
