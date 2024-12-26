<?
    require 'assets/control.php';
    
    if($_SERVER["REQUEST_METHOD"] == "GET"){
        if(isset($_GET['view'])){
            $file = __DIR__.'/assets/labels/'.$_SESSION['uid'].'.pdf';
            if(file_exists($file)){
                header('Content-Description: File Transfer');
                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; filename="Label.pdf"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: '.filesize($file));
                readfile($file);
                exit;
            }
        }
        if(isset($_GET['fields'])){
            $id = $_SESSION['uid'];
            $conn = new mysqli(getenv('DATABASE_HOST'), getenv('DATABASE_USER'), getenv('DATABASE_PASS'), getenv('DATABASE_NAME'));
            $stmt = $conn->prepare("SELECT phone,address,city,state,zip FROM `filler` WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            $conn->close();
            
            if(!isset($row['phone']) || !isset($row['address']) || !isset($row['city']) || !isset($row['state']) || !isset($row['zip'])) exit('Invalid profile input, please look over your profile.');
            
            $fields = $_GET['fields'];
            $count = count($fields);
            
            if ($count % 4 !== 0) exit('Invalid Input');
            
            $packageItems = [];
            $options = ["options" => ["min_range" => 1, "max_range" => 99]];
            
            for ($i = 0; $i < $count; $i += 4) {
                if (($length = filter_var($fields[$i], FILTER_VALIDATE_INT, $options)) !== 0 && ($width = filter_var($fields[$i + 1], FILTER_VALIDATE_INT, $options)) !== 0 && ($height = filter_var($fields[$i + 2], FILTER_VALIDATE_INT, $options)) !== 0 && ($weight = filter_var($fields[$i + 3], FILTER_VALIDATE_INT, $options)) !== 0){
                    $packageItems[] = [
                        "weight" => ["value" => $weight, "units" => "LB"],
                        "dimensions" => ["length" => $length, "width" => $width, "height" => $height, "units" => "IN"]
                    ];
                }else exit('Invalid Input');
            }
            
            $label = [
                "labelResponseOptions" => "URL_ONLY",
                "requestedShipment" => [
                    "shipper" => [ 
                        "contact" => [
                            "personName" => $_SESSION['fn']." ".$_SESSION['ln'],
                            "phoneNumber" => $row['phone'],
                            "companyName" => $_SESSION['auid']
                        ],
                        "address" => [
                            "streetLines" => count($ap=array_map('trim',explode(',',$row['address'])))>1?$ap:[$row['address']],
                            "city" => $row['city'],
                            "stateOrProvinceCode" => $row['state'],
                            "postalCode" => $row['zip'],
                            "countryCode" => "US"
                        ]
                    ],
                    "recipients" => [
                        [
                            "contact" => [
                                "personName" => "Solutions Inc",
                                "phoneNumber" => "6465430322"
                            ],
                            "address" => [
                                "streetLines" => ["1314A Jericho Tpke"],
                                "city" => "New Hyde Park",
                                "stateOrProvinceCode" => "NY",
                                "postalCode" => "11040",
                                "countryCode" => "US"
                            ]
                        ]
                    ],
                    "shipDatestamp" => date("Y-m-d"),
                    "serviceType" => "FEDEX_GROUND",
                    "packagingType" => "YOUR_PACKAGING",
                    "pickupType" => "USE_SCHEDULED_PICKUP",
                    "blockInsightVisibility" => 0,
                    "shippingChargesPayment" => [
                        "paymentType" => "SENDER"
                    ],
                    "labelSpecification" => [
                        "imageType" => "PDF",
                        "labelStockType" => "PAPER_85X11_TOP_HALF_LABEL"
                    ],
                    "requestedPackageLineItems" => $packageItems
                ],
                "accountNumber" => [
                    "value" => "732303812"
                ]
            ];
            
            $ch = curl_init("https://apis.fedex.com/oauth/token");
            curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => 1, CURLOPT_POSTFIELDS => http_build_query(['grant_type' => 'client_credentials', 'client_id' => 'l74c75f724593345a29f43c508ad19e5f5', 'client_secret' => '004fbf58-ffa6-4941-8f29-f5247ffa2849'])]);
            curl_setopt_array($ch, [CURLOPT_URL => "https://apis.fedex.com/ship/v1/shipments", CURLOPT_RETURNTRANSFER => 1,  CURLOPT_HTTPHEADER => ["Authorization: Bearer ".json_decode(curl_exec($ch), 1)['access_token'],"Content-Type: application/json"], CURLOPT_POSTFIELDS => json_encode($label)]);
            $response = json_decode(curl_exec($ch), 1);
            curl_close($ch);
            
            if(isset($response['output'])){
                if($count===4) $url = $response['output']['transactionShipments'][0]['pieceResponses'][0]['packageDocuments'][0]['url'];
                else $url = $response['output']['transactionShipments'][0]['shipmentDocuments'][0]['url'];
                
                file_put_contents(__DIR__."/assets/labels/".$_SESSION['uid'].".pdf", file_get_contents($url));
                header('Location: ?view');
            }else echo "Invalid input, look over your profile." . $response;
            
            exit;
        }
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
            body>div:has(#labels){height:100%}
            #labels{width:100%;border-radius:.5rem;padding:1rem 1rem 0 1rem;margin:0 .5rem}
            .wd{display:flex;margin-bottom:.8rem}
            .rl{display:flex}
            .rl svg{height:40px;display:block;fill:#757575;cursor:pointer;width:40px;background:#f1f3f4;border-radius:2rem}
            .wd svg{height:30px;margin:auto 0 auto .4rem;display:block;fill:#757575;cursor:pointer;width:30px;background:#f1f3f4;border-radius:2rem}
            form *{user-select:none; /* Standard */-webkit-user-select:none; /* Safari */-moz-user-select:none; /* Firefox */-ms-user-select:none}
            .rl button, .rl a{cursor:pointer;outline:0;border:0;padding:.5rem 2rem;color:black;border-radius:2rem;font-weight:600;background:#ddd;font-size:.9rem;display:flex;align-items:center}
            .rl button, .rl svg{margin-right:.5rem}
            #labels form{height:100%;display:flex;flex-direction:column}
            .wholder{margin-top:1rem;overflow-y:auto}
            .wd input{border:0;outline:0;text-align:center;padding:.7rem 1rem;font-size:1.1rem;margin-right:.2rem;font-weight:500;line-height:1.3rem;border-radius:2rem;background:#f1f3f4}
            .wd input:first-of-type{border-top-right-radius:.5rem;border-bottom-right-radius:.5rem;padding:.7rem .8rem .7rem 1.2rem}
            .wd input:nth-child(2){border-radius:.4rem}
            .wd input:nth-child(3){border-top-left-radius:.5rem;border-bottom-left-radius:.5rem;padding:.7rem 1.2rem .7rem .8rem}
            .wd input:last-of-type{margin-left:.5rem}
        </style>
    </head>
    <body>
        <?require 'assets/header.php'?>
        <nav>
            <main>
                <a href="#" class="y">Request Labels</a>
            </main>
        </nav>
        <main>
            <div>
                <div id="labels">
                    <form onsubmit="requestLabel(this,event)">
                        <div class="rl">
                            <button type="submit">Request 1 Label</button>
                            <svg onclick="addDims(this.parentNode.parentNode)">
                                <path d="m25.5 19h-4.2v-4.3c0-.7-.6-1.2-1.3-1.2-.7 0-1.2.5-1.2 1.2v4.3h-4.2c-.7 0-1.3.5-1.3 1.2 0 .3.1.6.4.9.2.2.5.3.9.3h4.2v4.3c0 .3.1.6.4.9.2.2.5.3.8.3.7 0 1.3-.5 1.3-1.2v-4.2h4.2c.7 0 1.2-.6 1.2-1.3 0-.7-.5-1.2-1.2-1.2"></path>
                            </svg>
                            <?if(file_exists(__DIR__.'/assets/labels/'.$_SESSION['uid'].'.pdf')){?>
                            <a href="?view">View Latest Label</a>
                            <?}?>
                        </div>
                        <div class="wholder">
                            <div class="wd">
                                <input type="number" name="fields[]" placeholder="W" min="1" max="99" required>
                                <input type="number" name="fields[]" placeholder="L" min="1" max="99" required>
                                <input type="number" name="fields[]" placeholder="H" min="1" max="99" required>
                                <input type="number" name="fields[]" placeholder="LB" min="1" max="99" required>
                                <svg onclick="remDim(this.parentNode)">
                                    <path d="m19.5 18.2-3-3 3.1-3c.5-.5.4-1.3-.1-1.8-.5-.5-1.2-.5-1.7 0l-3 3.1-3-3c-.5-.5-1.3-.6-1.8-.1-.2.2-.3.5-.3.9 0 .3.1.6.4.9l3 3-3.1 3c-.2.2-.3.5-.3.9 0 .3.1.6.3.8.5.5 1.3.6 1.8.1l3-3 2.9 3c.5.5 1.3.4 1.8-.1.5-.5.5-1.2 0-1.7"></path>
                                </svg>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </main>
        <div id="toast" onclick="this.classList.remove('show')">test</div>
    </body>
    <script>
        let tid;
        
        /*async function requestLabel(a,e) {
            e.preventDefault();
            
            const b = await fetch("#", {method:'POST', body: new FormData(a)});
            const t = await b.text();
            console.log(t);
            let toast = document.querySelector("#toast");
            if(!toast.classList.contains("show")){
                clearTimeout(tid);
                toast.textContent="Requested"
                toast.classList = "show green";
                tid = setTimeout(() => {toast.classList.remove("show")}, 10000);
            }
        }*/
        function addDims(a) {
            const w = a.querySelector('.wholder');
            let n = w.children.length;
            if(n<25){
                w.insertBefore(w.children[0].cloneNode(1),w.firstChild);
                a.querySelector("button").textContent = "Request "+(n+1)+" Labels";
            }
        }
        function remDim(a){
            let n = a.parentNode.children.length;
            let btn = a.parentNode.parentNode.querySelector("button");
            if(n>1){
                if(n==2) btn.textContent = "Request 1 Label";
                else btn.textContent = "Request "+(n-1)+" Labels";
                a.remove();
            }
        }
    </script>
</html>