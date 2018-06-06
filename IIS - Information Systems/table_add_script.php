<?php
	include_once "functions.php";

    function save_form_data(){
        $_SESSION['form_mi'] = $_POST['mistnost'];
        $_SESSION ['form_ci'] = $_POST['cislo'];
        $_SESSION['form_ka'] = $_POST['kapacita'];

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
    
    unset($_SESSION['form_mi']);
    unset($_SESSION ['form_ci']);
    unset($_SESSION['form_ka']);


  	$db = connect_to_serv();
    $mistnost=$cislo=$kapacita="";
    $mistnost = mysqli_real_escape_string($db,$_POST['mistnost']);
    $cislo = mysqli_real_escape_string($db,$_POST['cislo']);
    $kapacita = mysqli_real_escape_string($db,$_POST['kapacita']);

    $query = "SELECT mistnost, cislo,kapacita,id_stul FROM stul WHERE hide_stul = '1' AND mistnost = '$mistnost' AND cislo = '$cislo' ";
    if (($result = mysqli_query($db, $query)) && (mysqli_affected_rows($db) == 1)) {
        $query = "";
        while ($row = mysqli_fetch_assoc($result)){
            $id = $row['id_stul'];
            $query = "UPDATE stul SET hide_stul = '0', kapacita = '$kapacita' WHERE `id_stul` = '$id'";
            if ((mysqli_query($db, $query) == 0)) {
                if (mysqli_affected_rows($db) != 1) {
                    $_SESSION["message"] = "Chyba při vkládání stolu!";
                } else $_SESSION["message"] = "Chyba při vkládání stolu!";
                $_SESSION["msg_flag"] = 1;
                save_form_data();
                printf("error: %s\n", mysqli_error($db));
                mysqli_close($db);
                header("location: table_add.php");
            } else {
                $_SESSION["message"] = "Stůl byl vložen!";
                $_SESSION["msg_flag"] = 0;
                mysqli_close($db);
                header("location: table_list.php");
            }
        }
    } else {
        $query = "";
        $query = "INSERT INTO stul (`mistnost`, `cislo`, `kapacita`) VALUES ('$mistnost','$cislo','$kapacita')";  

        if ((mysqli_query($db, $query) == 0)) {
            if (mysqli_affected_rows($db) != 1) {
                $_SESSION["message"] = "Chyba při vkládání stolu, stůl v dané místnosti už existuje!";
            } else $_SESSION["message"] = "Chyba při vkládání stolu!";
            $_SESSION["msg_flag"] = 1;
            save_form_data();
            printf("error: %s\n", mysqli_error($db));
            mysqli_close($db);
            header("location: table_add.php");
        } else {
            $_SESSION["message"] = "Stůl byl vložen!";
            $_SESSION["msg_flag"] = 0;
            mysqli_close($db);
            header("location: table_list.php");
        }
    }
    die();
?>