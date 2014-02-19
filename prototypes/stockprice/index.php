<pre>
<?
function get_company_data ($symbol) {

  $quotes = new Quotes(); 
	
	$symbols = explode(",",$symbol) ; 
	
	for ($n=0; $n<count($symbols); $n++)
    {
        $quotes->mSetSymbol(strtoupper($symbols[$n])) ; 
        $quotes->mLoadYahoo() ;
				if (   $quotes->_strVolume < 10000
				    or $quotes->_strLastPrice > 3
				    or $quotes->_strLastPrice < 0.10						
						) {
					return 'NA';
				} else {
					return str_pad($quotes->_strLastPrice,10," ",STR_PAD_LEFT).
								 str_pad($quotes->_strChange,10," ",STR_PAD_LEFT).
								 str_pad($quotes->_strPercentChange,10," ",STR_PAD_LEFT).
								 str_pad($quotes->_strVolume,10," ",STR_PAD_LEFT).
								 str_pad($quotes->_strOpen,10," ",STR_PAD_LEFT).
								 str_pad($quotes->_strMarketCap,10," ",STR_PAD_LEFT);
				}
		}
}

include('functions.php');


if (file_exists ('otcbb.csv'))
{
	$row = 0;
	$handle = fopen('otcbb.csv', "r");
	//START WHILE 1
	while ($data = fgetcsv($handle, 2000, ",")) 
	{
		$num = count($data);
		//echo "<p> $num fields in line $row: --------<br>";
		//Replace the single quotes in the data with escaped quotes for entry into database
		
		for ($j=0; $j<$num; $j++) 
		{
			$data_piece = $data[$j];
			$data[$j] = str_replace ("'", "\'", $data_piece);
		}
		
		//proceed from row 2		
		if ($row > 0) {
		  $str_company_data = get_company_data ($data[0].".OB");
			if ($str_company_data != 'NA') {
				echo str_pad($data[0],10," ").$str_company_data."<br>";
			}
		}
		ob_flush();
		flush();

	$row = 	$row + 1;
	}
}			
?>
</pre>