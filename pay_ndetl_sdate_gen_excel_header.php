<?
					$arr_months_longnames = array();
					$arr_months_longnames["Jan"] = 'January';
					$arr_months_longnames["Feb"] = 'February';
					$arr_months_longnames["Mar"] = 'March';
					$arr_months_longnames["Apr"] = 'April';
					$arr_months_longnames["May"] = 'May';
					$arr_months_longnames["Jun"] = 'June';
					$arr_months_longnames["Jul"] = 'July';
					$arr_months_longnames["Aug"] = 'August';
					$arr_months_longnames["Sep"] = 'September';
					$arr_months_longnames["Oct"] = 'October';
					$arr_months_longnames["Nov"] = 'November';
					$arr_months_longnames["Dec"] = 'December';

					// Couple of empty cells to make it look better
					$wks->write(0, 1, "", $format_title);
					$wks->write(1, 1, "", $format_title);
					
					
					$wks->setColumn(0, 0, 4);
					$wks->setColumn(1, 1, 0.2);
					$wks->setColumn(2, 2, 4);
					$wks->setColumn(3, 3, 4);
					$wks->setColumn(4, 4, 25);
					$wks->setColumn(5, 5, 0.2);
					$wks->setColumn(6, 8, 11);
					$wks->setColumn(9, 9, 0.2);
					$wks->setColumn(10, 10, 11);
					$wks->setColumn(11, 11, 8);
					$wks->setColumn(12, 12, 11);
					$wks->setColumn(13, 13, 0.2);
					$wks->setColumn(14, 16, 11);
					$wks->setColumn(17, 17, 4);
					$wks->setColumn(18, 18, 0.2);
					$wks->setColumn(19, 19, 11);
					$wks->setColumn(20, 20, 0.2);
					$wks->setColumn(21, 21, 11);
					
					$wks->setRow(2,30);
					
					$wks->write(0, 2, $arr_months_longnames[$brk_month]." ".$brk_year, $format_title_1);
					$wks->mergeCells(0,2,0,4);
					$wks->write(0, 10, "Gross Payout", $format_title_2);
					$wks->mergeCells(0,10,0,12);
					$wks->write(1, 2, "TYPE", $format_title_2);
					$wks->write(1, 3, "REP", $format_title_2);
					$wks->write(1, 4, "NAME", $format_title_2);
					$wks->write(1, 10, $str_label_payout_rate, $format_title_2);
					$wks->write(1, 11, "Rate", $format_title_2);
					$wks->write(1, 12, "Amount", $format_title_2);
					$wks->write(1, 14, "Adjustments", $format_title_2);
					$wks->mergeCells(1,14,1,17);
					$wks->write(1, 19, "Pay Out For:", $format_title_2);
					$wks->write(1, 21, "Pay Out For:", $format_title_2);
					$wks->write(2, 6, "Comm.", $format_title_3);
					$wks->write(2, 7, "Checks", $format_title_3);
					$wks->write(2, 8, "Total", $format_title_3);
					$wks->write(2, 10, "Standard", $format_title_3);
					$wks->write(2, 11, "Special", $format_title_2);
					$wks->mergeCells(2,11,2,12);
					$xyz = '="Rolling 12mon.'.chr(10).'< $15K"';
					//echo $xyz;
					$wks->writeFormula(2, 14, $xyz, $format_title_4);
					$wks->write(2, 15, "TW", $format_title_3);
					$wks->write(2, 16, "Other", $format_title_3);
					$wks->write(2, 17, "FN".chr(10)."#", $format_title_4);
					$wks->write(2, 19, $brk_month." ".$brk_year, $format_title_2);
					$wks->write(2, 21, "YTD 2007", $format_title_2);
?>