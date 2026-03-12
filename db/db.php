<?php
$servername = "localhost";
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

// echo "<script>alert('Connected successfully to the database.');</script>";


    $checkTable = mysqli_query($conn, "SHOW TABLES LIKE 'students'");
        if (mysqli_num_rows($checkTable) == 0) {

        $sqlFile = __DIR__ . "/seed.sql";

        if (file_exists($sqlFile)) {

            $sql = file_get_contents($sqlFile);

            if ($conn->multi_query($sql)) {

                do {
                    // flush multi queries
                } while ($conn->next_result());

            } else {

                echo "Error importing SQL file: " . $conn->error;
            }
        }
    }
?>