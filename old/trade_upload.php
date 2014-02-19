<?php

  include('top.php');
	 
	include('includes/functions.php'); 
	
	//FOR DATES OTHER THAN PREVIOUS BUSINESS DAY, CREATE MECHANISM TO HANDLE IT.
	$trade_date_to_process = previous_business_day();

	//$trade_date_to_process = '2004-02-20';
	
  ////
	// Funtion to check if uploads have been performed for the previous trade date.
		


?>
<tr>
	<td valign="top">
	<BR>
		<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
			<tr>
				<td>
					<table width="100%" bgcolor="#FFFFFF" cellspacing="4" cellpadding="4" border="0">
						<tr>
							<td>
							<a class="csys_regtext"><BR>Trades to be imported for <B>TRADE DATE: <?=format_date_ymd_to_mdy(previous_business_day())?></B></a><BR><BR><BR>
							<a class="csys_regtext_bold">Steps:</a>
							</td>
						</tr>
						<tr>
							<td>
							<ul class="csys_regtext">
							<li>Export results from <b>TVDCOMPL.trd</b> file using Moxy Client, to Excel.</li>
							<li>Save the exported data as a CSV File (Comma Separated Values). Name the file "<b>trda.csv</b>".</li>
							<li>Save the Trade Allocation Report as Excel (csv) file. Name the file "<b>trdb.csv</b></li>
              <li>When you have both files <b>trda.csv</b> and <b>trdb.csv</b> created/saved, proceed to upload using the form below.</li>
              <li>For detailed instructions, please <a href="help_TradeUpload.pdf" target="_blank">CLICK HERE.</a></li>							
							</ul>		
							</td>
						</tr>
  					<tr>
							<td>
							<?
							//FORM SUBMITTED FOR FILE UPLOAD
							if ($to_upload == 1) {
							
							$proceed = 1;
							
							echo '<a class="csys_regtext_bold">Status:</a>';
							echo '<ul class="csys_regtext">';
							
							//GET FILE NAME
							$arr_fileinfo = $_FILES["file"];
							$arr_fileinfo = $arr_fileinfo["name"];
							$filetoparse = $arr_fileinfo[0];
							echo "<li>File to upload : <u>". $filetoparse ."</u></li>";          
							
							//GET FILE TYPE
							$arr_fileinfo = $_FILES["file"];
							$arr_fileinfo = $arr_fileinfo["type"];
							$filetype = $arr_fileinfo[0];
							echo "<li>File type : <u>". $filetype."</u></li>";
							
							//UPLOAD DESTINATION
							$path = "/var/www/html/compliance/data/trades";
							$where_to_go = $path."/"; 
							
							//CHECK FOR VALID FILENAME (trda.csv) A:TRADES
							if ($filetoparse != 'trda.csv') {
								echo "<li>ERROR! Filename <u>". $filetoparse."</u> is not allowed. Filename expected is trda.csv.  Please check the csv filename and try again.</li>";
								unset($to_upload);
								} else {
								//FILENAME CHECK SUCCESSFUL
								while (list ($chave, $valor) = each ($_FILES['file']['tmp_name'])) {
								
									//FILE SUCCESSFULLY COPIED TO DESTINATION
									if (move_uploaded_file($_FILES['file']['tmp_name'][$chave], $where_to_go . $_FILES['file']['name'][$chave])) 
									{
										print "<li>Trade File ".$filetoparse." uploaded successfully!</li>";
										//PARSE UPLOADED FILE AND INSERT INTO STAGING AREA IN DATABASE

										//PARSING TRADE FILE
										if ($filetoparse == 'trda.csv') {         

												echo "<li>Parsing trade file ... ".$filetoparse."</li>";
		
												$row = 1;
												$handle = fopen($where_to_go.$filetoparse, "r");
												while ($data = fgetcsv($handle, 2000, ",")) {
													 $num = count($data);
													//echo "<p> $num fields in line $row: --------";
													 $row++;
												
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
										sys_mail($_app_administrator,"Trade File upload error","There was an unknown error in uploading trade file from Moxy into the CompSys","Comp Header"); 
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
							
							//GET FILE NAME
							$arr_fileinfo = $_FILES["file"];
							$arr_fileinfo = $arr_fileinfo["name"];
							$filetoparse = $arr_fileinfo[0];
							echo "<li>File to upload : <b>". $filetoparse ."</b></li>";          
							
							//GET FILE TYPE
							$arr_fileinfo = $_FILES["file"];
							$arr_fileinfo = $arr_fileinfo["type"];
							$filetype = $arr_fileinfo[0];
							echo "<li>File type : <b>". $filetype."</b></li>";
							
							//UPLOAD DESTINATION
							$path = "/var/www/html/compliance/data/trades";
							$where_to_go = $path."/"; 
							
							//CHECK FOR VALID FILENAMES (trdb.csv) B:ALLOCATION
							if ($filetoparse != 'trdb.csv' ) {
								echo "<li>ERROR! Filename <u>". $filetoparse."</u> is not allowed. Expected filename is trdb.csv.  Please check the csv filename and try again.</li>";
								} else {
								//FILENAME CHECK SUCCESSFUL
								while (list ($chave, $valor) = each ($_FILES['file']['tmp_name'])) {
								
									//FILE SUCCESSFULLY COPIED TO DESTINATION
									if (move_uploaded_file($_FILES['file']['tmp_name'][$chave], $where_to_go . $_FILES['file']['name'][$chave])) 
									{
										print "<li>Trade File ".$filetoparse." uploaded successfully!</li>";
										//PARSE UPLOADED FILE AND INSERT INTO STAGING AREA IN DATABASE

										//PARSING ALLOCATION FILE
										if ($filetoparse == 'trdb.csv') {   

												echo "<li>Parsing trade file ... ".$filetoparse."</li>";
		
												$row = 1;
												$handle = fopen($where_to_go.$filetoparse, "r");
												while ($data = fgetcsv($handle, 2000, ",")) {
													 $num = count($data);
													  //if ($data[1] == 'Complete') {
														//	echo "<p> $num fields in line $row: --------";
													  //	}
													 $row++;
												
													 $insertquery = "insert into Trades_stage_m_alloc values 
																				 (".
																					"'".''."',".
																					"'".$data[0]."',".
																					"'".$data[1]."',".
																					"'".$data[2]."',".
																					"'".$data[3]."',".
																					"'".$data[4]."',".
																					"'".$data[5]."',".
																					"'".process_price($data[6])."',".
																					"'".$data[7]."',".
																					"'".$data[8]."',".
																					"'".$data[9]."',".
																					"'".$data[10]."',".
																					"'".$data[11]."',".
																					"'".$data[12]."',".
																					"'".format_date_mdy_to_ymd($data[13])."')";
																	
													 $insertquery = str_replace ("\\", "\\\\", $insertquery);
											
													 if ($data[1] == 'Complete') {
															//echo $insertquery."<BR>";
															$result = mysql_query($insertquery) or die(mysql_error());
															//echo("Record Inserted!");										
															}
				
												}
											
										fclose($handle);
																						
									  echo "<li>" . ($row - 1) . " rows inserted from ". $filetoparse . " into staging tables of database.</li>";

                    //////
										//// 
										//   THIS SECTION MOVES TRADES FROM STAGING TO MAIN AREA
										
										
											//just in case data exists, delete trades for process date from tsmt.
											$_query_a = "delete from Trades_m where trdm_trade_date = '".$trade_date_to_process."'";
											//echo "<BR><BR>=> _query_a ".$_query_a;
											$_result_a = mysql_query($_query_a) or die(mysql_error());
											
											//move all trades from tsmt to trdm where acct is not null
											$_query_b = "insert into Trades_m (trdm_order_id,
																												trdm_account_number,
																												trdm_buy_sell,
																												trdm_quantity,
																												trdm_symbol,
																												trdm_sec_description,
																												trdm_price,
																												trdm_trade_date,
																												trdm_settle_date,
																												trdm_trade_time)
																							   select tsmt_order_id,
																												tsmt_account_number,
																							          tsmt_buy_sell,
																												ROUND(tsmt_quantity),
																												tsmt_symbol,
																												tsmt_sec_description,
																												ROUND(tsmt_price,2),
																												tsmt_trade_date,
																												tsmt_settle_date,
																												tsmt_trade_time					
																									 from Trades_stage_m_trade
 																								  where tsmt_account_number != ''
																									  and tsmt_trade_date = '".$trade_date_to_process."'";
											
											//echo "<BR><BR>=> _query_b ".$_query_b;
											$_result_b = mysql_query($_query_b) or die(mysql_error());
											
											//move all trades from tsma to trdm where acct. in tsmt is null, join by order_id
											$_query_c = "insert into Trades_m (trdm_order_id,
																												trdm_account_number,
																												trdm_buy_sell,
																												trdm_quantity,
																												trdm_symbol,
																												trdm_price,
																												trdm_trade_date)
																							   select b.tsmt_order_id,
																								        a.tsma_account_number,
																												a.tsma_buy_sell,
																												ROUND(a.tsma_quantity),
																												a.tsma_symbol,
																												ROUND(a.tsma_price,2),
																												a.tsma_trade_date
																									 from Trades_stage_m_alloc a, Trades_stage_m_trade b 
 																								  where a.tsma_order_id = b.tsmt_order_id
																									  and b.tsmt_account_number = ''
																									  and a.tsma_trade_date = '".$trade_date_to_process."'";
											
											//echo "<BR><BR>=> _query_c ".$_query_c;
											$_result_c = mysql_query($_query_c) or die(mysql_error());
											
											//fields missing in tsma (sec description, settle date and trade time need to be updated from tsmt)
											
											// = '' replaced with  IS NULL, works on PPLINUX, but not on COMPLIANCE.TOCQUEVILLE.COM
											
											$_query_d = "SELECT trdm_auto_id, trdm_order_id from Trades_m where trdm_settle_date IS NULL and trdm_trade_date = '".$trade_date_to_process."'";
											$result_d = mysql_query($_query_d) or die (mysql_error());
											
											//echo "<BR><BR>=> _query_d ".$_query_d;
											
											while ( $row_d = mysql_fetch_array($result_d) ) {
											
													$trdm_auto_id = $row_d["trdm_auto_id"];
													$trdm_order_id = $row_d["trdm_order_id"];
													
													$_query_e = "SELECT tsmt_sec_description, tsmt_settle_date, tsmt_trade_time from Trades_stage_m_trade where tsmt_order_id = '".$trdm_order_id."'";
													$result_e = mysql_query($_query_e) or die (mysql_error());
													
													//echo "<BR><BR>=> _query_e ".$_query_e;
											
                          while ( $row_e = mysql_fetch_array($result_e) ) {
	
	    												$tsmt_sec_description = $row_e["tsmt_sec_description"];
			    										$tsmt_trade_time = $row_e["tsmt_trade_time"];
															$tsmt_settle_date = $row_e["tsmt_settle_date"];
															
															$_query_f = "update Trades_m set trdm_sec_description = '".$tsmt_sec_description."' , trdm_settle_date = '".$tsmt_settle_date."' , trdm_trade_time = '".$tsmt_trade_time."' where trdm_auto_id = '".$trdm_auto_id."'";
															$result_f = mysql_query($_query_f) or die (mysql_error());
															
															//echo "Update statement is : ". $_query_f . "<BR><BR>";
													
													}
											
											}
											
											//echo "<BR><BR>DONE!!!!!!!!!!!!!!!!!!!!!!!";
												
										
										
										//  END PROCESSING TRADES FROM STAGING TO MAIN
										////
										//////
									
										
										//END PARSING ALLOCATION FILE														
										}
										else {
											echo "UNEXPECTED ERROR! Please inform Technical Support.";
											}
										//END PARSE UPLOAD
									
									//FILE NOT COPIED TO DESTINATION
									} else {
										print "<li>There was an error uploading the trades file to the system! Please contact Technical Support.</li>";
										$proceed = 0;
										//SEND EMAIL TO ADMINISTRATOR WITH THIS ERROR
										sys_mail($_app_administrator,"Trade File upload error","There was an unknown error in uploading trade file from Moxy into the CompSys","Comp Header"); 
										}
								//END WHILE
								}
								echo "</ul>";
								
								//END ELSE (FILENAME CHECK)
								}
							} else {
							echo " "; //Do nothing
							}
							 
							?>
							
							
							<?
							
							if ($to_upload == '') {$to_upload = 1; $browse_for = "trda.csv";} elseif($to_upload == 1){$to_upload = 2; $browse_for = "trdb.csv";} else {$to_upload = 3; $browse_for = " ";} 							
							?>
							
							<?
							
							if ($to_upload != 3) {
							?>							
							<a class="csys_regtext"><br>Please click on the <b>Browse</b> button and select file ===> <b><?=$browse_for?></b> <=== and click on <b>Upload</b></a>
							<form action="<?=$PHP_SELF?>" enctype="multipart/form-data" method="post" name="fileupload">
							<input class="Text"  name="file[]" type="file" size="50">
							
							<input type="hidden" name="to_upload" value="<?=$to_upload?>">
							
							<input class="Text" type="submit" name="submit" value="Upload">
							</form>
							<?
							} else { 
							?>
						  <a class="csys_regtext_bold">Process Completed:</a>
							<ul class="csys_regtext">
							<li class="csys_regtext">Files <b>trda.csv</b> and <b>trdb.csv</b> successfully uploaded and trades successfully extracted.</li>
							</ul>
							<?  include ('email_report_m_inc.php'); ?>
							
							
							
							<?
							}
							?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</td>
</tr>

<?php

  include('bottom.php');
	 
?>