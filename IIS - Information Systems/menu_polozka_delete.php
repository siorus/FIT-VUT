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

    $query = "UPDATE polozka_menu SET hide_polozka = '1' WHERE `id_menu` = $id";
    $query2 = "UPDATE obsahuje SET hide_obsahuje = '1' WHERE `id_menu` = $id";
    $query3 = "SELECT * FROM obsahuje WHERE `id_menu` = $id";
    if (($result = (mysqli_query($db, $query3))) && (mysqli_affected_rows($db) != 0)) {
    	while ($row = mysqli_fetch_assoc($result)){
        if ((mysqli_query($db, $query2) == 0)) {
            $_SESSION["message"] = "Chyba při mazání položky!";
            $_SESSION["msg_flag"] = 1;
            printf("error: %s\n", mysqli_error($db));
        } else {
            $_SESSION["message"] = "Položka byla smazána!";
            $_SESSION["msg_flag"] = 0;
        }
    	}
    }      

    if ((mysqli_query($db, $query) == 0)) {
        $_SESSION["message"] = "Chyba při mazání položky!";
        $_SESSION["msg_flag"] = 1;
        printf("error: %s\n", mysqli_error($db));
    } else {
        $_SESSION["message"] = "Položka byla smazána!";
        $_SESSION["msg_flag"] = 0;
    }  


    mysqli_close($db);
    header("location: menu_polozka.php");
    die();

?>