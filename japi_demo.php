<?php
// Variablen
$ac_key = "Tr_b4oEfhD0nmVjoJ_qqcv-mv6pB2gfrNNyFkh1aiE7BdJSiiKO9Wvod6rbBo8PoBFWsgQ7ck21_AL8shbCYLCIRNv_eMo0rZ5Zm6eySP7TiyV8t-073ni4EQbm2-EDHo9VQj_Pcg7J_q-hT_dMLqozLYnVBk3ODH5GlIQGMt88.";
$ac_url = "http://www.stuniverse.de/japi.php?action=";

// Wir holen uns die Daten mit file()
$ress = @file($ac_url.$ac_key);

// Fehler abfangen
if ($ress == 0 || !$ress) echo "Keine Daten vorhanden";
else
{
	/* Die eigentliche Anzeige. Die Userinfo enthält folgendes Daten
	$ress[0] = SiederID
	$ress[1] = Siedlername
	$ress[2] = Timestamp der letzten Aktion
	$ress[3] = Name der Allianz (wenn vorhanden)
	*/
	
	echo "Ich bin Siedler ".$ress[1]." mit der ID ".$ress[0]."<br>";
	echo "Zuletzt war ich am ".date("d.m.Y H:i",$ress[2])." online<br>";
	if ($ress[3]) echo "Ich bin in der Allianz ".$ress[3];
}
?>