<?php

function readCormat($sentence)
{
	if(empty($sentence) or !$sentence)
	{
		return;
	}
	$list_words = array();
	$numOfspaces = 0;
	$n = -1;
	$m = -1;
	for($i = 0; $i < strlen($sentence); $i++)
	{
		if($n == -1)
		{
			$n = $i;
		}
		elseif($sentence[$i] == "%" && $m == -1)
		{
			$m = $i + 1;
			array_push($list_words, substr($sentence, $n, $m - $n - 1));
		}
		elseif($sentence[$i] == "%")
		{
			$n = $m;
			$m = $i + 1;
			array_push($list_words, substr($sentence, $n, $m - $n - 1));
		}
		elseif($i == strlen($sentence) - 1)
		{
			$n = $m;
			$m = $i;
			array_push($list_words, substr($sentence, $n, $m - $n + 1));
		}
	}
	$cormatArray = array();
	for($j = 0; $j < count($list_words); $j++)
	{
		$n = -1;
		$m = -1;
		$cormatSingleElement = array();
		$sentence = $list_words[$j];
		for($i = 0; $i < strlen($sentence); $i++)
		{
			if($n == -1)
			{
				$n = $i;
			}
			elseif($sentence[$i] == "|" && $m == -1)
			{
				$m = $i + 1;
				array_push($cormatSingleElement, substr($sentence, $n, $m - $n - 1));
			}
			elseif($sentence[$i] == "|")
			{
				$n = $m;
				$m = $i + 1;
				array_push($cormatSingleElement, substr($sentence, $n, $m - $n - 1));
			}
			elseif($i == strlen($sentence) - 1)
			{
				$n = $m;
				$m = $i;
				array_push($cormatSingleElement, substr($sentence, $n, $m - $n + 1));
			}
		}
		array_push($cormatArray, $cormatSingleElement);
	}
	return $cormatArray;
}

function returnLog($date, $log_array)
{
	if(!$log_array)
	{
		return;
	}
	$logs = array();
	$pattern = "/{$date}/";
	foreach($log_array as $logg)
	{
		if(preg_match($pattern, $logg['0']))
		{
			array_push($logs, $logg);
		}
	}
	return $logs;
}

function readLog()
{
	if(file_exists("./".strval(date("Y-m-d"))) and filesize(("./".strval(date("Y-m-d")))) > 0)
	{
	$myfile = fopen("./".strval(date("Y-m-d")), "r");
	return fread($myfile, filesize(("./".strval(date("Y-m-d")))));
	}
	fopen("./".strval(date("Y-m-d")), "w");	
}

function writeLog($log_text)
{
	$myfile = fopen("./".strval(date("Y-m-d")), "a");
	fwrite($myfile, $log_text);
}

function getLogs($date)
{
	$val = readLog();
	print_r(returnLog($date, readCormat($val)));
}

?>