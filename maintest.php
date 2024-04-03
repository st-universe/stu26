
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
	<title>Star Trek Universe</title>
<SCRIPT LANGUAGE='JavaScript'>
var Win = null;
function cp(objekt,datei) { <?php echo "document.images[objekt].src = \"".$gfx."/\" + datei + \".gif\"";?> }
var digital = new Date( "<?php echo date("M, d Y G:i:s") ?>");


</script>
<?php

//<script type="text/javascript" src="gfx/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>
//<script type="text/javascript" src="gfx/overlib2.js"><!-- overLIB (c) Erik Bosrup --></script>
//<script type="text/javascript" src="gfx/drag.js"><!-- overLIB (c) Erik Bosrup --></script>
//<script language="javascript" src="gfx/overlib_exclusive.js"></script>
echo '<link rel="STYLESHEET" type="text/css" href=gfx/css/1.css>';?>
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
<body bgcolor="#000000" onload="startup();" style="margin-top: 0px;">
<div id="overDiv" style="-moz-opacity:0.9; position:absolute; visibility:hidden; z-index:1000;"></div>

<?php

	echo "
	<li><div id=\"navi\" style=\"position: fixed; left: 5px; top: 1px;\"><ul>
	><a href=main.php onMouseOver=cp('m_ma','buttons/menu_main1') onMouseOut=cp('m_ma','buttons/menu_main0')><img src=".$gfx."/buttons/menu_main0.gif border=0 name=m_ma> Maindesk</a></li>
	<li><a href=?p=colony onMouseOver=cp('m_co','buttons/menu_planets1') onMouseOut=cp('m_co','buttons/menu_planets0')><img src=".$gfx."/buttons/menu_planets0.gif border=0 name=m_co> Kolonien</a></li>
	<li><a href=?p=ship onMouseOver=cp('m_shi','buttons/menu_ships1') onMouseOut=cp('m_shi','buttons/menu_ships0')><img src=".$gfx."/buttons/menu_ships0.gif border=0 name=m_shi> Schiffe</a></li>
	<li><a href=?p=research onMouseOver=cp('m_fo','buttons/menu_forsch1') onMouseOut=cp('m_fo','buttons/menu_forsch0')><img src=".$gfx."/buttons/menu_forsch0.gif border=0 name=m_fo> Forschung</a></li>
	<li><a href=?p=comm onMouseOver=cp('m_com','buttons/menu_comm1') onMouseOut=cp('m_com','buttons/menu_comm0')><img src=".$gfx."/buttons/menu_comm0.gif border=0 name=m_com> Kommunikation</a></li>
	<li><a href=?p=trade onMouseOver=cp('m_wa','buttons/menu_trade1') onMouseOut=cp('m_wa','buttons/menu_trade0')><img src=".$gfx."/buttons/menu_trade0.gif border=0 name=m_wa> Warenbörse</a></li>
	<li><a href=?p=ally onMouseOver=cp('m_al','buttons/menu_ally1') onMouseOut=cp('m_al','buttons/menu_ally0')><img src=".$gfx."/buttons/menu_ally0.gif border=0 name=m_al> Allianzschirm</a></li>
	<li><a href=?p=map onMouseOver=cp('m_map','buttons/menu_map1') onMouseOut=cp('m_map','buttons/menu_map0')><img src=".$gfx."/buttons/menu_map0.gif border=0 name=m_map> Sternenkarte</a></li>
	<li><a href=?p=db onMouseOver=cp('m_da','buttons/menu_data1') onMouseOut=cp('m_da','buttons/menu_data0')><img src=".$gfx."/buttons/menu_data0.gif border=0 name=m_da> Datenbanken</a></li>
	<li><a href=?p=history onMouseOver=cp('m_hi','buttons/menu_history1') onMouseOut=cp('m_hi','buttons/menu_history0')><img src=".$gfx."/buttons/menu_history0.gif border=0 name=m_hi> History</a></li>
	<li><a href=?p=opt onMouseOver=cp('m_op','buttons/menu_option1') onMouseOut=cp('m_op','buttons/menu_option0')><img src=".$gfx."/buttons/menu_option0.gif border=0 name=m_op> Einstellungen</a></li>
	<li><a href=?p=logout onMouseOver=cp('m_lg','buttons/menu_logout1') onMouseOut=cp('m_lg','buttons/menu_logout0')><img src=".$gfx."/buttons/menu_logout0.gif border=0 name=m_lg> Logout</a></li>
	</ul></div>";

?>


<style type="text/css">
div.header {
   position: fixed;
   left: 140px;
   right: 5px;
   top: 0px;
   z-index: 900;
}
div.colinfo {
   position: fixed;
   left: 5px;
   top: 350px;
   width: 130px;
}
</style>
<!--[if IE]>
<style type="text/css">
div.header {
   position: absolute;
   left: 140px;
   right: 5px;
   top: 0px;
}
div.colinfo {
   position: absolute;
   left: 5px;
   top: 350px;
   width: 130px;
}
</style>
<![endif]--> 

</div>
</body>
</html>
