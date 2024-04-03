<?php
class stations extends qpm
{
	function stations()
	{
		global $db, $_SESSION, $_GET, $map, $gfx;
		$this->gfx = $gfx;
		$this->db = $db;
		$this->uid = $_SESSION['uid'];
		$this->sess = $_SESSION;
		if (($_GET['p'] == "station" || $_GET['p'] == "station2") && check_int($_GET['id'])) {
			$result = $this->db->query("SELECT a.*,b.name as cname,b.rows,b.cols,b.renderstring FROM stu_stations as a LEFT JOIN stu_stations_classes as b USING(stations_classes_id) WHERE a.user_id=" . $this->uid . " AND a.id=" . $_GET['id'] . " LIMIT 1", 4);
			if ($result == 0) exit;
			foreach ($result as $key => $value) $this->$key = $value;
		} elseif ($_GET['p'] == "station" && $_GET['s'] && !is_numeric($_GET['id'])) exit;
	}


	function renderstation($sid)
	{
		global $_GET;
		echo "<style>
			.stat {
			background-image:url('http://www.stuniverse.de/main/gfx/banner.png');
			background-color:black;
			background-repeat:no-repeat;
			}
			.fore {
			background-image:url('http://www.stuniverse.de/gfx/fields/0.gif');
			background-color:black;
			background-repeat:no-repeat;
			}
		</style>";
		$fd = $this->db->query("SELECT a.aktiv,a.type,a.field_id,a.component_id FROM stu_stations_fielddata as a WHERE a.stations_id=" . $sid . " ORDER BY a.field_id");
		echo "<table cellpadding=1 cellspacing=1 background=http://www.stuniverse.de/main/gfx/banner.png><tr><td>";
		echo "<table cellpadding=1 cellspacing=1 background=http://www.stuniverse.de/gfx/fields/0.gif><tr>";
		$rendering = explode(",", $this->renderstring);
		while ($i < $this->rows * $this->cols) {

			if ($i % $this->rows == 0) echo "</tr><tr>";
			if ($rendering[$i]) {
				$data = $data = mysql_fetch_assoc($fd);
				echo "<td width=30><img src=" . $this->gfx . "/fields/" . $data[type] . ".gif></td>";
			} else echo "<td width=30>a</td>";

			$i++;
		}
		echo "</tr></table></td></tr></table>";
	}

	function getstationlist()
	{
		return $this->db->query("SELECT a.*, b.type,b.name as sname,b.cx,b.cy FROM stu_stations as a LEFT JOIN stu_systems as b USING(systems_id) WHERE a.user_id=" . $this->uid . " ORDER BY a.stations_classes_id,a.id");
	}

	function loadfield($fieldId, $statId)
	{
		$this->fdd = $this->db->query("SELECT a.type,a.component_id,a.aktiv,b.name,b.eps,b.is_activateable FROM stu_stations_fielddata as a LEFT JOIN stu_station_components as b ON b.component_id=a.component_id WHERE a.stations_id=" . $statId . " AND a.field_id=" . $fieldId . " LIMIT 1", 4);
	}

	function getstastorage($id)
	{
		return $this->db->query("SELECT a.goods_id,a.count,b.name FROM stu_stations_storage as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.stations_id=" . $id);
	}

	function lowerstorage($id, $good, $count)
	{
		$result = $this->db->query("UPDATE stu_stations_storage SET count=count-" . $count . " WHERE stations_id=" . $id . " AND goods_id=" . $good . " AND count>" . $count . " LIMIT 1", 6);
		if ($result == 0) $this->db->query("DELETE FROM stu_stations_storage WHERE stations_id=" . $id . " AND goods_id=" . $good . " LIMIT 1");
	}

	function lowereps($id, $count)
	{
		$result = $this->db->query("UPDATE stu_stations SET eps=eps-" . $count . " WHERE id=" . $id . " AND LIMIT 1", 6);
	}

	function upperstorage($id, $good, $count)
	{
		$result = $this->db->query("UPDATE stu_stations_storage SET count=count+" . $count . " WHERE stations_id=" . $id . " AND goods_id=" . $good . " LIMIT 1", 6);
		if ($result == 0) $this->db->query("INSERT INTO stu_stations_storage (stations_id,goods_id,count) VALUES ('" . $id . "','" . $good . "','" . $count . "')");
	}

	function loadstastorage()
	{
		$this->result = $this->db->query("SELECT SUM(a.count) as gc,a.goods_id FROM stu_station_component_goods as a LEFT JOIN stu_stations_fielddata as b USING(component_id) WHERE b.stations_id=" . $this->id . " AND b.aktiv=1 GROUP BY a.goods_id");
		while ($d = mysql_fetch_assoc($this->result)) $this->goods[$d['goods_id']] = $d['gc'];
		$this->result = $this->db->query("SELECT a.goods_id,a.name,b.count FROM stu_goods as a LEFT JOIN stu_stations_storage as b ON a.goods_id=b.goods_id AND b.stations_id=" . $this->id . " ORDER BY a.sort");
		$this->storage = $this->db->query("SELECT SUM(count) FROM stu_stations_storage WHERE stations_id=" . $this->id, 1);
	}

	function deactivatecompo($field)
	{
		if (!$this->fdd) $this->loadfield($field, $this->id);
		if ($this->fdd == 0) return;
		if ($this->fdd['aktiv'] == 0) return;
		if ($this->fdd['component_id'] == 0) return;
		if (!$this->fdd['is_activateable']) return;
		if ($this->fdd['aktiv'] > 1) return;
		$bd = $this->db->query("SELECT * FROM stu_station_components WHERE component_id=" . $this->fdd[component_id] . " LIMIT 1", 4);
		$this->db->query("START TRANSACTION");

		$this->db->query("UPDATE stu_stations_fielddata SET aktiv=0 WHERE field_id=" . $field . " AND stations_id=" . $this->id . " LIMIT 1");
		$this->db->query("UPDATE stu_stations SET bev_work=bev_work-" . $bd['bev_use'] . ",bev_free=bev_free+" . $bd['bev_use'] . ",bev_max=bev_max-" . $bd['bev_pro'] . ",max_schilde=max_schilde-" . $bd['schilde'] . "" . ($this->schilde > $this->max_schilde - $bd['schilde'] ? ",schilde=" . ($this->max_schilde - $bd['schilde']) : "") . "  WHERE id=" . $this->id . " LIMIT 1");
		$this->db->query("COMMIT");
		if ($this->uflag == 1) return;
		if ($this->fdd[component_id] > 30) return $this->fdd['name'] . " auf Feld " . $field . " deaktiviert" . $return;
		else return "Sammeloperation unterbrochen" . $return;
	}

