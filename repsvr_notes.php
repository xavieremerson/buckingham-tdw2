<?
   include 'includes/global.php';
   include 'includes/dbconnect.php';
   include 'includes/functions.php';

		if (isset($note_id)) {
		//print_r($_POST);
							$note_update_val = str_replace("'","\'",substr($_POST["ta_".$note_id],0,1000));
		
							if ($_POST["keep_open_".$note_id] == 1) {
									$qry_update_note = "UPDATE mgmt_reports_notes
																				set 
																				msrn_notes = concat(msrn_notes,
																														"."'\\n'".",
																														'[Added on ".date('m/d H:i a')."]'".",
																														"."'\\n'".",
																														'".$note_update_val."'),
																				msrn_isopen = 1
																				WHERE auto_id = '".$note_id."'";
						   //echo $qry_update_note;
							} else {
									$qry_update_note = "UPDATE mgmt_reports_notes
																				set 
																				msrn_notes = concat(msrn_notes,
																														"."'\\n'".",
																														'[Closed on ".date('m/d H:i a')."]'".",
																														"."'\\n'".",
																														'".$note_update_val."'),
																				msrn_isopen = 0
																				WHERE auto_id = '".$note_id."'";
							}
							
							$result_update_note = mysql_query($qry_update_note) or die(tdw_mysql_error($qry_update_note));
		}

if ($save) {

		//print_r($_POST);
		
		if (!isset($is_open)) {
		$is_open = 0;
		}
		//Array ( [addnote] => xdfsdfsdfsdf [user_id] => 252 [rep_auto_id] => 68 [save] => Save ) 
					if ($addnote != '') {
							$qry_insert_note = "INSERT INTO mgmt_reports_notes
																		(auto_id, 
																		msrn_rep_auto_id, 
																		msrn_userid, 
																		msrn_notes_datetime, 
																		msrn_notes,
																		msrn_isopen, 
																		msrn_isactive) 
																		VALUES (
																		NULL , 
																		'".$rep_auto_id."', 
																		'".$user_id."', 
																		now(), 
																		'".str_replace("'","\'",substr($addnote,0,1000))."', 
																		'".$is_open."',
																		'1'
																		)";
							$result_insert_note = mysql_query($qry_insert_note) or die(tdw_mysql_error($qry_insert_note));
						}
}


//get_count_notes
 $arr_count_notes = array();
 $query_count_notes = "SELECT msrn_rep_auto_id , count( msrn_notes ) as count_notes
											FROM mgmt_reports_notes
											WHERE msrn_rep_auto_id = ".$rep_auto_id." 
											GROUP BY msrn_rep_auto_id";
											
 //xdebug("query_count_notes",$query_count_notes);	
 //exit;										

 $result_count_notes = mysql_query($query_count_notes) or die(tdw_mysql_error($query_count_notes));
 
 if (mysql_num_rows($result_count_notes) > 0) {
 $proceed_show = 1;
 } else {
 $proceed_show = 0;
 }

	while ($row_count_notes = mysql_fetch_array($result_count_notes)) {
	$arr_count_notes[$row_count_notes["msrn_rep_auto_id"]]=	$row_count_notes["count_notes"];
 }


//print_r($_POST);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Add/View Notes</title>
<link rel="stylesheet" type="text/css" href="includes/styles.css" />
<style type="text/css">
<!--
#scrollElement {
	width: 590px;
	height: 370px;
	padding: 1px;
	border: 1px solid #cc0000;
	overflow: scroll; 
}
-->
</style>

</head>

<body leftmargin="3" topmargin="3" rightmargin="3" bottommargin="3" onunload="window.opener.location.reload();self.close();return false;"> <!-- onunload="window.opener.location.reload();self.close();return false;" -->

