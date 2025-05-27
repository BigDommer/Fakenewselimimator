<?php
session_start(); // <== ADD THIS if it's not already present

$conn = new mysqli("localhost", "Username", "Password", "ratingsite");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$averages = [];
$result = $conn->query("SELECT site, AVG(community_rating) as avg_rating FROM news_ratings GROUP BY site");
while ($row = $result->fetch_assoc()) {
    $averages[$row['site']] = round($row['avg_rating'], 2);
}
?>


<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>News Tables</title>
    <link rel="stylesheet" href="Login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
      /* If you have a light background, no need for a text shadow. */
      background: url("images/WebsiteBackground.jpg") no-repeat center center/cover;
      min-height: 100vh;
    }
    h1 {
      text-align: center;
      margin-bottom: 20px;
      /* Changed from #fff to #222 for better readability */
      color: #162938;
      /* Remove or lighten the text-shadow if needed */
      /* text-shadow: 1px 1px 2px #000; */
    }
    .news-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 30px;
      background: rgba(255, 255, 255, 0.8);
    }
    .news-table th, .news-table td {
      border: 1px solid #162938;
      padding: 8px;
      text-align: left;
      color: #000;
    }
    .news-table th {
      background-color: #162938;
      color: #fff;
      font-size: 1.1rem;
    }
    /* Makes inputs look like plain text */
    .news-table input[type="text"],
    .news-table input[type="number"] {
      width: 100%;
      border: none;
      background: transparent;
      font-size: 1rem;
      padding: 6px;
      color: #000;
    }

    /* Removes outline glow and sets cleaner focus style */
    .news-table input[type="text"]:focus,
    .news-table input[type="number"]:focus {
      outline: none;
      background-color: rgba(255, 255, 255, 0.7);
      border-bottom: 1px solid #162938;
    }

    /* Optional: subtle hover effect */
    .news-table input:hover {
      background-color: rgba(255, 255, 255, 0.2);
      cursor: text;
    }
    .star-rating {
      display: inline-flex;
      cursor: pointer;
    }
    .star-rating i {
      font-size: 1.2rem;
      color: #ccc;
      transition: color 0.2s;
    }
    .star-rating i.filled {
      color: #ffc107;
    }
    .overall-stars i {
      color: #ffc107;
      font-size: 1.2rem;
    }
    </style>

<div style="text-align: right; padding: 10px;">
    <a href="logout.php" style="
        padding: 10px 20px;
        background-color: #162938;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
    ">Logout</a>
</div>

</head>
<body>

<h1 style="text-align:center;">News Rating Tables</h1>

   <!-- NBC TABLE -->
<table id="nbcTable" class="news-table">
  <tr><th colspan="2">NBC News Ratings</th></tr>
  <tr><td>Credibility</td><td>4 Stars</td></tr>
  <tr><td>Reliability</td><td>5 Stars</td></tr>
  <tr><td>Bias</td><td>Middle Bias</td></tr>
  <tr><td>Community Rating</td><td>
  <div class="star-rating" data-site="nbc" data-type="community"></div>
  </td></tr>
  <tr>
  <td>Professional Rating</td>
  <td>
  <div class="star-rating" data-site="nbc" data-type="professional"></div>
  </td>
  </tr>
  <tr><td>Overall Rating</td>
  <td>
  <div class="overall-stars" data-rating="<?php
    $community = $averages['nbc'] ?? null;
    $stmt = $conn->prepare("SELECT rating FROM professional_ratings WHERE site = ?");
    $siteName = 'nbc';
    $stmt->bind_param("s", $siteName);
    $stmt->execute();
    $stmt->bind_result($pro);
    $stmt->fetch();
    $stmt->close();

    if ($community !== null && $pro !== null) {
      echo round(($community + $pro) / 2, 1);
    } else {
      echo "0";
    }
  ?>"></div>
  </td></tr>
</table>

