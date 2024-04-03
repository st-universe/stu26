<?php
include_once("/home/stuniverse/webroot/inc/func.inc.php");
include_once("/home/stuniverse/webroot/inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;

$data = $db->query("SELECT cx,cy FROM stu_map WHERE type=1 ORDER BY RAND() LIMIT 1",4);
$db->query("INSERT INTO `stu_ships` (`id`, `user_id`, `rumps_id`, `plans_id`, `fleets_id`, `systems_id`, `cx`, `cy`, `sx`, `sy`, `direction`, `name`, `alvl`, `warp`, `warpcore`, `warpable`, `cloak`, `cloakable`, `eps`, `max_eps`, `batt`, `max_batt`, `huelle`, `max_huelle`, `schilde`,`max_schilde`, `schilde_status`, `lss_range`, `kss_range`, `traktor`, `traktormode`, `dock`, `crew`, `max_crew`, `min_crew`, `nbs`, `lss`, `trumps_id`, `replikator`, `phaser`, `cfield`, `torp_type`, `shuttle_type`, `is_hp`, `points`, `lastmaintainance`, `still`, `maintain`, `batt_wait`,`hud`) VALUES (NULL, '5', '999', '1', '0', '0', '".$data['cx']."', '".$data['cy']."', '0', '0', NULL, 'Kubus Adv-".date("j")."', '1', '0', '0', NULL, '0', NULL, '0', '0', '0', '0', '10', '10', '0', '0', '0', '0', '0', '0', NULL, '0', '0', '0', '0', NULL, NULL, '0', NULL, '0', '0', '0', '', '0', '', '', '', '', '', '1')");
?>
