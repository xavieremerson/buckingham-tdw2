<?
//Since this is a AJAX requested page, all inputs to this page should be passed with the param string
//Also, all the relevant includes should be a part of this page including css, etc
include('includes/global.php');
include('includes/dbconnect.php');
include('includes/functions.php');
?>
<script language='JavaScript'>
function showhidedetail(divid,ifid) { 
	if (document.getElementById) { // DOM3 = IE5, NS6 Generally this is what it is

		if (document.getElementById(divid).style.getAttribute("visibility") == "" || document.getElementById(divid).style.getAttribute("visibility") == "hidden" ) {
		document.getElementById(divid).style.visibility = 'visible'; 
		document.getElementById(divid).style.display = 'block'; 
		document.getElementById('img'+divid).src = 'images/lf_v1/collapse.png';
		autofitIframe(ifid);
		} else {
		document.getElementById(divid).style.visibility = 'hidden'; 
		document.getElementById(divid).style.display = 'none'; 
		document.getElementById('img'+divid).src = 'images/lf_v1/expand.png';
		autofitIframe(ifid);
		}		

	} 
	else { 
		if (document.layers) { // Netscape 4 
			alert("Netscape 4");
			document.AELT.visibility = 'visible'; 
		} 
		else { // IE 4 
			alert("IE 4");
			document.all.AELT.style.visibility = 'visible'; 
		}
	} 
} 
</script>
<?
if ($_GET) { // form submitted //format_date_ymd_to_mdy($trade_date_to_process)
//assign the variables here
$ifid = $ifid;
$rep_to_process = $rep_num;
$trade_date_to_process = $tdate;
$process_advisor_code_subacct = $adv;
} else {
$ifid = "test";
$rep_to_process = "045";
$process_advisor_code_subacct = "AELT";
$trade_date_to_process = previous_business_day();
}
?>

<script language="JavaScript" type="text/JavaScript">
function autofitIframe(id){
	if (!window.opera && !document.mimeType && document.all && document.getElementById){
      parent.document.getElementById(id).style.height=this.document.body.offsetHeight+"px";
    }
    else if(document.getElementById) {
    parent.document.getElementById(id).style.height=this.document.body.scrollHeight+"px"
   }
}
</script>
<link href="includes/styles.css" rel="stylesheet" type="text/css">
<!--onload="autofitIframe('ca_trades')"--> 
<body onLoad="autofitIframe('<?=$ifid?>')">


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
<?
//get all subaccounts for the selected advisor as of the trade date selected (do not show subaccounts of the future)
$query_level_b_acct = "SELECT distinct(comm_account_number) as comm_account_number
											FROM mry_comm_rr_level_b
											WHERE comm_advisor_code = '".$process_advisor_code_subacct."' 
											AND comm_rr = '".$rep_to_process."'
											AND comm_trade_date <= '".$trade_date_to_process."'
											ORDER BY comm_account_number";
//xdebug("query_level_b_acct",$query_level_b_acct);											
$result_level_b_acct = mysql_query($query_level_b_acct) or die(mysql_error());
$is_empty = mysql_num_rows($result_level_b_acct);
if ($is_empty == 0) {
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;---"; //something to allow for autofit of iframe to work.
}

$level_b_count = 1; //for css style

