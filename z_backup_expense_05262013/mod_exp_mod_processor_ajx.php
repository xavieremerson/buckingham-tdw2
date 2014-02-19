<?
include('includes/global.php');
include('includes/dbconnect.php');
include('includes/functions.php');

//show_array($_GET);
//exit;

$arr_ids = explode("^",$ids);
//show_array($arr_ids);

foreach($arr_ids as $k=>$v) {

	$varname = 'chk_'.$v;
	if ($$varname) {
		//echo "checkbox ".$varname . " found";
		$qry = 	"update exp_expense_items 
							set exp_processed = 1,
							exp_processed_by = '". $user_id ."',
							exp_processed_date = now()
							where auto_id = '".$v."'";
	  $result = mysql_query($qry) or die(tdw_mysql_error($qry));
	}/* else {
		$qry = 	"update exp_expense_items 
							set exp_approved = 0,
							exp_approver = '". $user_id ."',
							exp_approval_date = now()
							where auto_id = '".$v."'";
	  $result = mysql_query($qry) or die(tdw_mysql_error($qry));
	}*/

}
//							exp_approver_comment = '".str_replace("'","\\'",$acomment)."'

exit;	
?>