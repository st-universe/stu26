<?php

ini_set(default_charset, "");

@session_start();
if ($_POST)
{
	foreach($_POST as $key => $value)
	{
		$_GET[$key] = $value;
	}
}
if ($_GET['s'] == "sb" && $_GET['p'] == "comm" && $_GET['snu'])
{
	$_GET['s'] = "knu";
	$_GET['ukn'] = $_GET['snu'];
}
include_once("inc/func.inc.php");
include_once("inc/config.inc.php");
include_once("inc/inputfilter.inc.php");
include_once("class/db.class.php");
$db = new db;
if ($eId) show_error();
include_once("class/sess.class.php");
$sess = new sess;
include_once("class/user.class.php");
$u = new user;
include_once("class/qpm.class.php");
$qpm = new qpm;
include_once("class/game.class.php");
$g = new game;

// Login + Überprüfung durchführen
if (!$_POST['login'] && !$_POST['pass'] && $_SESSION['login'] != 1) header("Location: http://www.stuniverse.de");
elseif ($_POST['login'] && $_POST['pass'])
{
	$eId = $sess->login();
	@session_start();
	$u = new user;
	$qpm = new qpm;
	$g = new game;
}
if ($eId != 0) show_error();

if ($_SESSION['login'] != 1) die(show_error(106));

$gfx = $_SESSION['gfx_path'];
if ($_GET['p'] == "logout") $sess->logout();
if ($eId) show_error();
if ($_GET['p'] == "colony" && $_GET['ac'] == "sba" && $_GET['show_x'] && check_int($_GET['shd']))
{
	$tda = $db->query("SELECT a.user_id,b.slots FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) WHERE a.id=".$_GET['shd']." LIMIT 1",4);
	if ($tda['user_id'] == $_SESSION['uid'])
	{
		if ($tda['slots'] == 0) { $_GET['p'] = "ship"; $_GET['s'] = "ss"; $_GET['id'] = $_GET['shd']; }
		else  { $_GET['p'] = "stat"; $_GET['s'] = "ss"; $_GET['id'] = $_GET['shd']; }
	}
	else $_GET['s'] = "sc";
}
if ($_GET['s'] == "ase" && !check_int($_GET['shd']) && $_GET['wtd'] != "sl") $_GET['s'] = "sc";

// Spielstatus abfragen
$gs = $db->query("SELECT value FROM stu_game_vars WHERE var='state' LIMIT 1",1);
if ($gs == 2) die(show_error(900));
if ($gs == 3 && $_SESSION['uid'] > 102 && $_SESSION['uid'] != 199) die(show_error(901));

