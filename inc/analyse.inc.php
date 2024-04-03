<?php

$domtechgood = 99;
if (($field['buildings_id'] == 80) || ($field['buildings_id'] == 81) || ($field['buildings_id'] == 82))
{

	$res = $db->query("SELECT a.goods_id,a.count,b.name FROM stu_colonies_storage as a left join stu_goods as b on a.goods_id = b.goods_id WHERE a.colonies_id=".$_GET['id']." AND (a.goods_id = ".$domtechgood.")");
	if (mysql_num_rows($res) != 0)
	{
		echo "<br><table><tr><td style=\"border : 1px solid #262323;\"><b>Datenkern</b></td></tr><tr><td style=\"border : 1px solid #262323;\">";
		while($data=mysql_fetch_assoc($res))
		{

			echo " <a href=?ps=".$_SESSION['pagesess']."&p=colony&s=sc&id=".$_GET["id"]."&a=ran&mod=".$data[goods_id]."><img src=".$gfx."/goods/".$data[goods_id].".gif border=0> Datenkern einspeisen</a><br>";
		}
		echo "</td></tr></table>";
	}
}

if (($field['buildings_id'] == 80) || ($field['buildings_id'] == 81) || ($field['buildings_id'] == 82) || ($field['buildings_id'] == 99))
{
		$res = $db->query("SELECT research_id FROM stu_researched WHERE user_id = ".$_SESSION['uid']." AND research_id = 8007",1);
		
		if ($res) {
			echo "<br><table><tr><td style=\"border : 1px solid #262323;\"><b>Datenkern-Kopie erstellen</b></td></tr><tr><td style=\"border : 1px solid #262323;\">";

				echo "Kosten: <img src=gfx//goods/98.gif title='Dominion-Bauteile'> 5<br><br> <a href=?ps=".$_SESSION['pagesess']."&p=colony&s=sc&id=".$_GET["id"]."&a=cdc><img src=".$gfx."/goods/99.gif border=0> Kopie erstellen</a><br>";

			echo "</td></tr></table>";
		}
}






?>
