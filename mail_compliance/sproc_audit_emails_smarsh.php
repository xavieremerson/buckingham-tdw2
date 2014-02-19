<?

error_reporting(E_ALL);
ini_set('max_execution_time', 7200); 
ini_set('date.timezone', 'America/New_York');
include("_csys_receivemail.class.php");


$str_html_file = "";
$str_html_file .= '
<style type="text/css">
<!--
.tdwthbrdr {
  font-family: Verdana, Arial, Helvetica, sans-serif;
  font-size: 11px;
  color: #000000;
  border-collapse: collapse;
	border: 1px #777777 solid
}
.fblue {
  font-family: Verdana, Arial, Helvetica, sans-serif;
  font-size: 11px;
  color: #0000ff;
}
-->
</style>';
?>

<?php
//get TDW functions
require_once("../includes/functions.php");
require_once("../includes/dbconnect.php");
require_once("../includes/global.php");

function strip_angles ($str) { //from email addresses  "Buck InvUpdate" <BuckInvUpdate@BuckResearch.com>
	$final_str = str_replace('"','',$str);
	$final_str = substr($final_str,0,stripos($final_str,"<"));
	return $final_str;
}

function show_nice_date ($str) {
	$show_str = format_date_ymd_to_mdy(substr($str,0,10));
	$date1 = mktime( substr($str,11,2)-4, substr($str,14,2), 0, 0, 0, 0 );  // changed -5 to -4 for daylight saving.
	$show_str .= " ".date("h:ia", $date1);
	return $show_str;
}

function to_db($str) {
	$arr_vals = explode("^",$str);
	
	$clean_str_subject = str_replace("'","\\'",$arr_vals[4]);
	
	$clean_str_subject = preg_replace('/[^a-z0-9 ]/is', '', $clean_str_subject);
	
	$qry = "INSERT INTO eml_research_emails 
					( auto_id , eml_type, eml_trade_date , eml_subject , eml_from , eml_to , eml_format_time , eml_isactive ) 
					VALUES (
					NULL ,
					'".$arr_vals[0]."', 
					'".previous_business_day()."', 
					'".substr($clean_str_subject,0,200)."', 
					'".str_replace("'","\\'",$arr_vals[2])."', 
					'".str_replace("'","\\'",$arr_vals[3])."', 
					'".$arr_vals[1]."', 
					'1'
					)";
		//echo $qry;
		$result = mysql_query($qry) or die (tdw_mysql_error($qry));
}

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$trade_date_to_process = previous_business_day();
//$trade_date_to_process ='2008-02-01';
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

$str_html_file = "";

$str_html_file .= "<font color='blue'>Trade Date: ".format_date_ymd_to_mdy($trade_date_to_process)."</font><br><br>";

//  RESEARCH DISSEMINATION &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//Processing Research Dissemination

$obj= new receiveMail('tdw-monitor@buckresearch.com','16286$brMail','tdw-monitor@buckresearch.com','owa.smarshexchange.com','imap','143',false); 
//Connect to the Mail Box
$obj->connect(); 
// Get Total Number of Unread Email in mail box
$tot=$obj->getTotalMails(); 
$arr_rd_emails = array();
for($i=$tot;$i>0;$i--)
{
	$head=$obj->getHeaders($i);  // Get Header Info Return Array Of Headers **Array Keys are (subject,to,toOth,toNameOth,from,fromName,msd_date)

	$strDate = $head['msg_date'];
	$strDateCompare = date('Y-m-d',strtotime(substr($strDate,5,11)));
	$strDateDB = date('m/d/Y h:ia',strtotime(substr($strDate,5,20)));
	$arr_rd_emails[] = array($head['fromName'],ucfirst($head['to']),$head['subject'],$head['msg_date'],$strDateDB,$strDateCompare);
}
$obj->close_mailbox();   //Close Mail Box

//show_array($arr_rd_emails);
//exit;

