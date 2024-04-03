<?php
include_once("/home/stuniverse/webroot/inc/func.inc.php");
include_once("/home/stuniverse/webroot/inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;

$color = array(
0 => "red",
1 => "green",
2 => "blue",
3 => "yellow",
4 => "orange",
5 => "purple",
6 => "brown",
7 => "aqua"
);

$runde = $db->query("SELECT MAX(round) FROM stu_wallstreet",1);
$wert = 0;
$result = $db->query("SELECT b.id,b.user FROM stu_npc_contactlist as a LEFT JOIN stu_user as b ON b.id=a.recipient WHERE a.rkn='1' AND a.user_id=14");
$rkn = mysql_num_rows($result);
while ($data=mysql_fetch_assoc($result))
{
	$wert += $db->query("SELECT SUM(b.count*c.ws_wert) FROM stu_colonies as a LEFT JOIN stu_colonies_storage as b ON b.colonies_id=a.id LEFT JOIN stu_goods as c ON c.goods_id=b.goods_id WHERE a.user_id=".$data['id'],1);
	$wert += $db->query("SELECT SUM(b.count*c.ws_wert) FROM stu_ships as a LEFT JOIN stu_ships_storage as b ON b.ships_id=a.id LEFT JOIN stu_goods as c ON c.goods_id=b.goods_id WHERE a.user_id=".$data['id'],1);
	$wert += $db->query("SELECT SUM(points) FROM stu_ships WHERE user_id=".$data['id'],1);

	$db->query("INSERT INTO stu_wallstreet (round,user_id,wert) VALUES ('".($runde+1)."','".$data['id']."','".round($wert/$rkn)."')");
	$wert = 0;
}

#exit;
// Graphen erstellen (User/Tick)
include($global_path."/inc/graph/jpgraph.php");
include($global_path."/inc/graph/jpgraph_bar.php");
include($global_path."/inc/graph/jpgraph_line.php");


$g = new Graph(700,600,"auto");
$g->SetScale("textlin");
$g->setcolor("white");
$g->img->SetMargin(60,20,20,200);


$result = $db->query("SELECT b.id,b.user FROM stu_npc_contactlist as a LEFT JOIN stu_user as b ON b.id=a.recipient WHERE a.rkn='1' AND a.user_id=14");
while($data=mysql_fetch_assoc($result))
{
	$res = $db->query("SELECT round,wert FROM stu_wallstreet WHERE user_id=".$data['id']." ORDER BY round ASC");
	while($dat = mysql_fetch_assoc($res))
	{
		$runden[] = $dat['round'];
		$werte[] = $dat['wert'];
	}
	$l = new LinePlot($werte);
	$col = array_rand($color);
	$l->SetColor($color[$col]);
	unset($color[$col]);
	$l->SetLegend(stripslashes(strip_tags($data['user'])));
	$g->Add($l);
	if (!$runds) $runds = $runden;
	unset($werte);
}

$g->legend->Pos(0,0.7);
$g->xaxis->SetColor("#8897cf");
$g->yaxis->SetColor("#8897cf");
$g->yaxis->scale->SetGrace(10,10);
$g->xaxis->SetTickLabels($runds);
$g->Stroke($global_path."/gfx/graph/wallstreet.jpg");


?>