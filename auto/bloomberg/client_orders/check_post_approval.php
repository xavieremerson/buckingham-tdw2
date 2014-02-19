<?
include('../../includes/dbconnect.php');
include('../../includes/functions.php'); 
include('../../includes/global.php'); 

include('client_rep_functions.php');

//============================================================================================
//============================================================================================
//First get all approved requests
$arr_emp_approved_list = array();
$arr_emp_approved_time = array();
$qry = "select auto_id, etpa_symbol, etpa_approval_time 
				from etpa_request
				where etpa_approval_time > '".date('Y-m-d')."'
				AND etpa_is_approved = 1
				AND etpa_is_routed = 0";
//ydebug("qry",$qry);
$result = mysql_query($qry) or die(tdw_mysql_error($qry));
while ($row = mysql_fetch_array($result)) {
	$arr_emp_approved_list[$row["auto_id"]] = $row["etpa_symbol"];
	$arr_emp_approved_time[$row["auto_id"]] = $row["etpa_approval_time"];
}
print_r($arr_emp_approved_list);
print_r($arr_emp_approved_time);
//============================================================================================
//============================================================================================
//get all client orders (distinct symbols) and then check for it.
$arr_client_symbols = array();
$arr_client_symbols_time = array();
//$qry = "select distinct(Ticker) as Ticker, min(manual_time) as manual_time from tradeware_reports_raw group by Ticker"; 
$qry = "select distinct(Ticker) as Ticker, min(Timez) as manual_time from client_orders_street group by Ticker"; 
$result = mysql_query($qry) or die(tdw_mysql_error($qry));
while ($row = mysql_fetch_array($result)) {
	$arr_client_symbols[] = $row["Ticker"];
	$arr_client_symbols_time[$row["Ticker"]] = date("H:i:s",strtotime($row["manual_time"]));
}
//print_r($arr_client_symbols_time);
//============================================================================================
//============================================================================================
//Find matches
$arr_matched = array();
foreach ($arr_emp_approved_list as $k=>$v) {
	if (in_array($v,$arr_client_symbols)) {
	  echo $arr_emp_approved_time[$k]."/".
		     strtotime($arr_emp_approved_time[$k]).
				 "/".
				 date('H:i:sa',strtotime($arr_emp_approved_time[$k])).
				 "   ".
				 $arr_client_symbols_time[$v].
				 "/". 
				 strtotime($arr_client_symbols_time[$v]).
				 "/".
				 date('H:i:sa',strtotime($arr_client_symbols_time[$v])).
				 "\n";
		 echo strtotime($arr_emp_approved_time[$k])."\n";
		 echo strtotime($arr_client_symbols_time[$v])."\n";
		//LOGIC HERE NEEDS TO BE CONFIRMED.....
		
		//properly convert time
		
		//mktime ([ int $hour = date("H") [, int $minute = date("i") [, int $second = date("s") [, int $month = date("n") [, int $day = date("j") [, int $year = date("Y") [, int $is_dst = -1 ]]]]]]] )
		$arr_z_client_time = explode(":", $arr_client_symbols_time[$v]);
		
		
		$z_client_time = mktime ($arr_z_client_time[0], $arr_z_client_time[1] , $arr_z_client_time[2] , date("n"), date("j"), date("Y"));
		ydebug("z_client_time",$z_client_time);
		echo date('H:i:sa',$z_client_time);
		
		if (strtotime($arr_emp_approved_time[$k]) > strtotime($arr_client_symbols_time[$v]) && (strtotime($arr_emp_approved_time[$k]) - strtotime($arr_client_symbols_time[$v])) < 3600 ) {
			$arr_matched[] = $k;
		}
	}
}
echo "Matched data\n";
print_r($arr_matched);
//exit;
//============================================================================================
//============================================================================================
//Create Email to send out.

