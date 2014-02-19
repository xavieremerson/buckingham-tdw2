<?
include('inc_header.php');
?>
<script src="includes/prototype/prototype.js" type="text/javascript"></script>
<link href="main_empl.css" rel="stylesheet" type="text/css">
<script language="javascript">
<?
include('main_empl.js.php');
?>

//This will bring the user back to the Main Page if the Trade Entry window has not been used for 15 seconds
function startExec_nouse() {
  new PeriodicalExecuter(nouse_trade_entry, 15);
}

function nouse_trade_entry()
{
	if ($("symbol").value == '')  {
		window.location="http://192.168.20.63/etpa/main_empl.php"; 
	}
}

startExec_nouse();

function sh(id) {  //show hint
	switch (id) {
		case 1: $("hint_symbol").innerHTML =	"<font color='blue'><strong>If OPTION, Enter underlying SYMBOL</strong></font>"; 	break;
		case 2: $("hint_buysell").innerHTML = "Possible Values are B, S, C, SS"; break;
		case 3: $("instruct").innerHTML = "Enter Quantity"; break;
		case 4: $("instruct").innerHTML = "Enter Symbol"; break;
		case 5: $("instruct").innerHTML = "Enter Symbol"; break;
		default: $("instruct").innerHTML = "Blank Message";
	}
}

function hh(id) {  //show hint
		//alert($("buysell").value);
	switch (id) {
		case 1: 
		$("hint_symbol").innerHTML =	""; break;
		case 2: 
		if(
				$("buysell").value != 'b' && 
				$("buysell").value != 'c' && 
				$("buysell").value != 'ss' && 
				$("buysell").value != 's' && 
				$("buysell").value != 'B' && 
				$("buysell").value != 'S' && 
				$("buysell").value != 'C' && 
				$("buysell").value != 'SS' &&
				$("buysell").value != ''
			) {
		$("hint_buysell").innerHTML = "<font color=red><b>INVALID VALUE!</b></font>";
		break;
		} else {
		$("hint_buysell").innerHTML = ""; 
		break;
		}
		default: $("hint_symbol").innerHTML = "";
	}
}

function get_company_name() {

	if ($("symbol").value.length == 0) {
		//alert("no symbol");
		return false;
	}
	var url = 'http://192.168.20.63/etpa/main_empl_ajx.php';
	var pars = 'user_id=<?=$user_id?>';
  pars = pars + '&mod_request=cname';
  pars = pars + '&instr='+ $("instr").value;
  pars = pars + '&symbol='+ $("symbol").value;

  new Ajax.Request
	(
		url,   
		{     
			method:'get', 
			parameters:pars,    
			onSuccess: 
				function(transport){       
					var response = transport.responseText;       
          $("valid_company").innerHTML = response;
					check_restricted();
					check_client_order();
				},     
			onFailure: 
			function(){ $("err_notify").innerHTML = "Error accessing Company Data using Symbol"; }
		}
	);
}

function check_restricted() {

	if ($("symbol").value.length == 0) {
		//alert("no symbol");
		return false;
	}
	var url = 'http://192.168.20.63/etpa/main_empl_ajx.php';
	var pars = 'user_id=<?=$user_id?>';
  pars = pars + '&mod_request=isrestricted';
  pars = pars + '&symbol='+ $("symbol").value;
	
  new Ajax.Request
	(
		url,   
		{     
			method:'get', 
			parameters:pars,    
			onSuccess: 
				function(transport){       
					var response = transport.responseText;       
					//alert("["+response+"]");
          if (response == "1") {
					$("action_submit").disabled=true;
					//$("action_save").disabled=true;
					$("valid_company").innerHTML = $("valid_company").innerHTML + "<br>" + "<font color=red>Company on Restricted List. Submission disabled</font>";
					//alert($("valid_company").innerHTML + "<br>" + "<font color=red>Company on Restricted List. Submission disabled</font>")
					} else {
					$("action_submit").disabled=false;
					//$("action_save").disabled=false;
					}
				},     
			onFailure: 
			function(){ $("err_notify").innerHTML = "Error accessing Company Data using Symbol"; }
		}
	);
}

