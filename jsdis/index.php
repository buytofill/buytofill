<?php 
    session_start();

    echo 'here1';

    $dsn = "mysql:host=127.0.0.1;dbname=jsdistributiondb;charset=utf8mb4";
    $pdo = new PDO($dsn, getenv('user'), getenv('pass'));

    echo 'here';
    exit;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/png" href="./req/img/logo.jpg">
        <script src="https://code.iconify.design/1/1.0.7/iconify.min.js"></script>
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://kit.fontawesome.com/e3893a1793.js" crossorigin="anonymous"></script>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Inclusive+Sans:ital@0;1&display=swap');
            @import url('https://fonts.googleapis.com/css2?family=Kanit:wght@400;500;600;700&display=swap');
            @import url("https://fonts.googleapis.com/css2?family=League+Spartan:wght@400;500;600;700;800;900&display=swap");
            @import url('https://fonts.googleapis.com/css2?family=Alumni+Sans:wght@700&display=swap');
            *{text-decoration: none; color: white; margin: 0; padding: 0; font-family: 'Kanit', sans-serif; transition: transform .5s ease;}
        </style>
        <script src="https://unpkg.com/i18next/dist/umd/i18next.js"></script>
        <style>
            *{margin: 0; padding: 0;}
            :root{
                --one: #FFFFFF;
                --two: #7FB069;
                --three: #0D1B1E;
                --four: #AFA2FF;
                --five: #7A89C2;
            }
            nav > div:not(:last-child){display: flex; margin-right: 1rem;}
            nav div input:hover,#navSign div input:focus{background: #21262D !important;} 
            #card div a{background: white; display: flex; align-items: center; padding: 0.7rem 1rem; border-radius: 10px; font-weight: 600;}
            #card div a:hover{}
            #card div a svg{margin-right: 5px; height: 20px; width: 20px;}
            #card:hover{transform: scale(1.01);}

            #results::-webkit-scrollbar {
                width: 12px;
            }   
            #results::-webkit-scrollbar-track {
                background: transparent;
            }

            #results::-webkit-scrollbar-thumb {
                background-color: white;
                border-radius: 4px;
                border: 2px solid gray;
            }

            .logo path:nth-child(1){fill: #081C15;}
            .logo path:nth-child(2){fill: #52B788;}
            .logo path:nth-child(3){fill: #081C15;}
            .logo path:nth-child(4){fill: #52B788;}
            .logo path:nth-child(5){fill: #081C15;}
            .logo path:nth-child(6){fill: #D8F3DC;}
            @media (max-width: 1000px){
                .perspective-text{
                    font-size: 40px !important;
                    position: relative !important;
                    top: 0 !important;
                    left: 0 !important;
                    transform: translate(0,0) !important;
                }
                .socials{
                    display: none;
                }
                .holding2,.holder{
                    flex-direction: column !important;
                    max-width: 100% !important;
                }
                #searchinv{
                    width: 100% !important;
                    margin-left: 0 !important;
                    border-radius: 1rem;
                    padding-bottom: 2rem;
                }
                .textanimholder{
                    display: flex;
                    justify-content: center; 
                    margin-right: 5rem;
                }
                #next{
                    margin-top: 5rem;
                }
                #next p{
                    color: white;
                    margin-bottom: 1rem;
                }
                #next > div{
                    margin-bottom: .5rem !important;
                }
                #next a{
                    background: white;
                    border-radius: 5px;
                }
                #next a svg{
                    border-radius: 5px;
                    background: white;
                }
                #next a path{
                    color: black;
                }
            }
            @media (max-width: 700px){
                body{
                background: lightgray;
            }
                .perspective-text{
                    font-size: 30px !important;
                    margin-top: 2rem;
                    margin-right: 2rem;
                }
                .firstone button{
                    display: none;
                }
            }
            @media (max-width: 470px){
                .ml-3{
                    margin-left: 0 !important;
                }
                .px-12{
                    padding-left: 1rem !important;
                    padding-right: 1rem !important;
                }
                .px-10{
                    padding-left: 1rem !important;
                    padding-right: 1rem !important;
                }
                .p-7{
                    padding-top: 1.5rem !important;
                }
                nav{
                    height: 0 !important;
                }
                nav a:nth-child(1){
                    margin-left: 0 !important;
                }
            }
            @media (max-width: 400px){
                .perspective-text{
                    letter-spacing: 0px !important;
                }
                nav a, nav p{
                    font-size: 1rem !important;
                }
            }
            #searchicon path{color: gray;}

            #midref a{display: flex; align-items: center; padding: 5px; font-size: 1.5rem;}
        </style> 
        <script>  
            i18next.init({
                    lng: 'en',
                    resources: {
                        en: {
                            translation: {
                                "exploretext": "Explore our wide range of electronics sourced from every corner of the globe, bringing you a selection like never before, to ensure you find exactly what you're searching for.",
                                "visitsocials": "Visit our socials",
                                "signup": "SIGN UP",
                                "signin": "SIGN IN",
                            }
                        },
                        es: {
                            translation: {
                                "exploretext": "Explore nuestra amplia gama de productos electrónicos procedentes de todos los rincones del mundo, ofreciéndole una selección como nunca antes, para asegurarse de encontrar exactamente lo que está buscando.",
                                "visitsocials": "Visita nuestras redes sociales",
                                "signup": "INSCRIBIRSE",
                                "signin": "INICIAR SESIÓN",
                            }
                        }
                    }
                });

                function changeLanguage(lng) {
                    i18next.changeLanguage(lng);
                }
                i18next.on('languageChanged', () => {
                    document.querySelector('#exploretext').textContent = i18next.t('exploretext');
                    document.querySelector('.socials').textContent = i18next.t('visitsocials');
                    document.querySelector('.signup').textContent = i18next.t('signup');
                    document.querySelector('.signin').textContent = i18next.t('signin');
                });  
            document.addEventListener('DOMContentLoaded', () => { 

                const bar = document.getElementById('search-bar')
                fetch(`db/search.php?query=Amazon`)
                            .then(response => response.text())
                            .then(data => {
                                data = JSON.parse(data);
                                document.getElementById('results').innerHTML = data.htmltext})
                            .catch(error => console.error('Error:', error));

                bar.addEventListener('input', (e) => {
                    if(bar.value == ""){
                        document.getElementById('results').innerHTML = `
                        <div class="h-full flex flex-col items-center justify-center p-4">
                            <p class="text-xl text-[--five] font-bold text-center">Need help finding something?</p>
                            <img style="margin: auto; margin-top: 0; margin-bottom: 0; max-height: 80%;" src="req/img/nosearch.webp">
                            <a class="text-xl bg-[--five] text-white font-bold text-center rounded-xl p-2 px-3">CONTACT US</a>
                        </div>`
                        document.querySelector('#searchicon path').style.color = "gray"
                    }else{
                        document.querySelector('#searchicon path').style.color = "black"
                        fetch(`db/search.php?query=${bar.value}`)
                            .then(response => response.text())
                            .then(data => {
                                data = JSON.parse(data);
                                if(data.htmltext==""){
                                    document.getElementById('results').innerHTML = `
                                    <div class="h-full flex flex-col items-center justify-center p-4">
                                        <p class="text-xl text-[--five] font-bold text-center">We couldn't find that item.</p>
                                        <img style="margin: auto; margin-top: 0; margin-bottom: 0; max-height: 80%;" src="req/img/nosearch.webp">
                                        <a class="text-xl bg-[--five] text-white font-bold text-center rounded-xl p-2 px-3">CONTACT US</a>
                                    </div>`
                                }else{
                                    document.getElementById('results').innerHTML = data.htmltext
                                }
                            })
                            .catch(error => console.error('Error:', error));
                    }
                });

                const mask = document.getElementById("grid-mask");
                let lastTime = Date.now();
                let lastX = 0;
                let lastY = 0;

                mask.style.webkitMaskImage = `radial-gradient(circle, rgba(255,255,255,1) 0%, rgba(0,0,0,0) 10%)`;
                window.addEventListener('mousemove', function(event) {
                    const x = event.clientX;
                    const y = event.clientY;

                    const currentTime = Date.now();
                    const deltaTime = currentTime - lastTime;
                    lastTime = currentTime;

                    const speed = Math.sqrt((x - lastX) ** 2 + (y - lastY) ** 2) / deltaTime;

                    const size = Math.min(30, Math.max(10, speed * 1000));

                    mask.style.webkitMaskImage = `radial-gradient(circle at ${x}px ${y}px, rgba(255,255,255,1) 0%, rgba(0,0,0,0) ${size}%)`;

                    lastX = x;
                    lastY = y;
                });
            });
        </script>
    </head>
    <body class="flex flex-col">
        <div id="grid-mask" class="absolute w-full h-full opacity-20 z-[-5]" style="background-image: linear-gradient(#000000 0.9px, transparent 0.9px), linear-gradient(to right, #000000 0.9px, #e5e5f7 0.9px); background-size: 18px 18px;"></div>
        <div id="grid-mask2" class="absolute w-full h-full z-[-6] overflow-hidden">
            <style>
                #gradient-canvas {
                    transform: translate(-10%,-30%) rotate(3deg);
                    border: 10px solid #101827;
                }
            </style>
            <?php require('req/gradientanim.php')?>
        </div>
        <nav class="p-7 flex justify-between w-full px-12 h-[10vh]">
            <div class="items-center firstone">
                <p class="text-[--one] font-black text-xl ml-3">JS DISTRIBUTION</p>
                <button onclick="changeLanguage('en')"><i class="iconify w-10 h-10 mx-5" data-icon="twemoji:flag-england"></i></button>
                <button onclick="changeLanguage('es')"><i class="iconify w-10 h-10" data-icon="emojione-v1:flag-for-spain"></i></button>
            </div> 
            <div class="flex items-center">
                <?php
                    if(isset($_SESSION['user']['role']) && $_SESSION['user']['role'] == "999"){
                        echo '
                        <a href="admin" class="font-black text-xl">DATABASE</a>';
                    }
                ?>
                <div class="flex">
                    <?php
                        if(isset($_SESSION['user']['id'])){
                            echo '<a href="profile" class="font-black text-xl ml-5">PROFILE</a>';
                        }else{
                            echo '<a href="signup.php" class="font-black text-xl signup">SIGN UP</a><a href="login.php" class="font-black text-xl ml-5 signin">SIGN IN</a>';
                        }
                    ?>
                </div>
            </div>
        </nav>
        <div class="flex justify-between h-[85vh] w-full px-10 mb-10 holder">
            <div class="flex h-full justify-between flex-col max-w-[45%] holding2">
                <div class="textanimholder">
                    <?php require('req/textanim.php')?>
                </div>
                <div id="next">
                    <div class="flex flex-row mb-5 items-center justify-between">
                        <p class="text-gray-900 font-black text-3xl socials">Visit our Socials</p>
                        <div class="flex gap-2">
                            <a href="//linkedin.com/in/josh-sham-807126243/" target="_blank">
                                <i class="iconify text-3xl bg-gray-900 p-1 rounded" data-icon="la:linkedin"></i>
                            </a>
                            <a href="//api.whatsapp.com/send/?phone=19292186315&text&type=phone_number&app_absent=0" target="_blank">
                                <i class="iconify text-3xl bg-gray-900 p-[6px] rounded" data-icon="akar-icons:whatsapp-fill"></i>
                            </a>
                            <a href="//instagram.com/jsdistributionofficial" target="_blank">
                                <i class="iconify text-3xl bg-gray-900 p-[6px] rounded" data-icon="akar-icons:instagram-fill"></i>
                            </a>
                            <a href="#" class="bg-gray-900 p-1 rounded flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                                </svg>
                            </a>
                        </div>
                    </div>
                    <p class="text-gray-800 text-xl mb-2" id="exploretext">Explore our wide range of electronics sourced from every corner of the globe, bringing you a selection like never before, to ensure you find exactly what you're searching for.</p>
                </div>
            </div>
            <div class="h-full w-1/2 ml-5 flex justify-center flex-col" id="searchinv">
                <div class="p-2 overflow-hidden rounded-xl mb-2 searchinvbg" style="background: hsla(0,0%,100%,.2);">
                    <div class="flex bg-white rounded-lg items-center overflow-hidden mb-[2px]">
                        <svg id="searchicon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 pl-1 mx-1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                        <input type="text" id="search-bar" class="outline-none w-full p-1 box-border text-black" placeholder="Search our inventory"/> 
                    </div>
                </div>
                <div class="p-2 h-full overflow-hidden rounded-xl" style="background: hsla(0,0%,100%,.2);">
                    <div id="results" class="overflow-y-scroll p-2 bg-white rounded-lg h-full" style="box-shadow: 0 0 10px gray;">
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

