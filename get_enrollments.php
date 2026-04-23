<?php
include('db_config.php');
session_start();

if (isset($_SESSION['user_name'])) {
    $u_name = $_SESSION['user_name'];
    $result = mysqli_query($conn, "SELECT campaign_name FROM enrollments WHERE user_name='$u_name'");
    
    $enrolled = [];
    while($row = mysqli_fetch_assoc($result)) {
        $enrolled[] = $row['campaign_name'];
    }
    echo json_encode($enrolled);
}
?>