<?php
include("phpchartdir.php");

#Get the selected year and month
$selectedYear = $HTTP_GET_VARS["year"];
$selectedMonth = $HTTP_GET_VARS["x"] + 1;

#Get the monthly revenue
$monthlyRevenue = $HTTP_GET_VARS["value"];

#
#  In this demo, we just split the total revenue into 3 parts using random
#  numbers. In real life, the data probably can come from a database.
#
srand($selectedMonth * 2000 + $selectedYear);
$data = array_pad(array(), 3, 0);
$data[0] = (rand() / getrandmax() * 0.1 + 0.5) * $monthlyRevenue;
$data[1] = (rand() / getrandmax() * 0.1 + 0.2) * $monthlyRevenue;
$data[2] = $monthlyRevenue - $data[0] - $data[1];

#The labels for the pie chart
$labels = array("Services", "Hardware", "Software");

#Create a PieChart object of size 360 x 260 pixels
$c = new PieChart(360, 260);

#Set the center of the pie at (180, 140) and the radius to 100 pixels
$c->setPieSize(180, 130, 100);

#Add a title to the pie chart using 13 pts Times Bold Italic font
$c->addTitle("Revenue Breakdown for $selectedMonth/$selectedYear",
    "timesbi.ttf", 13);

#Draw the pie in 3D
$c->set3D();

#Set the pie data and the pie labels
$c->setData($data, $labels);

#Create the image and save it in a temporary location
$chart1URL = $c->makeSession("chart1");

#Create an image map for the chart
$imageMap = $c->getHTMLImageMap("piestub.php", "",
    "title='{label}:USD {value|0}K'");
?>
<html>
<body>
<h1>Simple Clickable Pie Chart</h1>
<p><a href="viewsource.php?file=<?php echo $HTTP_SERVER_VARS["SCRIPT_NAME"]?>">
View Source Code
</a></p>

<img src="myimage.php?<?php echo $chart1URL?>" border="0" usemap="#map1">
<map name="map1">
<?php echo $imageMap?>
</map>
</body>
</html>
