<?php
include_once("/srv/www/stu_sys/webroot/inc/config.inc.php");
include_once($global_path."/inc/func.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;

$result =$db->query("SELECT a.cx , a.cy , b.systems_id , d.race , c.user_id  FROM stu_map as a LEFT JOIN stu_systems as b ON b.cx =a.cx AND b.cy =a.cy LEFT  JOIN stu_ships as c ON c.systems_id =b.systems_id LEFT JOIN stu_user as d ON d.id =c.user_id LEFT JOIN stu_rumps as e ON e.rumps_id=c.rumps_id WHERE c.cloak='0' AND e.slots>0 AND c.user_id<100 GROUP by c.user_id, c.systems_id   HAVING d.race <= 4 ORDER BY a.cy,a.cx");

while($data=mysql_fetch_assoc($result))
{
	if (($data['systems_id'] != 1443) && ($data['systems_id'] != 1503) && ($data['systems_id'] != 1271) && ($data['systems_id'] != 1293))
	{
		$val[$data['cx']][$data['cy']][$data['race']] += 50;
		$val[$data['cx']][$data['cy']]['sys'] = $data['systems_id'];
	}
}

$wprange = 2.0;

$pc = 0;
for($i=1;$i<=160;$i++)
{
	for($j=1;$j<=160;$j++)
	{
		if ($val[$i][$j]['sys'] == 0) $field[$i][$j] = 0;
		else
		{
			if (($val[$i][$j][1] > 20) || ($val[$i][$j][2] > 20) || ($val[$i][$j][3] > 20) || ($val[$i][$j][4] > 20) || ($val[$i][$j][5] > 20))
			{
			$field[$i][$j] = 9;
			if (($val[$i][$j][1] > $val[$i][$j][2]*$wprange) && ($val[$i][$j][1] > $val[$i][$j][3]*$wprange) && ($val[$i][$j][1] >  $val[$i][$j][4]*$wprange) && ($val[$i][$j][1] > $val[$i][$j][5]*$wprange)) $field[$i][$j] = 1;

			if (($val[$i][$j][2] > $val[$i][$j][1]*$wprange) && ($val[$i][$j][2] > $val[$i][$j][3]*$wprange) && ($val[$i][$j][2] > $val[$i][$j][4]*$wprange) && ($val[$i][$j][2] > $val[$i][$j][5]*$wprange)) $field[$i][$j] = 2;

			if (($val[$i][$j][3] > $val[$i][$j][1]*$wprange) && ($val[$i][$j][3] > $val[$i][$j][2]*$wprange) && ($val[$i][$j][3] > $val[$i][$j][4]*$wprange) && ($val[$i][$j][3] > $val[$i][$j][5]*$wprange)) $field[$i][$j] = 3;

			if (($val[$i][$j][4] > $val[$i][$j][1]*$wprange) && ($val[$i][$j][4] > $val[$i][$j][2]*$wprange) && ($val[$i][$j][4] > $val[$i][$j][3]*$wprange) && ($val[$i][$j][4] > $val[$i][$j][5]*$wprange)) $field[$i][$j] = 4;

			$pc++;
			$points[$pc]['x'] = $i;			
			$points[$pc]['y'] = $j;
			$points[$pc]['race'] = $field[$i][$j];
			//echo "<br>".$pc.": ".$points[$pc]['x']."|".$points[$pc]['y']."  R: ".$points[$pc]['race'];
			}
			else $field[$i][$j] = 0;
		}
	}
}

$pc++;
$points[$pc]['x'] = 21;			
$points[$pc]['y'] = 95;
$points[$pc]['race'] = 1;
$pc++;
$points[$pc]['x'] = 25;			
$points[$pc]['y'] = 130;
$points[$pc]['race'] = 1;
$pc++;
$points[$pc]['x'] = 52;			
$points[$pc]['y'] = 147;
$points[$pc]['race'] = 1;
$pc++;
$points[$pc]['x'] = 131;			
$points[$pc]['y'] = 35;
$points[$pc]['race'] = 1;
$pc++;
$points[$pc]['x'] = 41;
$points[$pc]['y'] = 80;
$points[$pc]['race'] = 1;

$pc++;
$points[$pc]['x'] = 100;			
$points[$pc]['y'] = 105;
$points[$pc]['race'] = 2;
$pc++;
$points[$pc]['x'] = 87;
$points[$pc]['y'] = 94;
$points[$pc]['race'] = 2;
$pc++;
$points[$pc]['x'] = 68;
$points[$pc]['y'] = 111;
$points[$pc]['race'] = 2;
$pc++;
$points[$pc]['x'] = 125;
$points[$pc]['y'] = 135;
$points[$pc]['race'] = 2;


$pc++;
$points[$pc]['x'] = 152;
$points[$pc]['y'] = 80;
$points[$pc]['race'] = 3;
$pc++;
$points[$pc]['x'] = 129;
$points[$pc]['y'] = 66;
$points[$pc]['race'] = 3;
$pc++;
$points[$pc]['x'] = 99;
$points[$pc]['y'] = 27;
$points[$pc]['race'] = 3;
$pc++;
$points[$pc]['x'] = 85;
$points[$pc]['y'] = 39;
$points[$pc]['race'] = 3;

$pc++;
$points[$pc]['x'] = 39;
$points[$pc]['y'] = 26;
$points[$pc]['race'] = 4;
$pc++;
$points[$pc]['x'] = 20;
$points[$pc]['y'] = 18;
$points[$pc]['race'] = 4;
$pc++;
$points[$pc]['x'] = 146;
$points[$pc]['y'] = 50;
$points[$pc]['race'] = 4;

$pc++;
$points[$pc]['x'] = 61;
$points[$pc]['y'] = 6;
$points[$pc]['race'] = 3;

$pc++;
$points[$pc]['x'] = 63;
$points[$pc]['y'] = 23;
$points[$pc]['race'] = 3;

$pc++;
$points[$pc]['x'] = 125;
$points[$pc]['y'] = 56;
$points[$pc]['race'] = 3;

$pc++;
$points[$pc]['x'] = 142;
$points[$pc]['y'] = 68;
$points[$pc]['race'] = 3;

$pc++;
$points[$pc]['x'] = 152;
$points[$pc]['y'] = 70;
$points[$pc]['race'] = 3;

$pc++;
$points[$pc]['x'] = 48;
$points[$pc]['y'] = 8;
$points[$pc]['race'] = 4;

$pc++;
$points[$pc]['x'] = 10;
$points[$pc]['y'] = 30;
$points[$pc]['race'] = 4;

$pc++;
$points[$pc]['x'] = 4;
$points[$pc]['y'] = 34;
$points[$pc]['race'] = 4;

$pc++;
$points[$pc]['x'] = 18;
$points[$pc]['y'] = 56;
$points[$pc]['race'] = 1;

$pc++;
$points[$pc]['x'] = 151;
$points[$pc]['y'] = 36;
$points[$pc]['race'] = 4;

$pc++;
$points[$pc]['x'] = 147;
$points[$pc]['y'] = 29;
$points[$pc]['race'] = 1;

$pc++;
$points[$pc]['x'] = 157;
$points[$pc]['y'] = 33;
$points[$pc]['race'] = 4;

$pc++;
$points[$pc]['x'] = 142;
$points[$pc]['y'] = 13;
$points[$pc]['race'] = 1;

$pc++;
$points[$pc]['x'] = 53;
$points[$pc]['y'] = 129;
$points[$pc]['race'] = 1;

$pc++;
$points[$pc]['x'] = 45;
$points[$pc]['y'] = 109;
$points[$pc]['race'] = 1;

$pc++;
$points[$pc]['x'] = 57;
$points[$pc]['y'] = 80;
$points[$pc]['race'] = 1;

$pc++;
$points[$pc]['x'] = 91;
$points[$pc]['y'] = 146;
$points[$pc]['race'] = 2;

$pc++;
$points[$pc]['x'] = 68;
$points[$pc]['y'] = 115;
$points[$pc]['race'] = 2;

$pc++;
$points[$pc]['x'] = 62;
$points[$pc]['y'] = 96;
$points[$pc]['race'] = 2;

$pc++;
$points[$pc]['x'] = 76;
$points[$pc]['y'] = 77;
$points[$pc]['race'] = 2;

$pc++;
$points[$pc]['x'] = 63;
$points[$pc]['y'] = 83;
$points[$pc]['race'] = 2;

$pc++;
$points[$pc]['x'] = 144;
$points[$pc]['y'] = 114;
$points[$pc]['race'] = 2;

$pc++;
$points[$pc]['x'] = 158;
$points[$pc]['y'] = 151;
$points[$pc]['race'] = 2;


for($i=1;$i<=160;$i++)
{
	for($j=1;$j<=160;$j++)
	{
		if ($field[$i][$j] == 0)
		{
			$race = 0;
			$dtemp = 30;
			for($k=1;$k<=$pc;$k++)
			{
				$dist = sqrt( ($i-$points[$k]['x'])*($i-$points[$k]['x']) + ($j-$points[$k]['y'])*($j-$points[$k]['y']));
				if ($dist < $dtemp) 
				{
					$race = $points[$k]['race'];
					$dtemp = $dist;
				}
			}
			if ($race != 0) $field[$i][$j] = $race;
		}
	}
}

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
			$db->query("UPDATE stu_map SET faction_id=0,is_border='0' WHERE cx=".$i." AND cy=".$j." LIMIT 1");
			continue;
		}
		$db->query("UPDATE stu_map SET faction_id=".$field[$i][$j].",is_border='1' WHERE cx=".$i." AND cy=".$j." LIMIT 1");
	}
}






?>
