<?  
    try {
        $pdo = new PDO("mysql:host=127.0.0.1;dbname=buytofill;charset=utf8mb4", "admin_jasdgfasfdafes", "wZR}v&xg=S0Fsadwa3213damn");

        echo "Connected successfully using PDO!";
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
    exit;
    require 'assets/control.php';
    
    if($_SERVER["REQUEST_METHOD"]=="POST"){
        if(isset($_POST['id'],$_POST['qty'])){
            $oid = dav($_POST['id']);
            if(!$oid) echo 'Tampered'; 
            $uid = $_SESSION['uid'];
            $qty = $_POST['qty'];
            
            $conn = new mysqli(getenv('DATABASE_HOST'), getenv('DATABASE_USER'), getenv('DATABASE_PASS'), getenv('DATABASE_NAME'));
            $conn->begin_transaction();
            
            try {
                $checkStmt = $conn->prepare("SELECT commited, qty FROM `order` WHERE id = ?");
                $checkStmt->bind_param("i", $oid);
                $checkStmt->execute();
                $checkStmt->bind_result($currentCommited, $orderQty);
                $checkStmt->fetch();
                $checkStmt->close();
            
                $newCommited = $currentCommited + $qty;
                date_default_timezone_set('America/New_York');
                $created = date('mdHi');
                
                if ($newCommited <= $orderQty) {
                    $stmt = $conn->prepare("INSERT INTO `commit` (qty, oid, uid, created) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("iiii", $qty, $oid, $uid, $created);
                    $stmt->execute();
            
                    $updateStmt = $conn->prepare("UPDATE `order` SET commited = ? WHERE id = ?");
                    $updateStmt->bind_param("ii", $newCommited, $oid);
                    $updateStmt->execute();
                
                    $conn->commit();
                    $stmt->close();
                    $updateStmt->close();
                    
                    o('Created commitment');
                } else {
                    $conn->rollback();
                    o('Commitment exceeds order limit', 400);
                }
                $conn->close();
            } catch (Exception $e) {
                $conn->rollback();
                if (isset($stmt)) $stmt->close();
                if (isset($updateStmt)) $updateStmt->close();
                $conn->close();
                o('Error inserting data', 400);
            }
        }
        exit;
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
        <meta name="keywords" content="BuyToFill"/>
        <link rel="icon" href="assets/favicon.ico"/>
        <link rel="stylesheet" href="assets/styles.css">
        <style>
            :root{--deal-p: 0%}
            
            #container{gap:1rem;display:-ms-grid;-ms-grid-columns: repeat(3, 1fr);display:grid;grid-template-columns:repeat(3, 1fr)}
            #container>div{z-index:0;overflow:hidden;position:relative;border-radius:.4rem;background:#141414}
            #container>div:before{z-index:-1;content:'';position:absolute;background:#ffffff04;width:var(--deal-p);top:0;left:0;height:100%}
            
            .above,.float{display:flex}
            .img{display:flex;height:80px;width:80px;min-width:80px;padding:.5rem;background:#2a2a2a;border-bottom-right-radius:.4rem}
            .img img{max-height:100%;max-width:100%;border-radius:.2rem;margin:auto}
            .info{width:calc(100% - 80px)}
            .item{padding-top:.4rem;color:#bbb;font-weight:400;text-wrap:nowrap}
            .item p{overflow-x:auto;padding:0 .5rem}
            ::-webkit-scrollbar{display:none}
            .item p:last-child{font-weight:300;color:#aaa;padding-top:.2rem;font-size:.9rem}
            .float>p,span{font-size:.8rem;font-weight:500;color:#aaa;padding:.35rem .5rem;border-bottom-right-radius:.4rem}
            .float>p{display:flex;background:#2a2a2a;position:relative}
            .float>p:before{content:'';position:absolute;width:8px;height:8px;left:0;bottom:-8px;background-image:url("data:image/svg+xml;charset=UTF-8,<svg viewBox='0 0 3 3' xmlns='http://www.w3.org/2000/svg'><path d='m3 0c-1.7 0-3 1.3-3 3v-3' fill='%232a2a2a'/></svg>")}
            .float span{margin-left:-.5rem;background:#1f1f1f;color:#777;padding-left:1rem}
            .banner{margin-left:auto;font-size:.7rem;font-weight:900;display:flex;padding:.1rem .8rem;border-bottom-left-radius:.4rem}
            .banner p{margin:auto}
            .banner.f{background:var(--grn)}
            .banner.h{background:#ff7d2f}
            .banner.n{background:var(--purp);color:#fff}
            
            .below{display:flex}
            .links{width:100%;display:flex;overflow:auto;padding:.35rem}
            .links a{cursor:pointer;min-width:30px;width:30px;height:30px;overflow:hidden;border-radius:.2rem}
            .links a:not(:last-child){margin-right:.35rem}
            .links svg{width:100%;height:100%}
            form{display:flex}
            form input,form button{border-top-left-radius:.4rem;padding:.2rem 1rem}
            form input[type="number"]{background:#2a2a2a;color:#eee;margin-right:-.4rem;padding-right:1.4rem;text-align:center;width:7ch}
            form button{cursor:pointer;text-wrap:nowrap;background:var(--grn);font-weight:600;text-align:center;font-size:.8rem}
            form p{background: #333333;color:#bbb;margin-right:-.4rem;padding:0.74rem 1.4rem 0 1rem;font-size:.9rem;text-align:center;border-top-left-radius:.4rem}
            input[type="number"]::-webkit-outer-spin-button,input[type="number"]::-webkit-inner-spin-button{-webkit-appearance:none;margin:0}
            input[type="number"]{-moz-appearance:textfield}
        </style>
    </head>
    <body>
        <?require 'assets/header.php'?>
        <nav>
            <main>
                <a href="#" class="y">All Deals</a>
                <!--a href="#">Featured</a>
                <a href="#">New</a>
                <a href="#">Advanced</a-->
            </main>
            <!--div>
                <a href="#">Filters</a>
                <hr>
                <a href="#">Export</a>
            </div-->
        </nav>
        <main>
            <div id="container"><?
            
                    $conn = new mysqli(getenv('DATABASE_HOST'), getenv('DATABASE_USER'), getenv('DATABASE_PASS'), getenv('DATABASE_NAME'));
                    $stmt = $conn->prepare("
                        SELECT o.id,o.pid,o.qty,o.commited,i.name,o.retailvalue,o.price,i.brand,o.expiration,i.spec,i.upc,o.links,i.link
                        FROM `order` o INNER JOIN `item` i ON o.pid = i.id 
                        WHERE status = 1 AND o.expiration > NOW() ORDER BY o.created DESC
                    ");
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if($result->num_rows > 0){
                        while ($row = $result->fetch_assoc()){
                            $percent = round($row['commited']/$row['qty']*100,2);
                                                            
              ?><div style="--deal-p:<?=$percent?>%">
                    <div class="above">
                        <div class="img">
                            <img title="Preview" src="assets/images/<?=$row['pid']?>.webp">
                        </div>
                        <div class="info">
                            <div class="float">
                                <p>$<?=$row['price']-$row['retailvalue']?> - <?=round(($row['price']/$row['retailvalue'])*100-100,2)?>%</p>
                                <span>Exp: <?=(new DateTime($row['expiration']))->format('n/j/y')?></span>
                                <div class="banner n"><p>NEW</p></div>
                            </div>
                            <div class="item">
                                <p><?=$row['name']?></p>
                                <p><?=$row['color'].' '.$row['spec']?></p>
                            </div>
                        </div>
                    </div>
                    <div class="below">
                        <div class="links"><?
                        if($row['links']){
                            $linksAndIndex = explode('-', $row['links']);
                            $links = explode(' ', $linksAndIndex[0]);
                            
                            foreach(explode(' ', $linksAndIndex[1]) as $index) array_push($links, explode(' ', $row['link'])[$index]);
                        }else $links = explode(' ', $row['link']);
                        
                        foreach ($links as $link){
                            $link = "https://".$link;
                            if(strpos($link,'bb')){echo'<a title="Best Buy" target="_blank" href="https://bestbuy.com/site/'.substr($link,10).'.p"><svg fill=#fff style="background:#0046be"><path d="m5.3 8.7v6.4h3.2c1.2 0 2.4-.5 2.4-1.8 0-1-.7-1.4-1.3-1.6.4-.2.9-.5.9-1.3 0-1-1-1.7-2.3-1.7zm1.9 1.5h.8c.2 0 .5.2.5.5 0 .2-.3.4-.5.4h-.8zm0 2.3h1c.3 0 .6.2.6.5 0 .3-.3.6-.7.6h-.9"/><path d="m6.9 15.7v6.3h3.1c1.3 0 2.4-.4 2.4-1.8 0-.9-.6-1.3-1.3-1.5.4-.2.9-.5.9-1.3 0-1-.9-1.7-2.2-1.7zm1.9 1.5h.7c.3 0 .5.2.5.5 0 .2-.2.4-.5.4h-.7zm0 2.3h1c.3 0 .6.2.6.5 0 .3-.3.6-.7.6h-.9"/><path d="m11 15v-6.3h4.9v1.6h-2.9v.8h2.4v1.4h-2.4v1h2.9v1.5"/><path d="m18.6 15.2c1.3 0 2.4-.8 2.4-2.1 0-2.1-2.6-1.8-2.6-2.5 0-.2.2-.4.5-.4.4 0 .8.3.8.3l1.1-1.1c-.4-.5-1.2-.8-2.1-.8-1.5 0-2.5.9-2.5 2 0 2.1 2.7 1.8 2.7 2.5 0 .2-.3.5-.7.5-.4 0-.8-.3-1-.6l-1.2 1.2c.5.5 1.2 1 2.6 1"/><path d="m22.5 15v-4.7h-1.6v-1.6h5.2v1.6h-1.6v4.7"/><path d="m12.5 15.7h1.9v3.8c0 .4.4.7.8.7.4 0 .7-.3.7-.7v-3.8h2v3.8c0 1.4-1.2 2.6-2.8 2.6-1.5 0-2.6-1.3-2.6-2.7z"/><path d="m20.2 22v-2.2l-2.2-4.1h1.9l1.3 2.2 1.3-2.2h2l-2.3 4.1v2.2"/></svg></a>';}
                            elseif(strpos($link,'am')){echo'<a title="Amazon" target="_blank" href="https://amzn.com/dp/'.substr($link,10).'"><svg fill=#f90 style="background:#232e3e"><path d="m22.7 15.5c-8.6 4-13.8.7-17.2-1.5-.2-.2-.7 0-.3.5 1.2 1.3 4.9 4.6 9.6 4.6 4.9 0 7.8-2.7 8.1-3.1.4-.5.1-.7-.2-.6zm2.4-1.4c-.3-.4-1.5-.4-2.2-.3-.7.1-1.9.6-1.8.8.1.1.2 0 .7 0 .6 0 1.9-.1 2.3.2.2.5-.6 2.2-.6 2.5-.1.4 0 .5.2.2.3-.2.7-.7 1-1.4.4-.7.5-1.7.4-2z"/></svg></a>';}
                            elseif(strpos($link,'samsclub.com')){echo'<a title="Samsclub" target="_blank" href="'.$link.'"><svg fill=#fff style="background:#0067a0"><path d="m14.6 7.8c.4.4.4 1.1 0 1.5l-6 6 6 6.1a1.1 1.1 0 010 1.5l-1.4 1.4-8.1-8.2a1 1 0 010-1.5l8.1-8.2m12.3 8.2a1 1 0 010 1.4l-8.3 8.3-1.4-1.5a1 1 0 010-1.3l6.1-6.2-6-6a1.1 1.1 0 010-1.5l1.3-1.4"></svg></a>';}
                            elseif(strpos($link,'bjs.com')){echo'<a title="BJs" target="_blank" href="'.$link.'"><svg style="background:#d21243"><path d="m17 17.7c0 1.1.1 4.6-3.3 4.6-.6 0-1.4-.1-2-.4v-2.7c.3.4.9.8 1.4.7 1.3.1 1.2-1.3 1.2-2.2v-9.2h2.7v9.2m2.1-9.4v2.5h1.5c0 .5-.8 1.1-1.5 1.3v1c1.1-.2 2.4-1.1 2.5-2.3v-2.6l-2.5.1m-8.1 4c0-1.1-.1-2.3-1.1-3-.8-.8-1.7-.8-2.8-.8h-3.7v13.6h3.2c2.5 0 4.5-1.1 4.5-3.8 0-2.3-1-2.8-2.1-3.4 1.3-.2 2.1-2.1 2-2.6m14.8 3.2c-.4-.3-.7-.6-1.2-.5-.4-.1-.7.2-.7.6.1 1 2.8.8 2.8 3.4 0 1.7-1 3.1-2.8 3-.9.1-1.9-.3-2.6-.9l1.1-1.8c.3.4.7.6 1.2.6.4 0 .7-.3.7-.7 0-1.2-2.8-.9-2.8-3.6 0-1.7 1.4-2.6 2.9-2.6.9 0 1.6.2 2.3.8"><path fill=#d21243 d="m6.3 19.8c1.1.1 1.7-.7 1.7-1.8 0-1.1-.6-1.9-1.7-1.8h-.2v3.6h.2zm0-5.7c1 0 1.6-.7 1.6-1.7 0-1-.6-1.7-1.6-1.6h-.2v3.3h.2z"></svg></a>';}
                            elseif(strpos($link,'ebay.com')){echo'<a title="Ebay" target="_blank" href="'.$link.'"><svg><path fill=#e53238 d="m4.8 12.2c-1.9 0-3.5.8-3.5 3.2 0 1.9 1.1 3.2 3.5 3.2 3 0 3.1-2 3.1-2h-1.4s-.3 1.1-1.7 1.1c-1.2 0-2.1-.8-2.1-2h5.4v-.7c0-1.1-.7-2.8-3.3-2.8zm-.1.9c1.2 0 2 .7 2 1.7h-4c0-1.1 1-1.7 2-1.7z"><path fill=#0064d2 d="m8.1 9.8v7.5c0 .5 0 1.1 0 1.1h1.3s.1-.5.1-.9c0 0 .6 1.1 2.4 1.1 1.9 0 3.2-1.3 3.2-3.2 0-1.8-1.2-3.2-3.2-3.2-1.9 0-2.4 1-2.4 1v-3.4h-1.4zm3.5 3.3c1.3 0 2.1 1 2.1 2.3 0 1.3-1 2.2-2.1 2.2-1.4 0-2.1-1-2.1-2.2 0-1.1.6-2.3 2.1-2.3z"><path fill=#f5af02 d="m18.5 12.2c-2.9 0-3.1 1.5-3.1 1.8h1.4s.1-.9 1.6-.9c.9 0 1.6.4 1.6 1.2v.3h-1.6c-2.3 0-3.4.7-3.4 2 0 1.3 1.1 2 2.5 2 2 0 2.7-1.1 2.7-1.1 0 .4 0 .9 0 .9h1.3s-.1-.6-.1-.9v-3c0-1.9-1.6-2.3-2.9-2.3zm1.5 3.3v.4c0 .5-.3 1.8-2.1 1.8-1.1 0-1.5-.5-1.5-1.1 0-1.1 1.5-1.1 3.6-1.1z"><path fill=#86b817 d="m20.6 12.4h1.6l2.3 4.6 2.3-4.6h1.5l-4.2 8.2h-1.5l1.2-2.3-3.2-5.9z"></svg></a>';}
                            elseif(strpos($link,'canon.com')){echo'<a title="Canon" target="_blank" href="'.$link.'"><svg fill="#fff" style="background:#cc2229"><path d="m3.5 16c.4.7 1.3 1.2 2.2 1.2 1.2 0 2-1.2 2-1.2l0 .3c-.5.9-1.6 1.4-2.8 1.4-1.4 0-2.6-.7-3.2-1.7-.2-.4-.3-.7-.3-1.2 0-1.5 1.5-2.8 3.5-2.8 1.3 0 2.4.6 3 1.4l-2.3 1.4 1.3-2c-.3-.2-.8-.5-1.3-.5-1.3 0-2.4 1.2-2.4 2.5 0 .5.1.8.3 1.2m6.2 0a.9.9 90 00-.1.2c0 .5.5.8 1 .8.5 0 .9-.3.9-.8a.9.9 90 000-.2c-.2-.5-.5-.8-.9-.8-.5 0-.9.3-.9.8m3.3 0 .5 1.5h-1.6l-.3-1c-.3.6-.9 1-1.8 1-1 0-1.8-.6-1.8-1.4 0 0 0-.1 0-.1.1-.7.9-1.3 1.8-1.3.5 0 1 .2 1.4.4l-.5-1.4h-1.8l1.7-.6c1.3-.5 1.7.6 1.7.6m5.8 2.3v1.5h-1.6v-3.4a.5.5 90 00-.4-.4.5.5 90 00-.5.3v3.5h-1.6v-3.8h-.9s1.2-.7 1.7-.7c.4 0 .8.3.8.7.6-.3 1.2-.7 1.7-.7.5 0 .8.3.8.8m2.6 2.2.2.8c0 .3.3.5.6.5.3 0 .6-.3.6-.5 0-.1 0-.1 0-.1l-.9-3.1c0-.2-.3-.5-.6-.5-.3 0-.6.3-.6.6m3.3 2.3c-.3 1-1.3 1.7-2.3 1.7-1 0-1.9-.7-2.2-1.7a2.4 2.4 90 01-.1-.7c0-1.4 1-2.4 2.3-2.4 1.3 0 2.4 1 2.4 2.4 0 .2-.1.5-.1.7m2.7 0v1.5h-1.7v-3.8h-.8s1.3-.7 1.7-.7c.4 0 .8.3.8.7.6-.3 1.3-.7 1.7-.7.4 0 .8.3.8.8v3.7h-1.7v-3.4a.5.5 90 00-.4-.4.5.5 90 00-.4.3"/></svg></a>';}
                            elseif(strpos($link,'costco.com')){echo'<a title="Costco" target="_blank" href="'.$link.'"><svg fill=#fff style="background:#e31837"><path d="m11 7c2.6-1.3 5.4-2 8.3-2 2 0 4.1.1 6 .7-.3 2.6-.7 4.9-1.2 7.5-1.2-1.1-2.5-1.8-4-2-1.4-.1-2.6 0-3.6.7-.9.4-1.7 1-2.3 1.8-.4.6-.7 1.3-.6 2.1 0 .7.4 1.3.9 1.8.4.4 1 .9 1.8 1 .9.3 1.9.3 2.8.2 1.5-.3 3-.9 4.3-1.8-.7 2.4-1.3 4.9-1.7 7.3-.9.3-1.8.5-2.5.5-1.4.1-2.6.3-3.9.3-1.8 0-3.6-.3-5.4-.9-1.2-.6-2.6-1.4-3.5-2.4-.6-.6-1.2-1.5-1.5-2.4-.4-1.2-.6-2.7-.3-4.1.2-1 .6-2.2 1.2-3.3 1.2-2.1 3.2-3.7 5.3-4.9z"/></svg></a>';}
                            elseif(strpos($link,'abt.com')){echo'<a title="Abt" target="_blank" href="'.$link.'"><svg fill=#fff style="background:#007dc3"><path d="m12 8c-.6-1-2.1-1.1-2.3-1.1-.1 0-1.5-.2-2.5.7-1 1-2.1 4.2-4.5 10.6h2.8l.9-2.6 1.6.1-.7 2.5h2.8c.1 0 2.2-8 2.3-8.7.1-.7-.1-1.1-.4-1.5zm-3.2 5.4-1.6 0c.4-1.2 1.2-3.6 1.3-3.6v0c.2-.5.6-.5.8-.5.3.1.5.2.6.5 0 .4-1.1 3.6-1.1 3.6zm10.6-1.4c-.3-.8-1-1.1-1.3-1.1-1.1-.3-1.1-.2-1.9-.1-.5 0 .9-4.4.8-4.5-.2-.1-.7-.1-.9 0-.2 0-2.7 1.4-2.8 1.5 0 .1 0 .4.2.7.1.2.1.4.1.7-.1.2-1.1 3.6-2.5 8.7l2.7.1.5-.6s.3.4 1.3.5c.5 0 1.1.1 1.8-.3 2.3-1.2 2.4-4.6 2-5.6zm-3.2 2.5c-.5 1.4-.9 1.3-1 1.3-.1 0-.3 0-.5-.2-.2-.1 0-.7 0-.7s.5-1.7.7-2c.2-.3.4-.2.5-.2.2.1.8.2.3 1.8zm5-3.8-1.4-.1.3-1.1s.6-.2 1.2-.4c.1-.1.9-.5 1.4-1.2.9-1.3 1-1.6 1-1.6h1.2s-.9 2.8-.6 3c.1.1 1.1.1 1.1.1l-.2 1.2s-1.4 0-1.3 0c0 0-1.6 4.8-1.5 5.2.2.5.9.4.9.4s.1.1.1.2c0 .2-.1.5-.2.7-.1.5-.6.9-1.6.8-.9-.1-1.9-.3-1.8-1.4.1-.9 1.4-5.8 1.4-5.8zm-18.7 8.6c6.5-.2 11.5-.2 11.5-.2l-1 1 8.9-.4-1.1 1.4 5.5.6-9.5 1.9 1.2-1.6-10.9.4.9-1.5-5.5-1.6z"/></svg></a>';}
                            elseif(strpos($link,'lenovo.com')){echo'<a title="Lenovo" target="_blank" href="'.$link.'"><svg fill=#fff style="background:#e22219"><path d="m13 14.2c-.5 0-1 .2-1.3.6v-.6h-1.1v4h1.1v-2.3c0-.4.3-.9.9-.9.4 0 .9.4.9.9v2.2h1v-2.4c.1-.9-.6-1.5-1.5-1.5m9.2 0-.9 2.7-1-2.7h-1.2l1.5 3.9h1.2l1.6-3.9m-13.9 2.7c-.5.3-.7.4-1.1.4-.4 0-.7-.1-.9-.3l2.8-1.1c-.1-.5-.3-.9-.5-1.2-.4-.4-.9-.6-1.6-.6-1.2 0-2 .9-2 2.1 0 1.1.8 2 2.2 2 .6 0 1.4-.4 1.7-.8l-.6-.5m-2.1-1.5c.2-.2.5-.4.9-.4.3 0 .7.2.8.5l-1.9.8c0-.3 0-.7.2-.9m-1.4 1.8h-2.4v-4.2h-1.1v5.1h3.5m19.3.1c-1.2 0-2-.9-2-2 0-1.2.8-2 2-2 1.3 0 2.2.8 2.2 2 0 1.1-.9 2-2.2 2m0-3.2c-.5 0-1 .5-1 1.2 0 .5.5 1 1 1 .7 0 1.1-.4 1.1-1 0-.7-.4-1.2-1.1-1.2m-8.2 3.2c-1.2 0-2.1-.9-2.1-2 0-1.2.9-2 2.1-2 1.2 0 2.1.8 2.1 2s-.9 2-2.1 2m0-3.2c-.6 0-1 .5-1 1.2 0 .5.4 1 1 1 .6 0 1-.4 1-1 0-.7-.4-1.2-1-1.2"></svg></a>';}
                            elseif(strpos($link,'google.com')){echo'<a title="Google" target="_blank" href="'.$link.'"><svg><path fill=#4285F4 d="m25.6 15.3c0-.8-.1-1.6-.2-2.3h-10.4v4.3h5.9c-.2 1.3-1 2.5-2.2 3.3v2.7h3.6c2.1-1.9 3.3-4.7 3.3-8"/><path fill=#34A853 d="m15 26c3 0 5.5-1 7.3-2.7l-3.6-2.7c-1 .6-2.2 1-3.7 1-2.9 0-5.3-1.9-6.2-4.5h-3.6v2.8c1.8 3.6 5.5 6.1 9.8 6.1"/><path fill=#FBBC05 d="m8.8 17.1c-.2-.7-.3-1.4-.3-2.1s.1-1.4.3-2.1v-2.8h-3.6c-.8 1.5-1.2 3.1-1.2 4.9s.4 3.4 1.2 4.9l2.8-2.2.8-.6"/><path fill=#EA4335 d="m15 8.4c1.6 0 3.1.5 4.2 1.6l3.2-3.1c-2-1.8-4.4-2.9-7.4-2.9-4.3 0-8 2.5-9.8 6.1l3.6 2.8c.9-2.6 3.3-4.5 6.2-4.5"/></svg></a>';}
                            elseif(strpos($link,'samsung.com')){echo'<a title="Samsung" target="_blank" href="'.$link.'"><svg fill=#034ea2 style="background:#034ea2"><path fill=#fff d="m27.3 12.8c.3 1.9-5 4.5-11.9 5.7-6.9 1.2-12.7.6-13.1-1.4-.3-1.9 5-4.5 11.9-5.7 6.9-1.2 12.7-.6 13.1 1.4"/><path d="m7 15.5c0 .1 0 .2 0 .2 0 .1-.1.2-.2.2-.2 0-.3-.1-.3-.3l0-.2-.7 0 0 .2c0 .6.5.7 1 .7.4 0 .8-.1.9-.6 0-.2 0-.3 0-.4-.1-.5-1.1-.7-1.2-1 0 0 0-.1 0-.1 0-.1.1-.2.2-.2.2 0 .3.1.3.2 0 .1 0 .2 0 .2l.6 0 0-.2c0-.5-.5-.6-.8-.6-.5 0-.9.1-.9.5-.1.2-.1.3 0 .4.1.5 1 .7 1.1 1"/><path d="m9.2 13.9-.4 2.3-.7 0 .5-2.5 1.1 0 .5 2.5-.7 0-.3-2.3"/><path d="m11.8 16.3-.4-2.5 0 0 0 2.5-.7 0 .1-2.6 1 0 .4 1.8 0 0 .4-1.8 1 0 .1 2.6-.6 0-.1-2.5 0 0-.4 2.5"/><path d="m15.4 15.5c.1.1 0 .1 0 .2 0 .1 0 .2-.2.2-.2 0-.3-.1-.3-.3l0-.2-.7 0 0 .2c0 .6.5.7 1 .7.4 0 .8-.1.9-.6 0-.2 0-.3 0-.4-.1-.5-1.1-.7-1.2-1 0 0 0-.1 0-.1 0-.1.1-.2.3-.2.1 0 .2.1.2.3 0 0 0 .1 0 .1l.6 0 0-.2c0-.5-.5-.6-.8-.6-.5 0-.9.1-.9.6 0 .1-.1.2 0 .3.1.5 1 .7 1.1 1"/><path d="m17.6 15.9c.2 0 .2-.2.2-.2.1-.1.1-.1.1-.1l0-1.9.6 0 0 1.8c0 .1 0 .2 0 .2 0 .5-.4.6-.9.6-.5 0-.9-.1-.9-.6 0 0 0-.1 0-.2l0-1.8.6 0 0 1.9c0 0 0 0 .1.1 0 0 0 .2.2.2"/><path d="m20.8 15.7 0-2 .6 0 0 2.5-.9 0-.7-2.1 0 0 .1 2.1-.7 0 0-2.5 1 0 .6 2"/><path d="m23 15.8c.2 0 .2-.1.3-.2 0 0 0 0 0-.1l0-.3-.3 0 0-.4.9 0 0 .7c0 0 0 .1 0 .1 0 .5-.4.7-.9.7-.5 0-.9-.2-.9-.7 0 0 0-.1 0-.1l0-1.1c0-.1 0-.1 0-.2 0-.5.4-.6.9-.6.5 0 .9.1.9.6 0 .1 0 .2 0 .2l0 .1-.6 0 0-.2c0 0 0 0-.1-.1 0 0 0-.2-.2-.2-.2 0-.3.2-.3.2 0 .1 0 .1 0 .2l0 1.1c0 .1 0 .1 0 .1 0 .1.1.2.3.2z"/></svg></a>';}
                            elseif(strpos($link,'hp.com')){echo'<a title="HP" target="_blank" href="'.$link.'"><svg fill=#fff style="background:#015294"><path d="m15 4.2-2.4 6.6 1.6 0c1.3 0 1.7.9 1.4 1.9l-2.5 6.6h-2.3l2.6-7.3h-1.2l-2.6 7.3h-2.4l5.3-14.9c-4.9 1.1-8.5 5.5-8.5 10.8 0 5.1 3.5 9.5 8.3 10.7l5.3-15.1h3.9c.9 0 1.9.5 1.4 1.8l-2.2 6.2c-.2.7-.9.8-1.4.8h-2.4l-2.3 6.5c.1.1.2.1.4.1 6.1 0 11-4.9 11-11 0-6.1-4.9-11-11-11zm4.6 7.8-2.3 6.4h1.2l2.3-6.4z"/></svg></a>';}
                            elseif(strpos($link,'verizon.com')){echo'<a title="Verizon" target="_blank" href="'.$link.'"><svg fill=#D52B1E style="background:#000"><polygon points="20.82125,5.96 13.5515,21.512 10.81775,15.61925 7.86125,15.61925 12.35675,25.25825 14.74625,25.25825 23.7575,5.96"/></svg></a>';}
                            elseif(strpos($link,'bhphotovideo.com')){echo'<a title="Bhphotoideo" target="_blank" href="'.$link.'"><svg fill=#be291a style="background:#be291a"><path fill=#fff d="m21.2 9.3 0 4.6h-2.9l0-4.6h-4v4.5l-.5 0c0 0 0-.6 0-1.1 0-2.2-1.5-3.4-3.6-3.4h-6v4.6 2 5.5h12.7l.1 0h1.2l.1 0v-4h2.9v4h4v-12.1h-4zm-13.3 2.9h1.4v0c.5 0 .9.4.9.8 0 .5-.4.9-.9.9v0h-1.4v-1.7zm1.9 6 0 0-1.9 0v-1.7h1.9v0c.5 0 .9.4.9.9 0 .4-.4.8-.9.8"/><path d="m16.7 19.6c.2-.3.3-.7.4-1.1l0-.1c.1-.4.2-.8.2-.9v-.1h-1.1l0 .1c-.1.3-.1.5-.1.7-.1.1-.1.3-.2.5l-1.5-1.7c.6-.4 1.4-1 1.4-2.1l0-.1h0c-.1-1-.9-1.8-2-1.8-1 0-1.9.8-1.9 1.9v0c0 0 0 0 0 0 0 .8.3 1.1.9 1.8-.2.2-.6.4-.8.7-.5.4-.8 1-.9 1.7 0 .6.3 1.3.7 1.8.5.4 1.2.7 2 .7 0 0 0 0 0 0l0 0c.8 0 1.5-.2 2-.8.1 0 .2-.1.3-.2l.8.8h1.4l-1.6-1.8zm-3-3.4c-.3-.3-.8-.8-.8-1.3 0-.5.4-.9.9-.9.5 0 .9.4.9.9 0 .6-.6 1-1 1.3zm-1.5 2.7 0 0c0 0 0-.1 0-.1.1-.1.1-.2.2-.4 0 0 0 0 .1 0 0-.1 0-.1 0-.1l0 0c.1-.1.1-.1.1-.1 0 0 .1-.1.1-.1.3-.2.7-.5.8-.6l1.9 2.2c-.1.2-.2.3-.3.4-.4.3-.8.5-1.3.5-.5 0-.9-.2-1.2-.4-.2-.3-.4-.6-.4-.9 0-.2 0-.3 0-.4z"/></svg></a>';}
                            elseif(strpos($link,'lowes.com')){echo'<a title="Lowes" target="_blank" href="'.$link.'"><svg fill=#004990 style="background:#004990"><path fill=#fff d="m15 9-6.4 2.8-4 0 0 1.1-2 0 0 7.6 24.9 0 0-7.6-2 0 0-1.1-4 0-6.5-2.8z"/><path d="m21 13.8 0 1.2.8 0 .2-1.2"/><path d="m6.5 18 0-4.2-1.1 0 0 5.2 2.9 0 0-1"/><path d="m18 19 2.8 0 0-1-1.6 0 0-.7 1.6 0 0-.9-1.6 0 0-.7 1.6 0 0-1-2.8 0"/><path d="m10.9 15.7-1 0 0 2.3 1 0 0-2.3m1.2 2.7c0 .3-.3.6-.6.6l-2.2 0c-.3 0-.5-.3-.5-.6l0-3.1c0-.3.2-.6.5-.6l2.2 0c.3 0 .6.3.6.6"/><path d="m16.2 14.7 0 3.3-.7 0 0-3.3-1 0 0 3.3-.6 0 0-3.3-1.2 0 0 3.7c0 .3.3.6.6.6l1.3 0c.2 0 .4-.2.4-.4 0 .2.3.4.5.4l1.3 0c.3 0 .6-.3.6-.6l0-3.7"/><path d="m24.8 17.4 0 0 0-.1-.1 0c-.1-.4-.6-.7-1.2-.9l0 0c-.2-.1-.5-.2-.6-.5 0-.1 0-.2.1-.2.1-.1.2-.2.4-.2.2 0 .5.2.7.2l.7.3 0-.9-.1 0c0 0-.6-.4-1.2-.5-.8 0-1.3.2-1.5.6-.3.3-.3.9-.1 1.3.3.4.7.6 1.1.8l.5.3c.2.1.3.2.2.4 0 .1-.2.2-.4.2-.5 0-1.4-.4-1.4-.4l-.1 0 0 1.1.1 0c0 0 .8.2 1.6.2.4 0 .7 0 .9-.2 0 0 .5-.4.5-1"/></svg></a>';}
                            elseif(strpos($link,'homedepot.com')){echo'<a title="Home Depot" target="_blank" href="'.$link.'"><svg fill=#fff style="background:#F96302"><path d="m5.6 30 .2-.5-5.3-5.2-.5 0v-1l2.6-2.3.1 1.2 5.1 5.1 1.1 0-2.4 2.7m8.6 0 0-.1-2.8-3.1-.4.1-.1-.2 1.3-1.4 3.7 3.7-.5.5-.7.5-.5 0m1.8-1.4 0-.7-2.8-2.8-.6 0-.2-.1.9-.5.7-.2.9.3 1 .6.6.8.3 1-.1.8-.5.9m-8-1.5-.1-1.2-2.3-2.3-.8.8-.5-.5.8-.8-2.2-2.2-1.2-.2 3.1-3.1.1 1.1 5.2 5.1 1 0 0 .6m6.4 2.4 0 0 0-.6-3-2.8-.5 0-.1-.3 1.5-1.4 3.7 3.7m.2-.4.1-2 1 1.1-1.1 1.1m1.3-1.6 0 0-.2 0 .2-.4-3-2.9-.5 0 0-.3 1.3-1.3 3.3 3.4.4-.3.1 0 0 .5m-3.7 1.2-.9-.1-.4-1.3.3 0 1.3 1.3-.1.1m-7.4-1.2-3.5-2-1.1-1.7-.2-2 .7-1.8.4 0 .2.9 5.1 5.1.9.1.5.4-1.6 1m3.1-.7-.1-.2 1.1-1 .9 1.2-1.7.1m-1.6-1-.1-1-6.4-5.6 1-.7.6-.3 2 .1 3.2 2.2 1 3.6-.9 1.9m6-.4-.1-.1 0-.6-1.2-1.2-.5 0 0 .1-.3-.2.1-.3 1.1-.6.9.2.4.2.1.1-.2-.9 0-1 .4-.8.1 0 .2.4-.1.1 0 .3 2.7 2.7.3.1.5 0 .1.1 0 .2-.5.2-.9.3-.8-.1-.7-.4-.7-.6.1.7-.2.6-.5.7m4.5-1.8-.2-.2 0-.5-2.9-2.8-.4 0-.3-.2 1-.6 1.1.2-.7-.8.7-.8.8 1.7-.2.1.7.4.6.8.4 1 0 .8-.5 1m-8.6-.9-3.5-4.6 5.4 2.8-1.9 1.8m10.4-1.3 0-.5-3.3-3.3 1.1-1.2 3.3 3.4.3-.2 0 .7-1.4 1.3m-11.1-3.5-4.1-1.8-1.5.2 0-.6 6.8-6.5.3 1 5 5 1.1.3-2.7 2.9-.5-.8-4.3-4.5 3.3 6.8m-14.2-.6-3.1-3.7 1.2-1 3.5 3m-5-.5-.6-.5.8-.7.6 1.6-.3.1m5.3-.7 0-.6-2.7-2.8-.5 0 0-.1 1.5-1.4.2 0 0 .5 2.8 2.7.4.1-1.7 1.6m19.1-.2-1.5-.7.7-.7 1.2 1.2 0 0 0 .2m-17.7-1.6 0-.3-1.2-1.5-.5.5-.2-.4.5-.4-1.3-1.2-.3 0-.2-.3 1.4-1.3.4.1-.3.1 0 .3 2.8 2.7.4-.1.2.2m12.9 1.6 0-.9-5.3-5.4-1 0 2.5-2.8 6.5 6.6-2.7 2.5m-19.1 0-1.4-.7.6-.6 1.1 1m6-1.5 0-.5-2.7-2.7-.5 0 1.1-1.6 1.8 1.8 1.8 1.8m14.4-.1-.4-.3.4-.5.2-.9-.1-.9-.3-.7 0-.3.4-.1 1.2 1.1v1.4l-.6.7-.7.7m-14.3-.5-.2-.2.2-1.1-.2-.5.3-.1.9.9m-1.7-.4-.9-.1-.2-.3-.2-.7.2-.4m14.6.5-1.2-.4-.4.1-.5-.5 0-.7-.2-1.2.2-.1 2.6 2.5-.4.3m-16.3-.9 1-1.1 1 1m9.4-2.5-.2-.2 1.1-1.3h1.4l.6.5.5.6 0 .6-.4 0-.8-.4-1 0-.9.5"/></svg></a>';}
                            elseif(strpos($link,'dell.com')){echo'<a title="Dell" target="_blank" href="'.$link.'"><svg fill=#fff style="background:#007db8"><path d="m8.3 15c0-.9-.63-1.35-1.35-1.35l-.45 0 0 2.7.45 0c.72 0 1.35-.45 1.35-1.35m8.73.9-3.6 2.88-3.33-2.52c-.45 1.08-1.62 1.89-2.97 1.89l-2.79 0 0-6.3 2.79 0c1.44 0 2.52.9 2.97 1.98l3.33-2.61 1.17.99-3.06 2.34.63.45 3.06-2.34 1.26.9-3.06 2.43.54.45 3.06-2.34 0-2.25 2.16 0 0 4.5 2.16 0 0 1.8-4.32 0 0-2.25zm7.11.36 2.25 0 0 1.89-4.41 0 0-6.3 2.16 0 0 4.5z"/></svg></a>';}
                            elseif(strpos($link,'apple.com')){echo'<a title="Apple" target="_blank" href="'.$link.'"><svg fill=#fff style="background:#161616"><path d="m22.2 11.6a4.374 4.374 90 00-2.187 3.645 4.2525 4.2525 90 002.673 3.888 10.206 10.206 90 01-1.3365 2.7945c-.8505 1.215-1.701 2.43-3.0375 2.43s-1.701-.8505-3.159-.8505c-1.458 0-2.0655.8505-3.159.8505s-2.0655-1.0935-3.0375-2.43a11.907 11.907 90 01-2.0655-6.4395c0-3.7665 2.43-5.832 4.86-5.832 1.3365 0 2.3085.8505 3.159.8505.729 0 1.944-.8505 3.402-.8505a4.617 4.617 90 013.888 1.944m-4.617-3.5235a4.374 4.374 90 001.0935-2.673 1.8225 1.8225 90 000-.3645 4.374 4.374 90 00-2.916 1.458 4.2525 4.2525 90 00-1.0935 2.5515 1.701 1.701 90 00.1215.3645 1.458 1.458 90 00.243 0 3.7665 3.7665 90 002.5515-1.3365"></svg></a>';}
                            elseif(strpos($link,'wm')){echo'<a title="Walmart" target="_blank" href="https://walmart.com/ip/'.substr($link,10).'"><svg fill=#fdbb30 style="background:#1d76d3"><path d="m11.8 13.1c.2-.4.2-.9 0-1.1l-4.7-3.3c-.5-.3-1.3 0-1.7.7-.4.8-.3 1.6.2 1.8l5.2 2.5c.3.1.7-.1 1-.6"/><path d="m11.8 16.7c.2.4.2.9 0 1.1l-4.7 3.3c-.5.3-1.3 0-1.7-.7-.4-.8-.3-1.6.2-1.8l5.2-2.5c.3-.1.7.1 1 .6"/><path d="m18 13.1c.2.5.7.7 1 .6l5.2-2.5c.5-.2.5-1 .1-1.8-.4-.7-1.1-1-1.6-.7l-4.7 3.3c-.3.2-.3.7 0 1.1"/><path d="m18 16.7c.2-.5.7-.7 1-.6l5.2 2.5c.5.2.5 1 .1 1.8-.4.7-1.1 1-1.6.7l-4.7-3.3c-.3-.2-.3-.7 0-1.1"/><path d="m14.9 18.5c.5 0 .9.2 1 .6l.5 5.7c0 .6-.7 1-1.5 1-.9 0-1.5-.4-1.5-1l.5-5.7c0-.4.5-.6 1-.6"/><path d="m14.9 11.3c.5 0 .9-.2 1-.6l.5-5.7c0-.6-.7-1-1.5-1-.9 0-1.5.4-1.5 1l.5 5.7c0 .4.5.6 1 .6"/></svg></a>';}
                            elseif(strpos($link,'tg')){echo'<a title="Target" target="_blank" href="https://target.com/p/-/A-'.substr($link,10).'"><svg fill=#E50024 style="background:#E50024"><path stroke="#fff" stroke-width="3.5" d="m14.7 6.1a9 9 90 10.144 0zm.144 7.2a1.8 1.8 90 11-.01 0"/></svg></a>';}
                            elseif(strpos($link,'woot.com')){echo'<a title="Woot" target="_blank" href="'.$link.'"><svg fill=#fff style="background:#669510"><path d="m26.5 10.5c-.3 0-.4.1-.5.2-.5 1.3 0 4.9.6 4.9.8.1 1.4-2.5 1.5-4.5 0-.4-.1-.6-.4-.6 0 0-.9 0-1.1 0 0 0-.1 0-.2 0zm-3.1.8c-.3 0-1 .1-1.3.1-.2.1-.2.1-.3.2 0 .1-.1 1.2-.1 1.5 0 0-.4 0-.5 0-.1 0-.2 0-.2.6 0 .5 0 .6.2.6.1 0 .4-.1.5-.1-.1.5-.1 1-.1 1.4 0 .8.2 1.9 1.7 1.9 1.7 0 1.7-.7 1.9-1 0-.1-.6-.6-.7-.6 0 0-.3.6-.6.6-.3 0-.5-.5-.5-1 0-.5 0-.9 0-1.4.6 0 .9 0 1.2 0 .2 0 .2 0 .3-.3l0-.4c0-.3-.1-.4-.2-.4-.2 0-.4 0-.6 0-.1 0-.4 0-.6.1 0-.4 0-.6 0-.8.1-.1.1-.7.1-.8 0-.2 0-.2-.2-.2zm-5 1.6c-1.5 0-3 .7-3 2.4 0 1.1.8 2.3 2.7 2.3 2.1 0 2.8-1.4 2.8-2.5 0-1.4-1-2.2-2.5-2.2zm-14.6.1c-.1 0-1.1.2-1.2.2-.2.1-.3.1-.3.2 0 .2 1.1 2.5 1.7 3.5.4.6.5.7.7.7.2 0 .3-.1.6-.5.4-.5.9-1.3 1-1.6l.6 1.5c.2.3.3.6.6.6.3 0 .5-.2.7-.5.2-.3.6-.9 1-1.6.3-.5 1.1-1.9 1.1-2.2 0-.1-.1-.1-.3-.1-.1-.1-.7-.2-.8-.2-.2 0-.3 0-.4.4 0 .1-.6 2-.9 2-.3 0-.7-2.1-.8-2.1-.1-.3-.1-.3-.2-.3-.1 0-.9.2-1.2.2-.1 0-.3.1-.3.2 0 .1.4.9.4.9 0 0-.5 1.1-.7 1.1-.2 0-.8-1.9-.9-2-.1-.3-.2-.4-.4-.4zm9 .1c-1.5 0-3 .7-3 2.3 0 1.1.8 2.3 2.7 2.3 2.1 0 2.8-1.4 2.8-2.5 0-1.4-1-2.1-2.5-2.1zm5.7 1.2c.8 0 1 .6 1 .8 0 .5-.7 1-1.3 1-.6 0-1.1-.3-1.1-.8 0-.7.8-1 1.4-1zm-5.6.2c.8 0 1 .5 1 .8 0 .5-.7 1-1.3 1-.6 0-1.1-.4-1.1-.9 0-.6.8-.9 1.4-.9zm13.8 1.5c-.5 0-1.3.3-1.3.9 0 .4.5.7 1 .7.6 0 1.2-.4 1.2-.9 0-.2-.2-.7-.9-.7z"/></svg></a>';}
                            else{echo'here';}
                        }
                      ?></div>
                        <form onsubmit="commit(this,event)">
                            <input type="hidden" name="id" value="<?=enc($row['id'])?>">
                            <input type="number" name="qty" placeholder="QTY" min=1 max=10000 required>
                            <p>$<?=rtrim(rtrim($row['price'], '0'), '.')?></p>
                            <button type="submit">COMMIT</button>
                        </form>
                    </div>
                </div><? } } $conn->close();
          ?></div>
        </main>
    </body>
    <script>
        let tid; 
        let toast = document.querySelector("#toast");
            
        async function commit(a,e) {
            e.preventDefault();
            
            const b = await fetch("#", {method:'POST', body: new FormData(a)});
            const t = await b.json();
            
            if(!toast.classList.contains("show")){
                clearTimeout(tid);
                toast.textContent=t
                toast.classList = "show green";
                tid = setTimeout(() => {toast.classList.remove("show")}, 10000);
            }
        }
    </script>
</html>