function check_client_order() {

	if ($("symbol").value.length == 0) {
		//alert("no symbol");
		return false;
	}
	var url = 'http://192.168.20.63/etpa/main_empl_ajx.php';
	var pars = 'user_id=<?=$user_id?>';
  pars = pars + '&mod_request=isclientorder';
  pars = pars + '&symbol='+ $("symbol").value;
	
  new Ajax.Request
	(
		url,   
		{     
			method:'get', 
			parameters:pars,    
			onSuccess: 
				function(transport){       
					var response = transport.responseText;       
					//alert("["+response+"]");
		  if (response == "1") {
					$("action_submit").disabled=true;
					//$("action_save").disabled=true;
					$("valid_company").innerHTML = $("valid_company").innerHTML + "<br>" + "<font color=red>This trade cannot be approved at this time.<br>Please contact Trading if neccesary.</font>";
					//alert($("valid_company").innerHTML + "<br>" + "<font color=red>Company on Restricted List. Submission disabled</font>")
					} else {
								//$("action_submit").disabled=false;
								var dummy = false;
					//$("action_save").disabled=false;
					}
				},     
			onFailure: 
			function(){ $("err_notify").innerHTML = "Error accessing Client Orders using Symbol"; }
		}
	);
}

function zconfirmSubmit()
{
		if (confirm("Are you sure you wish to submit your request for preapproval?")) {
			//alert("OK clicked");
			return true;
		} else {
			//alert("Cancel clicked");
			return false;
		}
}

function check_holding_period() {

	//var test = "<br><font color=red>Potential Holding Period violation.</font>";
  //alert( test.replace(/<br><font color=red>Potential Holding Period violation.<\/font>/gi,"") ); 
	//return false;
	
	if ($("instr").value != "EQ") {
		return false;
	}

	var url = 'http://192.168.20.63/etpa/all_holding_period_ajx.php';
	var pars = 'user_id=<?=$user_id?>';
  pars = pars + '&mod_request=hperiod';
  pars = pars + '&symbol='+ $("symbol").value;
  pars = pars + '&quantity='+ $("qty").value;
  pars = pars + '&buysell='+ $("buysell").value;

  new Ajax.Request
	(
		url,   
		{     
			method:'get', 
			parameters:pars,    
			onSuccess: 
				function(transport){       
					var response = transport.responseText;       
					//alert("["+response+"]");
		  if (response != "0") {
							$("action_submit").disabled=true;
							$("submit_override").style.visibility="visible";
							$("submit_override").style.display="inline";
							var strMsg = $("valid_company").innerHTML;
							$("valid_company").innerHTML = strMsg + response;
							$("override_type").value = "Potential Holding Period situation."; 
					} else {
								//$("override_type").value = ""; 
							//$("action_submit").disabled=false;
							var dummy = false;
					}
				},     
			onFailure: 
			function(){ $("err_notify").innerHTML = "Unable to perform Holding Period check. ERR: HPV-0413"; }
		}
	);
}

