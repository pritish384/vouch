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

$bot_token = $config['bot_token'];

foreach ($roles as $key => $role) {
    if($role == $config['vouch_role']){
        $validrole= true;
        break;
    }
}

foreach ($guilds as $key => $guildData) {
    if($guildData['id'] == $config['guild_id']){
        $inGuild = true;
        break;
    }
}

if(!$validrole){
    $error = "You do not have the required role.";
    $_SESSION['errorstatus'] = $error;
  header('Location: error.php');
  exit();
}

if(!$inGuild){
    $error = "You are not in the server.";
    $_SESSION['errorstatus'] = $error;
  header('Location: error.php');
  exit();
}




$avatar_url = "https://cdn.discordapp.com/avatars/$discord_id/$avatar.jpg";



// check how many vouches user has left and store in a variable name $vouchesleft

$sql = "SELECT vouchleft FROM $dbname.vouch WHERE userid = $discord_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    $vouchesleft = $row["vouchleft"];
    if($vouchesleft == 0){
        $error = "You have 0 vouches left.";
        $_SESSION['errorstatus'] = $error;
        header('Location: error.php');
        exit();
       
    }
  }
} elseif (empty($vouchesleft)) {
    $sql = "INSERT INTO $dbname.vouch (userid, vouchleft, eligible) VALUES ($discord_id, 5 , 1)";
    $conn->query($sql);
    header('Location: vouch-form.php');
    exit();
}

$sql = "SELECT eligible FROM $dbname.vouch WHERE userid = $discord_id";
$result = $conn->query($sql);
// check if user is eligible to vouch
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
      $eligible = $row["eligible"];
      if($eligible == 0){
          $error = "You are not eligible to vouch.";
          $_SESSION['errorstatus'] = $error;
          header('Location: error.php');
          exit();
         
      }
    }
  }




$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/css2?family=Fira+Sans&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="./dist/eventform.css">
        <script src="./dist/script.js"></script>

        <title>Mystic Vouch Form</title>
        <style>
            label:not(.radio) {
                font-size: 1.25rem;
            }
        </style>
    </head>
    <body>
        <div class="jumbotron text-center">
            <h1>Mystic Vouch Form</h1>
        </div>

        <div class="container mb-3">
            <div class="d-flex">
                <img src="<?php echo $avatar_url?>" class="rounded-circle" width="64" height="64" alt="Your avatar">
                
                
                <h2 class="ml-3 mt-2" id="username"><?php echo $name;?>#<?php echo $discriminator;?> </h2>
                
                
                
            </div>
                <h2>Vouches Left: <?php echo $vouchesleft;?> </h2>

            <form class="mt-3" method="post" action="" >
                
                <div class="form-group">
                    <label id="white" for="reasoning">Mention User Id of User you want to vouch</label>
                    <p id="red"><i>(Mandatory)</i></p>
                    <div class="textInput">
                        <textarea class="form-control" id="vouch_user_id" name="vouch_user_id" required maxlength="19"></textarea>
                        <div class="remainingLength"></div>
                    </div>
                </div>
                <div class="text-right">
                    <button type="submit" class="btn">Find</button>
                </div>
            </form>
            <br>
            <?php
            if(isset($_POST['vouch_user_id'])) {
                $vouch_user_id = $_POST['vouch_user_id'];
                
                if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $vouch_user_id)){
                    $error = "User id contains invalid characters.";
                    $_SESSION['errorstatus'] = $error;
                    header('Location: error.php');
                    exit();
                }

                if (strlen($vouch_user_id) < 17){
                    $error = "User id is too short.";
                    $_SESSION['errorstatus'] = $error;
                    header('Location: error.php');
                    exit();
                }

                if (strlen($vouch_user_id) > 19){
                    $error = "User id is too long.";
                    $_SESSION['errorstatus'] = $error;
                    header('Location: error.php');
                    exit();
                }


                if (empty($vouch_user_id)){
                    $error = "Please enter a user id.";
                    $_SESSION['errorstatus'] = $error;
                    header('Location: error.php');
                    exit();
                }


                if ($vouch_user_id == $discord_id){
                    $error = "You cannot vouch yourself.";
                    $_SESSION['errorstatus'] = $error;
                    header('Location: error.php');
                    exit();
                }

                $url = "https://discord.com/api/users/$vouch_user_id";
                $headers = array(
                    "Authorization: Bot $bot_token"
                );
            
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            
                $result = curl_exec($ch);
                $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
            
                if($statusCode == 200) {
                    $vouch_userData = json_decode($result, true);
                    $vouch_username = $vouch_userData['username'];
                    $vouch_discriminator = $vouch_userData['discriminator'];
                    $vouch_avatar = $vouch_userData['avatar'];
                    $vouch_avatar_url = "https://cdn.discordapp.com/avatars/$vouch_user_id/$vouch_avatar.jpg";


                    if (isset($vouch_userData['bot']) == 1){
                        $error = "You cannot vouch a bot.";
                        $_SESSION['errorstatus'] = $error;
                        header('Location: error.php');
                        exit();
                    }



                    echo "
                    <div class='card-header'>
                    <div class='row'>
                        <div class='col-auto me-auto ms-auto me-lg-0 ms-lg-0'>
                            
                            <img src=$vouch_avatar_url loading='lazy' class='rounded-circle user-avatar' width='64px' height='64px' alt='user avatar' />

                        </div>
                        <div class='col-auto me-auto ms-auto me-lg-0 ms-lg-0 text-center text-lg-start align-self-center'>
                            <b>$vouch_username#$vouch_discriminator</b>
                            <div class='small text-muted'>$vouch_user_id</div>
                        </div>
                            <div class='text-center btncenter'>
                            <form method='post' action='./submit-form.php' id='myForm' onsubmit='disableSubmit()'>
                            <button type='submit' id='submitButton' class='btn'>vouch</button>
                            <input type='hidden' name='vouch_user_id' value='$vouch_user_id'>
                            <input type='hidden' name='vouch_username' value='$vouch_username'>
                            <input type='hidden' name='vouch_discriminator' value='$vouch_discriminator'>
                            </form>
                            </div>
                        </div>
                    </div>";
                    
                    
                    
                } 
                }
            
            ?>         
            

        </div>

    </body>
</html>

