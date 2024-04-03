<?php
class trade extends qpm
{
	function trade()
	{
		global $db,$_SESSION,$gfx;
		$this->db = $db;
		$this->uid = $_SESSION['uid'];
		$this->gfx = $gfx;
		$this->sess = $_SESSION;
	}

	function getofferlist($network,$sort,$mode,$cou="",$pa)
	{
		if (!$network || !check_int($network)) return;
		if ($pa && !check_int($pa)) return;
		if ($mode && !check_int($mode)) return;
		$sort = $this->db->sqlencode($sort);
		$cou = $this->db->sqlencode($cou);

		if ($sort == "x")
		{
			$this->result = $this->db->query("SELECT a.offer_id,UNIX_TIMESTAMP(a.date) as date_t,a.count,a.marks,a.user_id,a.wgoods_id,a.wcount,a.ggoods_id,a.gcount,c.user,c.race,d.count as vcount FROM stu_trade_offers as a LEFT JOIN stu_goods as b ON b.goods_id=a.ggoods_id LEFT JOIN stu_user as c ON c.id=a.user_id LEFT JOIN stu_trade_goods as d ON d.goods_id=a.wgoods_id AND d.user_id=".$this->uid." AND d.network_id = ".$network." WHERE a.network_id = ".$network." AND ISNULL(b.view) ORDER BY a.date DESC LIMIT ".(($pa-1)*40).",40");
			$this->sc = $this->db->query("SELECT COUNT(a.offer_id) FROM stu_trade_offers as a LEFT JOIN stu_goods as b ON b.goods_id=a.ggoods_id WHERE a.network_id = ".$network." AND ISNULL(b.view)",1);
			return;
		}
		if ($sort == "xl")
		{
			$this->result = $this->db->query("SELECT a.offer_id,UNIX_TIMESTAMP(a.date) as date_t,a.count,a.marks,a.user_id,a.wgoods_id,a.wcount,a.ggoods_id,a.gcount,b.user,b.race,c.count as vcount FROM stu_trade_offers as a LEFT JOIN stu_user as b ON b.id=a.user_id LEFT JOIN stu_trade_goods as c ON c.goods_id=a.wgoods_id AND c.user_id=".$this->uid." AND c.network_id = ".$network." WHERE a.network_id = ".$network." AND a.rel>300 ORDER BY a.date DESC LIMIT ".(($pa-1)*40).",40");
			$this->sc = $this->db->query("SELECT COUNT(offer_id) FROM stu_trade_offers WHERE network_id = ".$network." AND (rel>300 OR marks>=15)",1);
			return;
		}
		if ($sort == "xs")
		{
			$this->result = $this->db->query("SELECT a.offer_id,UNIX_TIMESTAMP(a.date) as date_t,a.count,a.marks,a.user_id,a.wgoods_id,a.wcount,a.ggoods_id,a.gcount,b.user,b.race,c.count as vcount FROM stu_trade_offers as a LEFT JOIN stu_user as b ON b.id=a.user_id LEFT JOIN stu_trade_goods as c ON c.goods_id=a.wgoods_id AND c.user_id=".$this->uid." AND c.network_id = ".$network." WHERE a.network_id = ".$network." AND a.user_id=".$mode." ORDER BY a.date DESC LIMIT ".(($pa-1)*40).",40");
			$this->sc = $this->db->query("SELECT COUNT(offer_id) FROM stu_trade_offers WHERE  network_id = '".$network."' AND user_id=".$mode,1);
			return;
		}
		if ($sort == "keine" || !is_numeric($sort) || $sort < 1 || !is_numeric($mode) || $mode < 1 ||$mode > 2) $sort = "";
		else
		{
			if ($mode == 1) $sort = " AND a.ggoods_id='".$sort."'";
			else $sort = " AND a.wgoods_id='".$sort."'";
		}
		if ($sort == "")
		{
			$this->result = $this->db->query("SELECT a.offer_id,UNIX_TIMESTAMP(a.date) as date_t,a.user_id,a.count,a.marks,a.wgoods_id,a.wcount,a.ggoods_id,a.gcount,b.user,b.race,c.count as vcount FROM stu_trade_offers as a LEFT JOIN stu_user as b ON a.user_id=b.id LEFT JOIN stu_trade_goods as c ON c.goods_id=a.wgoods_id AND c.user_id=".$this->uid." AND c.network_id = ".$network." WHERE a.network_id = ".$network." ORDER BY a.date DESC LIMIT ".(($pa-1)*40).",40");
			$this->sc = $this->db->query("SELECT COUNT(offer_id) FROM stu_trade_offers as a WHERE a.network_id = ".$network."",1);
		}
		else
		{
			if (check_int($cou) && strlen($cou) < 6)
			{
				if ($mode == 1) $qad = " AND a.gcount<=".$cou;
				else $qad = " AND a.wcount<=".$cou;
			}
			$this->result = $this->db->query("SELECT a.offer_id,UNIX_TIMESTAMP(a.date) as date_t,a.user_id,a.count,a.marks,a.wgoods_id,a.wcount,a.ggoods_id,a.gcount,b.user,b.race,c.count as vcount FROM stu_trade_offers as a LEFT JOIN stu_user as b ON b.id=a.user_id LEFT JOIN stu_trade_goods as c ON c.goods_id=a.wgoods_id AND c.user_id=".$this->uid." AND c.network_id = ".$network." WHERE a.network_id = ".$network.$sort.$qad." ORDER BY a.date DESC LIMIT ".(($pa-1)*40).",40");
			$this->sc = $this->db->query("SELECT COUNT(offer_id) FROM stu_trade_offers as a WHERE a.network_id = ".$network.$sort.$qad."",1);
		}
	}

