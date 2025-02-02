<?php
session_start();
if (isset($_SESSION['user']['id'])) {
    header('Location: index.php');
    exit;
}
$host = 'localhost';
$db = 'jsdistributiondb';
$user = 'root';
$pass = '';

$_fn = "";
$_email = "";
$_pwd = "";
$_rpwd = "";
$_resend = "none;";

$conn = new mysqli('208.109.25.227', 'ajsgdcxboylhkAfs', 'U1uSxWV{~}7E', 'jsdistributiondb');

if($conn->connect_error){die("Connection failed: " . $conn->connect_error);}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fn = $_POST['fn'];
    $email = $_POST['email'];
    $pwd = $_POST['pwd'];
    $rpwd = $_POST['rpwd'];

    if (!preg_match("/^[a-zA-Z0-9._]*$/", $fn)){
        $_fn = "Must consist of only letters";
    } else if (strlen($fn) < 3){
        $_fn = "Must have at least 3 letters";
    } else if (strlen($fn) > 50){
        $_fn = "Must have no more than 50 letters";
    }
    if(strlen($pwd) < 8){
        $_pwd = "Password should be at least 8 characters";
    } 
    if(strlen($pwd) > 200){
        $_pwd = "Password should not exceed 200 characters";
    }   
    if (!preg_match("/[A-Z]/", $pwd)){
        $_pwd = "Password should contain at least one uppercase letter (A-Z)";
    } 
    if (!preg_match("/[a-z]/", $pwd)){
        $_pwd = "Password should contain at least one lowercase letter (a-z)";
    } 
    if (!preg_match("/[0-9]/", $pwd)){
        $_pwd = "Password should contain at least one number (0-9)";
    }
    if ($rpwd !== $pwd){
        $_rpwd = "Passwords do not match, please try again.";
    }

    $email_check_query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($email_check_query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $_email = "Email already registered";
    }elseif(empty($_fn) && empty($_email) && empty($_pwd) && empty($_rpwd)){
        if(!isset($_SESSION['user'])){
            $_SESSION['user'] = [
                'userStatus' => "sessionCreatedEmailNotSent",
                'fn' => $fn,
                'pwd' => $pwd,
                'email' => $email
            ];
        }
        $_resend = "block;";
        
        if(isset($_SESSION['user']['userStatus']) && $_SESSION['user']['userStatus'] == "sessionCreatedEmailNotSent"){
            $_SESSION['user']['emailToken'] = bin2hex(random_bytes(16));
            $verificationLink = "http://localhost/dev/jsdis/actions/verify.php?token=" . $_SESSION['user']['emailToken'];

            $ch = curl_init(); curl_setopt($ch, CURLOPT_URL, 'https://api.brevo.com/v3/smtp/email');
            $headers = ['api-key: xkeysib--jADZ7oXwgtEtR3it', 'Content-Type: application/json', 'Accept: application/json'];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $data = [
                "sender" => ["name" => "JS Distribution", "email" => "noreply@jsdistributiongroup.com"],
                "to" => [["email" => $_SESSION['user']['email'], "name" => $_SESSION['user']['fn']]],
                "htmlContent" => "
                <!DOCTYPE html><html><body>
                    <h1 style='font-size: 1rem;  font-weight: 100; color: gray; text-align: center;'>To get started, we need you to confirm your email address.</h1> 
                    <div style='text-align: center; margin-top: 20px;'>
                        <a style='text-decoration: none; padding: 10px 20px; font-weight: bold; font-size: 1rem; border-radius: 100px; background: #333333; color: white;' href='".$verificationLink."'>Confirm email address</a>
                    </div>
                </body></html>",
                "subject" => "Confirm your email address"
            ];
            $payload = json_encode($data); curl_setopt($ch, CURLOPT_POST, true); curl_setopt($ch, CURLOPT_POSTFIELDS, $payload); curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); $response = curl_exec($ch); curl_close($ch);
            $_SESSION['user']['userStatus'] = "emailAutomaticallySent";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JSD Sign Up</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Bruno+Ace+SC&family=Karantina:wght@400;700&family=League+Spartan:wght@600;700&family=Major+Mono+Display&family=Shadows+Into+Light&family=Suez+One&display=swap');
        *{margin:0; padding: 0; font-family: 'League Spartan', sans-serif; text-decoration: 0; color: white;user-select: none;}
        :root{
            --primary: #C4C3D7;
            --purple1: #946bfa;
            --purple2: #8c60fc;
            --purple3: #9d7af5;
            --purple4: #b396fa;
        }
        input,a{padding: 14px 12px 12px; font-size: 1rem; border: 3px solid lightgray; color: black; border-radius: 10px; box-sizing: border-box; outline: 0;}
        input:hover{background: lightgray;}
        a:hover,input:hover,a:focus,input:focus{border-color: rgba(69,100,252,1)}
        #leg div{background-color: var(--purple2); height: 3px; width: 40%; border-radius: 10px;}
        #vis,#vis2{
            transition: all .5s ease;
        }
        #vis:hover,#vis2:hover{ 
            transform: scale(1.2); padding-bottom: 22.5px; 
        }
        .warn{color: #F44336; user-select: text; padding: 5px 2px;}
        @media (max-width: 1100px){
            #anim{
                width: 30% !important;
            }
            #acc{
                width: 100% !important;
            }
        }
        @media (max-width: 500px){
            #anim{
                display: none !important;
            }
            #acc{
                margin-right: 0 !important;
                height: 100% !important;
                border-radius: 0 !important;
            }
            h1{
                font-size: -webkit-xxx-large !important;
            }
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", (event) => {
            const vis = document.getElementById('vis');
            const pass = document.getElementById('pwd');
            const vis2 = document.getElementById('vis2');
            const pass2 = document.getElementById('pwd2');

            vis.addEventListener('click', function(){
                if(vis.innerText=="visibility"){
                    vis.innerText = "visibility_off"
                    pass.type = "text"
                }else{
                    vis.innerText = "visibility";
                    pass.type = "password"
                }
            });
            vis2.addEventListener('click', function(){
                if(vis2.innerText=="visibility"){
                    vis2.innerText = "visibility_off"
                    pass2.type = "text"
                }else{
                    vis2.innerText = "visibility";
                    pass2.type = "password"
                }
            });
            const resend = document.getElementById('resendEmail')
            allowed = true;
            function countdown(i) {
                if (i >= 0) {
                    setTimeout(function() {
                    if (i == 1) {
                        resend.innerText = "Resend Email in " + i + " second";
                    } else {
                        resend.innerText = "Resend Email in " + i + " seconds";
                    }
                    countdown(i - 1);
                    }, 1000);
                } else {
                    resend.innerText = "Resend Email";
                    allowed = true;
                }
            }
            resend.addEventListener('click', function() {
                if(allowed){
                    allowed = false;
                    fetch('actions/resend.php')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                    countdown(10);
                }
            });
        });
    </script>
