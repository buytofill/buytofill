 <?
    require 'assets/helper.php';
        
    if($_SERVER['REQUEST_METHOD']=="GET" && !isset($_SESSION['role'])){
        header('Location: .');
        exit;
    }
    if($_SERVER["REQUEST_METHOD"]=="POST"){ 
        $conn = new mysqli(getenv('DATABASE_HOST'), getenv('DATABASE_USER'), getenv('DATABASE_PASS'), getenv('DATABASE_NAME'));
        if($conn->connect_error){ say(['err'=>'Database connection failed'],500); }
        $encryption = $_POST['clickedEditTracking'] ?? $_POST['st-select'] ?? null;
    
        if($encryption){
            $cid = dav($encryption);
            if(!$cid){ say(['err' => 'Invalid or non-numeric ID'], 400); }
        }
        if(isset($_POST['clickedEditTracking'])){ handleEditTracking($conn, $cid); }
        if(isset($_POST['st-select'],$_POST['saveTracking'],$_POST['saveQty'])){ handleSaveTracking($conn, $cid); }
        if(isset($_POST['toDelete'])){ handleDelete($conn); }
    
        $conn->close();
    }
    
    function handleEditTracking($conn, $cid){
        $stmt = $conn->prepare("SELECT trackings, uid FROM `commit` WHERE id = ?");
        $stmt->bind_param("i", $cid);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            if ($_SESSION['uid'] === $row['uid']) {
                if (!empty($row['trackings'])) {
                    $trackings = array_map(function ($pair) {
                        list($qty, $tr) = explode('-', $pair, 2);
                        return ['quantity' => (int)$qty, 'tracking' => $tr];
                    }, explode(' ', $row['trackings']));
                    say($trackings);
                } else {
                    say([]);
                }
            } else {
                say(['err' => 'No Access'], 403);
            }
        } else {
            say(['err' => 'Not Found'], 404);
        }
    }
    
    function handleSaveTracking($conn, $cid){
        $trackingPairs = [];
        foreach (json_decode($_POST['saveTracking'], true)['results'] as $result) {
            $trackingPairs[] = $result['quantity'] . '-' . $result['tracking'];
        }
        $saveQty = intval($_POST['saveQty']);
        $stmt = $conn->prepare("UPDATE `commit` SET trackings = ?, qty = ? WHERE id = ?");
        $stmt->bind_param("sii", implode(' ', $trackingPairs), $saveQty, $cid);
        if ($stmt->execute()) {
            say(['success' => true], 200);
        } else {
            say(['err' => 'Update failed'], 500);
        }
    }
    
    function handleDelete($conn){
        $encryption = explode(',', $_POST['toDelete']);
        $cids = [];
    
        foreach ($encryption as $encrypted) {
            $cid = decryptAndValidate($encrypted);
            if (!$cid) {
                say(['err' => 'Invalid or non-numeric ID'], 400);
            }
            $cids[] = $cid;
        }
    
        $conn->begin_transaction();
        $stmt = $conn->prepare("UPDATE `commit` SET status = NULL WHERE id = ?");
    
        foreach ($cids as $cid) {
            $stmt->bind_param("i", $cid);
            $stmt->execute();
        }
    
        $conn->commit();
        say(['success' => true]);
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
            .btf-checkbox:hover{border:1px solid var(--purp)}
            .btf-checkbox:has(input:checked){background:var(--purp);border:1px solid var(--purp)}
            
            ::-webkit-scrollbar{display:none}
            #table{height:100%}
            #table>table{display:block;max-height:100%;overflow-y:auto;border-collapse:collapse}
            
            body>div>aside{border-right:1px solid var(--tint);width:100%;max-width:250px;transition:max-width .3s ease;height:100%;background:#161616;color:white;box-shadow:0 1px 1px 0 rgba(0,0,0,.3),1px 1px 1px 0 rgba(0,0,0,.3),-1px 1px 1px 0 rgba(0,0,0,.3)}
            body>div>aside p{text-transform:capitalize;margin:0;font-size:1.2rem;font-weight:600;transition:transform .3s ease;}
            body>div>aside div{height:calc(100% - 63px);overflow:auto;transition:transform .3s ease}
            body>div>aside header{display:flex;position:relative;align-items:center;box-sizing:border-box;justify-content:space-between;padding:1rem;padding-left:2rem;width:250px;transition:transform .3s ease}
            body>div>aside header:after{content:'';transition:width .3s ease;position:absolute;background:#414750;width:100%;height:1px;left:0;bottom:0}

            main>div{background:#161616;height:100%;border-radius:4px;overflow:hidden}
            main>div>header{background:#161616;padding:.5rem;display:flex;border-bottom:1px solid var(--tint);box-sizing:border-box;height:52px;gap:.5rem}
            main>div>header input{width:100%;border-radius:2px;background:#0F1012;padding:.3rem .6rem;border:0;color:white;outline:1px solid var(--tint)}
            main>div>header input:hover{outline-offset:2px}
            main>div>header input:focus{outline:1px solid var(--grn);outline-offset:1px}
            main>div>header > input::placeholder{color:#555;font-style:italic;font-weight:500}
            main>div>header > div{display:flex;align-items:center}
            main>div>header > div > input{margin:0 .5rem;width:5ch;text-align:center;padding:.6rem .1rem}
            main>div>header > div > svg{height:14px;fill:none;padding-left:1px}
            main>div>header > div > svg path{stroke:var(--tint);stroke-width:2px;stroke-linecap:round;stroke-linejoin:round}
            
            main>div thead{background:#161616;color:#555}
            main>div th{padding:.5rem 2rem;position:relative}
            main>div td{position:relative;font-size:.8rem}
            main>div>header a{display:flex;align-items:center;line-height:1rem;text-align:center;position:relative;background:#0F1012;font-weight:500;color:#555;cursor:pointer;border-radius:2px;padding:.3rem .8rem}
            
            main>div>header a div{    
                position:absolute;
                width:100%;
                left:0;
                outline: 1px solid var(--tint);
                top:0;
                z-index:10;
                border-radius:2px;
                max-height:0;
                padding-top:calc(50% - 0.25rem);
                overflow:hidden;
                transition:max-height .3s ease;
            }
                
            main>div>header a:hover div{max-height:2rem;outline:1px solid var(--tint);}
            main>div>header a div button{width:100%;padding:.5rem 0;background:#0F1012;outline:0;cursor:pointer;font-weight:500;color:#555;border:0}
            main>div>header a div button:hover{background:#161616;outline:1px solid var(--tint);border-radius:2px}   
            main>div table th:first-child{width:0px;padding:0 2rem}
            th:not(:last-child):before,td:not(:last-child):before{content:'';position:absolute;width:1px;height:50%;top:50%;transform:translateY(-50%);right:0;background:var(--tint)}
            .btf-checkbox{display:block;position:absolute;width:15px;height:15px;border:1px solid var(--tint);border-radius:2px;top:50%;cursor:pointer;left:50%;transform:translate(-50%,-50%)}
            .btf-checkbox input{display:none}
            thead tr{height:33px}
            
            tbody tr{border-top:1px solid var(--tint);height:50px;transition:height .3s ease;overflow:hidden;}
            td{width:fit-content;padding:0 1rem;color:#ccc;font-weight:500}
            td:nth-child(2){max-width:250px;width:100%;height:100%;gap:1rem;padding:0 1rem;text-wrap:nowrap;}
            td div{display:flex;align-items:center;gap:1rem}
            td:nth-child(2) div{overflow-x:scroll;}
            td:nth-child(2) img{border-radius:2px;max-height:30px}
            td:last-child{padding:0 .8rem}
            td:last-child button{width:100%;border:1px solid var(--tint);background:#0F1012;font-weight:bold;color:#555;cursor:pointer;border-radius:2px;padding:.5rem 1rem;text-wrap:nowrap}
            td:last-child button:hover{border:1px solid var(--grn)}
            #c-t{position:absolute;height:calc(100% - 100px);background:#161616;box-shadow:-1px 0 5px #0F1012;right:0;top:0;display:flex;flex-direction:column;min-width:500px;width:calc(50vw - 50px);transform:translateX(120%);transition:transform .3s ease}
            #c-t>header{padding:.2rem;display:flex;gap:.4rem;margin:1rem 1rem 0;border-radius:4px;background:#111;border:1px solid var(--tint);position:relative}
            #c-t>header:before{content:'';top:.4rem;left:.4rem;pointer-events:none;position:absolute;height:calc(100% - .8rem);background:var(--purp);border:1px solid var(--purb);width:calc(50% - .6rem);border-radius:2px;transition:transform .7s cubic-bezier(1, 1.5, 0, 0.8)}
            #c-t>header>button{cursor:pointer;border-radius:2px;color:white;font-weight:600;outline:0;padding:.6rem 2rem;width:50%;background:none;border:0;position:relative}
            .b-e>header:before{transform:translateX(calc(100% + .4rem))}
            #c-t>div{display:flex;gap:1rem;padding:0 1rem .5rem;height:100%;overflow:hidden}
            .b-e>div>div{transform:translateX(calc(-100% - 1rem))}
            #c-t>div>div{min-width:100%;border-radius:4px;transition:transform .6s ease;box-sizing:border-box;display:flex;flex-direction:column;font-weight:100;overflow:hidden}
            #c-t>div>div:first-child>button{cursor:pointer;padding:.4rem;background:var(--purp);color:white;font-weight:600;border:1px solid var(--purb);border-radius:4px;margin-bottom:.5rem;}
            #c-t>div>div:first-child>div{gap:.4rem;display:flex;flex-direction:column;overflow-y:scroll}
            #c-t>div>div:first-child>div>div{display:flex;gap:.4rem}
            #c-t>div>div:first-child>div>div>input{border:1px solid var(--tint);background:#111;padding:.8rem;border-radius:4px;color:white;font-weight:400;outline:0;}
            #c-t>div>div:first-child>div>div>input:hover{border-color:var(--purb)}
            #c-t>div>div:first-child>div>div>input:focus{border-color:var(--purp)}
            #c-t>div>div:first-child>div>div>input:first-child{width:5ch;text-align:right;}
            #c-t>div>div:first-child>div>div>input:last-child{width:100%;overflow-x:scroll;}
            
            #c-t>div>div:last-child{border:1px solid var(--tint);background:#111;padding:2rem 1rem 1rem .8rem}
            #c-t>div>div:last-child>div{overflow-x:hidden;width:100%;height:100%}
            #c-t div[contenteditable]{min-height:100px;box-sizing:border-box;color:white;width:fit-content;font-weight:400;min-width:100%;text-wrap:nowrap;outline:0;resize:none;line-height:1.5rem}
            #c-t div:has(>div>div[contenteditable]) p{color:#888;line-height:1.5rem;padding-right:1rem;margin-right:1rem;height:fit-content;font-weight:1000;text-align:right;user-select:none;border-right:2px dashed}
            #c-t div:has(>div>div[contenteditable]){display:flex;word-spacing:2rem}
            #c-t div header{display:flex;color:#888;font-weight:800;padding:0.4rem 0 0.5rem;margin-top:-1.5rem}
            #c-t div header span:first-child{width:1.5rem;display:block;text-align:right}
            #c-t div header span:not(:last-child){margin-right:1.5rem}
            
            #c-t footer{
                display:flex;
                gap:.5rem;
            }
            #c-t footer button{
                padding:0.7rem 1rem;
                color:white;
                font-weight:600;
                background:#111;
                border:1px solid var(--tint);
                border-radius:4px;
                cursor:pointer;
            }
            #c-t footer button:last-child{
                width:100%;
                border-color:var(--purb);
                background:var(--purp);
            }
            input::-webkit-outer-spin-button,
            input::-webkit-inner-spin-button {
              -webkit-appearance:none;
              margin:0;
            }
            
            /* Firefox */
            input[type=number] {
              -moz-appearance:textfield;
            }
            #c-t label{
                padding:.4rem .8rem;
                
                border:1px solid var(--tint);
                background:#111;
                display:flex;
                justify-content:space-between;
                margin:.5rem 1rem;
                box-sizing:border-box;
                align-items:center;
                border-radius:4px;
            }
            #c-t label span{
                color:white;
                font-weight:bold;
            }
            #c-t label input{
                color:white;
                border:1px solid var(--tint);
                background:#161616;
                max-width:10ch;
                padding:0.4rem;
                text-align:right;
                outline:0;
                border-radius:2px;
                font-weight:bold;
            }
            
            th{width:100%}
            
            .removed-row{
                opacity:0;   
                height:0;
            }
            .removed-row td{
                font-size:0;
                transition:font-size .3s ease;
            }
            .removed-row img{
                height:0;
            }
            .removed-row button{
                display:none;
            }
            
            .noCommits{
                position:absolute;
                display:block;
                top:50%;
                font-weight:500;
                left:50%;
                transform:translateX(-50%);
            }
            .noCommits a{
                text-decoration:none;
                font-weight:bolder;
                color:white;
            }
        </style>
    </head>
    <body> 
        <?require 'assets/header.php'?>
        <nav>
            <main>
                <a href="#" class="y">All Commitments</a>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none">
                    <path d="m19 18.5 4.5 4.575m-3-8.25c0 2.925-2.325 5.25-5.25 5.25-2.925 0-5.25-2.325-5.25-5.25 0-2.925 2.325-5.25 5.25-5.25 2.925 0 5.25 2.325 5.25 5.25" stroke-linecap="round" stroke="#fff" stroke-width=1.5 stroke-linejoin="round"/>
                </svg>
                <form>
                    <input type="text" placeholder="Search">
                </form>
            </main>
            <div>
                <a onclick="deleteSelectedTrackings()">Remove</a>
                <hr>
                <a>page controller</a>
            </div>
        </nav>
        <main>
            <div id="table">
                <table>
                    <thead>
                        <tr>
                            <th>
                                <label class="btf-checkbox">
                                    <input id="master-checkbox" type="checkbox" onclick="selectAll(this)">
                                </label>
                            </th>
                            <th>Item</th>
                            <th>Pending</th>
                            <th>Price</th>
                            <th>Trackings</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?
                            $conn = new mysqli(getenv('DATABASE_HOST'), getenv('DATABASE_USER'), getenv('DATABASE_PASS'), getenv('DATABASE_NAME'));
                            $uid = $_SESSION['uid'];
                            $stmt = $conn->prepare("SELECT o.pid, i.name, i.spec, o.price, c.qty, c.id FROM `commit` AS c INNER JOIN `order` AS o ON c.oid = o.id INNER JOIN `item` AS i ON o.pid = i.id WHERE c.uid = ? AND c.status >= 0 AND c.qty > 0 ORDER BY c.created DESC");
                            $stmt->bind_param("i", $uid);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()){
                                    ?>
                                    <tr>
                                        <td>
                                            <label class="btf-checkbox"><input name="response-checkbox" type="checkbox"></label>
                                        </td>
                                        <td><div><img src="assets/images/<?=$row['pid']?>.webp"><p><?=$row['name'].' | '.$row['spec']?></p></div></td>
                                        <td><?=$row['qty']?></td>
                                        <td>$<?=$row['price']?></td>
                                        <td><button data-value="<?=openssl_encrypt($row['id'], $_SESSION['cryptMethod'], $_SESSION['cryptKey'], 0, $_SESSION['cryptIV'])?>">Edit</button></td>
                                    </tr>
                                    <?
                                }
                            } else {
                                ?>
                                <tr><td class="noCommits">You have no commits, visit <a href='/deals'>/deals</a> to create a new one.</td></tr>
                                <?
                            }
                            $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
            <div id="c-t">
                <header>
                    <button onclick="convertToSingle(this)">Single Edit</button>
                    <button onclick="convertToBulk(this)">Bulk Edit</button>
                </header>
                <label for="totalQty">
                    <span>Quantity</span>
                    <input type="number" id="totalQty" placeholder="0">
                </label>
                <div>
                    <div class="first">
                        <button onclick="createTracking(this)">Add Tracking</button>
                        <div>
                            
                        </div>
                    </div>
                    <div class="second">
                        <header>
                            <span>#</span>
                            <span>Qty</span>
                            <span>Trackings</span>
                        </header>
                        <div>
                            <p>1</p>
                            <div style="width:100%;min-height:100px;overflow-x: scroll;height: fit-content;">
                                <div contenteditable="true"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <footer style="padding: 0 1rem 1rem;">
                    <button onclick="closeTrk(this.parentNode.parentNode)">Close</button>
                    <button onclick="saveTrk(this.parentNode.parentNode)">Save</button>
                </footer>
            </div>
        </main>
        <script>
            let bulk = document.querySelector('#c-t div[contenteditable]');
            
            let currDataValue = '';
    
            document.querySelectorAll('#table button[data-value]').forEach(a => {
                a.addEventListener('click',(event)=>{
                    document.querySelector('#c-t').classList.remove('b-e');
                    let val = a.getAttribute('data-value');
                    currDataValue = val;
                    fetch('commits',{method: 'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'clickedEditTracking='+encodeURIComponent(val)})
                    .then(response => response.json())
                    .then(data => {
                        if(Array.isArray(data)){
                            document.querySelector('#totalQty').value = document.querySelector('tr:has(button[data-value="'+val+'"]) td:nth-child(3)').textContent;
                            dad = document.querySelector('#c-t div div:first-child div');
                            dad.innerHTML = "";
                            if(data.length == 0){
                                const newDiv = document.createElement('div');
                                const firstDiv = document.createElement('input');
                                firstDiv.name = "trkinput";
                                firstDiv.placeholder = "Qty"
                                newDiv.appendChild(firstDiv);
                                const secondDiv = document.createElement('input');
                                secondDiv.placeholder = "Tracking"
                                secondDiv.name = "trkinput";
                                newDiv.appendChild(secondDiv);
                                dad.appendChild(newDiv);
                            }else{
                                for(i = 0; i<data.length; i++){
                                    const newDiv = document.createElement('div');
                                    const firstDiv = document.createElement('input');
                                    firstDiv.name = "trkinput";
                                    firstDiv.value = data[i].quantity;
                                    newDiv.appendChild(firstDiv);
                                    const secondDiv = document.createElement('input');
                                    secondDiv.name = "trkinput";
                                    secondDiv.value = data[i].tracking;
                                    newDiv.appendChild(secondDiv);
                                    dad.appendChild(newDiv);
                                }   
                            }
                            
                            
                            document.querySelector('#c-t').style.transform = "translateX(0)";
                        }
                    });
                });
            });
            
            let bulktr = '';
            bulk.addEventListener('input', function(event){
                let divCount = bulk.querySelectorAll('div').length;
                
                let extraTextNodeFound = false;
                for (let node of bulk.childNodes) {
                    if (node.nodeType === Node.TEXT_NODE && node.textContent.trim().length > 0) {
                        extraTextNodeFound = true;
                        break;
                    }
                }
                
                if(extraTextNodeFound)divCount += 1;
                divCount = Math.max(divCount, 1);
                if(divCount <= 500){
                    let str = "";
                    for(let i = 1; i <= divCount; i++)str += i + "<br>";
                    bulk.parentNode.parentNode.querySelector('p').innerHTML = str;
                    bulktr = bulk.innerHTML;
                }else if(event.inputType == "insertParagraph"){
                    this.innerHTML = bulktr;
                    this.parentNode.parentNode.parentNode.style.borderColor = '#883636';
                }
            });
            
            function convertToSingle(a){
                let container = a.parentNode.parentNode;
                container.classList.remove('b-e');
                const lines = container.querySelector('div[contenteditable]').innerText.split('\n');
                const single = container.querySelector('div div:first-child div');
                single.innerHTML = "";
                
                lines.forEach(line => {
                    const parts = line.split(' ', 2);
                    const newDiv = document.createElement('div');
                    if (parts.length === 2){
                        const firstDiv = document.createElement('input');
                        firstDiv.value = parts[0].replaceAll(/\s/g,'');
                        firstDiv.name = "trkinput";
                        newDiv.appendChild(firstDiv);
                        const secondDiv = document.createElement('input');
                        secondDiv.name = "trkinput";
                        secondDiv.value = parts[1];
                        newDiv.appendChild(secondDiv);
                    }else if(parts.length == 1){
                        const firstDiv = document.createElement('input');
                        if(parts[0]){
                            firstDiv.value = parts[0].replaceAll(/\s/g,'');   
                        }else{
                            firstDiv.placeholder = "Qty"
                        }
                        firstDiv.name = "trkinput";
                        newDiv.appendChild(firstDiv);
                        const secondDiv = document.createElement('input');
                        secondDiv.name = "trkinput";
                        secondDiv.placeholder="Tracking";
                        newDiv.appendChild(secondDiv);
                    }else if(parts.length == 0){
                        const firstDiv = document.createElement('input');
                        firstDiv.name = "trkinput";
                        firstDiv.placeholder="Qty";
                        newDiv.appendChild(firstDiv);
                        const secondDiv = document.createElement('input');
                        secondDiv.name = "trkinput";
                        secondDiv.placeholder="Tracking";
                        newDiv.appendChild(secondDiv);
                    }
                
                    single.appendChild(newDiv);
                });
            }
            
            function createTracking(a){
                let dad = a.parentNode.querySelector('div');
                const newDiv = document.createElement('div');
                const firstDiv = document.createElement('input');
                firstDiv.placeholder = "Qty";
                newDiv.appendChild(firstDiv);
                const secondDiv = document.createElement('input');
                secondDiv.placeholder = "Tracking";
                newDiv.appendChild(secondDiv);
                dad.insertBefore(newDiv,dad.firstChild);
            }
            
            function convertToBulk(a){
                let container = a.parentNode.parentNode;
                container.classList.add('b-e');
                const targetElement = container.querySelector('div div:first-child div');
                const para = container.querySelector('div[contenteditable]')
                para.innerHTML = "";
                Array.from(targetElement.children).forEach((a) => {
                    const div = document.createElement('div');
                    if(a.children[0].value!=""){
                        div.textContent += a.children[0].value + " " + a.children[1].value;
                        para.appendChild(div);
                        let str = "";
                        for(let i = 1; i <= bulk.querySelectorAll('div').length; i++)str += i + "<br>";
                        bulk.parentNode.parentNode.querySelector('p').innerHTML = str;
                    }else{
                        bulk.parentNode.parentNode.querySelector('p').textContent = 1;
                    }
                });
            }
            
            function closeTrk(a){
                a.style.transform = "translateX(120%)";
            }
            
            function saveTrk(a){
                data = '';
                let results = [];
                let qty = document.querySelector('#totalQty').value;
                if(a.classList.contains('b-e')){
                    Array.from(document.querySelector('.second>div>div>div').children).forEach((divElement) => {
                        temp = divElement.textContent.split(' ', 2);
                        let obj = {quantity:temp[0],tracking:temp[1]};
                        results.push(obj); 
                    });
                }else{
                    Array.from(document.querySelector('.first>div').children).forEach((divElement) => {
                        let inputs = Array.from(divElement.children);
                        if(inputs.length === 2 && inputs.every(input => input.tagName === 'INPUT')){
                            if(inputs[0].value || inputs[1].value){
                                let obj = {quantity: inputs[0].value,tracking: inputs[1].value};
                                results.push(obj);
                            }
                        }
                    });
                }
                fetch('commits',{method: 'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`saveQty=${qty}&st-select=${encodeURIComponent(currDataValue)}&saveTracking=${encodeURIComponent(JSON.stringify({results}))}`})
                .then(response => response.json())
                .then(data => {
                    if(data['success']){
                        document.querySelector('tr:has(button[data-value="'+currDataValue+'"]) td:nth-child(3)').textContent = qty;
                        document.querySelector("#c-t").style.transform = 'translateX(100%)';
                    }
                });
            }
            
            function deleteSelectedTrackings(){
                let a = [...document.querySelectorAll('tbody .btf-checkbox input:checked')].map(b => b.parentNode.parentNode.parentNode.querySelector('button').getAttribute('data-value'));
                
                fetch('commits',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`toDelete=`+encodeURIComponent(a)})
                .then(response => response.json())
                .then(data => {
                    if(data['success']){
                        a.forEach((c) => {
                            const tr = document.querySelector(`tr:has(button[data-value="${c}"])`);
                            if(tr){
                                tr.classList.add('removed-row');
                                setTimeout(()=>{tr.remove()}, 800);
                            }
                        });
                        
                        setTimeout(()=>{
                            tbod = document.querySelector('tbody');
                            if(!tbod.children.length){
                                const newRow = document.createElement('tr');
                                const newCell = document.createElement('td');
                                newCell.classList = "noCommits";
                                newCell.innerHTML = "You have no commits, visit <a href='/deals'>/deals</a> to create a new one."
                                newRow.appendChild(newCell);
                                tbod.appendChild(newRow);   
                            }
                        }, 800);
                    }
                });
            }
            
            function selectAll(a) {
                let selectAllCheckbox = a;
                let boxes = document.querySelectorAll('tbody input[type="checkbox"]');
                let isChecked = selectAllCheckbox.checked;
                
                boxes.forEach(box => {
                    box.checked = isChecked;
                });
            }
        </script>
    </body>
</html>