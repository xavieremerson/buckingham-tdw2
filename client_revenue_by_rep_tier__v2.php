<?
///////////////////////////////////////////////  START OF MANAGE SECTION  ////////////////////////////////////////////////////////////

	echo "<center>";
	?>
	<? tsp(100, "Client Revenue BY Sales Rep."); ?>

		<table width="100%" border="0" cellpadding="1", cellspacing="0">
    <tr> 
		<?
    if ($mode == 'm') {
    ?>
    <td width="200">&#9658;<a class="ilt" href="client_revenue_by_rep_tier_excel__v2.php?mode=m&filter_rep=<?=$filter_rep?>&filter_tier=<?=$filter_tier?>" target="_blank">Export to Excel</a></td>
		<?
    } else {
		?>
    <td width="200">&#9658;<a class="ilt" href="client_revenue_by_rep_tier_excel__v2.php?mode=r&user_initials=<?=$user_initials?>" target="_blank">Export to Excel</a></td>
    <?
		}
		if ($mode == 'm') {
		?>
    <td width="10">&nbsp;</td>
    <!--<td width="150">
    <form action="<?=$PHP_SELF?>" method="get">
    <input type="hidden" name="mod" value="client_revenue" />
    <input type="hidden" name="mode" value="m" />
    <select name="filter_rep" id="filter_rep" size="1">
			<option value="" selected="selected"> All Reps. </option>
			<option value="BRG"> BRG </option>
    	<?
				foreach ($arr_reps as $k=>$v) {
					if ($v != "") {
						echo '<option value="'.$k.'"> '.$v.' </option>';
					} 
				}
			?>
    </select>
    </td>-->
    <td width="10">&nbsp;</td>
    <!--<td width="60">
    <select name="filter_tier" id="filter_tier" size="1">
			<option value="" selected="selected"> All Tiers. </option>
			<option value="1"> Tier 1 </option>
			<option value="2"> Tier 2 </option>
			<option value="3"> Tier 3 </option>
			<option value="4"> Tier 4 </option>
    </select>
    </td>
    <td width="10">&nbsp;</td>
    <td width="50"><input type="submit" name="Filter" value="   SHOW   "/></td>-->
		</form>	
		<?
    }
    ?>    
    <td align="right"><a class="ilt"><font color="red">NOTE:</font> To apply multiple column sort, please use Shift + Click.</a></td>
		</tr></table>
          <!--TABLE 2 START-->
					<link rel="stylesheet" href="includes/jquery/__jquery.tablesorter/themes/blue/style.css" type="text/css" media="print, projection, screen" />
					<script type="text/javascript" src="includes/jquery/jquery-1.8.2.min.js"></script>
					<script type="text/javascript" src="includes/jquery/__jquery.tablesorter/jquery.tablesorter.min.js"></script>
					<script type="text/javascript">

					$(document).ready(function() {     
						// call the tablesorter plugin and assign widgets with id "zebra" (Default widget in the core) and the newly created "reindextbl"
						$("#myTable").tablesorter(
							{widgets: ['zebra']},
							{headers:  {2: {sorter:false} } }
						);
					}); 
					</script>	
 					<table id="myTable" class="tablesorter" border="0" cellpadding="0" cellspacing="1">
						<thead>
            <tr>
              <td width="1400" colspan="19" bgcolor="#f7f7f7"><center><strong>Change in Account Status</strong></center></td>
							<td bgcolor="#ffffff">&nbsp;</td>
						</tr>
            <tr>
              <td colspan="4" bgcolor="#f7f7f7">&nbsp;</td>
							<td colspan="3" bgcolor="#009933"><center><strong><font color="#FFFFFF">TIER 1</font></strong></center></td>
              <td colspan="3" bgcolor="#00CC66"><center><strong>TIER 2</strong></center></td>
              <td colspan="3" bgcolor="#FFCC33"><center><strong>TIER 3</strong></center></td>
              <td colspan="3" bgcolor="#FF0000"><center><strong><font color="#FFFFFF">TIER 4</font></strong></center></td>
              <td colspan="3" bgcolor="#000000"><center><strong><font color="#FFFFFF">Prospects & Inactive Accounts</font></strong></center></td>
							<td bgcolor="#f7f7f7">&nbsp;</td>
						</tr>
            <tr>
              <td width="40" bgcolor="#e6eeee">&nbsp;</td>
              <th width="150">Sales Rep.</th>
              <th width="80">Type</th>
              <th width="40"># Accts.</th>
							<th width="60">Status<br />Quo</th>
							<th width="45">Adds</th>
							<th width="45">Drops</th>
							<th width="60">Status<br />Quo</th>
							<th width="45">Adds</th>
							<th width="45">Drops</th>
							<th width="60">Status<br />Quo</th>
							<th width="45">Adds</th>
							<th width="45">Drops</th>
							<th width="60">Status<br />Quo</th>
							<th width="45">Adds</th>
							<th width="45">Drops</th>
							<th width="60">Status<br />Quo</th>
							<th width="45">Adds</th>
							<th width="45">Drops</th>
							<td bgcolor="#ffffff">&nbsp;</td>
						</tr>
					  </thead>
					  <tbody>	
						<? 
						$query = "SELECT * from _client_revenue_by_rep_tier order by auto_id";
						$result = mysql_query($query) or die(mysql_error());
						$count_row = 0;
						while ( $row = mysql_fetch_array($result) ) 
						{
							echo	"<tr>".
											"<td valign='middle' nowrap='nowrap'><a href='#'><img src='images/t12m_s.png' border='0' onclick='CreateWnd(\"chart_mult_years_by_rep.php?rep=".$row["client_code"]."\", 620, 330, false);'></a></td>".
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
											"<td>&nbsp;</td>". 
										"</tr>";
						}

						?> 
					 </tbody>
 					</table>
	
		<? tep();
		
		echo "</center>";
/////////////////////////////////////////////////END OF MANAGE SECTION/////////////////////////////////////////////////
?>