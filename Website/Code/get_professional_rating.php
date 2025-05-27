<?php
$conn = new mysqli("localhost", "Username", "Password", "ratingsite");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$site = $_GET['site'];

$sql = "SELECT rating FROM professional_ratings WHERE site=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $site);
$stmt->execute();
$stmt->bind_result($rating);
$stmt->fetch();
$stmt->close();
$conn->close();

echo $rating;
?>
