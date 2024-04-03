<?php
if (!$db) die();
ini_set(default_charset, "");
if ($db->query("SELECT COUNT(id) FROM stu_user WHERE id>100",1) >= 1000)
{
	meldung("Zur Zeit sind leider nur maximal 1000 Accounts möglich.");
}
else
	{
	if ($_GET["sb"] == "Neu starten")
	{
		unset($_SESSION["ud"]);
		unset($_SESSION[step]);
	}
	
	$leftstyle = "padding-left:0px;padding-top:4px;padding-bottom:4px;padding-right:8px;margin:0px;vertical-align:top;background:none;";
	
	if ($_SESSION[step] == 1 && is_array($_GET["ud"]))
	{
		if (!$_GET[ud][login] || strlen($_GET[ud][login]) < 5 || !preg_match('=^[a-zA-Z0-9]+$=i', $_GET[ud][login])) $error[login] = "Der Loginname muss aus mindestens 5 Buchstaben/Zahlen bestehen";
		if ($_GET[ud][login] && preg_match('=^[a-zA-Z0-9]+$=i', $_GET[ud][login]) && strlen($_GET[ud][login]) >= 5 && $db->query("SELECT id FROM stu_user WHERE login='".$_GET[ud][login]."'",1) != 0) $error[login] = "Dieser Loginname wird bereits verwendet";
		if (!$_GET[ud][name] || strlen($_GET[ud][name]) < 6 || !preg_match('=^[a-zA-Z0-9]+$=i', $_GET[ud][name])) $error[name] = "Der Spielername muss aus mindestens 6 Buchstaben/Zahlen bestehen";
		if (!$_GET[ud][email] || !eregi("^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,4}$",$_GET[ud][email])) $error[email] = "Diese Emailadresse ist nicht gültig";
		if (!$_GET[ud][email2] || !eregi("^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,4}$",$_GET[ud][email2])) $error[email2] = "Diese Emailadresse ist nicht gültig";
		if ($_GET[ud][email] && eregi("^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,4}$",$_GET[ud][email]) && $db->query("SELECT id FROM stu_user WHERE email='".$_GET[ud][email]."'",1) != 0) $error[email] = "Diese Email-Adresse wird bereits verwendet";
		if ($_GET[ud][email] != $_GET[ud][email2] && $_GET[ud][email] && $_GET[ud][email2]) $error[email2] = "Die Emailadressen stimmen nicht übererin";
		if (!$_GET[ud][pwd] || strlen($_GET[ud][pwd]) < 5 || !preg_match('=^[a-zA-Z0-9]+$=i', $_GET[ud][pwd])) $error[pwd] = "Das Passwort muss aus mindestens 5 Buchstaben/Zahlen bestehen";
		if ($_GET[ud][pwd] != $_GET[ud][pwd2]) $error[pwd] = "Die Passwörter stimmen nicht überein";
		if (!is_array($error)) $_SESSION["ud"] = $_GET["ud"];
	}
	if ($_SESSION[step] == 2)
	{
		if ($_GET[ar] != 1) $error[rules] = "Du musst die Regeln akzpetieren um die Registrierung fortzusetzen";
		if ($_GET[faction] < 1 || $_GET[faction] > 3) $error[faction] = "Die gewählte Rasse ist ungültig";
		if (!is_array($error)) $_SESSION["ud"][faction] = $_GET[faction];
	}
	
	// Nächsten Schritt wählen
	if (!$_SESSION[step] || $_SESSION[step] == "") $_SESSION[step] = 1;
	elseif ($_SESSION[step] == 1 && !is_array($error) && is_array($_SESSION["ud"])) $_SESSION[step] = 2;
	elseif ($_SESSION[step] == 2 && !is_array($error) && is_array($_SESSION["ud"])) $_SESSION[step] = 3;
	elseif ($_SESSION[step] == 3 && is_array($_SESSION["ud"]) && $_GET[s] == "anm") $_SESSION[step] = 4;
	
	
	if ($_SESSION[step] == 1)
	{
		$content .= "<form action=index.php method=get><input type=hidden name=p value=reg><input type=hidden name=s value=anm>
		<table class=\"tcal\">
		<tr>
			<td colspan=2>Bitte mache Dich vor der Registrierung mit den <a href=index.php?p=rules target=_blank>Regeln</a> und der <a href=index.php?p=story target=_blank>Story</a> in STU vertraut.<br>Die Registrierung erfolgt in 3 kurzen Schritten.<br><br>
			<u><b>Schritt 1</b></u><br><br></td></tr>
			<tr><td colspan=2>Der Loginname ist der Name, mit dem du dich in das Spiel einloggst</td></tr>
			<tr><td width=200>Loginname</td><td><input type=text class=text size=15 name=ud[login] value=\"".(is_string($_GET["ud"][login]) && !$error[login] ? $_GET["ud"][login] : "")."\"></td></tr>";
			if ($error[login]) $content .= "<tr><td colspan=2><font color=#FF0000>".$error[login]."</font></td></tr>";
			$content .= "<tr><td colspan=2>Der Spielername ist der Name, mit dem du dich im Spiel identifizierst (z.B. in privaten Nachrichten)*</td></tr>
			<tr><td>Spielername</td><td><input type=text class=text size=20 name=ud[name] value=\"".(is_string($_GET["ud"][name]) && !$error[name] ? $_GET["ud"][name] : "")."\"></td></tr>";
			if ($error[name]) $content .= "<tr><td colspan=2><font color=#FF0000>".$error[name]."</font></td></tr>";
			$content .= "<tr><td colspan=2>Die Emailadresse wird benötigt um dir den Aktivierungslink zu schicken.</td></tr>
			<tr><td>Email-Adresse</td><td><input type=text class=text size=20 name=ud[email] value=\"".(is_string($_GET["ud"][email]) && !$error[email] ? $_GET["ud"][email] : "")."\"></td></tr>";
			if ($error[email]) $content .= "<tr><td colspan=2><font color=#FF0000>".$error[email]."</font></td></tr>";
			$content .= "<tr><td>Email wiederholen</td><td><input type=text class=text size=20 name=ud[email2] value=\"".(is_string($_GET["ud"][email2]) && !$error[email2] ? $_GET["ud"][email2] : "")."\"></td></tr>";
			if ($error[email2]) $content .= "<tr><td colspan=2><font color=#FF0000>".$error[email2]."</font></td></tr>";
			$content .= "<tr><td colspan=2>Dein Passwort um dich einzuloggen*</td></tr>
			<tr><td>Passwort</td><td><input type=password class=text size=10 name=ud[pwd]></td></tr>";
			if ($error[pwd]) $content .= "<tr><td colspan=2><font color=#FF0000>".$error[pwd]."</font></td></tr>";
			$content .= "<tr><td>Passwort wiederholen</td><td><input type=password class=text size=10 name=ud[pwd2]></td></tr>";
			if ($error[pwd2]) $content .= "<tr><td colspan=2><font color=#FF0000>".$error[pwd2]."</font></td></tr>";
			$content .= "<tr><td colspan=2 align=right><br><input type=submit value=\"Schritt 2 &gt;&gt;\" class=button></td></tr><tr><td colspan=2>* Spielername und Passwort können später geändert werden</td></tr>	
		</table>
		</form>";
		
		
		$content = "<div style=\"padding:4px;\">".$content."</div>";
		
		echo "<table style=\"border:none;border-spacing:0px;border-collapse:separate;background:none;\" width=100%>";	
			echo "<tr><td style=\"".$leftstyle."\">".fixedPanel(1,"Registrierung - Schritt 1","mregi",$gfx."/buttons/icon/data.gif",$content)."</td></tr>";
		echo "</table>";
	}
	if ($_SESSION[step] == 2)
	{
		$content .= "<script language=\"Javascript\">
		{
			function selchg()
			{
				fac = document.forms[0].faction.value;
				if (fac == 0) tx = \"Bitte wähle eine Rasse\";
				if (fac == 1) tx = \"<table  width=100% class=tcal><tr><td width=64><center><img src=gfx/rassen/1kn.png></center></td><td style='padding-left: 10px'>Die Vereinte Föderation der Planeten wurde Mitte des 22. Jahrhunderts gegründet. Sie basiert auf dem friedlichen Zusammenleben ihrer Mitgliedsvölker und widmet sich der Erforschung und der Erhaltung des Friedens in der Galaxie.<br><br>Da nun die Föderation die Typhon-Ausdehnung für Siedler geöffnet hat, ziehen zahllose Föderationsangehörige aus, teilweise um die Ideale der Föderation zu verbreiten und den Frieden zwischen den verfeindeten Mächten aufrecht zu erhalten, teilweise aber auch, um ihre eigenen Ziele in den unerforschten Weiten der Typhon-Ausdehnung zu verwirklichen.</td></tr><tr><td width=64><center><img src=gfx/ships/5101.gif></center></td><td style='padding-left: 10px'>Die Raumschiffe der Föderation zeichnen sich durch Ausgewogenheit aus. Sie besitzen meist gleich starke Hülle und Schilde, können jedoch nicht mit Tarnvorrichtungen ausgerüstet werden.</td></tr><tr><td width=64><center><img src=gfx/goods/5102.gif><img src=gfx/goods/5103.gif><img src=gfx/goods/5104.gif></center></td><td style='padding-left: 10px'>Föderationssiedler können verbesserte Warpkerne bauen. Diese sind die besten verfügbaren Schiffsreaktoren, und können von keiner anderen Rasse gebaut werden.</td></tr><tr><td width=64><center><img src=gfx/goods/6104.gif></center></td><td style='padding-left: 10px'>Die Standardwaffe der Föderation ist der Phaser. Der eingesetzte Phasertyp ist die genauste verfügbare Waffe, richtet aber bei Treffer am wenigsten Schaden an.</td></tr><tr><td width=64><center><img src=gfx/buildings/61/1.gif></td><td style='padding-left: 10px'>Mit Rekristallisierenden Warpkernen können Föderationssiedler ihre Kolonien ohne Dilithiumkosten mit Energie versorgen.</td></tr><tr><td width=64><center><img src=gfx/bev/crew_1.gif></center></td><td style='padding-left: 10px'>Siedler der Föderation können nur an einer Station der Föderation starten.</td></tr></table>\";
				if (fac == 2) tx = \"<table  width=500 class=tcal><tr><td width=64><center><img src=gfx/rassen/2kn.png></center></td><td style='padding-left: 10px'>Die Romulaner sind Abkömmlinge der Vulkanier, die Vulkan vor knapp 2000 Jahren verlassen haben, um sich auf den Zwillingsplaneten Romulus und Remus niederzulassen und nach ihren eigenen Vorstellungen zu leben. Inzwischen ist aus den damaligen Siedlern ein Imperium geworden, das im ganzen Quadranten berüchtigt ist.<br><br>Romulanische Siedler ziehen nun in die freigegebene Typhon-Ausdehnung aus, um die Region auszukundschaften, Ressourcen zu erschließen und dem Imperium zu dienen. Einige haben allerdings auch Gründe, über die nicht laut gesprochen wird...</td></tr><tr><td width=64><center><img src=gfx/ships/5102.gif></center></td><td style='padding-left: 10px'>Die Raumschiffe der Romulaner verfügen über stärkere Schilde, aber schwächere Hüllenpanzerung. Die meisten romulanischen Schiffstypen können mit Tarnvorrichtungen ausgerüstet werden.</td></tr><tr><td width=64><center><img src=gfx/goods/3102.gif><img src=gfx/goods/3103.gif><img src=gfx/goods/3104.gif></center></td><td style='padding-left: 10px'>Romulanische Computer sind denen der anderen Rassen überlegen. Nur Romulaner können die besten Computer herstellen.</td></tr><tr><td width=64><center><img src=gfx/goods/6204.gif></center></td><td style='padding-left: 10px'>Romulaner verwenden bevorzugt Disruptoren. Der romulanische Disruptor richtet erreicht gegenüber vergleichbaren Waffentypene eine hohe Genauigkeit, büßt aber dafür etwas an Schaden ein.</td></tr><tr><td width=64><center><img src=gfx/buildings/62/1.gif></td><td style='padding-left: 10px'>Gravitische Turbinen beziehen Energie direkt aus dem Gravitationsfeld eines Planeten oder Mondes. Sie liefern damit saubere, zuverlässliche Energie für Kolonien. Aufgrund neuster technischer Entwicklungen ist diese Energie unabhängig von der Stärke des Gravitationsfeldes, kann aber nur auf Planeten gebaut werden.</td></tr><tr><td width=64><center><img src=gfx/bev/crew_2.gif></center></td><td style='padding-left: 10px'>Siedler der Romulaner können nur an einer Station des Romulanischen Sternenimperiums starten.</td></tr></table>\";
				if (fac == 3) tx = \"<table  width=500 class=tcal><tr><td width=64><center><img src=gfx/rassen/3kn.png></center></td><td style='padding-left: 10px'>Die Klingonen sind berüchtigte Krieger, die stets auf der Suche nach Kämpfen sind, um Ehre zu gewinnen.<br><br>Der Rausch des Kampfes und die Verheißung von zu verdienender Ehre ist es, was viele Klingonen in die Typhon-Ausdehnung führt. Es gilt jedoch auch zu befürchten, dass neu siedelnde Häuser des Klingonischen Imperiums in einen nahenden Bürgerkrieg gezogen werden könnten, wenn der Kampf gegen einen ehrenvollen Gegner ausbleibt...</td></tr><tr><td width=64><center><img src=gfx/ships/5103.gif></center></td><td style='padding-left: 10px'>Die Raumschiffe der Klingonen verfügen über starke Hüllenpanzerung, dafür musste aber Raum für Schilde geopfert werden. Viele der klingonischen Schiffe können mit Tarnvorrichtungen ausgerüstet werden.</td></tr><tr><td width=64><center><img src=gfx/goods/7102.gif><img src=gfx/goods/7103.gif><img src=gfx/goods/7104.gif></center></td><td style='padding-left: 10px'>Klingonen schätzen die Wendigkeit ihrer Schiffe im Kampf. Daher können Klingonische Siedler als einzige die besten Impulsantriebe produzieren.</td></tr><tr><td width=64><center><img src=gfx/goods/6304.gif></center></td><td style='padding-left: 10px'>Klingonische Disruptoren richten verheerende Schäden an. Keine andere baubare Energiewaffe erreicht ihr Schadenspotential. Leider erreicht auch keine andere ihre Ungenauigkeit und Unberechenbarkeit...</td></tr><tr><td width=64><center><img src=gfx/buildings/63/1.gif></td><td style='padding-left: 10px'>Die enormen Fusionsreaktoren der Klingonen erzeugen ein hohes Maß an Energie bei enormem Verbrauch an Deuterium.</td></tr><tr><td width=64><center><img src=gfx/bev/crew_3.gif></center></td><td style='padding-left: 10px'>Siedler der Klingonen können nur an einer Station des Klingonischen Imperiums starten.</td></tr></table>\";
				if (fac == 4) tx = \"<table  width=500 class=tcal><tr><td width=64><center><img src=gfx/rassen/4kn.png></center></td><td style='padding-left: 10px'>Die Cardassianische Union ist seit langem auf der Suche nach neuen Rohstoffquellen. Seit die Vorkommen auf der Cardassianischen Heimatwelt erschöpft ist, haben sich die Cardassianer von einem Volk der Denker zu einem militaristischen Staat entwickelt, der die Suche nach Ressourcen rücksichtslos vorantriebt.<br><br>Der Dominionkrieg hat Cardassia hart getroffen, und schwer beschädigt, sowohl in der Infrastruktur als auch in der Moral. Jetzt, da neue Rohstoffe in den reichen Systemen der Typhon-Ausdehnung zur Verfügung stehen, machen sich Cardassianer auf, um ihrer Union zu neuer Stärke zu verhelfen.</td></tr><tr><td width=64><center><img src=gfx/ships/1804.gif></center></td><td style='padding-left: 10px'>Die Raumschiffe der Cardassianer besitzen ein ausgewogenes Verhältnis von Hülle und Schilden. Ihre Designs verwenden keine Tarnvorrichtungen und sind nicht mit ihnen ausrüstbar.</td></tr><tr><td width=64><center><img src=gfx/goods/4102.gif><img src=gfx/goods/4103.gif><img src=gfx/goods/4104.gif></center></td><td style='padding-left: 10px'>Cardassianische Sensoren sind die besten baubaren Sensorenphalanxen. Keine andere Rasse erreicht ihre Sensorreichweite.</td></tr><tr><td width=64><center><img src=gfx/goods/6404.gif></center></td><td style='padding-left: 10px'>Cardassianer verwenden Phaserwaffen. Ihre Funktionsweise unterscheidet sich dabei leicht von den Phasern der Föderation. Diese Waffen verursachen hohen Schaden bei geringer Genauigkeit.</td></tr><tr><td width=64><center><img src=gfx/buildings/64/7.gif></td><td style='padding-left: 10px'>Mit dem Solarfokus steht Cardassianischen Siedlern die effizienteste Solargestützte Energiequelle zur Verfügung.</td></tr><tr><td width=64><center><img src=gfx/bev/crew_4.gif></center></td><td style='padding-left: 10px'>Siedler der Cardassianer können nur an einer Station der Cardassianischen Union starten.</td></tr></table>\";
				if (fac == 5) tx = \"<table  width=500 class=tcal><tr><td width=64><center><img src=gfx/rassen/5kn.png></center></td><td style='padding-left: 10px'>Die Ferengi sind ein Volk von Händlern und Gaunern, die nur nach Profiten und weltlichen Gewinnen streben.<br><br>Mit der Besiedlung der Typhon-Ausdehnung versammeln sich viele verschiedene Völker an einem Ort, ideale Bedingungen für Handel und Ausbeutung der unzähligen anderen Siedler. Verständlich also, dass viele Profit-witternde Ferengi bereits auf dem Weg sind, ihre eigenen Finanzimperien aufzubauen...</td></tr><tr><td width=64><center><img src=gfx/ships/1805.gif></center></td><td style='padding-left: 10px'>Ferengi-Raumschiffe besitzen eine niedrigere Hüllenstärke, machen dies aber durch stärkere Schilde wieder wett. Ferengi können keine Tarnvorrichtungen benutzen.</td></tr><tr><td width=64><center><img src=gfx/goods/8102.gif><img src=gfx/goods/8103.gif><img src=gfx/goods/8104.gif></center></td><td style='padding-left: 10px'>Die EPS-Leitungen von Ferengispielern sind unübertroffen. Nur Ferengi können diese Module herstellen, die damit einem Schiff den größtmöglichen Energiespeicher bereitstellen.</td></tr><tr><td width=64><center><img src=gfx/goods/6504.gif></center></td><td style='padding-left: 10px'>Ferengi benutzen bevorzugt Plasmaphaser. Die Ferengi-Version der Plasmawaffe verursacht geringere Schäden bei höherer Trefferchance.</td></tr><tr><td width=64><center><img src=gfx/buildings/65/6.gif></td><td style='padding-left: 10px'>Ladungssammler ermöglichen die Energieproduktion bei arktischen Temperaturen.</td></tr><tr><td width=64><center><img src=gfx/bev/crew_5.gif></center></td><td style='padding-left: 10px'>Siedler der Ferengi können nur an einer Station der Ferengi-Allianz starten.</td></tr></table>\";				
				if (fac == 6) tx = \"<table  width=500 class=tcal><tr><td width=64><center><img src=gfx/rassen/6kn.png></center></td><td style='padding-left: 10px'>Die reptiloiden Gorn zeigen erst kürzlich Interesse an den reichen Rohstoffen der Typhon-Ausdehnung. Ihre aggressive Haltung gegenüber anderen Rassen in Verbindung mit ihrer Stärke und Zähigkeit wird sicher dafür sorgen, dass sie gegen ihre Konkurrenten bestehen können.</td></tr><tr><td width=64><center><img src=gfx/ships/1806.gif></center></td><td style='padding-left: 10px'>Gorn-Raumschiffe wirken klobig und stabil - und das sind sie auch. Die Schiffe der Gorn besitzen verstärkte Hüllen, es wurde jedoch an der Schildstärke gespart. Gorn können keine Tarnvorrichtungen benutzen.</td></tr><tr><td width=64><center><img src=gfx/goods/1102.gif><img src=gfx/goods/1103.gif><img src=gfx/goods/1104.gif></center></td><td style='padding-left: 10px'>Panzerung der Gorn bietet den bestmöglichen Schutz für die Hüllenintegrität eines Schiffes.</td></tr><tr><td width=64><center><img src=gfx/goods/6604.gif></center></td><td style='padding-left: 10px'>Plasmakanonen der Gorn sind auf rohe Gewalt ausgelegt. Sie verursachen daher hohen Schaden, gehen gelegentlich aber mal daneben.</td></tr><tr><td width=64><center><img src=gfx/buildings/66/18.gif></td><td style='padding-left: 10px'>Die Gorn haben gelernt, die rohe Kraft eines Vulkans direkt anzuzapfen, und können aus diesen eine große Menge an Energie ziehen.</td></tr><tr><td width=64><center><img src=gfx/bev/crew_6.gif></center></td><td style='padding-left: 10px'>Siedler der Gorn können nur an einer Station der Gorn Hegemonie starten.</td></tr></table>\";
				document.getElementById(\"rinfo\").innerHTML = tx;
			}
		}
		</script>
		<form action=index.php method=get><input type=hidden name=p value=reg><input type=hidden name=s value=anm>
		<table class=\"tcal\">
		<tr>
			<td colspan=2><u><b>Schritt 2</b></u><br><br></td></tr>
			<tr><td colspan=2>Wie in jedem Spiel gibt es in Star Trek Universe Regeln, die von jedem Spieler akzeptiert werden müßen. Diese kannst Du unter dem Menüpunkt <a href=?p=reg&s=rul>Regeln</a> nachlesen.</td></tr>
			<tr><td width=50%>Ich akzeptiere die Regeln</td><td><input type=\"checkbox\" name=\"ar\" value=\"1\"></td></tr>";
			if ($error[rules]) $content .= "<tr><td colspan=2><font color=#FF0000>".$error[rules]."</font></td></tr>";
			$content .= "<tr><td colspan=2>Wähle jetzt die Rasse, die Du spielen willst.</td></tr>
			<tr><td>Rasse</td><td><select name=faction onChange=\"selchg();\"><option value=0>---------------<option value=1>Föderation<option value=2>Romulaner<option value=3>Klingonen</select></td></tr>";
			if ($error[faction]) $content .= "<tr><td colspan=2><font color=#FF0000>".$error[faction]."</font></td></tr>";
			$content .= "<tr><td colspan=2><div id=\"rinfo\">Bitte wähle eine Rasse</div></td></tr>";
			$content .= "<tr><td colspan=2 align=right><br><input type=submit value=\"Schritt 3 &gt;&gt;\" class=button></td></tr>	
		</table>
		</form>";
		
		$content = "<div style=\"padding:4px;\">".$content."</div>";
		
		echo "<table style=\"border:none;border-spacing:0px;border-collapse:separate;background:none;\" width=100%>";	
			echo "<tr><td style=\"".$leftstyle."\">".fixedPanel(1,"Registrierung - Schritt 1","mregi",$gfx."/buttons/icon/data.gif",$content)."</td></tr>";
		echo "</table>";		
	}
	if ($_SESSION[step] == 3)
	{
		switch ($_SESSION["ud"][faction])
		{
			case 1;
				$tx = "Föderation";
				break;
			case 2:
				$tx = "Romulaner";
				break;
			case 3:
				$tx = "Klingonen";
				break;
			case 4:
				$tx = "Cardassianer";
				break;
			case 5:
				$tx = "Ferengi";
				break;
			case 6:
				$tx = "Gorn";
				break;
		}
		$content .= "<form action=index.php method=get><input type=hidden name=p value=reg><input type=hidden name=s value=anm>
		<table class=\"tcal\">
		<tr>
			<td colspan=2><u><b>Schritt 3</b></u><br><br>Zusammenfassung der Daten<br><br></td></tr>
			<tr><td>Loginname</td><td>".$_SESSION["ud"][login]."</td></tr>
			<tr><td>Spielername</td><td>".$_SESSION["ud"][name]."</td></tr>
			<tr><td>Email-Adresse</td><td>".$_SESSION["ud"][email]."</td></tr>
			<tr><td>Gewählte Rasse</td><td>".$tx."</td></tr>
			<tr><td colspan=2>Wenn diese Daten richtig sind kannst Du die Registrierung abschließen</td></tr>
			<tr><td colspan=2 align=right><br><input type=submit value=\"Neu starten\" class=button name=sb> <input type=submit value=\"Registrierung abschließen\" class=button></td></tr>	
		</table>
		</form>";
		
		$content = "<div style=\"padding:4px;\">".$content."</div>";
		
		echo "<table style=\"border:none;border-spacing:0px;border-collapse:separate;background:none;\" width=100%>";	
			echo "<tr><td style=\"".$leftstyle."\">".fixedPanel(1,"Registrierung - Schritt 1","mregi",$gfx."/buttons/icon/data.gif",$content)."</td></tr>";
		echo "</table>";		
	}
	if ($_SESSION[step] == 4)
	{
		$main->register();
		$content .= "<table class=\"tcal\">
		<tr>
		<td>Registrierung erfolgreich!<br><br>";
		$content .= "Deine Anmeldung wurde gespeichert.<br>
		In wenigen Minuten erhälst Du eine Email um Deinen Account zu aktivieren.<br><br>Viel Spaß!<br><br><br>
		(Hinweis: Die Mail kann möglicherweise in Deinem Spam-Ordner landen. Bitte sieh auch dort nach. Sollte auch dies nichts bringen, bitte im Forum beschweren.)</td>
		</tr></table>";
		
		$content = "<div style=\"padding:4px;\">".$content."</div>";
		
		echo "<table style=\"border:none;border-spacing:0px;border-collapse:separate;background:none;\" width=100%>";	
			echo "<tr><td style=\"".$leftstyle."\">".fixedPanel(1,"Registrierung - Schritt 1","mregi",$gfx."/buttons/icon/data.gif",$content)."</td></tr>";
		echo "</table>";		
	}
}
?>
