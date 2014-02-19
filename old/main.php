<?php
include('top.php');
include('includes/functions.php'); 
?>
<tr>	
	<td valign="top">
		<!-- START TABLE 1 -->
		<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
			<tr> 
				<td><!--Main Page<BR>User logged in: <B><? echo $user; ?></B>--></td>
			</tr>
			<tr valign="top"> 
				<td valign="top" nowrap width="1050">
					<!-- START TABLE 2 -->
					<table width="100%" height="100%"  border="0" cellspacing="0" cellpadding="5">
						<tr valign="top"> 
							<td valign="top" width="330" height="100%" nowrap>  <!-- 350 -->
							
								<?
									$tdate = previous_business_day ();
									/****************************  LISTS DIPLAY  ***********************************************************************************/
									//SYSTEM LISTS
									$query_list_system = "SELECT slis_system_lists.*,  mlis_num_trades FROM mlis_main_list LEFT JOIN slis_system_lists ON slis_auto_id = mlis_list_id WHERE slis_isactive = '1' AND mlis_list_type_id = '1' AND mlis_trade_date = '".$tdate."' ORDER BY slis_auto_id";
									$result_list_system = mysql_query($query_list_system) or die (mysql_error());

									//ADMIN LISTS
									$query_list_admin = "SELECT alis_admin_lists.*, mlis_num_trades FROM mlis_main_list LEFT JOIN alis_admin_lists ON alis_auto_id = mlis_list_id WHERE alis_isactive = '1' AND mlis_list_type_id = '2' AND mlis_trade_date = '".$tdate."' ORDER BY alis_auto_id";
									$result_list_admin = mysql_query($query_list_admin) or die (mysql_error());
	
									//USER LISTS
									$query_list_user = "SELECT usli_user_lists.*, mlis_num_trades FROM mlis_main_list LEFT JOIN usli_user_lists ON usli_auto_id = mlis_list_id WHERE usli_isactive = '1' AND usli_user_id = '".$user_id."' AND mlis_list_type_id = '3' AND mlis_trade_date = '".$tdate."' ORDER BY usli_auto_id";
									$result_list_user = mysql_query($query_list_user) or die (mysql_error());
									/*******************************************************************************************************************************/
									
									/*****************************  ACTION ITEMS  ***********************************************************************************/
									//OPEN ITEMS
									$query_open_item = "SELECT COUNT(*) AS count FROM acti_action_item_flag WHERE acti_datetime_closed = '' and acti_user_id = ".$user_id;
									$result_open_item = mysql_query($query_open_item) or die (mysql_error());
									$row_open_item = mysql_fetch_array($result_open_item);
									
									//CLOSED ITEMS
									$query_closed_item = "SELECT COUNT(*) AS count FROM acti_action_item_flag WHERE acti_datetime_closed != '' and acti_user_id = ".$user_id;
									$result_closed_item = mysql_query($query_closed_item) or die (mysql_error());
									$row_closed_item = mysql_fetch_array($result_closed_item);
									/*******************************************************************************************************************************/
								
									/*****************************  ALERTS  ****************************************************************************************/
									$query_alert = "SELECT * FROM aloc_allocation WHERE (aloc_percent >= aloc_limit) AND aloc_isactive = '1'";
									$result_alert = mysql_query($query_alert) or die(mysql_error());
									/*******************************************************************************************************************************/
									
									/****************************** TRADE ORDERS  ***********************************************************************************/
									if($user_id == $_comp_off_id)
									{
										$query_order = "SELECT * FROM ordr_orders WHERE ordr_isactive = '1' AND ordr_authorizedby = ''";
									}
									else
									{
										$query_order = "SELECT * FROM ordr_orders WHERE ordr_user_id = '".$user_id."' AND ordr_isactive = '1'";
									}
									$result_order = mysql_query($query_order) or die(mysql_error());
									/*******************************************************************************************************************************/
								
									/****************************** DASHBOARD CUSTOMIZATION  *********************************************************************/
									$query_dash = "SELECT cdas_component_id FROM cdas_customize_dashboard WHERE cdas_user_id = '".$user_id."' AND cdas_isactive = '1' AND cdas_admin_authorized = '1' AND cdas_user_authorized = '1'";
									$result_dash = mysql_query($query_dash) or die(mysql_error());
									
									$arr_component = array();
									while($row_dash = mysql_fetch_array($result_dash))
									{
										$arr_component[] = $row_dash['cdas_component_id'];
									}
									
									if(count($arr_component) < 1)
									{
										echo '<a class= "links12" >Customize your dashboard by going to:<br></a> <a class= "links12" href="myprofile.php">My Profile/Preferences</a>';
									}
									/*******************************************************************************************************************************/
								?>
							
								<?
								if(in_array(1, $arr_component))
								{
								?>
								<? table_start(320, "Trades in Lists"); ?>
								<div id="tableContainer1" class="tableContainer1">
											<!--TABLE 4 START-->
											<table class="scrollTable" width="100%" cellspacing="0">
												<tbody class="scrollContent1">
												<?
												//SYSTEM LISTS
												while($row_list_system = mysql_fetch_array($result_list_system))
												{
												?>
													<tr class="tablerow" onClick="javascript:parent.location.href='lists_display.php?list_type=<?=$row_list_system['slis_list_type_id']?>&type=<?=$row_list_system['slis_auto_id']?>'"> 
														<td height="20"><a href="lists_display.php?list_type=<?=$row_list_system['slis_list_type_id']?>&type=<?=$row_list_system['slis_auto_id']?>"><img src="images/lf_v1/goto_details.jpg" border="0"></a>&nbsp;&nbsp;<a class="emptrades1" href="lists_display.php?list_type=<?=$row_list_system['slis_list_type_id']?>&type=<?=$row_list_system['slis_auto_id']?>"><?=$row_list_system["slis_title_name"]?></a></td>
														<td><a class="emptrades1"><?=$row_list_system["mlis_num_trades"]?></a></td>
													</tr>
													
												<?
												}
												?>
												
												<?
												//ADMIN LISTS
												while($row_list_admin = mysql_fetch_array($result_list_admin))
												{
												?>
													<tr class="tablerow" onClick="javascript:parent.location.href='lists_display.php?list_type=<?=$row_list_admin['alis_list_type_id']?>&type=<?=$row_list_admin['alis_auto_id']?>'"> 
														<td height="20"><a href="lists_display.php?list_type=<?=$row_list_admin['alis_list_type_id']?>&type=<?=$row_list_admin['alis_auto_id']?>"><img src="images/lf_v1/goto_details.jpg" border="0"></a>&nbsp;&nbsp;<a class="emptrades2" href="lists_display.php?list_type=<?=$row_list_admin['alis_list_type_id']?>&type=<?=$row_list_admin['alis_auto_id']?>"><?=$row_list_admin["alis_title_name"]?></a></td>
														<td><a class="emptrades2"><?=$row_list_admin["mlis_num_trades"]?></a></td>
													</tr>
												<?
												}
												?>
												
												<?
												//USER LISTS
												while($row_list_user = mysql_fetch_array($result_list_user))
												{
												?>
													<tr class="tablerow" onClick="javascript:parent.location.href='lists_display.php?list_type=<?=$row_list_user['usli_list_type_id']?>&type=<?=$row_list_user['usli_auto_id']?>'"> 
														<td height="20"><a class="emptrades3" href="lists_display.php?list_type=<?=$row_list_user['usli_list_type_id']?>&type=<?=$row_list_user['usli_auto_id']?>"><?=$row_list_user["usli_title_name"]?></a></td>
														<td><a class="emptrades3"><?=$row_list_user["mlis_num_trades"]?></a></td>
													</tr>
												<?
												}
												?>
 												</tbody>
											</table>
										<!-- TABLE 4 END -->
								</div>
											<? table_end();  ?>
								<?
								}
								
								?>
								<br>
								<!-- Last 5 IPOs -->
								<? table_start(320, "Last 5 IPOs"); ?>
								<?
								$qry_ipo = "select * from IPO_info order by ipo_date desc limit 5";
								//ipo_symbol ipo_description 
								$result_ipo = mysql_query($qry_ipo) or die (mysql_error());
								?>
								
								
								<div id="tableContainer9" class="tableContainer9">
										<table class="scrollTable"  width="100%"  cellspacing="0">
											<tbody class="scrollContent2">
											<?
											while ( $row = mysql_fetch_array($result_ipo) ) {
											?>
											<tr class="tablerow"> 
												<td height="18" width="20"><a class="emptrades" href="javascript:CreateWnd('pop_quote.php?param_symbol=<?=$row["ipo_symbol"]?>', 500, 200, false);"><img src="images/lf_v1/goto_mktdata.jpg" border="0"></a>&nbsp;&nbsp;<a class="emptrades" href="javascript:CreateWnd('pop_quote.php?param_symbol=<?=$row["ipo_symbol"]?>', 500, 200, false);"><?=$row["ipo_symbol"]?></a></td>
												<td width="200"><a class="emptrades" href="javascript:CreateWnd('pop_quote.php?param_symbol=<?=$row["ipo_symbol"]?>', 500, 200, false);"><?=$row["ipo_description"]?></a></td>
												<td width="100"><a class="emptrades"><?=format_date_ymd_to_mdy($row["ipo_date"])?></a></td>
											</tr>
											<?
											}
											?>
 												</tbody>
										</table>
								</div>
											<? table_end();  ?>
								<?

								if(in_array(2, $arr_component))
								{								
								?>
								<br>
								<? table_start(320, "Action Items"); ?>
								<div id="tableContainer2" class="tableContainer2">
										<!--START TABLE 6-->
										<table class="scrollTable"  width="100%"  cellspacing="0">
											<tbody class="scrollContent2">
											<tr class="tablerow" onClick="javascript:parent.location.href='actionitems.php'"> 
												<td height="20" width="50"><a class="emptrades" href="actionitems.php"><img src="images/lf_v1/tasks_todo.jpg" border="0"></a>&nbsp;&nbsp;<a class="emptrades" href="actionitems.php">Open</a></td>
												<td width="30"><a class="emptrades"><?=$row_open_item["count"]?></a></td>
											</tr>
											<tr class="tablerow" onClick="javascript:parent.location.href='actionitems_close.php'"> 
												<td height="20"><a class="emptrades" href="actionitems_close.php"><img src="images/lf_v1/tasks_done.jpg" border="0"></a>&nbsp;&nbsp;<a class="emptrades" href="actionitems_close.php">Closed</a></td>
												<td><a class="emptrades"><?=$row_closed_item["count"]?></a></td>
											</tr>
											</tbody>
										</table>
										<!-- END TABLE 6 -->
								</div>
											<? table_end();  ?>
								<?
								}	
									
								if(in_array(3, $arr_component))
								{								
								?>
								<br>
								<!-- ALERTS -->
								<? table_start(320, "Alerts"); ?>
								<div id="tableContainer2a" class="tableContainer2a">
											<!--START TABLE 6-->
											<table class="scrollTable"  width="100%"  cellspacing="0">
												<tbody class="scrollContent2a">
												<?
												if(mysql_num_rows($result_alert) > 0)
												{
													while($row_alert = mysql_fetch_array($result_alert))
													{
														$query_port = "SELECT port_name FROM port_portfolio WHERE port_auto_id = '".$row_alert['aloc_port_id']."' AND port_isactive = '1'";
														$result_port = mysql_query($query_port) or die(mysql_error());
														$row_port = mysql_fetch_array($result_port);
													?>
														<tr class="tablerow" onClick=""> 
															<td  width="100%"><b><?=$row_alert["aloc_name"]?></b> in <b><?=$row_port['port_name']?></b> is above threshold.</td>
														</tr>
												<?
													}
												}
												else
												{
												?>
													<tr class="tablerow" onClick=""> 
														<td width="100%"><a class="emptrades">Currently there are no alerts.</a></td>
													</tr>
												<?
												}
												?>
												</tbody>
											</table>
											<!-- END TABLE 6 -->
								</div>
											<? table_end();  ?>
								<?
								}
							
								
								if(in_array(4, $arr_component))
								{								
									if(mysql_num_rows($result_order) > 0)
									{
									?>
									<br>
									<!-- ORDER AUTHORIZATION -->
								<? table_start(320, "Order Pre-authorization"); ?>
									<div id="tableContainer2b" class="tableContainer2b">
												<!--START TABLE 6A -->
												<table class="scrollTable"  width="100%"  cellspacing="0">
													<tbody class="scrollContent2b">
													<?
													while($row_order = mysql_fetch_array($result_order))
													{
														if($user_id == $_comp_off_id)
														{
															$flag = 1;
													?>
															<tr class="tablerow" onClick="javascript:CreateWnd('pop_order_authorize.php?id=<?=$row_order['ordr_auto_id']?>&user_id=<?=$user_id?>&flag=<?=$flag?>>', 680, 430, false);"> 
																<td>
																	<b><?=$row_order["ordr_buy_sell"]?>&nbsp;<?=$row_order['ordr_quantity']?></b> of <b><?=$row_order['ordr_symbol']?></b>
																</td>
															</tr>
														<?
														}
														else
														{
															$flag = 0;
														?>												
															<tr class="tablerow" onClick="javascript:CreateWnd('pop_order_authorize.php?id=<?=$row_order['ordr_auto_id']?>&user_id=<?=$user_id?>&flag=<?=$flag?>>', 680, 430, false);"> 
																<td>
																	<b><?=$row_order["ordr_buy_sell"]?>&nbsp;<?=$row_order['ordr_quantity']?></b> of <b><?=$row_order['ordr_symbol']?></b>
																</td>
																<td>	
																	<?
																	if($row_order["ordr_isauthorized"] > 0)
																	{
																	?>
																	<font color="#009900">Accepted</font>
																	<?
																	}
																	else
																	if($row_order["ordr_isauthorized"] < 0)
																	{
																	?>
																	<font color="#ff0000">Declined</font>
																	<?
																	}
																	else
																	{
																	?>
																	<font color="#000000">Pending</font>
																	<?
																	}
																	?>
																</td>
															</tr>
														<?
														}
														?>
													<?
													}
													?>
													</tbody>
												</table>
												<!-- END TABLE 6A -->
									</div>
											<? table_end();  ?>
									<?
									}
								}
								//x
								?>

							</td>
							<td valign="top" width="495" nowrap align="left">   <!-- 645 -->
								<!-- START TABLE 8 -->	
								<table width="495" border="0" cellpadding="0" cellspacing="0">
									<?
									if(in_array(6, $arr_component))
									{								
									?>
									<tr>
										<td>
								<? table_start(490, "Trades by Type (Last 3 months)"); ?>
																		<img src="data/charts/custvsemp.png" border="1">
								<? table_end();  ?>
										
										</td>
									</tr>
								</table>
								<br>
								<table>
									<?
									}
									
									if(in_array(7, $arr_component))
									{								
									?>
									<tr>
										<td>
								<? table_start(490, "Top Buys/Sells"); ?>
																<!-- START TABLE 9 -->
																<table>
																	<tr>
																		<td>
																			<img src="inc_pie_chart1.php" border="1">
																		</td>
																		<td>
																			<img src="inc_pie_chart2.php" border="1">
																		</td>
																	</tr>
																</table>
																<!-- END TABLE 9 -->
											<? table_end();  ?>
										</td>
									</tr>
								</table>
								<br>
								<table>
									<?
									}
									if(in_array(8, $arr_component))
									{								

										$query_port = "SELECT port_auto_id AS d_value, port_name AS d_option FROM port_portfolio WHERE port_isactive = '1'";
										$result_port = mysql_query($query_port) or die(mysql_error());
									?>
									<form name="port_frm" action="main.php" method="post">
									<tr>
										<td>
									<? table_start(490, "Allocations"); ?>
																<table border="0" align="center" width="490" cellpadding="0" cellspacing="0">
																	<tr> 
																		<td width="100%">
																			<a class="appmytext">&nbsp;Select Portfolio :</a>
																			&nbsp;&nbsp;
																			<select class="Text" name="port_id">
																			<? createdropdown2($result_port, $port_id); ?>
																			</select>
																			&nbsp;&nbsp;
																			<input class="submit" type="submit" value=" Go ">
																		</td>
																	</tr>
																
																	<tr>
																		<td width="100%">
																				<fieldset style="BORDER-RIGHT: #9999ff 1px solid; 
																				BORDER-TOP: #9999ff 1px solid; 
																				BORDER-LEFT: #9999ff 1px solid;
																				PADDING: 0px;
																				WIDTH: 490px; 
																				HEIGHT: 220px;
																				COLOR: #555555; 
																				BORDER-BOTTOM: #9999ff 1px solid"> 
																				<img src="inc_pie_chart3.php?pid=<?=$port_id?>" border="0">
																				</fieldset>
																		</td>
																	</tr>
																</table>
																
											<? table_end();  ?>

										</td>
									</tr>
									<?
									}
									?>
									</form>
								</table>
								<!-- END TABLE 8 -->
							</td>
							<!-- Third Column Begin -->
							<td align="left">
								<table><tr><td>
									
								<?						
									if(in_array(5, $arr_component))
								{								
								?>								
								<!-- emp/cust trades table here -->
								<? table_start(220, "Customer/Employee Trades"); ?>
										<div id="tableContainer3" class="tableContainer3">
										<!-- START TABLE 7 -->
										<table border="0" cellpadding="0" cellspacing="0" width="100%" class="scrollTable">
										<tr>
											<td width="60">&nbsp;&nbsp;<a class="mainpage_tablehead">Symbol</a></td>
											<td>&nbsp;&nbsp;<a class="mainpage_tablehead">Customer</a></td>
											<td>&nbsp;&nbsp;<a class="mainpage_tablehead">Employee</a></td>
										</tr>

										<tbody class="scrollContent2">
										
										<?
										//$query_tickers = "SELECT a.tdat_ticker, a.tdat_cust, a.tdat_emp, b.trdm_sec_description FROM tdat_ticker_data a, Trades_m b WHERE a.tdat_trade_date = '".$tdate."' AND a.tdat_isactive = '1' and a.tdat_ticker = b.trdm_symbol ORDER BY a.tdat_ticker ASC";
										$query_tickers = "SELECT a.tdat_ticker, a.tdat_cust, a.tdat_emp FROM tdat_ticker_data a WHERE a.tdat_trade_date = '".$tdate."' AND a.tdat_isactive = '1' ORDER BY a.tdat_ticker ASC";
										$result_tickers = mysql_query($query_tickers) or die(mysql_error());
										
										while($row_tickers = mysql_fetch_array($result_tickers))
										{
											if ($row_tickers['tdat_cust']==0) {
												$cust_tradeval = "&ndash;";
											} else {
												$cust_tradeval = $row_tickers['tdat_cust'];
											}
											
											if ($row_tickers['tdat_emp']==0) {
											$emp_tradeval = "&ndash;";
											} else {
											$emp_tradeval = $row_tickers['tdat_emp'];
											}
										
										?>
										
										<tr>
											<td width="60"><a class="emptrades" href="javascript:CreateWnd('pop_quote.php?param_symbol=<?=$row_tickers['tdat_ticker']?>', 500, 200, false);"><img src="images/lf_v1/goto_mktdata.jpg" border="0"></a>&nbsp;&nbsp;<?=$row_tickers['tdat_ticker']?></td>
											<td width="115"><?=$cust_tradeval?></td>
											<td width="115"><?=$emp_tradeval?></td>
										</tr>
										<?
										}
										?>
										
									</tbody>
								</table>
								<!-- END TABLE 7 -->
								</div>
											<? table_end();  ?>
								<?
								}
								?>
								</td>
								</tr>
								<tr><td>
								<br>
								<? table_start(220, "Messages"); ?>
								<div id="status_app">
								<!-- <iframe src="status_app/frame.php"></iframe> -->
								<? include("status_app/frame.php");?>
								</div>
								<? table_end();  ?>
								</td></tr>
								</table>
							</td>
							<!-- Third Column End -->
						</tr>
					</table>
				<!-- END TABLE 2 -->		
				</td>
			</tr>
		</table>
		<!-- END TABLE 1 -->
	</td>
</tr>
<?php
include('bottom.php');
?>

