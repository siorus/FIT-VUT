<?php
	include_once "functions.php";

    function save_form_data(){
        $_SESSION['form_po'] = $_POST['poznamka'];
        $_SESSION ['form_te'] = $_POST['telefon'];
        $_SESSION['form_jm'] = $_POST['jmeno'];
        $_SESSION['form_ka'] = $_POST['kapacita1'];
    }

    session_start();
    include_once "timeout.php";
    if (empty ( $_SESSION ['timeout'] ) || !isset ( $_SESSION ['timeout'] )) {
        timeout();
    }
    if ((($_SESSION["rola"] == "majitel") || ($_SESSION["rola"] == "provozni")) &&(time () >= (int) $_SESSION ['timeout'])) {
        $_SESSION['message'] = "Byl jste automaticky odhlášen";
        $_SESSION['msg_flag'] = 1;
        header("location: index.php");
        die();
    }
    timeout();
    if (!isset($_SESSION["login"])){
    header("location: index.php");
    die();
    }
    
    unset($_SESSION ['form_po']);
    unset($_SESSION['form_te']);
    unset($_SESSION['form_jm']);
    unset($_SESSION['form_ka']);


  	$db = connect_to_serv();
    $poznamka=$telefon=$jmeno=$od=$do=$kapacita="";
    $poznamka = mysqli_real_escape_string($db,$_POST['poznamka']);
    $telefon = mysqli_real_escape_string($db,$_POST['telefon']);
    $jmeno = mysqli_real_escape_string($db,$_POST['jmeno']);
    $kapacita = mysqli_real_escape_string($db,$_POST['kapacita1']);
    $od = mysqli_real_escape_string($db,$_POST['datum_od1']);
    $do = mysqli_real_escape_string($db,$_POST['datum_do1']);

    if ($kapacita == ""){
        $query = "INSERT INTO rezervace (`jmeno`, `telefon`, `poznamka`) VALUES ('$jmeno','$telefon','$poznamka')";
    } else $query = "INSERT INTO rezervace (`pocet_osob`, `jmeno`, `telefon`, `poznamka`) VALUES ('$kapacita','$jmeno','$telefon','$poznamka')";
    
    

    if ((mysqli_query($db, $query) == 0)) {
        if (mysqli_affected_rows($db) != 1) {
            $_SESSION["message"] = "Chyba při vkládání rezervace!";
        } else {
            $_SESSION["message"] = "Chyba při vkládání rezervace!";
        }
        $_SESSION["msg_flag"] = 1;
        save_form_data();
        printf("error: %s\n", mysqli_error($db));
        mysqli_close($db);
        header("location: rezervace_add.php");
        die();
    } else $id = mysqli_insert_id($db);

    foreach ($_POST['stul'] as $id_stul) {
        $query = "INSERT INTO rezervace_stul (`id_rezervace`, `id_stul`, `datum_rezervace`, `datum_do`) VALUES ('$id','$id_stul','$od','$do')";

        if ((mysqli_query($db, $query) == 0)) {
            if (mysqli_affected_rows($db) != 1) {
                $_SESSION["message"] = "Chyba při vkládání rezervace!";
            } else $_SESSION["message"] = "Chyba při vkládání rezervace!";
            $_SESSION["msg_flag"] = 1;
            save_form_data();
            printf("error: %s\n", mysqli_error($db));
            mysqli_close($db);
            header("location: rezervace_add.php");
            die();
        }
    
    }

    $_SESSION["message"] = "Rezervace byla vložena!";
    $_SESSION["msg_flag"] = 0;
    mysqli_close($db);
    header("location: rezervace_list.php");
    die();

?>