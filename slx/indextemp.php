<?
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
<body>
<table width="100%" height="100%"> 
	<tr valign="top"> 
		<td valign="top"> 

<?	

	$qry_slx = "SELECT auto_id, rrname, rrnum from slx_bmap";
	$result_slx = mysql_query($qry_slx) or die (mysql_error());	 

	while ( $row_slx = mysql_fetch_array($result_slx) ) 
		{
			
			$owner = $row_slx["rrname"]."|".$row_slx["rrnum"]."|".$row_slx["auto_id"];

				if ($owner) {
				
						$getval = explode("|",$owner);
						
						echo "&nbsp;&nbsp;&nbsp;<b>".$getval[0]."</b><br>&nbsp;&nbsp;&nbsp;RR Numbers: <b>";
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
								<a class="input">&nbsp;&nbsp;&nbsp;Please place a check against the names of clients and leave prospects unchecked.</a><br><br>
								<table>
									<tr>
										<td class="heading">Contact/Company</td>
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
									?>
									<tr>
										<td class="showdata"><input type="checkbox">&nbsp;&nbsp;&nbsp;<?=$row_slx1["company"]?></td>
									</tr>
									<?
									$j = $j + 1;
									}
				?>
								</table>
							</td>
						</tr>
					</table></form>
				
				<?
				}
		}
?>
		</td>
	</tr>
	<tr valign="bottom">
		<td>
			<table width="100%" height="20">
				<tr valign="top">
					
          <td align="center" valign="bottom">&nbsp; </td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</body>
</html>
