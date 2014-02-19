<?
   include 'includes/global.php';
   include 'includes/dbconnect.php';
   include 'includes/functions.php';
	 
function get_shared_repnames ($rr_num) {
		$qry_shared_rep_users = "select srep_user_id from sls_sales_reps where srep_isactive = 1 and srep_rrnum = '".$rr_num."'";
    $result_shared_rep_users = mysql_query($qry_shared_rep_users) or die(tdw_mysql_error($qry_shared_rep_users));
		$str_reps = "";
		while($row_shared_rep_users = mysql_fetch_array($result_shared_rep_users)) {
			$str_reps = get_user_by_id($row_shared_rep_users["srep_user_id"]) . " / " . $str_reps;
		}
		return $rr_num . " : " . substr($str_reps,0,strlen($str_reps)-3);
}

function get_shared_rep_userid ($rr_num) {
	$qry_shared_rep_users = "select srep_user_id from sls_sales_reps where srep_isactive = 1 and srep_rrnum = '".$rr_num."'";
	//echo $qry_shared_rep_users;
	$result_shared_rep_users = mysql_query($qry_shared_rep_users) or die(tdw_mysql_error($qry_shared_rep_users));
	if (mysql_num_rows($result_shared_rep_users) == 1) {
		while($row_shared_rep_users = mysql_fetch_array($result_shared_rep_users)) {
			$arr_userid[0] = $row_shared_rep_users["srep_user_id"];
		}
			$arr_userid[1] = 0;
	} else {
		$i = 0;
		while($row_shared_rep_users = mysql_fetch_array($result_shared_rep_users)) {
			$arr_userid[$i] = $row_shared_rep_users["srep_user_id"];
			$i++;
		}
	}
	return $arr_userid;
}

//Array ( [sel_rep] => 225 [percent_1] => 50 [sel_rep2] => 236 [percent_2] => 50 [srep] => 208 [save] => Save & Close ) 

if ($save) {

//save the values in the database
//get all the values passed....

	$rep_1_userid = $sel_rep;
	$rep_2_userid = $sel_rep2;

  $shared_rep_num = $srep;

	//then set the relevant records to inactive
	$qry_inactive = "update sls_sales_reps 
											set 
												srep_isactive = 0,
												srep_enddate = now(),
												srep_last_updated_by = '".$user_id."'
											 where srep_rrnum = '".$shared_rep_num."'";
	//echo $qry_inactive;										 
	$result_inactive = mysql_query($qry_inactive) or die(tdw_mysql_error($qry_inactive));
	
	//srep_auto_id  srep_user_id  srep_rrnum  srep_percent  srep_begindate  srep_enddate  srep_isactive  srep_last_updated  srep_last_updated_by  
	
	//then insert relevant records
	$qry_insert_1 = "INSERT INTO sls_sales_reps 
									( srep_auto_id,
										srep_user_id,
										srep_rrnum,
										srep_begindate,
										srep_enddate,
										srep_isactive,
										srep_last_updated,
										srep_last_updated_by ) 
									VALUES (
										NULL,
										'".$rep_1_userid."', 
										'".$shared_rep_num."', 
										now(), 
										NULL,
										'1', 
										now(), 
										'".$user_id."')";
	$result_insert_1 = mysql_query($qry_insert_1) or die(tdw_mysql_error($qry_insert_1));
	
	$qry_insert_2 = "INSERT INTO sls_sales_reps 
									( srep_auto_id,
										srep_user_id,
										srep_rrnum,
										srep_begindate,
										srep_enddate,
										srep_isactive,
										srep_last_updated,
										srep_last_updated_by ) 
									VALUES (
										NULL,
										'".$rep_2_userid."', 
										'".$shared_rep_num."', 
										now(), 
										NULL,
										'1', 
										now(), 
										'".$user_id."')";
	$result_insert_2 = mysql_query($qry_insert_2) or die(tdw_mysql_error($qry_insert_2));

}


//print_r($_POST);
?>
  <form action="<?=$_SERVER['REQUEST_URI']?>" method="post">
