<?php
    session_start();
    
    if($_SERVER['REQUEST_METHOD']=="GET"){
        if(isset($_SESSION['role']) && $_SESSION['role'] == "staff" && $_SESSION['level'] == 5){
            $_SESSION['cryptMethod'] = 'AES-256-CBC';
            $_SESSION['cryptKey'] = openssl_random_pseudo_bytes(32);
            $_SESSION['cryptIV'] = openssl_random_pseudo_bytes(openssl_cipher_iv_length($_SESSION['cryptMethod'])); 
        }else{
            header('Location: login');
        }
    }
    if($_SERVER['REQUEST_METHOD']=="POST"){
        $conn = new mysqli(getenv('DATABASE_HOST'), getenv('DATABASE_USER'), getenv('DATABASE_PASS'), getenv('DATABASE_NAME'));
        if(isset($_POST['paymentDetailsInput'])){ handleDetails($conn); }
        $conn->close();
    }
     
    
    function say($data, $statusCode = 200){
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }
 
    if($_SESSION['role'] == "staff" && $_SESSION['level'] == 5 && isset($_GET['payout'])){
        require('main/N2A.php');
        
        
        $conn = new mysqli(getenv('DATABASE_HOST'), getenv('DATABASE_USER'), getenv('DATABASE_PASS'), getenv('DATABASE_NAME'));
        $result = $conn->query("
            SELECT f.id AS fid, SUM(c.arrived * o.price) AS owed, 
                MAX(f.AHN) AS AHN, 
                MAX(f.BAN) AS BAN, 
                MAX(f.RN) AS RN, 
                MAX(f.BAT) AS BAT FROM `commit` c INNER JOIN `filler` f ON c.uid = f.id INNER JOIN `order` o ON o.id = c.oid WHERE c.status = 1 GROUP BY f.id");
        
        $filename = "payout_" . date('Y-m-d') . ".csv";
        ob_clean();
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Pragma: no-cache');
        $output = fopen('php://output','w');
        
        while ($row = $result->fetch_assoc()){
            if($row['BAT'] == 0){ $BAT = "CCD"; }
            else{ $BAT = "PPD"; }
            $test = [
                $row['RN'],
                $row['BAN'],
                $BAT,
                $row['AHN'],
                N2A($row['fid']),
                $row['owed']
            ];
            fputcsv($output, $test);
        }
    
        fclose($output);
        $conn->close();
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>BuyToFill</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="handheldfriendly" content="true"/>
    <meta name="MobileOptimized" content="width"/>
    <meta name="description" content="BuyToFill Dashboard"/>
    <meta name="author" content=""/>
    <meta name="keywords" content="BuyToFill"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <link rel="icon" href="main/favicon.ico"/>
    <link rel="stylesheet" href="main/style.css">
  </head>
  <body data-theme="dark">
        <div class="preloader"><img src="main/favicon.ico"/></div>
        <?require("main/top.php")?>
        <div>
            <?require("main/side.php")?>
            <div>
                <?require("main/bread.php")?>
                <div>
                    <button onclick="window.location.href = '/payout?payout'" style="padding: .5rem 1.5rem; color: #000; font-weight: 600; border-radius: .4rem; border: 0; background: #6CEBA5">Download Payouts</button>
                </div>
            </div>
        </div>
        <script src="main/main.js"></script>
  </body>
</html>