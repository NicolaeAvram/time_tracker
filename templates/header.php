<header>
<nav>
<?php if(isset($_SESSION['username']) && !empty($_SESSION['username'])):?>
    <h3>Bine ati venit, <?php echo $_SESSION['prenume']?></h3>
    <a href="index.php">Acasa</a><br>
    <a href="departamente.php">Departamente</a><br>
    <a href="register.php">Register</a><br>
    <a href="logout.php">Logout</a><br>
<?php else:?>
    <a href="register.php">Register</a><br>
    <a href="login.php">Login</a><br>
    
<?php endif ?>    
</nav>
<hr>
</header>