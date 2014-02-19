<?
/*
This program finds out the trades in clients where the rep number is different than the one on the account master
*/
?>
<?
ini_set('max_execution_time', 3600);

include('../includes/dbconnect.php');
include('../includes/global.php'); 
include('../includes/functions.php'); 

//initiate page load time routine
$time=getmicrotime(); 

//Create Lookup Array of Client Code / Client Name
	$qry_clients = "select * from int_clnt_clients";
	$result_clients = mysql_query($qry_clients) or die (tdw_mysql_error($qry_clients));
	$arr_clients = array();
	while ( $row_clients = mysql_fetch_array($result_clients) ) 
	{
		$arr_clients[$row_clients["clnt_code"]] = $row_clients["clnt_name"];
	}

//Get the Name/Address information into Memory Table for lookup purposes
$result_nadd_flush = mysql_query("truncate table mry_nfs_nadd") or die (mysql_error());
$result_nadd_populate = mysql_query("insert into mry_nfs_nadd select * from nfs_nadd") or die (mysql_error());

//Create an array of account names and advisor code for lookup.
	$qry_acct_adv = "select nadd_full_account_number, nadd_advisor from mry_nfs_nadd";
	$result_acct_adv = mysql_query($qry_acct_adv) or die (tdw_mysql_error($qry_acct_adv));
	$arr_acct_adv = array();
	while ( $row_acct_adv = mysql_fetch_array($result_acct_adv) ) 
	{
		$arr_acct_adv[strtoupper(trim($row_acct_adv["nadd_full_account_number"]))] = $row_acct_adv["nadd_advisor"];
	}

//select all clients from nfs_nadd
$qry_get_clients = "SELECT
			distinct(nadd_advisor) from mry_nfs_nadd 
			WHERE nadd_branch = 'PDY'
			AND nadd_advisor not like '&%'
			ORDER BY nadd_advisor";
?>
<table>
	<tr>
		<td>Client Code</td>
		<td>Client Name</td>
		<td>RR1</td>
		<td>RR2</td>
		<td>Trades processed with.</td>
	</tr>

<?


//xdebug("qry_get_clients",$qry_get_clients);

$result_get_clients = mysql_query($qry_get_clients) or die (tdw_mysql_error($qry_get_clients));
while ( $row_get_clients = mysql_fetch_array($result_get_clients) )
				{
					
					//echo $row_get_clients["nadd_advisor"]."<br>";
					//get the rr values for the client
					$qry_get_rr = "SELECT max(nadd_rr_owning_rep) as nadd_rr_owning_rep, max(nadd_rr_exec_rep) as nadd_rr_exec_rep
															from mry_nfs_nadd 
															WHERE nadd_branch = 'PDY'
															AND nadd_advisor = '".$row_get_clients["nadd_advisor"]."'  
															ORDER BY nadd_advisor";

					//xdebug("qry_get_rr",$qry_get_rr);
					
					$result_get_rr = mysql_query($qry_get_rr) or die (tdw_mysql_error($qry_get_rr));
					while ( $row_get_rr = mysql_fetch_array($result_get_rr) )
									{
									//echo $row_get_rr["nadd_rr_owning_rep"]."<br>";
									//echo $row_get_rr["nadd_rr_exec_rep"]."<br>";
											//find trades that do not have these rr values and get the rr value
											$str_diff_reps = "";
											$query_trades = "SELECT trad_rr 
																			 FROM mry_comm_rr_trades
																			 WHERE trad_advisor_code = '".$row_get_clients["nadd_advisor"]."'
																			 AND trad_rr != '".$row_get_rr["nadd_rr_owning_rep"]."'
																			 AND trad_is_cancelled != '1' GROUP BY trad_rr";
											//xdebug ("query_trades",$query_trades);
											$result_trades = mysql_query($query_trades) or die(tdw_mysql_error($query_trades));
											$countval = 1;
											while($row_trades = mysql_fetch_array($result_trades))
											{
												//do something
												$str_diff_reps = $row_trades["trad_rr"].",".$str_diff_reps;
												$countval = $countval + 1;
											}
											
											if ($str_diff_reps != '') {
											?>
												<tr>
													<td><?=$row_get_clients["nadd_advisor"]?></td>
													<td><?=$arr_clients[$row_get_clients["nadd_advisor"]]?></td>
													<td><?=$row_get_rr["nadd_rr_owning_rep"]?></td>
													<td><?=$row_get_rr["nadd_rr_exec_rep"]?></td>
													<td><?=$str_diff_reps?></td>
												</tr>
											<?
											}
									}
				}
?>
</table>
<?
/*
	$query_trades = "SELECT * 
									 FROM nfs_trades
									 WHERE trad_run_date = '".$trade_date_to_process."'
									 AND trad_branch = 'PDY'
									 AND trad_cancel_code != '1'";
	xdebug ("query_trades",$query_trades);
	$result_trades = mysql_query($query_trades) or die(tdw_mysql_error($query_trades));
	$countval = 1;
	while($row_trades = mysql_fetch_array($result_trades))
	{
		//do something
		$countval = $countval + 1;
	}
*/

//show page load time
	echo " ". sprintf("%01.7f",((getmicrotime()-$time)/1000))." s."; 						
?>