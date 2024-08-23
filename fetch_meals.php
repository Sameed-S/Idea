<?php
header('Content-Type: application/json');

// Connect to the SQLite database
$db_file = '.db'; // Ensure this is the correct path to your SQLite database
$conn = new SQLite3($db_file);

if (!$conn) {
    echo json_encode(['error' => 'Database connection failed.']);
    exit();
}

// Query to fetch all meals
$query = 'SELECT * FROM meals';
$result = $conn->query($query);

$meals = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $meals[] = $row;
}

// Check if meals were retrieved
if (empty($meals)) {
    echo json_encode(['error' => 'No meals found in the database.']);
    exit();
}

// Return the meals as JSON
echo json_encode(['meals' => $meals]);

// Close the database connection
$conn->close();
?>
