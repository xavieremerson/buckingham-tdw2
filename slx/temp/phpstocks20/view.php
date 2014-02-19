<?php 
	include('./quotes.php');
	include('./html.php');
	$html = new HTML;
	$quotes = new Quotes;
class views {
	/******************************************************/
	/* This function generates a basic view of the quotes */
	/******************************************************/
	function basic($symbol) {
		global $quotes;
		global $html;
    		  /* go get associative array from quote.php */
    		  $hash = $quotes->yahoo($symbol);
    		  echo $hash[RHAT][company];
		  $html->header($hash);
		/*  $html->basic($hash); /* print out basic info */
	
	}

	/*********************************************************/
	/* This function generates a detailed view of the quotes */
	/*********************************************************/
	function detailed($symbol) {
		global $quotes;
		global $html;
		
		/* go get associative array from quote.php */
		$hash = $quotes->yahoo($symbol);
		$html->header(); /* generic header for stocks */
		$html->detailed($hash); /* print out detailed info */
	
	
	}

}
?>
	
	