	function activatecompo($field)
	{
		$this->loadfield($field, $this->id);
		if ($this->fdd == 0) return;
		if ($this->fdd['component_id'] == 0) return;
		if (!$this->fdd['is_activateable']) return;
		if ($this->fdd['aktiv'] == 1) return;
		if ($this->fdd['aktiv'] > 1) return "Dieses Modul wurde noch nicht fertiggestellt";
		$bd = $this->db->query("SELECT * FROM stu_station_components WHERE component_id=" . $this->fdd['component_id'] . " LIMIT 1", 4);
		if ($bd == 0) return;
		if ($bd['research_id'] > 0 && $this->db->query("SELECT research_id FROM stu_researched WHERE research_id=" . $bd['research_id'] . " AND user_id=" . $this->uid, 1) == 0) return "Dieses Modul kann nicht aktiviert werden";
		if ($bd['bev_use'] > 0 && $bd['bev_use'] > $this->bev_free) {
			if ($this->fdd[component_id] > 30) return "Zum Aktivieren des Moduls (" . $this->fdd['name'] . ") werden " . $bd['bev_use'] . " Arbeitslose ben�tigt - Verf�gbar sind " . $this->bev_free;
			else return "Dieses Schiff ben�tigt f�r seine Sammeloperation " . $bd['bev_use'] . " Crewmitglieder";
		}

		$this->db->query("START TRANSACTION");
		$this->db->query("UPDATE stu_stations_fielddata SET aktiv=1 WHERE field_id=" . $field . " AND stations_id=" . $this->id . " LIMIT 1");
		$this->db->query("UPDATE stu_stations SET bev_work=bev_work+" . $bd['bev_use'] . ",bev_free=bev_free-" . $bd['bev_use'] . ",bev_max=bev_max+" . $bd['bev_pro'] . ",max_schilde=max_schilde+" . $bd['schilde'] . " WHERE id=" . $this->id . " LIMIT 1");
		$this->db->query("COMMIT");
		$this->bev_work += $bd['bev_use'];
		$this->bev_free -= $bd['bev_use'];
		if ($this->fdd[component_id] > 30) return $this->fdd['name'] . " auf Feld " . $field . " aktiviert";
		else return "Sammeloperation begonnen";
	}


	function loadpossiblebuildings()
	{
		$this->result = $this->db->query("SELECT a.* FROM stu_station_components as a LEFT OUTER JOIN stu_researched as b ON a.research_id = b.research_id WHERE a.field" . $this->fdd[type] . "='1' AND (b.research_id>0 OR a.research_id=0) ORDER BY a.name");
	}


	function build($building, $field)
	{
		if ($field < 1 || $field > 1000) return;
		if ($nxb != "" && !check_int($nxb)) return;

		$this->loadfield($field, $this->id);

		if ($this->fdd[component_id] != 0) return "Auf diesem Feld ist bereits ein Modul installiert.";

		if ($this->db->query("SELECT COUNT(*) FROM stu_station_components WHERE component_id=" . $building . " AND field" . $this->fdd[type] . "='1'", 1) == 0) return;

		$data = $this->db->query("SELECT a.* FROM stu_station_components as a LEFT JOIN stu_researched as b ON a.research_id=b.research_id AND b.user_id=" . $this->uid . " WHERE a.component_id=" . $building . " AND (b.research_id>0 OR a.research_id=0)", 4);
		if ($data == 0) return;

		if ($data[blimit] > 0 && $data[blimit] <= $this->db->query("SELECT COUNT(*) FROM stu_stations_fielddata WHERE stations_id=" . $this->id . " AND component_id=" . $building, 1)) return "Von diesem Modultyp k�nnen nur " . $data[bclimit] . " pro Station installiert werden";

		if ($this->eps < $data[eps_cost]) return $return . "Zum Einbau wird " . $data[eps_cost] . " Energie ben�tigt - Vorhanden ist nur " . $this->eps;
		$result = $this->db->query("SELECT a.goods_id,a.count,b.count as vcount,c.name FROM stu_station_component_cost as a LEFT JOIN stu_stations_storage as b ON a.goods_id=b.goods_id AND b.stations_id=" . $this->id . " LEFT JOIN stu_goods as c ON a.goods_id=c.goods_id WHERE a.component_id=" . $building . " ORDER BY c.sort");
		while ($cost = mysql_fetch_assoc($result)) {
			if ($cost['vcount'] < $cost['count']) {
				return $return . "Es werden " . $cost['count'] . " " . $cost[name] . " ben�tigt - Vorhanden sind nur " . (!$cost[vcount] ? 0 : $cost[vcount]);
			}
		}
		$result = $this->db->query("SELECT a.goods_id,a.count,b.count as vcount,c.name FROM stu_station_component_cost as a LEFT JOIN stu_stations_storage as b ON a.goods_id=b.goods_id AND b.stations_id=" . $this->id . " LEFT JOIN stu_goods as c ON a.goods_id=c.goods_id WHERE a.component_id=" . $building . " ORDER BY c.sort");
		while ($cost = mysql_fetch_assoc($result)) {
			$this->lowerstorage($this->id, $cost[goods_id], $cost['count']);
		}
		$this->db->query("UPDATE stu_stations SET eps=eps-" . $data[eps_cost] . " WHERE id=" . $this->id . " LIMIT 1");
		$res = $this->db->query("UPDATE stu_stations_fielddata SET component_id=" . $data[component_id] . ",aktiv=" . (time() + $data[buildtime]) . " WHERE field_id=" . $field . " AND stations_id=" . $this->id . " LIMIT 1", 6);

		return $return . "Modul (" . $data[name] . ") wird installiert - Fertigstellung: " . date("d.m. H:i", time() + $data[buildtime]) . " Uhr";
	}

