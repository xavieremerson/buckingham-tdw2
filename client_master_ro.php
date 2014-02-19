<?php
//BRG
include('inc_header.php');
?>
<script language="JavaScript" src="includes/prototype/prototype.js"></script>
<script language="javascript">

function test() {
alert("Add Client or Prospect");
}

function process_excel () {

	$("legend_type").style.visibility = "hidden";
	$("legend_type").style.display = "none";

  var url = 'http://192.168.20.63/tdw/client_master_excel.php';
  var pars = 'user_id=<?=$user_id?>';
  pars = pars + '&mod_request=appr';
	
	if ($("show_deleted").checked == true) {
		pars = pars + '&show_deleted=1';
	} else {
		pars = pars + '&show_deleted=0';
	}
	
	if ($('filter_by').value == '-ALL-') {
		pars = pars + '&thiscriteria=ALL';
	} else if ($('filter_by').value == 'filter_name') {
		pars = pars + '&thiscriteria=filter_name';
		pars = pars + '&valcriteria=' + $("sel_filter_name").value;
	} else if ($('filter_by').value == 'filter_tier') {
		pars = pars + '&thiscriteria=filter_tier';
		pars = pars + '&valcriteria=' + $("sel_filter_tier").value;
	} else if ($('filter_by').value == 'filter_reps') {
		pars = pars + '&thiscriteria=filter_reps';
		pars = pars + '&valcriteria=' + $("sel_filter_reps").value;
	} else if ($('filter_by').value == 'filter_trdr') {
		pars = pars + '&thiscriteria=filter_trdr';
		pars = pars + '&valcriteria=' + $("sel_filter_trdr").value;
	} else if ($('filter_by').value == 'filter_type') {
		pars = pars + '&thiscriteria=filter_type';
		pars = pars + '&valcriteria=' + $("sel_filter_type").value;
	} else {
		var dummmy = 1;
	}
	
		pars = pars + '&mqy_sel=' + $("mqy_sel").value;
	
	pars = pars + '&req_ajax=1';
  var ran_number= Math.random()*5; 
	pars = pars + '&xrand=' + ran_number;

  var newurl = url + "?" + pars;
	
	window.open(newurl,'excelfile')
	//$("content_area").src = newurl;

}


function getClientData()
{

	$("legend_type").style.visibility = "hidden";
	$("legend_type").style.display = "none";

  var url = 'http://192.168.20.63/tdw/client_master_ro_ajx.php';
  var pars = 'user_id=<?=$user_id?>';
  pars = pars + '&mod_request=appr';
	
	if ($("show_deleted").checked == true) {
		pars = pars + '&show_deleted=1';
	} else {
		pars = pars + '&show_deleted=0';
	}
	
	if ($('filter_by').value == '-ALL-') {
		pars = pars + '&thiscriteria=ALL';
	} else if ($('filter_by').value == 'filter_name') {
		pars = pars + '&thiscriteria=filter_name';
		pars = pars + '&valcriteria=' + $("sel_filter_name").value;
	} else if ($('filter_by').value == 'filter_tier') {
		pars = pars + '&thiscriteria=filter_tier';
		pars = pars + '&valcriteria=' + $("sel_filter_tier").value;
	} else if ($('filter_by').value == 'filter_reps') {
		pars = pars + '&thiscriteria=filter_reps';
		pars = pars + '&valcriteria=' + $("sel_filter_reps").value;
	} else if ($('filter_by').value == 'filter_trdr') {
		pars = pars + '&thiscriteria=filter_trdr';
		pars = pars + '&valcriteria=' + $("sel_filter_trdr").value;
	} else if ($('filter_by').value == 'filter_type') {
		pars = pars + '&thiscriteria=filter_type';
		pars = pars + '&valcriteria=' + $("sel_filter_type").value;
	} else {
		var dummmy = 1;
	}
	
		pars = pars + '&mqy_sel=' + $("mqy_sel").value;
	
	pars = pars + '&req_ajax=1';
  var ran_number= Math.random()*5; 
	pars = pars + '&xrand=' + ran_number;

  var newurl = url + "?" + pars;
	$("content_area").src = newurl;
	
}


