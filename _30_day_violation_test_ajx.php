<?
  include('includes/dbconnect.php');
  include('includes/global.php');
  include('includes/functions.php');

  //show_array($_GET);
	
/*	$user_id = 295; //235; //295;
  $mod_request = 'hperiod';
  $symbol = 'spf'; //'SPF'; //'NFLX'; //'LGND'; //'NFLX';
  $quantity = 20;
	$buysell = 'B';*/ 

	$str_buysell = strtoupper(trim($buysell));
	

if ($mod_request == 'hperiod') { //get holding period violations

	 if ($str_buysell == 'S' || $str_buysell == 'B' ) { //Process the following only if there is a Buy or a Sell
	 
					 $hval = db_single_val("select var_value as single_val from var_lookup_values where var_type = 'hval'");
					 
					 //xdebug("hval",$hval);
					 //xdebug("symbol",$symbol);
					 
					 $nval = db_single_val("select Fullname as single_val from users where ID = '".$user_id."'");
					 //xdebug("nval",$nval);
					 
					 //xdebug("quantity",$quantity);
				
					 //Get the date cutoff
						$str_cutoff  = mktime(0, 0, 0, date('m')  , date('d')-($hval+1), date('Y'));
						$date_cutoff = date('Y-m-d',$str_cutoff); //business_day_backward(strtotime(date('Y-m-d')),($hval+1));
					 //NOT USING BUSINESS DAYS, INSTEAD USING CALENDAR DAYS ABOVE
					 //xdebug("date_cutoff",$date_cutoff);
		
	 					 //getting all Fidelity Accounts and Buys
					 $arr_acct = array();
					 $qry = "SELECT emp_acct_number 
									 FROM emp_employee_accounts_master 
									 WHERE emp_user_id = '".$user_id."'";
					 $result = mysql_query($qry) or die(tdw_mysql_error($qry));
					 while ($row = mysql_fetch_array($result)) {
							$arr_acct[] = $row["emp_acct_number"];		
					 }
					 $str_acct = " ('". implode("','",$arr_acct) ."') "; 
					 //xdebug("str_acct",$str_acct);
				
		}
			
	 //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& 
	 //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& 
	 //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& 
	 if ($str_buysell == 'S') {


					 //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					 //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					 
					 //Get all buys in the symbol in the past 30 days.
					 $qry = "select round(sum(quantity),0) as single_val from fidelity_emp_trades 
																			where symbol = '".strtoupper($symbol)."' 
																			and acct_num in " . $str_acct . " 
																			and buy_sell = 'B'
																			and trade_date > '".$date_cutoff."'";
					 
					 //xdebug("qry",$qry);
				
					 $qty_buy_fidelity = db_single_val($qry);
					 //xdebug("qty_buy_fidelity",$qty_buy_fidelity);
					 //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					 //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					 //Get Buys in external accounts.
					 //otd_emp_trades_external 
					 //auto_id  otd_account_id  otd_trade_date  otd_buysell  otd_symbol  otd_quantity  otd_price  otd_entered_by  otd_last_edited_by  otd_last_edited_on  otd_isactive  
					 //oac_emp_accounts 
					 // auto_id  oac_emp_userid  oac_custodian  oac_account_number  oac_acct_close_date  oac_entered_by  oac_last_edited_by  oac_last_edited_on  oac_comment  oac_isactive 	
				
					 $qry = "select sum(a.otd_quantity) as single_val from otd_emp_trades_external a
										left join oac_emp_accounts b on a.otd_account_id = b.auto_id
										where a.otd_symbol = '".strtoupper($symbol)."' 
										and a.otd_buysell = 'B'
										and a.otd_trade_date > '".$date_cutoff."'
										and b.oac_emp_userid = '".$user_id."'";
				
					 //xdebug("qry",$qry);
				
					 $qty_buy_others = db_single_val($qry);
					 //xdebug("qty_buy_others",$qty_buy_others);
					 //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					 //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
				
					 $total_buys = $qty_buy_fidelity + $qty_buy_others;
					 //xdebug("total_buys",$total_buys);
				
					 $str_to_pass = "";
					 if ($total_buys != 0 && $quantity != 0) {
							$str_to_pass = "<br><font color='red'>Potential ".$hval." days Holding Period violation.</font>"; //"1"; //
					 } else {
							$str_to_pass = "0"; //"All OK";
					 }
					 //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					 //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
				
					 echo $str_to_pass;

	 } else if ($str_buysell == 'B') {

					 //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					 //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					 
					 //Get all sells in the symbol in the past 30 days.
					 $qry = "select round(sum(quantity),0) as single_val from fidelity_emp_trades 
																			where symbol = '".strtoupper($symbol)."' 
																			and acct_num in " . $str_acct . " 
																			and buy_sell = 'S'
																			and trade_date > '".$date_cutoff."'";
					 
					 //xdebug("qry",$qry);
				
					 $qty_sell_fidelity = db_single_val($qry);
					 //xdebug("qty_buy_fidelity",$qty_buy_fidelity);
					 //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					 //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					 //Get Buys in external accounts.
					 //otd_emp_trades_external 
					 //auto_id  otd_account_id  otd_trade_date  otd_buysell  otd_symbol  otd_quantity  otd_price  otd_entered_by  otd_last_edited_by  otd_last_edited_on  otd_isactive  
					 //oac_emp_accounts 
					 // auto_id  oac_emp_userid  oac_custodian  oac_account_number  oac_acct_close_date  oac_entered_by  oac_last_edited_by  oac_last_edited_on  oac_comment  oac_isactive 	
				
					 $qry = "select sum(a.otd_quantity) as single_val from otd_emp_trades_external a
										left join oac_emp_accounts b on a.otd_account_id = b.auto_id
										where a.otd_symbol = '".strtoupper($symbol)."' 
										and a.otd_buysell = 'S'
										and a.otd_trade_date > '".$date_cutoff."'
										and b.oac_emp_userid = '".$user_id."'";
				
					 //xdebug("qry",$qry);
				
					 $qty_sell_others = db_single_val($qry);
					 //xdebug("qty_buy_others",$qty_buy_others);
					 //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					 //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
				
					 $total_sells = $qty_sell_fidelity + $qty_sell_others;
					 //xdebug("total_buys",$total_buys);
				
					 $str_to_pass = "";
					 if ($total_sells != 0 && $quantity != 0) {
							$str_to_pass = "<br><font color='red'>Potential ".$hval." days Holding Period violation.</font>"; //"1"; //
					 } else {
							$str_to_pass = "0"; //"All OK";
					 }
					 //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					 //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
				
					 echo $str_to_pass;

	 } else {
					 $str_to_pass = "0";
					 echo $str_to_pass;
	 }	
	 
	 //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& 
	 //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& 
	 //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& 
	 		 
}

?>