<?php
session_start();
$host = 'localhost';
$db = 'jsdistributiondb';
$user = 'root';
$pass = '';

if(isset($_GET['token']) && $_SESSION['user']['emailToken'] == $_GET['token']){
    echo $_SESSION['user']['emailToken'];
    $conn = new mysqli($host, $user, $pass, $db);

    $hashed_password = password_hash($_SESSION['user']['pwd'], PASSWORD_DEFAULT);
    $insert_query = "INSERT INTO users (fn, email, pwd) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    if(!$stmt){echo "Error preparing statement: " . $conn->error; exit();}
    $stmt->bind_param('sss', $_SESSION['user']['fn'], $_SESSION['user']['email'], $hashed_password);
    if ($stmt->execute()){}else{echo "Error: " . $stmt->error;}
    session_unset(); session_destroy(); 
    header('Location: ../login.php');
}else{
    header('Location: ../signup.php');
}
?>