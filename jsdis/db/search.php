<?php
session_start();

// Enable error reporting (for debugging)
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

$dsn = "mysql:host=127.0.0.1;dbname=jsdistributiondb;charset=utf8mb4";

try {
    // Create a new PDO instance with error mode set to exceptions
    $pdo = new PDO($dsn, getenv('user'), getenv('pass'), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Get the query parameter from the URL and wrap it with wildcards for the LIKE operator
$query = isset($_GET["query"]) ? $_GET["query"] : '';
$queryLike = "%" . $query . "%";

// Prepare the SQL statement using a named parameter
$sql = "SELECT * FROM stock WHERE code LIKE :query OR name LIKE :query";
$stmt = $pdo->prepare($sql);

// Execute the statement by binding the parameter
$stmt->execute(['query' => $queryLike]);

// Fetch all the resulting rows as an associative array
$rows = $stmt->fetchAll();

// Count the number of rows returned
$row_count = count($rows);

$panel = "";

// Build the HTML panel output using the fetched data
foreach ($rows as $row) {
    $panel .= '
        <div class="flex mb-2 overflow-hidden shadow-2xl">
            <div class="flex min-w-[100px] max-w-[100px] max-h-[100px] items-center justify-center bg-[--one] p-4 box-border">
                <img class="max-h-[100px] box-border p-3 max-w-[100px]" src="' . htmlspecialchars($row['img']) . '">
            </div>
            <div class="p-2 box-border flex flex-col justify-between bg-white w-full">
                <div class="transition-[10s ease-in]">
                    <p class="text-black checktext">' . htmlspecialchars($row['name']) . '</p>
                </div>
                <div class="flex">
                    <div class="text-black bg-gray-300 w-fit p-1 px-3 rounded">
                        ' . htmlspecialchars($row['code']) . '
                    </div>
                    <a class="resultinfo bg-gray-300 ml-1 flex items-center px-1 rounded" target="_blank" href="req?db=' . urlencode($row['code']) . '">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="black" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>';
}

// Prepare the JSON response
$response = [
    'row_count' => $row_count,
    'htmltext' => $panel
];

// Output the JSON response
echo json_encode($response);

// Clean up: close the statement and connection
$stmt = null;
$pdo = null;
?>