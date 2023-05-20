<?php

function event_logger(){
    $event_path = __DIR__."/events.log";
    $file = fopen($event_path, 'a+');

    $timestamp = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
    $tip_request = $_SERVER['REQUEST_METHOD'];
    $agent = $_SERVER['HTTP_USER_AGENT'];
    if(isset($_SESSION['username']) && !empty($_SESSION['username'])){
        $utilizator = $_SESSION['username'];
    } else {
        $utilizator= 'neautentificat';
    }
    $path_request = $_SERVER['DOCUMENT_ROOT'].$_SERVER['PHP_SELF'];

    $continut = $timestamp.', request method: '.$tip_request.', request agent: '.$agent;
    $continut.= ', nume utilizator: '.$utilizator.', adresa fisierului: '.$path_request. PHP_EOL;
    fwrite($file, $continut);

    fclose($file);
}

function error_logger($denumire_camp, $etapa){
    $error_path = __DIR__."/errors.log";
    $file = fopen($error_path, 'a+');

    $timestamp = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
    $tip_request = $_SERVER['REQUEST_METHOD'];
    $mesaj = $_POST['errors'][$denumire_camp];
    if(!empty($_SESSION['username'])){
        $utilizator = $_SESSION['username'];
    } else {
        $utilizator= 'neautentificat';
    }
    $informatie = $_POST[$denumire_camp];

    $continut = $timestamp.', request method: '.$tip_request.', mesajul de eroare: '.$mesaj;
    $continut.= ', nume utilizator: '.$utilizator.', etapa: '.$etapa.', informatia gresita: '.$informatie.PHP_EOL;
    fwrite($file, $continut);

    fclose($file);

}
