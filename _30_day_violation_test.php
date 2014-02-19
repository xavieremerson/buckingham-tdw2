<?php
//error_reporting(E_ALL);
include('inc_header.php');
?>
		<!-- START TABLE 1 -->
		<table width="100%" height="100%" border="0" cellspacing="1" cellpadding="0">
			<tr> 
				<td valign="top">
				<?
//======================================================================================================================
// 30 Day Holding Period Violation Testing
tsp(100, "Review 30 Day Holding Period Detection");
?>
<table width="100%" border="0" cellpadding="2">
<form id="clnt_activity" name="clnt_activity" method="get" action="<?=$PHP_SELF?>">
  <tr>
    <td width="300">Select Employee:</td>
    <td width="400">
      <select class="Text" id="user_idx" name="user_idx" size="1" style="width:300px">
      <option value="">Please Select</option>
      <option value="" >------ ------</option>
      <?
      $result = mysql_query("SELECT * FROM users WHERE user_isactive = '1' AND is_login_acct = '1' ORDER BY Fullname") or die (mysql_error());
      
      while ( $row = mysql_fetch_array($result) ) {
      ?>
        <option value="<?=$row["ID"]?>" <?=( trim($row["ID"]) == trim($user_idx) ) ? 'selected' : ''?>><?=substr($row["Fullname"],0,20)?></option>
      <?
      }
      ?>	
      </select>
    </td>
    <td>(As if he/she were making the preapproval request)</td>
  </tr>
  <tr>
    <td>Date Request to be made</td>
    <td>
			<SCRIPT LANGUAGE="JavaScript" SRC="includes/calendarpopup/CalendarPopup.js"></SCRIPT>
      <SCRIPT LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
        <SCRIPT LANGUAGE="JavaScript">
        var caldate = new CalendarPopup("divdate");
        caldate.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
      </SCRIPT>
      <input type="text" id="iddateval" class="Text1" name="dateval" size="12" maxlength="12" value="<?=$dateval?>">
      &nbsp;&nbsp;
      <A HREF="#" onClick="caldate.select(document.forms['clnt_activity'].dateval,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A>					
    </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Symbol</td>
    <td><input type="text"  name="symbol" style="font-family:verdana;width:200px;font-size:12px" id="symbol" value="<?=$symbol?>" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Quantity</td>
    <td><input type="text" name="quantity" style="font-family:verdana;width:200px;font-size:12px" id='quantity' value="<?=$quantity?>" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Buy/Sell</td>
    <td>
    <select class="Text" id="str_buysell" name="str_buysell" size="1" style="width:100px">
    <option value="" <?=( '' == trim($str_buysell) ) ? 'selected' : ''?>>Select</option>
    <option value="B" <?=( 'B' == trim($str_buysell) ) ? 'selected' : ''?>>Buy</option>
    <option value="S" <?=( 'S' == trim($str_buysell) ) ? 'selected' : ''?>>Sell</option>
    <option value="C" <?=( 'C' == trim($str_buysell) ) ? 'selected' : ''?>>Cover</option>
    <option value="S" <?=( 'SS' == trim($str_buysell) ) ? 'selected' : ''?>>Short</option>
    </select>
    </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><input type="submit" name="valsubmit" value="Enter Test Request for Preapproval"/></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</form>
</table>

	<DIV ID="divdate" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
