<?php
if (!is_object($db)) exit;
include_once("class/nagus.class.php");
$t = new nagus;
include_once("inc/lists/goods.php");

switch ($_GET['s'])
{
	default:
		$v = "mainpage";
	// case "ma":
		// $v = "mainpage";
		// break;
	// case "sgc":
		// $v = "showgoodcat";
		// if (!check_int($_GET['id']))
		// {
			// $v = "mainpage";
			// break;
		// }
		// $good = $db->query("SELECT name FROM stu_goods WHERE view='1' AND goods_id<200 AND goods_id=".$_GET['id'],1);
		// if ($good === 0) $v = "mainpage";
		// break;
	// case "sc":
		// if ($_GET['cat'] == 1) $v = "showcat1";
		// if ($_GET['cat'] == 2) $v = "showcat2";
		// if ($_GET['cat'] == 3) $v = "showcat3";
		// if ($_GET['cat'] == 4) $v = "showcat4";
		// break;
	// case "smt":
		// $v = "showmodcat";
		// if (!check_int($_GET['id'])) $v = "mainpage";
		// if ($_GET['id'] < 1 || $_GET['id'] > 11) $v = "mainpage";
		// break;
	// case "sl":
		// $v = "showlast";
		// break;
}
if (!$_GET['pa'] || !check_int($_GET['pa']) || $_GET['pa'] == 0) $_GET['pa'] = 1;

echo "
<style>
td.pages {
	text-align: center;
	width: 20px;
	border: 1px groove #8897cf;
}
td.pages:hover
{
	background: #262323;
}
div.quote {
    width: 800px;
	padding: 10px;
text-align: justify;
}
div.attribution {
    font-style: oblique;
    font-weight: bold;
    text-align: right;
	width: 800px;
}
</style>";

include_once("inc/lists/goods.php");

if ($v == "mainpage")
{

	echo "<script language=\"Javascript\">
	var elt;
	function get_window(elt,width)
	{
		return overlib('<div id=rinfo></div>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER, STICKY, EXCLUSIVE, DRAGGABLE, ALTCUT, RELX, 400, RELY, 70, WIDTH, width);
	}
	function getrinfo(rid,fg)
	{	
		elt = fg;
		get_window(elt,422);
		sendRequest('backend/rinfo_noscroll.php?PHPSESSID=".session_id()."&rid=' + rid);
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
	
	pageheader("/ <a href=?p=trade>Warenbörse</a> / <b>Deals des Großen Nagus</b>");
	
	if ($_GET['a'] == "takeoffer" && check_int($_GET['id']) && $_GET['id'] > 0) $result = $t->takeoffer($_GET['id']);
	
	if ($result) meldung($result);
	$t->getofferlist();
	
	echo "<table width=1000px>";
	echo "<tr><td width=800px>";
	echo "<div class=\"quote\">\"Willkommen, geschätzer Kunde! Mir ist zu Ohren gekommen, dass dieser Sektor über große Reichtümer verfügt. Und meine Ohren täuschen sich nie. Ich habe daher besondere Angebote speziell für Sie vorbereitet. Die hier angebotenen Schiffsdesigns wurden von fähigen Ferengi-Ingenieuren modifiziert, um Ihnen die Möglichkeit zu bieten, eigene Schiffsbauteile zu verwenden. Sie machen daher garantiert ein gutes Geschäft. Auf gute Profite!\"</div>";
	echo "<div class=\"attribution\"> Der Große Nagus Rom</div>";
	echo "</td>";
	echo "<td width=200px><img src=".$gfx."/rom.jpg title=\"\"></td></tr></table>";
	
	if (mysql_num_rows($t->result) == 0) meldung("Keine Angebote vorhanden");
	else
	{

		
		echo "<br><br><table bgcolor=#262323 cellspacing=1 cellpadding=1 width=100%>
		<tr>
			<th width=800 colspan=3>Angebot</th>
			<th width=300>Restriktionen</th>
			<th width=100></th>
			<th>verlangt</th>
		</tr>";		
		while($data=mysql_fetch_assoc($t->result))
		{
			$i++;
			$k++;
			if ($k == 2)
			{
				$trc = " style=\"background-color: #171616\"";
				$k = 0;
			}
			else $trc = "";		
		
		
			echo "<form action=main.php method=post>
				<input type=hidden name=p 	value=nagus>
				<input type=hidden name=a 	value=takeoffer>
				<input type=hidden name=id 	value=".$data['rumps_id'].">
			<tr height=60>
			<td width=200>&nbsp;<img src=".$gfx."/ships/".$data['rumps_id'].".gif title=\"\"></td>
			<td".$trc." width=500>&nbsp;Konstruktionspläne: ".stripslashes($data['name'])."</td>
			<td".$trc." width=100><center><a href=\"javascript:void(0)\" onClick=\"getrinfo(".$data['rumps_id'].",'rinfo');\" onmouseover=cp('scs".$i."','buttons/lupe2') onmouseout=cp('scs".$i."','buttons/lupe1')><img src=gfx//buttons/lupe1.gif border=0 name=scs".$i." title=\"Werte\"> Werte</a></center></td>"	;			
			
			if ($data['race'] == 0) echo "<td".$trc."></td>";
			if ($data['race'] == 1) echo "<td".$trc.">&nbsp;<img src=".$gfx."/rassen/1s.gif title=\"\"> Föderation</td>";
			if ($data['race'] == 2) echo "<td".$trc.">&nbsp;<img src=".$gfx."/rassen/2s.gif title=\"\"> Romulaner</td>";
			if ($data['race'] == 3) echo "<td".$trc.">&nbsp;<img src=".$gfx."/rassen/3s.gif title=\"\"> Klingonen</td>";
			if ($data['race'] == 4) echo "<td".$trc.">&nbsp;<img src=".$gfx."/rassen/4s.gif title=\"\"> Cardassianer</td>";
			if ($data['race'] == 5) echo "<td".$trc.">&nbsp;<img src=".$gfx."/rassen/5s.gif title=\"\"> Ferengi</td>";
			if ($data['race'] == 6) echo "<td".$trc.">&nbsp;<img src=".$gfx."/rassen/6s.gif title=\"\"> Gorn</td>";

			echo "<td".$trc."><center><input type=submit class=button value=Annehmen></center>";
			echo "</td>
			<td".$trc.">&nbsp;<img src=".$gfx."/goods/8.gif title=\"Dilithium\"> ".$data['cost']."</td>
			</tr></form>";
		}
		
		
		
		
		echo "</table>";
		
		
		
		
		
		
	}
	
	
	
	
	
	
	
	
	
	
	

}
?>