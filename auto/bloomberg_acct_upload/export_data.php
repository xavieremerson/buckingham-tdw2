<?
include('../../includes/functions.php');
include('../../includes/global.php');
include('../../includes/dbconnect.php');

//Create an array of client codes for lookup
$qry_adv_code = "select clnt_code, clnt_alt_code from int_clnt_clients";
$result_adv_code = mysql_query($qry_adv_code) or die (tdw_mysql_error($qry_adv_code));
$arr_adv_code = array();
while ( $row_adv_code = mysql_fetch_array($result_adv_code) ) 
{
	$arr_adv_code[strtoupper(trim($row_adv_code["clnt_code"]))] = strtoupper(trim($row_adv_code["clnt_alt_code"]));
}
												
$output_filename = "INITIAL_FILE_".date('mdY').".txt";
$fp = fopen($output_filename, "w");

//Header
$string = "BUCKCAP   ".date("mdY")."  ". date("His").str_pad("",22," ",1)."FULL NAME & ADDRESS".chr(13); //."<br>"
fputs ($fp, $string);

//Clients List
/*
$query_acct = "SELECT * 
							FROM mry_nfs_nadd 
							where nadd_full_account_number like 'PDS%' or nadd_full_account_number like 'PDZ%'
							order by nadd_full_account_number";
*/
$query_acct = "SELECT * 
							FROM mry_nfs_nadd 
							where nadd_full_account_number like 'PDY%'
							order by nadd_full_account_number ";
							
$result_acct = mysql_query($query_acct) or die(tdw_mysql_error($query_acct));

while($row_acct = mysql_fetch_array($result_acct)) {
   //check if a mapped code for tradeware exists
	 if ($arr_adv_code[strtoupper(trim($row_acct["nadd_advisor"]))] != "") {		
			$string =  str_pad($row_acct["nadd_full_account_number"],9," ",1).
								 str_pad("",3," ",1).
								 "A".
								 str_pad("",26," ",1).
								 "2". //Put 1 for Principal and 2 for Agency
								 str_pad($arr_adv_code[strtoupper(trim($row_acct["nadd_advisor"]))],10," ",1).
								 str_pad("",2," ",1).
								 strtoupper(trim($row_acct["nadd_rr_owning_rep"])).chr(13); // Delete Account should be added later
			fputs ($fp, $string);
			$string =  str_pad($row_acct["nadd_full_account_number"],9," ",1).
								 str_pad("",3," ",1).
								 "D".
								 str_pad(substr(trim($row_acct["nadd_address_line_1"]),0,32),32," ",1).
								 str_pad(substr(trim($row_acct["nadd_address_line_2"]),0,32),32," ",1).
								 str_pad(substr(trim($row_acct["nadd_address_line_3"]),0,32),32," ",1).chr(13);  //."<br>"
			fputs ($fp, $string);
			}
}

			$string =  "***EOF***"; //.chr(13);
			fputs ($fp, $string);


fclose($fp);

//Header("Location: ".$output_filename);
//include($output_filename);

/*
echo "LEFT PAD <br>";
echo str_pad("test",20,"_",0)."<br>";
echo "RIGHT PAD <br>";
echo str_pad("test",20,"_",1);
*/

/*
Header :

Position 1-10 	- “ PERSHING”
Position 11-17	- date
Position 18-23	- time
Position 49-78	- description

Description	retval
FULL NAME & ADDRESS						INITIAL_FILE
NAME AND ADDRESS CHANGE FILE	CHANGE_FILE
NAME AND ADDRESS ADD FILE	ADD_FILE

Detail Lines :

Position 13 - rec_type 
Position 1-9 – account number

rec_type = ‘A’
	Position 41-50	-	customer name
	Position 41-50 -	longname
	Position 41-50 - 	customer_id
	Position 40 - 		acct_type
	Position 53-55 - 	owner_rrnum
	Position 131 -		delete account

rec_type = ‘B’

rec_type = ‘C’
	Position 24-33 -	Phone Number

rec_type = ‘D’
	Position 14-45 -	Long Name
	Position 46-77 -	Address1
	Position 78-109 -	Address2

rec_type = ‘F’
	Position 71-74 – firm clearing number

*/
?>