// NPC-Klasse von vornerein includieren
if ($_SESSION['uid'] < 100)
{
	include_once("class/npc.class.php");
	$npc = new npc;
}
switch($_GET['p'])
{
	default:
		$inc = "desk.php";
	case "main":
		$inc = "desk.php";
		break;
	case "ship":
		$inc = "ship.php";
		break;
	case "station":
		$inc = "stations.php";
		break;
	case "colony":
		$inc = "colony.php";
		break;
	case "colony2":
		$inc = "colony2.php";
		break;
	case "comm":
		$inc = "comm.php";
		break;
	case "stat":
		$inc = "station.php";
		break;
	case "research":
		$inc = "research.php";
		break;
	case "trade":
		$inc = "trade.php";
		break;
	case "history":
		$inc = "history.php";
		break;
	case "ally":
		$inc = "ally.php";
		break;
	case "db":
		$inc = "db.php";
		break;
	case "nagus":
		$inc = "nagusdeals.php";
		break;
	case "fergp":
		$inc = "fergpost.php";
		break;
	case "log":
		$inc = "logbook.php";
		break;
	case "demography":
		$inc = "demography.php";
		break;
	case "conflict":
		$inc = "conflict.php";
		break;
	case "opt":
		$inc = "options.php";
		if ($_GET['vac'] == 1 && $_SESSION['vac_possible'] > 0)
		{
			$db->query("UPDATE stu_user SET vac_blocktime=".(time()+10800).",vac_possible='".($_SESSION['vac_possible']-1)."' WHERE id=".$_SESSION['uid']." LIMIT 1");
			$db->query("UPDATE stu_ships SET alvl='1' WHERE user_id=".$_SESSION['uid']);
			die(show_error(903));
		}
		break;
	case "map":
		$inc = "starmap.php";
		break;
	case "npc":
		if ($_SESSION['npc_type'] != 1 && $_SESSION['npc_type'] != 2 && $_SESSION['npc_type'] != 3) die(show_error(902));
		$inc = "npc.php";
		break;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
	<title>Star Trek Universe</title>
<script LANGUAGE='JavaScript' type='text/javascript'>
var Win = null;
function cp(objekt,datei) { <?php echo "document.images[objekt].src = \"".$gfx."/\" + datei + \".gif\"";?> }
var digital = new Date( "<?php echo date("M, d Y G:i:s") ?>");
function clock() {
	if(!document.all && !document.getElementById) return;
	if (!document.getElementById("uhrzeit")) return;
	var hours   = digital.getHours();
	var minutes = digital.getMinutes();
	var seconds = digital.getSeconds();
	digital.setSeconds( seconds+1 );
	if(hours <= 9) hours = "0" + hours;
	if(minutes <= 9) minutes = "0" + minutes;
	if(seconds <= 9) seconds = "0" + seconds;
	dispTime = hours + ":" + minutes + ":" + seconds;
	if(document.getElementById) {
	    document.getElementById("uhrzeit").innerHTML = dispTime;
	} else if(document.all) {
	   	uhrzeit.innerHTML = dispTime;
	}
	setTimeout("clock()", 1000);
}
function startup()
{
	clock();
	setTimeout("pmcheck()",150000);
	<?php if($_GET['p'] == "db") echo "starte_ueberwachung();"; ?>
}
function pmcheck()
{
	elt = 'pmcheck';
	sendRequest('backend/pmcheck.php?PHPSESSID=<?php session_id() ?>');
	setTimeout("pmcheck()",150000);
}
function createRequestObject()
{
	var ro;
	var browser = navigator.appName;
	if(browser == "Microsoft Internet Explorer"){
		ro = new ActiveXObject("Microsoft.XMLHTTP");
	}else{
		ro = new XMLHttpRequest();
	}
	return ro;
}
var http = createRequestObject();
var http2 = createRequestObject();

function sendRequest(action)
{
	http.open('get', action);
	http.onreadystatechange = handleResponse;
	http.send(null);
}
function handleResponse()
{
	if(http.readyState == 4)
	{
		var response = http.responseText;
		if(response.length > 0)
		{
			document.getElementById(elt).innerHTML = response;
			return;
		}
	}
}

function getOffsetRect(elem) {
    var box = elem.getBoundingClientRect()
    
    var body = document.body
    var docElem = document.documentElement
    
    var scrollTop = window.pageYOffset || docElem.scrollTop || body.scrollTop
    var scrollLeft = window.pageXOffset || docElem.scrollLeft || body.scrollLeft
    
    var clientTop = docElem.clientTop || body.clientTop || 0
    var clientLeft = docElem.clientLeft || body.clientLeft || 0
    
    var top  = box.top +  scrollTop - clientTop
    var left = box.left + scrollLeft - clientLeft
    return { y: Math.round(top), x: Math.round(left), boxtop: Math.round(box.top), boxleft: Math.round(box.left) }
}
function positionElement(rel,element,approxHeight) {
	var offset = getOffsetRect(rel);
	var winHeight = isNaN(window.innerHeight) ? window.clientHeight : window.innerHeight;
	var winWidth = isNaN(window.innerWidth) ? window.clientWidth : window.innerWidth;
	
	var space = 30;
	
	var x;
	var y;
	
	// element.style.boxShadow = '0px 0px 16px 8px rgba(200,200,200,0.75)';
	// element.style.backgroundColor = "#000000";
	element.style.background = 'none';
	element.style.position = 'fixed';
	element.style.visibility = 'visible';


	if (offset.boxleft > winWidth/2) {
		x = winWidth - offset.boxleft - space + rel.width + rel.width ;
		element.style.right = x+'px';
		element.style.left = null;
	} else {
		x = rel.offsetWidth + offset.boxleft + space;
		element.style.left = x+'px';
		element.style.right = null;
	}
	
	if (offset.boxtop > winHeight/2) {
		y = winHeight - offset.boxtop - rel.height;
		element.style.top = null;
		element.style.bottom = y+'px';
	} else {
		y = offset.boxtop;
		element.style.top = y+'px';
		element.style.bottom = null;
	}	
}
function putWindow(element) {
	var offset = getOffsetRect(rel);
	var winHeight = isNaN(window.innerHeight) ? window.clientHeight : window.innerHeight;
	var winWidth = isNaN(window.innerWidth) ? window.clientWidth : window.innerWidth;
	
	var space = 30;
	
	var x;
	var y;
	
	// element.style.boxShadow = '0px 0px 16px 8px rgba(200,200,200,0.75)';
	// element.style.backgroundColor = "#000000";
	element.style.background = 'none';
	element.style.position = 'fixed';
	element.style.visibility = 'visible';


	if (offset.boxleft > winWidth/2) {
		x = winWidth - offset.boxleft - space + rel.width + rel.width ;
		element.style.right = x+'px';
		element.style.left = null;
	} else {
		x = rel.offsetWidth + offset.boxleft + space;
		element.style.left = x+'px';
		element.style.right = null;
	}
	
	if (offset.boxtop > winHeight/2) {
		y = winHeight - offset.boxtop - rel.height;
		element.style.top = null;
		element.style.bottom = y+'px';
	} else {
		y = offset.boxtop;
		element.style.top = y+'px';
		element.style.bottom = null;
	}	
}
function openJsWin(elt,width,relx,rely)
{
	return overlib('<div id='+elt+'></div>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER, DRAGGABLE, ALTCUT, STICKY, RELX, relx, RELY, rely, WIDTH, width);
}
function openPJsWin(elt,width)
{
	return overlib('<div id='+elt+'></div>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER, DRAGGABLE, ALTCUT, STICKY, WIDTH, width);
}
function opensi(vari)
{
	elt = 'sinfo';
	sendRequest('backend/sinfo.php?PHPSESSID=<?php session_id() ?>&id=' + vari + '');	
	return overlib('<div id="sinfo"></div>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER, STICKY, RELX, 150, RELY, 50, WIDTH, 650, EXCLUSIVE, DRAGGABLE, ALTCUT);
}
function openfl()
{
        str="folist.php";
        Win = window.open(str,'Win','width=300,height=400,resizeable=no,scrollbars=yes');
        window.open(str,'Win','width=300,height=400');
        Win.opener = self;
}
function opennotes()
{
        str="main.php?p=comm&s=nz";
        Win = window.open(str,'WinNotes','width=850,height=700,resizeable=no,scrollbars=no');
        window.open(str,'WinNotes','width=410,height=360');
        Win.opener = self;
}    

function toggle_visibility(id) {
	var e = document.getElementById(id);
	if(e.style.display == 'block') {
		e.style.display = 'none';
	} else {
		e.style.display = 'block';
	}
}		

function toggle_highlighted(e) {
	e.classList.toggle('closed');
}
function mov(e,c) {
	e.classList.remove(c);
	e.classList.add('menuColorActive');
}
function mot(e,c) {
	e.classList.add(c);
	e.classList.remove('menuColorActive');
}

function hideInfo() {
	document.getElementById('infodiv').innerHTML = "";
    document.getElementById('infodiv').style.visibility = "hidden";
}
function hideContent() {
	document.getElementById('contentdiv').innerHTML = "";
    document.getElementById('contentdiv').style.visibility = "hidden";
}

function showGood(rel,id)
{
	elt = 'infodiv';
	sendRequest('backend/infodisplay/goods.php?PHPSESSID=".session_id()."&gid='+id);
	positionElement(rel,document.getElementById('infodiv'),510);
}
function showMiscInfo(rel,id)
{
	elt = 'infodiv';
	sendRequest('backend/infodisplay/misc.php?PHPSESSID=".session_id()."&gid='+id);
	positionElement(rel,document.getElementById('infodiv'),510);
}
function showBuild(rel,id)
{
	elt = 'infodiv';
	sendRequest('backend/infodisplay/building.php?PHPSESSID=".session_id()."&id='+id);
	positionElement(rel,document.getElementById('infodiv'),420);
}
function showColonyShortInfo(rel,id,cid)
{
	elt = 'infodiv';
	sendRequest('backend/colony/colonyshort.php?PHPSESSID=".session_id()."&id='+id+'&cid='+cid);
	positionElement(rel,document.getElementById('infodiv'),420);
}




</script>
<script type="text/javascript" src="gfx/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>
<script type="text/javascript" src="gfx/overlib2.js"><!-- overLIB (c) Erik Bosrup --></script>
<script type="text/javascript" src="gfx/drag.js"><!-- overLIB (c) Erik Bosrup --></script>

<script language="javascript" src="gfx/overlib_exclusive.js"></script>
<?php echo '<link rel="STYLESHEET" type="text/css" href=gfx/css/'.$_SESSION['skin'].'.css>';?>
<link rel="alternate" type="application/atom+xml" title="Atom-Datei" href="http://www.stuniverse.de/static/kn.xml">
<link rel="shortcut icon" href="favicon.ico" />
<style>
td.pages {
	text-align: center;
	width: 20px;
	border: 1px groove #8897cf;
}
td.pages:hover
{
	background: #262323;
}
</style>
</head>
<?php

if ($_SESSION['disable_background']) {
	echo "<body style='background: #000000;margin-top: 0px;' onload='startup();'>";
} else {
	echo "<body style='background: url(gfx/stars.jpg) repeat fixed top left #000000;margin-top: 0px;' onload='startup();'>";
}

echo "
<div id=\"overDiv\" style=\"-moz-opacity:0.9; position:absolute; visibility:hidden; z-index:1000;\"></div>
<div id=\"infodiv\" style=\"z-index:12; visibility:hidden;position:absolute;\"></div>
<div id=\"contentdiv\" style=\"z-index:10; visibility:hidden;position:absolute;\"></div>
";
if ($_GET['s'] != "nz")
{
	echo "
	<div class=\"mainnavi\" id=\"navi\"  ><ul>";
	echo "<li class=\"menuColor1 mainmenu\" onmouseover=\"mov(this,'menuColor1');\" onmouseout=\"mot(this,'menuColor1');\"><a href=main.php onMouseOver=cp('m_ma','buttons/hover/w/maindesk') onMouseOut=cp('m_ma','buttons/inactive/n/maindesk')><img src=".$gfx."/buttons/inactive/n/maindesk.gif border=0 name=m_ma> Maindesk</a></li>";
	echo "<li class=\"menuColor3 mainmenu\" onmouseover=\"mov(this,'menuColor3');\" onmouseout=\"mot(this,'menuColor3');\"><a href=?p=colony onMouseOver=cp('m_co','buttons/hover/w/planet') onMouseOut=cp('m_co','buttons/inactive/n/planet')><img src=".$gfx."/buttons/inactive/n/planet.gif border=0 name=m_co> Kolonien</a></li>";
	// echo "<li class=\"menuColor3 mainmenu\" onmouseover=\"mov(this,'menuColor3');\" onmouseout=\"mot(this,'menuColor4');\"><a href=?p=demography onMouseOver=cp('m_dm','buttons/hover/w/people') onMouseOut=cp('m_dm','buttons/inactive/n/people')><img src=".$gfx."/buttons/inactive/n/people.gif border=0 name=m_dm> Einwohner</a></li>";
	// echo "<li><a href=?p=station onMouseOver=cp('m_sta','buttons/hover/w/station') onMouseOut=cp('m_sta','buttons/inactive/n/station')><img src=".$gfx."/buttons/inactive/n/station.gif border=0 name=m_sta> Stationen</a></li>";
	echo "<li class=\"menuColor2 mainmenu\" onmouseover=\"mov(this,'menuColor2');\" onmouseout=\"mot(this,'menuColor2');\"><a href=?p=ship onMouseOver=cp('m_shi','buttons/hover/w/ships') onMouseOut=cp('m_shi','buttons/inactive/n/ships')><img src=".$gfx."/buttons/inactive/n/ships.gif border=0 name=m_shi> Schiffe</a></li>";
	// echo "<li class=\"menuColor2 mainmenu\" onmouseover=\"mov(this,'menuColor2');\" onmouseout=\"mot(this,'menuColor2');\"><a href=?p=research onMouseOver=cp('m_fo','buttons/hover/w/research') onMouseOut=cp('m_fo','buttons/inactive/n/research')><img src=".$gfx."/buttons/inactive/n/research.gif border=0 name=m_fo> Forschung</a></li>";
	echo "<li class=\"menuColor1 mainmenu\" onmouseover=\"mov(this,'menuColor1');\" onmouseout=\"mot(this,'menuColor1');\"><a href=?p=comm onMouseOver=cp('m_com','buttons/hover/w/comms') onMouseOut=cp('m_com','buttons/inactive/n/comms')><img src=".$gfx."/buttons/inactive/n/comms.gif border=0 name=m_com> Kommunikation</a></li>";
	echo "<li class=\"menuColor4 mainmenu\" onmouseover=\"mov(this,'menuColor4');\" onmouseout=\"mot(this,'menuColor4');\"><a href=?p=trade onMouseOver=cp('m_wa','buttons/hover/w/trade') onMouseOut=cp('m_wa','buttons/inactive/n/trade')><img src=".$gfx."/buttons/inactive/n/trade.gif border=0 name=m_wa> Warenbörse</a></li>";
	echo "<li class=\"menuColor4 mainmenu\" onmouseover=\"mov(this,'menuColor4');\" onmouseout=\"mot(this,'menuColor4');\"><a href=?p=ally onMouseOver=cp('m_al','buttons/hover/w/nodes') onMouseOut=cp('m_al','buttons/inactive/n/nodes')><img src=".$gfx."/buttons/inactive/n/nodes.gif border=0 name=m_al> Allianz</a></li>";
	echo "<li class=\"menuColor2 mainmenu\" onmouseover=\"mov(this,'menuColor2');\" onmouseout=\"mot(this,'menuColor2');\"><a href=?p=map onMouseOver=cp('m_map','buttons/hover/w/star') onMouseOut=cp('m_map','buttons/inactive/n/star')><img src=".$gfx."/buttons/inactive/n/star.gif border=0 name=m_map> Sternenkarte</a></li>";
	// echo "<li class=\"menuColor1 mainmenu\" onmouseover=\"mov(this,'menuColor1');\" onmouseout=\"mot(this,'menuColor1');\"><a href=?p=conflict onMouseOver=cp('m_con','buttons/hover/w/conflict') onMouseOut=cp('m_con','buttons/inactive/n/conflict')><img src=".$gfx."/buttons/inactive/n/conflict.gif border=0 name=m_con> Konflikt</a></li>";
	echo "<li class=\"menuColor3 mainmenu\" onmouseover=\"mov(this,'menuColor3');\" onmouseout=\"mot(this,'menuColor3');\"><a href=?p=db onMouseOver=cp('m_da','buttons/hover/w/data') onMouseOut=cp('m_da','buttons/inactive/n/data')><img src=".$gfx."/buttons/inactive/n/data.gif border=0 name=m_da> Datenbanken</a></li>";
	echo "<li class=\"menuColor1 mainmenu\" onmouseover=\"mov(this,'menuColor1');\" onmouseout=\"mot(this,'menuColor1');\"><a href=?p=history onMouseOver=cp('m_hi','buttons/hover/w/time') onMouseOut=cp('m_hi','buttons/inactive/n/time')><img src=".$gfx."/buttons/inactive/n/time.gif border=0 name=m_hi> History</a></li>";
	echo "<li class=\"menuColor3 mainmenu\" onmouseover=\"mov(this,'menuColor3');\" onmouseout=\"mot(this,'menuColor3');\"><a href=?p=opt onMouseOver=cp('m_op','buttons/hover/w/options') onMouseOut=cp('m_op','buttons/inactive/n/options')><img src=".$gfx."/buttons/inactive/n/options.gif border=0 name=m_op> Einstellungen</a></li>";
	echo "<li class=\"menuColor4 mainmenu\" onmouseover=\"mov(this,'menuColor4');\" onmouseout=\"mot(this,'menuColor4');\"><a href=?p=logout onMouseOver=cp('m_lg','buttons/hover/r/dooropen') onMouseOut=cp('m_lg','buttons/inactive/n/door')><img src=".$gfx."/buttons/inactive/n/door.gif border=0 name=m_lg> Logout</a></li>";
	echo "</ul></div>
	<div class=\"tools\" style=\"margin:6px;text-align:center;\">
	<a href=javascript:opennotes() ".getHover('no','inactive/n/text','hover/w/text')."><img src=".$gfx."/buttons/inactive/n/text.gif name=no border=0 title='Notizzettel öffnen'></a>
	<a href=javascript:openfl() ".getHover('cl','inactive/n/list','hover/w/list')."><img src=".$gfx."/buttons/inactive/n/list.gif border=0 name=cl title='Kontaktliste öffnen'></a>
	<a href=?p=comm&s=pe ".getHover('pe','inactive/n/pm_in','hover/w/pm_in')."><img src=".$gfx."/buttons/inactive/n/pm_in.gif border=0 name=pe title='Posteingang'></a>
	<a href=?p=comm&s=pa ".getHover('pa','inactive/n/pm_out','hover/w/pm_out')."><img src=".$gfx."/buttons/inactive/n/pm_out.gif border=0 name=pa title='Postausgang'></a>
	<a href=?p=comm&s=nn ".getHover('nn','inactive/n/mail','hover/w/mail')."><img src=".$gfx."/buttons/inactive/n/mail.gif border=0 name=nn title='Neue Nachricht schreiben'></a>
	</div>";
}
?>

<?php
if ($_SESSION['uid'] == 11 || $_SESSION['uid'] == 101)
{
	//  Start TIMER
	//  -----------
	$stimer = explode( ' ', microtime() );
	$stimer = $stimer[1] + $stimer[0];
	//  -----------
	
	#$db->debug = 1;
	
	unset($result);
}

include_once($inc);

if ($_SESSION['uid'] == 11 || $_SESSION['uid'] == 101)
{
	if ($db->debug == 1)
	{
		echo "<br>Query: ".$db->qcount."<br><br>".$db->queries;
	}
	
	//  End TIMER
	//  ---------
	$etimer = explode( ' ', microtime() );
	$etimer = $etimer[1] + $etimer[0];

	echo '<p style="margin:auto; text-align:center">';
	printf( "Script timer: <b>%f</b> seconds.", ($etimer-$stimer) );
	echo '</p>';
}
?>
</div>
</body>
</html>
