						<script type="text/javascript" src="includes/jquery/jquery.js"></script>
            <script type='text/javascript' src='includes/jquery/jquery.autocomplete.js'></script>
            <link rel="stylesheet" type="text/css" href="includes/jquery/jquery.autocomplete.css" />
          
            <input type='text' name="val_symbol" id="ticker" style='font-family:verdana;width:120px;font-size:12px' id='tb' value='<?=$str_val_symbol?>'/> <!--  onFocus="set_val_null('tb')" --> 
						
              <?
              $query_sel_symbol = "SELECT distinct(news_symbol) from news_events
                                    ORDER BY news_symbol";
              $result_sel_symbol = mysql_query($query_sel_symbol) or die(mysql_error());
							$arr_tickers = array();
              $count_row_symbol = 0;
              while($row_sel_symbol = mysql_fetch_array($result_sel_symbol))
              {
                $arr_tickers[] = $row_sel_symbol["news_symbol"];
              }
							$str_tickers = implode('","',$arr_tickers);
						  ?>	
            
            
						<script type="text/javascript">
            $(document).ready(function() {
              $("#ticker").autocompleteArray(
                [
                  <?='"'.$str_tickers.'"'?>
                ],
                {
                  delay:2,
                  minChars:1,
                  matchSubset:1,
                  autoFill:false,
                  maxItemsToShow:10
                }
              );
            });
            </script>
            <script>
            function set_val_null(str_id) { 
              if (document.getElementById(str_id).value == 'Enter Symbol') {
                document.getElementById(str_id).value = ""; 
              }
            }
            </script>
            <?
							if ($val_symbol) {
								$str_val_symbol = $val_symbol;
							} else {
								$str_val_symbol = 'Enter Symbol';
							}
						?>
