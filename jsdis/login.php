<?php
session_start();
$dsn = "mysql:host=127.0.0.1;dbname=buytofill;charset=utf8mb4";

if (isset($_SESSION['user']['id'])) {
    header('Location: .');
    exit;
}

$host = 'localhost';
$db = 'jsdistributiondb';
$user = 'root';
$pass = '';

$_status = "";

$conn = new mysqli('208.109.25.227', 'ajsgdcxboylhkAfs', 'U1uSxWV{~}7E', 'jsdistributiondb');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $pwd = $_POST['pwd'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($pwd, $user['pwd'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'fn' => $user['fn'],
            'email' => $user['email'],
            'role' => $user['role']
        ];
        
        header('Location: .');
        exit;
    }else{
        $_status = "Invalid Email or Password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JSD Login</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Bruno+Ace+SC&family=Karantina:wght@400;700&family=League+Spartan:wght@600;700&family=Major+Mono+Display&family=Shadows+Into+Light&family=Suez+One&display=swap');
        :root{
            --primary: #C4C3D7;
            --purple1: #946bfa;
            --purple2: #8c60fc;
            --purple3: #9d7af5;
            --purple4: #b396fa;
        }
        *{margin:0; padding: 0; font-family: 'League Spartan', sans-serif; text-decoration: 0; color: white; user-select: none; outline: 0;}
        body{background: var(--primary);}
        input:hover{background: lightgray;}
        a:hover,input:hover,a:focus,input:focus{border-color: rgba(69,100,252,1)}
        input,a{padding: 14px 12px 12px; margin-top: 20px; font-size: 1rem; border: 3px solid lightgray; color: black; border-radius: 10px; box-sizing: border-box;}
        #leg div{background-color: var(--purple2); height: 3px; width: 40%; border-radius: 10px;}
        @media (max-width: 1400px){
            h1{font-size: 4rem !important;}
        }
        @media (max-width: 1200px){
            #acc,#anim{width: 50% !important;}
            #before{width: 90vw !important; left: 5vw !important;}
            #inim{padding-left: 75px !important;}
        }
        @media (max-width: 800px){
            #anim{display: none !important;}
            #acc{width: 100% !important; padding: 5vw !important;}
            h1{margin-bottom: 50px;}
            #before{top: 5vh !important; height: 90vh;}
        }
        @media (max-width: 450px){
            #circle{display: none !important;}
            #before{height: 100vh !important; top: 0 !important; left: 0 !important; width: 100vw !important; border-radius: 0 !important;}
        }
        @media (max-width: 350px){
            h1{font-size: 3rem !important;}
            h1 span{font-size: 1.5rem !important;}
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", (event) => {
            const animTab = document.getElementById("anim").getBoundingClientRect();
            const iframe = document.getElementById("inim");
            iframe.style.height = animTab.height + "px"
            iframe.style.width = animTab.height + "px"

            const vis = document.getElementById('vis');
            const pass = document.getElementById('pwd')

            vis.addEventListener('click', function(){
                if(vis.innerText=="visibility"){
                    vis.innerText = "visibility_off"
                    pass.type = "text"
                }else{
                    vis.innerText = "visibility";
                    pass.type = "password"
                }
            });
        });
        <div id="google_translate_element"></div>

<script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');
}
</script>

<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

    </script>
</head>
<body style="height: 100vh; overflow: hidden;">
    <div id="circle" style="width: 150vw; height: 150vw; top: 50%; transform: translate(-50%, -50%); background: var(--purple1); display: block; border-radius: 50%; z-index: -10; position: absolute;"></div>
    <div id="before" style="position: absolute; width: 70vw; background: var(--purple2); height: 80vh; top: 10vh; left: 15vw; border-radius: 20px; display: flex; overflow:hidden;">
        <div id="anim" style="width: 55%; display: flex; justify-content: center; align-items: center; background: #2CD2B6;">
            <iframe id="inim" style="padding: 100px 0 0 20px; border: none; position: absolute;" src="https://rive.app/community/518-984-or-switch-it-up/embed" allowfullscreen></iframe>
        </div>
        <div id="acc" style="width: 45%; background: white; padding: 2vw; box-sizing: border-box; z-index: 10; display: flex; flex-direction: column; justify-content: space-evenly;">
            <form style="display: flex; flex-direction: column;" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <h1 style="background: linear-gradient(24deg, var(--purple2) 0%, rgba(69,100,252,1) 50%, var(--purple2) 70%); text-align:center; font-family: 'Karantina', cursive; font-size: 5rem; -webkit-text-fill-color: transparent; -webkit-background-clip: text;">JSDISTRIBUTION<br><span style="font-size: 2rem; padding-top: 10px;">Welcome back!</span></h1>
                <p style="color: gray; text-align: center;"><?php echo $_status?></p>
                <input type="email" name="email" placeholder="Email" required> 
                <div>
                    <input id="pwd" style="width: 100%; padding-right: 45px;" type="password" name="pwd" placeholder="Password" required><span id="vis" style="color: gray; cursor: pointer; position: absolute; right: calc(2vw + 15px); padding-top: 30px;" class="material-symbols-outlined">visibility</span>
                </div>
                <input type="submit" value="LOGIN" style="color: white; cursor: pointer; background: linear-gradient(24deg, var(--purple1) 0%, rgba(69,100,252,1) 100%);">
                <div id="leg" style="align-items: center; justify-content: center; height: 20px; width: 100%; display: flex; margin: 23px 0 0;"><div></div><p style="padding: 2px 10px 0; color: var(--purple2);">OR</p><div></div></div>
                <a href="signup.php" style="color: white; display: block; text-align: center; background: linear-gradient(24deg, var(--purple1) 0%, rgba(69,100,252,1) 100%);">SIGN UP</a>
                <a href="." style="margin-top: 20px; color: gray; text-align: center;">BROWSE AS GUEST</a> 
            </form>
        </div>
    </div>
</body>
</html>