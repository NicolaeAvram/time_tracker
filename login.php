<?php 
require_once('includes/db.inc.php');
require_once('includes/functions.inc.php');
session_start();

if($_SERVER["REQUEST_METHOD"]=='POST' && isset($_POST['submit'])){
    if(validare_input([
        'username'=>['required'=>true],
        'parola'=>['required'=>true]
    ])){
        $username = $_POST['username'];
        $parola = $_POST['parola'];
        $utilizator = getOne('utilizatori','username',$username);
        
        if(!empty($utilizator) && $utilizator['username'] === $username){
            if(password_verify($parola, $utilizator['parola'])){
                if($utilizator['status'] =='activ'){
                    $_SESSION['id'] = $utilizator['id'];
                    $_SESSION['username'] = $utilizator['username'];
                    $_SESSION['prenume'] = $utilizator['prenume'];
                    $_SESSION['rol'] = $utilizator['rol'];
                    $_SESSION['id_dep_utilizator'] = $utilizator['id_dep_utilizator'];
                    $_SESSION['status'] = $utilizator['status'];
                    $_SESSION['logintime']=date($_SERVER['REQUEST_TIME']);
                    header('location: index.php');
                } else {
                    $_POST['errors']['username'] = "Utilizatorul este dezactivat";  
                }
            } else {
                $_POST['errors']['parola'] = "Parola este gresita";    
            }
        } else {
            $_POST['errors']['username'] = "Utilizatorul nu exista";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <?php require('templates/header.php') ?>
    <h3 style="text-align:center">Log in </h3>
    <div style="justify-content:center; display:flex">
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>">
            <?php generare_input('text','username',null)?>
            <br>
            <?php generare_input('password','parola',null)?>
            <br>
            <input type="submit" name="submit" value="login">
        </form>
    </div>
</body>
</html>