<?
include('includes/global.php');
include('includes/dbconnect.php');
include('includes/functions.php');

//show_array($_GET);
//exit;

//=====================================================================================================
//Check conditions and report back.
$report_str = "";
$report_error = 0;
if ( $sel_exp_type == 'Client' || $sel_exp_type == 'Prospect') {

	if (is_array($exp_pfname) && count($exp_pfname) > 0 && trim($exp_pfname[0]) == "") {
		$report_str = "For expense type of Client or Prospect, you must enter at least one Person/Contact.<br>";
		$report_error = 1;
	}
}


if ($report_error == 1) {
	echo $report_str;
	exit;
}
//=====================================================================================================


$arr_ids = explode("^",$ids);
//show_array($arr_ids);

foreach($arr_ids as $k=>$v) {

	$varname = 'chk_'.$v;
	if ($$varname) {
		//echo "checkbox ".$varname . " found";
		$qry = 	"update exp_expense_items 
							set exp_approved = 1,
							exp_approver = '". $sel_approver ."',
							exp_approval_date = now(),
							exp_approver_comment = '".str_replace("'","\\'",$acomment)."'
							where auto_id = '".$v."'";
	  $result = mysql_query($qry) or die(tdw_mysql_error($qry));
	} else {
		$qry = 	"update exp_expense_items 
							set exp_approved = 0,
							exp_approver = '". $sel_approver ."',
							exp_approval_date = now()
							where auto_id = '".$v."'";
	  $result = mysql_query($qry) or die(tdw_mysql_error($qry));
	}

}

$qry = 	"update exp_expense_email_actions  
					set exp_acted_upon = 1,
					exp_acted_upon_when = now()
					where exp_md5 = '".$eid."'";
$result = mysql_query($qry) or die(tdw_mysql_error($qry));


exit;	
?>