	function takeoffer($network,$id,$ac)
	{
		if (!check_int($network)) return;		
		if (!check_int($id)) return;		
		if ($ac && !check_int($ac)) return $ac;		
		$data = $this->db->query("SELECT a.offer_id,a.network_id,a.user_id,a.count,a.wgoods_id,a.wcount,a.ggoods_id,a.gcount,b.allys_id FROM stu_trade_offers as a LEFT JOIN stu_user as b ON b.id=a.user_id WHERE a.offer_id=".$id." AND a.network_id = ".$network." LIMIT 1",4);
		if ($data == 0) return;
		if ($this->uid == $data['user_id']) return "Eigene Angebote können nicht angenommen werden!";
		if ($network != $data['network_id']) return;
		if ($this->db->query("SELECT mode FROM stu_contactlist WHERE user_id=".$data['user_id']." AND recipient=".$this->uid." LIMIT 1",1) == 3) return "Dieser Siedler betrachtet Dich als Feind - Du kannst seine Angebote nicht annehmen";
		if ($this->sess['allys_id'] > 0 && $data['allys_id'] > 0 && $this->db->query("SELECT type FROM stu_ally_relationship WHERE ((allys_id1=".$this->sess['allys_id']." AND allys_id2=".$data['allys_id'].") OR (allys_id2=".$this->sess['allys_id']." AND allys_id1=".$data['allys_id']."))",1) == 1) return "Deine Allianz befindet sich mit der Allianz des Anbieters im Krieg - Das Angebot kann nicht angenommen werden";
		if (strlen($ac) > 3) $ac = 255;
		if ($data['count'] < $ac) $ac = $data['count'];
		
		
		// volume check
		$wball = $this->db->query("SELECT max_storage FROM stu_trade_networks WHERE network_id = ".$network."",1);
		// $wbmod = $this->db->query("SELECT value FROM stu_game_vars WHERE var = 'wblimitmod'",1);

		$myall = $this->tradesumnet($this->uid,$network); 
		// $mymod = $this->tradesummod($this->uid) ;	
		
		$volumediff = $ac * ($data['gcount'] - $data['wcount']);
		
		if ($volumediff > 0) {
			if ($myall + $volumediff > $wball) {
				return "Dieses Angebot kann nicht angenommen werden, da die Warenmenge die zulässige maximale Lagermenge für Waren an der Warenbörse von ".$wball." übersteigen würde.";
			}
		}
		
		// ich will module
		// if ($data['ggoods_id'] > 1000) {
			// $moddiff = $data['gcount'];
			// if ($data['wgoods_id'] > 1000) $moddiff -= $data['wcount'];
			// $moddiff = $moddiff * $ac;
			
			// if ($moddiff > 0) {
				// if ($mymod + $moddiff > $wbmod) {
				// return "Dieses Angebot kann nicht angenommen werden, da die Warenmenge die zulässige maximale Lagermenge für Module an der Warenbörse von ".$wbmod." übersteigt.";
				// }
			// }
		// }
		
		$c = $this->db->query("SELECT count FROM stu_trade_goods WHERE network_id=".$network." AND offer_id=0 AND user_id=".$this->uid." AND goods_id=".$data['wgoods_id']." LIMIT 1",1);
		
		$wgoodname = $this->db->query("SELECT name FROM stu_goods WHERE goods_id=".$data['wgoods_id'],1);
		$ggoodname = $this->db->query("SELECT name FROM stu_goods WHERE goods_id=".$data['ggoods_id'],1);
		
		if ($c < $data['wcount']*$ac) return "Für diese Transaktion werden ".($data['wcount']*$ac)." ".$wgoodname." benötigt (".$c." vorhanden)";
		$pm = "Angebot ".$id." angenommen<br>Es wurde ".$ac." Mal ".$data['gcount']." ".$ggoodname." gegen ".$data['wcount']." ".$wgoodname." getauscht";

		$this->db->query("START TRANSACTION");
		// $this->upper_stat($data['ggoods_id'],$data['gcount']*$ac);
		// $this->upper_stat($data['wgoods_id'],$data['wcount']*$ac);
		$this->lowerstorage($network,$data['wgoods_id'],$data['wcount']*$ac,$this->uid);
		$this->upperstorage($network,$data['ggoods_id'],$data['gcount']*$ac,$this->uid);
		$this->upperstorage($network,$data['wgoods_id'],$data['wcount']*$ac,$data['user_id']);
		if ($data['count']-$ac <= 0)
		{
			$this->db->query("DELETE FROM stu_trade_offers WHERE offer_id=".$id." AND network_id = ".$network." LIMIT 1");
			$this->db->query("DELETE FROM stu_trade_marks WHERE offer_id=".$id." AND network_id = ".$network." LIMIT 1");
		}
		else $this->db->query("UPDATE stu_trade_offers SET count=count-".$ac." WHERE offer_id=".$id." AND network_id = ".$network." LIMIT 1");
		$this->db->query("COMMIT");
		// $this->upperHistoryStorage($data['ggoods_id'],$data['gcount']*$ac,$data['wgoods_id'],$data['wcount']*$ac);
		$this->send_pm($this->uid,$data['user_id'],$pm,2);
		return $pm;
	}

