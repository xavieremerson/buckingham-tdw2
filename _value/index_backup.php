<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Valuation Prototype</title>
<link href="../includes/styles.css" rel="stylesheet" type="text/css" />
<script src="../includes/prototype/prototype.js" language="javascript"></script>
</head>
<?
//error_reporting(0);
include('../includes/global.php');
include('../includes/dbconnect.php');
include('../includes/functions.php');
include('config.php');
?>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0">
<table width="100%" border="0" cellpadding="10" cellspacing="0" bgcolor="#000099">
  <tr>
    <td><font face="Verdana, Arial, Helvetica, sans-serif" size="+5" color="#CCCCCC">Valuation <font size="+2">(Prototype)</font></font></td>
  </tr>
</table>
<form name="startproc" action="<?=$PHP_SELF?>" method="POST">
<table border="0" cellspacing="0" cellpadding="5">
  <tr>
    <td><input type="submit" name="process_2" value="View Sample Data"  /></td>
    <td><input type="submit" name="process_1" value="Start Processing"  /></td>
    <td><input type="submit" name="process_3" value="Save and Process"  /></td>
		<td>&nbsp;</td>
  </tr>
</table>
</form>
<?
if ($process_1) {
			$file = fopen("D:/tdw/tdw/_value/working.csv","r");
			
			$row = 1;
			while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
					$num = count($data);
					//echo "<p> $num fields in line $row: <br /></p>\n";
					$row++;
				 
					if ($data[0] == 'csus' or 1==1) {
					
						if ($data[0] == 'csgb') {
							$symbol = substr($data[1], 0, (strlen($data[1])-2) ) . ".L";
						} elseif ($data[0] == 'clus' OR $data[0] == 'ptus') {
							$symbol = str_replace("+","",$data[1]) . ".X";
						} else {
							$symbol = $data[1];
						}
					
						$str_company_detail = get_company_detail($symbol);
						//echo $str_company_detail . "<br />\n";
						$acd = explode("^", $str_company_detail); 
			
						if ($acd[1] != '0.00') {
						?>
						<table width="600">
							<tr>
								<td width="100"><?=$data[0]?></td>
								<td width="100"><?=$data[1]?></td>
								<td width="200"><?=$acd[0]?></td>
								<td width="100"><?=$acd[1]?></td>
							</tr>
						</table>          
						<?
						} else {
						?>
						<table width="600" bgcolor="#CCCCCC">
							<tr>
								<td width="100"><?=$data[0]?></td>
								<td width="100"><?=$data[1]?></td>
								<td width="200"><?=$acd[0]?></td>
								<td width="100"><input type="text" name="xprice" value="" maxlength="10" size="10"</td>
							</tr>
						</table>          
						<?
						}
						ob_flush();
						flush();
					}
				 
					/*	  for ($c=0; $c < $num; $c++) {
									echo $data[$c] . "<br />\n";
							}
					*/
			}
			
			fclose($file);
}
//============================================================================================================

if ($process_2) {

echo "<pre>";
include('working.csv');
echo "</pre>";
}








?>