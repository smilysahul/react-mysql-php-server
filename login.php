<?php
header('Content-Type: application/json');

// Allow all origins (CORS)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: *');

// Respond to preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Database configuration
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "intern";
    $port = 3307;

    // Create database connection
    $conn = new mysqli($servername, $username, $password, $database, $port);

    if ($conn->connect_error) {
        $response = [
            'success' => false,
            'message' => 'Connection failed: ' . $conn->connect_error,
        ];
        echo json_encode($response);
        exit();
    }

    // Get data from the request
    $data = json_decode(file_get_contents("php://input"));

    // Check for valid data
    if (!empty($data->username) && !empty($data->password)) {
        $username = $conn->real_escape_string($data->username);
        $password = $conn->real_escape_string($data->password);

        // Query the database
        $sql = "SELECT * FROM login WHERE username = '$username' AND password = '$password'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $response = [
                'success' => true,
                'message' => 'Login successful!',
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Invalid username or password.',
            ];
        }
    } else {
        $response = [
            'success' => false,
            'message' => 'Invalid data received.',
        ];
    }

    // Close the database connection
    $conn->close();

    // Send the response
    echo json_encode($response);
} else {
    $response = [
        'success' => false,
        'message' => 'Invalid request method.',
    ];

    echo json_encode($response);
}
?>