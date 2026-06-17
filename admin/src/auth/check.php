<?php
// admin/src/auth/check.php

// Step up two directories to reach the global root config file
require_once __DIR__ . '/../../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = sanitize($_POST['password']); 

    // Look up the user details and extract their permission group id
    $query = "SELECT id, role_id FROM users WHERE email = '$email' AND password = '$password' LIMIT 1";
    $response = $conn->query($query);

    if ($response && $response->num_rows === 1) {
        $user = $response->fetch_assoc();
        
        // Save authorization identity properties to active session arrays
        $_SESSION["userInfo"] = $user['id'];
        $_SESSION["userRole"] = $user['role_id']; 
        
        // Traffic Control Routing Layer
        if ($user['role_id'] == 1 || $user['role_id'] == 2) {
            // Authorized Dashboard operators (Admin / Vendor)
            redirect("../../index.php"); 
        } else {
            // Normal consumer client identity -> route to general public catalog
            redirect("../../../index.php"); 
        }
    } else {
        // Credential matching failure
        redirect("../../login.php?error=failed_login");
    }
} else {
    redirect("../../login.php");
}