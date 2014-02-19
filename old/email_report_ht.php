<?

////
// This report needs to be initiated as a cron job or as an include in another
// script so as to send emails to required recipients.
// 
include ('includes/functions.php');
include ('includes/dbconnect.php');

// Get the date for which to process report
	//$trade_date_to_process = previous_business_day();
	$trade_date_to_process = '2004-02-20';
	
echo "<BR> Processing Trade Date: [". $trade_date_to_process ."] <BR>";

// Verify if report creation condition exists

////Check for Hathaway Trades
//

//***********************************************************************
//Get Hathaway Accounts data in a local variable
$arr_accounts = array('60587023','60500509','60500507','60587015','60587014','60587020','60587016','60586505','60587017','60580154','60580156','60580237','60586502','60580512');
$accounts_string = "'60587023','60500509','60500507','60587015','60587014','60587020','60587016','60586505','60587017','60580154','60580156','60580237','60586502','60580512'";
//print_r($arr_accounts);

//Get Hathaway Accounts Names on account
$result1 = mysql_query("SELECT acct_number, concat( acct_name1, ', ', acct_name2) as 'acct_name'  FROM Employee_accounts where acct_is_active = 1 and acct_number in ('60587023','60500509','60500507','60587015','60587014','60587020','60587016','60586505','60587017','60580154','60580156','60580237','60586502','60580512') ORDER BY acct_number") or die (mysql_error());
$i = 0;
$arr_accountnames = array();
	while ( $row = mysql_fetch_array($result1) ) {
				$arr_accountnames[$row["acct_number"]] = $row["acct_name"];
				$i = $i+1;
	}
//print_r($arr_accountnames);
//***********************************************************************
// This array contains symbols in which there are Hathaway trades
$arr_symbols = array();

//***********************************************************************

//Get trades and check if condition is met.
$rcm = 0;		 		//Report conditions met variable
$rcm_final = 0; //Report conditions met variable final

				if ($trdm_trade_date != '') { 
				$str_trdm_trade_date = " where trdm_trade_date = '". $trdm_trade_date ."'";
				} else {
				$str_trdm_trade_date = " where trdm_trade_date = '". $trade_date_to_process ."'";
				}			  

				if ($trdm_symbol != '') { 
				$str_trdm_symbol = " and trdm_symbol = '".$trdm_symbol."'";
				} else {
				$str_trdm_symbol = " and trdm_symbol != '' and LENGTH(trdm_symbol) < 8";
				}			  

				if ($trdm_account_number != '') { 
				$str_trdm_account_number = " and trdm_account_number = '".$trdm_account_number."'";
				} else {
				$str_trdm_account_number = " and trdm_account_number not like '0000%'";
				}			  
						$query_statement_ht = "SELECT 	trdm_auto_id, 
																				trdm_account_number, 
																				trdm_trade_date, 
																				trdm_settle_date, 
																				abs(round(trdm_quantity,0)) as 'trdm_quantity',
																				round(trdm_price,2) as 'trdm_price',
																				trdm_buy_sell,
																				UPPER(trdm_symbol) as 'trdm_symbol',
																				trdm_sec_description,
																				trdm_trade_time
																 FROM Trades_m " . $str_trdm_trade_date . 
																 $str_trdm_symbol .
																 $str_trdm_account_number . ' and trdm_account_number in ('. $accounts_string . ") ".
																 " ORDER BY trdm_symbol, trdm_trade_time";	
						
						//echo "<BR>".$query_statement_ht."<BR>";
						//exit;
			      $result = mysql_query($query_statement_ht) or die (mysql_error());
	
						$symbol_i = 0;
						while ( $row = mysql_fetch_array($result) ) {

							if (in_array($row["trdm_account_number"], $arr_accounts)) {
							//echo "<BR>Report creation condition tested! Succeeded!";
							//echo "<BR>".$row["trdm_account_number"].','.$row["trdm_symbol"].','.$row["trdm_sec_description"].','.$row["trdm_buy_sell"].','.$row["trdm_quantity"].','.$row["trdm_price"].','.format_date_ymd_to_mdy($row["trdm_trade_date"]).','.$row["trdm_trade_time"];
								if (in_array($row["trdm_symbol"], $arr_symbols)) {
								//echo "not added";
								} else {
								$arr_symbols[$symbol_i] = $row["trdm_symbol"];
								$symbol_i = $symbol_i +1 ;
									if ($symbol_string == ''){
									$symbol_string = "'".$row["trdm_symbol"]."'";
									} else {
									$symbol_string = $symbol_string . ",'" . $row["trdm_symbol"]."'";
									}
								}
								
							$rcm = 1;
							
							} else {
							//echo "<BR>"."Report creation condition tested! Failed!";
							//echo "<BR>".$row["trdm_account_number"].','.$row["trdm_symbol"].','.$row["trdm_sec_description"].','.$row["trdm_buy_sell"].','.$row["trdm_quantity"].','.$row["trdm_price"].','.format_date_ymd_to_mdy($row["trdm_trade_date"]).','.$row["trdm_trade_time"];
							//Do nothing
							}
						}
						
						//print_r($arr_symbols);
						echo "<BR> Symbols in Hathaway trades: [". $symbol_string ."] <BR>";