$value_exit = 0;
while ( $value_exit == 0 ) {
	
	if (count($arr_rd_emails) > 0) {
		//=============================================================================
		$str_html_file .= "<font color='blue'>Research Dissemination</font>";
		
		$str_html_file .= '<table width="900" border="1" cellspacing="0" cellpadding="0" class="tdwthbrdr">'; 
		$count_rd = 0;
		$count_rd_all = 0;
		foreach($arr_rd_emails as $k=>$val) { 
				//	$arr_rd_emails[] = array($head['fromName'],ucfirst($head['to']),$head['subject'],$head['msg_date'],$strDateDB,$strDateCompare);
				if ($val[5] == $trade_date_to_process) { 
				
				if ($val[1]=="Research Dissemination") {
					$count_rd = $count_rd + 1;
					$count_rd_all = $count_rd_all + 1;
					//echo "<br>RD ==>".trim(strip_angles($item->A_PROPSTAT[0]->A_PROP[0]->D_TO[0]->_text));
				} else {
					$count_rd_all = $count_rd_all + 1;
					//echo "<br>Not RD ==>".trim(strip_angles($item->A_PROPSTAT[0]->A_PROP[0]->D_TO[0]->_text));
				}
				
				$arr_rd[] = "Research Dissemination"."^".
										$val[4]."^".
										$val[0]."^".
										$val[1]."^".
										$val[2];
				to_db("Research Dissemination"."^".
										$val[4]."^".
										$val[0]."^".
										$val[1]."^".
										$val[2]);
				$str_html_file .= '
								<tr>
									<td width="140">'.$val[4].'</td>
									<td>TO: Research Dissemination FROM: '.$val[0].'</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td><a class="fblue">'.preg_replace('/[^a-z0-9 ]/is', '', $val[2]).'</a></td>
								</tr>
								<tr><td colspan="2">&nbsp;</td></tr>';
				}
		} 
		
		echo "# of Research Dissemination items = ".$count_rd."<br>";
		echo "# of All in Research Dissemination items = ".$count_rd_all."<br>";
		echo "# of Difference = ". ($count_rd_all-$count_rd) . "<br>";
		
		$str_html_file .= "</table>\n"; 
		$str_html_file .= "<br><br>";
		echo "File Research Dissemination created";
		//=============================================================================
	
		$value_exit = 1;				
	}

	ob_flush();
	flush();
	//$pauseval = 5;
	//echo "Pausing for ".$pauseval." seconds.<br>";
	//sleep($pauseval);
}

//  BUCKINVUPDATE &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
// Processing BuckInvUpdate

$obj= new receiveMail('BuckInvUpdate@buckresearch.com','286$brMail','BuckInvUpdate@buckresearch.com','owa.smarshexchange.com','imap','143',false); 
//Connect to the Mail Box
$obj->connect(); 
// Get Total Number of Unread Email in mail box
$tot=$obj->getTotalMails(); 
$arr_iu_emails = array();
$arr_iu_date_subject = array(); // For matching from RD
for($i=$tot;$i>0;$i--)
{
	$head=$obj->getHeaders($i);  // Get Header Info Return Array Of Headers **Array Keys are (subject,to,toOth,toNameOth,from,fromName,msd_date)

	$strDate = $head['msg_date'];
	$strDateCompare = date('Y-m-d',strtotime(substr($strDate,5,11)));
	$strDateDB = date('m/d/Y h:ia',strtotime(substr($strDate,5,20)));
	$arr_iu_emails[] = array($head['fromName'],ucfirst($head['to']),$head['subject'],$head['msg_date'],$strDateDB,$strDateCompare);
	$arr_iu_date_subject[] = $strDateCompare."^".$head['subject'];

}
$obj->close_mailbox();   //Close Mail Box

//show_array($arr_rd_emails);
//exit;

$value_exit = 0;
while ( $value_exit == 0 ) {

	if (count($arr_iu_emails > 0)) {
		//=============================================================================
		$str_html_file .= "<font color='blue'>Investment Update</font>";
		$str_html_file .= '<table width="900" border="1" cellspacing="0" cellpadding="0" class="tdwthbrdr">'; 
		$count_iu = 0;
		foreach($arr_iu_emails as $k=>$val) { 
				
				if ($val[5] == $trade_date_to_process) { 
			
					$count_iu = $count_iu + 1;
		
				$arr_iu[] = "Buck InvUpdate"."^".
										$val[4]."^".
										$val[0]."^".
										$val[1]."^".
										$val[2];
				to_db("Buck InvUpdate"."^".
										$val[4]."^".
										$val[0]."^".
										$val[1]."^".
										$val[2]);
				$str_html_file .= '
								<tr>
									<td width="140">'.$val[4].'</td>
									<td>TO: Buck InvUpdate FROM: '.$val[0].'</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td><a class="fblue">'.preg_replace('/[^a-z0-9 ]/is', '', $val[2]).'</a></td>
								</tr>
								<tr><td colspan="2">&nbsp;</td></tr>';
				}
		}

		echo "# of Buck InvUpdate items = ".$count_iu."<br>"; 
		$str_html_file .= "</table>\n"; 
		$str_html_file .= "<br><br>";
		echo "File InvUpdate created";
		//=============================================================================
	
		$value_exit = 1;				
	}


	ob_flush();
	flush();
	//$pauseval = 5;
	//echo "Pausing for ".$pauseval." seconds.<br>";
	//sleep($pauseval);

}

