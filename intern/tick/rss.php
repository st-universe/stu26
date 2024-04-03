<?php
include_once("/srv/www/stu_sys/webroot/inc/config.inc.php");
include_once($global_path . "inc/func.inc.php");
include_once($global_path . "class/db.class.php");
$db = new db;

/* vim: set expandtab tabstop=4 shiftwidth=4: */
//+----------------------------------------------------------------------+
//| WAMP (XP-SP1/1.3.33/5.0.15/5.1.0)                                   |
//+----------------------------------------------------------------------+
//| Copyright(c) 2001-2006 Michael Wimmer                                |
//+----------------------------------------------------------------------+
//| I don't have the time to read through all the licences to find out   |
//| what the exactly say. But it's simple. It's free for non commercial  |
//| projects, but as soon as you make money with it, i want my share :-) |
//+----------------------------------------------------------------------+
//| Authors: Michael Wimmer <flaimo@gmx.net>                             |
//+----------------------------------------------------------------------+
//
// $Id$

/**
 * @package AtomBuilder
 * @category FLP
 * @filesource
 */
//error_reporting(E_ALL);
unlink($global_path . "/static/atom100---Star-Trek-Universe-KN-Feed.xml");

ob_start();
include_once $global_path . 'inc/atom/class.AtomBuilder.inc.php';

/* create new feed object with the required informations: title, url to feed, id of the feed */
$atom = new AtomBuilder('Star Trek Universe KN-Feed', 'http://www.stuniverse.de', 'tag:stuniverse.de,' . date("Y-m-d", time()) . ':/kn');
$atom->setEncoding('UTF-8'); // only needed if NOT utf-8
$atom->setLanguage('de'); // recommended, but not required
$atom->setSubtitle(utf8_encode('Der allt�gliche Wahnsinn')); //optional
$atom->setRights('2006 © stuniverse.de'); //optional
$atom->setUpdated(date('c', time())); // required !! last time the feed or one of it's entries was last modified/updated; in php5 you can use date('c', $timestamp) to generate a valid date
$atom->setAuthor('Star Trek Universe', 'info@changeme.de', 'http://www.stuniverse.de'); // name required, email and url are optional
$atom->setIcon('http://www.stuniverse.de/gfx/favicon.ico'); // optional
$atom->addContributor('Changeme', 'Changeme@Changeme.de');

/* you can add a lot of different links to the feed and it's entries, see intertwingly.net/wiki/pie/ for more infos */
$atom->addLink('http://www.stuniverse.de/', 'Star Trek Universe', 'alternate', 'text/html', 'de');

$result = $db->query("SELECT a.*,UNIX_TIMESTAMP(a.date) as date_tsp,b.user FROM stu_kn as a LEFT JOIN stu_user as b ON a.user_id=b.id ORDER BY a.date DESC LIMIT 15");
while ($data = mysql_fetch_assoc($result)) {

	$entry_1 = $atom->newEntry(utf8_encode(strip_tags(stripslashes($data[user]))) . " - " . (!$data[titel] ? "Kein Titel" : utf8_encode(strip_tags(stripslashes($data[titel])))), 'http://www.stuniverse.de/static/skn.php?id=' . $data[id], 'tag:stuniverse.de,' . date("Y-m-d", $data[date_tsp]) . ':/static/skn.php?id=' . $data[id] . ''); // required infos: title, link, id
	$entry_1->setUpdated(date('c', $data[date_tsp])); // required (last modified/updated)
	$entry_1->setAuthor(utf8_encode(strip_tags(stripslashes($data[user])))); // name required, email and url are optional
	$entry_1->setRights('2006 © stuniverse.de'); // optional
	$entry_1->setContent(utf8_encode(nl2br(stripslashes($data[text]))), 'html');
	$entry_1->addContributor('Changeme', 'Changeme@Changeme.de');
	$atom->addEntry($entry_1); // add the created entry to the feed
}

$version = '1.0.0'; // 1.0 is the only version so far
//$atom->outputAtom($version);

// saves the xml file to the given path and returns the path + filename as a string
$path = $global_path . 'static/';
$atom->saveAtom($version, $path);


ob_end_flush();