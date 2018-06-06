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
    $ingr = $_GET['ingr'];
    $nazev = $_GET['nazev'];
  	$db = connect_to_serv();

    $query = "DELETE FROM obsahuje WHERE `id_ingredience` = '$ingr' AND `id_menu` ='$id'";

    if ((mysqli_query($db, $query) == 0)) {
        if (mysqli_affected_rows($db) != 1) {
            $_SESSION["message"] = "Chyba při modifikaci stolu!";
        } else $_SESSION["message"] = "Chyba při mazání ingredience!";
        $_SESSION["msg_flag"] = 1;
        printf("error: %s\n", mysqli_error($db));
    } else {
        $_SESSION["message"] = "Ingredience byla smazána!";
        $_SESSION["msg_flag"] = 0;
    }  
    mysqli_close($db);
    header("location: menu_polozka_modify_ingred.php?id=".$id."&nazev=".$nazev);
    die();

?>