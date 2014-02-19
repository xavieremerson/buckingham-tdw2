<?php
include("phpchartdir.php");

#
#Displays the monthly revenue for the selected year. The selected year
#should be passed in as a query parameter called "year"
#
if (isset($HTTP_GET_VARS["year"]))
	$SelectedYear = $HTTP_GET_VARS["year"];
else
	$SelectedYear = 2001;

#
#Create an SQL statement to get the revenues of each month for the
#selected year.
#
$SQLstatement = "Select Month(TimeStamp) - 1, Software, Hardware, Services " .
      "From revenue Where Year(TimeStamp)=" . $SelectedYear;

#
#Read in the revenue data into arrays
#
mysql_connect("localhost", "test", "test");
$result = mysql_db_query("sample", $SQLstatement);
$software = array_pad(array(), 12, 0);
$hardware = array_pad(array(), 12, 0);
$services = array_pad(array(), 12, 0);
while ($row = mysql_fetch_row($result)) {
	$software[$row[0]] = $row[1];
	$hardware[$row[0]] = $row[2];
	$services[$row[0]] = $row[3];
}

#
#Now we obtain the data into arrays, we can start to draw the chart 
#using ChartDirector
#	
	
#Create a XYChart of size 420 pixels x 240 pixels
$c = new XYChart(420, 240);

#Set the chart background to pale yellow (0xffffc0) with a 2 pixel 3D border
$c->setBackground(0xffffc0, 0xffffc0, 2);
	
#Set the plotarea at (70, 50) and of size 320 x 150 pixels. Set background
#color to white (0xffffff). Enable both horizontal and vertical grids by
#setting their colors to light grey (0xc0c0c0)
$c->setPlotArea(70, 50, 320, 150, 0xffffff, 0xffffff, 0xc0c0c0, 0xc0c0c0);

#Add a title to the chart
$title = $c->addTitle("Revenue for " . $SelectedYear, "timesbi.ttf");
$title->setBackground(0xffff00);

#Add a legend box at the top of the plotarea
$legend = $c->addLegend(70, 30, 0, "", 8);
$legend->setBackground(Transparent);

#Add a stacked bar chart layer using the supplied data
$layer = $c->addBarLayer2(Stack);
$layer->addDataSet($software, -1, "Software");
$layer->addDataSet($hardware, -1, "Hardware");
$layer->addDataSet($services, -1, "Services");
$layer->setBorderColor(Transparent, 1);
	
#Set the x axis labels. In this example, the labels must be Jan - Dec.
$labels = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug",
	"Sept", "Oct", "Nov", "Dec");
$c->xAxis->setLabels($labels);

#Set the x-axis width to 2 pixels
$c->xAxis->setWidth(2);

#Set the y axis title
$c->yAxis->setTitle("USD (K)");

#Set the y-axis width to 2 pixels
$c->yAxis->setWidth(2);

#Output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
