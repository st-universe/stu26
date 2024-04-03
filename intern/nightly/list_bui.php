<?php
include_once("/var/www/virtual/stuniverse.de/htdocs/inc/config.inc.php");
include_once($global_path."/inc/func.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;

$op = "<table class=\"tcal\"><th style=\"width: 30px;\"></th><th>Gebäude</th><th>Gebaut</th>";
$result = $db->query("SELECT COUNT(b.field_id) as bc,a.buildings_id,a.name,a.upgrade_from FROM stu_buildings as a LEFT JOIN stu_colonies_fielddata as b USING(buildings_id) WHERE a.view='1' GROUP BY a.buildings_id ORDER BY a.buildings_id");
while($data=mysql_fetch_assoc($result))
{
	$j++;
	if ($j == 2)
	{
		$trc = " style=\"background-color: #171616\"";
		$j = 0;
	}
	// if ($data['upgrade_from'] == 1 || $data['upgrade_from'] == 53) $field = 1;
	// else $field = $db->query("SELECT type FROM stu_field_build WHERE buildings_id=".($data['buildings_id'])." AND type<200 ORDER BY TYPE ASC LIMIT 1",1);
	// else $field = $db->query("SELECT type FROM stu_field_build WHERE buildings_id=".($data['buildings_id'])." AND type<200 ORDER BY TYPE ASC LIMIT 1",1);
	$op .= "<tr>
	<td".$trc."><img src=gfx/buildings/".$data['buildings_id']."/0.png></td>
	<td".$trc.">".stripslashes($data['name'])."</td>
	<td".$trc.">".(!$data['bc'] ? 0 : $data['bc'])."</td>
	</tr>";
	unset($trc);
}
$op .= "</table>";
unlink($global_path."/inc/lists/gbl_bui.php");
$fp = fopen($global_path."/inc/lists/gbl_bui.php","a+");
fwrite($fp,$op);
fclose($fp);
?>
