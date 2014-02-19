<?
// include the class file	
include ("easyexcel.php");

// Create a new EasyExcel object
// Be careful with the names, spaces are not valid characters
// Name of the object vbscript
// Name of the main worksheet
$test = new EasyExcel("Sellings","Sellings");
// Create the excel sheet
// Width in %
// Height 
$test->AddExcel(100,350);
// The sql sentence used to generate the data
// You will have to change dbconnect method to connect the right mysql server and database
$sql="select * from tsellings";
// These fields go to the excel sheet
$test->m_arrFieldsShow[] ="Region";
$test->m_arrFieldsShow[] ="City";
$test->m_arrFieldsShow[] ="Seller";
$test->m_arrFieldsShow[] ="Product";
$test->m_arrFieldsShow[] ="Units";
$test->m_arrFieldsShow[] ="TotalPrice";
// Create the method in vbscript
$test->Begin_OnLoad();
// Generate the main sheet executing the query
$test->GenExcel($sql);
// We close the method in vbscript
$test->Close_OnLoad();
?>