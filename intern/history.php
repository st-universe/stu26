<?php
include_once("../inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;

$time = date("m",time()-1003600);
$result = $db->query("SELECT message,UNIX_TIMESTAMP(date) as date_tsp FROM stu_history WHERE MONTH(date)=".$time." ORDER BY date");
while($data=mysql_fetch_assoc($result))
{
	echo "<tr><td>".stripslashes($data['message'])."</td><td class=td2>".date("d.m.Y H:i",$data['date_tsp'])."</td></tr>";
}
?>