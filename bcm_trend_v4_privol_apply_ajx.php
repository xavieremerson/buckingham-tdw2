<?
include('includes/dbconnect.php');
include('includes/global.php');
include('includes/functions.php');


$startdate = format_date_mdy_to_ymd($datefrom);
$enddate = format_date_mdy_to_ymd($dateto);
$valsymbol = strtoupper(trim($symbol));
?>





<table border="0" cellpadding="0" cellspacing="0">
<tr>
	<td width="2" height="445" bgcolor="#3399FF"></td>
  <td width="10">&nbsp;</td>
  <td valign="top">
  <a class="ilt">PRICE / VOLUME CRITERIA APPLIED TO <?=$valsymbol?></a><br><br>
  <a class="ilt">DATE RANGE: </a><?=$datefrom . " to " .$dateto?><br><br>

	<?
	//create percentages for price
	$arr_prices = hist_prices($valsymbol, $startdate, $enddate);
	$arr_dates = array();
	$arr_vals = array();
	foreach($arr_prices as $k=>$v) {
		$arr_dates[] = $k;
		$arr_vals[] = $v;
	}

  //show_array($arr_prices);
  //show_array($arr_dates);
  //show_array($arr_vals);

	$arr_price_percent = array();
	for($i=0; $i<count($arr_prices)-1; $i++) {
	
		$arr_price_percent[$arr_dates[$i]] = round( (($arr_vals[$i] - $arr_vals[$i+1])/$arr_vals[$i])*100 , 2); //abs ();
	}

	$price_criteria = db_single_val("select bct_value as single_val from bcm_trend_config where bct_type = 'price' and bct_isactive = 1");
	//xdebug("price_criteria",$price_criteria);
	//show_array($arr_price_percent);

	$arr_price_percent_filtered = array();
	$arr_price_percent_filtered_dates = array();
	foreach($arr_price_percent as $k=>$v) {
		 //echo abs($v) ."//". abs($price_criteria)."<br>";
		 if ( abs($v) > abs($price_criteria) ) {
		 		$arr_price_percent_filtered[$k] = $v;
				$arr_price_percent_filtered_dates[] = $k;
		 }
	}
	
	//show_array($arr_price_percent_filtered);
	//show_array($arr_price_percent_filtered_dates);

	$str_dates = " ('".implode("','",$arr_price_percent_filtered_dates)."') ";

	
	//get trades from bcm fulfilling the criteria
		$query = "SELECT * FROM `oth_other_trades` 
										 where oth_symbol = '".$valsymbol."' 
											 and oth_trade_date in ".$str_dates." 
										 order by oth_trade_date desc";
		//xdebug("query",$query);
		$result = mysql_query($query) or die(tdw_mysql_error($query));

		$val_rows = mysql_num_rows($result);
		if ($val_rows > 0) {
		?>
            <a class="ilt">CRITERIA: PRICE CHANGE % ABOVE MONITORING THRESHOLD</a><br>
            <table border="1" cellpadding="2" cellspacing="0">
                <tr style="font:Arial; font-size:12px; font-weight:bold; color:#000066">
                    <td width="100">Date</td>
                    <td width="100" align="right">Quantity</td>
                    <td width="100" align="right">Buy/Sell</td>
                    <td width="100" align="right">Price</td>
                    <td width="100" align="right" nowrap="nowrap">% change Closing Price</td>
                    <td width="100" align="right" nowrap="nowrap">% change Price Criteria</td>
                </tr>
    	<?
			while($row = mysql_fetch_array($result)) {
		?>		
				<tr>
                    <td><?=format_date_ymd_to_mdy($row["oth_trade_date"])?></td>
                    <td align="right"><?=number_format($row["oth_quantity"],0,"",",")?></td>
                    <td align="right"><?=$row["oth_buysell"]?></td>
                    <td align="right"><?=$row["oth_price"]?></td>
                    <td align="right"><?=$arr_price_percent_filtered[$row["oth_trade_date"]]?>%</td>
                    <td align="right"><?=$price_criteria?>%</td>
				</tr>
    	<?
			}
		?>
            </table>
		<?
        } else {
		?>
            <a class="ilt">CRITERIA: PRICE CHANGE % ABOVE MONITORING THRESHOLD <font color="red">[No trades meet the criteria]</font></a><br>
        <?
		}
        ?>

		<br /><br /> 
    
	<?
	
	$volume_criteria = db_single_val("select bct_value as single_val from bcm_trend_config where bct_type = 'volume' and bct_isactive = 1");
	//xdebug("volume_criteria",$volume_criteria);

	//create volumes
	$arr_volumes = hist_volume($valsymbol, $startdate, $enddate);
	$arr_dates = array();
	$arr_vals = array();
	foreach($arr_volumes as $k=>$v) {
		$arr_dates[] = $k;
		$arr_vals[] = $v;
	}

  //show_array($arr_volumes);
  //show_array($arr_dates);
  //show_array($arr_vals);
	
		
	//get trades from bcm fulfilling the criteria
	//Added group by after Megan/Robert reported individual cases as being errors
		$query = "SELECT oth_trade_date, sum(oth_quantity) as oth_quantity, oth_buysell, avg(oth_price) as oth_price  
										 FROM `oth_other_trades` 
										 where oth_symbol = '".$valsymbol."' 
											 and oth_trade_date between '".$startdate."' and '".$enddate."' 
										 group by oth_trade_date, oth_symbol, oth_buysell
										 order by oth_trade_date desc";
		//xdebug("query",$query);
		$result = mysql_query($query) or die(tdw_mysql_error($query));

		$val_rows = mysql_num_rows($result);
		if ($val_rows > 0) {
		?>
            <a class="ilt">CRITERIA: % OF TOTAL VOLUME ABOVE MONITORING THRESHOLD</a><br>
            <table border="1" cellpadding="2" cellspacing="0">
              <tr style="font:Arial; font-size:12px; font-weight:bold; color:#000066">
                <td width="100">Date</td>
                <td width="100">Quantity</td>
                <td width="100">Buy/Sell</td>
                <td width="100">Price</td>
                <td width="100">% of Daily Volume</td>
                <td width="100" nowrap="nowrap">% Daily Vol. Criteria</td>
              </tr>
    	<?
					while($row = mysql_fetch_array($result)) {
						if (round(( $row["oth_quantity"]/$arr_volumes[$row["oth_trade_date"]] )*100,2) > $volume_criteria) {
						?>	
							<tr>
								<td><?=format_date_ymd_to_mdy($row["oth_trade_date"])?></td>
								<td align="right"><?=number_format($row["oth_quantity"],0,"",",")?></td>
								<td align="right"><?=$row["oth_buysell"]?></td>
								<td align="right"><?=number_format($row["oth_price"],2,".",",")?></td>
								<td align="right"><?=round(( $row["oth_quantity"]/$arr_volumes[$row["oth_trade_date"]] )*100,2)?>%</td>
								<td align="right"><?=$volume_criteria?>%</td>
							</tr>
						<?
						}
					}
		?>
    </table>
    <?
		} else {
		?>
      <a class="ilt">CRITERIA: % OF TOTAL VOLUME ABOVE MONITORING THRESHOLD <font color="red">[No trades meet the criteria]</font></a><br>
    <?
		}
		?>

		<!-- NEWS SECTION -->
		<br /><br /> 
    
	<?
	
	$qry_news = "select * from news_events 
						 where news_date between '".$startdate."' and '".$enddate."'
						 and news_symbol = '".$valsymbol."'
						 order by auto_id";
	//xdebug("query",$query);
	$result_news = mysql_query($qry_news) or die(tdw_mysql_error($qry_news));
  $arr_ndates = array();
	$arr_news = array();
	//with the arrays above it will hold only one piece of news for the date.
	while($row = mysql_fetch_array($result_news)) {
		$arr_ndates[] = $row["news_date"];
		$arr_nnews[$row["news_date"]] = $row["news_notes"];
	}
	
	$valid_dates = " ('" . implode("','", $arr_ndates) . "') ";
	
	//get trades from bcm fulfilling the criteria
	$query = "SELECT * FROM `oth_other_trades` 
										 where oth_symbol = '".$valsymbol."' 
											 and oth_trade_date in " . $valid_dates . " 
										 order by oth_trade_date desc";
		//xdebug("query",$query);
		$result = mysql_query($query) or die(tdw_mysql_error($query));

		$val_rows = mysql_num_rows($result);
		if ($val_rows > 0) {
		?>
    <a class="ilt">NEWS / EVENTS IN THE SELECTED TIME FRAME</a><br>
    <table border="1" cellpadding="2" cellspacing="0" width="600">
    	<tr style="font:Arial; font-size:12px; font-weight:bold; color:#000066">
        <td width="100">Date</td>
        <td width="100">Quantity</td>
        <td width="100">Buy/Sell</td>
        <td width="100">Price</td>
        <td width="200">&nbsp;</td>
			</tr>
			<?
      while($row = mysql_fetch_array($result)) {
      ?>
        <tr>
          <td><?=format_date_ymd_to_mdy($row["oth_trade_date"])?></td>
          <td align="right"><?=number_format($row["oth_quantity"],0,"",",")?></td>
          <td align="right"><?=$row["oth_buysell"]?></td>
          <td align="right"><?=$row["oth_price"]?></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td colspan="5"><?=nl2br($arr_nnews[$row["oth_trade_date"]])?></td>
        </tr>
      <?
      }
		?>
    </table>
    <?
		} else {
		?>
      <a class="ilt">NEWS / EVENTS IN THE SELECTED TIME FRAME <font color="red">[NONE]</font></a><br>
    <?
		}
		?>
    

  </td>