if (count($arr_matched) > 0) {
  //echo "Email will be sent...";
  $arr_tradeware_buy_sell = array('1'=>'Buy',
																	'2'=>'Buy minus',
																	'3'=>'Buy cover',
																	'4'=>'Sell',
																	'5'=>'Sell plus',
																	'6'=>'Sell short',
																	'7'=>'Sell short exempt');

	$email_log = '
		<table width="100%" border="0" cellspacing="0" cellpadding="10">
			<tr> 
				<td valign="top">
					<a class="bodytext12"><strong>Details of Employee Request and Client Order</strong></a>
					<hr>';

	foreach($arr_matched as $k=>$v) {
	
	
		//update is_routed 
		
		
		$result = mysql_query("update etpa_request set etpa_is_routed = 1 where auto_id = ".$v) or die(mysql_error());
		
		
		$qry = "select 
							auto_id,
							etpa_requestor,
							etpa_instrument,
							etpa_side,
							etpa_symbol,
							etpa_qty,
							etpa_order_type,
							etpa_sp_instruction,
							etpa_limit_price,
							etpa_entry_time,
							etpa_request_time,
							etpa_is_saved,
							etpa_is_submitted,
							etpa_is_approved,
							etpa_approver,
							date_format(etpa_approval_time,'%h:%i:%s %p') as etpa_approval_time,
							etpa_approver_comment,
							etpa_is_routed,
							etpa_isactive
						FROM etpa_request
						WHERE auto_id = '".$v."'"; 
		$result = mysql_query($qry) or die(tdw_mysql_error($qry));
		while ($row = mysql_fetch_array($result)) {
		
			$email_log .= '<br><a class="bodytext12">'.
												"Employee: <strong>".get_user_by_id($row["etpa_requestor"])."</strong><br>
												Buy/Sell: <strong>".$row["etpa_side"]."</strong><br>
												Quantity: <strong>".$row["etpa_qty"]."</strong><br>
												Symbol: <strong>".$row["etpa_symbol"]."</strong><br>
												Approver: <strong>".get_user_by_id($row["etpa_approver"])."</strong><br>
												Approval Time: <strong>".$row["etpa_approval_time"].'</strong><br><br>';

			//Now getting client order details.
			$qry_c = "select
									Ticker,
									Quantity,
									fill_price,
									buy_sell,
									customer_id,
									max(manual_time) as manual_time,
									parent_id
								FROM tradeware_reports_raw 
								WHERE Ticker = '".$row["etpa_symbol"]."'
								GROUP BY Ticker";
			$result_c = mysql_query($qry_c) or die(tdw_mysql_error($qry_c));
			while ($row_c = mysql_fetch_array($result_c)) {
			
			//Get some client details
			//Client Name
			$c_client_name = db_single_val("select max(trim(clnt_name)) as single_val from int_clnt_clients where clnt_alt_code = trim('".$row_c["customer_id"]."')");
			if ($c_client_name == "") { $c_client_name_string = "";} else {$c_client_name_string = "(".$c_client_name.")";}

			//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			//ADDED THIS 2009-10-13
			if ($c_client_name == "") {
				// If alt_code is not found insert alt code based on first 4 letters of the client code
				$zzqry = "update int_clnt_clients set clnt_alt_code = '".$row_c["customer_id"]. "' where clnt_code = '".substr($row_c["customer_id"],0,4)."'";
				$result = mysql_query($zzqry);
				$c_client_name = db_single_val("select max(trim(clnt_name)) as single_val from int_clnt_clients where clnt_alt_code = trim('".$row_c["customer_id"]."')");
				if ($c_client_name == "") { $c_client_name_string = "";} else {$c_client_name_string = "(".$c_client_name.")";}
				//xdebug("zzqry",$zzqry);
	
				$qry_update_record_history = "insert into int_clnt_clients_tradeware values (NULL, 'Updated to value ".$row_c["customer_id"]."',now())";
				//xdebug("qry_update_record_history",$qry_update_record_history);
				$result_update_record_history = mysql_query($qry_update_record_history);			
			}
			//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			
			//Client Rep
			$c_client_rep = db_single_val("select concat(clnt_rr1,'^',clnt_rr2) as single_val from int_clnt_clients where clnt_alt_code = trim('".$row_c["customer_id"]."')");
			ydebug("c_client_rep",$c_client_rep);
			
			$arr_c_reps = explode("^",$c_client_rep);
			print_r($arr_c_reps);
			
			if (trim($arr_c_reps[1])=="") { //single

				$c_rep_num = get_rr_num (get_userid_for_initials ($arr_c_reps[0]) );
				$c_rep_name = db_single_val("select Fullname as single_val from users where ID = '".get_userid_for_initials ($arr_c_reps[0])."'");
	
				$c_str_sales_rep = $c_rep_name . " [".$c_rep_num."]";
	
				ydebug("c_str_sales_rep",$c_str_sales_rep);

			} else { //shared
			
				ydebug("arr_c_reps[0]",$arr_c_reps[0]);
				ydebug("arr_c_reps[1]",$arr_c_reps[1]);

				$c_shared_rep_rep_num = get_shared_rr_num ($arr_c_reps[0], $arr_c_reps[1]);
				
				$c_rep_0 = db_single_val("select Fullname as single_val from users where ID = '".get_userid_for_initials ($arr_c_reps[0])."'");
				$c_rep_1 = db_single_val("select Fullname as single_val from users where ID = '".get_userid_for_initials ($arr_c_reps[1])."'");
				
				$c_str_sales_rep = $c_rep_0." / ".$c_rep_1;
				ydebug("c_str_sales_rep",$c_str_sales_rep);
				
			}
			
			$email_log .= '<br><a class="bodytext12">'.
												"Client ID: <strong>".$row_c["customer_id"]." ".$c_client_name_string."</strong><br>
												RR: <strong>".$c_str_sales_rep."</strong><br>
												Buy/Sell: <strong>".$arr_tradeware_buy_sell[$row_c["buy_sell"]]."</strong><br>
												Time: <strong>".date('H:i:sa',strtotime($row_c["manual_time"]))."</strong><br>
												Transaction ID: <strong>".$row_c["parent_id"].'</strong><br><br><hr><br><br>';
			}
		}
						
					
	}					
					

	$email_log .= '
					<p>&nbsp;</p>
					<p>&nbsp;</p>
			</tr>
		</table>';

   		//echo $email_log; 

		//create mail to send
		$html_body = "";
		$html_body .= zSysMailHeader("");
		$html_body .= $email_log;
		$html_body .= zSysMailFooter ();
		
		$subject = "Client Order / Approved Preapproval Request.";
		$text_body = $subject;
		
		zSysMailer('pprasad@centersys.com', "", $subject, $html_body, $text_body, "") ;
		echo "Email sent to Pravin...";
		//zSysMailer('TSutera@BuckResearch.com', "", $subject, $html_body, $text_body, "") ;
    //zSysMailer("compliance@buckresearch.com", "BRG Compliance", $subject, $html_body, $text_body, "") ;
    	
		//zSysMailer('lkarp@buckresearch.com', "", $subject, $html_body, $text_body, "") ;
		//zSysMailer('jperno@buckresearch.com', "", $subject, $html_body, $text_body, "") ;
		//zSysMailer('rdaniels@buckresearch.com', "", $subject, $html_body, $text_body, "") ;
		//zSysMailer('ehogenboom@BuckResearch.com', "", $subject, $html_body, $text_body, "") ;
  	echo "Emails sent out...";
}



//============================================================================================
//============================================================================================
//Update the etpa_is_routed


//============================================================================================
//============================================================================================
?>