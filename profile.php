<?
    require 'assets/control.php';
    
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($_POST['ahn'], $_POST['ban'], $_POST['rn'], $_POST['bat'], $_POST['address'], $_POST['state'], $_POST['city'], $_POST['zip'])){
            $conn = new mysqli(getenv('DATABASE_HOST'), getenv('DATABASE_USER'), getenv('DATABASE_PASS'), getenv('DATABASE_NAME'));
            
            $pds = $_POST['bat'];
            $uid = $_SESSION['uid'];
            if($pds == "b") $pds = 0;
            else $pds = 1;
            $stmt = $conn->prepare("UPDATE filler SET ahn = ?,ban = ?,rn = ?,bat = ?,address = ?,state = ?, city = ?, zip = ? WHERE id = ?");
            $stmt->bind_param("sssissssi", $_POST['ahn'], $_POST['ban'], $_POST['rn'], $pds, $_POST['address'], $_POST['state'], $_POST['city'], $_POST['zip'], $uid);
            $stmt->execute();
            $stmt->close();
            $conn->close();
            
            o('Updated Profile');
        }
        exit;
    }
    if($_SERVER["REQUEST_METHOD"] == "GET"){
        if(isset($_GET['logout'])){
            $_SESSION = array();
            session_destroy();
            header('Location: .');
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
            #container{background:#474bff77;height:calc(100% - 2rem);width:500px;border-radius:.4rem;margin:1rem auto 0;border:1px solid var(--purp)}
            
            form{padding:1rem;display:flex;flex-direction:column;height:100%;justify-content:space-between}
            form>div{color:#888;padding-bottom:2rem}
            label,input,select{width:100%}
            .form>div{margin-bottom:.4rem}
            .form>div:has(div){display:flex}
            .form>div>div{width:calc(50% - .2rem)}
            .form>div>div:first-child{margin-right:.2rem}
            .form>div>div:last-child{margin-left:.2rem}
            input,select{border:1px solid #444;border-radius:.2rem;appearance:none;color:#666;background:#131216;outline:0;padding:.5rem}
            label{display:block;margin:0 -1rem 0 .1rem;font-weight:400;font-size:.8rem;height:20px;color:#777}
            input:hover,input:focus,select:hover,select:focus{color:#888;border-color:#666}
            div:has(>input:hover)>label,div:has(>input:focus)>label,div:has(>select:hover)>label,div:has(>select:focus)>label{color:#aaa}
            form button{padding:.8rem;background:var(--purp);border-radius:10rem;cursor:pointer;color:#fff;font-weight:700;
        </style>
    </head>
    <body>
        <?require 'assets/header.php'?>
        <nav>
            <main>
                <a href="#" class="y">Payment Information</a>
            </main>
            <div>
                <a href="?logout">Log Out</a>
            </div>
        </nav>
        <main>
            <div id="container"><?
                if($_SESSION['role'] == "filler"){
              ?><form onsubmit="update(this,event)">
                    <div class="form"><?  
                    
                        $uid = $_SESSION['uid'];
                        $email = $_SESSION['email'];
                        $conn = new mysqli(getenv('DATABASE_HOST'), getenv('DATABASE_USER'), getenv('DATABASE_PASS'), getenv('DATABASE_NAME'));
                        $stmt = $conn->prepare("SELECT ahn,ban,rn,bat,address,state,city,zip FROM filler WHERE id = ?");
                        $stmt->bind_param("i", $uid);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        while ($row = $result->fetch_assoc()){
                            
                      ?><div>
                            <label for="ahn">Account Holder Name</label>
                            <input id="ahn" name="ahn" value="<?=$row['ahn']??''?>" placeholder="John Doe" type="text" minlength="2" maxlength="50" autocomplete="off" required>
                        </div>
                        <div>
                            <div>
                                <label for="ban">Bank Account Number</label>
                                <input id="ban" name="ban" value="<?=$row['ban']??''?>" type="password" placeholder="123456789" minlength="8" maxlength="17" autocomplete="new-password" required>
                            </div>
                            <div>
                                <label for="rn">Routing Number</label>
                                <input id="rn" name="rn" value="<?=$row['rn']??''?>" type="password" minlength="9" maxlength="9" placeholder="123456789" autocomplete="new-password" required>
                            </div>
                        </div>
                        <div>
                            <label for="bat">Bank Account Type</label>
                            <select id="bat" name="bat" required>
                                <option <?=($row['bat']??false)?'':'selected'?>></option>
                                <option value="b" <?=($row['bat']??null)===0?'selected':'';?>>Business</option>
                                <option value="p" <?=($row['bat']??null)===1?'selected':'';?>>Personal</option>
                            </select>
                        </div>
                        <div>
                            <div>
                                <label for="add">Address</label>
                                <input id="add" name="address" value="<?=$row['address']??''?>" placeholder="5 Inter Cir, Apt 201" type="text" minlength="2" maxlength="50" autocomplete="off" required>
                            </div>
                            <div>
                                <label for="cit">City</label>
                                <input id="cit" name="city" value="<?=$row['city']??''?>" placeholder="New Hyde Park" type="text" minlength="2" maxlength="50" autocomplete="off" required>
                            </div>
                        </div>
                        <div>
                            <div>
                                <label for="sta">State</label>
                                <input id="sta" name="state" value="<?=$row['state']??''?>" placeholder="NY" type="text" minlength="2" maxlength="2" autocomplete="off" required>
                            </div>
                            <div>
                                <label for="zip">ZIP Code</label>
                                <input id="zip" name="zip" value="<?=$row['zip']??''?>" placeholder="11040" type="text" minlength="5" maxlength="5" autocomplete="off" required>
                            </div>
                        </div><?  
                        }
                        $stmt->close();
                        $conn->close();
                  ?></div>
                    <button type="submit">Save Information</button>
                </form><?
                }
          ?></div>
        </main>
    </body>
    <script>
        let tid; 
        let toast = document.querySelector("#toast");
        
        async function update(a,e) {
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