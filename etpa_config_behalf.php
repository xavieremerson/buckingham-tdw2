<script src="includes/prototype/prototype.js" type="text/javascript"></script>
<script language ="Javascript">
<!--
function process_data()
{
	var url = 'http://192.168.20.63/tdw/etpa_config_behalf_ajx.php';
	var pars = 'user_id=<?=$user_id?>';
  pars = pars + '&mod_request=save_parent_child';
  pars = pars + '&'+ $("userSelected").serialize(true);
  pars = pars + '&'+ $("selectParent").serialize(true);

  var ran_number= Math.random()*5; 
	pars = pars + '&xrand=' + ran_number;
	//alert(pars);
	//return false;

    //showdebug(pars);
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
						$("populate_selections").innerHTML = response;            
						move(document.frm_select_users.selectUser, document.frm_select_users.userSelected);
          },     
        onFailure: 
        	function(){ 
						$("testtest").innerHTML = "Communication Error! Please report with Code TDW-1503";
					}
      }
    );

}

function get_data()
{
	var url = 'http://192.168.20.63/tdw/etpa_config_behalf_ajx.php';
	var pars = 'user_id=<?=$user_id?>';
  pars = pars + '&mod_request=get_parent_child';
  pars = pars + '&'+ $("selectParent").serialize(true);

  var ran_number= Math.random()*5; 
	pars = pars + '&xrand=' + ran_number;
	//alert(pars);
	//return false;

    //showdebug(pars);
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
						$("populate_selections").innerHTML = response;            
						move(document.frm_select_users.selectUser, document.frm_select_users.userSelected);
          },     
        onFailure: 
        	function(){ 
						$("testtest").innerHTML = "Communication Error! Please report with Code TDW-1503";
					}
      }
    );

}

function reportError(request)
{
	$("testtest").innerHTML = "Communication Error! Please report with Code TDW-1503";
}

-->
</script>
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
tsp(100, "Trade Preapproval Employees entering request on behalf of other employees.");
?>

				<br />&nbsp;&nbsp;<a class="ilt">Select Employee</a>
				<form action="<?=$PHP_SELF?>" method="post" enctype="multipart/form-data" name="frm_select_users">
  			<table>
        	<tr>
          	<td>
              <select class="Text" name="selectParent" size="1" style="width: 200" onchange="get_data()"> <!-- onDblClick="move(selectUser, userSelected)"-->
              <option value="">Select Employee</option>
							<?
                     $qry_result = "select `ID`, `Fullname` from users where `user_isactive` = 1 and `is_login_acct`= 1 order by Firstname";
                     $result = mysql_query($qry_result);
                     while ( $row = mysql_fetch_array($result) ) 
                      {
                        echo '<option value="' . $row["ID"] . '">' . $row["Fullname"] . '</option>'."\n";
                      }
               ?>							
              </select>
            </td>
          </tr>
        </table>
				<div id="populate_selections">
        <br />&nbsp;&nbsp;<a class="ilt">Select Employee getting approvals on behalf</a>
        <table>
				<tr> 
					<td>
					<select class="Text" name="selectUser" multiple size="8" style="width: 200" onDblClick="move(selectUser, userSelected)">
					<?
								 $qry_result = "select `ID`, `Fullname` from users where `user_isactive` = 1 and `is_login_acct`= 1 order by Firstname";
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
					<select class="Text" name="userSelected" id="userSelected" multiple size="8" style="width: 200" onDblCLick="move(userSelected,  selectUser)">
					</select>
					</td>
  			</tr>
				<tr>
				<td align="left" class="csys_regtext">&nbsp;</td>
				<td colspan="2" align="left"><input class="Submit" name="Save" type="button" value="Save" onclick="javascript:select_all(userSelected);process_data()"></td>
				</tr>
			</table>
			<br />
      <hr size="1" noshade color="#0099FF" />
      &nbsp;&nbsp;<a class="ilt">Employee and People who can enter preapprovals on behalf of the employee.</a>
      <hr size="1" noshade color="#0099FF" />
      <table class="ilt">
<?
			//==============================================================================================
			$qry_show = "select a.etpa_parent_id, a.etpa_child_id, b.Fullname as parent_name, c.Fullname as child_name from etpa_on_behalf a
									 left join users b on a.etpa_parent_id = b.ID 
									 left join users c on a.etpa_child_id = c.ID
									 order by b.Fullname, c.Fullname"; 
			$result_show = mysql_query($qry_show);
			$cnt = 1;
			$old_val = "xxx";
			
			while ( $row = mysql_fetch_array($result_show) ) 
			{
				//$arr_data[$row["etpa_parent_id"]."##".$row["etpa_child_id"]] = $row["parent_name"]."##".$row["child_name"];
				if ($row["parent_name"] == $old_val) {
					?>
					<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;&nbsp;&nbsp;<?=$row["child_name"]?></td></tr>
					<?
					$old_val = $row["parent_name"];
				} else {
					?>
					<tr><td><?=$cnt?>.&nbsp;&nbsp;</td><td><?=$row["parent_name"]?></td><td>&nbsp;&nbsp;&nbsp;<?=$row["child_name"]?></td></tr>
					<?
					$old_val = $row["parent_name"];
					$cnt = $cnt + 1;
				}
				
			}
			echo "</table>";
			//show_array($arr_data);
			//==============================================================================================
?>
      </div>
			</form>
<?

tep();
?>