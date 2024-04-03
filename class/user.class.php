<?php
class user
{
	function user()
	{
		global $db, $_SESSION;
		$this->db = $db;
		$this->sess = $_SESSION;
		$this->uid = $_SESSION["uid"];
		$this->lvl = $_SESSION["level"];
	}

	function updatedata($field, $value)
	{
		$this->db->query("UPDATE stu_user SET " . $field . "='" . $value . "' WHERE id=" . $this->sess[uid]);
	}

	function updateprofile($field, $value)
	{
		$result = $this->db->query("UPDATE stu_user_profiles SET " . $field . "='" . $value . "' WHERE user_id=" . $this->sess[uid], 6);
		if ($result == 0) $this->db->query("INSERT INTO stu_user_profiles (user_id," . $field . ") VALUES ('" . $this->sess[uid] . "','" . $value . "')");
	}

	function gf($field, $id)
	{
		return $this->db->query("SELECT " . $field . " FROM stu_user WHERE id=" . $id, 1);
	}

	function getnotes()
	{
		return $this->db->query("SELECT notes FROM stu_user WHERE id=" . $this->sess[uid], 1);
	}

	function setnotes($txt)
	{
		return $this->db->query("UPDATE stu_user SET notes='" . addslashes($txt) . "' WHERE id=" . $this->sess[uid]);
	}

