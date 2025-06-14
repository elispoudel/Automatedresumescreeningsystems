<?php 
session_start();
include 'connect.php';

// Retrieve message from session (if exists)
$message = isset($_SESSION['message']) ? $_SESSION['message'] : ""; 
unset($_SESSION['message']); // Clear message after displaying

if(isset($_POST['signUp'])){
    $firstName = $_POST['fName'];
    $lastName = $_POST['lName'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if email already exists
    $checkEmail = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($checkEmail);

    if ($result->num_rows > 0) {
        $_SESSION['message'] = "Email Address Already Exists!";
    } else {
        $insertQuery = "INSERT INTO users(firstName, lastName, email, password)
                        VALUES ('$firstName', '$lastName', '$email', '$password')";
        if ($conn->query($insertQuery) === TRUE) {
            $_SESSION['message'] = "Account created successfully! Please log in.";
            header("Location: signup.php");
            exit();
        } else {
            $_SESSION['message'] = "Error: " . $conn->error;
        }
    }

    // Redirect to prevent form resubmission & display message
    header("Location: signup.php");
    exit();
}


if(isset($_POST['signIn'])){
    $email=$_POST['email'];
    $password=$_POST['password'];
    
    $sql="SELECT * FROM users WHERE email='$email' and password='$password'";
    $result=$conn->query($sql);
    if($result->num_rows>0){
     session_start();
     $row=$result->fetch_assoc();
     $_SESSION['email']=$row['email'];
     header("Location: dashboard/index.php");
     exit();
    }
    else{
     echo "Not Found, Incorrect Email or Password";
    }
 
 }
 ?>