//// Given that there are trades in Hathaway accounts, find out if there are trades in the following accounts
//   and if there are, the conditions are met, to produce and email the report.
//   Accounts: 44594053		Gold Fund
//             40500079   Gold Partners (Domestic)
//             40500080   Gold Partners (Offshore)
						
if ($rcm == 1) { 

$arr_monitored_fund_accts = array('44594053','40500079','40500080');
$monitored_fund_accts_string = "'44594053','40500079','40500080'";

						$query_statementz = "SELECT trdm_auto_id, 
																				trdm_account_number, 
																				trdm_trade_date, 
																				trdm_settle_date, 
																				abs(round(trdm_quantity,0)) as 'trdm_quantity',
																				round(trdm_price,2) as 'trdm_price',
																				trdm_buy_sell,
																				UPPER(trdm_symbol) as 'trdm_symbol',
																				trdm_sec_description,
																				trdm_trade_time
																 FROM Trades_m " . $str_trdm_trade_date . 
																 $str_trdm_symbol . ' and trdm_symbol in ('. $symbol_string . ") ".
																 $str_trdm_account_number . ' and trdm_account_number in ('.$monitored_fund_accts_string.") ".
																 " ORDER BY trdm_symbol, trdm_trade_time";	
						
						//echo "<BR> query_statementz	: [". $query_statementz ."] <BR>";
						//exit;
			      $resultz = mysql_query($query_statementz) or die (mysql_error());
						
						while ( $rowz = mysql_fetch_array($resultz) ) {
							
							//echo $rowz["trdm_account_number"] . "=> " . $rowz["trdm_symbol"] . "=> " .$rowz["trdm_trade_date"] . "<BR>";
							if ($rowz["trdm_account_number"] != '') {
							$rcm_final = 1;
							}
					  }
			
}

echo "<BR> Creating and emailing report to recipients.<BR>"; //= ". $rcm_final;

