<?
include('includes/functions.php');
include('includes/global.php');
include('includes/dbconnect.php');
												
$output_filename = "Client_Details.xls";
$fp = fopen($exportlocation.$output_filename, "w");

$str = '<html xmlns="http://www.w3.org/1999/xhtml">
				<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>
				<body>';
fputs ($fp, $str);


$str = '<table width="800" border="1" cellspacing="0" cellpadding="0">
					<tr>
							<td width="375">Name</td>
							<td width="100">Status</td>
							<td width="60">Code</td>
							<td width="60">RR1</td>
							<td width="60">RR2</td>
							<td width="60">Trader</td>
							<td width="500">Address</td>
					</tr>';
fputs ($fp, $str);



						//Clients List
						$query_client = "SELECT * from int_clnt_clients 
															where clnt_isactive != 0
															and clnt_status not like 'X%' 
															and clnt_code not like 'ADJ %'
															order by clnt_name";
						$result_client = mysql_query($query_client) or die(tdw_mysql_error($query_client));
						while($row = mysql_fetch_array($result_client)) {
						
							if ($row["clnt_status"] == 'A') {
								$str_status = 'Active Client';
							} else {
								$str_status = 'Prospect';
							}
							
							//clnt_address_line_1 clnt_address_line_2 clnt_address_city clnt_address_state clnt_address_postal_code
							if ($row["clnt_address_line_1"] == "" ) {
							$str_address ="";
							} else {
								$str_address = $row["clnt_address_line_1"];
								if ($row["clnt_address_line_2"] != ""){
									$str_address .= ", ".$row["clnt_address_line_2"];
								}
								if ($row["clnt_address_city"]!="") {
									$str_address .= ", ".$row["clnt_address_city"];
								}
								if ($row["clnt_address_state"]!="") {
									$str_address .= ", ".$row["clnt_address_state"];
								}
								if ($row["clnt_address_postal_code"]!="") {
									$str_address .= " ".$row["clnt_address_postal_code"].".";
								}
							}
						$str = '<tr>
											<td>'.$row["clnt_name"].'</td>
											<td>'.$str_status.'</td>
											<td>'.$row["clnt_code"].'</td> 
											<td>'.$row["clnt_rr1"].'</td> 
											<td>'.$row["clnt_rr2"].'</td> 
											<td>'.$row["clnt_trader"].'</td> 
											<td>'.$str_address.'</td> 
										</tr>';
						fputs ($fp, $str);					
						
						}

$str = '</table>
	</body>
</html>';
fputs ($fp, $str);

fclose($fp);

//Header("Location: data/exports/".$output_filename);
Header("Location: http://192.168.20.63/tdw/fileserve_xls.php?l=data/exports/&f=".$output_filename);
?>