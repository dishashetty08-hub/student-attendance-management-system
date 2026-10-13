<?php

session_start();

/* Remove all session data */
$_SESSION = array();

/* Destroy the session */
session_destroy();

/* Send user back to login */
header("Location: index.php");
exit();

?>