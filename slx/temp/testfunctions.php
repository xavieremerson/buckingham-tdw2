<?

echo round("123.34",0);

echo strlen(round("123.34",0));

function format_no_decimal_comma($inputval){

$inputval = round($inputval,0);
$lenval = strlen($inputval);

	if ($lenval > 3){
	
		if     ($lenval == 4) { return substr($inputval,0,1).",".substr($inputval,1,3); }
		elseif ($lenval == 5) { return substr($inputval,0,2).",".substr($inputval,2,3); }
		elseif ($lenval == 6) { return substr($inputval,0,3).",".substr($inputval,3,3); }
		elseif ($lenval == 7) { return substr($inputval,0,1).",".substr($inputval,1,3).",".substr($inputval,4,3); }
		elseif ($lenval == 8) { return substr($inputval,0,2).",".substr($inputval,2,3).",".substr($inputval,5,3); }
		elseif ($lenval == 9) { return substr($inputval,0,3).",".substr($inputval,3,3).",".substr($inputval,6,3); }
		else { return $inputval;}		
	
	} else {
	return $inputval; 
	}

}

echo "<BR><BR>";

echo format_no_decimal_comma("123746682.03");


?>

