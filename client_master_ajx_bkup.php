<?
  include('includes/dbconnect.php');
  include('includes/global.php');
  include('includes/functions.php');


//echo "from ajax";
//exit;
//print_r($_GET);
?>

<? 
if ($show_deleted == 1) {
	$str_show_deleted = " clnt_isactive like '%' ";
} else {
	$str_show_deleted = " clnt_isactive = '1' ";
}

$query_clients = "SELECT * from int_clnt_clients where ".$str_show_deleted." and clnt_name like '".$strltr."%' order by clnt_name";
?>
<script language="javascript" src="includes/javascript/gs_sortable.js"></script>

    <table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
			<tr>
				<td>
					<!--TABLE 2 START-->
					<!--<script language="JavaScript" src="includes/javascript/sorttable.js" type="text/javascript"></script>-->
					<script type="text/javascript">
          <!--
          var TSort_Data = new Array ('sort_this', '','','','s', 's', 's', 's', 's','s','');
					var TSort_Classes = new Array ('ztrlight', 'ztrdark');
					var TSort_Icons = new Array (' &#923;',' V');
          tsRegister();
          // -->
          </script> 
					<table id="sort_this" width="100%"  border="0" cellspacing="1" cellpadding="1"><!-- class="sortable" preserve_style="cell"-->
						<thead>
							<tr class="headrow">
						  <td width="28">DEL</td>
							<td width="30">EDIT</td>
							<td width="30">UnDEL</td>
							<td width="210">Client Name</td>
							<td width="80">Code</td>
							<td width="80">T'ware Code</td>
							<td width="35">RR1</td>
							<td width="35">RR2</td>
							<td width="35">Trdr.</td>
							<td width="65"><?=date('Y')?> YTD</td>
							<td width="65">Last Year</td>
							<td width="65">Tier</td>
							<td width="200">Comments</td>
							<td>&nbsp;</td>
							</tr>
            </thead>
						
							<script type="text/javascript">
							var dc = new Array()
				
						<?
												
						$str_get_append = "";
						if ($show_deleted == 1) {
						$str_get_append = "&show_deleted=1&strltr=".$strltr;
						} else {
						$str_get_append = "&strltr=".$strltr;
						}
						
						
						$result = mysql_query($query_clients) or die(mysql_error());
						$count_row = 0;
						while ( $row = mysql_fetch_array($result) ) 
						{
						
						if ($row["clnt_alt_code"]=='INACTIVE') {
						$str_clnt_code = "<font color=red>".$row["clnt_alt_code"].'</font>';
						} else {
						$str_clnt_code = $row["clnt_alt_code"];
						}

						echo 'dc ['.$count_row.'] = "'.$row["clnt_auto_id"].'^'.
																									trim($row["clnt_name"]).'^'.
																									trim(str_replace("'","",$row["clnt_name"])).'^'.
																									$row["clnt_code"].'^'.
																									$str_clnt_code.'^'.
																									$row["clnt_rr1"].'^'.
																									$row["clnt_rr2"].'^'.
																									$row["clnt_trader"].'^'.
																									$row["clnt_isactive"].'^'.
																									$str_get_append.'"'.";\n";
						
						$count_row = $count_row + 1;
						}
						?>
							for (i=0;i<dc.length;i++)
							{
									var rowclients_array = new Array()
									var rowclass
									if (i%2 == 0) {
										rowclass = " class=\"ztrdark\"";
									} else {
										rowclass = " class=\"ztrlight\"";
									}
							
									rowclients_array=dc[i].split("^");
									
									if(rowclients_array[8]=="1") {
											document.write(
													"<tr" + rowclass + ">"+
													"<td nowrap>&nbsp; <a href=\"cmgmt.php?type=manage&action=remove&ID="+rowclients_array[0]+rowclients_array[9]+"\"  onclick=\"javascript:return confirm('Are you sure you want to remove "+rowclients_array[2]+" from the list?')\"><img src=\"images/themes/standard/delete.gif\" alt=\"Delete\"></a>&nbsp; </td>"+
													"<td nowrap>&nbsp; <a href=\"javascript:CreateWnd(\'cmgmt_edit.php?ID="+rowclients_array[0]+"', 550, 450, false);\"><img src=\"images/themes/standard/edit.gif\" alt=\"Edit\"></a>&nbsp; </td>"+
													"<td>"+" "+"</td>"+ 
													"<td>&nbsp;"+rowclients_array[1]+"</td>"+ 
													"<td>&nbsp;"+rowclients_array[3]+"</td>"+ 
													"<td>&nbsp;"+rowclients_array[4]+"</td>"+ 
													"<td>&nbsp;"+rowclients_array[5]+"</td>"+ 
													"<td>&nbsp;"+rowclients_array[6]+"</td>"+ 
													"<td>&nbsp;"+rowclients_array[7]+"</td>"+ 
													"<td></td>" +
													"<td></td>" +
													"<td></td>" +
													"<td></td>" +
													"<td></td></tr>");							
									} else {
											document.write(
													"<tr" + rowclass + ">"+
													"<td nowrap>&nbsp;</td>"+
													"<td nowrap>&nbsp;</td>"+
													"<td nowrap>&nbsp; <a href=\"cmgmt.php?type=manage&action=undelete&ID="+rowclients_array[0]+rowclients_array[9]+"\"  onclick=\"javascript:return confirm('Are you sure you want to ADD BACK "+rowclients_array[2]+" to the list?')\"><img src=\"images/plus14.png\" alt=\"Add Back\"></a>&nbsp; </td>"+
													"<td>&nbsp;"+rowclients_array[1]+"</td>"+ 
													"<td>&nbsp;"+rowclients_array[3]+"</td>"+ 
													"<td>&nbsp;"+rowclients_array[4]+"</td>"+ 
													"<td>&nbsp;"+rowclients_array[5]+"</td>"+ 
													"<td>&nbsp;"+rowclients_array[6]+"</td>"+ 
													"<td>&nbsp;"+rowclients_array[7]+"</td>"+ 
													"<td></td>" +
													"<td></td>" +
													"<td></td>" +
													"<td></td>" +
													"<td></td></tr>");							
									}
							}
							
							</script>
					</table>

				</td>
			</tr>
		</table>
