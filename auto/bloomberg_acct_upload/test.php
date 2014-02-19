<?
include('../../includes/include.txt');
include('../../includes/global.php');
include('../../includes/functions.php');
include('../../includes/dbconnect.php');

//Create account files
//*************************************************************************************************************
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
								 " ". //Put 1 for Principal and 2 for Agency
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
//*************************************************************************************************************
//Create config script to be used by batch file.
//*************************************************************************************************************
$output_script_filename = "tradeware_ftp_script.txt";
$fp = fopen($output_script_filename, "w");
//Put commands in the config file
fputs ($fp, "cd incoming".chr(13));
fputs ($fp, "put ".$tdw_local_location."auto\\tradeware\\".$output_filename.chr(13));
fputs ($fp, "pwd".chr(13));
fputs ($fp, "ls".chr(13));
fclose($fp);

//*************************************************************************************************************
//Run batch file to upload file


//Do error checking to confirm file transmission, by downloading the uploaded file to an alternate location
//and confirming that files are identical







?>