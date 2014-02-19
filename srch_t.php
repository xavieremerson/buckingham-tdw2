<?php
//BRG
include('inc_header.php');

?>
		<!-- START TABLE 1 -->
		<table width="100%" height="100%" border="0" cellspacing="1" cellpadding="0">
			<tr> 
				<td valign="top">

<style type="text/css">
<!--
tr.test {
	color: #FFFFFF;
	background-color: #000000;
}

tr.selrow {
	color: #FFFFFF;
	background-color: #0000FF;
}

-->
</style>
<?

	function mysql_first_data($q, $rowname)
	{
		$result = mysql_query ($q);
		if (mysql_num_rows($result) > 0)
		{
			$rowname = explode("|", $rowname);
			for ($i = 0; $i < count($rowname); $i++)
			{
				global $$rowname[$i];
				$$rowname[$i] = mysql_result ($result, 0, $rowname[$i]);
			}
		}	
	}

	function show_number($numberperpage, $limit, $alltotal, $currenttotal, $link)
	{
		$class_field = "iltc";
		
		if (($limit + $currenttotal) > $numberperpage)
		{	
			$next_limit = $limit - $numberperpage;
			?>
			<a href="<?=$link?>&limit=<?=$next_limit?>" class="<?=$class_field?>">&lt;&lt; Previous</a>&nbsp;
			<?
		}	
		
		$x = $alltotal;
		
		$limit_x = 0;
		$x_count = 1;
		$numlinks = 0;
		
		while ($x > 0 and $numlinks < 10)
		{
			if ($limit != $limit_x)
				print ('<a class="iltc">|</a><a href="'.$link.'&limit='.$limit_x.'" class="'.$class_field.'">&nbsp;'.$x_count.'&nbsp;</a>');
			else
				print ('<a class="iltc">|</a><a class="ilt_nl">&nbsp;'.$x_count.'&nbsp;</a>');
			$x_count++; 
			$limit_x = $limit_x + $numberperpage;
			$x = $x - $numberperpage;
			$numlinks = $numlinks + 1;
		}
		
		if ($alltotal > ($limit + $currenttotal))
		{	
			$next_limit = $limit + $currenttotal;
			print ('<a class="iltc">|</a> <a href="'.$link.'&limit='.$next_limit.'" class="'.$class_field.'">Next &gt;&gt;</a>');
		}	
		else
			print ('<a class="iltc">|</a>');
		print ("<br><br>");
	}

	
	if ($_POST) {
	//print_r($_POST);
			$limit = 0;
			$_GET = array();
			$srchval = trim($srchval);

	}

	
	
	$q1 = "SELECT trad_full_account_number 
	      FROM nfs_trades WHERE
				trad_cusip like '%".$srchval."%' or 
				trad_trade_reference_number like '%".$srchval."%' or 
				trad_symbol like '%".$srchval."%' or 
				trad_short_name like '%".$srchval."%' or 
				trad_quantity like '%".$srchval."%' or 
				trad_sec_desc_1 like '%".$srchval."%'";
	//xdebug("q1",$q1);
	mysql_first_data ($q1, "trad_full_account_number");
	

	if (!isset($limit)) 
		$limit = 0;

	$alltotal = 0;
	$q2 = "SELECT COUNT(*) AS alltotal FROM nfs_trades WHERE
				trad_cusip like '%".$srchval."%' or 
				trad_trade_reference_number like '%".$srchval."%' or 
				trad_symbol like '%".$srchval."%' or 
				trad_short_name like '%".$srchval."%' or 
				trad_quantity like '%".$srchval."%' or 
				trad_sec_desc_1 like '%".$srchval."%'";
	//xdebug("q2",$q2);
	mysql_first_data ($q2, "alltotal");

	$numberperpage = 20;
	$q3 = "SELECT trad_full_account_number,
								trad_buy_sell, 
								trad_trade_date,
								trad_symbol
	      FROM nfs_trades WHERE
				trad_cusip like '%".$srchval."%' or 
				trad_trade_reference_number like '%".$srchval."%' or 
				trad_symbol like '%".$srchval."%' or 
				trad_short_name like '%".$srchval."%' or 
				trad_quantity like '%".$srchval."%' or 
				trad_sec_desc_1 like '%".$srchval."%'
				ORDER BY trad_full_account_number DESC LIMIT $limit, $numberperpage";
	//xdebug("q3",$q3);
	$result = mysql_query ($q3);
	$currenttotal = mysql_num_rows ($result);

	tsp("","Search Results");
	?>
	
		<table width="100%">
			<tr>
				<td>
					<form name="acct_search" action="" method="post">
					<input type="text" name="srchval" value="<?=$srchval?>" size="30" maxlength="30">
					<input type="submit" name="search" value="Search" />
					</form>
				</td>
				<td class="iltr" valign="bottom">Items <?=($limit)+1?> to <?=($limit + $currenttotal)?> from <?=$alltotal?> total results.</td>
			</tr>
	  </table>

		
		<table width="100%" height="100%" cellpadding="1", cellspacing="0" bgcolor="#AAAAAA">
			<tr>
				<td>		
						<script language="JavaScript" src="includes/javascript/sorttable.js" type="text/javascript"></script>
						<table class="sortable" preserve_style="cell" width="100%" border="0" cellspacing="1" cellpadding="1">
							<thead class="datadisplay"> <!--  class="datadisplay" -->
								<tr bgcolor="#CCCCCC">
									<td width="150">Account Number</td>
									<td width="250"> Trade Date</td>
									<td width="150">Buy/Sell</td>
									<td width="150">Symbol</td>
								</tr>
							</thead>
							<tbody id="offTblBdy" class="datadisplay"> 
							<script type="text/javascript">
								var dt = new Array()
				
							<? 
							$count_row = 0;
							while ($row = mysql_fetch_object($result))
							{
								echo 'dt ['.$count_row.'] = "'.trim($row->trad_full_account_number).'^'.
																							 trim($row->trad_trade_date).'^'.
																							 trim($row->trad_buy_sell).'^'.
																							 trim($row->trad_symbol).'"'.";\n";
							
								$count_row = $count_row + 1;
							}
							?>						
									for (i=0;i<dt.length;i++)
									{
									var rowtrades_array = new Array()
									var rowclass
									var research_link
									if (i%2 == 0) {
										rowclass = "trdark";
									} else {
										rowclass = "trlight";
									}
									
									rowtrades_array=dt[i].split("^");
									
									document.write(
													"<tr class='" + rowclass + "' " +	"onmouseover=\"this.className = 'test'\" " + "onmouseout=\"this.className = '" + rowclass + "'\" " +	"onClick=\"this.className = 'selrow'\">"+"<td>&nbsp;&nbsp;&nbsp;"+rowtrades_array[0]+"</td>"+
													"<td><div align='left'>&nbsp; &nbsp; &nbsp; "+rowtrades_array[1]+"</div></td>"+
													"<td align='left'>&nbsp;" + rowtrades_array[2] + "&nbsp;&nbsp;&nbsp;</td><td align='left'>&nbsp;" + rowtrades_array[3] + "&nbsp;&nbsp;&nbsp;</td></tr>");
									}
									</script>
						</tbody>
					</table>
				</td>
			</tr>
		</table>

	<?
	tep();
	
	show_number($numberperpage, $limit, $alltotal, $currenttotal, "srch_a.php?srchval=".$srchval);

?>
				</td>
			</tr>
		</table>
		<!-- END TABLE 1 -->
<?php
include('inc_footer.php'); 
?>

