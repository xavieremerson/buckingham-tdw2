<?

$format_bold =& $wkb->addFormat();
$format_bold->setBold();

$format_heading =& $wkb->addFormat();
$format_heading->setBold();

/*
$format_title_1 =& $wkb->addFormat();
$format_title_1->setBold();
$format_title_1->setPattern(0);
$format_title_1->setFontFamily('Arial'); 
//$format_title_1->setTop(1); // Top border 
//$format_title_1->setBottom(1); // Bottom border 
$format_title_1->setSize('9');
$format_title_1->setBold(); 
//$format_title_1->setColor('red'); 
// And since our title is going to be so big, we'll merge a few cells to account for it.<br />
//$format_title_1->setAlign('merge');
$format_title->setAlign('center');
*/

$format_title_1 =& $wkb->addFormat();
$format_title_1->setBold();
$format_title_1->setPattern(0);
$format_title_1->setFontFamily('Arial'); 
$format_title_1->setSize('14');

$format_title_2 =& $wkb->addFormat();
$format_title_2->setBold();
$format_title_2->setPattern(0);
$format_title_2->setFontFamily('Arial'); 
$format_title_2->setSize('9');
$format_title_2->setAlign('center');

$bold10right =& $wkb->addFormat();
$bold10right->setBold();
$bold10right->setFontFamily('Arial'); 
$bold10right->setSize('10');
$bold10right->setAlign('left');

$arial8bold =& $wkb->addFormat();
$arial8bold->setBold();
$arial8bold->setFontFamily('Arial'); 
$arial8bold->setSize('8');
$arial8bold->setAlign('left');

$arial8 =& $wkb->addFormat();
$arial8->setFontFamily('Arial'); 
$arial8->setSize('8');
$arial8->setAlign('left');

$arial7 =& $wkb->addFormat();
$arial7->setFontFamily('Arial'); 
$arial7->setSize('7');
$arial7->setAlign('left');

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
$format_data_1->setFontFamily('Arial'); 
$format_data_1->setSize('10');

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

$format_data_4 =& $wkb->addFormat();
$format_data_4->setTextWrap();
$format_data_4->setPattern(0);
$format_data_4->setFontFamily('Arial'); 
$format_data_4->setSize('7');
$format_data_4->setAlign('right');


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
$format_currency_2->setNumFormat('$#,##0.00;($#,##0.00)');

$format_currency_3 =& $wkb->addFormat();
$format_currency_3->setPattern(0);
$format_currency_3->setFontFamily('Arial'); 
$format_currency_3->setSize('8');
$format_currency_3->setNumFormat('#,##0.00;(#,##0.00)');
$format_currency_3->setColor('gray'); 

$format_currency_4 =& $wkb->addFormat();
$format_currency_4->setPattern(0);
$format_currency_4->setFontFamily('Arial'); 
$format_currency_4->setSize('9');
$format_currency_4->setNumFormat('#,##0;(#,##0)');

$format_currency_4b =& $wkb->addFormat();
$format_currency_4b->setBold();
$format_currency_4b->setPattern(0);
$format_currency_4b->setFontFamily('Arial'); 
$format_currency_4b->setSize('9');
$format_currency_4b->setNumFormat('#,##0;(#,##0)');

$format_num0 =& $wkb->addFormat();
$format_num0->setPattern(0);
$format_num0->setFontFamily('Arial'); 
$format_num0->setSize('11');
$format_num0->setNumFormat('#,##0');
$format_num0->setAlign('right');

$format_num1 =& $wkb->addFormat();
$format_num1->setPattern(0);
$format_num1->setFontFamily('Arial'); 
$format_num1->setSize('10');
$format_num1->setNumFormat('#,##0');
$format_num1->setAlign('right');

$format_currency_arial8 =& $wkb->addFormat();
$format_currency_arial8->setAlign('right');
$format_currency_arial8->setFontFamily('Arial'); 
$format_currency_arial8->setSize('8');
$format_currency_arial8->setNumFormat('$#,##0.00;($#,##0.00)');

$curr_arial8b =& $wkb->addFormat();
$curr_arial8b->setBold();
$curr_arial8b->setPattern(0);
$curr_arial8b->setFontFamily('Arial'); 
$curr_arial8b->setSize('8');
$curr_arial8b->setNumFormat('$#,##0.00;($#,##0.00)');


$format_adjplus_arial8 =& $wkb->addFormat();
$format_adjplus_arial8->setAlign('right');
$format_adjplus_arial8->setFontFamily('Arial'); 
$format_adjplus_arial8->setSize('8');
$format_adjplus_arial8->setNumFormat('$#,##0.00;($#,##0.00)');
$format_adjplus_arial8->setBold();
$format_adjplus_arial8->setColor('blue'); 

$format_adjminus_arial8 =& $wkb->addFormat();
$format_adjminus_arial8->setAlign('right');
$format_adjminus_arial8->setFontFamily('Arial'); 
$format_adjminus_arial8->setSize('8');
$format_adjminus_arial8->setNumFormat('$#,##0.00;($#,##0.00)');
$format_adjminus_arial8->setBold();
$format_adjminus_arial8->setColor('red'); 

$format_adj =& $wkb->addFormat();
$format_adj->setAlign('left');
$format_adj->setFontFamily('Arial'); 
$format_adj->setSize('8');
$format_adj->setBold();
$format_adj->setColor('blue'); 

/*

$wks->write(2, 8, "COMMISSIONS", $format_heading);
$wks->write(2, 11, "Concession/", $format_heading);
$wks->write(2, 14, "C H E C K S", $format_heading);



$wks->write(3, 3, "Type", $format_heading);
$wks->write(3, 4, "Rep", $format_heading);
$wks->write(3, 5, "Name", $format_heading);
$wks->write(3, 6, "");
$wks->write(3, 7, "Sole", $format_heading);
$wks->write(3, 8, "");
$wks->write(3, 9, "Shared", $format_heading);
$wks->write(3, 10, "");
$wks->write(3, 11, "Facilitation", $format_heading);
$wks->write(3, 12, "");
$wks->write(3, 13, "Sole", $format_heading);
$wks->write(3, 14, "");
$wks->write(3, 15, "Shared", $format_heading);
$wks->write(3, 16, "");
$wks->write(3, 17, "20%", $format_heading);
$wks->write(3, 18, "10%", $format_heading);
$wks->write(3, 19, "22%", $format_heading);
$wks->write(3, 20, "Other", $format_heading);
$wks->write(3, 21, "Adj.", $format_heading);
$wks->write(3, 22, "");
$wks->write(3, 23, "");


			$query_trades = "SELECT 
													trad_rr,
													FORMAT(sum(trad_commission),2) as trad_commission,
													sum(trad_commission) as for_sum_trad_commission
												FROM mry_comm_rr_trades 
												WHERE trad_is_cancelled = 0 
												AND trad_settle_date between '".$brk_start_date."' AND '".$brk_end_date."'
												AND trad_rr like '0%'												
												GROUP BY trad_rr 
												ORDER BY trad_rr";
			//xdebug("query_trades",$query_trades);
			//exit;
			
			$result_trades = mysql_query($query_trades) or die (tdw_mysql_error($query_trades));

			$i = 5;
			while ($row_trades = mysql_fetch_array($result_trades) ) 
			{
				$wks->write($i, 3, "Sole");
				$wks->write($i, 4, ' '.$row_trades["trad_rr"].' ');
				$wks->write($i, 5, get_repname_by_rr_num($row_trades["trad_rr"]),$format_bold);
				$wks->write($i, 7, $row_trades["for_sum_trad_commission"]);
				$i++;


			}
*/


?>