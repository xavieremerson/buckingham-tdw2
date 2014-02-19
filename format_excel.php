<?
$title_1 =& $wkb->addFormat();
$title_1->setBold();
$title_1->setPattern(0);
$title_1->setFontFamily('Arial'); 
$title_1->setSize('14');

$title_2 =& $wkb->addFormat();
$title_2->setBold();
$title_2->setPattern(0);
$title_2->setFontFamily('Arial'); 
$title_2->setSize('9');
$title_2->setAlign('center');


$title_3 =& $wkb->addFormat();
$title_3->setBold();
$title_3->setTextWrap();
$title_3->setPattern(0);
$title_3->setFontFamily('Arial'); 
$title_3->setSize('8');
$title_3->setAlign('center');

$title_4 =& $wkb->addFormat();
$title_4->setBold();
$title_4->setTextWrap();
$title_4->setPattern(0);
$title_4->setFontFamily('Arial'); 
$title_4->setSize('7');
$title_4->setAlign('center');

$data_1 =& $wkb->addFormat();
$data_1->setPattern(0);
$data_1->setFontFamily('Arial Narrow'); 
$data_1->setSize('9');

$data_2 =& $wkb->addFormat();
$data_2->setPattern(0);
$data_2->setFontFamily('Arial'); 
$data_2->setSize('9');
$data_2->setAlign('right');

$data_3 =& $wkb->addFormat();
$data_3->setBold();
$data_3->setPattern(0);
$data_3->setFontFamily('Arial'); 
$data_3->setSize('9');

$data_4 =& $wkb->addFormat();
$data_4->setTextWrap();
$data_4->setPattern(0);
$data_4->setFontFamily('Arial'); 
$data_4->setSize('7');
$data_4->setAlign('right');


$currency_1 =& $wkb->addFormat();
$currency_1->setPattern(0);
$currency_1->setFontFamily('Arial'); 
$currency_1->setSize('9');
$currency_1->setNumFormat('#,##0.00;(#,##0.00)');

$currency_2 =& $wkb->addFormat();
$currency_2->setBold();
$currency_2->setPattern(0);
$currency_2->setFontFamily('Arial'); 
$currency_2->setSize('9');
$currency_2->setNumFormat('#,##0.00;(#,##0.00)');

$currency_3 =& $wkb->addFormat();
$currency_3->setPattern(0);
$currency_3->setFontFamily('Arial'); 
$currency_3->setSize('8');
$currency_3->setNumFormat('#,##0.00;(#,##0.00)');
$currency_3->setColor('gray'); 

$currency_4 =& $wkb->addFormat();
$currency_4->setPattern(0);
$currency_4->setFontFamily('Arial'); 
$currency_4->setSize('9');
$currency_4->setNumFormat('#,##0;(#,##0)');

$currency_4b =& $wkb->addFormat();
$currency_4b->setBold();
$currency_4b->setPattern(0);
$currency_4b->setFontFamily('Arial'); 
$currency_4b->setSize('9');
$currency_4b->setNumFormat('#,##0;(#,##0)');

$num0 =& $wkb->addFormat();
$num0->setPattern(0);
$num0->setFontFamily('Arial'); 
$num0->setSize('8');
$num0->setNumFormat('#,##0');
$num0->setAlign('right');

?>