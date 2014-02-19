<?
  include('includes/dbconnect.php');
  include('includes/global.php');
	include('includes/functions.php');

  //analyst coverage list showing
	
	$user_id = 213;
	
	//get email for user
	$user_email = db_single_val("SELECT Email as single_val FROM users WHERE ID = '".$user_id."'"); 
	
	xdebug("user_email",$user_email);  

	//get all stocks covered by the analyst
	$qry_symbols = "select acv_symbol from acv_analyst_coverage where acv_email = '".$user_email."'";
	$result_symbols = mysql_query($qry_symbols) or die(tdw_mysql_error($qry_symbols));
	$arr_symbols = array();
	while($row_symbols = mysql_fetch_array($result_symbols)) {
		$arr_symbols[] = $row_symbols["acv_symbol"];
	}
	
	print_r($arr_symbols);
	
	$arr_sectors = array();
	foreach($arr_symbols as $k=> $symbol_val) {
	//now get distinct sector from the list for this user
	$sector_val = db_single_val("select industry as single_val from sec_master where symbol = '".$symbol_val."'");
	$arr_sectors[$sector_val] = $symbol_val;		
	}
	
	//print_r($arr_sectors);
	
	$arr_final_symbol_list = array();
	foreach($arr_sectors as $sector=>$val) {
		if($sector != ''){
		//xdebug("sector",$sector);  
			$qry_symbols_final = "select symbol, description from sec_master where industry = '".$sector."'";
			$result_symbols_final = mysql_query($qry_symbols_final) or die(tdw_mysql_error($qry_symbols_final));
			while($row_symbols_final = mysql_fetch_array($result_symbols_final)) {
				$arr_final_symbol_list[$row_symbols_final["symbol"]] = $row_symbols_final["description"];
			}
		}
	}	
	print_r($arr_final_symbol_list);

?>
