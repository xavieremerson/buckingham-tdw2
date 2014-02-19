<?
function hist_prices($symbol, $startdate, $enddate) {
	
	//Process start and end dates
	$arr_startdate = explode("-", $startdate);
	$arr_enddate = explode("-", $enddate);
	
	$arr_mon = array("01"=>"00","02"=>"01","03"=>"02","04"=>"03","05"=>"04","06"=>"05","07"=>"06","08"=>"07","09"=>"08","10"=>"09","11"=>"10","12"=>"11",);
	
  //Date	Open	High	Low	Close	Volume	Adj Close
  // http://ichart.finance.yahoo.com/table.csv?s=AEO&a=03&b=14&c=2009&d=05&e=12&f=2009&g=d&ignore=.csv

	$row = 1;
	$str_url = "http://ichart.finance.yahoo.com/table.csv?s=".$symbol.
	           "&a=".$arr_mon[$arr_startdate[1]]."&b=".$arr_startdate[2]."&c=".$arr_startdate[0].
						 "&d=".$arr_mon[$arr_enddate[1]]."&e=".$arr_enddate[2]."&f=".$arr_enddate[0].
						 "&g=d&ignore=.csv";
	//echo $str_url;
	$handle = fopen($str_url, "r");
	$arr_price = array();
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			if ($row > 1) {
				$num = count($data);
				//echo "<p> $num fields in line $row: <br /></p>\n";
				/*
				for ($c=0; $c < $num; $c++) {
						echo $data[$c] . "<br />\n";
				}
				*/
				//$arr_price[substr($data[0],5,5)] = $data[4];
				$arr_price[$data[0]] = $data[4];
			}
			$row++;
	}
	fclose($handle);
	return $arr_price;
}
?>