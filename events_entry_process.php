<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<style type="text/css">
<!--
.tbl_head_news {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
	color: #000066;
}
-->
</style>
</head>
<body>
<?
include('includes/dbconnect.php');
include('includes/functions.php');


if ($vdate != '') {
/*
xdebug("vdate",$vdate);
xdebug("vamount",$vamount);
xdebug("vclient",$vclient);
xdebug("vcomment",$vcomment);
xdebug("venteredby",$venteredby);
*/

$str_sql_insert = "INSERT INTO news_events 
										( auto_id , 
										  news_date , 
											news_symbol , 
											news_event , 
											news_notes , 
											news_entered_by , 
											news_entered_datetime , 
											news_isactive ) 
										VALUES (										
											NULL , 
                      '".date('Y-m-d',strtotime($vdate))."', 
                      '".strtoupper($vsymbol)."', 
                      '".$vtype."', 
                      '".str_replace("'","\\'",$vnote)."', 
                      '".$venteredby."', 
											now(), 
											'1')";

//echo $str_sql_insert;
$result_insert = mysql_query($str_sql_insert) or die(tdw_mysql_error($str_sql_insert));
$success_str = "<img src='./images/blinkbox.gif' border='0'> Saved News/Event for Symbol ".strtoupper($vsymbol);
}
?>
<script language="JavaScript" src="includes/js/popup.js"></script>

<link rel="stylesheet" type="text/css" href="includes/styles.css">
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr>
						  <td align="center"><a class="ghm"><?=$success_str?></a><!--<?=rand(1000000000,9999999999)?>--></td>
						</tr>
						</table>

	<? tsp(100, "Last 20 News/Events entered."); ?>
		
		<table width="100%" height="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
			<tr>
				<td>
					<table width="100%"  border="0" cellspacing="1" cellpadding="1">
						<tr class="tbl_head_news">
							<td width="60">Date</td>
						  <td width="80">Type</td>
							<td width="60">Symbol</td> 
							<td width="600">Note</td>
							<td width="150">Entered By</td>
							<td>&nbsp;</td>
						</tr>
						<?
						$str_sql_select = "SELECT a.*, b.Fullname 
																from news_events  a
																left join Users b on a.news_entered_by = b.ID
																WHERE a.news_isactive = 1
                                order by a.auto_id desc limit 20";
            //xdebug("str_sql_select",$str_sql_select);
						$result_select = mysql_query($str_sql_select) or die(tdw_mysql_error($str_sql_select));

						$count_row_select = 0;
						while ( $row_select = mysql_fetch_array($result_select) ) 
						{
							if ($count_row_select%2) {
										$class_row_select = "trdark";
							} else { 
									$class_row_select = "trlight"; 
							} 
						?>
						<tr class="<?=$class_row_select?>"> 
 							<td valign="top">&nbsp;&nbsp;<?=format_date_ymd_to_mdy($row_select["news_date"])?></td>
							<td valign="top">&nbsp;&nbsp;<?=$row_select["news_event"]?></td>
							<td valign="top">&nbsp;&nbsp;<?=$row_select["news_symbol"]?></td>
							<td valign="top">&nbsp;&nbsp;<?=$row_select["news_notes"]?></td>
							<td valign="top">&nbsp;&nbsp;<?=trim($row_select["Fullname"])?></td>
							<td>&nbsp;</td>
						</tr>
						<?php
						$count_row_select = $count_row_select + 1;
						}
						?>
					</table>
				</td>
			</tr>
		</table>
		<? tep(); ?>
		</body>
</html>