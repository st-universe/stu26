<?php
include_once("/srv/web/stu_sys/webroot/inc/config.inc.php");
include_once($global_path."/inc/func.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;

function drawoffergood($first = 0)
{
    $draw = rand(1,14);
    switch($draw)
    {
        case  1: $good =  2; break;
        case  2: $good =  3; break;
        case  3: $good =  4; break;
        case  4: $good =  6; break;
        case  5: $good =  7; break;
        case  6: $good =  8; break;
        case  7: $good =  9; break;
        case  8: $good = 30; break;
        case  9: $good = 31; break;
        case 10: $good = 32; break;
        case 11: $good = 33; break;
        case 12: $good = 34; break;
        case 13: $good = 35; break;
        case 14: $good = 40; break;
    }
    if ($good == $first)
    {
        if ($good == 8) $good = 6;
        else $good= 8;
    }
    return $good;

}

$timeadd = rand(3,13);

$offermod = rand(901,910);
if ($offermod >= 909) $offermod = 906;
$offergood1 = drawoffergood();
$offergood2 = drawoffergood($offergood1);
$db->query("INSERT INTO stu_trade_ferg (user_id,give_good,give_count,date,want_good1,want_good2) VALUES ('23','".$offermod."','1',NOW() - INTERVAL ".$timeadd." HOUR,'".$offergood1."','".$offergood2."')");

// Jetzt das Wrack
$data = $db->query("SELECT * FROM stu_sys_map WHERE (type < 7 OR type = 11 OR type=12) ORDER BY RAND() LIMIT 1",4);
$insertid = $db->query("INSERT INTO stu_ships (id, user_id, rumps_id, plans_id, fleets_id, systems_id, cx, cy, sx, sy, direction, name, alvl, warp, warpcore, warpable, cloak, cloakable, eps, max_eps, batt, max_batt, huelle, max_huelle, schilde, max_schilde, schilde_status, lss_range, kss_range, traktor, traktormode, dock, crew, max_crew, min_crew, nbs, lss, trumps_id, replikator, phaser, cfield, torp_type, shuttle_type, is_hp, points, lastmaintainance, still, maintain, batt_wait, hud) VALUES ('".$insertid."', '1', '8', '23', '0', '".$data['systems_id']."', '0', '0', '".$data['sx']."', '".$data['sy']."', NULL, 'Wrack', '1', '0', '0', NULL, '0', NULL, '0', '0', '0', '0', '15', '15', '0', '0', '0', '0', '0', '0', NULL, '0', '0', '0', '0', NULL, NULL, '8', NULL, '0', '0', '0', '', '0', '', '', '', '', '', '1')",5);
$wrackmod = rand(901,908);
$db->query("INSERT INTO stu_ships_storage (ships_id,goods_id,count) VALUES ('".$insertid."','".$wrackmod ."','1')");



























?>
