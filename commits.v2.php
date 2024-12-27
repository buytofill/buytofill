<?
    require 'assets/helper.php';
    
    if(isset($_GET['i'])){
        $cid = dav($_GET['i']);
        if(!$cid)header('Location: commits.v2.php');
    }
?>
<!DOCTYPE html>
<html lang=en>
    <head>
        <meta charset=utf-8 />
        <title>BuyToFill</title>
        <meta name=viewport content="width=device-width, initial-scale=1" />
        <meta name=handheldfriendly content=true />
        <meta name=MobileOptimized content=width />
        <meta name=description content=BuyToFill />
        <meta name=author content=BuyToFill />
        <meta name=keywords content=BuyToFill />
        <link rel=icon href=assets/favicon.ico />
        <link rel=stylesheet href=assets/styles.v1.css />
        <style>
            :root{
                --slider: 0%;
            }    
            
            body{background:#171717;color:#fff}
            nav{transition:margin 1s ease;background:#474bff;height:52px;width:100%;color:#fff;display:flex;border-bottom:.5px solid #555;box-sizing:border-box;padding:0 10%}
            nav:after{content:'';position:absolute;transition:width .5s ease,transform .5s ease,left .5s ease;background:#fff;border-radius:1rem;height:1.5px;top:51px;left:0;width:0}
            nav:has(a:first-of-type:hover):after{left:50%;width:125px;transform:translateX(-180px)}
            nav:has(a:nth-of-type(2):hover):after{left:50%;width:87px;transform:translateX(-55px)}
            nav:has(a:last-of-type:hover):after{left:50%;width:148px;transform:translateX(31px)}
            nav:has(a:first-of-type:active):after,nav:has(a:nth-of-type(2):active):after,nav:has(a:last-of-type:active):after,
            nav:has(a:first-of-type:focus):after,nav:has(a:nth-of-type(2):focus):after,nav:has(a:last-of-type:focus):after{left:100%;width:0;transform:none}
            nav>a{font-size:.9rem;font-weight:400;align-self:center;padding:1.11rem 1.5rem 1rem;color:#fff;text-decoration:none}
            nav>a.a{font-weight:700;color:#fff}
            nav>svg{height:24px;width:24px;margin:auto 0}
            nav>svg:first-child{margin-right:auto}
            nav>svg:last-child{margin-left:auto}
            header{white-space:nowrap;border-bottom:1px solid #333;background:#474bff77;display:flex;padding:0 10%;font-size:.8rem}
            header p{margin-right:auto}
            header a{align-self:center;padding:2px 1rem 0;color:#ccc;text-decoration:none}
            header a.a{font-weight:600;color:#fff}
            nav,header{overflow-x:auto;overflow-y:hidden}
            header a:last-child{padding-right:0}
            hr{margin:.6rem .5rem;border-color:#ccc}
            header>svg{display:none}
            header>a:first-of-type{margin-left:.5rem}
            header>a:last-of-type{padding-right:0}
            html[data-scroll="1"] nav{margin-top:-52px}
            html[data-scroll="1"] main{padding-top:calc(42px + 5%)}
            body>div{background:hsl(0 0% 3.1%/.5);backdrop-filter:blur(24px);position:absolute;top:0;left:0;width:100%}
            
            main{transition:padding-top 1s ease;padding:calc(94px + 2rem) 2rem 2rem;width:calc(100% - 4rem);height:calc(100% - 94px);overflow-y:auto;position:fixed;z-index:-1;overflow-x:hidden}
            main>a{text-decoration:none;width:calc(25% - 1rem + 1px);box-sizing:border-box;white-space:nowrap;overflow:hidden;background:#202124;display:inline-block;border-radius:.5rem;margin:0 1rem 1rem 0;border:1px solid transparent;transition:background .2s ease,border-color .2s ease,transform .2s ease}
            main>a:nth-of-type(4n){margin-right:0}
            main>a:hover,main>a:active{background:#24262b;border-color:#222;transform:scale(1.05)}
            .top{display:flex;font-weight:100;font-size:.78rem;padding:.8rem .8rem .2rem .8rem}
            .top div:last-child{margin-left:auto;padding-left:.5rem}
            .bottom{position:relative;display:flex;border-top:1px solid #333;box-sizing:border-box;padding:.7rem .8rem .65rem;color:#ddd;font-weight:200;font-size:.78rem}
            .bottom>span{margin-left:auto}
            .bottom:after{position:absolute;top:-.5px;left:0;height:.5px;width:var(--slider);content:'';background:#48f58a;opacity:.5}
            .name{padding:.2rem .8rem .8rem;font-size:.85rem}
            
            @media(max-width:1300px){
                main>a{width:calc(33% - 1rem + .35rem)}
                main>a:nth-of-type(4n){margin-right:1rem}
                main>a:nth-of-type(3n){margin-right:0;}
            }
            @media(max-width:1000px){
                main>a{width:calc(50% - 1rem + .35rem)}
                main>a:nth-of-type(3n){margin-right:1rem}
                main>a:nth-of-type(2n){margin-right:0}
            }
            @media(max-width:650px){ 
                main>a{width:100%;margin-right:0}
                nav>svg{opacity:0}
            }
            
            main>div{color:#ccc;font-weight:100;margin-bottom:.8rem;font-size:1rem}
            
            
            
            
            aside{position:absolute;top:0;left:0;width:100vw;height:100vh;background:#fff1;padding:1rem;box-sizing:border-box;backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px)}
            #mgCmt{padding:1rem 0 0 1rem;display:flex;position:absolute;top:4vw;left:2vw;width:calc(100% - 4vw);height:calc(100% - 8vw);background:#171717;border-radius:.5rem;box-sizing:border-box}
            #links,#edit,#item{background:#24262b;box-sizing:border-box;margin:0 1rem 1rem 0;border-radius:.4rem}
            #status{margin-right:1rem;display:flex;flex-direction:column;width:300px;padding:1.5rem 2rem 0;background:#24262b;box-sizing:border-box;border-radius:.4rem}
            #status>svg{margin:.5rem 0 1rem}
            #below{display:flex;height:100%}
            #inner{height:calc(100% - 1rem);display:flex;flex-direction:column}
            #status>svg>path{stroke-linejoin:round;stroke-linecap:round}
            #status>div{padding:1rem;border-bottom:1px solid #343434;margin-bottom:1rem}
            #status>div>h3{margin:0;margin-bottom:.5rem;display:flex;justify-content:center}
            #status>div>p{margin:0;display:flex;justify-content:center;font-size:600;color:#aaa;font-size:.78rem}
            svg>#outline{}
            #links{min-width:200px;height:calc(100% - 1rem)}
            #edit{width:100%;height:calc(100% - 1rem)}
            #queue{min-width:300px;margin-right:1rem;background:#24262b;box-sizing:border-box;border-radius:.4rem}
            #item{height:80%}
            
            #trksrc{background: transparent;
    width: calc(100% - 2rem);
    border: 2px solid #334;
    border-radius: 2rem;
    padding: 1rem 1.4rem .9rem;
    color: #fff;
    margin: 1rem;
    box-sizing: border-box;}
        </style>
    </head>
    <body>
        <div>
            <nav>
                <svg fill=currentColor viewBox="0 0 24 24"><path d="m20 8c.4.5 1.5 0 2.4 0 1-.4 0-4.6 0-6.9-2.4 0-6.6-.9-7 0 0 .9-.5 2.1 0 2.6s1.6 0 2.5 0C.9 21.9.9 19.6.9 20.7c1.1 2.5-.6.9 2 2.1 1.1 0-1.1 0 17.1-17.2 0 1-.5 2 0 2.4m-4.2 15.5c.5.9 4.8 0 7 0 0-2.3 1.1-6.4 0-6.8-.9 0-1.8-.5-2.4 0s0 1.3 0 2.2c-6.8-6-5.3-6-5.9-6s-2.1 1.4-2.1 2.1 0-.9 6 6.2c-1 0-2.1-.6-2.6 0s0 1.3 0 2.3M1.3 3.7c0 .4 0-.5 7.2 7.3.8-.3 1.6-1.2 1.9-1.8-7.7-7.3-6.8-7.3-7.3-7.3C1 3 2.4 1.6 1.3 3.7"></path></svg>
                <!--a <?=$_SERVER['PHP_SELF']=="/dash.php"?'class=a ':''?>href=/dash>Dashboard</a-->
                <a <?=$_SERVER['PHP_SELF']=="/deals.v2.php"?'class=a ':''?>href=/deals.v2.php>Deals</a>
                <a <?=$_SERVER['PHP_SELF']=="/commits.v2.php"?'class=a ':''?>href=/commits.v2.php>Commitments</a>
                <svg viewBox="0 0 16 16" fill=none stroke=currentColor stroke-width=1.5><path d="m15 8A7 7 0 111 8a7 7 0 1114 0"></path></svg>
            </nav>
            <header>
                <p><?=$_SESSION['auid']?></p>
                <a <?=isset($_GET['l'])||isset($_GET['t'])||isset($_GET['p'])||isset($_GET['c'])?'':'class=a '?>href=/commits.v2.php>New</a>
                <a <?=isset($_GET['t'])?'class=a ':''?>href=?t>In Transit</a>
                <a <?=isset($_GET['p'])?'class=a ':''?>href=?p>Processed</a>
                <a <?=isset($_GET['c'])?'class=a ':''?>href=?c>Complete</a>
                <hr>
                <a <?=isset($_GET['l'])?'class=a ':''?>href=?l>Manage Label</a>
                <svg viewBox="0 0 24 24" fill=none>
                    <path d="M4 6H20M4 12H20M4 18H20" stroke=#000 stroke-width=2 stroke-linecap=round stroke-linejoin=round />
                </svg>
            </header>
        </div>
        <?if(!isset($_GET["i"])){?>
        <main>
        <?
            $conn = new mysqli(getenv('DATABASE_HOST'), getenv('DATABASE_USER'), getenv('DATABASE_PASS'), getenv('DATABASE_NAME'));
            $uid = $_SESSION['uid'];
            
            $prepared = "
                SELECT o.pid, i.name, i.spec, i.brand, o.price, c.qty, c.id, o.expiration,
                (SELECT IFNULL(SUM(t.qty), 0) FROM `trackings` AS t WHERE t.cid = c.id) AS tracked
                FROM `commit` AS c
                INNER JOIN `order` AS o ON c.oid = o.id
                INNER JOIN `item` AS i ON o.pid = i.id
                WHERE c.uid = ? AND c.status >= 0
                HAVING c.qty != tracked
                ORDER BY c.created DESC
            ";
            if(isset($_GET['t'])){
                $prepared = "
                    SELECT o.pid, i.name, i.spec, i.brand, o.price, c.qty, c.id, o.expiration,
                    (SELECT IFNULL(SUM(t.qty), 0) FROM `trackings` AS t WHERE t.cid = c.id) AS tracked,
                    (SELECT IFNULL(SUM(t.qty), 0) FROM `trackings` AS t WHERE t.cid = c.id AND t.here >= 1) AS arrived
                    FROM `commit` AS c
                    INNER JOIN `order` AS o ON c.oid = o.id
                    INNER JOIN `item` AS i ON o.pid = i.id
                    WHERE c.uid = ? AND c.status >= 0
                    HAVING c.qty = tracked AND c.qty != arrived
                    ORDER BY c.created DESC
                ";
            }else if(isset($_GET['p'])){
                $prepared = "
                    SELECT o.pid, i.name, i.spec, i.brand, o.price, c.qty, c.id, o.expiration,
                    (SELECT IFNULL(SUM(t.qty), 0) FROM `trackings` AS t WHERE t.cid = c.id) AS tracked,
                    (SELECT IFNULL(SUM(t.qty), 0) FROM `trackings` AS t WHERE t.cid = c.id AND t.here >=1 ) AS arrived,
                    (SELECT IFNULL(SUM(t.qty), 0) FROM `trackings` AS t WHERE t.cid = c.id AND t.here = 2) AS paid
                    FROM `commit` AS c
                    INNER JOIN `order` AS o ON c.oid = o.id
                    INNER JOIN `item` AS i ON o.pid = i.id
                    WHERE c.uid = ? AND c.status >= 0
                    HAVING c.qty = tracked AND c.qty = arrived AND c.qty != paid
                    ORDER BY c.created DESC
                ";
            }else if(isset($_GET['c'])){
                $prepared = "
                    SELECT o.pid, i.name, i.spec, i.brand, o.price, c.qty, c.id, o.expiration,
                    (SELECT IFNULL(SUM(t.qty), 0) FROM `trackings` AS t WHERE t.cid = c.id) AS tracked,
                    (SELECT IFNULL(SUM(t.qty), 0) FROM `trackings` AS t WHERE t.cid = c.id AND t.here >= 1) AS arrived,
                    (SELECT IFNULL(SUM(t.qty), 0) FROM `trackings` AS t WHERE t.cid = c.id AND t.here = 2) AS paid
                    FROM `commit` AS c
                    INNER JOIN `order` AS o ON c.oid = o.id
                    INNER JOIN `item` AS i ON o.pid = i.id
                    WHERE c.uid = ? AND c.status >= 0
                    HAVING c.qty = tracked AND c.qty = arrived AND c.qty = paid
                    ORDER BY c.created DESC
                ";
            }
            $stmt = $conn->prepare($prepared);
            $stmt->bind_param("i", $uid);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()){
                    if(isset($row['qty']) && isset($row['tracked']) && $row['tracked'] != 0 && $row['tracked']<$row['qty']) $tracked=$row['tracked']/$row['qty']*100;
                    else $tracked= 0;
                    
        ?>
            <a href="?i=<?=enc($row['id'])?>">
                <div class=top>
                    <div><?=$row['brand']==NULL?'Unknown':$brands[$row['brand']]?></div>
                    <div><?if(!isset($_GET['t'])&&!isset($_GET['p'])&&!isset($_GET['c'])){?>Ship by <?=date('n/j',strtotime($row['expiration']));}?></div>
                </div>
                <div class=name><?=$row['name'].' '.$row['spec']?></div>
                <?if(isset($_GET['t'])){?>
                <div class=bottom style="--slider:<?=$row['arrived']/$row['qty']*100?>%">Received <?=$row['arrived']?>/<?=$row['qty']?> items<span>$<?=$row['price']?></span></div>
                <?}else if(isset($_GET['p'])){?>
                <div class=bottom style="--slider:<?=$row['paid']/$row['qty']*100?>%">Paid <?=$row['paid']?>/<?=$row['qty']?> items<span>$<?=$row['price']?></span></div>
                <?}else if(isset($_GET['c'])){?>
                <div class=bottom style="--slider:100%">Paid $<?=$row['paid']*$row['price']?><span>$<?=$row['price']?></span></div>
                <?}else{?>
                <div class=bottom style="--slider:<?=$tracked?>%">Tracking <?=$row['tracked'].'/'.$row['qty']?> items<span>$<?=$row['price']?></span></div>
                <?}?>
            </a>
        <?
                }
            }
            $stmt->$close;
            $conn->$close;
        ?>
        </main>
        <?}?>
        <?if(isset($_GET['i'])){
            $conn = new mysqli(getenv('DATABASE_HOST'), getenv('DATABASE_USER'), getenv('DATABASE_PASS'), getenv('DATABASE_NAME'));
            $uid = $_SESSION['uid'];
                
            $stmt = $conn->prepare("
                SELECT c.*, SUM(t.qty) AS qtyTracked,
                    SUM(CASE WHEN t.here >= 1 THEN t.qty ELSE 0 END) AS qtyArrived,
                    SUM(CASE WHEN t.here = 2 THEN t.qty ELSE 0 END) AS qtyPaid
                FROM `commit` c
                JOIN `trackings` t ON t.cid = c.id
                WHERE c.id = ? AND c.uid = ?
                GROUP BY c.id
            ");
            $stmt->bind_param("ii", $cid, $uid);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
        ?>
        <aside>
            <div id=mgCmt>
                <div id=links>
                    
                </div>
                <div id=inner>
                    <div id=item></div>
                    <div id=below>
                        <div id=status>
                            <div>
                                <h3>Incomplete</h3>
                                <p>Awaiting trackings</p>
                            </div>
                            <svg viewBox="-5.3 -5.1 10.7 9.2">
                                <path id=outline stroke=#45c9 fill=#45c2 stroke-width=.1 d="M0-5 5.2 4 0 0 5.2 4-5.2 4-5.2 4ZL0 0-5.2 4"></path>
                                <path stroke=#fffa stroke-width=.1 fill=#fff2 d="M0-<?=5*($row['qtyTracked']/$row['qty'])?> <?=5.2*($row['qtyArrived']/$row['qty'])?> <?=4*($row['qtyArrived']/$row['qty'])?>-<?=5*($row['qtyPaid']/$row['qty'])?> <?=4*($row['qtyPaid']/$row['qty'])?>Z"></path>
                            </svg>
                        </div>
                        <div id=queue></div>
                    </div>
                </div>
                <div id=edit>
                    <div>
                        <div><?=$row['name']?></div>
                        <!--input type=number placeholder=Qty-->
                        <form>
                            <input id=trksrc type=text placeholder=Search>
                        </form>
                        <div>
                            <div>
                                text
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
        <?
            $stmt->$close;
            $conn->$close;
        }
        ?>
    </body>
    <?if(!isset($_GET["i"])){?>
    <script>
        let a = document.querySelector('main');
        
        let lst = a.scrollTop;
        
        const deb = (fn) => {
            let frame;
            return (...params) => {
                if(frame) cancelAnimationFrame(frame);
                frame = requestAnimationFrame(() => {fn(...params)});
            } 
        };
        
        const scrl = () => {
            let cst = a.scrollTop;
        
            if(cst == 0 || cst < lst - 5) document.documentElement.dataset.scroll = 0;
            else if(cst > lst) document.documentElement.dataset.scroll = 1;
            isu = document.documentElement.dataset.scroll;
            lst = cst;
        }
        
        a.addEventListener('scroll', deb(scrl), {passive: 1});
        
        scrl();
    </script>
    <?}?>
</html>









