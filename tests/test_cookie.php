<?php
$cookiesSet = array_keys($_COOKIE);
for ($x=0;$x<count($cookiesSet);$x++) setcookie($cookiesSet[$x],"",time()-1);
?>
<?
/*
						setcookie('tdw', "this is a test", 99999999,'testtesttest','192.168.20.63',0);
						
						setcookie( 'test', "aaaaaaaaaaaaaaaaa", time() + 60*60*24*30, '/', '', 0 );
						
						setcookie( 'rtyrtyrty', "bbbbbbbbbbbbb", time() + 60*60*24*30, '/', '', 0 );
						
						setcookie( 'trtyrtest', "cccccccccccccccccc", time() + 60*60*24*30, '/', '', 0 );
						
						setcookie( 'tertyrtyst', "dddddddddddddd", time() + 60*60*24*30, '/', '', 0 );
						
						setcookie( 'tesrtyrtyt', "eeeeeeeeeeeeeee", time() + 60*60*24*30, '/', '', 0 );
						
						setcookie( 'testrtyrty', "zzzzzzzzzzzzzzzz", time() + 15, '/', '', 0 );
						
						print_r($_COOKIE);
						
*/						
?>