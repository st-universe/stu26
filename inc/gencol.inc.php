<?php
function getfieldstockbyclass($c) 
{
	$s[count]      =  56;
	for ($i=1;$i<=$s[count];$i++) { 
		$s[$i][stock] = 0;
		$s[$i][type] = $i;
	}
	if ($c == 1)
	{
		$s[ges] = 0;
		$s[6][stock]  = rand(4,6);
		$s[ges] += $s[6][stock];
		$s[7][stock]  = rand(3,5);
		$s[ges] += $s[7][stock];
		$s[31][stock]  = rand(5,8);
		$s[ges] += $s[31][stock];
		$s[5][stock]  = rand(8,12);
		$s[ges] += $s[5][stock];
		$s[2][stock]  = rand(8,12);
		$s[ges] += $s[2][stock];
		$s[1][stock]  = 54-$s[ges];
	} 
	if ($c == 20)
	{
		$s[ges] = 0;
		$s[6][stock]  = rand(2,4);
		$s[ges] += $s[6][stock];
		$s[7][stock]  = rand(1,3);
		$s[ges] += $s[7][stock];
		$s[31][stock]  = rand(3,5);
		$s[ges] += $s[31][stock];
		$s[5][stock]  = rand(6,8);
		$s[ges] += $s[5][stock];
		$s[2][stock]  = rand(6,8);
		$s[ges] += $s[2][stock];
		$s[1][stock]  = 35-$s[ges];
	} 
	elseif ($c == 2)
	{
		$s[ges] = 0;
		$s[6][stock]  = rand(3,5);
		$s[ges] += $s[6][stock];
		$s[31][stock]  = rand(3,6);
		$s[ges] += $s[31][stock];
		$s[5][stock]  = rand(7,10);
		$s[ges] += $s[5][stock];
		$s[2][stock]  = rand(14,20);
		$s[ges] += $s[2][stock];
		$s[1][stock]  = 54-$s[ges];
	} 
	elseif ($c == 21)
	{
		$s[ges] = 0;
		$s[6][stock]  = rand(2,3);
		$s[ges] += $s[6][stock];
		$s[31][stock]  = rand(2,3);
		$s[ges] += $s[31][stock];
		$s[5][stock]  = rand(4,7);
		$s[ges] += $s[5][stock];
		$s[2][stock]  = rand(9,13);
		$s[ges] += $s[2][stock];
		$s[1][stock]  = 35-$s[ges];
	} 
	elseif ($c == 3)
	{
		$s[ges] = 0;
		$s[6][stock]  = rand(3,5);
		$s[ges] += $s[6][stock];
		$s[31][stock]  = rand(1,2);
		$s[ges] += $s[31][stock];
		$s[5][stock]  = rand(26,35);
		$s[ges] += $s[5][stock];
		$s[2][stock]  = rand(5,7);
		$s[ges] += $s[2][stock];
		$s[1][stock]  = 54-$s[ges];
	} 
	elseif ($c == 22)
	{
		$s[ges] = 0;
		$s[6][stock]  = rand(1,2);
		$s[ges] += $s[6][stock];
		$s[31][stock]  = rand(0,1);
		$s[ges] += $s[31][stock];
		$s[5][stock]  = rand(17,23);
		$s[ges] += $s[5][stock];
		$s[2][stock]  = rand(2,4);
		$s[ges] += $s[2][stock];
		$s[1][stock]  = 35-$s[ges];
	} 
	elseif ($c == 4)
	{
		$s[ges] = 0;
		$s[32][stock]  = rand(20,30);
		$s[ges] += $s[32][stock];
		$s[7][stock]  = 54-$s[ges];
	} 
	elseif ($c == 23)
	{
		$s[ges] = 0;
		$s[32][stock]  = rand(13,20);
		$s[ges] += $s[32][stock];
		$s[7][stock]  = 35-$s[ges];
	} 
	elseif ($c == 5)
	{
		$s[ges] = 0;
		$s[33][stock]  = rand(12,18);
		$s[ges] += $s[33][stock];
		$s[14][stock]  = rand(9,14);
		$s[ges] += $s[14][stock];
		$s[6][stock]  = 54-$s[ges];
	} 
	elseif ($c == 24)
	{
		$s[ges] = 0;
		$s[33][stock]  = rand(8,12);
		$s[ges] += $s[33][stock];
		$s[14][stock]  = rand(6,9);
		$s[ges] += $s[14][stock];
		$s[6][stock]  = 35-$s[ges];
	} 
	elseif ($c == 6)
	{
		$s[ges] = 0;
		$s[6][stock]  = rand(5,8);
		$s[ges] += $s[6][stock];
		$s[8][stock]  = rand(5,9);
		$s[ges] += $s[8][stock];
		$s[34][stock]  = rand(5,8);
		$s[ges] += $s[34][stock];
		$s[15][stock]  = rand(5,7);
		$s[ges] += $s[15][stock];
		$s[9][stock]  = 54-$s[ges];
	} 
	elseif ($c == 25)
	{
		$s[ges] = 0;
		$s[6][stock]  = rand(3,5);
		$s[ges] += $s[6][stock];
		$s[8][stock]  = rand(3,6);
		$s[ges] += $s[8][stock];
		$s[34][stock]  = rand(3,5);
		$s[ges] += $s[34][stock];
		$s[15][stock]  = rand(3,5);
		$s[ges] += $s[15][stock];
		$s[9][stock]  = 35-$s[ges];
	} 
	elseif ($c == 7)
	{
		$s[ges] = 0;
		$s[34][stock]  = rand(5,9);
		$s[ges] += $s[34][stock];
		$s[15][stock]  = rand(4,6);
		$s[ges] += $s[15][stock];
		$s[18][stock]  = rand(3,5);
		$s[ges] += $s[18][stock];
		$s[29][stock]  = rand(2,3);
		$s[ges] += $s[29][stock];
		$s[22][stock]  = rand(2,4);
		$s[ges] += $s[22][stock];
		$s[27][stock]  = rand(4,7);
		$s[ges] += $s[27][stock];
		$s[9][stock]  = 54-$s[ges];
	} 
	elseif ($c == 26)
	{
		$s[ges] = 0;
		$s[34][stock]  = rand(3,6);
		$s[ges] += $s[34][stock];
		$s[15][stock]  = rand(3,5);
		$s[ges] += $s[15][stock];
		$s[18][stock]  = rand(2,3);
		$s[ges] += $s[18][stock];
		$s[29][stock]  = rand(1,2);
		$s[ges] += $s[29][stock];
		$s[22][stock]  = rand(1,2);
		$s[ges] += $s[22][stock];
		$s[27][stock]  = rand(3,5);
		$s[ges] += $s[27][stock];
		$s[9][stock]  = 35-$s[ges];
	} 
	elseif ($c == 8)
	{
		$s[ges] = 0;
		$s[35][stock]  = rand(5,9);
		$s[ges] += $s[35][stock];
		$s[13][stock]  = rand(5,8);
		$s[ges] += $s[13][stock];
		$s[23][stock]  = rand(3,5);
		$s[ges] += $s[23][stock];
		$s[28][stock]  = rand(4,7);
		$s[ges] += $s[28][stock];
		$s[17][stock]  = rand(2,3);
		$s[ges] += $s[17][stock];
		$s[11][stock]  = 54-$s[ges];
	} 
	elseif ($c == 27)
	{
		$s[ges] = 0;
		$s[35][stock]  = rand(3,6);
		$s[ges] += $s[35][stock];
		$s[13][stock]  = rand(3,5);
		$s[ges] += $s[13][stock];
		$s[23][stock]  = rand(2,3);
		$s[ges] += $s[23][stock];
		$s[28][stock]  = rand(2,4);
		$s[ges] += $s[28][stock];
		$s[17][stock]  = rand(1,2);
		$s[ges] += $s[17][stock];
		$s[11][stock]  = 35-$s[ges];
	} 
	elseif ($c == 9)
	{
		$s[ges] = 0;
		$s[55][stock]  = rand(9,12);
		$s[ges] += $s[55][stock];
		$s[52][stock]  = 54-$s[ges];
	} 
	elseif ($c == 29)
	{
		$s[ges] = 0;
		$s[54][stock]  = rand(5,8);
		$s[ges] += $s[54][stock];
		$s[51][stock]  = 35-$s[ges];
	} 
	elseif ($c == 28)
	{
		$s[ges] = 0;
		$s[12][stock]  = rand(5,8);
		$s[ges] += $s[12][stock];
		$s[10][stock]  = 35-$s[ges];
	} 
	elseif ($c == 10)
	{
		$s[ges] = 0;
		$s[31][stock]  = rand(5,8);
		$s[ges] += $s[31][stock];
		$s[25][stock]  = rand(3,5);
		$s[ges] += $s[25][stock];
		$s[4][stock]  = rand(14,20);
		$s[ges] += $s[4][stock];
		$s[201][stock]  = 54-$s[ges];
	} 
	return $s;
}