//  BUCKQUICKNOTE &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
// Processing BuckQuicknote

$obj= new receiveMail('BuckQuicknote@buckresearch.com','286$brMail','BuckQuicknote.com','owa.smarshexchange.com','imap','143',false); 
//Connect to the Mail Box
$obj->connect(); 
// Get Total Number of Unread Email in mail box
$tot=$obj->getTotalMails(); 
$arr_qn_emails = array();
$arr_qn_date_subject = array(); // For matching from RD
for($i=$tot;$i>0;$i--)
{
	$head=$obj->getHeaders($i);  // Get Header Info Return Array Of Headers **Array Keys are (subject,to,toOth,toNameOth,from,fromName,msd_date)

	$strDate = $head['msg_date'];
	$strDateCompare = date('Y-m-d',strtotime(substr($strDate,5,11)));
	$strDateDB = date('m/d/Y h:ia',strtotime(substr($strDate,5,20)));
	$arr_qn_emails[] = array($head['fromName'],ucfirst($head['to']),$head['subject'],$head['msg_date'],$strDateDB,$strDateCompare);
	$arr_qn_date_subject[] = $strDateCompare."^".$head['subject'];
}
$obj->close_mailbox();   //Close Mail Box

//show_array($arr_rd_emails);
//exit;

$value_exit = 0;
while ( $value_exit == 0 ) {

	if (count($arr_qn_emails) > 0) {
		//=============================================================================
		$str_html_file .= "<font color='blue'>QuickNote</font>";
		$str_html_file .= '<table width="900" border="1" cellspacing="0" cellpadding="0" class="tdwthbrdr">'; 
		$count_qn = 0;
		foreach($arr_qn_emails as $k=>$val) { 
				
				if ($val[5] == $trade_date_to_process) { 
			
					$count_qn = $count_qn + 1;
		
		
				$arr_qn[] = "QuickNote"."^".
										$val[4]."^".
										$val[0]."^".
										$val[1]."^".
										$val[2];
				to_db("QuickNote"."^".
										$val[4]."^".
										$val[0]."^".
										$val[1]."^".
										$val[2]);
				$str_html_file .= '
								<tr>
									<td width="140">'.$val[4].'</td>
									<td>TO: QuickNote FROM: '.$val[0].'</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td><a class="fblue">'.preg_replace('/[^a-z0-9 ]/is', '', $val[2]).'</a></td>
								</tr>
								<tr><td colspan="2">&nbsp;</td></tr>';
				}
		} 

		echo "# of Buck QuickNote items = ".$count_qn."<br>"; 
		$str_html_file .= "</table>\n"; 
		echo "File QuickNote created";
		//=============================================================================
	
		$value_exit = 1;				
	}


	ob_flush();
	flush();
	//$pauseval = 5;
	//echo "Pausing for ".$pauseval." seconds.<br>";
	//sleep($pauseval);

}

//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&

//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

//clean up unwanted characters
for ($i=0;$i<10;$i++) {
$str_html_file = str_replace(" <","<",$str_html_file);
$str_html_file = str_replace("\t","",$str_html_file);
$str_html_file = str_replace("\n","",$str_html_file);
$str_html_file = str_replace("\n\r","",$str_html_file);
$str_html_file = str_replace("\r","",$str_html_file);
}
//=====================================================================================================
//first clean the counts table and then insert the relevant data

$qry_del = "delete from eml_research_counts where eml_trade_date = '".$trade_date_to_process."'";
$result_del = mysql_query($qry_del) or die(tdw_mysql_error($qry_del));
$qry_insert_count = "INSERT INTO eml_research_counts 
									(auto_id, eml_trade_date,eml_type,eml_count,eml_isactive) 
									VALUES (
									NULL , 
									'".$trade_date_to_process."', 
									'Research Dissemination', 
									'".$count_rd."', 
									'1'
									)";
$result_insert_count = mysql_query($qry_insert_count) or die(tdw_mysql_error($qry_insert_count));
$qry_insert_count = "INSERT INTO eml_research_counts 
									(auto_id, eml_trade_date,eml_type,eml_count,eml_isactive) 
									VALUES (
									NULL , 
									'".$trade_date_to_process."', 
									'Buck InvUpdate', 
									'".$count_iu."', 
									'1'
									)";
