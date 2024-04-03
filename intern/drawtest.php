<?php

$dir = dirname(__FILE__);
echo "<p>Full path to this dir: " . $dir . "</p>";

function drawrandom($modules)
{
	$chance = 0;
	for ($i = 1; $i <= $modules[count]; $i++) 
	{
		$chance += $modules[$i][chance];
	}
	$rand = rand(1,$chance);
	$chance = 0;
	for ($i = 1; $i <= $modules[count]; $i++) 
	{
		if ($rand > $chance)
		{
			$result = $modules[$i][id];
		}
		$chance += $modules[$i][chance];
	}
	return $result;
}


function drawmodule($goodid)
{
	$npc = rand (1,100);
	if ($npc <= 60)
	{
		// Siedler-Module
		if ($goodid == 901)
		{
			$modules[count] = 10;
				
			$modules[1][id] = 200;
			$modules[1][chance] = 10;
				
			$modules[2][id] = 201;
			$modules[2][chance] = 20;
			
			$modules[3][id] = 202;
			$modules[3][chance] = 10;
			
			$modules[4][id] = 203;
			$modules[4][chance] = 20;			
			
			$modules[5][id] = 204;
			$modules[5][chance] = 30;
			
			$modules[6][id] = 205;
			$modules[6][chance] = 20;
			
			$modules[7][id] = 206;
			$modules[7][chance] = 30;
			
			$modules[8][id] = 207;
			$modules[8][chance] = 40;
			
			$modules[9][id] = 218;
			$modules[9][chance] = 20;
			
			$modules[10][id] = 219;
			$modules[10][chance] = 30;
		}
		else if ($goodid == 902)
		{
			$modules[count] = 12;
				
			$modules[1][id] = 250;
			$modules[1][chance] = 10;
				
			$modules[2][id] = 251;
			$modules[2][chance] = 20;

			$modules[3][id] = 252;
			$modules[3][chance] = 30;
						
			$modules[4][id] = 253;
			$modules[4][chance] = 20;
				
			$modules[5][id] = 254;
			$modules[5][chance] = 30;
			
			$modules[6][id] = 255;
			$modules[6][chance] = 40;
			
			$modules[7][id] = 256;
			$modules[7][chance] = 20;
				
			$modules[8][id] = 257;
			$modules[8][chance] = 30;
			
			$modules[9][id] = 258;
			$modules[9][chance] = 40;
			
			$modules[10][id] = 259;
			$modules[10][chance] = 20;
				
			$modules[11][id] = 260;
			$modules[11][chance] = 30;
			
			$modules[12][id] = 261;
			$modules[12][chance] = 40;
		}
		else if ($goodid == 903)
		{
			$modules[count] = 5;
				
			$modules[1][id] = 300;
			$modules[1][chance] = 10;
				
			$modules[2][id] = 301;
			$modules[2][chance] = 20;
				
			$modules[3][id] = 302;
			$modules[3][chance] = 30;
				
			$modules[4][id] = 303;
			$modules[4][chance] = 40;
			
			$modules[5][id] = 308;
			$modules[5][chance] = 30;
		}
		else if ($goodid == 904)
		{
			$modules[count] = 7;
				
			$modules[1][id] = 350;
			$modules[1][chance] = 10;
				
			$modules[2][id] = 351;
			$modules[2][chance] = 20;
				
			$modules[3][id] = 352;
			$modules[3][chance] = 30;
				
			$modules[4][id] = 353;
			$modules[4][chance] = 30;
			
			$modules[5][id] = 354;
			$modules[5][chance] = 30;
			
			$modules[6][id] = 355;
			$modules[6][chance] = 40;
			
			$modules[7][id] = 362;
			$modules[7][chance] = 30;
		}
		else if ($goodid == 905)
		{
			$modules[count] = 5;
				
			$modules[1][id] = 400;
			$modules[1][chance] = 10;
				
			$modules[2][id] = 401;
			$modules[2][chance] = 20;
				
			$modules[3][id] = 402;
			$modules[3][chance] = 30;
				
			$modules[4][id] = 403;
			$modules[4][chance] = 40;
			
			$modules[5][id] = 412;
			$modules[5][chance] = 30;
		}
		else if ($goodid == 906)
		{
			$modules[count] = 21;
				
			$modules[1][id] = 450;
			$modules[1][chance] = 10;
				
			$modules[2][id] = 451;
			$modules[2][chance] = 20;
				
			$modules[3][id] = 452;
			$modules[3][chance] = 30;
				
			$modules[4][id] = 453;
			$modules[4][chance] = 40;
			
			$modules[5][id] = 456;
			$modules[5][chance] = 10;
				
			$modules[6][id] = 457;
			$modules[6][chance] = 20;
				
			$modules[7][id] = 458;
			$modules[7][chance] = 30;
				
			$modules[8][id] = 459;
			$modules[8][chance] = 40;
			
			$modules[9][id] = 462;
			$modules[9][chance] = 10;
				
			$modules[10][id] = 463;
			$modules[10][chance] = 20;
				
			$modules[11][id] = 464;
			$modules[11][chance] = 30;
				
			$modules[12][id] = 465;
			$modules[12][chance] = 40;
			
			$modules[13][id] = 468;
			$modules[13][chance] = 10;
				
			$modules[14][id] = 469;
			$modules[14][chance] = 20;
				
			$modules[15][id] = 470;
			$modules[15][chance] = 30;
				
			$modules[16][id] = 471;
			$modules[16][chance] = 40;
			
			$modules[17][id] = 474;
			$modules[17][chance] = 10;
				
			$modules[18][id] = 475;
			$modules[18][chance] = 20;
				
			$modules[19][id] = 476;
			$modules[19][chance] = 30;
				
			$modules[20][id] = 477;
			$modules[20][chance] = 40;

			$modules[21][id] = 492;
			$modules[21][chance] = 30;
		}
		else if ($goodid == 907)
		{
			$modules[count] = 4;
				
			$modules[1][id] = 500;
			$modules[1][chance] = 10;
				
			$modules[2][id] = 501;
			$modules[2][chance] = 20;
				
			$modules[3][id] = 502;
			$modules[3][chance] = 30;
				
			$modules[4][id] = 503;
			$modules[4][chance] = 40;
		}
		else if ($goodid == 908)
		{
			$modules[count] = 4;
				
			$modules[1][id] = 550;
			$modules[1][chance] = 10;
				
			$modules[2][id] = 551;
			$modules[2][chance] = 20;
				
			$modules[3][id] = 552;
			$modules[3][chance] = 30;
				
			$modules[4][id] = 553;
			$modules[4][chance] = 40;
		}
	}
	else
	{
		// NPC-Module
		if ($goodid == 901)
		{
			$modules[count] = 5;
				
			$modules[1][id] = 213;
			$modules[1][chance] = 20;
			
			$modules[2][id] = 214;
			$modules[2][chance] = 30;
			
			$modules[3][id] = 220;
			$modules[3][chance] = 40;
			
			$modules[4][id] = 222;
			$modules[4][chance] = 40;
			
			$modules[5][id] = 223;
			$modules[5][chance] = 40;
		}
		else if ($goodid == 902)
		{
			$modules[count] = 1;
				
			$modules[1][id] = 266;
			$modules[1][chance] = 40;
		}
		else if ($goodid == 903)
		{
			$modules[count] = 2;
				
			$modules[1][id] = 306;
			$modules[1][chance] = 20;
			
			$modules[2][id] = 310;
			$modules[2][chance] = 40;
		}
		else if ($goodid == 904)
		{
			$modules[count] = 2;
				
			$modules[1][id] = 357;
			$modules[1][chance] = 40;
			
			$modules[2][id] = 358;
			$modules[2][chance] = 20;
		}
		else if ($goodid == 905)
		{
			$modules[count] = 3;
				
			$modules[1][id] = 406;
			$modules[1][chance] = 20;
			
			$modules[2][id] = 413;
			$modules[2][chance] = 30;
			
			$modules[3][id] = 416;
			$modules[3][chance] = 40;
		}
		else if ($goodid == 906)
		{
			$modules[count] = 9;
				
			$modules[1][id] = 483;
			$modules[1][chance] = 30;

			$modules[2][id] = 484;
			$modules[2][chance] = 40;
			
			$modules[3][id] = 494;
			$modules[3][chance] = 30;

			$modules[4][id] = 800;
			$modules[4][chance] = 30;

			$modules[5][id] = 801;
			$modules[5][chance] = 40;
			
			$modules[6][id] = 802;
			$modules[6][chance] = 30;

			$modules[7][id] = 803;
			$modules[7][chance] = 40;
			
			$modules[8][id] = 804;
			$modules[8][chance] = 30;

			$modules[9][id] = 805;
			$modules[9][chance] = 40;		
		}
		else if ($goodid == 907)
		{
			$modules[count] = 4;
				
			$modules[1][id] = 506;
			$modules[1][chance] = 20;
			
			$modules[2][id] = 508;
			$modules[2][chance] = 30;

			$modules[3][id] = 510;
			$modules[3][chance] = 30;

			$modules[4][id] = 511;
			$modules[4][chance] = 40;
		}
		else if ($goodid == 908)
		{
			$modules[count] = 2;
				
			$modules[1][id] = 556;
			$modules[1][chance] = 20;

			$modules[2][id] = 559;
			$modules[2][chance] = 40;		
		}
	}
	return drawrandom($modules);
}

echo drawmodule(901)."<br>";
echo drawmodule(902)."<br>";
echo drawmodule(903)."<br>";
echo drawmodule(904)."<br>";
echo drawmodule(905)."<br>";
echo drawmodule(906)."<br>";
echo drawmodule(907)."<br>";
echo drawmodule(908)."<br>";
?>