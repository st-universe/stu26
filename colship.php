<?php
// Prüfungen
if (!check_int($_GET["t"])) die(show_error(902));

// Funktionen
// Die können später in nen separates Objekt gepackt werden

$data = $db->query("SELECT a.id,a.rumps_id,a.name,a.huelle,a.max_huelle,a.schilde,a.max_schilde,a.schilde_status,a.eps,a.max_eps,a.batt,a.max_batt,a.torp_type,a.shuttle_type,a.crew,a.min_crew,a.max_crew,a.lastmaintainance,b.name as rname,b.storage,b.slots,b.is_shuttle,c.max_torps,c.maintaintime,c.m10 FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_ships_buildplans as c USING(plans_id) WHERE a.user_id=".$_SESSION["uid"]." AND a.id=".$_GET["t"]." AND systems_id=".$col->systems_id." AND sx=".$col->sx." AND sy=".$col->sy,4);
if ($data == 0) die(show_error(902));
function get_torp_load($data) { global $db; return $db->query("SELECT b.count FROM stu_torpedo_types as a LEFT JOIN stu_ships_storage as b ON b.goods_id=a.goods_id AND b.ships_id=".$data[id]." WHERE a.torp_type=".$data[torp_type],1); }
function get_possible_torps($data) { global $db,$col; return $db->query("SELECT a.name,b.goods_id,b.count FROM stu_torpedo_types as a LEFT JOIN stu_colonies_storage as b ON b.goods_id=a.goods_id AND b.colonies_id=".$col->id." WHERE a.type<=".$db->query("SELECT torp_type FROM stu_modules WHERE module_id=".$data[m10],1)." AND b.count>0"); }
function get_loaded_torptype($data) { global $db,$col; return $db->query("SELECT a.goods_id,a.name,b.count FROM stu_torpedo_types as a LEFT JOIN stu_colonies_storage as b ON b.goods_id=a.goods_id AND b.colonies_id=".$col->id." AND b.count>0 WHERE a.torp_type=".$data[torp_type],4); }

// Ende der Funktionen

// Geladene Torpedos
if ($data['max_torps'] > 0)
{
	// Geladene Torpedos
	$t_load = get_torp_load($data);
	// Torpedo-Typ laden, wenn vorhanden - wenn nich: mögliche Torpedos laden
	if ($data['torp_type'] > 0) $t_type = get_loaded_torptype($data);
}
$t_poss = get_possible_torps($data);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>Star Trek Universe</title>
<SCRIPT LANGUAGE='JavaScript'>
var max_torps = <?php echo $data['max_torps']; ?>;
var sessid =' <?php echo session_id(); ?>';
var colid = <?php echo $col->id; ?>;
var tid = <?php echo $data['id']; ?>;
var resb = '<table bgcolor=#262323 cellspacing=1 cellpadding=1><th>Meldung</th><tr><td>';
var rese = '</td></tr></table><br>';
var cole = <?php echo $col->eps; ?>;
var to = <?php $data['torp_type'] > 0 ? print($t_type['goods_id']) : print(0); ?>;
var t_load = <?php $data['torp_type'] > 0 ? print($t_load) : print(0); ?>;
var max_crew = <?php echo $data['max_crew']; ?>;
var crew = <?php echo $data['crew']; ?>;
var free_bev = <?php echo $col->bev_free; ?>;
var work_bev = <?php echo $col->bev_work; ?>;
var max_bev = <?php echo $col->bev_max; ?>;
var eps = <?php echo $data['eps']; ?>;
var max_eps = <?php echo $data['max_eps']; ?>;
var batt = <?php echo $data['batt']; ?>;
var max_batt = <?php echo $data['max_batt']; ?>;
var slots = <?php echo $data['slots']; ?>;
var is_shuttle = <?php echo $data['is_shuttle']; ?>;

