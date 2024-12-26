<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="handheldfriendly" content="true">
        <meta name="MobileOptimized" content="width">
        <meta name="description" content="BuyToFill">
        <meta name="author" content="">
        <meta name="keywords" content="BuyToFill"> 
        <title>BuyToFill</title>
        <style>
            body{background:#111;margin:0}
            body>div{
                width:50vw;
                height:100vh;
            }
            body>#main{
                background:White;
            }
        </style>
    </head>
    <body>
        <div id=main>
            
        </div>
        <div></div>
    </body>
    <!--body>
        <header>
            <div>
                <svg viewBox="0 0 102 102" xmlns="http://www.w3.org/2000/svg">
                    <path d="m85 32c-2-2 0-6 0-10-80 75-70 75-75 75-11-5-4 2-9-9 0-5 0 5 75-75-4 0-9 2-11 0-2-2 0-7 0-11 2-4 20 0 31 0 0 10 4 28 0 30-4 0-9 2-11 0"></path>
                    <path d="m67 100c0-4-2-8 0-10 2-2 7 0 11 0-26-31-26-24-26-27 0-3 6-9 9-9 3 0-4 0 26 26 0-4-2-8 0-10 2-2 7 0 11 0 4 2 0 20 0 30-10 0-29 4-31 0"></path>
                    <path d="m3 13c5-9-1-3 8-8 2 0-2 0 32 32-1 3-5 7-8 8-32-34-32-30-32-32"></path>
                </svg>
            </div>
            <button id="hp-o" title="mobile-menu" type="button">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill="currentColor" d="m1.6 5h14c.3 0 .5-.2.5-.5 0-.3-.2-.5-.5-.5h-14c-.2 0-.5.2-.5.5 0 .3.3.5.5.5m0 3.5h14c.3 0 .5-.2.5-.5 0-.3-.2-.5-.5-.5h-14c-.2 0-.5.2-.5.5 0 .3.3.5.5.5m0 3.5h14c.3 0 .5-.2.5-.5 0-.3-.2-.5-.5-.5h-14c-.2 0-.5.2-.5.5 0 .3.3.5.5.5"></path></path>
                </svg>
            </button>
            <p>buytofill</p>
            <div id="hp-l">
                <button type="button">BETA</button>
                <!--button type="button">Overview</button>
                <button type="button">Benefits</button>
                <button type="button">Terms & Conditions</button>
                <button type="button">FAQ</button->
            </div>
        </header>
        <main>
            <div>
                <h2>Welcome to BuyToFill</h2>
                <h3>Profit in no time.</h3>
                <form onsubmit="login(this,event)">
                    <div>
                        <input id="email" type="email" name="email" autocomplete="email" placeholder="johndoe@gmail.com" required>
                        <label for="email">Email</label>
                    </div>
                    <div>
                        <input id="password" type="password" name="password" autocomplete="new-password" placeholder="●●●●●●●●" required>
                        <label for="password">Password</label>
                    </div>
                    <button type="submit">Login</button>
                </form>
                <a href="/signup">Don't have an account? <span>Sign Up</span></a>
            </div>
            <aside></aside>
        </main>
        <div id="toast" onclick="this.classList=''">Invalid email or password.</div>
    </body>
    <script>
        let tid;
    
        async function login(a,e) {
            e.preventDefault();
            
            const b = await fetch("#", {method:'POST', body: new FormData(a)});
            const c = await b.json();
            if(!c){
                let toast = document.querySelector("#toast");
                
                if(toast.classList == ""){
                    clearTimeout(tid);
                    toast.classList = "show";
                    tid = setTimeout(() => {toast.classList = ""}, 10000);
                }
            }else window.location.href="deals"
        }
    </script-->
</html>