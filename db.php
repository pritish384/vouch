<!-- connect to db -->
<?php
// get info from config.json

$config = json_decode(file_get_contents('config.json'), true);


$servername = $config['servername'];
$username = $config['username'];
$password = $config['password'];
$dbname = $config['database'];


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

?>