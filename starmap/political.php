<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../inc/func.inc.php");
include_once("../inc/config.inc.php");
include_once("../class/db.class.php");
$db = new db;


@session_start();

if ($_SESSION["login"] != 1) exit;

$gfx = $_SESSION[gfx_path];
// if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "../gfx/";
if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "gfx/";

	$activecolor = "#ffffff";
	$inactivecolor = "#000000";
	
	function part($i) {
		global $activecolor, $inactivecolor, $gfx;
		return "<td style='background:transparent;width:239px;height:239px;'  onclick=\"loadPoliticalMapZoom(".$i.");\"><a href=#><div style='background:transparent;width:237px;height:237px;border:none' onmouseover=\"this.style.border = '1px solid ".$activecolor."'\" onmouseout=\"this.style.border = 'none'\"></div></a></td>";		
	}
	
	
	
	$normalmap = "
	<table  bgcolor=#262323 cellspacing=1 cellpadding=1 style=\"height:810px; width:730px;\" oncontextmenu=\"return false;\">
		<tr>
			<td style=\"text-align:center;height:30px;\"colspan=3><b>Karte des Deenia-Sektors</b></td>
		</tr>
		<tr>
			<td style=\"height:26px;width:200px;text-align:center;\" onclick=\"loadActualMap();\"><a href=#>Astrometrisch</a></td>
			<th style=\"height:26px;width:200px;text-align:center;\"><a href=#>Politisch</a></th>
			<td style=\"height:26px;width:200px;text-align:center;\" onclick=\"loadEventMap();\"><a href=#>Ereigniskarte</a></td>
		</tr>
		<tr>
			<td colspan=3><center>
				<table bgcolor=#000000 cellspacing=1 cellpadding=0 style=\"background: url('".$gfx."/starmap/political/full.png') top no-repeat;width:722px;\">
					<tr>
						".part(11)."
						".part(21)."
						".part(31)."
					</tr>
					<tr>
						".part(12)."
						".part(22)."
						".part(32)."
					</tr>
					<tr>
						".part(13)."
						".part(23)."
						".part(33)."
					</tr>
				</table></center>
			</td>
		</tr>
		<tr><th colspan=3 style=\"height:26px;text-align:center;\" onclick=\"loadPoliticalMap();\"></th></tr>
	</table>
	";

	
	
	echo $normalmap;
	
?>