<?
include('includes/functions.php'); 
include('includes/dbconnect.php'); 
include('includes/global.php'); 

//TO TEST THIS CODE.
// -- USE LINE 11
//-- REMOVE QUERY TO UPDATE REPORT_SENT TO 1

$trade_date_to_process = previous_business_day();
//$query_report_data = "SELECT * FROM rdat_report_data WHERE rdat_isactive = '1' AND rdat_get_report = '1' AND report_sent = '0'";
$query_report_data = "SELECT * FROM rdat_report_data WHERE rdat_time = 'now()' AND rdat_isactive = '1' AND rdat_get_report = '1' AND report_sent = '0'";
$result_report_data = mysql_query($query_report_data) or die(mysql_error());

//START WHILE 1
while($row_report_data = mysql_fetch_array($result_report_data))
{
	//START IF 1
	//WEEKLY REPORT
	if($row_report_data["rdat_rfre_id"] == '7')
	{
		$query_get_user = "SELECT Fullname, Email FROM Users WHERE ID = '".$row_report_data["rdat_user_id"]."' AND user_isactive = '1'";
		$result_get_user = mysql_query($query_get_user) or die(mysql_error());

		//START WHILE 2
		while($row_get_user = mysql_fetch_array($result_get_user))
		{	
			$control_id = gen_control_number();
			//COMPLIANCE REPORT
			if($row_report_data["rdat_repo_id"] == '1')
			{
				$mailsubject = 'Compliance Report for ' . $trade_date_to_process;
				$mailbodysubinfo = 'Compliance Report';
				$fileattach_pdf = "CRON_COMPLIANCE_".$trade_date_to_process.".pdf";
				$fileattach_html = "CRON_COMPLIANCE_".$trade_date_to_process.".html";
				$email_heading = "Compliance Report for Date (".$trade_date_to_process.") Generated on ".date("D, m/d/Y h:i a");
			}
			
			//TRADES REPORT
			if($row_report_data["rdat_repo_id"] == '2')
			{
				$mailsubject = 'Trades Report for ' . $trade_date_to_process;
				$mailbodysubinfo = 'Trades Report';
				$fileattach_pdf = "CRON_TRADES_".$trade_date_to_process.".pdf";
				$fileattach_html = "CRON_TRADES_".$trade_date_to_process.".html";
				$email_heading = "Trades Report for Date (".$trade_date_to_process.") Generated on ".date("D, m/d/Y h:i a");
			}
			
			//EMPLOYEE ACCOUNTS REPORT
			if($row_report_data["rdat_repo_id"] == '3')
			{
				$mailsubject = 'Employee Accounts Report for ' . $trade_date_to_process;
				$mailbodysubinfo = 'Employee Accounts Report';
				$fileattach_pdf = "CRON_EMPLOYEE_ACCOUNTS_".$trade_date_to_process.".pdf";
				$fileattach_html = "CRON_EMPLOYEE_ACCOUNTS_".$trade_date_to_process.".html";
				$email_heading = "Employee Accounts Report for Date (".$trade_date_to_process.") Generated on ".date("D, m/d/Y h:i a");
			}
			
			//STOCK LISTS REPORT
			if($row_report_data["rdat_repo_id"] == '4')
			{
				$mailsubject = 'Stock Lists Report for ' . $trade_date_to_process;
				$mailbodysubinfo = 'Stock Lists Report';
				$fileattach_pdf = "CRON_STOCK_LISTS_".$trade_date_to_process.".pdf";
				$fileattach_html = "CRON_STOCK_LISTS_".$trade_date_to_process.".html";
				$email_heading = "Stock Lists Report Report for Date (".$trade_date_to_process.") Generated on ".date("D, m/d/Y h:i a");				
			}

			//LINK MODE
			if($row_report_data["rdat_rmod_id"] == '1')
			{
				$link_mailbodysubinfo = 'Click the following link to get '.$mailbodysubinfo.'<Br><BR><a href="'.$_site_url .'data/exports/'.$fileattach_pdf.'">Report</a>';
				echo '<br>LINK ' . $link_mailbodysubinfo.'<Br>';
			}
	
	        //PDF MODE
			if($row_report_data["rdat_rmod_id"] == '2')
			{
				html_emails ($row_get_user["Email"], $mailsubject, $mailbodysubinfo, $emailheading, $fileattach_pdf, $control_id);
			}
			
			//HTML MODE
			if($row_report_data["rdat_rmod_id"] == '3')
			{
				html_emails ($row_get_user["Email"], $mailsubject, $mailbodysubinfo, $emailheading, $fileattach_html, $control_id);
			}
		}//END WHILE 2

		$query_update_report = "UPDATE rdat_report_data SET report_sent = '1' WHERE rdat_auto_id = '".$row_report_data["rdat_auto_id"]."'";
		$result_update_report = mysql_query($query_update_report) or die(mysql_error());
	}//END IF 1
	//DAILY REPORT
	//START ELSE 1
	else
	{
		$query_get_user = "SELECT Fullname, Email FROM Users WHERE ID = '".$row_report_data["rdat_user_id"]."' AND user_isactive = '1'";
		$result_get_user = mysql_query($query_get_user) or die(mysql_error());

		//START WHILE 2
		while($row_get_user = mysql_fetch_array($result_get_user))
		{	
			$control_id = gen_control_number();
			//COMPLIANCE REPORT
			if($row_report_data["rdat_repo_id"] == '1')
			{
				$mailsubject = 'Compliance Report for ' . $trade_date_to_process;
				$mailbodysubinfo = 'Compliance Report';
				$fileattach_pdf = "CRON_COMPLIANCE_".$trade_date_to_process.".pdf";
				$fileattach_html = "CRON_COMPLIANCE_".$trade_date_to_process.".html";
				$email_heading = "Compliance Report for Date (".$trade_date_to_process.") Generated on ".date("D, m/d/Y h:i a");
			}
			
			//TRADES REPORT
			if($row_report_data["rdat_repo_id"] == '2')
			{
				$mailsubject = 'Trades Report for ' . $trade_date_to_process;
				$mailbodysubinfo = 'Trades Report';
				$fileattach_pdf = "CRON_TRADES_".$trade_date_to_process.".pdf";
				$fileattach_html = "CRON_TRADES_".$trade_date_to_process.".html";
				$email_heading = "Trades Report for Date (".$trade_date_to_process.") Generated on ".date("D, m/d/Y h:i a");
			}
			
			//EMPLOYEE ACCOUNTS REPORT
			if($row_report_data["rdat_repo_id"] == '3')
			{
				$mailsubject = 'Employee Accounts Report for ' . $trade_date_to_process;
				$mailbodysubinfo = 'Employee Accounts Report';
				$fileattach_pdf = "CRON_EMPLOYEE_ACCOUNTS_".$trade_date_to_process.".pdf";
				$fileattach_html = "CRON_EMPLOYEE_ACCOUNTS_".$trade_date_to_process.".html";
				$email_heading = "Employee Accounts Report for Date (".$trade_date_to_process.") Generated on ".date("D, m/d/Y h:i a");
			}
			
			//STOCK LISTS REPORT
			if($row_report_data["rdat_repo_id"] == '4')
			{
				$mailsubject = 'Stock Lists Report for ' . $trade_date_to_process;
				$mailbodysubinfo = 'Stock Lists Report';
				$fileattach_pdf = "CRON_STOCK_LISTS_".$trade_date_to_process.".pdf";
				$fileattach_html = "CRON_STOCK_LISTS_".$trade_date_to_process.".html";
				$email_heading = "Stock Lists Report Report for Date (".$trade_date_to_process.") Generated on ".date("D, m/d/Y h:i a");				
			}

			//LINK MODE
			if($row_report_data["rdat_rmod_id"] == '1')
			{
				$link_mailbodysubinfo = 'Click the following link to get '.$mailbodysubinfo.'<Br><BR><a href="'.$_site_url .'data/exports/'.$fileattach_pdf.'">Report</a>';
				html_emails_dynamic ($row_get_user["Email"], "CompSys v 2.0 (Demo) <compliance@donotreply.com>", $mailsubject, $link_mailbodysubinfo, $emailheading, '', $control_id);
			}
	
	        //PDF MODE
			if($row_report_data["rdat_rmod_id"] == '2')
			{
				html_emails($row_get_user["Email"], $mailsubject, $mailbodysubinfo, $emailheading, $fileattach_pdf, $control_id);
			}
			
			//HTML MODE
			if($row_report_data["rdat_rmod_id"] == '3')
			{
				html_emails($row_get_user["Email"], $mailsubject, $mailbodysubinfo, $emailheading, $fileattach_html, $control_id);
			}
		}//END WHILE 2

		$query_update_report = "UPDATE rdat_report_data SET report_sent = '1' WHERE rdat_auto_id = '".$row_report_data["rdat_auto_id"]."'";
		$result_update_report = mysql_query($query_update_report) or die(mysql_error());
	}//END ELSE 1	
}//END WHILE 1

?>