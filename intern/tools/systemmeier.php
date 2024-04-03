<?php
include_once("../../inc/func.inc.php");
include_once("../../inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;

$result = $db->query("SELECT * FROM stu_map WHERE type=47 ORDER BY RAND()");
while($data=mysql_fetch_assoc($result))
{
	echo $data[cx]."|".$data[cy]."<br>
	Selektiere freies System...";
	$sys = $db->query("SELECT * FROM stu_systems WHERE cx=0 AND cy=0 ORDER BY RAND() LIMIT 1",4);
	if ($sys == 0) echo " Fehlgeschlagen - Fahre fort<br><br>";
	else
	{
		echo " System gefunden -> ".$sys[name]."<br><br>";
		$db->query("UPDATE stu_map SET type=".$sys[type]." WHERE cx=".$data[cx]." AND cy=".$data[cy]);
		$db->query("UPDATE stu_systems SET cx=".$data[cx].",cy=".$data[cy]." WHERE systems_id=".$sys[systems_id]);
	}
	flush();
}
?>