<?php
// if (!$sta->id) die();
if ($result) unset($result);
if ($sta->fdd[component_id] == 120) $result = $sta->getreptorps();
if ($result && mysql_num_rows($result) > 0 && $sta->fdd[aktiv] == 0)
{
	echo "<br><form action=main.php method=get><input type=hidden name=p value=station>
	<input type=hidden name=s value=show><input type=hidden name=id value=".$_GET[id].">
	<input type=hidden name=a value=rto>
	<table class=tcal><th colspan=2>Torpedoherstellung</th>";
	while($data=mysql_fetch_assoc($result))
	{
		$cr = $sta->gettorpcost($data[torp_type]);
		echo "<tr><td colspan=2><img src=".$gfx."/goods/".$data[goods_id].".gif> ".$data[name]."</td></tr>
		<tr><td width=60><input type=text size=2 name=mod[".$data[torp_type]."] class=text> x 5</td>
		<td><table cellpadding=4 cellspacing=0><tr><td><img src=".$gfx."/buttons/e_trans2.gif title='Energie'> ".$data[ecost]."/".($data[ecost] > $col->eps ? "<font color=#FF0000>".$col->eps."</font>" : $col->eps)."</td>";
		while($co=mysql_fetch_assoc($cr)) echo "<td><img src=".$gfx."/goods/".$co[goods_id].".gif title=\"".$co[name]."\"> ".$co['count']."/".(!$co[vcount] ? "<font color=#FF0000>0</font>" : ($co[vcount] < $co['count'] ? "<font color=#FF0000>".$co[vcount]."</font>" : $co[vcount]))."</td>";
		echo "</tr></table></td></tr>";
	}
	echo "<tr><td colspan=2 align=center><input type=submit value=Herstellen class=button></td></tr></table></form>";
}
?>