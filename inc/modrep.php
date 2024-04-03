<?php
if (!$col->id) die();
if ($col->fdd[buildings_id] == 90 && $col->fdd[aktiv] == 0) $result = $col->getrepmods(3);
if ($col->fdd[buildings_id] == 91 && $col->fdd[aktiv] == 0) $result = $col->getrepmods(2);
if ($col->fdd[buildings_id] == 92 && $col->fdd[aktiv] == 0) $result = $col->getrepmods(1);
if ($result && mysql_num_rows($result) > 0 && $col->fdd[aktiv] == 0)
{
	echo "<br><form action=main.php method=get><input type=hidden name=p value=colony>
	<input type=hidden name=s value=sc><input type=hidden name=id value=".$_GET[id].">
	<input type=hidden name=a value=rmo>
<input type=hidden name=ps value=".$_SESSION['pagesess'].">
	<table class=tcal><th colspan=2>Modulherstellung</th>";
	while($data=mysql_fetch_assoc($result))
	{
		if ($data['type'] != $lt)
		{
			echo "<tr><td class=\"m\" colspan=\"2\"><img src=".$gfx."/buttons/modul_".$data['type'].".gif> ".getmodtypedescr($data['type'])."</td></tr>";
			$lt = $data['type'];
		}
		$cr = $col->getmodulecost($data[module_id]);

		if ($data[module_id] == 959) $npcmulti = "x 10";
		else $npcmulti = "";

		echo "<tr><td colspan=2><img src=".$gfx."/goods/".$data[module_id].".gif> ".$data[name]." ".$npcmulti."</td></tr>
		<tr><td width=40><input type=text size=2 name=mod[".$data[module_id]."] class=text></td>
		<td><table cellpadding=4 cellspacing=0><tr><td><img src=".$gfx."/buttons/e_trans2.gif title='Energie'> ".$data[ecost]."/".($data[ecost] > $col->eps ? "<font color=#FF0000>".$col->eps."</font>" : $col->eps)."</td>";
		while($co=mysql_fetch_assoc($cr)) echo "<td><img src=".$gfx."/goods/".$co[goods_id].".gif title=\"".$co[name]."\"> ".$co['count']."/".(!$co[vcount] ? "<font color=#FF0000>0</font>" : ($co[vcount] < $co['count'] ? "<font color=#FF0000>".$co[vcount]."</font>" : $co[vcount]))."</td>";
		echo "</tr></table></td></tr>";
	}
	echo "<tr><td colspan=2 align=center><input type=submit value=Herstellen class=button></td></tr>";
}
echo "</table></form>";
if ($result) unset($result);
if ($col->fdd[buildings_id] == 90) $result = $col->getreptorps();
if ($result && mysql_num_rows($result) > 0 && $col->fdd[aktiv] == 0)
{
	echo "<br><form action=main.php method=get><input type=hidden name=p value=colony>
	<input type=hidden name=s value=sc><input type=hidden name=id value=".$_GET[id].">
	<input type=hidden name=a value=rto>
	<table class=tcal><th colspan=2>Torpedoherstellung</th>";
	while($data=mysql_fetch_assoc($result))
	{
		$cr = $col->gettorpcost($data[torp_type]);
		echo "<tr><td colspan=2><img src=".$gfx."/goods/".$data[goods_id].".gif> ".$data[name]."</td></tr>
		<tr><td width=60><input type=text size=2 name=mod[".$data[torp_type]."] class=text> x 5</td>
		<td><table cellpadding=4 cellspacing=0><tr><td><img src=".$gfx."/buttons/e_trans2.gif title='Energie'> ".$data[ecost]."/".($data[ecost] > $col->eps ? "<font color=#FF0000>".$col->eps."</font>" : $col->eps)."</td>";
		while($co=mysql_fetch_assoc($cr)) echo "<td><img src=".$gfx."/goods/".$co[goods_id].".gif title=\"".$co[name]."\"> ".$co['count']."/".(!$co[vcount] ? "<font color=#FF0000>0</font>" : ($co[vcount] < $co['count'] ? "<font color=#FF0000>".$co[vcount]."</font>" : $co[vcount]))."</td>";
		echo "</tr></table></td></tr>";
	}
	echo "<tr><td colspan=2 align=center><input type=submit value=Herstellen class=button></td></tr></table></form>";
}
?>
