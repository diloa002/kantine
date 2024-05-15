<?php

// Databaseforbindelsesparametere
$servername = "localhost";
$username = "root";
$password = "Test";
$dbname = "kantine";

// Opprett forbindelse
$conn = new mysqli($servername, $username, $password, $dbname);

// Sjekk tilkobling
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
