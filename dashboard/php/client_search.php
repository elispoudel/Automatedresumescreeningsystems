<?php
    include "conn.php";
    $searchTerm = $_POST['searchTerm'];
    $sql = mysqli_query($conn, "SELECT * FROM `addjob`
    WHERE (
        `jobTitle` LIKE '%{$searchTerm}%' OR
        `jobDescription` LIKE '%{$searchTerm}%'
    )");
    $output = "";
    if(mysqli_num_rows($sql) > 0){
        while($fecth = mysqli_fetch_assoc($sql)){
          $output .= '<tr>
            <td>'.$fecth['id'].'</td>
            <td>'.$fecth['jobTitle'].'</td>
            <td>'.$fecth['jobDescription'].'</td>
            <td>
                <a href="edit.php?client='.$fecth['crud_id'].'" class="btn btn-primary btn-sm">Edit</a>
                <a href="php/delete.php?client='.$fecth['crud_id'].'" class="btn btn-danger btn-sm">Delete</a>
            </td>
        </tr>';
        }
    }else{
        $output .= 'No Jobs Are Available From Your Search Term';
    }

    echo $output;

?>