<?php
session_start();
require_once('includes/db.inc.php');
require_once('includes/functions.inc.php');

if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['submit'])){
    if(validare_input([
        'nume'=>['required'=>true, 'min'=>3, 'max'=>25],
        'prenume'=>['required'=>true, 'min'=>3, 'max'=>25],
        'username'=>['required'=>true, 'min'=>3, 'max'=>25, 'uppercase'=>true],
        'parola'=>['required'=>true, 'min'=>8, 'max'=>60, 'uppercase'=>true, 'lowercase'=>true,'caracter_special'=>true, 'numar'=>true],
        'repeta_parola'=>['required'=>true, 'parola_identica'=>$_POST['parola']]
    ])){
        $nume = $_POST['nume'];
        $prenume = $_POST['prenume'];
        $username = $_POST['username'];
        $parola = $_POST['parola'];
        
        $query = "SELECT * FROM utilizatori WHERE username='$username'";
        $rezultat = mysqli_query($connection, $query);
        $rows = mysqli_num_rows($rezultat);
        if($rows >0){
            $_POST['errors']['username'] = "Utilizatorul a fost deja inregistrat";
        } else {
            $hash = password_hash($parola, PASSWORD_DEFAULT);
            if(create('utilizatori',['nume','prenume','username','parola'],
            [$nume,$prenume,$username,$hash])){
                echo "Utilizatorul a fost creat cu succes";
            }
        }
    }    
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=, initial-scale=1.0">
    <title>Inregistrare</title>
</head>
<body >
<?php require('templates/header.php'); ?>
<h1 style="text-align:center">Inregistrare utilizatori</h1>    
<div style="justify-content:center; display:flex">
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>">
    <?php 
    generare_input('text','nume',null);
    generare_input('text','prenume',null);
    generare_input('text','username',null);
    generare_input('password','parola',null);
    generare_input('password','repeta_parola',null);
    ?>
        <br>
        <input type="submit" name="submit" value="Inregistreaza">
    </form>
</div>
</body>
</html>
