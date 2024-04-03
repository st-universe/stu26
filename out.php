<?php
include_once("inc/func.inc.php");
include_once("inc/config.inc.php");
include_once("class/db.class.php");
$db = new db;
if (!check_int($_GET[ai])) exit;
$hp = $db->query("SELECT homepage FROM stu_allylist WHERE allys_id=".$_GET[ai]." LIMIT 1",1);
?>
<html>
<head>
<meta http-equiv="refresh" content="3; URL=<?php echo $hp; ?>">
<title>Star Trek Universe</title>
</head>
<body>
<h3>Einen Moment bitte...du wirst weitergeleitet...</h3>
</body>
</html>