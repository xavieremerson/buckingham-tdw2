<?php
require_once 'Spreadsheet/Excel/Writer.php';

$workbook = new Spreadsheet_Excel_Writer();
$worksheet =& $workbook->addWorksheet();

$radius = 20;
$worksheet->setColumn(0,$radius*2,1);

// Face
for ($i = 0; $i < 360; $i++)
{
    $worksheet->write(floor(sin((2*pi()*$i)/360)*$radius) + $radius + 1, floor(cos((2*pi()*$i)/360)*$radius) + $radius + 1, "x");
}
// Eyes (maybe use a format instead?)
$worksheet->writeURL(floor($radius*0.8), floor($radius*0.8), "0");
$worksheet->writeURL(floor($radius*0.8), floor($radius*1.2), "0");

// Smile
for ($i = 65; $i < 115; $i++)
{
    $worksheet->write(floor(sin((2*pi()*$i)/360)*$radius*1.3) + floor($radius*0.2), floor(cos((2*pi()*$i)/360)*$radius*1.3) + $radius + 1, "x");
}

// hide gridlines so they don't mess with our Excel art.
$worksheet->hideGridLines();

$workbook->send('face.xls');
$workbook->close();
?>
