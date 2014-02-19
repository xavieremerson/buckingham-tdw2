<?

// Make sure the drive which holds the Commissions Manager backjup datafiles is appropriately mapped.
//R:\CommMgr

//include('../includes/functions.php');
//include('../includes/global.php');
//include('../includes/dbconnect.php');

//flush the clients table
$result_flushtable = mysql_query("truncate table int_clnt_clients") or die (mysql_error());

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
	 foreach ($arr_row as $colname => $colval) {
			 if ($colname == 'CUST_CODE') {
				$custcode = $colval;
				echo $custcode. "<br>";
			 }
			 if ($colname == 'CUST_NAME') {
				$custname = $colval;
				$custname = str_replace("'","\'",$custname);
			 }
			 if ($colname == 'deleted') {
				$deleted = $colval;
					if ($deleted == 0)  {
						//echo "insert this record<br>";
						
						$qry_insertclient = "INSERT INTO int_clnt_clients (clnt_code,clnt_name) VALUES ('".$custcode."','".$custname."')";
						//echo $qry_insertclient . "<br>";
						$result_insertclient = mysql_query($qry_insertclient) or die (tdw_mysql_error($qry_insertclient));
						
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
