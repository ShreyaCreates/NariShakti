<?php
include('db_config.php');
session_start();

if (isset($_POST['campaign_name']) && isset($_SESSION['user_name'])) {
    $u_name = $_SESSION['user_name'];
    $c_name = mysqli_real_escape_string($conn, $_POST['campaign_name']);

    $check = mysqli_query($conn, "SELECT * FROM enrollments WHERE user_name='$u_name' AND campaign_name='$c_name'");
    
    if (mysqli_num_rows($check) == 0) {
        $sql = "INSERT INTO enrollments (user_name, campaign_name) VALUES ('$u_name', '$c_name')";
        mysqli_query($conn, $sql);
        echo "success";
    } else {
        echo "exists";
    }
}
?>