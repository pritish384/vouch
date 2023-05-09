<?php
session_start();
require 'db.php';
$config = json_decode(file_get_contents('config.json'), true);


if(!$_SESSION['logged_in']){
  $error = "You are not logged in.";
  $_SESSION['errorstatus'] = $error;
  header('Location: error.php');
  exit();
}
extract($_SESSION['userData']);


// get vouches left
$sql = "SELECT vouchleft FROM $dbname.vouch WHERE userid = $discord_id";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $vouchesleft = $row['vouchleft'];
  }
}




if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $vouch_user_id = $_POST['vouch_user_id'];
  $vouch_username = $_POST['vouch_username'];
  $vouch_discriminator = $_POST['vouch_discriminator'];
}else
{
  $error = "Please submit the form.";
  $_SESSION['errorstatus'] = $error;
  header('Location: error.php');
  exit();
}


if ($vouchesleft == 0) {
  $error = "You have no vouches left.";
  $_SESSION['errorstatus'] = $error;
  header('Location: error.php');
  exit();
}


//generate a random string.
function random_strings($length_of_string) 
{ 
  
    // String of all alphanumeric character 
    $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'; 
  
    // Shufle the $str_result and returns substring 
    // of specified length 
    return substr(str_shuffle($str_result),  
                       0, $length_of_string); 
}

$vouch_id = random_strings(36);


$api_token = $config['api_token'];

$data = array(
  'discord_id' => $discord_id,
  'discord_username' => $name,
  'discord_discriminator' => $discriminator,

  'vouches_left' => $vouchesleft-1,
  'vouch_user_id' => $vouch_user_id,
  'vouch_username'=> $vouch_username,
  'vouch_discriminator' => $vouch_discriminator,
  'vouch_id' => $vouch_id,
  'api_token' => $api_token

);

// insert $data array into database
$sql = "INSERT INTO $dbname.vouch_history (discord_id, discord_username, discord_discriminator, vouches_left, vouch_user_id, vouch_username, vouch_discriminator, vouch_id) VALUES ('$discord_id', '$name', '$discriminator', '$vouchesleft', '$vouch_user_id', '$vouch_username', '$vouch_discriminator', '$vouch_id')";
$conn->query($sql);

$payload = json_encode($data);

$api_url = $config['api_url'];


$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($payload))
);

$result = curl_exec($ch);
$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);


if ($responseCode !== 200  ) {
  $error = "API is down Try again later.";
  $_SESSION['errorstatus'] = $error;
  header("Location: error.php");
  exit();
  
}elseif($responseCode == 200){
  $conn->query("UPDATE $dbname.vouch SET vouchleft = $vouchesleft - 1 WHERE userid = $discord_id");

}

curl_close($ch);
$conn->close();

session_destroy();
?>
<!-- form submitted -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Form Submitted</title>
</head>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/success.css">
    <title>Success</title>
</head>
<body>
    <div class="card">
    <div class="main-container">
        <div class="check-container">
            <div class="check-background">
                <svg viewBox="0 0 65 51" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7 25L27.3077 44L58.5 7" stroke="white" stroke-width="13" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div class="check-shadow"></div>
        </div>
        <div class="text-container">
            <h1>Success</h1>
        </div>

    </div>
    <p>We Got your Submission!</p>

    </div>
</body>
</html>