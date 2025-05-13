<?php
session_start();
require_once "conn.php";

// Define variables and initialize with empty values
$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = $token_err = "";

// Check if token exists and is valid
if(empty($_GET["token"])) {
    header("location: login.php");
    exit();
}

$token = $_GET["token"];
$tsql = "SELECT LoginUsername FROM Login WHERE ResetToken = ? AND ResetTokenExpiry > GETDATE()";
$params = array($token);
$stmt = sqlsrv_query($conn, $tsql, $params);

if($stmt === false || !sqlsrv_has_rows($stmt)) {
    $token_err = "Invalid or expired reset token.";
}

if($_SERVER["REQUEST_METHOD"] == "POST" && empty($token_err)) {
    // Validate password
    if(empty(trim($_POST["new_password"]))) {
        $new_password_err = "Please enter the new password.";     
    } elseif(strlen(trim($_POST["new_password"])) < 8) {
        $new_password_err = "Password must have at least 8 characters.";
    } else {
        $new_password = trim($_POST["new_password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm the password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Check input errors before updating the database
    if(empty($new_password_err) && empty($confirm_password_err)) {
        // Update password and clear reset token
        $tsql = "UPDATE Login SET LoginPassword = ?, ResetToken = NULL, ResetTokenExpiry = NULL WHERE ResetToken = ?";
        $params = array(password_hash($new_password, PASSWORD_DEFAULT), $token);
        
        $stmt = sqlsrv_query($conn, $tsql, $params);
        if($stmt === false) {
            die(FormatErrors(sqlsrv_errors()));
        }
        
        // Redirect to login page
        header("location: login.php");
        exit();
    }
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
        .reset-confirm-block{ 
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
    <div class="reset-confirm-block">
        <div class="wrapper">
            <h2>Reset Password</h2>
            <?php if(!empty($token_err)): ?>
                <div class="alert alert-danger"><?php echo $token_err; ?></div>
                <p><a href="reset-password.php">Request a new reset link</a></p>
            <?php else: ?>
                <p>Please enter your new password.</p>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?token=" . $token; ?>" method="post">
                    <div class="form-group <?php echo (!empty($new_password_err)) ? 'has-error' : ''; ?>">
                        <label>New Password</label>
                        <input type="password" name="new_password" class="form-control">
                        <span class="help-block"><?php echo $new_password_err; ?></span>
                    </div>
                    <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control">
                        <span class="help-block"><?php echo $confirm_password_err; ?></span>
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="Submit">
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
