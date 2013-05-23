<?php

function searchFor($searchString, $inString, $nStart)		// returns the index of the character after the search string
{	$searchState = 0;
	$contentLength = strlen($inString);
	$searchStringLen = strlen($searchString);
	for($n=$nStart; $n<$contentLength; $n++)		// search for "Alexatrafficrankbased"
	{	if($searchState == $searchStringLen)
		{	return $n;
		}
		else if($inString[$n]==" " || $inString[$n]=="\t" || $inString[$n]=="\n")
		{	// do nothing
		}
		else if($inString[$n] == $searchString[$searchState])
		{	$searchState++;
		}
		else 
		{	$searchState = 0;
		}
	}
	
	return -1;
}


function storeSearchFor($searchString, $inString, $nStart, $storeString)		// same as searchFor above, but stores the string in between
{	$storeString = "";		// clear storeString
	$searchState = 0;
	$contentLength = strlen($inString);
	$searchStringLen = strlen($searchString);
	for($n=$nStart; $n<$contentLength; $n++)		// search for "Alexatrafficrankbased"
	{	//echo "n: $n   content: {$inString[$n]}\n";
		
		if($n-$nStart > $searchStringLen)
		{	$storeString = $storeString . $inString[$n-$searchStringLen-1];
		}
		
		if($searchState == $searchStringLen)
		{	return $n;
		}
		else if($inString[$n]==" " || $inString[$n]=="\t" || $inString[$n]=="\n")
		{	// do nothing
		}
		else if($inString[$n] == $searchString[$searchState])
		{	$searchState++;
		}
		else 
		{	$searchState = 0;	
		}
	}
	
	//if search term was not found
	return -1;
	
}

?>