//// Found trades in the symbol in which there are Hathaway trades
//   Create reports and email them to the required recipients
if ($rcm_final == 1) {

//***************************************************************************************
//***************************************************************************************

//HATHAWAY TRADES EMAIL	

$reportbody = '
  <tr> 
    <td align="left" valign="top"> <table width="100%" cellpadding="5" cellspacing="5" border="1">
        <tr valign="top"> 
          <td>
            <!--Table with thin cell border-->
            <table width="100%" cellpadding="1", cellspacing="0" bgcolor="#FFFFFF">
              <tr> 
                <td> 
									<table class="tablewithdata" id="accounts_table"  width="100%"  border="0" cellspacing="1" cellpadding="1">
                    <tr class="headingblue"> 
                      <td colspan=9>John Hathaway accounts</td>
                    </tr>
										<tr class="tableheading"> 
                      <td >Acct.</td>
                      <td >Symbol</td>
                      <td >Description</td>
                      <td align="right">B/S</td>
                      <td align="right">Qty.</td>
                      <td align="right">Price</td>
                      <td align="right">Trade Date</td>
                      <td align="right">Time</td>
                      <td align="center" valign="middle" >NAME</td>
                    </tr>';
										
										$result = mysql_query($query_statement_ht) or die (mysql_error());

										while ( $row = mysql_fetch_array($result) ) {

										$reportbody .= '
										<tr class="tablerow">
                      <td nowrap>'.$row["trdm_account_number"].'</td>
                      <td nowrap align="right">'.$row["trdm_symbol"].'&nbsp;&nbsp;</td>
                      <td nowrap>'.$row["trdm_sec_description"].'</td>
                      <td nowrap align="right">'.$row["trdm_buy_sell"].'&nbsp;&nbsp;&nbsp;</td>
                      <td nowrap align="right">'.$row["trdm_quantity"].'&nbsp;&nbsp;</td>
                      <td nowrap align="right">'.$row["trdm_price"].'&nbsp;&nbsp;</td>
                      <td nowrap align="right">'.$row["trdm_trade_date"].'&nbsp;&nbsp;</td>
                      <td nowrap align="right">'.$row["trdm_trade_time"].'&nbsp;&nbsp;</td>
                      <TD nowrap>'.$arr_accountnames[$row["trdm_account_number"]].'</TD>
                    </tr>';
										}

										//for 44594053
										$query_statement_a = "SELECT trdm_auto_id, 
																				trdm_account_number, 
																				trdm_trade_date, 
																				trdm_settle_date, 
																				abs(round(trdm_quantity,0)) as 'trdm_quantity',
																				round(trdm_price,2) as 'trdm_price',
																				trdm_buy_sell,
																				UPPER(trdm_symbol) as 'trdm_symbol',
																				trdm_sec_description,
																				trdm_trade_time
																 FROM Trades_m " . $str_trdm_trade_date . 
																 $str_trdm_symbol . ' and trdm_symbol in ('. $symbol_string . ") ".
																 $str_trdm_account_number . " and trdm_account_number ='44594053' ".
																 " ORDER BY trdm_symbol, trdm_trade_time";
																 
										$reportbody .= '
										     <tr class="headingblue"> 
                           <td colspan=9>&nbsp;</td>
                         </tr>
									       <tr class="headingblue"> 
                           <td colspan=9>Tocqueville Gold Fund</td>
                         </tr>';
												 
										$result_a = mysql_query($query_statement_a) or die (mysql_error());

										while ( $row_a = mysql_fetch_array($result_a) ) {

										$reportbody .= '
										<tr class="tablerow">
                      <td nowrap>'.$row_a["trdm_account_number"].'</td>
                      <td nowrap align="right">'.$row_a["trdm_symbol"].'&nbsp;&nbsp;</td>
                      <td nowrap>'.$row_a["trdm_sec_description"].'</td>
                      <td nowrap align="right">'.$row_a["trdm_buy_sell"].'&nbsp;&nbsp;&nbsp;</td>
                      <td nowrap align="right">'.$row_a["trdm_quantity"].'&nbsp;&nbsp;</td>
                      <td nowrap align="right">'.$row_a["trdm_price"].'&nbsp;&nbsp;</td>
                      <td nowrap align="right">'.$row_a["trdm_trade_date"].'&nbsp;&nbsp;</td>
                      <td nowrap align="right">'.$row_a["trdm_trade_time"].'&nbsp;&nbsp;</td>
                      <TD nowrap>'.$arr_accountnames[$row_a["acct_number"]].'</TD>
                    </tr>';																 
										}						 	
										
										//
										//for 40500079
										$query_statement_b = "SELECT trdm_auto_id, 
																				trdm_account_number, 
																				trdm_trade_date, 
																				trdm_settle_date, 
																				abs(round(trdm_quantity,0)) as 'trdm_quantity',
																				round(trdm_price,2) as 'trdm_price',
																				trdm_buy_sell,
																				UPPER(trdm_symbol) as 'trdm_symbol',
																				trdm_sec_description,
																				trdm_trade_time
																 FROM Trades_m " . $str_trdm_trade_date . 
																 $str_trdm_symbol . ' and trdm_symbol in ('. $symbol_string . ") ".
																 $str_trdm_account_number . " and trdm_account_number ='40500079' ".
																 " ORDER BY trdm_symbol, trdm_trade_time";
																 
										$reportbody .= '
										     <tr class="headingblue"> 
                           <td colspan=9>&nbsp;</td>
                         </tr>
									       <tr class="headingblue"> 
                           <td colspan=9>Tocqueville Gold Partners (Domestic)</td>
                         </tr>';
												 
										$result_b = mysql_query($query_statement_b) or die (mysql_error());

										while ( $row_b = mysql_fetch_array($result_b) ) {

										$reportbody .= '
										<tr class="tablerow">
                      <td nowrap>'.$row_b["trdm_account_number"].'</td>
                      <td nowrap align="right">'.$row_b["trdm_symbol"].'&nbsp;&nbsp;</td>
                      <td nowrap>'.$row_b["trdm_sec_description"].'</td>
                      <td nowrap align="right">'.$row_b["trdm_buy_sell"].'&nbsp;&nbsp;&nbsp;</td>
                      <td nowrap align="right">'.$row_b["trdm_quantity"].'&nbsp;&nbsp;</td>
                      <td nowrap align="right">'.$row_b["trdm_price"].'&nbsp;&nbsp;</td>
                      <td nowrap align="right">'.$row_b["trdm_trade_date"].'&nbsp;&nbsp;</td>
                      <td nowrap align="right">'.$row_b["trdm_trade_time"].'&nbsp;&nbsp;</td>
                      <TD nowrap>'.$arr_accountnames[$row_b["acct_number"]].'</TD>
                    </tr>';																 
										}

										//
										//for 40500080
										$query_statement_c = "SELECT trdm_auto_id, 
																				trdm_account_number, 
																				trdm_trade_date, 
																				trdm_settle_date, 
																				abs(round(trdm_quantity,0)) as 'trdm_quantity',
																				round(trdm_price,2) as 'trdm_price',
																				trdm_buy_sell,
																				UPPER(trdm_symbol) as 'trdm_symbol',
																				trdm_sec_description,
																				trdm_trade_time
																 FROM Trades_m " . $str_trdm_trade_date . 
																 $str_trdm_symbol . ' and trdm_symbol in ('. $symbol_string . ") ".
																 $str_trdm_account_number . " and trdm_account_number ='40500080' ".
																 " ORDER BY trdm_symbol, trdm_trade_time";
																 
										$reportbody .= '
										     <tr class="headingblue"> 
                           <td colspan=9>&nbsp;</td>
                         </tr>
									       <tr class="headingblue"> 
                           <td colspan=9>Tocqueville Gold Partners (Offshore)</td>
                         </tr>';
												 
										$result_c = mysql_query($query_statement_c) or die (mysql_error());

										while ( $row_c = mysql_fetch_array($result_c) ) {

										$reportbody .= '
										<tr class="tablerow">
                      <td nowrap>'.$row_c["trdm_account_number"].'</td>
                      <td nowrap align="right">'.$row_c["trdm_symbol"].'&nbsp;&nbsp;</td>
                      <td nowrap>'.$row_c["trdm_sec_description"].'</td>
                      <td nowrap align="right">'.$row_c["trdm_buy_sell"].'&nbsp;&nbsp;&nbsp;</td>
                      <td nowrap align="right">'.$row_c["trdm_quantity"].'&nbsp;&nbsp;</td>
                      <td nowrap align="right">'.$row_c["trdm_price"].'&nbsp;&nbsp;</td>
                      <td nowrap align="right">'.$row_c["trdm_trade_date"].'&nbsp;&nbsp;</td>
                      <td nowrap align="right">'.$row_c["trdm_trade_time"].'&nbsp;&nbsp;</td>
                      <TD nowrap>'.$arr_accountnames[$row_c["acct_number"]].'</TD>
                    </tr>';																 
										}


								//ending the table row and table tags
								$reportbody .= '
								                 </table>
															</td>
														</tr>
            							</table>
            							<!--Table with thin cell border ends-->
          							</td>
        							</tr>
      							</table>
									</td>
  							</tr>';
		
		sys_mail("prasad_pravin@yahoo.com, rcc@tocqueville.com", "Hathaway Trades Report.",$reportbody,"Trades Report (John Hathaway) generated on ".date('D, d/m/Y h:i:s a'));

		echo "<BR>Mail sent successfully to recipients.";



//***************************************************************************************
//***************************************************************************************

} else {

echo "Report condition not met! No report to be emailed";

}



