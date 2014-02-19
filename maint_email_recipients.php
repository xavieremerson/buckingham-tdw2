<?
//BRG
include('inc_header.php');
?>
<?
	tsp(100, "Email Recipients Maintenance"); 
//SSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSS
?>
<script language="JavaScript" src="includes/prototype/prototype.js"></script>
<script language="javascript">

function add_email_list () {
	$("email_list_new").style.visibility = "visible";
	$("email_list_new").style.display = "block";
	
	$("email_recipients").style.visibility = "hidden";
	$("email_recipients").style.display = "none";
	$("email_recipients_add").style.visibility = "hidden";
	$("email_recipients_add").style.display = "none";

}
	
function save_email_list() {
		if ($("list_name").value == "") {
			alert("Please enter a List Name.");
			return false;
		}
			
		var url = 'http://192.168.20.63/tdw/maint_email_recipients_ajx.php';
		var pars = 'user_id=<?=$user_id?>';
		pars = pars + '&mod_request=ADDNEWLIST';
		pars = pars + '&list_name=' + $("list_name").value;
		var ran_number= Math.random()*5; 
		pars = pars + '&xrand=' + ran_number;

			new Ajax.Request
			(
				url,   
				{     
					method:'get', 
					parameters:pars,    
					onSuccess: 
						function(transport){       
							var response = "";
							response = transport.responseText; 
							//alert(response);
							//$("email_recipients").innerHTML = response;
              window.location.reload(true);
						},     
					onFailure: 
					function(){ $("email_recipients").innerHTML = "Error accessing List on TDW Server"; }
				}
			);	
}	
	
function validateEmail()
{
	if ($("email_1").value != null && $("email_1").value != $("email_2").value)
  {
		alert("Please check the email address you entered.");
		return false;
  } else {
		add_to_email_list();
	}
}

function add_to_email_list()
{
		//alert("here");
		//alert("["+ $("sel_add_user").value +"]");
		//alert("["+ $("email_1").value +"]");
		if ($("sel_add_user").value == "" &&  $("email_1").value == "") {
			alert("Please select or enter Email Address.");
			return false;
		}
		
		if ($("sel_add_user").value != "" ) {
			var add_email = $("sel_add_user").value;
		} else {
			var add_email = $("email_1").value;
		}
	
		if ($("sel_function").value == "") {
			alert("Please select a List to which to add email recipient.");
			return false;		
		}
	
		var url = 'http://192.168.20.63/tdw/maint_email_recipients_ajx.php';
		var pars = 'user_id=<?=$user_id?>';
		pars = pars + '&mod_request=ADDTOLIST';
		pars = pars + '&add_email=' + add_email;
		pars = pars + '&listtype=' + $("sel_function").value;
		var ran_number= Math.random()*5; 
		pars = pars + '&xrand=' + ran_number;

		//alert(pars);
		//return false;
			
			new Ajax.Request
			(
				url,   
				{     
					method:'get', 
					parameters:pars,    
					onSuccess: 
						function(transport){       
							var response = "";
							response = transport.responseText; 
							//alert(response);
							$("email_recipients").innerHTML = response;
						},     
					onFailure: 
					function(){ $("email_recipients").innerHTML = "Error accessing List on TDW Server"; }
				}
			);	
}


function get_email_list()
{

		if ($("sel_function").value == "") {
			alert("Please select a value from the dropdown");
			return false;
		}
	
		var url = 'http://192.168.20.63/tdw/maint_email_recipients_ajx.php';
		var pars = 'user_id=<?=$user_id?>';
		pars = pars + '&mod_request=POPLIST';
		pars = pars + '&listtype=' + $("sel_function").value;
		var ran_number= Math.random()*5; 
		pars = pars + '&xrand=' + ran_number;

		//alert(pars);
		//return false;
			
			new Ajax.Request
			(
				url,   
				{     
					method:'get', 
					parameters:pars,    
					onSuccess: 
						function(transport){       
							var response = "";
							response = transport.responseText; 
							//alert(response);
							$("email_recipients").innerHTML = response;
							$("email_recipients_add").style.visibility = "visible";
							$("email_recipients_add").style.display = "block";
						},     
					onFailure: 
					function(){ $("email_recipients").innerHTML = "Error accessing List on TDW Server"; }
				}
			);	
}

