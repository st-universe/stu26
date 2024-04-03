<?php
include_once("/srv/www/stu_sys/webroot/inc/config.inc.php");
include_once($global_path."/inc/func.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;

$x = $mapfields['max_x']*4;
$y = $mapfields['max_y']*4;

//$smile=imagecreate($x,$y);
$smile = imagecreatefrompng($global_path."/intern/gfx/taktik.png");

$im1 = imagecreatetruecolor($x,$y);
$im2 = imagecreatetruecolor($x,$y);
$im3 = imagecreatetruecolor($x,$y);
$im4 = imagecreatetruecolor($x,$y);
$im5 = imagecreatetruecolor($x,$y);
//imagedestroy($smile);

imagecopy($im1,$smile,0,0,0,0,$x,$y);
imagecopy($im2,$smile,0,0,0,0,$x,$y);
imagecopy($im3,$smile,0,0,0,0,$x,$y);
imagecopy($im4,$smile,0,0,0,0,$x,$y);
imagecopy($im5,$smile,0,0,0,0,$x,$y);

$foed = imagecolorallocate($im1,0,66,216);
$rom = imagecolorallocate($im2, 32, 171, 0);
$kling = imagecolorallocate($im3, 151, 0, 0);
$card = imagecolorallocate($im4, 211, 209, 0);
$ferg = imagecolorallocate($im5, 241, 119, 0);



// Föd
$result = $db->query("SELECT COUNT(b.id) as cn,b.cx,b.cy FROM stu_npc_contactlist as a LEFT JOIN stu_ships as b ON b.user_id=a.recipient WHERE (a.user_id=10 AND a.rkn='1') OR b.user_id=10 GROUP BY b.cx,b.cy");
while($data=mysql_fetch_assoc($result))
{
	if ($data['cn'] < 10) continue;
	$col = $foed;
	$data['cx'] -= 1;
	$data['cy'] -= 1;
	imagesetpixel($im1,$data['cx']*4,$data['cy']*4,$col);
	imagesetpixel($im1,$data['cx']*4,$data['cy']*4+1,$col);
	imagesetpixel($im1,$data['cx']*4,$data['cy']*4+2,$col);
	imagesetpixel($im1,$data['cx']*4,$data['cy']*4+3,$col);
	
	imagesetpixel($im1,$data['cx']*4+1,$data['cy']*4,$col);
	imagesetpixel($im1,$data['cx']*4+1,$data['cy']*4+1,$col);
	imagesetpixel($im1,$data['cx']*4+1,$data['cy']*4+2,$col);
	imagesetpixel($im1,$data['cx']*4+1,$data['cy']*4+3,$col);
	
	imagesetpixel($im1,$data['cx']*4+2,$data['cy']*4,$col);
	imagesetpixel($im1,$data['cx']*4+2,$data['cy']*4+1,$col);
	imagesetpixel($im1,$data['cx']*4+2,$data['cy']*4+2,$col);
	imagesetpixel($im1,$data['cx']*4+2,$data['cy']*4+3,$col);
	
	imagesetpixel($im1,$data['cx']*4+3,$data['cy']*4,$col);
	imagesetpixel($im1,$data['cx']*4+3,$data['cy']*4+1,$col);
	imagesetpixel($im1,$data['cx']*4+3,$data['cy']*4+2,$col);
	imagesetpixel($im1,$data['cx']*4+3,$data['cy']*4+3,$col);

}
unlink($global_path."/npc/gfx/tac1.png");
imagepng($im1,$global_path."/npc/gfx/tac1.png");

// Rom
$result = $db->query("SELECT COUNT(b.id) as cn,b.cx,b.cy FROM stu_npc_contactlist as a LEFT JOIN stu_ships as b ON b.user_id=a.recipient WHERE (a.user_id=11 AND a.rkn='1') OR b.user_id=11 GROUP BY b.cx,b.cy");
while($data=mysql_fetch_assoc($result))
{
	if ($data['cn'] < 10) continue;
	$col = $rom;
	$data['cx'] -= 1;
	$data['cy'] -= 1;
	imagesetpixel($im2,$data['cx']*4,$data['cy']*4,$col);
	imagesetpixel($im2,$data['cx']*4,$data['cy']*4+1,$col);
	imagesetpixel($im2,$data['cx']*4,$data['cy']*4+2,$col);
	imagesetpixel($im2,$data['cx']*4,$data['cy']*4+3,$col);
	
	imagesetpixel($im2,$data['cx']*4+1,$data['cy']*4,$col);
	imagesetpixel($im2,$data['cx']*4+1,$data['cy']*4+1,$col);
	imagesetpixel($im2,$data['cx']*4+1,$data['cy']*4+2,$col);
	imagesetpixel($im2,$data['cx']*4+1,$data['cy']*4+3,$col);
	
	imagesetpixel($im2,$data['cx']*4+2,$data['cy']*4,$col);
	imagesetpixel($im2,$data['cx']*4+2,$data['cy']*4+1,$col);
	imagesetpixel($im2,$data['cx']*4+2,$data['cy']*4+2,$col);
	imagesetpixel($im2,$data['cx']*4+2,$data['cy']*4+3,$col);
	
	imagesetpixel($im2,$data['cx']*4+3,$data['cy']*4,$col);
	imagesetpixel($im2,$data['cx']*4+3,$data['cy']*4+1,$col);
	imagesetpixel($im2,$data['cx']*4+3,$data['cy']*4+2,$col);
	imagesetpixel($im2,$data['cx']*4+3,$data['cy']*4+3,$col);

}
unlink($global_path."/npc/gfx/tac2.png");
imagepng($im2,$global_path."/npc/gfx/tac2.png");

// kling
$result = $db->query("SELECT COUNT(b.id) as cn,b.cx,b.cy FROM stu_npc_contactlist as a LEFT JOIN stu_ships as b ON b.user_id=a.recipient WHERE (a.user_id=12 AND a.rkn='1') OR b.user_id=12 GROUP BY b.cx,b.cy");
while($data=mysql_fetch_assoc($result))
{
	if ($data['cn'] < 10) continue;
	$col = $kling;
	$data['cx'] -= 1;
	$data['cy'] -= 1;
	imagesetpixel($im3,$data['cx']*4,$data['cy']*4,$col);
	imagesetpixel($im3,$data['cx']*4,$data['cy']*4+1,$col);
	imagesetpixel($im3,$data['cx']*4,$data['cy']*4+2,$col);
	imagesetpixel($im3,$data['cx']*4,$data['cy']*4+3,$col);
	
	imagesetpixel($im3,$data['cx']*4+1,$data['cy']*4,$col);
	imagesetpixel($im3,$data['cx']*4+1,$data['cy']*4+1,$col);
	imagesetpixel($im3,$data['cx']*4+1,$data['cy']*4+2,$col);
	imagesetpixel($im3,$data['cx']*4+1,$data['cy']*4+3,$col);
	
	imagesetpixel($im3,$data['cx']*4+2,$data['cy']*4,$col);
	imagesetpixel($im3,$data['cx']*4+2,$data['cy']*4+1,$col);
	imagesetpixel($im3,$data['cx']*4+2,$data['cy']*4+2,$col);
	imagesetpixel($im3,$data['cx']*4+2,$data['cy']*4+3,$col);
	
	imagesetpixel($im3,$data['cx']*4+3,$data['cy']*4,$col);
	imagesetpixel($im3,$data['cx']*4+3,$data['cy']*4+1,$col);
	imagesetpixel($im3,$data['cx']*4+3,$data['cy']*4+2,$col);
	imagesetpixel($im3,$data['cx']*4+3,$data['cy']*4+3,$col);

}

unlink($global_path."/npc/gfx/tac3.png");
imagepng($im3,$global_path."/npc/gfx/tac3.png");

// Card
$result = $db->query("SELECT COUNT(b.id) as cn,b.cx,b.cy FROM stu_npc_contactlist as a LEFT JOIN stu_ships as b ON b.user_id=a.recipient WHERE (a.user_id=13 AND a.rkn='1') OR b.user_id=13 GROUP BY b.cx,b.cy");
while($data=mysql_fetch_assoc($result))
{
	if ($data['cn'] < 10) continue;
	$col = $card;
	$data['cx'] -= 1;
	$data['cy'] -= 1;
	imagesetpixel($im4,$data['cx']*4,$data['cy']*4,$col);
	imagesetpixel($im4,$data['cx']*4,$data['cy']*4+1,$col);
	imagesetpixel($im4,$data['cx']*4,$data['cy']*4+2,$col);
	imagesetpixel($im4,$data['cx']*4,$data['cy']*4+3,$col);
	
	imagesetpixel($im4,$data['cx']*4+1,$data['cy']*4,$col);
	imagesetpixel($im4,$data['cx']*4+1,$data['cy']*4+1,$col);
	imagesetpixel($im4,$data['cx']*4+1,$data['cy']*4+2,$col);
	imagesetpixel($im4,$data['cx']*4+1,$data['cy']*4+3,$col);
	
	imagesetpixel($im4,$data['cx']*4+2,$data['cy']*4,$col);
	imagesetpixel($im4,$data['cx']*4+2,$data['cy']*4+1,$col);
	imagesetpixel($im4,$data['cx']*4+2,$data['cy']*4+2,$col);
	imagesetpixel($im4,$data['cx']*4+2,$data['cy']*4+3,$col);
	
	imagesetpixel($im4,$data['cx']*4+3,$data['cy']*4,$col);
	imagesetpixel($im4,$data['cx']*4+3,$data['cy']*4+1,$col);
	imagesetpixel($im4,$data['cx']*4+3,$data['cy']*4+2,$col);
	imagesetpixel($im4,$data['cx']*4+3,$data['cy']*4+3,$col);

}

unlink($global_path."/npc/gfx/tac4.png");
imagepng($im4,$global_path."/npc/gfx/tac4.png");

// Ferg
$result = $db->query("SELECT COUNT(b.id) as cn,b.cx,b.cy FROM stu_npc_contactlist as a LEFT JOIN stu_ships as b ON b.user_id=a.recipient WHERE (a.user_id=14 AND a.rkn='1') OR b.user_id=14 GROUP BY b.cx,b.cy");
while($data=mysql_fetch_assoc($result))
{
	if ($data['cn'] < 10) continue;
	$col = $ferg;
	$data['cx'] -= 1;
	$data['cy'] -= 1;
	imagesetpixel($im5,$data['cx']*4,$data['cy']*4,$col);
	imagesetpixel($im5,$data['cx']*4,$data['cy']*4+1,$col);
	imagesetpixel($im5,$data['cx']*4,$data['cy']*4+2,$col);
	imagesetpixel($im5,$data['cx']*4,$data['cy']*4+3,$col);
	
	imagesetpixel($im5,$data['cx']*4+1,$data['cy']*4,$col);
	imagesetpixel($im5,$data['cx']*4+1,$data['cy']*4+1,$col);
	imagesetpixel($im5,$data['cx']*4+1,$data['cy']*4+2,$col);
	imagesetpixel($im5,$data['cx']*4+1,$data['cy']*4+3,$col);
	
	imagesetpixel($im5,$data['cx']*4+2,$data['cy']*4,$col);
	imagesetpixel($im5,$data['cx']*4+2,$data['cy']*4+1,$col);
	imagesetpixel($im5,$data['cx']*4+2,$data['cy']*4+2,$col);
	imagesetpixel($im5,$data['cx']*4+2,$data['cy']*4+3,$col);
	
	imagesetpixel($im5,$data['cx']*4+3,$data['cy']*4,$col);
	imagesetpixel($im5,$data['cx']*4+3,$data['cy']*4+1,$col);
	imagesetpixel($im5,$data['cx']*4+3,$data['cy']*4+2,$col);
	imagesetpixel($im5,$data['cx']*4+3,$data['cy']*4+3,$col);

}

unlink($global_path."/npc/gfx/tac5.png");
imagepng($im5,$global_path."/npc/gfx/tac5.png");
?>
