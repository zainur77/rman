<?php
include 'include/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sql = "DELETE FROM item_history";
    
    if ($conn->query($sql) === TRUE) {
        echo "All history records deleted successfully";
    } else {
        echo "Error deleting history: " . $conn->error;
    }
    
    $conn->close();
    header("Location: history.php");
    exit;
} else {
    echo "Invalid request";
}
?>
