<?php
require 'db.php';
$config = json_decode(file_get_contents('config.json'), true);
$dbname = $config['database'];

// request method must be POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    $discord_id = $data->discord_id;

    $sql = "UPDATE $dbname.vouch SET vouchleft = vouchleft + 1 WHERE userid = $discord_id";
    $result = $conn->query($sql);


    $conn->close();
    
} else {
    http_response_code(405);
    echo json_encode(array('error' => 'Method not allowed'));
}
?>