	// function upperHistoryStorage($ggood,$gcount,$wgood,$wcount)
	// {
		// $result = $this->db->query("UPDATE stu_trade_history SET gcount=gcount+".$gcount.",wcount=wcount+".$wcount." WHERE ggoods_id=".$ggood." AND wgoods_id=".$wgood." AND date='".date("Y-m-d")."'",6);
		// if ($result == 0) $this->db->query("INSERT INTO stu_trade_history (ggoods_id,gcount,wgoods_id,wcount,date) VALUES ('".$ggood."','".$gcount."','".$wgood."','".$wcount."',NOW())");
	// }	

	function upperstorage($network,$good,$count,$user)
	{
		if (!check_int($network)) return;		
		if (!check_int($good)) return;		
		if (!check_int($count)) return;		
		if (!check_int($user)) return;		
		$result = $this->db->query("UPDATE stu_trade_goods SET count=count+".$count." WHERE network_id = ".$network." AND goods_id=".$good." AND offer_id=0 AND user_id=".$user,6);
		if ($result == 0) $this->db->query("INSERT INTO stu_trade_goods (user_id,goods_id,count,date,network_id) VALUES ('".$user."','".$good."','".$count."',NOW(),'".$network."')");
	}
	
	// function upper_stat($good,$count)
	// {
		// $result = $this->db->query("UPDATE stu_trade_stats SET count=count+".$count." WHERE goods_id=".$good." LIMIT 1",6);
		// if ($result == 0) $this->db->query("INSERT INTO stu_trade_stats (goods_id,count) VALUES ('".$good."','".$count."')");
	// }

