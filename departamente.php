<?php
session_start();
require_once("includes/db.inc.php");
require_once("includes/functions.inc.php");

$query = "SELECT * FROM departamente";
$departamente = mysqli_query($connection, $query);

if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['adauga'])){
    if(validare_input([
        'nume_dep'=>['required'=>true, 'max'=>50]
    ])){
        if(create('departamente',['nume_dep'],[$_POST['nume_dep']])){
            header("location: departamente.php");
        } else {
            $_POST['errors']['adauga'] = "Departamentul nu a fost adaugat";
        }
    }
}

if($_SERVER['REQUEST_METHOD']==='GET' && isset($_GET['delete'])){
    if(delete('departamente', 'id_dep', $_GET['delete'])){
        header("location: departamente.php");
    } else {
        $_POST['errors']['id_dep'] = "Departamentul nu a putut fi sters";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departamente</title>
</head>
<body>
    <?php require("templates/header.php")?>
    <table>
        <tr>
            <th>Id</th>
            <th>Denumire</th>
            <th>Nr. utilizatori</th>
            <th>Ore lucrate</th>
            <th></th>
        </tr>
           
<?php foreach($departamente as $departament):?>
<?php 
    $id_dep = $departament['id_dep'];
    $nume_dep = $departament['nume_dep'];

    $query_utilizator = "SELECT * FROM utilizatori WHERE id_dep_utilizator='$id_dep'";
    $result_utilizator = mysqli_query($connection,$query_utilizator);
    $nr_utilizatori = mysqli_num_rows($result_utilizator);

    $query_act = "SELECT * FROM activitati WHERE nume_dep='$nume_dep'";
    $result_act = mysqli_query($connection, $query_act);
    $ore_lucrate_total = 0;
    while($activitate = mysqli_fetch_assoc($result_act)){
        $ore_lucrate_total = $ore_lucrate_total + $activitate['ore_lucrate'];
    }
?>
        <tr>
            <th><?php echo $departament['id_dep']?></th>
            <th><?php echo $departament['nume_dep'] ?></th>
            <th><?php echo $nr_utilizatori?></th>
            <th><?php echo $ore_lucrate_total ?></th>
            <th><a href="departamente.php?delete=<?php echo $id_dep?>" onClick="javascript: return confirm('Esti sigur ca vrei sa stergi?')"><button name="delete">Sterge</button></a></th>
        </tr>
        <?php endforeach?>
    </table>
    <hr>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>">
        <label style="font-weight:bold" for="nume_dep">Departament nou</label><br>
        <input type="text" name="nume_dep" placeholder="Introduceti nume departament">
        <input type="submit" name="adauga" value="Adauga departament">
    </form>
</body>
</html>