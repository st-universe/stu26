<?php
include_once("/srv/www/stu_sys/webroot/inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;

// Graphen erstellen (User/Tick)
include($global_path."/inc/graph/jpgraph.php");
include($global_path."/inc/graph/jpgraph_bar.php");
include($global_path."/inc/graph/jpgraph_line.php");


$g = new Graph(350,200,"auto");
$g->SetScale("textlin");
$g->setcolor("white");

$result = $db->query("SELECT runde,spieler FROM stu_game_rounds ORDER BY runde DESC LIMIT 10");
while($data=mysql_fetch_assoc($result))
{
	$daten[] = $data[spieler];
	$tick[] = $data[runde];
}
$l = new LinePlot(array_reverse($daten));
$l->SetFillColor("#c2b942");
$l->SetLegend("Spieler nach Ticks");
$g->Add($l);
$g->img->SetMargin(50,20,40,30);
$g->legend->Pos( 0.05,0.05,"right" ,"top");
$g->xaxis->SetColor("#8897cf");
$g->yaxis->SetColor("#8897cf");
$g->xaxis->SetTickLabels(array_reverse($tick));
unlink($global_path."/gfx/graph/usertick.jpg");
$g->Stroke($global_path."/gfx/graph/usertick.jpg");

unset($daten);
unset($tick);

// Graphen erstellen (Schiffe/Tick)
$g = new Graph(350,200,"auto");
$g->SetScale("textlin");
$g->setcolor("white");

$result = $db->query("SELECT runde,schiffe FROM stu_game_rounds ORDER BY runde DESC LIMIT 10");
while($data=mysql_fetch_assoc($result))
{
	$daten[] = $data[schiffe];
	$tick[] = $data[runde];
}
$l = new LinePlot(array_reverse($daten));
$l->SetFillColor("#c2b942");
$l->SetLegend("Schiffe nach Ticks");
$g->Add($l);
$g->img->SetMargin(50,20,40,30);
$g->legend->Pos( 0.05,0.05,"right" ,"top");
$g->xaxis->SetColor("#8897cf");
$g->yaxis->SetColor("#8897cf");
$g->xaxis->SetTickLabels(array_reverse($tick));
unlink($global_path."/gfx/graph/shiptick.jpg");
$g->Stroke($global_path."/gfx/graph/shiptick.jpg");

// Spiel-Status auf NORMAL setzen
$db->query("UPDATE stu_game_vars SET value='1' WHERE var='state'");

unset($daten);
unset($tick);

// Graphen erstellen (Wirtschaft/Tick)
$g = new Graph(350,200,"auto");
$g->SetScale("textlin");
$g->setcolor("white");

$result = $db->query("SELECT runde,wirtschaft FROM stu_game_rounds ORDER BY runde DESC LIMIT 10");
while($data=mysql_fetch_assoc($result))
{
	$daten[] = round($data[wirtschaft]);
	$tick[] = $data[runde];
}
$l = new LinePlot(array_reverse($daten));
$l->SetFillColor("#c2b942");
$l->SetLegend("Wirtschaft nach Ticks");
$g->Add($l);
$g->img->SetMargin(50,20,40,30);
$g->legend->Pos( 0.05,0.05,"right" ,"top");
$g->xaxis->SetColor("#8897cf");
$g->yaxis->SetColor("#8897cf");
$g->xaxis->SetTickLabels(array_reverse($tick));
unlink($global_path."/gfx/graph/wirttick.jpg");
$g->Stroke($global_path."/gfx/graph/wirttick.jpg");
?>