function uppercrew()
{
	if (cole==0) return;
	ctc = document.forms.crew.cc.value;
	if (ctc>=max_crew) return;
	if (ctc-crew>=free_bev) return;
	document.forms.crew.cc.value++;
}
function lowercrew()
{
	if (cole==0) return;
	ctc = document.forms.crew.cc.value;
	if (ctc<=0) return;
	document.forms.crew.cc.value--;
}
function uppereps()
{
	if (cole==0) return;
	etc = document.forms.etrans.seps.value;
	if (etc >= max_eps) return;
	if (etc-eps >= cole) return;
	document.forms.etrans.seps.value++;
}
function lowereps()
{
	etc = document.forms.etrans.seps.value;
	if (etc<=eps) return;
	document.forms.etrans.seps.value--;
}
function upperbatt()
{
	if (cole==0) return;
	etc = document.forms.ebatt.sbatt.value;
	if (etc >= max_batt) return;
	if (etc-batt >= cole) return;
	document.forms.ebatt.sbatt.value++;
}
function lowerbatt()
{
	etc = document.forms.ebatt.sbatt.value;
	if (etc<=batt) return;
	document.forms.ebatt.sbatt.value--;
}
function uppertorp()
{
	if (cole==0) return;
	if (document.forms.torp.tc.disabled == true)
	{
		if (to==0) return;
		else document.forms.torp.tc.disabled = false;
	}
	ctc = document.forms.torp.tc.value;
	if (ctc>=max_torps) document.forms.torp.tc.value=max_torps;
	else document.forms.torp.tc.value++;
}
function lowertorp()
{
	if (cole==0) return;
	if (document.forms.torp.tc.disabled == true)
	{
		if (to==0) return;
		else document.forms.torp.tc.disabled = false;
	}
	ctc = document.forms.torp.tc.value;
	if (ctc<=0) document.forms.torp.tc.value=0;
	else document.forms.torp.tc.value--;
}
function savetorp()
{
	if (to==0)
	{
		document.getElementById("result").innerHTML = resb + "Es wurde kein Torpedotyp gewählt" + rese;
		return;
	}
	if (cole==0)
	{
		document.getElementById("result").innerHTML = resb + "Keine Energie vorhanden" + rese;
		return;
	}
	var st = document.forms.torp.tc.value;
	if (t_load==st)
	{
		document.getElementById("result").innerHTML = resb + "Die Anzahl wurde nicht verändert" + rese;
		return;
	}
	deactivatebuttons();
	document.forms.torp.stbut.value = 'Beame...';
	if (st<1)
	{
		if (to==0)
		{
			document.getElementById("result").innerHTML = resb + "Es wurde keine Anzahl angegeben" + rese;
			return;
		}
		document.getElementById("result").innerHTML = resb + "Alle Torpedos werden entladen..." + rese;
		setTimeout("sendRequest('backend/colship.php?PHPSESSID=" + sessid + "&id=" + colid + "&t=" + tid + "&a=stm&to=" + to + "&c="+ max_torps +"')",1500);
		return;
	}
	if (st<t_load)
	{
		st = t_load-st;
		document.getElementById("result").innerHTML = resb + st + " Torpedos werden entladen..." + rese;
		setTimeout("sendRequest('backend/colship.php?PHPSESSID=" + sessid + "&id=" + colid + "&t=" + tid + "&a=stm&to=" + to + "&c="+ st +"')",1500);
		return;
	}
	if (t_load>0 && st>t_load) st = st-t_load;
	if (st>max_torps) st = max_torps;

	document.getElementById("result").innerHTML = resb + st + " Torpedos werden geladen..." + rese;
	setTimeout("sendRequest('backend/colship.php?PHPSESSID=" + sessid + "&id=" + colid + "&t=" + tid + "&a=stc&to=" + to + "&c=" + st + "')",1500);
	return;
}
function choosetorp(toid,nam)
{
	document.getElementById("tir").innerHTML = 'Gewählt: <img src=<?php echo $gfx; ?>/goods/'+ toid +'.gif title="'+ nam +'">';
	to = toid;
	ton = nam;
	document.forms.torp.tc.disabled = false;
}
function savecrew()
{
	if (cole==0)
	{
		document.getElementById("result").innerHTML = resb + "Keine Energie vorhanden" + rese;
		return;
	}
	var st = document.forms.crew.cc.value;
	if (crew==st)
	{
		document.getElementById("result").innerHTML = resb + "Die Anzahl wurde nicht verändert" + rese;
		return;
	}
	if (free_bev+work_bev >= max_bev && st<crew)
	{
		document.getElementById("result").innerHTML = resb + "Es ist kein Wohnraum auf der Kolonie vorhanden" + rese;
		return;
	}
	deactivatebuttons();
	document.forms.crew.scbut.value = 'Beame...';
	if (st<crew)
	{
		var cc = crew-st;
		if (cc > max_bev-free_bev-work_bev) cc = max_bev-free_bev-work_bev;
		document.getElementById("result").innerHTML = resb + cc + " Crewmitglieder werden heruntergebeamt..." + rese;
		setTimeout("sendRequest('backend/colship.php?PHPSESSID=" + sessid + "&id=" + colid + "&t=" + tid + "&a=scc&c="+ st +"')",1500);
		return;
	}
	if (free_bev == 0)
	{
		document.getElementById("result").innerHTML = resb + "Es befinden sich keine Arbeitslosen auf dem Planeten" + rese;
		return;
	}
	if (st > max_crew) st = max_crew;
	if (st-crew > free_bev) st = crew+free_bev;
	var cc = st-crew;
	document.getElementById("result").innerHTML = resb + cc + " Crewmitglieder werden hochgebeamt..." + rese;
	setTimeout("sendRequest('backend/colship.php?PHPSESSID=" + sessid + "&id=" + colid + "&t=" + tid + "&a=scc&c=" + st + "')",1500);
	return;
}
function setmaxeps()
{
	document.forms.etrans.seps.value = max_eps;
	saveeps();
}
function saveeps()
{
	if (cole==0)
	{
		document.getElementById("result").innerHTML = resb + "Keine Energie vorhanden" + rese;
		return;
	}
	var st = document.forms.etrans.seps.value;
	if (st<eps) return;
	if (eps==st)
	{
		document.getElementById("result").innerHTML = resb + "Die Anzahl wurde nicht verändert" + rese;
		return;
	}
	deactivatebuttons();
	document.forms.etrans.sebut.value = 'Transfer...';
	if (st > max_eps) st = max_eps;
	if (st-eps > cole) st = parseInt(eps)+parseInt(cole);
	var cc = st-eps;
	document.getElementById("result").innerHTML = resb + cc + " Energie wird transferiert..." + rese;
	setTimeout("sendRequest('backend/colship.php?PHPSESSID=" + sessid + "&id=" + colid + "&t=" + tid + "&a=sec&c=" + st + "')",1500);
	return;
}
function setmaxbatt()
{
	document.forms.ebatt.sbatt.value = max_batt;
	savebatt();
}
function savebatt()
{
	if (slots > 0)
	{
		document.getElementById("result").innerHTML = resb + "Die Ersatzbatterie von Stationen kann nicht aufgeladen werden" + rese;
		return;
	}
	if (is_shuttle > 0)
	{
		document.getElementById("result").innerHTML = resb + "Die Ersatzbatterie von Shuttles kann nicht aufgeladen werden" + rese;
		return;
	}
	if (cole==0)
	{
		document.getElementById("result").innerHTML = resb + "Keine Energie vorhanden" + rese;
		return;
	}
	var st = document.forms.ebatt.sbatt.value;
	if (batt==st)
	{
		document.getElementById("result").innerHTML = resb + "Die Anzahl wurde nicht verändert" + rese;
		return;
	}
	deactivatebuttons();
	document.forms.ebatt.sbbut.value = 'Lade...';
	if (st > max_batt) st = max_batt;
	if (st-batt > cole) st = cole;
	if (batt > st) batt = st;
	var cc = st-batt;
	document.getElementById("result").innerHTML = resb + "Die Batterie wird um " + cc + " Energie aufgeladen..." + rese;
	setTimeout("sendRequest('backend/colship.php?PHPSESSID=" + sessid + "&id=" + colid + "&t=" + tid + "&a=sbc&c=" + st + "')",1500);
	return;
}
// Alle Buttons deaktivieren
function deactivatebuttons()
{
	document.forms.etrans.sebut.disabled = true;
	document.forms.etrans.semax.disabled = true;
	document.forms.crew.scbut.disabled = true;
	document.forms.torp.stbut.disabled = true;
	document.forms.ebatt.sbbut.disabled = true;
	document.forms.ebatt.sbmax.disabled = true;
}
// Alle Buttons aktivieren
function activatebuttons()
{
	document.forms.etrans.sebut.disabled = false;
	document.forms.etrans.semax.disabled = false;
	document.forms.crew.scbut.disabled = false;
	document.forms.torp.stbut.disabled = false;
	document.forms.ebatt.sbbut.disabled = false;
	document.forms.ebatt.sbmax.disabled = false;
}
// Requestobjekt
function createRequestObject()
{
	var ro;
	var browser = navigator.appName;
	if(browser == "Microsoft Internet Explorer"){
		ro = new ActiveXObject("Microsoft.XMLHTTP");
	}else{
		ro = new XMLHttpRequest();
	}
    return ro;
}
var http = createRequestObject();
function sendRequest(action)
{
	http.open('get', action);
	http.onreadystatechange = handleResponse;
	http.send(null);
}
function handleResponse()
{
	if(http.readyState == 4)
	{
		var response = http.responseText;
		if(response.length > 0)
		{
			var rl = response.split('\n');
			activatebuttons();
			document.getElementById("result").innerHTML = resb + rl[2] + rese;
			if (rl[0] == "stc")
			{
				var meta = rl[1].split('|');
				document.forms.torp.stbut.value = 'Beamen';
				if (meta[0] > 0)
				{
					t_load = meta[1];
					document.getElementById("cole").innerHTML -= meta[2];
					document.getElementById("tir").innerHTML = "Geladen: <img src=<?php echo $gfx; ?>/goods/"+ to +".gif title='"+ ton +"'>";
					document.getElementById("ctp").innerHTML = '';
				}
				return;
			}
			if (rl[0] == "stm")
			{
				var meta = rl[1].split('|');
				if (meta[1] == 0)
				{
					document.getElementById("tir").innerHTML = '';
					document.getElementById("ctp").innerHTML = meta[3];
					document.getElementById("cole").innerHTML -= meta[2];
					cole -= meta[2];
					t_load = 0;
				}
				else
				{
					t_load = meta[1];
					document.getElementById("cole").innerHTML -= meta[2];
					cole -= meta[2];
				}
				document.forms.torp.stbut.value = 'Beamen';
				return;
			}
			if (rl[0] == "scc")
			{
				var meta = rl[1].split('|');
				document.getElementById("colc").innerHTML = meta[0];
				document.getElementById("cole").innerHTML = meta[1];
				document.forms.crew.cc.value = meta[2];
				crew = meta[2];
				free_bev = meta[0];
				cole = meta[1];
				document.forms.crew.scbut.value = 'Beamen';
				return;
			}
			if (rl[0] == "sec")
			{
				document.getElementById("epsstate").innerHTML = rl[3];
				var meta = rl[1].split('|');
				document.getElementById("cole").innerHTML = meta[1];
				document.forms.etrans.seps.value = meta[0];
				eps = meta[0];
				cole = meta[1];
				document.forms.etrans.sebut.value = 'Transfer';
				return;
			}
			if (rl[0] == "sbc")
			{
				var meta = rl[1].split('|');
				document.getElementById("cole").innerHTML = meta[1];
				document.forms.ebatt.sbatt.value = meta[0];
				batt = meta[0];
				cole = meta[1];
				document.forms.ebatt.sbbut.value = 'Laden';
				return;
			}
		}
    }
}
</script>
<?php

