<link rel="stylesheet" type="text/css" href="includes/styles.css">
<?
include('includes/functions.php');
include('includes/dbconnect.php');
include('includes/global.php');

include('fidemp_functions.php');

$arr_debug_info = array();


//function (this page debug)
function zdebug ($n,$v) {
	$x = 1;
	if ($x==1) {
		echo "<font color='green'>".$n . " = [".$v."]</font><br>"; 
	}
}

$arr_brk = explode('^',$sel_month);
$brk_month = $arr_brk[0];
$brk_year = $arr_brk[1];

//Get dates for the selected calendar month
$arr_cal_dates  = get_calendar_month_dates($brk_month,$brk_year);
$cal_start_date = $arr_cal_dates[0];
$cal_end_date   = $arr_cal_dates[1];

xdebug("Selected Period",$cal_start_date . " " .$cal_end_date);

//Start creating arrays

//account and names
$qry = "SELECT distinct(acct_num) as acct_num, concat(trim(first_name),' ',trim(middle_name),' ',trim(last_name)) as accname FROM fidelity_emp_trades 
				WHERE commission = 8
				AND trade_date between '".$cal_start_date."' and '".$cal_end_date."'
				group by acct_num
				order by acct_num";
$result = mysql_query($qry) or die (tdw_mysql_error($qry));
$arr_accnames = array();
while ( $row = mysql_fetch_array($result) ) 
{
	$arr_accnames[$row["acct_num"]] = $row["accname"]; 
}

//arr account vs trades at 8
$qry = "SELECT acct_num, count(acct_num) as count8 FROM fidelity_emp_trades 
				WHERE commission = 8
				AND trade_date between '".$cal_start_date."' and '".$cal_end_date."'
				group by acct_num";
$result = mysql_query($qry) or die (tdw_mysql_error($qry));
$arr_count_8 = array();
while ( $row = mysql_fetch_array($result) ) 
{
	$arr_count_8[$row["acct_num"]] = $row["count8"]; 
}

//arr account vs commision at 8
$qry = "SELECT acct_num, sum(commission) as commission FROM fidelity_emp_trades 
				WHERE commission = 8
				AND trade_date between '".$cal_start_date."' and '".$cal_end_date."'
				group by acct_num";
$result = mysql_query($qry) or die (tdw_mysql_error($qry));
$arr_comm_8 = array();
while ( $row = mysql_fetch_array($result) ) 
{
	$arr_comm_8[$row["acct_num"]] = $row["commission"]; 
}

//arr account vs trades NOT 8
$qry = "SELECT acct_num, count(acct_num) as countn8 FROM fidelity_emp_trades 
				WHERE commission <> 8
				AND trade_date between '".$cal_start_date."' and '".$cal_end_date."'
				group by acct_num";
$result = mysql_query($qry) or die (tdw_mysql_error($qry));
$arr_count_n8 = array();
while ( $row = mysql_fetch_array($result) ) 
{
	$arr_count_n8[$row["acct_num"]] = $row["countn8"]; 
}

//arr account vs commision NOT 8
$qry = "SELECT acct_num, sum(commission) as commission FROM fidelity_emp_trades 
				WHERE commission <> 8
				AND trade_date between '".$cal_start_date."' and '".$cal_end_date."'
				group by acct_num";
$result = mysql_query($qry) or die (tdw_mysql_error($qry));
$arr_comm_n8 = array();
while ( $row = mysql_fetch_array($result) ) 
{
	$arr_comm_n8[$row["acct_num"]] = $row["commission"]; 
}

//show_array($arr_comm_n8);

//initiate page load time routine
$time=getmicrotime(); 

xdebug("Process initiated at ",date('m/d/Y H:i:s a'));

//We give the path to our file here
//generate a random filename

$xlfilename = date('m-d_h.ia')."__".substr(md5(rand(1000000000,9999999999)),0,2).".xls";
//$xlfilename = "test.xls";
$fp = fopen("D:/tdw/tdw/data/xls/".$xlfilename, "w");

//$string = "\"Date\",\"Client Code\",\"Client Name\",\"Amount\",\"Type\",\"Reps\",\"Rep#\",\"Comments\",\"Entered By"."\"".chr(13); 

$str = '<html xmlns="http://www.w3.org/1999/xhtml">
				<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>
				<body>';
fputs ($fp, $str);

//50 60 100 200 10 100 100 10 100 100 10 100 100 100 100				
$str = '<table width="800" border="1" cellspacing="0" cellpadding="0">
					<tr>
						<td width="30">&nbsp;</td>
						<td width="60">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="200">&nbsp;</td>
						<td width="10">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="10">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="10">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
					</tr>';
