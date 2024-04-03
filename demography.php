<?php
if (!is_object($db)) exit;
switch($_GET[s])
{
	default:
		$v = "main";
	// case "ma":
		// $v = "main";
		// break;
}

if ($v == "main")
{
	pageheader("/ <b>Einwohner</b>");

	
	
	echo "<table width=100% class=tablelayout><tr>";
	echo "<td class=tablelayout colspan=2>";
	echo fixedPanel(2,"Coming soon","mdemog",$gfx."/buttons/icon/info.gif","");
	echo "</td></tr>";
	echo "</table>";
	
}

?>
