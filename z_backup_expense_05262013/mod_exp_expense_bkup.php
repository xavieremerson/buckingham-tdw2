<?
include('inc_header.php');
?>

<script language="javascript" src="includes/prototype/prototype.js"></script>
<script type="text/javascript">

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
oCell.innerHTML = '<table><tr>' + 
										'<td valign="top"><input type="Text1" size="26" value="" name="exp_pname[]"/></td>'+
										'<td valign="top"><input type="Text1" size="12" value="" name="exp_pphone[]"/></td>'+
										'<td valign="top"><input type="Text1" size="26" value="" name="exp_pemail[]"/></td>'+
										'<td valign="top"><textarea rows="2" cols="80" name="exp_pnote[]"></textarea></td>'+
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
            //alert(response);
						$("if_status").src= 'mod_exp_data_process.php?user_id=<?=$user_id?>';
            $("zerr").innerHTML = response;
          },     
        onFailure: 
        function(){ alert('Unexpected error. Technical Support has been notified.') }
      }
    );
}

/*function getFormValues(){
params_val = "vdate=" + document.de_comm.vdate.value + "&";
params_val = params_val + "vamount=" + document.de_comm.vamount.value + "&";
params_val = params_val + "vpaymenttype=" + document.de_comm.vpaymenttype.value + "&";
params_val = params_val + "vclient=" + document.de_comm.vclient.value + "&";
params_val = params_val + "vcomment=" + document.de_comm.vcomment.value + "&";
params_val = params_val + "venteredby=" + document.de_comm.venteredby.value;

	if (document.de_comm.vamount.value == '' || document.de_comm.vclient.value == '') {
		alert("Amount and Client are required fields. Please select/enter appropriate values and then proceed!");
		return false;
	} else {
		showDetail(params_val);	
	}
//showDetail(params_val);
}

function showDetail(str)
{ 
	var url = "<?=$_site_url?>pde_payment_entry_process.php" + "?" +  str;
	//alert(url);
  var trid;
	trid = 'if_status'; 
	if (document.getElementById) { // DOM3 = IE5, NS6 Generally this is what it is
			document.getElementById(trid).style.visibility = 'visible'; 
			document.getElementById(trid).style.display = 'block'; 
			document.getElementById(trid).src=url;
			//alert(document.getElementById(trid).src)
	} 
	else { 
		if (document.layers) { // Netscape 4 
			alert("Netscape 4");
			document.AELT.visibility = 'visible'; 
		} 
		else { // IE 4 
			alert("IE 4");
			document.all.AELT.style.visibility = 'visible'; 
		}
	} 

	setFocus('vpaymenttype');
	//document.de_comm.vdate.value = ""
	document.de_comm.vamount.value = ""
	document.de_comm.vclient.value = ""
	document.de_comm.vcomment.value = ""
	document.de_comm.vpaymenttype.value = "0"
} 
*/

</script>
	