pageheader("/ <a href=?p=colony>Kolonien</a> / <a href=?p=colony&s=sc&id=".$_GET["id"].">".stripslashes($col->name)."</a> / <b>Schiff ausrüsten</b>");

echo "<div id=\"result\"></div>
<table class=tcal>
<th></th><th></th><th>Hülle</th><th>Schilde</th><th>Energie</th>
<tr>
	<td><a href=?p=ship&s=ss&id=".$data['id']."><img src=".$gfx."/ships/".vdam($data).$data[rumps_id].".gif title=\"".ftit($data[rname])."\" border=0></a></td><td>".stripslashes($data[name])."</td>
	<td>".renderhuellstatusbar($data[huelle],$data[max_huelle])." ".$data[huelle]."/".$data[max_huelle]."</td>
	<td>".rendershieldstatusbar($data[schilde_status],$data[schilde],$data[max_schilde])." ".$data[schilde]."/".$data[max_schilde]."</td><td><div id=\"epsstate\">".renderepsstatusbar($data[eps],$data[max_eps])." ".$data[eps]."/".$data[max_eps]."</div></td>
</tr>
</table><br>
<table width=100% cellpadding=0 cellspacing=0>
<tr>
	<td width=250 valign=top>";
	// Torpedos start
	// Anzeige
	echo "<table class=tcal><th colspan=2>Torpedos</th><form onSubmit=\"return(false);\" name=torp><tr><td><div id=\"ctp\">";
	if ($data[torp_type] == 0)
	{
		echo "Typ wählen: ";
		if (mysql_num_rows($t_poss) != 0) while($dat=mysql_fetch_assoc($t_poss)) echo "<a href=\"javascript:void(0);\" onClick=\"choosetorp(".$dat[goods_id].",'".$dat[name]."');\"><img src=".$gfx."/goods/".$dat[goods_id].".gif border=\"0\" title=\"".ftit($dat[name])."\"></a>&nbsp;";
		else echo "-";
	}
	echo "</div></td><td><div id=\"tir\">";
	if ($data[torp_type] > 0) echo "Geladen: <img src=".$gfx."/goods/".$t_type[goods_id].".gif title=\"".$t_type[name]."\">";
	echo "</div></td></tr><tr><td><table cellpadding=0 cellspacing=0><tr><td rowspan=3>Anzahl:&nbsp;</td><td style=\"padding-left: 7px;\"><img src=".$gfx."/buttons/pup.gif title=\"Anzahl erhöhen\" onClick=\"uppertorp();\"></td></tr><tr><td style=\"padding-top: 2px; padding-bottom: 2px;\"><input type=text size=1 class=text name=tc value=".($data[torp_type] == 0 ? "0 disabled" : $t_load)."> / ".$data[max_torps]."</td></tr><tr><td style=\"padding-left: 7px;\"><img src=".$gfx."/buttons/pdown.gif title=\"Anzahl verringern\" onClick=\"lowertorp();\"></td></tr></table></td>
	<td><input type=button class=button value=Beamen name=\"stbut\" onClick=\"savetorp();\"></td></tr>";
	echo "</form></table>";
	// Torpedos end
	echo "</td><td width=10>&nbsp;</td><td valign=top width=250>";
	// Crew start
	echo "<table class=tcal><th colspan=2>Crew</th><form onSubmit=\"return(false);\" name=crew>
	<tr><td><table cellpadding=0 cellspacing=0><tr><td rowspan=3>Crew:&nbsp;</td><td style=\"padding-left: 7px;\"><img src=".$gfx."/buttons/pup.gif title=\"Anzahl erhöhen\" onClick=\"uppercrew();\"></td></tr><tr><td style=\"padding-top: 2px; padding-bottom: 2px;\"><input type=text size=1 class=text name=cc value=".$data[crew]."> / ".$data[max_crew]." (".$data[min_crew].")</td></tr><tr><td style=\"padding-left: 7px;\"><img src=".$gfx."/buttons/pdown.gif title=\"Anzahl verringern\" onClick=\"lowercrew();\"></td></tr></table></td><td><input type=button class=button value=Beamen name=\"scbut\" onClick=\"savecrew();\"></td></tr>
	</form></table>";
	// Crew end
	echo "</td><td width=10>&nbsp;</td><td valign=top width=150>";
	// Kolonie start
	echo "<table class=tcal><th colspan=2>Kolonie</th>
	<tr><td colspan=2><img src=".$gfx."/planets/".$col->colonies_classes_id.".gif width=15 height=15> ".$col->name."</td></tr>
	<tr><td><img src=".$gfx."/buttons/e_trans2.gif title=\"Energie\"> Energie</td><td><div id=\"cole\">".$col->eps."</div></td></tr>
	<tr><td><img src=".$gfx."/bev/bev_unused_1_".$_SESSION[race].".gif title=\"Arbeitslose\"> Arbeitslose</td><td><div id=\"colc\">".$col->bev_free."</div></td></tr></table>";
	// Kolonie end
	echo "</td><td>&nbsp;</td>
