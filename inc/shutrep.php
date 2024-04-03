<?php
if (!$col->id) die();
if ($_SESSION['level'] == 6)
{
	echo "<form action=main.php method=get><input type=hidden name=p value=colony>
<input type=hidden name=ps value=".$_SESSION['pagesess'].">
	<input type=hidden name=s value=sc><input type=hidden name=id value=".$_GET[id].">
	<input type=hidden name=a value=rsh>
	<table class=tcal><th colspan=2>Shuttleherstellung</th>";
	$cr = $col->getrumpcost(5);
	echo "<tr><td colspan=2><img src=".$gfx."/goods/10.gif> Workbee</td></tr>
	<tr><td width=40><input type=text size=2 name=mod[10] class=text></td>
	<td><table cellpadding=4 cellspacing=0><tr><td><img src=".$gfx."/buttons/e_trans2.gif title='Energie'> 15/".(15 > $col->eps ? "<font color=#FF0000>".$col->eps."</font>" : $col->eps)."</td>";
	while($co=mysql_fetch_assoc($cr)) echo "<td><img src=".$gfx."/goods/".$co[goods_id].".gif title=\"".$co[name]."\"> ".$co['count']."/".(!$co[vcount] ? "<font color=#FF0000>0</font>" : ($co[vcount] < $co['count'] ? "<font color=#FF0000>".$co[vcount]."</font>" : $co[vcount]))."</td>";
	echo "</tr></table></td></tr>";

	echo "<tr><td colspan=2 align=center><input type=submit value=Herstellen class=button></td></tr></table></form>";
}
?>
