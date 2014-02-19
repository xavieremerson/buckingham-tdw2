<?
  include('includes/dbconnect.php');
  include('includes/global.php');
	include('includes/functions.php');

//show_array($_GET);
//exit;

function extract_client ($str) {
	return substr($str,strpos($str,"[")+1,4); //Code is always 4 characters long.
}

$payment_type = array();

$payment_type[1] = "Research - Research";
$payment_type[2] = "Research - Independent";
$payment_type[3] = "Research - Geneva";
$payment_type[4] = "Broker-to-Broker";
$payment_type[5] = "Trading 2";
$payment_type[6] = "Other";

//Create Lookup Array of Client Code / Client Name
$qry_clients = "select clnt_code,
                       clnt_name,
											 trim(clnt_rr1) as clnt_rr1,
											 trim(clnt_rr2) as clnt_rr2
								from int_clnt_clients";
$result_clients = mysql_query($qry_clients) or die (tdw_mysql_error($qry_clients));
$arr_clients = array();
$arr_client_rrs = array();
while ( $row_clients = mysql_fetch_array($result_clients) ) 
{
	$arr_clients[$row_clients["clnt_code"]] = $row_clients["clnt_name"];
	$arr_client_rrs[$row_clients["clnt_code"]] = $row_clients["clnt_rr1"]."##".$row_clients["clnt_rr2"];
}

////
//function get user_id from rr_num
function get_userid_for_rr ($rr_num) {
	$user_id = db_single_val("SELECT ID as single_val FROM users WHERE rr_num = '".$rr_num."'");   
	return $user_id;
}

//function get user_id from Initials
function get_userid_for_initials ($Initials) {
	$user_id = db_single_val("SELECT ID as single_val FROM users WHERE Initials = '".$Initials."'");   
	return $user_id;
}

//function get sole rr_num from ID
function get_rr_num ($ID) {
	$rr_num = db_single_val("SELECT rr_num as single_val FROM users WHERE ID = '".$ID."'");   
	return $rr_num;
}

//get rr_num and initials for client
//function corrected, was giving wrong output
function get_rep_for_client ($arr_client_rrs, $client_code) {
  //$initial_a, $initial_b
	$arr_initials = explode('##',	$arr_client_rrs[$client_code]);
	$initial_a = $arr_initials[0];
	$initial_b = $arr_initials[1];
	
	if (strlen($initial_b) > 1 and strlen($initial_a) > 1) { //we are talking about shared reps.
	    //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			$userid_a = get_userid_for_initials($initial_a);
			$userid_b = get_userid_for_initials($initial_b);
			//xdebug("initials/userid_a",$initial_a."/".$userid_a);
			//xdebug("initials/userid_b",$initial_b."/".$userid_b);
			$qry_shared_rr_num = "SELECT trim(srep_rrnum) as srep_rrnum 
														FROM sls_sales_reps
														WHERE srep_user_id ='".$userid_a."'
														AND	srep_isactive = 1 
														AND srep_rrnum
														IN (
														SELECT trim(srep_rrnum) 
														FROM sls_sales_reps
														WHERE 
															srep_isactive = 1 
															AND srep_user_id ='".$userid_b."')";   
			//xdebug("qry_shared_rr_num",$qry_shared_rr_num);
			$result_shared_rr_num = mysql_query($qry_shared_rr_num) or die(tdw_mysql_error($qry_shared_rr_num));
			while($row_shared_rr_num = mysql_fetch_array($result_shared_rr_num)) {
				$shared_rr_num = $row_shared_rr_num["srep_rrnum"];
			}
			return $shared_rr_num;
	    //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	} elseif (strlen($initial_b) == 0 and strlen($initial_a) > 1) {
	    //===============================================================================================
			$prim_rr_num = get_rr_num (get_userid_for_initials ($initial_a));
			return $prim_rr_num;
	    //===============================================================================================
	} else {
	    return "";
	}
}



