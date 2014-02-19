<?
include('inc_header.php');
?>

<script type="text/javascript" language="javascript" src="includes/prototype/prototype.js"></script>
<script type="text/javascript">

function divsh () {
	if ( $("sh").innerHTML == 'Show Data Entry Section') {  
			$("divde").style.display = 'block';
			$("divde").style.visibility = 'visible';
			$("sh").innerHTML = 'Hide Data Entry Section';
	} else {
			$("divde").style.display = 'none';
			$("divde").style.visibility = 'hidden';
			$("sh").innerHTML = 'Show Data Entry Section';
	}
}

function export_excel() {
// Change "_blank" to something like "newWindow" to load all links in the same new window

			$("divde").style.display = 'none';
			$("divde").style.visibility = 'hidden';
			$("sh").innerHTML = 'Show Data Entry Section';
	

	var url = 'http://192.168.20.63/tdw/mod_exp_excel.php';
	var pars = 'user_id=<?=$user_id?>';
	var mytime= '&ms='+new Date().getTime();
	pars = pars + mytime;
  var ran_number= Math.random()*5; 
	pars = pars + '&xrand=' + ran_number;
	pars = pars + '&sel_month='+ $("sel_month").value;
/*	pars = pars + '&datefrom='+ $("iddatefrom").value;
	pars = pars + '&dateto='+ $("iddateto").value;
*/	
  var newurl = url + '?' + pars;
  //alert(newurl);
	//return false;

	var newWindow = window.open(newurl, '_blank');
	newWindow.focus();
	return false;
}


//add a new row to the table
function addRow()
{
//add a row to the rows collection and get a reference to the newly added row
var newRow = $("tblGrid").insertRow();

//add 3 cells (<td>) to the new row and set the innerHTML to contain text boxes
var oCell = newRow.insertCell();
oCell.style.verticalAlign="top";
oCell.innerHTML = '<a href="#" onclick="removeRow(this);" class="thisdoclink">Remove</a>'; 

var oCell = newRow.insertCell();
oCell.innerHTML = '<table><tr>'+
'<td valign="top">&nbsp;</td>'+
'<td valign="top"><input type="Text1" size="22" value="" name="exp_pfname[]"/></td>'+
'<td valign="top"><input type="Text1" size="22" value="" name="exp_plname[]"/></td>'+
'<td valign="top"><textarea rows="2"  cols="80" name="exp_pnote[]"></textarea></td>'+
'</tr></table>';
}
   
//deletes the specified row from the table
function removeRow(src)
{
	/* src refers to the input button that was clicked to get a reference to the containing <tr> element, get the parent of the parent (in this case <tr>)*/   
	var oRow = src.parentElement.parentElement;  
	//once the row reference is obtained, delete it passing in its rowIndex   
	$("tblGrid").deleteRow(oRow.rowIndex);  
}

function process_exp()  //get requests of pre-approval from users.
{
	var sform = $("exp_add").serialize();
	var url = 'http://192.168.20.63/tdw/mod_exp_insert.php';
	var pars = 'user_id=<?=$user_id?>';
	pars = pars + '&'+ sform;
  
	var mytime= '&ms='+new Date().getTime();
	pars = pars + mytime;
  var ran_number= Math.random()*5; 
	pars = pars + '&xrand=' + ran_number;
    
  //alert(pars);
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
						var responseLenght = response.length;
						 
							if (responseLenght > 0) {
								$("zerr").innerHTML = response;
							} else {
								$("if_status").src= 'mod_exp_data_process.php?user_id=<?=$user_id?>';
						    $("exp_add").reset();
								$("zerr").innerHTML = "";
							}
						//alert("Lenght of response is " + responseLenght);     
            //alert(response);
						//$("exp_add").reset();
          },     
        onFailure: 
        function(){ 
										alert('Unexpected error. Technical Support has been notified.') 
            				$("zerr").innerHTML = 'Unexpected error. Technical Support has been notified.';
									}
      }
    );
}

