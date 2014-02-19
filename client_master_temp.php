<?
  include('includes/dbconnect.php');
  include('includes/global.php');
  include('includes/functions.php');
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&

//name  sales1  sales2  trader  code  comment   




	$query_temp = "SELECT * from int_clients_temp";

	$result_temp = mysql_query($query_temp) or die (tdw_mysql_error($query_temp));
	while ( $row = mysql_fetch_array($result_temp) ) {

		echo  "[".strlen($row["name"]). "] ".$row["name"]."\n<br>";

			$qry_a = "INSERT INTO `warehouse`.`int_clnt_clients` (
								`clnt_auto_id` ,
								`clnt_code` ,
								`clnt_alt_code` ,
								`clnt_name` ,
								`clnt_rr1` ,
								`clnt_rr2` ,
								`clnt_reps_and_or` ,
								`clnt_reps_or` ,
								`clnt_reps_special` ,
								`clnt_trader` ,
								`clnt_timestamp` ,
								`clnt_status` ,
								`clnt_isactive` 
								)
								VALUES (
								NULL , '----', '----', '".$row["name"]."', '".$row["sales1"]."', '".$row["sales2"]."', '1', NULL , NULL , '".$row["trader"]."', 
								CURRENT_TIMESTAMP , '".$row["code"]."', '2')";

				$result_a = mysql_query($qry_a) or die (tdw_mysql_error($qry_a));
				echo  "INSERTED\n<br>";
				
				//Get the ID just created
				$new_id = db_single_val("select max(clnt_auto_id) as single_val from int_clnt_clients");
				
				if (trim($row["comment"]) != "") {
					$qry_b = "INSERT INTO `warehouse`.`int_clnt_clients_comments` (
										`auto_id` ,
										`clnt_auto_id` ,
										`clnt_comment` ,
										`clnt_comment_by` ,
										`clnt_timestamp` ,
										`clnt_isactive` 
										)
										VALUES (
										NULL , '".$new_id."', '".$row["comment"]."', '253', 
										CURRENT_TIMESTAMP , '1'
										)";
		
						$result_b = mysql_query($qry_b) or die (tdw_mysql_error($qry_b));
						echo  "INSERTED COMMENT\n<br>";

				} else {
						echo  "BLANK COMMENT\n<br>";
				}
		

	}

?>




