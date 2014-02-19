<?
//Since this is a IFRAME requested page, all inputs to this page should be passed with the param string
//Also, all the relevant includes should be a part of this page including css, etc
include('includes/global.php');
include('includes/dbconnect.php');
include('includes/functions.php');


$rep_to_process = $rep_id;
$trade_date_to_process = $tdate;
include('buss_src_inc_main.php');

//*********************************************************************************************
//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ 

		$previous_year_date = get_date_previous_year($trade_date_to_process);
		//Get all data from table into an array
		$qry_prev_year = "SELECT yrt_advisor_code, yrt_commission 
											FROM yrt_yearly_total_lookup
											WHERE yrt_rr  = '".$rep_to_process."'
											AND yrt_year = EXTRACT(YEAR FROM '".$previous_year_date."')
											GROUP BY yrt_advisor_code
											ORDER BY yrt_advisor_code";
		//xdebug('qry_prev_year',$qry_prev_year);
		$result_prev_year = mysql_query($qry_prev_year) or die (tdw_mysql_error($qry_prev_year));
		$arr_prev_year = array();
		while ( $row_prev_year = mysql_fetch_array($result_prev_year) ) 
		{
			$arr_prev_year[$row_prev_year["yrt_advisor_code"]] = $row_prev_year["yrt_commission"];
		}
		
		//print_r($arr_prev_year);

//*********************************************************************************************
//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
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
} 
</script>
<?
//show_array($_GET);
if ($_GET) { // form submitted //format_date_ymd_to_mdy($trade_date_to_process)
//assign the variables here
$ifid = $ifid;
$srep_user_id = $rep_id;
$trade_date_to_process = $tdate;
} else {
$ifid = "test";
$rep_to_process = "045";
$trade_date_to_process = previous_business_day();
}
?>

<script language="JavaScript" type="text/JavaScript">
function autofitIframe(id){
	if (!window.opera && !document.mimeType && document.all && document.getElementById){
      parent.document.getElementById(id).style.height=this.document.body.offsetHeight+"px";
    }
    else if(document.getElementById) {
    parent.document.getElementById(id).style.height=this.document.body.scrollHeight+"px";
   }
}
</script>

