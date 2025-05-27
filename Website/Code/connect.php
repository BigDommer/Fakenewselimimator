<?php
$host = "localhost";
$user = "Username"; // full cPanel MySQL username
$pass = "Password";           // the password you used
$db   = "ratingsite"; // full cPanel MySQL database name

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo "Failed to connect DB: " . $conn->connect_error;
}
?>
