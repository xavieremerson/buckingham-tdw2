<?
  include('includes/dbconnect.php');
  include('includes/global.php'); 
	include('includes/functions.php');

$output_filename = "emp_trades.xls";
$fp = fopen($exportlocation.$output_filename, "w");

$arr_vals = explode("^",$xl);
//show_array($arr_vals);
//exit;
/*
  0 = [d045c59a90]
  1 = [2012-12-13]
  2 = [2012-12-13]
  3 = [236]
  4 = [JCP]

datefrom = [12/13/2012]
dateto = [12/13/2012]
sel_emp = [236]
sel_symbol = [JCP]
*/

$datefrom = $arr_vals[1];
$dateto = $arr_vals[2];
$sel_emp = $arr_vals[3];
$sel_symbol = $arr_vals[4];

function get_empname_fid_acct($acct_num) {
	$emp_id = db_single_val("select emp_user_id as single_val from emp_employee_accounts_master where emp_acct_number ='".$acct_num."'");
	$emp_name = db_single_val("select Fullname as single_val from users where ID ='".$emp_id."'");
	return $emp_name;
}

$str = '<html xmlns="http://www.w3.org/1999/xhtml">
				<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>
				<body>';

$str .= '<table border="1" cellspacing="0" cellpadding="0">
					<tr>
							<td width="85">Trd Date</td>
							<td width="160">Employee</td>
							<td width="165">Acct. Name</td>
							<td width="100">Account</td> 
							<td width="160">Custodian</td> 
							<td width="60">Symbol</td>
							<td width="100">Cusip</td> 
							<td width="180">Sec. Description</td>
							<td width="40">Buy/Sell</td>
							<td align="right">Quantity</td>
							<td align="right" width="100">Price</td>
							<td width="80" align="right">Ord Entry</td>
							<td width="75" align="right">Ord Exec</td>
							<td></td>
					</tr>';

					  if ($datefrom) {
							$qry_1_date = " and trade_date between '".$datefrom."' and '".$dateto."' ";
							$qry_2_date = " and a.otd_trade_date between '".$datefrom."' and '".$dateto."' ";
						} else {
							$qry_1_date = " and trade_date between '".previous_business_day()."' and '".previous_business_day()."' ";
							$qry_2_date = " and a.otd_trade_date between '".previous_business_day()."' and '".previous_business_day()."' ";
						}
						
						//xdebug("qry_1_date",$qry_1_date);
						//xdebug("qry_2_date",$qry_2_date);
						
						if ($sel_symbol && $sel_symbol != 'SYMBOL' && trim($sel_symbol) != '') {
							$qry_1_sym = " and symbol = '".trim(strtoupper($sel_symbol))."' ";
							$qry_2_sym = " and a.otd_symbol = '".trim(strtoupper($sel_symbol))."' ";
						} else {
							$qry_1_sym = " ";
							$qry_2_sym = " ";
						}

						//xdebug("qry_1_sym",$qry_1_sym);
						//xdebug("qry_2_sym",$qry_2_sym);

						if ($sel_emp && trim($sel_emp) !="") {
							//xdebug("sel_emp",$sel_emp);
							//getting all Fidelity Accounts
							$arr_acct = array();
							$qry = "SELECT emp_acct_number 
										 FROM emp_employee_accounts_master 
										 WHERE emp_user_id = '".$sel_emp."'";
							$result = mysql_query($qry) or die(tdw_mysql_error($qry));
							while ($row = mysql_fetch_array($result)) {
							$arr_acct[] = $row["emp_acct_number"];		
							}
							$str_acct = " ('". implode("', '",$arr_acct) ."') "; 
							//echo '<a class="ilt">Account Numbers (Fidelity): '.str_replace("'","",$str_acct).'</a><br>';
							
							$qry_1_acct = " and acct_num in ". $str_acct. " ";
							
							//getting all External Accounts
							//auto_id oac_emp_userid oac_custodian oac_account_number
							$arr_acct_ext = array();
							$arr_acct_num = array();
							$arr_acct_id = array();
							$qry = "SELECT a.oac_emp_userid, a.auto_id, a.oac_custodian, a.oac_account_number 
									 FROM oac_emp_accounts a
									 LEFT JOIN users b on a.oac_emp_userid = b.ID
									 WHERE a.oac_emp_userid = '".$sel_emp."'";
							$result = mysql_query($qry) or die(tdw_mysql_error($qry));
							while ($row = mysql_fetch_array($result)) {
							$arr_acct_ext[] = $row["oac_custodian"].": ". $row["oac_account_number"];	
							$arr_acct_num[] = $row["oac_account_number"];	
							$arr_acct_id[] = $row["auto_id"];
							}
							$str_acct_ext = " ('". implode("', '",$arr_acct_id) ."') "; 
							//echo '<a class="ilt">Account Numbers (Others): '.str_replace("'","",$str_acct_ext).'</a>'; ////xdebug("str_acct",$str_acct);

							$qry_2_acct = " and a.otd_account_id in ". $str_acct_ext. " ";

						} else {
							//echo "No employee selected";
							$qry_1_acct = " ";
							$qry_2_acct = " ";
						}
						
						//xdebug("qry_1_acct",$qry_1_acct);
						//xdebug("qry_2_acct",$qry_2_acct);

						//**********************************************************************************

						$qry_1_main = "SELECT 
												auto_id,
												acct_num,
												trade_date,
												buy_sell,
												symbol,
												cusip,
												date_format(order_entry_time, '%l:%i%p') as order_entry_time,
												date_format(order_exec_time, '%l:%i%p') as order_exec_time,
												concat(sec_desc_1, ' ', sec_desc_2) as sec_desc,  
												round(sum(quantity),0) as quantity, 
												round(avg(price),2) as price,
												is_active, 
												substring(concat(trim(first_name),' ',trim(middle_name),' ',trim(last_name)),1,20) as Fullname
											FROM fidelity_emp_trades
											WHERE is_active  = 1 ".$qry_1_date.$qry_1_acct.$qry_1_sym."
											GROUP BY acct_num, symbol, trade_date, buy_sell";
						//echo $qry_1_main;
						/*
						trad_auto_id  trad_reference_number  trad_rr  trad_trade_date  trad_run_date  
						trad_settle_date  trad_advisor_code  trad_advisor_name  trad_account_name  
						trad_account_number  trad_symbol  trad_buy_sell  trad_quantity  trade_price  
						trad_commission  trad_cents_per_share  trad_is_cancelled  						
						*/
            ////xdebug("str_sql_select",$str_sql_select);
						$result_select = mysql_query($qry_1_main) or die(tdw_mysql_error($qry_1_main));

						$count_row_select = 0;
						while ( $row_select = mysql_fetch_array($result_select) ) 
						{

		$str .= '<tr> 
 							<td>'.format_date_ymd_to_mdy($row_select["trade_date"])."</td>".
							'<td>'.substr(get_empname_fid_acct($row_select["acct_num"]),0,18).'</td> 
							<td>'.substr($row_select["Fullname"],0,18).'</td>
							<td>'.$row_select["acct_num"].'</td>
							<td>Fidelity</td>
							<td>'.$row_select["symbol"].'</td>
							<td>'.$row_select["cusip"].'</td>
							<td>'.substr($row_select["sec_desc"],0,24).'</td>
							<td>'.offset_buy_sell($row_select["buy_sell"]).'</td>
							<td align="right">'.number_format($row_select["quantity"],0,'',',').'</td>
							<td align="right">'.number_format($row_select["price"],2,'.',',').'</td>
							<td align="right">'.$row_select["order_entry_time"].'</td>
							<td align="right">'.$row_select["order_exec_time"].'</td>
							<td></td>
						</tr>';
						$count_row_select = $count_row_select + 1;
						}
						
						$qry_2_main = "SELECT 
													a.otd_account_id, a.otd_trade_date, a.otd_buysell, a.otd_symbol, 
													a.otd_quantity, a.otd_price, a.otd_isactive, 
													b.oac_custodian, b.oac_account_number, c.Fullname
													from otd_emp_trades_external a
													left join oac_emp_accounts b on a.otd_account_id = b.auto_id
													left join users c on b.oac_emp_userid = c.ID
													WHERE a.otd_isactive = 1 ".$qry_2_date.$qry_2_acct.$qry_2_sym." ";
						//echo $qry_2_main;
						//otd_trade_date  otd_buysell  otd_symbol  otd_quantity  otd_price  otd_isactive  oac_custodian  oac_account_number  Fullname 
						//exit;
						$result_select = mysql_query($qry_2_main) or die(tdw_mysql_error($qry_2_main));
						while ( $row_select = mysql_fetch_array($result_select) ) 
						{

			$str .= '<tr> 
 							<td>'.format_date_ymd_to_mdy($row_select["otd_trade_date"]).'</td>
							<td>'.substr($row_select["Fullname"],0,18).'</td>
							<td>'.substr($row_select["Fullname"],0,18).'</td>
							<td>'.$row_select["oac_account_number"].'</td>
							<td>'.substr($row_select["oac_custodian"],0,24).'</td>
							<td>'.$row_select["otd_symbol"].'</td>
							<td></td>
							<td></td>
							<td>'.offset_buy_sell($row_select["otd_buysell"]).'</td>
							<td align="right">'.number_format($row_select["otd_quantity"],0,'',',').'</td>
							<td align="right">'.number_format($row_select["otd_price"],2,'.',',').'</td>
							<td align="right"></td>
							<td align="right"></td>
							<td></td>
						</tr>';

						$count_row_select = $count_row_select + 1;
						}
						
$str .= '</table></body></html>';
fputs ($fp, $str);

fclose($fp);


//echo "Location: data/exports/"."EmployeeAccounts_".Date("m-d-Y").".csv";
//This works!

//Header("Location: data/exports/".$output_filename);

Header("Location: http://192.168.20.63/tdw/fileserve_xls.php?l=data/exports/&f=".$output_filename);
?>