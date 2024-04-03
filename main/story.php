<?php
	ini_set(default_charset, "");

	$leftstyle = "padding-left:0px;padding-top:4px;padding-bottom:4px;padding-right:8px;margin:0px;vertical-align:top;background:none;";

	
	echo "<table style=\"border:none;border-spacing:0px;border-collapse:separate;background:none;\" width=100%>";	
	
	$text = "Wir schreiben das <span style=\"font-weight: bold; color: #ffd700;\">Jahr 2393</span>. Nach Phasen des Tumults, ausgelöst durch den Krieg mit dem Dominion und Instabilität innerhalb des Romulanischen Sternenimperiums, ist <span style=\"font-weight: bold; color: #dddddd;\">Ruhe im Alpha-Quadranten eingekehrt</span>. Doch es braut sich ein neuer Konflikt zusammen.";	
	echo "<tr><td style=\"".$leftstyle."\">".fixedPanel(1,"Zeit des Umbruchs","mimpr",$gfx."/buttons/icon/time.gif","<div style=\"padding:8px; font-size: 0.9rem;\">". $text ."</b></div>")."</td></tr>";
	
	
	$text = "15 Jahre ist es her, seit das verloren geglaube Raumschiff <span style=\"font-weight: bold; color: #66ffff;\">USS Voyager</span> aus dem Delta Quadranten zurückgekehrt ist, und mit sich unzählige Geschichten von Abenteuern in fremden Weiten brachte. Eine neue Generation wuchs auf mit einem starken <span style=\"font-weight: bold; color: #ffff66;\">Drang nach Entdeckung</span> und dem Willen, zwischen den Sternen seine Bestimmung zu finden. Doch nicht jeder hat das Zeug zum Offizier der Sternenflotte, und die Posten sind begrenzt. Kolonisten jedoch werden immer gesucht. Viele neu erschlossene, ferne Welten brauchen fähiges Personal. Bürger, die entschlossen sind, den Ruf der Ferne zu beantworten...";
	echo "<tr><td style=\"".$leftstyle."\">".fixedPanel(1,"In der vereinten Föderation der Planeten","mimpr",$gfx."/rassen/1s.gif","<div style=\"padding:8px; font-size: 0.9rem;\">". $text ."</b></div>")."</td></tr>";

	
	$text = "Durch die Machtübernahme des Klons <span style=\"font-weight: bold; color: #66ff66;\">Shinzon</span> und seiner Verbündete, bei der der komplette romulanische Senat getötet wurde, sowie sein unerwartetes Ableben auf Shinzons persönlicher Jagd nach <span style=\"font-weight: bold; color: #66ffff;\">Sternenflotten-Kapitän Jean-Luc Picard</span>, ist das Romulanische Sternenimperium immernoch geschwächt. Das Machtvakuum wurde durch zu ambitionierte Politiker ausgefüllt, die sich in endlosen Diskussionen über Kleinigkeiten verlieren. Der neue Prätor, hoffend, sein Volk durch eine gemeinsame Richtung zu einen, richtet derweil seinen Blick nach Außen: auf Expansion...";
	echo "<tr><td style=\"".$leftstyle."\">".fixedPanel(1,"Im Romulanischen Sternenimperium","mimpr",$gfx."/rassen/2s.gif","<div style=\"padding:8px; font-size: 0.9rem;\">". $text ."</b></div>")."</td></tr>";

	$text = "Es ist ruhig geworden im Klingonischen Imperium - zu ruhig. Die Geschichten über den heldenhaften <span style=\"font-weight: bold; color: #ff66ff;\">Kampf gegen das ehrlose Dominion</span> füllten einst die Hallen von Qo'noS mit dem Gelächter von Kriegern und dem Klackern von Kelchen voll Blutwein. Doch die Erinnerung verblasst, und keine neuen Heldentaten treten an ihren Platz. Es gibt keine Gegner mehr, deren Unterwerfung die Ehre klingonischer Krieger beweisen würde. <span style=\"font-weight: bold; color: #ff6666\">Keine Schlachten, die ihren Blutdurst stillen</span> würden.  Nur der Frieden, der mit der Föderation und den Romulanern besteht. Imperien, deren Macht weithin bekannt ist. Imperien, die würdige Kontrahenden darstellen würden...";
	echo "<tr><td style=\"".$leftstyle."\">".fixedPanel(1,"Im Klingonischen Imperium","mimpr",$gfx."/rassen/3s.gif","<div style=\"padding:8px; font-size: 0.9rem;\">". $text ."</b></div>")."</td></tr>";

	$text = "<div style=\"display: inline-block;\"><img src=\"https://www.st-universe.eu/gfx//starmap/actual/full.png\" width=200 height=200 style=\"float: left; padding: 8px;\">Nun richten sich alle Augen auf den <span style=\"font-weight: bold; color: #ffd700;\">Deenia-Sektor</span>. Dieser Sektor liegt im Grenzgebiet zwischen dem Alpha- und dem Beta-Quadranten unserer Galaxie. Die wenigen Erkundungsmissionen dieses Sektors berichten von zahlreichen Planeten voller Leben und wertvoller Ressourcen. Und doch ist er, abgesehen von einer dort einsässigen Spezies, den Evora, gänzlich unbewohnt. Grund hierfür ist das Abkommen von Jankata, welches die Großmächte vor langer Zeit getroffen haben, und welches ihnen ihnen untersagt, Gebietsansprüche in einem anderen Quadranten als ihrem Heimatquadranten zu erheben. Aufgrund des ungeklärten Status des Deenia-Sektors, ist auch ungeklärt, wer dort Kolonien errichten darf. <br><br>
Doch dies ändert sich nun. Unter Einbeziehung und in Absprache mit jeder größeren politischen, militärischen oder wirtschaftlichen Fraktion der Alpha- und Beta-Quadranten wird der <span style=\"font-weight: bold; color: #66ff66;\">Sektor zu einer neutralen Region</span> erklärt, in der besagte Fraktionen nur beobachtende Funktionen innehaben, sich weitgehend passiv verhalten sollen und etwaigen Kolonisten so Autonomie garantieren. Der Sektor wird damit zu einem Sammelbecken von Glücksrittern, Visionären und Suchenden, die alle nur ein Ziel haben: Eine Existenz außerhalb der von den interstellaren Großmächten gemachten Regeln.<span style=\"font-weight: bold; color: #ffd700;\"> Größtenteils unerforscht ist der Sektor voller unbekannter Gefahren, faszinierender Möglichkeiten und lukrativer Gelegenheiten, welche Siedler immer wieder vor neue Herausforderungen stellt.</span>
<br><br>
Die Großmächte bleiben derweil im Hintergrund. Unter Berufung auf Neutralität stellen sie Infrastruktur in Form von Handelsstationen zur Verfügung und zeigen limitierte Präsenz. Die Zeit wird zeigen, welche Interessen sie wirklich verfolgen...</div>";
	echo "<tr><td style=\"".$leftstyle."\">".fixedPanel(1,"Der Deenia-Sektor","mimpr",$gfx."/buttons/icon/map.gif","<div style=\"padding:8px; font-size: 0.9rem;\">". $text ."</b></div>")."</td></tr>";
	
	echo "</table>";
	
?>