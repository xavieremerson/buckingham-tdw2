<?php
include("phpchartdir.php");

#The XY data of the first data series
$dataX0 = array(50, 55, 37, 24, 42, 49, 63, 72, 83, 59);
$dataY0 = array(3.6, 2.8, 2.5, 2.3, 3.8, 3.0, 3.8, 5.0, 6.0, 3.3);

#The XY data of the second data series
$dataX1 = array(50, 55, 37, 24, 42, 49, 63, 72, 83, 59);
$dataY1 = array(1.6, 1.8, 0.8, 0.5, 1.3, 1.5, 2.3, 2.4, 2.9, 1.5);

#Create a XYChart object of size 450 x 420 pixels
$c = new XYChart(450, 420);

#Set the plotarea at (55, 65) and of size 350 x 300 pixels, with white
#background and a light grey border (0xc0c0c0). Turn on both horizontal and
#vertical grid lines with light grey color (0xc0c0c0)
$c->setPlotArea(55, 65, 350, 300, 0xffffff, -1, 0xc0c0c0, 0xc0c0c0, -1);

#Add a legend box at (50, 30) (top of the chart) with horizontal layout. Use 12
#pts Times Bold Italic font. Set the background and border color to Transparent.
$legendObj = $c->addLegend(50, 30, false, "timesbi.ttf", 12);
$legendObj->setBackground(Transparent);

#Add a title to the chart using 18 point Times Bold Itatic font.
$c->addTitle("Server Performance", "timesbi.ttf", 18);

#Add a title to the y axis using 12 pts Arial Bold Italic font
$c->yAxis->setTitle("Response Time (sec)", "arialbi.ttf", 12);

#Set the y axis line width to 3 pixels
$c->yAxis->setWidth(3);

#Set the y axis label format to show 1 decimal point
$c->yAxis->setLabelFormat("{value|1}");

#Add a title to the x axis using 12 pts Arial Bold Italic font
$c->xAxis->setTitle("Server Load (TPS)", "arialbi.ttf", 12);

#Set the x axis line width to 3 pixels
$c->xAxis->setWidth(3);

#Add a scatter layer using (dataX0, dataY0)
$c->addScatterLayer($dataX0, $dataY0, "Server AAA", DiamondSymbol, 11, 0x8000);

#Add a trend line layer for (dataX0, dataY0)
$trendLayer2Obj = $c->addTrendLayer2($dataX0, $dataY0, 0x8000);
$trendLayer2Obj->setLineWidth(3);

#Add a scatter layer for (dataX1, dataY1)
$c->addScatterLayer($dataX1, $dataY1, "Server BBB", TriangleSymbol, 9, 0x6666ff)
    ;

#Add a trend line layer for (dataX1, dataY1)
$trendLayer2Obj = $c->addTrendLayer2($dataX1, $dataY1, 0x6666ff);
$trendLayer2Obj->setLineWidth(3);

#output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
