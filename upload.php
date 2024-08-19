<?php
$servername = "localhost";
$username = "root"; // Change this to your MySQL username
$password = ""; // Change this to your MySQL password
$dbname = "file_uploads";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    
    if ($file['error'] == UPLOAD_ERR_OK) {
        $fileName = basename($file['name']);
        $fileTmpName = $file['tmp_name'];
        $fileDestination = 'uploads/' . $fileName;
        
        // Move the file to the uploads directory
        if (move_uploaded_file($fileTmpName, $fileDestination)) {
            // Prepare an SQL statement to insert file name into the database
            $stmt = $conn->prepare("INSERT INTO files (file_name) VALUES (?)");
            $stmt->bind_param("s", $fileName);
            
            if ($stmt->execute()) {
                echo "File uploaded and saved to database successfully.";
            } else {
                echo "Error: " . $stmt->error;
            }
            
            $stmt->close();
        } else {
            echo "Error moving uploaded file.";
        }
    } else {
        echo "File upload error: " . $file['error'];
    }
}

$conn->close();
?>