while($row_level_b_acct = mysql_fetch_array($result_level_b_acct))
	{

	$comm_account_number = $row_level_b_acct["comm_account_number"];

	//get data for advisor
	$query_level_b_acct_date = "SELECT max(comm_trade_date) as comm_trade_date
														FROM mry_comm_rr_level_b
														WHERE comm_rr = '".$rep_to_process."'
														AND comm_trade_date <= '".$trade_date_to_process."'
														AND UPPER(comm_account_number) = '".$comm_account_number."'";
														//0413 PLAIN ACCT NUMBER COMDITION FAILED HENCE USING UPPER
  //xdebug("query_level_b_acct_date",$query_level_b_acct_date);											
														
	$result_level_b_acct_date = mysql_query($query_level_b_acct_date) or die(tdw_mysql_error($query_level_b_acct_date));
	while($row_level_b_acct_date = mysql_fetch_array($result_level_b_acct_date))
	{
		$acct_date_val = $row_level_b_acct_date["comm_trade_date"];
  	//xdebug("acct_date_val",$acct_date_val);											
	}
	
	//get data from mry_comm_rr_level_b
	//fields are comm_rr  comm_trade_date  comm_advisor_code  
	//comm_account_name  comm_account_number  comm_total  comm_mtd  comm_qtd  comm_ytd 
	//xdebug("adv_date_val",$adv_date_val);
	//xdebug("trade_date_to_process",$trade_date_to_process);
	if ($acct_date_val != "") { //Proceed with processign subaccounts only if they exist
	
			//initialize the variables used
			//$show_previous_day_comm_sa = "";
			//$show_mtd_sa = "";
			//$show_qtd_sa = "";
			//$show_ytd_sa = "";

	
			if ($acct_date_val == $trade_date_to_process) { //data available for trade_date_to_process
					$query_level_b = "SELECT * 
														FROM mry_comm_rr_level_b
														WHERE comm_rr = '".$rep_to_process."'
														AND UPPER(comm_account_number) = '".$comm_account_number."'
														AND comm_trade_date = '".$acct_date_val."'";
					//xdebug("query_level_b",$query_level_b);											
					$result_level_b = mysql_query($query_level_b) or die(mysql_error());
					while($row_level_b = mysql_fetch_array($result_level_b)) 
					{
		
						$show_account_name = $row_level_b["comm_account_name"];
						$show_rr = $row_level_b["comm_rr"];
						$show_previous_day_comm_sa = number_format($row_level_b["comm_total"],0,'.',",");
						$show_mtd_sa = number_format($row_level_b["comm_mtd"],0,'.',",");
					//xdebug("show_mtd_sa",$show_mtd_sa);											
						$show_qtd_sa = number_format($row_level_b["comm_qtd"],0,'.',",");
					//xdebug("show_qtd_sa",$show_qtd_sa);											
						$show_ytd_sa = number_format($row_level_b["comm_ytd"],0,'.',",");
					//xdebug("show_ytd_sa",$show_ytd_sa);											
			
					}
			} else { //data not available for trade_date_to_process
					$query_level_be = "SELECT * 
														FROM mry_comm_rr_level_b
														WHERE comm_rr = '".$rep_to_process."'
														AND UPPER(comm_account_number) = '".$comm_account_number."'
														AND comm_trade_date = '".$acct_date_val."'";
					//xdebug("query_level_be",$query_level_be);											
					$result_level_be = mysql_query($query_level_be) or die(mysql_error());
					while($row_level_be = mysql_fetch_array($result_level_be)) 
					{
						$show_account_name = $row_level_be["comm_account_name"];
						$show_rr = $row_level_be["comm_rr"];
						$show_previous_day_comm_sa = '<a class="display_zero">'."0"."</a>";
						
						//$show_mtd_sa = number_format($row_level_be["comm_mtd"],0,'.',",");
						//$show_qtd_sa = number_format($row_level_be["comm_qtd"],0,'.',",");
						//$show_ytd_sa = number_format($row_level_be["comm_ytd"],0,'.',",");
						
																	$is_same_year = samebrokyear($acct_date_val,$trade_date_to_process);
																	$is_same_month = samebrokmonth($acct_date_val,$trade_date_to_process);
																	$is_same_qtr = samebrokqtr($acct_date_val,$trade_date_to_process);
																	//xdebug("adv_date_val",$adv_date_val);
																	//xdebug("trade_date_to_process",$trade_date_to_process);
																	//xdebug("is_same_year",$is_same_year);
																	//xdebug("is_same_month",$is_same_month);
																	//xdebug("is_same_qtr",$is_same_qtr);
																	
																	if ($is_same_month == 1) {
																					$show_mtd_sa = number_format($row_level_be["comm_mtd"],0,'.',",");
																	} else {
																					$show_mtd_sa = '<a class="display_zero">'."0"."</a>";
																	}
																	
																	if ($is_same_qtr == 1) {
																					$show_qtd_sa = number_format($row_level_be["comm_qtd"],0,'.',",");
																	} else {
																					$show_qtd_sa = '<a class="display_zero">'."0"."</a>";
																	}
																	
																	if ($is_same_year == 1) {
																					$show_ytd_sa = number_format($row_level_be["comm_ytd"],0,'.',",");
																	} else {
																					$show_ytd_sa = '<a class="display_zero">'."0"."</a>";
																	}
						
					}
			}
		}

	if ($level_b_count % 2) { 
			$class_row = "trdarksub";
	} else { 
			$class_row = "trlightsub"; 
	} 
?>
																				
	<tr class="<?=$class_row?>"> 
		<td align="left">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <?=$show_account_name?>&nbsp;&nbsp;</td>
		<td>&nbsp;&nbsp;<?=$show_rr?></td>
		<td align="right"><?=$show_previous_day_comm_sa?>&nbsp; &nbsp;</td>
		<td align="right"><?=$show_mtd_sa?>&nbsp; &nbsp;</td>
		<td align="right"><?=$show_qtd_sa?>&nbsp; &nbsp;</td>
		<td align="right"><?=$show_ytd_sa?>&nbsp; &nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
<?
$level_b_count = $level_b_count + 1;                                          
}
?>
	<tr> 
		<td width="240" align="left">&nbsp;</td>
		<td width="40">&nbsp;&nbsp;</td>
		<td width="70" align="right">&nbsp; &nbsp;</td>
		<td width="70" align="right">&nbsp; &nbsp;</td>
		<td width="70">&nbsp;</td>
		<td width="80"align="right">&nbsp; &nbsp;</td>
		<td width="70">&nbsp;</td>
		<td width="70" align="right">&nbsp; &nbsp;</td>
		<td width="70">&nbsp;</td>
		<td width="70">&nbsp;</td>
		<td width="70">&nbsp;</td>
		<td width="70">&nbsp;</td>
		<td width="70">&nbsp;</td>
		<td width="80">&nbsp;</td>
		<td width="80">&nbsp;</td>
		<td>&nbsp;</td>
	</tr>

</table>
</body>