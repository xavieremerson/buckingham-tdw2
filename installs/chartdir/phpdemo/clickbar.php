<?php
include("phpchartdir.php");

#
#For demo purpose, we use hard coded data. In real life, the following data
#could come from a database.
#
$revenue = array(4500, 5600, 6300, 8000, 12000, 14000, 16000, 20000, 24000,
    28000);
$labels = array("1992", "1993", "1994", "1995", "1996", "1997", "1998", "1999",
    "2000", "2001");

#Create a XYChart object of size 450 x 200 pixels
$c = new XYChart(450, 200);

#Add a title to the chart using Times Bold Italic font
$c->addTitle("Annual Revenue for Star Tech", "timesbi.ttf");

#Set the plotarea at (60, 25) and of size 350 x 150 pixels
$c->setPlotArea(60, 25, 350, 150);

#Add a blue (0x3333cc) bar chart layer using the given data. Set the bar border
#to 1 pixel 3D style.
$barLayerObj = $c->addBarLayer($revenue, 0x3333cc, "Revenue");
$barLayerObj->setBorderColor(-1, 1);

#Set x axis labels using the given labels
$c->xAxis->setLabels($labels);

#Add a title to the y axis
$c->yAxis->setTitle("USD (K)");

#Create the image and save it in a temporary location
$chart1URL = $c->makeSession("chart1");

#Create an image map for the chart
$imageMap = $c->getHTMLImageMap("clickline.php", "",
    "title='{xLabel}: USD {value|0}K'");
?>
<html>
<body>
<h1>Simple Clickable Bar Chart</h1>
<p><a href="viewsource.php?file=<?php echo $HTTP_SERVER_VARS["SCRIPT_NAME"]?>">
View Source Code
</a></p>

<img src="myimage.php?<?php echo $chart1URL?>" border="0" usemap="#map1">
<map name="map1">
<?php echo $imageMap?>
</map>

</body>
</html>
