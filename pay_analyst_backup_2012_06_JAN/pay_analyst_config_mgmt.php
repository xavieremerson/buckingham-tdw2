<script language ="Javascript">
<!--
function noenter() {
  return !(window.event && window.event.keyCode == 13); }

////function to move stuff between list boxes
function move (from, to) {
    var fbox = new Array();
    var tbox = new Array();
    var lookup = new Array();

    // Copy data from 'to' and 'from' boxes into 'tbox' and 'fbox'
    // arrays; if an item in 'from' is selected, it gets moved into
    // 'tbox'.'lookup' holds the values of each option.
    for (i=0; i<to.length; i++) {
		    if (to.options[i].value == -1) continue;
        lookup[to.options[i].text] = to.options[i].value;
        tbox[i] = to.options[i].text;
    }
    for (i=0; i<from.length; i++) {
		    if (from.options[i].value == -1) continue;
        lookup[from.options[i].text] = from.options[i].value;
        if (from.options[i].selected)
            tbox[tbox.length] = from.options[i].text;
        else
            fbox[fbox.length] = from.options[i].text;
    }

    // Sort both of the arrays, then fill up the selection boxes with
    // the sorted values.
    fbox.sort();
    tbox.sort();
    from.length = 0;
    to.length = 0;

    if (fbox.length == 0)
        from[0] = new Option('', -1);
    for (i=0; i<fbox.length; i++)
        from[i] = new Option(fbox[i], lookup[fbox[i]]);
    for (i=0; i<tbox.length; i++)
        to[i] = new Option(tbox[i], lookup[tbox[i]]);
}

function select_all (s) {
    for (i=0; i<s.length; i++)
        s.options[i].selected = 1;
		
		//make the name to reflect an array for PHP processing		
		s.name = s.name+"[]";
				
}

function option_compress(box, field) {
    field.value = "test"; 
    for (var i=0; i<box.options.length; i++) { 
        if (i>0) 
            field.value += ","; 
        if (box.options[i].selected) { 
            field.value += box.options[i].value; 
            box.options[i].selected = false; 
        } 
    } 
} 
////function to move stuff between list boxes (END)
-->
</script>
<?
tsp(100, "Analyst Allocations Configuration");
?>
<?
if ($_POST) {

	$result = mysql_query("truncate table pay_analyst_users");
 
	foreach($userSelected as $k=>$v) {
		$result = mysql_query("INSERT INTO pay_analyst_users (auto_id, user_id) VALUES ( NULL , '".$v."')");
  }
}
?>
				<br />&nbsp;&nbsp;<a class="ilt">Sales Reps. for Analyst Allocations</a>
				<form action="<?=$PHP_SELF?>" method="post" enctype="multipart/form-data" name="frm_select_users">
  			<table>
				<tr> 
					<td>
					<select class="Text" name="selectUser" multiple size="24" style="width: 200" onDblClick="move(selectUser, userSelected)">
					<?
								 $qry_result = "select `ID`, `Fullname` from users where `is_login_acct`= 1 and `Role` < 6 order by Firstname"; //`user_isactive` = 1 and
								 $result = mysql_query($qry_result);
								 while ( $row = mysql_fetch_array($result) ) 
									{
								 		echo '<option value="' . $row["ID"] . '">' . $row["Fullname"] . '</option>'."\n";
									}
					 ?>							
					</select>
					</td>
					<td>
					<input class="Submit" onclick="move(selectUser, userSelected)" type="button" value="&nbsp;&nbsp;&gt;&gt;&nbsp;&nbsp;">
					<br>
					<input class="Submit" onclick="move(userSelected,  selectUser)" type="button" value="&nbsp;&nbsp;&lt;&lt;&nbsp;&nbsp;">
					</td>
					<td>
					<select class="Text" name="userSelected" multiple size="24" style="width: 200" onDblCLick="move(userSelected,  selectUser)">
					</select>
					</td>
  			</tr>
				<tr>
				<td align="left" class="csys_regtext">&nbsp;</td>
				<td colspan="2" align="left"><input class="Submit" name="Save" type="submit" value="Save" onclick="javascript:select_all(userSelected);"></td>
				</tr>
			</table>
			</form>
<?
$str_users = "";
$qry_result = "select user_id from pay_analyst_users";
$result = mysql_query($qry_result);
while ( $row = mysql_fetch_array($result) ) 
{
  $str_users .= '"'.$row["user_id"].'",';
}
$str_users = substr($str_users,0,strlen($str_users)-1);
?>
<script language ="Javascript">
<!--
function in_array( what, where ){	var a=false;	for(var i=0;i<where.length;i++){	  if(what == where[i]){	    a=true;        break;	  }	}	return a;}

var sUsers = new Array(<?=$str_users?>);
for (i=0; i<document.frm_select_users.selectUser.options.length; i++) {
	var selectBox = document.frm_select_users.selectUser;
		if (in_array(selectBox.options[i].value, sUsers) == true) {
			selectBox.options[i].selected = 1;
		}
	
	//document.write(selectBox.options[i].value);
}
move(document.frm_select_users.selectUser, document.frm_select_users.userSelected);
-->
</script>
<?
tep();
?>