<?php
session_start();
include("connect.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="static/css/styless.css">
  <link rel="stylesheet" href="static/css/stylesprofile.css">
  <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
  
  <title>My Account</title>
</head>

<section id="sidebar">
    <a href="#" class="brand">
      <i class='bx '></i>
      <span class="text">HRHub</span>
    </a>
    <ul class="side-menu top">
      <li>
        <a href="index.php">
          <i class='bx bxs-dashboard' ></i>
          <span class="text">Dashboard</span>
        </a>
      </li>
      <li>
        <a href="availablejob.php">
          <i class='bx bxs-shopping-bag-alt' ></i>
          <span class="text">Available Job</span>
        </a>
      </li>
      <li>
        <a href="addjob.php">
          <i class='bx bxs-doughnut-chart' ></i>
          <span class="text">Add Job</span>
        </a>
      </li>
    </ul>
    <ul class="side-menu">
      <li class="active">
        <a href="myaccount.php">
          <i class='bx bxs-cog' ></i>
          <span class="text">My Account</span>
        </a>
      </li>
      <li>
        <a href="../logout.php" class="logout">
          <i class='bx bxs-log-out-circle' ></i>
          <span class="text">Logout</span>
        </a>
      </li>
    </ul>
</section>

<section id="content">
    <nav>
      <i class='bx bx-menu' ></i>
      <a href="#" class="nav-link">Automated Resume Screening System</a>
      <form action="#"></form>
      <input type="checkbox" id="switch-mode" hidden>
      <label for="switch-mode" class="switch-mode"></label>
      <a> <h1><?php 
            if(isset($_SESSION['email'])){
              $email=$_SESSION['email'];
              $query=mysqli_query($conn, "SELECT users.* FROM `users` WHERE users.email='$email'");
            while($row=mysqli_fetch_array($query)){
              echo $row['firstName'].' '.$row['lastName'];
                 }
               }
             ?></h1></a>
    </nav>

<body>
<?php
include 'config.php';
$message = "";
$email = $_SESSION['email']; 

$select_profile = $conn->prepare("SELECT * FROM users WHERE email = ?");
$select_profile->execute([$email]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

if (!$fetch_profile) {
    echo "Profile data not found.";
    exit;
}
if (isset($_POST['update'])) {
  $firstName = filter_var($_POST['firstName'], FILTER_SANITIZE_STRING);
  $lastName = filter_var($_POST['lastName'], FILTER_SANITIZE_STRING);
  $new_email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
  $password = trim($_POST['password']);

  // Check if the new email already exists in the database
  $check_email = $conn->prepare("SELECT email FROM users WHERE email = ? AND email != ?");
  $check_email->execute([$new_email, $email]);

  if ($check_email->rowCount() > 0) {
      $message = "Email already exists. Please choose another.";
  } else {
      $update_profile = $conn->prepare("UPDATE users SET firstName = ?, lastName = ?, email = ?, password = ? WHERE email = ?");
      $update_profile->execute([$firstName, $lastName, $new_email, $password, $email]);

      $_SESSION['email'] = $new_email;
      $_SESSION['profile_updated'] = true; // Set session variable to track update

      // Redirect to the same page to avoid form resubmission and refresh only once
      header("Location: myaccount.php");
      exit();
  }
}

// Check if session variable is set and then clear it
if (isset($_SESSION['profile_updated']) && $_SESSION['profile_updated']) {
  $message = "Successfully Updated";
  unset($_SESSION['profile_updated']); // Remove session variable to prevent multiple refreshes
}


?>

<section class="update-profile-container">
   <form action="" method="post">
      <div class="flex">
         <div class="inputBox">
            <span>First name : </span>
            <input type="text" name="firstName" required class="box" placeholder="Enter your first name" value="<?= isset($fetch_profile['firstName']) ? $fetch_profile['firstName'] : ''; ?>">
            <span>Last name : </span>
            <input type="text" name="lastName" required class="box" placeholder="Enter your last name" value="<?= isset($fetch_profile['lastName']) ? $fetch_profile['lastName'] : ''; ?>">
            </div>
            <div class="inputBox">
            <span>Email : </span>
            <input type="email" name="email" required class="box" placeholder="Enter your email" value="<?= isset($fetch_profile['email']) ? $fetch_profile['email'] : ''; ?>">
            <span>Password : </span>
            <input name="password" required class="box" placeholder="Enter your password" value="<?= isset($fetch_profile['password']) ? $fetch_profile['password'] : ''; ?>">
         </div>
      </div>
      <div class="flex-btn">
         <input type="submit" value="Update profile" name="update" class="btn">
      </div>
      <?php if ($message !== "") { ?>
         <p style="color: green; text-align: center; margin-top: 10px;"> <?= $message; ?> </p>
      <?php } ?>
   </form>
</section>

<script src="../dashboard/static/js/script.js"></script>
</body>
</html>
