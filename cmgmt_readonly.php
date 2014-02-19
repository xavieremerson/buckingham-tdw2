<?php
//BRG
include('inc_header.php');
?>
			<script type="text/javascript" src="includes/javascript/calendar/calendar.js"></script>
			<script type="text/javascript" src="includes/javascript/calendar/lang/calendar-en.js"></script>
			<!-- helper script that uses the calendar -->
			<script type="text/javascript" src="includes/javascript/calendar/helper.js"></script>
			<link rel="alternate stylesheet" type="text/css" media="all" href="includes/javascript/calendar/calendar-win2k-2.css" title="win2k-2" />
			<script type="text/javascript">setActiveStyleSheet(document.getElementById("defaultTheme"), "win2k-2");</script>
			
<?

///////////////////////////////////////////////  START OF MANAGE SECTION  ////////////////////////////////////////////////////////////
	if($type == "manage" or !isset($type) or 1==1)
    {
	tsp(100,"Client / Prospect Master");

?>
		&nbsp;&nbsp;&#9658;<a class="ilt" href="cmgmt_readonly_excel.php" target="_blank"">Export to Excel</a>		
		<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
			<tr>
				<td>
					<!--TABLE 2 START-->
					<script language="JavaScript" src="includes/javascript/sorttable.js" type="text/javascript"></script>

					<table class="sortable" preserve_style="cell" width="100%"  border="0" cellspacing="1" cellpadding="1">
						<tr>
							<td width="375">Name</td>
							<td width="100">Status</td>
							<td width="60">Code</td>
							<td width="60">RR1</td>
							<td width="60">RR2</td>
							<td width="60">Trader</td>
							<td width="500">Address</td>
							<td>&nbsp;</td>
						</tr>
						<?
           
						$query_clients = "SELECT * from int_clnt_clients 
															where clnt_isactive != 0
															and clnt_status not like 'X%' 
															and clnt_code not like 'ADJ %'
															order by clnt_name";
						//echo $query_trades;
						$result = mysql_query($query_clients) or die(mysql_error());
						$count_row = 0;
						while ( $row = mysql_fetch_array($result) ) 
						{
							if ($count_row%2 == 0) {
								$rowclass = '"trdark"';
							} else {
								$rowclass = '"trlight"';
							}
							
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
							
						?>
									<tr class=<?=$rowclass?>>
									<td>&nbsp;&nbsp;<?=$row["clnt_name"]?></td>
									<td><?=$str_status?></td>
									<td><?=$row["clnt_code"]?></td> 
									<td><?=$row["clnt_rr1"]?></td> 
									<td><?=$row["clnt_rr2"]?></td> 
									<td><?=$row["clnt_trader"]?></td> 
									<td><?=$str_address?></td> 
									<td>&nbsp;</td>
                  </tr>
            <?						
						$count_row = $count_row + 1;
						}
						?>
					</table>
				</td>
			</tr>
		</table>
	
		<?
		tep();
	}  
/////////////////////////////////////////////////END OF DELETE SECTION/////////////////////////////////////////////////
  include('inc_footer.php');
?>
