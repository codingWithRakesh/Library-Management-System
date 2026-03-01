<?php
$servername = "localhost:3307";
$username = "root";
$password = "";
$dbname = "liberary_management_system";

$conn = mysqli_connect($servername, $username, $password);

if (!$conn) {
    die("Server connection failed: " . mysqli_connect_error());
}

$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
mysqli_query($conn, $sql);

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}


