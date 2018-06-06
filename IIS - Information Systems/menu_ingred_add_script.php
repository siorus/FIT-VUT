<?php
    session_start();
	include_once "functions.php";
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

    function save_form_data(){
        $_SESSION['nazev'] = $_POST['nazev'];
    }

    if (!isset($_SESSION["login"])){
    header("location: index.php");
    die();
    }
    if (!(($_SESSION["rola"] == "majitel") || ($_SESSION["rola"] == "provozni"))) {
        header("location: permissions.php");
        die();
    }
    
    unset($_SESSION['nazev']);

  	$db = connect_to_serv();
    $nazev="";
    $nazev = mysqli_real_escape_string($db,$_POST['nazev']);

    $query = "SELECT * FROM ingredience WHERE hide_ingred = '1' AND nazev_ingredience = '$nazev'";
    if (($result = mysqli_query($db, $query)) && (mysqli_affected_rows($db) == 1)) {
        $query = "";
        while ($row = mysqli_fetch_assoc($result)){
            $id = $row['id_ingredience'];
            $query = "UPDATE ingredience SET hide_ingred = '0' WHERE `id_ingredience` = $id";
            if ((mysqli_query($db, $query) == 0)) {
                if (mysqli_affected_rows($db) != 1) {
                    $_SESSION["message"] = "Chyba při vkládání ingredience!";
                } else $_SESSION["message"] = "Chyba při vkládání ingredience!";
                $_SESSION["msg_flag"] = 1;
                save_form_data();
                printf("error: %s\n", mysqli_error($db));
                mysqli_close($db);
                header("location: table_add.php");
            } else {
                $_SESSION["message"] = "Ingredience byla vložena!";
                $_SESSION["msg_flag"] = 0;
                mysqli_close($db);
                header("location: table_list.php");
            }
        }
    } else {
        $query = "";
        $query = "INSERT INTO ingredience (`nazev_ingredience`) VALUES ('$nazev')";
        if ((mysqli_query($db, $query) == 0)) {
            if (mysqli_affected_rows($db) != 1) {
                $_SESSION["message"] = "Chyba při vkládání ingredience, ingredience už existuje!";
            } else $_SESSION["message"] = "Chyba při vkládání ingredience!";
            $_SESSION["msg_flag"] = 1;
            save_form_data();
            printf("error: %s\n", mysqli_error($db));
        } else {
            $_SESSION["message"] = "Ingredience byla vložena!";
            $_SESSION["msg_flag"] = 0;
        }
    }
    mysqli_close($db);
    header("location: menu_ingred.php");
    die();

?>