<?

// Turn off all error reporting
error_reporting(0);

  include('includes/dbconnect.php');
  include('includes/global.php'); 
	include('includes/functions.php');

$output_filename = "client_revenue_by_rep_tier.xls";
$fp = fopen($exportlocation.$output_filename, "w");

///////////////////////////////////////////////  START OF MANAGE SECTION  ////////////////////////////////////////////////////////////
          $str = '';
					$str .= '<html><body><table border="1">
						<thead>
            <tr>
              <td width="1400" colspan="18" bgcolor="#f7f7f7"><center><strong>Change in Account Status</strong></center></td>
							<td bgcolor="#ffffff">&nbsp;</td>
						</tr>
            <tr>
              <td colspan="3" bgcolor="#f7f7f7">&nbsp;</td>
							<td colspan="3" bgcolor="#009933"><center><strong><font color="#FFFFFF">TIER 1</font></strong></center></td>
              <td colspan="3" bgcolor="#00CC66"><center><strong>TIER 2</strong></center></td>
              <td colspan="3" bgcolor="#FFCC33"><center><strong>TIER 3</strong></center></td>
              <td colspan="3" bgcolor="#FF0000"><center><strong><font color="#FFFFFF">TIER 4</font></strong></center></td>
              <td colspan="3" bgcolor="#000000"><center><strong><font color="#FFFFFF">Prospects & Inactive Accounts</font></strong></center></td>
						</tr>
            <tr>
              <td width="150">Sales Rep.</td>
              <td width="80">Type</td>
              <td width="40"># Accts.</td>
							<td width="60">Status<br />Quo</td>
							<td width="45">Adds</td>
							<td width="45">Drops</td>
							<td width="60">Status<br />Quo</td>
							<td width="45">Adds</td>
							<td width="45">Drops</td>
							<td width="60">Status<br />Quo</td>
							<td width="45">Adds</td>
							<td width="45">Drops</td>
							<td width="60">Status<br />Quo</td>
							<td width="45">Adds</td>
							<td width="45">Drops</td>
							<td width="60">Status<br />Quo</td>
							<td width="45">Adds</td>
							<td width="45">Drops</td>
						</tr>';

						$query = "SELECT * from _client_revenue_by_rep_tier order by auto_id";
						$result = mysql_query($query) or die(mysql_error());
						$count_row = 0;
						while ( $row = mysql_fetch_array($result) ) 
						{
							$str .= "<tr>".
													"<td>".$row["sales_rep"]."</td>".
													"<td>".$row["type_accounts"]."</td>". 
													"<td>".$row["num_accounts"]."</td>". 
													"<td align='right'>".$row["tier_1_stat_quo"]."</td>". 
													"<td align='right'>".$row["tier_1_adds"]."</td>". 
													"<td align='right'>".$row["tier_1_drops"]."</td>". 
													"<td align='right'>".$row["tier_2_stat_quo"]."</td>". 
													"<td align='right'>".$row["tier_2_adds"]."</td>". 
													"<td align='right'>".$row["tier_2_drops"]."</td>". 
													"<td align='right'>".$row["tier_3_stat_quo"]."</td>". 
													"<td align='right'>".$row["tier_3_adds"]."</td>". 
													"<td align='right'>".$row["tier_3_drops"]."</td>". 
													"<td align='right'>".$row["tier_4_stat_quo"]."</td>". 
													"<td align='right'>".$row["tier_4_adds"]."</td>". 
													"<td align='right'>".$row["tier_4_drops"]."</td>". 
													"<td align='right'>".$row["count_prospects"]."</td>". 
													"<td align='right'>&nbsp;</td>". 
													"<td align='right'>&nbsp;</td>". 
												"</tr>";
						}

$str .= '</table>
	</body>
</html>';

fputs ($fp, $str);
fclose($fp);

//echo $str;
Header("Location: http://192.168.20.63/tdw/fileserve_xls.php?l=data/exports/&f=".$output_filename);
?>