<?

function get_holdings($v, $valsymbol) {

	//Get symbols on CITTA List.
	$count_symbol = db_single_val("select count(citta_company_symbol) as single_val from citta_list where
																 citta_company_symbol = '".$valsymbol."' and 
																 citta_isactive = 1");

		
	//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
	//create percentages for price
	$arr_prices = hist_prices($valsymbol, business_day_backward(strtotime($v),5), $v);
	$arr_dates = array();
	$arr_vals = array();
	foreach($arr_prices as $dt=>$pr) {
		$arr_dates[] = $dt;
		$arr_vals[] = $pr;
	}
	

	$arr_price_percent = array();
	for($i=0; $i<count($arr_prices)-1; $i++) {
		$arr_price_percent[$arr_dates[$i]] = round( (($arr_vals[$i] - $arr_vals[$i+1])/$arr_vals[$i])*100 , 2); //abs ();
	}
	//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

	//print_r($arr_price_percent); 

	if ($count_symbol > 0) {
		$str_clr = '<tr bgcolor="#FF0000" style="font:Arial; font-size:14px; font-weight:bold; color:#ffffff">
									<td>&nbsp;</td><td>T-0</td><td>T-1</td><td>T-2</td><td>T-3</td><td>T-4</td>
								</tr>'; 
	} else {
		$str_clr = '<tr style="font:Arial; font-size:14px; font-weight:bold; color:#000066">
									<td>&nbsp;</td><td>T-0</td><td>T-1</td><td>T-2</td><td>T-3</td><td>T-4</td>
								</tr>';
	}
	
	

  $str_return = '<table border="1" cellpadding="2" cellspacing="0" width="600">'.$str_clr.' 
      <tr>
				<td>Dates</td>
        <td>' . format_date_ymd_to_mdy($v) .'</td>
        <td>'.format_date_ymd_to_mdy(business_day_backward(strtotime($v),1)).'</td>
        <td>'.format_date_ymd_to_mdy(business_day_backward(strtotime($v),2)).'</td>
        <td>'.format_date_ymd_to_mdy(business_day_backward(strtotime($v),3)).'</td>
        <td>'.format_date_ymd_to_mdy(business_day_backward(strtotime($v),4)).'</td>
     	</tr>
      <tr>
        <td>Holding</td>
				<td>';
				
						$val_0 = db_single_val("select
											sum(quantity) as single_val 
											from pos_bcm_positions
											where reporting_date = '".$v."' ". 
											" and pos_symbol = '". $valsymbol ."'");
						$str_return .= '&nbsp;'.number_format($val_0,0,"",",");
				
				$str_return .= '</td>
        <td>';
						$val_1 = db_single_val("select
											sum(quantity) as single_val 
											from pos_bcm_positions
											where reporting_date = '".business_day_backward(strtotime($v),1)."' ". 
											" and pos_symbol = '". $valsymbol ."'");
						$str_return .= '&nbsp;'.number_format($val_1,0,"",",");
				
				$str_return .= '</td>
        <td>';
						$val_2 = db_single_val("select
											sum(quantity) as single_val 
											from pos_bcm_positions
											where reporting_date = '".business_day_backward(strtotime($v),2)."' ". 
											" and pos_symbol = '". $valsymbol ."'");
						$str_return .= '&nbsp;'.number_format($val_2,0,"",",");
				
				$str_return .= '</td>
        <td>';
						$val_3 = db_single_val("select
											sum(quantity) as single_val 
											from pos_bcm_positions
											where reporting_date = '".business_day_backward(strtotime($v),3)."' ". 
											" and pos_symbol = '". $valsymbol ."'");
						$str_return .= '&nbsp;'.number_format($val_3,0,"",",");
				
				$str_return .= '</td>
        <td>';
						$val_4 = db_single_val("select
											sum(quantity) as single_val 
											from pos_bcm_positions
											where reporting_date = '".business_day_backward(strtotime($v),4)."' ". 
											" and pos_symbol = '". $valsymbol ."'");
						$val_5 = db_single_val("select
											sum(quantity) as single_val 
											from pos_bcm_positions
											where reporting_date = '".business_day_backward(strtotime($v),5)."' ". 
											" and pos_symbol = '". $valsymbol ."'");
						$str_return .= '&nbsp;'.number_format($val_4,0,"",",");
				$str_return .= '</td>
     	</tr>
      <tr>
        <td>% change (Holding)</td>
      	<td>&nbsp;'.round((($val_0-$val_1)/$val_1)*100,2).'%</td>
      	<td>&nbsp;'.round((($val_1-$val_2)/$val_2)*100,2).'%</td>
      	<td>&nbsp;'.round((($val_2-$val_3)/$val_3)*100,2).'%</td>
      	<td>&nbsp;'.round((($val_3-$val_4)/$val_4)*100,2).'%</td>
      	<td>&nbsp;'.round((($val_4-$val_5)/$val_5)*100,2).'%</td>
     `</tr>
		 <tr><td>% change (Price)</td>';
		 foreach ($arr_price_percent as $dt=>$pr_chng) {
		 	$str_return .= '<td>&nbsp;'.round($pr_chng,1).'%</td>';
		 }
		 
		 $str_return .= '</tr></table><br>';

	return $str_return;
}

function get_holdings_excel($v, $valsymbol) {

	//Get symbols on CITTA List.
	$count_symbol = db_single_val("select count(citta_company_symbol) as single_val from citta_list where
																 citta_company_symbol = '".$valsymbol."' and 
																 citta_isactive = 1");

		
	//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
	//create percentages for price
	$arr_prices = hist_prices($valsymbol, business_day_backward(strtotime($v),5), $v);
	$arr_dates = array();
	$arr_vals = array();
	foreach($arr_prices as $dt=>$pr) {
		$arr_dates[] = $dt;
		$arr_vals[] = $pr;
	}
	

	$arr_price_percent = array();
	for($i=0; $i<count($arr_prices)-1; $i++) {
		$arr_price_percent[$arr_dates[$i]] = round( (($arr_vals[$i] - $arr_vals[$i+1])/$arr_vals[$i])*100 , 2); //abs ();
	}
	//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>


	if ($count_symbol > 0) {
		$str_clr = '<tr style="font:Arial; font-size:14px; font-weight:bold; color:#ffffff">
									<td>&nbsp;</td><td bgcolor="#FF0000" >T-0</td><td bgcolor="#FF0000" >T-1</td><td bgcolor="#FF0000" >T-2</td><td bgcolor="#FF0000" >T-3</td><td bgcolor="#FF0000" >T-4</td>
								</tr>'; 
	} else {
		$str_clr = '<tr style="font:Arial; font-size:14px; font-weight:bold; color:#000066">
									<td>&nbsp;</td><td>T-0</td><td>T-1</td><td>T-2</td><td>T-3</td><td>T-4</td>
								</tr>';
	}

  $str_return = $str_clr.' 
      <tr>
				<td>Dates</td>
        <td>' . format_date_ymd_to_mdy($v) .'</td>
        <td>'.format_date_ymd_to_mdy(business_day_backward(strtotime($v),1)).'</td>
        <td>'.format_date_ymd_to_mdy(business_day_backward(strtotime($v),2)).'</td>
        <td>'.format_date_ymd_to_mdy(business_day_backward(strtotime($v),3)).'</td>
        <td>'.format_date_ymd_to_mdy(business_day_backward(strtotime($v),4)).'</td>
     	</tr>
      <tr>
        <td>Holding</td>
        <td>';
				
						$val_0 = db_single_val("select
											sum(quantity) as single_val 
											from pos_bcm_positions
											where reporting_date = '".$v."' ". 
											" and pos_symbol = '". $valsymbol ."'");
						$str_return .= '&nbsp;'.number_format($val_0,0,"",",");
				
				$str_return .= '</td>
        <td>';
						$val_1 = db_single_val("select
											sum(quantity) as single_val 
											from pos_bcm_positions
											where reporting_date = '".business_day_backward(strtotime($v),1)."' ". 
											" and pos_symbol = '". $valsymbol ."'");
						$str_return .= '&nbsp;'.number_format($val_1,0,"",",");
				
				$str_return .= '</td>
        <td>';
						$val_2 = db_single_val("select
											sum(quantity) as single_val 
											from pos_bcm_positions
											where reporting_date = '".business_day_backward(strtotime($v),2)."' ". 
											" and pos_symbol = '". $valsymbol ."'");
						$str_return .= '&nbsp;'.number_format($val_2,0,"",",");
				
				$str_return .= '</td>
        <td>';
						$val_3 = db_single_val("select
											sum(quantity) as single_val 
											from pos_bcm_positions
											where reporting_date = '".business_day_backward(strtotime($v),3)."' ". 
											" and pos_symbol = '". $valsymbol ."'");
						$str_return .= '&nbsp;'.number_format($val_3,0,"",",");
				
				$str_return .= '</td>
        <td>';
						$val_4 = db_single_val("select
											sum(quantity) as single_val 
											from pos_bcm_positions
											where reporting_date = '".business_day_backward(strtotime($v),4)."' ". 
											" and pos_symbol = '". $valsymbol ."'");
						$val_5 = db_single_val("select
											sum(quantity) as single_val 
											from pos_bcm_positions
											where reporting_date = '".business_day_backward(strtotime($v),5)."' ". 
											" and pos_symbol = '". $valsymbol ."'");
						$str_return .= '&nbsp;'.number_format($val_4,0,"",",");
				$str_return .= '</td>
     	</tr>
      <tr>
        <td>% change (Holding)</td>
      	<td>&nbsp;'.round((($val_0-$val_1)/$val_1)*100,2).'%</td>
      	<td>&nbsp;'.round((($val_1-$val_2)/$val_2)*100,2).'%</td>
      	<td>&nbsp;'.round((($val_2-$val_3)/$val_3)*100,2).'%</td>
      	<td>&nbsp;'.round((($val_3-$val_4)/$val_4)*100,2).'%</td>
      	<td>&nbsp;'.round((($val_4-$val_5)/$val_5)*100,2).'%</td>
     </tr><tr><td>% change (Price)</td>';
		 
		 foreach ($arr_price_percent as $dt=>$pr_chng) {
		 	$str_return .= '<td>&nbsp;'.round($pr_chng,1).'%</td>';
		 }
		 
		 $str_return .= '</tr>';

	return $str_return;
}

?>