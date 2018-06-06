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

    $id_ob = $_GET['id_ob'];
    $id = $_GET['id'];
    $mistnost = $_GET['mistnost'];
    $cislo = $_GET['cislo'];
  	$db = connect_to_serv();

    $query = "UPDATE objednana_polozka SET hide_objednana = '1' WHERE `id_objednavka` = '$id_ob'";

    if ((mysqli_query($db, $query) == 0)) {
        if (mysqli_affected_rows($db) != 1) {
        $_SESSION["message"] = "Chyba při mazání objednávky!";
        $_SESSION["msg_flag"] = 1;
        printf("error: %s\n", mysqli_error($db));
        }
    } else {
        $_SESSION["message"] = "Objednávka byla smazána!";
        $_SESSION["msg_flag"] = 0;
    }  
    mysqli_close($db);
    header("location: objednavky_stul.php?id=$id&mistnost=$mistnost&cislo=$cislo");
    die();

?>