function get_expenses_for_month() {
// Change "_blank" to something like "newWindow" to load all links in the same new window

			$("divde").style.display = 'none';
			$("divde").style.visibility = 'hidden';
			$("sh").innerHTML = 'Show Data Entry Section';

	var url = 'mod_exp_data_process.php';
	var pars = 'user_id=<?=$user_id?>';
	var mytime= '&ms='+new Date().getTime();
	pars = pars + mytime;
  var ran_number= Math.random()*5; 
	pars = pars + '&xrand=' + ran_number;
	pars = pars + '&sel_month='+ $("sel_month").value;

  var newurl = url + '?' + pars;
	//alert(newurl);
	$("if_status").src= newurl;
	return false;
}


</script>
	
<!-- START TABLE 1 -->
<style type="text/css">
<!--
.gilt {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
	color: #006600;
	text-align: left;
	background-color: #99FF99;
	border: 1px solid #006600;
	padding-top: 3px;
	padding-right: 6px;
	padding-bottom: 3px;
	padding-left: 6px;
	text-decoration: none;
}
.gilt:hover {
	text-decoration: underline;
}
.oilt {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
	color: #FF6600;
	text-align: left;
	background-color: #FFF0E1;
	border: 1px solid #FFCC00;
	padding-top: 3px;
	padding-right: 6px;
	padding-bottom: 3px;
	padding-left: 6px;
	text-decoration: none;
}
.oilt:hover {
	text-decoration: underline;
}
.htr {	font-family: Arial, Helvetica, sans-serif;	font-size: 12px;	font-weight: bold;	color: #003399;	background-color: #eeeeee;}
.rilt {	font-family: Arial, Helvetica, sans-serif;	font-size: 11px;	font-weight: bold;	color: #003399;	text-align: right; }
.lilt {	font-family: Arial, Helvetica, sans-serif;	font-size: 11px;	font-weight: bold;	color: #003399;	text-align: left; }
.thisdoclink {	font-family: Arial, Helvetica, sans-serif;	font-size: 11px;	font-weight: bold;	color: #FF6600;	text-align: left;	text-decoration: underline;}

-->
</style>

<table width="100%" height="100%" border="0" cellspacing="1" cellpadding="0">
  <tr> 
    <td valign="top">
<? 
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%    
tsp(100, "Expense Reporting");
?>

<table width="100%" cellpadding="0" cellspacing="0">
  <form name="exp_criteria" id="exp_criteria" action="" method="post">
    <tr>
      <td width="110" align="center" nowrap="nowrap" class="rilt">&nbsp;Expense Period:&nbsp;</td>
			<td width="5">&nbsp;</td>
      <td width="100">																
      <select class="Text1" name="sel_month" id="sel_month" size="1" >
      <option value="<?=date("Y-m-01")?>"><?=date("F Y")?></option>
      <?
      $now=strtotime(date("d F Y"));
			for ($i=0;$i<12;$i++) {
				$now=$now-(60*60*24*(365/12));
				echo "<option value=\"".date("Y-m-01", $now)."\">" . date('F Y', $now) . "</option>\n";
			}
			?>
      <?
      //echo create_commission_month();
      ?>
      </select>
      </td>
		  <td width="5">&nbsp;</td>
			<SCRIPT LANGUAGE="JavaScript" SRC="includes/calendarpopup/CalendarPopup.js"></SCRIPT>
      <SCRIPT LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
      <SCRIPT LANGUAGE="JavaScript">
				var calexpdate_f = new CalendarPopup("divexpdate_f");
				calexpdate_f.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
				var calexpdate_t = new CalendarPopup("divexpdate_t");
				calexpdate_t.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
      </SCRIPT>						
      <td width="10"><img src="images/lf_v1/form_submit.png" onclick="get_expenses_for_month();" style="cursor:pointer" /></td>
      <td width="10">&nbsp;</td>
      <td width="10" class="rilt">OR&nbsp;&nbsp;</td>
      <td width="10"><img src="images/lf_v1/exp2excel.png" onclick="export_excel();" style="cursor:pointer"/></td>
      <td>&nbsp;</td>
		</tr>
  </form>
</table>
<?
tep();
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%    
?>
<!--<hr size="2" noshade color="#003366" />
<hr size="1" noshade color="#f4f8fb" />-->
<img src="images/spacer.gif" height="2" width="2" border="1" />
<?
tsp(100, "Add item:");
?>
<a id="sh" href="javascript:divsh();" class="gilt">Show Data Entry Section</a><!---->
<div id="divde" style="visibility:hidden;display:none">
<form id="exp_add" name="exp_add" action="mod_exp_post.php" method="post" target="_blank">
<table>
	<tr>
  	<td valign="top">
      <table cellpadding="4">
        <tr>
          <td colspan="2" align="right">  
          	<table>
            	<tr>
								<td class="rilt">From</td>
                <td width="10"><input type="text" id="idexpdate_f" class="Text1" name="exp_date_f" size="12" maxlength="12" value="<?=format_date_ymd_to_mdy(previous_business_day())?>"></td>
                <td><A HREF="#" onClick="calexpdate_f.select(document.forms['exp_add'].exp_date_f,'anchor3','MM/dd/yyyy'); return false;" NAME="anchor3" ID="anchor3"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
								<td class="rilt">To</td>
                <td width="10"><input type="text" id="idexpdate_t" class="Text1" name="exp_date_t" size="12" maxlength="12" value="<?=format_date_ymd_to_mdy(previous_business_day())?>"></td>
                <td><A HREF="#" onClick="calexpdate_t.select(document.forms['exp_add'].exp_date_t,'anchor4','MM/dd/yyyy'); return false;" NAME="anchor4" ID="anchor4"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
          		</tr>
            </table>
          </td>
			 </tr>
      <tr>  
        <td class="rilt" colspan="2" align="right">Division&nbsp;&nbsp;<select name="sel_exp_division" id="sel_exp_division" style="width:140px;">
          <option value="BRG">B R G</option>
          <option value="BCM">B C M</option>
          <option value="BUCK">B U C K</option>
          </select></td>
      </tr>
       <tr>  
          <td class="rilt" colspan="2" align="right">Expense Type&nbsp;&nbsp;<select name="sel_exp_type" id="sel_exp_type" style="width:140px;">
            <option value="Client">Client</option>
            <option value="Prospect">Prospect</option>
            <option value="Non-client">Non-Client</option>
            </select></td>
        </tr> 
        <tr>  
          <td class="rilt">Client / Prospect&nbsp;
                <script language="javascript" type="text/javascript" src="includes/actb/actb.js"></script>
                <script language="javascript" type="text/javascript" src="includes/actb/common.js"></script>
                <script>
                var clientarray=new Array(
                  <?
                  $query_sel_client = "SELECT comm_advisor_code, trim(comm_advisor_name) as comm_advisor_name 
                                        FROM lkup_clients
                                        ORDER BY comm_advisor_name, comm_advisor_code";
                  $result_sel_client = mysql_query($query_sel_client) or die(mysql_error());
                  ?>																
                  <?
                  $count_row_client = 0;
                  while($row_sel_client = mysql_fetch_array($result_sel_client))
                  {
                    if ($row_sel_client["comm_advisor_name"] == '') {
                    $display_val_client = $row_sel_client["comm_advisor_code"];
                    } else {
                    $display_val_client = str_replace("'","\\'",$row_sel_client["comm_advisor_name"]);
                    }
                    echo "'". $display_val_client . "  [" .$row_sel_client["comm_advisor_code"]."]',"; //."\n"
                  }
                  ?>
                  '');
                </script>
                <input type='text' name="val_client" style='font-family:verdana;width:180px;font-size:12px;' id='tb' value=''/> 
								<script>
                  obj = new actb(document.getElementById('tb'),clientarray);
                </script>
            </td>
        </tr>
       <tr>  
          <td class="rilt" colspan="2">If publicly traded, enter symbol &nbsp;&nbsp;<input type="Text1" size="12" value="" name="exp_symbol"/></td>
       </tr>
      </table> 
    </td>
  	<td valign="top">
      <table cellpadding="4">
        <tr>
          <td class="lilt">Description: <br />
          <textarea name="exp_desc" rows="3" cols="90" style="font:Verdana; font-size:13px; color:#000066"></textarea></td>
        </tr>
      </table> 
      <fieldset>
      <table cellpadding="2">
        <tr>
          <td class="rilt">Hotel</td>
          <td><input type="Text1" size="12" value="" name="exp_hotel"/></td>
          <td class="rilt">Meals</td>
          <td><input type="Text1" size="12" value="" name="exp_meals"/></td>
          <td class="rilt">Phone</td>
          <td><input type="Text1" size="12" value="" name="exp_phone"/></td>
          <td class="rilt">Entertainment</td>
          <td><input type="Text1" size="12" value="" name="exp_entertainment"/></td>
          <td class="rilt">Misc.</td>
          <td><input type="Text1" size="12" value="" name="exp_misc"/></td>
          <td class="rilt"></td>
          <td></td>
        </tr>
      </table>
      </fieldset>
      <fieldset>
      <legend class="lilt">Travel Section</legend>
      <table>
        <tr>
          <td class="rilt">Air</td>
          <td><input type="Text1" size="12" value="" name="exp_air"/></td>
          <td class="rilt">Train</td>
          <td><input type="Text1" size="12" value="" name="exp_train"/></td>
          <td class="rilt">Cab</td>
          <td><input type="Text1" size="12" value="" name="exp_cab"/></td>
          <td class="rilt">Rental</td>
          <td><input type="Text1" size="12" value="" name="exp_rental"/></td>
          <?
					$mile_rate = db_single_val("select exp_lookup_val as single_val from exp_expense_lookup_vals where exp_lookup_name = 'MILEAGE' and exp_isactive = 1");
					?>          
          <td class="rilt">Mileage [@ <?=$mile_rate?>]</td>
          <td><input type="Text1" size="12" value="" name="exp_mileage"/></td>
          <td class="rilt">Other</td>
          <td><input type="Text1" size="12" value="" name="exp_other"/></td>
        </tr>
			</table>
      </fieldset>
      <input type="checkbox" class="checkbox" style="size:30px" name="have_receipt" id="have_receipt" value="1"/> <a class="lilt">Check here, if you receipts for ALL items.</a>
    </td> 
  </tr> 
</table>

<table border="0">
	<tr>
  	 <td>
     		<table><tr>
     			<td class="ilt" colspan="5">Person(s)/Contact(s) [<a href="#" class="thisdoclink" onclick="addRow();">Add Another Person</a>]</td>
     		</tr></table>
     </td>
  </tr>
	<tr>
  	 <td>
     		<table>
        	<tr>
            <td width="55" valign="top" class="lilt">&nbsp;</td>
            <td width="133" valign="top" class="lilt">First Name</td>
            <td width="133" valign="top" class="lilt">Last Name</td>
            <td width="80" valign="top" class="lilt">Note</td>
     			</tr>
        </table>
     </td>
  </tr>
</table>
<table border="0" id="tblGrid">
  <tr>
  	 <td valign="top"><a href="#" onclick="removeRow(this);" class="thisdoclink">Remove</a> </td>
     <td>
     		<table><tr>
          <td valign="top">&nbsp;</td>
          <td valign="top"><input type="Text1" size="22" value="" name="exp_pfname[]"/></td>
          <td valign="top"><input type="Text1" size="22" value="" name="exp_plname[]"/></td>
          <td valign="top"><textarea rows="2"  cols="80" name="exp_pnote[]"></textarea></td>
     		</tr></table>
     </td>
  </tr>
</table>  
<input type="button" class="submit" name="test" value="&nbsp;&nbsp;&nbsp;&nbsp;Save&nbsp;&nbsp;&nbsp;&nbsp;" onclick="process_exp();" />
</form>
</div>
<div id="zerr" style="font:Verdana; color:#FF0000; font-size:12px; font-weight:bold; padding:4px"></div>
<?
tep();
?>
<hr size="1" noshade color="#f4f8fb" />
<table width="100%"><!-- style="visibility:hidden; display=none"-->
<tr>
	<td> 
	<iframe name="if_status" id="if_status" src="mod_exp_data_process.php?user_id=<?=$user_id?>" height="800" width="100%" marginwidth="0" marginheight="0" scrolling="yes" frameborder="0"></iframe>
	</td>
</tr>
</table>
<?php
include('inc_footer.php'); 
?>
<DIV ID="divexpdate_f" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
<DIV ID="divexpdate_t" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>