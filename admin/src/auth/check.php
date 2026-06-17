<?php
$conn = new mysqli("localhost", "root", "", "nexus");
session_start();
extract($_POST);
$getUserQuery = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
$response = $conn -> query($getUserQuery);
$status = $response -> num_rows;

if ($status == 1){
    // 1. Fetch the row data as an associative array
    $admin = $response->fetch_assoc();
    
    // 2. Store only the ID in the session (assuming your column name is 'id')
    $_SESSION["userId"] = $admin['id'];
    header("location:../index.html");
}
else{
    header("location:../login.php");
}
?>
