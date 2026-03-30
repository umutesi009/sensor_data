<?php
header('Content-Type: application/json');

// --- Database connection ---
$host = "localhost";
$db_name = "sensor";   // your database name
$username = "root";    // your MySQL username
$password = "";        // your MySQL password

$conn = new mysqli($host, $username, $password, $db_name);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// --- Get JSON input from POST ---
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['temperature']) || !isset($input['humidity'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$temperature = floatval($input['temperature']);
$humidity = floatval($input['humidity']);

// --- Insert into table ---
$stmt = $conn->prepare("INSERT INTO sensor_data (temperature, humidity) VALUES (?, ?)");
$stmt->bind_param("dd", $temperature, $humidity);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Data inserted successfully',
        'inserted' => [
            'temperature' => $temperature,
            'humidity' => $humidity,
            'id' => $stmt->insert_id
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Insert failed']);
}

$stmt->close();
$conn->close();
?>