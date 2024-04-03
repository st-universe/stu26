<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../inc/func.inc.php");
include_once("../inc/config.inc.php");
include_once("../class/db.class.php");

@session_start();

if (!check_int($_GET[id]) || !check_int($_GET[vfeld]) ||  !check_int($_GET[fid]) || !check_int($_GET[zfeld]) || $_SESSION["login"] != 1) exit;

$db = new db;

$gfx = $_SESSION[gfx_path];
if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "../gfx/";

$tf = $db->query("SELECT terraforming_id,name,ecost,flimit,t_time FROM stu_terraforming WHERE v_feld=".$_GET[vfeld]." AND z_feld=".$_GET[zfeld],4);
if ($tf == 0) exit;
$tfc = $db->query("SELECT a.goods_id,a.count,b.name,c.count as vcount FROM stu_terraforming_cost as a LEFT JOIN stu_goods as b ON a.goods_id=b.goods_id LEFT JOIN stu_colonies_storage as c ON c.goods_id=a.goods_id AND c.colonies_id=".$_GET['id']." WHERE a.terraforming_id=".$tf[terraforming_id]." ORDER BY b.sort");
$col_eps = $db->query("SELECT eps FROM stu_colonies WHERE id=".$_GET[id],1);

echo "<form action=main.php method=get>
<input type=hidden name=p value=colony><input type=hidden name=s value=sc>
<input type=hidden name=id value=".$_GET[id]."><input type=hidden name=a value=trf>
<input type=hidden name=tofid value=".$_GET[zfeld]."><input type=hidden name=fid value=".$_GET[fid].">
<input type=hidden name=ps value=".$_SESSION['pagesess'].">
<table class=tcal  style=\"border: 1px groove #8897cf;\">
<tr>
	<th>".$tf[name]."</th>
</tr>
<tr>
	<td align=center><img src=".$gfx."/fields/".$_GET[vfeld].".gif> => <img src=".$gfx."/fields/".$_GET[zfeld].".gif></td>
</tr>
<tr>
	<td><u>Kosten</u><br><img src=".$gfx."/buttons/e_trans2.gif title='Energie'> ".$tf[ecost]." / ".($tf[ecost] > $col_eps ? "<font color=#ff0000>".$col_eps."</font>" : $col_eps)."<br>";
	while($data=mysql_fetch_assoc($tfc)) echo "<img src=".$gfx."/goods/".$data[goods_id].".gif title='".$data[name]."'> ".$data['count']." / ".(!$data['vcount'] ? "<font color=#ff0000>0</font>" : ($data['vcount'] < $data['count'] ? "<font color=#ff0000>".$data['vcount']."</font>" : $data['vcount']))."<br>";
	echo "</td>
</tr>
<tr>
	<td><img src=".$gfx."/buttons/time.gif title=\"Dauer\"> ".gen_time($tf[t_time]).($tf[flimit] > 0 ? "<br>Limit: ".$tf[flimit] : "")."</td>
</tr>
<tr>
	<td align=center><input type=submit class=button value=Terraforming>&nbsp;&nbsp;&nbsp;<input type=button class=button onClick=\"cClick();\" value=Schließen></td>
</tr>
</form>
</table>";
?>
