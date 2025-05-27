<?php
$conn = new mysqli("localhost", "Username", "Password", "ratingsite");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$site = $_POST['site'];
$rating = floatval($_POST['rating']);
$ip = $_SERVER['REMOTE_ADDR']; // Use IP to separate guest votes

// Insert the guest vote
$insert = $conn->prepare("INSERT INTO guest_votes (site, rating, ip_address) VALUES (?, ?, ?)");
$insert->bind_param("sds", $site, $rating, $ip);
$insert->execute();
$insert->close();

// Calculate new average community rating from all guest votes
$avgQuery = $conn->prepare("SELECT AVG(rating) FROM guest_votes WHERE site = ?");
$avgQuery->bind_param("s", $site);
$avgQuery->execute();
$avgQuery->bind_result($average);
$avgQuery->fetch();
$avgQuery->close();

// Update the community_rating in news_ratings
// First check if the site exists
$check = $conn->prepare("SELECT id FROM news_ratings WHERE site = ?");
$check->bind_param("s", $site);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    $update = $conn->prepare("UPDATE news_ratings SET community_rating = ? WHERE site = ?");
    $update->bind_param("ds", $average, $site);
    $update->execute();
    $update->close();
} else {
    $insertNews = $conn->prepare("INSERT INTO news_ratings (site, community_rating) VALUES (?, ?)");
    $insertNews->bind_param("sd", $site, $average);
    $insertNews->execute();
    $insertNews->close();
}

$check->close();
$conn->close();

echo "Success";
?>

