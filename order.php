<?php
    session_start();
    require('main/env.php');
    
    if($_SERVER['REQUEST_METHOD']=="GET"){
            $_SESSION['cryptMethod'] = 'AES-256-CBC';
            $_SESSION['cryptKey'] = openssl_random_pseudo_bytes(32);
            $_SESSION['cryptIV'] = openssl_random_pseudo_bytes(openssl_cipher_iv_length($_SESSION['cryptMethod'])); 
   
    }
    if($_SERVER['REQUEST_METHOD']=="POST"){
        $conn = new mysqli(getenv('DATABASE_HOST'), getenv('DATABASE_USER'), getenv('DATABASE_PASS'), getenv('DATABASE_NAME'));
        if(isset($_POST['search'])){search($conn);}
        if(isset($_POST['qty'])){requestOrder($conn);}
        $conn->close();
    }
    function search($conn){
        $searchTerm = $_POST['search'];
        $stmt = $conn->prepare("(SELECT * FROM `item` WHERE `upc` LIKE ? LIMIT 5) UNION ALL (SELECT * FROM `item` WHERE `upc` NOT LIKE ? ORDER BY RAND() LIMIT 5) LIMIT 5");
        $likeTerm = '%' . $searchTerm . '%';
        $stmt->bind_param("ss",$likeTerm,$likeTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        $all = [];
        while ($row = $result->fetch_assoc()) {
            $all[] = $row;
        }
        print_r(json_encode($all));
        $stmt->close();
        exit;
    }
    function requestOrder($conn){
        $qty = $_POST['qty'];
        $price = $_POST['price'];
        $expiration = $_POST['expir'];
        $uid = $_SESSION['uid'];
        if($_POST['item']){
            $item = $_POST['item'];
            $stmt = $conn->prepare("INSERT INTO `order` (pid, uid, qty, original, expiration) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iiids", $item, $uid, $qty, $price, $expiration);
        }else if($_POST['upc']){
            $upc = $_POST['upc'];
            $stmt = $conn->prepare("INSERT INTO `order` (pupc, uid, qty, original, expiration) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iiids", $upc, $uid, $qty, $price, $expiration);
        }
        
        $stmt->execute();
        $stmt->close();
        
        say('Requested Order');
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
            #just-sold{
                margin-top:1rem;
                >p{
                    margin-left:1rem;
                    text-align:left;
                    font-size: 5rem;
                    pointer-events:none;
                    font-weight: bolder;
                    background: -webkit-linear-gradient(var(--purple), #1f1e1e);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                }
                >.container{
                    margin: -2rem 0 0;
                    padding:1.5rem 1rem 1rem;
                    background:#131216;
                    box-shadow:0 2px 5px #131216aa;
                    border-radius:.2rem;
                    display:flex;
                    flex-direction: row-reverse;
                    gap:.5rem;
                    .indoors{
                        width:10%;
                        flex:1;
                        aside{
                            display:flex;
                            justify-content:space-between;
                            align-items:center;
                            padding:0 .5rem .3rem;
                            p{
                                font-size:1rem;
                                font-weight:500;
                            }
                            div{
                                display:flex;
                                flex-direction:row;
                                gap:.5rem;
                                button{
                                    box-shadow: 1px 1px 4px black;
                                    cursor: pointer;
                                    height: fit-content;
                                    padding: .4rem 1rem;
                                    border-radius: 100px;
                                    border: 1px solid #464646;
                                    background: #232128;
                                    color: #ddd;
                                }
                            }
                        }
                        div{
                            padding-top: 2px;
                            display:flex;
                            flex-direction:column;
                            .item-name{
                                padding:.4rem .6rem;
                                border-radius:.5rem;
                                margin-top:-2px;
                                cursor:pointer;
                                position:relative;
                                &:hover{
                                    background: linear-gradient(-45deg, #1c1b20, #333);
                                    box-shadow:0 1px 4px #00000050;
                                    p{
                                        color: #fff;
                                        font-weight:500;
                                    }
                                    &:after{
                                        opacity:0;
                                    }
                                }
                            }
                            p{
                                color:#bbb;
                                font-weight: 200;
                                overflow:scroll;
                                text-wrap: nowrap;
                            }
                            .selected p{
                                color:var(--green) !important;
                                font-weight:500;
                            }
                            .item-name:not(:last-child):after{
                                content:'';
                                position:absolute;
                                left:.6rem;
                                bottom:0;
                                width:calc(100% - 1.2rem);
                                height:1px;
                                background:#1c1b20;
                            }
                        }
                    }
                    .img-container{
                        display:flex;
                        justify-content:center;
                        align-items:center;
                        padding: 1rem;
                        box-shadow:0 1px 4px #00000050;
                        background: #1c1b20;
                        border-radius: .5rem;
                        width:200px;
                        img{
                            width: fit-content;
                            height: fit-content;
                            max-width: 100%;
                            max-height: 100%;
                        }
                    }
                }
            }
            
            #order-with-us{
                margin-bottom:2rem;
                >p{
                    margin-right:1rem;
                    text-align:right;
                    font-size: 5rem;
                    pointer-events:none;
                    font-weight: bolder;
                    background: -webkit-linear-gradient(var(--purple), #1f1e1e);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                }
                >div{
                    padding:1rem;
                    background:#131216;
                    box-shadow:0 2px 5px #131216aa;
                    border-radius:.2rem;
                    margin-top:-2rem;
                    display:flex;
                    flex-direction:row;
                    >div{
                        width:100%;
                        max-width:55%;
                        &:first-child{
                            max-width:45%;
                            padding-right:1rem;
                        }
                    }
                    #searchwithus{
                        input{
                            margin-bottom:1rem;
                            padding: 1rem 1.5rem;
                            font-size: 1rem;
                            outline: 0;
                            color: white;
                            width: 100%;
                            background: #161616;
                            border-radius: .5rem;
                            border:1px solid transparent;
                            box-shadow:0 1px 4px #00000050;
                            &:hover{
                                border:1px solid #333;
                            }
                            &:focus{
                                border:1px solid #474BFF;
                            }
                        }
                        button{
                            margin-top: .5rem;
                            padding: .7rem 1rem;
                            position: relative;
                            font-weight: bold;
                            width: 100%;
                            border: 0;
                            box-shadow: 1px 1px 4px black;
                            border-radius: 100px;
                            background: var(--green);
                            cursor: pointer;
                        }
                        .item-name{
                            padding:1rem;
                            text-wrap: nowrap;
                            overflow: scroll;
                            position:relative;
                            border-radius:.5rem;
                            cursor:pointer;
                            &:hover{
                                background: linear-gradient(-45deg, #1c1b20, #333);
                                box-shadow: 0 1px 4px #00000050;
                                &:after{
                                    opacity:0;
                                }
                            }
                            &:not(:last-child){
                                &:after{
                                    content:'';
                                    left:1rem;
                                    bottom:0;
                                    position:absolute;
                                    width:calc(100% - 2rem);
                                    height:1px;
                                    background:#1c1b20;
                                }
                            }
                        }
                        .selected{
                            color: var(--green);
                            font-weight: bold;
                        }
                    }
                    #wideresults{
                        background: #161616;
                        border-radius: .5rem;
                        box-shadow:0 1px 4px #00000050;
                        width:100%;
                        padding:1rem;
                        display: flex;
                        position:relative;
                        flex-direction: column;
                        justify-content: space-between;
                        >div{
                            &:first-child{
                                height:100%;
                            }
                            >p{
                                font-weight:600;
                                text-wrap: nowrap;
                                overflow: scroll;
                                position:absolute;
                                top: 1.75rem;
                                left: 1.75rem;
                                width: calc(100% - 3.5rem);
                            }
                            >div{
                                overflow: scroll;
                                border-radius: .2rem;
                                display: flex;
                                gap: .5rem;
                                >span{
                                    font-weight: 100;
                                    box-shadow: 1px 1px 4px black;
                                    padding: .4rem 1rem;
                                    border-radius: .2rem;
                                    font-size: .8rem;
                                    border: 1px solid #464646;
                                    background: #232128;
                                    color: #ddd;
                                    display:flex;
                                    >span{
                                        width: 15px;
                                        height: 15px;
                                        margin:.02rem .5rem 0 -.2rem;
                                        display: block;
                                        border: 1px solid;
                                        border-radius: 100px;
                                    }
                                }
                            }
                            .img-container{
                                margin-bottom:.5rem;
                                height:40%;
                                display:flex;
                                justify-content:center;
                                align-items:center;
                                max-height: 140px;
                                padding: 1rem;
                                background:#1c1b20;
                                box-shadow: 0 1px 4px #00000050;
                                border: 1px solid var(--purple-tint);
                                border-radius:.25rem;
                                img{
                                    max-width: 100%;
                                    max-height: 100%;
                                }
                            }
                            .inputholder{
                                padding:.5rem 0 .5rem;
                            }
                            input{
                                padding: 1rem 1rem;
                                font-size: .9rem;
                                outline: 0;
                                color: white;
                                width: 100%;
                                background: #131216;
                                border-radius: .2rem;
                                border: 1px solid #333;
                                box-shadow: 0 1px 4px #00000050;
                                &:hover{
                                    border:1px solid #333;
                                }
                                &:focus{
                                    border:1px solid #474BFF;
                                }
                            }
                            #reqorder{
                                background: var(--purple);
                                border: 0;
                                width: 100%;
                                padding: .5rem;
                                border-radius: .5rem;
                                cursor:pointer;
                                color: white;
                                font-weight: bold;
                                box-shadow: 0 1px 5px #00000050;
                            }
                        }
                        
                    }
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
            <card id="just-sold">
                <p>Just Sold</p>
                <div class="container">
                    <?
                        $conn = new mysqli(getenv('DATABASE_HOST'), getenv('DATABASE_USER'), getenv('DATABASE_PASS'), getenv('DATABASE_NAME'));
                        $stmt = $conn->prepare("SELECT i.id,i.name,o.created FROM `order` o INNER JOIN `item` i ON o.pid = i.id ORDER BY o.id DESC LIMIT 10");
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $first = true;
                        $data = [];
                        while($row = $result->fetch_assoc()) {
                            $data[] = $row;
                        }
                        $timestamps = array_map(function($item) {
                            return strtotime($item['created']);
                        }, $data);
                        
                        $oldestDate = date('D, F j', min($timestamps));
                        $newestDate = date('D, F j', max($timestamps));
                    ?>
                    <div class="indoors">
                        <aside>
                            <p><?echo $oldestDate . " - " . $newestDate?></p>
                            <div>
                                <button>Refresh</button>
                                <button>View More</button>
                            </div>
                        </aside>
                        <div>
                        <?
                            foreach($data as $row){
                                ?>
                                    <div class="item-name <?if($first)echo 'selected'?>" onclick="setimg(<?echo $row['id']?>,this)"><p><?echo $row['name']?></p></div>
                                <?
                                $first = false;
                            }
                        ?>
                        </div>
                    </div>
                    <div class="img-container">
                        <img src="img?img=<?echo $data[0]['id']?>">
                    </div>
                </div>
            </card>
            <card id="order-with-us">
                <p>Order with Us</p>
                <div>
                    <?
                        $stmt = $conn->prepare("SELECT * FROM `item` LIMIT 5");
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $first = true;
                        $data = [];
                        while($row = $result->fetch_assoc()) {
                            $data[] = $row;
                        }
                    ?>
                    <div id="searchwithus">
                        <form onsubmit="event.preventDefault(); searchby(this)">
                            <input id="search" name="search" type="text" placeholder="Search by UPC" auto-complete="off">
                        </form>
                        <div id="results">
                            <?
                                foreach($data as $row){
                                    ?>
                                        <div class="item-name <?if($first)echo 'selected'?>" onclick="setorder(this,<?echo $row['id']?>,'<?echo $row['upc']?>','<?echo $row['name']?>','<?echo $row['brand']?>','<?echo $row['color']?>','<?echo $row['colorName']?>','<?echo $row['model']?>')"><?echo $row['upc']?> - <?echo $row['name']?></div>
                                    <?
                                    $first = false;
                                }
                            ?>
                        </div>
                        <button onclick="reqwsearch(this)">Can't find item? Request with Search</button>
                    </div>
                    <form id="wideresults" onsubmit="event.preventDefault(); requestOrder(this)">
                        <div>
                            <div class="img-container">
                                <img src="img?img=<?echo $data[0]['id']?>">
                            </div>
                            <div>
                                <span id="item-brand"><?echo $data[0]['brand']?></span>
                                <span id="item-upc"><?echo $data[0]['upc']?></span>
                                <span id="item-model"><?echo $data[0]['model']?></span>
                                <span id="item-color"><span style="background:#<?echo $data[0]['color']?>"></span><?echo $data[0]['colorName']?></span>
                            </div>
                            <div class="inputholder">
                                <input name="qty" type="text" placeholder="Quantity" auto-complete="off">
                                <input name="price" type="text" placeholder="Price" auto-complete="off">
                            </div>
                            <input name="expir" type="text" placeholder="Expiration" auto-complete="off">
                        </div>
                        <div>
                            <p id="item-n"><?echo $data[0]['name']?></p>
                            <input type="submit" id="reqorder" data-id="<?echo $data[0]['id']?>" value="Request Order">
                        </div>
                    </form>
                    <?
                        $conn->close();
                    ?>
                </div>
            </card>
        </div>
        <script>
            function reqwsearch(){
                document.querySelector('#item-n').remove();
                document.querySelector('#wideresults .img-container').remove();
                document.querySelector('#wideresults>div>div:first-child').remove();
            }
            function requestOrder(a){
                const formData = new FormData(a);
                if(!document.querySelector('#item-n')){
                    formData.append('upc', document.querySelector('#search').value);
                }else{
                    formData.append('item', a.children[1].children[1].getAttribute('data-id'));
                }
                fetch('#',{method:'POST',body:formData})
                .then(response => response.text())
                .then(data => {
                    console.log(data);
                });
            }
            let before = "";
            function searchby(a){
                let search = a.children[0].value;
                if(before != search && search != ""){
                    before = search;
                    const formData = new FormData(a);
                    fetch('#',{method:'POST',body:formData})
                    .then(response => response.json())
                    .then(data => {
                        let result = document.querySelector("#results");
                        result.innerHTML = "";
                        data.forEach(item => {
                            let upc = document.createElement('div');
                            upc.classList = "item-name";
                            upc.setAttribute('onclick',"setorder(this,'"+item.id+"','"+item.upc+"','"+item.name+"','"+item.brand+"','"+item.color+"','"+item.colorName+"','"+item.model+"')");
                            upc.textContent = item.upc+" - "+ item.name;
                            result.appendChild(upc);
                        })
                    });
                }
            }
            function setorder(t,id,upc,name,brand,color,colorname,model){
                document.querySelector('#reqorder').setAttribute('data-id',id);
                document.querySelectorAll('#searchwithus .selected').forEach(a => a.classList = "item-name");
                document.querySelector('#item-n').textContent = name;
                document.querySelector('#wideresults img').src = "img?img="+id;
                document.querySelector('#item-brand').textContent = brand;
                document.querySelector('#item-upc').textContent = upc;
                document.querySelector('#item-model').textContent = model;
                document.querySelector('#item-color').innerHTML = '<span style="background:#'+color+'"></span>'+colorname;
                t.classList = "item-name selected";
            }
            function setimg(a,t){
                document.querySelectorAll('.indoors .selected').forEach(a => a.classList = "item-name");
                t.classList = "item-name selected"
                document.querySelector('.img-container img').src = "img?img="+a
            }
        </script>
        <script src="main/main.js"></script>
  </body>
</html>