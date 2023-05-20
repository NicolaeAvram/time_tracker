<?php
session_start();
require_once('includes/db.inc.php');
require_once('includes/functions.inc.php');
require_once("utils/logging.inc.php");
event_logger();

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
            error_logger('username', 'introducere_date');
        } else {
            $hash = password_hash($parola, PASSWORD_DEFAULT);
            if(create('utilizatori',['nume','prenume','username','parola'],[$nume,$prenume,$username,$hash])){
                echo "Utilizatorul a fost creat cu succes";
            } else {
                $_POST['errors']['username'] = "Utilizatorul nu a putut fi creat";
                error_logger('create', 'query');  
            }
        }
    }    
}


?>

<?php require("templates/header.php"); ?>
<?php require("templates/navigation.php"); ?>
    <!-- Page Content -->
<div class="container-fluid">
    <div class="row d-flex justify-content-center">
        <div class="col-md-offset-5">
        <h1 class="page-header">Inregistrare utilizatori</h1>    
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>">
            <div class="form-group"><?php generare_input('text','nume',null);?></div>
            <div class="form-group"><?php generare_input('text','prenume',null);?></div>
            <div class="form-group"><?php generare_input('text','username',null);?></div>
            <div class="form-group"><?php generare_input('password','parola',null);?></div>
            <div class="form-group"><?php generare_input('password','repeta_parola',null);?></div>
            <br>
            <button class="btn btn-primary" type="submit" name="submit">Inregistreaza</button>
        </form>
        </div>
    </div>
</div>
<?php require("templates/footer.php"); ?>
