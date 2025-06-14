<?php
session_start();
include("connect.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
</head>
<body>

   <div class="greeting-container">
        <h1 id="greeting"></h1>
    </div>
    <div style="text-align:center; padding:15%;">
      <p  style="font-size:50px; font-weight:bold;">
         <script>
        // Get current hour
        const date = new Date();
        const hour = date.getHours();

        // Get greeting element
        const greetingElement = document.getElementById('greeting');
        
        // Set greeting based on time of day
        let greeting;
        if (hour < 5) {
            greeting = 'Good Night!';
        } else if (hour < 12) {
            greeting = 'Good Morning!';
        } else if (hour < 18) {
            greeting = 'Good Afternoon!';
        } else {
            greeting = 'Good Evening!';
        }

        // Update greeting text
        greetingElement.innerHTML = greeting;
    </script>





        <?php 
       if(isset($_SESSION['email'])){
        $email=$_SESSION['email'];
        $query=mysqli_query($conn, "SELECT users.* FROM `users` WHERE users.email='$email'");
        while($row=mysqli_fetch_array($query)){
            echo $row['firstName'].' '.$row['lastName'];
        }
       }
       ?>
       :)
      </p>
      <a href="logout.php">Logout</a>
    </div>
</body>
</html>