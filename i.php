 <?php
    session_start();
  
    if($_SERVER['REQUEST_METHOD']=="GET"){
        if(isset($_SESSION['role']) && $_SESSION['role'] == "staff"){
            $_SESSION['cryptMethod'] = 'AES-256-CBC';
            $_SESSION['cryptKey'] = openssl_random_pseudo_bytes(32);
            $_SESSION['cryptIV'] = openssl_random_pseudo_bytes(openssl_cipher_iv_length($_SESSION['cryptMethod'])); 
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
            }
            #container{
                margin-top:1rem;
                overflow:visible;
                #button-holder{
                    display:flex;
                    gap:1rem;
                    padding:0 0 1rem 0;
                    a{
                        box-shadow: 1px 1px 4px black;
                        cursor: pointer;
                        height: fit-content;
                        padding: .4rem 1rem;
                        border-radius: 100px;
                        border: 1px solid #464646;
                        background: #232128;
                        color: #ddd;
                        transition:scale .3s ease;
                        &:hover{scale: 1.1}
                    }
                }
            }
            #inv{
                display:flex;
                flex-direction:column;
                gap:.5rem;
                div{
                    display:flex;
                    justify-content:space-between;
                    align-items:center;
                    span{
                        width: calc(100% - 175px);
                        padding-right:1rem;
                        overflow:scroll;
                        text-wrap: nowrap;
                    }
                    input{
                        box-shadow: 1px 1px 4px black;
                        outline:0;
                        padding: .4rem 1rem;
                        border-radius: .8rem;
                        border: 1px solid #464646;
                        text-align:right;
                        background: #232128;
                        color: #ddd;
                        transition: scale .3s ease;
                    }
                }
            }
        </style>
    </head
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
                <div id="button-holder">
                    <a>Custom POs</a>
                    <a>Inventory</a>
                    <a>Invoices</a>
                </div>
                <div id="inv">
                <?
                    $conn = new mysqli(getenv('DATABASE_HOST'), getenv('DATABASE_USER'), getenv('DATABASE_PASS'), getenv('DATABASE_NAME'));
                    $stmt = $conn->prepare("SELECT * FROM `item`");
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()){
                ?>
                    <div>
                        <span><?echo $row['name']?></span>
                        <input value="<?echo $row['stock']?>">
                    </div>
                <?
                    }
                    $conn->close();
                ?>
                </div>
            </div>
        </div>
        <script src="main/main.js"></script>
  </body>
</html>