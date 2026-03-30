<?php
date_default_timezone_set("Africa/Kigali");
// Set response type to JSON
header("Content-Type: application/json");

// Database configuration
$host = "localhost";
$db_name = "sensor";
$username = "root";      // change if needed
$password = "";          // change if needed

// Create MySQL connection
$conn = new mysqli($host, $username, $password, $db_name);

// Check connection
if ($conn->connect_error) {
    die(json_encode([
        "status" => "error",
        "message" => "Connection failed: " . $conn->connect_error
    ]));
}

// Get raw POST data
$data = json_decode(file_get_contents("php://input"), true);

// Validate input
if (!isset($data['temperature']) || !isset($data['humidity'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid input"
    ]);
    exit();
}

$temperature = $data['temperature'];
$humidity = $data['humidity'];

// Prepare SQL statement (prevents SQL injection)
$stmt = $conn->prepare("INSERT INTO sensor_data (temperature, humidity) VALUES (?, ?)");
$stmt->bind_param("dd", $temperature, $humidity);

// Execute query
if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Data inserted successfully"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Insert failed"
    ]);
}

// Close connection
$stmt->close();
$conn->close();
?>