	function senddelmail()
	{
		$this->db->query("UPDATE stu_user SET delmark='1' WHERE id=" . $this->uid);
		mail($this->sess[email], "STU Accountl�schung", "Hallo " . strip_tags($this->sess[user]) . "<br><br>
		Du bekommst diese Mail um die L�schung deines Accounts zu best�tigen. Falls du diese L�schung nicht angeordnet hast, ignoriere diese Email.<br>
		Andernfalls klicke auf folgenden Link um die L�schung zu best�tigen.<br>
		<a href=http://www.stuniverse.de/confirmdel.php?user=" . $this->uid . ">http://www.stuniverse.de/confirmdel.php?user=" . $this->uid . "</a><br>
		Mit freundlichen Gr��en<br><br>
		Das STU-Team", "From: Star Trek Universe <automail@changeme.de>
Content-Type: text/html");
		addlog(105, $this->sess[uid], "Accountl�schung angefordert", 5);
	}

	function checkdelstate($user)
	{
		if ($this->db->query("SELECT delmark FROM stu_user WHERE id=" . $user, 1) != 1) die(show_error(902));
	}

	function confirmdel($arr)
	{
		$result = $this->db->query("SELECT id FROM stu_user WHERE id=" . $arr[user] . " AND pass='" . md5($arr[pass]) . "'", 1);
		if ($result == 0) exit();
		$this->db->query("UPDATE stu_user SET delmark='2' WHERE id=" . $result . " LIMIT 1");
	}


	function getLevelText($lvl)
	{

		if ($lvl == 0) {
			return "<br><b>Willkommen bei Star Trek Universe!</b>
					<br><br>
					Der erste Schritt ist die Auswahl eines geeigneten Planeten, der als erste Kolonie dienen soll. <br>";
		}
		if ($lvl == 1) {
			return "<br><b>Gl�ckwunsch, Deine erste Kolonie wurde gegr�ndet!</b>
					<br><br>
					Damit die Kolonie wachsen kann, muss erst die Grundversorgung gesichtert werden.";
		}
		if ($lvl == 2) {
			return "<br><b>Die Grundversorgung des Planeten mit Nahrung, Energie und Wohnraum wurde gesichert.</b>
					<br><br>
					Jetzt wird es Zeit, die Basis f�r Industrie zu schaffen. Mit transparentem Aluminium als Baustoff und einfachen Fusionsreaktoren zur Energiegewinnung er�ffnen sich neue M�glichkeiten.";
		}
		if ($lvl == 3) {
			return "<br><b>Die Produktion von transparentem Aluminium und eine brauchbare Energieproduktion aus Fusionsreaktoren sind angelaufen.</b><br>
					<b>Deine Gro�macht hat Dir au�erdem ein einfaches Raumschiff zur Verf�gung gestellt, mit der die Umgebung Deiner neuen Heimat erkundet werden kann.</b>				
					<br><br>
					Um weiter zu Expandieren und weitere Kolonien in den Weiten des Weltraums zu gr�nden, wird Duranium ben�tigt. Sichere die Produktion, indem du Minen und Duraniumanlagen baust.<br>
					Errichte dann eine Startrampe, um mit einem dort gebauten Kolonieschiff zu starten. Kolonisiere einen Mond der Klasse M (Erd�hnlich), L (Wald) oder O (Ozeanisch), um auf die n�chste Stufe aufzusteigen.<br>
				";
		}
		if ($lvl == 4) {
			return "<b>Eine zweite Kolonie auf einem Mond wurde errichtet.</b>
					<br><br>
					Nun da mehr als eine Kolonie zu versorgen ist, solltest du mit dem Bau von Schiffen beginnen, die Waren von einer zur anderen Kolonie bringen k�nnen - oder sie im Notfall verteidigen. 
					Deine Gro�macht hat dir einen alten Frachter vermacht - es sollte m�glich sein, diesen oder das Erkundungsschiff nachzubauen.<br>
					<br>
					Mit der nun verf�gbaren Technologie und Infrastruktur ist es m�glich, in die unwirtlicheren Regionen deiner Kolonien vorzusto�en - aber auch exotischere Planeten zu besiedeln, die mehr Rohstoffe bieten.<br>
					<br>Dein Kolonielimit erh�ht sich auf 2 Planeten und 2 Monde.
				";
		}
		if ($lvl == 5) {
			return "<br><b>Deine einstige einzelne Kolonie hat sich zu einem kleinen Reich ausgeweitet.</b>
					<br><br>
					Du hast nun den Punkt erreicht, ab dem f�r weitere Technologische Fortschritte geforscht werden muss. Errichte dazu Forschungszentren, die Forschungspunkte generieren, die wiederum f�r neue Forschungen ausgegeben werden k�nnen.<br>
					<br><br>Dein Kolonielimit erh�ht sich zus�tzlich auf 3 Planeten und 3 Monde, und die extremsten Kolonieklassen sind freigegeben.
					<br><br>Ein weiterer Levelaufstieg ist momentan nicht m�glich.";
		}
		return 0;
	}



	function getLevelInfo()
	{
		global $gfx;

		$currentlevel = $_SESSION['level'];
		// $currentlevel = 5;

		$nextlevel = $currentlevel + 1;

		$done = true;
		$criteria = array();
		$i = 1;
		while ($i < 10) {
			$crit = checkLevelCriterion($currentlevel, $i, $_SESSION['uid']);
			if ($crit) {
				array_push($criteria, $crit);
				if (!$crit['done']) $done = false;
			} else
				break;
			$i++;
		}
		if (count($criteria) == 0) $done = false;

		$res = "<table width=100%>";
		$res .= "<tr><th colspan=3 style=\"padding-left:4px;height:24px;font-weight:bold;\">Dein aktueller Level: " . $currentlevel . "</th></tr>";

		$res .= "<tr><td colspan=3 style=\"padding-left:4px;padding-bottom:4px;\">" . $this->getLevelText($currentlevel) . "</td></tr>";

		if ($currentlevel == 0) {
			$res .= "<tr><td colspan=3 style=\"text-align:center;padding-left:4px;height:30px;font-weight:bold;\"><a href=?p=main&s=gs style=\"color:#00ff00\"><img src=" . $gfx . "/buttons/stern1.gif> Klicke hier, um eine Kolonie zu gr�nden! <img src=" . $gfx . "/buttons/stern1.gif></a></th></tr>";
		}
		if ($done) {
			$res .= "<tr><td colspan=3 style=\"text-align:center;padding-left:4px;height:30px;font-weight:bold;\"><a href=?p=main&a=gnl style=\"color:#00ff00\"><img src=" . $gfx . "/buttons/stern1.gif> Klicke hier, um auf den n�chsten Level aufzusteigen! <img src=" . $gfx . "/buttons/stern1.gif></a></th></tr>";
		}

		if (count($criteria) > 0) {
			$res .= "<tr><th colspan=3 style=\"padding-left:4px;height:24px;font-weight:bold;\">F�r Aufstieg n�tig:</th></tr>";
			foreach ($criteria as $crit) {
				$res .= displayCriterion($crit);
			}
		}




		$res .= "</table>";

		return $res;
	}



	function getColonyInfo()
	{
		global $gfx;
		$planets = $this->db->query("SELECT COUNT(id) as cols FROM stu_colonies WHERE user_id=" . $this->uid . " AND colonies_classes_id <'300'", 1);
		$moons = $this->db->query("SELECT COUNT(id) as cols FROM stu_colonies WHERE user_id=" . $this->uid . " AND colonies_classes_id >'300'", 1);

		$res = "<table style=\"width:100%\"><tr><th colspan=2>Kolonisiert</th></tr>";

		$limit = getColonyLimit($this->lvl);

		$plancan = $limit['p'];
		$mooncan = $limit['m'];

		if ($planets >= $plancan) {
			$res .= "<tr><td><img src=" . $gfx . "/buttons/icon/planet.gif title=\"Planeten kolonisiert\"> Koloniserte Planeten</td><td style=\"width:100px;text-align:center;\"><font color='#66ff66'>" . $planets . " / " . $plancan . "</font></td></tr>";
		} else {
			$res .= "<tr><td><img src=" . $gfx . "/buttons/icon/planet.gif title=\"Planeten kolonisiert\"> Koloniserte Planeten</td><td style=\"width:100px;text-align:center;\"><font color='#ff6666'>" . $planets . " / " . $plancan . "</font></td></tr>";
		}
		if ($moons >= $mooncan) {
			$res .= "<tr><td><img src=" . $gfx . "/buttons/icon/moon.gif title=\"Monde kolonisiert\"> Koloniserte Monde</td><td style=\"width:100px;text-align:center;\"><font color='#66ff66'>" . $moons . " / " . $mooncan . "</font></td></tr>";
		} else {
			$res .= "<tr><td><img src=" . $gfx . "/buttons/icon/moon.gif title=\"Monde kolonisiert\"> Koloniserte Monde</td><td style=\"width:100px;text-align:center;\"><font color='#ff6666'>" . $moons . " / " . $mooncan . "</font></td></tr>";
		}

		$res .= "<tr><th colspan=2>M�gliche Klassen</th></tr>";

		if ($this->lvl < 4) {
			$res .= "<tr><td><img src=" . $gfx . "/planets/301.gif width=16 height=16> Klasse M (Erd�hnlich)</td><td style=\"width:100px;text-align:center;\"><img src=" . $gfx . "/buttons/icon/yes.gif width=16 height=16></td></tr>";
			$res .= "<tr><td><img src=" . $gfx . "/planets/302.gif width=16 height=16> Klasse L (Wald)</td><td style=\"width:100px;text-align:center;\"><img src=" . $gfx . "/buttons/icon/yes.gif width=16 height=16></td></tr>";
			$res .= "<tr><td><img src=" . $gfx . "/planets/303.gif width=16 height=16> Klasse O (Ozean)</td><td style=\"width:100px;text-align:center;\"><img src=" . $gfx . "/buttons/icon/yes.gif width=16 height=16></td></tr>";

			$res .= "<tr><td><img src=" . $gfx . "/planets/304.gif width=16 height=16> Klasse K (�dland)</td><td style=\"width:100px;text-align:center;\"><img src=" . $gfx . "/buttons/icon/no.gif width=16 height=16></td></tr>";
			$res .= "<tr><td><img src=" . $gfx . "/planets/305.gif width=16 height=16> Klasse G (Tundra)</td><td style=\"width:100px;text-align:center;\"><img src=" . $gfx . "/buttons/icon/no.gif width=16 height=16></td></tr>";
			$res .= "<tr><td><img src=" . $gfx . "/planets/306.gif width=16 height=16> Klasse H (W�ste)</td><td style=\"width:100px;text-align:center;\"><img src=" . $gfx . "/buttons/icon/no.gif width=16 height=16></td></tr>";
			$res .= "<tr><td><img src=" . $gfx . "/planets/309.gif width=16 height=16> Klasse P (Eis)</td><td style=\"width:100px;text-align:center;\"><img src=" . $gfx . "/buttons/icon/no.gif width=16 height=16></td></tr>";

			$res .= "<tr><td><img src=" . $gfx . "/planets/307.gif width=16 height=16> Klasse X (Lava)</td><td style=\"width:100px;text-align:center;\"><img src=" . $gfx . "/buttons/icon/no.gif width=16 height=16></td></tr>";
			$res .= "<tr><td><img src=" . $gfx . "/planets/310.gif width=16 height=16> Klasse D (Feld)</td><td style=\"width:100px;text-align:center;\"><img src=" . $gfx . "/buttons/icon/no.gif width=16 height=16></td></tr>";
		}
		if ($this->lvl == 4) {
			$res .= "<tr><td><img src=" . $gfx . "/planets/301.gif width=16 height=16> Klasse M (Erd�hnlich)</td><td style=\"width:100px;text-align:center;\"><img src=" . $gfx . "/buttons/icon/yes.gif width=16 height=16></td></tr>";
			$res .= "<tr><td><img src=" . $gfx . "/planets/302.gif width=16 height=16> Klasse L (Wald)</td><td style=\"width:100px;text-align:center;\"><img src=" . $gfx . "/buttons/icon/yes.gif width=16 height=16></td></tr>";
			$res .= "<tr><td><img src=" . $gfx . "/planets/303.gif width=16 height=16> Klasse O (Ozean)</td><td style=\"width:100px;text-align:center;\"><img src=" . $gfx . "/buttons/icon/yes.gif width=16 height=16></td></tr>";

			$res .= "<tr><td><img src=" . $gfx . "/planets/304.gif width=16 height=16> Klasse K (�dland)</td><td style=\"width:100px;text-align:center;\"><img src=" . $gfx . "/buttons/icon/yes.gif width=16 height=16></td></tr>";
			$res .= "<tr><td><img src=" . $gfx . "/planets/305.gif width=16 height=16> Klasse G (Tundra)</td><td style=\"width:100px;text-align:center;\"><img src=" . $gfx . "/buttons/icon/yes.gif width=16 height=16></td></tr>";
			$res .= "<tr><td><img src=" . $gfx . "/planets/306.gif width=16 height=16> Klasse H (W�ste)</td><td style=\"width:100px;text-align:center;\"><img src=" . $gfx . "/buttons/icon/yes.gif width=16 height=16></td></tr>";
			$res .= "<tr><td><img src=" . $gfx . "/planets/309.gif width=16 height=16> Klasse P (Eis)</td><td style=\"width:100px;text-align:center;\"><img src=" . $gfx . "/buttons/icon/yes.gif width=16 height=16></td></tr>";

			$res .= "<tr><td><img src=" . $gfx . "/planets/307.gif width=16 height=16> Klasse X (Lava)</td><td style=\"width:100px;text-align:center;\"><img src=" . $gfx . "/buttons/icon/no.gif width=16 height=16></td></tr>";
			$res .= "<tr><td><img src=" . $gfx . "/planets/310.gif width=16 height=16> Klasse D (Feld)</td><td style=\"width:100px;text-align:center;\"><img src=" . $gfx . "/buttons/icon/no.gif width=16 height=16></td></tr>";
		}
		if ($this->lvl > 4) {
			$res .= "<tr><td><img src=" . $gfx . "/planets/301.gif width=16 height=16> Klasse M (Erd�hnlich)</td><td style=\"width:100px;text-align:center;\"><img src=" . $gfx . "/buttons/icon/yes.gif width=16 height=16></td></tr>";
			$res .= "<tr><td><img src=" . $gfx . "/planets/302.gif width=16 height=16> Klasse L (Wald)</td><td style=\"width:100px;text-align:center;\"><img src=" . $gfx . "/buttons/icon/yes.gif width=16 height=16></td></tr>";
			$res .= "<tr><td><img src=" . $gfx . "/planets/303.gif width=16 height=16> Klasse O (Ozean)</td><td style=\"width:100px;text-align:center;\"><img src=" . $gfx . "/buttons/icon/yes.gif width=16 height=16></td></tr>";

			$res .= "<tr><td><img src=" . $gfx . "/planets/304.gif width=16 height=16> Klasse K (�dland)</td><td style=\"width:100px;text-align:center;\"><img src=" . $gfx . "/buttons/icon/yes.gif width=16 height=16></td></tr>";
			$res .= "<tr><td><img src=" . $gfx . "/planets/305.gif width=16 height=16> Klasse G (Tundra)</td><td style=\"width:100px;text-align:center;\"><img src=" . $gfx . "/buttons/icon/yes.gif width=16 height=16></td></tr>";
			$res .= "<tr><td><img src=" . $gfx . "/planets/306.gif width=16 height=16> Klasse H (W�ste)</td><td style=\"width:100px;text-align:center;\"><img src=" . $gfx . "/buttons/icon/yes.gif width=16 height=16></td></tr>";
			$res .= "<tr><td><img src=" . $gfx . "/planets/309.gif width=16 height=16> Klasse P (Eis)</td><td style=\"width:100px;text-align:center;\"><img src=" . $gfx . "/buttons/icon/yes.gif width=16 height=16></td></tr>";

			$res .= "<tr><td><img src=" . $gfx . "/planets/307.gif width=16 height=16> Klasse X (Lava)</td><td style=\"width:100px;text-align:center;\"><img src=" . $gfx . "/buttons/icon/yes.gif width=16 height=16></td></tr>";
			$res .= "<tr><td><img src=" . $gfx . "/planets/310.gif width=16 height=16> Klasse D (Feld)</td><td style=\"width:100px;text-align:center;\"><img src=" . $gfx . "/buttons/icon/yes.gif width=16 height=16></td></tr>";
		}

		return $res;
	}


	function delforschungen()
	{
		return "Diese Funktion wurde deaktiviert. Und das aus gutem Grund.";

		$this->db->query("UPDATE stu_user SET research_konstruktion=0,research_technik=0,research_verarbeitung=0 WHERE id=" . $this->uid);
		$this->db->query("DELETE FROM stu_researched WHERE research_id!=500 AND user_id=" . $this->uid);
		$this->db->query("DELETE FROM stu_rumps_user WHERE user_id=" . $this->uid);
		global $_SESSION;
		$_SESSION["r_konstruktion"] = 0;
		$_SESSION["r_technik"] = 0;
		$_SESSION["r_verarbeitung"] = 0;
		addlog(115, $this->sess[uid], "Forschungen zur�ckgesetzt f�r id " . $this->uid, 5);
	}

	function getcols()
	{
		return $this->db->query("SELECT a.id,a.colonies_classes_id,a.name,a.sx,a.sy,a.eps,a.max_eps,a.schilde,a.max_schilde,a.schilde_status,a.max_storage,b.name as sname,b.cx,b.cy,SUM(c.count) as sc FROM stu_colonies as a LEFT JOIN stu_systems as b ON b.systems_id=a.systems_id LEFT JOIN stu_colonies_storage as c ON c.colonies_id=a.id WHERE a.user_id=" . $this->uid . " GROUP BY a.id ORDER BY a.colonies_classes_id,a.id LIMIT 10");
	}
}