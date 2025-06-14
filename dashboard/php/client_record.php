<?php
    include "conn.php";
    $sql = mysqli_query($conn, "SELECT * FROM `addjob`");
    $output = "";
    if(mysqli_num_rows($sql) > 0){
        while($fecth = mysqli_fetch_assoc($sql)){
            $output .= '<tr>
            <td>'.$fecth['id'].'</td>
            <td>'.$fecth['jobTitle'].'</td>
            <td>'.$fecth['jobDescription'].'</td>
            <td>
                <a href="../dashboard/edit.php?client='.$fecth['crud_id'].'" class="btn btn-primary btn-sm">Edit</a>
                <a href="php/delete.php?client='.$fecth['crud_id'].'" class="btn btn-danger btn-sm">Delete</a>
            </td>
        </tr>';
        }
    }else{
        $output .= 'No Job Are Available';
    }

    echo $output;

?>