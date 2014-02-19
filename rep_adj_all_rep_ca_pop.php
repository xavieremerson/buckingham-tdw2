<html>
<head>
<title>Apply Adjustment</title>
<script language="Javascript" SRC="../includes/javascript.js"></script>
<link rel="stylesheet" type="text/css" href="includes/styles.css" />

<style type="text/css">
<!--
.adj_lbl {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
	font-weight: bold;
	color: #333333;
}
.adj_lbl_note {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
	font-weight: bold;
	color: #FF6600;
}
.adj_txt {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
	font-weight: bold;
	color: #0000ff;
}

-->
</style>
</head>
<? 
include('includes/dbconnect.php');
include('includes/global.php');
include('includes/functions.php');
/*
0 = [79]  userid
1 = [S]  single/bulk
2 = [2006-03-29]  trade_date
3 = [BUCK]  adv code
4 = [001]  rr
5 = [CHS]  ticker
6 = [B]  buy/sell

0 = [79]
1 = [SREF]
2 = [2006-03-30]
3 = [AMAR]
4 = [040]
5 = [HORC]
6 = [B]
7 = [06089833751]
*/
?>
 <!--<body onunload="window.opener.location.reload()"> -->
 <!--<body onunload="window.opener.document.clnt_activity.submit()">-->
 <body leftmargin="1" topmargin="1" rightmargin="1" bottommargin="1">
<?
echo "<center>";

tsp(100, "Apply Adjustment");
$vals_param = split('@',$param);

//show_array($vals_param);
//exit;

$user_id = $vals_param[0];

if ($vals_param[1] == "G") {
	$adj_type = "Bulk";
} elseif ($vals_param[1] == "S") {
	$adj_type = "Single";
	//get the account number and trade reference number here to display for change in acct number
  $qry_get_refnum_and_acct = "SELECT trad_account_number, trad_reference_number 
															FROM mry_comm_rr_trades 
															WHERE trad_rr = '".$vals_param[4]."'
																AND trad_trade_date = '".$vals_param[2]."'
																AND trad_advisor_code = '".$vals_param[3]."'
																AND trad_symbol = '".$vals_param[5]."'
																AND trad_buy_sell = '".$vals_param[6]."'";
	$result_get_refnum_and_acct = mysql_query($qry_get_refnum_and_acct) or die(tdw_mysql_error($qry_get_refnum_and_acct));
	while($row_get_refnum_and_acct = mysql_fetch_array($result_get_refnum_and_acct))
	{
		//get the values for trad_account_number, trad_reference_number
		$trade_ref = $row_get_refnum_and_acct["trad_reference_number"];
		$trad_acct_num =  $row_get_refnum_and_acct["trad_account_number"]; 
	}
} else { //"SREF"
	$adj_type = "Single";
	$trade_ref = $vals_param[7];
	//get the account number here to display for change in acct number
  $qry_get_refnum_and_acct = "SELECT trad_account_number 
															FROM mry_comm_rr_trades 
															WHERE trad_reference_number = '".$trade_ref."'";
	$result_get_refnum_and_acct = mysql_query($qry_get_refnum_and_acct) or die(tdw_mysql_error($qry_get_refnum_and_acct));
	while($row_get_refnum_and_acct = mysql_fetch_array($result_get_refnum_and_acct))
	{
		//get the values for trad_account_number
		$trad_acct_num =  $row_get_refnum_and_acct["trad_account_number"]; 
	}

}

