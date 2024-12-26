 <?php
    session_start();
  
    if($_SERVER['REQUEST_METHOD']=="GET"){
        if(isset($_SESSION['role']) && $_SESSION['role'] == "staff"){
            $_SESSION['cryptMethod'] = 'AES-256-CBC';
            $_SESSION['cryptKey'] = openssl_random_pseudo_bytes(32);
            $_SESSION['cryptIV'] = openssl_random_pseudo_bytes(openssl_cipher_iv_length($_SESSION['cryptMethod'])); 
        }else{
            header('Location: login');
        }
    }
    if($_SERVER['REQUEST_METHOD']=="POST"){
        $conn = new mysqli(getenv('DATABASE_HOST'), getenv('DATABASE_USER'), getenv('DATABASE_PASS'), getenv('DATABASE_NAME'));
         
        $conn->close();
    }
    function say($data, $code = 200){
        echo json_encode($data, $code);
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
        <style>
            body>div{
                overflow: hidden !important;
                padding-bottom:2rem; 
                overflow-y: scroll !important;
                take: overflow-x and overflow-y !important;
            }
            #container{
                margin-top:1rem;
                overflow:visible;
                display:flex;
                flex-direction:column;
                input{
                    outline:0;
                    margin-bottom:1rem;
                }
            }
        </style>
    </head>
    <body data-theme="dark">
        <?require('main/main.php')?>
        <div>
            <header>
                <div>
                    <h2>Welcome back, <?echo $_SESSION['fn']?></h2>
                    <h5><?echo date("l, F j")?></h5>
                </div>
                <div>
                    <button>Level <?echo $_SESSION['level']?></button>
                    <button>Profile</button>
                </div>
            </header>
            <div id="container">
                <?
                    $conn = new mysqli(getenv('DATABASE_HOST'), getenv('DATABASE_USER'), getenv('DATABASE_PASS'),  getenv('DATABASE_NAME')); 
                    
                    $conn->close();
                ?>
                <input id="sfil" type="text" placeholder="Search fillers...">
                <input id="sfil" type="text" placeholder="Search items...">
            </div>
        </div>
        <script src="main/main.js"></script>
  </body>
</html>