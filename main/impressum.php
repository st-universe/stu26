<?php
ini_set(default_charset, "");

$leftstyle = "padding-left:0px;padding-top:4px;padding-bottom:4px;padding-right:8px;margin:0px;vertical-align:top;background:none;";

$text = "<div style=\"padding:4px;\"><b>Verantwortlicher für den Inhalt dieser Seiten</b><br><br>mail(at)domain(.)de<br><br>Dieses Spiel ist nicht profitorientiert - hat also keinerlei Einkünfte durch Werbung oder bezahlte Accounts. Bitte dazu auch den Copyrighthinweis beachten.</div>";


echo "<table style=\"border:none;border-spacing:0px;border-collapse:separate;background:none;\" width=100%>";
echo "<tr><td style=\"" . $leftstyle . "\">" . fixedPanel(1, "Impressum", "mimpr", $gfx . "/buttons/icon/data.gif", $text) . "</td></tr>";
echo "</table>";