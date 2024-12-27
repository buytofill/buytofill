<?
    require 'assets/helper.php';

    if(isset($_SESSION['role'])){
        header('Location: /deals');
        exit;
    }elseif($_SERVER['REQUEST_METHOD'] == "GET"){
        if(isset($_GET['email']) && isset($_GET['password'])){
            print_r(getenv('DATABASE_HOST') . endl);
            print_r(getenv('DATABASE_USER') .endl);
            print_r(getenv('DATABASE_PASS') . endl);
            print_r(getenv('DATABASE_NAME') . endl);
            print_r(get_loaded_extensions() . endl);
            exit;

            $conn = new mysqli();
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error . "\n");
            }
            echo "Connection successful. Everything is working as expected.\n";
            $conn->close();
            exit;
            
            $email = $_GET['email'];
            $password = $_GET['password'];
            $stmt = $conn->prepare("SELECT pass,id,level,fn,ln, 'filler' as role FROM filler WHERE email = ? UNION SELECT pass,id,level,fn,ln, 'buyer' as role FROM buyer WHERE email = ? UNION SELECT pass,id,level,fn,ln, 'staff' as role FROM staff WHERE email = ?");
            if(!$stmt) die("Prepare failed: " . $conn->error);
            $stmt->bind_param("sss", $email, $email, $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if($result && $result->num_rows > 0){
                $row = $result->fetch_assoc();
                if(password_verify($password, $row['pass'])){
                    session_destroy();
                    session_start();
                    session_regenerate_id(true);
                    $_SESSION['role'] = $row['role'];
                    $_SESSION['uid'] = $row['id']; 
                    $_SESSION['level'] = $row['level'];
                    $_SESSION['fn'] = $row['fn'];
                    $_SESSION['ln'] = $row['ln'];
                    $_SESSION['auid'] = N2A($row['id']);
                    header('Location: /deals');
                }
            }
            $stmt->close();
            $conn->close();
            exit;
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="handheldfriendly" content="true">
        <meta name="MobileOptimized" content="width">
        <meta name="description" content="BuyToFill">
        <meta name="author" content="">
        <meta name="keywords" content="BuyToFill"> 
        <meta name="robots" content="index, follow">
        <title>BuyToFill</title>
        <link rel="icon" href="main/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="main/styles.css">
        <style>
            body{
                height:100%;
                position:fixed;
                width:100%;
                background:#131313;
                padding:1.5rem 1rem;
                -ms-flex-direction:column;
                flex-direction:column;
            }
            input:-webkit-autofill,input:-webkit-autofill:hover,input:-webkit-autofill:focus,input:-webkit-autofill:active{
                -webkit-background-clip: text;
                -webkit-text-fill-color: #fff;
                caret-color: #fff;
                box-shadow: inset 0 0 0 100px #131313 !important;
            }
            header{
                display:flex;
                align-items:center;
                padding: 0 2rem 0 calc(2rem + 5% - 5px);
                height:45px;
                gap:.7rem;
                width:100%;
            }
            header>svg{
                height:35px;
                fill:var(--purp);
                width:35px;
            }
            header>p{
                color: var(--purp);
                font-weight: 800;
                font-size: x-large;
            }
            #hp-l{
                background:#1e1e1e;
                height:100%;
                border-radius:10rem;
                display:flex;
                margin-left:auto;
                overflow:hidden;
            }
            #hp-l>button{
                padding: 0 1rem;
                display: flex;
                align-items: center;
                background:transparent;
                font-weight: bold;
                color: gray;
                cursor:pointer;
                outline:0;
                border:0;
                font-size:.8rem;
                transition:color .3s ease;
            }
            #hp-l>button:first-child{padding-left:1.5rem}
            #hp-l>button:last-child{padding-right:1.5rem}
            #hp-l>button:hover{color:#fff}
            main{
                padding: 2rem 2.5rem 1rem;
                height:100%;
                display:flex;
            }
            main>aside,main>div{transition:width .3s ease}
            main>div{
                width:40%;
                padding: 0 calc(5% + 3rem) 0 5%;
                margin:auto;
                transition:padding .3s ease;
            }
            main>aside{
                background:#17181a;
                width:60%;
                margin:1rem 0 1rem 1rem;
                border-radius:2rem;
            }
            main form{
                position:relative;
                margin-bottom:1.5rem;
            }
            main form button{
                padding:1rem;
                border-radius:5rem;
                border:0;
                color:#fff;
                width:100%;
                background: #1e1e1e;
                outline:0;
                font-weight:bold;
                opacity:.5;
            }
            main form div{position:relative}
            main form div input{
                outline:0;
                width:100%;
                margin-bottom:1rem;
                border: 2px solid #1e1e1e;
                border-radius:5rem;
                color:#fff;
                padding:1rem 1.5rem;
                font-size:.9rem;
                background:transparent;
            }
            main form div input:hover, main form div input:focus{border-color:var(--grn)}
            main form div label{
                position:absolute;
                font-weight:500;
                color: gray;
                border: 2px solid transparent;
                margin: .5rem 1rem;
                padding: .5rem;
                pointer-events:none;
                background:#131313;
                width:calc(100% - 2rem);
                left:0;
                transition: margin .1s ease;
                font-size:.9rem;
            }
            main form div:has(input:hover) label,main form div:has(input:not(:placeholder-shown)) label,main form div:has(input:focus) label{
                padding: 0 .5rem;
                margin: -.6rem 1rem;
                width:auto;
            }
            main form div:has(input:focus) label{
                color:var(--grn);
            }
            main form:has(div input:valid:not(:placeholder-shown)):not(:has(div input:invalid)) button{
                opacity: 1;
                cursor:pointer;
            }
            main div h2{color:#E8E8E8}
            main div h3{
                color: #484848;
                margin-bottom:2rem;
            }
            #hp-o{
                display:none;
                background: #1e1e1e;
                border: 0;
                margin-left:auto;
                color: white;
                border-radius: 5rem;
                height: 100%;
                padding: 0 .9rem 0 .8rem;
                align-items: center;
                justify-content: center;
            }
            @media(max-width:1400px){
                header{
                    padding-left:1rem;
                    padding-right:1rem;
                }
                main{padding:1rem 1rem 0}
                main>div{padding-left:0}
                main>aside,main>div{width:50%}
            }
            @media(max-width:820px){
                main{
                    display:flex;
                    -ms-flex-direction:column-reverse;
                    flex-direction:column-reverse;
                    justify-content: flex-end;
                }
                main>aside{
                    max-height: 45vh;
                    margin:.5rem 0 0;
                    width:100%;
                    height: calc(100vw - 3rem);
                }
                main>div{
                    padding:0;
                    margin:2rem 0 0;
                    width:100%;
                }
                #hp-l{display:none}
                #hp-o{display:flex}
            }
            @media(max-width:450px){
                body{padding:1.5rem .5rem}
            }
            
            main div a{color:var(--grn);text-align:center;width:100%;display:block;font-size:.85rem;font-weight:600}
            main div a span{text-decoration:underline}
        </style>
    </head>
    <body>
        <header>
            <div>
                <svg viewBox="0 0 102 102" xmlns="http://www.w3.org/2000/svg">
                    <path d="m85 32c-2-2 0-6 0-10-80 75-70 75-75 75-11-5-4 2-9-9 0-5 0 5 75-75-4 0-9 2-11 0-2-2 0-7 0-11 2-4 20 0 31 0 0 10 4 28 0 30-4 0-9 2-11 0"></path>
                    <path d="m67 100c0-4-2-8 0-10 2-2 7 0 11 0-26-31-26-24-26-27 0-3 6-9 9-9 3 0-4 0 26 26 0-4-2-8 0-10 2-2 7 0 11 0 4 2 0 20 0 30-10 0-29 4-31 0"></path>
                    <path d="m3 13c5-9-1-3 8-8 2 0-2 0 32 32-1 3-5 7-8 8-32-34-32-30-32-32"></path>
                </svg>
            </div>
            <button id="hp-o" title="mobile-menu" type="button">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill="currentColor" d="m1.6 5h14c.3 0 .5-.2.5-.5 0-.3-.2-.5-.5-.5h-14c-.2 0-.5.2-.5.5 0 .3.3.5.5.5m0 3.5h14c.3 0 .5-.2.5-.5 0-.3-.2-.5-.5-.5h-14c-.2 0-.5.2-.5.5 0 .3.3.5.5.5m0 3.5h14c.3 0 .5-.2.5-.5 0-.3-.2-.5-.5-.5h-14c-.2 0-.5.2-.5.5 0 .3.3.5.5.5"></path></path>
                </svg>
            </button>
            <p>buytofill</p>
            <div id="hp-l">
                <button type="button">BETA</button>
                <!--button type="button">Overview</button>
                <button type="button">Benefits</button>
                <button type="button">Terms & Conditions</button>
                <button type="button">FAQ</button-->
            </div>
        </header>
        <main>
            <div>
                <h2>Welcome to BuyToFill</h2>
                <h3>Profit in no time.</h3>
                <form>
                    <div>
                        <input id="email" type="email" name="email" autocomplete="email" placeholder="johndoe@gmail.com" required>
                        <label for="email">Email</label>
                    </div>
                    <div>
                        <input id="password" type="password" name="password" autocomplete="new-password" placeholder="●●●●●●●●" required>
                        <label for="password">Password</label>
                    </div>
                    <button type="submit">Login</button>
                </form>
                <a href="/signup">Don't have an account? <span>Sign Up</span></a>
            </div>
            <aside style="overflow:hidden;">
                <img src="assets/images/vr.webp" style="height:100%;">
            </aside>
        </main>
        <div id="toast" onclick="this.classList=''">Invalid email or password.</div>
    </body>
</html>
