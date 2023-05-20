<?php
session_start();
require_once("includes/db.inc.php");
require_once("includes/functions.inc.php");

if($_SERVER['REQUEST_METHOD'] == 'GET'){
$id_dep = $_GET['id_dep'];
} else {
    $id_dep = $_POST['id_dep'];
}
$id = $_SESSION['id'];
$data_minus_2sapt = $_SERVER['REQUEST_TIME'] -1209600;

$query_dep = "SELECT * FROM departamente WHERE id_dep=$id_dep";
$select_dep = mysqli_query($connection, $query_dep);
$departament = mysqli_fetch_assoc($select_dep);

$query_cat = "SELECT * FROM categorii WHERE id_departament_cat=$id_dep";
$select_cat = mysqli_query($connection,$query_cat);

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])){
    $nume_dep = $departament['nume_dep'];
    $nume_cat = $_POST['categorie'];
    $data = $_POST['data'];
    if(!isset($data) || empty($data)){
        $_POST['errors']['data'] = "Trebuie sa selectati o data";
    } elseif(strtotime($data) > $_SERVER['REQUEST_TIME']){
        $_POST['errors']['data'] = "Nu puteti adauga activitati in viitor";
    } elseif(strtotime($data)<$data_minus_2sapt) {
        $_POST['errors']['data'] = "Nu puteti adauga activitati mai in urma cu 2 sapt";
    } else {
        $ore_lucrate = $_POST['ore'];
        $ora_log = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
        $ore_lucrate_zi = 0;
        $query_activitati = "SELECT * FROM activitati WHERE id=$id AND data_act='$data'";
        $select_activitati = mysqli_query($connection, $query_activitati);
        while($activitati = mysqli_fetch_assoc($select_activitati)){
            $ore_lucrate_zi=$ore_lucrate_zi + $activitati['ore_lucrate'];
        }
        if($ore_lucrate_zi < 8 && $ore_lucrate_zi + $ore_lucrate <=8){
            if(create('activitati', ['id','nume_dep','nume_cat','data_act','ore_lucrate','ora_log'],[$id, $nume_dep, $nume_cat, $data, $ore_lucrate, $ora_log])){
                header("location: categorii.php?id_dep=$id_dep");
            } else {
                $_POST['errors']['query'] = "Nu s-a putut crea activitatea in db, desi activitatea exista";
            }
        } elseif($ore_lucrate>8) {
            $_POST['errors']['query'] = "Nu puteti lucra mai mult de 8 ore de-o data";
        } else{
            $_POST['errors']['query'] = "Nu puteti lucra mai mult de 8 ore intr-o zi. In data de ".$data." aveti lucrate deja ".$ore_lucrate_zi." ore.";   
        }
    }
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['introdu'])){
    $nume_cat = $_POST['nume_cat'];
    if(create('categorii', ['nume_cat','id_departament_cat'], [$nume_cat, $id_dep])){
        header("location: categorii.php?id_dep=$id_dep");
    }
}

//afisare sumarul activitatilor:

$query = "SELECT * FROM activitati WHERE id=$id ORDER BY data_act DESC";
$result = mysqli_query($connection, $query);


?>


<?php require("templates/header.php"); ?>
<?php require("templates/navigation.php"); ?>
    <!-- Page Content -->
    <div class="container-fluid">
        <div class="row">
        <!-- Left Page Column -->
        <div class="col-md-8">
    <h3 class="page-header">Activitatile disponibile din departamentul <?php echo $departament['nume_dep']?></h3>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>">
            <div class="form-group">
            <?php show_error('query')?>
            <input type="text" name="id_dep" value="<?php echo $departament['id_dep']?>" hidden>
            <label for="categorie">Selecteaza activitatea</label><br>
            <select name="categorie">
            <?php   
            while($categorie = mysqli_fetch_assoc($select_cat)):?>                
                <option value="<?php echo $categorie['nume_cat']?>"><?php echo $categorie['nume_cat']?></option>
            <?php endwhile?>
            </select>
            </div>
            <div class="form-group">
            <label for="data">Introdu data</label><br>
            <input type="date" name="data"><br>
            <?php show_error('data')?>
            </div>
            <div class="form-group">
            <label for="ore">Introdu nr. de ore</label><br>
            <input type="number" name="ore"><br>
            <?php show_error('ore')?>
            </div>
            <button class="btn btn-primary" type="submit" name="submit">Inregistreaza</button>
        </form>
    <br>
    <hr>
        <h3 class="page-header">Cele mai recente activitati introduse</h3>
        <table class="table table-bordered table-hover">
            <tr>
                <th>Nr. crt.</th>
                <th>Nume activitate</th>
                <th>Departament</th>
                <th>Data</th>   
                <th>Ore lucrate</th>
            </tr>
    <?php $i=1;  
    while($activitati = mysqli_fetch_assoc($result)):
    if(strtotime($activitati['ora_log'])>$_SESSION['logintime']):
    ?>
            
            <tr>
                <td><?php echo $i; $i++;?></td>
                <td><?php echo $activitati['nume_cat']?></td>
                <td><?php echo $activitati['nume_dep']?></td>
                <td><?php echo $activitati['data_act']?></td>
                <td><?php echo $activitati['ore_lucrate']?></td>
            </tr>
            
        <?php endif; endwhile;?>
        </table>
    </div>
    <!-- /.row -->
    <div class="col-md-4">
<?php if($id_dep == $_SESSION['id_dep_utilizator'] || $_SESSION['rol'] ==='admin'):?>
    <h3 class="page-header">Introduceti o categorie noua in departamentul <?php echo $departament['nume_dep']?></h3>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>">
        <div class="form-group">
            <input type="text" name="id_dep" value="<?php echo $departament['id_dep']?>" hidden>
            <label for="nume_cat">Categorie noua</label><br>
            <input type="text" name="nume_cat" autofocus required title="Denumirea categoriei"><br>
        </div>
        <button class="btn btn-info" name="introdu" >Introdu categorie</button>
    </form>
<?php endif ?>
    </div>
   
</div>
    <!-- /.container -->

        <?php require("templates/footer.php"); ?>