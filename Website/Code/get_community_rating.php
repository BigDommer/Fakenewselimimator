<?php
$conn = new mysqli("localhost", "Username", "Password", "ratingsite");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['site'])) {
    $site = $_GET['site'];
    $stmt = $conn->prepare("SELECT AVG(community_rating) as avg_rating FROM news_ratings WHERE site = ?");
    $stmt->bind_param("s", $site);
    $stmt->execute();
    $stmt->bind_result($avg_rating);
    $stmt->fetch();
    $stmt->close();

    echo $avg_rating !== null ? round($avg_rating, 2) : 0;
}
?>
