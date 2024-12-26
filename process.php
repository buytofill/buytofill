<?
    require 'assets/control.php';
    
    
    if($_SERVER['REQUEST_METHOD']=="GET"){
        if(isset($_SESSION['role']) && $_SESSION['role'] == "staff"){
            $_SESSION['cryptMethod'] = 'AES-256-CBC';
            $_SESSION['cryptKey'] = openssl_random_pseudo_bytes(32);
            $_SESSION['cryptIV'] = openssl_random_pseudo_bytes(openssl_cipher_iv_length($_SESSION['cryptMethod'])); 
        }else{
            header('Location: .');
        }
    }
    
    if($_SERVER["REQUEST_METHOD"]=="POST"){ 
        $conn = new mysqli(getenv('DATABASE_HOST'), getenv('DATABASE_USER'), getenv('DATABASE_PASS'), getenv('DATABASE_NAME'));
        if($conn->connect_error){ say(['err'=>'Database connection failed'],500); }
        if(isset($_POST['process'])){ handleProcess($conn); }
        if(isset($_POST['data'])){ handleConfirm($conn); }
        if(isset($_POST['addtrk'])){ addtrk($conn); }
        if(isset($_POST['confirmoid'])){ confirmoid($conn); }
        if(isset($_POST['cpofilecontents'])){ cpofilecontents($conn); }
        $conn->close();
    }
    
    function cpofilecontents($conn){
        $contents = explode("\n",$_POST['cpofilecontents']);
        $details = explode(",", $contents[0]);
        
        $cpoUserID = A2N($details[0]);
        $cpoUserMail = $details[1];
        
        $upcs = "";
        $qtys = "";
        $prices = "";
        
        $toCheck = [];
        $toQty = [];
        
        foreach ($contents as $content) {
            $content = explode(",", $content);
            if($details == $content){ continue; }
            if(!$upcs){ $upcs = $content[0]; }else{ $upcs = $upcs.",".$content[0]; }
            if(!$qtys){ $qtys = $content[1]; }else{ $qtys = $qtys.",".$content[1]; }
            if(!$prices){ $prices = $content[2]; }else{ $prices = $prices.",".$content[2]; }
            
            $toCheck[] = $content[0];
            $toQty[] = $content[1];
        }
        
        $missingUPCs = []; 
        
        $stmt = $conn->prepare("SELECT COUNT(*) FROM `item` WHERE upc = ?");
        
        foreach ($toCheck as $upc) {
            $stmt->bind_param("s", $upc);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($count);
            $stmt->fetch();
        
            if ($count == 0) $missingUPCs[] = $upc;
        }
        
        $stmt->close();
        
        if (!empty($missingUPCs)) {
            echo "UPCs not found in the items table:\n";
            foreach ($missingUPCs as $missingUPC) {
                echo $missingUPC . "\n";
            }
        } else {
            
            $conn->begin_transaction();
            
            try { 
                $stmt = $conn->prepare("INSERT INTO `cpo` (upc, qty, price, uid) SELECT ?, ?, ?, ? FROM dual WHERE EXISTS (SELECT 1 FROM `filler` WHERE id = ? AND email = ?)");
                $stmt->bind_param("sssiis", $upcs, $qtys, $prices, $cpoUserID, $cpoUserID, $cpoUserMail);
                if (!$stmt->execute() || $conn->affected_rows == 0) throw new Exception('Error inserting into cpo or no rows affected.');
            
                $updateStmt = $conn->prepare("UPDATE `item` SET stock = stock + ? WHERE upc = ?");
                foreach ($toCheck as $index => $upc) {
                    $qty = $toQty[$index];
                    $updateStmt->bind_param("is", $qty, $upc);
                    if (!$updateStmt->execute()) throw new Exception('Error updating item stock.');
                    
                }
                
                $conn->commit();
                say(['Success' => 'Data sent'], 200);
            
            } catch (Exception $e) {
                $conn->rollback();
                say(['err' => 'Error: ' . $e->getMessage()], 500);
            }


        }

        exit;
    }
    
    function handleConfirm($conn){
        // Begin a transaction
        $conn->begin_transaction();
    
        try {
            $uid = $_POST['data'];
            $a = explode(',', $_POST['qtyCid']);
            $trk = $_POST['tracking'];
            print_r($a);
            
            $info = array();
            for ($i = 0; $i < count($a); $i += 3) {
                $qty = $a[$i];
                $cid = dav($a[$i + 1]);
                $before = $a[$i + 2];
                // Validate $cid to ensure it is not false/empty
                if (!$cid) { 
                    // If $cid is invalid, throw an Exception
                    throw new Exception('Invalid or non-numeric ID');
                }
                $newTrk = $qty."-".$trk;
                $beforeTrk = $before."-".$trk;
                #echo $beforeTrk;
                echo 'wth';
                $stmt = $conn->prepare("
                    UPDATE 
                        `commit` AS c
                    JOIN 
                        `order` AS o ON c.oid = o.id
                    JOIN 
                        `item` AS i ON o.pid = i.id
                    SET 
                        c.qty = c.qty - ?, 
                        c.arrived = c.arrived + ?,
                        c.email = 0,
                        c.trackings = REPLACE(REPLACE(c.trackings, ?, ''), '  ', ' '),
                        c.scanned = CONCAT(?, ' ', c.scanned),
                        i.stock = i.stock + ?
                    WHERE 
                        c.id = ?
                ");
                
                $stmt->bind_param("iissii", $qty, $qty, $beforeTrk, $newTrk, $qty, $cid);
                $stmt->execute();
                echo 'wth';
                $stmt->close();

    
                $info[] = ['qty' => $qty, 'cid' => $cid];
            }
    
            $conn->commit();
            exit;
        } catch (Exception $e) {
            // An error occurred, rollback any changes made during the transaction
            $conn->rollback();
            // Respond with an error message or code
            say(['err' => $e->getMessage()], 400);
            exit;
        }
    }
    
    function handleProcess($conn){
        $res = mb_strtoupper($_POST['process']);
        $searchID;
        if(strlen($res) === 34){ 
            $res = substr($res, 22, 34); 
        }else if(strlen($res) == 30){
            $res = substr($res, 8, 30); 
        }else if(strlen($res) == 5){ 
            $searchID = A2N($res); 
        }
        $search = '%'.$res.'%';
        $stmt = $conn->prepare("SELECT c.trackings, c.uid, c.id AS cid, f.fn, f.ln, o.pid, i.name, i.brand, i.upc 
            FROM `commit` AS c 
            INNER JOIN `order` AS o ON c.oid = o.id 
            INNER JOIN `item` AS i ON o.pid = i.id 
            INNER JOIN `filler` AS f ON c.uid = f.id 
            WHERE (c.trackings LIKE ? OR c.uid = ?) AND c.status >= 0");
        $stmt->bind_param("si", $search, $searchID);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $trkmg = [];
        if ($result->num_rows > 0){
            while ($row = $result->fetch_assoc()){ 
                $trackings = explode(' ', $row['trackings']);
                foreach ($trackings as $tracking){
                    [$qty, $tr] = explode('-', $tracking);
                    if(($searchID != null || strpos($tracking, $res) !== false) && $qty != ""){
                        $trkmg[N2A($row['uid']).'-'.$row['fn'].' '.$row['ln'].'-'.$tr][] = [$qty,$row['name'],$row['brand'].' | '.$row['color'].' | '.$row['modelNumber'].' | '.$row['upc'],$row['pid'],enc($row['cid'])];
                    }
                }
            }
            say($trkmg);
        } else {
            say([]);
        }
    }
    
    function addtrk($conn){
        $stmt = $conn->prepare("SELECT c.trackings, c.uid, c.id AS cid, f.fn, f.ln, o.pid, i.name, i.manufacturer, i.modelNumber, i.upc 
            FROM `commit` AS c 
            INNER JOIN `order` AS o ON c.oid = o.id 
            INNER JOIN `item` AS i ON o.pid = i.id 
            INNER JOIN `filler` AS f ON c.uid = f.id 
            WHERE c.trackings LIKE ? OR c.uid = ?");
        $stmt->bind_param("si", $search, $searchID);
        $stmt->execute();
    }
    
    function confirmoid($conn){
        $res = mb_strtoupper($_POST['process']);
        $searchID;
        if(strlen($res) === 34){ 
            $res = substr($res, 22, 34); 
        }else if(strlen($res) == 5){ 
            $searchID = A2N($res); 
        }
        $search = '%'.$res.'%';
        $stmt = $conn->prepare("SELECT o.
            FROM `commit` AS c 
            INNER JOIN `order` AS o ON c.oid = o.id 
            INNER JOIN `item` AS i ON o.pid = i.id
            WHERE c.trackings LIKE ? OR c.uid = ?");
        $stmt->bind_param("si", $search, $searchID);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $trkmg = [];
        if ($result->num_rows > 0){
            while ($row = $result->fetch_assoc()){ 
                $trackings = explode(' ', $row['trackings']);
                foreach ($trackings as $tracking) {
                    [$qty, $tr] = explode('-', $tracking);
                    if($searchID != 0 || strpos($tracking, $res) !== false){
                        $trkmg[N2A($row['uid']).'-'.$row['fn'].' '.$row['ln'].'-'.$tr][] = [$qty,$row['name'],$row['manufacturer'].' | '.$row['color'].' | '.$row['modelNumber'].' | '.$row['upc'],$row['pid'],enc($row['cid'])];
                    }
                }
            }
            say($trkmg);
        } else {
            say([]);
        }
        echo $_POST['confirmoid'];
        exit;
    }

    function say($data, $statusCode = 200){
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>BuyToFill</title>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <meta name="handheldfriendly" content="true"/>
        <meta name="MobileOptimized" content="width"/>
        <meta name="description" content="BuyToFill"/>
        <meta name="author" content=""/>
        <meta name="keywords" content="BuyToFill"/>
        <link rel="icon" href="assets/favicon.ico"/>
        <link rel="stylesheet" href="assets/styles.css">
        <style>
            main header button{transition:all .5s ease;text-wrap:nowrap;padding:0;border-radius:2px;width:0;overflow:hidden;margin-left:-.5rem;cursor:pointer;font-weight:500;color:var(--col);background:var(--semi);border:0 solid var(--green-tint)}
            main header button:hover{color:var(--green);background:var(--green-bg);border-color:var(--green)}
            .btf-checkbox:hover{border:1px solid var(--green)}
            .btf-checkbox:has(input:checked){background:var(--green);border:1px solid var(--green)}
            td:nth-child(5){text-align:center}
            #trk-mg{flex-direction:column;overflow-y:scroll;gap:.5rem;display:flex;height:calc(100% - 52px);padding:.5rem;box-sizing:border-box}
            #trk-mg>div{box-shadow:-1px 1px 5px black;box-sizing:border-box;height:fit-content;width:100%;background:var(--semi);border:1px solid var(--green-tint);border-radius:4px;padding:.5rem}
            #trk-mg>div>div:first-child{display:flex;justify-content:space-between;align-items:center}
            #trk-mg>div>div:first-child>button,#trk-mg button{text-wrap:nowrap;margin-left:.5rem;box-shadow:-1px 1px 5px black;padding:.5rem 1.2rem;font-weight:bold;background:var(--green-bg);color:#5ca767;border:1px solid;border-radius:2px;cursor:pointer}
            #trk-mg>div>div:first-child>button:hover,#trk-mg button:hover{color:var(--green)}
            #trk-mg>div>div:first-child>div{color:#AAA;font-size:1rem;font-weight:600;margin-right:auto}
            #trk-mg>div>div:first-child>div>p:last-child{color:#999;font-size:.7rem;font-weight:400}
            #trk-mg>div>div:last-child{display:flex;flex-direction:column;margin-top:.5rem;gap:.5rem}
            #trk-mg>div>div:last-child>div{box-shadow:-1px 1px 5px black;border:1px solid var(--green-tint);background:var(--mid);height:50px;padding:.5rem;border-radius:2px;display:flex;box-sizing:border-box}
            #trk-mg>div>div:last-child>div>div{width:100%;justify-content: space-between;text-wrap:nowrap;display:flex;flex-direction:column;overflow:hidden}
            #trk-mg>div>div:last-child>div>aside{min-height:100%;aspect-ratio:1;height:100%;display:flex;align-items:center;padding-right:.5rem;justify-content:center}
            #trk-mg>div>div:last-child>div>div>p:first-child{color:#999;font-weight:600;font-size:.9rem}
            #trk-mg>div>div:last-child>div>div>p{width:100%;color:#999;font-weight:400;font-size:.7rem;overflow:scroll}
            #trk-mg>div>div:last-child>div>aside>img{max-height:100%;scale:1.15;max-width:100%}
            #trk-mg div div div input{box-shadow:-1px 1px 5px black;background:var(--semi);border:1px solid var(--green-tint);border-radius:2px;width:7ch;outline:0;color:white;text-align:right;padding:0 .5rem;box-sizing:border-box}
            main header .user{width:170px;padding:0 1rem;border-width:1px;margin-left:0}
            #trk-mg>div>div:last-child:has(div){margin-top:.5rem}
            input[name="OID"]{width:100%!important;text-align:left!important;margin:0 .5rem}
            #trk-mg button{margin:0}
            #trk-mg .red{background:#8036367a;color:#ae5a5a}
            #trk-mg .red:hover{color:#d24c4c}
            
            ::-webkit-scrollbar{display:none}
            
            main>div{background:#161616;height:100%;border-radius:4px;overflow:hidden}
             main header{background:#161616;padding:.5rem;display:flex;border-bottom:1px solid var(--tint);box-sizing:border-box;height:52px;gap:.5rem}
            main header input{width:100%;border-radius:2px;background:#0F1012;padding:.3rem .6rem;border:0;color:white;outline:1px solid var(--tint)}
            main header input:hover{outline-offset:2px}
            main header input:focus{outline:1px solid var(--grn);outline-offset:1px}
            main header > input::placeholder{color:#555;font-style:italic;font-weight:500}
            main header > div{display:flex;align-items:center}
            main header > div > input{margin:0 .5rem;width:5ch;text-align:center;padding:.6rem .1rem}
            main header > div > svg{height:14px;fill:none;padding-left:1px}
            main header > div > svg path{stroke:var(--tint);stroke-width:2px;stroke-linecap:round;stroke-linejoin:round}
            
        </style>
    </head>
    <body> 
        <?require 'assets/header.php'?>
        <nav>
            <main>
                <a href="#" class="y">Process</a>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none">
                    <path d="m19 18.5 4.5 4.575m-3-8.25c0 2.925-2.325 5.25-5.25 5.25-2.925 0-5.25-2.325-5.25-5.25 0-2.925 2.325-5.25 5.25-5.25 2.925 0 5.25 2.325 5.25 5.25" stroke-linecap="round" stroke="#fff" stroke-width=1.5 stroke-linejoin="round"/>
                </svg>
                <form>
                    <input type="text" placeholder="Search">
                </form>
            </main>
            <div>
                <a onclick="">Remove</a>
                <hr>
                <a>page controller</a>
            </div>
        </nav>
        <main>
            <header>
                <input type="text" id="filter" placeholder="Filter by UID or trackings." autocomplete="off">
                <button onclick="addtrk()" id="addtrk">Add Tracking</button>
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg"viewBox="0 0 16 16"><path d="M12 1 5 8l7 7"></path></svg>
                    <input type="num"value="1"name="page">
                    <svg xmlns="http://www.w3.org/2000/svg"viewBox="0 0 16 16"><path d="m4 1 7 7-7 7"></path></svg>
                </div>
            </header>
            <div id="trk-mg">
                <form action="/process" onsubmit="uploadcpo(this,event)" method="post" enctype="multipart/form-data" style="display:flex;justify-content:space-between">
                    <input type="file" name="fileUpload" accept=".csv" style="color:white">
                    <input type="submit" style="cursor:pointer;background:var(--green-bg);padding:.2rem 1rem;border:1px solid;color:var(--green);border-radius:2px"></input>
                </form>
            </div>
        </main>
        <script>
            let a = document.querySelector('#trk-mg');
            let filter = document.querySelector('#filter');
            
            let debounceTimer;
            
            function uploadcpo(a,e){
                e.preventDefault();
                
                file = a.querySelector('input').files[0];
                var reader = new FileReader();
                let text = reader.readAsText(file);
                reader.onload = function(e){
                    var contents = e.target.result;
                    fetch('process',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`cpofilecontents=`+contents})
                    .then(response => response.text())
                    .then(data => {
                        console.log(data);
                    });
                };
            }

            filter.addEventListener('input', function(){
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    let fv = filter.value;
                    fetch('process',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`process=`+fv})
                    .then(response => response.json())
                    .then(data => {
                        console.log(data);
                        let addtrk = document.querySelector('#addtrk')
                        if(/^[a-zA-Z]{5}$/.test(fv) && fv.length == 5)addtrk.classList = "user"
                        else addtrk.classList = "";
                        a.innerHTML="";
                        for(let res in data){
                            let re = res.split('-');
                            let c = document.createElement('div');
                            let tc = child(c,'div');
                            let io = child(c,'div');
                            for(var i = 0; i<data[res].length; i++){
                                let ic = child(io,'div');
                                let ica = child(ic,'aside');
                                let icc = child(ic,'div');
                                child(ic,'input',{value:data[res][i][0],before:data[res][i][0],cid:data[res][i][4],type:'number',placeholder:'Qty',name:'Qty'});
                                child(ica,'img',{src:'img?img='+data[res][i][3]});
                                child(icc,'p',{text:data[res][i][1]});
                                child(icc,'p',{text:data[res][i][2]});
                            };
                            let icd = child(tc,'div');
                            child(tc,'button',{onclick:'addItem(this)',text:'Add Item',data:re[0]});
                            child(tc,'button',{onclick:'confirm(this)',text:'Confirm',data:re[0],tracking:re[2]});
                            child(icd,'p',{text:re[2]});
                            child(icd,'p',{text:re[0]+" | "+re[1]});
                            a.appendChild(c);
                        }
                    });
                }, 1000);
            });
            
            function confirmOID(a){
                let par = a.parentNode;
                let av = par.querySelector('input').value;
                fetch('process',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`confirmoid=`+av})
                .then(response => response.text())
                .then(data => {
                    //console.log(data);
                    sendNotif("Confirmed",200);
                });
            }
            
            function addItem(a){
                let b = a.parentNode.parentNode.querySelector('div:last-child')
                let c = document.createElement('div');
                child(c,'button',{text:'Cancel',onclick:'cancelAddTrk(this)',class:"red"});
                child(c,'input',{type:'number',placeholder:'OID',name:'OID'});
                child(c,'button',{text:'Confirm OID',onclick:'confirmOID(this)'});
                b.appendChild(c);
            }
            function cancelAddTrk(a){
                a.parentNode.remove();
            }
            function addtrk(a){
                let fv = filter.value;
                fetch('process',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`addtrk=`+fv})
                .then(response => response.text())
                .then(data => {
                    console.log(fv);
                });
                /*let c = document.createElement('div');
                let tc = child(c,'div');
                let io = child(c,'div');
                let icd = child(tc,'div');
                child(tc,'button',{onclick:'addCommit(this)',text:'Add Commit',data:re[0]});
                child(tc,'button',{onclick:'confirm(this)',text:'Confirm',data:re[0],tracking:re[2]});
                child(icd,'p',{text:re[2]});
                child(icd,'p',{text:re[0]+" | "+re[1]});
                a.appendChild(c);*/
            }
            function confirm(a){
                fetch('process',{method:"POST",headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`data=${a.getAttribute('data')}&tracking=${a.getAttribute('tracking')}&qtyCid=`+Array.from(a.parentNode.parentNode.querySelectorAll('input[type=number]')).map(input => [input.value, encodeURIComponent(input.getAttribute('cid')), input.getAttribute('before')])})
                .then(response => response.text())
                .then(data => {
                    //console.log(data);
                    a.disabled = 'true';
                    Array.from(a.parentNode.parentNode.querySelectorAll('input[type=number]')).map(input => input.disabled = 'true')
                    sendNotif("Confirmed",200);
                });
            }
            
            function child(parent, elementType, attributes = {}) {
                let childElement = document.createElement(elementType);
                for (let key in attributes) {
                    if(key === 'text'){
                        childElement.textContent = attributes[key];
                    }else if(key === 'value'){
                        childElement.value = attributes[key];
                    }else{
                        childElement.setAttribute(key, attributes[key]);
                    }
                }
                parent.appendChild(childElement);
                return childElement;
            }
        </script>
        <script src="main/main.js"></script>
    </body>
</html>