</script>

							<?
							if($_POST) {
									if (trim($symbol) != "" AND trim($buysell) != "" AND trim($qty) != "") {
									
									
											$str_sp_instructions = "";
											if ($submit_override) {
												if ($spinstructions != "") {
													$str_sp_instructions = $override_type ."^". $spinstructions;
												} else {
													$str_sp_instructions = $override_type;
												}
											} else {
												$str_sp_instructions = $spinstructions;
											}
									
											if ($submit == 'Save') {
												$etpa_is_saved = 1;
												$etpa_is_submitted = 1;  //changed to 1 from zero until further action
												$saved = "saved.";
											} else {
												$etpa_is_saved = 1;
												$etpa_is_submitted = 1;
												$saved = "submitted for preapproval.";
											}
											
											$qry="INSERT INTO etpa_request 
														( auto_id , 				 etpa_requestor , 		etpa_instrument , 		etpa_side , 
															etpa_symbol , 		 etpa_qty , 					etpa_order_type , 		etpa_sp_instruction , 
															etpa_limit_price , etpa_entry_time , 		etpa_request_time , 	etpa_is_saved , 
															etpa_is_submitted , etpa_is_approved , 	etpa_approver , 			etpa_approver_comment , 
															etpa_is_routed , etpa_account_at,
															
															etpa_isactive ) 
														VALUES (
														NULL , 
														'".$user_id."', 
														'".$instr."', 
														'".strtoupper(trim($buysell))."', 
														'".strtoupper(trim($symbol))."', 
														'".number_format($qty,2,".","")."', 
														'".$ordtype."', 
														'".$str_sp_instructions."', 
														'".number_format($lprice,2,".","")."', 
														NOW(), 
														NOW(), 
														'".$etpa_is_saved."', 
														'".$etpa_is_submitted."', 
														'0', 
														NULL , 
														NULL , 
														'0', 
														'".$acctat."',
														'1')";
											$result = mysql_query($qry) or die(tdw_mysql_error($qry));
											$str_status = "<font color='green'>Trade Preapproval request ". $saved ."</font>";
										} else {
											$str_status = "<font color='red'>Record not saved. Required Fields are missing. Please try again.</font>";
										}
							} else {
									$str_status = "";
							}
