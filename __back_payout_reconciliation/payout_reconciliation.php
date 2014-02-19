<?
include('pay_analyst_js.php');
include('pay_analyst_css.php');
?>

<script language="JavaScript" src="includes/js/ajax_tbx.js"></script>
<script language ="Javascript">
<!--

error_reporting(E_ALL);

function procx(str) {
	var arr_str = new Array();
	arr_str=str.split("^");
	if (arr_str[0]=="b") {
		newStr = "You have chosen to UNLOCK Analyst Allocations for " + arr_str[5] + "\n" + "for Q" + arr_str[2] + " " + arr_str[3] + ".";
	} else {
		newStr = "You have chosen to SEND EMAIL REMINDER to " + arr_str[5] + "\n" + "for completion of Analyst Allocations for period Q" + arr_str[2] + " " + arr_str[3] + ".";
	}
	input_box=confirm( newStr + "\n\n" + "Are you sure?");
	if (input_box==true)	{ 
		AjaxRequest.get(
			{
				'url':'pay_analyst_summ_ajax.php?str='+ str
				,'onSuccess':function(req){ 
																		parse_req(req.responseText);
																	}
				,'onError':function(req){ document.getElementById('notify').innerHTML='Program Error! Please contact Technical Support.';}
			}
		);
	} else {
		return false;
	}
}




function parse_req(response) {
	var arr_response = new Array();
	arr_response=response.split("^");
	//scount is [2] passed back
	if(arr_response[0]=="c") {
		document.getElementById('notify').innerHTML=arr_response[1]; 
		document.getElementById('notify').className="notify_x"; 
		var citemid = 'c' + arr_response[2];
		document.getElementById(citemid).innerHTML="&nbsp;&nbsp;&nbsp;Email Sent";
	} else {
		document.getElementById('notify').innerHTML=arr_response[1]; 
		document.getElementById('notify').className="notify_x";
		var bitemid = 'b' + arr_response[2];
		document.getElementById(bitemid).innerHTML="&nbsp;&nbsp;&nbsp;Unlocked";
		var aitemid = 'a' + arr_response[2];
		document.getElementById(aitemid).innerHTML="&nbsp;&nbsp;&nbsp;<strong><font color='red'>No</font></strong>";
		
	}
	//alert($response);
}

function noenter() {
  return !(window.event && window.event.keyCode == 13); }
-->
</script>

<?
/*
function create_arr ($q) {
  $arr_created = array();
  $result = mysql_query($q) or die(tdw_mysql_error($q));
	while ( $row = mysql_fetch_array($result) )
	{
		array_push($arr_created, $row[0]);
	}
	return $arr_created;
}
*/
tsp(100, "Payout Reconciliation Summary and Reporting");
?>
<style type="text/css">
<!--
.notify_x {
	font-family: verdana;
	font-size: 12px;
	font-weight: bold;
	color: #009900;
	text-decoration: none;
	background-color: #E6FFE6;
	border-top-width: 1px;
	border-bottom-width: 1px;
	border-top-style: solid;
	border-right-style: none;
	border-bottom-style: solid;
	border-left-style: none;
	border-top-color: #00CC33;
	border-bottom-color: #00CC33;
}
-->
</style>

<table width="100%" border="0" cellpadding="4" cellspacing="0"> 
	<tr>
		<td>
		<!-- Top Menu -->		
		<form name="frm_criteria" action="<?=$PHP_SELF?>" method="get"><!-- onsubmit="return check_frm_criteria()"-->
		<select name="sel_qtr">
			<option value="">Select Quarter</option>
			<option value="1" <? if ($sel_qtr == 1) { echo "selected";}?>>Qtr. 1</option>
			<option value="2" <? if ($sel_qtr == 2) { echo "selected";}?>>Qtr. 2</option>
			<option value="3" <? if ($sel_qtr == 3) { echo "selected";}?>>Qtr. 3</option>
			<option value="4" <? if ($sel_qtr == 4) { echo "selected";}?>>Qtr. 4</option>
		</select>		
		&nbsp;&nbsp;&nbsp;
		<!--<select name="sel_year">
			<option value="">Select Year</option>
			<option value="2007" <? if ($sel_year == 2007) { echo "selected";}?>>2007</option>
			<option value="2008" <? if ($sel_year == 2008) { echo "selected";}?>>2008</option>
			<option value="2009" <? if ($sel_year == 2009) { echo "selected";}?>>2009</option>
			<option value="2010" <? if ($sel_year == 2010) { echo "selected";}?>>2010</option>
			<option value="2011" <? if ($sel_year == 2011) { echo "selected";}?>>2011</option>
		</select>	-->	
		<select name="sel_year">
			<option value="">Select Year</option>
		  <?
				$arr_yrs = array();
				for ($i=0;$i<10;$i++) {
					$arr_yrs[] = date('Y') - $i;
				}
				
				foreach($arr_yrs as $k=>$v) {
					?>
            <option value="<?=$v?>" <? if ($sel_year == $v) { echo "selected";} ?> ><?=$v?></option>
          <?
				}
			?>
		</select>		
		&nbsp;&nbsp;&nbsp;
		<input type="image" src="images/lf_v1/form_submit.png"/>
		&nbsp;&nbsp;&nbsp;
		<?
		if ($sel_qtr != "" AND $sel_year != "") {
		$str_xl = $user_id."^".$rr_num."^".$sel_qtr."^".$sel_year;
		?>
		<!-- <a href="pay_analyst_excel.php?xl=<?=$str_xl?>" target="_blank"><img src="images/lf_v1/exp2excel.png" border="0" /></a> -->
		<?
		}
		?>
		</form>
	<br><a href="<?=$PHP_SELF?>?<?=$_SERVER['QUERY_STRING']?>&action=excel" target="_blank"><img src="images/lf_v1/exp2excel.png" alt="Export to Excel" border="0"></a>
		<!-- End Top Menu -->		
		</td>
	</tr>
