<?php
include_once("/srv/www/stu_sys/webroot/inc/config.inc.php");

$verzeichnis = dir($global_path."/intern/backup");
while($datei = $verzeichnis->read())
{
	if (is_file($global_path."/intern/backup/".$datei) && filectime($global_path."/intern/backup/".$datei) < time()-2592000) unlink($global_path."/intern/backup/".$datei);
}
$verzeichnis->close();


system("/usr/bin/mysqldump -u".$dbd[user]." -p".$dbd[pass]." -hlocalhost ".$dbd[database]." --default-character-set=latin1 | gzip > ".$global_path."/intern/backup/".date("d-m-Y",time()).".gz");
?>
