<?php
	include_once "functions.php";

    function save_form_data(){
        $_SESSION['form_na'] = $_POST['nazev'];
        $_SESSION ['form_po'] = $_POST['popis'];
        $_SESSION['form_ty'] = $_POST['typ'];
        $_SESSION['form_ce'] = $_POST['cena'];
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
    if (!(($_SESSION["rola"] == "majitel") || ($_SESSION["rola"] == "provozni"))) {
        header("location: permissions.php");
        die();
    }
    
    unset($_SESSION['form_na']);
    unset($_SESSION ['form_po']);
    unset($_SESSION['form_ty']);
    unset($_SESSION['form_ce']);


  	$db = connect_to_serv();
    $nazev=$popis=$typ=$cena="";
    $nazev = mysqli_real_escape_string($db,$_POST['nazev']);
    $popis = mysqli_real_escape_string($db,$_POST['popis']);
    $typ = mysqli_real_escape_string($db,$_POST['typ']);
    $cena = mysqli_real_escape_string($db,$_POST['cena']);


    $query = "";
    $query = "INSERT INTO polozka_menu (`nazev`, `popis`, `typ`, `cena`) VALUES ('$nazev','$popis','$typ','$cena')";
    if ((mysqli_query($db, $query) == 0)) {
        if (mysqli_affected_rows($db) != 1) {
            $_SESSION["message"] = "Chyba při vkládání položky menu!";
        } else $_SESSION["message"] = "Chyba při vkládání položky menu!";
        $_SESSION["msg_flag"] = 1;
        save_form_data();
        printf("error: %s\n", mysqli_error($db));
        mysqli_close($db);
        header("location: menu_polozka_add.php");
    } else {
        $_SESSION["message"] = "Položka menu byla vložena!";
        $_SESSION["msg_flag"] = 0;
        $id = mysqli_insert_id($db);
        mysqli_close($db);
        header("location: menu_polozka_ingred_add.php?id=".$id."&nazev=".$nazev);
    }
   

    die();

?>