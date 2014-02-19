<?

					// Couple of empty cells to make it look better
					$wks->write(0, 1, "", $format_title);
					$wks->write(1, 1, "", $format_title);
					
					
					$wks->setColumn(0, 0, 4);
					$wks->setColumn(1, 1, 0.2);
					$wks->setColumn(2, 2, 4);
					$wks->setColumn(3, 3, 25);
					$wks->setColumn(4, 4, 0.2);
					$wks->setColumn(5, 6, 14);
					$wks->setColumn(7, 7, 0.2);
					$wks->setColumn(8, 8, 14);
					$wks->setColumn(9, 9, 0.2);
					$wks->setColumn(10, 11, 11);
					
					$wks->setRow(2,30);
					
					$wks->write(0, 2, $arr_months_longnames[$brk_month]." ".$brk_year. " : Sales & Trading Monthly Revenue Report", $format_title_1);

					$wks->write(1, 5, "Gross Revenue", $format_title_2);
					$wks->mergeCells(1,5,1,8);

					$wks->write(2, 2, "REP", $format_title_2);
					$wks->write(2, 3, "SALES", $format_title_2);
					$wks->write(2, 4, "", $format_title_2);
					//$wks->writeString(2, 5, '="Gross.'.chr(10).'Total"', $format_title_3);
					//$wks->writeString(2, 5, "Gross".chr(10)."Commission", $format_title_3);
					$wks->writeString(2, 5, "Commission", $format_title_3);
					
					$wks->write(2, 6, "Checks.", $format_title_3);
					$wks->write(2, 8, "TOTAL", $format_title_3);
?>