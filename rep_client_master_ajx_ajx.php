<?
  include('includes/dbconnect.php');
  include('includes/global.php');
  include('includes/functions.php');
	
if ($mod_request == "comment") {

			$arr_name_for_id = array(); //[$row_comment["clnt_comment_by"]]
			$qry_usr = "select ID, Fullname FROM users"; 
			$result_usr = mysql_query($qry_usr) or die (tdw_mysql_error($qry_usr));
			while ( $row_usr = mysql_fetch_array($result_usr) ) 
			{
				$arr_name_for_id[$row_usr["ID"]] = $row_usr["Fullname"];
			}
			
			$arr_clnt_comment = array();
			$qry_comment = "select auto_id, clnt_auto_id, clnt_comment, clnt_comment_by, clnt_timestamp, clnt_isactive  
										 FROM int_clnt_clients_comments 
										 WHERE clnt_isactive = 1
										 AND clnt_auto_id = '".$cid."'
										 AND auto_id not in (select max(auto_id) from int_clnt_clients_comments where clnt_auto_id = '".$cid."') 
										 ORDER BY clnt_auto_id, clnt_timestamp desc"; 
			$result_comment = mysql_query($qry_comment) or die (tdw_mysql_error($qry_comment));
			while ( $row_comment = mysql_fetch_array($result_comment) ) 
			{

				$whowhen = "";
				
				$whowhen = "[".date('m/d/y h:ia',strtotime($row_comment["clnt_timestamp"]))." ".$arr_name_for_id[$row_comment["clnt_comment_by"]]."]<br>";
				$str_comment_data = str_replace('"','',$row_comment["clnt_comment"]);
					echo $whowhen.$str_comment_data."<br>"; //"&#9658;".
			}


} else if ($mod_request == "???") {
echo "???";
} else {
echo "Blank Response from TDW Server";
}	
	
	
?>	