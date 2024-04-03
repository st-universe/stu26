<?php
include_once("/srv/web/stu_sys/webroot/inc/config.inc.php");
include_once($global_path."/inc/func.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;



//    Output handler
function output_handler($img) {
    header('Content-type: image/png');
    header('Content-Length: ' . strlen($img));
    return $img;
}

$smile = imagecreatefrompng($global_path."/intern/gfx/npcrpg-karte.png");

$red = imagecolorallocate($smile,255,0,0);

/*
$bla[1] = imagecreatefromgif($global_path."gfx/rassen/1s.gif");
$bla[2] = imagecreatefromgif($global_path."gfx/rassen/2s.gif");
$bla[3] = imagecreatefromgif($global_path."gfx/rassen/3s.gif");
$bla[4] = imagecreatefromgif($global_path."gfx/rassen/4s.gif");
$bla[5] = imagecreatefromgif($global_path."gfx/rassen/5s.gif");
*/

$bla[1] = imagecreatefrompng($global_path."npc/gfx/1.png");
$bla[2] = imagecreatefrompng($global_path."npc/gfx/2.png");
$bla[3] = imagecreatefrompng($global_path."npc/gfx/3.png");
$bla[4] = imagecreatefrompng($global_path."npc/gfx/4.png");
$bla[5] = imagecreatefrompng($global_path."npc/gfx/5.png");

function set_pixel($x,$y,$race)
{
	global $smile,$bla;
	imagecopy($smile,$bla[$race],($x-25),($y-25),1,1,49,49);
}


$result = $db->query("SELECT a.cx,a.cy,b.race FROM stu_ships as a LEFT JOIN stu_user as b ON b.id=a.user_id WHERE a.is_rkn>0 AND b.race<6");
while($data=mysql_fetch_assoc($result))
{
	set_pixel($data['cx']*5,$data['cy']*5,$data['race']);

}
//    Image output
ob_start("output_handler");
imagepng($smile);
ob_end_flush();
?>
