<?php

include("./highlight.class.php") ;

$demo = new Highlighter();

@$rech = $_POST['rech'] ;

$sentence = "<br><br>Each of the following questions begins with a sentence that has either one or two blanks.<br> 
The blanks indicate that a piece of the sentence is missing. Each sentence is followed by five<br> 
answer on the east side choices that consist of words or phrases. Select the answer choice that best completes the sentence<br>
Each of the following questions begins with a sentence that has either one or two blanks.<br> 
The blanks indicate that a piece of the sentence is missing. Each sentence is followed by five<br> 
answer on the east side choices that consist of words or phrases. Select the answer choice that best completes the sentence<br><br>" ;


if($demo->CheckSentence($sentence, $rech))
	echo $demo->CheckSentence($sentence, $rech) ; 
else 
{
	print($sentence) ;
	print("<font style='color:black; background-color:red;'>Nothing match !!</font>") ;
}
	

print("<form action='".$_SERVER['PHP_SELF']."' method='POST'>") ;

print("Give me the word you are looking for : <input type='text' name='rech'>&nbsp;") ;
print("<input type='submit' name='submit' value='Search'>") ;

print("</form>") ;

?>