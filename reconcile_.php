<?
  include('includes/dbconnect.php');
  include('includes/global.php');
  include('includes/functions.php');

												//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++
												$time_alpha=getmicrotime();
												//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++

												# Constants for dbf field types
												define ('BOOLEAN_FIELD',   'L');
												define ('CHARACTER_FIELD', 'C');
												define ('DATE_FIELD',      'D');
												define ('NUMBER_FIELD',    'N');
												
												# Constants for dbf file open modes
												define ('READ_ONLY',  '0');
												define ('WRITE_ONLY', '1');
												define ('READ_WRITE', '2');
												
												
/*												$db_file = '\\\\bucksnapNY\\SHARE1\\BRG\\Buck\\CommMgr\\COMMSION.DBF';
												
												\\\\bucksnapNY\\SHARE1\\BRG\\Buck\\CommMgr
*/												
												copy ("\\\\bucksnapNY\\SHARE1\\BRG\\Buck\\CommMgr\\COMMSION.DBF","D:/tdw/tdw/COMMSION.DBF");
                        //echo "COMMSION.DBF was last modified: " . date ("F d Y H:i:s.", filemtime("D:\\tdw\\tdw\\COMMSION.DBF"));
												//echo sprintf("%01.2f",((getmicrotime()-$time_alpha)/1000));
												# Path to dbf file
												$db_file = 'D:/tdw/tdw/COMMSION.DBF';

												# open dbf file for reading and writing
												$id = @ dbase_open ($db_file, READ_ONLY)
													 or die ("Could not open dbf file <i>$db_file</i>."); 
												
												# find the number of fields (columns) and rows in the dbf file
												$num_fields = dbase_numfields ($id);
												$num_rows   = dbase_numrecords($id);
												
												
												$date_start = "2009-10-02";
												$date_end = "2009-10-02";
												
												# Loop through the entries in the dbf file
												for ($i=1; $i <= $num_rows; $i++) {
													 $arr_row = dbase_get_record_with_names ($id,$i);
												
													 foreach ($arr_row as $colname => $colval) {
															 if ($colname == 'CUST_CODE') {
																$custcode = $colval;
															 }
															 if ($colname == 'TRADE_DATE') {
																$tradedate = substr($colval,0,4)."-".substr($colval,4,2)."-".substr($colval,6,2);
															 }
															 if ($colname == 'COMM_AMT') {
																$commamt = $colval;
															 }
															 if ($colname == 'deleted') {
																$deleted = $colval;
																	if ($deleted == 0 && $tradedate >= $date_start && $tradedate <= $date_end)  {
																		echo $tradedate."         ".$custcode."        ".$commamt."<br>";
																	} else {
																		//echo "ignore this record<br>";
																	}
															 }
														}
												} 
												
												# close the dbf file
												dbase_close($id);
												
												//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++
												echo sprintf("%01.2f",((getmicrotime()-$time_alpha)/1000));
												//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++

												?>
