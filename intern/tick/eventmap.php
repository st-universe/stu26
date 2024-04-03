<?php
include_once("/srv/www/stu_sys/webroot/inc/config.inc.php");
include_once($global_path."/inc/func.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;

$x = $mapfields['max_x']*3+2;
$y = $mapfields['max_y']*3+2;

function getcolor($hex)
{
	global $smile;
	$int = sscanf($hex, '#%2x%2x%2x');
	return imagecolorallocate($smile,$int[0],$int[1],$int[2]);
}

$smile=imagecreatefrompng($global_path."/intern/gfx/sectormap.png");
$kek=imagecolorallocate($smile,0,0,0);
$red=imagecolorallocate($smile,255,0,0);
$grey=imagecolorallocate($smile, 192, 192, 192);
$green = imagecolorallocate($smile, 32, 171, 0);

$foed = getcolor($db->query("SELECT darker_color FROM stu_factions WHERE faction_id=1 LIMIT 1",1));
$rom = getcolor($db->query("SELECT darker_color FROM stu_factions WHERE faction_id=2 LIMIT 1",1));
$kling = getcolor($db->query("SELECT darker_color FROM stu_factions WHERE faction_id=3 LIMIT 1",1));
$card = getcolor($db->query("SELECT darker_color FROM stu_factions WHERE faction_id=4 LIMIT 1",1));
$ferg = getcolor($db->query("SELECT darker_color FROM stu_factions WHERE faction_id=5 LIMIT 1",1));
$verek = getcolor($db->query("SELECT darker_color FROM stu_factions WHERE faction_id=7 LIMIT 1",1));
$kessok = getcolor($db->query("SELECT darker_color FROM stu_factions WHERE faction_id=8 LIMIT 1",1));
$um = getcolor($db->query("SELECT darker_color FROM stu_factions WHERE faction_id=9 LIMIT 1",1));

$hp = imagecolorallocate($smile,255,255,0);

imagefill($smile,0,0,$kek);


function set_pixel($x,$y,&$c)
{
	global $smile;
	imagesetpixel($smile,$x*3,$y*3,$c);
	imagesetpixel($smile,$x*3,$y*3+1,$c);
	imagesetpixel($smile,$x*3,$y*3+2,$c);
	
	imagesetpixel($smile,$x*3+1,$y*3,$c);
	#imagesetpixel($smile,$x*3+1,$y*3+1,$c);
	imagesetpixel($smile,$x*3+1,$y*3+2,$c);
	
	imagesetpixel($smile,$x*3+2,$y*3,$c);
	imagesetpixel($smile,$x*3+2,$y*3+1,$c);
	imagesetpixel($smile,$x*3+2,$y*3+2,$c);
}

#imagerectangle($smile,0,0,$x-1,$y-1,$grey);
// Grenzen
$result = $db->query("SELECT cx,cy,faction_id FROM stu_map WHERE faction_id>0");
while($data=mysql_fetch_assoc($result))
{
	switch ($data['faction_id'])
	{
		case 1:
			$col = $foed;
			break;
		case 2:
			$col = $rom;
			break;
		case 3:
			$col = $kling;
			break;
		case 4:
			$col = $card;
			break;
		case 5:
			$col = $ferg;
			break;
		case 7:
			$col = $verek;
			break;
		case 8:
			$col = $kessok;
			break;
		case 9:
			$col = $um;
			break;
	}
	set_pixel($data['cx'],$data['cy'],$col);

}

// HPs
$result = $db->query("SELECT cx,cy FROM stu_ships WHERE is_hp='1' AND user_id != 20");
while($data=mysql_fetch_assoc($result))
{
	set_pixel($data['cx'],$data['cy'],$hp);
}

$result = $db->query("SELECT type,coords_x,coords_y FROM stu_history WHERE coords_x>0 AND coords_y>0 AND UNIX_TIMESTAMP(date)>".(time()-86400));
while($data=mysql_fetch_assoc($result))
{
	if ($data['type'] == 1) $col = $red;
	if ($data['type'] == 4) $col = $green;
	
	imagesetpixel($smile,$data['coords_x']*3,$data['coords_y']*3,$col);
	imagesetpixel($smile,$data['coords_x']*3,$data['coords_y']*3+1,$col);
	imagesetpixel($smile,$data['coords_x']*3,$data['coords_y']*3+2,$col);
	
	imagesetpixel($smile,$data['coords_x']*3+1,$data['coords_y']*3,$col);
	imagesetpixel($smile,$data['coords_x']*3+1,$data['coords_y']*3+1,$col);
	imagesetpixel($smile,$data['coords_x']*3+1,$data['coords_y']*3+2,$col);
	
	imagesetpixel($smile,$data['coords_x']*3+2,$data['coords_y']*3,$col);
	imagesetpixel($smile,$data['coords_x']*3+2,$data['coords_y']*3+1,$col);
	imagesetpixel($smile,$data['coords_x']*3+2,$data['coords_y']*3+2,$col);
	

}
unlink($global_path."/gfx/graph/eventmap.png");
imagepng($smile,$global_path."/gfx/graph/eventmap.png");
?>