	function lowerstorage($network,$good,$count,$user)
	{
		if (!check_int($network)) return;		
		if (!check_int($good)) return;		
		if (!check_int($count)) return;		
		if (!check_int($user)) return;		
		$result = $this->db->query("UPDATE stu_trade_goods SET count=count-".$count." WHERE network_id = ".$network." AND goods_id=".$good." AND offer_id=0 AND user_id=".$user." AND count>".$count,6);
		if ($result == 0) $this->db->query("DELETE FROM stu_trade_goods WHERE network_id = ".$network." AND goods_id=".$good." AND offer_id=0 AND user_id=".$user);
	}

	function tradesumnet($userid,$network) {
		if (!check_int($userid)) return;
		if (!check_int($network)) return;		
		$result = $this->db->query("SELECT * FROM stu_trade_offers WHERE network_id = ".$network." AND user_id=".$userid);
		$sum = 0;
		while($data = mysql_fetch_assoc($result)) {
			if ($data['wcount'] > $data['gcount'])	$sum += $data['count'] * $data['wcount'];
			else									$sum += $data['count'] * $data['gcount'];
		}
	
		$result = $this->db->query("SELECT SUM(count) FROM stu_trade_goods WHERE network_id = ".$network." AND user_id=".$userid,1);
	
		$sum += $result;
	
		return $sum;
	}
	
	// function tradesummod($userid) {

		// $result = $this->db->query("SELECT * FROM stu_trade_offers WHERE user_id=".$userid);
		// $sum = 0;
		// while($data = mysql_fetch_assoc($result)) {
		
			// if (($data['wgoods_id'] > 1000) && ($data['ggoods_id'] < 1000)) {
				// $sum += $data['count'] * $data['wcount'];
			// } elseif (($data['wgoods_id'] < 1000) && ($data['ggoods_id'] > 1000)) {
				// $sum += $data['count'] * $data['gcount'];
			// } elseif (($data['wgoods_id'] > 1000) && ($data['ggoods_id'] > 1000)) {
				// if ($data['wcount'] > $data['gcount'])	$sum += $data['count'] * $data['wcount'];
				// else									$sum += $data['count'] * $data['gcount'];
			// }
		// }
	
		// $result = $this->db->query("SELECT SUM(count) FROM stu_trade_goods WHERE goods_id > 1000 AND user_id=".$userid,1);
	
		// $sum += $result;
	
		// return $sum;
	// }
	
	// function payout($good,$count,$hp)
	// {
		// $hp = $this->db->query("SELECT id,name,cx,cy,sx,sy,systems_id FROM stu_ships WHERE is_hp='1' AND id=".$hp." LIMIT 1",4);
		// if ($hp == 0) return;
		// $msg = "Folgende Waren wurden bei ".$hp['name']." ausgezahlt:<br>";
		// foreach ($good as $key => $value)
		// {
			// if (!$count[$key] || !is_numeric($count[$key]) || $count[$key] < 1) continue;
			// $result = $this->db->query("SELECT count FROM stu_trade_goods WHERE offer_id=0 AND user_id=".$this->uid." AND goods_id=".$value." LIMIT 1",1);
			// if ($result == 0) continue;
			// $result < $count[$key] ? $po = $result : $po = $count[$key];
			// $this->lowerstorage($value,$po,$this->uid);
			// $result = $this->db->query("UPDATE stu_ships_storage SET count=count+".$po." WHERE ships_id=".$hp['id']." AND goods_id=".$value,6);
			// if ($result == 0) $this->db->query("INSERT INTO stu_ships_storage (ships_id,goods_id,count) VALUES ('".$hp['id']."','".$value."','".$po."')");
			// $msg .= "<img src=".$this->gfx."/goods/".$value.".gif> ".$po."<br>";
		// }
		// $result = $this->db->query("SELECT a.id,a.rumps_id,a.huelle,a.max_huelle,a.name,a.eps,c.storage,SUM(b.count) as sc FROM stu_ships as a LEFT JOIN stu_ships_storage as b ON a.id=b.ships_id LEFT JOIN stu_rumps as c ON a.rumps_id=c.rumps_id WHERE ".($hp['systems_id'] > 0 ? "a.sx=".$hp['sx']." AND a.sy=".$hp['sy']." AND a.systems_id=".$hp['systems_id'] : "a.cx=".$hp['cx']." AND a.cy=".$hp['cy'])." AND a.user_id=".$this->uid." GROUP BY a.id");
		// if (mysql_num_rows($result) != 0)
		// {
			// $msg .= "<br><table class=tcal cellpadding=1 cellspacing=1><tr><th colspan=3>Schiffe am Handelsposten</th></tr><tr><td></td><td>Lagerraum</td><td>Energie</td></tr>";
			// while ($data=mysql_fetch_assoc($result)) $msg .= "<tr><td><a href=?p=ship&s=ss&id=".$data['id']."><img src=".$this->gfx."/ships/".vdam($data).$data['rumps_id'].".gif border=0 title=\"".ftit($data['name'])."\"></a></td><td>".(!$data['sc'] ? $data['storage'] : $data['storage']-$data['sc'])."</td><td>".$data['eps']."</td></tr>";
			// $msg .= "</table>";
		// }
		// return $msg;
	// }

