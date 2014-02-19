<?
//ini_set('max_execution_time', 7200);

include('../includes/dbconnect.php');
include('../includes/global.php'); 
include('../includes/functions.php'); 

//flush the table chk_totals_level_a before this bulk processing
//$result_chek = mysql_query("delete from chk_totals_level_a") or die(mysql_error());
 
//+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_
//PROCESS FOR TABLE: chk_totals_level_a 
//get aggregated checks for each client/advisor
//since as-of_trades are also included, order by trade date for proper processing

//chek_amount  chek_type  chek_advisor  chek_comments  chek_date  chek_entered_by  chek_isactive  
//auto_id  chk_check_date  chk_advisor_code  chk_advisor_name  chk_total  chk_mtd  chk_qtd  chk_ytd  chk_isactive  
$query_chek =  "SELECT sum(chek_amount) as total_checks,
												chek_date,
												chek_advisor
								FROM chk_chek_payments_etc
								WHERE chek_isactive = 1 and
								chek_processed != 1 and 
								chek_date > '2006-01-17'
								GROUP BY chek_advisor, chek_date
								ORDER BY chek_advisor, chek_date";
//echo $query_chek."<br>";
$result_chek = mysql_query($query_chek) or die(tdw_mysql_error($query_chek));

while($row_chek = mysql_fetch_array($result_chek))
{
	$total_checks  = $row_chek["total_checks"];
	$chk_advisor_code = $row_chek["chek_advisor"];
	$chk_date = $row_chek["chek_date"];
	
				//find if data point exists
				$qry_exists = "SELECT count(*) as countval 
											FROM chk_totals_level_a  
											WHERE chk_advisor_code = '".$chk_advisor_code."'";
											
				$result_exists = mysql_query($qry_exists) or die(mysql_error());
				while($row_exists = mysql_fetch_array($result_exists))
						{
						$countval = $row_exists["countval"];
						}

				//if data point exists then proceed with processing
				if ($countval > 0) { //values exist
						$query_mqydate = "SELECT max(chk_check_date) AS chk_check_date
															FROM chk_totals_level_a
															WHERE chk_advisor_code = '".$chk_advisor_code."'";
						$result_mqydate = mysql_query($query_mqydate) or die(mysql_error());
						while($row_mqydate = mysql_fetch_array($result_mqydate))
						{
						//getting the latest date value
						$latestdate = $row_mqydate["chk_check_date"];
						}
				
						$query_mqy = "SELECT * 
													FROM chk_totals_level_a
													WHERE chk_advisor_code = '".$chk_advisor_code."'
													AND chk_check_date = '".$latestdate."'";

						$result_mqy = mysql_query($query_mqy) or die(mysql_error());
						while($row_mqy = mysql_fetch_array($result_mqy))
						{
							$chk_mtd = $row_mqy["chk_mtd"];
							$chk_qtd = $row_mqy["chk_qtd"];
							$chk_ytd = $row_mqy["chk_ytd"];
						}
						
						//Process the numbers based on date logic
						$is_same_year = 	samebrokyear($latestdate,$chk_date);
						$is_same_month = 	samebrokmonth($latestdate,$chk_date);
						$is_same_qtr = 		samebrokqtr($latestdate,$chk_date);
						
						if ($is_same_year == 1) {
								if ($is_same_month == 1) {
										$insert_mtd = $chk_mtd + $total_checks;
										$insert_qtd = $chk_qtd + $total_checks;
										$insert_ytd = $chk_ytd + $total_checks;						 
								} else {
										if ($is_same_qtr == 1) {
											$insert_mtd = $total_checks;
											$insert_qtd = $chk_qtd + $total_checks;
											$insert_ytd = $chk_ytd + $total_checks;						 
										} else {
											$insert_mtd = $total_checks;
											$insert_qtd = $total_checks;
											$insert_ytd = $chk_ytd + $total_checks;						 
										}
								}
						} else {
								$insert_mtd = $total_checks;
								$insert_qtd = $total_checks;
								$insert_ytd = $total_checks;						 
						}

				} else { //rep/advisor have no prior entry, no data points exists, just insert data
					$insert_mtd = $total_checks;
					$insert_qtd = $total_checks; 
					$insert_ytd = $total_checks;						 
				}

			//insert into table rep_chk_rr_level_a
			$sql_level_a_insert = "INSERT INTO chk_totals_level_a 
															(auto_id,
															chk_check_date,
															chk_advisor_code,
															chk_advisor_name,
															chk_total,
															chk_mtd,
															chk_qtd,
															chk_ytd,
															chk_isactive) 
														VALUES (
														NULL , 
														'".$chk_date."', 
														'".$chk_advisor_code."', 
														'', 
														'".$total_checks."', 
														'".$insert_mtd."', 
														'".$insert_qtd."', 
														'".$insert_ytd."', 
														'1'
														)";

			echo $sql_level_a_insert."<hr>";
			$result_level_a_insert = mysql_query($sql_level_a_insert) or die(tdw_mysql_error($sql_level_a_insert));				
			
//set the processed flag to 1
$qry_set_processed = "UPDATE chk_chek_payments_etc
											SET chek_processed = 1
											WHERE chek_isactive = 1 and
											chek_date = '".$chk_date."'";
echo $qry_set_processed."<br>";
$result_set_processed = mysql_query($qry_set_processed) or die(tdw_mysql_error($qry_set_processed));													
}



//+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_
?>