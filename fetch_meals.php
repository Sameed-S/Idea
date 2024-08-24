<?php
// Include database connection file
include 'connect.php';

// API URL
$apiUrl = 'https://www.themealdb.com/api/json/v1/1/search.php?f=';

// Fetch data from the API
$response = file_get_contents($apiUrl);
if ($response === FALSE) {
    die('Error fetching data from API');
}

// Decode JSON data
$data = json_decode($response, true);
if ($data === NULL) {
    die('Error decoding JSON data');
}

// Prepare SQL statement
$sql = "
    INSERT INTO meals (idMeal, strMeal, strDrinkAlternate, strCategory, strArea, strInstructions, strMealThumb, strTags, strYoutube, strSource, strImageSource, strCreativeCommonsConfirmed, dateModified)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
";

$stmt = mysqli_prepare($con, $sql);

if (!$stmt) {
    die('Error preparing statement: ' . mysqli_error($con));
}

// Insert each meal into the database
foreach ($data['meals'] as $meal) {
    mysqli_stmt_bind_param($stmt, 'sssssssssssss', 
        $meal['idMeal'],
        $meal['strMeal'],
        $meal['strDrinkAlternate'],
        $meal['strCategory'],
        $meal['strArea'],
        $meal['strInstructions'],
        $meal['strMealThumb'],
        $meal['strTags'],
        $meal['strYoutube'],
        $meal['strSource'],
        $meal['strImageSource'],
        $meal['strCreativeCommonsConfirmed'],
        $meal['dateModified']
    );
    
    if (!mysqli_stmt_execute($stmt)) {
        echo 'Error executing statement: ' . mysqli_stmt_error($stmt) . '<br>';
    }
}

mysqli_stmt_close($stmt);

// Fetch the data to send to the frontend
$result = mysqli_query($con, "SELECT * FROM meals");

if (!$result) {
    die('Error fetching data from database: ' . mysqli_error($con));
}

$meals = array();
while ($row = mysqli_fetch_assoc($result)) {
    $meals[] = $row;
}

// Close the database connection
mysqli_close($con);

// Send the data as JSON
header('Content-Type: application/json');
echo json_encode($meals);
?>