<?php
session_start();
if($_SESSION['user']['userStatus'] == "emailAutomaticallySent"){ 
    $verificationLink = "https://www.jsdistributiongroup.com/actions/verify.php?token=".$_SESSION['user']['emailToken'];

    $ch = curl_init(); curl_setopt($ch, CURLOPT_URL, 'https://api.brevo.com/v3/smtp/email');
    $headers = ['AK', 'Content-Type: application/json', 'Accept: application/json'];
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
    $_SESSION['user']['userStatus'] = "waiting";
    sleep(10);
    $_SESSION['user']['userStatus'] = "emailAutomaticallySent";
}
?>