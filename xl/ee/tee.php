<?	
include ("easyexcel.php");

// Create a new object
// Be careful with the names, spaces are not valid characters
// Name of the object
// Name of the main worksheet
$test = new EasyExcel("Sellings","Sellings");
// Create the excel sheet
$test->AddExcel(100,350);
// In this test, three diferents chart are generated
// We need to do this before use them
// Indicate the name of the object, width in % and the height of the chart
$test->AddChart("ChartRegion",100,350);
$test->AddChart("ChartSellerProduct",100,350);
$test->AddChart("ChartSellerTotal",100,350);

// The sql sentence used to generate the data
$sql="select * from tsellings";
// These fields are going to be used to create more specific sheet
$test->m_arrFields[] ="Region";
$test->m_arrFields[] ="Seller";
$test->m_arrFields[] ="Product";
// These fields go to the excel sheet
$test->m_arrFieldsShow[] ="Region";
$test->m_arrFieldsShow[] ="City";
$test->m_arrFieldsShow[] ="Seller";
$test->m_arrFieldsShow[] ="Product";
$test->m_arrFieldsShow[] ="Units";
$test->m_arrFieldsShow[] ="TotalPrice";

// Create the method in vbscript
$test->Begin_OnLoad();

// Generate the main sheet with the data
$test->GenExcel($sql);
// Now we have an excel with the data showing the columns in m_arrFieldsShow

// We can generate new sheets and create a chart for every one

// First chart
// We want to show the data grouped by region and product
$arrRegionFields = array();
$arrRegionFields[] ="Region";
$arrRegionFields[] ="Product";
// Automatically a column with the number of ocurrences of the combination is generated.
// For every combination we are going to show the following information (and the number of ocurrences)
$arrRegionShow = array();
// The sum of units
$arrRegionShow[] ="Units.sum";
// The sum of the total price
$arrRegionShow[] ="TotalPrice.sum";
// The sum of the total price divided into the data of column d -> Units.sum
$arrRegionShow[] ="TotalPrice.avg.d";
// For this information (region - product) we are going to generate
// three different chart, 
// for every chart we indicate the column where is the data and legend we want to use
$arrValueColumns = array();
$arrLegends = array();
$arrValueColumns[] ="D";
$arrLegends[] ="NUMBER OF SELLS";
$arrValueColumns[] ="E";
$arrLegends[] ="TOTAL SELLS";
$arrValueColumns[] ="F";
$arrLegends[] ="AVERAGE PRICE";

// Now indicate what type of charts we want to generate
$arrType = array();
$arrType[] ="chChartTypeColumnClustered3D";
$arrType[] ="chChartTypeColumnClustered";
$arrType[] ="chChartTypeBar3D";
// Call the method to generate the new sheet with the information, and the charts
// $arrRegionFields -> array with the grouped fields
// "Region" -> name of the worksheet
// $bNumber -> indicate
// $arrRegionShow -> the fields we want to include in the worksheet
// Name of the chart object, we need to create using AddChart method
// "INFORMATION ABOUT REGIONS" -> Legend for the chart
// "A" is the column with the main field
// "B" is the column with the last field of the combination
// Normally these values will be the columns for the first field and last field of the $arrRegionFields
// $arrValuesColumns -> Columns used to generate the charts
// $arrTypes -> Type of every chart, if the type is "", the default value chChartTypeColumnClustered will be used
// $arrLegends -> Legend for every chart
$test->GenCountArray($arrRegionFields,"Region",$arrRegionShow,"ChartRegion","INFORMATION ABOUT REGIONS","A","B",$arrValueColumns,$arrType,$arrLegends);

// Second chart
$arrSellerFields = array();
$arrSellerFields[] ="Seller";
$arrSellerFields[] ="Product";
$arrSellerShow = array();
$arrSellerShow[] ="Units.sum";
$arrSellerShow[] ="TotalPrice.sum";
$arrSellerShow[] ="TotalPrice.avg.d";

$test->GenCountArray($arrSellerFields,"Seller",$arrSellerShow,"ChartSellerProduct","INFORMATION ABOUT SELLERS AND PRODUCTS","A","B",$arrValueColumns,$arrType,$arrLegends);

// Third chart
$arrSellerTotalFields = array();
$arrSellerTotalFields[] ="Seller";
$arrSellerTotalShow = array();
$arrSellerTotalShow[] ="Units.sum";
$arrSellerTotalShow[] ="TotalPrice.sum";
$arrSellerTotalShow[] ="TotalPrice.avg.c";
$arrValueTotalColumns = array();
$arrTotalLegends = array();
$arrValueTotalColumns[] ="C";
$arrLegendsTotal[] ="NUMBER OF SELLS";
$arrValueTotalColumns[] ="D";
$arrLegendsTotal[] ="TOTAL SELLS";
$arrValueTotalColumns[] ="E";
$arrLegendsTotal[] ="AVERAGE PRICE";
$arrSellerType = array();
$arrSellerType[] ="chChartTypeArea";
$arrSellerType[] ="chChartTypePie";
$arrSellerType[] ="";
$test->GenCountArray($arrSellerTotalFields,"SellerTotal",$arrSellerTotalShow,"ChartSellerTotal","INFORMATION ABOUT SELLERS","","A",$arrValueTotalColumns,$arrSellerType,$arrLegendsTotal);

// We close the method
$test->Close_OnLoad();

?>