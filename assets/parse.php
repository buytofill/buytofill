<?
    $data = file_get_contents('php://stdin');
    $sender = preg_match('/^From:\s*(.*)$/mi', $data, $a) ? $a[1] : '';
    
    #Supports Google | Not yahoo, outlook, icloud | Verification
    #handle only one item of order being cancelled vs whole order cancelled
    if($sender == 'Gmail Team <forwarding-noreply@google.com>'){
        $ch = curl_init(str_replace('mail-settings.google', 'mail.google', preg_match('/https:\/\/mail-settings\.google\.com\/mail\/vf-[^\s"]+/i', $data, $m) ? $m[0] : ''));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_exec($ch);
        curl_close($ch);
    }elseif($sender == '"Best Buy Notifications" <BestBuyInfo@emailinfo.bestbuy.com>'){
        $retailer = 0;
        $ref = substr($data, strpos($data, 'BBY01-') + 6, 12);
        $subject = preg_match('/^Subject:\s*(.*)$/mi', $data, $a) ? $a[1] : '';

        if($subject == "📦 Your package is going to be delivered. 📦") preg_match('/<a href="https:\/\/click\.emailinfo2\.bestbuy\.com\/\?qs=([a-zA-Z0-9]+)".*?>\s*Track Package\s*<\/a>/', $data, $a);
        else preg_match('/<a href="https:\/\/click\.emailinfo2\.bestbuy\.com\/\?qs=([a-zA-Z0-9]+)".*?>\s*View Order Details\s*<\/a>/', $data, $a);

        $ch = curl_init("https://click.emailinfo2.bestbuy.com/?qs=".$a[1]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "/");
        $t = curl_exec($ch);

        if($subject == "Thanks for your order." || $subject == "Your Best Buy order has been canceled."){
            $step = ($subject == "Thanks for your order.") ? 1 : 0;
            $s1 = strpos($t,'t1');
            $s2 = strpos($t,'t2');
            curl_setopt($ch, CURLOPT_URL, "https://www.bestbuy.com/profile/ss/orders/email-redirect/order-status?t1=".substr($t,$s1+5,$s2-$s1-18)."&t2=".substr($t,$s2+5,43));
        }elseif($subject == "We have your tracking number."){
            $step = 3;
            curl_setopt($ch, CURLOPT_URL, "https://www.bestbuy.com/profile/ss/orders/email-redirect/order-status?token=".substr($t, strpos($t, 'token') + 8, 44)); #check consistency
        }elseif($subject == "📦 Your package is going to be delivered. 📦"){
            $step = 4;
            $s1 = strpos($t,'t1');
            $s2 = strpos($t,'t2');
            curl_setopt($ch, CURLOPT_URL, "https://www.bestbuy.com/profile/ss/orders/email-redirect/order-status?t1=".substr($t,$s1+5,$s2-$s1-18)."&t2=".substr($t,$s2+5,43));
        }
        if(isset($step)){
            curl_setopt($ch, CURLOPT_PROXY, "149.51.62.201:8679");
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, "OC13515687:HYqgjfue");
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);

            $v = curl_exec($ch); #file_put_contents("email_log.txt", "\n\n\n\n\n".$v, FILE_APPEND);

            $vt = substr($v,strpos($v,'vt')+3,36);
            curl_setopt($ch, CURLOPT_URL, "https://www.bestbuy.com/profile/ss/api/v1/orders/BBY01-".$ref);
            curl_setopt($ch, CURLOPT_COOKIE, "CTT;vt=".$vt.";");
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_NOBODY, 0);
            $clipped = json_decode(curl_exec($ch));
            $orderContents = $clipped->order->items;
            if($step == 3){
                file_put_contents("email_log.txt", print_r(substr($v,strpos($v,'vt')+3,36), 1) . "\n\n", FILE_APPEND);
                file_put_contents("email_log.txt", print_r($orderContents, 1), FILE_APPEND);
            }
        }
        curl_close($ch);
    }elseif($sender == 'Target <orders@oe1.target.com>'){
        $retailer = 1;
        file_put_contents("email_log.txt", $data);
    }else exit;
    $dsn = "mysql:host=127.0.0.1;dbname=buytofill;charset=utf8mb4";
    
    if($step === 0 || $step === 1){
        $content = [];
        foreach($orderContents as $item){
            if(isset($content[$item->sku])){
                $content[$item->sku] += $item->quantity;
            }else{
                $content[$item->sku] = $item->quantity;
            }
        }  # UID should be in retailerOrders not commits
        #file_put_contents("email_log.txt", print_r($content, true) . "\n\n");
        #file_put_contents("email_log.txt", print_r($data, true) . "\n\n", FILE_APPEND);
        #exit;
        
        $p = getenv('USER');
        $uid = (ord($p[0])-64)*(ord($p[1])-64)*(ord($p[2])-64)*(ord($p[3])-64)*(ord($p[4])-64);
        $date = date('mdHi');

        $pdo = new PDO($dsn, getenv('u'), getenv('p'));
        if($step === 0){
            $stmt = $pdo->prepare("SELECT 1 FROM `retailerOrders` WHERE ref = ?");
            $stmt->execute([$ref]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
            if($result){
                $stmt = $pdo->prepare("UPDATE `retailerOrders` SET status = 0 WHERE ref = ?");
                $stmt->execute([$ref]);
                $exists = true;
            }else{
                $stmt = $pdo->prepare("INSERT INTO `retailerOrders` (ref, retailer, date, status) VALUES (?, ?, ?, 0)");
                $stmt->execute([$ref, $retailer, $date]);
                $exists = false;
            }
        }else{
            $stmt = $pdo->prepare("INSERT INTO `retailerOrders` (ref, retailer, date) VALUES (?, ?, ?);");
            $stmt->execute([$ref, $retailer, $date]);
        }
        
        $rid = $pdo->lastInsertId();
        
        #file_put_contents(__DIR__.'/email_log.txt', $rid, FILE_APPEND);
    
        $stmt = $pdo->prepare("INSERT INTO `commit` (uid, oid, rid, qty) VALUES (?, (SELECT o.id FROM `retailerKeys` rk INNER JOIN `order` o ON o.pid = rk.id WHERE rk.retailer = ? AND rk.ref = ? AND o.status = 1), ?, ?);");
        foreach ($content as $sku => $quantity) {
            try{
                $stmt->execute([$uid, $retailer, $sku, $rid, $quantity]);
            }catch(Exception $e){
                file_put_contents(__DIR__.'/email_log.txt', "Make sure there is an order for $sku and retailerKeys.retailer = $retailer and retailerKeys.ref = $sku exists (" . $e->getMessage() . ")\n");
            }
        }
    }elseif($step == 3 || $step == 4){
        $content = [];
        foreach ($orderContents as $item) {
            $sku = $item->sku;
            $quantity = $item->quantity;
        
            if (!isset($content[$sku])) {
                $content[$sku][] = [$item->fulfillment->tracking->trackingNumber, $quantity];
            } else {
                $content[$sku][1] += $quantity;
            }
        }
    
        $stmt = $pdo->prepare("SELECT rk.id, rk.ref FROM retailerOrders ro JOIN `commit` c ON ro.id = c.rid INNER JOIN `order` o ON o.id = c.oid INNER JOIN retailerKeys rk ON rk.id = o.pid AND rk.retailer = ? WHERE ro.ref = ?");
        $stmt->execute([$retailer, $ref]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($result) {
            foreach ($result as $row) {
                $sku = $row['ref'];
                if (array_key_exists($sku, $content)) {
                    $info = $content[$sku][0];
                    $iStmt = $pdo->prepare("
                        INSERT INTO trackings (cid, qty, tracking) 
                        SELECT c.id, ?, ? FROM retailerKeys rk 
                        INNER JOIN `order` o ON o.pid = rk.id 
                        INNER JOIN retailerOrders ro ON ro.ref = ?
                        INNER JOIN `commit` c ON c.oid = o.id AND c.rid = ro.id AND ro.retailer = ?
                        WHERE rk.ref = ? AND NOT EXISTS (SELECT 1 FROM trackings t WHERE t.cid = c.id AND t.tracking = ?);");
                    $iStmt->execute([$info[1], $info[0], $ref, $retailer, $sku, $info[0]]);
                }
            }
        }
    }
    
    exit;
?>