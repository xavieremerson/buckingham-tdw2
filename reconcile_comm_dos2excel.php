<?
include('includes/functions.php');
include('includes/global.php');
include('includes/dbconnect.php');
												
$date_start = $xstart;
$date_end   = $xend;

$output_filename = md5(rand(1000000000,9999999999))."_dos_data.csv";
$fp = fopen($exportlocation.$output_filename, "w");

$string = "\"Trade Date\",\"CLIENT\",\"Commission"."\"".chr(13); 

fputs ($fp, $string);

						//DOS COMMISSIONS
						$total_dos = 0;
						$query_dos = "SELECT * 
													FROM mry_dos_commission 
													order by clnt_code";
						$result_dos = mysql_query($query_dos) or die(tdw_mysql_error($query_dos));
						while($row_dos = mysql_fetch_array($result_dos)) {
						$total_dos = $total_dos + $row_dos["clnt_commission"];
						$grand_total_dos = $grand_total_dos + $row_dos["clnt_commission"];
					
						$string = "\"".format_date_ymd_to_mdy($row_dos["clnt_trade_date"])."\",\"".$row_dos["clnt_code"]."\",\"".$row_dos["clnt_commission"]."\"".chr(13); 
						//echo $string;
						fputs ($fp, $string);
						
						}

fclose($fp);

Header("Location: data/exports/".$output_filename);
?>