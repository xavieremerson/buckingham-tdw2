<?
include('includes/dbconnect.php');  
include('includes/global.php');  
include('includes/functions.php');  
?>
<html>
<head>
<?
echo "<!--"."Server: ".$_SERVER["SERVER_ADDR"]."-->\n";
echo "<!--"."Client: ".$_SERVER["REMOTE_ADDR"]."-->\n";
echo "<!--"."Administrator Email: ".$_SERVER["SERVER_ADMIN"]."-->\n";
echo "<!--"."Page Process Time: ".date("D, m/d/Y h:i a")."-->\n";
?>
<link REL="SHORTCUT ICON" href="images/favicon.ico"></link>
<link rel="bookmark" href="images/favicon.ico"></link>
<title>Lookup Accts</title>
<?
	$query_advisor = "SELECT cust_id, cust_code, cust_name FROM `tmp_cust` WHERE cust_code != '' order by cust_code";	
					
	$result_advisor = mysql_query($query_advisor) or die (mysql_error());
	while ( $rowx = mysql_fetch_array($result_advisor) ) 
	{
		$str_js .= '		new Array (';
		$result_acct = mysql_query("SELECT auto_id,cust_id,acct_number,acct_name,acct_new_number  from tmp_subacct where cust_id = '".$rowx["cust_id"]."' order by acct_name, acct_number") or die (mysql_error());
		while ( $rowy = mysql_fetch_array($result_acct) )
		{
				$str_address = str_replace('&','\&', $rowy["nadd_address_line_1"]);
				$str_address = str_replace('/','\/', $str_address);
				$str_address = str_replace('"','\"', $str_address);
				
				if ($rowy["acct_new_number"] == '') {
				$newacctnum = "---------";
				} else {
				$newacctnum = $rowy["acct_new_number"];
				}
				
				$str_js .= 'new Array("'.$rowy["acct_name"].' : '.$newacctnum.' : '. $rowy["acct_number"] .'",'.$rowy["auto_id"].'),
				';
		}
		$str_js .= 'new Array(" ",999999999999)
),
';
	}
	
	$str_js .= 'null);';
						
	?>
<script language="JavaScript" type="text/JavaScript">
accts = new Array(

<?=$str_js?>
						
function fillSelectFromArray(selectCtrl, itemArray, goodPrompt, badPrompt, defaultItem) {
var i, j;
var prompt;
// empty existing items
for (i = selectCtrl.options.length; i >= 0; i--) {
selectCtrl.options[i] = null; 
}
prompt = (itemArray != null) ? goodPrompt : badPrompt;
if (prompt == null) {
j = 0;
}
else {
selectCtrl.options[0] = new Option(prompt);
j = 1;
}
if (itemArray != null) {
// add new items
for (i = 0; i < itemArray.length; i++) {
selectCtrl.options[j] = new Option(itemArray[i][0]);
if (itemArray[i][1] != null) {
selectCtrl.options[j].value = itemArray[i][1]; 
}
j++;
}
// select first item (prompt) for sub list
selectCtrl.options[0].selected = true;
   }
}
</script>
<style type="text/css">
<!--
.input {	font-family: "Courier New", Courier, mono;	font-size: 14px;}
.headselect {	font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 16px;	font-weight: bold;	color: #000099;}
.centersys {	font-family: verdana;	font-size: 10px;	text-decoration: none;	color: #AAAAAA;	font-weight: bold;} 
.centersys:hover {	font-family: verdana;	font-size: 10px;	text-decoration: underline;	color: #FF0000;	font-weight: bold;}

-->
</style>
</head>
<body bgcolor="#F4F8FB" leftmargin="3" topmargin="3" rightmargin="3" bottommargin="3">
<table width="100%" height="100%" border="3" cellpadding="0" cellspacing="0" bordercolor="#333333" bordercolorlight="#999999" bordercolordark="#000000" bgcolor="#F4F8FB"> 
	<tr valign="top"> 
  	<td height="20"> 
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" bgcolor="FFFFFF">
      	<tr> 
        	<td width="91"><img src="images/logo.gif" ></td>
        	<td align="right" valign="top"> 
						<table width="100%"  border="0" cellspacing="0" cellpadding="0">
            	<tr> 
              	<td> 
									<table width="100%"  border="0" cellspacing="1" cellpadding="1">
                  	<tr> 
                    	<td align="left" valign="top"><a class="CompanyName"><img src="images/client.gif" border="0"></a></td>
                    	<td align="right" valign="top">&nbsp;</td>
                  	</tr>
                	</table>
								</td>
            	</tr>
            	<tr> 
              	<td><font color="#000066" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>ACCOUNT LOOKUP (OLD -&gt; NEW)</strong></font></td>
            	</tr>
          	</table>
					</td>
      	</tr>
    	</table>
  	</td>
	</tr>
	<tr valign="top"> 
		<td valign="top"> 
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr> 
					<td valign="top"> 
					<FORM NAME="main">
						<table>
							<tr>
								<td>&nbsp;</td>
							</tr>
							<tr> 
								<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
								<td class="input"> <SELECT class="input" NAME="Advisors" SIZE="30" style="width=350" onChange="fillSelectFromArray(this.form.Accts, ((this.selectedIndex == -1) ? null : accts[this.selectedIndex-1]));">
								<OPTION VALUE="-1" class="headselect">S E L E C T &nbsp; A C C O U N T</OPTION>
								<?
								$query_advisor = "SELECT cust_id, cust_code, cust_name FROM `tmp_cust` WHERE cust_code != '' order by cust_code";	

								$result_advisor = mysql_query($query_advisor) or die (mysql_error());
								$count_i = 1;
								while ( $row = mysql_fetch_array($result_advisor) ) 
								{
								?>
								<option value="<?=$count_i?>"><?=$row["cust_code"]?> : <?=$row["cust_name"]?></option>
								<?
								$count_i = $count_i + 1;
								}						
								?>
								</select> 
								</td>
								<td> 
								<SELECT class="input" NAME="Accts" SIZE="30" style="width=500">
									<OPTION VALUE="-1" class="headselect">R E S U L T : S U B &nbsp;A C C O U N T S</OPTION>
									<OPTION></OPTION>
								</SELECT> 
								</td>
							</tr>
						</table>
					</FORM>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr valign="bottom">
		<td>
			<table width="100%" height="20">
				<tr valign="top">
					<td align="center" valign="bottom">
						<table width="100%" height="20" border="0" cellpadding="0" cellspacing="0">
							<tr valign="top"> 
								<td align="center" valign="bottom">
									<hr align="center" size="1" color="#CCCCCC" noshade>
									<center><a class="centersys" href="http://www.centersys.com" target="_blank">CenterSys Group, Inc.</a></center>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</body>
</html>
