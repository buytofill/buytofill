<?php
session_start();

if(isset($_GET['db'])){
    $a = $_GET['db'];

    $conn = new mysqli('198.12.245.3', 'support', 'g21DEV21#work', 'jsdistributiondb');

    $query = $_GET["db"];
    $query = "%" . $query . "%";

    $stmt = $conn->prepare("SELECT * FROM stock WHERE code LIKE ?");
    $stmt->bind_param("s", $query);
    $stmt->execute();

    $result = $stmt->get_result();

    $name = "";
    $img = "";
    $desc = "";
    $brand = "";
    while ($row = $result->fetch_assoc()){
        $name = $row['name'];
        $img = $row['img'];
        $desc = $row['description'];
        $brand = $row['brand'];
    }
}else{
    echo "nerd";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require('head.php')?>
    <title>JS Distribution</title>
    <script>    
        document.addEventListener('DOMContentLoaded', () => {
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
    <style>
            path {
                stroke-dasharray: 2333px;
                stroke-dashoffset: 2333px;
                animation: anim 50s ease forwards;
            }
            @keyframes anim {
                to {
                    stroke-dashoffset: 0;
                }
            }
        </style>
</head>
<body class="flex flex-col">
    <div id="grid-mask" class="absolute w-full h-full opacity-20 z-[-5]" style="background-image: linear-gradient(#000000 0.9px, transparent 0.9px), linear-gradient(to right, #000000 0.9px, #e5e5f7 0.9px); background-size: 18px 18px;"></div>
    <div id="grid-mask2" class="absolute w-full h-full z-[-6] overflow-hidden">
        <?php require('gradientanim.php')?>
    </div>
    <nav class="p-7 flex justify-between w-full px-12 h-[10vh]">
        <a class="flex items-center" href="../">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="black" class="w-6 h-6">
                <path d="M18.75 19.5l-7.5-7.5 7.5-7.5m-6 15L5.25 12l7.5-7.5" />
            </svg>
            <svg  xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 100 24" stroke-width="1" stroke="black" class="h-7 mt-2 ml-1">
                <path d="M 52.896 15.696 L 47.088 15.696 L 47.088 0.24 L 52.896 0.24 L 52.896 4.56 L 56.28 0.24 L 62.832 0.24 L 57.552 6.648 L 63.312 15.696 L 57.168 15.696 L 54 10.32 L 52.896 11.664 L 52.896 15.696 Z M 45.84 0.84 L 45.84 5.904 Q 43.656 4.896 41.352 4.896 A 6.942 6.942 0 0 0 40.302 4.971 Q 39.747 5.056 39.294 5.238 A 3.294 3.294 0 0 0 38.688 5.556 Q 37.704 6.216 37.704 7.968 A 4.75 4.75 0 0 0 37.775 8.822 Q 37.972 9.895 38.712 10.38 A 3.705 3.705 0 0 0 39.784 10.848 Q 40.267 10.979 40.84 11.02 A 7.711 7.711 0 0 0 41.4 11.04 Q 43.704 11.04 45.888 10.032 L 45.888 15.096 Q 44.784 15.48 43.644 15.708 A 11.074 11.074 0 0 1 42.499 15.872 Q 41.904 15.927 41.236 15.935 A 18.92 18.92 0 0 1 41.016 15.936 A 16.503 16.503 0 0 1 38.443 15.749 Q 37.093 15.535 36.011 15.078 A 7.132 7.132 0 0 1 34.116 13.92 Q 31.855 11.972 31.779 8.233 A 12.917 12.917 0 0 1 31.776 7.968 Q 31.776 4.032 34.092 2.016 A 7.342 7.342 0 0 1 36.6 0.62 Q 37.692 0.252 39.027 0.102 A 17.457 17.457 0 0 1 40.968 0 A 17.536 17.536 0 0 1 42.203 0.041 Q 42.955 0.095 43.596 0.216 Q 44.736 0.432 45.84 0.84 Z M 20.496 15.696 L 14.448 15.696 L 20.4 0.24 L 26.424 0.24 L 32.376 15.696 L 26.328 15.696 L 25.224 12.816 L 21.6 12.816 L 20.496 15.696 Z M 9.264 15.696 L 0 15.696 L 0 0.24 L 9.168 0.24 Q 10.892 0.24 12.054 0.834 A 4.014 4.014 0 0 1 12.852 1.368 A 3.652 3.652 0 0 1 14.039 3.412 A 5.564 5.564 0 0 1 14.16 4.608 A 5.631 5.631 0 0 1 14.095 5.489 Q 14.015 5.993 13.837 6.414 A 3.344 3.344 0 0 1 13.764 6.576 A 3.775 3.775 0 0 1 13.256 7.368 A 3.166 3.166 0 0 1 12.72 7.872 A 4.409 4.409 0 0 1 13.53 8.363 A 3.464 3.464 0 0 1 14.16 8.988 A 2.206 2.206 0 0 1 14.502 9.652 Q 14.604 9.961 14.65 10.34 A 5.598 5.598 0 0 1 14.688 11.016 A 5.367 5.367 0 0 1 14.491 12.508 A 3.994 3.994 0 0 1 13.26 14.448 A 4.716 4.716 0 0 1 11.484 15.403 Q 10.782 15.614 9.943 15.673 A 9.656 9.656 0 0 1 9.264 15.696 Z M 5.448 9.768 L 5.448 11.76 L 7.608 11.76 A 1.873 1.873 0 0 0 8.043 11.714 Q 8.627 11.574 8.735 11.008 A 1.491 1.491 0 0 0 8.76 10.728 A 1.132 1.132 0 0 0 8.69 10.314 Q 8.477 9.768 7.608 9.768 L 5.448 9.768 Z M 5.448 4.176 L 5.448 6.168 L 7.608 6.168 A 1.873 1.873 0 0 0 8.043 6.122 Q 8.627 5.982 8.735 5.416 A 1.491 1.491 0 0 0 8.76 5.136 A 1.132 1.132 0 0 0 8.69 4.722 Q 8.477 4.176 7.608 4.176 L 5.448 4.176 Z M 23.4 5.904 L 22.224 8.856 L 24.576 8.856 L 23.4 5.904 Z" vector-effect="non-scaling-stroke"/>
            </svg>
        </a>
    </nav>
    <div class="h-[80vh] flex flex-col items-center justify-center">
        <div class="w-[60%] h-fit my-div rounded-3xl bg-white" style="outline: 10px solid hsla(0,0%,100%,.2);">
            <div class="p-10">
                <div class="flex">
                    <div class="min-w-[300px] w-[300px] min-h-[300px] h-[300px]">
                        <img style="width: 100%;" src="<?php echo $img?>" alt="">
                    </div>
                    <div class="flex flex-col justify-between">
                        <div>
                            <p class="text-gray-900 pl-10 font-black text-lg">Brand: <?php echo $brand?></p>
                            <p class="text-black pl-10 font-black text-2xl"><?php echo $name?></p>
                            <p class="text-gray-900 pl-10 text-md">UPC: <?php echo $a?></p>
                        </div>
                        <p class="text-black pl-10 text-xl"><?php echo $desc?></p>
                    </div>
                </div>
            </div>
        </div>
        <a href="https://api.whatsapp.com/send/?phone=19292186315&text&type=phone_number&app_absent=0" target="_blank" class="text-white w-[60%] bg-[#101827] block p-4 mt-10 rounded-2xl text-center" style="outline: 10px solid hsla(0,0%,100%,.2);">Contact Us</a>
    </div>
</body>
</html>

