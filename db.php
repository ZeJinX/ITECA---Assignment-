<?php

//My Database Configuration

$host = 'localhost';
$username = 'u777866905_admin';
$password = 'ZekePassword1234';
$databse = 'u777866905_user';

//Block that creates the connection
$conn = new mysqli($host, $username, $password, $databse);

//A check to see if the connection works
if ($conn->connect_error)
    {
        die("The Database connection failed: " . $conn->connect_error);
    }

?>