function show_numbers_pdf($numval) {
		if ($numval == 0) {
			return '<font color="888888">0</font>';
		} else {
			return number_format($numval,0,'.',",");
		}
}	

		$data_to_html_file = "";

		$data_to_html_file .= '
			<style type="text/css">
			<!--
			.data_black {font-family: "Courier New", Courier, mono;	font-size: 10px;	color: #000000;}
			tr.trlight {
				font-family: Arial;
				font-size: 11px;
				color: #000000;
			}
			tr.trdark {
				font-family: Arial;
				font-size: 11px;
				color: #000000;
			}
			-->
			</style>
			<table width="670" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td valign="top" width="50"><img src="../../images/logo_small.gif" width="47"></td>
					<td valign="top" align="left" width="500">
						<font color="#333333" size="3" face="Arial"><b>&nbsp;Report: CHECKS</b></font>
						<br>
						<font color="#333333" size="2" face="Arial">
							&nbsp;From : '.$datefrom.' to '.$dateto.'
						</font>
					</td>
					<td>&nbsp;</td>
				</tr>
			</table>
			<img src="../../images/border_a.png" width="670" height="2">';
			
			
		$data_to_html_file .= '<br>
													<table width="670" cellpadding="1", cellspacing="0" bgcolor="#EEEEEE" border="0">
														<tr>
															<td valign="top">		
																<table width="100%"  border="0" cellspacing="1" cellpadding="0">';
		
		     $page_header  = '<tr> 
														<td width="80">&nbsp;<font face="Arial" size="1"><b>Date</b></font></td>
														<td width="80" align="right"><font face="Arial" size="1"><b>Amount</b></font>&nbsp;</td>
														<td width="200" align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font face="Arial" size="1"><b>Type</b></font></td>
														<td width="60"><font face="Arial" size="1"><b>Client</b></font></td>
														<td width="160"><font face="Arial" size="1"><b>Client Name</b></font></td>
														<td width="80"><font face="Arial" size="1"><b>Rep.</b></font></td>
														<td width="120"><font face="Arial" size="1"><b>Rep. #</b></font></td>
													</tr>';
		
		$data_to_html_file .= $page_header;

if ($_GET) {

	if ($chk_type == 'ALL') {
		$str_append = " AND chek_type like '%' ";
	} else {
		$str_append = " AND chek_type = '".$chk_type."' ";
	}

	if($rep == '^ALL^') {
		$str_append_rep = " AND chek_reps_and like '%' ";
	} else {
		$arr_repinfo = split('\^',$rep);
		$rep_id = $arr_repinfo[1];
		$rep_initials = db_single_val("select Initials as single_val from users where ID = '".$rep_id."'");
		$str_append_rep = " AND chek_reps_and like '%".$rep_initials."%' ";
	}

	if ($clnt == 'Enter Client' or $clnt == '') {
		$str_append_client = " AND chek_advisor like '%' ";
	} else {
		$str_append_client = " AND chek_advisor = '". extract_client($clnt) ."' ";
	}

	//xdebug('datefilterval',$datefilterval);
	$date_from = format_date_mdy_to_ymd($datefrom);
	$date_to = format_date_mdy_to_ymd($dateto);

	$qry_get_checks = "SELECT auto_id,
											chek_amount,
											chek_type,
											chek_advisor,
											chek_comments,
											chek_date,
											chek_reps_and,
											chek_reps_or,
											chek_reps_special,
											chek_entered_by,
											chek_entered_datetime,
											chek_edited_by,
											chek_edited_datetime,
											chek_processed,
											chek_isactive
										FROM chk_chek_payments_etc
										WHERE chek_date between '".$date_from."' AND '".$date_to."'
										AND chek_isactive = 1 ".$str_append . $str_append_rep . $str_append_client ."
										ORDER BY chek_advisor";
	
/*	print_r($_GET);
	echo $qry_get_checks;
	exit;
*/													
	$result_get_checks = mysql_query($qry_get_checks) or die (tdw_mysql_error($qry_get_checks));
	$level_a_count = 0;
  $level_b_count = 0;
	$total_checks  = 0;
	while ( $row_get_checks = mysql_fetch_array($result_get_checks) ){

  //+++++++++++++++++++++++++++++++++++++++++++++++
	if ($level_a_count % 2) { 
			$class_row = "trdark";
			$bgcolor = "#F2F2F2"; 
	} else { 
			$class_row = "trlight";
			$bgcolor = "#FFFFFF";
	} 
	$data_to_html_file .= '
												<tr class ="'.$class_row.'" bgcolor="'.$bgcolor.'" >
													<td align="left">&nbsp;<font face="Arial" size="1">'.format_date_ymd_to_mdy($row_get_checks["chek_date"]).'</font></td>
													<td align="right"><font face="Arial" size="1">'.number_format($row_get_checks["chek_amount"],2,'.',',').'</font>&nbsp;</td>
													<td align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font face="Arial" size="1">'.$payment_type[$row_get_checks["chek_type"]].'</font></td>
													<td align="left"><font face="Arial" size="1">'.$row_get_checks["chek_advisor"].'</font></td>
													<td align="left"><font face="Arial" size="1">'.$arr_clients[$row_get_checks["chek_advisor"]].'</font></td>
													<td align="left"><font face="Arial" size="1">'.str_replace('##'," ",$arr_client_rrs[$row_get_checks["chek_advisor"]]).'</font></td>
													<td align="left"><font face="Arial" size="1">'.get_rep_for_client ($arr_client_rrs, $row_get_checks["chek_advisor"]).'</font></td>
												</tr>';

												$total_checks = $total_checks + $row_get_checks["chek_amount"];
												/*
												$total_mtd = $total_mtd + $arr_mtd_comm[$k];
												$total_qtd = $total_qtd + $arr_qtd_comm[$k];
												$total_ytd = $total_ytd + $arr_ytd_comm[$k];
												$total_cmtd = $total_cmtd + $arr_mtd_check[$k]; 
												$total_cqtd = $total_cqtd + $arr_qtd_check[$k]; 
												$total_cytd = $total_cytd + $arr_ytd_check[$k];  
												$total_grand_mtd = $total_grand_mtd + $arr_mtd_comm[$k]+$arr_mtd_check[$k];
												$total_grand_qtd = $total_grand_qtd + $arr_qtd_comm[$k]+$arr_qtd_check[$k];
												$total_grand_ytd = $total_grand_ytd + $arr_ytd_comm[$k]+$arr_ytd_check[$k];
												*/
		
   if ($level_a_count == 30) {
	 $level_a_count = 0;
	 }
	 	 
	 $level_a_count = $level_a_count + 1;			
   $level_b_count = $level_b_count + 1;			
	//+++++++++++++++++++++++++++++++++++++++++++++++

  }
}

						$data_to_html_file .= '
															<tr class ="'.$class_row.'" bgcolor="'.$bgcolor.'" >
																<td align="left">&nbsp;<font face="Arial" size="1"><b>TOTAL:</b></font></td>
																<td align="right"><font face="Arial" size="1"><b>'.number_format($total_checks,2,'.',',').'</b></font>&nbsp;</td>
																<td align="left">&nbsp;&nbsp;&nbsp;<font face="Arial" size="1">&nbsp;</font></td>
																<td align="left"><font face="Arial" size="1">&nbsp;</font></td>
																<td align="left"><font face="Arial" size="1">&nbsp;</font></td>
																<td align="right"><font face="Arial" size="1">&nbsp;</font></td>
																<td align="right"><font face="Arial" size="1">&nbsp;</font></td>
															</tr>
														</table>
													</td>
												</tr>
											</table>
											<!-- END TABLE 4 -->
										</td>
									</tr>';

							
							$data_to_html_file .= "<br><br>";
							$data_to_html_file .= '<hr align="left" width="670" size="1" noshade color="#0000CC">';
							$data_to_html_file .= '<font face="Arial" size="-3">Report created by '.$info_str. ' on '.date('m/d/Y').' at '.date('h:i:sa').' from m/c ['.$_SERVER["REMOTE_ADDR"].'] using '.$_SERVER['HTTP_USER_AGENT'].'</font>';
							$data_to_html_file .= '<hr align="left" width="670" size="1" noshade color="#0000CC">';



	$file_name_prefix = rand(0,9);
	$file_name = $file_name_prefix.".html";    
	$file_pdf_name = $file_name_prefix.".pdf"; 

//production
/*
	$file_name = $trade_date_to_process."_dcar.html";     
	$file_pdf_name = $trade_date_to_process."_dcar.pdf";     
*/

	$fp = fopen ("d:\\tdw\\tdw\\data\\prnt\\".$file_name, "w");  
	fwrite ($fp,$data_to_html_file);        
	fclose ($fp); 

$cmd_pdf = "d:\\tdw\\tdw\\includes\\createpdf_args.bat ". $file_pdf_name. " " . $file_name;
//echo $cmd_pdf."<br>";
shell_exec($cmd_pdf);

//delete the temp html file
$cmd = "del d:\\tdw\\tdw\\data\\prnt\\".$file_name;
//shell_exec($cmd);

header("Content-type: application/pdf");
header("Location: http://192.168.20.63/tdw/data/prnt/".$file_pdf_name);
exit();
?>