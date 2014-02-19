<?
  include('includes/dbconnect.php');
  include('includes/global.php');
	include('includes/functions.php');

  //get months in 2007
	
	$arr_months = array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov");
	
	
	//select distinct traders
	$qry = "select distinct(clnt_trader) from int_clnt_clients";
	$res = mysql_query($qry) or die(tdw_mysql_error($qry));
	while($row = mysql_fetch_array($res)) {
		$arr_trdr[] = $row["clnt_trader"];
	}
	
	//print_r($arr_trdr);

	foreach($arr_trdr as $k=>$v) {
	
		$arr_clnt = array();
		//get client list for trader
		$qry_1 = "select distinct(clnt_code) from int_clnt_clients where clnt_trader = '".$v."'";
		$res_1 = mysql_query($qry_1) or die(tdw_mysql_error($qry_1));
		while($row_1 = mysql_fetch_array($res_1)) {
			$arr_clnt[] = $row_1["clnt_code"];
		}
		//print_r($arr_clnt);
		$str_clnt = implode(",",$arr_clnt);
		$str_clnt = "('".str_replace(",","','",$str_clnt)."')";
		//echo $str_clnt;
		//echo "<br><br>";
		
		//for these clients get listed and OTC numbers
		$order_val = 0;
		foreach ($arr_months as $x=>$m) {
				$arr_date = get_commission_month_dates($m, '2007');
				
				//now get listed trades for the dates
				$qry_2 = "select sum(trad_quantity) as ql, sum(trad_commission) as cl from mry_comm_rr_trades where
									trad_trade_date between '".$arr_date[0]."' and '".$arr_date[1]."' and trad_advisor_code in ".$str_clnt.
									" and LENGTH(trad_symbol) < 4";
				$res_2 = mysql_query($qry_2) or die(tdw_mysql_error($qry_2));
				while($row_2 = mysql_fetch_array($res_2)) {
				  $str_ql = $row_2["ql"];
					$str_cl = $row_2["cl"];	
				}
				
				$qry_3 = "select sum(trad_quantity) as qo, sum(trad_commission) as co from mry_comm_rr_trades where
									trad_trade_date between '".$arr_date[0]."' and '".$arr_date[1]."' and trad_advisor_code in ".$str_clnt.
									" and LENGTH(trad_symbol) > 3";
				$res_3 = mysql_query($qry_3) or die(tdw_mysql_error($qry_3));
				while($row_3 = mysql_fetch_array($res_3)) {
				  $str_qo = $row_3["qo"];
					$str_co = $row_3["co"];	
				}
				
				$str_out = "<br>".$order_val.",".$v.",".$m.",".$str_ql.",".$str_cl.",".$str_qo.",".$str_co.chr(10);
				echo $str_out;
				
		$order_val = $order_val + 1;
		}
		
	}	
	

















  exit;

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
