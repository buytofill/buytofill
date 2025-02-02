<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="handheldfriendly" content="true">
    <meta name="MobileOptimized" content="width">
    <title>BuyToFill - Create Account</title>
    <link rel="icon" href="assets/logo.svg">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Righteous&display=swap');
        body{background:linear-gradient(rgb(255, 255, 255) 32%, rgb(244, 242, 240) 100%);margin:0;padding:0}
        h1,h2,h3,h4,h5,h6,p,a{font-family:'Inter',sans-serif;color:#282828}
        span{font-family:'Inter',sans-serif}
        a{cursor:pointer;font-weight:500;text-decoration:none;color:#282828}
        #logo{display:flex;position:absolute;top:3rem;left:3rem;color:#282828}
        #logo span{padding-top:.1rem;font-size:1.5rem;margin:auto 0 auto 1rem;font-family:'Righteous',serif}
        #logo svg{width:2rem}
        html, body {
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        #base{height:100vh;display:flex}
        header{margin:auto 1rem}
        aside{width:clamp(20rem, 100%, 30vw);margin:auto;padding:2rem}
        aside>div{max-width:clamp(3rem, 100%, 25rem);margin:auto}
        aside>div>a{display:flex;box-sizing:border-box;border:1px solid transparent;margin:auto;background:#282828;font-size:.8rem;color:#fff;padding:.6rem;border-radius:.4rem}
        aside>div>a>span{text-align:center;width:100%}
        main{flex:1}
        aside>div>a:not(:last-child){margin-bottom:1rem}
        #mail{background:#fff;color:#282828;border-color:rgb(211, 214, 223)}
        aside h1,aside h4{margin:auto auto 1rem auto;width:fit-content}
        aside h1{font-weight:500;font-size:2.5rem}
        aside h4{font-weight:400;font-size:.8rem;color:rgb(89, 91, 96);margin-bottom:2rem}
        aside label{display:flex;padding:.5rem;cursor:pointer}
        aside label input{opacity:0;position:absolute}
        aside label div{display:flex;background-color:rgb(255, 255, 255);border:1px solid rgb(211, 214, 223);-webkit-box-align:center;align-items:center;-webkit-box-pack:center;justify-content:center;position:relative;min-width:16px;min-height:16px;max-width:16px;max-height:16px;border-radius:4px;color:transparent}
        aside label:has(input:checked) div{background-color:#282828;color:#fff}
        aside label span{color:rgb(89, 91, 96);font-weight:400;font-size:.8rem;margin-left:.5rem}
        header svg{color:rgb(141, 144, 151)}
        header a{display:flex}
        header a:not(:last-child){margin-bottom:1.5rem}
        main{padding:2rem}
        #top{display:flex;justify-content:space-between;padding:1.5rem 0}
        #top span{font-weight:700;font-size:.8rem;color:#aaa}
        #top div a{background:#eee;padding:.5rem 1rem;border-radius:.4rem;font-weight:500;font-size:.9rem;color:#282828}
        #top div a:last-child{margin-left:.5rem;background:#282828;color:#fff}
        #filters{display:flex;justify-content:space-between;padding:1rem 0}

    </style>
</head>
<body>
    <a id=logo href="https://buytofill.com">
        <svg viewBox="0 0 16.4 16">
            <path fill="#282828" d="m14 5.3c.3.3 1.2 0 1.8 0 .6-.3 0-3.3 0-4.9-1.8 0-4.8-.7-5.1 0 0 .6-.3 1.4 0 1.8s1.1 0 1.7 0c-12.3 13-12.3 11.4-12.3 12.3.8 1.8-.3.7 1.5 1.5.8 0-.8 0 12.4-12.4 0 .7-.3 1.3 0 1.7m-3.1 11.1c.4.7 3.5 0 5.1 0 0-1.6.7-4.6 0-4.9-.6 0-1.4-.3-1.8 0s0 .9 0 1.7c-4.9-4.4-3.7-4.4-4.2-4.4s-1.5 1.1-1.5 1.6 0-.8 4.3 4.3c-.6 0-1.4-.3-1.8 0s0 1-.1 1.7m-10.3-14.3c0 .3 0-.4 5.3 5.2.5-.1 1.1-.8 1.3-1.3-5.6-5.2-4.9-5.2-5.3-5.2-1.5.8-.5-.2-1.3 1.3"/>
        </svg>
        <span>buytofill</span>
    </a>
    <div id=base>
        <aside>
            <h1>Welcome back!</h1>
            <h4>Select your preferred login method to continue</h4>
            <div>
                <a><span>Continue with Google</span></a>
                <a><span>Continue with Microsoft</span></a>
                <a id=mail><span>Continue with Email</span></a>
                <label>
                    <input type="checkbox">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width=".7em" height="1em"viewBox="0 0 256 230">
                            <polyline points="216 72 104 184 48 128" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="24"></polyline>
                        </svg>
                    </div>
                    <span>Remember me</span>
                </label>
            </div>
        </aside>
        <header>
            <a>
                <svg width="1.4rem" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16.6725 16.6412L21 21M19 11C19 15.4183 15.4183 19 11 19C6.58172 19 3 15.4183 3 11C3 6.58172 6.58172 3 11 3C15.4183 3 19 6.58172 19 11Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
            <a>
                <svg width="1.4rem" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.5 18C8.32843 18 9 18.6716 9 19.5C9 20.3284 8.32843 21 7.5 21C6.67157 21 6 20.3284 6 19.5C6 18.6716 6.67157 18 7.5 18Z" stroke="currentColor" stroke-width="2"/>
                    <path d="M16.5 18.0001C17.3284 18.0001 18 18.6716 18 19.5001C18 20.3285 17.3284 21.0001 16.5 21.0001C15.6716 21.0001 15 20.3285 15 19.5001C15 18.6716 15.6716 18.0001 16.5 18.0001Z" stroke="currentColor" stroke-width="2"/>
                    <path d="M2 3L2.26121 3.09184C3.5628 3.54945 4.2136 3.77826 4.58584 4.32298C4.95808 4.86771 4.95808 5.59126 4.95808 7.03836V9.76C4.95808 12.7016 5.02132 13.6723 5.88772 14.5862C6.75412 15.5 8.14857 15.5 10.9375 15.5H12M16.2404 15.5C17.8014 15.5 18.5819 15.5 19.1336 15.0504C19.6853 14.6008 19.8429 13.8364 20.158 12.3075L20.6578 9.88275C21.0049 8.14369 21.1784 7.27417 20.7345 6.69708C20.2906 6.12 18.7738 6.12 17.0888 6.12H11.0235M4.95808 6.12H7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </a>
        </header>
        <main>
            <div id=top>
                <span>BROWSE</span>
                <div>
                    <a>Manage</a>
                    <a>Invite</a>
                </div>
            </div>
            <h1>Stocklist</h1>
            <div id=filters>
                <div>
                    <a>Filter</a>
                </div>
                <div>
                    <a>Find</a>
                </div>
            </div> 
            <table>
            </table>
        </main>
    </div>
</body>
</html>