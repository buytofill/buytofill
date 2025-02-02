<?php
    session_start();

    $conn = new mysqli('198.12.245.3', 'support', 'g21DEV21#work', 'jsdistributiondb');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $query = $_GET["query"];
    $query = "%" . $query . "%";

    $stmt = $conn->prepare("SELECT * FROM stock WHERE code LIKE ? OR name LIKE ?");
    $stmt->bind_param("ss", $query, $query);
    $stmt->execute();

    $result = $stmt->get_result();
    $row_count = $result->num_rows;

    $panel = "";

    while ($row = $result->fetch_assoc()){
        $panel .= '
        <div class="flex mb-2 overflow-hidden shadow-2xl">
            <div class="flex min-w-[100px] max-w-[100px] max-h-[100px] items-center justify-center bg-[--one] p-4 box-border">
                <img class="max-h-[100px] box-border p-3 max-w-[100px]" src="'.$row['img'].'">
            </div>
            <div class="p-2 box-border flex flex-col justify-between bg-white w-full">
                <div class="transition-[10s ease-in]">
                    <p class="text-black checktext">'.$row['name'].'</p>
                </div>
                <div class="flex">
                    <div class="text-black bg-gray-300 w-fit p-1 px-3 rounded">
                        '.$row['code'].'
                    </div>
                    <a class="resultinfo bg-gray-300 ml-1 flex items-center px-1 rounded" target="_blank" href="req?db='.$row['code'].'">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="black" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>';
    }
    $response = array(
        'row_count' => $row_count,
        'htmltext' => $panel
    );

    echo json_encode($response);

    $stmt->close();
    $conn->close();
?>
