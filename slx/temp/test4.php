<?

	include('includes/dbconnect.php');
	include('includes/global.php');
	include('includes/functions.php');  

$control_id = gen_control_number();
html_emails ("prasad_pravin@yahoo.com", "This is the trade report for 05/18/2004.", " ", "Trade Report Generated on 05/18/2004", "Trades_Report_NEW2004-05-18.pdf", $control_id);




?>