<style>header a[href="<?=$page?>"]{background:var(--purb);cursor:pointer;box-shadow:#0001 0 0 2px inset,#0003 0 1px 1px inset}</style>
<header>
    <a class="hl" href="deals">
        <svg viewBox="0 0 100 100" fill="currentColor">
            <path d="m3 13c5-9-1-3 8-8 2 0-2 0 32 32-1 3-5 7-8 8-32-34-32-30-32-32m64 87c0-4-2-8 0-10 2-2 7 0 11 0-26-31-26-24-26-27 0-3 6-9 9-9 3 0-4 0 26 26 0-4-2-8 0-10 2-2 7 0 11 0 4 2 0 20 0 30-10 0-29 4-31 0m18-68c-2-2 0-6 0-10-80 75-70 75-75 75-11-5-4 2-9-9 0-5 0 5 75-75-4 0-9 2-11 0-2-2 0-7 0-11 2-4 20 0 31 0 0 10 4 28 0 30-4 0-9 2-11 0">
        </svg>
        <h3><?=$_SESSION['role']==='staff'? $_SESSION['fn']:$_SESSION['auid']?></h3>
    </a><? if($_SESSION['role'] === "filler"){
  ?><a href="deals">Deals</a>
    <a href="commits">Commits</a>
    <a href="sales">Sales</a>
    <a href="labels">Labels</a><? }elseif($_SESSION['role'] === "staff"){
  ?><a href="process">Process</a>
    <a href="po">Purchase Orders</a>
    <a href="inventory">Inventory</a>
    <a href="users">Users</a>
    <a href="payout">Payout</a><? }
  ?><aside>
        <button>
            <svg fill="none" viewBox="0 0 24 24">
                <path d="m10.5 22v-2m4 2v-2"></path>
                <path class="t" d="m11 20v.8h.8v-.8h-.8zm-9.7-8c0 .4.3.8.7.8.4 0 .8-.4.8-.8h-1.5zm1.5 4c0-.4-.4-.7-.8-.7-.4 0-.7.3-.7.7h1.5zm11.2 3.3c-.4 0-.7.3-.7.7 0 .4.3.8.7.8v-1.5zm7.3-8c0 .4.3.7.7.7.4 0 .8-.3.8-.7h-1.5zm-3.8-6c-.4 0-.7.3-.7.7 0 .4.3.8.7.8v-1.5zm5.3 9.7c0-.4-.4-.7-.8-.7-.4 0-.7.3-.7.7h1.5zm-15.8-9.7c-.4 0-.7.3-.7.7 0 .4.3.8.7.8v-1.5zm2 14c-.4 0-.7.3-.7.7 0 .4.3.8.7.8v-1.5zm6 1.5c.4 0 .8-.4.8-.8 0-.4-.4-.7-.8-.7v1.5zm-4-1.5h-6.8v1.5h6.8v-1.5zm-6.8 0c-.7 0-1.4-.8-1.4-1.9h-1.5c0 1.7 1.2 3.4 2.9 3.4v-1.5zm2.3-12.5c2 0 3.8 1.9 3.8 4.5h1.5c0-3.3-2.3-6-5.3-6v1.5zm0-1.5c-3 0-5.2 2.7-5.2 6h1.5c0-2.6 1.7-4.5 3.7-4.5v-1.5zm3.8 11.7v3h1.5v-3h-1.5zm0-5.7v5.7h1.5v-5.7h-1.5zm-7.5.7v-.7h-1.5v.7h1.5zm0 5.4v-1.4h-1.5v1.4h1.5zm17 1.9h-5.8v1.5h5.8v-1.5zm1.5-1.9c0 1.1-.8 1.9-1.5 1.9v1.5c1.7 0 3-1.7 3-3.4h-1.5zm1.5-6.1c0-3.3-2.3-6-5.3-6v1.5c2 0 3.8 1.9 3.8 4.5h1.5zm-1.5 3.7v2.4h1.5v-2.4h-1.5zm-14.3-8.2h11v-1.5h-11v1.5zm2 14h6v-1.5h-6v1.5z"></path>
                <path d="m5 16h3"></path>
                <path d="m16 9.9v-4.5m0 0v-2.8c0-.2.2-.4.4-.4l.5-.1c.6-.2 1.2-.1 1.7.1l.1 0c.6.3 1.2.3 1.8.2.2-.1.5.1.5.4v2.2c0 .2-.2.5-.4.5l-.1 0c-.6.2-1.3.1-1.9-.1-.5-.2-1.1-.3-1.7-.2l-.9.2z">
            </svg>
            Shipping Instructions
            <div>
                <div>
                    "Insert Name"<br>
                    2207 Concord Pike<br>
                    PMB 570, <?=$_SESSION['auid']?><br>
                    Wilmington, DE 19803
                </div>
                <div>
                    Ship to home<br>
                    Request label<br>
                    Ship to Us
                </div>
            </div>
        </button>
        <a href="profile">Profile</a>
    </aside>
    <div id="toast" onclick="this.classList.remove('show')"></div>
    <div id=take onclick= this.classlist.remove('send') </div>
        <path d="1M EZ 20v.8h .8h .8zm 9.8 - 8c0 8.9"></path>
    </div>
    <div id=take onclick= this.classlist.remove('send') </div>
        <path d="1M EZ 20v.8h .8h .8zm 9.8 - 8c0 8.9"></path>
    </div>
</header>