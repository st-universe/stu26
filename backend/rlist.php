<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../inc/func.inc.php");
include_once("../inc/config.inc.php");
include_once("../class/db.class.php");

@session_start();

if (!check_int($_GET[off]) || $_SESSION["login"] != 1) exit;
$res = $_GET[off];

$db = new db;

$gfx = $_SESSION[gfx_path];
if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "../gfx/";

$result = $db->query("SELECT rumps_id,name FROM stu_rumps WHERE npc!='1' AND is_shuttle='0' ORDER BY sort,rumps_id LIMIT ".$res.",4");

$max = $db->query("SELECT rumps_id,name FROM stu_rumps WHERE npc!='1' AND is_shuttle='0' ORDER BY sort,rumps_id",3);

echo "<style>td.kd:hover { background: #262323; }</style><table class=tcal><tr>
<td width=\"10\" height=\"95\">".($res-4 < 0 ? "&nbsp;" : "<a href=\"javascript:void(0);\" onClick=\"setpos(".($res-4).");\" ".getonm('srb','buttons/b_from')."><img src=".$gfx."/buttons/b_from1.gif name=srb border=0 title=\"Zurück\"></a>")."</td>";
while($da=mysql_fetch_assoc($result)) echo "<td align=center width=100 class=\"kd\"><a href=\"javascript:void(0);\" onClick=\"loadinfo($da[rumps_id],'ri');\"><img src=".$gfx."/ships/".$da[rumps_id].".gif border=0><br>".stripslashes($da[name])."</a></td>";
echo "<td width=\"10\">".($res+4 > $max ? "&nbsp;" : "<a href=\"javascript:void(0);\" onclick=\"setpos(".($res+4).");\" ".getonm('srf','buttons/b_to')."><img src=".$gfx."/buttons/b_to1.gif name=srf border=0 title=\"Vorwärts\"></a>")."</td></tr></table>";
?>