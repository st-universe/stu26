<?php
include_once("/var/www/st-universe.eu/inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;

function setColonyFields($id,$encodedFields) {
	global $db;
	if ($encodedFields != "XX") {
		$i = 1;
		while (strlen($encodedFields) > 0) {
		
			$nextfield = hexdec(substr($encodedFields,0,2));
			$encodedFields = substr($encodedFields,2);
		
			 $db->query("INSERT INTO stu_colonies_fielddata (colonies_id,field_id,type) VALUES ('".$id."','".$i."','".$nextfield."')");
			 $i++;
		}
	}
}


// $result = $db->query("SELECT id,user,allys_id FROM stu_user WHERE (delmark='2' OR (UNIX_TIMESTAMP(lastaction)<".(time()-2592000)." AND (ISNULL(vac_active) OR vac_active='')) OR (vac_active='1' AND UNIX_TIMESTAMP(lastaction)<".(time()-5184000).") OR (ISNULL(aktiv) AND UNIX_TIMESTAMP(lastaction)<".(time()-432000).") OR (aktiv='1' AND level='1' AND UNIX_TIMESTAMP(lastaction)<".(time()-172800)." AND id!=507)) AND (ISNULL(npc_type) OR npc_type='')");
$result = $db->query("SELECT id,user,allys_id FROM stu_user WHERE (delmark='2' OR (UNIX_TIMESTAMP(lastaction)<".(time()-2592000)." AND (ISNULL(vac_active) OR vac_active='')) OR (ISNULL(aktiv) AND UNIX_TIMESTAMP(lastaction)<".(time()-432000).")) AND (ISNULL(npc_type) OR npc_type='')");
//$result = $db->query("SELECT id,user,allys_id FROM stu_user WHERE delmark='2'");
while($data=mysql_fetch_assoc($result))
{
	// echo "<br>".$data[id]." - ".strip_tags($data[user])."\n";
	// continue;
	// break;
	// Allianzabschnitt
	if ($data[allys_id] > 0)
	{
		$dat= $db->query("SELECT allys_id,vize_user_id FROM stu_allylist WHERE praes_user_id=".$data[id],4);
		if ($dat != 0)
		{
			if ($dat[vize_user_id] == 0)
			{
				$db->query("DELETE FROM stu_allylist WHERE allys_id=".$data[allys_id]);
				$db->query("DELETE FROM stu_ally_relationship WHERE allys_id1=".$data[allys_id]." OR allys_id2=".$data[allys_id]);
				$db->query("DELETE FROM stu_ally_kn WHERE allys_id=".$data[allys_id]);
				$db->query("UPDATE stu_user SET allys_id=0 WHERE allys_id=".$data[allys_id]);
			}
			else $db->query("UPDATE stu_allylist SET praes_user_id=vize_user_id,vize_user_id=0 WHERE allys_id=".$data[allys_id]);
		}
		else $db->query("UPDATE stu_allylist SET vize_user_id=0 WHERE vize_user_id=".$data['id']);
	}
	// Andockrechte ALLIANZ
	$db->query("DELETE FROM stu_dockingrights WHERE type='3' AND id=".$data[allys_id]);
	// Kolonieabschnitt
	$res = $db->query("SELECT id,colonies_classes_id,fieldstring FROM stu_colonies WHERE user_id=".$data[id]);
	while($dat=mysql_fetch_assoc($res))
	{
		$db->query("DELETE FROM stu_colonies_fielddata WHERE colonies_id=".$dat[id]);
		setColonyFields($dat[id],$dat[fieldstring]);
		$db->query("UPDATE stu_colonies SET user_id=1,name='',bev_work=0,bev_free=0,bev_max=0,eps=0,max_eps=0,max_storage=0,schilde=0,max_schilde=0,lastrw=0,bevstop=0,einwanderung='1',maintain_ship=0,schilde_status='0' WHERE id=".$dat[id]);
		$db->query("DELETE FROM stu_colonies_actions WHERE colonies_id=".$dat[id]);
		$db->query("DELETE FROM stu_colonies_storage WHERE colonies_id=".$dat[id]);
		$db->query("DELETE FROM stu_colonies_trade WHERE colonies_id=".$dat[id]);
	}
	// Kontaktliste
	$db->query("DELETE FROM stu_contactlist WHERE user_id=".$data[id]." OR recipient=".$data[id]);
	// Andockrechte USER
	$db->query("DELETE FROM stu_dockingrights WHERE type='2' AND id=".$data[id]);
	// Flotten
	$db->query("DELETE FROM stu_fleets WHERE user_id=".$data[id]);
	// Ignoreliste
	$db->query("DELETE FROM stu_ignorelist WHERE user_id=".$data[id]." OR recipient=".$data[id]);
	// Quests
	$db->query("DELETE FROM stu_quests_user WHERE user_id=".$data[id]);
	$db->query("UPDATE stu_quests SET user_id=0,user_time=0 WHERE user_id=".$data[id]);
	// Forschungen
	$db->query("DELETE FROM stu_researched WHERE user_id=".$data[id]);
	// Freigeschaltete Rümpfe
	$db->query("DELETE FROM stu_rumps_user WHERE user_id=".$data[id]);
	// Sektordurchflüge
	$db->query("DELETE FROM stu_sectorflights WHERE user_id=".$data[id]);
	// Schiffe
	$res = $db->query("SELECT id FROM stu_ships WHERE user_id=".$data[id]);
	while($dat=mysql_fetch_assoc($res))
	{
		$db->query("DELETE FROM stu_ships WHERE id=".$dat[id]);
		$db->query("DELETE FROM stu_ships_storage WHERE ships_id=".$dat[id]);
		$db->query("DELETE FROM stu_ships_decloaked WHERE ships_id=".$dat[id]);
		$db->query("DELETE FROM stu_ships_ecalls WHERE ships_id=".$dat[id]);
		$db->query("DELETE FROM stu_ships_subsystems WHERE ships_id=".$dat[id]);
		// Andockrechte SCHIFF
		$db->query("DELETE FROM stu_dockingrights WHERE type='1' AND id=".$dat[id]);
	}
	// Enttarnte Schiffe
	$db->query("DELETE FROM stu_ships_decloaked WHERE user_id=".$data[id]);
	// Schiffe in Bau
	$db->query("DELETE FROM stu_ships_buildprogress WHERE user_id=".$data[id]);
	// Baupläne des Users
	$db->query("DELETE FROM stu_ships_buildplans WHERE user_id=".$data[id]);
	// Kartographierte Systeme
	$db->query("DELETE FROM stu_systems_user WHERE user_id=".$data[id]);
	// Handelsangebote
	$db->query("DELETE FROM stu_trade WHERE  user_id=".$data[id]);
	// Handelswaren
	$db->query("DELETE FROM stu_trade_goods WHERE user_id=".$data[id]);
	// User löschen
	$db->query("DELETE FROM stu_user WHERE id=".$data[id]);
	// Userprofil löschen
	$db->query("DELETE FROM stu_user_profiles WHERe user_id=".$data[id]);
	// NPC-Kontaktlisteneintrag löschen
	$db->query("DELETE FROM stu_npc_contactlist WHERE recipient=".$data['id']);
	// break;
}

//HIJACK SCRIPT FOR STATION DELETE

// $result = $db->query("SELECT a.id as sid,b.id as uid FROM `stu_stations` as a left join stu_user as b on a.user_id = b.id WHERE ISNULL(b.id)");
// while($data=mysql_fetch_assoc($result))
// {
	// if (!$data[uid]) {
		// $db->query("DELETE FROM stu_stations WHERE id=".$data['sid']." LIMIT 1");
		// $db->query("DELETE FROM stu_stations_fielddata WHERE stations_id=".$data['sid']);

		// $db->query("DELETE FROM stu_stations_storage WHERE stations_id=".$data['sid']);
		
	// }
	
// }

?>
