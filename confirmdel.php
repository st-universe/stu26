<?php
include_once("inc/func.inc.php");
include_once("inc/config.inc.php");
include_once("class/db.class.php");
$db = new db;
session_start();
include_once("class/sess.class.php");
$sess = new sess;
include_once("class/user.class.php");
$u = new user;
if ((!$_GET[user] || !check_int($_GET[user]) || $_GET[user] < 100) && !is_array($_POST)) die(show_error(902));
$u->checkdelstate(check_int($_GET[user]) ? $_GET[user] : $_POST[user]);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>Star Trek Universe - Accountlöschung</title>
</head>
<link rel="STYLESHEET" type="text/css" href=main/gfx/style.css>
<body>
<table class="totab" cellspacing="0" cellpadding="0">
<tr>
	<td height="80"><img src=main/gfx/banner.jpg></td>
</tr>
<tr>
	<td valign="top" class="ml">/ <b>Accountlöschung</b></td>
</tr>
<?php
if (is_string($_POST[pass]) && check_int($_POST[user]))
{
	$u->confirmdel($_POST);
	echo "<tr><td valign=\"top\">Die Löschung wurde bestätigt und kann nicht mehr rückgängig gemacht werden</td></tr></table></body></html>";
	exit;
}
?>
<form action="confirmdel.php" method="post">
<input type="hidden" name="user" value="<?php echo $_GET[user]; ?>">
<tr>
	<td valign="top">Bitte das Passwort des zu löschenden Accounts eingeben <input type="password" size="10" name="pass" class="text"> <input type="submit" value="Bestätigen" class="button"></td>
</tr>
</table>
</form>
</body>
</html>