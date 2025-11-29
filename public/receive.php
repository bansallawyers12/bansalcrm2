<?php
//phpinfo();
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Receive data from Server A
$data = json_decode(file_get_contents('php://input'), true);
echo "<pre>test=";print_r($data);
// Process the received data
if ($data !== null) {
    // Perform actions with the received data
    echo "Data received from Server A: ";
    print_r($data);
} else {
    echo "No data received from Server A.";
}
?>
