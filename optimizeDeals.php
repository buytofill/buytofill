<?
    require 'assets/control.php';
    
    if($_SERVER['REQUEST_METHOD'] == "GET"){ 
        if(isset($_GET['o'])) $o = dav($_GET['o']);
    }
?>
<!DOCTYPE html>
<html lang=en>
    <head>
        <meta charset=utf-8>
        <title>BuyToFill</title>
        <meta name=viewport content="width=device-width, initial-scale=1"/>
        <meta name=handheldfriendly content=true />
        <meta name=MobileOptimized content=width />
        <meta name=description content=BuyToFill />
        <meta name=author content=btf />
        <meta name=keywords content=BuyToFill />
        <link rel=icon href=assets/favicon.ico />
        <link rel=stylesheet href=assets/styles.css >
        <style>
            :root{
                --deal-p: 0%;
                --deal-h: 60px;
            }
            
            #container{display:inline-flex;flex-flow:wrap;gap:.5rem}
            #container>a{z-index:0;overflow:hidden;display:flex;position:relative;border-radius:.4rem;background:#28292a;height:calc(var(--deal-h) + 1rem);cursor:pointer;padding:.5rem 0 .5rem .5rem}
            #container>a:before{z-index:-1;content:'';position:absolute;background:#ffffff09;width:var(--deal-p);top:0;left:0;height:100%}
            #container>a>.img{min-height:60px;min-width:60px;width:60px;height:60px;display:flex;justify-content:center;align-items:center}
            #container>a>.img>img{max-height:100%;max-width:100%;border-radius:.2rem}
            #container>a:hover,#container>a.s{outline:2px solid var(--purp);outline-offset:-2px}
            #container .badges,#container .info>p{display:flex;padding:0 .5rem;text-wrap:nowrap}
            #container .badges>div{background:#3f3f42;border-radius:.2rem;border:1px solid #4c4c4f;font-size:.7rem;display:flex;align-items:center;font-weight:600;color:#aaa;padding:.15rem .4rem}
            #container .badges>div:first-child{margin-right:.25rem}
            #container .badges>.a{background:#4042cd88;border-color:var(--purp)}
            #container .info>p{color:#bbb;font-weight:500;margin-top:.1rem}
            #container .info>.below{font-size:.9rem;color:#999}
            
            <?if(isset($o)){?>
            
            body:after{content:'';z-index:1;position:absolute;top:0;left:0;background:#0009;width:100vw;height:100vh}
            #cInf{position:absolute;min-width:700px;padding:2rem;top:calc(50%);left:50%;z-index:2;box-shadow:0 0 5px 2px #0005;transform:translate(-50%, -50%);background:var(--dark);border-radius:1rem;border:1px solid #444}
            
            #cInf>div{display:flex}
            
            div:has(>label){position:relative}
            div>input{background:var(--dark);border-radius:.4rem;padding:1rem;width:100%;color:#ddd;font-size:1rem;font-weight:600;transition:border-color .2s ease;border:1px solid #444}
            div>label{position:absolute;font-weight:600;background:var(--dark);color:#555;top:50%;left:.6rem;transform:translateY(-50%);transition:top .2s ease,font-size .2s ease,color .2s ease;padding:0 .5rem;border-radius:1rem}
            div:has(>input[type="text"]:not(:placeholder-shown))>label, div:has(>input[type="number"]:not(:placeholder-shown))>label, div:has(>input:focus)>label, div:has(>input:hover)>label{top:0;font-size:.9rem}
            div>input:focus,div>input:hover{border-color:var(--purp)}
            div:has(>input:focus)>label{color:var(--purp)}
            
            #cInf form{width:50%}
            #cInf hr{height:70px;background:#333;width:2px;margin:auto 1rem}
            #cInf form p{color:#666;margin-bottom:1rem;font-size:.85rem}
            #cInf #x{top:0rem}
            #cInf #q{top:1rem;transform:translate(100%, 100%)}
            #cInf>a{position:absolute;transform:translateX(100%);right:-1rem;box-shadow:0 0 5px 2px #0005;background:#202124EE;border:1px solid #444;border-radius:1rem;padding:1rem}
            #cInf>a>svg{color:#888;height:calc(15px + .3rem);width:calc(15px + .5rem)}
            #cInf>a:hover>svg{color:var(--purp)}
            #cInf .img{margin-bottom:1rem;border-radius:.4rem;display:flex;height:60px;width:60px}
            #cInf .img img{border-radius:.4rem;max-height:100%;margin:auto;max-width:100%}
            
            #cInf .badges,#cInf .info>p{display:flex;padding:0 .5rem;text-wrap:nowrap}
            #cInf .badges>div{background:#3f3f42;border-radius:.2rem;border:1px solid #4c4c4f;font-size:.7rem;display:flex;align-items:center;font-weight:600;color:#aaa;padding:.15rem .4rem}
            #cInf .badges>div:not(:last-child){margin-right:.25rem}
            #cInf .badges>.a{background:#4042cd88;border-color:var(--purp)}
            #cInf .info>p{color:#bbb;font-weight:500;margin-top:.1rem}
            #cInf .info>.below{font-size:.9rem;color:#999}
            
            <?if(isset($_GET['assist'])){?>
            
            #cInf{top:calc(50%)}

            #assistance{background:#202124EE;display:flex;flex-direction:column;border:1px solid #444;position:absolute;top:-1rem;transform:translateY(-100%);padding:1.3rem 2rem .9rem;box-shadow:0 0 5px 2px #0005;border-radius:1rem;width:100%;left:0}
            #assistance>p{font-weight:400;margin:.5rem 0;font-size:.9rem;color:#999}
            #assistance>h2{font-weight:800;color:var(--grn);padding:.1rem 0 .5rem;font-size:.9rem;border-bottom:1px solid var(--grn);margin-bottom:.3rem}
            #q.a>svg{color:var(--purp)}
                
            <?}?>
            
            @media(max-width:900px){
                #cInf>a{transform:translateY(-100%)}
                #cInf #x{right:0;top:-1rem}
                #cInf #q{transform:translate(-100%,-100%);top:-1rem;right:1rem}
                #assistance{position:relative;padding:0;transform:none;box-shadow:none;margin-top:.5rem;border:none}
            }
            
            @media(max-width:770px){
                #cInf{top:unset;width:100%;bottom:0;left:unset;transform:unset;border-radius:unset;border-top-right-radius:2rem;min-width:unset}
                #cInf #x{left:1rem;top:-1rem;right:unset}
                #cInf #q{transform:translate(100%,-100%);top:-1rem;left:2rem;right:unset}
            }
            
            @media(max-width:700px){
                #cInf>div:has(>form){flex-direction:column}
                #cInf form{width:100%}
                #cInf form input{width:100%}
                #cInf hr{background:none;height:10px}
            }
            
            <?}?>
            
            @media(max-width:700px){
                #container>a{width:100%;max-width:calc(100vw - 2rem)}
            }
            
            <?if(isset($_GET['oid'])){?>
            #cInf form{width:100%}
            
            #cInf #b{left:-1rem;right:unset;transform:translateX(-100%);top:0}
            <?}?>
        </style>
    </head>
    <body>
        <?require 'assets/header.php'?>
        <nav>
            <main>
                <a href=# class=y>All Deals</a>
            </main>
        </nav>
        <main>
            <h1></h1>
            <div id=container><?
                    $conn = new mysqli(getenv('DATABASE_HOST'), getenv('DATABASE_USER'), getenv('DATABASE_PASS'), getenv('DATABASE_NAME')); 
                    $stmt = $conn->prepare("SELECT o.id,o.pid,o.qty,o.commited,o.retailvalue,o.price,i.name,i.spec,o.expiration FROM `order` o INNER JOIN `item` i ON o.pid = i.id WHERE status = 1 AND o.expiration > NOW() ORDER BY o.created DESC");
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if($result->num_rows > 0){
                        while ($row = $result->fetch_assoc()){
                            $amt = $row['price']-$row['retailvalue'];
                            
                            if(isset($o) && $o == $row['id']){
                                $oi = $row;
                                $oi['amt'] = $amt;
                            }
              ?><a <?if(isset($o) && $o == $row['id']){?>class=s <?}?>href="?o=<?=enc($row['id'])?>" style="--deal-p:<?=round($row['commited']/$row['qty']*100,2)?>%">
                    <div class=img><img title="<?=$row['name']?>" src="assets/images/<?=$row['pid']?>.webp"></div>
                    <div class=info>
                        <div class=badges>
                            <div class=<?=$amt>0?'a':($amt==0?'r':'b')?>>$<?=$amt?> - <?=round(($row['price']/$row['retailvalue'])*100-100,2)?>%</div>
                            <div>$<?=rtrim(rtrim($row['price'], '0'), '.')?></div>
                        </div>
                        <p><?=$row['name']?></p>
                        <p class=below><?=$row['spec']?></p>
                    </div>
                </a><? } } $conn->close();
          ?></div>
        </main>
    <? if(isset($o)){?>
        <div id=cInf>
            <?if(isset($_GET['assist'])){?>
            <div id=assistance>
                <h2>CHOOSE HOW YOU WANT TO COMMIT</h2>
                <p>If you are dropshipping from BestBuy to our shipping address, submit the order ID and we'll email you when it gets checked in.</p>
                <p>For anything else, submit the quantity and when you have recieved trackings, you may add them in the commits page.</p>
            </div>
            <?}?>
            <div id=itemInfo>
                <div class=img><img title="<?=$oi['name']?>" src="assets/images/<?=$oi['pid']?>.webp"></div>
                <div class=info>
                    <div class=badges>
                        <div class=<?=$oi['amt']>0?'a':($oi['amt']==0?'r':'b')?>>$<?=$oi['amt']?> - <?=round(($oi['price']/$oi['retailvalue'])*100-100,2)?>%</div>
                        <div>$<?=rtrim(rtrim($oi['price'], '0'), '.')?></div>
                        <div>Exp: <?=(new DateTime($row['expiration']))->format('n/j/y')?></div>
                    </div>
                    <p><?=$oi['name']?></p>
                    <p class=below><?=$oi['spec']?></p>
                </div>
            </div>
            <div> 
                <form>
                    <?=isset($_GET['oid'])?'':"<p>Commit with your BestBuy Order ID, and we'll take care of the rest!</p>"?>
                    <div>
                        <label for=oid>Order ID</label>
                        <input type=text name=oid id=oid placeholder=" "<?=isset($_GET['oid'])?' value='.$_GET['oid']:''?> required>
                        <input type=hidden name=o value=<?=$_GET['o']?>>
                    </div>
                </form>
                <?if(!isset($_GET['oid'])){?>
                <hr>
                <form>
                    <p>Commit your quantity and add the tracking numbers to the commitment yourself.</p>
                    <div>
                        <label for=qty>QTY</label>
                        <input type=number name=qty id=qty placeholder=" " required>
                        <input type=hidden name=o value=<?=$_GET['o']?>>
                    </div>
                </form>
                <?}?>
            </div>
            <?if(isset($_GET['oid'])){?>
            <a id=b href=?o=<?=$_GET['o']?><?=isset($_GET['assist'])?'&assist':''?>>
                <svg stroke=currentColor>
                    <path d="m4 12 16 0m-16 0 6-6m-6 6 6 6" stroke-width=1.5 stroke-linejoin=round stroke-linecap=round />
                </svg>
            </a>
            <?}?>
            <a id=q <?=isset($_GET['assist'])?'class=a':''?> href=?o=<?=$_GET['o']?><?=isset($_GET['oid'])?'&oid='.$_GET['oid']:''?><?=isset($_GET['assist'])?'':'&assist'?>>
                <svg fill=currentColor>
                    <path d="m8 8c0-1.7 1.7-3.2 3.5-3.2 1.7 0 3.4 1.5 3.4 3.2 0 1.6-.7 2.3-1.9 3.3-1.1 1-2.4 2.2-2.4 4.5 0 .4.4.8.9.8.4 0 .8-.4.8-.8 0-1.6.7-2.3 1.9-3.3 1-.9 2.4-2.2 2.4-4.5 0-2.9-2.7-4.8-5.1-4.8-2.5 0-5.2 1.9-5.2 4.8 0 .4.4.9.8.9.5 0 .9-.5.9-.9m3.5 13.4c.7 0 1.3-.6 1.3-1.3 0-.7-.6-1.3-1.3-1.3-.8 0-1.3.6-1.3 1.3 0 .7.5 1.3 1.3 1.3"/>
                </svg>
            </a>
            <a id=x href=optimizeDeals>
                <svg fill=currentColor>
                    <path d="m19.2 6.2a1 1 0 00-1.4-1.4l-5.8 5.8-5.8-5.8a1 1 0 00-1.4 1.4l5.8 5.8-5.8 5.8a1 1 0 101.4 1.4l5.8-5.8 5.8 5.8a1 1 0 001.4-1.4l-5.8-5.8 5.8-5.8"/>
                </svg>
            </a>
        </div>
        <?}?>
    </body>
</html>