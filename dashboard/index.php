<?php
session_start();
include("connect.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- Boxicons -->
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<!-- My CSS -->
	<link rel="stylesheet" href="static/css/styless.css">

	<title>Dashboard</title>
</head>
<body>
	<!-- SIDEBAR -->
	<section id="sidebar">
		<a href="#" class="brand">
			<i class='bx '></i>
			<span class="text">HRHub</span>
		</a>
		<ul class="side-menu top">
			<li class="active">
				<a href="#">
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
			<li>
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
	<!-- SIDEBAR -->



	<!-- CONTENT -->
	<section id="content">
		<!-- NAVBAR -->
		<nav>
			<i class='bx bx-menu' ></i>
			<a href="#" class="nav-link">Automated Resume Screening System</a>
			<form action="#">
			</form>
			<input type="checkbox" id="switch-mode" hidden>
			<label for="switch-mode" class="switch-mode"></label>
			<a><?php 
       			if(isset($_SESSION['email'])){
       		    $email=$_SESSION['email'];
       		    $query=mysqli_query($conn, "SELECT users.* FROM `users` WHERE users.email='$email'");
       		 	while($row=mysqli_fetch_array($query)){
            	echo $row['firstName'].' '.$row['lastName'];
                 }
               }
             ?></a>
		</nav>
		<!-- NAVBAR -->

		<!-- MAIN -->
		<main>
			<div class="head-title">
				<div class="left">
					<h1>Dashboard</h1>
					<ul class="breadcrumb">
						<li>
							<a href="#">Dashboard</a>
						</li>
						<li><i class='bx bx-chevron-right' ></i></li>
						<li>
							<a class="active" href="#">Home</a>
						</li>
					</ul>
				</div>	
			</div>

			<ul class="box-info">	
		  	<li>
                          <div class="text">
       					 	<h1 id="greeting"></h1>
			</div>
   		 <script>
        // Get current hour
        const date = new Date();
        const hour = date.getHours();

        // Get greeting element
        const greetingElement = document.getElementById('greeting');
        
        // Set greeting based on time of day
        let greeting;
        if (hour < 5) {
            greeting ='Good Night,';
        } else if (hour < 12) {
            greeting = 'Good Morning,';
        } else if (hour < 18) {
            greeting = 'Good Afternoon,';
        } else {
         greeting = 'Good Evening,';
        }
        // Update greeting text
        greetingElement.innerHTML = greeting;
		
    			</script>
		      </li>
	  		</ul>
    </main>
		<!-- MAIN -->

	</section>
	<!-- CONTENT -->
	
	<script src="../dashboard/static/js/script.js"></script>
</body>
</html>