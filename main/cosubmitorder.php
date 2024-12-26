<?php
    session_start();


    if(isset($_POST['quantity']) && !empty($_POST['quantity']) && isset($_POST['target']) && !empty($_POST['target']) && isset($_POST['expi']) && !empty($_POST['expi']) && strtotime($_POST['expi']) > strtotime(date('m/d/Y'))){
        $conn = new mysqli(getenv('DATABASE_HOST'), getenv('DATABASE_USER'), getenv('DATABASE_PASS'), getenv('DATABASE_NAME'));
        if($conn->connect_error) die("Connection failed: " . $conn->connect_error);
        $stmt = $conn->prepare("INSERT INTO orders (upc, user, quantity, targetprice, expiration, stat) VALUES (?, ?, ?, ?, ?, ?)");
        $status = "0";
        if($stmt){
            $stmt->bind_param('ssssss', $_POST['upc'], $_SESSION['email'], $_POST['quantity'], $_POST['target'], $_POST['expi'], $status);
            $result = $stmt->execute();

            if($result) echo 'submit';
            $stmt->close();
        }
        $conn->close();
    }
?>