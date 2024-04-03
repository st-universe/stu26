<?php
include_once("/srv/www/stu_sys/webroot/inc/config.inc.php");
include_once($global_path."/inc/func.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;
@session_start();



//    Output handler
function output_handler($img) {
    header('Content-type: image/png');
    header('Content-Length: ' . strlen($img));
    return $img;
}




if ($_SESSION['login'] != 1 || $_SESSION['allys_id'] == 0) exit;



$smile = imagecreatefrompng($global_path."/intern/gfx/sectormap.png");

$red = imagecolorallocate($smile,255,0,0);



function set_pixel($x,$y,&$c)
{
	global $smile;
	imagesetpixel($smile,$x*3,$y*3,$c);
	imagesetpixel($smile,$x*3,$y*3+1,$c);
	imagesetpixel($smile,$x*3,$y*3+2,$c);
	
	imagesetpixel($smile,$x*3+1,$y*3,$c);
	imagesetpixel($smile,$x*3+1,$y*3+1,$c);
	imagesetpixel($smile,$x*3+1,$y*3+2,$c);
	
	imagesetpixel($smile,$x*3+2,$y*3,$c);
	imagesetpixel($smile,$x*3+2,$y*3+1,$c);
	imagesetpixel($smile,$x*3+2,$y*3+2,$c);
}


$result = $db->query("SELECT a.cx,a.cy FROM stu_ships as a LEFT JOIN stu_user as b ON a.user_id=b.id WHERE b.allys_id=".$_SESSION['allys_id']);
while($data=mysql_fetch_assoc($result))
{
	imagesetpixel($smile,$data['cx']*3,$data['cy']*3,$red);
	imagesetpixel($smile,$data['cx']*3,$data['cy']*3+1,$red);
	imagesetpixel($smile,$data['cx']*3,$data['cy']*3+2,$red);
	
	imagesetpixel($smile,$data['cx']*3+1,$data['cy']*3,$red);
	imagesetpixel($smile,$data['cx']*3+1,$data['cy']*3+1,$red);
	imagesetpixel($smile,$data['cx']*3+1,$data['cy']*3+2,$red);
	
	imagesetpixel($smile,$data['cx']*3+2,$data['cy']*3,$red);
	imagesetpixel($smile,$data['cx']*3+2,$data['cy']*3+1,$red);
	imagesetpixel($smile,$data['cx']*3+2,$data['cy']*3+2,$red);
	

}
if ($_GET['cx'] && $_GET['cy'])
{
	$yel = imagecolorallocate($smile,255,255,0);
	set_pixel($_GET['cx'],$_GET['cy'],$yel);
}
//    Image output
ob_start("output_handler");
imagepng($smile);
ob_end_flush();
?>
