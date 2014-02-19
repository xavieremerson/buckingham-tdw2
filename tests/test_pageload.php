<? 
function getmicrotime(){ 
    list($usec, $sec) = explode(" ",microtime()); 
    return ((float)$usec + (float)$sec*1000); 
} 

$time=getmicrotime(); 
sleep(1);
echo "Page was generated in ".sprintf("%01.7f",((getmicrotime()-$time)/1000))." seconds."; 
?> 
