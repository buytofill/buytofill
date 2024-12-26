<?
    require 'assets/control.php';

    if($_SESSION['role'] != "staff"){
        header('Location: deals');
        exit;
    }

    if($_SERVER["REQUEST_METHOD"] == "GET"){
        if(isset($_GET['download'])){
            $conn = new mysqli(getenv('DATABASE_HOST'), getenv('DATABASE_USER'), getenv('DATABASE_PASS'), getenv('DATABASE_NAME'));
            $result = $conn->query("
                SELECT f.id AS fid, SUM(c.arrived * o.price) AS owed, 
                    MAX(f.AHN) AS AHN, 
                    MAX(f.BAN) AS BAN, 
                    MAX(f.RN) AS RN, 
                    MAX(f.BAT) AS BAT FROM `commit` c INNER JOIN `filler` f ON c.uid = f.id INNER JOIN `order` o ON o.id = c.oid WHERE c.status = 1 GROUP BY f.id");

            $filename = "payout_" . date('Y-m-d') . ".csv";
            ob_clean();
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="'.$filename.'"');
            header('Pragma: no-cache');
            $output = fopen('php://output','w');

            while ($row = $result->fetch_assoc()){
                if($row['BAT'] == 0){ $BAT = "CCD"; }
                else{ $BAT = "PPD"; }
                $test = [
                    $row['RN'],
                    $row['BAN'],
                    $BAT,
                    $row['AHN'],
                    N2A($row['fid']),
                    $row['owed']
                ];
                fputcsv($output, $test);
            }

            fclose($output);
            $conn->close();
            exit;
        }
        $currentPage = 1;
        $max = 17;
        if(isset($_GET['p'])) $currentPage = $_GET['p'];
        $offset = ($currentPage - 1) * $max;
        
        if(isset($_GET['id'])){
            $id = $_GET['id'];

            $conn = new mysqli(getenv('DATABASE_HOST'), getenv('DATABASE_USER'), getenv('DATABASE_PASS'), getenv('DATABASE_NAME'));
            $stmt = $conn->prepare("
                UPDATE commit 
                SET paid = paid + arrived, arrived = 0, status = 0
                WHERE id = ?
            ");
            $stmt->bind_param("i", $id);
            
            if($stmt->execute()) header('Location: po?p='.$currentPage);
            else echo "Error updating record: " . $stmt->error;
            
            $stmt->close();
            $conn->close();
            exit;
        }
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
            #container{height:calc(100% - 35px)}
            #container>div{color:#fff;display:flex;background:#212121;border-radius:.2rem;height:calc(100% / <?=$max?> - 3px)}
            #container>div:first-child{border-top-right-radius:.4rem;border-top-left-radius:.4rem}
            #container>div:last-child{border-bottom-right-radius:.4rem;border-bottom-left-radius:.4rem}
            #container>div:not(:last-child){margin-bottom:3px}
            #container>div>div:last-child,#container>div>div>div:last-child,#container .main{border-radius:.4rem;display:inherit;width:100%;border-top-right-radius:.2rem;border-bottom-right-radius:.2rem}
            #container>div:first-child>div>div,#container>div:first-child>div,#container>div:first-child .main{border-top-right-radius:.4rem}
            #container>div:last-child>div>div,#container>div:last-child>div,#container>div:last-child .main,#container>div:last-child .main a{border-bottom-right-radius:.4rem}
            #container>div>div:first-child,#container .long,#container>div>div>div>div:first-child{font-weight:400;padding:0 .5rem;font-size:.9rem;min-width:95px;width:95px;align-content:center;text-wrap:nowrap}
            #container>div>div:first-child{color:#606060}
            #container>div>div>div{color:#808080}
            #container .long{width:100%;transition:width .3s ease;text-align:left;background:transparent;user-select:text;cursor:auto;overflow-x:auto;color:#808080}
            #container>div>div>div>div:first-child{color:#999}
            #container>div>div:last-child{background:#252525}
            #container>div>div>div:last-child{background:#292929}
            #container>div>div>div>div:last-child{background:#2d2d2d}
            
            ::-webkit-scrollbar{display:none}
            
            #container>div:first-child{height:35px}
            #container>div:first-child>div:first-child,#container>div:first-child>div>div:first-child,#container>div:first-child>div>div>div:first-child{font-weight:600;font-size:.8rem;min-width:95px;width:95px;align-content:center;text-align:center}
        
            #container .main a{cursor:pointer;align-content:center;font-size:.9rem;width:100px;text-align:center;text-wrap:nowrap;position:relative;display:block;background:var(--grn);margin-left:auto;padding:0 .8rem;color:#000;font-weight:600;border-top-right-radius:.2rem;border-bottom-right-radius:.2rem}
            #container .main a.p{background:var(--purp);cursor:auto;color:#fff}
            #container .main .trk{width:160px}
            #container .main .trk .bg{height:35px;transition:top 1s ease;position:absolute}
            #container .main .trk:not(:has(.mt)):hover .bg{height:calc(100% - 2rem - 35px - 3px);background:#2d2d2d;top:calc(1rem + 35px + 3px);border-radius:.4rem;border:1px solid #666;width:160px;z-index:100}
            #container .main .trk:not(:has(.mt)):hover:before{content: '';position:absolute;background:#21212169;z-index:101;width:160px;transform:translateY(-.5px);pointer-events:none;height:36.5px}
            #container .main .trk .text{overflow:hidden;height:35px}
            
            #container .main .trk:not(:has(.mt)):hover .text{z-index:102;height:calc(100% - 2rem - 35px - 3px);position:absolute;top:calc(1rem + 35px + 3px);width:160px}
            #container .main .text span{display:block;height:35.5px;align-content:center;padding:0 .4rem}
            #container .main .text span:not(:last-child){margin-bottom:3px}
            #container .main{color:#aaa}
            #container .main .hd{font-size:.8rem;font-weight:600;width:160px;text-align:center;align-content:center}
            #container .main .itamt, #container .main .total{width:100px;align-content:center;padding:0 .4rem}
            #container div:first-child .main .itamt, #container div:first-child .main .total{text-align:center;font-size:.8rem;font-weight:600}
            #container .ittt{width:calc(100% + 1rem) !important}
        </style>
    </head>
    <body>
        <?require 'assets/header.php'?>
        <nav>
            <main>
                <a href="#" class="y">Checked In</a>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none">
                    <path d="m19 18.5 4.5 4.575m-3-8.25c0 2.925-2.325 5.25-5.25 5.25-2.925 0-5.25-2.325-5.25-5.25 0-2.925 2.325-5.25 5.25-5.25 2.925 0 5.25 2.325 5.25 5.25" stroke-linecap="round" stroke="#fff" stroke-width=1.5 stroke-linejoin="round"/>
                </svg>
                <form>
                    <input type="text" placeholder="Search using item name, trackings, UID, CID, or UPC" name="search">
                </form>
            </main>
            <div>
                <a href="?download">Download Payments</a>
                <hr>
                <form>
                    <?if(isset($_GET['search'])){?><input type="hidden" name=search value="<?=$_GET['search']?>"><?}?>
                    <input type="number" name=p placeholder="Page <?=$currentPage?>">
                </form>
            </div>
        </nav>
        <main>
            <div id="container">
                <div>
                    <div>ID</div>
                    <div>
                        <div class="ittt">ITEM</div>
                        <div>
                            <div>PRICE</div>
                            <div class="main">
                                <div class="hd">TRACKINGS</div>
                                <div class="itamt">AMOUNT</div>
                                <div class="total">TOTAL</div>
                            </div>
                        </div>
                    </div>
                </div><?
                    if(isset($_GET['search'])){
                        $search = $_GET['search'];
                        if(preg_match('/^[A-Z]{5}$/', $search)){
                            $search_query = " AND (c.uid LIKE ?)";
                        }else{
                            $search_query = " AND (c.uid LIKE ? OR c.id LIKE ? OR i.upc LIKE ? OR c.trackings LIKE ? OR i.name LIKE ?)";
                        }
                    } else {
                        $search_query = "";
                        $search = "";
                    }
                    
                    $conn = new mysqli(getenv('DATABASE_HOST'), getenv('DATABASE_USER'), getenv('DATABASE_PASS'), getenv('DATABASE_NAME'));
                    $stmt = $conn->prepare("SELECT c.arrived, c.paid, o.price, c.uid, c.id, o.price, i.name, i.spec, c.scanned, c.qty
                            FROM `commit` c
                            INNER JOIN `order` o ON c.oid = o.id
                            INNER JOIN `item` i ON o.pid = i.id
                            WHERE c.scanned IS NOT NULL $search_query
                            ORDER BY c.id DESC
                            LIMIT ? OFFSET ?");
                    
                    if ($search_query) {
                        if(preg_match('/^[A-Z]{5}$/', $search)){
                            $search = A2N($search);
                            $stmt->bind_param('iii', $search, $max, $offset);
                        }else{
                            $search_term = '%' . $search . '%';
                            $stmt->bind_param('sssssii', $search_term, $search_term, $search_term, $search_term, $search_term, $max, $offset);
                        }
                    } else $stmt->bind_param('ii', $max, $offset);
                    
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if($result->num_rows > 0){
                        while ($row = $result->fetch_assoc()){
                            $trackings = explode(' ', $row['scanned']);
                            $ttl = $row['arrived']+$row['qty'];
                            
              ?><div>
                    <div><?=N2A($row['uid']).' '.$row['id']?></div>
                    <div>
                        <button class="long"><?=$row['name'].' '.$row['spec']?></button>
                        <div>
                            <div><?=$row['price']?></div>
                            <div class="main">
                                <div class="trk">
                                    <div class="bg"></div>
                                    <div class="text <?=!$trackings[0]?'mt':''?>">
                                        <?foreach($trackings as $tracking):?>
                                            <span><?=$tracking?:'Empty'?></span>
                                        <?endforeach?>
                                    </div>
                                </div>
                                <div class="itamt"><?=$ttl==0?$row['paid']:$row['arrived'].'/'.$ttl?></div>
                                <div class="total">$<?=$row['arrived']*$row['price']?></div>
                                <a <?=$ttl==0?'class="p" ':''?><?=$ttl==0?'':'href="?p='.$currentPage.'&id='.$row['id'].'"'?>><?=$ttl==0?'Paid':'Mark Paid'?></a>
                            </div>
                        </div>
                    </div>
                </div><? } } $conn->close();
          ?></div>
        </main>
    </body>
    <script>
    
    </script>
</html>