<?php
    session_start();
    if(!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] != "999"){
        header('Location: ..');
        exit;
    }
?>
<?php require('../req/head.php')?>
    <title>JSD - Admin Panel</title>
    <style>
        .ap,p{color: white; display: flex; align-items: center; padding-bottom: 10px;}
        svg{margin-right: 10px; height: 25px; width: 25px;}
        svg path{color: white;}
        .adprod{background: #12181f;}
        .adprod img{max-height: 50px; margin: auto;}
        #adres::-webkit-scrollbar {
            display: none;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', () => { 
            fetch(`att/search.php?query=amazon`)
                        .then(response => response.text())
                        .then(data => {
                            data = JSON.parse(data);
                            document.getElementById('adres').innerHTML = data.htmltext})
                        
            document.querySelector("#search-bar").addEventListener('input', (e) => {
                fetch(`att/search.php?query=` + document.querySelector("#search-bar").value)
                        .then(response => response.text())
                        .then(data => {
                            data = JSON.parse(data);
                            document.getElementById('adres').innerHTML = data.htmltext})
            });
        });
    </script>
</head>
<body style='height: 100vh;'>
    <div id="grid-mask2" class="absolute w-full h-full z-[-6] overflow-hidden">
        <?php require('../req/gradientanim.php')?>
    </div>
    <div style="border: 1px solid #eceff133; width: 80vw; display: flex; height: 80vh; border-radius: 20px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: hsla(0,0%,100%,.2);">
        <div style="width: 200px; box-sizing: border-box; border-right: 1px solid #eceff133;">
            <p style="color: white; padding: 15px;"><i class="iconify" data-icon="fluent:fluid-16-filled"></i><?php echo $_SESSION['user']['id']?></p>
            <div style="display: flex; padding: 0 15px; flex-direction: column; border-top: 1px solid #eceff133;">
                <a class="ap" href="?users"><i class="iconify" data-icon="mdi:users"></i>Users</a>
                <a class="ap" href="?stock"><i class="iconify" data-icon="bi:upc-scan"></i>Stock</a>
                <a class="ap" href="?logs"><i class="iconify" data-icon="carbon:flow-logs-vpc"></i>Logs</a>
            </div>
        </div>
        <div style="width: 100%; padding: 20px; box-sizing: border-box;">
            <?php
                if(isset($_GET['stock'])){
                    require('att/searchbar.php');
                }
            ?>
        </div>
    </div>
</body>
</html>