<?php
include('includes/dbconnect.php');
include('includes/functions.php');

include("phpchartdir.php");

$tdate = previous_business_day ();

#The data for the pie chart
$data = array(1178812, 921076, 800402, 400252, 419082, 834700, 450251);

#The labels for the pie chart
$labels = array("MSFT", "ORCL", "IBM", "GOOG", "INTC", "FD", "XATA");

#Create a PieChart object of size 450 x 240 pixels
$c = new PieChart(240, 180);

#Set the center of the pie at (150, 100) and the radius to 80 pixels
$c->setPieSize(110, 80, 60);

#Add a title at the bottom of the chart using Arial Bold Italic font
$c->addTitle2(Bottom, " TOP BUYS", "arialb.ttf", 10, 0x000000);

#Draw the pie in 3D
$c->set3D();

#add a legend box where the top left corner is at (330, 40)
//$c->addLegend(330, 40);

#modify the label format for the sectors to $nnnK (pp.pp%)

$c->setLabelStyle("arialb.ttf", 8, 0x444444);
$c->setLabelFormat("{label}\n({percent}%)");

#Set the pie data and the pie labels
$c->setData($data, $labels);

#Explode the 1st sector (index = 0)
$c->setExplode(0);

#output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>