<?	   
	   table_start_percent(100, "Add Note");
		 ?>
			<form action="<?=$_SERVER['REQUEST_URI']?>" method="post" name="mainnote" id="mainnote">
			<textarea wrap="physical" name="addnote" cols="71" rows="8"></textarea><br />
			<input type="hidden" name="user_id" value="<?=$user_id?>" />
			<input type="hidden" name="rep_auto_id" value="<?=$rep_auto_id?>" />
      <table><tr><td>
			<input type="checkbox" value="1" name="is_open" checked="checked" title="Action Pending" /> &nbsp;<a class="ilt">Action Pending</a>&nbsp;</td></tr>
			<tr><td>
      <input name="save" type="submit" class="Submit" id="save" value="Save" onClick="javascript:document.mainnote.submit();"  />
			<input type="button" name="close" value="Close" class="Submit" onclick="window.opener.location.reload();self.close();return false;">
      </td></tr></table>
			&nbsp;&nbsp;&nbsp; <a class="ilt">(Limit: 1000 characters)</a></form>
		 <?
	   table_end_percent();
		 echo "<br>";
		 
		 
		 
		 //show section only if there is data to show
     if ($proceed_show == 1) {
		 
									
									if ($arr_count_notes[$report_id] == '') {
									$title_mesg = '';
									} elseif ($arr_count_notes[$report_id] == 1) {
									$title_mesg = '&nbsp;&nbsp;&nbsp;(1 Note)';
									} else {
									$title_mesg = '&nbsp;&nbsp;&nbsp;('.$arr_count_notes[$report_id].' Notes)';
									}
		 
		 							//xdebug("title_mesg",$title_mesg);
		 
								 ?>
								 <div id="scrollElement">
								 <?
									 // Get rep info data
								 table_start_percent(97, "View Notes".$title_mesg);
								 
								 $query_show_notes = "SELECT a.*, DATE_FORMAT( a.msrn_notes_datetime, '%c/%e/%y %l:%i %p' ) as note_time, b.Fullname 
																		FROM mgmt_reports_notes a, users b
																		where a.msrn_rep_auto_id = '".$rep_auto_id."'
																			AND a.msrn_userid = b.ID
																		ORDER BY a.auto_id desc";
																		
									$result_show_notes = mysql_query($query_show_notes) or die(tdw_mysql_error($query_show_notes));
									
									$count_row = 0;
									while ($row_show_notes = mysql_fetch_array($result_show_notes)) {
									if ($count_row%2 == 0) {
										$rowclass = " class=\"trlight\"";
									} else {
										$rowclass = " class=\"trdark\"";
									}
									?>
										<table width="565" border="0" cellspacing="0" cellpadding="4">
											<form name="frm_<?=$row_show_notes["auto_id"]?>" id="frm_<?=$row_show_notes["auto_id"]?>" action="<?=$_SERVER['REQUEST_URI']?>", method="post">
										<tr <?=$rowclass?>>
											<td><?=$row_show_notes["Fullname"]?> (<?=$row_show_notes["note_time"]?>)&nbsp;&nbsp;&nbsp;</td>
										</tr>
										<tr <?=$rowclass?>>
										 <?
										 $noteval_display = str_replace("\n",'<br>',$row_show_notes["msrn_notes"]);
										 //$noteval_display = str_replace(chr(13),'<br>',$row_show_notes["msrn_notes"]);
										 //$noteval_display = $row_show_notes["msrn_notes"];
										 
										 ?>
											<td><p align="justify"><b><?=$noteval_display?></b></p></td>
										</tr>
											<?
											if ($row_show_notes["msrn_isopen"]==1) {
											?>
											<tr <?=$rowclass?>>
											<td>
												<table>
												<tr><td>
												<textarea wrap="physical" name="ta_<?=$row_show_notes["auto_id"]?>" rows="3" cols="50"></textarea>
												<input type="hidden" name="note_id" value="<?=$row_show_notes["auto_id"]?>" />
												<input type="hidden" name="rep_auto_id" value="<?=$rep_auto_id?>" />
												<input type="hidden" name="keep_open_<?=$row_show_notes["auto_id"]?>" id="keep_open_<?=$row_show_notes["auto_id"]?>" value="" />
												</td>
												<td>
												<input onClick="javascript:document.getElementById('keep_open_<?=$row_show_notes["auto_id"]?>').value=1; document.frm_<?=$row_show_notes["auto_id"]?>.submit();" name="name" type="button" id="btn_keepopen" value="Add to Note" />
												<input onClick="javascript:document.getElementById('keep_open_<?=$row_show_notes["auto_id"]?>').value=0; window.opener.location.reload(); document.frm_<?=$row_show_notes["auto_id"]?>.submit();" type="button" name"btn_save" id="btn_save" value="Close Out" />
												</td></tr>
												</table>
											</td>
											</tr>
											<?
											}											
											?>
											</form>
  										</table>
											<hr width="565" size="2" noshade color="#999999" /> 

									<?
									$count_row++;
									}
						?>
						<!-- TABLE EDIT END -->
						<?php table_end_percent(); ?>
						</div>
		<?
		}
		?>
</body>
</html>