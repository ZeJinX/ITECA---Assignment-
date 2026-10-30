<?php 

//This will resume the current session so the server knows exaclty who is trying to log out
session_start();

//This will erase all the temp data stored in the session variables 
$_SESSION = array();

//This will absolbtely kill the session in the server 
session_destroy();

//This will redirect the user back to the login page 
header("Location: login.php");
exit();

?>