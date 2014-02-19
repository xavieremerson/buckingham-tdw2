<?		
include('../../includes/dbconnect.php');				
include('../../includes/functions.php');				

$filetoparse = 'SDN.CSV';
//CHECK FOR VALID FILENAME (trda.csv) A:TRADES
print "<li>SDN FILE ".$filetoparse." uploaded successfully!</li>";
										

$result_truncate = mysql_query("truncate table ofac_sdn_list") or die(tdw_mysql_error("truncate table ofac_sdn_list"));
//PARSING SDN FILE
if ($filetoparse == 'SDN.CSV') {         

		echo "<li>Parsing trade file ... ".$filetoparse."</li>";

		$row = 0;
		$handle = fopen("d:\\tdw\\tdw\\auto\\ofac\\sdallw32\\".$filetoparse, "r");
		while ($data = fgetcsv($handle, 2000, ",")) {
			 $num = count($data);
			 if ($data[1] != '') {
			 $insertquery = "INSERT INTO ofac_sdn_list
			 														( ent_num,
																		SDN_Name,
																		SDN_Type,
																		Program,
																		Title,
																		Call_Sign,
																		Vess_type,
																		Tonnage,
																		GRT,
																		Vess_flag,
																		Vess_owner,
																		Remarks ) 
																	VALUES (
																	'".
																	$data[0]."', '".
																	str_replace ("'", "\'", $data[1])."', '".
																	str_replace ("'", "\'", $data[2])."', '".
																	str_replace ("'", "\'", $data[3])."', '".
																	str_replace ("'", "\'", $data[4])."', '".
																	str_replace ("'", "\'", $data[5])."', '".
																	str_replace ("'", "\'", $data[6])."', '".
																	str_replace ("'", "\'", $data[7])."', '".
																	str_replace ("'", "\'", $data[8])."', '".
																	str_replace ("'", "\'", $data[9])."', '".
																	str_replace ("'", "\'", $data[10])."', '".
																	str_replace ("'", "\'", $data[11])."');";
									$result = mysql_query($insertquery) or die(tdw_mysql_error($insertquery));
									echo "Added : ". $data[1]."\n<br>";	
									ob_flush();
									flush();	
					}								
   
			 $row++;
		}

}

$filetoparse = 'ADD.CSV';
//CHECK FOR VALID FILENAME (trda.csv) A:TRADES
print "<li>ADD FILE ".$filetoparse." uploaded successfully!</li>";
										
$result_truncate = mysql_query("truncate table ofac_add_list") or die(tdw_mysql_error("truncate table ofac_add_list"));
//PARSING SDN FILE
if ($filetoparse == 'ADD.CSV') {         

		echo "<li>Parsing trade file ... ".$filetoparse."</li>";

		$row = 0;
		$handle = fopen("d:\\tdw\\tdw\\auto\\ofac\\sdallw32\\".$filetoparse, "r");
		while ($data = fgetcsv($handle, 2000, ",")) {
			 $num = count($data);
			 if ($data[1] != '') {
			 $insertquery = "INSERT INTO ofac_add_list 
			 														( ent_num,
																	  add_num,
																		address,
																		city,
																		country,
																		add_remarks ) 
																	VALUES (
																	'".
																	$data[0]."', '".
																	str_replace ("'", "\'", $data[1])."', '".
																	str_replace ("'", "\'", $data[2])."', '".
																	str_replace ("'", "\'", $data[3])."', '".
																	str_replace ("'", "\'", $data[4])."', '".
																	str_replace ("'", "\'", $data[5])."');";
									$result = mysql_query($insertquery) or die(tdw_mysql_error($insertquery));
									echo "Added : ". $data[1]."\n<br>";	
									ob_flush();
									flush();	
					}								
   
			 $row++;
		}

}



$filetoparse = 'ALT.CSV';
//CHECK FOR VALID FILENAME (trda.csv) A:TRADES
print "<li>ADD FILE ".$filetoparse." uploaded successfully!</li>";
										
$result_truncate = mysql_query("truncate table ofac_alt_list") or die(tdw_mysql_error("truncate table ofac_alt_list"));
//PARSING SDN FILE
if ($filetoparse == 'ALT.CSV') {         

		echo "<li>Parsing trade file ... ".$filetoparse."</li>";

		$row = 0;
		$handle = fopen("d:\\tdw\\tdw\\auto\\ofac\\sdallw32\\".$filetoparse, "r");
		while ($data = fgetcsv($handle, 2000, ",")) {
			 $num = count($data);
			 if ($data[1] != '') {
			 $insertquery = "INSERT INTO ofac_alt_list 
			 														( ent_num,
																	  alt_num,
																		alt_type,
																		alt_name,
																		alt_remarks) 
																	VALUES (
																	'".
																	$data[0]."', '".
																	str_replace ("'", "\'", $data[1])."', '".
																	str_replace ("'", "\'", $data[2])."', '".
																	str_replace ("'", "\'", $data[3])."', '".
																	str_replace ("'", "\'", $data[4])."');";
									$result = mysql_query($insertquery) or die(tdw_mysql_error($insertquery));
									echo "Added : ". $data[1]."\n<br>";	
									ob_flush();
									flush();	
					}								
   
			 $row++;
		}

}
?>