function getfieldbyxyw($x,$y,$w) 
{
	return (($y - 1) * $w) + ($x - 1);
}

function pickrandomfield($c,$f) 
{
	$size = getsizebyclass($c);
	$pick[x] = rand(1,$size[w]);
	$pick[y] = rand(1,$size[h]);
	if ($f[getfieldbyxyw($pick[x],$pick[y],$size[w])] == 0)
	{	
		if (($pick[y] == 1) || ($pick[y] == $size[h])) $pick[border] = 1;
		if ($size[h] == 6)
		{
			if (($pick[y] < ($size[h]/3)+1) || ($pick[y] > (2*$size[h])/3)) $pick[polar] = 1;
			else $pick[equat] = 1;
		}
		else 
		{
			if ($pick[y] == 3) $pick[equat] = 1;
			elseif ($pick[border] == 1) $pick[polar] = 1;
		}
		return $pick;
	}
	else return pickrandomfield($c,$f);
}

function pickrandomfieldonce($c,$f) 
{
	$size = getsizebyclass($c);
	$pick[x] = rand(1,$size[w]);
	$pick[y] = rand(1,$size[h]);
	if (($pick[y] == 1) || ($pick[y] == $size[h])) $pick[border] = 1;
	if ($size[h] == 6)
	{
		if (($pick[y] < ($size[h]/3)+1) || ($pick[y] > (2*$size[h])/3)) $pick[polar] = 1;
		else $pick[equat] = 1;
	}
	else 
	{
		if ($pick[y] == 3) $pick[equat] = 1;
		elseif ($pick[border] == 1) $pick[polar] = 1;
	}
	return $pick;
}

