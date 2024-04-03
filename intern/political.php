<?php
include_once("../inc/func.inc.php");
include_once("../inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;

$result =$db->query("SELECT a.cx,a.cy,b.systems_id,d.race FROM stu_map as a LEFT JOIN stu_systems as b ON b.cx=a.cx AND b.cy=a.cy LEFT JOIN stu_colonies as c ON c.systems_id=b.systems_id LEFT JOIN stu_user as d ON d.id=c.user_id ORDER BY a.cy,a.cx");

echo "<table><tr><td><table cellspacing=0 cellpadding=0><tr>";

// Schleife für Kartenfelder
$ls = 0;
$xt = 1;
while($data=mysql_fetch_assoc($result))
{
	//echo $data[cx]." - ".$data[cy]."<br>";

	if ($data[systems_id] != $ls)
	{
		$rc = count($races[$ls]);
		if ($ls)
		{
			//echo " -> ".$ls." - ".$rc."<br>";
			if ($rc == 0) echo "<td style=\"width: 5px; height: 5px;\" bgcolor=#444a4a title=\"".$ls." - ".$lx."|".$ly."\"></td>";
			elseif ($rc > 1) echo "<td style=\"width: 5px; height: 5px;\" bgcolor=#9b9b9b title=\"".$ls." - ".$lx."|".$ly."\"></td>";
			else
			{
				switch($rf[$ls])
				{
					case 1:
						$col = "Blue";
						break;
					case 2:
						$col = "Green";
						break;
					case 3:
						$col = "Darkred";
						break;
					case 4:
						$col = "Yellow";
						break;
					case 5:
						$col = "#ffa200";
						break;
					case 9:
						$col = "#000000";
						break;
				}
				//echo "--> ".$ls." - ".$rf[$ls]." - ".$col."<br>";
				echo "<td style=\"width: 5px; height: 5px;\" bgcolor=".$col." title=\"".$ls." - ".$lx."|".$ly."\"></td>";
			}
		}
		$ls = $data[systems_id];
	}
	if ($xt != $data[cy])
	{
		echo "</tr><tr>";
		$xt = $data[cy];
	}
	if (!$data[systems_id])
	{
		echo "<td style=\"width: 5px; height: 5px;\" bgcolor=#000000 title=\"".$data[cx]."|".$data[cy]."\"></td>";
		$lx = $data[cx];
		$ly = $data[cy];
		continue;
	}
	if ($data[race] != 9 && is_numeric($data['race']))
	{
		//echo $data[systems_id]." - ".$data[race]."<br>";
		$races[$data[systems_id]][$data[race]]++;
		$rf[$data[systems_id]] = $data[race];
	}
	$lx = $data[cx];
	$ly = $data[cy];
}
echo "</tr></table></td>
<td valign=top>
<b>Legende</b><br>
<table>
<tr>
<td style=\"width: 5px; height: 5px;\" bgcolor=Blue></td>
<td>Besetzt durch Föderation</td>
</tr>
<tr>
<td style=\"width: 5px; height: 5px;\" bgcolor=Green></td>
<td>Besetzt durch Romulaner</td>
</tr>
<tr>
<td style=\"width: 5px; height: 5px;\" bgcolor=Darkred></td>
<td>Besetzt durch Klingonen</td>
</tr>
<tr>
<td style=\"width: 5px; height: 5px;\" bgcolor=Yellow></td>
<td>Besetzt durch Cardassianer</td>
</tr>
<tr>
<td style=\"width: 5px; height: 5px;\" bgcolor=#ffa200></td>
<td>Besetzt durch Ferengi</td>
</tr>
<tr>
<td style=\"width: 5px; height: 5px;\" bgcolor=#9b9b9b></td>
<td>Umstritten</td>
</tr>
<tr>
<td style=\"width: 5px; height: 5px;\" bgcolor=#444a4a></td>
<td>Unbewohnt</td>
</tr>
</table>
</td>
</tr></table>";

?>