function showfiltercriteria(showcontainer) {
	//alert(showcontainer);
	//First hide all divs
	$("filter_reps").style.visibility = "hidden";
	$("filter_reps").style.display = "none";
	$("filter_name").style.visibility = "hidden";
	$("filter_name").style.display = "none";
	$("filter_tier").style.visibility = "hidden";
	$("filter_tier").style.display = "none";
	$("filter_trdr").style.visibility = "hidden";
	$("filter_trdr").style.display = "none";
	$("filter_type").style.visibility = "hidden";
	$("filter_type").style.display = "none";
	$("legend_type").style.visibility = "hidden";
	$("legend_type").style.display = "none";
	
	if ($("filter_by").value != "-ALL-") {
		$(showcontainer).style.visibility = "visible";
		$(showcontainer).style.display = "block";
	}
	
	if ($("filter_by").value == "filter_type") {
		$("legend_type").style.visibility = "visible";
		$("legend_type").style.display = "block";
	}

}
</script>

<script language="JavaScript">
<!--
function resize_iframe()
{

	var height=window.innerWidth;//Firefox
	if (document.body.clientHeight)
	{
		height=document.body.clientHeight;//IE
	}
	//resize the iframe according to the size of the
	//window (all these should be on the same line)
	document.getElementById("content_area").style.height=parseInt(height-
	document.getElementById("content_area").offsetTop-120)+"px";
}

// this will resize the iframe every
// time you change the size of the window.
window.onresize=resize_iframe; 

//Instead of using this you can use: 
//	<BODY onresize="resize_iframe()">


//-->
</script>

<?
///////////////////////////////////////////////  START OF MANAGE SECTION  ////////////////////////////////////////////////////////////
		echo "<center>";
	?>
	<? tsp100(100, "Client & Prospect Maintenance"); ?>
	<?	
		if($action == "remove")
		{
			$query_delete = "UPDATE int_clnt_clients SET clnt_isactive = '0' WHERE clnt_auto_id = '$ID'";
			$result_delete = mysql_query($query_delete) or die(mysql_error());
		}

		if($action == "undelete")
		{
			$query_delete = "UPDATE int_clnt_clients SET clnt_isactive = '1' WHERE clnt_auto_id = '$ID'";
			$result_delete = mysql_query($query_delete) or die(mysql_error());
		}

?>
	<style type="text/css">
<!--
.iframestyle {
	border-top: none;
	border-right: none;
	border-bottom: none;
	border-left: none;	
}
-->
  </style>
	
