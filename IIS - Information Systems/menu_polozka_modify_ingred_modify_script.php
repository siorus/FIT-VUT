<?php
    include_once "functions.php";

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
    $id = $_GET['id'];
    $nazev_po = $_GET['nazev'];
    $db = connect_to_serv();
    $nazev=$popis=$typ=$cena="";
    $id_ingred = mysqli_real_escape_string($db,$_POST['nazev']);
    $hmotnost = mysqli_real_escape_string($db,$_POST['hmotnost']);
    $jednotka = mysqli_real_escape_string($db,$_POST['jednotka']);

    $query = "UPDATE obsahuje SET `mnozstvi` = '$hmotnost', `jednotka` = '$jednotka' WHERE `id_menu` = '$id' AND `id_ingredience` = '$id_ingred'";
    

    if ((mysqli_query($db, $query) == 0)) {
        if (mysqli_affected_rows($db) != 1) {
            #$_SESSION["message"] = $query;
            $_SESSION["message"] = "Chyba při modifikaci ingredience k položce menu!";
        } else $_SESSION["message"] = "Chyba při modifikacia ingredience k položce menu!";
        $_SESSION["msg_flag"] = 1;
        printf("error: %s\n", mysqli_error($db));
        mysqli_close($db);
        header("location: menu_polozka_modify_ingred_modify.php?id=".$id."&nazev=".$nazev_po. "&ingr=" . $id_ingred);
    } else {
        $_SESSION["message"] = "Ingredience k položce byla modifikována!";
        $_SESSION["msg_flag"] = 0;
        header("location: menu_polozka_modify_ingred.php?id=".$id."&nazev=".$nazev_po);
    }


    die();

?>