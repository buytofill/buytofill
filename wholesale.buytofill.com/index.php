<?
    require_once dirname(__DIR__).'/assets/helper.php';

    
    if(isset($_SESSION['role']) && ($_SESSION['role'] < 8 && $_SESSION['role'] !== 4)) header('Location: https://buytofill.com');
    if(!isset($_SESSION['guest'])) $_SESSION['guest'] = true;
    
    if($_SERVER['REQUEST_METHOD'] == "GET"){
        if(isset($_GET['email']) && isset($_GET['password'])){
            $conn = new mysqli(getenv('DATABASE_HOST'), getenv('DATABASE_USER'), getenv('DATABASE_PASS'), getenv('DATABASE_NAME'));
            if($conn->connect_error) die($conn->connect_error);
            $email = $_GET['email'];
            $password = $_GET['password'];
            
            $stmt = $conn->prepare("SELECT pass,id,level,fn,ln, 'buyer' as role FROM buyer WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
    
            if($result && $result->num_rows > 0){
                $row = $result->fetch_assoc();
                if(password_verify($password, $row['pass'])){
                    session_destroy();
                    session_start();
                    session_regenerate_id(true);
                    $_SESSION['role'] = 4;
                    $_SESSION['uid'] = $row['id'];
                    $_SESSION['level'] = $row['level'];
                    $_SESSION['fn'] = $row['fn'];
                    $_SESSION['ln'] = $row['ln'];
                    $_SESSION['auid'] = N2A($row['id']);
                    $_SESSION['guest'] = false;
                }
            }
            
            $stmt->close();
            $conn->close();
            header('Location: /');
            exit;
        }elseif(isset($_GET['fn']) && isset($_GET['guestemail']) && isset($_GET['phone']) && isset($_GET['guestpassword'])){
            $_SESSION['fn'] = $_GET['fn'] ?? '';
            $_SESSION['email'] = $_GET['guestemail'] ?? '';
            $_SESSION['phone'] = preg_replace('/\D/', '', $_GET['phone']) ?? '';
            $_SESSION['guestPassword'] = $_GET['guestpassword'] ?? '';
            if(isset($_GET['action'])){
                header('Location: ?buyer');
                exit;
            }
            $pdo = new PDO($dsn, getenv('DATABASE_USER'), getenv('DATABASE_PASS'));
            $continue = true;
            
            // Validate inputs
            $fn = $_SESSION['fn'];
            $email = $_SESSION['email'];
            $phone = $_SESSION['phone'];
            
            if (
                empty($fn) ||
                !filter_var($email, FILTER_VALIDATE_EMAIL) ||
                !checkdnsrr(substr(strrchr($email, "@"), 1), "MX") ||
                empty($phone) ||
                strlen($phone) < 10
            ) {
                $continue = false;
            }
            
            // Check if the email already exists in the database
            if ($continue) {
                $stmt = $pdo->prepare('SELECT COUNT(*) FROM buyer WHERE email = :email');
                $stmt->execute(['email' => $email]);
                $emailExists = $stmt->fetchColumn();
            
                if ($emailExists) {
                    header('Location: ?exists');
                }else{
                    require_once dirname(__DIR__).'/assets/aws.phar';
                    $client = new Aws\Ses\SesClient(['region'=>'us-east-1','credentials'=>['key'=>'AKIAY2M4YSTP4HRRYZXB','secret'=>'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']]);
                    $_SESSION['code'] = mt_rand(100000,999999);
                    $client->sendEmail([
                        'Destination' => ['ToAddresses' => [$_SESSION['email']]],
                        'Message' => [
                            'Body' => ['Text' => ['Charset' => 'UTF-8', 'Data' => "Your BuyToFIll verification code is ".$_SESSION['code']. "\n\nThis code expires after five minutes or on renewal"]],
                            'Subject' => ['Charset' => 'UTF-8', 'Data' => "Continue with ".$_SESSION['code']]
                        ],
                        'Source' => 'BuyToFill <noreply@buytofill.com>']);
                    header('Location: ?code');
                    #add 5m cool down
                }
                exit;
            }
        }elseif(isset($_GET['code'])){
            if(!isset($_SESSION['code'])){
                header('Location: /');
                exit;
            }elseif($_GET['code'] == $_SESSION['code']){
                session_regenerate_id(1);
                unset($_SESSION['code']);
                
                $pdo = new PDO($dsn, getenv('DATABASE_USER'), getenv('DATABASE_PASS'));
                $stmt = $pdo->prepare('INSERT INTO buyer (fn, email, pass, phone) VALUES (?, ?, ?, ?)');
                $stmt->execute([$_SESSION['fn'], $_SESSION['email'], password_hash($_SESSION['guestPassword'], PASSWORD_BCRYPT), $_SESSION['phone']]);
                $_SESSION['uid'] = $pdo->lastInsertId();
                $_SESSION['role'] = 4;
                $_SESSION['guest'] = true;
                
                require_once dirname(__DIR__).'/assets/aws.phar';
                $client = new Aws\Ses\SesClient(['region'=>'us-east-1','credentials'=>['key'=>'AKIAY2M4YSTP4HRRYZXB','secret'=>'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']]);
                $client->sendEmail([
                    'Destination' => ['ToAddresses' => [$_SESSION['email']]],
                    'Message' => [
                       'Body' => ['Text' => ['Charset' => 'UTF-8', 'Data' => "Explore"]],
                        'Subject' => ['Charset' => 'UTF-8', 'Data' => "Welcome to BuyToFill"]
                    ],
                    'Source' => 'BuyToFill <noreply@buytofill.com>']);
                
                header('Location: .');
                exit;
            }
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
        <meta name="keywords" content="buytofill"/>
        <link rel="icon" href="assets/favicon.ico"/>
        <link rel="stylesheet" href="assets/styles.css">
        <style>
            body{background:#111;width:100vw;height:100vh;margin:0}
            #belowabove{display:flex;height:100%}
            #belowabove>#main{width:100%;display:flex;flex-direction:column}
            #belowabove>#side{background:#222;width:20%;border-left:1px solid #333;box-sizing:border-box;display:flex}
            #selector{height:100vh;overflow-y:auto;padding:1.5rem;width:100%}
            #selector>button{border:1px solid #444;overflow:hidden;transition:height .5s ease;height:65px;display:flex;flex-direction:column;background:#282828;border-radius:.5rem;font-weight:500;width:100%;cursor:pointer;font-size:1.2rem;color:#ddd}
            #selector>button>div{background:#333;display:flex;width:100%;height:65px;min-height:65px;border-radius:.5rem}
            #selector>button>div>p{height:100%;width:100%;user-select:none;font-weight:600;display:flex;padding-left:1.5rem;align-items:center}
            #selector>button>div>div{margin:auto 1.2rem auto auto}
            #selector>button>div>div>a{width:30px;height:30px;display:block}
            #selector>button>div svg{fill:#aaa}
            #selector>button:not(:last-child){margin-bottom:1rem}
            #selector>button:hover,#selector>button:focus{height:calc(65px + 65px * var(--brands))}
            #selector>button>a{height:65px;min-height:65px;padding:0 1.5rem;transition:padding-left .2s ease, color .2s ease;font-weight:400;display:flex;align-items:center;width:100%}
            #selector>button>a:hover{padding-left:1.8rem;color:#eee}
            #slider{border-right:1px solid #333;min-width:8px}
            button:has(#from){height:calc(65px + 65px * var(--brands))}
            #from{font-weight:600!important;padding-left:1.8rem!important;color:#eee}
            #top{color:#fff}
            #below{direction:rtl;padding:0 1rem;overflow-y:auto;border-top:1px solid #333}
            #below>div{border:1px solid #444;overflow:hidden;direction:ltr;cursor:pointer;background:#353535;font-size:1rem;color:#fff;border-radius:.5rem;height:55px;display:flex}
            #below>div:not(:last-child){margin-bottom:.5rem}
            #below>div:first-child{margin-top:1rem}
            #below>div:last-child{margin-bottom:2rem}
            ::-webkit-scrollbar{background:transparent}
            ::-webkit-scrollbar-thumb{background:#272727;border:1px solid #444;border-left:none}
            #below .name{border-radius:1rem;background:#272727;overflow-y:auto;white-space:nowrap;width:100%;padding-left:1rem}
            #below .name::-webkit-scrollbar{display:none}
            #below .bcontainer>div{height:100%;display:flex;align-items:center}
            #below .side{border-radius:.5rem;padding:.2rem 0}
            #below .side>div{white-space:nowrap;padding:0 1rem}
            #below>div>img{height:100%;min-width:55px;max-width:55px}
            #below .wmanager button{padding:0 1rem 0 1.1rem;height:100%;font-weight:600;font-size:1rem;border-top-left-radius:1rem;border-bottom-left-radius:1rem;background:rgb(112,112,255);background:-moz-linear-gradient(142deg, rgba(112,112,255,1) 0%, rgba(0,134,255,1) 100%);background:-webkit-linear-gradient(142deg, rgba(112,112,255,1) 0%, rgba(0,134,255,1)100%);background:linear-gradient(142deg, rgba(112,112,255,1) 0%, rgba(0,134,255,1) 100%);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#7070ff",endColorstr="#0086ff",GradientType=1);}
            .bcontainer{display:flex;width:100%;padding:.2rem 0;overflow:hidden}
            #search{padding: 1rem}
            #search input{width:100%;font-size:1rem;background:#272727;;padding:1rem;border-radius:.5rem;border:1px solid #444}
            input::placeholder{color:#ddd}
            #below .side{display:flex;align-items:center}
            .wmanager{display:flex;width:calc(100% - 55px);justify-content:space-between}
            .side>div{margin:0 .15rem 0 .15rem;position:relative;border-radius:1rem;height:100%;display:flex;align-items:center}
            .side>div span{display:none;position:absolute;background:#272727;top:-.5rem;text-align:center;left:0;padding:.45rem 0 0;width:100%;color:#ccc;font-weight:700;font-size:.7rem}
            .condition{min-width:80px;justify-content:center}
            .side>div:hover{background:#272727}
            .side>div:hover span{display:block}
            .side>div:first-child{margin-left:.3rem}
            .side>div:last-child{margin-right:.3rem}
            #popup{position:absolute;top:0;left:0;width:100vw;height:100vh;z-index:1}
            #blur{background:#0001;width:100vw;height:100vh;backdrop-filter:blur(2px)}
            #popup>#main{padding:1.5rem;border:1px solid #333;background:#1e1f20;width:45%;margin:auto;position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);border-radius:3.25rem}
            #popup>#main>div{background:#0e0e0e;padding:3rem 3rem 1.5rem 3rem;border:1px solid #333;border-radius:2rem}
            #popup>#main>div:nth-child(2){margin-top:1rem}
            
            #popup>#main>div>h2{color:#474BFF;font-weight:900}
            #popup>#main>div>div{
                display:flex;
                justify-content:space-between;
            }
            #popup>#main>div>div>div,#popup>#main>div>div>form{
                padding:2rem 0;
                color:#fff;
            }
            #popup>#main>div>div>form{
                padding-left:.5rem;display:flex;flex-direction:column;position:relative;
                min-width:50%;
            }
            #popup>#main>div>div>form>div:not(:last-child){
                margin-bottom:1.3rem;
            }
            #popup>#main>div>div>form>div{
                position:relative;
                background:#333;
                box-sizing:border-box;
                border:1px solid #888;
                border-radius:.5rem;
            }
            #popup>#main>div>div>form>div>label{
                position:absolute;
                top:1.25rem;
                left:1rem;
                cursor:pointer;
                color:#aaa;
                font-weight:500;
                transition:top .1s ease,font-size .1s ease;
            }
            #popup>#main>div>div>form>div:hover>label,#popup div:has(>input:focus) label{
                color:#6CEBA5!important;
            }
            #popup>#main>div>div>form>div:hover>label, #popup form div:not(:has(input:placeholder-shown)) label, #popup div:has(>input:focus) label{
                top:.5rem!important;
                border-radius:0;
                font-size:.8rem;
            }
            #popup>#main>div>div>form>div>input{
                font-size:1rem;
                padding:1.65rem 1.2rem .75rem 1rem;background:#0000;width:100%;height:100%;color:#fff;border-radius:.5rem;
            }
            
            #popup>#main>div>div>div>h2{font-size:2rem;font-weight:200}
            #popup>#main>div>div>div>h5{font-size:1rem;font-weight:200;padding-top:1rem;padding-right:2rem}
            aside{
                display:flex;
                margin-top:1.5rem;
                justify-content:flex-end;
            }
            aside button{
                padding:1rem 1.5rem;
                border-radius:2rem;font-weight:600;font-size:1rem;
                color:#000;
                background:#fff;cursor:pointer;
                border:none;
            }
            aside button:last-child{
                margin-left:1rem;
                background:#6CEBA5;
            }
            #fp{background:#0000;color:#fff}
        </style>
    </head>
    <body>
