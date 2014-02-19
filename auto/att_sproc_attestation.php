<?
	ini_set("memory_limit","256M");
	
  include('../includes/dbconnect.php');
  include('../includes/global.php');
	include('../includes/functions.php');


////SECTION: DO NOT RUN ON WEEKENDS OR HOLIDAYS
//====================================================================================================
//  NEEDS TO RUN ONLY ON WEEKDAYS AND NON-HOLIDAYS, THIS IS TO CHECK THAT CONDITION
	$str_date = date('Y-m-d');
	echo $str_date."<br>";
	if (
	    check_holiday ($str_date)==1 
	    or date('D',strtotime($str_date))=='Sat' 
			or date('D',strtotime($str_date))=='Sun'
		 ) {
		echo "Today is a holiday or weekend, hence terminating program execution!<br>";
		exit;
	} else {
		echo "Today is not a holiday or weekend, hence proceeding with program execution!<br>";
	}
  echo "Proceeding after holiday/weekend check....<br>";
//====================================================================================================

////SECTION: WHAT DAY TO PROCESS? 
//Previous Business Day should be applied here.
$trade_date_to_process = previous_business_day();
//$trade_date_to_process = '2011-01-05';
xdebug("trade_date_to_process",$trade_date_to_process); 

//// MRI Detection
//
$arr_mri["AXL"] = 'U';
$arr_mri["F"] = 'D';
show_array($arr_mri);

//// Find if there is a trade in the same side as the MRI
// 
$arr_trade_info = array("B^1000^AXL", "S^1000000^F");

//// Loop through trade items to send attestation
foreach($arr_trade_info as $k=>$v) {
	
	//trade details
	$arr_trade_item = explode("^",$v);
	show_array($arr_trade_item);
	
	//make an entry in attestation table
	
	$max_id_use = db_single_val("select max(auto_id)+1 as single_val from att_attestation");
	if ($max_id_use == "") {$max_id_use = 1;};
	
	
	$str_qry_a = "INSERT INTO att_attestation 
									(auto_id, 
										att_original_trade_id, 
										att_symbol, 
										att_mri_direction, 
										att_notes, 
										arr_isclosed, 
										arr_isactive
									) VALUES 
									(
										".$max_id_use.", 
										'123', 
										'".$arr_trade_item[2]."', 
										'".$arr_mri[$arr_trade_item[2]]."', 
										NULL, 
										'0', 
										'1'
									)";
	$result_a = mysql_query($str_qry_a) or die (tdw_mysql_error($str_qry_a));
	
	
	
	//***************************************************************************************
	//***************************************************************************************

	//// Get Portfolio Mgr., Analyst and Associate
	$val_pmgr = array("pprasad@centersys.com",79);
	$val_analyst = array("pprasad@centersys.com",79);
	$val_associate = array("pprasad@centersys.com",79);
	
	$arr_recipient = array($val_pmgr,$val_analyst,$val_associate);
	show_array($arr_recipient);
	
	foreach ($arr_recipient as $key => $val) {
	
		$qry_b = "INSERT INTO att_attestation_persons (
								att_id ,	person_id ,	att_email_sent ,	att_email_date_time ,	att_isclosed ,	att_isactive )
							VALUES ('".$max_id_use."', '".$val[1]."', '1', NOW( ) , '0', '1')";
		$result_b = mysql_query($qry_b) or die (tdw_mysql_error($qry_b));
		
		// Make entry in the attestation log table, which will be used in monitoring.
		$unique_email_md5 = md5(rand(1,999999999999));
		
		xdebug("unique_email_md5",$unique_email_md5);
		
		$link = "";
		$link = $_site_url."att_attestation_response.php?aid=".$max_id_use."&uid=".$val[1]."&mid=".$unique_email_md5;
		
		$email_log = '
							<table width="100%" border="0" cellspacing="0" cellpadding="10">
								<tr> 
									<td valign="top">
										<p><a class="bodytext12"><strong>Please click <a href="'.$link.'">>> HERE <<</a> to continue with the attestation process.</strong></a></p>			
										<p>&nbsp;</p>
										<p>&nbsp;</p>
										<p><a class="bodytext12"><strong>TDW Administrator</strong></a></p></td>
								</tr>
							</table>
								';
		//create mail to send
		$html_body = "";
		$html_body .= zSysMailHeader("");
		$html_body .= $email_log;
		$html_body .= zSysMailFooter ();
		
		$subject = "Attestation Requirement : ".format_date_ymd_to_mdy;
		$text_body = $subject;
		
		zSysMailer($val[0], "", $subject, $html_body, $text_body, "") ;
		echo $link . "<br>";
	}

	//***************************************************************************************
	//***************************************************************************************
}

exit;

?>