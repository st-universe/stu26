<?php
if (!is_object($db)) exit;
include_once($global_path."class/logbook.class.php");
$log = new logbook;

function get_log_type($ty)
{
	if ($ty == 1) return "Kampf";
	if ($ty == 2) return "Anomalien";
	if ($ty == 3) return "Sonstiges";
	if ($ty == 4) return "Privat";
}

switch ($_GET['s'])
{
	default:
		$v = "main";
	case "ma":
		$v = "main";
		break;
	case "sl":
		$v = "showlog";
		break;
	case "slc":
		if (!check_int($_GET['c']) || $_GET['c'] > 4) exit;
		$v = "showlogcat";
		break;
}

if ($v == "main")
{
	pageheader("/ <b>Logbücher</b>");
	$result = $log->load_available_logs();
	echo "<table class=\"tcal\">
	<th>Schiff</th><th>Status</th><th>Baudatum</th><th>Einträge</th>";
	if (mysql_num_rows($result) == 0) echo "<tr><td colspan=\"4\">Keine Einträge vorhanden</td></tr>";
	else
	{
		while($data=mysql_fetch_assoc($result))
		{
			$i++;
			if ($i == 2)
			{
				$trc = " style=\"background-color: #171616\"";
				$i = 0;
			}
			echo "<tr>
			<td".$trc."><a href=?p=log&s=sl&id=".$data['ships_id'].">".stripslashes($data['name'])."</a> (".$data['ships_id'].")</td>
			<td".$trc.">".($data['destroytime'] > 0 ? "außer Dienst (".date("d.m.",$data['destroytime']).setyear(date("Y",$data['destroytime'])).date(" H:i",$data['destroytime']).")" : "Im Einsatz")."</td>
			<td".$trc.">".date("d.m.",$data['buildtime']).setyear(date("Y",$data['buildtime'])).date(" H:i",$data['buildtime'])."</td>
			<td".$trc.">".$data['lc']."</td>
			</tr>";
			$trc = "";
		}
	}
	echo "</table>";
}

