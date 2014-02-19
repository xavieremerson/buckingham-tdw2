<?
include('inc_header.php');
?>
		<!-- START TABLE 1 -->
		<table width="100%" height="100%" border="0" cellspacing="1" cellpadding="0">
			<tr> 
				<td valign="top">  
        <!--<script language="JavaScript" src="includes/prototype/prototype.js"></script>-->
<?
$user_init = $user_initials;

///////////////////////////////////////////////  START OF MANAGE SECTION  ////////////////////////////////////////////////////////////
	echo "<center>";
	?>
	<? tsp(100, "Client Revenue"); ?>

		<table width="100%" border="0" cellpadding="1", cellspacing="0">
    <tr> 
    <td width="200">&#9658;<a class="ilt" href="client_revenue_excel__v2_srep.php?mode=r&user_init=<?=$user_init?>" target="_blank">Export to Excel</a></td>
    <td width="10">&nbsp;</td>
		</form>	
    <td align="right"><a class="ilt"><font color="red">NOTE:</font> To apply multiple column sort, please use Shift + Click.</a></td>
		</tr></table>
          <!--TABLE 2 START-->
					<link rel="stylesheet" href="includes/jquery/__jquery.tablesorter/themes/blue/style.css" type="text/css" media="print, projection, screen" />
					<script type="text/javascript" src="includes/jquery/jquery-1.8.2.min.js"></script>
					<script type="text/javascript" src="includes/jquery/__jquery.tablesorter/jquery.tablesorter.min.js"></script>
					<script type="text/javascript">
					/*$(function(table) {
						// add new widget called reindextbl
						$.tablesorter.addWidget({
							// give the widget a id
							id: "reindextbl",
							// format is called when the on init and when a sorting has finished
							format: function(table) {
								// insert row counter
								$('table tbody tr').each(function(idx){
								$(this).children(":eq(0)").html(idx + 1);
								});
							}
						});
				
					});*/

					$(document).ready(function() {     
						// call the tablesorter plugin and assign widgets with id "zebra" (Default widget in the core) and the newly created "reindextbl"
						$("#myTable").tablesorter(
							{widgets: ['zebra']},
							{headers: { 2:{sorter:false} } }
						);
					}); 
					
					/*$(document).ready(function() {     
						// call the tablesorter plugin and assign widgets with id "zebra" (Default widget in the core) and the newly created "reindextbl"
						$('table tbody tr').each(function(idx){
								$('tr').children(":eq(0)").html(idx + 1);
						 });
					});*/ 
					</script>	
					<table id="myTable" class="tablesorter" border="0" cellpadding="0" cellspacing="1">
						<thead>
						<tr>
              <th width="120">City/State</td>
							<th width="40">RR2</th>
							<th width="40">Trdr.</th>
              <th width="40">Tier</th>
              <th width="40">Yrs.</th>
              <th width="30">u/d</th>
              <th width="60">Region</th>
							<th width="40">RR1</th>
							<th width="45">HIST</th>
							<th width="220">Client</th>
							<th width="65">MTD($)</th>
							<th width="65">YTD($K)</th>
							<th width="50">Ann.</th>
							<th width="50"><?=date('Y')-1?></th>
							<th width="50"><?=date('Y')-2?></th>
							<th width="50"><?=date('Y')-3?></th>
							<th width="50"><?=date('Y')-4?></th>
							<th width="60">CY Ann.</th>
							<th width="90">CY vs PY %</th>
							<!--<th width="80">Tier</th>-->
							<th>&nbsp;</th>
						</tr>
					  </thead>
					  <tbody>	
						
            <script type="text/javascript">
						var dt = new Array()

						<? 

						$query_clients = "SELECT * from _client_revenue
															WHERE rr1 = '".$user_init."' or rr2 = '".$user_init."' order by client_name";
						//echo $query_clients;
						$result = mysql_query($query_clients) or die(mysql_error());
						$count_row = 0;
						while ( $row = mysql_fetch_array($result) ) 
						{
								echo	'dt ['.$count_row.'] = "'.$row["city_state"].'^'.
																									$row["rr2"].'^'. 
																									$row["trader"].'^'.
																									$row["tier"].'^'.
																									$row["tier_years"].'^'.
																									$row["tier_up_down"].'^'.
																									$row["region"].'^'.
																									$row["rr1"].'^'. 
																									$row["history_chart"].'^'.
																									$row["client_name"].'^'. 
																									number_format($row["clnt_curr_mtd"],0,"",",").'^'. 
																									$row["ytd"].'^'.
																									$row["ytd_annualized"].'^'.
																									$row["prior_year_1"].'^'.
																									$row["prior_year_2"].'^'.
																									$row["prior_year_3"].'^'.
																									$row["prior_year_4"].'^'.
																									$row["cur_year_annualized"].'^'.
																									$row["cur_year_vs_prev_year_pct"].'"'.";\n";
						
								/*echo	"<tr>".
													"<td>".$row["city_state"]."</td>".
													"<td>&nbsp;".$row["rr2"]."</td>". 
													"<td>&nbsp;".$row["trader"]."</td>".
													"<td>".$row["tier"]."</td>".
													"<td>".$row["tier_years"]."</td>".
													"<td>".$row["tier_up_down"]."</td>".
              						"<td>".$row["region"]."</td>".
													"<td>&nbsp;".$row["rr1"]."</td>". 
													"<td valign='middle' nowrap='nowrap'><a href='#'><img src='images/t12m_s.png' border='0' onclick='CreateWnd(\"chart_mult_years.php?clnt=" .$row["history_chart"]."\", 620, 330, false);'></a></td>" .
													"<td>&nbsp;".$row["client_name"]."</td>". 
													"<td>".$row["ytd"]."</td>".
													"<td>".$row["ytd_annualized"]."</td>".
													"<td>".$row["prior_year_1"]."</td>".
													"<td>".$row["prior_year_2"]."</td>".
													"<td>".$row["prior_year_3"]."</td>".
													"<td>".$row["cur_year_annualized"]."</td>".
													"<td align='right'>".$row["cur_year_vs_prev_year_pct"]."%</td>".
													"<td>&nbsp;</td>".
												"</tr>";*/
						$count_row = $count_row + 1;
						}																								
						?> 

				for (i=0;i<dt.length;i++)
					{
					var row_data = new Array()
					row_data=dt[i].split("^");					
					
					document.write(
													"<tr><td>"+row_data[0]+"</td>"+
													"<td>&nbsp;"+row_data[1]+"</td>"+ 
													"<td>&nbsp;"+row_data[2]+"</td>"+
													"<td>"+row_data[3]+"</td>"+
													"<td>"+row_data[4]+"</td>"+
													"<td>"+row_data[5]+"</td>"+
              						"<td>"+row_data[6]+"</td>"+
													"<td>&nbsp;"+row_data[7]+"</td>"+ 
													"<td valign='middle' nowrap='nowrap'><a href='#'><img src='images/t12m_s.png' border='0' onclick='CreateWnd(\"chart_mult_years.php?clnt="+row_data[8]+"\", 620, 330, false);'></a></td>"+
													"<td>&nbsp;"+row_data[9]+"</td>"+ 
													"<td align='right'>"+row_data[10]+"</td>"+
													"<td align='right'>"+row_data[11]+"</td>"+
													"<td align='right'>"+row_data[12]+"</td>"+
													"<td align='right'>"+row_data[13]+"</td>"+
													"<td align='right'>"+row_data[14]+"</td>"+
													"<td align='right'>"+row_data[15]+"</td>"+
													"<td align='right'>"+row_data[16]+"</td>"+
													"<td align='right'>"+row_data[17]+"</td>"+
													"<td align='right'>"+row_data[18]+"%</td>"+
													"<td>&nbsp;</td></tr>");
					}
					</script>
         </tbody>
        </table>
	
		<? tep(); ?>

<?		
		echo "</center>";
/////////////////////////////////////////////////END OF MANAGE SECTION/////////////////////////////////////////////////
?>
				</td>
			</tr>
		</table>
		<!-- END TABLE 1 -->
<? 
include('inc_footer.php'); 
?>