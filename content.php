<?php
/* Microsoft Azure Database PHP login protected page by childofcode.com */

// Initialize the session, Check if the user is logged in, if not then redirect him to login page
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

require_once "conn.php";

// Fetch user details
$tsql = "SELECT Email, LastLoginTime, IPAddress, CreatedAt FROM Login WHERE LoginUsername = ?";
$params = array($_SESSION["username"]);
$stmt = sqlsrv_query($conn, $tsql, $params);

if($stmt === false) {
    die(FormatErrors(sqlsrv_errors()));
}

$userDetails = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta charset="utf-8">
	<meta name=description content="PHP and Microsoft Azure Database Login by childofcode.comn">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Microsoft Azure Database PHP login page secure content page</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script> 
    <style type="text/css">
         body{ 
            color: #666;
            font: 14px sans-serif; 
            background-color: #fafafa;
        }
        .content-block{ 
            padding: 20px; 
            margin: 0 auto;
            max-width: 800px;
        }
        .wrapper{ 
            padding: 20px; 
            background-color: white;
            border: 1px solid #eaeaea;
            border-radius: 4px;
        }
        .user-info {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        .page-header {
            margin-top: 0;
        }
    </style>
    <script>
        window.onload = function()
        {
            // logout function
            $(document).off("click", "#logout-button");
            $(document).on("click", "#logout-button", function()
            {
                window.location.href = "logout.php";
            });
        }
    </script>
</head>
<body>
    <div class="content-block">
        <div class="wrapper">
            <div class="page-header">
                <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to your dashboard.</h1>
            </div>
            <div class="user-info">
                <h3>Your Account Information</h3>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($userDetails['Email']); ?></p>
                <p><strong>Last Login:</strong> <?php echo $userDetails['LastLoginTime'] ? $userDetails['LastLoginTime']->format('Y-m-d H:i:s') : 'Never'; ?></p>
                <p><strong>IP Address:</strong> <?php echo htmlspecialchars($userDetails['IPAddress']); ?></p>
                <p><strong>Account Created:</strong> <?php echo $userDetails['CreatedAt']->format('Y-m-d H:i:s'); ?></p>
                <p><strong>Permission Level:</strong> <?php echo htmlspecialchars($_SESSION["userpermission"]); ?></p>
            </div>
            <p>
                <a href="logout.php" class="btn btn-danger">Sign Out</a>
            </p>
        </div>
    </div>
</body>
</html>
<?php
sqlsrv_close($conn);
?>