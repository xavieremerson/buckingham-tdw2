<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Edit Info</title>
<link rel="stylesheet" type="text/css" href="includes/styles.css" />
</head>
<body>
  <form action="<?=$_SERVER['REQUEST_URI']?>" method="post">
<?php
   session_start();
   include 'includes/global.php';
   include 'includes/dbconnect.php';
   include 'includes/functions.php';
   $ID = $_REQUEST['ID'];
   $repname = $_REQUEST['repname'];
   $action = $_REQUEST['action'];
   $i = 0; 
   
   if ($action == "edit") {
	  if (isset($_POST["save"])) {
	     $selected_rep2 = $_POST['sel_rep2'];
		 $srep_rrnum = $_POST['srep_rrnum'];

		 //RRNUM and Select Rep ERROR CHECKING
		 $array = array(1, 1, 1, 1, 1);
		 $test_name = array();
		 $test_name[1] = "The Shared RR Number cannot be blank.";
		 $test_name[2] = "The Shared RR Number entered is invalid:<br />&nbsp;&nbsp;Please enter a 3-digit number.";
		 $test_name[3] = "Shared RR Numbers must start with a 2 to<br />&nbsp;&nbsp;indicate that 2 sales reps share it.";
		 $test_name[4] = "The Shared RR Number is already being used.";
		 $test_name[5] = "You did not select a Shared Rep Name.";
			
		 if ($srep_rrnum == "") {
		    $array[1] = "0";
		    $srep_rrnum = 0;
		 } else {
		    if (!is_numeric($srep_rrnum) || strlen($srep_rrnum) < 3) {
		       $array[2] = "0";
		    } 
		    if (ord($srep_rrnum) != 50) {
			   $array[3] = "0";
		    } 
		    $unique_srep_rrnum = mysql_query("SELECT srep_rrnum from sls_sales_reps where srep_user_id='".$ID."' and srep_rrnum = '".$srep_rrnum."'") or die(mysql_error());
		    if (mysql_num_rows($unique_srep_rrnum) > 0) {
		       $array[4] = "0";
		    }
		  } 
		  if ($selected_rep2 == "0") {
		     $array[5] = "0";
		  } 
        
		  // ERRORS FOUND IN INPUT
		  if ($array[1] == "0" OR $array[2] == "0" OR $array[3] == "0" OR $array[4] == "0" OR $array[5] == "0") {
		     $errmsg = '<a class="red9">&nbsp;&nbsp;There are one or more invalid or incomplete fields.<br />Please resolve this problem and re-submit the data.</a>';
		  } else {
		     //if ($selected_rep2 != 0 && $selected_rep2 != $ID) {
		     $query_del_rep = "DELETE from sls_sales_reps where srep_user_id='".$ID."'";
	  	     $result_del_rep = mysql_query($query_del_rep) or die(mysql_error());
	  	     $query_ins_rep = "INSERT into sls_sales_reps (srep_user_id, srep_rrnum) values ('".$ID."', '".$srep_rrnum."'),('".$selected_rep2."', '".$srep_rrnum."')";
	  	     $result_ins_rep = mysql_query($query_ins_rep) or die(mysql_error());
			 ?>
	         <script type="text/javascript">
	           opener.location.href = opener.location.href
	           self.close()
	         </script>
		  <?php
		  }
	  }
	   
      // Show data
	  table_start_percent2(100, "Add RR Number");
	  ?>
	    <tr><td>&nbsp;</td></tr>
		<tr><td colspan="3" class="errnote">
	  <? 
	  for ($x = 1; $x < 6; $x++) {
	     if ($array[$x] == "0") {
		    echo "&nbsp;&nbsp;".$test_name[$x]."<br />";
		 } 
	  } 
      ?>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr valign="top">
	    <td colspan="3"><hr size="1" noshade="noshade" color="#0000FF" /></td>
	    </tr>
		<tr><td colspan="3" class="errnote">
		</td></tr>
        <tr valign="top" bgcolor="#333333" class="tblhead_a">
          <th><a>Rep Name</a></th>
          <th><a>Shared RR Number <font color="#FF0000">*</font></a></th>
          <th><a>Shared Rep Name <font color="#FF0000">*</font></a></th>
        </tr>
      </thead>
	  <tfoot class="datadisplay">
	    <tr valign="top">
	      <td colspan="3"><hr size="1" noshade="noshade" color="#0000FF" /></td></tr>
	    <tr valign="top">
	      <td colspan="3" align="center">
		    <p class="Contact">Fields marked with an asterisk <font color="#FF0000">*</font> are required<br />
		      <?=$errmsg?></p>
		    <p><input name="save" type="submit" class="Submit" id="save" value="Save" /></p>
		  </td>
		</tr>
      <tbody id="repTblBdy" class="datadisplay">
        <tr>
          <td width="10"><select name="sel_rep" size="1" class="datadisplay"><option value="<?=$ID?>"><?=$repname?></option></select></td>
          <td class="ilt" style="text-align: center"><input name="srep_rrnum" type="text" class="Text" style="text-align: center" maxlength="3" /></td>
           <td width="10"><select name="sel_rep2" size="1" class="datadisplay">
      <?
	  foreach ($repinfo2 as $row) {
	     $is_disabled = ($row["ID"] == $ID)? " disabled":"";
  		  ?>
		  <option value="<?=$row["ID"]?>"<?=$is_disabled?>><?=$row["Fullname"]?></option>
 	  	  <?php
  	  }
	 ?>
	    </select></td>
	  <?php
	  table_end_percent2();
	  exit;
   }
       // Get rep info data
	   table_start_percent2(100, "Edit RR (".$repname.")");
	   $repinfo = array();
  	  $query_show_rep = "SELECT ID, Fullname from users where Role = 3 and user_isactive = '1' ORDER by Fullname";
      $result_show_rep = mysql_query($query_show_rep) or die(mysql_error());
      while ($row_show_rep = mysql_fetch_array($result_show_rep)) {
         $repinfo[] = $row_show_rep;
      }
	  // Brackets are needed when creating an associative array with set index
	  $blank[0] = array("ID" => 0, "Fullname" => "-------------------");
	  // No brackets are needed when combining two arrays
	  $_SESSION['repinfo2'] = $blank + $repinfo;
	  $query_show_rrnum = "SELECT srep_rrnum from sls_sales_reps where srep_user_id=".$ID." and srep_rrnum != '' order by srep_rrnum";
  	  $result_show_rrnum = mysql_query($query_show_rrnum) or die(mysql_error());
	  
  	  // No shared RR number
	  if (mysql_num_rows($result_show_rrnum) < 1) { 
   	     ?>
		 <tr><td colspan="3"><p class="errnote" style="text-align: center">No Shared RR Numbers!</p></td></tr>
		 <tr><td colspan="3" align="center"><a href="<?=$_SERVER['REQUEST_URI']?>&amp;action=edit">Add shared RR number?</a></td></tr>
		 <?php
		  exit;
	  // If form saved
	  } elseif (isset($_POST["save"])) {
	     $selected_rep = $_POST["sel_rep"];
		 $selected_rep2 = $_POST["sel_rep2"];
	    
		 foreach ($srep_rrnum as $rrnum) {
		    // If selected rep name in the first select box does not match the current rep name being edited
		    // (No changes were made)
    	    if ($selected_rep[$i] != $ID) {
  	  	       $query_del_rep = "DELETE from sls_sales_reps where srep_rrnum='".$rrnum."' and srep_user_id='".$ID."'";
	  	       $result_del_rep = mysql_query($query_del_rep) or die(mysql_error());
	  	       $query_ins_rep = "INSERT into sls_sales_reps (srep_user_id, srep_rrnum) values ('".$selected_rep[$i]."', '".$rrnum."')";
	  	       $result_ins_rep = mysql_query($query_ins_rep) or die(mysql_error());
    	    }
		    // If selected rep name in the second select box does not match the previously selected rep names
		    // (No changes were made)
		    if ($selected_rep2[$i] != $gotten_id[$i]["srep_user_id"]) {
		       $query_del_rep2 = "DELETE from sls_sales_reps where srep_rrnum='".$rrnum."' and srep_user_id='".$selected_rep2[$i]."'";
	  	       $result_del_rep2 = mysql_query($query_del_rep2) or die(mysql_error());
      	       $query_ins_rep2 = "INSERT into sls_sales_reps (srep_user_id, srep_rrnum) values ('".$selected_rep2[$i]."', '".$rrnum."')";
	  	       $result_ins_rep2 = mysql_query($query_ins_rep2) or die(mysql_error());
		    }
		    $i = $i + 1;
		 }
	  	 ?>
	      <script type="text/javascript">
	        opener.location.href = opener.location.href
	        self.close()
	      </script>
	     <?php
  	  }
	  
	  // Show data
      $count_rrnum = 1;
  	  ?>
        <tr bgcolor="#333333" class="tblhead_a">
          <th><a>Rep Name</a></th>
          <th><a>Shared RR Number</a></th>
          <th><a>Shared Rep Name</a></th>
        </tr>
    </thead>
	<tfoot class="datadisplay">
	  <tr valign="top">
	    <td colspan="3"><hr size="1" noshade="noshade" color="#0000FF" /></td></tr>
      <tr valign="top">
        <td colspan="3" align="center"><p><input name="save" type="submit" class="Submit" id="save" value="Save &amp; Close" /></p></td>
      </tr>
    <tbody id="repTblBdy" class="datadisplay">
      <?php
	  $_SESSION['srep_rrnum'] = array();
	  $_SESSION['gotten_id'] = array();
      while ($row_show_rrnum = mysql_fetch_array($result_show_rrnum)) {
         if ($count_rrnum %  2) { 
	  	  	 $class_row = ' class="alternateRow"';
		 } else { 
	  		 $class_row = ''; 
		 }
		 ?>
      <tr<?=$class_row?>>
        <td width="10">
          <select name="sel_rep[]" size="1" class="datadisplay">
		 <?php
  		 foreach ($repinfo as $row) {
    	    $is_selected = ($row["ID"] == $ID)? " selected":"";
		    if ($row["ID"] == $ID) {
	  		   $selected = $row["ID"];
		 }
  		 ?>
	     <option value="<?=$row["ID"]?>"<?=$is_selected?>><?=$row["Fullname"]?></option>
  		 <?php
		 } 
		 ?>
          </select></td>
        <td align="center"><?=$srep_rrnum[] = $row_show_rrnum["srep_rrnum"]?></td>
        <td width="10">
	   <select name="sel_rep2[]" size="1" class="datadisplay">
         <?php
  		 //$is_selected = "";
		 // Limit 1 result row - Sometimes a sales rep has multiple shared rrnums
		 // which returns multiple rows with the same srep_user_id
  		 $query_get_id = "SELECT srep_user_id from sls_sales_reps, users where srep_rrnum='". $row_show_rrnum["srep_rrnum"]."' and srep_user_id != '".$selected."' and Role = 3 LIMIT 1";
  		 $result_get_id = mysql_query($query_get_id) or die(mysql_error());
    	 while ($row_get_id = mysql_fetch_array($result_get_id)) {
	  	    $gotten_id[] = $row_get_id;
		}
		if (mysql_num_rows($result_get_id) < 1) { //single rep, no shared rep in this case
	      $gotten_id[] = array("srep_user_id" => 0);
		  foreach ($repinfo2 as $row) {
      		$is_selected = ($row["ID"] == 0)? " selected":"";
			$is_disabled = ($row["ID"] == $ID)? " disabled":"";
      		?>
            <option value="<?=$row["ID"]?>"<?=$is_selected?><?=$is_disabled?>><?=$row["Fullname"]?></option>
          	<?php
    	  }
   		} else {
  		foreach ($repinfo2 as $row) {
    	    $is_selected = ($row["ID"] == $gotten_id[$i]["srep_user_id"])? " selected":"";
			$is_disabled = ($row["ID"] == $ID)? " disabled":"";
  		    ?>
		    <option value="<?=$row["ID"]?>"<?=$is_selected?><?=$is_disabled?>><?=$row["Fullname"]?></option>
          <?php
		}
	  }
  ?>
          </select></td>
		
  <?php
    $i++;
    $count_rrnum++;
  }
  ?>
<!-- TABLE EDIT END -->
<?php table_end_percent2(); ?>
    </form>
</body>
</html>