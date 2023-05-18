<?php
session_start();
require_once("includes/db.inc.php");
require_once("includes/functions.inc.php");
if(!isset($_SESSION['id'])){
    header("location: login.php");
}
if($_SESSION['rol']==='admin'){
    $query = "SELECT * FROM utilizatori";
    $utilizatori = mysqli_query($connection, $query);
} else if($_SESSION['rol'] === 'utilizator'){
    $id=$_SESSION['id'];
    $query = "SELECT * FROM utilizatori WHERE id=$id";
    $utilizatori = mysqli_query($connection, $query);
}

if($_SERVER['REQUEST_METHOD']==='GET' && isset($_GET['delete'])){
        $id = $_GET['delete'];
        if(delete('utilizatori','id',$_GET['delete'])){
            header("location:index.php");
        } else{
            $_POST['error']['id'] = "Utilizatorul nu a putut fi sters";
        }
}
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['select'])){
    $id_dep = $_POST['departament'];
    header("location: categorii.php?id_dep=$id_dep");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time tracker</title>
</head>
<body>
<?php require('templates/header.php'); ?>
<table style="text-align:center">
        <tr>
        
            <th>Nr.crt.</th>
            <th>Username</th>
            <th>Nume</th>
            <th>Prenume</th>        
            <th>Rol</th>
            <th>Angajat la</th>
            <th></th>
            <th></th>
        </tr>

<?php $i=1; foreach($utilizatori as $utilizator):?>    
        <tr>
        
<?php 
$id_dep = $utilizator['id_dep_utilizator'];
$query = "SELECT * FROM departamente WHERE id_dep='$id_dep'";
$select_departamente = mysqli_query($connection, $query);
$nume_departament = mysqli_fetch_assoc($select_departamente);
?>
            <td hidden><?php echo $utilizator['id']?></td>
            <td><?php echo $i; $i++;?></td>
            <td><?php echo $utilizator['username']?></td>
            <td><?php echo $utilizator['nume']?></td>
            <td><?php echo $utilizator['prenume']?></td>
            <td><?php echo $utilizator['rol']?></td>
            <td><?php echo $nume_departament['nume_dep']?></td>
            
            <td><a href="utilizator.php?id=<?php echo $utilizator['id']?>"><button name="profile">User profile</button></a></td>
            <td><a href="index.php?delete=<?php echo $utilizator['id']?>" onClick="javascript: return confirm('Esti sigur ca vrei sa stergi?')"><button name="delete">Sterge</button></a></td>
            
        </tr>


<?php endforeach?>

    </table>
    <hr>
<h3>Adauga o activitate in departamentul:</h3>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>">
        <select name="departament" id="departament">
<?php   
$query = "SELECT * FROM departamente";
$departamente = mysqli_query($connection, $query);
foreach($departamente as $departament):?>                
            <option value="<?php echo $departament['id_dep']?>"><?php echo $departament['nume_dep']?></option>
<?php endforeach?>
        </select>
        <button type="submit" name="select">Selecteaza</button>
    </form>
    <hr>

    

</body>
</html>