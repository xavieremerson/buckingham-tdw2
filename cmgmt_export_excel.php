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
						<td width="250"><b>Client Name</b></td>
						<td width="80"><b>Client Code</b></td>
						<td width="100"><b>Tradeware Code</b></td>
						<td width="80"><b>Rep 1</b></td>
						<td width="80"><b>Rep 2</b></td>
						<td width="80"><b>Trader</b></td>
					</tr>';
fputs ($fp, $str);



						//Clients List
						$query_client = "SELECT * 
													FROM int_clnt_clients where clnt_isactive = 1  
													order by clnt_name";
						$result_client = mysql_query($query_client) or die(tdw_mysql_error($query_client));
						while($row_client = mysql_fetch_array($result_client)) {
						
						$str = '<tr>
									<td>'.$row_client["clnt_name"].'</td>
									<td>'.$row_client["clnt_code"].'</td>
									<td>'.$row_client["clnt_alt_code"].'</td>
									<td>'.$row_client["clnt_rr1"].'</td>
									<td>'.$row_client["clnt_rr2"].'</td>
									<td>'.$row_client["clnt_trader"].'</td>
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