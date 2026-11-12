<?php
session_start();
if (!isset($_SESSION['userID'])) {
    header('refresh: 0; url=signin.html');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <style>
        body {
            background-color: #09f3f3;
            text-align: center;
        }
    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
</head>
<body>
    Username: <?php echo $_SESSION['fNAME'] . " " . $_SESSION['lNAME']; ?><br>
    <a href="signin.html">logout</a>
</body>
</html>
