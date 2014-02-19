<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Valuation Prototype</title>
<link href="../includes/styles.css" rel="stylesheet" type="text/css" />
<script src="../includes/prototype/prototype.js" language="javascript"></script>

<script language="javascript" src="javascript.js"></script>

<style type="text/css">
table.sample {
	border-width: 1px;
	border-spacing: ;
	border-style: solid;
	border-color: gray;
	border-collapse: collapse;
}

table.sample td {
	border-width: 1px;
	padding: 2px;
	border-collapse: collapse;
	border-style: solid;
	border-color: gray;
	-moz-border-radius: ;
}

.missing {
	background-color:#ECFFFF;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
	color: #333333;
}

</style>
<script language="javascript">
	function initx() {
		$("button_process").disabled=true;
		//alert("show");
		//$("img_proc").style.display = "block";
		$("img_proc").style.visibility = "visible";

		//setTimeout(seq_get_data(), 5000)

		seq_get_data();
		//$("img_proc").style.display = "block";
		//alert("hide");
		$("img_proc").style.visibility = "hidden";
		$("button_save").disabled=false;
	}
</script>
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
    <td><font face="Verdana, Arial, Helvetica, sans-serif" size="+5" color="#CCCCCC">Valuation <font size="+2">(Beta)</font></font></td>
  </tr>
</table>
<?
if ($_POST) {
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$file_output = date('mdy')."_output.pri";

	$fp = fopen ("//bucksnapNY/SHARE2/Port/BuckCap/Pricing/Axyx/".$file_output, "w");  
	
	
	for ($j=0;$j<$count_total;$j++) {
			/*	data_1 = [adus^shpgy]
				desc_val_1 = [Shire plc]
				price_val_1 = [36.70]
				data_2 = [cbus^6046759b6]
				desc_val_2 = [ ]
				price_2 = [2.222]
			*/
			$var_data = 			"data_".$j;
			$var_desc = 			"desc_val_".$j;
			$var_price = 			"price_".$j;
			$var_price_val = 	    "price_val_".$j;
			
			$arr_company = explode("^",$$var_data);
			
			
			//if price is entered, save in db
			if ($$var_price != "") {
			$sql = "delete from valuation_info where symbol = '".$arr_company[1]."'";
			//echo $sql;
			$result = mysql_query($sql) or die (mysql_error());
			$sql = "insert into valuation_info values (null,'".$arr_company[1]."', '".$$var_price."', now())";
			//echo $sql;
			$result = mysql_query($sql) or die (mysql_error());
		
			//$str_val =  $arr_company[0] . chr(9) . $arr_company[1] . chr(9) .$$var_desc. chr(9) . $$var_price . "\r\n";
			$str_val =  $arr_company[0] . chr(9) . $arr_company[1] . chr(9) .$$var_price. chr(9) . chr(9) . "1" . "\r\n";
			fwrite ($fp,$str_val);        
			
			} else {
			
			//$str_val =  $arr_company[0] . chr(9) . $arr_company[1] . chr(9) .$$var_desc. chr(9) . $$var_price_val . "\r\n";
			$str_val =  $arr_company[0] . chr(9) . $arr_company[1] . chr(9) .$$var_price_val. chr(9) . chr(9) . "1" . "\r\n";
			fwrite ($fp,$str_val);        

			}
	}
	
	fclose ($fp);   
	
	copy("//bucksnapNY/SHARE2/Port/BuckCap/Pricing/Axyx/".date('mdy').".pri", "//bucksnapNY/SHARE2/Port/BuckCap/Pricing/Axyx/" . date('mdy')."-original.pri");
	shell_exec("del K:/Port/BuckCap/Pricing/Axys/".date('mdy').".pri");
	copy("//bucksnapNY/SHARE2/Port/BuckCap/Pricing/Axyx/".$file_output, "//bucksnapNY/SHARE2/Port/BuckCap/Pricing/Axyx/" . date('mdy').".pri");

	?>
  <font face="Verdana, Arial, Helvetica, sans-serif" color="#000066" style="font-size:14px">
  <br><br><br><br>
  <strong>&nbsp;&nbsp;&nbsp;&nbsp;1. Output File : <?=$file_output?> created.</strong><br><br>
  <strong>&nbsp;&nbsp;&nbsp;&nbsp;2. Original file <?=date('mdy').".pri"?> saved as <?=date('mdy')."-original.pri"?>.</strong><br><br>
  <strong>&nbsp;&nbsp;&nbsp;&nbsp;3. File <?=date('mdy').".pri"?> updated and ready for import into Axys.</strong><br><br>
  </font><br>
  <?	
	
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
}
?>
<form id="test" name="test" action="<?=$PHP_SELF?>" method="POST">
    