if ($v == "showlog")
{
	echo "<script>
	function markmessages()
	{
		for(var i=0;i<document.logs.length;++i)
		{
			document.forms.logs.elements[i].checked = true;
		}
	}
	</script>";
	pageheader("/ <a href=?p=log>Logbücher</a> / <b>".strip_tags(stripslashes($log->ship['name']))."</b>");
	if (!check_int($_GET['pa']) || $_GET['pa'] < 1) $_GET['pa'] = 1;
	
	if ($_GET['a'] == "dlo" && is_array($_GET['dlo'])) $result = $log->delete_logs($_GET['id'],$_GET['dlo']);
	if ($result) meldung($result);
	
	$result = $log->load_complete_log($_GET['pa']*50-50);
	$in = $_GET['pa'];
	$i = $in-2;
	$j = $in+2;
	$knc = $log->load_complete_log_count();
	$ceiled_knc = ceil($knc/50);
	
	$ps0 = "<td>Seite: <a href=?p=log&s=sl&id=".$log->ship['id']."&pa=1><<</a> <a href=?p=log&s=sl&id=".$log->ship['id']."&pa=".($pa == 1 ? 1 : $pa-1)."><</a></td>";
	if ($i > 1) $ps = "<td class=\"pages\"><a href=?p=log&s=sl&id=".$log->ship['id']."&pa=1>1</a></td>";
	if ($j < $ceiled_knc) $pe = "<td class=\"pages\"><a href=?p=log&s=sl&id=".$log->ship['id']."&pa=".$ceiled_knc.">".$ceiled_knc."</a></td>";
	if ($j > $ceiled_knc) $j = $ceiled_knc;
	if ($i < 1) $i = 1;
	while($i<=$j)
	{
		$pages .= "<td class=\"pages\"><a href=?p=log&s=sl&id=".$log->ship['id']."&pa=".$i.">".($i == $in ? "<div style=\"font-weight : bold; color: Yellow;\">".$i."</div>" : $i)."</a></td>";
		$i++;
	}
	$i = $in-2;
	$j = $in+2;
	$pages = $ps.($i > 2 ? "<td style=\"width: 20px; text-align: center;\">...</td>" : "").$pages.($ceiled_knc > $j+1 ? "<td style=\"width: 20px; text-align: center;\">... </td>" : "").$pe;
	$pe0 = "<td><a href=?p=log&s=sl&id=".$log->ship['id']."&pa=".($pa == $ceiled_knc ? 1 : $pa+1).">></a>&nbsp;<a href=?p=log&s=sl&id=".$log->ship['id']."&pa=".$ceiled_knc.">>></a> (".$knc." Einträge)</td>";
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1>
	<tr><th style=\"width: 100px;\">Einträge</th>
	<td class=\"pages\" style=\"width: 100px;\"><a href=?p=log&s=sl&id=".$_GET['id'].">Alle</a></td>
	<td class=\"pages\" style=\"width: 100px;\"><a href=?p=log&s=slc&c=1&id=".$_GET['id'].">Kampf</a></td>
	<td class=\"pages\" style=\"width: 100px;\"><a href=?p=log&s=slc&c=2&id=".$_GET['id'].">Anomalien</a></td>
	<td class=\"pages\" style=\"width: 100px;\"><a href=?p=log&s=slc&c=3&id=".$_GET['id'].">Sonstige</a></td>
	<td class=\"pages\" style=\"width: 100px;\"><a href=?p=log&s=slc&c=4&id=".$_GET['id'].">Privat</a></td></tr>
	</table><br>
	<form action=main.php method=post name=\"logs\"><input type=hidden name=p value=log><input type=hidden name=s value=sl><input type=hidden name=a value=dlo><input type=hidden name=id value=".$_GET['id'].">
	<table class=\"tcal\">
	<tr><td colspan=\"4\"><table><tr>".$ps0.$pages.$pe0."<td><input type=\"button\" value=\"Alle markieren\" class=\"button\" onClick=\"markmessages();\"> <input type=\"submit\" value=\"Markierte löschen\" class=\"button\" name=\"dso\"></td></tr></table></td></tr>
	<th style=\"width: 130px;\">Datum</th><th></th><th>Aktion</th>";
	if (mysql_num_rows($result) == 0) echo "<tr><td colspan=\"4\">Keine Einträge vorhanden</td></tr>";
	else
	{
		$i = 0;
		while($data=mysql_fetch_assoc($result))
		{
			$i++;
			if ($i == 2)
			{
				$trc = " style=\"background-color: #171616\"";
				$i = 0;
			}
			echo "<tr>
				<td".$trc.">".date("d.m.",$data['date_tsp']).setyear(date("Y",$data['date_tsp'])).date(" H:i",$data['date_tsp'])."</td>
				<td".$trc.">".$data['type']."</td>
				<td".$trc.">".nl2br(stripslashes($data['text']))."</td>
				<td".$trc."><input type=\"checkbox\" name=\"dlo[]\" value=\"".$data['log_id']."\"></td>
			</tr>";
			$trc = "";
		}
	}
	echo "<tr><td colspan=\"4\"><table><tr>".$ps0.$pages.$pe0."<td><input type=\"button\" value=\"Alle markieren\" class=\"button\" onClick=\"markmessages();\"> <input type=\"submit\" value=\"Markierte löschen\" class=\"button\" name=\"dso\"></td></tr></table></td></tr>
	</table>
	</form>";

}
if ($v == "showlogcat")
{
	pageheader("/ <a href=?p=log>Logbücher</a> / <b>".strip_tags(stripslashes($log->ship['name']))." (".get_log_type($_GET['c']).")</b>");
	if (!check_int($_GET['pa']) || $_GET['pa'] < 1) $_GET['pa'] = 1;
	
	if ($_GET['a'] == "dlo" && is_array($_GET['dlo'])) $result = $log->delete_logs($_GET['id'],$_GET['dlo']);
	if ($result) meldung($result);
	
	$result = $log->load_log_by_type($_GET['c'],$_GET['pa']*50-50);
	$in = $_GET['pa'];
	$i = $in-2;
	$j = $in+2;
	$knc = $log->load_log_count_by_type($_GET['c']);
	$ceiled_knc = ceil($knc/50);
	
	$ps0 = "<td>Seite: <a href=?p=log&s=slc&id=".$log->ship['id']."&c=".$_GET['c']."&pa=1><<</a> <a href=?p=log&s=slc&id=".$log->ship['id']."&c=".$_GET['c']."&pa=".($pa == 1 ? 1 : $pa-1)."><</a></td>";
	if ($i > 1) $ps = "<td class=\"pages\"><a href=?p=log&s=slc&id=".$log->ship['id']."&c=".$_GET['c']."&pa=1>1</a></td>";
	if ($j < $ceiled_knc) $pe = "<td class=\"pages\"><a href=?p=log&s=slc&id=".$log->ship['id']."&c=".$_GET['c']."&pa=".$ceiled_knc.">".$ceiled_knc."</a></td>";
	if ($j > $ceiled_knc) $j = $ceiled_knc;
	if ($i < 1) $i = 1;
	while($i<=$j)
	{
		$pages .= "<td class=\"pages\"><a href=?p=log&s=slc&id=".$log->ship['id']."&c=".$_GET['c']."&pa=".$i.">".($i == $in ? "<div style=\"font-weight : bold; color: Yellow;\">".$i."</div>" : $i)."</a></td>";
		$i++;
	}
	$i = $in-2;
	$j = $in+2;
	$pages = $ps.($i > 2 ? "<td style=\"width: 20px; text-align: center;\">...</td>" : "").$pages.($ceiled_knc > $j+1 ? "<td style=\"width: 20px; text-align: center;\">... </td>" : "").$pe;
	$pe0 = "<td><a href=?p=log&s=slc&id=".$log->ship['id']."&c=".$_GET['c']."&pa=".($pa == $ceiled_knc ? 1 : $pa+1).">></a>&nbsp;<a href=?p=log&s=sl&id=".$log->ship['id']."&pa=".$ceiled_knc.">>></a> (".$knc." Einträge)</td>";
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1>
	<tr><th style=\"width: 100px;\">Einträge</th>
	<td class=\"pages\" style=\"width: 100px;\"><a href=?p=log&s=sl&id=".$_GET['id'].">Alle</a></td>
	<td class=\"pages\" style=\"width: 100px;\"><a href=?p=log&s=slc&c=1&id=".$_GET['id'].">Kampf</a></td>
	<td class=\"pages\" style=\"width: 100px;\"><a href=?p=log&s=slc&c=2&id=".$_GET['id'].">Anomalien</a></td>
	<td class=\"pages\" style=\"width: 100px;\"><a href=?p=log&s=slc&c=3&id=".$_GET['id'].">Sonstige</a></td>
	<td class=\"pages\" style=\"width: 100px;\"><a href=?p=log&s=slc&c=4&id=".$_GET['id'].">Privat</a></td></tr>
	</table><br>
	<form action=main.php method=post name=\"logs\"><input type=hidden name=p value=log><input type=hidden name=s value=slc><input type=hidden name=a value=dlo><input type=hidden name=id value=".$_GET['id']."><input type=hidden name=c value=".$_GET['c'].">
	<table class=\"tcal\">
	<tr><td colspan=\"3\"><table><tr>".$ps0.$pages.$pe0."<td><input type=\"button\" value=\"Alle markieren\" class=\"button\" onClick=\"markmessages();\"> <input type=\"submit\" value=\"Markierte löschen\" class=\"button\" name=\"dso\"></td></tr></table></td></tr>
	<th style=\"width: 130px;\">Datum</th><th>Aktion</th>";
	if (mysql_num_rows($result) == 0) echo "<tr><td colspan=\"3\">Keine Einträge vorhanden</td></tr>";
	else
	{
		$i = 0;
		while($data=mysql_fetch_assoc($result))
		{
			$i++;
			if ($i == 2)
			{
				$trc = " style=\"background-color: #171616\"";
				$i = 0;
			}
			echo "<tr>
				<td".$trc.">".date("d.m.",$data['date_tsp']).setyear(date("Y",$data['date_tsp'])).date(" H:i",$data['date_tsp'])."</td>
				<td".$trc.">".nl2br(stripslashes($data['text']))."</td>
				<td".$trc."><input type=\"checkbox\" name=\"dlo[]\" value=\"".$data['log_id']."\"></td>
			</tr>";
			$trc = "";
		}
	}
	echo "<tr><td colspan=\"3\"><table><tr>".$ps0.$pages.$pe0."<td><input type=\"button\" value=\"Alle markieren\" class=\"button\" onClick=\"markmessages();\"> <input type=\"submit\" value=\"Markierte löschen\" class=\"button\" name=\"dso\"></td></tr></table></td></tr>
	</table>";
}
?>