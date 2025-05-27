<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "Username", "Password", "ratingsite");

if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed"]);
    exit;
}

$site = $_GET['site'] ?? '';

$community = 0;
$professional = 0;

// Fetch average guest vote
$stmt = $conn->prepare("SELECT AVG(rating) FROM guest_votes WHERE site = ?");
$stmt->bind_param("s", $site);
$stmt->execute();
$stmt->bind_result($community);
$stmt->fetch();
$stmt->close();

// Fetch professional rating
$stmt2 = $conn->prepare("SELECT rating FROM professional_ratings WHERE site = ?");
$stmt2->bind_param("s", $site);
$stmt2->execute();
$stmt2->bind_result($professional);
$stmt2->fetch();
$stmt2->close();

$conn->close();

echo json_encode([
    "community" => round($community, 2),
    "professional" => $professional ?? 0
]);
