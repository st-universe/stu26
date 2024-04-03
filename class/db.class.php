<?php
class db
{
	function db()
	{
		global $dbd,$eId;
		$this->dblink = mysql_connect($dbd['server'],$dbd['user'],$dbd['pass']);
		if (!$this->dblink) $eId = 100;
		
		mysql_set_charset("latin1",$this->dblink);
		@mysql_select_db($dbd['database'], $this->dblink);
		$this->debug = 0;
		
		$this->mysqlilink = mysqli_connect($dbd['server'],$dbd['user'],$dbd['pass'],$dbd['database']);
		if (mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}		
	}
	
	function query($qry,$m=0)
	{
		global $_SESSION,$_GET;
		if (!$qry) return 0;
		if ($this->debug == 1)
		{
			$this->qcount++;
			$this->queries .= "<br><br>".$qry;
		}
		$result = @mysql_query($qry,$this->dblink);
		if (mysql_error() && $_SESSION['uid'] < 103) echo "Fehler in Datenbankquery<br>".$qry."<br>".mysql_error();
		if (mysql_error()) addlog(200,$_SESSION['uid'],"Page ".$_GET['p'].",Sektion ".$_GET['s']."<br>".mysql_error()."<br>".$qry);
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
	
	function sqlencode($s) {
		return mysqli_real_escape_string($this->mysqlilink, $s);
	}
	
	
	// function pquery($qry,$params,$m=0)
	// {
		// global $_SESSION,$_GET;
		// if (!$qry) return 0;

		
		
		// $stmt = mysqli_prepare($link, $qry);
		
		
		
		// $result = @mysqli_stmt_execute($stmt);
		
		
		
		
		
		// if (mysqli_error($this->mysqlilink) && $_SESSION['uid'] < 103) echo "Fehler in Datenbankquery<br>".$qry."<br>".mysqli_error();
		// if (mysqli_error($this->mysqlilink)) addlog(200,$_SESSION['uid'],"Page ".$_GET['p'].",Sektion ".$_GET['s']."<br>".mysqli_error()."<br>".$qry);
		// if ($m == 0) return $result;
		// if ($m == 5) return @mysql_insert_id();
		// if ($m == 6) return @mysql_affected_rows();
		// if (@mysqli_num_rows($result) == 0) return 0;
		// if ($m == 1)
		// {
			// $res = @mysql_result($result,0);
			// if (!$res) return 0;
			// return $res;
		// }
		// if ($m == 3) return @mysql_num_rows($result);
		// if ($m == 4) return @mysql_fetch_assoc($result);
	// }
}
?>
