<?php
if (!$db || !check_int($_GET[user]) || !is_string($_GET[actcode]) || strlen($_GET[actcode]) != 10) die();
$main->activateuser($_GET[user],$_GET[actcode]);
echo "<table width=100% bgcolor=#262323 cellspacing=1 cellpadding=1><tr><th>/ <a href=?p>Hauptseite</a> / <b>Aktivierung</b></th></tr></table><br>
<table class=\"tcal\">
<tr><td>Aktivierung erfolgreich - Du kannst Dich jetzt einloggen.<br><br>
Viel Spaﬂ in Star Trek Universe</td></tr>
</table>";
?>