<?if(!isset($_SESSION['role']) && !isset($_GET['code']) && !isset($_GET['buyer'])){?>
        <div id=popup>
            <div id=blur></div>
            <div id=main>
                <div id=loginAsCustomer>
                    <h2>buytofill</h2>
                    <div>
                        <div>
                            <h2>Sign In</h2>
                            <h5>Use your BuyToFill Customer Account</h5>
                        </div>
                        <form>
                            <div>
                                <label for=email>Email</label>
                                <input name=email id=email type=text placeholder="" required>
                            </div>
                            <div>
                                <label for=password>Password</label>
                                <input name=password id=password type=password placeholder="" required>
                            </div>
                            <aside>
                                <!--button type=submit id=fp>Forgot Password?</button-->
                                <button type="submit">Continue</button>
                            </aside>
                        </form>
                    </div>
                </div>
                <div id=joinAsGuest>
                    <h2>buytofill</h2>
                    <div>
                        <div>
                            <h2>Create a Guest Account</h2>
                            <h5>Quickly access our deals and login, become a customer to view prices and add to cart.</h5>
                        </div>
                        <form method="GET" action=""> 
                            <div>
                                <label for=fn>Full Name</label>
                                <input type=text name=fn id=fn placeholder="" required value="<?=$_GET['fn']?>">
                            </div>
                            <div>
                                <label for=guestemail>Email</label>
                                <input name=guestemail id=guestemail type=text placeholder="" required value="<?=$_GET['guestemail']?>">
                            </div>
                            <div>
                                <label for=phone>Phone</label>
                                <input type="telephone" id=phone name=phone placeholder="" required value="<?=$_GET['phone']?>">
                            </div>
                            <div>
                                <label for=guestpassword>Password</label>
                                <input name=guestpassword id=guestpassword type=password placeholder="" required value="<?=$_GET['guestpassword']?>">
                            </div>
                            <aside>
                                <button type="submit" name="action" value="extend">Become a Buyer</button>
                                <button type="submit">Create</button>
                            </aside>
                        </form>
                    </div>
                </div>
            </div>
        </div>
<?}elseif(isset($_GET['code']) && isset($_SESSION['code'])){?>
        <div id=popup>
            <div id=blur></div>
            <div id=main>
                <div id=emailedCode>
                    <h2>buytofill</h2>
                    <div>
                        <div>
                            <h2>Confirm Email</h2>
                            <h5>We've sent you an email to verify your account</h5>
                        </div>
                        <form>
                            <div>
                                <label for=code>Verification Code</label>
                                <input type=text name=code id=code placeholder="" required value="<?=$_GET['fn']?>">
                            </div>
                            <aside>
                                <button type="submit">Verify</button>
                            </aside>
                        </form>
                    </div>
                </div>
            </div>
        </div>
<?}elseif(isset($_GET['buyer']) && isset($_SESSION['fn']) && isset($_SESSION['email']) && isset($_SESSION['phone']) && isset($_SESSION['guestPassword'])){?>
        <div id=popup>
            <div id=blur></div>
            <div id=main>
                <div id=joinAsGuest>
                    <h2>buytofill</h2>
                    <div>
                        <div>
                            <h2>Create a Buyer Account</h2>
                            <h5>Quickly access our deals and login, become a customer to view prices and add to cart.</h5>
                        </div>
                        <form method="GET" action=""> 
                            <div>
                                <label for=fn>Full Name</label>
                                <input type=text name=fn id=fn placeholder="" required value="<?=$_SESSION['fn']?>">
                            </div>
                            <div>
                                <label for=guestemail>Email</label>
                                <input name=guestemail id=guestemail type=text placeholder="" required value="<?=$_SESSION['email']?>">
                            </div>
                            <div>
                                <label for=phone>Phone</label>
                                <input type="telephone" id=phone name=phone placeholder="" required value="<?=$_SESSION['phone']?>">
                            </div>
                            <div>
                                <label for=guestpassword>Password</label>
                                <input name=guestpassword id=guestpassword type=password placeholder="" required value="<?=$_SESSION['guestPassword']?>">
                            </div>
                            <aside>
                                <button type="submit" name="action" value="extend">Become a Buyer</button>
                                <button type="submit">Create</button>
                            </aside>
                        </form>
                    </div>
                </div>
            </div>
        </div>
<?}?>
        <div id="aboveall">
            test
        </div>
        <div id="belowabove">
            <div id=main>
                <div id=top>
                    <form id=search>
                        <input type=text name=query placeholder="Search by Product Name or Number" value=<?=$_GET['query']??''?>>
                    </form>
                </div>
                <div id=below><?
        $conditions = ['New'];
        $types = ['Laptops', 'Smart Home', 'Streaming', 'Tablet', 'Security', 'Mobile', 'Wearables', 'Accessories', 'Headphones', 'Consoles', 'Monitors', 'Apple'];
        $brands = ['Google', 'Amazon', 'Apple', 'Roku', 'Meta', 'Nintendo', 'Msi', 'Hp', 'Lenovo', 'Gigabyte', 'Beats', 'Samsung', 'Acer', 'Asus', 'Ring', 'Gopro', 'Dyson', 'Oneplus', "Microsoft", "Sony", "Xbox", "Playstation", "Dell", "Onn", "Alienware", "Garmin", "Razor", "Dji", "Jbl", "Steam"];
        
        $sql = "SELECT name,upc,price,brand,cond,stock,MOQ FROM item WHERE type IS NOT NULL AND brand IS NOT NULL";
        if(!empty($_GET)){
            if(isset($_GET['query'])){
                $sql = "SELECT name,upc,price,brand,cond,stock,MOQ FROM item WHERE type IS NOT NULL AND brand IS NOT NULL AND (name LIKE ? OR upc LIKE ?)";
            }else{
                $sql = "SELECT name,upc,price,brand,cond,stock,MOQ FROM item WHERE type ";
                foreach(array_keys($_GET) as $key){
                    $key = ucwords(str_replace('_', ' ', $key));
                    if(($index = array_search($key, $types)) !== false){
                        $sql .= "= ".$index." AND brand ";
                    }elseif(($index = array_search($key, $brands)) !== false){
                        $sql .= "= ".$index;
                    }
                }
            }
        }
        
        $conn = new mysqli(getenv('DATABASE_HOST'), getenv('DATABASE_USER'), getenv('DATABASE_PASS'), getenv('DATABASE_NAME'));
        $stmt = $conn->prepare($sql);
        if(isset($_GET['query'])){
            $query = '%' . $_GET['query'] . '%';
            $stmt->bind_param("ss", $query, $query);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()){
                  ?><div>
                        <img src="logo/<?=$row['brand']?>.svg" alt="Brand Logo">
                        <div class=wmanager>
                            <div class=bcontainer>
                                <div class=name><?=$row['name'].' - '.$row['upc']?></div>
                            </div>
                            <div class=side>
                                <?if(!$_SESSION['guest']){?><div class=price><span>PRICE</span>$<?=$row['price']?></div><?}?>
                                <div class=condition><span>CONDITION</span><?=$conditions[$row['cond']]?></div>
                                <div class=qty><span>QTY</span><?=$row['stock']?></div>
                                <div class=location><span>LOCATION</span>New York</div>
                                <?if($row['MOQ'] != 0){?><div class="moq"><span>MOQ</span>MOQ <?=$row['MOQ']?></div><?}?>
                            </div>
                            <button>Interested</button>
                        </div>
                    </div><?
        }
        $stmt->close();
        $conn->close();
              ?></div>
            </div>
            <div id=side>
                <div id=slider></div>
                <div id=selector><?
        $conn = new mysqli(getenv('DATABASE_HOST'), getenv('DATABASE_USER'), getenv('DATABASE_PASS'), getenv('DATABASE_NAME'));
        $stmt = $conn->prepare("SELECT type,brand FROM item WHERE type IS NOT NULL AND brand IS NOT NULL");
        $stmt->execute();
        $result = $stmt->get_result();
        $active = [];
        while ($row = $result->fetch_assoc()){
            $type = $types[$row['type']];
            $brand = $brands[$row['brand']];
    
            if(!isset($active[$type])){
                $active[$type] = [];
            }
            if(!in_array($brand, $active[$type])) {
                $active[$type][] = $brand;
            }
        }
        $stmt->close();
        $conn->close();
        
        foreach ($active as $type => $brandList) {
                  ?><button style="--brands:<?=count($brandList)?>">
                    <div>
                        <p><?=$type?></p>
                    </div><?
            foreach ($brandList as $brand) {
                  ?><a <?if(isset($_GET[str_replace(' ', '_', strtolower($type))]) && isset($_GET[strtolower($brand)])){?>id=from<?}?> href='?<?=urlencode(strtolower($type))."&".strtolower($brand)?>'>
                      <?=$brand?>
                    </a><?
          }   ?></button>
      <?  }   ?></div>
            </div>
        </div>
    </body>
</html>