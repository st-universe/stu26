<?php
// Including necessary files
include_once("inc/config.inc.php");
include_once("class/db.class.php");
include_once("class/japi.class.php");

// Check if there is a need to wake up the silly class. Maybe we'll stop right here. 
// Very simple - should block simple attempts
//if (!$_GET['action']) exit;

//error_reporting(E_ALL);

// Initiating classes
$db = new db;
$japi = new japi;

if ($_GET["en"])
{
	$string = serialize(array("mode" => "getUserInfo","uid" => $_GET['id'],"pwd" => $_GET['pw']));
	$japi->encrypt_data($string);
	echo $japi->urlsafe_b64encode(trim($japi->encrypted_data));
	echo "<br><br>";
	echo trim($japi->encrypted_data);
	exit;
}

// Enabling Debug
$japi->debug_toggle_state();

// OK first we have to know, wahts going on. decrypting requestdata
$japi->decrypt_data($_GET['action']);

// Performing security check
$japi->verify_data();

// Switch for requestmode
switch($japi->decrypted_data['mode'])
{
	case "getUserInfo":
		$japi->debug_message("Requesting Userinfo");
		$japi->get_user_info();
		break;
	case "getAllyInfo":
		$japi->debug_message("Requesting Allyinfo");
		$japi->get_ally_info();
		break;
	default:
		exit;
}
?>