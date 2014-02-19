<?php

  include('top.php');
	 
	include('includes/functions.php'); 
	
	//FOR DATES OTHER THAN PREVIOUS BUSINESS DAY, CREATE MECHANISM TO HANDLE IT.
	//$trade_date_to_process = format_date_ymd_to_mdy(previous_business_day());
	$trade_date_to_process = previous_business_day();
	//$trade_date_to_process = '2004-02-20';
	
?>

<tr><td align="right" valign="top">

				<table class="tablewithdata" width="100%"  border="0" cellspacing="0" cellpadding="0">
					<tr><form action="<?=$PHP_SELF?>?action=filter" id="filtertrade" method="post"> 
						<td width="300" align="left" valign="middle" class="links12">Select Trade Date</td>
						<td align="right">
						<select class="Text" name="trdm_trade_date" size="1" >
						<option value="">&nbsp;&nbsp;&nbsp;TRADE DATE&nbsp;&nbsp;&nbsp;</option>
						<option value="">==========</option>
						<?
						
						$i = 1;
						while ($i < 30) {

						$previoustime = time() - (60*60*24*$i);
						$previousday = date("Y-m-d", $previoustime);
 
 						if (date("l", $previoustime) == "Sunday") {
						$previoustime = time() - (60*60*24*($i+2));
						$previousday = date("Y-m-d", $previoustime);
						$i = $i+2 + 1;	
 							if ( check_holiday($previousday) == 1) {						
							$previoustime = time() - (60*60*24*($i));
							$previousday = date("Y-m-d", $previoustime);
							$i = $i+1;	
							}
						} elseif (date("l", $previoustime) == "Monday" and check_holiday($previousday) == 1) {
						$previoustime = time() - (60*60*24*($i+3));
						$previousday = date("Y-m-d", $previoustime);
						$i = $i+3 + 1;	
						} else {
						$previousday = "ERROR!";
						$i = $i+1;						
						}
  						
					 ?>
						<option value="<?=date("Y-m-d", time() - (60*60*24*($i-1)))?>"><?=date("m-d-Y", time() - (60*60*24*($i-1)))?></option>
						
						<?
						}						
						?>
						
						</select>
						
						<input class="Submit" name="submit1" type="submit" value="  Get Report  ">
						</td>
						
					</form></tr>
				</table>
				
<!--</td></tr>

<tr><td align="left" valign="top">-->

<table width="100%" cellpadding="5" cellspacing="5" border="1">

	<tr valign="top">

		<td>
					<!--Table with thin cell border-->
					<? if ($trdm_trade_date != '') {
					?>
					<center>
					<a class="links12">To access report for Trade Date <?=$trdm_trade_date?>, click </a><a class="links12" href="data/exports/Trades_Report_<?=$trdm_trade_date?>.html" target="_blank">HERE.</a>
					
					
					</center>
					<?
					}
					?>
      		<!--Table with thin cell border ends-->

  </td>
	</tr>
</table>

</td>
</tr>


<?php
  include('bottom.php');
?>