</tr>
</table>



<?
function hist_prices($symbol, $startdate, $enddate) {
	
	//Process start and end dates
	$arr_startdate = explode("-", $startdate);
	$arr_enddate = explode("-", $enddate);
	
	$arr_mon = array("01"=>"00","02"=>"01","03"=>"02","04"=>"03","05"=>"04","06"=>"05","07"=>"06","08"=>"07","09"=>"08","10"=>"09","11"=>"10","12"=>"11",);
	
  //Date	Open	High	Low	Close	Volume	Adj Close
  // http://ichart.finance.yahoo.com/table.csv?s=AEO&a=03&b=14&c=2009&d=05&e=12&f=2009&g=d&ignore=.csv

	$row = 1;
	$str_url = "http://ichart.finance.yahoo.com/table.csv?s=".$symbol.
	           "&a=".$arr_mon[$arr_startdate[1]]."&b=".$arr_startdate[2]."&c=".$arr_startdate[0].
						 "&d=".$arr_mon[$arr_enddate[1]]."&e=".$arr_enddate[2]."&f=".$arr_enddate[0].
						 "&g=d&ignore=.csv";
	//echo $str_url;
	$handle = fopen($str_url, "r");
	$arr_price = array();
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			if ($row > 1) {
				$num = count($data);
				//echo "<p> $num fields in line $row: <br /></p>\n";
				/*
				for ($c=0; $c < $num; $c++) {
						echo $data[$c] . "<br />\n";
				}
				*/
				//$arr_price[substr($data[0],5,5)] = $data[4];
				$arr_price[$data[0]] = $data[4];
			}
			$row++;
	}
	fclose($handle);
	return $arr_price;
}

