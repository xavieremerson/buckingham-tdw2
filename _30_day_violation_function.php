<?
  include('includes/dbconnect.php');
  include('includes/global.php');
  include('includes/functions.php');

////
// Detecting Holding Period Violation
// Date Arg is mm/dd/YYYY
function potential_holding_period($user_idx, $dateval, $symbol, $quantity, $str_buysell, $return_type = "S") {		//S = string, N = number

	 $hval = db_single_val("select var_value as single_val from var_lookup_values where var_type = 'hval'");
	 $str_date = format_date_mdy_to_ymd($dateval);
	 //NOT USING BUSINESS DAYS, INSTEAD USING CALENDAR DAYS
	 $str_cutoff  = mktime(0, 0, 0, date('m', strtotime($str_date)), date('d', strtotime($str_date))-($hval+1), date('Y', strtotime($str_date)));
	 $date_cutoff = date('Y-m-d',$str_cutoff);
	 //getting all Fidelity Accounts and Buys
	 $arr_acct = array();
	 $qry = "SELECT emp_acct_number 
					 FROM emp_employee_accounts_master 
					 WHERE emp_user_id = '".$user_idx."'";
	 $result = mysql_query($qry) or die(tdw_mysql_error($qry));
	 while ($row = mysql_fetch_array($result)) {
			$arr_acct[] = $row["emp_acct_number"];		
	 }
	 $str_acct = " ('". implode("', '",$arr_acct) ."') "; 
	
	 $arr_acct_ext = array();
	 $qry = "SELECT a.oac_emp_userid, a.oac_custodian, a.oac_account_number 
					 FROM oac_emp_accounts a
					 LEFT JOIN users b on a.oac_emp_userid = b.ID
					 WHERE oac_emp_userid = '".$user_idx."'";
	 $result = mysql_query($qry) or die(tdw_mysql_error($qry));
	 while ($row = mysql_fetch_array($result)) {
			$arr_acct_ext[] = $row["oac_custodian"].": ". $row["oac_account_number"];		
	 }
	 $str_acct_ext = " ('". implode("', '",$arr_acct_ext) ."') "; 
				
	 //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& 
	 if ($str_buysell == 'S') {


		 //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++					 
		 //Get all buys in the symbol in the past 30 days.
		 $qry = "select round(sum(quantity),0) as single_val from fidelity_emp_trades 
																where symbol = '".strtoupper($symbol)."' 
																and acct_num in " . $str_acct . " 
																and buy_sell = 'B'
																and trade_date > '".$date_cutoff."'";
		 $qty_buy_fidelity = db_single_val($qry);
		 //xdebug("qty_buy_fidelity",$qty_buy_fidelity);
						
		 //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		 //Get Buys in external accounts.
		 $qry = "select sum(a.otd_quantity) as single_val from otd_emp_trades_external a
							left join oac_emp_accounts b on a.otd_account_id = b.auto_id
							where a.otd_symbol = '".strtoupper($symbol)."' 
							and a.otd_buysell = 'B'
							and a.otd_trade_date > '".$date_cutoff."'
							and b.oac_emp_userid = '".$user_idx."'";
		 $qty_buy_others = db_single_val($qry);
		 
		 //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	
		 $total_buys = $qty_buy_fidelity + $qty_buy_others;

		 $str_return = "";
		 
		 if ($return_type == "S") {
				 if ($total_buys != 0 && $quantity != 0) {
						$str_return = "Potential ".$hval." days Holding Period violation.";
				 } else {
						$str_return = "---";
				 }
	   } else if ($return_type == "N") {
				 if ($total_buys != 0 && $quantity != 0) {
						$str_return = "1";
				 } else {
						$str_return = "0";
				 }
		 } else {
		 		 $str_return = "?";
		 }
		 //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

		 return $str_return;
	 }
	 //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& 
}

echo potential_holding_period('209', '09/26/2012', 'COH', '13000', 'S', "S");
?>