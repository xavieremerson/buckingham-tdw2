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
					$wks->setColumn(3, 3, 25);
					$wks->setColumn(4, 4, 0.2);
					$wks->setColumn(5, 6, 11);
					$wks->setColumn(7, 7, 0.2);
					$wks->setColumn(8, 8, 11);
					$wks->setColumn(9, 9, 0.2);
					$wks->setColumn(10, 11, 11);
					$wks->setColumn(12, 12, 0.2);
					$wks->setColumn(13, 15, 8);
					$wks->setColumn(16, 16, 6);
					$wks->setColumn(17, 17, 0.2);
					$wks->setColumn(18, 26, 11);
					
					$wks->setRow(2,30);
					
					$wks->write(0, 2, $arr_months_longnames[$brk_month]." ".$brk_year, $format_title_1);

					$wks->write(1, 5, "Gross Revenue", $format_title_2);
					$wks->mergeCells(1,5,1,8);

					$wks->write(1, 10, "Gross Payout", $format_title_2);
					$wks->mergeCells(1,10,1,11);

					$wks->write(1, 13, "Adjustments", $format_title_2);
					$wks->mergeCells(1,13,1,16);
					
					$wks->write(1, 18, $brk_month." ".$brk_year." Gross Payout", $format_title_2);
					$wks->mergeCells(1,18,1,21);

					$wks->write(2, 2, "REP", $format_title_2);
					$wks->write(2, 3, "NAME", $format_title_2);
					$wks->write(2, 4, "", $format_title_2);
					//$wks->writeString(2, 5, '="Gross.'.chr(10).'Total"', $format_title_3);
					//$wks->writeString(2, 5, "Gross".chr(10)."Commission", $format_title_3);
					$wks->writeString(2, 5, "Commission", $format_title_3);
					
					$wks->write(2, 6, "Checks.", $format_title_3);
					$wks->write(2, 8, "TOTAL", $format_title_3);
					$wks->write(2, 10, "Standard", $format_title_3);
					$wks->write(2, 11, "Special", $format_title_3);

					//$xyz = "Rolling 12mon".chr(10).'< $15K';
					//$wks->writeString(2, 13, $xyz, $format_title_4);

					$wks->write(2, 13, "TW", $format_title_3);
					$wks->write(2, 14, "Non Payout", $format_title_3);
					$wks->write(2, 15, "Other", $format_title_3);
					$wks->write(2, 16, "FN".chr(10)."#", $format_title_3);
					$wks->write(2, 18, "Sole", $format_title_4);
					$wks->write(2, 19, "Shared", $format_title_4);
					$wks->write(2, 20, "Total", $format_title_4);
					$wks->write(2, 21, "Monthly Draw", $format_title_4);
					$wks->write(2, 22, "Overage", $format_title_4);
					$wks->write(2, 23, "Hold", $format_title_4);
					$wks->write(2, 24, "Payout", $format_title_4);
					$wks->write(2, 25, "Lastname", $format_title_4);
?>