function hist_volume($symbol, $startdate, $enddate) {
	
	//Process start and end dates
	$arr_startdate = explode("-", $startdate);
	$arr_enddate = explode("-", $enddate);
	
	$arr_mon = array("01"=>"00","02"=>"01","03"=>"02","04"=>"03","05"=>"04","06"=>"05","07"=>"06","08"=>"07","09"=>"08","10"=>"09","11"=>"10","12"=>"11",);
	
  //Date	Open	High	Low	Close	Volume	Adj Close
  // http://ichart.finance.yahoo.com/table.csv?s=AEO&a=03&b=14&c=2009&d=05&e=12&f=2009&g=d&ignore=.csv

	$row = 1;
	$str_url = "http://ichart.finance.yahoo.com/table.csv?s=".$symbol.
	           "&a=".$arr_mon[$arr_startdate[1]]."&b=".$arr_startdate[2]."&c=".$arr_startdate[0].
						 "&d=".$arr_mon[$arr_enddate[1]]."&e=".$arr_enddate[2]."&f=".$arr_enddate[0].
						 "&g=d&ignore=.csv";
	//echo $str_url;
	$handle = fopen($str_url, "r");
	$arr_vol = array();
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			if ($row > 1) {
				$num = count($data);
				//echo "<p> $num fields in line $row: <br /></p>\n";
				/*
				for ($c=0; $c < $num; $c++) {
						echo $data[$c] . "<br />\n";
				}
				*/
				//$arr_price[substr($data[0],5,5)] = $data[4];
				$arr_vol[$data[0]] = $data[5];
			}
			$row++;
	}
	fclose($handle);
	return $arr_vol;
}

?>