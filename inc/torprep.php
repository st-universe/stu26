<?php
if (!$col->id) die();
if ($col->fdd[buildings_id] == 80 && $col->fdd[aktiv] == 0) $result = $col->getreptorps();


if ($result && mysql_num_rows($result) > 0 && $col->fdd[aktiv] == 0)
{
	
	echo "<br><form action=main.php method=get><input type=hidden name=p value=colony>
	<input type=hidden name=s value=sc><input type=hidden name=id value=".$_GET[id].">
<input type=hidden name=ps value=".$_SESSION['pagesess'].">
	<input type=hidden name=a value=tpo>
	<table class=tcal><th colspan=2>Torpedoherstellung</th>";
	while($data=mysql_fetch_assoc($result))
	{


		echo "<tr><td colspan=2><img src=".$gfx."/goods/".$data[torp_type].".gif> ".stripslashes($data['name'])."</td></tr>
		<tr><td width=40 style=\"background-color: #171616\"><input type=text size=2 name=mod[".$data['torp_type']."] class=text></td>
		<td style=\"background-color: #171616\"><img src=".$gfx."/buttons/e_trans2.gif title='Energie'> ".$data['ecost']."/".($data['ecost'] > $col->eps ? "<font color=#FF0000>".$col->eps."</font>" : $col->eps)."&nbsp;&nbsp;";
		$cr = $col->gettorpcost($data['torp_type']);
		while($co=mysql_fetch_assoc($cr)) echo "<img src=".$gfx."/goods/".$co[goods_id].".gif title=\"".$co[name]."\"> ".$co['count']."/".(!$co[vcount] ? "<font color=#FF0000>0</font>" : ($co[vcount] < $co['count'] ? "<font color=#FF0000>".$co[vcount]."</font>" : $co[vcount]))."&nbsp;&nbsp;";
		echo "</td></tr>";
	}
	echo "<tr><td colspan=2 align=center><input type=submit value=Herstellen class=button></td></tr>";
}
echo "</table></form>";
if ($result) unset($result);

?>
