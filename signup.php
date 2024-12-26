<?
    session_start();
    require('main/env.php');
    
    
    /* print_r($result['@metadata']['statusCode']); */ 
    
    if(isset($_POST['email'])){
        list($email, $phone, $pwd, $rpwd, $fn, $ln) = array($_POST['email'], $_POST['phone'], $_POST['pwd'], $_POST['rpwd'], $_POST['fn'], $_POST['ln']);
        
        if($fn==""||$ln==""){ say("First and Last Name is required",400); }
        if(!filter_var($email,FILTER_VALIDATE_EMAIL)||!checkdnsrr(substr(strrchr($email,"@"),1),"MX")){ say("Invalid Email",400); }
        if($phone=="" || strlen($phone) < 10){ say("Invalid Phone Number",400); }
        if(strlen($phone) > 10){ say("Phone Number doesn't need an EXT",400); }
        if(strlen($pwd)<8||!preg_match('/[^A-Za-z0-9]/',$pwd)||strlen($pwd)>32||!preg_match('/[0-9]/',$pwd)||!preg_match('/[a-z]/',$pwd)||!preg_match('/[A-Z]/',$pwd)){ say("Weak Password",400); }
        if($pwd!==$rpwd){ say("Passwords don't match",400); }
        
        if(isset($_SESSION['st'])){
            $time = time() - $_SESSION['st'];
            if($time==1){ say("Retry in 1 second",400); }
            else if($time<30){ say("Retry in ".(30-$time)." seconds",400); }
        }
        
        require 'aws.phar';
        $client = new Aws\Ses\SesClient(['region'=>'us-east-1','credentials'=>['key'=>'AKIAY2M4YSTP4HRRYZXB','secret'=>${{ secrets.AWS_SECRET }}]]);
        
        $code = $_SESSION['emailedCode'] = mt_rand(100000,999999);
        $client->sendEmail([
            'Destination' => [
                'ToAddresses' => [
                    $email
                ]
            ],
            'Message' => [
                'Body' => [
                    'Html' => [
                        'Charset' => 'UTF-8',
                        'Data' => '<div style="background-color:#0F1012;padding:2rem 2rem 3rem 2rem;border-radius:.5rem;font-family:\'Roboto\',system-ui">
                            <img src="https://www.buytofill.com/main/logo.png" style="width:40px;height:40px;margin-bottom:1rem">
                            <div style="max-width:450px;margin:auto;background:#474BFF;padding:.6rem 0 0;border-radius:.25rem">
                                <p style="font-size:1.5rem;font-weight:500;margin:0;color:#fff;padding:0 2rem">Your BuyToFill Code</p>
                                <p style="color:#eee;margin:0;padding:0 2rem">This code will deactivate after 5 minutes or if you request a new code.</p>
                                <div style="color:#eee;font-size:3rem;border-top:2px dotted #eee;display:flex;justify-content:center;flex-wrap:nowrap;padding:.5rem 1rem;margin:1.5rem 1rem .5rem">
                                    <div>'.$code.'</div>
                                </div>
                            </div>
                        </div>'
                    ],
                    'Text' => [
                        'Charset' => 'UTF-8',
                        'Data' => $code
                    ]
                ],
                'Subject' => [
                    'Charset' => 'UTF-8',
                    'Data' => $code ." - Verification Code for BuyToFill"
                ]
            ],
            'Source' => 'BuyToFill <noreply@buytofill.com>'
        ]);
        $_SESSION['st'] = time(); 
        
        list($_SESSION['uEmail'], $_SESSION['pwd'], $_SESSION['fn'], $_SESSION['ln'], $_SESSION['phone']) = array($email, $pwd, $fn, $ln, $phone);
        say("Email sent to ".$_SESSION['uEmail'],200);
    }
    
    if(isset($_POST['emailedCode'])){
        $emailedCode = $_POST['emailedCode'];
    
        if($emailedCode == $_SESSION['emailedCode'] && $_SESSION['emailedCode'] != "" && time() - $_SESSION['st'] < 300){
            $_SESSION['emailedCode'] = $_SESSION['uEmail'];
            require 'aws.phar';
            $client = new Aws\PinpointSMSVoiceV2\PinpointSMSVoiceV2Client(['region'=>'us-east-2','credentials'=>['key'=>'AKIAY2M4YSTP4HRRYZXB','secret'=>${{ secrets.AWS_SECRET }}]]);
            $code = $_SESSION['smsCode'] = mt_rand(100000,999999);
            $result = $client->sendTextMessage([
                'DestinationPhoneNumber' => "+1".$_SESSION['phone'],
                'MessageBody' => $code.' is your BuyToFill verification code.',
                'OriginationIdentity' => '+18558502206'
            ]);
            say($result, 200);
            $_SESSION['st'] = time();
            say("Email verified",200);
        }else{
            say("Invalid Code",400);
        }
    }
     
    if(isset($_POST['smsCode'])){
        $smsCode = $_POST['smsCode'];
        if($smsCode == $_SESSION['smsCode'] && $_SESSION['smsCode'] != "" && time() - $_SESSION['st'] < 300){
            $_SESSION['smsCode'] = $_SESSION['phone'];
            $pass = password_hash($_SESSION['pwd'], PASSWORD_DEFAULT);
            $conn = new mysqli(getenv('DATABASE_HOST'), getenv('DATABASE_USER'), getenv('DATABASE_PASS'), getenv('DATABASE_NAME'));
            $stmt = $conn->prepare("INSERT INTO `filler` (fn, ln, email, phone, pass) SELECT ?, ?, ?, ?, ? FROM dual WHERE NOT EXISTS (SELECT 1 FROM `filler` WHERE email = ? OR phone = ?)");
            $stmt->bind_param("sssissi", $_SESSION['fn'], $_SESSION['ln'], $_SESSION['emailedCode'], $_SESSION['smsCode'], $pass, $_SESSION['emailedCode'], $_SESSION['smsCode']);
            if($stmt->execute()){
                if($conn->affected_rows > 0){
                    $_SESSION['uid'] = $conn->insert_id;
                    $conn->close();
                    $_SESSION['role'] = "filler";
                    $_SESSION['level'] = 1;
                    say("Account verified",200);
                } else {
                    $conn->close();
                    say("Email or Phone is registered",100);
                }
            }
            $conn->close();
        }else{
            say("Invalid Code",400);
        }
    }
    
    function say($data, $statusCode = 200){
        echo json_encode([$data,$statusCode]);
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>BuyToFill - Create Account</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="handheldfriendly" content="true">
        <meta name="MobileOptimized" content="width">
        <meta name="description" content="BuyToFill">
        <meta name="author" content="">
        <meta name="keywords" content="BuyToFill">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="shortcut icon" type="image/png" href="main/logo.png">
        <link rel="stylesheet" href="main/style.css">
        <style> 
            :root{
                --form-width:450px;
            }
            body>div{height:auto}
            .cutenput{display:flex;max-width:var(--form-width);gap:1rem}
            .enput{color:white;position:relative;width:var(--form-width)}
            .enput input{background:none;border:0;box-sizing:border-box;font-size:1rem;color:#fff;border-radius:.25rem;padding:.8rem 1rem;outline:2px solid #333;width:100%;transition:outline .1s ease,padding .2s ease}
            .enput span{position:absolute;left:.5rem;padding:0 .5rem;color:#777;font-size:1rem;top:.81rem;margin-top:.5px;pointer-events:none;font-weight:700;background:#111;transition:all .2s ease}
            .enput:has(input:hover) span,.enput:has(input:focus) span,.enput:not(:has(input:placeholder-shown)) span{top:-23%;font-size:.8rem}
            .enput:has(input:focus) span,enput:hover span{color:#6CEBA5}
            input:focus,.enput:hover input{outline:2px solid #6CEBA5}
            input:-webkit-autofill,input:-webkit-autofill:hover,input:-webkit-autofill:focus,input:-webkit-autofill:active{-webkit-text-fill-color:white!important;-webkit-box-shadow:0 0 0 30px #111 inset!important;caret-color:white}
            input[type="number"]{-moz-appearance:textfield;appearance:textfield}
            input[type="number"]::-webkit-outer-spin-button,input[type="number"]::-webkit-inner-spin-button{-webkit-appearance:none;margin:0}
            .enput svg{stroke-width:1.8px;stroke:#777;fill:none;width:27px;height:27px;position:absolute;right:.8rem;top:1.47rem;transform:translateY(-50%);cursor:pointer}
            .enput svg path:first-child{stroke:none}
            .enput svg:hover path:nth-child(2){fill:#777}
            .enput svg:hover path:nth-child(3),.enput svg:hover path:nth-child(4),.showpass path:nth-child(3),.showpass path:nth-child(4){stroke:var(--mid)}
            .enput .showpass path:nth-child(2){fill:var(--green)!important;stroke:var(--green)}
            .enput input[type='password'],.enput:has(.showpass) input{padding:.8rem 2.8rem .8rem 1rem}
            .enput p{margin:0;overflow:hidden;height:0;transition:all .2s ease;color:#777}
            .enput:has(.pwd:focus) p{height:40px;margin-top:.5rem;margin-bottom:-.5rem}
            .enput:has(p):has(input:focus) span{top:-11%}
            
            form a{cursor:pointer;background:transparent;padding:0}
            input[type="submit"],.submitBack{cursor:pointer;padding:.5rem 1rem;background:var(--green-bg);outline:1px solid;color:var(--green);border:0;border-radius:3px;font-size:1rem;font-weight:300}
            input[type="submit"]:hover,.submitBack:hover{outline-width:2px;font-weight:500}
            
            form{display:flex;flex-direction:column;align-items:center;margin-bottom:1rem;gap:1rem;top:50%;left:50%;transform:translate(-50%,-50%);position:absolute}
            form>div:not(.enput){display:flex;align-items:center;width:100%;gap:1rem;justify-content:space-between}
            form>div:not(.enput) a{color:#666;font-weight:600;text-decoration:none}
            form>div:not(.enput) a span{color:var(--purple);text-decoration:underline}
            form>div:not(.enput) div{display:flex;align-items:center;gap:1rem}
            form>div:not(.enput) div p{color:var(--green);font-weight:600}
            
            div:has(>form){overflow:visible!important;height:fit-content!important}
            @keyframes next{
                0%{transform:translate(-50%,-50%) perspective(600px) scale(1) rotateY(0)} 
                100%{transform:translate(-100%,-50%) perspective(700px) scale(0) rotateY(-70deg)}
            }
            @keyframes toNext{
                0%{transform:translate(0,-50%) perspective(700px) scale(0) rotateY(70deg)}
                100%{transform:translate(-50%,-50%) perspective(600px) scale(1) rotateY(0)}
            }
            @keyframes toBack{
                0%{transform:translate(-50%,-50%) perspective(600px) scale(1) rotateY(0)}
                100%{transform:translate(0,-50%) perspective(700px) scale(0) rotateY(70deg)}
            }
            @keyframes back{
                0%{transform:translate(-100%,-50%) perspective(700px) scale(0) rotateY(-70deg)}
                100%{transform:translate(-50%,-50%) perspective(600px) scale(1) rotateY(0)} 
            }
            body:has(input[name='pwd']:focus) .suh{
                height:320px;
            }
            
            .suh{transition:height .2s ease;height:300px;display:flex;gap:1rem;width:var(--form-width);margin-top:-12rem;flex-direction:column}
            div:has(>form) form:not(:first-child){transform:translate(-100%,-50%) perspective(700px) scale(0) rotateY(70deg)}
            
            @media(max-width:500px){
                :root{
                    --form-width:calc(100vw - 40px);
                }
            }
            @media(max-width:400px){
                .cutenput{
                    flex-direction:column;
                }
                .suh{
                    height:330px;
                }
            }
            @media(max-width:340px){
                .suh{
                    height:360px;
                }
                form>div:not(.enput){
                    flex-direction:column-reverse;
                    gap: 0.5rem;
                }
                form>div:not(.enput) div{
                    width: 100%;
                    justify-content: space-between;
                }
                form>div:not(.enput) a{
                    display: flex;
                    flex-direction: column;
                    width:100%;
                }
            }
            @media(max-width:210px){
                body>div>div,.suh p,.suh h4{display:none}
                .suh{
                    transform: translateY(150%);
                    height: fit-content;
                }
            }
            @media(max-height:500px){
                html{overflow:auto;margin-top:14rem}
            }
            
            :-webkit-scrollbar {
              display:auto!important;
            }
            
            body{background-size:cover;background-position:center center;display:flex;align-items:center;justify-content:center;background-image:url("data:image/svg+xml;utf8,%3Csvg viewBox=%220 0 2000 1500%22 xmlns=%22http:%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cmask id=%22b%22 x=%220%22 y=%220%22 width=%222000%22 height=%221500%22%3E%3Cpath fill=%22url(%23a)%22 d=%22M0 0h2000v1500H0z%22%2F%3E%3C%2Fmask%3E%3Cpath fill=%22%23474BFF%22 d=%22M0 0h2000v1500H0z%22%2F%3E%3Cg style=%22transform-origin:center center%22 stroke=%22%236ceba5%22 stroke-width=%22.4%22 mask=%22url(%23b)%22%3E%3Cpath fill=%22none%22 d=%22M0 0h50v50H0zM250 0h50v50h-50zM400 0h50v50h-50zM550 0h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5b4%22 d=%22M650 0h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M750 0h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5cb%22 d=%22M850 0h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M950 0h50v50h-50zM1050 0h50v50h-50zM1100 0h50v50h-50zM1200 0h50v50h-50zM1450 0h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba598%22 d=%22M1500 0h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1550 0h50v50h-50zM1600 0h50v50h-50zM1650 0h50v50h-50zM1800 0h50v50h-50zM100 50h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba585%22 d=%22M250 50h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M350 50h50v50h-50zM550 50h50v50h-50zM600 50h50v50h-50zM700 50h50v50h-50zM850 50h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba56a%22 d=%22M1250 50h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1350 50h50v50h-50zM1450 50h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba522%22 d=%22M1700 50h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5cc%22 d=%22M1800 50h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5ab%22 d=%22M1900 50h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5e5%22 d=%22M50 100h50v50H50z%22%2F%3E%3Cpath fill=%22%236ceba5f6%22 d=%22M250 100h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M300 100h50v50h-50zM550 100h50v50h-50zM650 100h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5c8%22 d=%22M750 100h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5d0%22 d=%22M900 100h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1150 100h50v50h-50zM1250 100h50v50h-50zM1300 100h50v50h-50zM1400 100h50v50h-50zM1600 100h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba51b%22 d=%22M1650 100h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1800 100h50v50h-50zM1850 100h50v50h-50zM100 150h50v50h-50zM550 150h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba59c%22 d=%22M700 150h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba57a%22 d=%22M750 150h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba588%22 d=%22M850 150h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1000 150h50v50h-50zM1050 150h50v50h-50zM1100 150h50v50h-50zM1750 150h50v50h-50zM300 200h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba50c%22 d=%22M450 200h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M550 200h50v50h-50zM850 200h50v50h-50zM900 200h50v50h-50zM1100 200h50v50h-50zM1200 200h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5fb%22 d=%22M1250 200h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1500 200h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba587%22 d=%22M1550 200h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1700 200h50v50h-50zM1800 200h50v50h-50zM1950 200h50v50h-50zM50 250h50v50H50zM250 250h50v50h-50zM300 250h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5d1%22 d=%22M650 250h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M900 250h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5d2%22 d=%22M1200 250h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba599%22 d=%22M1300 250h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1450 250h50v50h-50zM1550 250h50v50h-50zM1600 250h50v50h-50zM1750 250h50v50h-50zM1800 250h50v50h-50zM1850 250h50v50h-50zM1950 250h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba577%22 d=%22M0 300h50v50H0z%22%2F%3E%3Cpath fill=%22%236ceba561%22 d=%22M50 300h50v50H50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M100 300h50v50h-50zM350 300h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba574%22 d=%22M550 300h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M750 300h50v50h-50zM800 300h50v50h-50zM900 300h50v50h-50zM950 300h50v50h-50zM1050 300h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba575%22 d=%22M1250 300h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1350 300h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba593%22 d=%22M1800 300h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba573%22 d=%22M1850 300h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M0 350h50v50H0z%22%2F%3E%3Cpath fill=%22%236ceba53f%22 d=%22M200 350h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M250 350h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba589%22 d=%22M400 350h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5fc%22 d=%22M450 350h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba53c%22 d=%22M650 350h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1350 350h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5f4%22 d=%22M1500 350h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1650 350h50v50h-50zM1850 350h50v50h-50zM1900 350h50v50h-50zM0 400h50v50H0zM100 400h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba537%22 d=%22M450 400h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M550 400h50v50h-50zM600 400h50v50h-50zM800 400h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5ae%22 d=%22M850 400h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1050 400h50v50h-50zM1500 400h50v50h-50zM1600 400h50v50h-50zM1650 400h50v50h-50zM1700 400h50v50h-50zM1850 400h50v50h-50zM1950 400h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5b5%22 d=%22M50 450h50v50H50z%22%2F%3E%3Cpath fill=%22%236ceba5ba%22 d=%22M100 450h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba58b%22 d=%22M150 450h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M250 450h50v50h-50zM350 450h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5d4%22 d=%22M400 450h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba56b%22 d=%22M550 450h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M750 450h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba553%22 d=%22M900 450h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M950 450h50v50h-50zM1200 450h50v50h-50zM1350 450h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba575%22 d=%22M1550 450h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1900 450h50v50h-50zM1950 450h50v50h-50zM150 500h50v50h-50zM200 500h50v50h-50zM300 500h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5c6%22 d=%22M550 500h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M600 500h50v50h-50zM650 500h50v50h-50zM1150 500h50v50h-50zM1250 500h50v50h-50zM1350 500h50v50h-50zM1550 500h50v50h-50zM0 550h50v50H0zM100 550h50v50h-50zM400 550h50v50h-50zM500 550h50v50h-50zM600 550h50v50h-50zM1150 550h50v50h-50zM1800 550h50v50h-50zM1900 550h50v50h-50zM1950 550h50v50h-50zM200 600h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba556%22 d=%22M350 600h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba55a%22 d=%22M400 600h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M600 600h50v50h-50zM850 600h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5fb%22 d=%22M1050 600h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1100 600h50v50h-50zM1200 600h50v50h-50zM1400 600h50v50h-50zM1500 600h50v50h-50zM1550 600h50v50h-50zM1600 600h50v50h-50zM1650 600h50v50h-50zM1700 600h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5af%22 d=%22M1750 600h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1800 600h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5e2%22 d=%22M250 650h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M750 650h50v50h-50zM800 650h50v50h-50zM1050 650h50v50h-50zM1150 650h50v50h-50zM1200 650h50v50h-50zM1400 650h50v50h-50zM1550 650h50v50h-50zM1600 650h50v50h-50zM1800 650h50v50h-50zM1850 650h50v50h-50zM1950 650h50v50h-50zM50 700h50v50H50zM150 700h50v50h-50zM350 700h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba50a%22 d=%22M400 700h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba59f%22 d=%22M450 700h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M500 700h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5dd%22 d=%22M550 700h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M600 700h50v50h-50zM850 700h50v50h-50zM900 700h50v50h-50zM950 700h50v50h-50zM1100 700h50v50h-50zM1150 700h50v50h-50zM1400 700h50v50h-50zM1500 700h50v50h-50zM1650 700h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba59b%22 d=%22M1900 700h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M0 750h50v50H0z%22%2F%3E%3Cpath fill=%22%236ceba53f%22 d=%22M50 750h50v50H50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M100 750h50v50h-50zM300 750h50v50h-50zM350 750h50v50h-50zM750 750h50v50h-50zM1050 750h50v50h-50zM1200 750h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba577%22 d=%22M1300 750h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1350 750h50v50h-50zM1550 750h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba54f%22 d=%22M1900 750h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M250 800h50v50h-50zM300 800h50v50h-50zM450 800h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba53c%22 d=%22M600 800h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5e5%22 d=%22M750 800h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M900 800h50v50h-50zM950 800h50v50h-50zM1000 800h50v50h-50zM1100 800h50v50h-50zM1250 800h50v50h-50zM1300 800h50v50h-50zM1400 800h50v50h-50zM1450 800h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba51f%22 d=%22M1550 800h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1650 800h50v50h-50zM0 850h50v50H0zM250 850h50v50h-50zM450 850h50v50h-50zM500 850h50v50h-50zM600 850h50v50h-50zM700 850h50v50h-50zM750 850h50v50h-50zM950 850h50v50h-50zM1000 850h50v50h-50zM1600 850h50v50h-50zM1650 850h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5a3%22 d=%22M1750 850h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M0 900h50v50H0zM200 900h50v50h-50zM350 900h50v50h-50zM450 900h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba551%22 d=%22M600 900h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5e6%22 d=%22M650 900h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5e0%22 d=%22M1000 900h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1250 900h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba58c%22 d=%22M1300 900h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1400 900h50v50h-50zM1500 900h50v50h-50zM1550 900h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5a0%22 d=%22M1650 900h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba58b%22 d=%22M150 950h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5d6%22 d=%22M200 950h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M350 950h50v50h-50zM400 950h50v50h-50zM600 950h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5a2%22 d=%22M1050 950h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5cb%22 d=%22M1200 950h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1350 950h50v50h-50zM1450 950h50v50h-50zM1500 950h50v50h-50zM1600 950h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba55f%22 d=%22M1900 950h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M0 1000h50v50H0z%22%2F%3E%3Cpath fill=%22%236ceba5f4%22 d=%22M300 1000h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M350 1000h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5f8%22 d=%22M400 1000h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M500 1000h50v50h-50zM550 1000h50v50h-50zM700 1000h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5a4%22 d=%22M950 1000h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1050 1000h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5e2%22 d=%22M1250 1000h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1300 1000h50v50h-50zM1800 1000h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba539%22 d=%22M1850 1000h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1950 1000h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba508%22 d=%22M50 1050h50v50H50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M150 1050h50v50h-50zM200 1050h50v50h-50zM250 1050h50v50h-50zM350 1050h50v50h-50zM450 1050h50v50h-50zM750 1050h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5e7%22 d=%22M800 1050h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba58d%22 d=%22M900 1050h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M950 1050h50v50h-50zM1050 1050h50v50h-50zM1100 1050h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5be%22 d=%22M1150 1050h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1300 1050h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba51b%22 d=%22M1650 1050h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1750 1050h50v50h-50zM1900 1050h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba58d%22 d=%22M1950 1050h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M0 1100h50v50H0zM50 1100h50v50H50zM500 1100h50v50h-50zM600 1100h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5d2%22 d=%22M950 1100h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1150 1100h50v50h-50zM1250 1100h50v50h-50zM1350 1100h50v50h-50zM1700 1100h50v50h-50zM1750 1100h50v50h-50zM1850 1100h50v50h-50zM1950 1100h50v50h-50zM50 1150h50v50H50zM100 1150h50v50h-50zM300 1150h50v50h-50zM350 1150h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba581%22 d=%22M400 1150h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M600 1150h50v50h-50zM750 1150h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5a5%22 d=%22M1050 1150h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1100 1150h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba584%22 d=%22M1300 1150h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1400 1150h50v50h-50zM1450 1150h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba54b%22 d=%22M1600 1150h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba522%22 d=%22M1650 1150h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M50 1200h50v50H50z%22%2F%3E%3Cpath fill=%22%236ceba520%22 d=%22M150 1200h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M350 1200h50v50h-50zM500 1200h50v50h-50zM650 1200h50v50h-50zM750 1200h50v50h-50zM800 1200h50v50h-50zM950 1200h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5a6%22 d=%22M1050 1200h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1100 1200h50v50h-50zM1150 1200h50v50h-50zM1350 1200h50v50h-50zM1400 1200h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5a4%22 d=%22M1500 1200h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1600 1200h50v50h-50zM1650 1200h50v50h-50zM1850 1200h50v50h-50zM50 1250h50v50H50zM250 1250h50v50h-50zM300 1250h50v50h-50zM500 1250h50v50h-50zM550 1250h50v50h-50zM700 1250h50v50h-50zM800 1250h50v50h-50zM950 1250h50v50h-50zM1000 1250h50v50h-50zM1150 1250h50v50h-50zM1300 1250h50v50h-50zM1500 1250h50v50h-50zM1650 1250h50v50h-50zM1750 1250h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5b7%22 d=%22M1850 1250h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1900 1250h50v50h-50zM1950 1250h50v50h-50zM50 1300h50v50H50zM250 1300h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba59d%22 d=%22M450 1300h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M550 1300h50v50h-50zM600 1300h50v50h-50zM750 1300h50v50h-50zM800 1300h50v50h-50zM850 1300h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5c4%22 d=%22M1050 1300h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1100 1300h50v50h-50zM1300 1300h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba595%22 d=%22M1600 1300h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba50f%22 d=%22M1650 1300h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1700 1300h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba58d%22 d=%22M1850 1300h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M150 1350h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5ce%22 d=%22M300 1350h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M350 1350h50v50h-50zM850 1350h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba50e%22 d=%22M950 1350h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba561%22 d=%22M1000 1350h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1050 1350h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba555%22 d=%22M1100 1350h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5a4%22 d=%22M1150 1350h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1200 1350h50v50h-50zM1250 1350h50v50h-50zM1450 1350h50v50h-50zM1600 1350h50v50h-50zM1750 1350h50v50h-50zM1800 1350h50v50h-50zM1950 1350h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba571%22 d=%22M50 1400h50v50H50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M250 1400h50v50h-50zM550 1400h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba52e%22 d=%22M650 1400h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5fb%22 d=%22M700 1400h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M850 1400h50v50h-50zM1000 1400h50v50h-50zM1100 1400h50v50h-50zM1500 1400h50v50h-50zM1600 1400h50v50h-50zM1650 1400h50v50h-50zM1700 1400h50v50h-50zM1900 1400h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5b0%22 d=%22M1950 1400h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M0 1450h50v50H0zM150 1450h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba50f%22 d=%22M200 1450h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M350 1450h50v50h-50zM500 1450h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba566%22 d=%22M550 1450h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M700 1450h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5f6%22 d=%22M800 1450h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M850 1450h50v50h-50zM900 1450h50v50h-50zM1050 1450h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5ff%22 d=%22M1200 1450h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1250 1450h50v50h-50zM1450 1450h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba57e%22 d=%22M1600 1450h50v50h-50z%22%2F%3E%3Cpath fill=%22none%22 d=%22M1850 1450h50v50h-50z%22%2F%3E%3Cpath fill=%22%236ceba5de%22 d=%22M1950 1450h50v50h-50z%22%2F%3E%3C%2Fg%3E%3Cdefs%3E%3CradialGradient id=%22a%22%3E%3Cstop offset=%2250%25%22 stop-color=%22%23fff%22 stop-opacity=%220%22%2F%3E%3Cstop offset=%221%22 stop-color=%22%23fff%22 stop-opacity=%22.5%22%2F%3E%3C%2FradialGradient%3E%3C%2Fdefs%3E%3C%2Fsvg%3E")}
        </style>
    </head>
    <body>
        <?require('main/notifs.php')?>
        <div style="position:absolute;width:100vw;align-items:center;overflow:hidden;display:flex;z-index:-1;justify-content:center;">
            <svg viewBox="0 0 480 480" style="height:950px;min-width:950px">
        	    <path fill="#111" d="M412.5,275.5Q375,311,374.5,372Q374,433,316.5,415Q259,397,214,412Q169,427,125,404Q81,381,75,331.5Q69,282,62.5,238.5Q56,195,79.5,157Q103,119,141,98.5Q179,78,220,71Q261,64,318,54Q375,44,407,90Q439,136,444.5,188Q450,240,412.5,275.5Z" />
            </svg>
        </div>
        <div style="display:flex;flex-direction:column;justify-content:center;align-items:center;gap:1rem;position:absolute;">
            <div class="suh">
                <div style="text-align:center">
                    <a href="."><svg viewBox="0 0 100 100" fill="#6CEBA5" height="50px"><path d="m85 30v-10l-75 75-9-9 75-75h-11v-11h31v30"></path><path d="m67 100v-10h11l-26-27 9-9 26 26v-10h11v30"></path><path d="m1 11 8-8 32 32-8 8"></path></svg></a>
                </div>
                <h4 style="color: var(--purple);font-size:1.5rem; width:100%;margin-bottom:-.5rem;">Sign Up</h4>
                <p style="color:#666;text-align:left;width:100%;font-weight:500">Earn with us from the comfort of your home.</p>
            </div>
            <div>
                <form onsubmit="proceed(event,this)">
                    <div class="cutenput">
                        <div class="enput">
                            <input name="fn" type="text" placeholder="" autocomplete="given-name">
                            <span>First Name</span>
                        </div>
                        <div class="enput">
                            <input name="ln" type="text" placeholder="" autocomplete="family-name">
                            <span>Last Name</span>
                        </div>
                    </div>
                    <div class="enput">
                        <input name="email" type="email" placeholder="" autocomplete="email">
                        <span>Email</span>
                    </div>
                    <div class="enput">
                        <input name="phone" type="number" placeholder="" autocomplete="phone">
                        <span>Phone Number</span>
                    </div>
                    <div class="enput">
                        <input name="pwd" type="password" placeholder="" autocomplete="new-password" class="pwd">
                        <span>Password</span>
                        <svg onclick="showpass(this)"viewBox="0 0 24 24"><path d="M0 0h24v24H0z"/><path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z"/><path d="M8 11m0 1a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1v3a1 1 0 0 1 -1 1h-6a1 1 0 0 1 -1 -1z" /><path d="M10 11v-2a2 2 0 1 1 4 0v2"/></svg>
                        <p>Password must be 8-32 characters long, including an uppercase letter, a lowercase letter, a number, and a special character.</p>
                    </div>
                    <div class="enput">
                        <input name="rpwd" type="password" placeholder="" autocomplete="current-password" class="pwd">
                        <span>Repeat Password</span>
                        <svg onclick="showpass(this)"viewBox="0 0 24 24"><path d="M0 0h24v24H0z"/><path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z"/><path d="M8 11m0 1a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1v3a1 1 0 0 1 -1 1h-6a1 1 0 0 1 -1 -1z" /><path d="M10 11v-2a2 2 0 1 1 4 0v2"/></svg>
                    </div>
                    <div>
                        <div style="display:flex; flex-direction:column;gap:0;align-items:revert;">
                            <a href="signupasbuyer">Not a filler? <span>Register as Buyer</span></a>
                            <a href=".">Already have an account? <span>Login</span></a>
                        </div>
                        <div><p>1/3</p><input type="submit" value="Proceed"></div>
                    </div>
                </form>
                <form onsubmit="vEmail(event,this)">
                    <div class="enput">
                        <input name="emailedCode" type="number" placeholder="" autocomplete="one-time-code">
                        <span>Email Code</span>
                    </div>
                    <div>
                        <button class="submitBack" type="button" onclick="vEmail(this)">Back</button>
                        <div>
                            <p>2/3</p>
                            <input type="submit" value="Proceed">
                        </div>
                    </div>
                </form>
                <form onsubmit="vPhone(event,this)">
                    <div class="enput">
                        <input name="smsCode" type="number" placeholder="" autocomplete="one-time-code">
                        <span>SMS Code</span>
                    </div>
                    <div>
                        <button class="submitBack" type="button" onclick="vPhone(this)">Back</button>
                        <div>
                            <p>3/3</p>
                            <input type="submit" value="Create Account">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </body>
    <script>
        
        function proceed(e,a){
            e.preventDefault();
            const formData = new FormData(a);
            fetch('#',{method:'POST',body:formData})
            .then(response => response.json())
            .then(data => {
                sendNotif(data[0],data[1])
                if(data[1] == 200){
                    a.style="animation:next .5s ease forwards";
                    a.parentNode.parentNode.querySelector(".suh").style="height:175px";
                    a.parentNode.querySelector("form:nth-child(2)").style="animation:toNext .6s ease forwards";
                }
            });
        }
        function vEmail(e,a){
            if(!a){
                a = e.parentNode.parentNode;
                a.style="animation:toBack .5s ease forwards";
                a.parentNode.parentNode.querySelector(".suh").style="height:300px";
                a.parentNode.querySelector("form:first-child").style="animation:back .6s ease forwards";
            }else if(e.submitter.value == "Proceed"){
                e.preventDefault();
                const formData = new FormData(a);
                fetch('#',{method:'POST',body:formData})
                .then(response => response.json())
                .then(data => {
                    sendNotif(data[0],data[1])
                    if(data['1'] == 200){
                        const formData = new FormData(a);
                        a.style="animation:next .5s ease forwards";
                        a.parentNode.querySelector("form:nth-child(3)").style="animation:toNext .6s ease forwards";
                    }
                });
            }
        }
        function vPhone(e,a){
            if(!a){
                a = e.parentNode.parentNode;
                a.style="animation:toBack .5s ease forwards";
                a.parentNode.querySelector("form:nth-child(2)").style="animation:back .6s ease forwards";
            }else if(e.submitter.value == "Create Account"){
                e.preventDefault();
                const formData = new FormData(a);
                fetch('#',{method:'POST',body:formData})
                .then(response => response.json())
                .then(data => {
                    sendNotif(data[0],data[1]);
                    if(data['1'] == 200){
                        window.location.href = "https://buytofill.com/deals?tutorial"
                    }
                });
            }
        }
        function showpass(a){
            var b=a.parentNode.querySelector('input');
            if(b.getAttribute('type')=='text'){b.setAttribute('type','password'); a.classList=""}
            else{b.setAttribute('type', 'text');a.classList="showpass"}
        }
    </script>
</html>
