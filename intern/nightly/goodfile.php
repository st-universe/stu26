<?php
include_once("/srv/www/stu_sys/webroot/inc/config.inc.php");
include_once($global_path."/inc/func.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;

$fd = '<?php
function getgoodname($id)
{
	switch($id)
	{
';

$result = $db->query("SELECT goods_id,name FROM stu_goods ORDER BY sort");
while($data=mysql_fetch_assoc($result))
{
	$fd .= '		case '.$data['goods_id'].':
			return "'.stripslashes($data['name']).'";
';
}

$fd .= '	}
}
?>';
unlink($global_path."/inc/lists/goods.php");
$fp = fopen($global_path."/inc/lists/goods.php","a+");
fwrite($fp,$fd);
fclose($fp);
?>