<!-- START TABLE 1 -->
<style type="text/css">
<!--
.htr {	font-family: Arial, Helvetica, sans-serif;	font-size: 12px;	font-weight: bold;	color: #003399;	background-color: #eeeeee;}
.rilt {	font-family: Arial, Helvetica, sans-serif;	font-size: 11px;	font-weight: bold;	color: #003399;	text-align: right; }
.lilt {	font-family: Arial, Helvetica, sans-serif;	font-size: 11px;	font-weight: bold;	color: #003399;	text-align: left; }
.thisdoclink {	font-family: Arial, Helvetica, sans-serif;	font-size: 11px;	font-weight: bold;	color: #FF6600;	text-align: left;	text-decoration: underline;}
-->
</style>
<?
if ($x or $val_client or $_POST) { // form submitted //format_date_ymd_to_mdy($trade_date_to_process)
		

			$sel_datefrom = $datefrom;
			$sel_dateto = $dateto;

			//if brokerage month is selected use that info to create dateto and datefrom values
			if($_POST["sel_month"] == '') {

				$sel_datefrom = $datefrom;
				$sel_dateto = $dateto;
				
				$datefrom = format_date_mdy_to_ymd($datefrom);
				$dateto = format_date_mdy_to_ymd($dateto);
				
				$string_heading = "Selection: RR #: ".$show_rep." Client(s): ".$show_client. "&nbsp;&nbsp;&nbsp;&nbsp;Symbol(s): ".$show_symbol. "&nbsp;&nbsp;&nbsp;&nbsp;Date From: ".$_POST["datefrom"]. "&nbsp;&nbsp;&nbsp;&nbsp;Date To: ".$_POST["dateto"];

			} else {

				$sel_datefrom = $datefrom;
				$sel_dateto = $dateto;

				// ^ caused problems, had to escape it				
				$arr_split_input = split("\^", $sel_month);
				$arr_dates = get_commission_month_dates($arr_split_input[0],$arr_split_input[1]);
				$datefrom = $arr_dates[0];
				$dateto = $arr_dates[1];
				
				$string_heading = "Selection: RR #: ".$show_rep." Client(s): ".$show_client. "&nbsp;&nbsp;&nbsp;&nbsp;Symbol(s): ".$show_symbol. "&nbsp;&nbsp;&nbsp;&nbsp;Date From: ".format_date_ymd_to_mdy($datefrom). "&nbsp;&nbsp;&nbsp;&nbsp;Date To: ".format_date_ymd_to_mdy($dateto);
				

			}

} else {

			$sel_datefrom = format_date_ymd_to_mdy(date('Y-m-01'));
			$sel_dateto = format_date_ymd_to_mdy(date('Y-m-d'));

			$string_heading = "";
			$datefrom = $sel_datefrom;
			$dateto = $sel_dateto;
}

?>
<table width="100%" height="100%" border="0" cellspacing="1" cellpadding="0">
  <tr> 
    <td valign="top">
<? 
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%    
tsp(100, "Expense Reporting");
?>

<table width="100%" cellpadding="0" cellspacing="0">
  <form name="clnt_activity" id="idclnt_activity" action="" method="post">
    <tr>
      <td width="100" align="center" nowrap="nowrap" class="ilt">&nbsp;Filter Criteria:&nbsp;</td>
			<td width="5">&nbsp;</td>
      <td width="100">																
      <select class="Text1" name="sel_month" size="1" >
      <option value="">&nbsp;SELECT MONTH&nbsp;&nbsp;</option>
      <option value="">_______________</option>
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
      <td width="120" align="center" nowrap="nowrap" class="rilt">&nbsp;or select Date Range&nbsp;</td>
			<SCRIPT LANGUAGE="JavaScript" SRC="includes/calendarpopup/CalendarPopup.js"></SCRIPT>
      <SCRIPT LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
      <SCRIPT LANGUAGE="JavaScript">
				var calfrom = new CalendarPopup("divfrom");
				calfrom.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
				var calto = new CalendarPopup("divto");
				calto.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
				var calexpdate = new CalendarPopup("divexpdate");
				calexpdate.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
      </SCRIPT>						
      <td width="10">From:</td>
      <td width="10"><input type="text" id="iddatefrom" class="Text1" name="datefrom" size="12" maxlength="12" value="<?=$sel_datefrom?>"></td>
      <td width="20" align="center"><A HREF="#" onClick="calfrom.select(document.forms['clnt_activity'].datefrom,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
      <td width="5">&nbsp;</td>
      <td width="10">To:</td>
      <td width="10"><input type="text" id="iddateto" class="Text1" name="dateto" size="12" maxlength="12" value="<?=$sel_dateto?>"></td>
      <td width="20" align="center"><A HREF="#" onClick="calto.select(document.forms['clnt_activity'].dateto,'anchor2','MM/dd/yyyy'); return false;" NAME="anchor2" ID="anchor2"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
      <td width="10">&nbsp;</td>
      <td width="10"><input type="image" src="images/lf_v1/form_submit.png"></td>
      <td>&nbsp;</td>
		</tr>
  </form>
</table>
<?
tep();
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%    
?>
<!--<hr size="2" noshade color="#003366" />
-->
<br />
<?
tsp(100, "Add item:");
?>
<script type="text/javascript" src="./includes/datepick/js/datepicker.js"></script>
<!--<link href="./includes/datepick/css/demo.css"       rel="stylesheet" type="text/css" />-->
<link href="./includes/datepick/css/datepicker.css" rel="stylesheet" type="text/css" />

<form id="exp_add" name="exp_add" action="mod_exp_post.php" method="post" target="_blank">
<table>
	<tr>
  	<td valign="top">
      <table cellpadding="4">
        <tr>
          <td class="rilt">Date</td>
          <td>
          	<table><tr>
            <td width="10"><input type="text" id="idexpdate" class="Text1" name="exp_date" size="12" maxlength="12" value="<?=format_date_ymd_to_mdy(previous_business_day())?>"></td>
            <td><A HREF="#" onClick="calexpdate.select(document.forms['exp_add'].exp_date,'anchor3','MM/dd/yyyy'); return false;" NAME="anchor3" ID="anchor3"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
          	</tr></table>
          </td>
			 </tr>
        <tr>  
          <td class="rilt">Expense Type</td>
          <td>
            <select name="sel_exp_type" id="sel_exp_type" style="width:140px;">
            <option value="Client">Client</option>
            <option value="Prospect">Prospect</option>
            <option value="Non-client">Non-Client</option>
            </select>
          </td>
        </tr>
        <tr>  
          <td class="rilt">Client / Prospect</td>
            <td width="205">
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
                <input type='text' name="val_client" style='font-family:verdana;width:250px;font-size:12px' id='tb' value=''/> 
								<script>
                  obj = new actb(document.getElementById('tb'),clientarray);
                </script>
            </td>
        </tr>
      </table> 
    </td>
  	<td valign="top">
      <table cellpadding="4">
        <tr>
          <td class="lilt">Description: <br />
          <textarea name="exp_desc" rows="6" cols="60"></textarea></td>
        </tr>
      </table> 
    </td>
  	<td valign="top">
      <table cellpadding="2">
        <tr>
          <td class="rilt">Hotel</td>
          <td><input type="Text1" size="12" value="" name="exp_hotel"/></td>
        </tr>
        <tr>
          <td class="rilt">Transportation</td>
          <td><input type="Text1" size="12" value="" name="exp_transport"/></td>
        </tr>
        <tr>
          <td class="rilt">Fuel</td>
          <td><input type="Text1" size="12" value="" name="exp_fuel"/></td>
        </tr>
        <tr>
          <td class="rilt">Meals</td>
          <td><input type="Text1" size="12" value="" name="exp_meals"/></td>
        </tr>
			</table>
    </td>
    <td valign="top">
      <table cellpadding="2">
        <tr>
          <td class="rilt">Phone</td>
          <td><input type="Text1" size="12" value="" name="exp_phone"/></td>
        </tr>
        <tr>
          <td class="rilt">Entertainment</td>
          <td><input type="Text1" size="12" value="" name="exp_entertainment"/></td>
        </tr>
        <tr>
          <td class="rilt">Misc.</td>
          <td><input type="Text1" size="12" value="" name="exp_misc"/></td>
        </tr>
      </table> 
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
     		<table><tr>
          <td width="50" valign="top" class="lilt">&nbsp;</td>
          <td width="150" valign="top" class="lilt">Name</td>
          <td width="85" valign="top" class="lilt">Phone</td>
          <td width="150" valign="top" class="lilt">Email</td>
          <td width="80" valign="top" class="lilt">Note</td>
     		</tr></table>
     </td>
  </tr>
</table>
<table border="0" id="tblGrid">
  <tr>
  	 <td valign="top"><a href="#" onclick="removeRow(this);" class="thisdoclink">Remove</a> </td>
     <td>
     		<table><tr>
          <td valign="top"><input type="Text1" size="26" value="" name="exp_pname[]"/></td>
          <td valign="top"><input type="Text1" size="12" value="" name="exp_pphone[]"/></td>
          <td valign="top"><input type="Text1" size="26" value="" name="exp_pemail[]"/></td>
          <td valign="top"><textarea rows="2" cols="80" name="exp_pnote[]"></textarea></td>
     		</tr></table>
     </td>
  </tr>
</table>  
<input type="button" name="test" value="Save" onclick="process_exp();" />
</form>
<div id="zerr"></div>
<?
tep();
?>
<!--<hr size="2" noshade color="#003366" />
--><table width="100%"><!-- style="visibility:hidden; display=none"-->
<tr>
	<td> 
	<iframe name="if_status" id="if_status" src="mod_exp_data_process.php?user_id=<?=$user_id?>" height="800" width="100%" marginwidth="0" marginheight="0" scrolling="yes" frameborder="0"></iframe>
	</td>
</tr>
</table>
<?php
include('inc_footer.php'); 
?>
<DIV ID="divfrom" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
<DIV ID="divto" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>			
<DIV ID="divexpdate" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>