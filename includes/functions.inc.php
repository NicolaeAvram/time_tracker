<?php 

function generare_input($type, $name, $row){
    if($_SERVER['REQUEST_METHOD']==='POST'){
        $show_value = isset($_POST[$name]) && !empty($_POST[$name]) ? $_POST[$name] : null;
    } elseif($_SERVER['REQUEST_METHOD']==='GET') {
        $show_value = isset($row[$name]) && !empty($row[$name]) ? $row[$name] : null;
    }
    $input = '<input type="'.$type.'" name="'.$name.'" value="'.$show_value.'"><br>';

    if(isset($_POST['errors'][$name]) && !empty($_POST['errors'][$name])){
        $input .='<p style="color:red">'.$_POST['errors'][$name].'</p>'; 
    } 
    echo '<label for="'.$name.'">'.strtoupper($name).'</label><br>';
    echo $input;
}

function validare_input($reguli_de_validare){
    foreach($reguli_de_validare as $denumire_camp=>$reguli){
        foreach($reguli as $regula_key=>$regula_value){
            $camp= htmlspecialchars($_POST[$denumire_camp]);
            if(!isset($_POST['errors'][$denumire_camp]) || empty($_POST['errors'][$denumire_camp])){   
                switch($regula_key){
                    case 'required':
                        if($regula_value){
                            if(!isset($camp) || empty($camp)){
                                $_POST['errors'][$denumire_camp] = $denumire_camp . " este obligatoriu";
                               
                            }
                        }
                        break;
                    case 'min':
                        if(strlen($camp)<$regula_value){
                            $_POST['errors'][$denumire_camp] = $denumire_camp . " este prea scurt";
                        }
                        break;
                    case 'max':
                        if(strlen($camp)>$regula_value){
                            $_POST['errors'][$denumire_camp] = $denumire_camp . " este prea lung";

                        }
                        break;
                    case 'valoare_min':
                        if($camp<$regula_value){
                            $_POST['errors'][$denumire_camp] = $denumire_camp . " este prea mica";
                        }
                        break;
                    case 'incepe':
                        if(substr($camp, 0, 2)!=$regula_value[0] && substr($camp, 0, 4)!= $regula_value[1]){
                            $_POST['errors'][$denumire_camp] = "Campul ". $denumire_camp . " are prefixul incorect";
                         
                        }
                        break;
                    case 'lowercase':
                        if(ctype_upper($camp)) {
                            $_POST['errors'][$denumire_camp] = $denumire_camp . " trebuie sa contina minim o minuscula";                            
                         
                        } 
                        break;
                    case 'uppercase':
                        if(ctype_lower($camp)){
                            $_POST['errors'][$denumire_camp] = $denumire_camp . " trebuie sa contina minim o majuscula";
                        
                        } 
                        break;
                    case 'caracter_special':
                        if(!preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $camp)){
                            $_POST['errors'][$denumire_camp] = $denumire_camp . " trebuie sa contina minim un caracter special";
                        
                        }
                        break;
                    case 'numar':
                        if(!preg_match('~[0-9]+~', $camp)){
                            $_POST['errors'][$denumire_camp] = $denumire_camp . " trebuie sa contina minim o cifra";
                        
                        }
                        break;
                    case 'parola_identica':
                        if($_POST[$denumire_camp] !== $regula_value){
                            $_POST['errors'][$denumire_camp] = "Parolele trebuie sa fie identice";
                        
                        }
                        break;
                    case 'valid_mail':
                        if(!preg_match('/@/', $camp)){
                            $_POST['errors'][$denumire_camp] = $denumire_camp . " trebuie sa fie sub forma de adresa de email";
                        } else {
                            $mail_cu_extensie = explode("@",$camp)[1];
                            if(!isset(explode(".", $mail_cu_extensie)[1]) || empty(explode(".", $mail_cu_extensie)[1]) ){
                                $_POST['errors'][$denumire_camp] = $denumire_camp . " trebuie sa aiba extensie valida";

                            } else {
                                $mail = explode(".", $mail_cu_extensie)[0];
                                $_POST['valid']=0;
                                foreach($regula_value as $value){
                                    if($mail === $value){
                                        $_POST['valid'] = 1;
                                    }
                                }
                                if($_POST['valid'] == 0){
                                    $_POST['errors'][$denumire_camp] = $denumire_camp . " trebuie sa aiba un domeniu de email valid";

                                }    
                            }
                        }
                        break;
                    default: 
                }
            }
        }
    }
    if(isset($_POST['errors']) && !empty($_POST['errors'])){
        return false;
    }
    return true;
}

function show_error($denumire_camp){  
    if(isset($_POST['errors'][$denumire_camp]) && !empty($_POST['errors'][$denumire_camp])){
        echo '<p style="color:red">'.$_POST['errors'][$denumire_camp].'</p>'; 
    } 
}

function getOne($table, $nume, $valoare){
    global $connection;
    $query = "SELECT * FROM $table WHERE $nume ='$valoare' LIMIT 1 ";
    $result = mysqli_query($connection, $query);
    if(!$result) {
        die ('query failed' . mysqli_error($connection));
    } 
    return mysqli_fetch_assoc($result);
}

function create($table, array $columns, array $values){
    global $connection;
    $query = "INSERT INTO $table(". implode(",", $columns);
    $query.= ") VALUES ('".implode("','", $values)."')";
    $result = mysqli_query($connection, $query);
    if(!$result){
        die ('query failed' . mysqli_error($connection));
    } 
    return $result ? true : false;
}

function update($table, array $columns_with_values, $id){
    global $connection;
    foreach($columns_with_values as $key=>$value){
        $perechi[] = "{$key}='{$value}'";
    }
    $query = "UPDATE $table SET ".implode(", ", $perechi)." WHERE id = '$id'";
    $result = mysqli_query($connection, $query);
    if(!$result) {
        die ('query failed' . mysqli_error($connection));
    } 
    // return mysqli_affected_rows($connection)===1 ? true : false;
}

function delete($table, $nume, $valoare){
    global $connection;
    $query = "DELETE FROM $table WHERE $nume='$valoare'";
    $result = mysqli_query($connection, $query);
    if(!$result) {
        die ('query failed' . mysqli_error($connection));
    } 
    return (mysqli_affected_rows($connection)==1) ? true : false;
}


function event_logger(){
    $event_path = __DIR__."/events.log";
    $file = fopen($event_path, 'a+');

    $timestamp = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
    $tip_request = $_SERVER['REQUEST_METHOD'];
    $agent = $_SERVER['HTTP_USER_AGENT'];
    if(isset($_SESSION['nume']) && !empty($_SESSION['nume'])){
        $utilizator = $_SESSION['nume'];
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
    if(!empty($_SESSION['nume'])){
        $utilizator = $_SESSION['nume'];
    } else {
        $utilizator= 'neautentificat';
    }
    $informatie = $_POST[$denumire_camp];

    $continut = $timestamp.', request method: '.$tip_request.', mesajul de eroare: '.$mesaj;
    $continut.= ', nume utilizator: '.$utilizator.', etapa: '.$etapa.', informatia gresita: '.$informatie.PHP_EOL;
    fwrite($file, $continut);

    fclose($file);

}
