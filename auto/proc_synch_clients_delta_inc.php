<?

// Make sure the drive which holds the Commissions Manager backup datafiles is appropriately mapped.
//R:\CommMgr

//include('../includes/functions.php');
//include('../includes/global.php');
//include('../includes/dbconnect.php');

//flush the clients table
//$result_flushtable = mysql_query("truncate table int_clnt_clients") or die (mysql_error());

//Create an array of client codes for lookup so only new clients are inserted into the client master
$qry_adv_code = "select clnt_code from int_clnt_clients";
$result_adv_code = mysql_query($qry_adv_code) or die (tdw_mysql_error($qry_adv_code));
$arr_adv_code = array();
$count_adv_code = 0;
while ( $row_adv_code = mysql_fetch_array($result_adv_code) ) 
{
	$arr_adv_code[$count_adv_code] = strtoupper(trim($row_adv_code["clnt_code"]));
	$count_adv_code = $count_adv_code + 1;
}
//show_array($arr_adv_code);

# Constants for dbf field types
define ('BOOLEAN_FIELD',   'L');
define ('CHARACTER_FIELD', 'C');
define ('DATE_FIELD',      'D');
define ('NUMBER_FIELD',    'N');

# Constants for dbf file open modes
define ('READ_ONLY',  '0');
define ('WRITE_ONLY', '1');
define ('READ_WRITE', '2');

# Path to dbf file
//Using UNC Path to access the commissions manager backup file
//This synchup mechanism will be become obsolete when the DOS System is removed. 
$db_file = '\\\\bucksnapNY\\SHARE1\\BRG\\Buck\\CommMgr\\BUCKCUST.DBF';

# open dbf file for reading and writing
$id = @ dbase_open ($db_file, READ_ONLY)
   or die (err_email("Could not open dbf file <i>$db_file</i>.","Could not open dbf file <i>$db_file</i>.")); 

# find the number of fields (columns) and rows in the dbf file
$num_fields = dbase_numfields ($id);
$num_rows   = dbase_numrecords($id);

//print "dbf file <i>$db_file</i> contains $num_rows row(s) with $num_fields field(s) in each row.\n";

# Loop through the entries in the dbf file
for ($i=1; $i <= $num_rows; $i++) {
   //print "\nRow $i of the database contains this information:<blockquote>";
   $arr_row = dbase_get_record_with_names ($id,$i);
	 //show_array($arr_row);
	 foreach ($arr_row as $colname => $colval) {
			 if ($colname == 'CUST_CODE') {
				$custcode = $colval;
				//echo $custcode. "<br>";
			 }
			 if ($colname == 'CUST_NAME') {
				$custname = $colval;
				$custname = str_replace("'","\'",$custname);
			 }
			 if ($colname == 'SALES_REPS') {
			 	$salesreps = $colval;
				$splitval_salesreps = explode(",",$salesreps);
				$clnt_rr1 = $splitval_salesreps[0];
				
				//HD = Hard Dollars, must be BRG (DOS cannot handle 3 sharacters)
				if ($clnt_rr1 == "HD") { $clnt_rr1 = "BRG";  }
				
				$clnt_rr2 = $splitval_salesreps[1];
				
				if ($clnt_rr2 == "HD") { $clnt_rr2 = "BRG";  }
				
				$clnt_trader = $splitval_salesreps[2];
				
			 }
			 if ($colname == 'deleted') {
				$deleted = $colval;
					if ($deleted == 0)  {
					  //insert only if the code does not already exist in the client master.
						if (in_array($custcode,$arr_adv_code)) {
							//Do an update of record
							//echo "Client w/ code ". $custcode . " exists in the TDW client master.<br>"; 
							
							
							if (trim($clnt_rr1) != '') {
									$qry_updateclient = "UPDATE int_clnt_clients 
																				SET clnt_name = '".$custname."',
																						clnt_rr1 = '".trim($clnt_rr1)."',
																						clnt_rr2 = '".trim($clnt_rr2)."',
																						clnt_trader = '".trim($clnt_trader)."' WHERE clnt_code = '".$custcode."'";
									//xdebug("Update query",$qry_updateclient);
									$result_updateclient = mysql_query($qry_updateclient) or die (tdw_mysql_error($qry_updateclient));
							} else {
									echo "Client w/ code ". $custcode . " has no reps assigned.<br>"; 
							}

						} else {
							$qry_insertclient = "INSERT INTO int_clnt_clients 
																		( clnt_auto_id , 
																		  clnt_code , 
																			clnt_alt_code , 
																			clnt_name , 
																			clnt_rr1 , 
																			clnt_rr2 , 
																			clnt_trader , 
																			clnt_isactive ) 
																		VALUES (
																		NULL , 
																		'".$custcode."', 
																		'INACTIVE', 
																		'".$custname."', 
																		'".trim($clnt_rr1)."', 
																		'".trim($clnt_rr2)."', 
																		'".trim($clnt_trader)."', 
																		'1')";


							//xdebug("Insert query",$qry_insertclient);
							$result_insertclient = mysql_query($qry_insertclient) or die (tdw_mysql_error($qry_insertclient));
							
							//******************************************************************************************
							//Get the auto_id from the table and insert a new row in the payout rate table
							
							$new_auto_id = db_single_val("select clnt_auto_id as single_val from int_clnt_clients where clnt_code = '".$custcode."'");
							
							$qry_insertclientpayout = "INSERT INTO int_clnt_payout_rate 
																		( clnt_auto_id,
																		  clnt_default_payout,
																			clnt_special_payout_rate,
																			clnt_start_month,
																			clnt_default_n_months,
																			clnt_name,
																			clnt_isactive
																		) 
																		VALUES (
																		'".$new_auto_id."',  
																		'1', 
																		'', 
																		'', 
																		'1', 
																		'".$custname."', 
																		'1')";

							//xdebug("Insert query payout",$qry_insertclientpayout);
							$result_insertclientpayout = mysql_query($qry_insertclientpayout) or die (tdw_mysql_error($qry_insertclientpayout));
							//******************************************************************************************
							
						}
					} else {
						//echo "ignore this record<br>";
					}
			 }
		}

	 //print_r (dbase_get_record_with_names ($id,$i));
   //print "</blockquote>";
} 

# close the dbf file
dbase_close($id);
echo "Clients successfully synched up b/w DOS and TDW";
?>