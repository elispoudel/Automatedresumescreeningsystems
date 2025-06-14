<?php
    include "conn.php";

    $client = $_GET['client'];
    if(!isset($client)){
        header("location: ../addjob.php");
    }
    $delete = mysqli_query($conn, "DELETE FROM `addjob` WHERE `crud_id` = '$client'");

    
    if($delete){
        header("location: ../addjob.php");
    }

?>