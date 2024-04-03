<?php
	ini_set(default_charset, "");

	$leftstyle = "padding-left:0px;padding-top:4px;padding-bottom:8px;padding-right:8px;margin:0px;vertical-align:top;background:none;";

	$result = $main->getlatestnews();
	$i = 0;
	while($ne=mysql_fetch_assoc($result))
	{
		$news[$i] = "<b>".date("d.m.Y H:i",$ne['date_tsp'])." - ".utf8_encode(stripslashes($ne['subject']))."</b><br>".utf8_encode(nl2br(stripslashes($ne[text]))).($ne[refs] ? "<br><br><font color=gray>Links</font><br>".nl2br($ne[refs]) : "")."";
		
		$title = date("d.m.Y H:i",$ne['date_tsp'])." - <font color=#ffffff>".utf8_encode(stripslashes($ne['subject']))."</font>";
		$text = "<div style=\"padding:4px;\">".utf8_encode(nl2br(stripslashes($ne[text])))."</div>";
		
		$news[$i] = coloredSimplePanel($ne['color'],$title,"mwelcome",$gfx."/buttons/icon/".$ne['pic'].".gif",$text);
		$i++;
	}
	$welcometext = "<div style=\"padding:0px;\"><table><tr><td><img src=gfx/bev/crew/1m.png border=0><img src=gfx/bev/crew/2m.png border=0><img src=gfx/bev/crew/3m.png border=0></td>
	<td style=\"padding:4px;\">Star Trek Universe ist ein <b>kostenloses, rundenbasierendes Onlinestrategiespiel</b>, in dem Du die Kontrolle über Siedler einer von drei Großmächten (Föderation, Klingonen oder Romulaner) übernimmst. <br><br>
	<span style=\"font-weight: bold; color: #66ff66;\">Kolonisiere</span> neue Welten, <span style=\"font-weight: bold; color: #ff6666;\">kämpfe</span> erbitterte Schlachten gegen Deine Feinde, <span style=\"font-weight: bold; color: #66ffff;\">erforsche</span> die Weiten eines fremden Sektors oder lehne dich einfach zurück und genieße in Ruhe die <span style=\"font-weight: bold; color: #ffd700;\">Stories</span>, die andere Spieler veröffentlichen.</tr></table></div>";

	echo "<table style=\"border:none;border-spacing:0px;border-collapse:separate;background:none;\" width=100%>";	
		echo "<tr><td style=\"".$leftstyle."\">".fixedPanel(1,"Willkommen bei Star Trek Universe!","mwelcome",$gfx."/buttons/icon/beam.gif",$welcometext)."</td></tr>";
		echo "<tr><td style=\"".$leftstyle."\">".$news[0]."</td></tr>";
		echo "<tr><td style=\"".$leftstyle."\">".$news[1]."</td></tr>";
		echo "<tr><td style=\"".$leftstyle."\">".$news[2]."</td></tr>";
	echo "</table>";
	
?>