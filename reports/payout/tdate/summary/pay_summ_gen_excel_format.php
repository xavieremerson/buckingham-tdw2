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
$format_currency_2->setNumFormat('#,##0.00;(#,##0.00)');

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
$format_num0->setSize('8');
$format_num0->setNumFormat('#,##0');
$format_num0->setAlign('right');
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
												AND trad_trade_date between '".$brk_start_date."' AND '".$brk_end_date."'
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