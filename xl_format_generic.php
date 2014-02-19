<?

$format_bold =& $wkb->addFormat();
$format_bold->setBold();

$format_heading =& $wkb->addFormat();
$format_heading->setBold();

$format_title_1 =& $wkb->addFormat();
$format_title_1->setBold();
$format_title_1->setPattern(0);
$format_title_1->setFontFamily('Arial'); 
$format_title_1->setSize('10');

$format_title_2 =& $wkb->addFormat();
$format_title_2->setBold();
$format_title_2->setPattern(0);
$format_title_2->setFontFamily('Arial'); 
$format_title_2->setSize('9');
$format_title_2->setAlign('center');


$format_title_3 =& $wkb->addFormat();
$format_title_3->setBold();
$format_title_3->setTextWrap();
$format_title_3->setPattern(0);
$format_title_3->setFontFamily('Arial'); 
$format_title_3->setSize('8');
$format_title_3->setAlign('center');

$format_title_4 =& $wkb->addFormat();
$format_title_4->setBold();
$format_title_4->setTextWrap();
$format_title_4->setPattern(0);
$format_title_4->setFontFamily('Arial'); 
$format_title_4->setSize('7');
$format_title_4->setAlign('center');

$format_data_1 =& $wkb->addFormat();
$format_data_1->setPattern(0);
$format_data_1->setFontFamily('Arial Narrow'); 
$format_data_1->setSize('9');

$format_data_2 =& $wkb->addFormat();
$format_data_2->setPattern(0);
$format_data_2->setFontFamily('Arial'); 
$format_data_2->setSize('9');
$format_data_2->setAlign('right');

$format_data_3 =& $wkb->addFormat();
$format_data_3->setBold();
$format_data_3->setPattern(0);
$format_data_3->setFontFamily('Arial'); 
$format_data_3->setSize('9');

$format_currency_1 =& $wkb->addFormat();
$format_currency_1->setPattern(0);
$format_currency_1->setFontFamily('Arial'); 
$format_currency_1->setSize('9');
$format_currency_1->setNumFormat('#,##0.00;(#,##0.00)');

$format_currency_2 =& $wkb->addFormat();
$format_currency_2->setBold();
$format_currency_2->setPattern(0);
$format_currency_2->setFontFamily('Arial'); 
$format_currency_2->setSize('9');
$format_currency_2->setNumFormat('#,##0.00;(#,##0.00)');

$format_currency_3 =& $wkb->addFormat();
$format_currency_3->setPattern(0);
$format_currency_3->setFontFamily('Arial'); 
$format_currency_3->setSize('8');
$format_currency_3->setNumFormat('#,##0.00;(#,##0.00)');
$format_currency_3->setColor('gray'); 

$format_currency_0 =& $wkb->addFormat();
$format_currency_0->setPattern(0);
$format_currency_0->setFontFamily('Arial'); 
$format_currency_0->setSize('9');
$format_currency_0->setNumFormat('#,##0;(#,##0)');

$format_text_1 =& $wkb->addFormat();
$format_text_1->setPattern(0);
$format_text_1->setFontFamily('Arial'); 
$format_text_1->setSize('9');
$format_text_1->setAlign('left');


?>