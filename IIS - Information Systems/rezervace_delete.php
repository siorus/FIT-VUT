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
    
    $id_rez = $_GET['id_rez'];
    $db = connect_to_serv();

    $query = "SELECT id_stul FROM rezervace NATURAL JOIN rezervace_stul NATURAL JOIN stul WHERE id_rezervace='$id_rez'";
    if ($result = mysqli_query($db, $query) ) {
        $query = "";
        while($row = mysqli_fetch_assoc($result)){
            $id_stul = $row['id_stul'];
            $query = "UPDATE rezervace_stul SET hide_rezstul='1' WHERE `id_rezervace` = '$id_rez' AND `id_stul` = '$id_stul'";
            if ((mysqli_query($db, $query) == 0)) {
                if (mysqli_affected_rows($db) != 1) {
                    $_SESSION["message"] = "Chyba při mazání rezervace1!";
                }
                $_SESSION["msg_flag"] = 1;
                printf("error: %s\n", mysqli_error($db));
                mysqli_close($db);
                header("location: rezervace_list.php");
                die(); 
            }
            $id_stul = "";
        }
    }
    $query = "";
    $query = "UPDATE rezervace SET `hide_rez`='1' WHERE `id_rezervace` = '$id_rez'";

    if ((mysqli_query($db, $query) == 0)) {
        if (mysqli_affected_rows($db) != 1) {
            $_SESSION["message"] = "Chyba při mazání rezervace2!";
        }
        $_SESSION["msg_flag"] = 1;
        printf("error: %s\n", mysqli_error($db));
        mysqli_close($db);
        header("location: rezervace_list.php");
        die();

    }

    $_SESSION["message"] = "Rezervace byla smazána!";
    $_SESSION["msg_flag"] = 0;
    mysqli_close($db);
    header("location: rezervace_list.php");
    die();

?>