//OLD STUFF (HARD CODED REPORT)
			

/*$reportbody = '
  <tr> 
    <td align="left" valign="top"> <table width="100%" cellpadding="5" cellspacing="5" border="1">
        <tr valign="top"> 
          <td>
            <!--Table with thin cell border-->
            <table width="100%" cellpadding="1", cellspacing="0" bgcolor="#FFFFFF">
              <tr> 
                <td> 
									<table class="tablewithdata" id="accounts_table"  width="100%"  border="0" cellspacing="1" cellpadding="1">
                    <tr class="headingblue"> 
                      <td colspan=9>John Hathaway accounts</td>
                    </tr>
                    <tr class="tableheading"> 
                      <td >Acct.</td>
                      <td >Symbol</td>
                      <td >Description</td>
                      <td align="right">B/S</td>
                      <td align="right">Qty.</td>
                      <td align="right">Price</td>
                      <td align="right">Trade Date</td>
                      <td align="right">Time</td>
                      <td align="center" valign="middle" >NAME</td>
                    </tr>
                    <tr class="tablerow">
                      <td nowrap>60500509</td>
                      <td nowrap align="right">AAPL&nbsp;&nbsp;</td>
                      <td nowrap>Apple Computer Inc.</td>
                      <td nowrap align="right">B&nbsp;&nbsp;&nbsp;</td>
                      <td nowrap align="right">1000&nbsp;&nbsp;</td>
                      <td nowrap align="right">22.40&nbsp;&nbsp;</td>
                      <td nowrap align="right">02/20/2004&nbsp;&nbsp;</td>
                      <td nowrap align="right">03:49:39pm&nbsp;&nbsp;</td>
                      <TD nowrap>HATHAWAY DAVID R</TD>
                    </tr>
                    <tr class="tablerow">
                      <td nowrap>60500509</td>
                      <td nowrap align="right">AMZN&nbsp;&nbsp;</td>
                      <td nowrap>Amazon.com Inc.</td>
                      <td nowrap align="right">S&nbsp;&nbsp;&nbsp;</td>
                      <td nowrap align="right">3000&nbsp;&nbsp;</td>
                      <td nowrap align="right">45.03&nbsp;&nbsp;</td>
                      <td nowrap align="right">02/20/2004&nbsp;&nbsp;</td>
                      <td nowrap align="right">03:20:27pm&nbsp;&nbsp;</td>
                      <TD nowrap>HATHAWAY DAVID R</TD>
                    </tr>
                    <tr class="tablerow">
                      <td nowrap>60587015</td>
                      <td nowrap align="right">ANS&nbsp;&nbsp;</td>
                      <td nowrap>Airnet Systems Inc.</td>
                      <td nowrap align="right">S&nbsp;&nbsp;&nbsp;</td>
                      <td nowrap align="right">5000&nbsp;&nbsp;</td>
                      <td nowrap align="right">4.10&nbsp;&nbsp;</td>
                      <td nowrap align="right">02/20/2004&nbsp;&nbsp;</td>
                      <td nowrap align="right">03:49:37pm&nbsp;&nbsp;</td>
                      <TD nowrap>HATHAWAY J</TD>
                    </tr>
                    <tr class="tablerow">
                      <td nowrap>60587016</td>
                      <td nowrap align="right">AA&nbsp;&nbsp;</td>
                      <td nowrap>Alcoa Inc.</td>
                      <td nowrap align="right">S&nbsp;&nbsp;&nbsp;</td>
                      <td nowrap align="right">500&nbsp;&nbsp;</td>
                      <td nowrap align="right">36.60&nbsp;&nbsp;</td>
                      <td nowrap align="right">02/20/2004&nbsp;&nbsp;</td>
                      <td nowrap align="right">01:49:00pm&nbsp;&nbsp;</td>
                      <TD nowrap>HATHAWAY JULIA</TD>
                    </tr>
                  

										<tr class="headingblue"> 
                      <td colspan=9>&nbsp;</td>
                    </tr>
									  <tr class="headingblue"> 
                      <td colspan=9>Tocqueville Gold Fund</td>
                    </tr>
                    <tr class="tablerow"> 
                      <td colspan=9>&nbsp;</td>
                    </tr>
										<tr class="tablerow"> 
                      <td colspan=9>No matches found!</td>
                    </tr>



										<tr class="headingblue"> 
                      <td colspan=9>&nbsp;</td>
                    </tr>

                    <tr class="headingblue"> 
                      <td colspan=9>Tocqueville Gold Partners (Domestic)</td>
                    </tr>
                    <tr class="tablerow">
                      <td nowrap>40500079</td>
                      <td nowrap align="right">AAPL&nbsp;&nbsp;</td>
                      <td nowrap>Apple Computer Inc.</td>
                      <td nowrap align="right">B&nbsp;&nbsp;&nbsp;</td>
                      <td nowrap align="right">10000&nbsp;&nbsp;</td>
                      <td nowrap align="right">22.40&nbsp;&nbsp;</td>
                      <td nowrap align="right">02/20/2004&nbsp;&nbsp;</td>
                      <td nowrap align="right">03:49:39pm&nbsp;&nbsp;</td>
                      <TD nowrap>&nbsp;</TD>
                    </tr>
                    <tr class="tablerow">
                      <td nowrap>40500079</td>
                      <td nowrap align="right">AAPL&nbsp;&nbsp;</td>
                      <td nowrap>Apple Computer Inc.</td>
                      <td nowrap align="right">B&nbsp;&nbsp;&nbsp;</td>
                      <td nowrap align="right">5000&nbsp;&nbsp;</td>
                      <td nowrap align="right">23.50&nbsp;&nbsp;</td>
                      <td nowrap align="right">02/20/2004&nbsp;&nbsp;</td>
                      <td nowrap align="right">04:30:25pm&nbsp;&nbsp;</td>
                      <TD nowrap>&nbsp;</TD>
                    </tr>
                      <tr class="tablerow">
                      <td nowrap>60587015</td>
                      <td nowrap align="right">ANS&nbsp;&nbsp;</td>
                      <td nowrap>Airnet Systems Inc.</td>
                      <td nowrap align="right">S&nbsp;&nbsp;&nbsp;</td>
                      <td nowrap align="right">5000&nbsp;&nbsp;</td>
                      <td nowrap align="right">4.10&nbsp;&nbsp;</td>
                      <td nowrap align="right">02/20/2004&nbsp;&nbsp;</td>
                      <td nowrap align="right">03:49:37pm&nbsp;&nbsp;</td>
                      <TD nowrap>&nbsp; </TD>
                    </tr>
                     <tr class="tablerow">
                      <td nowrap>60587015</td>
                      <td nowrap align="right">ANS&nbsp;&nbsp;</td>
                      <td nowrap>Airnet Systems Inc.</td>
                      <td nowrap align="right">S&nbsp;&nbsp;&nbsp;</td>
                      <td nowrap align="right">5000&nbsp;&nbsp;</td>
                      <td nowrap align="right">4.10&nbsp;&nbsp;</td>
                      <td nowrap align="right">02/20/2004&nbsp;&nbsp;</td>
                      <td nowrap align="right">03:49:37pm&nbsp;&nbsp;</td>
                      <TD nowrap>&nbsp;</TD>
                    </tr> 

										<tr class="headingblue"> 
                      <td colspan=9>&nbsp;</td>
                    </tr>
										<tr class="headingblue"> 
                      <td colspan=9>Tocqueville Gold Partners (Offshore)</td>
                    </tr>

										<tr class="tablerow"> 
                      <td colspan=9>&nbsp;</td>
                    </tr>
										<tr class="tablerow"> 
                      <td colspan=9>No matches found!</td>
                    </tr>
                 </table></td>
              </tr>
            </table>
            <!--Table with thin cell border ends-->
          </td>
        </tr>
      </table>
			</td>
  	</tr>';
		*/

//sys_mail("prasad_pravin@yahoo.com", "Sample Report, please review.",$reportbody,"Trades Report (John Hathaway) generated on Fri, 02/27/2004 12:32 pm");

//echo "mail sent to prasad_pravin";

?>
