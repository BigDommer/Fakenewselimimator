<?php
$conn = new mysqli("localhost", "Username", "Password", "ratingsite");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$site = $_POST['site'];
$rating = $_POST['rating'];

$sql = "REPLACE INTO professional_ratings (site, rating) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sd", $site, $rating);
$stmt->execute();
$stmt->close();
$conn->close();

echo "Success";
?>
