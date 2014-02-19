											?>
												<tr>
													<td valign="top">
													<hr size="1" noshade color="#000066">
														<table class="all_general">
																<tr>
																	<td>
																				<!-- Showing clients in NFS which don't exist in DOS -->
																				<hr size="3" noshade color="#0000ff">
																				<table class="all_general">
																					<tr>
																						<td width="50"><strong>Client</strong></td>
																						<td width="30"><strong>Commission</strong></td>
																					</tr>
																					<?
																					//DOS COMMISSIONS
																					$total_dos = 0;
																					$query_dos = "SELECT * 
																												FROM mry_dos_commission order by clnt_code";
																					$result_dos = mysql_query($query_dos) or die(tdw_mysql_error($query_dos));
																					while($row_dos = mysql_fetch_array($result_dos)) {
																					//$total_dos = $total_dos + $row_dos["clnt_commission"];
																					//$grand_total_dos = $grand_total_dos + $row_dos["clnt_commission"];
																						if (in_array($row_dos["clnt_code"],$arr_clients)) {
																						//do nothing
																						} else {
																						$total_dos = $total_dos + $row_dos["clnt_commission"];
																						$grand_total_dos = $grand_total_dos + $row_dos["clnt_commission"];
																					?>
																					<tr>
																						<td><?=$row_dos["clnt_code"]?></td>
																						<td align="right"><?=number_format($row_dos["clnt_commission"],2)?></td>
																					</tr>
																					<?
																						}
																					}
																					?>
																					<tr>
																						<td><strong></strong></td>   
																						<td align="right">
																						<?
																						echo '<strong><font color="0000ff"><u>'.number_format($total_dos,2).'</u></font></strong>';
																						?>
																						</td>
																					</tr>
																				</table>
																	</td>
																</tr>
														</table>
													</td>
													<td bgcolor="#666666" width="2"></td>
													<td>
													</td>
												</tr>
											<?