</head>
<body style="display:flex; height: 100vh; background: #85A24E; align-items: center;">
    <div id="anim" style="width: 55%; height: 80%; padding-top: 10%; display: flex; justify-content: center; align-items: center;">
        <iframe style="border: none; height: 100%; width: 100%;" src="https://rive.app/community/3370-7083-cup-walk/embed" allowfullscreen></iframe>
    </div>
    <div id="acc" style="width: 45%; height: 95vh; margin-right: 2.5vh; border-radius: 20px; background: white; padding: 2vw; box-sizing: border-box; z-index: 10; display: flex; flex-direction: column; justify-content: space-evenly;">
        <div>
            <h1 style="background: linear-gradient(24deg, var(--purple2) 0%, rgba(69,100,252,1) 50%, var(--purple2) 70%); text-align:center; font-family: 'Karantina', cursive; font-size: 5rem; -webkit-text-fill-color: transparent; -webkit-background-clip: text; margin-bottom: 10px;">JSDISTRIBUTION</h1>
            <a href="index.php" style="color: white; display: block; background: linear-gradient(24deg, var(--purple1) 0%, rgba(69,100,252,1) 100%); width: fit-content; margin: auto;">BROWSE AS GUEST</a> 
        </div>
        <form id="emailform" style="display: flex; flex-direction: column;" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <input type="text" name="fn" placeholder="Full Name" required> 
            <p class='warn'><?php echo $_fn?></p>
            <input type="email" name="email" placeholder="Email" required> 
            <p class='warn'><?php echo $_email?></p>
            <div>
                <input id="pwd" style="width: 100%; padding-right: 45px;" type="password" name="pwd" placeholder="Password" required><span id="vis" style="color: gray; cursor: pointer; position: absolute; right: calc(2vw + 35px); padding-top: 12.5px;" class="material-symbols-outlined">visibility</span>
            </div>
            <p class='warn'><?php echo $_pwd?></p>
            <div>
                <input id="pwd2" style="width: 100%; padding-right: 45px;" type="password" name="rpwd" placeholder="Repeat Password" required><span id="vis2" style="color: gray; cursor: pointer; position: absolute; right: calc(2vw + 35px); padding-top: 12.5px;" class="material-symbols-outlined">visibility</span>
            </div>
            <p class='warn'><?php echo $_rpwd?></p>
            <input type="submit" value="CREATE AN ACCOUNT" style="color: white; cursor: pointer; background: linear-gradient(24deg, var(--purple1) 0%, rgba(69,100,252,1) 100%);">
            <a id='resendEmail' style='color: gray; text-align: center; margin-top: 10px; cursor: pointer; display: <?php echo $_resend?>'>RESEND EMAIL CONFIRMATION</a>
            <div id="leg" style="align-items: center; justify-content: center; height: 20px; width: 100%; display: flex; margin: 23px 0 0;"><div></div><p style="padding: 2px 10px 0; color: var(--purple2);">OR</p><div></div></div>
            <a href="login.php" style="margin-top: 20px; color: white; display: block; background: linear-gradient(24deg, var(--purple1) 0%, rgba(69,100,252,1) 100%); text-align: center;">LOGIN</a>
        </form>
    </div>
</body>
</html>