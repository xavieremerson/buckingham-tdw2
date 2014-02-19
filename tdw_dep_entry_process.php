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

if ($vsource != '') {

	if ($is_edit > 0) {
    $result_update = mysql_query("update tdw_server_dependencies set dep_isactive = 0 where auto_id = '".$is_edit."'") or die(tdw_mysql_error("ERROR"));
	}

//print_r($_GET);
$str_sql_insert = "INSERT INTO tdw_server_dependencies 
									( auto_id , dep_source , dep_type , dep_direction , 
									dep_remarks , dep_added , dep_edited , dep_user , dep_isactive ) 
									VALUES (
									NULL , 
									'".str_replace("\\","\\\\",$vsource)."', 
									'".$vtype."', 
									'".str_replace("\\","\\\\",$vdirection)."', 
									'".str_replace("\\","\\\\",str_replace("\n","<br>",$vremarks))."', 
									'".date('Y-m-d')."', 
									'".date('Y-m-d')."', 
									'".$venteredby."', 
									'1'
									)";

//echo $str_sql_insert;
$result_insert = mysql_query($str_sql_insert) or die(tdw_mysql_error($str_sql_insert));
$success_str = "<img src='./images/blinkbox.gif' border='0'> Entered Dependency Info. for \"".$vsource."\"";
}
?>
<script language="JavaScript" src="includes/js/popup.js"></script>
<link rel="stylesheet" type="text/css" href="includes/styles.css">
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr>
						  <td align="center"><a class="ghm"><?=$success_str?></a><!--<?=rand(1000000000,9999999999)?>--></td>
						</tr>
						</table>

	<? tsp(100, "Help Data List."); ?>
		
		<table width="100%" height="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
			<tr>
				<td>
					<table width="100%"  border="0" cellspacing="1" cellpadding="1">
						<tr>
						  <td width="40">Edit</td>
						  <td width="200">Source</td>
							<td width="200">Type</td>
							<td width="60">Direction</td> 
						  <td>Comments</td>
						</tr>
						<?
						$str_sql_select = "SELECT * 
																FROM tdw_server_dependencies
																WHERE dep_isactive = 1
                                order by auto_id desc";
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
							<td>&nbsp;<a href="tdw_dep_entry_container.php?rec_edit=<?=$row_select["auto_id"]?>" target="_parent"><img src="images/themes/standard/edit.gif" /></a></td>
 							<td>&nbsp;&nbsp;<?=$row_select["dep_source"]?></td>
							<td>&nbsp;&nbsp;<?=$row_select["dep_type"]?></td>
							<td>&nbsp;&nbsp;<?=$row_select["dep_direction"]?></td>
							<td><?=str_replace("\n","<br>",$row_select["dep_remarks"])?></td>
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