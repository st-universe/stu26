<?php
if (!$col->id) die();

if ($col->fdd[buildings_id] == 4) {

	echo "<br><form action=main.php method=get><input type=hidden name=p value=colony><input type=hidden name=s value=sc><input type=hidden name=id value=".$_GET[id]."><input type=hidden name=ps value=".$_SESSION['pagesess'].">	<input type=hidden name=a value=ets><table class=tcal style='width:900px;'><th colspan=2>Abfallentsorgung</th>";
	
	$result =  $col->gettrashgoods();
	
	$odd = 0;
	while($data=mysql_fetch_assoc($result))
	{
		if ($odd == 0) echo "<tr>";
		
		echo "<td>";
		echo "<table class=tcal style='width:300px;'><tr><td colspan=3><img src=".$gfx."/goods/".$data[goods_id].".gif title='".$data[name]."'> ".$data[name]."</td></tr><tr><td style='width:50px; background-color: #171616;'>&nbsp;</td>";
		
		echo "<td style=\"background-color: #171616\" width=65><input type=text size=2 name=mod[".$data[goods_id]."] class=text style='width:60px;'></td>";
		
		echo "<td style=\"background-color: #171616\"> / ".$data['count']."&nbsp;&nbsp;";

		echo "</td></tr></table>";
		
		echo "</td>";
		
		if ($odd == 2) echo "</tr>";
		
		$odd++;
		$odd = $odd % 3;
	}
	echo "<tr><td colspan=3 align=center><input type=submit value=Entsorgen class=button></td></tr></table></form>";
}


?>