<script language="JavaScript" type="text/JavaScript">
//pass variables as separate args
function sh_level3s(divid,rrnum,tdate) { //show/hide level2 shared rep
  var trid;
	trid = 'if_'+ divid; 
	if (document.getElementById) { // DOM3 = IE5, NS6 Generally this is what it is

		if (document.getElementById(trid).style.getAttribute("visibility") == "" || document.getElementById(trid).style.getAttribute("visibility") == "hidden" ) {
			document.getElementById(trid).style.visibility = 'visible'; 
			document.getElementById(trid).style.display = 'block'; 
			document.getElementById('img'+ divid).src = 'images/lf_v1/collapse.png';
			if (document.getElementById(trid).src == "") {
			document.getElementById(trid).src='http://192.168.20.63/tdw/rep_bssrc_if_srep.php?ifid='+trid+'&rep_num='+rrnum+'&tdate='+tdate;
			}
			//alert(document.getElementById(trid).src)
		} else {
			document.getElementById(trid).style.visibility = 'hidden'; 
			document.getElementById(trid).style.display = 'none'; 
			document.getElementById('img'+ divid).src = 'images/lf_v1/expand.png';
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

<link href="includes/styles.css" rel="stylesheet" type="text/css">
<!--onload="autofitIframe('ca_trades')"--> 
														<table>
															<tr>
																<td colspan="14">
																	<table width="100%">
																		<tr>
																			<td>

<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
	<tr>
		<td valign="top">		
			<table width="100%"  border="0" cellspacing="1" cellpadding="0">
			<?
			$level_a_count = 0;
			$arr_main_processed = array();
			foreach($arr_clnt_for_rr as $k=>$v) {
				if ($k != '' AND ($arr_ytd_comm[$k]+$arr_ytd_check[$k])> 0) {

						$arr_main_processed[] = $k;
						//$arr_master[] = str_pad($k, 7).str_pad($arr_clients[$k],40).$arr_day_comm[$row_day_comm["trad_advisor_code"]]."<br>";
						if(get_previous_yr_data($k)> 0) {
							$pyc_percent = number_format((($arr_ytd_comm[$k]+$arr_ytd_check[$k])/get_previous_yr_data($k))*100,0,'','');
						} else {
							$pyc_percent = 0;
						}
						if ($level_a_count % 2) { 
								$class_row = "trdark";
						} else { 
								$class_row = "trlight"; 
						} 

				?>
					<tr class="<?=$class_row?>" >
						<td width="260" valign="middle"><a href='#'><img src="images/t12m_s.png" border="0" onclick="CreateWnd('chart_t12m.php?clnt=<?=$k?>', 620, 330, false);"></a>
						&nbsp;
						<?=look_up_client($k)?></td>
						<td width="70" align="right"><?=show_numbers($arr_day_comm[$k])?>&nbsp;&nbsp;</td>
						<td width="100" align="right"><?=show_numbers($arr_mtd_comm[$k])?>&nbsp;&nbsp;</td>
						<td width="100" align="right"><?=show_numbers($arr_qtd_comm[$k])?>&nbsp;&nbsp;</td>
						<td width="100" align="right"><?=show_numbers($arr_ytd_comm[$k])?>&nbsp;&nbsp;</td>
						<td width="70" align="right"><?=show_numbers($arr_mtd_check[$k])?>&nbsp;&nbsp;</td>
						<td width="70" align="right"><?=show_numbers($arr_qtd_check[$k])?>&nbsp;&nbsp;</td>
						<td width="70" align="right"><?=show_numbers($arr_ytd_check[$k])?>&nbsp;&nbsp;</td>
						<td width="70" align="right"><?=show_numbers($arr_mtd_comm[$k]+$arr_mtd_check[$k])?>&nbsp;&nbsp;</td>
						<td width="70" align="right"><?=show_numbers($arr_qtd_comm[$k]+$arr_qtd_check[$k])?>&nbsp;&nbsp;</td>
						<td width="80"align="right"><?=show_numbers($arr_ytd_comm[$k]+$arr_ytd_check[$k])?>&nbsp;&nbsp;</td>
						<td width="80" align="right"><?=show_numbers(get_previous_yr_data($k))?>&nbsp;&nbsp;</td>
						<td width="50" align="right"><?=$pyc_percent?>&nbsp;&nbsp;&nbsp;&nbsp;</td> 
						<td align="right">&nbsp;</td>
					</tr>
					<?
					$total_pbd = $total_pbd + $arr_day_comm[$k];
					$total_mtd = $total_mtd + $arr_mtd_comm[$k];
					$total_qtd = $total_qtd + $arr_qtd_comm[$k];
					$total_ytd = $total_ytd + $arr_ytd_comm[$k];
					$total_cmtd = $total_cmtd + $arr_mtd_check[$k]; 
					$total_cqtd = $total_cqtd + $arr_qtd_check[$k]; 
					$total_cytd = $total_cytd + $arr_ytd_check[$k];  
					$total_grand_mtd = $total_grand_mtd + $arr_mtd_comm[$k]+$arr_mtd_check[$k];
					$total_grand_qtd = $total_grand_qtd + $arr_qtd_comm[$k]+$arr_qtd_check[$k];
					$total_grand_ytd = $total_grand_ytd + $arr_ytd_comm[$k]+$arr_ytd_check[$k];

					$level_a_count = $level_a_count + 1;			
			}
		}													
		?>
		</table>
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
			 <tr class="display_totals"> 
				<td width="260" align="left">&nbsp;&nbsp;TOTALS:</td>
				<td width="70" align="right"><?=show_numbers($total_pbd)?>&nbsp;&nbsp;</td>
				<td width="100" align="right"><?=show_numbers($total_mtd)?>&nbsp;&nbsp;</td>
				<td width="100" align="right"><?=show_numbers($total_qtd)?>&nbsp;&nbsp;</td>
				<td width="100" align="right"><?=show_numbers($total_ytd)?>&nbsp;&nbsp;</td>
				<td width="70" align="right"><?=show_numbers($total_cmtd)?>&nbsp;&nbsp;</td>
				<td width="70" align="right"><?=show_numbers($total_cqtd)?>&nbsp;&nbsp;</td>
				<td width="70" align="right"><?=show_numbers($total_cytd)?>&nbsp;&nbsp;</td>
				<td width="70" align="right"><?=show_numbers($total_grand_mtd)?>&nbsp;&nbsp;</td>
				<td width="70" align="right"><?=show_numbers($total_grand_qtd)?>&nbsp;&nbsp;</td>
				<td width="80" align="right"><?=show_numbers($total_grand_ytd)?>&nbsp;&nbsp;</td>
				<td width="80" align="right">&nbsp;</td>
				<td width="50" align="right">&nbsp;</td>
				<td>&nbsp;</td>                          
			</tr>
		</table>
	</td>
</tr>
</table>

																			</td>
																		</tr>
																	</table>
																</td>
															</tr>
														</table>
