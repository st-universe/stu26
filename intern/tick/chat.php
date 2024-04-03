<?php
include_once("/srv/www/stu_sys/webroot/inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;
$fp = fopen("http://webeye.euirc.net/infopanel/?request=stu&key=jsadf","r");  
$fp_value = fread($fp,32768);  
$fp_value = preg_replace("°[^a-z] = °",'" => ',$fp_value);
fclose($fp);
eval ($fp_value);
eval ($fp_who_value);
$db->query("UPDATE stu_game_vars SET value='".$irc_info[user]."' WHERE var='chat'");
?>
