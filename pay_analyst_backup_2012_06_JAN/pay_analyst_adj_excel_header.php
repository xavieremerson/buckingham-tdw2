<?
					// Couple of empty cells to make it look better
					$wks->write(0, 1, "", $format_title);
					$wks->write(1, 1, "", $format_title);
					
					
					$wks->setColumn(0, 0, 24);
					/*
					for($colval=1;$colval<(count($arr_master_clnt_rr)+1);$colval++)  {
						$wks->setColumn(0, $colval, 20);
					}

					$wks->setColumn(0, (count($arr_master_clnt_rr)+1), 4);
					$wks->setColumn(0, (count($arr_master_clnt_rr)+2), 24);

					*/

					//xdebug();
					//exit;
					/*
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
					$wks->setColumn(13, 13, 11);
					$wks->setColumn(14, 16, 8);
					$wks->setColumn(17, 17, 6);
					$wks->setColumn(18, 18, 0.2);
					$wks->setColumn(19, 27, 11);
					*/
					
					$wks->setRow(2,20);
					
					$wks->write(0, 0, "Q".$sel_qtr." ".$sel_year, $format_title_1);

					/*
					$wks->write(2, 2, "REP", $format_title_2);
					$wks->write(2, 3, "NAME", $format_title_2);
					$wks->write(2, 4, "", $format_title_2);
					$wks->writeString(2, 5, "Commission", $format_title_3);
					
					$wks->write(2, 6, "Checks.", $format_title_3);
					$wks->write(2, 8, "TOTAL", $format_title_3);
					$wks->write(2, 10, "Standard", $format_title_3);
					$wks->write(2, 11, "Special", $format_title_3);

					$xyz = "Rolling 12mon".chr(10).'< $15K';
					//echo $xyz;
					*/
?>