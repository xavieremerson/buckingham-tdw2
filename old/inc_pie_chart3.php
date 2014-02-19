<?php
include("phpchartdir.php");
include('includes/dbconnect.php');
include('includes/functions.php');


if($pid == '')
{
	$pid = 1;
}

$query_port = "SELECT port_name FROM port_portfolio WHERE port_auto_id = '".$pid."' AND port_isactive = '1'";
$result_port = mysql_query($query_port) or die(mysql_error());
$row_port = mysql_fetch_array($result_port);


$query_alloc = "SELECT * FROM aloc_allocation WHERE aloc_port_id = '".$pid."' AND aloc_isactive = '1'";
$result_alloc = mysql_query($query_alloc) or die(mysql_error());

$data = array();
$labels = array();

while($row_alloc = mysql_fetch_array($result_alloc))
{
	#The data for the pie chart
	$data[] = $row_alloc['aloc_percent'];
	
	#The labels for the pie chart
	$labels[] = $row_alloc['aloc_name'];
}

#Create a PieChart object of size 500 x 230 pixels, with a light blue (0xccccff)
#background and a 1 pixel 3D border
$c = new PieChart(490, 220, 0xffffff, -1, 0);

//ccccff
#Add a title box using Times Bold Italic/14 points as font and 0x9999ff as
#background color
$titleObj = $c->addTitle($row_port['port_name'], "timesb.ttf", 10);
$titleObj->setBackground(0xDDDDDD);

#Set the center of the pie at (250, 120) and the radius to 100 pixels
$c->setPieSize(230, 120, 100);

#Draw the pie in 3D
$c->set3D();

#add a legend box where the top left corner is at (330, 40)
//$c->addLegend(450, 35);
 

#Use the side label layout method
$c->setLabelLayout(SideLayout);

#Set the label box the same color as the sector with a 1 pixel 3D border
$labelStyleObj = $c->setLabelStyle();
$labelStyleObj->setBackground(SameAsMainColor, Transparent, 3);

#Set the border color of the sector the same color as the fill color. Set the
#line color of the join line to black (0x0)
$c->setLineColor(SameAsMainColor, 0x0);

#Set the start angle to 135 degrees may improve layout when there are many small
#sectors at the end of the data array (that is, data sorted in descending
#order). It is because this makes the small sectors position near the horizontal
#axis, where the text label has the least tendency to overlap. For data sorted
#in ascending order, a start angle of 45 degrees can be used instead.
$c->setStartAngle(135);

#Set the pie data and the pie labels
$c->setData($data, $labels);

#output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