function fieldborderstype($x,$y,$f,$t,$c) 
{
	$size = getsizebyclass($c);
	if (($f[getfieldbyxyw($x+1,$y,$size[w])] == $t) && (($x+1) <= $size[w])) return true;
	elseif (($f[getfieldbyxyw($x-1,$y,$size[w])] == $t) && (($x-1) > 0)) return true;
	elseif (($f[getfieldbyxyw($x,$y+1,$size[w])] == $t) && (($y+1) <= $size[h])) return true;
	elseif (($f[getfieldbyxyw($x,$y-1,$size[w])] == $t) && (($y-1) > 0)) return true;
	else return false;
}

function fieldborderstypecount($x,$y,$f,$t,$c) 
{
	$size = getsizebyclass($c);
	$bla = 0;
	if (($f[getfieldbyxyw($x+1,$y,$size[w])] == $t) && (($x+1) <= $size[w])) $bla++;
	elseif (($f[getfieldbyxyw($x-1,$y,$size[w])] == $t) && (($x-1) > 0)) $bla++;
	elseif (($f[getfieldbyxyw($x,$y+1,$size[w])] == $t) && (($y+1) <= $size[h])) $bla++;
	elseif (($f[getfieldbyxyw($x,$y-1,$size[w])] == $t) && (($y-1) > 0)) $bla++;
	return $bla;
}

function getsizebyclass($c) 
{
	$size[w] = 9;
	$size[h] = 6;
	$size[g] = 1;
	if (($c >= 11) && ($c <= 30))
	{
		$size[w] = 7;
		$size[h] = 5;
		$size[g] = 0;
	} 
	return $size;
}

function getgroundbyfield($f) 
{
	if (($f == 5) || ($f == 16)) return 73;
	elseif (($f == 6) || ($f == 14)) return 74;
	elseif ($f == 27) return 76;
	elseif ($f == 52) return 53;
	elseif ($f == 55) return 56;
	else return 71;
}

