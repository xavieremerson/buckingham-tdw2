<?

include('../includes/functions.php');
include('../includes/global.php');
include('../includes/dbconnect.php');

//initiate page load time routine
$time=getmicrotime(); 

//get reps from query  on table mry_comm_rr and join on users
$qry_get_reps = "SELECT
									a.ID, a.rr_num, concat(a.Lastname, ', ', a. Firstname) as rep_name, b.trad_rr 
									from users a, mry_comm_rr_trades b
								WHERE a.rr_num = b.trad_rr
								AND b.trad_rr like '0%'
								GROUP BY b.trad_rr
								ORDER BY a.Lastname";
xdebug("qry_get_reps",$qry_get_reps);

$result_get_reps = mysql_query($qry_get_reps) or die (tdw_mysql_error($qry_get_reps));
?>
<select name="test">
<?
while ( $row = mysql_fetch_array($result_get_reps) )
				{
					?> 
					<option value="<?=$row["trad_rr"]."^".$row["ID"]?>"><?=$row["rep_name"]?>&nbsp; &nbsp; (<?=$row["rr_num"]?>)</option>
					<?
				}
?>
</select>
<?

//get reps from query  on table mry_comm_rr and join on users
$qry_get_clients = "SELECT
										distinct(nadd_advisor) from mry_nfs_nadd 
										WHERE nadd_rr_owning_rep = '040'
										AND nadd_branch = 'PDY'
										AND nadd_advisor not like '&%'
										ORDER BY nadd_advisor";
xdebug("qry_get_clients",$qry_get_clients);

$result_get_clients = mysql_query($qry_get_clients) or die (tdw_mysql_error($qry_get_clients));
while ( $row_clients = mysql_fetch_array($result_get_clients) )
				{
					echo $row_clients["nadd_advisor"]."<br>";
				}

















echo " ". sprintf("%01.7f",((getmicrotime()-$time)/1000))." s.";
exit;




?>
