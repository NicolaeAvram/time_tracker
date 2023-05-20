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


<?php require("templates/header.php"); ?>
<?php require("templates/navigation.php"); ?>
    <!-- Page Content -->
    <div class="container-fluid">
        <div class="row">
        <!-- Left Page Column -->
        <div class="col-md-8">
                <h1 class="page-header">Departamente</h1>     
    <table class="table table-bordered table-hover">
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
            <td><?php echo $departament['id_dep']?></td>
            <td><?php echo $departament['nume_dep'] ?></td>
            <td><?php echo $nr_utilizatori?></td>
            <td><?php echo $ore_lucrate_total ?></td>
            <td><a href="departamente.php?delete=<?php echo $id_dep?>" onClick="javascript: return confirm('Esti sigur ca vrei sa stergi?')"><button class="btn btn-danger" name="delete">Sterge</button></a></td>
        </tr>
        <?php endforeach?>
    </table>
    </div>
    <div class="col-md-4">

<h3 class="page-header">Adauga un departament nou</h3>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>">
        <div class="form-group">
            <label style="font-weight:bold" for="nume_dep">Departament nou</label><br>
            <input autofocus required title="Denumirea departamentului" class="form-control" type="text" name="nume_dep" placeholder="Introduceti nume departament">
        </div>
        <button class="btn btn-primary" type="submit" name="adauga">Adauga departament</button>
        </form>
    </div>
        <!-- /.row -->
        </div>
    <!-- /.container -->

        <?php require("templates/footer.php"); ?>