	function removecomponent($field, $nxb = "")
	{
		$this->loadfield($field, $this->id);
		if ($this->fdd == 0) return;
		if ($this->fdd['component_id'] == 0) return;
		if ($this->fdd['aktiv'] == 1) $return = $this->deactivatecompo($field) . "<br>";

		$bd = $this->db->query("SELECT lager,eps,bev_use,schilde,wk_proc FROM stu_station_components WHERE component_id=" . $this->fdd['component_id'] . " LIMIT 1", 4);

		$this->db->query("UPDATE stu_stations_fielddata SET component_id=0,aktiv=0 WHERE field_id=" . $field . " AND stations_id=" . $this->id . " LIMIT 1");
		if ($this->fdd['aktiv'] < 2) {
			if ($this->eps > $this->max_eps - $bd['eps']) $this->eps = $this->max_eps - $bd['eps'];
			$this->wkload = min($this->wkload, ($this->wkload_max - $bd['wk_proc']));
			// if ($this->schilde > $this->max_schilde-$bd['schilde']) $this->schilde = $this->max_schilde-$bd['schilde'];
			$this->db->query("UPDATE stu_stations SET max_storage=max_storage-" . $bd['lager'] . ",eps=" . $this->eps . ",max_eps=max_eps-" . $bd['eps'] . ",wkload_max=wkload_max-" . $bd[wk_proc] . ",wkload=" . $this->wkload . " WHERE id=" . $this->id . " LIMIT 1");
		}
		return $return . $this->fdd['name'] . " auf Feld " . $field . " deinstalliert" . $abm;
	}

	function getbuildoptions()
	{
		$res = $this->db->query("SELECT a.* FROM stu_stations_classes as a LEFT JOIN stu_researched as b ON a.research_id=b.research_id AND b.user_id=" . $this->uid . " WHERE (b.research_id>0 OR a.research_id=0) AND a.stations_classes_id < 99 ORDER BY a.stations_classes_id");
		while ($data = mysql_fetch_assoc($res)) {
			$bla[count]++;
			$bla[$bla[count]][id] = $data[stations_classes_id];
			$bla[$bla[count]][ecost] = $data[ecost];
			$bla[$bla[count]][name] = $data[name];

			$goodstring = "";
			$res2 = $this->db->query("SELECT a.goods_id,a.count,b.count as vcount,c.name FROM stu_stations_buildcost as a LEFT JOIN stu_stations_storage as b ON a.goods_id=b.goods_id AND b.stations_id=" . $this->id . " LEFT JOIN stu_goods as c ON a.goods_id=c.goods_id WHERE a.stations_classes_id=" . $data[stations_classes_id] . " ORDER BY c.sort");
			while ($cost = mysql_fetch_assoc($res2)) {
				if ($cost['vcount'] < $cost['count']) {
					$goodstring .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='http://www.stuniverse.de/gfx/goods/" . $cost[goods_id] . ".gif' border=0 title='" . $cost[name] . "'> <font color=red>" . $cost[count] . "</font>";
				} else $goodstring .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='http://www.stuniverse.de/gfx/goods/" . $cost[goods_id] . ".gif' border=0 title='" . $cost[name] . "'> <font color=green>" . $cost[count] . "</font>";
			}
			if ($this->eps < $data[ecost]) $bla[$bla[count]][goods] = "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='http://www.stuniverse.de/gfx/buttons/e_trans2.gif' border=0 title='Energie'> <font color=red>" . $data[ecost] . "</font>";
			else $bla[$bla[count]][goods] = "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='http://www.stuniverse.de/gfx/buttons/e_trans2.gif' border=0 title='Energie'> <font color=green>" . $data[ecost] . "</font>";
			$bla[$bla[count]][goods] .= $goodstring;
		}
		return $bla;
	}


	function buildstation($id)
	{


		if ($this->stations_classes_id != 99) return "He! Das is garkein Konstrukt!";
		$newdata = $this->db->query("SELECT * FROM stu_stations_classes WHERE stations_classes_id=" . $id . " LIMIT 1", 4);
		$pointsused = $this->db->query("SELECT SUM(b.slimit) FROM stu_stations as a LEFT JOIN stu_stations_classes as b ON a.stations_classes_id = b.stations_classes_id WHERE a.user_id=" . $this->uid . "", 1);
		$pointsused += $this->db->query("SELECT SUM(b.slimit) FROM stu_stations_buildprogress as a LEFT JOIN stu_stations_classes as b ON a.build_id = b.stations_classes_id WHERE a.user_id=" . $this->uid . "", 1);

		if ($id == 1) {
			$s = $this->db->query("SELECT count(*) FROM stu_stations WHERE user_id=" . $this->uid . " AND stations_classes_id = 1", 1);
			$s += $this->db->query("SELECT count(*) FROM stu_stations_buildprogress WHERE user_id=" . $this->uid . " AND build_id = 1", 1);
			if ($s >= 3) return "Limit f�r Phalanxen erreicht (3), Bau nicht m�glich.";
		} elseif ($id == 2) {
			$s = $this->db->query("SELECT count(*) FROM stu_stations WHERE user_id=" . $this->uid . " AND stations_classes_id = 2", 1);
			$s += $this->db->query("SELECT count(*) FROM stu_stations_buildprogress WHERE user_id=" . $this->uid . " AND build_id = 2", 1);
			if ($s >= 3) return "Limit f�r Depots erreicht (3), Bau nicht m�glich.";
		} else {
			if ($pointsused + $newdata[slimit] > 16) return "Limit f�r Stationen �berschritten, Bau nicht m�glich.";
		}

		if ($this->eps < $newdata[ecost]) return "Nicht genug Energie vorhanden.";

		$result = $this->db->query("SELECT a.goods_id,a.count,b.count as vcount,c.name FROM stu_stations_buildcost as a LEFT JOIN stu_stations_storage as b ON a.goods_id=b.goods_id AND b.stations_id=" . $this->id . " LEFT JOIN stu_goods as c ON a.goods_id=c.goods_id WHERE a.stations_classes_id=" . $id . " ORDER BY c.sort");
		while ($cost = mysql_fetch_assoc($result)) {
			if ($cost['vcount'] < $cost['count']) {
				return $return . "Es werden " . $cost['count'] . " " . $cost[name] . " ben�tigt - Vorhanden sind nur " . (!$cost[vcount] ? 0 : $cost[vcount]);
			}
		}
		$result = $this->db->query("SELECT a.goods_id,a.count,b.count as vcount,c.name FROM stu_stations_buildcost as a LEFT JOIN stu_stations_storage as b ON a.goods_id=b.goods_id AND b.stations_id=" . $this->id . " LEFT JOIN stu_goods as c ON a.goods_id=c.goods_id WHERE a.stations_classes_id=" . $id . " ORDER BY c.sort");
		while ($cost = mysql_fetch_assoc($result)) {
			$this->lowerstorage($this->id, $cost[goods_id], $cost['count']);
		}
		$this->db->query("UPDATE stu_stations SET eps=eps-" . $newdata[ecost] . ",slimit=" . $newdata[slimit] . " WHERE id=" . $this->id . " LIMIT 1");

		$finished = time() + $newdata[buildtime];

		$this->db->query("INSERT INTO stu_stations_buildprogress (stations_id,user_id,build_id,buildtime) VALUES (" . $this->id . "," . $this->uid . "," . $id . "," . $finished . ")");

		return "Bau von " . $newdata[name] . " begonnen - Fertigstellung: " . date("d.m. H:i", time() + $newdata[buildtime]) . " Uhr";
	}

