<?php
include("phpchartdir.php");

#The data for the chart
$dataY0 = array(4, 4.5, 5, 5.25, 5.75, 5.25, 5, 4.5, 4, 3, 2.5, 2.5);
$dataX0 = array(chartTime(1997, 1, 1), chartTime(1998, 6, 25), chartTime(1999,
    9, 6), chartTime(2000, 2, 6), chartTime(2000, 9, 21), chartTime(2001, 3, 4),
    chartTime(2001, 6, 8), chartTime(2002, 2, 4), chartTime(2002, 5, 19),
    chartTime(2002, 8, 16), chartTime(2002, 12, 1), chartTime(2003, 1, 1));

$dataY1 = array(7, 6.5, 6, 5, 6.5, 7, 6, 5.5, 5, 4, 3.5, 3.5);
$dataX1 = array(chartTime(1997, 1, 1), chartTime(1997, 7, 1), chartTime(1997,
    12, 1), chartTime(1999, 1, 15), chartTime(1999, 6, 9), chartTime(2000, 3, 3
    ), chartTime(2000, 8, 13), chartTime(2001, 5, 5), chartTime(2001, 9, 16),
    chartTime(2002, 3, 16), chartTime(2002, 6, 1), chartTime(2003, 1, 1));

#Create a XYChart object of size 500 x 270 pixels, with a pale blue (0xe0e0ff)
#background, a light blue (0xccccff) border, and 1 pixel 3D border effect.
$c = new XYChart(500, 270, 0xe0e0ff, 0xccccff, 1);

#Set the plotarea at (50, 50) and of size 420 x 180 pixels, using white
#(0xffffff) as the plot area background color. Turn on both horizontal and
#vertical grid lines with light grey color (0xc0c0c0)
$plotAreaObj = $c->setPlotArea(50, 50, 420, 180, 0xffffff);
$plotAreaObj->setGridColor(0xc0c0c0, 0xc0c0c0);

#Add a legend box at (55, 25) (top of the chart) with horizontal layout. Use 10
#pts Arial Bold Italic font. Set the background and border color to Transparent.
$legendObj = $c->addLegend(55, 25, false, "arialbi.ttf", 10);
$legendObj->setBackground(Transparent);

#Add a title to the chart using 14 points Times Bold Itatic font, using blue
#(0x9999ff) as the background color
$titleObj = $c->addTitle("Interest Rates", "timesbi.ttf", 14);
$titleObj->setBackground(0x9999ff);

#Set the y axis label format to display a percentage sign
$c->yAxis->setLabelFormat("{value}%");

#Add a red (0xff0000) step line layer to the chart and set the line width to 2
#pixels
$layer0 = $c->addStepLineLayer($dataY0, 0xff0000, "Country AAA");
$layer0->setXData($dataX0);
$layer0->setLineWidth(2);

#Add a blue (0x0000ff) step line layer to the chart and set the line width to 2
#pixels
$layer1 = $c->addStepLineLayer($dataY1, 0xff, "Country BBB");
$layer1->setXData($dataX1);
$layer1->setLineWidth(2);

#output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
