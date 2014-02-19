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

if ($vtitle != '') {

$str_sql_insert = "INSERT INTO help_data 
										(help_auto_id ,
										 help_title,
										 help_detail) 
									 VALUES (
											NULL , 
											'".$vtitle."', 
                      '".str_replace(chr(13),"<br>",$vdetail)."')";

//echo $str_sql_insert;
$result_insert = mysql_query($str_sql_insert) or die(tdw_mysql_error($str_sql_insert));
$success_str = "<img src='./images/blinkbox.gif' border='0'> Entered Help Info. for \"".$vtitle."\"";
}
?>
<script language="JavaScript" src="includes/js/popup.js"></script>
<link rel="stylesheet" type="text/css" href="includes/styles.css">
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr>
						  <td align="center"><a class="ghm"><?=$success_str?></a><!--<?=rand(1000000000,9999999999)?>--></td>
						</tr>
						</table>

	<? tsp(100, "TDW Dependencies"); ?>
		
		<table width="100%" height="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
			<tr>
				<td>
					<table width="100%"  border="0" cellspacing="1" cellpadding="1">
						<tr>
						  <td width="60">#</td>
							<td width="300">Title</td>
							<td width="600">Display Data</td> 
							<td>&nbsp;</td>
						</tr>
						<?
						$str_sql_select = "SELECT * 
																from help_data
                                order by help_auto_id desc";
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
 							<td>&nbsp;&nbsp;<?=$row_select["help_auto_id"]?></td>
							<td>&nbsp;&nbsp;<?=$row_select["help_title"]?></td>
							<td>&nbsp;&nbsp;<textarea readonly="readonly" rows="6" cols="80" class="Text"><?=str_replace("<br>",chr(13),$row_select["help_detail"])?></textarea></td>
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