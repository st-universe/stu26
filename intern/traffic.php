<?php
include_once("../inc/func.inc.php");
include_once("../inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;

$result =$db->query("SELECT a.cx,a.cy,COUNT(b.ships_id) as tr FROM stu_map as a LEFT JOIN stu_sectorflights as b ON b.cx=a.cx AND b.cy=a.cy GROUP BY a.cx,a.cy ORDER BY a.cy,a.cx");

echo "<table cellspacing=0 cellpadding=0><tr>";

// Schleife für Kartenfelder
while($data=mysql_fetch_assoc($result))
{
	if ($xt != $data[cy])
	{
		echo "</tr><tr>";
		$xt = $data[cy];
	}
	if ($data[tr] == 0) $col = "#000000";
	if ($data[tr] > 0 && $data[tr] < 10) $col = "#FFFFCA";
	if ($data[tr] >= 10 && $data[tr] < 20) $col = "#FFFDAA";
	if ($data[tr] >= 20 && $data[tr] < 30) $col = "#FFFB40";
	if ($data[tr] >= 30 && $data[tr] < 40) $col = "#FFC640";
	if ($data[tr] >= 40 && $data[tr] < 50) $col = "#FF8C66";
	if ($data[tr] >= 50 && $data[tr] < 60) $col = "#E13800";
	if ($data[tr] >= 60) $col = "#F0A800";
	echo "<td style=\"width: 5px; height: 5px;\" bgcolor=".$col."></td>";
}
echo "</tr></table>";

?>