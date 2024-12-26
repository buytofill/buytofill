<?
    session_start();
    
    if ($_SERVER['REQUEST_METHOD'] == "GET") {
        if (isset($_SESSION['role']) && $_SESSION['role'] == "staff") {
            $_SESSION['cM'] = 'AES-256-CBC';
            $_SESSION['cK'] = openssl_random_pseudo_bytes(32);
            $_SESSION['cI'] = openssl_random_pseudo_bytes(openssl_cipher_iv_length($_SESSION['cM'])); 
            
            function enc($string) {
                return openssl_encrypt($string,$_SESSION['cM'],$_SESSION['cK'],0,$_SESSION['cI']);
            }
        }else{
            
            header('Location: login');
        }
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $conn = new mysqli(getenv('DATABASE_HOST'), getenv('DATABASE_USER'), getenv('DATABASE_PASS'), getenv('DATABASE_NAME'));
        
        function dav($encryption) {
            return filter_var(openssl_decrypt($encryption, $_SESSION['cM'], $_SESSION['cK'], 0, $_SESSION['cI']), FILTER_VALIDATE_INT) ?: false;
        }
        
        $uid = $_SESSION['uid'];
        
        
        
        
        $conn->close();
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?require('main/head.php')?>
        <title>BuyToFill</title>
        <link rel="stylesheet" href="main/styles.css"/>
        <style>
            #content{padding: 0 2rem}
            .grid-container{
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                grid-template-rows: repeat(3, 1fr);
                gap: 1.5rem;
                width: 100%;
                padding: 2rem 0;
                overflow-y:scroll;
            }
            .grid-container::-webkit-scrollbar{display:none}
            .box {
                display: flex;
                justify-content: center;
                align-items: center;
                border: 1px solid #333;
                background: #151515;
                border-radius: .25rem;
                min-height:200px;
                min-width:200px;
                flex-direction:column;
            }
            .b-head{
                width:100%;
                padding: .5rem .8rem;
                font-size: .9rem;
                font-weight:600;
                color: #555;
                border-bottom: 1px solid #333;
            }
            .b-main{
                height: 100%;
                width: 100%;
                padding: 0 .5rem;
                display: flex;
                flex-direction: column;
                overflow: hidden;
            }
            .b-main>div>button, .b-main>div>label{
                width:100%;
                background: transparent;
                border: 0;
                color: #ccc;
                padding: .5rem;
                cursor:pointer;
                font-weight:600;
                display:flex;
                height:35px;
                align-items:center;
                justify-content:center;
                border-radius: .1rem;
            }
            .b-main>div::-webkit-scrollbar{display:none}
            .b-main>input{
                background: transparent;
                border: 1px solid #333;
                color:#ccc;
                outline:0;
                margin-top: .5rem;
                padding: .5rem;
                border-top-right-radius: .5rem;
                border-top-left-radius: .5rem;
            }
            .b-main .upinbtns button{
                background: #222;
            }
            .b-main .upinbtns label{
                background: #222;
                padding: .5rem;
                font-size:.85rem;
                cursor:pointer;
                font-weight:600;
                border:0;
                color: #ccc;
                border-radius: .1rem;
                height:35px;
            }
            .b-main button:has(svg){padding:0}
            .box1 { grid-column: span 2; }
            .box2 { grid-row: span 2; }
            .bm-holder{
                overflow-y: scroll;
                display: flex;
                flex-direction: column;
                padding: .2rem;
                border: 1px solid #333;
                gap: .2rem;
                border-bottom:0;
                border-top:0;
            }
            .bm-holder>div{
                background:#1a1a1a; display: flex; padding: .5rem;
                color:#ccc;
                gap:.5rem;
                justify-content:space-between;
                align-items: center;
            }
            .bm-holder>div>span{
                text-wrap:nowrap;
                overflow-x: scroll;
                font-size:.8rem;
                &::-webkit-scrollbar{
                    display:none;
                }
            }
            .bm-holder>div>p{
                font-weight:600;
                font-size:.8rem;
            }
            .bm-managebtns{
                border:1px solid #333;
                border-bottom-left-radius:.4rem;
                display: flex;margin: 0 0 .5rem;
                border-bottom-right-radius:.4rem;
            }
            .bm-managebtns input{
                border: 1px solid #333;
                border-top:0;
                border-bottom:0;
                width:100%;
                background: transparent;
                padding: .4rem;color:#ccc;text-align:center;outline:0;
            }
            button svg{
                stroke: currentColor;
                height:20px;
                stroke-width: 1.5px;
                stroke-linecap: round;
            }
            input::-webkit-outer-spin-button,
            input::-webkit-inner-spin-button {
              -webkit-appearance: none;
              margin: 0;
            }
            .upinbtns{
                display: flex;margin: 0 0 .5rem; gap: .5rem;
            }
        </style>
    </head>
    <body>
        <?require('main/nav.php')?>
        <div id="content-wrapper">
            <?require('main/header.php')?>
            <div id="content">
                <div class="grid-container">
                    <div class="box box2">
                        <div class="b-head">
                            Inventory
                        </div>
                        <div class="b-main">
                            <input type="text" placeholder="Search" onchange="invSearch(this)">
                            <div class="bm-holder"><?
                            
        $conn = new mysqli(getenv('DATABASE_HOST'), getenv('DATABASE_USER'), getenv('DATABASE_PASS'), getenv('DATABASE_NAME'));
        $uid = $_SESSION['uid'];
        $stmt = $conn->prepare("
            SELECT 
                id, 
                name, color, modelNumber,
                stock
            FROM `item`
            WHERE stock > 0
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                
                              ?><div>
                                    <span><?echo $row['name']?> - <?echo $row['color']?> - <?echo $row['modelNumber']?></span>
                                    <p><?echo $row['stock']?></p>
                                </div><?
                                
            }
        }
        
        $stmt->close();
        $conn->close();
                                
                          ?></div>
                            <div class="bm-managebtns">
                                <button>
                                    <svg viewBox="0 0 24 24">
                                        <path d="m13 15.2 6.1 4.5c1.3.9 2.9-.3 2.9-2.1l0-2.6m-9-6.2 6.1-4.5c1.3-.9 2.9.3 2.9 2.1l0 4.6"></path>
                                        <path d="m6.6 7.7 3.7-2.4c1.2-.8 2.7.2 2.7 1.8v9.8c0 1.6-1.5 2.6-2.7 1.8l-7.4-4.8c-1.2-.9-1.2-2.9 0-3.8l.9-.6"></path>
                                    </svg>
                                </button>
                                <input type="number" value="1" min="1" max="10" onchange="console.log(this)">
                                <button>
                                    <svg viewBox="0 0 24 24">
                                        <path d="m11 15.2-6.1 4.5c-1.3.9-2.9-.3-2.9-2.1l0-2.6m9-6.2-6.1-4.5c-1.3-.9-2.9.3-2.9 2.1l0 4.6"></path> 
                                        <path d="m17.4 7.7-3.7-2.4c-1.2-.8-2.7.2-2.7 1.8l0 9.8c0 1.6 1.5 2.6 2.7 1.8l7.4-4.8c1.2-.9 1.2-2.9 0-3.8l-.9-.6"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="upinbtns">
                                <label for="file-u">Upload</label>
                                <input type="file" id="file-u" accept=".csv" hidden onchange="console.log(this)">
                                <label for="file-i">Invoice</label>
                                <input type="file" id="file-i" accept=".csv" hidden onchange="console.log(this)">
                                <button>Save</button>
                            </div>
                        </div>
                    </div>
                    <!--div class="box box2">
                        <div class="b-head">
                            Inventory
                        </div>
                        <div class="b-main">
                            <div>
                                text
                            </div>
                        </div>
                    </div>
                    <div class="box">
                        <div class="b-head">
                            Purchase Orders
                        </div>
                        <div class="b-main">
                            <div>
                                text
                            </div>
                        </div>
                    </div>
                    <div class="box box2">
                        <div class="b-head">
                            Custom POs
                        </div>
                        <div class="b-main">
                            <div>
                                text
                            </div>
                        </div>
                    </div>
                    <div class="box">
                        <div class="b-head">
                            Requested Payouts
                        </div>
                        <div class="b-main">
                            <div>
                                text
                            </div>
                        </div>
                    </div>
                    <div class="box box1">
                        <div class="b-head">
                            Invoices
                        </div>
                        <div class="b-main">
                            <div>
                                text
                            </div>
                        </div>
                    </div-->
                </div>
            </div>
        </div>
    </body>
    <script>
        function invSearch(a){
            console.log(a.value)
        }
        function toCloud(a,e){
            e.preventDefault();
            
            var contents = e.target.result;
            fetch('process',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`fileContent=`+contents})
            .then(response => response.text())
            .then(data => {
                console.log(data);
            });
        }
    </script>
</html>