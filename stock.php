<?php
    session_start();
  
    if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){
        header('Location: .');
        exit;
    }
    
    if (isset($_GET['selected'])) {
        $conn = new mysqli(getenv('DATABASE_HOST'), getenv('DATABASE_USER'), getenv('DATABASE_PASS'), getenv('DATABASE_NAME'));
        if($_GET['selected'] == 'All'){
            $query = "SELECT brand, name, model, upc, stock FROM products";
            $result = $conn->query($query);
        }else{
            if(strpos($_GET['selected'], '+') !== false){
                $brands = explode('+', $_GET['selected']);
                $brands = array_map(function($brand) use ($conn){ return $conn->real_escape_string($brand); }, $brands);
                $inClause = "'" . implode("', '", $brands) . "'";
                $stmt = $conn->prepare("SELECT brand, name, model, upc, stock FROM products WHERE brand IN ($inClause)");
                $stmt->bind_param("s", $inClause);
                $stmt->execute();
                $result = $stmt->get_result();
            }else{
                $stmt = $conn->prepare("SELECT brand, name, model, upc, stock FROM products WHERE brand = ?");
                $stmt->bind_param("s", $_GET['selected']);
                $stmt->execute();
                $result = $stmt->get_result();
            }
        }
        $filename = "products_" . date('Y-m-d') . ".csv";
        ob_clean();
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        $output = fopen('php://output', 'w');
        while ($row = $result->fetch_assoc()){ fputcsv($output, array($row['brand'], $row['name'], $row['model'], $row['upc'], $row['stock'])); }
        fclose($output);
        $conn->close();
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>BuyToFill</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <meta name="handheldfriendly" content="true"/>
        <meta name="MobileOptimized" content="width"/>
        <meta name="description" content="BuyToFill Deals"/>
        <meta name="author" content="BuyToFill"/>
        <meta name="keywords" content="BuyToFill"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <link rel="shortcut icon" type="image/png" href="package/dist/images/logos/favicon.ico"/>
        <link rel="stylesheet" href="package/dist/css/style-dark.min.css"/>
        <link rel="stylesheet" href="main/toast.css">
        <style>
            ::-webkit-scrollbar {
              width: 10px;
            }
            
            ::-webkit-scrollbar-track {
              background: none;
            }
            
            ::-webkit-scrollbar-thumb {
              background: #1A1B1E;
              border-radius: 8px;
              box-shadow: 0px 4px 4px 0px rgba(0, 0, 0, 0.25);
              border: 2px solid #060606;
            }
            .up{
                transform: translateY(0%) !important;
            }
            .down{
                transform: translateY(150%);
            }
            .clicked{
                outline: 2px solid !important;
                outline-offset: 2px;
                transform: scale(.9);
            }
            ::placeholder {
                color: white;
                opacity: 1;
            }
            select{
                padding: .5rem;
            }
        </style>
    </head>
    <body> 
        <div class="preloader">
            <img src="package/dist/images/logos/favicon.ico" alt="loader" class="lds-ripple img-fluid" />
        </div>
        <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
            <?php require("main/nav.php")?>
            <div class="body-wrapper" style="height: 100vh; padding: .4rem; overflow: hidden;">
                <form style="margin-bottom: .2rem; display: flex; gap: .4rem;" onsubmit="event.preventDefault(); window.location.href = '/stock?selected=' + encodeURIComponent(this.querySelector('select').value);" >
                    <select class="form-select" id="selectbrand" name="selectbrands">
                        <option value="All">All</option>
                        <option value="Amazon+Ring+Blink">Amazon + Ring + Blink</option>
                        <option value="Apple">Apple</option>
                        <option value="ASUS">ASUS</option>
                        <option value="Dell">Dell</option>
                        <option value="Google">Google</option>
                        <option value="HP">HP</option>
                        <option value="Lenovo">Lenovo</option>
                        <option value="Meta">Meta</option>
                        <option value="Microsoft">Microsoft</option>
                        <option value="MSI">MSI</option>
                        <option value="Playstation">Playstation</option>
                        <option value="Roku">Roku</option>
                        <option value="Samsung">Samsung</option>
                        <option value="Sony">Sony</option>
                        <option value="Xbox">Xbox</option>
                    </select>
                    <button style="color: #000; font-weight: 600; border: 0; background: #6CEBA5; border-radius: 8px; align-items: center; gap: .2rem; padding: .5rem 1.5rem; display: flex; text-wrap: nowrap;">Download <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg></button>
                </form>
            </div>
        </div>
        <script src="main/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
        <script src="package/dist/libs/simplebar/dist/simplebar.min.js"></script>
        <script src="package/dist/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
        <script src="package/dist/js/app.min.js"></script>
        <script src="package/dist/js/app.dark.init.js"></script>
        <script src="package/dist/js/custom.js"></script>
    </body>
</html>