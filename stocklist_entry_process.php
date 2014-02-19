<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
</head>
<body>
<?
include('includes/dbconnect.php');
include('includes/functions.php');

if($r) {
$qry="update bcm_watchlist
			set bcm_datetime_stop = now()
			where auto_id = '".$r."'";
//,	bcm_comment = concat(bcm_comment, '\n', 'Removed by ')
$result = mysql_query($qry) or die(tdw_mysql_error($qry));
$str_status = "<font color='green'>Watch List entry for ".strtoupper(trim($symbol))." archived.</font>";
}

if (trim($symbol) != "") {

//print_r($_GET);

//exit;
/*
*/
	if (trim($symbol) != "") {
	
			$str_start_date = date ("Y-m-d H:i:s", strtotime($syear."-".$smonth."-".$sday." ".$shour.":".$smin.$sampm));
			if ($is_manual_close) {
				$str_end_date = '2099-12-31 00:00:00';
				$str_open_end = 1;
			} else {
				$str_end_date = date ("Y-m-d H:i:s", strtotime($eyear."-".$emonth."-".$eday." ".$ehour.":".$emin.$eampm));
				$str_open_end = 0;
			}
			
			$qry="insert into bcm_watchlist
						(auto_id,
						bcm_date_added,
						bcm_cusip,
						bcm_datetime_start,
						bcm_datetime_stop,
						bcm_added_by,
						bcm_open_end,
						bcm_comment) values (
						NULL,
						now(),
						'".strtoupper(trim($symbol))."',
						'".$str_start_date."',
						'".$str_end_date."',
						'".$venteredby."',
						'".$str_open_end."',
						'".str_replace("'","",$notes)."'
						)";
			//xdebug("qry",$qry);
			$result = mysql_query($qry) or die(tdw_mysql_error($qry));
			$str_status = "<font color='green'>Watch List entry for ".strtoupper(trim($symbol))." saved.</font>";
		} else {
			$str_status = "<font color='red'>Record not saved. Symbol is missing. Please try again.</font>";
		}



$success_str = "<img src='./images/blinkbox.gif' border='0'> ".$str_status;
}
?>
<script language="JavaScript" src="includes/js/popup.js"></script>
<link rel="stylesheet" type="text/css" href="includes/styles.css">
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr>
						  <td align="center"><a class="ghm"><?=$success_str?></a><!--<?=rand(1000000000,9999999999)?>--></td>
						</tr>
						</table>

	<? tsp(100, "Watch List"); ?>
		
    <table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
			<tr>
				<td>
					<!--TABLE 2 START-->
					<script language="JavaScript" src="includes/javascript/sorttable.js" type="text/javascript"></script>

					<table class="sortable" preserve_style="cell" width="100%"  border="0" cellspacing="1" cellpadding="1">
					<tr>
						<td width="30">&nbsp;</td>
						<td width="80">Symbol</td>
						<td width="100">Start</td>
						<td width="100">End</td>
						<td width="400">Comment / Info</td>
						<td>&nbsp;</td>
					</tr>

				 <?
				 $max_row_id = db_single_val("select max(auto_id) as single_val from bcm_watchlist");
		
				 if ($period) {
						if ($period == 'T') {
							$str_period = " bcm_datetime_start > '".date('Y-m-d')."' or bcm_datetime_stop = '2099-12-31' 
															or (
															bcm_datetime_stop < '".date('Y-m-d')." 23:59:00' 
															AND 
															bcm_datetime_stop > '".date('Y-m-d')." 00:00:00' 
															)";
						} elseif ($period == 'P') {
							$str_period = " bcm_datetime_start > '".previous_business_day()."' and bcm_datetime_start < '".date('Y-m-d')."' " ;
						} else {
							$str_period = " bcm_datetime_stop > now() or bcm_datetime_stop = '2099-12-31' ";
						}
				 } else {
							$str_period = " bcm_datetime_stop > now() or bcm_datetime_stop = '2099-12-31' ";
				 }
		
				 //auto_id, bcm_date_added,  bcm_cusip,  bcm_datetime_start,  bcm_datetime_stop,  bcm_auto,  bcm_open_end,  bcm_comment  
				 $qry_rlist = "SELECT * FROM `bcm_watchlist` 
											 where " . $str_period . " 
											 ORDER BY auto_id desc"; //bcm_cusip, bcm_datetime_stop 
				 $result_rlist = mysql_query($qry_rlist) or die(tdw_mysql_error($qry_rlist));
					
				 $hold_symbol = "";
				 $count_row = 0;
				 while ($row = mysql_fetch_array($result_rlist)) {
					 if ($hold_symbol == "" or $row["bcm_cusip"] != $hold_symbol) {
								if ($count_row%2 == 0) {
									$rowclass = " class=\"trlight\"";
								} else {
									$rowclass = " class=\"trdark\"";
								}
						
								if ($row["bcm_datetime_stop"] == "2099-12-31 00:00:00") {
									$str_show_end = "";
								} else {
									$str_show_end = date("m/d h:ia",strtotime($row["bcm_datetime_stop"]));
								}
						?>
						<tr <?=$rowclass?>>
							<td><a href="<?=$PHP_SELF?>?r=<?=$row["auto_id"]?>">
              			<img src="images/themes/standard/delete.gif" border="0" alt="Remove" />
                  </a>
              </td> <!-- onclick="info_show('<?=$row["auto_id"]?>^<?=$row["bcm_cusip"]?>');" -->
							<td>&nbsp;&nbsp;<?=$row["bcm_cusip"]?></td>
							<td><?=date("m/d h:ia",strtotime($row["bcm_datetime_start"]))?></td>
							<td><?=$str_show_end?></td>
							<td><?=$row["bcm_comment"]?></td>
							<td>&nbsp;</td>
						</tr>
						<?
						$count_row++;
						}
 				  $hold_symbol = $row["bcm_cusip"];
					}
					?>
					</table>
					</div>
					</div>
					<? tep(); ?>
      		<input type="hidden" name="max_row_id" id="max_row_id" value="<?=$max_row_id?>" />
		</td>
	</tr>
</table>
		<? tep(); ?>
		</body>
</html>