<?php
class ferg
{
	function __construct()
	{
		global $db,$ship;
		$this->db = $db;
		$this->ship = $ship;
		if ($this->systems_id > 0) die(show_error(902));
		$tar = $this->db->query("SELECT id FROM stu_ships WHERE cx=".$this->ship->cx." AND cy=".$this->ship->cy." AND (rumps_id=9000)",1);
		if ($tar == 0) die(show_error(902));
	}
	
	function changegood()
	{
		if ($this->db->query("SELECT count FROM stu_ships_storage WHERE goods_id=998 AND ships_id=".$this->ship->id." LIMIT 1",1) == 0) return "Es befindet sich kein Gutschein auf dem Schiff";
		$data = $this->getrandomgood();
		if ($this->db->query("SELECT SUM(count) FROM stu_ships_storage WHERE ships_id=".$this->ship->id,1)+$data['count']-1 > $this->ship->storage) return "Es steht nicht genügend Lagerraum auf dem Schiff zur Verfügung";
		$this->ship->lowerstorage($this->ship->id,998,1);
		$this->ship->upperstorage($this->ship->id,$data['goods_id'],$data['count']);
		return "Für den Gutschein erhälst du ".$data['count']." ".getgoodname($data['goods_id']);
	}
	
	function getrandomgood()
	{
		$rnd = rand(1,33);
		switch($rnd)
		{
			case 1:
				return array("goods_id" => 222,"count" => 28);
			case 2:
				return array("goods_id" => 223,"count" => 28);
			case 3:
				return array("goods_id" => 266,"count" => 28);
			case 4:
				return array("goods_id" => 310,"count" => 1);
			case 5:
				return array("goods_id" => 357,"count" => 3);
			case 6:
				return array("goods_id" => 416,"count" => 1);
			case 7:
				return array("goods_id" => 483,"count" => 3);
			case 8:
				return array("goods_id" => 484,"count" => 3);
			case 9:
				return array("goods_id" => 511,"count" => 1);
			case 10:
				return array("goods_id" => 559,"count" => 10);
			case 11:
				return array("goods_id" => 800,"count" => 3);
			case 12:
				return array("goods_id" => 801,"count" => 3);
			case 13:
				return array("goods_id" => 802,"count" => 3);
			case 14:
				return array("goods_id" => 803,"count" => 3);
			case 15:
				return array("goods_id" => 804,"count" => 3);
			case 16:
				return array("goods_id" => 805,"count" => 3);
			case 17:
				return array("goods_id" => 1101,"count" => 2);
			case 18:
				return array("goods_id" => 1102,"count" => 2);
			case 19:
				return array("goods_id" => 1103,"count" => 2);
			case 20:
				return array("goods_id" => 1104,"count" => 2);
			case 21:
				return array("goods_id" => 1105,"count" => 2);
			case 22:
				return array("goods_id" => 1106,"count" => 2);
			case 23:
				return array("goods_id" => 1107,"count" => 2);
			case 24:
				return array("goods_id" => 1108,"count" => 2);
			case 25:
				return array("goods_id" => 1110,"count" => 2);
			case 26:
				return array("goods_id" => 1201,"count" => 2);
			case 27:
				return array("goods_id" => 1202,"count" => 2);
			case 28:
				return array("goods_id" => 1203,"count" => 2);
			case 29:
				return array("goods_id" => 1204,"count" => 2);
			case 30:
				return array("goods_id" => 1205,"count" => 2);
			case 31:
				return array("goods_id" => 1206,"count" => 2);
			case 32:
				return array("goods_id" => 1207,"count" => 2);
			case 33:
				return array("goods_id" => 1208,"count" => 2);
		}
	}
}
?>