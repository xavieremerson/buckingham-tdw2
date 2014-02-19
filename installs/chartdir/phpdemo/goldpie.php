<?php
include("phpchartdir.php");

#The data for the pie chart
$data = array(25, 18, 15, 12, 8, 30, 35);

#The labels for the pie chart
$labels = array("Labor", "Licenses", "Taxes", "Legal", "Insurance",
    "Facilities", "Production");

#Create a PieChart object of size 300 x 230 pixels
$c = new PieChart(300, 230);

#Set the background color of the chart to gold (goldGradient). Use a 2 pixel 3D
#border.
$c->setBackground($c->gradientColor($goldGradient), -1, 2);

#Set the center of the pie at (150, 115) and the radius to 80 pixels
$c->setPieSize(150, 115, 80);

#Add a title box using 10 point Arial Bold font. Set the background color to red
#metallic (redMetalGradient). Use a 1 pixel 3D border.
$titleObj = $c->addTitle("Pie Chart Coloring Demo", "arialbd.ttf", 10);
$titleObj->setBackground($c->gradientColor($redMetalGradient), -1, 1);

#Draw the pie in 3D
$c->set3D();

#Set the pie data and the pie labels
$c->setData($data, $labels);

#output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
