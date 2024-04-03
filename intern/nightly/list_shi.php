<?php
include_once("/var/www/virtual/stuniverse.de/htdocs/inc/config.inc.php");
include_once($global_path."/inc/func.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;

$op = "<table class=\"tcal\"><th></th><th>Anzahl</th><th>Schiffsklasse</th>";
$result = $db->query("SELECT COUNT(b.id) as sc,a.rumps_id,a.name FROM stu_rumps as a LEFT JOIN stu_ships as b USING(rumps_id) WHERE a.npc='0' AND b.user_id>100 GROUP BY a.rumps_id ORDER BY a.sort,a.rumps_id");
while($data=mysql_fetch_assoc($result))
{
	$j++;
	$bc = "#000000";
	if ($j == 2)
	{
		$bc = "#171616";
		$j = 0;
	}
	$op .= "<tr>
	<td style=\"background-color: #000000;width:200px;height:50px;text-align:center;\"><img src=gfx/ships/".$data['rumps_id'].".gif></td>
	<td style=\"text-align:center; font-weight:bold; width:30px; background-color: ".$bc.";\">".(!$data['sc'] ? 0 : $data['sc'])."</td>
	<td style=\"text-align:left; padding-left:5px; background-color: ".$bc.";\">".stripslashes($data['name'])."</td>
	
	</tr>";
	unset($trc);
}
$op .= "</table>";
unlink($global_path."/inc/lists/shi_bui.php");
$fp = fopen($global_path."/inc/lists/shi_bui.php","a+");
fwrite($fp,$op);
fclose($fp);
?>
