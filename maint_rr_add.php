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


if ($save) {

//print_r($_POST);

//save the values in the database
//get all the values passed....

	$rep_1_userid = $sel_rep;
	$rep_2_userid = $sel_rep2;

  $shared_rep_num = $srep;
	
	if ($rep_1_userid != 0 AND
	    $rep_2_userid != 0 AND
			$shared_rep_num != '') 
			{
			$proceed = 1;

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
}

?>
  <form action="<?=$_SERVER['REQUEST_URI']?>" method="post">
<?php

   $srep_num = $_REQUEST['srep_num'];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Add Shared RR</title>
<link rel="stylesheet" type="text/css" href="includes/styles.css" />
</head>

<body leftmargin="2" topmargin="2" rightmargin="2" bottommargin="2"
<?
if ($save) {
echo 'onload="window.opener.location.reload();self.close();return false;"';
}
?>
>
<?	   
       // Get rep info data
	   tsp(100, "Add Shared RR");
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
          <td><a>&nbsp;&nbsp;RR #&nbsp;&nbsp;&nbsp;</a></td>
          <td><a>Rep Name</a></td>
        </tr>
    <tbody id="repTblBdy" class="datadisplay">
	  <tr valign="top">
	    <td colspan="5"><hr size="1" noshade="noshade" color="#0000FF" /></td>
		</tr>
      <tr<?=$class_row?>>
        <td width="10">
          <select name="sel_rep" size="1" class="datadisplay">
		 			<option value="0"> Select RR</option>
		 <?php
		 	 $arr_userid = get_shared_rep_userid ($srep_num);
			  
  		 foreach ($repinfo as $row) {
  		 ?>
	     <option value="<?=$row["ID"]?>"><?=$row["Fullname"]?></option>
  		 <?php
		 	} 
		 ?>
     		</select>
				</td>
        <td align="center"><input name="srep" value="" type="text" size="6" maxlength="3" /></td>
        <td width="10">
	   <select name="sel_rep2" size="1" class="datadisplay">
     <option value="0"> Select RR</option>
		     <?php

					foreach ($repinfo as $row) {
					?>
						<option value="<?=$row["ID"]?>"><?=$row["Fullname"]?></option>
					<?php
					}
  				?>
          </select></td>
					</tr>
					</tbody>
						<tfoot class="datadisplay">
			<tr valign="top">
				<td colspan="5"><hr size="1" noshade="noshade" color="#0000FF" /></td>
			</tr>
      <tr valign="top">
        <td colspan="5" align="center">
				<input name="save" type="submit" class="Submit" id="save" value="Save" />
				<input type="button" name="close" value="Close" class="Submit" onclick="window.opener.location.reload();self.close();return false;">
				</td>
      </tr>
</tfoot>
</table>		
<!-- TABLE EDIT END -->
<?php tep(); ?>
</body>
</html>