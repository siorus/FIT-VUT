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
    $id = $_GET['id'];
    $nazev_po = $_GET['nazev'];
  	$db = connect_to_serv();
    $nazev=$popis=$typ=$cena="";
    $id_ingred = mysqli_real_escape_string($db,$_POST['nazev']);
    $hmotnost = mysqli_real_escape_string($db,$_POST['hmotnost']);
    $jednotka = mysqli_real_escape_string($db,$_POST['jednotka']);

    $query = "INSERT INTO obsahuje (`id_menu`, `id_ingredience`, `mnozstvi`, `jednotka`) VALUES ('$id','$id_ingred','$hmotnost','$jednotka')";
    

    if ((mysqli_query($db, $query) == 0)) {
        if (mysqli_affected_rows($db) != 1) {
            #$_SESSION["message"] = $query;
            $_SESSION["message"] = "Chyba při přidáváni ingredience k položce menu, ingredience je už s položkou asociována!";
        } else $_SESSION["message"] = "Chyba při přidáváni ingredience k položce menu!";
        $_SESSION["msg_flag"] = 1;
        printf("error: %s\n", mysqli_error($db));
    } else {
        $_SESSION["message"] = "Položka menu byla vložena!";
        $_SESSION["msg_flag"] = 0;
    }

    mysqli_close($db);
    header("location: menu_polozka_ingred_add.php?id=".$id."&nazev=".$nazev_po);
    die();

?>