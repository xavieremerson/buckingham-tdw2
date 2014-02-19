<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Edit Info</title>
<link rel="stylesheet" type="text/css" href="includes/styles.css" />
</head>
<body>

<?php
  include('includes/global.php');
  include('includes/dbconnect.php');
  include('includes/functions.php');
  
  function print_repinfo($repinfo){
    // extract  the data from our array
    $repname_info = $repinfo["Fullname"];
  	$ID_info = $repinfo["ID"];
  	// print the rep info
  	//print("<li>$id: $repname, $ID<br/>");
 
  	//while($row = mysql_fetch_array($q)) {
  	$selected = ($ID_info == $ID)? " selected":"";
  	echo'<option value="'.$ID_info.$selected.'">'.$repname_info.'</option>';
  	//}
  	//echo"</select>";
  }	
  table_start_percent(100, "Edit RR (".$repname.")");
?>

<form action="<? $PHP_SELF?>" method="post">
  <!--TABLE EDIT START-->
  <table width="400" border="0" cellpadding="1" cellspacing="1"id="rep_table">
  <?php
  $i = 0;
  $query_show_rrnum = "SELECT srep_rrnum from tmp_sls_sales_reps where srep_user_id=".$ID." and srep_rrnum != '' order by srep_rrnum";
  $result_show_rrnum = mysql_query($query_show_rrnum) or die(mysql_error());
  $count_rrnum = 1;
  if (mysql_num_rows($result_show_rrnum) < 1) {
    echo "<tr><td align=\"center\"><h3>No shared Rep numbers!</h3></td></tr>";
	if ($save) {
	  ?>
	    <script type="text/javascript">
	      opener.location.href = opener.location.href
	      self.close()
	    </script>
	  <?
	}
  }
  else {
    ?>
      <thead class="datadisplay">
      <tr bgcolor="#333333" class="tblhead_a">
        <th>Rep Name</th>
        <th>Shared RR Number</th>
        <th>Shared Rep Name</th>
      </tr>
	</thead>  
    <tbody id="repTblBdy" class="datadisplay">
 <?	
   while($row_show_rrnum = mysql_fetch_array($result_show_rrnum))
  {

    if ($count_rrnum %  2) { 
	  $class_row = ' class="alternateRow"';
	} else { 
	  $class_row = ''; 
	}
?>

      <tr<?=$class_row?>>
        <td width="10"><select name="sel_rep[]" size="1">
	
<?
  $query_show_rep = "SELECT ID, Fullname from tmp_users where Role = 3 and user_isactive = '1'";
  $result_show_rep = mysql_query($query_show_rep) or die(mysql_error());
  $repinfo = array();
  while ($row_show_rep = mysql_fetch_array($result_show_rep))
  {
   array_push($repinfo,$row_show_rep);
  }
  mysql_free_result($result_show_rep);
  ?>
        </select></td>
        <td align="center"><?=$row_show_rrnum["srep_rrnum"]	?></td>
        <td width="10"><select name="sel_rep2[]" size="1">
<?
  $query_get_id = "SELECT srep_user_id, id, role from tmp_sls_sales_reps, tmp_users where srep_rrnum='".    $row_show_rrnum["srep_rrnum"]."' and srep_user_id != '".$selected."' and srep_user_id = id and Role = 3";
  $result_get_id = mysql_query($query_get_id) or die(mysql_error());
		
  if (mysql_num_rows($result_get_id) <= 0)  //single rep, no shared rep in this case
  {
    // do nothing
	$blank = "--------------------------";
	echo "<option value=\"0\">".$blank."</option>";
  } else {
    while ($row_get_id = mysql_fetch_array($result_get_id))
	{
	  $gotten_id = $row_get_id["srep_user_id"];
	}
	$query_show_rep = "SELECT ID, Fullname from tmp_users where Role = 3 and user_isactive = '1'";
	$result_show_rep = mysql_query($query_show_rep) or die(mysql_error());
	while ($row_show_rep = mysql_fetch_array($result_show_rep))
	{
 	  ?>
	  <option value="<?=$ID?>" 
	  <?
	    if ($row_show_rep["ID"] == $gotten_id) 
		{
		  echo " selected";
		}
	  ?>>
      <?=$Fullname?></option>
	<? 
	}
  }
  ?>
	</select></td>
		
  </tr>
  <?
    $count_rrnum = $count_rrnum + 1;
    if($save) {
  	  $query_del_rep = "DELETE from tmp_sls_sales_reps where srep_rrnum='".$row_show_rrnum["srep_rrnum"]."'";
	  $result_del_rep = mysql_query($query_del_rep) or die(mysql_error());
	  $query_ins_rep = "INSERT into tmp_sls_sales_reps (srep_user_id, srep_rrnum) values ('".$sel_rep[$i]."', '".$row_show_rrnum["srep_rrnum"]."')";
	  $result_ins_rep = mysql_query($query_ins_rep) or die(mysql_error());
      
	  if ($sel_rep2[$i] != 0) {
        $query_ins_rep2 = "INSERT into tmp_sls_sales_reps (srep_user_id, srep_rrnum) values ('".$sel_rep2[$i]."', '".$row_show_rrnum["srep_rrnum"]."')";
		$result_ins_rep2 = mysql_query($query_ins_rep2) or die(mysql_error());
	  }
	  $i++;
	  ?>
	  <script type="text/javascript">
	  opener.location.href = opener.location.href
	  self.close()
	  </script>
	  <?
    }
  }
  }
  ?>
  <tr><br />
  <td colspan="3" align="center"><input name="save" type="submit" id="save" value="Save &amp; Close" /></td>
  </tr>
  </tbody>
</table>
<!--TABLE EDIT END-->
<? table_end_percent(); ?>

</form>
</body>
</html>
