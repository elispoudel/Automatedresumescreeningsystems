<?php
session_start();
include("config.php"); // config.php sets up the PDO connection as $conn

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Ensure user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['email'];

// Fetch user data for sidebar (if needed)
$select_profile = $conn->prepare("SELECT * FROM users WHERE email = ?");
$select_profile->execute([$email]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

// --- Fetch job details or listing ---
if (isset($_GET['crud_id'])) {
    $crud_id = $_GET['crud_id'];
    // Fetch the specific job record (removed jobImage column)
    $stmt = $conn->prepare("SELECT crud_id, jobTitle, jobDescription FROM addjob WHERE crud_id = ?");
    $stmt->execute([$crud_id]);
    $job_detail = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    // Otherwise, fetch all job listings
    $stmt = $conn->prepare("SELECT crud_id, jobTitle, jobDescription FROM addjob");
    $stmt->execute();
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Available Job</title>
  <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
  <!-- Main CSS files -->
  <link rel="stylesheet" href="static/css/multiple.css">
  <link rel="stylesheet" href="static/css/jobavailable.css">
  <link rel="stylesheet" href="static/css/styless.css">
  <!-- New CSS file for button details -->
  <link rel="stylesheet" href="static/css/details.css">
</head>
<body>
  <!-- Your existing HTML content -->
 <!-- SIDEBAR -->
 <section id="sidebar">
      <a href="#" class="brand">
          <i class='bx'></i>
          <span class="text">HRHub</span>
      </a>
      <ul class="side-menu top">
          <li><a href="index.php"><i class='bx bxs-dashboard'></i><span class="text">Dashboard</span></a></li>
          <li class="active"><a href="availablejob.php"><i class='bx bxs-shopping-bag-alt'></i><span class="text">Available Jobs</span></a></li>
          <li><a href="addjob.php"><i class='bx bxs-doughnut-chart'></i><span class="text">Add Job</span></a></li>
      </ul>
      <ul class="side-menu">
          <li><a href="myaccount.php"><i class='bx bxs-cog'></i><span class="text">My Account</span></a></li>
          <li><a href="../logout.php" class="logout"><i class='bx bxs-log-out-circle'></i><span class="text">Logout</span></a></li>
      </ul>
  </section>
  <!-- SIDEBAR -->

  <!-- CONTENT -->
  <section id="content">
      <!-- NAVBAR -->
      <nav>
        <i class='bx bx-menu'></i>
        <a href="#" class="nav-link">Automated Resume Screening System</a>
        <form action="#"></form>
        <input type="checkbox" id="switch-mode" hidden>
        <label for="switch-mode" class="switch-mode"></label>
        <a>
          <?php
          // Display user's first and last name
          $query = $conn->prepare("SELECT firstName, lastName FROM users WHERE email = ?");
          $query->execute([$email]);
          $user = $query->fetch(PDO::FETCH_ASSOC);
          if ($user) {
              echo htmlspecialchars($user['firstName'] . ' ' . $user['lastName']);
          }
          ?>
        </a>
      </nav>
      <!-- MAIN CONTENT -->
      <div class="job-container">
          <?php if (isset($job_detail) && $job_detail): ?>
              <!-- DETAILS VIEW: Two-column layout -->
              <div class="details-container">
                  <!-- Left Column: Job Details -->
                  <div class="job-details">
                      <h2><?= htmlspecialchars($job_detail['jobTitle']) ?></h2>
                      <p><?= nl2br(htmlspecialchars($job_detail['jobDescription'])) ?></p>
                      <a class="back-btn" href="availablejob.php">Back to Job Listings</a>
                  </div>
                  <!-- Right Column: PDF Upload Interface -->
                  <div class="upload-container">
                     <div class="container">
                        <div class="card">
                            <div class="card-header text-center">
                                <h2>Resume Matcher</h2>
                            </div>
                            <div class="card-body">
                                <form id="resume-matcher-form" method="POST" enctype="multipart/form-data">
                                    <!-- Hidden input for Job Title -->
                                    <input type="hidden" name="job_title" value="<?= isset($job_detail) ? htmlspecialchars($job_detail['jobTitle']) : '' ?>">

                                    <!-- Hidden input for Job Description -->
                                    <input type="hidden" name="job_description" value="<?= isset($job_detail) ? htmlspecialchars($job_detail['jobDescription']) : '' ?>">

                                    <div class="form-group">
                                        <label for="resumes">Upload Resumes:</label>
                                        <p>Please upload resumes.</p>
                                        <input type="file" class="form-control" id="resumes" name="resumes" multiple required accept=".pdf, .docx, .txt">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Match Resumes</button>
                                </form>
                                <!-- NEW FILTER CONTROLS: initially hidden and aligned to the right -->
                                <div id="filter-container" style="display: none; margin-top: 10px;">
                                    <button id="filter-btn" class="btn btn-secondary">Filter</button>
                                    <div id="filter-panel" style="display: none; margin-top: 5px;">
                                        <button id="decrement-btn">-</button>
                                        <span id="filter-value">01</span>
                                        <button id="increment-btn">+</button>
                                        <button id="apply-filter-btn" class="btn btn-secondary">Apply</button>
                                    </div>
                                </div>
                                <div id="results-container">
                                    <!-- Results from Flask will be displayed here -->
                                </div>
                            </div>
                        </div>
                     </div>
                  </div>
              </div>
          <?php else: ?>
              <!-- LISTING VIEW -->
              <?php if (isset($jobs) && count($jobs) > 0): ?>
                  <?php foreach ($jobs as $job): ?>
                      <div class="job-card">
                          <h2><?= htmlspecialchars($job['jobTitle']) ?></h2>
                          <p>
                              <?php
                              $description = isset($job['jobDescription']) ? $job['jobDescription'] : 'No description available';
                              $words = explode(' ', $description);
                              echo htmlspecialchars(implode(' ', array_slice($words, 0, 10))) . '...';
                              ?>
                          </p>
                          <!-- "Details" link passes the job's crud_id -->
                          <a class="details-btn" href="availablejob.php?crud_id=<?= urlencode($job['crud_id']) ?>">Details</a>
                      </div>
                  <?php endforeach; ?>
              <?php else: ?>
                  <p>No job listings available.</p>
              <?php endif; ?>
          <?php endif; ?>
      </div>
  </section>

  <script>
    // Global variable to store matched resumes from Flask
    let matchedResumes = [];
    const resultsContainer = document.getElementById('results-container');

    // Function to format numbers with leading zero if less than 10
    function formatNumber(num) {
      return num < 10 ? '0' + num : num;
    }

    // Function to display resumes based on count (top N resumes)
    function displayResumes(count) {
      resultsContainer.innerHTML = ''; // Clear previous results
      const n = Math.min(count, matchedResumes.length);
      for (let i = 0; i < n; i++) {
          const resume = matchedResumes[i];
          const resumeElement = document.createElement('div');
          resumeElement.innerHTML = `
              <p>${i+1}. <strong>Name:</strong> ${resume.name}</p>
              <p><strong>Similarity Score:</strong> ${resume.similarity_score}</p>
              <hr>
          `;
          resultsContainer.appendChild(resumeElement);
      }
    }

    document.getElementById('resume-matcher-form').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission

        // Create a FormData object to send the form data
        const formData = new FormData(this);

        // Send the form data to the Flask backend using AJAX
        fetch('http://localhost:5001/matcher', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            resultsContainer.innerHTML = ''; // Clear previous results

            if (data.error) {
                resultsContainer.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
            } else {
                // Save matched resumes and display them all initially
                matchedResumes = data.matched_resumes;
                displayResumes(matchedResumes.length);
                // Reveal the Filter controls now that results are available
                document.getElementById('filter-container').style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(`An error occurred: ${error.message}`);
        });
    });

    // Toggle filter panel when Filter button is clicked
    document.getElementById('filter-btn').addEventListener('click', function() {
      const filterPanel = document.getElementById('filter-panel');
      if (filterPanel.style.display === 'none' || filterPanel.style.display === '') {
        filterPanel.style.display = 'block';
      } else {
        filterPanel.style.display = 'none';
      }
    });

    // Increase filter value when + button is clicked
    document.getElementById('increment-btn').addEventListener('click', function() {
      const filterValueElem = document.getElementById('filter-value');
      let currentValue = parseInt(filterValueElem.textContent);
      currentValue++;
      filterValueElem.textContent = formatNumber(currentValue);
    });

    // Decrease filter value when - button is clicked (min value is 1)
    document.getElementById('decrement-btn').addEventListener('click', function() {
      const filterValueElem = document.getElementById('filter-value');
      let currentValue = parseInt(filterValueElem.textContent);
      if (currentValue > 1) {
          currentValue--;
          filterValueElem.textContent = formatNumber(currentValue);
      }
    });

    // Apply filter to display only the top N resumes
    document.getElementById('apply-filter-btn').addEventListener('click', function() {
      const filterValueElem = document.getElementById('filter-value');
      const filterCount = parseInt(filterValueElem.textContent);
      displayResumes(filterCount);
    });
  </script>

  <script src="static/js/script.js"></script>
</body>
</html>