<div id="err_notify"></>
<?
tep();
if ($_GET) {
?><br /></div>
<?
//print_r($_GET);
tsp(100, "Supporting Data for 30 Day Holding Period Detection");
//_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET
//print_r($_GET);

	 if ($str_buysell == 'S' || $str_buysell == 'B' ) { //Process the following only if there is a Buy or a Sell
	 
						$hval = db_single_val("select var_value as single_val from var_lookup_values where var_type = 'hval'");
						$nval = db_single_val("select Fullname as single_val from users where ID = '".$user_id."'");
						$str_date = format_date_mdy_to_ymd($dateval);
						$str_cutoff  = mktime(0, 0, 0, date('m', strtotime($str_date)), date('d', strtotime($str_date))-($hval+1), date('Y', strtotime($str_date)));
						$date_cutoff = date('Y-m-d',$str_cutoff); //business_day_backward(strtotime(date('Y-m-d')),($hval+1));
					 //NOT USING BUSINESS DAYS, INSTEAD USING CALENDAR DAYS ABOVE
					 echo '<a class="ilt">30 Day Prior Date: '.format_date_ymd_to_mdy($date_cutoff).'</a><br>'; //xdebug("str_acct",$str_acct);
		
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
					 echo '<a class="ilt">Account Numbers (Fidelity): '.str_replace("'","",$str_acct).'</a><br>'; //xdebug("str_acct",$str_acct);

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
					 echo '<a class="ilt">Account Numbers (Others): '.str_replace("'","",$str_acct_ext).'</a>'; //xdebug("str_acct",$str_acct);
				
		}
			
	 //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& 
	 //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& 
	 //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& 
	 if ($str_buysell == 'S') {

				//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
				//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					 
				//Get all FIDELITY buys in the symbol in the past 30 days.
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
								and b.oac_emp_userid = '".$user_idx."'";
				//xdebug("qry",$qry);
				$qty_buy_others = db_single_val($qry);  
				//xdebug("qty_buy_others",$qty_buy_others);
					 
				//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
				//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
				
				$total_buys = $qty_buy_fidelity + $qty_buy_others;
				//xdebug("total_buys",$total_buys);
				
				$str_to_pass = "";
				if ($total_buys != 0 && $quantity != 0) {
					$str_to_pass = "<br><strong><font color='red'>Potential ".$hval." days Restriction Situation.</font></strong>";
				} else {
					$str_to_pass = "<br><strong><font color='green'>NO Potential ".$hval." days Restriction Situation.</font></strong>"; //"1"; //
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
										and b.oac_emp_userid = '".$user_idx."'";
					 //xdebug("qry",$qry);
					 $qty_sell_others = db_single_val($qry);
					 //xdebug("qty_buy_others",$qty_buy_others);
					 //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					 //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
				
					 $total_sells = $qty_sell_fidelity + $qty_sell_others;
					 //xdebug("total_buys",$total_buys);
				
					 $str_to_pass = "";
					 if ($total_sells != 0 && $quantity != 0) {
							$str_to_pass = "<br><strong><font color='red'>Potential ".$hval." days Restriction Situation.</font></strong>";
					 } else {
							$str_to_pass = "<br><strong><font color='green'>NO Potential ".$hval." days Restriction Situation.</font></strong>"; //"1"; //
					 }
					 //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					 //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
				
					 echo $str_to_pass;

	 } else {
					 $str_to_pass = "Error! Please make sure you have filled all fields above and resubmit.";
					 echo $str_to_pass;
	 }	
	 
	 //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& 
	 //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& 
	 //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& 

   // RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW 

		//RAW DATA
		$qry = "select * from fidelity_emp_trades 
						where symbol = '".strtoupper($symbol)."' 
						and acct_num in " . $str_acct . " 
						and trade_date between DATE_SUB('".$str_date."', INTERVAL 180 DAY) and '".$str_date."'
						ORDER BY trade_date desc";
						//and buy_sell = 'B'
						//'".$date_cutoff."'
		//xdebug("qry",$qry);
		$result = mysql_query($qry) or die(tdw_mysql_error($qry));
		$count_return = mysql_num_rows($result);
		if ($count_return > 0) {
		?>
		<table width="100%" height="100%" cellpadding="1", cellspacing="0" bgcolor="#AAAAAA">
				<tr>
					<td>		
						<script language="JavaScript" src="includes/javascript/sorttable.js" type="text/javascript"></script>
						<table class="sortable" preserve_style="cell" width="100%" border="0"  cellspacing="1" cellpadding="1">
							<thead class="datadisplay">
								<tr bgcolor="#cccccc">
									<td ts_type="date" width="90">Trade Date</td>
									<td width="56">Symbol</td>
									<td width="200">Sec. Description</td>
									<td width="80">Buy/Sell</td>
									<td width="80">Quantity</td>
									<td width="300">Account Number</td>
									<td>&nbsp;</td>
								</tr>
							</thead>
							<tbody id="offTblBdy" class="datadisplay">
							<?		
							$countx = 0;
							while ($row = mysql_fetch_array($result)) {
								
								if ($countx%2) {
										$rowclass = "trdark";
								} else { 
										$rowclass = "trlight"; 
								} 					 
								/*auto_id processed_on symbol
									cusip sec_type sec_desc_1 sec_desc_2 sec_desc_3 sec_desc_4 sec_desc_5 sec_desc_6 buy_sell acct_type 
									acct_num broker price quantity commission principal trade_date settle_date accrued_interest net 
									cancel correct ssno_1 first_name middle_name last_name ssno_2 custom_1 option_symbol order_entry_time 
									order_exec_time is_active
								*/
								?>
								<tr class="<?=$rowclass?>">
									<td>&nbsp;&nbsp;&nbsp;<?=format_date_ymd_to_mdy($row["trade_date"])?></td> 
									<td>&nbsp;&nbsp;&nbsp;<?=$row["symbol"]?></td>
									<td>&nbsp;&nbsp;&nbsp;<?=$row["sec_desc_1"]?></td>
									<td>&nbsp;&nbsp;&nbsp;<?=offset_buy_sell($row["buy_sell"])?></td>
									<td align='right'>&nbsp;&nbsp;&nbsp;<?=round($row["quantity"],0)?>&nbsp;&nbsp;&nbsp;</td>
									<td align='right'><?=$row["acct_num"]?>&nbsp;&nbsp;&nbsp;</td>
									<td>&nbsp;</td>
								</tr>
							<?
							$countx++;
						}
						?>
					</table>
				</td>
			</tr>
		</table>
		<?
		} //end if

		//RAW DATA
		$qry = "select a.*, b.oac_custodian, b.oac_account_number  from otd_emp_trades_external a
						left join oac_emp_accounts b on a.otd_account_id = b.auto_id
						where a.otd_symbol = '".strtoupper($symbol)."' 
						and a.otd_trade_date between DATE_SUB('".$str_date."', INTERVAL 180 DAY) and '".$str_date."'
						and b.oac_emp_userid = '".$user_idx."'";
						//and a.otd_buysell = 'B'
						//$date_cutoff
		//xdebug("qry",$qry);
		$result = mysql_query($qry) or die(tdw_mysql_error($qry));
		$count_return = mysql_num_rows($result);
		if ($count_return > 0) {
		?>
			<table width="100%" height="100%" cellpadding="1", cellspacing="0" bgcolor="#AAAAAA">
				<tr>
					<td>		
						<script language="JavaScript" src="includes/javascript/sorttable.js" type="text/javascript"></script>
						<table class="sortable" preserve_style="cell" width="100%" border="0"  cellspacing="1" cellpadding="1">
							<thead class="datadisplay">
								<tr bgcolor="#cccccc">
									<td ts_type="date" width="90">Trade Date</td>
									<td width="56">Symbol</td>
									<td width="200">Sec. Description</td>
									<td width="80">Buy/Sell</td>
									<td width="80">Quantity</td>
									<td width="300">Account Number</td>
									<td>&nbsp;</td>
								</tr>
							</thead>
							<tbody id="offTblBdy" class="datadisplay">
							<?	
							$str_company_name = get_company_name(strtoupper($symbol));
							
							$countx = 0;
							while ($row = mysql_fetch_array($result)) {
								
								if ($countx%2) {
										$rowclass = "trdark";
								} else { 
										$rowclass = "trlight"; 
								} 					 
								//xdebug("qry",$qry);
								//auto_id  otd_account_id  otd_trade_date  otd_buysell  otd_symbol  otd_quantity  otd_price  otd_entered_by  
								//otd_last_edited_by  otd_last_edited_on  otd_isactive  oac_custodian  oac_account_number 
								?>
									<tr class="<?=$rowclass?>">
									<td>&nbsp;&nbsp;&nbsp;<?=format_date_ymd_to_mdy($row["otd_trade_date"])?></td> 
									<td>&nbsp;&nbsp;&nbsp;<?=$row["otd_symbol"]?></td>
									<td>&nbsp;&nbsp;&nbsp;<?=$str_company_name?></td>
									<td>&nbsp;&nbsp;&nbsp;<?=offset_buy_sell($row["otd_buysell"])?></td>
									<td align='right'>&nbsp;&nbsp;&nbsp;<?=round($row["otd_quantity"],0)?>&nbsp;&nbsp;&nbsp;</td>
									<td align='right'><?=$row["oac_account_number"]?>&nbsp;(<?=$row["oac_custodian"]?>)&nbsp;&nbsp;</td>
									<td>&nbsp;</td>
									</tr>
								<?
								$countx++;
							}
							?>
						</table>
					</td>
				</tr>
			</table>
		<?
		} //end if

   // RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW RAW 

//_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET_GET
tep();
}
//======================================================================================================================
?>
				</td>
			</tr>
		</table>
		<!-- END TABLE 1 -->
<?php
include('inc_footer.php'); 
?>

