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
  	$db = connect_to_serv();
    $nazev=$popis=$typ=$cena="";
    $nazev = mysqli_real_escape_string($db,$_POST['nazev']);
    $popis = mysqli_real_escape_string($db,$_POST['popis']);
    $typ = mysqli_real_escape_string($db,$_POST['typ']);
    $cena = mysqli_real_escape_string($db,$_POST['cena']);

    $query = "UPDATE polozka_menu SET `nazev`= '$nazev', `popis`= '$popis', `typ`= '$typ', `cena` = '$cena' WHERE `id_menu` = '$id'";
    

    if ((mysqli_query($db, $query) == 0)) {
        if (mysqli_affected_rows($db) != 1) {
            $_SESSION["message"] = $query;
            #$_SESSION["message"] = "Chyba při modifikaci položky menu!";
        } else $_SESSION["message"] = "Chyba při modifikaci položky menu!";
        $_SESSION["msg_flag"] = 1;
        printf("error: %s\n", mysqli_error($db));
        mysqli_close($db);
        header("location: menu_polozka_modify.php?id=".$id);
    } else {
        $_SESSION["message"] = "Položka menu byla změnena!";
        $_SESSION["msg_flag"] = 0;
        mysqli_close($db);
        header("location: menu_polozka.php");
    }

    

    die();

?>