<!-- FOX TABLE -->
<table id="foxTable" class="news-table">
  <tr><th colspan="2">FOX News Ratings</th></tr>
  <tr><td>Credibility</td><td>2 Stars</td></tr>
  <tr><td>Reliability</td><td>4 Stars</td></tr>
  <tr><td>Bias</td><td>Right Leaning</td></tr>
  <tr><td>Community Rating</td><td>
  <div class="star-rating" data-site="fox" data-type="community"></div>
  </td></tr>
  <tr>
  <td>Professional Rating</td>
  <td>
  <div class="star-rating" data-site="fox" data-type="professional"></div>
  </td>
  </tr>
  <tr><td>Overall Rating</td>
  <td>
  <div class="overall-stars" data-rating="<?php
    $community = $averages['fox'] ?? null;
    $stmt = $conn->prepare("SELECT rating FROM professional_ratings WHERE site = ?");
    $siteName = 'fox';
    $stmt->bind_param("s", $siteName);
    $stmt->execute();
    $stmt->bind_result($pro);
    $stmt->fetch();
    $stmt->close();

    if ($community !== null && $pro !== null) {
      echo round(($community + $pro) / 2, 1);
    } else {
      echo "0";
    }
  ?>"></div>
  </td></tr>
</table>

<!-- CNN TABLE -->
<table id="cnnTable" class="news-table">
  <tr><th colspan="2">CNN News Ratings</th></tr>
  <tr><td>Credibility</td><td>3 Stars</td></tr>
  <tr><td>Reliability</td><td>5 Stars</td></tr>
  <tr><td>Bias</td><td>Left Leaning</td></tr>
  <tr><td>Community Rating</td><td>
  <div class="star-rating" data-site="cnn" data-type="community"></div>
  </td></tr>
  <tr>
  <td>Professional Rating</td>
  <td>
  <div class="star-rating" data-site="cnn" data-type="professional"></div>
  </td>
  </tr>
  <tr><td>Overall Rating</td>
  <td>
  <div class="overall-stars" data-rating="<?php
    $community = $averages['cnn'] ?? null;
    $stmt = $conn->prepare("SELECT rating FROM professional_ratings WHERE site = ?");
    $siteName = 'cnn';
    $stmt->bind_param("s", $siteName);
    $stmt->execute();
    $stmt->bind_result($pro);
    $stmt->fetch();
    $stmt->close();

    if ($community !== null && $pro !== null) {
      echo round(($community + $pro) / 2, 1);
    } else {
      echo "0";
    }
  ?>"></div>
  </td></tr>
</table>

<!-- WSJ TABLE -->
<table id="wsjTable" class="news-table">
  <tr><th colspan="2">Wall Street Journal Ratings</th></tr>
  <tr><td>Credibility</td><td>4 Stars</td></tr>
  <tr><td>Reliability</td><td>5 Stars</td></tr>
  <tr><td>Bias</td><td>Middle Bias</td></tr>
  <tr><td>Community Rating</td><td>
  <div class="star-rating" data-site="wsj" data-type="community"></div>
  </td></tr>
  <tr>
  <td>Professional Rating</td>
  <td>
  <div class="star-rating" data-site="wsj" data-type="professional"></div>
  </td>
  </tr>
  <tr><td>Overall Rating</td>
  <td>
  <div class="overall-stars" data-rating="<?php
    $community = $averages['wsj'] ?? null;
    $stmt = $conn->prepare("SELECT rating FROM professional_ratings WHERE site = ?");
    $siteName = 'wsj';
    $stmt->bind_param("s", $siteName);
    $stmt->execute();
    $stmt->bind_result($pro);
    $stmt->fetch();
    $stmt->close();

    if ($community !== null && $pro !== null) {
      echo round(($community + $pro) / 2, 1);
    } else {
      echo "0";
    }
  ?>"></div>
  </td></tr>
</table>

<!-- NYT TABLE -->
<table id="nytTable" class="news-table">
  <tr><th colspan="2">New York Times Ratings</th></tr>
  <tr><td>Credibility</td><td>4 Stars</td></tr>
  <tr><td>Reliability</td><td>5 Stars</td></tr>
  <tr><td>Bias</td><td>Middle Bias</td></tr>
  <tr><td>Community Rating</td><td>
  <div class="star-rating" data-site="nyt" data-type="community"></div>
  </td></tr>
  <tr>
  <td>Professional Rating</td>
  <td>
  <div class="star-rating" data-site="nyt" data-type="professional"></div>
  </td>
  </tr>
  <tr><td>Overall Rating</td>
  <td>
  <div class="overall-stars" data-rating="<?php
    $community = $averages['nyt'] ?? null;
    $stmt = $conn->prepare("SELECT rating FROM professional_ratings WHERE site = ?");
    $siteName = 'nyt';
    $stmt->bind_param("s", $siteName);
    $stmt->execute();
    $stmt->bind_result($pro);
    $stmt->fetch();
    $stmt->close();

    if ($community !== null && $pro !== null) {
      echo round(($community + $pro) / 2, 1);
    } else {
      echo "0";
    }
  ?>"></div>
  </td></tr>