function generatefields($c)
{
	$size = getsizebyclass($c);
	$s = getfieldstockbyclass($c);
	// 11 -> 20, 12 -> 21, 13 -> 22
	if (($c == 1) || ($c == 2) || ($c == 3) || ($c == 20) || ($c == 21) || ($c == 22))
	{
		$overflow = 0;
		while ($s[6][stock] > 0)
		{
			$overflow++;
			if ($overflow > 100) 
			{
				$s[6][stock] -= 1;
				$s[1][stock] += 1;
			}
			else
			{
				$pick = pickrandomfield($c,$f);
				if ($pick[polar] == 1)
				{
					if ((fieldborderstype($pick[x],$pick[y],$f,6,$c)) || ($pick[border] == 1))
					{
						$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 6;
						$s[6][stock] -= 1;
					}
				}
			}
		}
		$overflow = 0;
		while ($s[7][stock] > 0)
		{
			$overflow++;
			if ($overflow > 100) 
			{
				$s[7][stock] -= 1;
				$s[1][stock] += 1;
			}
			else
			{
				$pick = pickrandomfield($c,$f);
				if ($pick[equat] == 1)
				{
					$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 7;
					$s[7][stock] -= 1;
				}
			}
		}
		$overflow = 0;
		while ($s[5][stock] > 0)
		{
			$overflow++;
			if ($overflow > 100) 
			{
				$s[5][stock] -= 1;
				$s[1][stock] += 1;
			}
			else
			{
				$pick = pickrandomfield($c,$f);
				if (!fieldborderstype($pick[x],$pick[y],$f,7,$c))
				{
					if ((fieldborderstypecount($pick[x],$pick[y],$f,6,$c) >= 2) || ((fieldborderstypecount($pick[x],$pick[y],$f,6,$c) == 1) && ($pick[border] == 1)))
					{
						$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 16;
						$s[5][stock] -= 1;
					}
					else
					{
						$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 5;
						$s[5][stock] -= 1;
					}
				}
			}
		}
		$overflow = 0;
		while ($s[31][stock] > 0)
		{
			$overflow++;
			if ($overflow > 100) 
			{
				$s[31][stock] -= 1;
				$s[1][stock] += 1;
			}
			else
			{
				$pick = pickrandomfield($c,$f);
				if (fieldborderstypecount($pick[x],$pick[y],$f,5,$c) < 4)
				{
					if ((fieldborderstype($pick[x],$pick[y],$f,6,$c)) && ($pick[polar] == 1))
					{
						$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 33;
						$s[31][stock] -= 1;
					}
					else
					{
						$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 31;
						$s[31][stock] -= 1;
					}
				}
			}
		}
		$overflow = 0;
		while ($s[2][stock] > 0)
		{
			$overflow++;
			if ($overflow > 100) 
			{
				$s[2][stock] -= 1;
				$s[1][stock] += 1;
			}
			else
			{
				$pick = pickrandomfield($c,$f);
				if ((($pick[border] == 1) || (fieldborderstype($pick[x],$pick[y],$f,3,$c))) && ($pick[equat] != 1))
				{
					$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 3;
					$s[2][stock] -= 1;
				}
				else
				{
					$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 2;
					$s[2][stock] -= 1;
				}
			}
		}
		while ($s[1][stock] > 0)
		{
			$pick = pickrandomfield($c,$f);
			$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 1;
			$s[1][stock] -= 1;
		}
	}
	// 14 -> 23, 20 -> 28, 19 -> 11
	elseif (($c == 4) || ($c == 9) || ($c == 23) || ($c == 11) || ($c == 28) || ($c == 29))
	{
		while ($s[32][stock] > 0)
		{
			$pick = pickrandomfield($c,$f);
			$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 32;
			$s[32][stock] -= 1;
		}
		while ($s[7][stock] > 0)
		{
			$pick = pickrandomfield($c,$f);
			$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 7;
			$s[7][stock] -= 1;
		}
		$overflow = 0;
		while ($s[54][stock] > 0)
		{
			$overflow++;
			if ($overflow > 100) 
			{
				$s[54][stock] -= 1;
				$s[51][stock] += 1;
			}
			else
			{
				$pick = pickrandomfield($c,$f);
				if (!fieldborderstype($pick[x],$pick[y],$f,54,$c))
				{
					$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 54;
					$s[54][stock] -= 1;
				}
			}
		}
		$overflow = 0;
		while ($s[55][stock] > 0)
		{
			$overflow++;
			if ($overflow > 100) 
			{
				$s[55][stock] -= 1;
				$s[52][stock] += 1;
			}
			else
			{
				$pick = pickrandomfield($c,$f);
				if (!fieldborderstype($pick[x],$pick[y],$f,55,$c))
				{
					$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 55;
					$s[55][stock] -= 1;
				}
			}
		}
		while ($s[51][stock] > 0)
		{
			$pick = pickrandomfield($c,$f);
			$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 51;
			$s[51][stock] -= 1;
		}
		while ($s[52][stock] > 0)
		{
			$pick = pickrandomfield($c,$f);
			$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 52;
			$s[52][stock] -= 1;
		}
		$overflow = 0;
		while ($s[12][stock] > 0)
		{
			$overflow++;
			if ($overflow > 100) 
			{
				$s[12][stock] -= 1;
				$s[10][stock] += 1;
			}
			else
			{
				$pick = pickrandomfield($c,$f);
				if (!fieldborderstype($pick[x],$pick[y],$f,12,$c))
				{
					$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 12;
					$s[12][stock] -= 1;
				}
			}
		}
		while ($s[10][stock] > 0)
		{
			$pick = pickrandomfield($c,$f);
			$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 10;
			$s[10][stock] -= 1;
		}
	}
	// 15 -> 24
	elseif (($c == 5) || ($c == 24))
	{
		while ($s[33][stock] > 0)
		{
			$pick = pickrandomfield($c,$f);
			$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 33;
			$s[33][stock] -= 1;
		}
		while ($s[14][stock] > 0)
		{
			$pick = pickrandomfield($c,$f);
			$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 14;
			$s[14][stock] -= 1;
		}
		while ($s[6][stock] > 0)
		{
			$pick = pickrandomfield($c,$f);
			$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 6;
			$s[6][stock] -= 1;
		}
	}
	// 16 -> 25, 17 -> 26
	elseif (($c == 6) || ($c == 25) || ($c == 7) || ($c == 26))
	{
		$overflow = 0;
		while ($s[18][stock] > 0)
		{
			$overflow++;
			if ($overflow > 100) 
			{
				$s[18][stock] -= 1;
				$s[9][stock] += 1;
			}
			else
			{
				$pick = pickrandomfield($c,$f);
				if (!fieldborderstype($pick[x],$pick[y],$f,18,$c))
				{
					$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 18;
					$s[18][stock] -= 1;
				}
			}
		}
		$overflow = 0;
		while ($s[29][stock] > 0)
		{
			$overflow++;
			if ($overflow > 100) 
			{
				$s[29][stock] -= 1;
				$s[9][stock] += 1;
			}
			else
			{
				$pick = pickrandomfield($c,$f);
				if (!fieldborderstype($pick[x],$pick[y],$f,29,$c) && !fieldborderstype($pick[x],$pick[y],$f,18,$c))
				{
					$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 29;
					$s[29][stock] -= 1;
				}
			}
		}
		$overflow = 0;
		while ($s[6][stock] > 0)
		{
			$overflow++;
			if ($overflow > 100) 
			{
				$s[6][stock] -= 1;
				$s[1][stock] += 1;
			}
			else
			{
				$pick = pickrandomfield($c,$f);
				if ($pick[polar] == 1)
				{
					if ((fieldborderstype($pick[x],$pick[y],$f,6,$c)) || ($pick[border] == 1))
					{
						$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 6;
						$s[6][stock] -= 1;
					}
				}
			}
		}
		$overflow = 0;
		while ($s[8][stock] > 0)
		{
			$overflow++;
			if ($overflow > 100) 
			{
				$s[8][stock] -= 1;
				$s[1][stock] += 1;
			}
			else
			{
				$pick = pickrandomfield($c,$f);
				if ($pick[equat] == 1)
				{
					$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 8;
					$s[8][stock] -= 1;
				}
			}
		}
		$overflow = 0;
		while ($s[22][stock] > 0)
		{
			$overflow++;
			if ($overflow > 100) 
			{
				$s[22][stock] -= 1;
				$s[9][stock] += 1;
			}
			else
			{
				$pick = pickrandomfield($c,$f);
				if (!fieldborderstype($pick[x],$pick[y],$f,22,$c))
				{
					$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 22;
					$s[22][stock] -= 1;
				}	
			}
		}
		$overflow = 0;
		while ($s[27][stock] > 0)
		{
			$overflow++;
			if ($overflow > 100) 
			{
				$s[27][stock] -= 1;
				$s[9][stock] += 1;
			}
			else
			{
				$pick = pickrandomfield($c,$f);
				if (!fieldborderstype($pick[x],$pick[y],$f,27,$c))
				{
					$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 27;
					$s[27][stock] -= 1;
				}
			}
		}
		while ($s[34][stock] > 0)
		{
			$pick = pickrandomfield($c,$f);
			$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 34;
			$s[34][stock] -= 1;
		}
		while ($s[15][stock] > 0)
		{
			$pick = pickrandomfield($c,$f);
			$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 15;
			$s[15][stock] -= 1;
		}
		while ($s[9][stock] > 0)
		{
			$pick = pickrandomfield($c,$f);
			$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 9;
			$s[9][stock] -= 1;
		}
	}
	// 18 -> 27
	elseif (($c == 8) || ($c == 27))
	{
		$overflow = 0;
		while ($s[17][stock] > 0)
		{
			$overflow++;
			if ($overflow > 100) 
			{
				$s[17][stock] -= 1;
				$s[11][stock] += 1;
			}
			else
			{
				$pick = pickrandomfield($c,$f);
				if (!fieldborderstype($pick[x],$pick[y],$f,17,$c))
				{
					$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 17;
					$s[17][stock] -= 1;
				}
			}
		}
		$overflow = 0;
		while ($s[28][stock] > 0)
		{
			$overflow++;
			if ($overflow > 100) 
			{
				$s[28][stock] -= 1;
				$s[11][stock] += 1;
			}
			else
			{
				$pick = pickrandomfield($c,$f);
				if (!fieldborderstype($pick[x],$pick[y],$f,28,$c) && !fieldborderstype($pick[x],$pick[y],$f,17,$c))
				{
					$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 28;
					$s[28][stock] -= 1;
				}	
			}
		}
		$overflow = 0;
		while ($s[23][stock] > 0)
		{
			$overflow++;
			if ($overflow > 100) 
			{
				$s[23][stock] -= 1;
				$s[11][stock] += 1;
			}
			else
			{
				$pick = pickrandomfield($c,$f);
				if (!fieldborderstype($pick[x],$pick[y],$f,23,$c))
				{
					$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 23;
					$s[23][stock] -= 1;
				}
			}
		}
		$overflow = 0;
		while ($s[13][stock] > 0)
		{
			$overflow++;
			if ($overflow > 100) 
			{
				$s[13][stock] -= 1;
				$s[11][stock] += 1;
			}
			else
			{
				$pick = pickrandomfield($c,$f);
				if (!fieldborderstype($pick[x],$pick[y],$f,13,$c))
				{
					$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 13;
					$s[13][stock] -= 1;
				}
			}
		}
		while ($s[35][stock] > 0)
		{
			$pick = pickrandomfield($c,$f);
			$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 35;
			$s[35][stock] -= 1;
		}
		while ($s[11][stock] > 0)
		{
			$pick = pickrandomfield($c,$f);
			$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 11;
			$s[11][stock] -= 1;
		}
	}
	elseif ($c == 10)
	{
		$overflow = 0;
		while ($s[25][stock] > 0)
		{
			$overflow++;
			if ($overflow > 100) 
			{
				$s[25][stock] -= 1;
				$s[201][stock] += 1;
			}
			else
			{
				$pick = pickrandomfield($c,$f);
				if (!fieldborderstype($pick[x],$pick[y],$f,25,$c))
				{
					$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 25;
					$s[25][stock] -= 1;
				}
			}
		}
		$overflow = 0;
		while ($s[4][stock] > 0)
		{
			$overflow++;
			if ($overflow > 100) 
			{
				$s[4][stock] -= 1;
				$s[201][stock] += 1;
			}
			else
			{
				$pick = pickrandomfield($c,$f);
				if (fieldborderstype($pick[x],$pick[y],$f,4,$c) || fieldborderstype($pick[x],$pick[y],$f,25,$c))
				{
					$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 4;
					$s[4][stock] -= 1;
				}
			}	
		}
		while ($s[31][stock] > 0)
		{
			$pick = pickrandomfield($c,$f);
			$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 31;
			$s[31][stock] -= 1;
		}
		while ($s[201][stock] > 0)
		{
			$pick = pickrandomfield($c,$f);
			$f[getfieldbyxyw($pick[x],$pick[y],$size[w])] = 201;
			$s[201][stock] -= 1;
		}
	}
	return $f;
}