	function deloffer($network,$offerId)
	{
		if (!check_int($network)) return;	
		if (!check_int($offerId)) return;		
		$data = $this->db->query("SELECT offer_id,UNIX_TIMESTAMP(date) as date_tsp,count,ggoods_id,gcount FROM stu_trade_offers WHERE network_id = ".$network." AND user_id=".$this->uid." AND offer_id=".$offerId." LIMIT 1",4);
		if ($data == 0) return;
		if ($data['date_tsp']+10800 > time()) return "Angebote können erst 3 Stunden nach der Erstellung gelöscht werden";
		$this->db->query("START TRANSACTION");
		$this->upperstorage($network,$data['ggoods_id'],($data['gcount']*$data['count']),$this->uid);
		$this->db->query("DELETE FROM stu_trade_offers WHERE network_id = ".$network." AND offer_id=".$offerId." LIMIT 1");
		$this->db->query("COMMIT");
		$this->db->query("DELETE FROM stu_trade_marks WHERE offer_id=".$offerId);
		return "Angebot ".$offerId." gelöscht";
	}

	// function npcdeloffer($offerId)
	// {
		// $data = $this->db->query("SELECT a.offer_id,a.user_id,a.count,a.wgoods_id,a.wcount,a.ggoods_id,a.gcount,b.allys_id FROM stu_trade_offers as a LEFT JOIN stu_user as b ON b.id=a.user_id WHERE a.offer_id=".$offerId." LIMIT 1",4);
		// if ($data == 0) return;
		// $rc = $this->db->query("SELECT race FROM stu_user WHERE id=".$data['user_id']." LIMIT 1",1);
		// if ($rc == 1 && $_SESSION['uid'] != 10 && $_SESSION['uid'] != 2) return;
		// if ($rc == 2 && $_SESSION['uid'] != 11 && $_SESSION['uid'] != 2) return;
		// if ($rc == 3 && $_SESSION['uid'] != 12 && $_SESSION['uid'] != 2) return;
		// if ($rc == 4 && $_SESSION['uid'] != 13 && $_SESSION['uid'] != 2) return;
		// if ($rc == 5 && $_SESSION['uid'] != 14 && $_SESSION['uid'] != 2) return;
		// $pm = "Warenbörsen-Angebot ".$offerId." wurde gelöscht<br>Es beinhaltete ".$data['count']." Mal ".$data['gcount']." ".getgoodname($data['ggoods_id'])." gegen ".$data['wcount']." ".getgoodname($data['wgoods_id']);
		// $this->db->query("START TRANSACTION");
		// $this->upperstorage($data['ggoods_id'],($data['gcount']*$data['count']),$data['user_id']);
		// $this->db->query("DELETE FROM stu_trade_offers WHERE offer_id=".$offerId." LIMIT 1");
		// $this->db->query("COMMIT");
		// $this->db->query("DELETE FROM stu_trade_marks WHERE offer_id=".$offerId);
		// $this->send_pm(1,$data['user_id'],$pm,2);
		// return "Angebot ".$offerId." gelöscht";
	// }

