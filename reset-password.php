<?php
session_start();
require_once "conn.php";

// Define variables and initialize with empty values
$email = $msg = "";
$email_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate email
    if(empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        if(!$email) {
            $email_err = "Please enter a valid email address.";
        }
    }
    
    if(empty($email_err)) {
        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Update user with reset token
        $tsql = "UPDATE Login SET ResetToken = ?, ResetTokenExpiry = ? WHERE Email = ?";
        $params = array($token, $expiry, $email);
        
        $stmt = sqlsrv_query($conn, $tsql, $params);
        if($stmt === false) {
            die(FormatErrors(sqlsrv_errors()));
        }
        
        if(sqlsrv_rows_affected($stmt) > 0) {
            // Send reset email (in production, implement proper email sending)
            $resetLink = "http://" . $_SERVER['HTTP_HOST'] . 
                        dirname($_SERVER['PHP_SELF']) . 
                        "/reset-confirm.php?token=" . $token;
            
            $msg = "A password reset link has been sent to your email. (Development: $resetLink)";
        } else {
            $email_err = "No account found with that email address.";
        }
    }
    
    sqlsrv_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ 
            color: #666;
            font: 14px sans-serif; 
            background-color: #fafafa;
        }
        .reset-block{ 
            padding: 20px; 
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            min-height: 100vh;
        }
        .wrapper{ 
            width: 350px;
            padding: 20px; 
            background-color: white;
            border: 1px solid #eaeaea;
        }
    </style>
</head>
<body>
    <div class="reset-block">
        <div class="wrapper">
            <h2>Reset Password</h2>
            <p>Please enter your email address to reset your password.</p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo $email; ?>">
                    <span class="help-block"><?php echo $email_err; ?></span>
                </div>
                <?php if(!empty($msg)): ?>
                    <div class="alert alert-info"><?php echo $msg; ?></div>
                <?php endif; ?>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Reset Password">
                </div>
                <p>Remember your password? <a href="login.php">Login here</a>.</p>
            </form>
        </div>
    </div>
</body>
</html>
