<?
echo "Module Disabled. Please contact support@centersys.com";
exit;
?>




<?
//=================================================================================================
include('../includes/dbconnect.php');  
include('../includes/global.php');  
include('../includes/functions.php');  
?>
<html>
<head>
<?
echo "<!--"."Server: ".$_SERVER["SERVER_ADDR"]."-->\n";
echo "<!--"."Client: ".$_SERVER["REMOTE_ADDR"]."-->\n";
echo "<!--"."Administrator Email: ".$_SERVER["SERVER_ADMIN"]."-->\n";
echo "<!--"."Page Process Time: ".date("D, m/d/Y h:i a")."-->\n";
?>
<link REL="SHORTCUT ICON" href="/tdw/images/favicon.ico"></link>
<link rel="bookmark" href="/tdw/images/favicon.ico"></link>
<title>Map Accounts</title>
<script language="JavaScript" src="../includes/js/popup.js"></script>
<style type="text/css">
<!--
.input {	font-family: "Courier New", Courier, mono;	font-size: 14px;}
.showdata {	font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 12px;	font-weight: bold;	color: #000099;}
.heading {	font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 12px;	font-weight: bold;	color: #660000;}
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
        	<td width="91"><img src="../images/logo.gif" ></td>
        	<td align="right" valign="top"> 
						<table width="100%"  border="0" cellspacing="0" cellpadding="0">
            	<tr> 
              	<td> 
									<table width="100%"  border="0" cellspacing="1" cellpadding="1">
                  	<tr> 
                    	<td align="left" valign="top"><a class="CompanyName"><img src="../images/client.gif" border="0"></a></td>
                    	<td align="right" valign="top">&nbsp;</td>
                  	</tr>
                	</table>
								</td>
            	</tr>
            	<tr> 
              	<td><font color="#000066" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>UTILITY: Mapping Contacts to Clients (for SalesLogix)</strong></font></td>
            	</tr>
          	</table>
					</td>
      	</tr>
    	</table>
  	</td>
	</tr>
	<tr valign="top"> 
		<td valign="top"> 

<?	
if ($submitmaps) {
	foreach($_POST as $key => $value)
	   {
		$exploded_val = explode("^",$value);
		if ($exploded_val[1] != '') {
		
			$sqlstrx = "select * from slx_temp where id = '".$exploded_val[1]."'";
			$result_x = mysql_query($sqlstrx) or die (mysql_error());
			while ( $row_x = mysql_fetch_array($result_x) ) {
				$getcompany = $row_x["company"];
				$getowner = $row_x["owner"];
				$getowner = str_replace("'","\'",$getowner);
			}
			$sqlstry = "update slx_temp set temp2 = '".$value."' where company = '".$getcompany."' and owner = '".$getowner."'";
			$result_y = mysql_query($sqlstry) or die (mysql_error());

		}
	}
}

	$qry_slx = "SELECT auto_id, rrname, rrnum from slx_bmap";
	$result_slx = mysql_query($qry_slx) or die (mysql_error());	 
	?>
	<form name="ownerselect" action="<?=$PHP_SELF?>" method="post">
	<br>&nbsp;&nbsp;&nbsp;<select name="owner">
	<option value=""></option>
	<?
	while ( $row_slx = mysql_fetch_array($result_slx) ) 
		{
			?>
			<option value="<?=$row_slx["rrname"]?>|<?=$row_slx["rrnum"]?>|<?=$row_slx["auto_id"]?>"><?=$row_slx["rrname"]?></option>
			<?
		}
	?>
	</select>
	<input name="submit" type="submit" value="Get Contact Companies">
	</form>
	<?

if ($owner) {

		$getval = explode("|",$owner);
		
		echo "&nbsp;&nbsp;&nbsp;Selected: <b>".$getval[0]."</b><br>&nbsp;&nbsp;&nbsp;RR Numbers: <b>";
		$getrrnum = explode(";",$getval[1]);
		foreach($getrrnum as $key => $value)
		   {
			$rrnumval = trim($value);
			if ($rrnumval != '') {
			echo $value . ",&nbsp;";
			$sqlstr .= " or nadd_rr_exec_rep = '".$rrnumval."' ";
			}
		   }
		echo "</b><br>";
?>
	<?
	$qry_slx1 = "SELECT distinct(company) from slx_temp where owner = '".str_replace("'","\'",$getval[0])."'";
	$result_slx1 = mysql_query($qry_slx1) or die (mysql_error());	 
	?>
	<form name="processowner" method="post" action="<?=$PHP_SELF?>">
	<table>
		<tr>
			<td>
				<table>
					<tr>
						<td class="heading">Contact Company</td>
						<td colspan="2" class="heading">Advisor/Client</td>
					</tr>
					<?
					$j = 1;
					while ( $row_slx1 = mysql_fetch_array($result_slx1) ) 
					
						{
							$qry_slx2 = "SELECT max(id) as id, temp2 from slx_temp where company = '".str_replace("'","\'",$row_slx1["company"])."' group by temp2";
							$result_slx2 = mysql_query($qry_slx2) or die (mysql_error());
								while ( $row_slx2 = mysql_fetch_array($result_slx2) ) 
									{
									$idval = $row_slx2["id"];
									$mappedname = $row_slx2["temp2"];
									}
						$js_company = str_replace("'"," ",$row_slx1["company"]);
					?>
					<tr>
						<td class="showdata"><?=$row_slx1["company"]?></td>
						<td class="showdata"><input name="newmap<?=$idval?>" value="<?=$mappedname?>"></td>
						<td><a href="javascript:CreateWnd('pop.php?rep=<?=$getval[2]?>&count=<?=$idval?>&company=<?=$js_company?>', 400, 400, false);">Lookup</a></td>
					</tr>
					<?
					$j = $j + 1;
					}
?>
				</table>
			</td>
		</tr>
		<tr><td><input name="submitmaps" type="submit" value="SAVE"></td></tr>
	</table></form>

<?
}
?>

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