	// function npcgetoffergoods($offerId)
	// {
		// $data = $this->db->query("SELECT a.offer_id,a.user_id,a.count,a.wgoods_id,a.wcount,a.ggoods_id,a.gcount,b.allys_id FROM stu_trade_offers as a LEFT JOIN stu_user as b ON b.id=a.user_id WHERE a.offer_id=".$offerId." LIMIT 1",4);
		// if ($data == 0) return;
		// $rc = $this->db->query("SELECT race FROM stu_user WHERE id=".$data['user_id']." LIMIT 1",1);
		// if ($rc == 1 && $_SESSION['uid'] != 10 && $_SESSION['uid'] != 2) return;
		// if ($rc == 2 && $_SESSION['uid'] != 11 && $_SESSION['uid'] != 2) return;
		// if ($rc == 3 && $_SESSION['uid'] != 12 && $_SESSION['uid'] != 2) return;
		// if ($rc == 4 && $_SESSION['uid'] != 13 && $_SESSION['uid'] != 2) return;
		// if ($rc == 5 && $_SESSION['uid'] != 14 && $_SESSION['uid'] != 2) return;
		// $pm = "Warenbörsen-Angebot ".$offerId." wurde gelöscht<br>Es beinhaltete ".$data['count']." Mal ".$data['gcount']." ".getgoodname($data['ggoods_id'])." gegen ".$data['wcount']." ".getgoodname($data['wgoods_id']);
		// $this->db->query("START TRANSACTION");
		// $this->db->query("DELETE FROM stu_trade_offers WHERE offer_id=".$offerId." LIMIT 1");
		// $this->db->query("COMMIT");
		// $this->db->query("DELETE FROM stu_trade_marks WHERE offer_id=".$offerId);
		// $this->send_pm(1,$data['user_id'],$pm,2);
		// return "Angebot ".$offerId." eingezogen";
	// }

	function getgivegoodlist($network) { 
		if (!check_int($network)) return;
		return $this->db->query("SELECT a.goods_id,a.count,b.name FROM stu_trade_goods as a LEFT JOIN stu_goods as b USING(goods_id) WHERE (a.mode='0' OR ISNULL(a.mode)) AND a.network_id = ".$network." AND a.user_id=".$this->uid." ORDER BY b.sort"); 
	}

	function getwantgoodlist() { return $this->db->query("SELECT goods_id,name FROM stu_goods WHERE view='1' ORDER BY sort"); }

	function newoffer($network,$gg,$wg,$gc,$wc,$acount)
	{
		if (!check_int($network)) return;
		if (!check_int($gg)) return;
		if (!check_int($wg)) return;
		if (!check_int($gc)) return;
		if (!check_int($wc)) return;
		if (!check_int($acount)) return;

		
		if ($acount > 255) return "Es sind maximal 255 Angebote möglich";
		if ($this->db->query("SELECT COUNT(offer_id) FROM stu_trade_offers WHERE network_id = ".$network." AND user_id=".$this->uid,1) >= 20) return "Es können maximal 20 Angebote pro Warenbörse erstellt werden.";
		if ($gg == $wg) return "Es kann nicht die selbe Ware angeboten und verlangt werden";
		if ($gc <= 0 || $gc > 9999) return "Es muss ein Wert zwischen 1 und 9999 eingegeben werden";
		if ($wc <= 0 || $wc > 9999) return "Es muss ein Wert zwischen 1 und 9999 eingegeben werden";
		
		if ($this->db->query("SELECT view FROM stu_goods WHERE goods_id=".$wg." LIMIT 1",1) == 0) return "Diese Ware kann nicht verlangt werden";
		
		$c = $this->db->query("SELECT a.count FROM stu_trade_goods as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.network_id = ".$network." AND (a.mode='0' OR ISNULL(a.mode)) AND a.goods_id=".$gg." AND a.user_id=".$this->uid,1);
		if ($c == 0) return;
		if ($c < $gc*$acount) return "Es wird ".($gc*$acount)." ".$this->db->query("SELECT name FROM stu_goods WHERE goods_id=".$gg,1)." benötigt - Es sind jedoch nur ".$c." vorhanden";
		
		
		// volume check
		$wball = $this->db->query("SELECT max_storage FROM stu_trade_networks WHERE network_id = ".$network."",1);
		// $wbmod = $this->db->query("SELECT value FROM stu_game_vars WHERE var = 'wblimitmod'",1);

		$myall = $this->tradesumnet($this->uid,$network); 
		// $mymod = $this->tradesummod($this->uid) ;	
		
		$volumediff = $acount * ($wc - $gc);
		
		if ($volumediff > 0) {
			if ($myall + $volumediff > $wball) {
				return "Dieses Angebot würde, falls angenommen, die zulässige maximale Lagermenge für Waren an der Warenbörse von ".$wball." übersteigen. Bitte eine kleinere Gesamtmenge an Waren verlangen.";
			}
		}
		
		$data = $this->db->query("SELECT SUM(gcount*count) as gc,SUM(wcount*count) as wc FROM stu_trade_offers WHERE network_id = ".$network." AND ggoods_id=".$gg." AND wgoods_id=".$wg,4);
		if ($data['gc'] == 0 || !$gc) $vd = 0;
		else $vd = @round(@$data['wc']/$data['gc'],2);
		$od = @round(@$wc/$gc,2);
		if ($vd != 0) $war = round((100/$vd)*$od);
		else $war = 100;
		
		$this->db->query("START TRANSACTION");
		$this->lowerstorage($network,$gg,$gc*$acount,$this->uid);
		$this->db->query("INSERT INTO stu_trade_offers (user_id,date,count,ggoods_id,gcount,wgoods_id,wcount,rel,network_id) VALUES ('".$this->uid."',NOW(),'".$acount."','".$gg."','".$gc."','".$wg."','".$wc."','".$war."','".$network."')");
		$this->db->query("COMMIT");
		return "Angebot erstellt";
	}

