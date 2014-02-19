<?
  include('includes/dbconnect.php');
  include('includes/global.php');
	include('includes/functions.php');

//show_array($_GET);
//exit;

function show_numbers_pdf($numval) {
		if ($numval == 0) {
			return '<font color="888888">0</font>';
		} else {
			return number_format($numval,0,'.',",");
		}
}	

if ($_GET) {
	//xdebug('datefilterval',$datefilterval);
	$trade_date_to_process = format_date_mdy_to_ymd($datefilterval);
	$arr_repinfo = split('\^',$sel_rep);
	$rep_to_process = $arr_repinfo[0];
	$rep_id = $arr_repinfo[1];
	$rep_name = $arr_repinfo[2];
} else {
  $trade_date_to_process = previous_business_day();

	$qry_get_rep_default = "SELECT
														a.ID, a.rr_num, concat(a.Lastname, ', ', a. Firstname) as rep_name, b.trad_rr 
														from users a, mry_comm_rr_trades b
													WHERE a.rr_num = b.trad_rr
													AND b.trad_rr like '0%'
													AND a.user_isactive = 1
													AND a.Role > 2
													AND a.Role < 5
													GROUP BY b.trad_rr 
													ORDER BY a.Lastname LIMIT 1";
	$result_get_rep_default = mysql_query($qry_get_rep_default) or die (tdw_mysql_error($qry_get_rep_default));
	while ( $row_get_rep_default = mysql_fetch_array($result_get_rep_default) )
					{
						$sel_rep = $row_get_rep_default["trad_rr"]."^".$row_get_rep_default["ID"];
						$rep_name = $row_get_rep_default["rep_name"] . "&nbsp; &nbsp;". "(".$row_get_rep_default["rr_num"].")";
					}
	$arr_repinfo = split('\^',$sel_rep);
	$rep_to_process = $arr_repinfo[0];
	$rep_id = $arr_repinfo[1];

}

