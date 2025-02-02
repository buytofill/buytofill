<?php
session_start();
if(!isset($_SESSION['user']['id'])){
    header('Location: ..');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JSD - Profile</title>
</head>
<body>
    
</body>
</html>