//show_array($_POST);
if ($proceed) {

	$sel_vals_count = 0;  //1=rep 2=acct 4=client code (logic = sum of any two cannot equal the third.
	
	if (trim($new_rep)  !='') {$sel_vals_count = $sel_vals_count + 1;}
	if (trim($new_acct) !='') {$sel_vals_count = $sel_vals_count + 2;}
	if (trim($new_clnt) !='') {$sel_vals_count = $sel_vals_count + 4;}

	if ($sel_vals_count == 0) {
			echo "<a class='adj_txt'>You must change RR or Account Number or Client Code to proceed. Please start again.</a>";
			exit;
	} elseif ($sel_vals_count == 1) { // rep  change
			echo "<a class='adj_txt'>Proceeding with RR changes.</a>";
			//#############################################################
	    //rep change process
			//non existent rep
			$qry_rep_exists = "SELECT trad_rr
												FROM mry_comm_rr_trades
												WHERE trad_rr = '".$new_rep."'
												LIMIT 0 , 10";
			$result_rep_exists = mysql_query($qry_rep_exists) or die(tdw_mysql_error($qry_rep_exists));
			$count_rep = mysql_num_rows($result_rep_exists);
			if ($count_rep == 999) { //TBD
			echo "<a class='adj_txt'>RR is invalid. Please start again.</a>";
			exit;
			}
			
			//xdebug("trade_ref",$_POST["trade_ref"]);
			//get affected rows
			if ($adj_type_code == 'SREF') {  
				//xdebug("trade_ref",$trade_ref);
				$qry_affected_trades = "SELECT * FROM mry_comm_rr_trades 
																WHERE trad_reference_number = '".$_POST["trade_ref"]."' AND trad_is_cancelled = 0";
			} else {
				// new_rep, adj_type_code, trade_date, adv_code, old_rep, symbol, buysell, trade_ref
				$qry_affected_trades = "SELECT * FROM mry_comm_rr_trades 
																WHERE 
																trad_rr = '".$old_rep."'
																AND trad_trade_date = '".$trade_date."'
																AND trad_advisor_code = '".$adv_code."'
																AND trad_symbol = '".$symbol."'
																AND trad_buy_sell = '".$buysell."' AND trad_is_cancelled = 0";
			}
			
			//xdebug("qry_affected_trades",$qry_affected_trades);
			$result_affected_row = mysql_query($qry_affected_trades) or die(tdw_mysql_error($qry_affected_trades));
			while($row_affected_row = mysql_fetch_array($result_affected_row))
			{
			
			//insert affected rows into adj table
			$qry_insert_affected_row = "INSERT INTO rep_comm_rr_trades_adj 
																	(trad_auto_id,
																	 trad_reference_number,
																	 trad_rr,
																	 trad_trade_date,
																	 trad_settle_date,
																	 trad_run_date,
																	 trad_advisor_code,
																	 trad_advisor_name,
																	 trad_account_name,
																	 trad_account_number,
																	 trad_symbol,
																	 trad_buy_sell,
																	 trad_quantity,
																	 trade_price,
																	 trad_commission,
																	 trad_cents_per_share,
																	 trad_adj_rr,
																	 trad_adj_changed_by,
																	 trad_adj_comment,
																	 trad_adj_datetime,
																	 trad_is_cancelled
																	 ) 
																	VALUES (
																	'".$row_affected_row["trad_auto_id"]."', 
																	'".trim($row_affected_row["trad_reference_number"])."', 
																	'".$row_affected_row["trad_rr"]."', 
																	'".$row_affected_row["trad_trade_date"]."', 
																	'".$row_affected_row["trad_settle_date"]."', 
																	'".$row_affected_row["trad_run_date"]."', 
																	'".$row_affected_row["trad_advisor_code"]."', 
																	'".str_replace("'","",$row_affected_row["trad_advisor_name"])."', 
																	'".str_replace("'","",$row_affected_row["trad_account_name"])."', 
																	'".$row_affected_row["trad_account_number"]."', 
																	'".$row_affected_row["trad_symbol"]."', 
																	'".$row_affected_row["trad_buy_sell"]."', 
																	'".$row_affected_row["trad_quantity"]."', 
																	'".$row_affected_row["trade_price"]."', 
																	'".$row_affected_row["trad_commission"]."', 
																	'".$row_affected_row["trad_cents_per_share"]."', 
																	'".$new_rep."', 
																	'".$_POST["user_id"]."', 
																	'"."->no comments<-"."', 
																	now(), 
																	'0'
																	)";
			
			//figure out page load time 
			//$time=getmicrotime();
																					
			//xdebug("qry_insert_affected_row",$qry_insert_affected_row);
			$result_insert_affected_row = mysql_query($qry_insert_affected_row) or die(tdw_mysql_error($qry_insert_affected_row));
			
			//update this row in the mry table to is_cancelled = 2
			$qry_update_affected_row = "UPDATE mry_comm_rr_trades SET trad_is_cancelled = 2
																	WHERE trad_reference_number = '".trim($row_affected_row["trad_reference_number"])."' AND trad_is_cancelled = 0";
			//xdebug("qry_update_affected_row",$qry_update_affected_row);
			$result_update_affected_row = mysql_query($qry_update_affected_row) or die(tdw_mysql_error($qry_update_affected_row));	
			
			//update this row in the rep table to is_cancelled = 2
			$qry_update_affected_row_ = "UPDATE rep_comm_rr_trades SET trad_is_cancelled = 2
																	WHERE trad_reference_number = '".trim($row_affected_row["trad_reference_number"])."' AND trad_is_cancelled = 0";
			//xdebug("qry_update_affected_row",$qry_update_affected_row);
			$result_update_affected_row_ = mysql_query($qry_update_affected_row_) or die(tdw_mysql_error($qry_update_affected_row_));	
			
			//insert a similar row in mry_comm_rr with the new rr
			$qry_new_insert_affected_row = "INSERT INTO mry_comm_rr_trades 
																		(trad_reference_number,
																		 trad_rr,
																		 trad_trade_date,
																		 trad_settle_date,
																		 trad_run_date,
																		 trad_advisor_code,
																		 trad_advisor_name,
																		 trad_account_name,
																		 trad_account_number,
																		 trad_symbol,
																		 trad_buy_sell,
																		 trad_quantity,
																		 trade_price,
																		 trad_commission,
																		 trad_cents_per_share,
																		 trad_is_cancelled
																		 ) 
																		VALUES (
																		'".trim($row_affected_row["trad_reference_number"])."', 
																		'".$new_rep."', 
																		'".$row_affected_row["trad_trade_date"]."', 
																		'".$row_affected_row["trad_settle_date"]."',
																		'".$row_affected_row["trad_run_date"]."', 
																		'".$row_affected_row["trad_advisor_code"]."', 
																		'".str_replace("'","",$row_affected_row["trad_advisor_name"])."', 
																		'".str_replace("'","",$row_affected_row["trad_account_name"])."', 
																		'".$row_affected_row["trad_account_number"]."', 
																		'".$row_affected_row["trad_symbol"]."', 
																		'".$row_affected_row["trad_buy_sell"]."', 
																		'".$row_affected_row["trad_quantity"]."', 
																		'".$row_affected_row["trade_price"]."', 
																		'".$row_affected_row["trad_commission"]."', 
																		'".$row_affected_row["trad_cents_per_share"]."', 
																		'0'
																		)";
				//xdebug("qry_new_insert_affected_row",$qry_new_insert_affected_row);
				$result_new_insert_affected_row = mysql_query($qry_new_insert_affected_row) or die(tdw_mysql_error($qry_new_insert_affected_row));
			
			//insert a similar row in rep_comm_rr_trades with the new rr
			$qry_new_insert_affected_row_ = "INSERT INTO rep_comm_rr_trades 
																		(trad_reference_number,
																		 trad_rr,
																		 trad_trade_date,
																		 trad_settle_date,
																		 trad_run_date,
																		 trad_advisor_code,
																		 trad_advisor_name,
																		 trad_account_name,
																		 trad_account_number,
																		 trad_symbol,
																		 trad_buy_sell,
																		 trad_quantity,
																		 trade_price,
																		 trad_commission,
																		 trad_cents_per_share,
																		 trad_is_cancelled
																		 ) 
																		VALUES (
																		'".trim($row_affected_row["trad_reference_number"])."', 
																		'".$new_rep."', 
																		'".$row_affected_row["trad_trade_date"]."', 
																		'".$row_affected_row["trad_settle_date"]."',
																		'".$row_affected_row["trad_run_date"]."', 
																		'".$row_affected_row["trad_advisor_code"]."', 
																		'".str_replace("'","",$row_affected_row["trad_advisor_name"])."', 
																		'".str_replace("'","",$row_affected_row["trad_account_name"])."', 
																		'".$row_affected_row["trad_account_number"]."', 
																		'".$row_affected_row["trad_symbol"]."', 
																		'".$row_affected_row["trad_buy_sell"]."', 
																		'".$row_affected_row["trad_quantity"]."', 
																		'".$row_affected_row["trade_price"]."', 
																		'".$row_affected_row["trad_commission"]."', 
																		'".$row_affected_row["trad_cents_per_share"]."', 
																		'0'
																		)";
				//xdebug("qry_new_insert_affected_row",$qry_new_insert_affected_row);
				$result_new_insert_affected_row_ = mysql_query($qry_new_insert_affected_row_) or die(tdw_mysql_error($qry_new_insert_affected_row_));
			
				//echo " ". sprintf("%01.7f",((getmicrotime()-$time)/1000))." s."; 			
			}
			
			?>
			<br><br><br>&nbsp;&nbsp;&nbsp;<a class='adj_txt'>Adjustment Applied successfully.</a>
			<br><br>&nbsp;&nbsp;&nbsp;<a class="adj_txt" href="javascript:window.close();">>>CLOSE<<</a>
			
			<script language="javascript">
			//window.opener.document.clnt_activity.submit();
			</script>
			<?
			//#############################################################
	} elseif ($sel_vals_count == 2) { // acct change
			echo "<a class='adj_txt'>Proceeding with Account changes.</a>";
			//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		  //account number modification process
			//non existent account
			$qry_acct_exists = "SELECT trad_account_number
													FROM mry_comm_rr_trades
													WHERE trad_account_number = '".trim(strtoupper($new_acct))."'
													LIMIT 0 , 10";
			$result_acct_exists = mysql_query($qry_acct_exists) or die(tdw_mysql_error($qry_acct_exists));
			$count_acct = mysql_num_rows($result_acct_exists);
			if ($count_acct == 0) {
			echo "<a class='adj_txt'>The Account Number you entered is invalid. Please start over again.</a>";
			exit;
			}
			
			//xdebug("trade_ref",$_POST["trade_ref"]);
			//get affected rows
			if ($adj_type_code == 'SREF') {  
				//xdebug("trade_ref",$trade_ref);
				$qry_affected_trades = "SELECT * FROM mry_comm_rr_trades 
																WHERE trad_reference_number = '".$_POST["trade_ref"]."' AND trad_is_cancelled = 0";
			} else {
				// new_rep, adj_type_code, trade_date, adv_code, old_rep, symbol, buysell, trade_ref
				$qry_affected_trades = "SELECT * FROM mry_comm_rr_trades 
																WHERE 
																trad_rr = '".$old_rep."'
																AND trad_trade_date = '".$trade_date."'
																AND trad_advisor_code = '".$adv_code."'
																AND trad_symbol = '".$symbol."'
																AND trad_buy_sell = '".$buysell."' AND trad_is_cancelled = 0";
			}
			
			//xdebug("qry_affected_trades",$qry_affected_trades);
			$result_affected_row = mysql_query($qry_affected_trades) or die(tdw_mysql_error($qry_affected_trades));
			while($row_affected_row = mysql_fetch_array($result_affected_row))
			{
			
			//insert affected rows into adj table
			$qry_insert_affected_row = "INSERT INTO rep_comm_rr_trades_adj 
																	(trad_auto_id,
																	 trad_reference_number,
																	 trad_rr,
																	 trad_trade_date,
																	 trad_settle_date,
																	 trad_run_date,
																	 trad_advisor_code,
																	 trad_advisor_name,
																	 trad_account_name,
																	 trad_account_number,
																	 trad_symbol,
																	 trad_buy_sell,
																	 trad_quantity,
																	 trade_price,
																	 trad_commission,
																	 trad_cents_per_share,
																	 trad_adj_account_number,
																	 trad_adj_changed_by,
																	 trad_adj_comment,
																	 trad_adj_datetime,
																	 trad_is_cancelled
																	 ) 
																	VALUES (
																	'".$row_affected_row["trad_auto_id"]."', 
																	'".trim($row_affected_row["trad_reference_number"])."', 
																	'".$row_affected_row["trad_rr"]."', 
																	'".$row_affected_row["trad_trade_date"]."', 
																	'".$row_affected_row["trad_settle_date"]."', 
																	'".$row_affected_row["trad_run_date"]."', 
																	'".$row_affected_row["trad_advisor_code"]."', 
																	'".str_replace("'","",$row_affected_row["trad_advisor_name"])."', 
																	'".str_replace("'","",$row_affected_row["trad_account_name"])."', 
																	'".$row_affected_row["trad_account_number"]."', 
																	'".$row_affected_row["trad_symbol"]."', 
																	'".$row_affected_row["trad_buy_sell"]."', 
																	'".$row_affected_row["trad_quantity"]."', 
																	'".$row_affected_row["trade_price"]."', 
																	'".$row_affected_row["trad_commission"]."', 
																	'".$row_affected_row["trad_cents_per_share"]."', 
																	'".$new_acct."', 
																	'".$_POST["user_id"]."', 
																	'"."->no comments<-"."', 
																	now(), 
																	'0'
																	)";
			
			//figure out page load time 
			//$time=getmicrotime();
																					
			//xdebug("qry_insert_affected_row",$qry_insert_affected_row);
			$result_insert_affected_row = mysql_query($qry_insert_affected_row) or die(tdw_mysql_error($qry_insert_affected_row));
			
			//update this row in the mry table to is_cancelled = 2
			$qry_update_affected_row = "UPDATE mry_comm_rr_trades SET trad_is_cancelled = 2
																	WHERE trad_reference_number = '".trim($row_affected_row["trad_reference_number"])."' AND trad_is_cancelled = 0";
			//xdebug("qry_update_affected_row",$qry_update_affected_row);
			$result_update_affected_row = mysql_query($qry_update_affected_row) or die(tdw_mysql_error($qry_update_affected_row));	
			
			// @@@@@@@@@@@@@@@@@@@@ TEMPORARILY COMMENTED OUT FOR DEV TIME TESTING
			//update this row in the rep table to is_cancelled = 2
			$qry_update_affected_row_ = "UPDATE rep_comm_rr_trades SET trad_is_cancelled = 2
																	WHERE trad_reference_number = '".trim($row_affected_row["trad_reference_number"])."' AND trad_is_cancelled = 0";
			//xdebug("qry_update_affected_row",$qry_update_affected_row);
			$result_update_affected_row_ = mysql_query($qry_update_affected_row_) or die(tdw_mysql_error($qry_update_affected_row_));	
			//
			
			//insert a similar row in mry_comm_rr with the new account
			$qry_new_insert_affected_row = "INSERT INTO mry_comm_rr_trades 
																		(trad_reference_number,
																		 trad_rr,
																		 trad_trade_date,
																	   trad_settle_date,
																		 trad_run_date,
																		 trad_advisor_code,
																		 trad_advisor_name,
																		 trad_account_name,
																		 trad_account_number,
																		 trad_symbol,
																		 trad_buy_sell,
																		 trad_quantity,
																		 trade_price,
																		 trad_commission,
																		 trad_cents_per_share,
																		 trad_is_cancelled
																		 ) 
																		VALUES (
																		'".trim($row_affected_row["trad_reference_number"])."', 
																		'".$row_affected_row["trad_rr"]."', 
																		'".$row_affected_row["trad_trade_date"]."', 
																		'".$row_affected_row["trad_settle_date"]."', 
																		'".$row_affected_row["trad_run_date"]."', 
																		'".$row_affected_row["trad_advisor_code"]."', 
																		'".str_replace("'","",$row_affected_row["trad_advisor_name"])."', 
																		'".str_replace("'","",$row_affected_row["trad_account_name"])."', 
																		'".$new_acct."', 
																		'".$row_affected_row["trad_symbol"]."', 
																		'".$row_affected_row["trad_buy_sell"]."', 
																		'".$row_affected_row["trad_quantity"]."', 
																		'".$row_affected_row["trade_price"]."', 
																		'".$row_affected_row["trad_commission"]."', 
																		'".$row_affected_row["trad_cents_per_share"]."', 
																		'0'
																		)";
				//xdebug("qry_new_insert_affected_row",$qry_new_insert_affected_row);
				$result_new_insert_affected_row = mysql_query($qry_new_insert_affected_row) or die(tdw_mysql_error($qry_new_insert_affected_row));
			
	
				// @@@@@@@@@@@@@@@@@@@@ TEMPORARILY COMMENTED OUT FOR DEV TIME TESTING
				//insert a similar row in rep_comm_rr_trades with the new account
				$qry_new_insert_affected_row_ = "INSERT INTO rep_comm_rr_trades 
																		(trad_reference_number,
																		 trad_rr,
																		 trad_trade_date,
																	   trad_settle_date,
																		 trad_run_date,
																		 trad_advisor_code,
																		 trad_advisor_name,
																		 trad_account_name,
																		 trad_account_number,
																		 trad_symbol,
																		 trad_buy_sell,
																		 trad_quantity,
																		 trade_price,
																		 trad_commission,
																		 trad_cents_per_share,
																		 trad_is_cancelled
																		 ) 
																		VALUES (
																		'".trim($row_affected_row["trad_reference_number"])."', 
																		'".$row_affected_row["trad_rr"]."', 
																		'".$row_affected_row["trad_trade_date"]."', 
																		'".$row_affected_row["trad_settle_date"]."', 
																		'".$row_affected_row["trad_run_date"]."', 
																		'".$row_affected_row["trad_advisor_code"]."', 
																		'".str_replace("'","",$row_affected_row["trad_advisor_name"])."', 
																		'".str_replace("'","",$row_affected_row["trad_account_name"])."', 
																		'".$new_acct."', 
																		'".$row_affected_row["trad_symbol"]."', 
																		'".$row_affected_row["trad_buy_sell"]."', 
																		'".$row_affected_row["trad_quantity"]."', 
																		'".$row_affected_row["trade_price"]."', 
																		'".$row_affected_row["trad_commission"]."', 
																		'".$row_affected_row["trad_cents_per_share"]."', 
																		'0'
																		)";
				//xdebug("qry_new_insert_affected_row",$qry_new_insert_affected_row);
				$result_new_insert_affected_row_ = mysql_query($qry_new_insert_affected_row_) or die(tdw_mysql_error($qry_new_insert_affected_row_));

				//
				
				//echo " ". sprintf("%01.7f",((getmicrotime()-$time)/1000))." s."; 			
			}
			
			?>
			<br><br><br>&nbsp;&nbsp;&nbsp;<a class='adj_txt'>Adjustment Applied successfully.</a>
			<br><br>&nbsp;&nbsp;&nbsp;<a class="adj_txt" href="javascript:window.close();">>>CLOSE<<</a>
			
			<script language="javascript">
			//window.opener.document.clnt_activity.submit();
			</script>
			<?
			
			//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++	
	} elseif ($sel_vals_count == 4) { // clnt change
			$val_clnt_code = trim(strtoupper($new_clnt));
			
			echo "<a class='adj_txt'>Proceeding with Client Code changes.</a>";
	    //=============================================================
	    //rep change process
			//non existent rep
			
			//check for valid code in client table, not in mry_comm_rr_trades
			/*$qry_rep_exists = "SELECT trad_advisor_code
												FROM mry_comm_rr_trades
												WHERE trad_advisor_code = '".$val_clnt_code."'
												LIMIT 0 , 10";*/
			$qry_clnt_exists = "SELECT clnt_code as single_val FROM int_clnt_clients where clnt_code = '".$val_clnt_code."'";
			$result_clnt_exists = mysql_query($qry_clnt_exists) or die(tdw_mysql_error($qry_clnt_exists));
			$count_clnt = mysql_num_rows($result_clnt_exists);
			if ($count_clnt == 0) {
			echo "<a class='adj_txt'>Client Code is invalid. Please start again.</a>";
			exit;
			}
			
				//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
				//Get relevant information for the client code.
				$qry_get_clnt_detl = "SELECT nadd_full_account_number, nadd_advisor, nadd_short_name, nadd_rr_owning_rep, nadd_address_line_1
															FROM `mry_nfs_nadd` 
															WHERE `nadd_advisor` = '".$val_clnt_code."'
															LIMIT 1";
				$result_get_clnt_detl = mysql_query($qry_get_clnt_detl) or die(tdw_mysql_error($qry_get_clnt_detl));
				while($row_get_clnt_detl = mysql_fetch_array($result_get_clnt_detl))
				{
						$val_new_acct_num  = $row_get_clnt_detl['nadd_full_account_number'];
						$val_new_acct_name = $row_get_clnt_detl['nadd_short_name'];
						$val_new_rep =      $row_get_clnt_detl['nadd_rr_owning_rep'];
						$val_clnt_name =    db_single_val("select clnt_name as single_val from int_clnt_clients where trim(clnt_code) = '".$val_clnt_code."'"); //$row_get_clnt_detl['nadd_address_line_1'];
						
				}
				//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
			
			//xdebug("trade_ref",$_POST["trade_ref"]);
			//get affected rows
			if ($adj_type_code == 'SREF') {  
				//xdebug("trade_ref",$trade_ref);
				$qry_affected_trades = "SELECT * FROM mry_comm_rr_trades 
																WHERE trad_reference_number = '".$_POST["trade_ref"]."' AND trad_is_cancelled = 0";
			} else {
				// new_rep, adj_type_code, trade_date, adv_code, old_rep, symbol, buysell, trade_ref
				$qry_affected_trades = "SELECT * FROM mry_comm_rr_trades 
																WHERE 
																trad_rr = '".$old_rep."'
																AND trad_trade_date = '".$trade_date."'
																AND trad_advisor_code = '".$adv_code."'
																AND trad_symbol = '".$symbol."'
																AND trad_buy_sell = '".$buysell."' AND trad_is_cancelled = 0";
			}
			
			//xdebug("qry_affected_trades",$qry_affected_trades);
			$result_affected_row = mysql_query($qry_affected_trades) or die(tdw_mysql_error($qry_affected_trades));
			while($row_affected_row = mysql_fetch_array($result_affected_row))
			{
			
			//insert affected rows into adj table
			$qry_insert_affected_row = "INSERT INTO rep_comm_rr_trades_adj 
																	(trad_auto_id,
																	 trad_reference_number,
																	 trad_rr,
																	 trad_trade_date,
																	 trad_settle_date,
																	 trad_run_date,
																	 trad_advisor_code,
																	 trad_advisor_name,
																	 trad_account_name,
																	 trad_account_number,
																	 trad_symbol,
																	 trad_buy_sell,
																	 trad_quantity,
																	 trade_price,
																	 trad_commission,
																	 trad_cents_per_share,
																	 trad_adj_rr,
																	 trad_adj_changed_by,
																	 trad_adj_comment,
																	 trad_adj_datetime,
																	 trad_is_cancelled
																	 ) 
																	VALUES (
																	'".$row_affected_row["trad_auto_id"]."', 
																	'".trim($row_affected_row["trad_reference_number"])."', 
																	'".$row_affected_row["trad_rr"]."', 
																	'".$row_affected_row["trad_trade_date"]."', 
																	'".$row_affected_row["trad_settle_date"]."', 
																	'".$row_affected_row["trad_run_date"]."', 
																	'".$row_affected_row["trad_advisor_code"]."', 
																	'".str_replace("'","",$row_affected_row["trad_advisor_name"])."', 
																	'".str_replace("'","",$row_affected_row["trad_account_name"])."', 
																	'".$row_affected_row["trad_account_number"]."', 
																	'".$row_affected_row["trad_symbol"]."', 
																	'".$row_affected_row["trad_buy_sell"]."', 
																	'".$row_affected_row["trad_quantity"]."', 
																	'".$row_affected_row["trade_price"]."', 
																	'".$row_affected_row["trad_commission"]."', 
																	'".$row_affected_row["trad_cents_per_share"]."', 
																	'".$new_rep."', 
																	'".$_POST["user_id"]."', 
																	'"."->no comments<-"."', 
																	now(), 
																	'0'
																	)";
			
			//figure out page load time 
			//$time=getmicrotime();
																					
			//xdebug("qry_insert_affected_row",$qry_insert_affected_row);
			$result_insert_affected_row = mysql_query($qry_insert_affected_row) or die(tdw_mysql_error($qry_insert_affected_row));
			
			//update this row in the mry table to is_cancelled = 2
			$qry_update_affected_row = "UPDATE mry_comm_rr_trades SET trad_is_cancelled = 2
																	WHERE trad_reference_number = '".trim($row_affected_row["trad_reference_number"])."' AND trad_is_cancelled = 0";
			//xdebug("qry_update_affected_row",$qry_update_affected_row);
			$result_update_affected_row = mysql_query($qry_update_affected_row) or die(tdw_mysql_error($qry_update_affected_row));	
			
			//update this row in the rep table to is_cancelled = 2
			$qry_update_affected_row_ = "UPDATE rep_comm_rr_trades SET trad_is_cancelled = 2
																	WHERE trad_reference_number = '".trim($row_affected_row["trad_reference_number"])."' AND trad_is_cancelled = 0";
			//xdebug("qry_update_affected_row",$qry_update_affected_row);
			$result_update_affected_row_ = mysql_query($qry_update_affected_row_) or die(tdw_mysql_error($qry_update_affected_row_));	
			
			//insert a similar row in mry_comm_rr with the new rr   >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
			$qry_new_insert_affected_row = "INSERT INTO mry_comm_rr_trades 
																		(trad_reference_number,
																		 trad_rr,
																		 trad_trade_date,
																		 trad_settle_date,
																		 trad_run_date,
																		 trad_advisor_code,
																		 trad_advisor_name,
																		 trad_account_name,
																		 trad_account_number,
																		 trad_symbol,
																		 trad_buy_sell,
																		 trad_quantity,
																		 trade_price,
																		 trad_commission,
																		 trad_cents_per_share,
																		 trad_is_cancelled
																		 ) 
																		VALUES (
																		'".trim($row_affected_row["trad_reference_number"])."', 
																	  '".trim($val_new_rep)."', 
																		'".$row_affected_row["trad_trade_date"]."', 
																		'".$row_affected_row["trad_settle_date"]."',
																		'".$row_affected_row["trad_run_date"]."', 
																		'".$val_clnt_code."', 
																		'".str_replace("'","",$val_clnt_name)."', 
																		'".'ADJ-'.trim($val_new_acct_name)."', 
																		'".trim($val_new_acct_num)."', 
																		'".$row_affected_row["trad_symbol"]."', 
																		'".$row_affected_row["trad_buy_sell"]."', 
																		'".$row_affected_row["trad_quantity"]."', 
																		'".$row_affected_row["trade_price"]."', 
																		'".$row_affected_row["trad_commission"]."', 
																		'".$row_affected_row["trad_cents_per_share"]."', 
																		'0'
																		)";
				//xdebug("qry_new_insert_affected_row",$qry_new_insert_affected_row);
				$result_new_insert_affected_row = mysql_query($qry_new_insert_affected_row) or die(tdw_mysql_error($qry_new_insert_affected_row));
			
			//insert a similar row in rep_comm_rr_trades with the new rr
			$qry_new_insert_affected_row_ = "INSERT INTO rep_comm_rr_trades 
																		(trad_reference_number,
																		 trad_rr,
																		 trad_trade_date,
																		 trad_settle_date,
																		 trad_run_date,
																		 trad_advisor_code,
																		 trad_advisor_name,
																		 trad_account_name,
																		 trad_account_number,
																		 trad_symbol,
																		 trad_buy_sell,
																		 trad_quantity,
																		 trade_price,
																		 trad_commission,
																		 trad_cents_per_share,
																		 trad_is_cancelled
																		 ) 
																		VALUES (
																		'".trim($row_affected_row["trad_reference_number"])."', 
																	  '".trim($val_new_rep)."', 
																		'".$row_affected_row["trad_trade_date"]."', 
																		'".$row_affected_row["trad_settle_date"]."',
																		'".$row_affected_row["trad_run_date"]."', 
																		'".$val_clnt_code."', 
																		'".str_replace("'","",$val_clnt_name)."', 
																		'".'ADJ-'.trim($val_new_acct_name)."', 
																		'".trim($val_new_acct_num)."', 
																		'".$row_affected_row["trad_symbol"]."', 
																		'".$row_affected_row["trad_buy_sell"]."', 
																		'".$row_affected_row["trad_quantity"]."', 
																		'".$row_affected_row["trade_price"]."', 
																		'".$row_affected_row["trad_commission"]."', 
																		'".$row_affected_row["trad_cents_per_share"]."', 
																		'0'
																		)";
				//xdebug("qry_new_insert_affected_row",$qry_new_insert_affected_row);
				$result_new_insert_affected_row_ = mysql_query($qry_new_insert_affected_row_) or die(tdw_mysql_error($qry_new_insert_affected_row_));
			
				//echo " ". sprintf("%01.7f",((getmicrotime()-$time)/1000))." s."; 			
			}
			
			?>
			<br><br><br>&nbsp;&nbsp;&nbsp;<a class='adj_txt'>Adjustment Applied successfully.</a>
			<br><br>&nbsp;&nbsp;&nbsp;<a class="adj_txt" href="javascript:window.close();">>>CLOSE<<</a>
			
			<script language="javascript">
			//window.opener.document.clnt_activity.submit();
			</script>
			<?
			//=============================================================
	} else {
			echo "<a class='adj_txt'>Cannot handle your input. You probably entered more than one item changes. Please start over.</a>";
			exit;
	}
	
	exit;

} else {
?>
  <form action="<?=$PHP_SELF?>" method="post">
   <table width="100%" border="0" cellpadding="4" cellspacing="5" bgcolor="#FFFFFF">
     <tr>
       <td class="adj_lbl" nowrap="nowrap"> Adjustment Type: </td>
       <td class="adj_txt">&nbsp;&nbsp;<?=$adj_type?></td>
     </tr>
     <tr>
       <td class="adj_lbl" nowrap="nowrap"> Trade Date: </td>
       <td class="adj_txt">&nbsp;&nbsp;<?=$vals_param[2]?></td>
     </tr>
     <tr>
       <td class="adj_lbl" nowrap="nowrap"> Client Code: </td>
       <td class="adj_txt">&nbsp;&nbsp;<?=$vals_param[3]?></td>
     </tr>
     <tr>
       <td colspan="2" class="adj_lbl_note" nowrap="nowrap"> Please make changes to ONLY ONE of the three items below. </td>
     </tr>
     <tr bgcolor="#CCCCCC">
       <td class="adj_lbl"> RR #: </td>
       <td class="adj_txt">&nbsp;&nbsp;<?=$vals_param[4]?>&nbsp;&nbsp;To:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="new_rep" type="text" size="12" maxlength="12"></td>
     </tr>
<?
if ($trad_acct_num) {
?>
     <tr bgcolor="#CCCCCC">
       <td class="adj_lbl" nowrap="nowrap"> Acct #: </td>
       <td class="adj_txt">&nbsp;&nbsp;<?=$trad_acct_num?>&nbsp;&nbsp;To:&nbsp;&nbsp;<input name="new_acct" type="text" size="12" maxlength="12"></td>
     </tr>
<?
}
?>
     <tr bgcolor="#CCCCCC">
       <td class="adj_lbl"> Client Code: </td>
       <td class="adj_txt">&nbsp;&nbsp;<?=$vals_param[3]?>&nbsp;&nbsp;To:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="new_clnt" type="text" size="12" maxlength="12"></td>
     </tr>
     <tr>
       <td class="adj_lbl" nowrap="nowrap"> Symbol: </td>
       <td class="adj_txt">&nbsp;&nbsp;<?=$vals_param[5]?></td>
     </tr>
     <tr>
       <td class="adj_lbl" nowrap="nowrap"> Buy/Sell: </td>
       <td class="adj_txt">&nbsp;&nbsp;<?=$vals_param[6]?></td>
     </tr>
     <tr>
       <td colspan="2" align="center"><input type="hidden" name="adj_type_code" value="<?=$vals_param[1]?>">
					<input type="hidden" name="trade_date" value="<?=$vals_param[2]?>">
					<input type="hidden" name="adv_code" value="<?=$vals_param[3]?>">
					<input type="hidden" name="old_rep" value="<?=$vals_param[4]?>">
					<input type="hidden" name="symbol" value="<?=$vals_param[5]?>">
					<input type="hidden" name="buysell" value="<?=$vals_param[6]?>">
					<input type="hidden" name="trade_ref" value="<?=$trade_ref?>">
					<input type="hidden" name="user_id" value="<?=$user_id?>">
					<input name="proceed" type="submit" class="Submit" value="Apply Adjustment">
			 </td>
     </tr>
   </table>
 </form>
<?
}

tep();
echo "</center>";
?>
</body>
</html>