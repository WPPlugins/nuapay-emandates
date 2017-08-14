<?php 

//checks to see if array variable has a null values
function check($array)
{
	foreach($array as $key=>$value)
	{
		if(array_key_exists($key, $array)) {
			if (is_null($array[$key])) {
				return true;
			}
		}		
	}
	return false;
}