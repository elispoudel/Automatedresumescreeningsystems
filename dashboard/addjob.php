<?php
session_start();
include("connect.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css">
	<link rel="stylesheet" href="static/css/bootstrap.css">
	<link rel="stylesheet" href="static/css/styless.css">

	<title>Add Job</title>
</head>
<body>
	<!-- SIDEBAR -->
	<section id="sidebar">
		<a href="#" class="brand">
			<i class='bx '></i>
			<span class="text">HRHub</span>
		</a>
		<ul class="side-menu top">
			<li>
				<a href="index.php">
					<i class='bx bxs-dashboard'></i>
					<span class="text">Dashboard</span>
				</a>
			</li>
			<li>
				<a href="availablejob.php">
					<i class='bx bxs-shopping-bag-alt'></i>
					<span class="text">Available Job</span>
				</a>
			</li>
			<li class="active">
				<a href="addjob.php">
					<i class='bx bxs-doughnut-chart'></i>
					<span class="text">Add Job</span>
				</a>
			</li>
		</ul>
		<ul class="side-menu">
			<li>
				<a href="myaccount.php">
					<i class='bx bxs-cog'></i>
					<span class="text">My Account</span>
				</a>
			</li>
			<li>
				<a href="../logout.php" class="logout">
					<i class='bx bxs-log-out-circle'></i>
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
			<i class='bx bx-menu'></i>
			<a href="#" class="nav-link">Automated Resume Screening System</a>
			<form action="#">
			</form>
			<input type="checkbox" id="switch-mode" hidden>
			<label for="switch-mode" class="switch-mode"></label>
			<a>  
			<?php 
				if (isset($_SESSION['email'])) {
					$email = $_SESSION['email'];
					$query = mysqli_query($conn, "SELECT users.* FROM `users` WHERE users.email='$email'");
					while ($row = mysqli_fetch_array($query)) {
						echo $row['firstName'].' '.$row['lastName'];
					}
				}
				?>

			</a>
		</nav>
		<!-- NAVBAR -->

        <!-- Job List Section -->
		<div class="container my-5">
    <h2>List Of Jobs</h2>
    <div class="mb-3">
        <a href="create.php" class="col-sm-3 mb-3 btn btn-primary" role="button">New Jobs</a>
        <div class="col-sm-6">
		<input type="text" name="search" placeholder="Search Client.." id="search_bar" class="form-control">
        </div>
    </div>
    <br>
    <br>

    <table class="table">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Job Title</th>
                <th scope="col">Job Description</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody id="tbody">
            <?php include "php/client_record.php"; ?>
        </tbody>
		</table>
</div>
<script src="static/js/search.js"></script>
</body>
 <script src="../dashboard/static/js/script.js"></script>

</html>
