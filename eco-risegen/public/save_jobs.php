<?php
session_start();
require_once 'config.php'; // Make sure this contains connectDatabase()

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

// Check if data is sent via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Sanitize and capture input
    $title    = mysqli_real_escape_string($conn, $_POST['job_title']);
    $company  = mysqli_real_escape_string($conn, $_POST['company_name']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $type     = mysqli_real_escape_string($conn, $_POST['job_type']);
    $desc     = mysqli_real_escape_string($conn, $_POST['description']);

    // Prepare SQL using placeholders
    $stmt = $conn->prepare("INSERT INTO jobs (job_title, company_name, location, job_type, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $title, $company, $location, $type, $desc);

    // Execute and return JSON response
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Job posted successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}

$conn->close();
?>