	function markoffer($network,$offerId)
	{
		if (!check_int($network)) return;
		if (!check_int($offerId)) return;	
		if ($this->db->query("SELECT offer_id FROM stu_trade_offers WHERE offer_id=".$offerId." LIMIT 1",1) == 0) return;
		if ($this->db->query("SELECT offer_id FROM stu_trade_marks WHERE offer_id=".$offerId." AND user_id=".$this->uid." LIMIT 1",1) > 0) return "Das Angebot wurde bereits von Dir gemeldet";
		$this->db->query("INSERT INTO stu_trade_marks (offer_id,user_id) VALUES ('".$offerId."','".$this->uid."')");	
		$this->db->query("UPDATE stu_trade_offers SET marks=marks+1 WHERE offer_id=".$offerId." LIMIT 1");
		return "Das Angebot wurde gemeldet";
	}

	function change_offer_count($network,$cn,$offerId)
	{
		if (!check_int($network)) return;
		if (!check_int($cn)) return;
		if (!check_int($offerId)) return;
		$data = $this->db->query("SELECT offer_id,count,ggoods_id,gcount FROM stu_trade_offers WHERE network_id = ".$network." AND offer_id=".$offerId." AND user_id=".$this->uid." LIMIT 1",4);
		if ($data == 0) return;
		if ($data['count'] > $cn) return "Die Anzahl kann nur erhöht werden";
		if ($cn > 255) $cn = 255;
		$cn -= $data['count'];
		if ($cn <= 0) return;
		$res = $this->db->query("SELECT count FROM stu_trade_goods WHERE network_id = ".$network." AND offer_id=0 AND user_id=".$this->uid." AND goods_id=".$data['ggoods_id']." LIMIT 1",1);
		if ($res < $data['gcount']) return "Es wird mindestens ".$data['gcount']." ".$this->db->query("SELECT name FROM stu_goods WHERE goods_id=".$data['ggoods_id'],1)." benötigt";
		if ($res < $cn*$data['gcount']) $cn = floor($res/$data['gcount']);
		$this->lowerstorage($network,$data['ggoods_id'],($data['gcount']*$cn),$this->uid);
		$this->db->query("UPDATE stu_trade_offers SET count=count+".$cn." WHERE network_id = ".$network." AND offer_id=".$offerId." LIMIT 1");
		return "Das Angebot wurde um ".$cn." Einheiten erhöht";
	}
	