include('comm_m_src_inc_main.php');


		$data_to_html_file = "";

		$data_to_html_file .= '
			<style type="text/css">
			<!--
			.data_black {font-family: "Courier New", Courier, mono;	font-size: 10px;	color: #000000;}
			tr.trlight {
				font-family: Arial;
				font-size: 11px;
				color: #000000;
			}
			tr.trdark {
				font-family: Arial;
				font-size: 11px;
				color: #000000;
			}
			-->
			</style>
			<table width="670" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td valign="top" width="50"><img src="../../images/logo_small.gif" width="47"></td>
					<td valign="top" align="left" width="500">
						<font color="#333333" size="3" face="Arial"><b>&nbsp;RR Commissions Summary [ '.$rep_name .' ]</b></font>
						<br>
						<font color="#333333" size="2" face="Arial">
							&nbsp;Trade Date: '.format_date_ymd_to_mdy($trade_date_to_process).'
						</font>
					</td>
					<td>&nbsp;</td>
				</tr>
			</table>
			<img src="../../images/border_a.png" width="670" height="2">';
			
			
		$data_to_html_file .= '
												<table width="670" cellpadding="1", cellspacing="0" bgcolor="#EEEEEE" border="1">
													<tr>
														<td valign="top">		
														<table width="100%"  border="0" cellspacing="1" cellpadding="0">
															<tr> 
																<td colspan="2" bgcolor="#ffffff" width="140"><font face="Arial" size="1">&nbsp;&nbsp;"Brokerage Month Basis"</font></td>
																<td bgcolor="#F0F0F0" colspan="4" align="center"><font face="Arial" size="1">C O M M I S S I O N S</font></td>
																<td bgcolor="#CCCCCC" colspan="3" align="center"><font face="Arial" size="1">C H E C K S</font></td>
																<td bgcolor="#EEEEEE" colspan="3" align="center"><font face="Arial" size="1">T O T A L</font></td>
																<td bgcolor="#F0F0F0" align="center"><font face="Arial" size="1">LAST YEAR</font></td>
																<td bgcolor="#F0F0F0" align="center"><font face="Arial" size="1">%</font></td>
															<tr> 
																<td width="120" bgcolor="#CCCCCC"><font face="Arial" size="1">&nbsp;&nbsp;&nbsp;&nbsp;CLIENT (PRIMARY)</font></td>
																<td width="20" bgcolor="#CCCCCC"><font face="Arial" size="1">&nbsp;RR</font></td>
																<td width="40" bgcolor="#EEEEEE" align="right"><font face="Arial" size="1">'.substr(format_date_ymd_to_mdy($trade_date_to_process),0,5).'&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
																<td width="40" bgcolor="#EEEEEE" align="right"><font face="Arial" size="1">MTD&nbsp;&nbsp;</font></td>
																<td width="50" bgcolor="#EEEEEE" align="right"><font face="Arial" size="1">QTD&nbsp;&nbsp;</font></td>
																<td width="50" bgcolor="#EEEEEE" align="right"><font face="Arial" size="1">YTD&nbsp;&nbsp;</font></td>
																<td width="40" bgcolor="#888888" align="right"><font face="Arial" size="1">MTD&nbsp;&nbsp;</font></td>
																<td width="40" bgcolor="#888888" align="right"><font face="Arial" size="1">QTD&nbsp;&nbsp;</font></td>
																<td width="55" bgcolor="#888888" align="right"><font face="Arial" size="1">YTD&nbsp;&nbsp;</font></td>
																<td width="50" bgcolor="#CCCCCC" align="right"><font face="Arial" size="1">MTD&nbsp;&nbsp;</font></td>
																<td width="50" bgcolor="#DDDDDD" align="right"><font face="Arial" size="1">QTD&nbsp;&nbsp;</font></td>
																<td width="50" bgcolor="#EEEEEE" align="right"><font face="Arial" size="1">YTD&nbsp;&nbsp;</font></td>
																<td width="40" bgcolor="#F3F3F3" align="right"><font face="Arial" size="1"> </font></td>
																<td width="40" bgcolor="#F3F3F3" align="center"><font face="Arial" size="1"> of LY </font></td>
															</tr>';
															
															
															$level_a_count = 0;
															$arr_main_processed = array();
															foreach($arr_clnt_for_rr as $k=>$v) {
																if ($k != '' AND ($arr_ytd_comm[$k]+$arr_ytd_check[$k])> 0) {
		
																		$arr_main_processed[] = $k;
																		if(get_previous_yr_data($k)> 0) {
																			$pyc_percent = number_format((($arr_ytd_comm[$k]+$arr_ytd_check[$k])/get_previous_yr_data($k))*100,0,'','');
																		} else {
																			$pyc_percent = 0;
																		}
																		if ($level_a_count % 2) { 
																				$class_row = "trdark";
																				$bgcolor = "#F2F2F2"; 
																		} else { 
																				$class_row = "trlight";
																				$bgcolor = "#FFFFFF";
																		} 
		
						$data_to_html_file .= '
																	<tr class ="'.$class_row.'" bgcolor="'.$bgcolor.'" >
																		<td><font face="Arial" size="-4">&nbsp;'.look_up_client($k).'</font></td>
																		<td><font face="Arial" size="1">&nbsp;&nbsp;'.$rep_to_process.'</font></td>
																		<td align="right"><font face="Arial" size="1">'.show_numbers_pdf($arr_day_comm[$k]).'</font></td>
																		<td align="right"><font face="Arial" size="1">'.show_numbers_pdf($arr_mtd_comm[$k]).'</font></td>
																		<td align="right"><font face="Arial" size="1">'.show_numbers_pdf($arr_qtd_comm[$k]).'</font></td>
																		<td align="right"><font face="Arial" size="1">'.show_numbers_pdf($arr_ytd_comm[$k]).'</font></td>
																		<td align="right"><font face="Arial" size="1">'.show_numbers_pdf($arr_mtd_check[$k]).'</font></td>
																		<td align="right"><font face="Arial" size="1">'.show_numbers_pdf($arr_qtd_check[$k]).'</font></td>
																		<td align="right"><font face="Arial" size="1">'.show_numbers_pdf($arr_ytd_check[$k]).'</font></td>
																		<td align="right"><font face="Arial" size="1">'.show_numbers_pdf($arr_mtd_comm[$k]+$arr_mtd_check[$k]).'</font></td>
																		<td align="right"><font face="Arial" size="1">'.show_numbers_pdf($arr_qtd_comm[$k]+$arr_qtd_check[$k]).'</font></td>
																		<td align="right"><font face="Arial" size="1">'.show_numbers_pdf($arr_ytd_comm[$k]+$arr_ytd_check[$k]).'</font></td>
																		<td align="right"><font face="Arial" size="1">'.show_numbers_pdf(get_previous_yr_data($k)).'</font></td>
																		<td align="right"><font face="Arial" size="1">'.$pyc_percent.'&nbsp;&nbsp;</font></td> 
																	</tr>';

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

						$data_to_html_file .= '
														</table>
														<table width="670" border="0" cellspacing="1" cellpadding="0">
															 <tr> 
																<td width="120" align="left">&nbsp;&nbsp;TOTALS:</td>
																<td width="20"><font face="Arial" size="1">&nbsp;&nbsp;</font></td>
																<td width="40" align="right"><font face="Arial" size="1"><b>'.show_numbers_pdf($total_pbd).'</b></font></td>
																<td width="40" align="right"><font face="Arial" size="1"><b>'.show_numbers_pdf($total_mtd).'</b></font></td>
																<td width="50" align="right"><font face="Arial" size="1"><b>'.show_numbers_pdf($total_qtd).'</b></font></td>
																<td width="50" align="right"><font face="Arial" size="1"><b>'.show_numbers_pdf($total_ytd).'</b></font></td>
																<td width="40" align="right"><font face="Arial" size="1"><b>'.show_numbers_pdf($total_cmtd).'</b></font></td>
																<td width="40" align="right"><font face="Arial" size="1"><b>'.show_numbers_pdf($total_cqtd).'</b></font></td>
																<td width="55" align="right"><font face="Arial" size="1"><b>'.show_numbers_pdf($total_cytd).'</b></font></td>
																<td width="50" align="right"><font face="Arial" size="1"><b>'.show_numbers_pdf($total_grand_mtd).'</b></font></td>
																<td width="50" align="right"><font face="Arial" size="1"><b>'.show_numbers_pdf($total_grand_qtd).'</b></font></td>
																<td width="50" align="right"><font face="Arial" size="1"><b>'.show_numbers_pdf($total_grand_ytd).'</b></font></td>
																<td width="40" align="right">&nbsp;</td>
																<td width="40" align="right">&nbsp;</td>
															</tr>
														</table>
													</td>
												</tr>
											</table>
											<!-- END TABLE 4 -->
										</td>
									</tr>
									<tr id="shrd">
										<td>
											<br>
												<table width="670" cellpadding="1", cellspacing="0" bgcolor="#EEEEEE" border="1">
													<tr>
														<td valign="top">		
														<table width="670" border="0" cellspacing="1" cellpadding="0">
															<tr> 
																<td colspan="2" bgcolor="#ffffff" width="140"><font face="Arial" size="1">&nbsp;&nbsp;"Brokerage Month Basis"</font></td>
																<td bgcolor="#F0F0F0" colspan="4" align="center"><font face="Arial" size="1">C O M M I S S I O N S</font></td>
																<td bgcolor="#CCCCCC" colspan="3" align="center"><font face="Arial" size="1">C H E C K S</font></td>
																<td bgcolor="#EEEEEE" colspan="3" align="center"><font face="Arial" size="1">T O T A L</font></td>
																<td bgcolor="#F0F0F0" align="center"><font face="Arial" size="1">LAST YEAR</font></td>
																<td bgcolor="#F0F0F0" align="center"><font face="Arial" size="1">%</font></td>
															<tr> 
																<td width="120" bgcolor="#CCCCCC"><font face="Arial" size="1">&nbsp;&nbsp;&nbsp;&nbsp;CLIENT (SHARED)</font></td>
																<td width="20" bgcolor="#CCCCCC"><font face="Arial" size="1">&nbsp;&nbsp;RR</font></td>
																<td width="40" bgcolor="#EEEEEE" align="right"><font face="Arial" size="1">'.substr(format_date_ymd_to_mdy($trade_date_to_process),0,5).'&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
																<td width="40" bgcolor="#EEEEEE" align="right"><font face="Arial" size="1">MTD&nbsp;&nbsp;</font></td>
																<td width="50" bgcolor="#EEEEEE" align="right"><font face="Arial" size="1">QTD&nbsp;&nbsp;</font></td>
																<td width="50" bgcolor="#EEEEEE" align="right"><font face="Arial" size="1">YTD&nbsp;&nbsp;</font></td>
																<td width="40" bgcolor="#888888" align="right"><font face="Arial" size="1">MTD&nbsp;&nbsp;</font></td>
																<td width="40" bgcolor="#888888" align="right"><font face="Arial" size="1">QTD&nbsp;&nbsp;</font></td>
																<td width="55" bgcolor="#888888" align="right"><font face="Arial" size="1">YTD&nbsp;&nbsp;</font></td>
																<td width="50" bgcolor="#CCCCCC" align="right"><font face="Arial" size="1">MTD&nbsp;&nbsp;</font></td>
																<td width="50" bgcolor="#DDDDDD" align="right"><font face="Arial" size="1">QTD&nbsp;&nbsp;</font></td>
																<td width="50" bgcolor="#EEEEEE" align="right"><font face="Arial" size="1">YTD&nbsp;&nbsp;</font></td>
																<td width="40" bgcolor="#F3F3F3" align="right"><font face="Arial" size="1"> </font></td>
																<td width="40" bgcolor="#F3F3F3" align="center"><font face="Arial" size="1"> of LY </font></td>
															</tr>';


															$level_b_count = 0;
															foreach($arr_relevant_shared_clients as $k=>$v) {
																if ($k != '') {
		
																		if(get_previous_yr_data($k)>0) {
																			$pyc_percent = number_format((($arr_ytd_comm[$k]+$arr_ytd_check[$k])/get_previous_yr_data($k))*100,0,'',",");
																		} else {
																			$pyc_percent = 0;
																		}
		
																		if ($level_b_count % 2) { 
																				$class_row = "trdark";
																				$bgcolor = "#F2F2F2"; 
																		} else { 
																				$class_row = "trlight";
																				$bgcolor = "#FFFFFF";
																		} 
		
																	if (!in_array($k, $arr_main_processed)) {
																		$val_chek_mtd = $arr_mtd_check_shrd[$k];
																		$val_chek_qtd = $arr_qtd_check_shrd[$k];
																		$val_chek_ytd = $arr_ytd_check_shrd[$k];
																	} else {
																		$val_chek_mtd = 0;
																		$val_chek_qtd = 0;
																		$val_chek_ytd = 0;
																	}
		
						$data_to_html_file .= '
																	<tr class ="'.$class_row.'" bgcolor="'.$bgcolor.'" >
																		<td><font face="Arial" size="-4">&nbsp;'.look_up_client($k).'</font></td>
																		<td>&nbsp;&nbsp;<font face="Arial" size="1">'.$v.'</font></td>
																		<td align="right"><font face="Arial" size="1">'.show_numbers_pdf($arr_day_comm_shrd[$k]).'&nbsp;&nbsp;</font></td>
																		<td align="right"><font face="Arial" size="1">'.show_numbers_pdf($arr_mtd_comm_shrd[$k]).'&nbsp;&nbsp;</font></td>
																		<td align="right"><font face="Arial" size="1">'.show_numbers_pdf($arr_qtd_comm_shrd[$k]).'&nbsp;&nbsp;</font></td>
																		<td align="right"><font face="Arial" size="1">'.show_numbers_pdf($arr_ytd_comm_shrd[$k]).'&nbsp;&nbsp;</font></td>
																		<td align="right"><font face="Arial" size="1">'.show_numbers_pdf($val_chek_mtd).'&nbsp;&nbsp;</font></td>
																		<td align="right"><font face="Arial" size="1">'.show_numbers_pdf($val_chek_qtd).'&nbsp;&nbsp;</font></td>
																		<td align="right"><font face="Arial" size="1">'.show_numbers_pdf($val_chek_ytd).'&nbsp;&nbsp;</font></td>
																		<td align="right"><font face="Arial" size="1">'.show_numbers_pdf($arr_mtd_comm_shrd[$k]+$val_chek_mtd).'&nbsp;&nbsp;</font></td>
																		<td align="right"><font face="Arial" size="1">'.show_numbers_pdf($arr_qtd_comm_shrd[$k]+$val_chek_qtd).'&nbsp;&nbsp;</font></td>
																		<td align="right"><font face="Arial" size="1">'.show_numbers_pdf($arr_ytd_comm_shrd[$k]+$val_chek_ytd).'&nbsp;&nbsp;</font></td>
																		<td align="right"><font face="Arial" size="1">'.show_numbers_pdf(get_previous_yr_data($k)).'&nbsp;&nbsp;</font></td>
																		<td align="right"><font face="Arial" size="1">'.show_numbers_pdf($pyc_percent).'&nbsp;&nbsp;&nbsp;&nbsp;</font></td> 
																	</tr>';

																	$total_pbd_shrd = $total_pbd_shrd + $arr_day_comm_shrd[$k];
																	$total_mtd_shrd = $total_mtd_shrd + $arr_mtd_comm_shrd[$k];
																	$total_qtd_shrd = $total_qtd_shrd + $arr_qtd_comm_shrd[$k];
																	$total_ytd_shrd = $total_ytd_shrd + $arr_ytd_comm_shrd[$k];
																	$total_cmtd_shrd = $total_cmtd_shrd + $val_chek_mtd; 
																	$total_cqtd_shrd = $total_cqtd_shrd + $val_chek_qtd; 
																	$total_cytd_shrd = $total_cytd_shrd + $val_chek_ytd;  
																	$total_grand_mtd_shrd = $total_grand_mtd_shrd + $arr_mtd_comm_shrd[$k]+$val_chek_mtd;
																	$total_grand_qtd_shrd = $total_grand_qtd_shrd + $arr_qtd_comm_shrd[$k]+$val_chek_qtd;
																	$total_grand_ytd_shrd = $total_grand_ytd_shrd + $arr_ytd_comm_shrd[$k]+$val_chek_ytd;
		
																	$level_b_count = $level_b_count + 1;			
															}
														}													
						$data_to_html_file .= '
														</table>
														<table width="670"  border="0" cellspacing="1" cellpadding="0">
															 <tr class="display_totals"> 
																<td width="120" align="left">&nbsp;&nbsp;TOTALS:</td>
																<td width="20">&nbsp;&nbsp;</td>
																<td width="40" align="right"><font face="Arial" size="1"><b>'.show_numbers_pdf($total_pbd_shrd).'</b>&nbsp;&nbsp;</font></td>
																<td width="40" align="right"><font face="Arial" size="1"><b>'.show_numbers_pdf($total_mtd_shrd).'</b>&nbsp;&nbsp;</font></td>
																<td width="50" align="right"><font face="Arial" size="1"><b>'.show_numbers_pdf($total_qtd_shrd).'</b>&nbsp;&nbsp;</font></td>
																<td width="50" align="right"><font face="Arial" size="1"><b>'.show_numbers_pdf($total_ytd_shrd).'</b>&nbsp;&nbsp;</font></td>
																<td width="40" align="right"><font face="Arial" size="1"><b>'.show_numbers_pdf($total_cmtd_shrd).'</b>&nbsp;&nbsp;</font></td>
																<td width="40" align="right"><font face="Arial" size="1"><b>'.show_numbers_pdf($total_cqtd_shrd).'</b>&nbsp;&nbsp;</font></td>
																<td width="55" align="right"><font face="Arial" size="1"><b>'.show_numbers_pdf($total_cytd_shrd).'</b>&nbsp;&nbsp;</font></td>
																<td width="50" align="right"><font face="Arial" size="1"><b>'.show_numbers_pdf($total_grand_mtd_shrd).'</b>&nbsp;&nbsp;</font></td>
																<td width="50" align="right"><font face="Arial" size="1"><b>'.show_numbers_pdf($total_grand_qtd_shrd).'</b>&nbsp;&nbsp;</font></td>
																<td width="50" align="right"><font face="Arial" size="1"><b>'.show_numbers_pdf($total_grand_ytd_shrd).'</b>&nbsp;&nbsp;</font></td>
																<td width="40" align="right">&nbsp;</td>
																<td width="40" align="right">&nbsp;</td>
															</tr>
														</table>
													</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>';
							
							$data_to_html_file .= "<br><br>";
							$data_to_html_file .= '<hr align="left" width="670" size="1" noshade color="#0000CC">';
							$data_to_html_file .= '<font face="Arial" size="-3">Report created by '.$info_str. ' on '.date('m/d/Y').' at '.date('h:i:sa').' from m/c ['.$_SERVER["REMOTE_ADDR"].'] using '.$_SERVER['HTTP_USER_AGENT'].'</font>';
							$data_to_html_file .= '<hr align="left" width="670" size="1" noshade color="#0000CC">';



	$file_name_prefix = rand(0,9);
	$file_name = $file_name_prefix.".html";    
	$file_pdf_name = $file_name_prefix.".pdf"; 

//production
/*
	$file_name = $trade_date_to_process."_dcar.html";     
	$file_pdf_name = $trade_date_to_process."_dcar.pdf";     
*/

	$fp = fopen ("d:\\tdw\\tdw\\data\\prnt\\".$file_name, "w");  
	fwrite ($fp,$data_to_html_file);        
	fclose ($fp); 

$cmd_pdf = "d:\\tdw\\tdw\\includes\\createpdf_args.bat ". $file_pdf_name. " " . $file_name;
//echo $cmd_pdf."<br>";
shell_exec($cmd_pdf);

//delete the temp html file
$cmd = "del d:\\tdw\\tdw\\data\\prnt\\".$file_name;
shell_exec($cmd);

header("Content-type: application/pdf");
header("Location: http://192.168.20.63/tdw/data/prnt/".$file_pdf_name);
exit();
?>