<?

// Turn off all error reporting
error_reporting(0);

  include('includes/dbconnect.php');
  include('includes/global.php'); 
	include('includes/functions.php');

$output_filename = "client_revenue.xls";
$fp = fopen($exportlocation.$output_filename, "w");

$str = '<html xmlns="http://www.w3.org/1999/xhtml">
				<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>
				<body>';	
		
$str .= '<table border="1">
					<tr>
						<th width="120">City/State</th>
						<th width="40">RR2</th>
						<th width="40">Trdr.</th>
						<th width="40">Tier</th>
						<th width="40">Yrs.</th>
						<th width="30">u/d</th>
						<th width="60">Region</th>
						<th width="40">RR1</th>
						<th width="220">Client</th>
						<th width="65">MTD($)</th>
						<th width="65">YTD($K)</th>
						<th width="50">Ann.</th>
						<th width="50">'.(date('Y')-1).'</th>
						<th width="50">'.(date('Y')-2).'</th>
						<th width="50">'.(date('Y')-3).'</th>
						<th width="60">CY Ann.</th>
						<th width="90">CY vs PY %</th>
						<!--<th width="80">Tier</th>-->
					</tr>';

					
						$query_clients = "SELECT * from _client_revenue
															WHERE rr1 = '".$user_init."' or rr2 = '".$user_init."' order by client_name";
						//echo $query_clients;
						$result = mysql_query($query_clients) or die(mysql_error());
						$count_row = 0;
						while ( $row = mysql_fetch_array($result) ) 
						{						
								$str .= "<tr>".
													"<td>".$row["city_state"]."</td>".
													"<td>&nbsp;".$row["rr2"]."</td>". 
													"<td>&nbsp;".$row["trader"]."</td>".
													"<td>".$row["tier"]."</td>".
													"<td>".$row["tier_years"]."</td>".
													"<td>".$row["tier_up_down"]."</td>".
              						"<td>".$row["region"]."</td>".
													"<td>&nbsp;".$row["rr1"]."</td>". 
													"<td>&nbsp;".$row["client_name"]."</td>". 
													"<td>".round($row["clnt_curr_mtd"],0)."</td>". 
													"<td>".$row["ytd"]."</td>".
													"<td>".$row["ytd_annualized"]."</td>".
													"<td>".$row["prior_year_1"]."</td>".
													"<td>".$row["prior_year_2"]."</td>".
													"<td>".$row["prior_year_3"]."</td>".
													"<td>".$row["cur_year_annualized"]."</td>".
													"<td align='right'>".$row["cur_year_vs_prev_year_pct"]."%</td>".
												"</tr>";
						$count_row = $count_row + 1;
						}																								


$str .= '</table>
	</body>
</html>';

fputs ($fp, $str);
fclose($fp);

//echo $str;
Header("Location: http://192.168.20.63/tdw/fileserve_xls.php?l=data/exports/&f=".$output_filename);
?>