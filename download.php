<?php
$servername = "localhost";
$username = "root"; // Change this to your MySQL username
$password = ""; // Change this to your MySQL password
$dbname = "file_uploads";

// Define the directory where files are stored
$upload_dir = 'uploads/'; // Change this to your file upload directory

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the file ID from the URL
$file_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch the file details from the database
$sql = "SELECT file_name FROM files WHERE id = ?"; // Only file_name is stored
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $file_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $file_name = $row['file_name']; // Fetch the file name

    // Construct the file path
    $file_path = $upload_dir . $file_name;

    // Check if file exists
    if (file_exists($file_path)) {
        // Send the file to the browser
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        flush(); // Flush system output buffer
        readfile($file_path);
        exit;
    } else {
        echo "File does not exist.";
    }
} else {
    echo "Invalid file ID.";
}

$stmt->close();
$conn->close();
?>
