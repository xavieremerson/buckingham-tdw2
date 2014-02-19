<?php
include("phpchartdir.php");

#The data for the pie chart
$data = array(72, 18, 15, 12);

#The labels for the pie chart
$labels = array("Labor", "Machinery", "Facilities", "Computers");

#The depths for the sectors
$depths = array(30, 20, 10, 10);

#Create a PieChart object of size 360 x 300 pixels, with a light blue (0xccccff)
#background and a 1 pixel 3D border
$c = new PieChart(360, 300, 0xccccff, -1, 1);

#Set the center of the pie at (180, 175) and the radius to 100 pixels
$c->setPieSize(180, 175, 100);

#Add a title box using Times Bold Italic/14 points as font and 0x9999ff as
#background color
$titleObj = $c->addTitle("Project Cost Breakdown", "timesbi.ttf", 14);
$titleObj->setBackground(0x9999ff);

#Set the pie data and the pie labels
$c->setData($data, $labels);

#Draw the pie in 3D
$c->set3D2($depths);

#Set the start angle to 225 degrees may improve layout when the depths of the
#sector are sorted in descending order, because it ensures the tallest sector is
#at the back.
$c->setStartAngle(225);

#output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
