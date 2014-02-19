<?
include('includes/functions.php');
include('includes/dbconnect.php');
include('includes/global.php');
require_once 'Spreadsheet/Excel/Writer.php';

include('pay_payout_functions.php');

 
//Create Lookup Array of Client Code / Client Name
$qry_clients = "select * from int_clnt_clients";
$result_clients = mysql_query($qry_clients) or die (tdw_mysql_error($qry_clients));
$arr_clients = array();
while ( $row_clients = mysql_fetch_array($result_clients) ) 
{
	$arr_clients[$row_clients["clnt_code"]] = $row_clients["clnt_name"]; 
	$clnt = $row_clients["clnt_code"];
	$clnt_name = $row_clients["clnt_name"];
			//get rr_num for client
		$qry = "select trim(clnt_rr1) as rr1, trim(clnt_rr2) as rr2 from int_clnt_clients where clnt_code = '".$clnt."'";
		$result = mysql_query($qry) or die (tdw_mysql_error($qry));
		while($row = mysql_fetch_array($result)) {
			$rr1 = $row["rr1"];
			$rr2 = $row["rr2"];	
			if ($rr1 != '' OR $rr2 != '') {
				if ($rr2 == '') {
					$tmp_rr_num = get_rr_num (get_userid_for_initials ($rr1));
					//xdebug("clnt/rr1/rr2/rep#", $clnt_name."/".$rr1."/".$rr2."/".$tmp_rr_num);
				} else {
					$tmp_rr_num = get_shared_rr_num ($rr1, $rr2);
					//xdebug("clnt/rr1/rr2/rep#", $clnt_name."/".$rr1."/".$rr2."/".$tmp_rr_num);
				}
			}
		}
		
		echo $row_clients["clnt_name"]."^".$tmp_rr_num."^".get_repname_by_rr_num($tmp_rr_num)."<br>";

}
?>