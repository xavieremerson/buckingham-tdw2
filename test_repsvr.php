<?
include('includes/functions.php');
include('includes/global.php');
include('includes/dbconnect.php');
/*
xdebug("email",$email);
xdebug("src",$src);
//http://192.168.20.63/tdw/repsvr.php?rep=DCAR&src=438159172006N08N0779QQQQQQQQa7b23482fd50e037605483415820f1f7
//capture person information
xdebug("userid",$userid);

xdebug("trade_date",$trade_date);

xdebug("report_file",$report_file);
*/
$rep_id = $rep;
$userid = str_replace('Q','',substr($src,18,10));
$trade_date = str_replace('N','-',substr($src,8,10));
$report_file = $trade_date."_".$rep_id.".pdf";

xdebug("rep_id",$rep_id);
xdebug("userid",$userid);
xdebug("trade_date",$trade_date);
xdebug("report_file",$report_file);



exit;
//581805412008N04N15QQQQQQQQQQa7b23482fd50e037605483415820f1f7

$rep_auto_id = db_single_val("select auto_id as single_val from mgmt_reports_creation where msrv_rep_file = '".$report_file."'");


		//if record does not already exist for user then insert it.
		$qry_exists = "SELECT * FROM mgmt_sup_report_views where msrv_rep_id = '".$rep_id."' and msrv_user_id = '".$userid."' and msrv_rep_auto_id = '".$rep_auto_id."'";
/*		xdebug("qry_exists",$qry_exists);
		exit;
*/		$result_exist = mysql_query($qry_exists) or die (tdw_mysql_error());
		$count = mysql_num_rows($result_exist);
		if ($count == 0) {
		$qry_insert = "INSERT INTO mgmt_sup_report_views ( 
										auto_id , 
										msrv_rep_id ,
										msrv_rep_auto_id, 
										msrv_user_id , 
										msrv_view_datetime , 
										msrv_isactive ) 
									VALUES (
									NULL , 
									'".$rep_id."',
									'".$rep_auto_id."', 
									'".$userid."', 
									NOW(), 
									'1')";
		
		$result_insert = mysql_query($qry_insert) or die (tdw_mysql_error($qry_insert));
		}
$file = "data/compliance/$report_file";
/*echo "here";
exit;

xdebug("file",$file);
*//*
header("Pragma: public");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Content-type: application/pdf");
header("Content-Length: ".filesize($file));
header("Content-disposition: attachment; filename=".$report_file);
header("Content-Transfer-Encoding: binary");
header("Accept-Ranges: ".filesize($file)); 
readfile($file);
*/
header("Content-type: application/pdf");
header("Location: http://192.168.20.63/tdw/data/compliance/".$report_file);
exit();
?>