$result_insert_count = mysql_query($qry_insert_count) or die(tdw_mysql_error($qry_insert_count));
$qry_insert_count = "INSERT INTO eml_research_counts 
									(auto_id, eml_trade_date,eml_type,eml_count,eml_isactive) 
									VALUES (
									NULL , 
									'".$trade_date_to_process."', 
									'Buck QuickNote', 
									'".$count_qn."', 
									'1'
									)";
$result_insert_count = mysql_query($qry_insert_count) or die(tdw_mysql_error($qry_insert_count));
$qry_insert_count =  "INSERT INTO eml_research_counts 
											(auto_id, eml_trade_date, eml_type, eml_count, eml_isactive) 
											VALUES (
											NULL , 
											'".$trade_date_to_process."', 
											'Total', 
											'".$count_rd_all."', 
											'1'
											)";
$result_insert_count = mysql_query($qry_insert_count) or die(tdw_mysql_error($qry_insert_count));
//=====================================================================================================
//set all data in the table to inactive for this trade date and then insert this record.

			//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			//check the data for the correctness
			if (($count_iu+$count_qn+$count_rd)==$count_rd_all) {
				echo "Totals ARE correct and there are no exceptions";
				$is_ok = 1;
			} else {
				echo "Totals are NOT incorrect and there are exceptions";
				$is_ok = 0;
			}
			
			//FIND IF OK OR NOT OK IN MATCHING.
			//First get array of all dates.
			$arr_dates_list = array();
			foreach ($arr_rd_emails as $k=>$v) {
				$arr_dates_list[$v[5]] = $v[5];
			}
			
			//Form an array of IS_OK by Trade Date
			$arr_IS_OK = array(); // Form = date=>IS_OK
			
			foreach ($arr_dates_list as $kdate=>$dval) {
				
				//Get matched IU in RD
				$is_match_iu = 1;
				foreach ($arr_rd_emails as $rd_index=>$arr_rd_vals) {
					if ($arr_rd_vals[5] == $dval) {
						if (stripos($arr_rd_vals[2], "investment update")===true) {
							//Look at IU list for match
							if (!in_array($arr_rd_vals[5]."^".$arr_rd_vals[2],$arr_iu_date_subject)) {
								$is_match_iu = 0;
							}
						}
					}
				}
			
				//Get matched QN in RD
				$is_match_qn = 1;
				foreach ($arr_rd_emails as $rd_index=>$arr_rd_vals) {
					if ($arr_rd_vals[5] == $dval) {
						if (stripos($arr_rd_vals[2], "quick note")===true) {
							//Look at QN list for match
							if (!in_array($arr_rd_vals[5]."^".$arr_rd_vals[2],$arr_qn_date_subject)) {
								$is_match_qn = 0;
							}
						}
					}
				}
			
				if ($is_match_iu == 1 && $is_match_qn == 1) {
					$arr_IS_OK[$dval] = 1;
				} else {
					$arr_IS_OK[$dval] = 0;
				}	
				
			}

			//LOGIC UPDATED, COMMENTED OUT ABOVE
			//IS OK LOGIC PERFORMED IN THE BEGINNING SECTION
			$is_ok = $arr_IS_OK[$trade_date_to_process];

			
			//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

$result_update_isactive = mysql_query("update eml_research_compliance set eml_isactive = 0 where eml_trade_date = '".$trade_date_to_process."'");
$clean_str_file = str_replace("'","\\'",$str_html_file);
$clean_str_file = str_replace("’","\\'",$clean_str_file);

//$clean_str_file = preg_replace('/[^a-z0-9 ]/is', '', $clean_str_file);

$qry_insert_htmlfile = "INSERT INTO eml_research_compliance
													(auto_id, eml_is_ok, eml_trade_date, eml_html_file, eml_isactive) 
												VALUES (
												NULL ,
												'".$is_ok."', 
												'".$trade_date_to_process."', 
												'".$clean_str_file."', 
												'1'
												)";
$result_insert_htmlfile = mysql_query($qry_insert_htmlfile) or die(tdw_mysql_error($qry_insert_htmlfile));

//=====================================================================================================
//show the data FOR NOW THAT IS
$qry_data = "select * from eml_research_compliance where eml_trade_date = '".$trade_date_to_process."' and eml_isactive = 1";
$result_data = mysql_query($qry_data) or die(tdw_mysql_error($qry_data));
while($row_data = mysql_fetch_array($result_data)) {
	//echo $row_data['eml_html_file'];
}

//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^


echo $str_html_file;
exit;
//$locval = "/exchange/BuckInvUpdate/Inbox";
?>