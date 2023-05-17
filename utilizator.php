<?php
require_once('includes/db.inc.php');
require_once("includes/functions.inc.php");
session_start();

if($_SERVER['REQUEST_METHOD']==='GET' && isset($_GET['id'])){
    if(isset($_GET['id']) && !empty($_GET['id'])){
        $id = htmlspecialchars($_GET['id']);
        $utilizator = getOne('utilizatori','id',$id);
        // afisarea datelor pt activitatile din ultima saptamana:
        $data_minus_1sapt = $_SERVER['REQUEST_TIME'] -604800;
        $data_minus_2zile = $_SERVER['REQUEST_TIME'] -172800;
        $query = "SELECT * FROM activitati WHERE id=$id ORDER BY data_act DESC";
        $result = mysqli_query($connection, $query);
    }
}

if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['submit'] )){

    if(validare_input([
        'nume'=>['required'=>true, 'min'=>3, 'max'=>25],
        'prenume'=>['required'=>true, 'min'=>3, 'max'=>25],
        'username'=>['required'=>true, 'min'=>3, 'max'=>25, 'uppercase'=>true],
            ])){
        $id = $_POST['id'];
        $nume = $_POST['nume'];
        $prenume = $_POST['prenume'];
        $username = $_POST['username'];
        $parola = $_POST['parola'];
        $rol = $_POST['rol'];
        $status = $_POST['status'];
        $id_dep_utilizator = $_POST['departament'];
        //verificare parola
        $query_parola = "SELECT parola FROM utilizatori WHERE id = '$id'";
        $select_parola = mysqli_query($connection, $query_parola);
        $utilizator = mysqli_fetch_assoc($select_parola);
        $parola_db = $utilizator['parola'];    
        if(!empty($parola)){
            if(validare_input(['parola'=>['required'=>true, 'min'=>8, 'max'=>60, 'uppercase'=>true, 'lowercase'=>true,'caracter_special'=>true, 'numar'=>true]])){
                $hash = password_hash($parola, PASSWORD_DEFAULT);
                update('utilizatori',['nume'=>$nume, 'prenume'=>$prenume, 'username'=>$username, 'parola'=>$hash, 'rol'=>$rol, 'id_dep_utilizator'=>$id_dep_utilizator, 'status'=>$status], $id);
                header("location: utilizator.php?id=$id");
            }
        } else {
            update('utilizatori',['nume'=>$nume, 'prenume'=>$prenume, 'username'=>$username, 'parola'=>$parola_db, 'rol'=>$rol, 'id_dep_utilizator'=>$id_dep_utilizator, 'status'=>$status], $id);
            header("location: utilizator.php?id=$id");
        }
        
    }
}


//delete activitate
if($_SERVER['REQUEST_METHOD']==='GET' && isset($_GET['delete'])){
    $id_act = $_GET['delete'];
    $query = "SELECT * FROM activitati WHERE id_act = $id_act";
    $result = mysqli_query($connection, $query);
    $activitate = mysqli_fetch_assoc($result);
    $id = $activitate['id'];
    if(delete('activitati', 'id_act', $_GET['delete'])){
        header("location: utilizator.php?id=$id");
    } else {
        $_POST['errors']['delete'] = "Departamentul nu a putut fi sters";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Utilizator</title>
</head>
<body>
<?php require('templates/header.php')?>  
<h3>Date personale</h3>
<form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>">   
    <input type="number" name="id" value="<?php echo $utilizator['id']?>" hidden>
<?php     
    generare_input('text','nume', $utilizator);
    generare_input('text','prenume', $utilizator);
    generare_input('text','username', $utilizator);
?>
    <label for="parola">PAROLA</label><br>
    <input type="password" name="parola"><br>

<?php if($_SESSION['rol']=='admin'):
//input DEPARTAMENT
    $id_dep = $utilizator['id_dep_utilizator'];
    $query = "SELECT * FROM departamente WHERE id_dep='$id_dep'";
    $select_departamente = mysqli_query($connection, $query);
    $departament = mysqli_fetch_assoc($select_departamente);
    $nume_departament = $departament['nume_dep'];
    $id_departament = $departament['id_dep'];
?>
    <label for="departament">DEPARTAMENT</label><br>
    <select name="departament" id="departament">    
    <option value="<?php echo $id_departament?>"><?php echo $nume_departament ?></option>
<?php 

$query = "SELECT * FROM departamente";
$select_departamente = mysqli_query($connection, $query);
while($departament = mysqli_fetch_assoc($select_departamente)){
    $nume_dep = $departament['nume_dep'];
    $id_dep = $departament['id_dep'];
    echo "<option value='$id_dep'>$nume_dep</option>";
} ?>
        </select><br>
       
   
<!-- input ROL -->
    <label for="rol">ROL</label><br>
    <select name="rol" id="rol">    
    <option value="<?php echo $utilizator['rol']?>"><?php echo $utilizator['rol'] ?></option>
<?php 
if($utilizator['rol'] == 'admin'){
    echo "<option value='utilizator'>utilizator</option>";
} else {
    echo "<option value='admin'>admin</option>";
} ?>
        </select><br>
<?php show_error('rol') ?>

<!-- input STATUS -->
<label for="status">STATUS</label><br>
    <select name="status" id="status">    
    <option value="<?php echo $utilizator['status']?>"><?php echo $utilizator['status'] ?></option>
<?php 
if($utilizator['status'] == 'activ'){
    echo "<option value='dezactiv'>dezactiv</option>";    
} else {
    echo "<option value='activ'>activ</option>";

} ?>
        </select><br>
<?php show_error('status') ?>
<?php endif ?>
    <br>
    <input type="submit" name="submit" value="Modifica">
</form>  
<hr>
<h3>Activitati desfasurate in ultima saptamana</h3>
<table style="text-align:center">
    <tr>
        <th>Nr. crt.</th>
        <th>Nume activitate</th>
        <th>Departament</th>
        <th>Data</th>   
        <th>Ore lucrate</th>
        <th></th>
        <th></th>
    </tr>
<?php $i=1;  
while($activitati = mysqli_fetch_assoc($result)):
    if(strtotime($activitati['data_act'])>$data_minus_1sapt):
?>
    
    <tr>
        <td><?php echo $i; $i++;?></td>
        <td><?php echo $activitati['nume_cat']?></td>
        <td><?php echo $activitati['nume_dep']?></td>
        <td><?php echo $activitati['data_act']?></td>
        <td><?php echo $activitati['ore_lucrate']?></td>
        <?php if(strtotime($activitati['ora_log'])>$data_minus_2zile):?>
        <td><a href="edit_categorii.php?id_act=<?php echo $activitati['id_act']?>"><button name="edit">Modifica activitatea</button></a></td>
        <th><a href="utilizator.php?delete=<?php echo $activitati['id_act']?>" onClick="javascript: return confirm('Esti sigur ca vrei sa stergi?')"><button name="delete">Sterge activitatea</button></a></th>        
<?php show_error('delete')?>
        <?php endif?>
    </tr>
    
<?php endif; endwhile;?>
</table>





</body>
</html>



