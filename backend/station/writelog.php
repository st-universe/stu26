<?php
header("Content-Type: text/html; charset=iso-8859-1");
header("Content-Type: text/html; charset=iso-8859-1");
@session_start();

if ($_SESSION['login'] != 1) exit;

echo "<form action=main.php method=post><input type=hidden name=p value=stat><input type=hidden name=s value=ss>
<input type=hidden name=a value=wlo><input type=hidden name=id value=".$_GET['id'].">
<table class=\"tcal\" style=\"border: 1px groove #8897cf;\">
<th colspan=\"2\" onMouseOver=\"switch_drag_on();\" onMouseOut=\"switch_drag_off();\">Logbucheintrag schreiben</th>
<tr><td><textarea cols=70 rows=20 name=tx></textarea></td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td><input type=\"submit\" value=\"Senden\" class=\"button\"> <input type=\"button\" value=\"Schließen\" class=\"button\" onClick=\"cClick();\"></td></tr>
</table>
</form>";
?>