<?

include('includes/functions.php');
include('includes/global.php');
include('includes/dbconnect.php');

//flush the clients table
$result_flushtable = mysql_query("truncate table mry_dos_commission") or die (mysql_error());

//R:\CommMgr


?>


<?
/*function get_dbf_header($dbfname) {
   $fdbf = fopen($dbfname,'r');

   $dbfhdrarr = array();
   $buff32 = array();
   $i = 1;
   $goon = true;

   while ($goon) {
     if (!feof($fdbf)) {
         $buff32 = fread($fdbf,32);
         if ($i > 1) {
           if (substr($buff32,0,1) == chr(13)) {
               $goon = false;
           } else {
               $pos = strpos(substr($buff32,0,10),chr(0));
               $pos = ($pos == 0?10:$pos);

               $fieldname = substr($buff32,0,$pos);
               $fieldtype = substr($buff32,11,1);
               $fieldlen = ord(substr($buff32,16,1));
               $fielddec = ord(substr($buff32,17,1));

array_push($dbfhdrarr, array($fieldname,$fieldtype,$fieldlen,$fielddec));

           }
         }
         $i++;
     } else {
         $goon = false;
     }
   }

   fclose($fdbf);
   return($dbfhdrarr);
}

$arr = get_dbf_header('\\\\bucknypdc3\\buck\\CommMgr\\TRADING.DBF');
print_r($arr);
*/
?> 


<?php

//$datefrom = previous_business_day();
//$dateto   = previous_business_day();

$datefrom = '2006-03-21';
$dateto   = '2006-03-21';

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
//$db_file = '\\\\bucknypdc3\\buck\\CommMgr\\TRADING.DBF';
$db_file = '\\\\bucknypdc3\\buck\\CommMgr\\COMMSION.DBF';

# open dbf file for reading and writing
$id = @ dbase_open ($db_file, READ_ONLY)
   or die ("Could not open dbf file <i>$db_file</i>."); 

# find the number of fields (columns) and rows in the dbf file
$num_fields = dbase_numfields ($id);
$num_rows   = dbase_numrecords($id);

print "dbf file <i>$db_file</i> contains $num_rows row(s) with $num_fields field(s) in each row.\n";

# Loop through the entries in the dbf file
for ($i=1; $i <= $num_rows; $i++) {
   print "\nRow $i of the database contains this information:<blockquote>";
   $arr_row = dbase_get_record_with_names ($id,$i);

	 foreach ($arr_row as $colname => $colval) {
			 if ($colname == 'CUST_CODE') {
				$custcode = $colval;
				echo $custcode. "<br>";
			 }
			 if ($colname == 'TRADE_DATE') {
				$tradedate = substr($colval,0,4)."-".substr($colval,4,2)."-".substr($colval,6,2);
			 }
			 if ($colname == 'COMM_AMT') {
				$commamt = $colval;
			 }
			 if ($colname == 'deleted') {
				$deleted = $colval;
					if ($deleted == 0 && $tradedate == $datefrom)  {
						echo "insert this record<br>";
						
						$qry_insert_dos =  "INSERT INTO mry_dos_commission (clnt_trade_date,clnt_code,clnt_commission) 
																	VALUES (
																	'".$tradedate."', 
																	'".$custcode."', 
																	'".$commamt."'
																	)"; 
						
						
						echo $qry_insert_dos . "<br>";
						$result_insert_dos = mysql_query($qry_insert_dos) or die (tdw_mysql_error($qry_insert_dos));
						
					} else {
						echo "ignore this record<br>";
					}
			 }
		}

	 //print_r (dbase_get_record_with_names ($id,$i));
   print "</blockquote>";
} 

# close the dbf file
dbase_close($id);
?>
