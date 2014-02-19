<?
 
ini_set('max_execution_time', 7200);
ini_set('memory_limit','256M');
ini_set("display_errors", 0); 
 
  include('../includes/dbconnect.php');
  include('../includes/global.php');
	include('../includes/functions.php');

	include('sproc_alert_bcm_vol_pct_func.php');

	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	//$enddate = previous_business_day();
	$enddate = "2010-12-16";
	//xdebug('enddate',$enddate);
	$startdate = business_day_backward(strtotime($enddate),1);
	//xdebug('startdate',$startdate);


	//Get all symbols for the day.
	$query = "SELECT distinct(oth_symbol) as symbols FROM `oth_other_trades` 
									 where oth_trade_date between '".$startdate."' and '".$enddate."' 
									 order by oth_symbol";
	$result = mysql_query($query) or die(tdw_mysql_error($query));
	$val_rows = mysql_num_rows($result);
	if ($val_rows > 0) {
		$arr_symbols = array();
		while($row = mysql_fetch_array($result)) {
			if (strlen(trim($row["symbols"])) < 7 && !strpos(trim($row["symbols"])," ")) { 
				$arr_symbols[] = strtoupper(trim($row["symbols"])); 
			}
		}
	}

	//show_array($arr_symbols);
	//exit;
	
	//$valsymbol = strtoupper(trim($symbol));
	//$valsymbol = "ARO";
	//xdebug('valsymbol',$valsymbol);

  $arr_symbols = array();
	$arr_symbols[] = 'ARO';
	$arr_symbols[] = 'AXL';


	//Email output string
	$str_output = "";
	
	//process each symbol perform lookup and processing
	foreach ($arr_symbols as $k=>$valsymbol) {
		//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		//create percentages for price
		$arr_prices = hist_prices($valsymbol, $startdate, $enddate);
		$arr_dates = array();
		$arr_vals = array();
		foreach($arr_prices as $k=>$v) {
			$arr_dates[] = $k;
			$arr_vals[] = $v;
		}

		$arr_price_percent = array();
		for($i=0; $i<count($arr_prices)-1; $i++) {
			$arr_price_percent[$arr_dates[$i]] = round( (($arr_vals[$i] - $arr_vals[$i+1])/$arr_vals[$i])*100 , 2); //abs ();
		}
	
		$price_criteria = db_single_val("select bct_value as single_val from bcm_trend_config where bct_type = 'price' and bct_isactive = 1");

		$arr_price_percent_filtered = array();
		$arr_price_percent_filtered_dates = array();
		foreach($arr_price_percent as $k=>$v) {
			 if ( abs($v) > abs($price_criteria) ) {
					$arr_price_percent_filtered[$k] = $v;
					$arr_price_percent_filtered_dates[] = $k;
			 }
		}

			$str_dates = " ('".implode("','",$arr_price_percent_filtered_dates)."') ";
		
			//get trades from bcm fulfilling the criteria
			$query = "SELECT * FROM `oth_other_trades` 
											 where oth_symbol = '".$valsymbol."' 
												 and oth_trade_date in ".$str_dates." 
											 order by oth_trade_date desc";
			//xdebug("query",$query);
			$result = mysql_query($query) or die(tdw_mysql_error($query));
	
			$val_rows = mysql_num_rows($result);
			
			if ($val_rows > 0) { //<a class="ilt">SYMBOL: </a>$valsymbol<br>
			?>
		<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td width="10">&nbsp;</td>
			<td valign="top">
        <a class="ilt">CRITERIA: PRICE CHANGE % ABOVE MONITORING THRESHOLD</a><br>
        <table border="1" cellpadding="2" cellspacing="0">
          <tr style="font:Arial; font-size:12px; font-weight:bold; color:#000066">
            <td width="80">Date</td>
            <td width="80">Symbol</td>
            <td width="80" align="right">Quantity</td>
            <td width="80" align="right">Buy/Sell</td>
            <td width="80" align="right">Price</td>
            <td width="100" align="right" nowrap="nowrap">% change Closing Price</td>
            <td width="100" align="right" nowrap="nowrap">% change Price Criteria</td>
          </tr>
				<?
					while($row = mysql_fetch_array($result)) {
				?>		
          <tr>
            <td><?=format_date_ymd_to_mdy($row["oth_trade_date"])?></td>
            <td><?=$valsymbol?></td>
            <td align="right"><?=number_format($row["oth_quantity"],0,"",",")?></td>
            <td align="right"><?=$row["oth_buysell"]?></td>
            <td align="right"><?=$row["oth_price"]?></td>
            <td align="right"><?=$arr_price_percent_filtered[$row["oth_trade_date"]]?>%</td>
            <td align="right"><?=$price_criteria?>%</td>
          </tr>
				<?
						//insert into table this data for putting attestations.
					}
				?>
				</table><br />
				<?
			} else {
				$var_dummy = 1;
			?>
				<!--<a class="ilt">CRITERIA: PRICE CHANGE % ABOVE MONITORING THRESHOLD <font color="red">[No trades meet the criteria]</font></a><br>-->
			<?
				}

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
				$query = "SELECT * FROM `oth_other_trades` 
												 where oth_symbol = '".$valsymbol."' 
													 and oth_trade_date between '".$startdate."' and '".$enddate."' 
												 order by oth_trade_date desc";
				//xdebug("query",$query);
				$result = mysql_query($query) or die(tdw_mysql_error($query));

						
				$val_rows = mysql_num_rows($result);
				//xdebug("val_rows",$val_rows);
				if ($val_rows > 0) {
							$arr_string_data = array();
							while($row = mysql_fetch_array($result)) {
								if (round(( $row["oth_quantity"]/$arr_volumes[$row["oth_trade_date"]] )*100,2) > $volume_criteria) {
									$arr_string_data[] = 	format_date_ymd_to_mdy($row["oth_trade_date"]) ."^". 
									                      $valsymbol ."^". 
																				number_format($row["oth_quantity"],0,"",",") ."^". 
																				$row["oth_buysell"] ."^". 
																				$row["oth_price"] ."^". 
																				round(( $row["oth_quantity"]/$arr_volumes[$row["oth_trade_date"]] )*100,2) ."%^".
																				$volume_criteria ."%^";
								}
							}
				
							if (count($arr_string_data)>0) {
							?>
								<a class="ilt">CRITERIA: % OF TOTAL VOLUME ABOVE MONITORING THRESHOLD</a><br>
								<table border="1" cellpadding="2" cellspacing="0">
									<tr style="font:Arial; font-size:12px; font-weight:bold; color:#000066">
										<td width="80">Date</td>
										<td width="80">Symbol</td>
										<td width="80">Quantity</td>
										<td width="80">Buy/Sell</td>
										<td width="80">Price</td>
										<td width="100">% of Daily Volume</td>
										<td width="100" nowrap="nowrap">% Daily Vol. Criteria</td>
									</tr>
							<?
              		foreach($arr_string_data as $k=>$v) {
										$arrout = explode("^",$v);
										?>
                    <tr>
                      <td><?=$arrout[0]?></td>
                      <td align="right"><?=$arrout[1]?></td>
                      <td align="right"><?=$arrout[2]?></td>
                      <td align="right"><?=$arrout[3]?></td>
                      <td align="right"><?=$arrout[4]?></td>
                      <td align="right"><?=$arrout[5]?></td>
                      <td align="right"><?=$arrout[6]?></td>
                    </tr>
                    <?
									}
							echo '</table><br />';		
							}
				} else {
				?>
					<a class="ilt">CRITERIA: % OF TOTAL VOLUME ABOVE MONITORING THRESHOLD <font color="red">[No trades meet the criteria]</font></a><br>
				<?
				}
				?>
		
				<!-- NEWS SECTION -->
				
				
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
						<td width="80">Date</td>
						<td width="80">Symbol</td>
						<td width="80">Quantity</td>
						<td width="80">Buy/Sell</td>
						<td width="80">Price</td>
						<td width="200">&nbsp;</td>
					</tr>
					<?
					while($row = mysql_fetch_array($result)) {
					?>
						<tr>
							<td><?=format_date_ymd_to_mdy($row["oth_trade_date"])?></td>
							<td><?=$valsymbol?></td>
							<td align="right"><?=number_format($row["oth_quantity"],0,"",",")?></td>
							<td align="right"><?=$row["oth_buysell"]?></td>
							<td align="right"><?=$row["oth_price"]?></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td colspan="6"><?=nl2br($arr_nnews[$row["oth_trade_date"]])?></td>
						</tr>
					<?
					}
				?>
				</table><br />
				<?
				} else {
					$var_dummy = 1;
					//<a class="ilt">NEWS / EVENTS IN THE SELECTED TIME FRAME <font color="red">[NONE]</font></a><br>
				}
				?>
			</td>
		</tr>
		</table>
		<?
		//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%	
	}


	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	ob_flush();
	flush();
	
	exit;
	
?>
