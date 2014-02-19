<?php

class Highlighter
{
    //$sentence is the sentence that you are looking for
    //$rech is the word you searching in the sentence
	function CheckSentence($sentence, $rech)
	{	
		$len = strlen($rech) ;
		
		if ($len != 0) 
		{
			$find = $sentence;
		
			while ($find = stristr($find, $rech)) // find $search text - case insensitiv
			{	
				$txt = substr($find, 0, $len);	// get new search text 
				
				$find = substr($find, $len);
				
				$subject = str_replace($txt, "<font style='color:black; background-color:red;'>" . $txt ."</font>", $sentence);
			}
		}			
		// depend what you need. i used a return just for the demo page
        return @$subject ;
        //echo $subject ;
	}
}

?>