<?						
//CHECK FOR VALID FILENAME (trda.csv) A:TRADES
print "<li>SDN FILE ".$filetoparse." uploaded successfully!</li>";
										
//PARSE UPLOADED FILE AND INSERT INTO STAGING AREA IN DATABASE

$filetoparse 'SDN.CSV';

//PARSING TRADE FILE
if ($filetoparse == 'SDN.CSV') {         

		echo "<li>Parsing trade file ... ".$filetoparse."</li>";

		$row = 1;
		$handle = fopen("d:\\tdw\\tdw\\auto\\ofac\\sdallw32\\".$filetoparse, "r");
		while ($data = fgetcsv($handle, 2000, ",")) {
			 $num = count($data);
			 echo "$num fields in line $row: --------";
			 $row++;

}

/*		
			 $insertquery = "insert into Trades_stage_m_trade values 
										 (".
											"'".''."',".
											"'".$data[1]."',".
											"'".$data[2]."',".
											"'".$data[3]."',".
											"'".$data[4]."',".
											"'".process_price($data[5])."',".
											"'".$data[6]."',".
											"'".format_date_mdy_to_ymd($data[7])."',".
											"'".$data[8]."',".
											"'".format_date_mdy_to_ymd($data[10])."',".
											"'".str_replace("'",'"',$data[11])."',".
											"'".$data[38]."')";   
																					
																					
																					//"'".format_date_mdy_to_ymd($data[13])."')";

																	
													 $insertquery = str_replace ("\\", "\\\\", $insertquery);
													 //echo $insertquery."<BR>";
													 $result = mysql_query($insertquery) or die(mysql_error());
													 //echo("Record Inserted!");										

						
												} //END while
												
												echo "<li>" . ($row - 1) . " records inserted from ". $filetoparse . " into staging tables of database.</li>";
											
										fclose($handle);
											
										echo "<li>Trades from file ".$filetoparse." uploaded to database successfully!<BR>";
											//END PARSING TRADE FILE
											} else {
											echo "UNEXPECTED ERROR! Please inform Technical Support.";
											}
										//END PARSE UPLOAD
									
									//FILE NOT COPIED TO DESTINATION
									} else {
										print "<li>There was an error uploading the trades file to the system! Please contact Technical Support.</li>";
										$proceed = 0;
										//SEND EMAIL TO ADMINISTRATOR WITH THIS ERROR
										sys_mail($_app_administrator,"Trade File upload error","There was an unknown error in uploading trade file from Moxy into the TDW","Comp Header"); 
										}
								//END WHILE
								}
								echo "</ul>";
								
								//END ELSE (FILENAME CHECK)
								}
							
							//END FILE UPLOAD OPTION	  
							} elseif ($to_upload == 2) {
							
							$proceed = 1;
							
							echo '<a class="csys_regtext_bold">Status:</a>';
							echo '<ul class="csys_regtext">';
							
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</td>
</tr>
*/

?>