<?
  include('includes/dbconnect.php');
  include('includes/global.php');
  include('includes/functions.php');

	function get_company_info($symbol) {
		$quotes = new Quotes(); 
		$symbols = explode(",",$symbol) ; 
		for ($n=0; $n<count($symbols); $n++)
			{
					$quotes->mSetSymbol(strtoupper($symbols[$n])) ;  
					$quotes->mLoadYahoo() ;
					return $quotes->_strCompany."^".$quotes->_strLastPrice."^".$quotes->_strVolume."^".$quotes->_strMarketCap;
			}
	}

	
if ($mod_request == 'cname') { //show approvers online

		$zsymbol = strtoupper(trim($symbol));
		//echo $symbol;

		
		
		$arr_company_detail = explode("^",get_company_info($zsymbol));  
		//print_r($arr_company_detail );
		//exit;
		if (
				(trim($arr_company_detail[0]) == strtoupper($zsymbol) || trim($arr_company_detail[0]) == "") //trim($arr_company_detail[0]) == '' or 
			 ) {
			echo "Probably an invalid Symbol!";
		} else {

					//BCM Activity
					$qry_activity = "select oth_broker, oth_trade_date, oth_buysell, oth_symbol, oth_quantity, oth_price 
													 from oth_other_trades where oth_symbol = '".$zsymbol."' 
													 order by oth_trade_date desc limit 1";
					$result_activity = mysql_query($qry_activity) or die(tdw_mysql_error($qry_activity));
					$countval = mysql_num_rows($result_activity);
					if ($countval == 1) {
						while($row = mysql_fetch_array($result_activity))
						{
							$str_out = "Most recent BCM Activity : ". $row["oth_buysell"] . " " . $row["oth_quantity"]  . " " . $zsymbol . " on " . format_date_ymd_to_mdy($row["oth_trade_date"]) .".";
						}
					} else {
							$str_out = "No BCM activity found";
					}


		
			echo "<strong>".$arr_company_detail[0]."</strong> Price: <strong>".$arr_company_detail[1] . "</strong> Vol: <strong>".number_format($arr_company_detail[2],0,"",",")."</strong><br>".$str_out;
		}
}
?>