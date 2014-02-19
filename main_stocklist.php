<script src="includes/prototype/prototype.js" type="text/javascript"></script>
		<!-- BEGIN CONTAINER -->
		     <?
				 tsp(100, "BCM Watch List"); 
				 ?>
				 <!--<a href="main_appr_rlist.php?period=C">[Current] &nbsp;&nbsp; </a>
				 <a href="main_appr_rlist.php?period=T">[Today] &nbsp;&nbsp; </a>
				 <a href="main_appr_rlist.php?period=P">[Previous Day] &nbsp;&nbsp; </a>
				 <a href="main_appr_rlist_add.php">[ ADD TO LIST ] &nbsp;&nbsp; </a>-->
				 <br />
         <img src="images/btn_add.gif" border="0"/>
				 <div id="scrollElement_a">
				 <div id="rlist_container">
		
    
    <table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
			<tr>
				<td>
					<!--TABLE 2 START-->
					<script language="JavaScript" src="includes/javascript/sorttable.js" type="text/javascript"></script>

					<table class="sortable" preserve_style="cell" width="100%"  border="0" cellspacing="1" cellpadding="1">
					<tr>
						<td width="30">&nbsp;</td>
						<td width="80">Symbol</td>
						<td width="100">Start</td>
						<td width="100">End</td>
						<td width="400">Comment / Info</td>
						<td>&nbsp;</td>
					</tr>

				 <?
				 $max_row_id = db_single_val("select max(auto_id) as single_val from bcm_watchlist");
		
				 if ($period) {
						if ($period == 'T') {
							$str_period = " bcm_datetime_start > '".date('Y-m-d')."' or bcm_datetime_stop = '2099-12-31' 
															or (
															bcm_datetime_stop < '".date('Y-m-d')." 23:59:00' 
															AND 
															bcm_datetime_stop > '".date('Y-m-d')." 00:00:00' 
															)";
						} elseif ($period == 'P') {
							$str_period = " bcm_datetime_start > '".previous_business_day()."' and bcm_datetime_start < '".date('Y-m-d')."' " ;
						} else {
							$str_period = " bcm_datetime_stop > now() or bcm_datetime_stop = '2099-12-31' ";
						}
				 } else {
							$str_period = " bcm_datetime_stop > now() or bcm_datetime_stop = '2099-12-31' ";
				 }
		
				 //auto_id, bcm_date_added,  bcm_cusip,  bcm_datetime_start,  bcm_datetime_stop,  bcm_auto,  bcm_open_end,  bcm_comment  
				 $qry_rlist = "SELECT * FROM `bcm_watchlist` 
											 where " . $str_period . " 
											 ORDER BY bcm_cusip, bcm_datetime_stop desc";
				 $result_rlist = mysql_query($qry_rlist) or die(tdw_mysql_error($qry_rlist));
					
				 $hold_symbol = "";
				 $count_row = 0;
				 while ($row = mysql_fetch_array($result_rlist)) {
					 if ($hold_symbol == "" or $row["bcm_cusip"] != $hold_symbol) {
								if ($count_row%2 == 0) {
									$rowclass = " class=\"trlight\"";
								} else {
									$rowclass = " class=\"trdark\"";
								}
						
								if ($row["bcm_datetime_stop"] == "2099-12-31 00:00:00") {
									$str_show_end = "";
								} else {
									$str_show_end = date("m/d h:ia",strtotime($row["bcm_datetime_stop"]));
								}
						?>
						<tr <?=$rowclass?>>
							<td><img src="images/themes/standard/delete.gif" border="0" alt="Remove" /></td> <!-- onclick="info_show('<?=$row["auto_id"]?>^<?=$row["bcm_cusip"]?>');" -->
							<td>&nbsp;&nbsp;<?=$row["bcm_cusip"]?></td>
							<td><?=str_replace(date("m/d "),"",date("m/d h:ia",strtotime($row["bcm_datetime_start"])))?></td>
							<td><?=$str_show_end?></td>
							<td><?=$row["bcm_comment"]?></td>
							<td>&nbsp;</td>
						</tr>
						<?
						$count_row++;
						}
 				  $hold_symbol = $row["bcm_cusip"];
					}
					?>
					</table>
					</div>
					</div>
					<? tep(); ?>
      		<input type="hidden" name="max_row_id" id="max_row_id" value="<?=$max_row_id?>" />
		</td>
	</tr>
</table>

		 <div id="err_notify" class="font_bcm_error"></div>
     <!-- END CONTAINER -->