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

    
    $query = "SELECT * FROM obsahuje WHERE hide_obsahuje = '1' AND id_menu = '$id' ";
    if (($result = mysqli_query($db, $query)) && (mysqli_affected_rows($db) == 1)) {
        $query = "";
        while ($row = mysqli_fetch_assoc($result)){
            $id_ingred = $row['id_ingredience'];
            $query = "UPDATE obsahuje SET hide_obsahuje = '0', mnozstvi = '$hmotnost', jednotka = '$jednotka' WHERE `id_menu` = '$id' and `id_ingredience` = '$id_ingred' ";
            if ((mysqli_query($db, $query) == 0)) {
                if (mysqli_affected_rows($db) != 1) {
                    $_SESSION["message"] = "Chyba při přidáváni ingredience k položce menu!";
                } else $_SESSION["message"] = "Chyba při přidáváni ingredience k položce menu!";
                $_SESSION["msg_flag"] = 1;
                save_form_data();
                printf("error: %s\n", mysqli_error($db));
                mysqli_close($db);
                header("location: menu_polozka_modify_ingred.php?id=".$id."&nazev=".$nazev_po);
            } else {
                $_SESSION["message"] = "Ingrediece k položka menu byla vložena!";
                $_SESSION["msg_flag"] = 0;
                mysqli_close($db);
                header("location: menu_polozka_modify_ingred.php?id=".$id."&nazev=".$nazev_po);
            }
        }
    } else {
        $query = "";
        $query = "INSERT INTO obsahuje (`id_menu`, `id_ingredience`, `mnozstvi`, `jednotka`) VALUES ('$id','$id_ingred','$hmotnost','$jednotka')";
        if ((mysqli_query($db, $query) == 0)) {
            if (mysqli_affected_rows($db) != 1) {
                #$_SESSION["message"] = $query;
                $_SESSION["message"] = "Chyba při přidáváni ingredience k položce menu, ingredience je už s položkou asociována!";
            } else $_SESSION["message"] = "Chyba při přidáváni ingredience k položce menu!";
            $_SESSION["msg_flag"] = 1;
            printf("error: %s\n", mysqli_error($db));
            mysqli_close($db);
            header("location: menu_polozka_modify_ingred_add.php?id=".$id."&nazev=".$nazev_po);
        } else {
            $_SESSION["message"] = "Ingrediece k položka menu byla vložena!";
            $_SESSION["msg_flag"] = 0;
            header("location: menu_polozka_modify_ingred.php?id=".$id."&nazev=".$nazev_po);
        }
    }

    die();

?>