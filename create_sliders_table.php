<?php
// Include configuration file
require_once 'includes/config.php';

// Connect to database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected to database successfully.<br>";

// Read SQL file
$sql = file_get_contents('database/sliders.sql');

// Execute multi query
if ($conn->multi_query($sql)) {
    do {
        // Store first result set
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());
    
    echo "Sliders table created successfully!<br>";
} else {
    echo "Error creating sliders table: " . $conn->error . "<br>";
}

// Close connection
$conn->close();

echo "Done!";
?> 