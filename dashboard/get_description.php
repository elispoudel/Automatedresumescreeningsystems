<?php
include("connect.php");

// Get job ID from the request
if (isset($_GET['id'])) {
    $jobId = $_GET['id'];

    // Fetch job description based on job ID
    $query = "SELECT jobTitle, jobDescription FROM addjob WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $jobId); // "i" for integer parameter
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $job = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'jobTitle' => $job['jobTitle'],
            'jobDescription' => $job['jobDescription']
        ]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>
