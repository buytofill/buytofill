<?
    require 'assets/control.php';
    if($_SERVER['REQUEST_METHOD'] == "GET"){
        if(isset($_GET['b'])){
            $b = dav($_GET['b']);
            if(!$b || $b<1 || $b>25){
                header('Location: ?');
                exit;
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
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
        <link rel=stylesheet href=assets/styles.css />
        <style>
            body>svg{min-width:100vw;min-height:100vh;user-select:none;pointer-events:none}
        <?if(isset($b)){?>
            #a{width:25rem;background:#fff;border-radius:.75rem;padding:2rem;position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);box-shadow:#0005 0 5px 15px 0,#0004 0 15px 35px -5px,#0005 0 0 0 1px}
            #aS{position:absolute;right:-1rem;top:0;transform:translateX(100%);border-radius:.5rem;background:#fff}
        <?}else{?>
            #a{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);display:flex;flex-direction:column}
            #logo,h1,h3{align-self:center;margin-bottom:1rem}
            #logo>svg{color:#fff;width:70px;height:70px}
            #a>h1{color:#fff;font-size:3.5rem;white-space:nowrap}
            #a>h3{margin-bottom:8rem;color:#eee;font-weight:300;width:80%;text-align:center}
            #gs{align-self:center;background:#575bec;transition:outline-offset .1s ease;color:#fff;font-weight:600;padding:1rem 3rem;border-radius:.5rem;outline:1px solid #fff}
            #gs:hover,#gs:focus{outline-offset:3px}
            @media(max-width:500px){
                #a>h1{font-size:10vw}
                #a>h3{font-size:4vw}
            }
        <?}?>
        </style>
    </head>
    <body>
        <svg viewBox="0 0 1440 900">
            <path d="M0 0H1440V900H0z" fill="url(#paint0_linear)"/>
            <path d="M141 246C215 120 321 96 450 100c110 2 229 32 330-12C881 45 951-33 1060-28c147 6 210 125 201 241-13 162-150 244-286 236-112-7-231-18-332 57-92 69-155 212-283 225C108 756 5 481 141 246z" fill="url(#paint1_linear)" style="mix-blend-mode:color-dodge"/>
            <path d="M1199 152c57 58 57 153 0 211L342 1234c-57 58-151 58-208 0s-57-154 0-212L991 152c57-58 151-58 208 0z" fill="url(#paint2_linear)" style="mix-blend-mode:color-dodge"/>
            <path d="M355 1011c36 36 94 36 130 0l533-542c36-36 36-95 0-132s-94-36-129 0L355 879c-36 36-36 96 0 132z" fill="url(#paint3_linear)" style="mix-blend-mode:color-dodge"/>
            <path d="M848 774c24 25 65 25 89 0l367-372c24-25 24-66 0-90-25-25-65-25-89 0L848 684c-24 25-24 65 0 90z" fill="url(#paint4_linear)" style="mix-blend-mode:color-dodge"/>
            <path d="M947 354c0 0 0 0 1-1l373-379c1-1 1-2 0-2-1-1-2-1-2 0L945 351c0 1 0 2 0 2 1 1 1 1 2 1z" fill="url(#paint5_linear)" style="mix-blend-mode:screen"/>
            <path d="M479 997c0 0 1 0 1 0l652-663c1 0 1-1 0-2 0 0-1 0-2 0L478 995c-1 0-1 1 0 2 0 0 0 0 1 0z" fill="url(#paint6_linear)" style="mix-blend-mode:screen"/>
            <path d="M21 1027c0 0 1 0 1 0l306-311c0 0 0-1 0-2-1 0-1 0-2 0L20 1025c0 1 0 1 0 2 1 0 1 0 1 0z" fill="url(#paint7_linear)" style="mix-blend-mode:screen"/>
            <path d="M1162 238c0 0 1 0 1-1l3-28 27-3c1 0 2-1 2-1l2-28 28-3c1 0 1-1 2-1l2-29 28-2c1 0 1-1 1-2s-1-1-1-1l-30 2c0 0-1 1-1 2l-2 28-28 3c-1 0-2 0-2 1l-2 28-28 3c-1 0-1 0-1 1l-3 30c0 0 1 1 1 1 1 0 1 0 1 0z" fill="url(#paint8_linear)" style="mix-blend-mode:screen"/>
            <path d="M141 847c0 0 0 0 0 0-1 0-1-1-1-2l2-24c0-1 1-1 1-1l23-2 2-23c0 0 0-1 1-1l22-2 2-23c0 0 1-1 2-1l23-2c1 0 2 0 2 1s-1 2-2 2l-22 2-2 22c0 1-1 2-1 2l-22 2-3 22c0 1 0 2-1 2l-22 2-2 22c0 1-1 2-2 2z" fill="url(#paint9_linear)" style="mix-blend-mode:screen"/>
            <defs>
                <linearGradient id="paint0_linear" x1="1283.08" y1="-121.873" x2="95.2248" y2="1047.7" gradientUnits="userSpaceOnUse">
                    <stop offset="0" stop-color="#3f42d5"/>
                    <stop offset="1" stop-color="#474bff"/>
                </linearGradient>
                <linearGradient id="paint1_linear" x1="526.132" y1="79.1813" x2="882.91" y2="665.013" gradientUnits="userSpaceOnUse">
                    <stop offset="0" stop-color="#050505"/>
                    <stop offset=".5" stop-color="#131313"/>
                    <stop offset=".8" stop-color="#292929"/>
                    <stop offset="1" stop-color="#424242"/>
                </linearGradient>
                <linearGradient id="paint2_linear" x1="222.819" y1="1071.31" x2="1389.32" y2="106.719" gradientUnits="userSpaceOnUse">
                    <stop/>
                    <stop offset="0.1942" stop-color="#050505"/>
                    <stop offset="0.4169" stop-color="#131313"/>
                    <stop offset="0.6536" stop-color="#2A2A2A"/>
                    <stop offset="0.8985" stop-color="#494949"/>
                    <stop offset="1" stop-color="#595959"/>
                </linearGradient>
                <linearGradient id="paint3_linear" x1="962.861" y1="438.379" x2="236.413" y2="1039.08" gradientUnits="userSpaceOnUse">
                    <stop/>
                    <stop offset="0.1942" stop-color="#050505"/>
                    <stop offset="0.4169" stop-color="#131313"/>
                    <stop offset="0.6536" stop-color="#2A2A2A"/>
                    <stop offset="0.8985" stop-color="#494949"/>
                    <stop offset="1" stop-color="#595959"/>
                </linearGradient>
                <linearGradient id="paint4_linear" x1="1265.53" y1="380.976" x2="766.485" y2="793.641" gradientUnits="userSpaceOnUse">
                    <stop/>
                    <stop offset="0.1942" stop-color="#050505"/>
                    <stop offset="0.4169" stop-color="#131313"/>
                    <stop offset="0.6536" stop-color="#2A2A2A"/>
                    <stop offset="0.8985" stop-color="#494949"/>
                    <stop offset="1" stop-color="#595959"/>
                </linearGradient>
                <linearGradient id="paint5_linear" x1="944.914" y1="162.496" x2="1321.33" y2="162.496" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#918D60"/>
                    <stop offset="0.3772" stop-color="#555338"/>
                    <stop offset="0.799" stop-color="#181710"/>
                    <stop offset="1"/>
                </linearGradient>
                <linearGradient id="paint6_linear" x1="477.224" y1="664.584" x2="1132.61" y2="664.584" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#918D60"/>
                    <stop offset="0.3772" stop-color="#555338"/>
                    <stop offset="0.799" stop-color="#181710"/>
                    <stop offset="1"/>
                </linearGradient>
                <linearGradient id="paint7_linear" x1="328.001" y1="870.499" x2="19.9997" y2="870.499" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#918D60"/>
                    <stop offset="0.3772" stop-color="#555338"/>
                    <stop offset="0.799" stop-color="#181710"/>
                    <stop offset="1"/>
                </linearGradient>
                <linearGradient id="paint8_linear" x1="1160.02" y1="188.482" x2="1258.12" y2="188.482" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#918D60"/>
                    <stop offset="0.3772" stop-color="#555338"/>
                    <stop offset="0.799" stop-color="#181710"/>
                    <stop offset="1"/>
                </linearGradient>
                <linearGradient id="paint9_linear" x1="139.794" y1="806.196" x2="219.552" y2="806.196" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#918D60"/>
                    <stop offset="0.3772" stop-color="#555338"/>
                    <stop offset="0.799" stop-color="#181710"/>
                    <stop offset="1"/>
                </linearGradient>
            </defs>
        </svg>
        <div id=a>
        <?if(isset($b)){?>
            text
            <div id=aS>
                <input type=text>
                <div>
                    <h3>COMMITS</h3>
                    <div>
                        cmts
                    </div>
                </div>
            </div>
        <?}else{?>
            <a id=logo href=commits>
                <svg fill="currentColor"><path d="m59.5 22.4c1.4 1.4 4.9 0 7.7 0 2.8-1.4 0-14 0-21-7.7 0-20.3-2.8-21.7 0 0 2.8-1.4 6.3 0 7.7s4.9 0 7.7 0C.7 65.1.7 58.1.7 61.6c3.5 7.7-1.4 2.8 6.3 6.3 3.5 0-3.5 0 52.5-52.5 0 2.8-1.4 5.6 0 7M46.9 70c1.4 2.8 14.7 0 21.7 0 0-7 2.8-19.6 0-21-2.8 0-6.3-1.4-7.7 0s0 4.2 0 7c-21-18.2-16.1-18.2-18.2-18.2s-6.3 4.2-6.3 6.3 0-2.8 18.2 18.9c-2.8 0-6.3-1.4-7.7 0s0 4.2 0 7M2.1 9.1c0 1.4 0-1.4 22.4 22.4 2.1-.7 4.9-3.5 5.6-5.6C6.3 3.5 9.1 3.5 7.7 3.5 1.4 7 5.6 2.8 2.1 9.1"></path></svg>
            </a>
            <h1>Automatic Labels</h1>
            <h3>Work with our newest feature to create labels whenever, wherever.</h3>
            <a id=gs href="?b=<?=enc(1)?>">Get Started</a>
        <?}?>
        </div>
    </body>
</html>