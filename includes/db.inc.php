<?php 
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'time_tracker';

$connection = mysqli_connect($servername, $username, $password, $dbname);
if(!$connection){
    die('Conexiunea a esuat' . mysqli_connect_error());
}
?>