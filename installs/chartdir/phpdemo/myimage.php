<?php
session_start();
print $HTTP_SESSION_VARS[$HTTP_GET_VARS["img"]];
?>
