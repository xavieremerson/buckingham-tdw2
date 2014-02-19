<?
function create_arr ($q, $i=1) {
  $arr_created = array();
	$result = mysql_query($q) or die(tdw_mysql_error($q));
	if ($i == 1) {
		while ( $row = mysql_fetch_array($result) )
		{
			$arr_created[] = $row["v"];
		}
	} else {
		while ( $row = mysql_fetch_array($result) )
		{
			$arr_created[$row["k"]] = $row["v"];
		}
	}
	return $arr_created;
}

//Create Lookup Array of Client Code / Client Name

	$qry_clients = "select * from int_clnt_clients";
	$result_clients = mysql_query($qry_clients) or die (tdw_mysql_error($qry_clients));
	$arr_clients = array();
	while ( $row_clients = mysql_fetch_array($result_clients) ) 
	{
		$arr_clients[$row_clients["clnt_code"]] = trim($row_clients["clnt_name"]);
	}
	
	
	//temporary MUST CHANGE THIS LATER
	function look_up_client($clnt) {
		global $arr_clients;
		if ($arr_clients[$clnt] == '') {
		   return $clnt;
		} else {
		   return $arr_clients[$clnt];
		}
	}

//show_array($arr_clnt_for_rr);
//get initials for the user
$user_initials = db_single_val("select Initials as single_val from users where rr_num = '".$rep_to_process."'");

?>