<?php
	echo "<script language=\"Javascript\">
	var elt;
	function getrinfo(rid,fg)
	{	
		elt = fg;
		sendRequest('backend/rinfo.php?PHPSESSID=".session_id()."&rid=' + rid);
		return overlib('<div id=rinfo></div>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER, STICKY, RELX, 450, RELY, 70, WIDTH, 422);
	}
	function loadinfo(rid,fg)
	{	
		elt = fg;
		sendRequest('backend/rdetail.php?PHPSESSID=".session_id()."&rid=' + rid);
	}
	function setpos(off)
	{
		elt = 'rl';
		sendRequest('backend/rlist.php?PHPSESSID=".session_id()."&off=' + off);
	}
	</script>";

	pageheader("/ <a href=?p=ship>Schiffe</a> / <a href=?p=stat&s=ss&id=".$_GET['id'].">".stripslashes($ship->name)."</a> / <b>Stationsbau</b>");
	$result = $ship->getPossibleStations();
	if (mysql_num_rows($result) == 0)
	{
		meldung("Du kannst noch keine Station bauen");
		exit;
	}
	if ($_GET["sb"] == "Bauen") meldung($ship->buildstation());
	echo "<form action=main.php method=Get><input type=hidden name=p value=stat><input type=hidden name=s value=ssm><input type=hidden name=id value=".$_GET[id].">
	<table class=tcal><tr>";
	while($data=mysql_fetch_assoc($result)) echo "<td valign=top><input type=radio name=stat value=".$data[rumps_id].($_GET["stat"] == $data[rumps_id] ? " CHECKED" : "")."> ".$data[name]." (<a href=\"javascript:void(0)\" onClick=\"getrinfo(".$data[rumps_id].",'rinfo');\">?</a>)<br><img src=".$gfx."/ships/".$data[rumps_id].".gif title=\"".$data[name]."\"></td>";
	echo "</tr></table><input type=submit value=Auswählen class=button></form>";
	if (check_int($_GET["stat"]))
	{
		if (!$col->checkrump($_GET["stat"])) die(show_error(902));
		$col->getrumpbyid($_GET["stat"]);
		echo "<form action=main.php method=get><input type=hidden name=p value=stat><input type=hidden name=s value=ssm><input type=hidden name=id value=".$_GET[id]."><input type=hidden name=stat value=".$_GET["stat"].">
		<table class=tcal>
		<tr><td colspan=8><img src=".$gfx."/buttons/sb_schilde_".$_SESSION["race"].".gif></td></tr>
		<tr>
			<td><b>Hüllenpanzerung</b></td><td><b>Spezial</b></td><td><b>Anzahl</b></td><td><b>Hülle</b></td></td><td><b>Punkte</b></td><td><b>Bauzeit</b></td><td><b>Wartungsbedarf</b></td>
		</tr>";
		$result = $ship->getmodbylvl($col->rump["m1minlvl"],$col->rump["m1maxlvl"],1);
		$result2 = $ship->getmodbylvl($col->rump["m2minlvl"],$col->rump["m2maxlvl"],2);
		while($data=mysql_fetch_assoc($result))
		{
			if ($_GET["m".$data[type]] == $data[module_id]) $bm[] = $data;
			echo "<tr>
			<td><input type=\"radio\" name=\"m".$data[type]."\" value=\"".$data[module_id]."\"".($_GET["m".$data[type]] == $data[module_id] ? " CHECKED" : "")."> <img src=".$gfx."/goods/".$data[module_id].".gif title=\"".$data[name]."\"> ".$data[name]."</td>
			<td>".$col->getmodulespecial($data)."</td>
			<td>".(!$data["count"] ? "<font color=#FF0000>0</font>" : ($data["count"] < $col->rump["m".$data[type]."c"] ? "<font color=#FF0000>".$data["count"]."</font>" : $data["count"]))."/".$col->rump["m".$data[type]."c"]."</td>
			<td>".$data[huelle]." (= ".round($data[huelle]*$col->rump["m".$data[type]."c"])." Hülle)</td>
			<td>".getwprange($data[level],$data[points])."</td>
			<td>".getbzrange($data[level],$data[buildtime])."</td>
			<td>".getmaintainrange($data[level],$data[maintaintime])."</td>
			</tr>";
		}
		echo "<tr><td><b>Schildemitter</b></td><td><b>Spezial</b></td><td><b>Anzahl</b></td><td><b>Schilde</b></td><td><b>Punkte</b></td></td><td><b>Bauzeit</b></td><td><b>Wartungsbedarf</b></td></tr>";
		while($data=mysql_fetch_assoc($result2))
		{
			if ($_GET["m".$data[type]] == $data[module_id]) $bm[] = $data;
			echo "<tr>
			<td><input type=\"radio\" name=\"m".$data[type]."\" value=\"".$data[module_id]."\"".($_GET["m".$data[type]] == $data[module_id] ? " CHECKED" : "")."> <img src=".$gfx."/goods/".$data[module_id].".gif title=\"".$data[name]."\"> ".$data[name]."</td>
			<td>".$col->getmodulespecial($data)."</td>
			<td>".(!$data["count"] ? "<font color=#FF0000>0</font>" : ($data["count"] < $col->rump["m".$data[type]."c"] ? "<font color=#FF0000>".$data["count"]."</font>" : $data["count"]))."/".$col->rump["m".$data[type]."c"]."</td>
			<td>".$data[schilde]." (= ".round($data[schilde]*$col->rump["m".$data[type]."c"])." Schilde)</td>
			<td>".getwprange($data[level],$data[points])."</td>
			<td>".getbzrange($data[level],$data[buildtime])."</td>
			<td>".getmaintainrange($data[level],$data[maintaintime])."</td>
			</tr>";
		}
		echo "</table><br>";
		$result = $ship->getmodbylvl($col->rump["m10minlvl"],$col->rump["m10maxlvl"],10);
		$result2 = $ship->getmodbylvl($col->rump["m6minlvl"],$col->rump["m6maxlvl"],6);
		echo "<table class=tcal>
		<tr><td colspan=8><img src=".$gfx."/buttons/sb_waffen_".$_SESSION["race"].".gif></td></tr>
		<tr><td><b>Strahlenwaffen</b></td><td><b>Spezial</b></td><td><b>Anzahl</b></td><td><b>Stärke</b></td><td><b>Punkte</b></td></td><td><b>Bauzeit</b></td><td><b>Wartungsbedarf</b></td></tr>
		<tr><td colspan=7><input type=\"radio\" name=\"m6\" value=\"\"".(!$_GET["m6"] ? " CHECKED" : "")."> Keine</td></tr>";
		while($data=mysql_fetch_assoc($result2))
		{
			if ($_GET["m".$data[type]] == $data[module_id]) $bm[] = $data;
			$weapon = $col->getweaponbyid($data[module_id]);
			echo "<tr>
			<td><input type=\"radio\" name=\"m".$data[type]."\" value=\"".$data[module_id]."\"".($_GET["m".$data[type]] == $data[module_id] ? " CHECKED" : "")."> <img src=".$gfx."/goods/".$data[module_id].".gif title=\"".$data[name]."\"> ".$data[name]."</td>
			<td>".$col->getmodulespecial($data)."</td>
			<td>".(!$data["count"] ? "<font color=#FF0000>0</font>" : ($data["count"] < $col->rump["m".$data[type]."c"] ? "<font color=#FF0000>".$data["count"]."</font>" : $data["count"]))."/".$col->rump["m".$data[type]."c"]."</td>
			<td>".$weapon[strength]." (Abweichung: ".$weapon[varianz]."%)</td>
			<td>".getwprange($data[level],$data[points])."</td>
			<td>".getbzrange($data[level],$data[buildtime])."</td>
			<td>".getmaintainrange($data[level],$data[maintaintime])."</td>
			</tr>";
		}
		if ($col->rump[m10c] > 0)
		{
			echo "<tr><td><b>Torpedorampe</b></td><td><b>Spezial</b></td><td><b>Anzahl</b></td><td><b>T-Kapazität</b></td><td><b>Punkte</b></td><td><b>Bauzeit</b></td><td><b>Wartungsbedarf</b></td>
			</tr>
			<tr><td colspan=7><input type=\"radio\" name=\"m10\" value=\"\"".(!$_GET["m10"] ? " CHECKED" : "")."> Keine</td></tr>";
			while($data=mysql_fetch_assoc($result))
			{
				if ($_GET["m".$data[type]] == $data[module_id]) $bm[] = $data;
				echo "<tr>
				<td><input type=\"radio\" name=\"m".$data[type]."\" value=\"".$data[module_id]."\"".($_GET["m".$data[type]] == $data[module_id] ? " CHECKED" : "")."> <img src=".$gfx."/goods/".$data[module_id].".gif title=\"".$data[name]."\"> ".$data[name]."</td>
				<td>".$col->getmodulespecial($data)."</td>
				<td>".(!$data["count"] ? "<font color=#FF0000>0</font>" : ($data["count"] < $col->rump["m".$data[type]."c"] ? "<font color=#FF0000>".$data["count"]."</font>" : $data["count"]))."/".$col->rump["m".$data[type]."c"]."</td>
				<td>".($data[torps]*$col->rump["m".$data[type]."c"])."</td>
				<td>".getwprange($data[level],$data[points])."</td>
				<td>".getbzrange($data[level],$data[buildtime])."</td>
				<td>".getmaintainrange($data[level],$data[maintaintime])."</td>
				</tr>";
			}
		}
		echo "</table><br>";
		$result = $ship->getmodbylvl($col->rump["m8minlvl"],$col->rump["m8maxlvl"],8);
		$result2 = $ship->getmodbylvl($col->rump["m5minlvl"],$col->rump["m5maxlvl"],5);
		echo "<table class=tcal>
		<tr><td colspan=8><img src=".$gfx."/buttons/sb_energie_".$_SESSION["race"].".gif></td></tr>
		<tr>
			<td><b>EPS-Leitungen</b></td><td><b>Spezial</b></td><td><b>Anzahl</b></td><td><b>EPS</b></td><td><b>Punkte</b></td><td><b>Bauzeit</b></td><td><b>Wartungsbedarf</b></td>
		</tr>";
		while($data=mysql_fetch_assoc($result))
		{
			if ($_GET["m".$data[type]] == $data[module_id]) $bm[] = $data;
			echo "<tr>
			<td><input type=\"radio\" name=\"m".$data[type]."\" value=\"".$data[module_id]."\"".($_GET["m".$data[type]] == $data[module_id] ? " CHECKED" : "")."> <img src=".$gfx."/goods/".$data[module_id].".gif title=\"".$data[name]."\"> ".$data[name]."</td>
			<td>".$col->getmodulespecial($data)."</td>
			<td>".(!$data["count"] ? "<font color=#FF0000>0</font>" : ($data["count"] < $col->rump["m".$data[type]."c"] ? "<font color=#FF0000>".$data["count"]."</font>" : $data["count"]))."/".$col->rump["m".$data[type]."c"]."</td>
			<td>".$data[eps]." (= ".round($data[eps]*$col->rump["m".$data[type]."c"])." EPS)</td>
			<td>".getwprange($data[level],$data[points])."</td>
			<td>".getbzrange($data[level],$data[buildtime])."</td>
			<td>".getmaintainrange($data[level],$data[maintaintime])."</td>
			</tr>";
		}
		if ($col->rump[m5c] > 0)
		{
			echo "<tr><td><b>Warpkern</b></td><td><b>Spezial</b></td><td><b>Anzahl</b></td><td><b>Reaktor</b><td><b>Punkte</b></td></td><td><b>Bauzeit</b></td><td><b>Wartungsbedarf</b></td></tr>
			<tr><td colspan=8><input type=\"radio\" name=\"m5\" value=\"\"".(!$_GET["m5"] ? " CHECKED" : "")."> Keinen</td></tr>";
			while($data=mysql_fetch_assoc($result2))
			{
				if ($_GET["m".$data[type]] == $data[module_id]) $bm[] = $data;
				echo "<tr>
				<td><input type=\"radio\" name=\"m".$data[type]."\" value=\"".$data[module_id]."\"".($_GET["m".$data[type]] == $data[module_id] ? " CHECKED" : "")."> <img src=".$gfx."/goods/".$data[module_id].".gif title=\"".$data[name]."\"> ".$data[name]."</td>
				<td>".$col->getmodulespecial($data)."</td>
				<td>".(!$data["count"] ? "<font color=#FF0000>0</font>" : ($data["count"] < $col->rump["m".$data[type]."c"] ? "<font color=#FF0000>".$data["count"]."</font>" : $data["count"]))."/".$col->rump["m".$data[type]."c"]."</td>
				<td>".$data[reaktor]." (=".($data[reaktor]*$col->rump["m".$data[type]."c"]).")<br>Kapazität: ".($data[wkkap]*$col->rump["m".$data[type]."c"])."</td>
				<td>".getwprange($data[level],$data[points])."</td>
				<td>".getbzrange($data[level],$data[buildtime])."</td>
				<td>".getmaintainrange($data[level],$data[maintaintime])."</td>
				</tr>";
			}
		}
		echo "</table><br>";
		$result = $ship->getmodbylvl($col->rump["m4minlvl"],$col->rump["m4maxlvl"],4);
		echo "<table class=tcal>
		<tr><td colspan=8><img src=".$gfx."/buttons/sb_sensor_".$_SESSION["race"].".gif></td></tr>
		<tr>
			<td><b>Sensoren</b></td><td><b>Spezial</b></td><td><b>Anzahl</b></td><td><b>KSS/LSS</b><td><b>Punkte</b></td></td><td><b>Bauzeit</b></td><td><b>Wartungsbedarf</b></td>
		</tr>";
		while($data=mysql_fetch_assoc($result))
		{
			if ($_GET["m".$data[type]] == $data[module_id]) $bm[] = $data;
			echo "<tr>
			<td><input type=\"radio\" name=\"m".$data[type]."\" value=\"".$data[module_id]."\"".($_GET["m".$data[type]] == $data[module_id] ? " CHECKED" : "")."> <img src=".$gfx."/goods/".$data[module_id].".gif title=\"".$data[name]."\"> ".$data[name]."</td>
			<td>".$col->getmodulespecial($data)."</td>
			<td>".(!$data["count"] ? "<font color=#FF0000>0</font>" : ($data["count"] < $col->rump["m".$data[type]."c"] ? "<font color=#FF0000>".$data["count"]."</font>" : $data["count"]))."/".$col->rump["m".$data[type]."c"]."</td>
			<td>".$data[kss]."/".$data[lss]."</td>
			<td>".getwprange($data[level],$data[points])."</td>
			<td>".getbzrange($data[level],$data[buildtime])."</td>
			<td>".getmaintainrange($data[level],$data[maintaintime])."</td>
			</tr>";
		}
		echo "</table><br>";
		$result = $ship->getmodbylvl($col->rump["m3minlvl"],$col->rump["m3maxlvl"],3);
		$result2 = $ship->getmodbylvl($col->rump["m9minlvl"],$col->rump["m9maxlvl"],9);
		echo "<table class=tcal>
		<tr><td colspan=8><img src=".$gfx."/buttons/sb_support_".$_SESSION["race"].".gif></td></tr>
		<tr>
			<td><b>Computer</b></td><td><b>Spezial</b></td><td><b>Anzahl</b></td><td><b>Treffer-%</b></td><td><b>Punkte</b></td><td><b>Bauzeit</b></td><td><b>Wartungsbedarf</b></td>
		</tr>";
		while($data=mysql_fetch_assoc($result))
		{
			if ($_GET["m".$data[type]] == $data[module_id]) $bm[] = $data;
			echo "<tr>
			<td><input type=\"radio\" name=\"m".$data[type]."\" value=\"".$data[module_id]."\"".($_GET["m".$data[type]] == $data[module_id] ? " CHECKED" : "")."> <img src=".$gfx."/goods/".$data[module_id].".gif title=\"".$data[name]."\"> ".$data[name]."</td>
			<td>".$col->getmodulespecial($data)."</td>
			<td>".(!$data["count"] ? "<font color=#FF0000>0</font>" : ($data["count"] < $col->rump["m".$data[type]."c"] ? "<font color=#FF0000>".$data["count"]."</font>" : $data["count"]))."/".$col->rump["m".$data[type]."c"]."</td>
			<td>+".$data[hit_val]."%</td>
			<td>".getwprange($data[level],$data[points])."</td>
			<td>".getbzrange($data[level],$data[buildtime])."</td>
			<td>".getmaintainrange($data[level],$data[maintaintime])."</td>
			</tr>";
		}
		if ($col->rump[cloakable] == 1)
		{
			echo "<tr><td><b>Tarnung</b></td><td><b>Spezial</b></td><td><b>Anzahl</b></td><td><b>Tarnung</b></td><td><b>Punkte</b></td></td><td><b>Bauzeit</b></td><td><b>Wartungsbedarf</b></td></tr>
			<tr><td colspan=7><input type=\"radio\" name=\"m9\" value=\"\"".(!$_GET["m9"] ? " CHECKED" : "")."> Keine</td></tr>";
			while($data=mysql_fetch_assoc($result2))
			{
				if ($_GET["m".$data[type]] == $data[module_id]) $bm[] = $data;
				echo "<tr>
				<td><input type=\"radio\" name=\"m".$data[type]."\" value=\"".$data[module_id]."\"".($_GET["m".$data[type]] == $data[module_id] ? " CHECKED" : "")."> <img src=".$gfx."/goods/".$data[module_id].".gif title=\"".$data[name]."\"> ".$data[name]."</td>
				<td>".$col->getmodulespecial($data)."</td>
				<td>".(!$data["count"] ? "<font color=#FF0000>0</font>" : ($data["count"] < $col->rump["m".$data[type]."c"] ? "<font color=#FF0000>".$data["count"]."</font>" : $data["count"]))."/".$col->rump["m".$data[type]."c"]."</td>
				<td>Level ".$data[level]."</td>
				<td>".getwprange($data[level],$data[points])."</td>
				<td>".getbzrange($data[level],$data[buildtime])."</td>
				<td>".getmaintainrange($data[level],$data[maintaintime])."</td>
				</tr>";
			}
		}
		echo "</table><br><input type=submit value=Vorschau class=button> <input type=submit name=sb value=Bauen class=button><br><br>";
		unset($weapon);
		if (is_array($bm))
		{
			foreach($bm as $key => $data)
			{
				$huelle += $data[huelle]*$col->rump["m".$data[type]."c"];
				$bz += $data[buildtime];
				$maintain += $data[maintaintime];
				$points += $data[points];
				$schilde += $data[schilde]*$col->rump["m".$data[type]."c"];
				$reaktor += $data[reaktor]*$col->rump["m".$data[type]."c"];
				$wkkap += $data[wkkap];
				$eps += $data[eps]*$col->rump["m".$data[type]."c"];
				if ($data[type] == 1) $ev_mul = $data[evade_val];
				else $evade += $data[evade_val];
				$abfang += $data[intercept_val];
				if ($data[type] == 4)
				{
					$lss = $data[lss]+($col->rump["m".$data[type]."c"] - 1);
					$kss = $data[kss]+($col->rump["m".$data[type]."c"] - 1);
				}
				$torps += $data[torps]*$col->rump["m".$data[type]."c"];
				$detect += $data[detect_val];
				$cloak += $data[cloak_val];
				if ($data[type] == 6) $weapon = $col->getweaponbyid($data[module_id]);
			}
			$points = round(($col->rump[wp] * $points) / 10,1);
			$reaktor += $col->rump[reaktor];
			$evade += $col->rump[evade_val];
			$evade = $evade * $ev_mul;
			$bz += $col->rump[buildtime];
			$maintain += $col->rump[maintaintime];
			if (!$weapon) $weapon = array("strength" => 0,"varianz" => 0);
			echo "<table class=tcal><th colspan=14>Vorschau</th>
			<tr><td>Bauzeit</td><td>Hülle</td><td>Schilde</td><td>Reaktor (WK-Kapazität)</td><td>EPS</td><td>Ausweich-%</td><td>Abfang-%</td><td>Enttarnung</td><td>Tarnung</td><td>KSS/LSS</td><td>Torpedos</td><td>Phaser</td><td>Abweichung</td><td>Punkte</td></tr>
			<tr><td>".gen_time($bz)."</td><td>".round($huelle)."</td><td>".round($schilde)."</td><td>".$reaktor." (".$wkkap.")</td><td>".round($eps)."</td><td>".$evade."</td><td>".$abfang."</td><td>".$detect."</td><td>".$cloak."</td><td>".$kss."/".$lss."</td><td>".$torps."</td><td>".$weapon[strength]."</td><td>".$weapon[varianz]."%</td><td>".$points."</td></tr></table><br>";
			$plan = $ship->getpossiblebuildplans();
			if ($plan != 0)
			{
				echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=700><th colspan=2>Verfügbarer Bauplan</th>
				<tr><td><img src=".$gfx."/ships/".$_GET["stat"].".gif> ".$plan[name]."</td><td>".(!$plan[idc] ? 0 : $plan[idc])." Schiffe nach diesem Bauplan gebaut</td></tr></table>";
			}
			else
			{
				echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=400><th colspan=2>Kein Bauplan gefunden - Lege neuen an</th>
				<tr><td>Name <input type=text size=15 name=bpn class=text></td></tr></table>";
			}
			echo "</form>";
		}
	}
?>