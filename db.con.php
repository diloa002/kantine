<?php

// Databaseforbindelsesparametere
// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "kantine";

// Opprett forbindelse
// $conn = new mysqli($servername, $username, $password, $dbname);



$hostname = "localhost:3306";   
$user = "root";
$password = "";
$db = "kantine";


$conn = mysqli_connect($hostname, $user, $password, $db);

// Sjekk tilkobling
 if ($conn->connect_error) {
     die("Connection failed: " . $conn->connect_error);
 }

?>