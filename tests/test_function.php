<?
  include('includes/dbconnect.php');
  include('includes/global.php');
	include('includes/functions.php');


//====================================================================================================
//  NEEDS TO RUN ONLY ON WEEKDAYS AND NON-HOLIDAYS, THIS IS TO CHECK THAT CONDITION
	$sel_client = "MILP";

	function get_fullname_for_initials ($Initials) {
		$user_fullname = db_single_val("SELECT Fullname as single_val FROM users WHERE Initials = '".$Initials."'");   
		return $user_fullname;
	}

	$query_get_client_info = "SELECT * from int_clnt_clients where clnt_code = '".$sel_client."'";
	$result_get_client_info = mysql_query($query_get_client_info) or die(mysql_error());
	$str_show_reps_traders = "";
	while($row_get_client_info = mysql_fetch_array($result_get_client_info)){
			$initials_rr1  = $row_get_client_info["clnt_rr1"];			
			$initials_rr2  = $row_get_client_info["clnt_rr2"];			
			$initials_trdr = $row_get_client_info["clnt_trader"];		
			if (strlen($initials_rr2) < 2) {
				$str_show_reps_traders = "Sales Rep. : ".get_fullname_for_initials($initials_rr1)."<br>Trader : ".get_fullname_for_initials($initials_trdr);
			} else {
				$str_show_reps_traders = "Sales Rep. : ".get_fullname_for_initials($initials_rr1)." / ".get_fullname_for_initials($initials_rr2)."<br>Trader : ".get_fullname_for_initials($initials_trdr);
			}
	}




//====================================================================================================

?>
	