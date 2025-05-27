<?php
session_start();
$host = "localhost";
$user = "Username"; // full cPanel MySQL username
$pass = "Password";           // the password you used
$db   = "ratingsite"; // full cPanel MySQL database name

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$averages = [];
$result = $conn->query("SELECT site, AVG(rating) as avg_rating FROM guest_votes GROUP BY site");
while ($row = $result->fetch_assoc()) {
    $averages[$row['site']] = round($row['avg_rating'], 2);
}

function getProfessionalRating($conn, $site) {
    $stmt = $conn->prepare("SELECT rating FROM professional_ratings WHERE site = ?");
    $stmt->bind_param("s", $site);
    $stmt->execute();
    $stmt->bind_result($rating);
    $stmt->fetch();
    $stmt->close();
    return $rating ?? 0;

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE-edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">  <link rel="stylesheet" href="Login.css">
  <title>News Rating</title>
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
    .fa-star.filled {
    color: gold;
    }

  </style>
</head>
<body>
  <?php if (!isset($_GET['extension'])): ?>
  <div class="container" id="signup" style="display:none;">
    <h1 class="form-title">Register</h1>
    <form method="post" action="register.php">
      <div class="input-group">
         <i class="fas fa-user"></i>
         <input type="text" name="fName" id="fName" placeholder="First Name" required>
         <label for="fname">First Name</label>
      </div>
      <div class="input-group">
          <i class="fas fa-user"></i>
          <input type="text" name="lName" id="lName" placeholder="Last Name" required>
          <label for="lName">Last Name</label>
      </div>
      <div class="input-group">
          <i class="fas fa-envelope"></i>
          <input type="email" name="email" id="email" placeholder="Email" required>
          <label for="email">Email</label>
      </div>
      <div class="input-group">
          <i class="fas fa-lock"></i>
          <input type="password" name="password" id="password" placeholder="Password" required>
          <label for="password">Password</label>
      </div>
     <input type="submit" class="btn" value="Sign Up" name="signUp">
    </form>
    <p class="or">
      ----------or--------
    </p>
    <div class="icons">
      <i class="fab fa-google"></i>
      <i class="fab fa-facebook"></i>
    </div>
    <div class="links">
      <p>Already have an account ?</p>
      <button id="signInButton">Sign In</button>
    </div>
  </div>

  <div class="container" id="signIn">
      <h1 class="form-title">Sign In</h1>
      <form method="post" action="register.php">
        <div class="input-group">
            <i class="fas fa-envelope"></i>
            <input type="email" name="email" id="email" placeholder="Email" required>
            <label for="email">Email</label>
        </div>
        <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" id="password" placeholder="Password" required>
            <label for="password">Password</label>
        </div>
        <p class="recover">
          <a href="#">Recover Password</a>
        </p>
       <input type="submit" class="btn" value="Sign In" name="signIn">
      </form>
      <p class="or">
        ----------or--------
      </p>
      <div class="icons">
        <i class="fab fa-google"></i>
        <i class="fab fa-facebook"></i>
      </div>
      <div class="links">
        <p>Don't have an account yet?</p>
        <button id="signUpButton">Sign Up</button>
      </div>
    </div>
  </div>
  <?php endif; ?>
  <!-- NBC TABLE -->
  <table id="nbcTable" class="news-table">
    <tr><th colspan="2">NBC News Ratings</th></tr>
    <tr><td>Credibility</td><td>4 Stars</td></tr>
    <tr><td>Reliability</td><td>5 Stars</td></tr>
    <tr><td>Bias</td><td>Middle Bias</td></tr>
    <tr><td>Community Rating</td><td>
    <div class="star-rating" data-site="nbc" data-type="community" data-readonly="false"></div>
  </td></tr>
<tr><td>Professional Rating</td><td>
  <div class="star-rating" data-site="nbc" data-type="professional" data-readonly="true"></div>
  </td></tr>
  <tr><td>Overall Rating</td><td>
  <div class="overall-stars" data-site="nbc"></div>
</td></tr>
  </table>

  <!-- FOX TABLE -->
  <table id="foxTable" class="news-table">
    <tr><th colspan="2">FOX News Ratings</th></tr>
    <tr><td>Credibility</td><td>2 Stars</td></tr>
    <tr><td>Reliability</td><td>4 Stars</td></tr>
    <tr><td>Bias</td><td>Right Leaning</td></tr>
    <tr><td>Community Rating</td><td>
    <div class="star-rating" data-site="fox" data-type="community" data-readonly="false"></div>
  </td></tr>
<tr><td>Professional Rating</td><td>
  <div class="star-rating" data-site="fox" data-type="professional" data-readonly="true"></div>
  </td></tr>
  <tr><td>Overall Rating</td><td>
  <div class="overall-stars" data-site="fox"></div>
</td></tr>
  </table>

  <!-- CNN TABLE -->
  <table id="cnnTable" class="news-table">
    <tr><th colspan="2">CNN News Ratings</th></tr>
    <tr><td>Credibility</td><td>3 Stars</td></tr>
    <tr><td>Reliability</td><td>5 Stars</td></tr>
    <tr><td>Bias</td><td>Left Leaning</td></tr>
    <tr><td>Community Rating</td><td>
    <div class="star-rating" data-site="cnn" data-type="community" data-readonly="false"></div>
  </td></tr>
<tr><td>Professional Rating</td><td>
  <div class="star-rating" data-site="cnn" data-type="professional" data-readonly="true"></div>
  </td></tr>
  <tr><td>Overall Rating</td><td>
  <div class="overall-stars" data-site="cnn"></div>
</td></tr>
  </table>

  <!-- WSJ TABLE -->
  <table id="wsjTable" class="news-table">
    <tr><th colspan="2">Wall Street Journal Ratings</th></tr>
    <tr><td>Credibility</td><td>4 Stars</td></tr>
    <tr><td>Reliability</td><td>5 Stars</td></tr>
    <tr><td>Bias</td><td>Middle Bias</td></tr>
    <tr><td>Community Rating</td><td>
    <div class="star-rating" data-site="wsj" data-type="community" data-readonly="false"></div>
  </td></tr>
<tr><td>Professional Rating</td><td>
  <div class="star-rating" data-site="wsj" data-type="professional" data-readonly="true"></div>
  </td></tr>
  <tr><td>Overall Rating</td><td>
  <div class="overall-stars" data-site="wsj"></div>
</td></tr>
  </table>

  <!-- NYT TABLE -->
  <table id="nytTable" class="news-table">
    <tr><th colspan="2">New York Times Ratings</th></tr>
    <tr><td>Credibility</td><td>4 Stars</td></tr>
    <tr><td>Reliability</td><td>5 Stars</td></tr>
    <tr><td>Bias</td><td>Middle Bias</td></tr>
    <tr><td>Community Rating</td><td>
    <div class="star-rating" data-site="nyt" data-type="community" data-readonly="false"></div>
  </td></tr>
<tr><td>Professional Rating</td><td>
  <div class="star-rating" data-site="nyt" data-type="professional" data-readonly="true"></div>
  </td></tr>
  <tr><td>Overall Rating</td><td>
  <div class="overall-stars" data-site="nyt"></div>
</td></tr>
  </table>

  <!-- CBS TABLE -->
  <table id="cbsTable" class="news-table">
    <tr><th colspan="2">CBS News Ratings</th></tr>
    <tr><td>Credibility</td><td>4 Stars</td></tr>
    <tr><td>Reliability</td><td>5 Stars</td></tr>
    <tr><td>Bias</td><td>Middle Left</td></tr>
    <tr><td>Community Rating</td><td>
    <div class="star-rating" data-site="cbs" data-type="community" data-readonly="false"></div>
  </td></tr>
<tr><td>Professional Rating</td><td>
  <div class="star-rating" data-site="cbs" data-type="professional" data-readonly="true"></div>
  </td></tr>
  <tr><td>Overall Rating</td><td>
  <div class="overall-stars" data-site="cbs"></div>
</td></tr>
  </table>

  <script src="js/popup.js"></script>
  <script src="js/overall_stars.js"></script>

</body>
</html>