	function arrayadd($arr, $ins)
	{
		$arr[count]++;
		$arr[$arr[count]] = $ins;
		return $arr;
	}

	function setstation($id, $class)
	{

		unset($arr);
		if ($class == 11) {

			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 3);
			$arr = $this->arrayadd($arr, 3);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 3);
			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 6);
		} elseif ($class == 12) {



			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 6);

			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 3);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 5);

			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 5);

			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 6);

			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 3);
			$arr = $this->arrayadd($arr, 3);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 6);

			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 5);

			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 5);

			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 6);
		} elseif ($class == 13) {



			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 5);

			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 3);
			$arr = $this->arrayadd($arr, 3);
			$arr = $this->arrayadd($arr, 3);
			$arr = $this->arrayadd($arr, 5);

			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 5);


			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 5);

			$arr = $this->arrayadd($arr, 1);

			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 2);

			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 2);

			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 6);

			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 6);

			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 6);
		} elseif ($class == 14) {

			$arr = $this->arrayadd($arr, 2);

			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 5);

			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 5);

			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 6);

			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 2);

			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 3);
			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 3);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 3);
			$arr = $this->arrayadd($arr, 1);

			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 4);

			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 6);

			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 5);

			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 4);
		} elseif ($class == 15) {

			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 5);

			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 5);

			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 3);
			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 5);

			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 6);

			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 2);

			$arr = $this->arrayadd($arr, 3);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 3);

			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 5);

			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 6);

			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 4);

			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 6);

			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 6);

			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 4);
		} elseif ($class == 16) {

			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 5);

			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 3);
			$arr = $this->arrayadd($arr, 2);

			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 3);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 3);
			$arr = $this->arrayadd($arr, 5);

			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 1);

			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 5);

			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 2);

			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 4);

			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 2);

			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 4);

			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 6);

			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 6);
		} elseif ($class == 4) {


			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 5);

			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 1);

			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 3);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 5);

			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 4);

			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 1);

			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 6);

			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 2);

			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 1);

			$arr = $this->arrayadd($arr, 4);

			$arr = $this->arrayadd($arr, 4);

			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 6);
		} elseif ($class == 3) {


			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 2);

			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 5);

			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 6);

			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 4);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 5);


			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 1);
		} elseif ($class == 2) {


			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 5);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 5);

			$arr = $this->arrayadd($arr, 4);

			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 6);

			$arr = $this->arrayadd($arr, 4);

			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 6);

			$arr = $this->arrayadd($arr, 4);

			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 2);
			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 6);

			$arr = $this->arrayadd($arr, 6);
			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 1);
			$arr = $this->arrayadd($arr, 6);
		} elseif ($class == 1) {


			$arr = $this->arrayadd($arr, 9);
			$arr = $this->arrayadd($arr, 9);
			$arr = $this->arrayadd($arr, 9);
			$arr = $this->arrayadd($arr, 9);
			$arr = $this->arrayadd($arr, 9);
		}


		$this->db->query("DELETE FROM stu_stations_fielddata WHERE stations_id = " . $id . "");
		for ($i = 1; $i <= $arr[count]; $i++) {
			$this->db->query("INSERT INTO stu_stations_fielddata (stations_id,field_id,type) VALUES ('" . $id . "','" . $i . "','" . $arr[$i] . "')");
		}
	}







	function finishbuilding($id)
	{

		$progress = $this->db->query("SELECT a.*,b.* FROM stu_stations_buildprogress as a LEFT JOIN stu_stations_classes as b ON a.build_id = b.stations_classes_id WHERE a.stations_id=" . $id . " LIMIT 1", 4);
		if ($progress == 0) return;

		$this->db->query("UPDATE stu_stations SET stations_classes_id = " . $progress[build_id] . ",bev_max = " . $progress[basebev] . ", armor = " . $progress[basearmor] . ", max_armor = " . $progress[basearmor] . ", max_schilde = " . $progress[baseshield] . ", max_eps = " . $progress[baseeps] . ", name= '" . $progress[name] . "', wkload_max = " . $progress[basewk] . ",max_storage=" . $progress[baselager] . " WHERE id = " . $id . "");

		$this->setstation($id, $progress[build_id]);

		$this->send_pm(1, $progress[user_id], ("Bau von " . $progress[name] . " abgeschlossen."), 5);

		$this->db->query("DELETE FROM stu_stations_buildprogress WHERE stations_id=" . $id . " LIMIT 1");
	}

	function setwkmode($mode)
	{
		$this->db->query("UPDATE stu_stations SET wkfull = " . $mode . " WHERE id = " . $this->id . "");
		$this->wkfull = $mode;
		return "Warpkernladungsmodus ge�ndert";
	}


	function loadenergydata()
	{
		$eplus = $this->db->query("SELECT SUM(b.eps_proc) FROM stu_stations_fielddata as a LEFT OUTER JOIN stu_station_components as b on a.component_id = b.component_id WHERE a.aktiv = 1 AND b.eps_proc > 0 AND a.stations_id = " . $this->id . "", 1);
		$eminus = $this->db->query("SELECT SUM(b.eps_proc) FROM stu_stations_fielddata as a LEFT OUTER JOIN stu_station_components as b on a.component_id = b.component_id WHERE a.aktiv = 1 AND b.eps_proc < 0 AND a.stations_id = " . $this->id . "", 1);
		$crew = $this->db->query("SELECT (bev_work+bev_free+bev_crew) FROM stu_stations WHERE id = " . $this->id . "", 1);

		$mode = $this->db->query("SELECT wkfull FROM stu_stations WHERE id = " . $this->id . "", 1);

		$replis = $this->db->query("SELECT COUNT(aktiv) FROM stu_stations_fielddata WHERE stations_id = " . $this->id . " AND aktiv=1 AND component_id = 102", 1);

		$wkloadsize = 40;

		$rs = 5 + $replis * 3;

		$erepli = ceil($crew / $rs);

		$warpcores = $this->db->query("SELECT count(aktiv) FROM stu_stations_fielddata as a LEFT OUTER JOIN stu_station_components as b on a.component_id = b.component_id WHERE a.aktiv = 1 AND b.eps_proc > 0 AND a.stations_id = " . $this->id . "", 1);


		$nrofloads = $warpcores;

		if ($mode == 1) {

			if (($nrofloads * $wkloadsize) - $eplus > ($this->wkload_max - $this->wkload)) {
				$use = ($this->wkload_max - $this->wkload) + $eplus;
				$nrofloads = ceil($use / $wkloadsize) - 1;
				$wkplus = $nrofloads * $wkloadsize - $eplus;
			} else $wkplus = $nrofloads * $wkloadsize - $eplus;
		} else {

			if ($eplus < $this->wkload) $nrofloads = 0;
			else $nrofloads = ceil($eplus / $wkloadsize);
			$wkplus = $nrofloads * $wkloadsize - $eplus;
			if ($wkplus > ($this->wkload_max - $this->wkload)) $wkplus = ($this->wkload_max - $this->wkload);
		}

		$this->wkplus = $wkplus;
		$this->eplus = $eplus;
		$this->eminus = $eminus;
		$this->erepli = $erepli;
		$this->egesamt = $eplus + $eminus - $erepli;
		$this->nrofloads = $nrofloads;
		$this->repl = $rs;
	}


	function loadenergydatabyid($id)
	{
		$data = $this->db->query("SELECT * FROM stu_stations WHERE id = " . $id . "", 4);
		$eplus = $this->db->query("SELECT SUM(b.eps_proc) FROM stu_stations_fielddata as a LEFT OUTER JOIN stu_station_components as b on a.component_id = b.component_id WHERE a.aktiv = 1 AND b.eps_proc > 0 AND a.stations_id = " . $id . "", 1);
		$eminus = $this->db->query("SELECT SUM(b.eps_proc) FROM stu_stations_fielddata as a LEFT OUTER JOIN stu_station_components as b on a.component_id = b.component_id WHERE a.aktiv = 1 AND b.eps_proc < 0 AND a.stations_id = " . $id . "", 1);
		$crew = $this->db->query("SELECT (bev_work+bev_free+bev_crew) FROM stu_stations WHERE id = " . $id . "", 1);

		$mode = $this->db->query("SELECT wkfull FROM stu_stations WHERE id = " . $id . "", 1);

		$replis = $this->db->query("SELECT COUNT(aktiv) FROM stu_stations_fielddata WHERE stations_id = " . $id . " AND aktiv=1 AND component_id = 102", 1);

		$wkloadsize = 40;

		$rs = 5 + $replis * 3;

		$erepli = ceil($crew / $rs);

		$warpcores = $this->db->query("SELECT count(aktiv) FROM stu_stations_fielddata as a LEFT OUTER JOIN stu_station_components as b on a.component_id = b.component_id WHERE a.aktiv = 1 AND b.eps_proc > 0 AND a.stations_id = " . $id . "", 1);


		$nrofloads = $warpcores;

		if ($mode == 1) {

			if (($nrofloads * $wkloadsize) - $eplus > ($data[wkload_max] - $data[wkload])) {
				$use = ($data[wkload_max] - $data[wkload]) + $eplus;
				$nrofloads = ceil($use / $wkloadsize) - 1;
				$wkplus = $nrofloads * $wkloadsize - $eplus;
			} else $wkplus = $nrofloads * $wkloadsize - $eplus;
		} else {

			if ($eplus < $data[wkload]) $nrofloads = 0;
			else $nrofloads = ceil($eplus / $wkloadsize);
			$wkplus = $nrofloads * $wkloadsize - $eplus;
			if ($wkplus > ($data[wkload_max] - $data[wkload])) $wkplus = ($data[wkload_max] - $data[wkload]);
		}
		$ret[wkplus] = $wkplus;
		$ret[eplus] = $eplus;
		$ret[eminus] = $eminus;
		$ret[erepli] = $erepli;
		$ret[egesamt] = $eplus + $eminus - $erepli;
		$ret[nrofloads] = $nrofloads;
		$ret[repl] = $rs;

		return $ret;
	}

	function nbs()
	{
		return $this->db->query("SELECT a.id,a.fleets_id,a.rumps_id,a.name,a.huelle,a.max_huelle,a.cloak,a.schilde_status,a.schilde,a.max_schilde,a.user_id,a.traktor,a.traktormode,a.trumps_id,a.user,a.allys_id,a.warp,a.cname,a.trumfield,a.slots,a.is_shuttle,a.fname,a.fship_id,a.is_rkn,b.type,c.ships_id as dcship_id,d.mode FROM stu_views_nbs as a LEFT JOIN stu_ally_relationship as b ON (a.allys_id=b.allys_id1 AND b.allys_id2=" . $_SESSION['allys_id'] . ") OR (a.allys_id=b.allys_id2 AND b.allys_id1=" . $this->sess['allys_id'] . ") LEFT JOIN stu_ships_decloaked as c ON a.id=c.ships_id AND c.user_id=" . $this->sess['uid'] . " LEFT JOIN stu_contactlist as d ON d.user_id=a.user_id AND d.recipient=" . $this->sess['uid'] . " WHERE a.id!=" . $this->id . " AND a.systems_id=" . $this->systems_id . " AND a.sx=" . $this->sx . " AND a.sy=" . $this->sy . " GROUP BY a.id");
	}


	function loadshields($count)
	{
		if ($this->schilde >= $this->max_schilde) return "Schilde sind bereits voll geladen";
		if ($count > $this->eps) $count = $this->eps;
		$sh = $this->schilde + $count;
		if ($this->schilde + $count > $this->max_schilde) {
			$count = ceil($this->max_schilde - $this->schilde);
			$sh = $this->max_schilde;
		}
		$this->db->query("UPDATE stu_stations SET eps = eps-" . $count . ",schilde =" . $sh . " WHERE id=" . $this->id . " LIMIT 1");
		$this->eps -= $count;
		return "Schilde um " . $count . " Einheiten geladen";
	}

	function setshstatus($st)
	{
		if ($st != 0 && $st != 1) return "Lass den Bl�dsinn.";
		if ($st == 1 && $this->schilde <= 0) return "Die Schilde k�nnen nicht aktiviert werden, wenn sie nicht geladen sind.";
		$this->db->query("UPDATE stu_stations SET schilde_status = '" . $st . "' WHERE id = " . $this->id . "");
		$this->schilde_status = $st;
		if ($st == 1) return "Schilde aktiviert";
		if ($st == 0) return "Schilde deaktiviert";
	}

	function getshddmgreduce()
	{
		$phaser = $this->db->query("SELECT SUM(b.schildredu) FROM stu_stations_fielddata as a LEFT JOIN stu_station_components as b on a.component_id = b.component_id WHERE a.stations_id = " . $this->id . " AND b.schildtyp = 1 AND a.aktiv=1", 1);
		$disrup = $this->db->query("SELECT SUM(b.schildredu) FROM stu_stations_fielddata as a LEFT JOIN stu_station_components as b on a.component_id = b.component_id WHERE a.stations_id = " . $this->id . " AND b.schildtyp = 3 AND a.aktiv=1", 1);
		$plasma = $this->db->query("SELECT SUM(b.schildredu) FROM stu_stations_fielddata as a LEFT JOIN stu_station_components as b on a.component_id = b.component_id WHERE a.stations_id = " . $this->id . " AND b.schildtyp = 2 AND a.aktiv=1", 1);
		return array("p" => $phaser, "d" => $disrup, "l" => $plasma);
	}

	function changename($name)
	{
		$name = strip_tags($name, "<font></font><i></i><b></b>");
		if (!check_html_tags($name)) $name = strip_tags($name);
		$this->db->query("UPDATE stu_stations SET name='" . str_replace("\"", "", $name) . "' WHERE id=" . $this->id . " LIMIT 1");
		return "Der Name der Station wurde ge�ndert";
	}

	function sendfreighter($field, $tx, $ty, $ts)
	{
		if ($ts == 0) $type = $this->db->query("SELECT type FROM stu_map WHERE cx = " . $tx . " AND cy = " . $ty . "", 1);
		else $type = $this->db->query("SELECT type FROM stu_sys_map WHERE sx = " . $tx . " AND sy = " . $ty . " AND systems_id = " . $ts . "", 1);

		if ($type == 2) $b = 1;
		elseif ($type == 3) $b = 2;
		elseif ($type == 11) $b = 3;
		elseif ($type == 12) $b = 4;
		elseif ($type == 59) $b = 6;
		elseif ($type == 69) $b = 5;
		else return "Auf diesem Feldtyp ist das Sammeln nicht m�glich";

		if ($this->bev_free < 3) return "Es werden 3 freie Crewmitglieder ben�tigt";

		$fld = $this->db->query("SELECT * FROM stu_stations_fielddata WHERE field_id = " . $field . " AND stations_id = " . $this->id . "", 4);

		if ($fld[component_id] != 0) return "Dieses Schiff sammelt bereits";
		if ($fld[field_id] < 100) return "Dies ist kein Schiff";
		if ($fld[ship] == 0) return "Fehlende Schiffsdaten - sofort Admin melden.";

		$range = 9;
		if ($fld[type] > 20) {
			$b += 10;
			$range = 6;
		}

		$plus = $this->db->query("SELECT COUNT(field_id) FROM stu_stations_fielddata WHERE stations_id = " . $this->id . " AND component_id = 116", 1);

		$range += $plus * 3;
		$range = min($range, $this->sensor);
		$homesys = $this->db->query("SELECT * FROM stu_systems WHERE systems_id = " . $this->systems_id . "", 4);
		if ($ts == 0) {
			$dist = max(abs($homesys[cx] - $tx), abs($homesys[cy] - $ty));
		} else {
			$targetsys = $this->db->query("SELECT * FROM stu_systems WHERE systems_id = " . $ts . "", 4);
			$dist = max(abs($homesys[cx] - $targetsys[cx]), abs($homesys[cy] - $targetsys[cy]));
		}



		if ($range < ($dist)) return "Dieses Ziel ist au�erhalb der Reichweite des Schiffs.";


		if ($ts == 0) {

			$this->db->query("START TRANSACTION");
			$this->db->query("UPDATE stu_ships SET cx=" . $tx . ",cy=" . $ty . ",systems_id = " . $ts . ",sx=0,sy=0 WHERE id = " . $fld[ship] . "");

			$this->db->query("UPDATE stu_stations_fielddata SET aktiv=1,component_id=" . $b . " WHERE stations_id = " . $this->id . " AND field_id = " . $field . "");
			$this->db->query("UPDATE stu_stations SET bev_work=bev_work+3,bev_free=bev_free-3 WHERE id=" . $this->id . " LIMIT 1");
			$this->db->query("UPDATE stu_ships SET crew=3 WHERE id=" . $fld[ship] . " LIMIT 1");
			$this->db->query("COMMIT");
		} else {
			$this->db->query("START TRANSACTION");
			$this->db->query("UPDATE stu_ships SET cx=" . $targetsys[cx] . ",cy=" . $targetsys[cy] . ",systems_id = " . $ts . ",sx=" . $tx . ",sy=" . $ty . " WHERE id = " . $fld[ship] . "");

			$this->db->query("UPDATE stu_stations_fielddata SET aktiv=1,component_id=" . $b . " WHERE stations_id = " . $this->id . " AND field_id = " . $field . "");
			$this->db->query("UPDATE stu_stations SET bev_work=bev_work+3,bev_free=bev_free-3 WHERE id=" . $this->id . " LIMIT 1");
			$this->db->query("UPDATE stu_ships SET crew=3 WHERE id=" . $fld[ship] . " LIMIT 1");
			$this->db->query("COMMIT");
		}





		$this->bev_work += 3;
		$this->bev_free -= 3;



		return "Kurs wird gesetzt, Sammelvorgang wird gestartet!";
	}

	function returnfreighter($field)
	{

		$fld = $this->db->query("SELECT * FROM stu_stations_fielddata WHERE field_id = " . $field . " AND stations_id = " . $this->id . "", 4);

		if ($fld[component_id] == 0) return "Dieses Schiff ist bereits hier.";
		if ($fld[field_id] < 100) return "Dies ist kein Schiff";
		if ($fld[ship] == 0) return "Fehlende Schiffsdaten - sofort Admin melden.";

		$targetsys = $this->db->query("SELECT * FROM stu_systems WHERE systems_id = " . $this->systems_id . "", 4);

		$this->db->query("START TRANSACTION");
		$this->db->query("UPDATE stu_ships SET cx=" . $targetsys[cx] . ",cy=" . $targetsys[cy] . ",systems_id = " . $this->systems_id . ",sx=" . $this->sx . ",sy=" . $this->sy . " WHERE id = " . $fld[ship] . "");

		$this->db->query("UPDATE stu_stations_fielddata SET aktiv=0,component_id=0 WHERE stations_id = " . $this->id . " AND field_id = " . $field . "");
		$this->db->query("UPDATE stu_stations SET bev_work=bev_work-3,bev_free=bev_free+3 WHERE id=" . $this->id . " LIMIT 1");
		$this->db->query("UPDATE stu_ships SET crew=0 WHERE id=" . $fld[ship] . " LIMIT 1");
		$this->db->query("COMMIT");





		$this->bev_work -= 3;
		$this->bev_free += 3;




		return "Kurs auf die Heimatbasis wird gesetzt.";
	}

	function freefreighter($field)
	{

		$fld = $this->db->query("SELECT * FROM stu_stations_fielddata WHERE field_id = " . $field . " AND stations_id = " . $this->id . "", 4);

		if ($fld[component_id] != 0) return "Schiff muss zun�chst zur Station zur�ckkehren";
		if ($fld[field_id] < 100) return "Dies ist kein Schiff";
		if ($fld[ship] == 0) return "Fehlende Schiffsdaten - sofort Admin melden.";


		if ($this->bev_free < 3) return "Dem Schiff m�ssen 3 Crewmitglieder bereitgestellt werden.";

		$this->db->query("START TRANSACTION");
		$this->db->query("UPDATE stu_ships SET assigned=0,crew=3 WHERE id = " . $fld[ship] . "");
		$this->db->query("DELETE FROM stu_stations_fielddata WHERE stations_id = " . $this->id . " AND field_id = " . $field . " LIMIT 1");
		$this->db->query("UPDATE stu_stations SET bev_free=bev_free-3 WHERE id=" . $this->id . " LIMIT 1");
		$this->db->query("COMMIT");
		return "Frachter wurde freigestellt.";
	}

	function generateszcode()
	{
		global $_SESSION;
		$sc = substr(md5(ceil($_SESSION['logintime'] / $this->id)), 0, 6);
		$_SESSION['szcode'] = $sc;
	}

	function selfdestruct($id, $code)
	{
		$data = $this->db->query("SELECT * FROM stu_stations WHERE id=" . $id . " AND user_id=" . $this->uid, 4);
		if ($data == 0) die(show_error(902));

		if ($_SESSION['szcode'] != $code) {
			$_SESSION['szcode'] = "";
			return "Der Code ist ung�ltig";
		}

		$this->db->query("START TRANSACTION");
		$this->db->query("DELETE FROM stu_stations WHERE id=" . $id . " AND user_id=" . $this->uid . "");
		$this->db->query("DELETE FROM stu_stations_fielddata WHERE stations_id = " . $id . "");
		$this->db->query("DELETE FROM stu_stations_storage WHERE stations_id = " . $id . "");
		$this->db->query("DELETE FROM stu_stations_buildprogress WHERE stations_id = " . $id . "");
		$this->db->query("UPDATE stu_ships SET assigned=0 WHERE assigned=" . $id . "");
		$this->db->query("COMMIT");
		return "Selbstzerst�rung wurde eingeleitet.";
	}

	function subspacescansystem($sysId)
	{
		return $this->db->query("SELECT a.sx as cx,a.sy as cy,a.type, max(UNIX_TIMESTAMP(b.date)) as ftime, count(b.ships_id) as fcount FROM stu_sys_map as a LEFT JOIN stu_sectorflights as b ON a.sy = b.sy AND a.sx = b.sx AND a.systems_id = b.systems_id WHERE a.systems_id = " . $sysId . " GROUP BY a.sx,a.sy ORDER BY a.sy,a.sx");
	}

	function subspacescanwarp($x, $y, $range)
	{
		return $this->db->query("SELECT a.cx,a.cy,a.type, max(UNIX_TIMESTAMP(b.date)) as ftime, count(b.ships_id) as fcount FROM stu_map as a LEFT JOIN stu_sectorflights as b ON a.cy = b.cy AND a.cx = b.cx AND b.systems_id = 0 WHERE a.cx BETWEEN " . ($x - $range) . " AND " . ($x + $range) . " AND a.cy BETWEEN " . ($y - $range) . " AND " . ($y + $range) . " GROUP BY a.cx,a.cy ORDER BY a.cy,a.cx");
	}



	function gettorpcost($torp_type)
	{
		return $this->db->query("SELECT a.goods_id,a.count,b.name,c.count as vcount FROM stu_torpedo_cost as a LEFT JOIN stu_goods as b USING(goods_id) LEFT JOIN stu_stations_storage as c ON a.goods_id=c.goods_id AND c.stations_id=" . $this->id . " WHERE a.torp_type=" . $torp_type . " ORDER BY b.sort");
	}

	function getreptorps()
	{
		return $this->db->query("SELECT a.torp_type,a.name,a.goods_id,a.ecost FROM stu_torpedo_types as a LEFT JOIN stu_researched as b ON a.research_id=b.research_id AND b.user_id=" . $this->uid . " WHERE (a.research_id=0 OR (a.research_id>0 AND !ISNULL(b.user_id))) ORDER BY torp_type");
	}

	function torpedoherstellung($moarr)
	{
		foreach ($moarr as $key => $value) {
			if (!check_int($value) || !check_int($key) || $value < 1) continue;
			if ($value > 99) $value = 99;
			if ($this->db->query("SELECT field_id FROM stu_stations_fielddata WHERE component_id=120 AND aktiv=0 AND stations_id=" . $this->id, 1) == 0) return;
			$data = $this->db->query("SELECT a.torp_type,a.name,a.goods_id,a.ecost FROM stu_torpedo_types as a LEFT JOIN stu_researched as b ON a.research_id=b.research_id AND b.user_id=" . $this->uid . " WHERE (a.research_id=0 OR (a.research_id>0 AND !ISNULL(b.user_id))) AND a.torp_type=" . $key, 4);
			if ($data == 0) die(show_error(902));
			if ($data[ecost] * $value > $this->eps) $value = floor($this->eps / $data[ecost]);
			if ($this->eps < $data[ecost] * $value) {
				$msg .= "F�r die Herstellung von Torpedosdes Typs " . $data[name] . " wird " . $data[ecost] . " Energie ben�tigt - Vorhanden ist nur " . $this->eps . "<br>";
				continue;
			}
			$cr = $this->gettorpcost($key);
			while ($co = mysql_fetch_assoc($cr)) {
				if ($value * $co['count'] > $co[vcount]) {
					$value = floor($co[vcount] / $co['count']);
					if ($value == 0) {
						$msg .= "F�r die Herstellung von Torpedos des Typs " . $data[name] . " werden " . $co['count'] . " " . $co[name] . " ben�tigt - Vorhanden ist nur " . (!$co[vcount] ? 0 : $co[vcount]) . "<br>";
						break;
					}
				}
				if ($value != 0) $cost[$co[goods_id]] = $co['count'];
			}
			if ($value == 0) {
				$msg .= "Es wurden keine Torpedos des Typs " . $data[name] . " hergestellt<br>";
				$this->db->query("ROLLBACK");
				continue;
			}
			$this->db->query("START TRANSACTION");
			if (is_array($cost)) {
				foreach ($cost as $key2 => $value2) $this->lowerstorage($this->id, $key2, $value2 * $value);
			}
			$this->upperstorage($this->id, $data[goods_id], $value * 5);
			$this->db->query("UPDATE stu_stations SET eps=eps-" . ($value * $data[ecost]) . " WHERE id=" . $this->id);
			$this->db->query("COMMIT");
			$this->eps -= ($value * $data[ecost]);
			$msg .= "Es wurden " . ($value * 5) . " Torpedos des Typs " . $data[name] . " hergestellt<br>";
		}
		return $msg;
	}

	function loadtorp($field, $torp)
	{
		$this->loadfield($field, $this->id);
		if ($this->fdd == 0) return;
		if ($this->fdd['component_id'] == 0) return;
		if (!$this->fdd['is_activateable']) return;
		if ($this->fdd['aktiv'] > 1) return "Dieses Modul wurde noch nicht fertiggestellt";

		if ($this->fdd['component_id'] == 71) $torpclass = 1;
		if ($this->fdd['component_id'] == 72) $torpclass = 2;
		if ($this->fdd['component_id'] == 73) $torpclass = 3;
		if ($this->fdd['component_id'] == 74) $torpclass = 4;
		if ($this->fdd['component_id'] == 75) $torpclass = 2;
		if ($this->fdd['component_id'] == 76) $torpclass = 2;

		if ($torpclass == 0) return "Dies ist kein Torpedowerfer";

		$torpdata = $this->db->query("SELECT a.* FROM stu_torpedo_types as a LEFT JOIN stu_stations_storage as b on a.goods_id = b.goods_id WHERE b.stations_id=" . $this->id . " AND b.count > 0 AND a.torp_type = " . $torp . " LIMIT 1", 4);

		if ($torpdata[type] > $torpclass) return "Diese Torpedotyp ist zu gro� f�r den Werfer.";
		if ($torpdata == 0) return "Keine Torpedos vorhanden";
		$this->db->query("UPDATE stu_stations_fielddata SET torptype=" . $torp . " WHERE field_id=" . $field . " AND stations_id=" . $this->id . " LIMIT 1");

		return $torpdata[name] . "s werden geladen.";
	}

	function attackfleet($target)
	{
		if ($this->uid != 102) "Noch nicht fertig";
		return "Boom Fleet";
	}

	function attackship($target)
	{
		if ($this->uid != 102) "Noch nicht fertig";
		return "Boom Ship";
	}
}