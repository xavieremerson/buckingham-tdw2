<title>Edit Client</title>
<script language="Javascript" SRC="../includes/javascript.js"></script>
<script language="JavaScript" src="includes/js/popup.js"></script>
<link rel="stylesheet" type="text/css" href="includes/styles.css" />
<script language="JavaScript" type="text/javascript">
function showhidepayout(divid) { 
	if (document.getElementById) { // DOM3 = IE5, NS6 Generally this is what it is

		if (document.getElementById(divid).style.getAttribute("visibility") == "" || document.getElementById(divid).style.getAttribute("visibility") == "hidden" ) {
		document.getElementById(divid).style.visibility = 'visible'; 
		document.getElementById(divid).style.display = 'block'; 
		} else {
		document.getElementById(divid).style.visibility = 'hidden'; 
		document.getElementById(divid).style.display = 'none'; 
		}		

	} 
	else { 
			alert("Browser Version not compatable!");
	} 
} 
</script>

<? 
include('includes/dbconnect.php');
include('includes/global.php');
include('includes/functions.php');

function get_fullname_for_initials ($Initials) {
	$initial_val = trim($Initials);
	if ($initial_val == '') {
	return '';
	} else {
	$user_fullname = db_single_val("SELECT Fullname as single_val FROM users WHERE Initials = '".$initial_val."'");   
	return $user_fullname . " [".$initial_val."]";
	}
}

$output_filename = "Client_Details.csv";
$fp = fopen($exportlocation.$output_filename, "w");

$string = "\"Client Name\",\"Client Code\",\"Default Payout\",\"Rolling 12 Months\",\"Rep 1\",\"Rep 1 Payout\",\"Rep 2\",\"Rep 2 Payout"."\"".chr(13); 

fputs ($fp, $string);

$qry_clnt_info = "SELECT 
										a.clnt_auto_id, a.clnt_code, a.clnt_alt_code, a.clnt_name, a.clnt_rr1, a.clnt_rr2, 
										a.clnt_trader, a.clnt_isactive,
										b.clnt_default_payout, b.clnt_special_payout_rate, b.clnt_start_month, b.clnt_default_n_months  
									FROM int_clnt_clients a, int_clnt_payout_rate b
									WHERE a.clnt_auto_id = b.clnt_auto_id
									AND a.clnt_isactive = 1
									AND length(trim(a.clnt_rr1)) > 0
									AND a.clnt_rr1 in (select Initials from Users where length(Initials) = 2)
									ORDER BY a.clnt_rr1, a.clnt_rr2, a.clnt_name ";
$result_clnt_info = mysql_query($qry_clnt_info) or die (tdw_mysql_error($qry_clnt_info));

while ( $row_clnt_info = mysql_fetch_array($result_clnt_info) ) 
{

			if ($row_clnt_info["clnt_default_payout"]==1) {
				$str_default_payout = "Yes";
			} else {
				$str_default_payout = "No";
			}
 	 
			if ($row_clnt_info["clnt_default_n_months"]==1) {
				$str_default_n_months = "Yes";
			} else {
				$str_default_n_months = "No";
			}
			
			
			$arr_payouts = explode("#",$row_clnt_info["clnt_special_payout_rate"]);
			//show_array($arr_payouts);

					$str_rep_id_1 = "";
					$str_rep_payout_1 = "";
					$str_rep_id_2 = "";
					$str_rep_payout_2 = "";


			foreach($arr_payouts as $k=>$v) {
				if ($k ==0) {
					$arr_payout_val = explode("^",$v);
					$str_rep_id_1 = $arr_payout_val[0];
					$str_rep_payout_1 = $arr_payout_val[1];
				} else {
					$arr_payout_val = explode("^",$v);
					$str_rep_id_2 = $arr_payout_val[0];
					$str_rep_payout_2 = $arr_payout_val[1];
				}
			}
			
			
			$string = "\"".$row_clnt_info["clnt_name"].
			       "\",\"".$row_clnt_info["clnt_code"].
			       "\",\"".$str_default_payout.
			       "\",\"".$str_default_n_months.
			       "\",\"".get_fullname_for_initials($row_clnt_info["clnt_rr1"]).
			       "\",\"".$str_rep_payout_1.
			       "\",\"".get_fullname_for_initials($row_clnt_info["clnt_rr2"]).
			       "\",\"".$str_rep_payout_2.
						 "\"".chr(13); 
			//echo $string;
			fputs ($fp, $string);
}


fclose($fp);

Header("Location: data/exports/".$output_filename);

exit; 	
?>