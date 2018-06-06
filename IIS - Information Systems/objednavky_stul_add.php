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
    
    $id = $_GET['id'];
    $mistnost = $_GET['mistnost'];
    $cislo = $_GET['cislo'];

  	$db = connect_to_serv();
    $nazev="";
    $nazev = mysqli_real_escape_string($db,$_POST['nazev']);

    $query = "SELECT nazev,cena FROM polozka_menu WHERE id_menu = '$nazev'";

    if ($result = mysqli_query($db, $query)) {
        $query = "";
        while ($row = mysqli_fetch_assoc($result)){
            $cena = $row['cena'];
            $id_zam = $_SESSION['id'];
            $nazev_polozky = $row['nazev'];
            $query = "INSERT INTO objednana_polozka (`status`,`cena`,`id_menu`,`id_zamestnanec`,`id_stul`,`nazev`) VALUES ('objednano','$cena','$nazev','$id_zam','$id', '$nazev_polozky')";
            if ((mysqli_query($db, $query) == 0)) {
                if (mysqli_affected_rows($db) != 1) { 
                    $_SESSION["message"] = "Chyba při vkládání položky menu!";
                } else $_SESSION["message"] = "Chyba při vkládání položky menu!";
                $_SESSION["msg_flag"] = 1;
                printf("error: %s\n", mysqli_error($db));
            } else {
            $_SESSION["message"] = "Položka menu byla vložena!";
            $_SESSION["msg_flag"] = 0;
            }
        }
    }
    
    mysqli_close($db);
    header("location: objednavky_stul.php?id=$id&mistnost=$mistnost&cislo=$cislo");
    die();

?>