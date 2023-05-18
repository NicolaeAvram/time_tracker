<?php
session_start();
require_once("includes/db.inc.php");
require_once("includes/functions.inc.php");

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    $id_act = $_GET['id_act'];
} else {
    $id_act = $_POST['id_act'];
}

$data_minus_2sapt = $_SERVER['REQUEST_TIME'] -1209600;

$query_act = "SELECT * FROM activitati WHERE id_act=$id_act";
$select_act = mysqli_query($connection,$query_act);
$activitate = mysqli_fetch_assoc($select_act);

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])){
    $id= $activitate['id'];
    $nume_dep = $_POST['departament'];
    $nume_cat = $_POST['categorie'];

        $query = "SELECT * FROM categorii WHERE nume_cat='$nume_cat'";
        $result = mysqli_query($connection, $query);
        $categorie = mysqli_fetch_assoc($result);
        $id_departament_cat = $categorie['id_departament_cat'];
        
        $query = "SELECT * FROM departamente WHERE id_dep=$id_departament_cat";
        $result = mysqli_query($connection, $query);
        $departament = mysqli_fetch_assoc($result);

    if($nume_dep === $departament['nume_dep']){
        $data = $_POST['data_act'];
        if(strtotime($data) > $_SERVER['REQUEST_TIME']){
            $_POST['errors']['data'] = "Nu puteti adauga activitati in viitor";
        } elseif(strtotime($data)<$data_minus_2sapt) {
            $_POST['errors']['data'] = "Nu puteti adauga activitati mai in urma cu 2 sapt";
        } else {
            $ore_lucrate = $_POST['ore_lucrate'];
            $ora_log = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
            $ore_lucrate_zi = 0;
            $query_activitati = "SELECT * FROM activitati WHERE id=$id AND data_act='$data'";
            $select_activitati = mysqli_query($connection, $query_activitati);
            while($activitati = mysqli_fetch_assoc($select_activitati)){
                $ore_lucrate_zi=$ore_lucrate_zi + $activitati['ore_lucrate'];
            }
            if($data == $activitate['data_act']){
                $ore_lucrate_zi = $ore_lucrate_zi - $activitate['ore_lucrate'];
            }    
            
            if($ore_lucrate_zi < 8 && $ore_lucrate_zi + $ore_lucrate <=8){
                $query = "UPDATE activitati SET id=$id, nume_dep='$nume_dep', nume_cat='$nume_cat', data_act='$data', ore_lucrate=$ore_lucrate, ora_log='$ora_log' WHERE id_act= '$id_act'";
                $result = mysqli_query($connection, $query);
                header("location: utilizator.php?id=$id");
            } elseif($ore_lucrate>8) {
                $_POST['errors']['ore'] = "Nu puteti lucra mai mult de 8 ore de-o data";
            } else {
                $_POST['errors']['ore'] = "Nu puteti lucra mai mult de 8 ore intr-o zi. In data de ".$data." aveti lucrate deja ".$ore_lucrate_zi." ore.";   
            }
        }
    } else {
        $_POST['errors']['departament'] = "Categoria aleasa trebuie sa fie din cadrul departamentului corespunzator";
    }
}




?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificare categorie</title>
</head>
<body>
    <?php require('templates/header.php');?>
    <h3>Ati selectat urmatoarea activitate:</h3>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>">
        
        <input type="text" name="id_act" value="<?php echo $activitate['id_act']?>" hidden>
        <label for="departament">NUME DEPARTAMENT</label><br>
        <select name="departament" id="departament">
            <option value="<?php echo $activitate['nume_dep']?>"><?php echo $activitate['nume_dep']?></option>
<?php   
$query = "SELECT * FROM departamente";
$departamente = mysqli_query($connection, $query);
foreach($departamente as $departament):
    if($activitate['nume_dep']!==$departament['nume_dep']):?>                
            <option value="<?php echo $departament['nume_dep']?>"><?php echo $departament['nume_dep']?></option>
<?php endif; endforeach;?>
        </select>
        <?php show_error('departament')?>
        <br>

        <label for="categorie">NUME CATEGORIE</label><br>
        <select name="categorie" id="categorie">
            <option value="<?php echo $activitate['nume_cat']?>"><?php echo $activitate['nume_cat']?></option>
<?php   
$query = "SELECT * FROM categorii";
$categorii = mysqli_query($connection, $query);
foreach($categorii as $categorie):
    
    if($categorie['nume_cat']!==$activitate['nume_cat']):?>                
            <option value="<?php echo $categorie['nume_cat']?>"><?php echo $categorie['nume_cat']?></option>
<?php endif; endforeach;?>
        </select>
        <br>
<?php 
generare_input('date','data_act',$activitate);
show_error('data');
generare_input('number','ore_lucrate',$activitate);
show_error('ore');
?>
            <button type="submit" name="submit">Modifica activitatea</button>
        </form>
    <br>
    <hr>
 
 
</body>
</html>