</table>
<?
if ($sel_qtr != "" AND $sel_year != "") {



include('payout_reconciliation_inc_main.php');

include('payout_reconciliation_main_more.php');














	//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@


 //print_r($arr_composite_primary);	
 //print_r($arr_clnt_rr_initials);
	// ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ 
	//SHOW THE DATA
	?>
		<div id="notify">&nbsp;</div>
		<?ob_start();?>
		<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
			<tr>
				<td>
					<!--TABLE 2 START-->
					<script language="JavaScript" type="text/javascript"></script>

					<table width="100%"  border="0" cellspacing="1" cellpadding="0">
						<tr>
							<td bgcolor="#222222" width="200"><a class="tblhead_a">Analyst</a></td>
							<td bgcolor="#222222" width="80"><a class="tblhead_a">Sales</a></td>
							<td bgcolor="#222222" width="130"><a class="tblhead_a">Diff</a></td>
							<td bgcolor="#222222" width="130"><a class="tblhead_a">Client</a></td>
							<td bgcolor="#222222">&nbsp;</td>
						</tr>
	<?

//print_r($GLOBALS);
/*		//Create Array of all clients to show here
		$arr_quarter_brok_dates = get_quarter_dates ($sel_qtr, $sel_year);
		$arr_quarter_cal_dates = get_quarter_dates ($sel_qtr, $sel_year, "C");
*/
//		$pay_adv_q = "SELECT distinct pay_advisor_code FROM pay_analyst_allocations WHERE pay_sales_id=".$user_id." and pay_year=".$sel_year." and  pay_qtr=".$sel_qtr;
		$pay_adv_q = "SELECT distinct pay_advisor_code FROM pay_analyst_allocations WHERE pay_year=".$sel_year." and  pay_qtr=".$sel_qtr;
		$pay_advisor_code = create_arr($pay_adv_q);


		if (count($pay_advisor_code)) {


		$advisor_codes = implode(",", $pay_advisor_code);
		$advisor_codes = str_replace(",",'","',$advisor_codes);
		$advisor_codes = '("'.$advisor_codes.'")';



		$commissions_q = "SELECT trad_trade_date, trad_advisor_code, sum(trad_commission) as commission
			FROM mry_comm_rr_trades
			WHERE trad_trade_date between '".$arr_quarter_cal_dates[0]."' AND '".$arr_quarter_cal_dates[1]."' and trad_is_cancelled = 0 AND
			trad_advisor_code in ".$advisor_codes."
			GROUP by trad_advisor_code";

//print_r($commissions_q);
/*
		SELECT r, trad_advisor_code, sum(trad_commission) as commission
			FROM mry_comm_rr_trades
			where 
			trad_advisor_code in ".$advisor_codes."
			GROUP by trad_advisor_code";
		//print_r($commissions_q);
*/
		$scount = 1;

		$result_comm = mysql_query($commissions_q) or die (tdw_mysql_error($result_comm));
		$total_sales = 0;
		$total_analyst = 0;



						foreach ($arr_master_clnt_rr as $k=>$v) {
							$total_commission = $arr_master_composite[$k];  //arr_composite_primary

//			$total_commission = $arr_composite_primary[$k];  //arr_composite_primary
			$check[0] = $arr_comm_for_rr_comm[$k];//$arr_composite_shared[$k];  //arr_composite_primary



			if ($check[0]!=$total_commission) {
				$diff_style = "style=\"color: red\"";
			}
			else
				
				$diff_style = "";

			?>

						<tr <?=$diff_style?> <? if ($scount % 2) { echo "class='trlight'"; } else { echo "class='trdark'";}?>>
							<td><?=$total_commission+0.0?></td>
							<td><?=$check[0]+0.0?></td>
							<td ><?=$total_commission-$check[0]?></td>
							<td><?=$v?></td>
							<td>&nbsp;</td>
						</tr>
<?
		$scount = $scount + 1;
		$total_sales += $total_commission;
		$total_analyst += $total_commission+$check[0];
		$total_diff += $check[0];
		}
	
		?>

						<tr>
							<td bgcolor="#222222" align="center" colspan="3" border="1"><a class="tblhead_a">Total</a></td>
							<td bgcolor="#222222">&nbsp;</td>
							<td bgcolor="#222222">&nbsp;</td>
						</tr>
						<tr class='trlight'>
							<td><?=$total_analyst?></td>
							<td><?=$total_sales?></td>
							<td><?=$total_diff?></td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							
						</tr>
<?

	}
		?>
					</table>
				</td>
			</tr>
		</table>







		<?
	if ($_GET["action"]=="excel") {
		$output_filename = "rr_list.xls";
		$fp = fopen($exportlocation.$output_filename, "w");
		fputs ($fp, ob_get_contents());
		Header("Location: http://192.168.20.63/tdw/fileserve_xls.php?l=data/exports/&f=".$output_filename);
		exit;
	}

	// ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ 

	//@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@
} else {
	echo "&nbsp;&nbsp;Please select Quarter and Year.<br /><br />";
}

tep();

?>