?>
		<!-- BEGIN CONTAINER -->
		 <?
     include('main_empl_top_menu.php'); 
     ?>
		 <div id="trd_order">
		 <?
		 tsp(100, "Enter Trade Order(s)"); 
	   ?>		
		 <table width="100%" cellpadding="1" cellspacing="0" style="border: 1px solid #ffc9a6;"><tr><td valign="top">
     <form id="trd_request" name="trd_request" method="post" action="main_empl_trade.php" onSubmit="return zconfirmSubmit();">
			<table cellpadding="0" cellspacing="0" >
				<tr>
        	<td>
						<table class="font_etpa">
              <tr>
                <td><?=$str_status?></td>
              </tr>
            </table>          
          </td>
        </tr>
				<tr>
        	<td>
						<table width="100%" class="font_etpa">
              <tr>
                <td width="75">Instrument</td>
                <td>
                	<select class="text" name="instr" id="instr">
                  	<option value="EQ">Equity</option>
                    <option value="OP">Option</option>
                    <option value="OT">Other</option>
                   </select></td>
                <td width="5">&nbsp;</td>
                <td width="75" align="right">Account @ </td>
                <td>
                	<select class="text" name="acctat" id="acctat">
                	<?
									$qry_acct_at = "select auto_id, var_label from var_display_fields where var_type = 'ACCTAT' order by var_order";  
              		$result_acct_at = mysql_query($qry_acct_at) or die(tdw_mysql_error($qry_acct_at));
                  while ($row_acct_at = mysql_fetch_array($result_acct_at)) {
										echo "<option value='".$row_acct_at["auto_id"]."'>".$row_acct_at["var_label"]."</option>";
									}
									?>
                  </select>
                </td>
                <td>&nbsp;</td>
              </tr>
            </table>          
          </td>
        </tr>
				<tr>
        	<td>
						<table class="font_etpa">
              <tr>
                <td width="75">Symbol</td>
                <td><input class="text" name="symbol" id="symbol" type="text" value="" size="12" maxlength="12"/ onfocus="sh(1)" onBlur="hh(1)" onChange="get_company_name();hh(1)"><font color="red" size="+1"> * </font></td>
                <td><div id="hint_symbol"></div></td>
              </tr>
            </table>          
          </td>
        </tr>
				<tr>
        	<td>
						<table class="font_etpa">
              <tr>
                <td></td>
                <td colspan="2"><div id="valid_company"></div></td>
              </tr>
            </table>          
          </td>
        </tr>
				<tr>
        	<td>
						<table class="font_etpa">
              <tr>
                <td width="75">Buy/Sell</td>
                <td><input class="text"  name="buysell" id="buysell" type="text" value="" size="3" onFocus="sh(2)" onChange="hh(2)" onBlur="hh(2)"/><font color="red" size="+1"> *</font></td>
                <td><div id="hint_buysell"></td>
              </tr>
            </table>          
          </td>
        </tr>
				<tr>
        	<td>
						<table class="font_etpa">
              <tr>
                <td width="75">Quantity</td>
                <td><input class="text"  name="qty" id="qty" type="text" value="" size="8" onBlur="check_holding_period()" /><font color="red" size="+1"> *</font></td>
                <td>&nbsp;</td>
              </tr>
            </table>          
          </td>
        </tr>
				<tr>
        	<td>
						<table class="font_etpa">
              <tr>
                <td width="75">Order Type</td>
                <td><select name="ordtype"style="width:100px;">
                <option value="MKT"SELECTED>Market</option>
                <option value="LMT">Limit</option>
                <option value="SMKT" >Stop market</option>
                <option value="SLMT" >Stop limit</option>
                <option value="TSP" >Trailing stop %</option>
                <option value="TSD" >Trailing stop $</option>
              </select></td>
                <td>&nbsp;</td>
              </tr>
            </table>          
          </td>
        </tr>
				<tr>
        	<td>
						<table class="font_etpa">
              <tr>
                <td width="75">Special Instr.</td>
                <td><select name="spinstructions">
                <option value="" SELECTED>SP. INSTR.</option>
                <option value="AON" >AON (All or None)</option>
                <option value="FOK" >FOK (Fill or Kill)</option>
                </select></td>
                <td>&nbsp;</td>
              </tr>
            </table>          
          </td>
        </tr>
				<tr>
        	<td>
						<table class="font_etpa">
              <tr>
                <td width="75">Price</td>
                <td><input class="text"  name="lprice" type="text" value="" size="7"/></td>
                <td>&nbsp;</td>
               </tr>  
            </table>          
          </td>
        </tr>
				<tr>
        	<td>
						<table class="font_etpa">
              <tr>
                <td colspan="2">
      						 <input type="hidden" id="override_type" name="override_type" value="" />
                   <input type="submit" name="submit" id="action_submit" value="Submit" />&nbsp;&nbsp;
                   <input type="submit" name="submit_override" id="submit_override" value="Request Override" style="visibility:hidden; display:none"/>
                </td>
                <td>&nbsp;</td>
               </tr>  
            </table>          
          </td>
        </tr>
      </table>
      </form>
      <div id="instruct" class="instruct" align="right"><font color="red" size="+1"> * </font>indicates Required Value</div>
      </td></tr></table>
     <? tep(); ?>
     </div>
		 <table width="100%" cellpadding="1" cellspacing="0" style="border: 1px solid #ffc9a6;"><tr><td valign="top">
     <font class="font_etpa_sm">Approvers Online:</font> 
		 <?
     echo '<div id="appr_online">';
		 $qry_online = "SELECT Lastname as approver, `user_isonline`
										FROM `users` WHERE `is_trade_approver` = 1
										AND `user_isactive` = 1";
		 $result_online = mysql_query($qry_online) or die(tdw_mysql_error($qry_online));
		 while ($row_online = mysql_fetch_array($result_online)) {
			
			 if ($row_online["user_isonline"] == 1) {
				echo '<img src="images/isonline.png" border="0">&nbsp;<a class="online">'.$row_online["approver"]."&nbsp;&nbsp;";
			 } else {
				echo '<img src="images/isoffline.png" border="0">&nbsp;<a class="offline">'.$row_online["approver"]."&nbsp;&nbsp;";
			 }
		 }
		 ?>
		 </td></tr></table>
		 <div id="err_notify" class="font_etpa_error"></div>
     <!-- END CONTAINER -->
<?
include('inc_footer.php');
?>
