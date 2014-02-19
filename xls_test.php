<link rel="stylesheet" type="text/css" href="includes/styles.css">
<?
include('includes/functions.php');
include('includes/dbconnect.php');
include('includes/global.php');
require_once 'Spreadsheet/Excel/Writer.php';

//VARIABLES
$var_wks_name = "Test Worksheet Name";

//initiate page load time routine
$time=getmicrotime(); 

//We give the path to our file here
$xlfilename = date('Y-m-d_h.i.s.a')."__".md5(rand(1000000000,9999999999)).".xls";
$wkb = new Spreadsheet_Excel_Writer('data/xls/'.$xlfilename);

			//FORMATTING IN THE FOLLOWING FILE
			include('format_excel.php');

			$wks =& $wkb->addWorksheet($var_wks_name);
			$wks->setLandscape ();
			$wks->setMarginLeft(0.4);
			$wks->setMarginRight(0.4);
			$wks->setMarginTop(0.5);
			$wks->setMarginBottom(0.4);
			$wks->setFooter ("TDW (Buckingham : Trade Data Warehouse)", $margin=0.5);
			
			$wks->setPaper(5);
			

			//FOLLOWING FILE CONTAINS THE HEADER DATA FOR EXCEL WORKSHEET
			include('format_excel_header.php');

			$wks->setRow(0,36);
			$wks->mergeCells(0,0,0,14);

      $wks->insertBitmap(0,0,"images/tdw_xls.bmp");

// We still need to explicitly close the workbook
$wkb->close();
//Header("Location: http://192.168.20.63/tdw/data/xls/test.xls");

//show page load time
	echo "Report generated in ". sprintf("%01.2f",((getmicrotime()-$time)/1000))." s.\n<br>"; 						

?>
<a href="http://192.168.20.63/tdw/data/xls/<?=$xlfilename?>" target="_blank">Click here to download the generated report (File Format: Excel)</a><br /><br />
<?
xdebug("Process completed at :",date('m/d/Y H:i:s a'));
?>