<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>Logfile-Parsing</title>
</head>
<body>
<?php
include_once("../inc/config.inc.php");
if (!$_GET['action'])
{
	$verzeichnis = dir($global_path."intern/log/"); 
	while($datei = $verzeichnis->read())
	{ 
	  if(!is_dir($global_path."intern/log/".$datei)) $data[] = $datei;
	} 
	$verzeichnis->close();
	$k=0;
	for ($i=0;$i<count($data);$i++)
	{
		$arr = split(".l",$data[$i]);
		echo "<a href=logfile.php?action=parse&log=".$arr[0].">".$arr[0]."</a>";
		if ($i+1 < count($data)) echo " - ";
		if ($k==6)
		{
			echo "<br>";
			$k=0;
		}
		$k++;
	}
}
	elseif ($action = "parse")
{
	if (file_exists($global_path."intern/log/".$_GET['log'].".log")) $dir = @file($global_path."intern/log/".$_GET['log'].".log");
	echo "
	<table with=99% cellpadding=2 cellspacing=2>
	<tr>
		<td width=100% colspan=5>Logfile vom ".str_replace("_",".",$_GET['log'])." - Insgesamt ".count($dir)." Zeile(n)</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>Uhrzeit</td>
		<td>IP</td>
		<td>User-ID</td>
		<td>Errorcode</td>
		<td>Vorgang</td>
	</tr>";
	for ($i=0;$i<count($dir);$i++)
	{
		$rowdata = split("%-%",$dir[$i]);
		echo "<tr>
			<td bgcolor=#C0C0C0>".$rowdata[0]."</td>
			<td bgcolor=#C0C0C0>".$rowdata[1]."</td>
			<td bgcolor=#C0C0C0>".$rowdata[2]."</td>
			<td bgcolor=#C0C0C0>".$rowdata[3]."</td>
			<td bgcolor=#C0C0C0>".stripslashes($rowdata[4])."</td>
		</tr>";
	}
	echo "</table>";
}
?>
</body>
</html>