	function getownofferlist($network) { 
		if (!check_int($network)) return;
		return $this->db->query("SELECT offer_id,user_id,UNIX_TIMESTAMP(date) as date_t,count as ocount,ggoods_id,gcount,wgoods_id,wcount FROM stu_trade_offers WHERE user_id=".$_SESSION['uid']." AND network_id = ".$network." ORDER BY date DESC"); 
	}

	// function getpricedate() { $this->pricedate = $this->db->query("SELECT UNIX_TIMESTAMP(MAX(date)) FROM stu_trade_prices",1); }

	// function getcurrentprices() { return $this->db->query("SELECT a.goods_id,a.name,b.price,b.price2 FROM stu_goods as a LEFT JOIN stu_trade_prices as b USING(goods_id) WHERE a.view='1' AND UNIX_TIMESTAMP(b.date)='".$this->pricedate."' ORDER BY a.sort"); }

	// function goodtransfer(&$recipient,&$goods_id,&$count)
	// {
		// if ($count <= 1) return "Es müssen mindestens 2 Einheiten überwiesen werden";
		// if ($count > 15000) return "Es dürfen maximal 15000 Einheiten auf einmal überwiesen werden";
		// if ($this->db->query("SELECT id FROM stu_user WHERE id=".$recipient." LIMIT 1",1) == 0) return "Empfänger nicht vorhanden";
		// if ($this->db->query("SELECT count FROM stu_trade_goods WHERE offer_id=0 AND user_id=".$this->uid." AND goods_id=".$goods_id." LIMIT 1",1) < $count) return "Du hast keine ".$count." ".getgoodname($goods_id)." in Deinem Konto";
		
		
		// $wball = $this->db->query("SELECT value FROM stu_game_vars WHERE var = 'wblimitall'",1);
		// $wbmod = $this->db->query("SELECT value FROM stu_game_vars WHERE var = 'wblimitmod'",1);

		// $rcall = $this->tradesumall($recipient); 
		// $rcmod = $this->tradesummod($recipient) ;	
		
		
		// if ($count + $rcall > $wball) return "Diese Überweisung übersteigt das Warenlimit des Empfängers";
		// if ($goods_id > 1000) {
			// if ($count + $rcmod > $wbmod) return "Diese Überweisung übersteigt das Modullimit des Empfängers";
		// }
		
		
		
		// $this->lowerstorage($goods_id,$count,$this->uid);
		// if ($recipient > 100) $count = floor(($count/100)*95);
		// $this->upperstorage($goods_id,$count,$recipient);
		// $this->send_pm($this->uid,$recipient,"Der Siedler ".addslashes($this->sess['user'])." hat Dir ".$count." ".getgoodname($goods_id)." überwiesen",2);
		// return "Es wurden ".$count." ".getgoodname($goods_id)." überwiesen";
	// }
	
	
	
	function getTradeNetworks() {
	
		$res = array();
	
		$qry = "SELECT net.*, 
          (SELECT sum(count) FROM stu_trade_goods as goods WHERE goods.user_id = ".$this->uid." AND goods.network_id = net.network_id) as goodsum,
          (SELECT count(*) FROM stu_trade_offers as offers WHERE offers.user_id = ".$this->uid." AND offers.network_id = net.network_id) as offercount, 
          (SELECT count(*) FROM stu_trade_offers as offers WHERE offers.network_id = net.network_id) as totaloffers, 		  
          (SELECT SUM(offers.count * GREATEST(offers.wcount,offers.gcount)) FROM stu_trade_offers as offers WHERE offers.user_id = ".$this->uid." AND offers.network_id = net.network_id) as offergoodsum,
          (SELECT count(*) FROM stu_ships as ships WHERE ships.user_id = ".$this->uid." AND ships.cx = net.cx AND ships.cy = net.cy) as shipsthere,
          (SELECT rumps_id FROM stu_ships as ships WHERE ships.id = net.ships_id) as wbpic,
		  (SELECT users.user FROM stu_ships as ships LEFT JOIN stu_user as users ON ships.user_id = users.id WHERE ships.id = net.ships_id) as wbuser
FROM stu_trade_networks as net WHERE 1";
	
	
		$result = $this->db->query($qry);
		
		while($data = mysql_fetch_assoc($result)) {
			array_push($res,$data);
		}
		
		return $res;
	
	
	
	
	
	
	
	
	
	
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>