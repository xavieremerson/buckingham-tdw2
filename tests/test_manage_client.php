<?
  include('includes/dbconnect.php');
  include('includes/global.php');
	include('includes/functions.php');

	//function get user_id from Initials
	function get_userid_for_initials ($Initials) {
		$user_id = db_single_val("SELECT ID as single_val FROM users WHERE Initials = '".$Initials."'");   
		return $user_id;
	}

	// get client details from table and ppopulate other table
	$query_sel_client =  "SELECT clnt_auto_id  clnt_code,
													clnt_alt_code,
													clnt_name,
													clnt_rr1,
													clnt_rr2,
													clnt_reps_and_or,
													clnt_reps_or,
													clnt_reps_special,
													clnt_trader,
													clnt_timestamp,
													clnt_isactive 
												FROM int_clnt_clients
												ORDER BY clnt_name";
	$result_sel_client = mysql_query($query_sel_client) or die(mysql_error());
	while($row_sel_client = mysql_fetch_array($result_sel_client)) {
		echo $row_sel_client["clnt_name"]."<br>";
		
		//Get reps for the clients and populate the other table
		if (strlen(trim($row_sel_client["clnt_rr1"])) == 2) {
			//get the name and userid of the user
		
		}
		if (strlen(trim($row_sel_client["clnt_rr2"])) == 2) {
			//get the name and userid of the user
		
		}
	}

exit;
?>