</table>

<!-- CBS TABLE -->
<table id="cbsTable" class="news-table">
  <tr><th colspan="2">CBS News Ratings</th></tr>
  <tr><td>Credibility</td><td>4 Stars</td></tr>
  <tr><td>Reliability</td><td>5 Stars</td></tr>
  <tr><td>Bias</td><td>Middle Left</td></tr>
  <tr><td>Community Rating</td><td>
  <div class="star-rating" data-site="cbs" data-type="community"></div>
  </td></tr>
  <tr>
  <td>Professional Rating</td>
  <td>
  <div class="star-rating" data-site="cbs" data-type="professional"></div>
  </td>
  </tr>
  <tr><td>Overall Rating</td>
  <td>
  <div class="overall-stars" data-rating="<?php
    $community = $averages['cbs'] ?? null;
    $stmt = $conn->prepare("SELECT rating FROM professional_ratings WHERE site = ?");
    $siteName = 'cbs';
    $stmt->bind_param("s", $siteName);
    $stmt->execute();
    $stmt->bind_result($pro);
    $stmt->fetch();
    $stmt->close();

    if ($community !== null && $pro !== null) {
      echo round(($community + $pro) / 2, 1);
    } else {
      echo "0";
    }
  ?>"></div>
  </td></tr>
</table>
<script>
document.querySelectorAll('.community-rating').forEach(input => {
    input.addEventListener('change', function () {
        const site = this.getAttribute('data-site');
        const rating = this.value;

        fetch('update_rating.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `site=${site}&rating=${rating}`
        })
        .then(response => response.text())
        .then(data => console.log(data));
    });
});

document.querySelectorAll('.professional-rating').forEach(input => {
    const site = input.getAttribute('data-site');

    fetch(`get_professional_rating.php?site=${site}`)
        .then(response => response.text())
        .then(rating => input.value = rating);
});
document.querySelectorAll('.star-rating').forEach(container => {
    const site = container.dataset.site;
    const type = container.dataset.type;
    const isPro = type === 'professional';
    const isReadOnly = isPro && !<?= json_encode(isset($_SESSION['email'])) ?>;

    const maxStars = 5;
    let currentRating = 0;

    // Load community rating from localStorage
    if (type === 'community') {
        const saved = localStorage.getItem(`rating_${site}`);
        currentRating = saved ? parseFloat(saved) : 0;
    }

    const stars = [];

    for (let i = 1; i <= maxStars; i++) {
        const star = document.createElement('i');
        star.classList.add('fa', 'fa-star');
        if (i <= currentRating) star.classList.add('filled');

        if (!isReadOnly) {
            star.addEventListener('click', () => {
                currentRating = i;
                updateStars();
                saveRating();
            });
        }

        stars.push(star);
        container.appendChild(star);
    }

    function updateStars() {
        stars.forEach((star, index) => {
            star.classList.toggle('filled', index < currentRating);
        });
    }

    function saveRating() {
        if (type === 'community') {
            localStorage.setItem(`rating_${site}`, currentRating);
            fetch('update_rating.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `site=${site}&rating=${currentRating}`
            }).then(res => res.text()).then(console.log);
        } else {
            fetch('update_professional_rating.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `site=${site}&rating=${currentRating}`
            }).then(res => res.text()).then(console.log);
        }
    }

    updateStars();
});
document.querySelectorAll('.overall-stars').forEach(container => {
    const rating = parseFloat(container.dataset.rating);
    const maxStars = 5;
    const fullStars = Math.floor(rating);
    const halfStar = (rating % 1) >= 0.25 && (rating % 1) < 0.75;
    const roundUp = (rating % 1) >= 0.75;

    for (let i = 1; i <= maxStars; i++) {
        const star = document.createElement('i');

        if (i <= fullStars) {
            star.className = 'fas fa-star';
        } else if (i === fullStars + 1 && halfStar) {
            star.className = 'fas fa-star-half-alt';
        } else if (i <= fullStars + 1 && roundUp) {
            star.className = 'fas fa-star';
        } else {
            star.className = 'far fa-star';
        }

        container.appendChild(star);
    }

    // optional: also show the numeric score
    const score = document.createElement('span');
    score.textContent = ` (${rating})`;
    score.style.marginLeft = '6px';
    container.appendChild(score);
});
</script>
</body>
</html>