<table width="100%" height="100%" border="0" cellpadding="1" cellspacing="0"> 
  <tr>
    <td width="5">&nbsp;</td>
    <td align="left"><a class="links11" style="cursor:pointer;" onclick="showPopWin('client_master_add.php?user_id=<?=$user_id?>', 470, 400, null);"><img src="images/plus14.png" border="0" /> Add Prospect</a></td>
    <td width="200" align="right"><a class="links11">Filter Condition </a></td>
    <td width="150" align="right">
      <select name="filter_by" id="filter_by" onchange="showfiltercriteria( $('filter_by').value )" style="width:170px">
        <option value="-ALL-">Show All</option>
        <option value="filter_type">Client or Prospect</option>
        <option value="filter_name">Client/Prospect Name</option>
        <option value="filter_tier">Client Tier</option>
        <option value="filter_reps">Sales Reps.</option>
        <option value="filter_trdr">Traders</option>
      </select>
    </td>
    <td width="200">
     <div id="filter_type" style="visibility:hidden; display:none;">
        <select name="sel_filter_type" id="sel_filter_type" size="1" style="width:200px">
          <option value="A">Active Clients</option>
          <option value="AP">All Prospects</option>
          <option value="P1">P1</option>
          <option value="P2">P2</option>
          <option value="P3">P3</option>
          <option value="P4">P4</option>
          <option value="P0">P0</option>
          <option value="NP">All Non-Prospects</option>
          <option value="X1">X1</option>
          <option value="X2">X2</option>
          <option value="X3">X3</option>
          <option value="X4">X4</option>
		 		</select>
     </div>
     <div id="filter_tier" style="visibility:hidden; display:none;">
        <select name="sel_filter_tier" id="sel_filter_tier" size="1" style="width:200px">
          <option value="1">Tier 1</option>
          <option value="2">Tier 2</option>
          <option value="3">Tier 3</option>
          <option value="4">Tier 4</option>
		 		</select>
     </div>
     <div id="filter_reps" style="visibility:hidden; display:none">
        <select name="sel_filter_reps" id="sel_filter_reps" size="1" style="width:200px">
        <?
        $qry_get_reps = "SELECT
                          a.ID, a.rr_num, concat(a.Firstname, ' ', a.Lastname ) as rep_name, a.Initials, a.rr_num as trad_rr 
                          from users a
                        WHERE a.rr_num like '0%'
                        AND a.Role > 2
                        AND a.Role < 5
                        ORDER BY a.Firstname";
        $result_get_reps = mysql_query($qry_get_reps) or die (tdw_mysql_error($qry_get_reps));
        while($row_get_reps = mysql_fetch_array($result_get_reps))
        {
        ?>
        <option value="<?=$row_get_reps["Initials"]?>"> <?=$row_get_reps["rep_name"]?></option>
        <?
        }
		 ?>
     </select>
		 </div>
     <div id="filter_trdr" style="visibility:hidden; display:none">
        <select name="sel_filter_trdr" id="sel_filter_trdr" size="1" style="width:200px">
        <?
        $qry_get_reps = "SELECT
                          a.ID, a.rr_num, concat(a.Firstname, ' ', a.Lastname ) as rep_name, a.Initials, a.rr_num as trad_rr 
                          from users a
                        WHERE a.rr_num like '0%'
                        AND a.Role = 4
												AND a.user_isactive = 1
                        ORDER BY a.Firstname";
        $result_get_reps = mysql_query($qry_get_reps) or die (tdw_mysql_error($qry_get_reps));
        while($row_get_reps = mysql_fetch_array($result_get_reps))
        {
        ?>
        <option value="<?=$row_get_reps["Initials"]?>"> <?=$row_get_reps["rep_name"]?></option>
        <?
        }
		 ?>
     </select>
		 </div>
     <div id="filter_name" style="visibility:hidden; display:none">
      <select id="sel_filter_name" name="sel_filter_name" style="width:200px">
        <option value="A">Select First Letter</option>
      <?

      $qry = "SELECT count(clnt_name) as xcount , substring(clnt_name, 1, 1 ) as strltr
          FROM int_clnt_clients 
          WHERE 1 = 1
          GROUP BY substring(clnt_name, 1, 1 )";
      $result = mysql_query($qry) or die(tdw_mysql_error($qry));      
      while ( $row = mysql_fetch_array($result) )	{
        echo '<option value="'.$row["strltr"].'">'.$row["strltr"].'</option>';//<a class="links11" href="client_master.php?strltr='.$row["strltr"].$str_get_link.'"> '.$row["strltr"].' </a>&nbsp;';
      }
      ?>
      </select>
      </div>
    </td>
		<td width="150">
    <select id="mqy_sel" style="width:150px">
    	<option value="Y">Include YTD</option>
    	<option value="Q">Include QTD</option>    	
      <option value="M">Include MTD</option>
    </select>
    </td>
		<td width="160"><input name="show_deleted" id="show_deleted" type="checkbox" value="1"/> <a class="links11">Show Deleted Clients</a></td> 
    <td width="80"><input type="button" name="btn_clnt_data" id="btn_clnt_data" value=" SUBMIT " onclick="getClientData();" /></td>
    <td width="150">&nbsp;&nbsp;<a class="ilt" href="javascript:process_excel();"><img src="images/lf_v1/exp2excel.png" border="0" /></a></td><!-- target="_blank"-->
		</tr>
    </table>
    <div id="legend_type" class="links11" style="visibility:hidden; display:none;">
		<hr size="1" noshade color="#0099FF" />
      Legend: Client/Prospect Type Codes.<br />
      <ol>
        <li>A : Active Client</li>
        <li>P1 : Prospect Assigned</li>
        <li>P2 : Prospect Unassigned, Formerly Covered w/ Business</li>
        <li>P3 : Prospect Unassigned, Formerly Covered w/o Business</li>
        <li>P4 : Prospect Unassigned, New Prospect</li>
        <li>P0 : Prospect Unassigned, OTHER</li>
        <li>X1 : Non-Prospect - Formerly Deleted</li>
        <li>X2 : Out of Business</li>
        <li>X3 : DO NOT CALL</li>
        <li>X4 : Non-Prospect : OTHER</li>
      </ol>
		<hr size="1" noshade color="#0099FF" />
    </div>
    <!----><iframe src="client_master_ro_ajx.php?proc_user=<?=$user_id?>" class="iframestyle" id="content_area" scrolling="auto" width="100%" style="border-width:thin; margin-top:0px; margin-left:0px;" onload="resize_iframe()"></iframe>

		<? tep100();
		
		echo "</center>";
/////////////////////////////////////////////////END OF MANAGE SECTION/////////////////////////////////////////////////

  include('inc_footer.php');
?>
