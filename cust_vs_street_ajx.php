<script language="JavaScript" src="includes/wz/wz_tooltip.js" type="text/javascript"></script>
<style>
.sdata {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 11px;
	text-transform: capitalize;
}
.headsub {
	background-color: #0000CC;
	font-weight: bold;
	font-size: 11px;
	color: #99FFFF;
}
.light2 {
	background-color: #FFFFFF;
}
.dark2 {
	background-color: #EFEFEF;
}
</style>
<?

  include('includes/dbconnect.php');
  include('includes/global.php');
  include('includes/functions.php');

//*********************************************************************************************

function previous_bizday ($dateval=NULL) {

	if ($dateval==NULL) {
		$working_dateval = date('Y-m-d');
	} else {
		$working_dateval = $dateval;
	}
	
	$i = 1;
	while ($i < 7) {
		 if (date("w",strtotime($working_dateval)-(60*60*24*$i)) > 0 AND
				 date("w",strtotime($working_dateval)-(60*60*24*$i)) < 6 AND
				 check_holiday(date("Y-m-d", strtotime($working_dateval)-(60*60*24*$i))) == 0 ) {
				$val_pbd = date("Y-m-d",strtotime($working_dateval)-(60*60*24*$i));
			 return $val_pbd;
		 } else {
				$i = $i + 1;
		 }
	}
}

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//declare some arrays
$arr_street = array();
$qry_street = "SELECT
								Ticker,
								Quantity,
								fill_price,
								buy_sell,
								Exchange,
								count_Exchange,
								customer_id,
								trade_date,
								exec_broker,
								count_exec_broker,
								min_manual_time,
								max_manual_time,
								parent_id,
								count_parent_id
							FROM tradeware_trades_consolidated 
							where trade_date = '".$spotdate."' 
							AND Ticker = '".$symbol."'";

$result_street = mysql_query($qry_street) or die(tdw_mysql_error($qry_street));
while ($row = mysql_fetch_array($result_street)) {
	$arr_street[] =	$row["trade_date"]."^".
	                $row["customer_id"]."^".
									$row["Ticker"]."^".
									$row["buy_sell"]."^".
									$row["Quantity"]."^".
									$row["Exchange"]."^".
									$row["exec_broker"]."^".
									$row["parent_id"]."^".
									$row["fill_price"];
}

//show_array($arr_street);
//exit;

?>

<STYLE>
<!--
blink {color: red}
-->
</STYLE>
<SCRIPT>
<!--
function doBlink() {
	var blink = document.all.tags("BLINK")
	for (var i=0; i<blink.length; i++)
		blink[i].style.visibility = blink[i].style.visibility == "" ? "hidden" : "" 
}

function startBlink() {
	if (document.all)
		setInterval("doBlink()",300)
}
window.onload = startBlink;
// -->
</SCRIPT>

    <table class="sdata" cellspacing="0" cellpadding="2">
    <tr class="headsub">
    <td>Symbol</td>
    <td>Customer</td>
    <td>B/S</td>
    <td>Quantity</td>
    <td>Price</td>
    <td>Exchange</td>
    <td>Broker</td>
    <td>ID</td>
    </tr>
    <?
    $count_sub++;
    foreach ($arr_street as $sindx=>$svalstr) {
    		$arr_temp_street = explode("^",$svalstr);
    		?>	
        <tr>
        	<td><?=$arr_temp_street[2]?></td>
        	<td><?=$arr_temp_street[1]?></td>
        	<td><?=$arr_temp_street[3]?></td>
        	<td align="right"><?=number_format($arr_temp_street[4],0,"",",")?></td>
        	<td align="right"><?=number_format($arr_temp_street[8],2,".",",")?></td>
        	<td><?=$arr_temp_street[5]?></td>
        	<td><?=$arr_temp_street[6]?></td>
        	<td><?=$arr_temp_street[7]?></td>
        </tr>
        <?
		    $count_sub++;
    }
    ?>
    </table>