<?
if (!$button_save) { //

//Create filename to process
$file_input = date('mdy').".pri";
?>
<table border="0" cellpadding="10">
	<tr>
  	<td>
			<?
      if (!is_file("//bucksnapNY/SHARE2/Port/BuckCap/Pricing/Axyx/".$file_input)) {
				?>
				<font face="Verdana, Arial, Helvetica, sans-serif" color="#ff0000" style="font-size:12px">
				Holdings File (<?=$file_input?>) is missing. Please create file before proceeding.</font><br>
				<? 
				exit;		
			}	else {
			?>
        <font face="Verdana, Arial, Helvetica, sans-serif" color="#000066" style="font-size:12px">
        Found File <?=$file_input?>.</font><br>
        <?
        //exit;
      }
      ?>
      <table border="0" cellspacing="0" cellpadding="5">
        <tr>
      <!--    <td><input type="submit" name="process_2" value="View Sample Data"  /></td>
      -->    
      
          <td><input type="button" id="button_process"  name="process_1" value="Start Processing"  onClick="initx()" /></td>
          <td nowrap width="10"><div id="img_proc" name="img_proc" style="visibility:hidden"><img src="./images/mozilla_blu.gif" border="0"> Processing...</div></td>
          <td><input type="submit" id="button_save" name="button_save" value="Save and Process" disabled /></td>
          <td></td>
          <td>&nbsp;</td>
        </tr>
      </table>
      <table class="sample">
			<?
			//$file = fopen("K:/Port/BuckCap/Pricing/Axys/032709.pri","r");
			
			$fcontents = file("//bucksnapNY/SHARE2/Port/BuckCap/Pricing/Axyx/".$file_input);
			//$fcontents = file("032709.pri");
			
			$hold_val = sizeof($fcontents);
			for($i=0; $i<sizeof($fcontents); $i++) { 
				$line = trim($fcontents[$i]); 
				$arr = explode("\t", $line); 
				?>
							<tr id="tr_<?=$i?>" >
								<input type="hidden" id="data_<?=$i?>" name="data_<?=$i?>" value="<?=$arr[0]."^".$arr[1]?>">
                <td width="50" id="proc_<?=$i?>">&nbsp;<?=$i+1?></td>
                <td width="100"><?=$arr[0]?></td>
								<td width="100"><?=$arr[1]?></td>
								<td width="200" id="desc_<?=$i?>"></td>
								<input type="hidden" id="desc_val_<?=$i?>" name="desc_val_<?=$i?>" value="">
								<td width="100" id="price_<?=$i?>"></td>
								<input type="hidden" id="price_val_<?=$i?>" name="price_val_<?=$i?>" value="">
								<td nowrap width="400" id="comment_<?=$i?>"></td>
							</tr>	
        <?      			
				//show_array($arr);
			}

/*			while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
					$num = count($data);
					//echo "<p> $num fields in line $row: <br /></p>\n";
					$row++;
				 
						?>
<!--
-->						<?
			}*/
			//fclose($file);
?>
            <input type="hidden" id="count_total" name="count_total" value="<?=$hold_val?>">
       </table>
    </td>
  </tr>
</table>
</form>
<?
}
//===========================================================================================================
?>