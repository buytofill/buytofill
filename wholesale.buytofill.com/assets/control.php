<?
    session_start();
    
    //CONTROL setup page control for buyer, both, and admin
    /*$filler = ["deals","commits","sales","label"]; 
    $page = substr($_SERVER['REQUEST_URI'], 1);
    if  (!isset($_SESSION['role']) || 
        (isset($_SESSION['role']) && (
            ($_SESSION['role'] != "filler" && in_array($page, $filler))
        ))
    ){
        header("Location: .");
        exit;
    }*/
    if (!isset($_SESSION['role']) && basename($_SERVER['PHP_SELF']) != 'index.php') {
        header('Location: .');
        exit();
    }
    
    if(isset($_SESSION['role']) && !isset($_SESSION['cryptIV'])){
        $_SESSION['cryptMethod'] = 'AES-256-CBC';
        $_SESSION['cryptKey'] = openssl_random_pseudo_bytes(32);
        $_SESSION['cryptIV'] = openssl_random_pseudo_bytes(openssl_cipher_iv_length($_SESSION['cryptMethod'])); 
    }
    
    foreach(file(__DIR__.'/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line){
        list($name, $value) = explode('=', $line, 2);
        putenv("$name=$value");
    }
    
    function dav($subject){
        $cid = openssl_decrypt(base64_decode($subject), $_SESSION['cryptMethod'], $_SESSION['cryptKey'], 0, $_SESSION['cryptIV']);
        if ($cid === false) return false;
        return filter_var($cid, FILTER_VALIDATE_INT) ?: false;
    }
    
    function enc($subject){
        return base64_encode(openssl_encrypt($subject, $_SESSION['cryptMethod'], $_SESSION['cryptKey'], 0, $_SESSION['cryptIV']));
    }
    
    function o($data, $status = 200){
        http_response_code($status);
        echo json_encode($data);
    }
    
    function N2A($toN2A){
        $alphabet = range('A', 'Z');
        $result = '';
        $toN2A--;
        $position = [0, 0, 0, 0, 0];
        for ($i = 4; $i >= 0; $i--){ 
            $position[$i] = $toN2A % 26; 
            $toN2A = floor($toN2A / 26); 
        }
        foreach ($position as $pos){ 
            $result .= $alphabet[$pos]; 
        }
        return $result;
    }

    function A2N($toA2N){
        $alphabet = range('A','Z');
        $toA2N = str_split($toA2N);
        $result = 0;
        $power = count($toA2N) - 1;
        foreach ($toA2N as $char){
            $position = array_search($char, $alphabet);
            $result += ($position + 1) * (26 ** $power);
            $power--;
        }
        return $result-475254;
    }
    
    $brands = [
        1 => 'Amazon',
        2 => 'Apple',
        3 => 'Roku',
        4 => 'Meta',
        5 => 'Nintendo',
    ];
?>