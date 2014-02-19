<script language="JavaScript" src="includes/js/popup.js"></script>
<script language ="Javascript">
function setFocus(nextid) {
  document.getElementById(nextid).focus();
}

function bar(evt, nextid){
var k=evt.keyCode||evt.which;
 if (k==13 && nextid != "") {
   setFocus(nextid);
 }
return k!=13;
}

function getFormValues(){
params_val = "vdate=" + "xxx" + "&";
params_val = params_val + "vamount=" + document.de_comm.vamount.value + "&";
params_val = params_val + "vrep=" + document.de_comm.vrep.value + "&";
params_val = params_val + "vcomment=" + document.de_comm.vcomment.value + "&";
params_val = params_val + "venteredby=" + document.de_comm.venteredby.value;

	if (document.de_comm.vamount.value == '' || document.de_comm.vrep.value == '') {
		alert("Amount and Rep./Trader are required fields. Please select/enter appropriate values and then proceed!");
		return false;
	} else {
		showDetail(params_val);	
	}
//showDetail(params_val);
}

function showDetail(str)
{ 
	var url = "<?=$_site_url?>payout_draw_process.php" + "?" +  str;
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

	setFocus('vrep');
	//document.de_comm.vdate.value = ""
	document.de_comm.vamount.value = ""
	document.de_comm.vrep.value = ""
	document.de_comm.vcomment.value = ""
} 
</script>


<style type="text/css">
<!--
.txt_status {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10px;
	color: #0000FF;
}
-->
</style>
<body onLoad="setFocus('vrep'); showDetail('');">
<?
tsp(100, "Monthly Draw Data Entry");
?>
&nbsp;&nbsp;
<form name="de_comm" id="de_comm">
<table width="500" height="100%" border="0" cellspacing="0" cellpadding="5">
  <tr>
    <td class="ilt">&nbsp;&nbsp;&nbsp;Select Sales Rep. / Trader</td>
    <td>
        <select class="Text2" name="vrep" size="1" onKeyPress="return bar(event, 'vamount')">
        <option value="">&nbsp;Sales Rep. / Trader &nbsp;</option>
        <option value="">____________</option>
        <?
        //get reps from query  on table mry_comm_rr_trades and join on users
        
        //*************************************************************************
        //This query with join on mry_comm_rr_trades was taking too long, altered
        //to just show reps.
        //*************************************************************************
        
        $qry_get_reps = "SELECT
                          ID, rr_num, concat(Firstname, ' ', Lastname ) as rep_name, rr_num as trad_rr 
                          from users
                        WHERE rr_num like '0%'
                        AND Role > 2
                        AND Role < 5
												AND user_isactive = 1
                        ORDER BY Firstname";
        
        $result_get_reps = mysql_query($qry_get_reps) or die (tdw_mysql_error($qry_get_reps));
        while($row_get_reps = mysql_fetch_array($result_get_reps))
        {
        ?>
          <option value="<?=$row_get_reps["ID"]?>"><?=str_pad($row_get_reps["rep_name"], 20, ".")?>(<?=$row_get_reps["rr_num"]?>)</option>
        <?
        }
        ?>
		</td>
  </tr>
  <tr>
    <td class="ilt">&nbsp;&nbsp;&nbsp;Amount</td>
    <td><input class="Text" name="vamount" type="text" size="30" maxlength="30" onKeyPress="return bar(event, 'vcomment')"></td>
  </tr>
  <tr>
    <td class="ilt">&nbsp;&nbsp;&nbsp;Comment</td>
    <td><input class="Text" name="vcomment" type="text" size="30" maxlength="100" onKeyPress="return bar(event, 'Submit')"></td>
  </tr>
  <tr>
    <td class="ilt">&nbsp;&nbsp;&nbsp;Entered By:</td>
    <td class="ilt"><?=$userfullname?></td> 
  </tr>
  <tr>
    <td></td>
    <td><input name="Submit" type="button" onClick="getFormValues()" value="&nbsp;&nbsp;&nbsp;SAVE&nbsp;&nbsp;&nbsp;"></td>
  </tr>
</table>
<input type="hidden" name="venteredby" value="<?=$user_id?>">
</form>
<?
tep();
?>

<table width="100%"><!-- style="visibility:hidden; display=none"-->
<tr>
	<td> 
	<iframe name="if_status" src="payout_draw_process.php" height="500" width="100%" marginwidth="0" marginheight="0" scrolling="yes" frameborder="0"></iframe>
	</td>
</tr>
</table>
<DIV ID="divvdate" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
</body>

