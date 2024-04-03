<?php
	ini_set(default_charset, "");

	$leftstyle = "padding-left:0px;padding-top:4px;padding-bottom:4px;padding-right:8px;margin:0px;vertical-align:top;background:none;";

	$text = "<div style=\"padding:12px;\"><b>Ups. Noch nicht fertig.</b></div>";

	
	echo "<table style=\"border:none;border-spacing:0px;border-collapse:separate;background:none;\" width=100%>";	
		echo "<tr><td style=\"".$leftstyle."\">".fixedPanel(1,"ToDo","mimpr",$gfx."/buttons/icon/data.gif",$text)."</td></tr>";
	echo "</table>";
	
?>