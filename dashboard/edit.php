<?php
    include "php/conn.php";

    // Redirect if client parameter is not set
    if (!isset($_GET['client'])) {
        header("Location: addjob.php");
        exit;
    }

    $client = mysqli_real_escape_string($conn, $_GET['client']);
    $select = mysqli_prepare($conn, "SELECT * FROM `addjob` WHERE crud_id = ?");
    mysqli_stmt_bind_param($select, "s", $client);
    mysqli_stmt_execute($select);
    $result = mysqli_stmt_get_result($select);
    $fecth = mysqli_fetch_assoc($result);

    // Processing form data when form is submitted
    if (isset($_POST['submit'])) {
        $title = trim($_POST['jobTitle']);
        $description = trim($_POST['jobDescription']);

        // Prepare an update statement
        $update = mysqli_prepare($conn, "UPDATE `addjob` SET `jobTitle` = ?, `jobDescription` = ? WHERE `crud_id` = ?");
        mysqli_stmt_bind_param($update, "sss", $title, $description, $client);

        if (mysqli_stmt_execute($update)) {
            // Redirect to edit page with success message
            header("Location: edit.php?client=$client&success=Job Edited Successfully");
            exit();  
        } else {
            echo "Error updating record: " . mysqli_error($conn);
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="static/css/bootstrap.css">
</head>
<body>
<style>
        .textarea-job-description {
            width: 100%; /* Full width of the container */
            height: 200px; /* Sufficient height for detailed descriptions */
            resize: vertical; /* Allows the user to resize vertically */
        }
    </style>
    <div class="container my-5">
        <h2>Edit Job</h2>
        <br><br>

        <form action="" method="post">
            <?php
                if (isset($_GET['success'])) {
                    echo '<div class="w-25 alert alert-success" role="alert">' . $_GET['success'] . '</div>';
                }
            ?>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Job Title</label>
                <div class="col-sm-6">
                    <input type="text" value="<?php echo htmlspecialchars($fecth['jobTitle']); ?>" name="jobTitle" class="form-control" placeholder="Job Title" required>
                </div>
            </div>
            <div class="row mb-3">
    <label class="col-sm-3 col-form-label">Job Description</label>
    <div class="col-sm-6">
        <textarea name="jobDescription" class="form-control textarea-job-description" placeholder="Job Description" required rows="5" cols="30"><?php echo htmlspecialchars($fecth['jobDescription']); ?></textarea>
    </div>
</div> 
      <div class="row mb-3">
                <button type="submit" name="submit" class="col-sm-3 btn btn-primary">Edit</button>
                <div class="col-sm-6">
                    <a href="addjob.php" class="btn btn-outline-primary">Go Back</a>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