fputs ($fp, $str);

$str = '<tr>
					<td colspan="15" align="center"><font size="+2"><b>Fidelity - Buckingham Research Group</b></font></td>
				</tr>
				<tr>
					<td colspan="15" align="center"><font size="+1"><b>Monthly Trading Cost Summary</b></font></td>
				</tr>';
fputs ($fp, $str);

/*
					Trading @ $8/Trade			Trading @ Other						
		Account #	Account Name		Trades 	Amount		Trades 	Amount		Margin Interest Charged	12b 1 Fees	Other	Total

z	z	Account #	Account Name	z	Trades 	Amount	z	Trades 	Amount	z	Margin Interest Charged	12b 1 Fees	Other	Total	

*/

$str = '<tr>
					<td>&nbsp;</td>				<td>&nbsp;</td>					<td>&nbsp;</td>					<td>&nbsp;</td>					<td>&nbsp;</td>
					<td colspan="2">Trading @ $8/Trade</td>
					<td>&nbsp;</td>
					<td colspan="2">Trading @ Other</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td><td>&nbsp;</td>					
					<td>Account #</td>					
					<td>Account Name</td><td>&nbsp;</td>
					<td>Trades</td>
					<td>Amount</td>
					<td>&nbsp;</td>
					<td>Trades</td>
					<td>Amount</td>
					<td>&nbsp;</td>
					<td>Margin Interest Charged</td>
					<td>12b 1 Fees</td>
					<td>Other</td>
					<td>Total</td>
				</tr>';
fputs ($fp, $str);

//loop through the array now
$xcount = 1; 
foreach($arr_accnames as $k=>$v) {

$str = 	'<tr>
					<td>&nbsp;</td><td>'.$xcount.'</td>					
					<td align="left">'.$k.'</td>					
					<td>'.substr($v, 0, 20).'</td><td>&nbsp;</td>
					<td>'.$arr_count_8[$k].'</td>
					<td>$'.number_format($arr_comm_8[$k],2,'.',',').'</td>
					<td>&nbsp;</td>
					<td>'.$arr_count_n8[$k].'</td>
					<td>';
					
					if ($arr_count_n8[$k]) { 
					$str .= "$".number_format($arr_comm_n8[$k],2,'.',',');
					} else {
					$str .= "";
					}
					
$str .=  '</td>
					<td>&nbsp;</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>';
fputs ($fp, $str);
$xcount++;
	
}


//Get totals
$total_8 = db_single_val("SELECT sum(commission) as single_val FROM fidelity_emp_trades 
													WHERE commission = 8
													AND trade_date between '".$cal_start_date."' and '".$cal_end_date."'");
$total_n8 = db_single_val("SELECT sum(commission) as single_val FROM fidelity_emp_trades 
													WHERE commission <> 8
													AND trade_date between '".$cal_start_date."' and '".$cal_end_date."'");


$str = '<tr>
					<td>&nbsp;</td><td>&nbsp;</td>					
					<td>&nbsp;</td>					
					<td><b>TOTAL</b></td><td>&nbsp;</td>
					<td></td>
					<td>$'.number_format($total_8,2,'.',',').'</td>
					<td>&nbsp;</td>
					<td></td>
					<td>$'.number_format($total_n8,2,'.',',').'</td>
					<td>&nbsp;</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>';
fputs ($fp, $str);


$str = '</table>';
fputs ($fp, $str);

$str = '</body></html>';
fputs ($fp, $str);

fclose($fp);

//Header("Location: http://192.168.20.63/tdw/data/xls/test.xls");

//show page load time
	echo "Report generated in ". sprintf("%01.2f",((getmicrotime()-$time)/1000))." s.\n<br>"; 						

?>
<!--<p class="ilt">Following is the preformatting for printing the Summary Report<br />
- LEGAL<br />
- Landscape<br />
- 1 Page Wide by 1 Page Tall 
<br />
Should you want to print in a format other than this, please use Page Setup in Excel to get the desired print output.<br /></p>-->
<a href="http://192.168.20.63/tdw/fileserve_xls.php?l=data/xls/&f=<?=$xlfilename?>" target="_blank">Click here to download the generated report (File Format: Excel)</a><br /><br />
<?
xdebug("Process ". $rnd_process_id . " completed at ",date('m/d/Y H:i:s a'));
//echo "RR^NAME^TOTALCOMM^TOTALCHECKS^STANDARDPAY^RATE^SPECIALPAY^ROLLING12MON"."<br>";
//show_array($arr_master);
//show_array($arr_sp_payout);
//show_array(sp_payout_rate_alt('AIMA', '', $arr_sp_payout));
?>
<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