<?php

   $srep_num = $_REQUEST['srep_num'];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Edit RR (<?=get_shared_repnames ($srep_num)?>)</title>
<link rel="stylesheet" type="text/css" href="includes/styles.css" />
</head>
<body leftmargin="2" topmargin="2" rightmargin="2" bottommargin="2">

<?	   
       // Get rep info data
	   tsp(100, "Edit RR (".get_shared_repnames ($srep_num).")");
 	  $query_show_rep = "SELECT ID, Fullname
													FROM users
													WHERE rr_num NOT LIKE '%999%'
													AND rr_num IS NOT NULL 
													AND rr_num != ''
													AND user_isactive = 1
													ORDER BY Fullname";
      $result_show_rep = mysql_query($query_show_rep) or die(mysql_error());
      while ($row_show_rep = mysql_fetch_array($result_show_rep)) {
         $repinfo[] = $row_show_rep;
      }
	  // Brackets are needed when creating an associative array with set index
	  $blank[0] = array("ID" => 0, "Fullname" => "-------------------");

?>
		<table width="100%"> 
        <tr bgcolor="#333333" class="tblhead_a">
          <td><a>Rep Name</a></td>
          <td><a>RR #</a></td>
          <td><a>Rep Name</a></td>
        </tr>
    <tbody id="repTblBdy" class="datadisplay">
	  <tr valign="top">
	    <td colspan="5"><hr size="1" noshade="noshade" color="#0000FF" /></td>
		</tr>

      <tr<?=$class_row?>>
        <td width="10">
          <select name="sel_rep" size="1" class="datadisplay">
		 <?php
		 	 $arr_userid = get_shared_rep_userid ($srep_num);
			 //$arr_userpercent = get_shared_rep_percent ($srep_num);		
			  
  		 foreach ($repinfo as $row) {
    	    $is_selected = ($row["ID"] == $arr_userid[0])? " selected":"";
		    if ($row["ID"] == $arr_userid[0]) {
	  		   $selected = $row["ID"];
		 }
  		 ?>
	     <option value="<?=$row["ID"]?>"<?=$is_selected?>><?=$row["Fullname"]?></option>
  		 <?php
		 } 
		 ?>
     </select></td>
        <td align="center"><?=$srep_num?></td>
        <td width="10">
	   <select name="sel_rep2" size="1" class="datadisplay">
     <option value="0">-----------------------</option>
		     <?php
  		 //$is_selected = "";
		 // Limit 1 result row - Sometimes a sales rep has multiple shared rrnums
		 // which returns multiple rows with the same srep_user_id
		 
			
			if ($arr_userid[1]==0) { //single rep, no shared rep in this case
					$gotten_id[] = array("srep_user_id" => 0);
					foreach ($repinfo as $row) {
							$is_selected = ($row["ID"] == 0)? " selected":"";
							$is_disabled = ($row["ID"] == $arr_userid[0])? " disabled":"";
							?>
								<option value="<?=$row["ID"]?>"<?=$is_selected?><?=$is_disabled?>><?=$row["Fullname"]?></option>
								<?php
					}
				} else {
					foreach ($repinfo as $row) {
							$is_selected = ($row["ID"] == $arr_userid[1])? " selected":"";
							$is_disabled = ($row["ID"] == $arr_userid[0])? " disabled":"";
							?>
						<option value="<?=$row["ID"]?>"<?=$is_selected?><?=$is_disabled?>><?=$row["Fullname"]?></option>
							<?php
					}
				}
  				?>
          </select></td>
					</tr>
					</tbody>
	  <tr valign="top">
	    <td colspan="5"><hr size="1" noshade="noshade" color="#0000FF" /></td>
		</tr>
      <tr valign="top">
        <td colspan="5" align="center">
				<input type="hidden" name="srep" value="<?=$srep_num?>" />
				<input name="save" type="submit" class="Submit" id="save" value="Save" />
				<input type="button" name="close" value="Close" class="Submit" onclick="window.opener.location.reload();self.close();return false;">
				</td>
      </tr>
</table>
		
<!-- TABLE EDIT END -->
<?php tep(); ?>
</body>
</html>