function generategroundfromfields($c,$f)
{
	$size = getsizebyclass($c);
	$size[h] = $size[h] / 2;
	for ($y=0;$y<=$size[h];$y++) 
	{ 
		for ($x=0;$x<=$size[w];$x++) 
		{ 
			$t[1] = getfieldbyxyw($x,(2*$y - 1),$size[w]);
			$t[2] = getfieldbyxyw($x,(2*$y),$size[w]);
			$i = getfieldbyxyw($x,$y,$size[w]);
			if (($f[$t[1]] == 31) || ($f[$t[1]] == 32) || ($f[$t[1]] == 33) || ($f[$t[1]] == 34) || ($f[$t[1]] == 35) || ($f[$t[2]] == 31) || ($f[$t[2]] == 32) || ($f[$t[2]] == 33) || ($f[$t[2]] == 34) || ($f[$t[2]] == 35))
			{
				$g[$i] = 71;
			}
			else
			{
				$r = rand(1,2);
				$g[$i] = getgroundbyfield($f[$t[$r]]);
			}
		}
	}
	return $g;
}

function generate_colony($cid=0,$classId=0)
{
	if ($classId == 0 || $cid == 0) return 0;
	global $db;
	$size = getsizebyclass($classId);
	$f = generatefields($classId);
	
	if ($classId == 29) 
	{
		// Ringe für Klasse J
		for ($i=0;$i<$size[w];$i++)
		{
			$j++;
			$db->query("INSERT INTO stu_colonies_fielddata (colonies_id,field_id,type) VALUES ('".$cid."','".$j."','101')");
		}
		for ($i=$size[w];$i<($size[w]*2);$i++)
		{
			$j++;
			$db->query("INSERT INTO stu_colonies_fielddata (colonies_id,field_id,type) VALUES ('".$cid."','".$j."','119')");
		}
	}
	elseif ($classId == 9) 
	{
		// Klasse I
		for ($i=0;$i<$size[w];$i++)
		{
			$j++;
			$db->query("INSERT INTO stu_colonies_fielddata (colonies_id,field_id,type) VALUES ('".$cid."','".$j."','100')");
		}
		for ($i=$size[w];$i<($size[w]*2);$i++)
		{
			$j++;
			$db->query("INSERT INTO stu_colonies_fielddata (colonies_id,field_id,type) VALUES ('".$cid."','".$j."','118')");
		}
	}
	elseif (($classId == 8) || ($classId == 27))
	{
		// Klasse N
		for ($i=0;$i<$size[w];$i++)
		{
			$j++;
			$db->query("INSERT INTO stu_colonies_fielddata (colonies_id,field_id,type) VALUES ('".$cid."','".$j."','100')");
		}
		for ($i=$size[w];$i<($size[w]*2);$i++)
		{
			$j++;
			$db->query("INSERT INTO stu_colonies_fielddata (colonies_id,field_id,type) VALUES ('".$cid."','".$j."','117')");
		}
	}
	elseif (($classId == 7) || ($classId == 26))
	{
		// Klasse X
		for ($i=0;$i<$size[w];$i++)
		{
			$j++;
			$db->query("INSERT INTO stu_colonies_fielddata (colonies_id,field_id,type) VALUES ('".$cid."','".$j."','100')");
		}
		for ($i=$size[w];$i<($size[w]*2);$i++)
		{
			$j++;
			$db->query("INSERT INTO stu_colonies_fielddata (colonies_id,field_id,type) VALUES ('".$cid."','".$j."','114')");
		}
	}
	elseif (($classId == 6) || ($classId == 25))
	{
		// Klasse Ödland
		for ($i=0;$i<$size[w];$i++)
		{
			$j++;
			$db->query("INSERT INTO stu_colonies_fielddata (colonies_id,field_id,type) VALUES ('".$cid."','".$j."','100')");
		}
		for ($i=$size[w];$i<($size[w]*2);$i++)
		{
			$j++;
			$db->query("INSERT INTO stu_colonies_fielddata (colonies_id,field_id,type) VALUES ('".$cid."','".$j."','111')");
		}
	}
	elseif (($classId == 5) || ($classId == 24))
	{
		// Klasse P
		for ($i=0;$i<$size[w];$i++)
		{
			$j++;
			$db->query("INSERT INTO stu_colonies_fielddata (colonies_id,field_id,type) VALUES ('".$cid."','".$j."','100')");
		}
		for ($i=$size[w];$i<($size[w]*2);$i++)
		{
			$j++;
			$db->query("INSERT INTO stu_colonies_fielddata (colonies_id,field_id,type) VALUES ('".$cid."','".$j."','112')");
		}
	}
	elseif (($classId == 4) || ($classId == 23))
	{
		// Klasse Wüste
		for ($i=0;$i<$size[w];$i++)
		{
			$j++;
			$db->query("INSERT INTO stu_colonies_fielddata (colonies_id,field_id,type) VALUES ('".$cid."','".$j."','100')");
		}
		for ($i=$size[w];$i<($size[w]*2);$i++)
		{
			$j++;
			$db->query("INSERT INTO stu_colonies_fielddata (colonies_id,field_id,type) VALUES ('".$cid."','".$j."','113')");
		}
	}
	elseif ($classId == 10) 
	{
		// Klasse R
		for ($i=0;$i<$size[w];$i++)
		{
			$j++;
			$db->query("INSERT INTO stu_colonies_fielddata (colonies_id,field_id,type) VALUES ('".$cid."','".$j."','300')");
		}
		for ($i=$size[w];$i<($size[w]*2);$i++)
		{
			$j++;
			$db->query("INSERT INTO stu_colonies_fielddata (colonies_id,field_id,type) VALUES ('".$cid."','".$j."','115')");
		}
	}
	elseif ($classId == 28) 
	{
		// Klasse D
		for ($i=0;$i<$size[w];$i++)
		{
			$j++;
			$db->query("INSERT INTO stu_colonies_fielddata (colonies_id,field_id,type) VALUES ('".$cid."','".$j."','100')");
		}
		for ($i=$size[w];$i<($size[w]*2);$i++)
		{
			$j++;
			$db->query("INSERT INTO stu_colonies_fielddata (colonies_id,field_id,type) VALUES ('".$cid."','".$j."','116')");
		}
	}
	else
	{
		for ($i=0;$i<$size[w];$i++)
		{
			$j++;
			$db->query("INSERT INTO stu_colonies_fielddata (colonies_id,field_id,type) VALUES ('".$cid."','".$j."','100')");
		}
		for ($i=$size[w];$i<($size[w]*2);$i++)
		{
			$j++;
			$db->query("INSERT INTO stu_colonies_fielddata (colonies_id,field_id,type) VALUES ('".$cid."','".$j."','111')");
		}
	}
	if ($size[g] == 1) $g = generategroundfromfields($classId,$f);
	for ($i=0;$i<($size[w] * $size[h]);$i++)
	{
		$j++;
		$db->query("INSERT INTO stu_colonies_fielddata (colonies_id,field_id,type) VALUES ('".$cid."','".$j."','".$f[$i]."')");
	}

	if ($size[g] != 1) return 0;
	for ($i=0;$i<($size[w] * ($size[h]/2));$i++)
	{
		$j++;
		$db->query("INSERT INTO stu_colonies_fielddata (colonies_id,field_id,type) VALUES ('".$cid."','".$j."','".$g[$i]."')");
	}
}
?>