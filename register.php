<?php
require_once "conn.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate input
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Use prepared statement
    $tsql = "INSERT INTO Login (LoginUsername, LoginPassword, Email) VALUES (?, ?, ?)";
    $params = array($username, $hashedPassword, $email);
    
    $stmt = sqlsrv_query($conn, $tsql, $params);
    if ($stmt === false) {
        die(FormatErrors(sqlsrv_errors()));
    }
}