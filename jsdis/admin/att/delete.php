<?php
    session_start();
    if(!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] != "999"){
        header('Location: ..');
        exit;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $conn = new mysqli('208.109.25.227', 'ajsgdcxboylhkAfs', 'U1uSxWV{~}7E', 'jsdistributiondb');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
    
        $query = $_POST["upcToDel"];
        $action = "deleted $query";
    
        $insertStmt = $conn->prepare("INSERT INTO `log` (`id`, `action`) VALUES (?, ?)");
        $insertStmt->bind_param("is", $_SESSION['user']['id'], $action);
    
        if ($insertStmt->execute()) {
            // Log entry inserted successfully, now proceed with deletion
            $deleteStmt = $conn->prepare("DELETE FROM stock WHERE code LIKE ?");
            $deleteStmt->bind_param("s", $query);
            
            if ($deleteStmt->execute()) {
                echo "Deletion and log entry insertion successful.";
            } else {
                echo "Error deleting record: " . $conn->error;
            }
    
            $deleteStmt->close();
        } else {
            echo "Error inserting log entry: " . $conn->error;
        }
    
        $insertStmt->close();
        $conn->close();
    }    
?>

