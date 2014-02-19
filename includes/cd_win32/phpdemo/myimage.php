<?php
if (!session_id()) { session_start(); }
session_cache_limiter("private_no_expire"); 
if (isset($_GET))
	print $_SESSION[$_GET["img"]];
else
	print $HTTP_SESSION_VARS[$HTTP_GET_VARS["img"]];
?>