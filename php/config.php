<?php 
$servername = "mysql";
$port = 3306;
$username = "root";
$password = "root";
$dbname = "wellness";

$conn = new mysqli($servername, $username, $password, $dbname , $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
