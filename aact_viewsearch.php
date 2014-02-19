<?php
//BRG
include('inc_header.php');
//include('includes/functions.php');  
include("includes/class.pagination.php");

//Date in YYYY-MM-DD format
$trade_date_to_process = previous_business_day();

?>
<style type="text/css">
<!--
.showres {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #000033;
}
-->
</style>



	<table width="100%" cellpadding="2" cellspacing="2"><tr><td>
	<? table_start_percent(100, "Accounts Search/View"); ?>

		<!-- START TABLE 1 -->
		<table class="tablewithdata" width="100%"  border="0" cellspacing="0" cellpadding="0">
			<tr>
				<form action="<?=$PHP_SELF?>" id="filteracct" method="POST"> 
				<td align="left">&nbsp;&nbsp;<a class="ilt">Enter Search Term: </a>&nbsp;
				<input name="str_search" class="ilt" value="<?=$str_search?>" size="30" maxlength="30">&nbsp;&nbsp;
				<INPUT type="submit" name="submit1" value="Search" border="0">
			  </td>
				</form>
			</tr>
		</table>
		<!-- END TABLE 1 -->
	</td></tr>
	<tr><td>
			
			<?php
			include('includes/dbconnect.php');
					  
			$date = date("Ymd");
			?>
				
			<?
			if ($str_search != '') 
			{ 
				$xstart = 0;
			}
			
			$query_acct = "SELECT
			  nadd_firm,
				nadd_branch,
				nadd_account_number,
				nadd_full_account_number,
				nadd_advisor,
				nadd_short_name,
				nadd_rr_owning_rep,
				nadd_rr_exec_rep,
				nadd_num_address_lines,
				nadd_address_line_1,
				nadd_address_line_2,
				nadd_address_line_3
				FROM mry_nfs_nadd 
				WHERE 
				(nadd_full_account_number LIKE '%" . $str_search . "%' 
					or nadd_advisor LIKE '%" . $str_search . "%'
					or nadd_short_name LIKE '%" . $str_search . "%'
					or nadd_rr_owning_rep LIKE '%" . $str_search . "%'
					or nadd_rr_exec_rep LIKE '%" . $str_search . "%'
					or nadd_address_line_1 LIKE '%" . $str_search . "%'
					or nadd_address_line_2 LIKE '%" . $str_search . "%'
					or nadd_address_line_3 LIKE '%" . $str_search . "%' 
					) AND
					(nadd_short_name NOT LIKE '&%') AND
					(nadd_advisor NOT LIKE '&%')
				ORDER BY nadd_full_account_number";


				$acctspage = new Pagination;

				$acctspage->sql = $query_acct; // the (basic) sql statement (use the SQL whatever you like)
				$result = $acctspage->get_page_result(); // result set
				$num_rows = $acctspage->get_page_num_rows(); // number of records in result set 
				$nav_links = $acctspage->navigation(" | ", "currentStyle"); // the navigation links (define a CSS class selector for the current link)
				$nav_info = $acctspage->page_info("to"); // information about the number of records on page ("to" is the text between the number)
				$simple_nav_links = $acctspage->back_forward_link(true); // the navigation with only the back and forward links, use true to use images
				$total_recs = $acctspage->get_total_rows(); // the total number of records


				//echo $query_statement;
				$result = mysql_query($query_acct) or die (mysql_error());
			?>
			
		<tr>
				<td valign="top">
				<?php echo "<a class=\"pagination\">&nbsp;&nbsp;Records ".$nav_info." of ".$total_recs."</a>"; ?>
					<!-- START TABLE 3 -->
					<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
						<tr>
							<td valign="top">
								<!-- START TABLE 4 -->
								<!-- class="tablewithdata" -->
								<table class="sortable" id="accounts_table"  width="100%"  border="0" cellspacing="1" cellpadding="0">
									<!-- class="tableheading12" -->
									<tr height="20" bgcolor="#333333"> 
										<td>&nbsp;&nbsp;&nbsp;&nbsp;Account #</td>  
										<td>Rep.</td> 
										<td>Advisor</td>
										<td>Short Name</td>
										<td>Address Line 1</td>
										<td>Address Line 2</td>
										<td>Address Line 3</td>
										<td>&nbsp;</td>
									</tr>
			
									<?			    
									//for START
									for ($i = 0; $i < $num_rows; $i++) {
										$nadd_full_account_number = mysql_result($result, $i, "nadd_full_account_number");
										$nadd_rr_exec_rep = mysql_result($result, $i, "nadd_rr_exec_rep");
										$nadd_advisor = mysql_result($result, $i, "nadd_advisor");
										$nadd_short_name = mysql_result($result, $i, "nadd_short_name");
										$nadd_address_line_1 = mysql_result($result, $i, "nadd_address_line_1");
										$nadd_address_line_2 = mysql_result($result, $i, "nadd_address_line_2");
										$nadd_address_line_3 = mysql_result($result, $i, "nadd_address_line_3");
									?>
										<tr class="showres"><!--tablerow-->
											<td>&nbsp;&nbsp;<?=$nadd_full_account_number?></td>
											<td>&nbsp;&nbsp;<?=$nadd_rr_exec_rep?></td>
											<td>&nbsp;&nbsp;<?=$nadd_advisor?></td>
											<td>&nbsp;&nbsp;<?=$nadd_short_name?></td>
											<td align="left">&nbsp;<?=$nadd_address_line_1?></td>
											<td align="left">&nbsp;<?=$nadd_address_line_2?></td>
											<td align="left">&nbsp;<?=$nadd_address_line_3?></td>
											<td>&nbsp;</td>										
									</tr>
									<?
									}//for END
									?>
								</table>
									<!-- END TABLE 4 -->
									<script language="JavaScript">
									<!--
										tigra_tables('accounts_table', 1, 0, '#ffffff', '#F3F1FF', '#B8D6FE', '#cccccc');
									// -->
									</script>
							</td>
						</tr>
					</table>
						<!-- END TABLE 3 -->
				</td>
		</tr>
</table>
		
<table><tr><td valign="bottom">
<?
		echo $nav_links;
?>
</td></tr></table>
		<!-- END TABLE 2 -->
		<!--Table with thin cell border ends-->
	<? table_end_percent(); ?>
		</td></tr></table>
<?php
  include('inc_footer.php');
?>