</tr>
<tr>
	<td width=250 valign=top>";
	// Energietransfer start
	echo "<table class=tcal><th colspan=2>Energietransfer</th><form onSubmit=\"return(false);\" name=etrans>
	<tr><td><table cellpadding=0 cellspacing=0><tr><td rowspan=3>EPS:&nbsp;</td><td style=\"padding-left: 7px;\"><img src=".$gfx."/buttons/pup.gif title=\"Anzahl erhöhen\" onClick=\"uppereps();\"></td></tr><tr><td style=\"padding-top: 2px; padding-bottom: 2px;\"><input type=text size=3 class=text name=seps value=".$data[eps]."> / ".$data[max_eps]."</td></tr><tr><td style=\"padding-left: 7px;\"><img src=".$gfx."/buttons/pdown.gif title=\"Anzahl verringern\" onClick=\"lowereps();\"></td></tr></table></td><td><input type=button class=button value=Transfer name=\"sebut\" onClick=\"saveeps();\">&nbsp;<input type=\"button\" class=\"button\" value=\"max\" name=\"semax\" onClick=\"setmaxeps();\"></td></tr>
	</form></table>";
	// Energietransfer end
	echo "</td><td width=10>&nbsp;</td><td valign=top width=250>";
	// Ersatzbatterie start
	echo "<table class=tcal><th colspan=2>Ersatzbatterie</th><form onSubmit=\"return(false);\" name=ebatt>
	<tr><td><table cellpadding=0 cellspacing=0><tr><td rowspan=3>Batterie:&nbsp;</td><td style=\"padding-left: 7px;\"><img src=".$gfx."/buttons/pup.gif title=\"Anzahl erhöhen\" onClick=\"upperbatt();\"></td></tr><tr><td style=\"padding-top: 2px; padding-bottom: 2px;\"><input type=text size=3 class=text name=sbatt value=".$data[batt]."> / ".$data[max_batt]."</td></tr><tr><td style=\"padding-left: 7px;\"><img src=".$gfx."/buttons/pdown.gif title=\"Anzahl verringern\" onClick=\"lowerbatt();\"></td></tr></table></td><td><input type=button class=button value=Transfer name=\"sbbut\" onClick=\"savebatt();\">&nbsp;<input type=\"button\" class=\"button\" value=\"max\" name=\"sbmax\" onClick=\"setmaxbatt();\"></td></tr>
	</form></table>";
	// Ersatzbatterie end
	echo "</td><td width=10>&nbsp;</td><td valign=top width=150>";
	// Platz für Zeug

	echo "</td><td>&nbsp;</td>
</tr>
</table>";
?>