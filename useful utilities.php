<?php
	//include_once($_SERVER['PHP_ROOT'] . "/useful utilities.php");
	
	function locateIp($ip)
	{
	    $d = file_get_contents("http://www.ipinfodb.com/ip_query.php?ip=$ip&output=xml");
	    if (!$d) return false; // Failed to open connection
	    
		$answer = new SimpleXMLElement($d);
	    if ($answer->Status != 'OK') return false; // Invalid status code
	    
		$country_code = $answer->CountryCode;
		$country_name = $answer->CountryName;
        $region_name = $answer->RegionName;
        $city = $answer->City;
        $zippostalcode = $answer->ZipPostalCode;
        $latitude = $answer->Latitude;
        $longitude = $answer->Longitude;
        
		return array('latitude' => $latitude, 'longitude' => $longitude, 'zippostalcode' => $zippostalcode, 'city' => $city, 'region_name' => $region_name, 'country_name' => $country_name, 'country_code' => $country_code, 'ip' => $ip);
	}
	
	function plural($num, $plural=false, $singular=false, $zero=false) 
	{	$plur = $num != 1;
		
		if($num == 0 && $zero !== false)
		{	return $zero;
		}
		
		if($singular == false)
		{	if($plural === false)
			{	if($plur)	return "s";
			}
			else if($plural === "people")
			{	if($plur)	return $plural;
				else		return "person";
			}
			else if($plural === "people are")
			{	if($plur)	return $plural;
				else		return "person is";
			}
		}
		else
		{	if($plur)	return $plural;
			else		return $singular;
		}	
	}
	
	function nPeople($num)
	{
		if ($num == 0) return 'no one';
		if ($num == 1) return '1 person';
		return "$num people";
	}
	
	// dayGranularity can be set true to forgo minutes an hours and just write "Today"
	// $time should be a unix timestamp (you can use strtotime() to get a unix timestamp from a formated string)
	// if $now is true, it will print "now" instead of "0 seconds", if false, it'll print "0 seconds ago"
	function showRelativeTime($time, $dayGranularity=false, $now=true)
	{	//*
		$diff = time() - $time;
		
		if($diff >= 0)
		{	$before = '';
			$after = ' ago';
		}else
		{	$before = 'In ';
			$after = '';
		}
		
		$scale = timeScale($diff);
		if($dayGranularity==true && in_array($scale, array('s','m','h')))	// if day granularity is turned on, scale by day at minimum
		{	$scale = 'd';
		}
		$number = timeDiff($diff, $scale);
		
		if($scale === 's')	$word = 'second';
		else if($scale === 'm')	$word = 'minute';
		else if($scale === 'h')	$word = 'hour';
		else if($scale === 'd')	$word = 'day';
		else if($scale === 'w')	$word = 'week';
		else if($scale === 'M')	$word = 'month';
		else if($scale === 'y')	$word = 'year';
		
		if($number >= 1)
		{	$value = intval($number);
		}else if($dayGranularity && $diff > 0)
		{	$value = 'Today';
		}else if($now)
		{	$value = 'Now';
		}else
		{	$value = '0';
		}
		
		echo $before.$value.' '.$word.plural(intval($number)).$after;
	}
	
	// returns a number of timeunits (positive or negative) representing the $timeDifference
	// $timeunit should be one of the following
	//		's' for seconds
	//		'm' for minutes (note the lower-case m)
	//		'h' for hours
	//		'd' for days
	//		'w' for weeks
	//		'M' for months (note the capital M)
	//		'y' for years
	// $timeDifference should be the difference between two timeStamps in epoch time format
	function timeDiff($timeDifference, $timeunit)
	{	if($timeunit === 's')
		{	return $timeDifference;
		}else if($timeunit === 'm')
		{	return $timeDifference/60;
		}else if($timeunit === 'h')
		{	return $timeDifference/3600;		// 60/60;
		}else if($timeunit === 'd')
		{	return $timeDifference/86400;		// 60/60/24
		}else if($timeunit === 'w')
		{	return $timeDifference/604800;		// 60/60/24/7
		}else if($timeunit === 'M')
		{	return $timeDifference/2592000;		// 60/60/24/30
		}else if($timeunit === 'y')
		{	return $timeDifference/31536000;	//	60/60/24/365
		}	
	}
	
	// returns the smallest timeunit that will give an integer granularity greater than or equal to 1
	// $timeDifference should be the difference between two timeStamps in epoch time format
	// returns one of the following
	//		's' for seconds
	//		'm' for minutes (note the lower-case m)
	//		'h' for hours
	//		'd' for days
	//		'w' for weeks
	//		'M' for months (note the capital M)
	//		'y' for years
	function timeScale($timeDifference)
	{	$diff = abs($timeDifference);
	
		$diff = intval($diff/60); if($diff < 1) return 's';
		$diff = intval($diff/60); if($diff < 1) return 'm';
		$diff = intval($diff/24); if($diff < 1) return 'h';
		$diff = intval($diff/7); if($diff < 1) return 'd';
		$diff = intval($diff*7/(30)); if($diff < 1) return 'w';
		$diff = intval($diff*30/(365)); if($diff < 1) return 'M';
		//else
		return 'y';	// years are the highest it goes
	}
	
	function showDate($timeStamp, $timeZoneOffset, $format = "M d")
	{	return date($format, $timeStamp + $timeZoneOffset);
	}	
	
	function strMult($c, $num)	//  copies a character or string on itself a number of times
	{	$result = "";
		for($n=0; $n<$num; $n++)
		{	$result .= $c;
		}
		return $result;
	}
	function getUniqueID()
	{	static $uniqueIDnum=0;	// number for creating unique non-conflicting ids (like unique_id_3 etc)
		$uniqueIDnum += 1;
		return "unique_id_".$uniqueIDnum;
	}
	
	function getExtension($fileName)
	{	
		$list = explode(".",$fileName);
		return $list[count($list)-1];
	}
	
	// merges associative arrays
	// keys of array2 will take precedence
	function assarray_merge($array1, $array2)
	{	foreach($array2 as $k => $v)
		{	$array1[$k] = $v;
		}
		return $array1;
	}
	
	function showBreaks($x)
	{	$order = array("\r\n", "\n", "\r");	// I don't remember why this is called order (maybe because the order of this array is important?)
		return str_replace($order, "<br>", $x); 
	}

	// Currently an extremely crude way of determining the timezone
	function setTimeZone($offsetInSeconds)
	{	foreach(DateTimeZone::listAbbreviations() as $abrv=> $list)
		{	foreach($list as $zone)
			{	$signedDifferenceInMinutes = ($zone["offset"] + $offsetInSeconds)/60;
				if(-30 <=$signedDifferenceInMinutes&&$signedDifferenceInMinutes<= 30)
				{	date_default_timezone_set($zone["timezone_id"]);
					return;
				}
			}
		}
	}
	
	// $type should either be "'" or '"' (either a single quote or a double quote)
	// you should not leave $type as null
		// $type = "html" escapes the string for use in html
		// $type = "'" or '"' escapes for that type of string (in javascript)
	function escape_string($str, $type=null)
	{	if($str !== null)
		{	if($type === "'" || $type === '"')
			{	$str = addcslashes($str, "\n\r\t\\".$type);//str_replace(array('\\', $type),array('\\\\', '\\'.$type), $str);
			}else if($type === "html")
			{	$str = htmlspecialchars($str, ENT_QUOTES);
			}else
			{	$str = str_replace(array('\\'),array('\\\\'), $str);	// this is basically addslashes
			}
		}
		return $str;
	}
	
	// tests if an array is associative or not
	function isAssoc($arr)
	{	return array_keys($arr) != range(0, count($arr) - 1);
	}
	
	// returns a string of javascript code
	function phpArrayToJSarray($array)
	{	$isAssociative = isAssoc($array);
		if($isAssociative)
		{	$result = "{";
		}else
		{	$result = "[";
		}
		
		$oneAlready = false;
		foreach($array as $k => $x)
		{	if($oneAlready)
			{	$result .= ",";
			}
			$oneAlready = true;
			
			if($isAssociative)
			{	$result .= '"'.$k.'"'.":";
			}
			
			if(is_array($x))
			{	$result .= phpArrayToJSarray($x);
			}else if(is_int($x))
			{	$result .= $x;
			}else
			{	$result .= '"'.escape_string($x, '"').'"';
			}
		}
		if($isAssociative)
		{	$result .= "}";
		}else
		{	$result .= "]";
		}
		return $result;
	}
	
	// returns modified text
	// NOTE: the text passed in should be 
	function replaceURLsWithLinks($text, $forEscapedHTML=false)
	{	$coreChars = '-|[a-zA-Z0-9]|&|;|/|%|=|~|_|!|:|,|\@|\#|\\|\+|\?|\||\.|\(|\)';	// why is the \+ not working to include the plus sign?
		
		$core = '(('.$coreChars.')+)'; 
		$protocolStart = '((?i)(http|https)://)'.$core;
		$wwwStart = '(?i)(www\.)'.$core;
		
		if($forEscapedHTML)
		{	$callback = "replaceURLsWithLinks_callback_forEscapedHTML";
		}else
		{	$callback = "replaceURLsWithLinks_callback";
		}
		
		return preg_replace_callback('@'.$protocolStart.'|'.$wwwStart.'@', $callback, $text);
	}
	function replaceURLsWithLinks_callback_forEscapedHTML($m)	// used for the preg_replace_callback in replaceURLsWithLinks
	{	return replaceURLsWithLinks_callback($m, true);
	}
	function replaceURLsWithLinks_callback($m, $forEscapedHTML=false)	// used for the preg_replace_callback in replaceURLsWithLinks
	{	$rawResult_withoutHttp = $m[3];
		if(isset($m[5]))
		{	$rawResult_withoutHttp.= $m[5].$m[6];
		}
		$rawRestult = $m[1].$rawResult_withoutHttp;
		
		if($m[1] != "")
		{	$result = $rawRestult;
			$result_withoutHttp = $rawResult_withoutHttp;
		}else
		{	$result = "http://".$rawRestult;
			$result_withoutHttp = $rawRestult;
		}
		
		return '<a href="'.htmlspecialchars_decode($result,ENT_QUOTES).'">'.$result_withoutHttp.'</a>';
	}



	// ensures that a path exists (creates it if not)
    // ignores a file at the end if there is one - only creates directories
    function createPath($path) {
        $parts = explode('/',$path);
        $dirs = array_slice($parts, 0, count($parts)-1);

        $curdir = '';
        foreach($dirs as $dir) {
            $curdir .= $dir.'/';

            if(!file_exists($curdir)) {
                mkdir($curdir);
            }
        }
    }
