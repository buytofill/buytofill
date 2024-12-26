<?
    $d = json_decode(file_get_contents('php://input'));
    http_response_code(200);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    if($d->fromAddress == "BestBuyInfo@emailinfo.bestbuy.com"){
        $retailer = 0;
        $data = $d->html;
        $ref = substr($data, strpos($data, 'BBY01-') + 6, 12);
        
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "/");
        
        curl_setopt($ch, CURLOPT_URL, "https://click.emailinfo2.bestbuy.com/?qs=".substr($data, strpos($data, 'View O') - 394, 128));
        $t = curl_exec($ch);
        if($d->subject == "Thanks for your order." || $d->subject == "Your Best Buy order has been canceled."){
            $step = ($d->subject == "Thanks for your order.") ? 1 : 0;
            $s1 = strpos($t,'t1');
            $s2 = strpos($t,'t2');
            curl_setopt($ch, CURLOPT_URL, "https://www.bestbuy.com/profile/ss/orders/email-redirect/order-status?t1=".substr($t,$s1+5,$s2-$s1-18)."&t2=".substr($t,$s2+5,43));
        }elseif($d->subject == "We have your tracking number."){
            $step = 3;
            curl_setopt($ch, CURLOPT_URL, "https://www.bestbuy.com/profile/ss/orders/email-redirect/order-status?token=".substr($t, strpos($t, 'token') + 8, 44)); #check consistency
        }
        if(isset($step)){
            $v = curl_exec($ch);
            curl_setopt($ch, CURLOPT_URL, "https://www.bestbuy.com/profile/ss/api/v1/orders/BBY01-".$ref);
            curl_setopt($ch, CURLOPT_COOKIE, "vt=".substr($v,strpos($v,'vt')+3,36)."; SID;");
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_NOBODY, 0);
            $orderContents = json_decode(curl_exec($ch))->order->items;
            if($step == 3){
                file_put_contents('emails/email_log.txt', print_r(substr($v,strpos($v,'vt')+3,36)."; SID;", 1), FILE_APPEND);
                file_put_contents('emails/email_log.txt', print_r($orderContents, 1), FILE_APPEND);
            }
        }
    }elseif($d->fromAddress == "forwarding-noreply@google.com"){
        $plus = explode('+', explode('@', $d->toAddress)[0])[1];
        if (strlen($plus) == 5 && preg_match('/^[A-Za-z]+$/', $plus)){
            curl_setopt($ch, CURLOPT_URL, "https://mail.google.com/mail/vf-".substr($d->html,strpos($d->html,"%5B"),118));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_exec($ch);
            curl_close();
        }
    } #Add Auth for BTF ID | https://developers.google.com/gmail/api/reference/rest/v1/users.settings.forwardingAddresses/create

    curl_close($ch);
    $ch = curl_init("https://accounts.zoho.com/oauth/v2/token");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'client_id=1000.FXT2V82FAZPDXSQL6BGHFL3E4T0DJC&client_secret=bda8db8e2217d787206d1e5563506df58152c09a95&grant_type=client_credentials&scope=ZohoMail.messages.DELETE,ZohoMail.messages.READ');
    $token = json_decode(curl_exec($ch))->access_token;
    
    if($step === 0 || $step === 1){
        $content = [];
        foreach($orderContents as $item){
            if(isset($content[$item->sku])){
                $content[$item->sku] += $item->quantity;
            }else{
                $content[$item->sku] = $item->quantity;
            }
        }  # UID should be in retailerOrders not commits
        
        curl_setopt_array($ch, [
            CURLOPT_URL => "https://mail.zoho.com/api/accounts/8890026000000024025/folders/".$d->folderId."/messages/".$d->messageId."/header?raw=false",
            CURLOPT_HTTPHEADER => ["Authorization: Bearer ".$token],
            CURLOPT_POSTFIELDS => 0,
            CURLOPT_HTTPGET => 1]); # Get BTF-ID | Replace using google sign in
        $p = json_decode(curl_exec($ch))->data->headerContent->{'Delivered-To'}[0];
        $uid = (ord($p[4])-64)*(ord($p[5])-64)*(ord($p[6])-64)*(ord($p[7])-64)*(ord($p[8])-64);
        
        date_default_timezone_set('America/New_York');
        $date = date('mdHi');
        
        $conn = new mysqli(getenv('DATABASE_HOST'), getenv('DATABASE_USER'), getenv('DATABASE_PASS'), getenv('DATABASE_NAME'));
        if($step === 0){
            $stmt = $conn->prepare("SELECT 1 FROM `retailerOrders` WHERE ref = ?");
            $stmt->bind_param("s", $ref);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if($result->num_rows > 0){
                $stmt = $conn->prepare("UPDATE `retailerOrders` SET status = 0 WHERE ref = ?");
                $stmt->bind_param("s", $ref);
                $exists = true;
            }else{
                $stmt = $conn->prepare("INSERT INTO `retailerOrders` (ref, retailer, date, status) VALUES (?, ?, ?, 0)");
                $stmt->bind_param("sii", $ref, $retailer, $date);
                $exists = false;
            }
        }else{
            $stmt = $conn->prepare("INSERT INTO `retailerOrders` (ref, retailer, date) VALUES (?, ?, ?);");
            $stmt->bind_param("sii", $ref, $retailer, $date);
        }
        
        $stmt->execute();
        $stmt->close();
        
        $rid = $conn->insert_id;
        
        file_put_contents('emails/email_log.txt', $rid, FILE_APPEND);
        
        $stmt = $conn->prepare("INSERT INTO `commit` (uid, oid, rid, qty) VALUES (?, (SELECT o.id FROM `retailerKeys` rk INNER JOIN `order` o ON o.pid = rk.id WHERE rk.retailer = $retailer AND rk.ref = ? AND o.status = 1), ?, ?);");
        foreach ($content as $sku => $quantity) {
            try{
                $stmt->bind_param("isii", $uid, $sku, $rid, $quantity);
                $stmt->execute();
            }catch(Exception $e){
                file_put_contents('emails/email_log.txt', "Make sure there is an order for $sku and retailerKeys.retailer = $retailer and retailerKeys.ref = $sku exists (" . $e->getMessage() . ")\n", FILE_APPEND);
            }
        }
        
        $stmt->close();
    
        $conn->close();
    }elseif($step == 3){
        $content = [];
        foreach ($orderContents as $item) $content[$item->sku][] = [$item->fulfillment->tracking->trackingNumber, $item->quantity];
        
        $conn = new mysqli(getenv('DATABASE_HOST'), getenv('DATABASE_USER'), getenv('DATABASE_PASS'), getenv('DATABASE_NAME'));
        $stmt = $conn->prepare("SELECT rk.id, rk.ref FROM retailerOrders ro JOIN `commit` c ON ro.id = c.rid INNER JOIN `order` o ON o.id = c.oid INNER JOIN retailerKeys rk ON rk.id = o.pid AND rk.retailer = $retailer WHERE ro.ref = ?;");
        $stmt->bind_param("s", $ref);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $sku = $row['ref'];
                if (array_key_exists($sku, $content)) {
                    $info = $content[$sku][0];
                    $insert_stmt = $conn->prepare("INSERT INTO trackings (cid, qty, tracking) VALUES ((
                        SELECT c.id FROM retailerKeys rk INNER JOIN `order` o ON o.pid = rk.id  INNER JOIN retailerOrders ro ON ro.ref = ? INNER JOIN `commit` c ON c.oid = o.id AND c.rid = ro.id AND ro.retailer = $retailer WHERE rk.ref = ?), ?, ?);");
                    $insert_stmt->bind_param("ssis", $ref, $sku, $info[1], $info[0]);
                    $insert_stmt->execute();
                    $insert_stmt->close();
                }
            }
        }
        
        $stmt->close();
        $conn->close();
    }

    curl_close($ch);
    $ch = curl_init("https://mail.zoho.com/api/accounts/8890026000000024025/folders/".$d->folderId."/messages/".$d->messageId);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer ".$token]);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_exec($ch);
    curl_close($ch);

    exit;
?>