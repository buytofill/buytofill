<?
    session_start();
    
    if(isset($_SESSION['role']) && !isset($_SESSION['cryptIV'])) $_SESSION['crypt'] = [openssl_random_pseudo_bytes(32), openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-256-CBC'))];
    
    function dav($subject){
        $cid = openssl_decrypt(base64_decode($subject), 'AES-256-CBC', $_SESSION['crypt'][0], 0, $_SESSION['crypt'][1]);
        if ($cid === false) return false;
        return filter_var($cid, FILTER_VALIDATE_INT) ?: false;
    }
    
    function enc($subject){
        return base64_encode(openssl_encrypt($subject, 'AES-256-CBC', $_SESSION['crypt'][0], 0, $_SESSION['crypt'][1]));
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

    function A2N($s){
        $b=unpack('C5',$s);
        return ((($b[1]-65)*26+$b[2]-65)*26+$b[3]-65)*26*26+($b[4]-65)*26+$b[5]-64;
    }
?>