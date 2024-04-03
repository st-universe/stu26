<?php
include_once("../inc/func.inc.php");
include_once("../inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;

$result =$db->query("SELECT a.cx , a.cy , b.systems_id , d.race , sum(c.points ) as wp, c.user_id, d.is_rkn  FROM stu_map as a LEFT JOIN stu_systems as b ON b.cx =a.cx AND b.cy =a.cy LEFT  JOIN stu_ships as c ON c.systems_id =b.systems_id LEFT JOIN stu_user as d ON d.id =c.user_id WHERE c.cloak='0' GROUP by c.user_id, c.systems_id   HAVING d.is_rkn != '0' AND d.race <= 4 ORDER BY a.cy,a.cx");

echo "<table><tr><td><table cellspacing=0 cellpadding=0><tr>";

while($data=mysql_fetch_assoc($result))
{
	if (($data[systems_id] != 1443) && ($data[systems_id] != 1503) && ($data[systems_id] != 1271) && ($data[systems_id] != 1293))
	{
		$val[$data[cx]][$data[cy]][$data[race]] += $data[wp];
		$val[$data[cx]][$data[cy]][sys] = $data[systems_id];
		// echo $data[cx]." - ".$data[cy]." - ".$val[$data[cx]][$data[cy]][$data[race]]."  ".$data[race]."<br>";
	}
}

$wprange = 2.0;

$pc = 0;
for($i=1;$i<=160;$i++)
{
	for($j=1;$j<=160;$j++)
	{
		if ($val[$i][$j][sys] == 0) $field[$i][$j] = 0;
		else
		{
			if (($val[$i][$j][1] > 20) || ($val[$i][$j][2] > 20) || ($val[$i][$j][3] > 20) || ($val[$i][$j][4] > 20))
			{
			$field[$i][$j] = 9;
			if (($val[$i][$j][1] > $val[$i][$j][2]*$wprange) && ($val[$i][$j][1] > $val[$i][$j][3]*$wprange) && ($val[$i][$j][1] >  $val[$i][$j][4]*$wprange) && ($val[$i][$j][1] > $val[$i][$j][5]*$wprange)) $field[$i][$j] = 1;

			if (($val[$i][$j][2] > $val[$i][$j][1]*$wprange) && ($val[$i][$j][2] > $val[$i][$j][3]*$wprange) && ($val[$i][$j][2] > $val[$i][$j][4]*$wprange) && ($val[$i][$j][2] > $val[$i][$j][5]*$wprange)) $field[$i][$j] = 2;

			if (($val[$i][$j][3] > $val[$i][$j][1]*$wprange) && ($val[$i][$j][3] > $val[$i][$j][2]*$wprange) && ($val[$i][$j][3] > $val[$i][$j][4]*$wprange) && ($val[$i][$j][3] > $val[$i][$j][5]*$wprange)) $field[$i][$j] = 3;

			if (($val[$i][$j][4] > $val[$i][$j][1]*$wprange) && ($val[$i][$j][4] > $val[$i][$j][2]*$wprange) && ($val[$i][$j][4] > $val[$i][$j][3]*$wprange) && ($val[$i][$j][4] > $val[$i][$j][5]*$wprange)) $field[$i][$j] = 4;


			$pc++;
			$points[$pc][x] = $i;			
			$points[$pc][y] = $j;
			$points[$pc][race] = $field[$i][$j];
			//echo "<br>".$pc.": ".$points[$pc][x]."|".$points[$pc][y]."  R: ".$points[$pc][race];
			}
			else $field[$i][$j] = 0;
		}
	}
}

$pc++;
$points[$pc][x] = 21;			
$points[$pc][y] = 95;
$points[$pc][race] = 1;
$pc++;
$points[$pc][x] = 25;			
$points[$pc][y] = 130;
$points[$pc][race] = 1;
$pc++;
$points[$pc][x] = 52;			
$points[$pc][y] = 147;
$points[$pc][race] = 1;
$pc++;
$points[$pc][x] = 131;			
$points[$pc][y] = 35;
$points[$pc][race] = 1;
$pc++;
$points[$pc][x] = 41;
$points[$pc][y] = 80;
$points[$pc][race] = 1;

$pc++;
$points[$pc][x] = 100;			
$points[$pc][y] = 105;
$points[$pc][race] = 2;
$pc++;
$points[$pc][x] = 87;
$points[$pc][y] = 94;
$points[$pc][race] = 2;
$pc++;
$points[$pc][x] = 68;
$points[$pc][y] = 111;
$points[$pc][race] = 2;
$pc++;
$points[$pc][x] = 125;
$points[$pc][y] = 135;
$points[$pc][race] = 2;


$pc++;
$points[$pc][x] = 152;
$points[$pc][y] = 80;
$points[$pc][race] = 3;
$pc++;
$points[$pc][x] = 129;
$points[$pc][y] = 66;
$points[$pc][race] = 3;
$pc++;
$points[$pc][x] = 99;
$points[$pc][y] = 27;
$points[$pc][race] = 3;
$pc++;
$points[$pc][x] = 85;
$points[$pc][y] = 39;
$points[$pc][race] = 3;

$pc++;
$points[$pc][x] = 39;
$points[$pc][y] = 26;
$points[$pc][race] = 4;
$pc++;
$points[$pc][x] = 20;
$points[$pc][y] = 18;
$points[$pc][race] = 4;

for($i=1;$i<=160;$i++)
{
	for($j=1;$j<=160;$j++)
	{
		if ($field[$i][$j] == 0)
		{
			$race = 0;
			$dtemp = 16;
			for($k=1;$k<=$pc;$k++)
			{
				$dist = sqrt( ($i-$points[$k][x])*($i-$points[$k][x]) + ($j-$points[$k][y])*($j-$points[$k][y]));
				if ($dist < $dtemp) 
				{
					$race = $points[$k][race];
					$dtemp = $dist;
				}
			}
			if ($race != 0) $field[$i][$j] = $race;
		}
	}
}


/*
$field[21][95] = 11;
$field[25][130] = 11;
$field[52][147] = 11;
$field[131][35] = 11;
$field[41][80] = 11;

$field[100][105] = 12;
$field[87][94] = 12;
$field[68][111] = 12;
$field[125][135] = 21;

$field[152][80] = 13;
$field[129][66] = 13;
$field[99][27] = 13;
$field[85][39] = 13;

$field[39][26] = 14;
$field[20][18] = 14;

*/

$field[70][70] = 7;
$field[76][69] = 7;
$field[70][72] = 7;
$field[66][66] = 7;

$field[90][6] = 8;
$field[67][13] = 8;

$field[87][2] = 8;
$field[88][2] = 8;
$field[89][2] = 8;
$field[90][2] = 8;

$field[86][3] = 8;
$field[87][3] = 8;
$field[88][3] = 8;
$field[89][3] = 8;
$field[90][3] = 8;
$field[91][3] = 8;
$field[92][3] = 8;

$field[86][4] = 8;
$field[87][4] = 8;
$field[88][4] = 8;
$field[89][4] = 8;
$field[90][4] = 8;
$field[91][4] = 8;
$field[92][4] = 8;
$field[93][4] = 8;

$field[87][5] = 8;
$field[88][5] = 8;
$field[89][5] = 8;
$field[90][5] = 8;
$field[91][5] = 8;
$field[92][5] = 8;
$field[93][5] = 8;

$field[88][6] = 8;
$field[89][6] = 8;
$field[94][6] = 8;
$field[91][6] = 8;
$field[92][6] = 8;
$field[93][6] = 8;

$field[88][7] = 8;
$field[89][7] = 8;
$field[90][7] = 8;
$field[91][7] = 8;
$field[92][7] = 8;
$field[93][7] = 8;

$field[89][8] = 8;
$field[90][8] = 8;
$field[91][8] = 8;


$field[65][64] = 7;
$field[66][64] = 7;
$field[67][64] = 7;

$field[64][65] = 7;
$field[65][65] = 7;
$field[66][65] = 7;
$field[67][65] = 7;
$field[68][65] = 7;

$field[64][66] = 7;
$field[65][66] = 7;
$field[67][66] = 7;
$field[68][66] = 7;
$field[69][66] = 7;

$field[64][67] = 7;
$field[65][67] = 7;
$field[66][67] = 7;
$field[67][67] = 7;
$field[68][67] = 7;
$field[69][67] = 7;
$field[70][67] = 7;
$field[71][67] = 7;
$field[72][67] = 7;
$field[73][67] = 7;
$field[74][67] = 7;
$field[75][67] = 7;
$field[76][67] = 7;

$field[77][68] = 7;
$field[65][68] = 7;
$field[66][68] = 7;
$field[67][68] = 7;
$field[68][68] = 7;
$field[69][68] = 7;
$field[70][68] = 7;
$field[71][68] = 7;
$field[72][68] = 7;
$field[73][68] = 7;
$field[74][68] = 7;
$field[75][68] = 7;
$field[76][68] = 7;

$field[77][69] = 7;
$field[78][69] = 7;
$field[66][69] = 7;
$field[67][69] = 7;
$field[68][69] = 7;
$field[69][69] = 7;
$field[70][69] = 7;
$field[71][69] = 7;
$field[72][69] = 7;
$field[73][69] = 7;
$field[74][69] = 7;
$field[75][69] = 7;

$field[77][70] = 7;
$field[78][70] = 7;
$field[67][70] = 7;
$field[68][70] = 7;
$field[69][70] = 7;
$field[71][70] = 7;
$field[72][70] = 7;
$field[73][70] = 7;
$field[74][70] = 7;
$field[75][70] = 7;
$field[76][70] = 7;

$field[77][71] = 7;
$field[67][71] = 7;
$field[68][71] = 7;
$field[69][71] = 7;
$field[70][71] = 7;
$field[71][71] = 7;
$field[72][71] = 7;
$field[73][71] = 7;
$field[74][71] = 7;
$field[75][71] = 7;
$field[76][71] = 7;

$field[68][72] = 7;
$field[69][72] = 7;
$field[71][72] = 7;
$field[72][72] = 7;
$field[73][72] = 7;
$field[74][72] = 7;
$field[75][72] = 7;
$field[76][72] = 7;

$field[68][73] = 7;
$field[69][73] = 7;
$field[70][73] = 7;
$field[71][73] = 7;
$field[72][73] = 7;

$field[69][74] = 7;
$field[70][74] = 7;
$field[71][74] = 7;


for($j=1;$j<=160;$j++)
{
	for($i=1;$i<=160;$i++)
	{
		if ($field[$i][$j] == $field[$i-1][$j] && $field[$i][$j] == $field[$i+1][$j] && $field[$i][$j] == $field[$i][$j-1] && $field[$i][$j] == $field[$i][$j+1])
		{
			echo "<td style=\"width: 3px; height: 3px;\" bgcolor=#000000 title=\"".$i."|".$j."\"></td>";
			if ($i == 160) 	echo "</tr><tr>";
			continue;
		}
		switch($field[$i][$j])
		{
		case 0:
			$col = "#000000";
			$racefield[0]++;
			break;
		case 1:
			$col = "#0000AA";
			$racefield[1]++;
			break;
		case 2:
			$col = "#00AA00";
			$racefield[2]++;
			break;
		case 3:
			$col = "#AA0000";
			$racefield[3]++;
			break;
		case 4:
			$col = "#AAAA00";
			$racefield[4]++;
			break;
		case 5:
			$col = "#ffa200";
			$racefield[5]++;
			break;
		case 9:
			$col = "#AAAAAA";
			$racefield[9]++;
			break;
		case 7:
			$col = "#6666AA";
			$racefield[7]++;
			break;
		case 8:
			$col = "#DD00DD";
			$racefield[8]++;
			break;
		}
		echo "<td style=\"width: 3px; height: 3px;\" bgcolor=".$col." title=\"".$i."|".$j."\"></td>";
		if ($i == 160) 	echo "</tr><tr>";
	}
}


echo "</tr></table></td>
<td valign=top>
<b>Legende</b><br>
<table>
<tr>
<td style=\"width: 5px; height: 5px;\" bgcolor=#0000AA></td>
<td>Grenze der Föderation</td>
</tr>
<tr>
<td style=\"width: 5px; height: 5px;\" bgcolor=#00AA00></td>
<td>Grenze der Romulaner</td>
</tr>
<tr>
<td style=\"width: 5px; height: 5px;\" bgcolor=#AA0000></td>
<td>Grenze der Klingonen</td>
</tr>
<tr>
<td style=\"width: 5px; height: 5px;\" bgcolor=#AAAA00></td>
<td>Grenze der Cardassianer</td>
</tr>
<tr>
<td style=\"width: 5px; height: 5px;\" bgcolor=#6666AA></td>
<td>Grenze der Verekkianer</td>
</tr>
<tr>
<td style=\"width: 5px; height: 5px;\" bgcolor=#AA6622></td>
<td>Grenze der Kessok</td>
</tr>
<tr>
<td style=\"width: 5px; height: 5px;\" bgcolor=#AAAAAA></td>
<td>Umstrittene Territorien</td>
</tr>";

/*
echo "<tr>
<td colspan=2></td>
</tr>
<tr>
<td colspan=2>Kontrolliert durch Föderation: ".$racefield[1]." Sektoren</td>
</tr>
<tr>
<td colspan=2>Kontrolliert durch Romulaner: ".$racefield[2]." Sektoren</td>
</tr>
<tr>
<td colspan=2>Kontrolliert durch Klingonen: ".$racefield[3]." Sektoren</td>
</tr>
<tr>
<td colspan=2>Kontrolliert durch Cardassianer: ".$racefield[4]." Sektoren</td>
</tr>
<tr>
<td colspan=2>Kontrolliert durch Verekkianer: ".$racefield[7]." Sektoren</td>
</tr>
<tr>
<td colspan=2>Kontrolliert durch Kessok: ".$racefield[8]." Sektoren</td>
</tr>
<tr>
<td colspan=2>Umkämpft: ".$racefield[9]." Sektoren</td>
</tr>
<tr>
</tr>";
*/
echo "</table>
</td>
</tr></table>";

		/*
		case 1:
			$col = "#0000AA";
			$racefield[1]++;
			break;
		case 2:
			$col = "#00AA00";
			$racefield[2]++;
			break;
		case 3:
			$col = "#AA0000";
			$racefield[3]++;
			break;
		case 4:
			$col = "#AAAA00";
			$racefield[4]++;
			break;
		case 5:
			$col = "#ffa200";
			$racefield[5]++;
			break;
		case 9:
			$col = "#AAAAAA";
			$racefield[9]++;
			break;
		case 11:
			$col = "#6666FF";
			$racefield[1]++;
			break;
		case 12:
			$col = "#66FF66";
			$racefield[2]++;
			break;
		case 13:
			$col = "#FF6666";
			$racefield[3]++;
			break;
		case 14:
			$col = "#FFFF66";
			$racefield[4]++;
			break;
		*/

?>