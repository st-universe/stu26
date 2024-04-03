<?php
class db
{
	function db()
	{
		global $dbd,$eId;
		$this->dblink = @mysql_connect($dbd[server],$dbd[user],$dbd[pass]);
		if (!$this->dblink) $eId = 100;
		@mysql_select_db($dbd[database], $this->dblink);
	}
	
	function query($qry,$m=0)
	{
		global $_SESSION,$myGame,$qcount,$_GET;
		if (!$qry) return 0;
		$qcount += 1;
		$result = mysql_query($qry,$this->dblink);
		if (mysql_error() && $_SESSION["uid"] < 103) echo "Fehler in Datenbankquery<br>".$qry."<br>".mysql_error();
		if (mysql_error()) addlog(200,$_SESSION["uid"],"Page ".$_GET[p].",Sektion ".$_GET[s]."<br>".mysql_error()."<br>".$qry);
		if ($m == 0) return $result;
		if ($m == 5) return @mysql_insert_id();
		if ($m == 6) return @mysql_affected_rows();
		if (@mysql_num_rows($result) == 0) return 0;
		if ($m == 1)
		{
			$res = @mysql_result($result,0);
			if (!$res) return 0;
			return $res;
		}
		if ($m == 3) return @mysql_num_rows($result);
		if ($m == 4) return @mysql_fetch_assoc($result);
	}

}
?>
