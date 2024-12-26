<?
    require 'assets/control.php';
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
            
            body{background:#000;color:#fff}
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
            main>a{text-decoration:none;width:calc(25% - 1rem + 1px);box-sizing:border-box;white-space:nowrap;overflow:hidden;background:#171717;display:inline-block;border-radius:.5rem;margin:0 1rem 1rem 0;border:1px solid transparent;transition:background .2s ease,border-color .2s ease,transform .2s ease}
            main>a:nth-of-type(4n){margin-right:0}
            main>a:hover,main>a:active{background:#111;border-color:#222;transform:scale(1.05)}
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
                main{width:calc(100% - 4rem)}
                nav>svg{opacity:0}
            }
            
            main>div{color:#ccc;font-weight:100;margin-bottom:.8rem;font-size:1rem}
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
                <a class=a href=/deals.v2.php>New</a>
                <svg viewBox="0 0 24 24" fill=none>
                    <path d="M4 6H20M4 12H20M4 18H20" stroke=#000 stroke-width=2 stroke-linecap=round stroke-linejoin=round/>
                </svg>
            </header>
        </div>
        <main>
        <?
            $conn = new mysqli(getenv('DATABASE_HOST'), getenv('DATABASE_USER'), getenv('DATABASE_PASS'), getenv('DATABASE_NAME'));
            $stmt = $conn->prepare("SELECT o.id,o.qty,o.commited,o.retailvalue,o.price,i.brand,o.expiration,i.name,i.spec FROM `order` o INNER JOIN `item` i ON o.pid = i.id WHERE o.status = 1 AND o.expiration > NOW() ORDER BY o.created DESC");
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()){
                    $perc = $row['commited']/$row['qty']*100;
        ?>
            <a href="?d=<?=enc($row['id'])?>">
                <div class=top>
                    <div><?=$row['brand']==NULL?'Unknown':$brands[$row['brand']]?></div>
                    <div>Ship by <?=date('n/j',strtotime($row['expiration']))?></div>
                </div>
                <div class=name><?=$row['name'].' '.$row['spec']?></div>
                <div class=bottom style="--slider:<?=$perc?>%">$<?=$row['price']-$row['retailvalue']?> - <?=round(($row['price'] / $row['retailvalue'] * 100) - 100, 2)?>%<span>$<?=$row['retailvalue']?></span></div>
            </a>
        <?
                }   
            }
            $stmt->$close;
            $conn->$close;
        ?>
        </main>
    </body>
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
</html>