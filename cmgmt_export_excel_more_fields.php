<?
include('includes/functions.php');
include('includes/global.php');
include('includes/dbconnect.php');
	
include('pay_payout_functions.php');
											
$output_filename = "clients.csv";
$fp = fopen($exportlocation.$output_filename, "w");

$string = "\"Client Name\",\"Client Code\",\"Tradeware Code\",\"Rep 1\",\"Rep 2\",\"Trader"."\"".chr(13); 

fputs ($fp, $string);

						//Clients List
						$query_client = "SELECT * 
													FROM int_clnt_clients where clnt_isactive = 1  
													order by clnt_name";
						$result_client = mysql_query($query_client) or die(tdw_mysql_error($query_client));
						while($row_client = mysql_fetch_array($result_client)) {
						
						$rr1 = trim($row_client["clnt_rr1"]);
						$rr2 = trim($row_client["clnt_rr2"]);	
						if ($rr1 != '' OR $rr2 != '') {
							if ($rr2 == '') {
								$tmp_rr_num = get_rr_num (get_userid_for_initials ($rr1));
							} else {
								$tmp_rr_num = get_shared_rr_num ($rr1, $rr2);
							}
						}


						$string = "\"".$row_client["clnt_name"]."\",\"".$row_client["clnt_code"]."\",\"".$row_client["clnt_alt_code"]."\",\"".$row_client["clnt_rr1"]."\",\"".$tmp_rr_num."\",\"".get_repname_by_rr_num ($tmp_rr_num)."\",\"".$row_client["clnt_rr2"]."\",\"".$row_client["clnt_trader"]."\"".chr(13); 
						//echo $string;
						fputs ($fp, $string);
						}

fclose($fp);

Header("Location: data/exports/".$output_filename);
?>