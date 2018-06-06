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

    $i = 0;
    $where_raw = "";
    foreach($_POST as $key)
    {
        $where_raw = $where_raw. "id_objednavka = " . "'$key'" . " OR ";
        $i++;
    }

    $i--;
    if ($i == 0) {
        header("location: objednavky_stul.php?id=$id&mistnost=$mistnost&cislo=$cislo");
        die();
    }
    $where_clause = str_replace("OR id_objednavka = '' OR", "", $where_raw);

        

    $query = 'SELECT SUM(cena) as suma FROM `objednana_polozka` WHERE ' . $where_clause;
    if ($result = mysqli_query($db, $query)) {
        while ($row = mysqli_fetch_assoc($result)){
            $cena = $row['suma'];
            $_SESSION["msg_flag"] = 0;
            }
    }
    $query = "";
    $id_zam = $_SESSION['id'];
    $query = "INSERT INTO uctenka (`suma`,`id_zamestnanec`) VALUES ('$cena', '$id_zam')";
    if ((mysqli_query($db, $query) == 0)) {
        if (mysqli_affected_rows($db) != 1) { 
            $_SESSION["message"] = "Chyba při vkládání účtenky, účtenka byla dříve vložena!";
        } else $_SESSION["message"] = "Chyba při vkládání účtenky!";
        $_SESSION["msg_flag"] = 1;
        printf("error: %s\n", mysqli_error($db));
    } else {
        $id_uctenka = mysqli_insert_id($db);
        $_SESSION["message"] = "Účtenka byla vložena!";
        $_SESSION["msg_flag"] = 0;
    }

    $query = "";
    foreach($_POST as $key){  
        $query = "UPDATE objednana_polozka SET status = 'zuctovano',id_uctenka = '$id_uctenka' WHERE id_objednavka = " . "'". $key."'";
        if ((mysqli_query($db, $query) == 0)) {
            if (mysqli_affected_rows($db) != 1) { 
                $_SESSION["message"] = "Chyba při vkládání účtenky!";
            } else $_SESSION["message"] = "Chyba při vkládání účtenky!";
            $_SESSION["msg_flag"] = 1;
            printf("error: %s\n", mysqli_error($db));
        } else {
            $_SESSION["message"] = "Účtenka byla vložena!";
            $_SESSION["msg_flag"] = 0;
        }
        $query = "";
    }
    mysqli_close($db);
    
    header("location: objednavky_stul.php?id=$id&mistnost=$mistnost&cislo=$cislo");
    die();

?>