function del_item(auto_id)
{

	 	//alert(auto_id);
	
		var url = 'http://192.168.20.63/tdw/maint_email_recipients_ajx.php';
		var pars = 'user_id=<?=$user_id?>';
		pars = pars + '&mod_request=DELITEM';
		pars = pars + '&listtype=' + $("curlist").value;
		pars = pars + '&auto_id=' + auto_id;
		var ran_number= Math.random()*5; 
		pars = pars + '&xrand=' + ran_number;

		//alert(pars);
		//return false;
			new Ajax.Request
			(
				url,   
				{     
					method:'get', 
					parameters:pars,    
					onSuccess: 
						function(transport){       
							var response = "";
							response = transport.responseText; 
							//alert(response);
							$("email_recipients").innerHTML = response;
						},     
					onFailure: 
					function(){ $("email_recipients").innerHTML = "Error accessing List on TDW Server"; }
				}
			);	
}

</script>

<form id="sel_list" name="sel_list" action="#" method="post">
<a class="ilt">Select TDW Function</a>: 
<div id="sel_function_area">
<?
$qry = "select
								email_feature_name, email_feature_code
							FROM email_recipient_feature
							WHERE
							  email_feature_is_active = 1
							  ORDER BY email_feature_name";
			$result = mysql_query($qry) or die(tdw_mysql_error($qry));
			$count_row = 1;
			echo '<select id="sel_function" name="sel_function">
							<option value="">Select from List</option>';
			while ($row = mysql_fetch_array($result)) {
				echo '<option value="'.$row["email_feature_code"].'">'.$row["email_feature_name"].'</option>'; //$count_row . ".&nbsp;&nbsp;&nbsp;" . $row["email_recipient_address"] . '&nbsp;&nbsp;&nbsp;<a href="#"><img src="images/themes/standard/delete.gif" border="0" onclick="del_item('.$row["auto_id"].');"></a><br><br>';
				$count_row++;
			}
			echo '</select>&nbsp;&nbsp;
<input type="button" name="getlist" id="getlist" onclick="get_email_list()" value="Get Email List" /></div>';
?>
</form>
<br />
<a class="ilt">OR:</a> <input type="button" name="addtolist" id="addtolist" onclick="add_email_list()" value="Create New Email List" />
<div id="email_list_new" style="visibility:hidden; display:none">
<table>
<tr><td><a class="ilt">List Name:</a></td><td><input type="text" name="list_name" id="list_name" value="" style="width:250px" /></td></tr>
<tr><td align="center" colspan="2"><input type="button" name="savetolist" id="savetolist" onclick="save_email_list()" value="Save New Email List" /></td></tr>
</table> 
</div>

<div id="email_recipients"></div>
<div id="email_recipients_add" style="visibility:hidden; display:none">
<br />
<a class="ilt">Select Employee</a><select id="sel_add_user" name="sel_add_user">
<option value="">Select Employee</option>
<?
$qry = "select
					ID, Fullname, Email
				FROM users
				WHERE
					user_isactive = 1 
				ORDER BY Fullname";
$result = mysql_query($qry) or die(tdw_mysql_error($qry));
while ($row = mysql_fetch_array($result)) {
	echo "<option value='".$row["Email"]."'>".$row["Fullname"]."</option>";
}
?>
</select><br /><br />&nbsp;&nbsp;&nbsp; <a class="ilt">OR</a><br /><br />
<a class="ilt">Enter Email Address:</a><input type="text" name="email_1" id="email_1" value="" style="width:250px" /><br />
<a class="ilt">Confirm Email:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a><input type="text" name="email_2" id="email_2" value="" style="width:250px"/>
<br />
<input type="button" id="btn_add_email" name="btn_add_email" value="ADD" onclick="validateEmail();" />
</div>
<? 
//EEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEE
tep(); 
?>
<?
include('inc_footer.php');
?>