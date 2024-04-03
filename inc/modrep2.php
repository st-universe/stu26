<?php
if (!$col->id) die();
if ($col->fdd[buildings_id] == 40 && $col->fdd[aktiv] == 0) $result = $col->getallmods();
// if ($col->fdd[buildings_id] == 91 && $col->fdd[aktiv] == 0) $result = $col->getrepmods(2);
// if ($col->fdd[buildings_id] == 92 && $col->fdd[aktiv] == 0) $result = $col->getrepmods(1);
if ($result && mysql_num_rows($result) > 0 && $col->fdd[aktiv] == 0)
{
	// REM
	echo "<form name=\"pform\">
	<table class=tcal>
	<th>Bauplan anzeigen</th>
	<tr>
	<td width=320><select name=\"bp\" onChange=\"select_buildplan();\"><option value=0>-----------";
	$res = $col->get_buildplan_list();
	while($data=mysql_fetch_assoc($res)) echo "<option value=".$data['plans_id'].">".stripslashes(strip_tags($data['name']));
	echo "</select></td>
	<td><id id=\"binfo\"></div></td>
	</tr>
	</table>
	</form>";
	echo "<form name=\"rform\">
	<table class=tcal>
	<th>Rumpf anzeigen</th>
	<tr>
	<td width=320><select name=\"bp\" onChange=\"select_rump();\"><option value=0>-----------";
	$col->loadpossiblerumps();
	while($data=mysql_fetch_assoc($col->result)) echo "<option value=".$data['rumps_id'].">".stripslashes($data['name']);
	echo "</select></td>
	<td><id id=\"rinfo\"></div></td>
	</tr>
	</table>
	</form>";
	// REM
	
	echo "<br><form action=main.php method=get><input type=hidden name=p value=colony>
	<input type=hidden name=s value=sc><input type=hidden name=id value=".$_GET[id].">
<input type=hidden name=ps value=".$_SESSION['pagesess'].">
	<input type=hidden name=a value=rmo>
	<table class=tcal><th colspan=2>Modulherstellung</th>";
	while($data=mysql_fetch_assoc($result))
	{
		if ($data['type'] == 7) continue;
		if ($data['type'] != $lt)
		{
			if ($lt > 0) echo "<tr><td colspan=2 align=center><input type=submit value=Herstellen class=button></td></tr>";
			echo "<tr><td class=\"m\" colspan=\"2\"><img src=".$gfx."/buttons/modul_".$data['type'].".gif> ".getmodtypedescr($data['type'])."</td></tr>";
			$lt = $data['type'];
		}
		$cr = $col->getmodulecost($data[module_id]);
		// if (($data[module_id] == 955) || ($data[module_id] == 959) || ($data[module_id] == 963)) $npcmulti = "<font color=#DE7B52> x10</font>";
		// elseif ($data[module_id] == 951) $npcmulti = "<font color=#DE7B52> x4</font>";
		// else 
		$npcmulti = "";
		echo "<tr><td colspan=2><img src=".$gfx."/goods/".$data[module_id].".gif>".$npcmulti." ".stripslashes($data['name'])." (".(!$data['count'] ? 0 : $data['count']).")</td></tr>
		<tr><td width=40 style=\"background-color: #171616\"><input type=text size=2 name=mod[".$data[module_id]."] class=text></td>
		<td style=\"background-color: #171616\"><img src=".$gfx."/buttons/e_trans2.gif title='Energie'> ".$data['ecost']."/".($data['ecost'] > $col->eps ? "<font color=#FF0000>".$col->eps."</font>" : $col->eps)."&nbsp;&nbsp;";
		while($co=mysql_fetch_assoc($cr)) echo "<img src=".$gfx."/goods/".$co[goods_id].".gif title=\"".$co[name]."\"> ".$co['count']."/".(!$co[vcount] ? "<font color=#FF0000>0</font>" : ($co[vcount] < $co['count'] ? "<font color=#FF0000>".$co[vcount]."</font>" : $co[vcount]))."&nbsp;&nbsp;";
		echo "</td></tr>";
	}
	echo "<tr><td colspan=2 align=center><input type=submit value=Herstellen class=button></td></tr>";
}
echo "</table></form>";
if ($result) unset($result);
// if ($col->fdd[buildings_id] == 90) $result = $col->getreptorps();
// if ($result && mysql_num_rows($result) > 0 && $col->fdd[aktiv] == 0)
// {
	// echo "<br><form action=main.php method=get><input type=hidden name=p value=colony>
	// <input type=hidden name=s value=sc><input type=hidden name=id value=".$_GET[id].">
// <input type=hidden name=ps value=".$_SESSION['pagesess'].">
	// <input type=hidden name=a value=rto>
	// <table class=tcal><th colspan=2>Torpedoherstellung</th>";
	// while($data=mysql_fetch_assoc($result))
	// {
		// $cr = $col->gettorpcost($data[torp_type]);
		// echo "<tr><td colspan=2><img src=".$gfx."/goods/".$data[goods_id].".gif> ".$data[name]."</td></tr>
		// <tr><td style=\"background-color: #171616\" width=60><input type=text size=2 name=mod[".$data[torp_type]."] class=text> x 5</td>
		// <td style=\"background-color: #171616\"><img src=".$gfx."/buttons/e_trans2.gif title='Energie'> ".$data[ecost]."/".($data[ecost] > $col->eps ? "<font color=#FF0000>".$col->eps."</font>" : $col->eps)."&nbsp;&nbsp;";
		// while($co=mysql_fetch_assoc($cr)) echo "<img src=".$gfx."/goods/".$co[goods_id].".gif title=\"".$co[name]."\"> ".$co['count']."/".(!$co[vcount] ? "<font color=#FF0000>0</font>" : ($co[vcount] < $co['count'] ? "<font color=#FF0000>".$co[vcount]."</font>" : $co[vcount]))."&nbsp;&nbsp;";
		// echo "</td></tr>";
	// }
	// echo "<tr><td colspan=2 align=center><input type=submit value=Herstellen class=button></td></tr></table></form>";
// }

// if ($result) unset($result);
// if ($col->fdd[buildings_id] == 90) $result = $col->getjunkmodules(90,$col->id);
// if ($col->fdd[buildings_id] == 91) $result = $col->getjunkmodules(91,$col->id);
// if ($col->fdd[buildings_id] == 92) $result = $col->getjunkmodules(92,$col->id);
// if ($result && mysql_num_rows($result) > 0 && $col->fdd[aktiv] == 0)
// {
	// echo "<br><form action=main.php method=get><input type=hidden name=p value=colony>
	// <input type=hidden name=s value=sc><input type=hidden name=id value=".$_GET[id].">
	// <input type=hidden name=a value=rju>
// <input type=hidden name=ps value=".$_SESSION['pagesess'].">
	// <table class=tcal><th colspan=2>Schrottmodule instandsetzen</th>";
	// while($data=mysql_fetch_assoc($result))
	// {
		// echo "<tr><td colspan=2><img src=".$gfx."/goods/".$data[goods_id].".gif> ".$data[name]."</td></tr>
		// <tr><td width=60><input type=text size=2 name=mod[".$data[goods_id]."] class=text></td>
		// <td><table cellpadding=4 cellspacing=0><tr><td><img src=".$gfx."/buttons/e_trans2.gif title='Energie'> 10 /".(10 > $col->eps ? "<font color=#FF0000>".$col->eps."</font>" : $col->eps)."</td>";
		// echo "</tr></table></td></tr>";
	// }
	// echo "<tr><td colspan=2 align=center><input type=submit value=Instandsetzen class=button></td></tr></table></form>";
// }
?>
