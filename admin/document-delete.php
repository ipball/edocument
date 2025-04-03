<?php
include '../config.php';
include 'auth.php';

$id = $_GET['id'];

// Get the document filename
$result = mysqli_query($conn, "SELECT file_name FROM documents WHERE id = $id");
$document = mysqli_fetch_assoc($result);
$file_name = $document['file_name'];

// Delete the document record
$query = "DELETE FROM documents WHERE id = $id";
mysqli_query($conn, $query);

// Delete the document file
if ($file_name) {
    $file_path = "../uploads/documents/" . $file_name;
    if (file_exists($file_path)) {
        unlink($file_path);
    }
}

header('Location: ' . $admin_url . '/document-list.php');
?>
