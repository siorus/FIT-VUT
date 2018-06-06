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
    if (($_POST['heslo']) != ($_POST['heslo_zop'])){
        $_SESSION["message"] = "Hesla se neshodují!";
        $_SESSION["msg_flag"] = 1;
        header("location: user_modify.php?id=".$id);
        die();
    }
    $email ="";
    $email = $_POST['email'];
    /*if (!((preg_match("/^.+\@.+\..+$/", $email)) || (""==trim($_POST['email'])))){
        save_form_data();
        $_SESSION["message"] = "Email má zlý formát!";
        $_SESSION["msg_flag"] = 1;
        header("location: add_user.php");
        die();
    }
    */
  	$db = connect_to_serv();

    $jmeno=$prijmeni=$op=$telefon=$email=$ulice=$cislo=$mesto=$psc=$funkce=$heslo=$login="";
    $jmeno = mysqli_real_escape_string($db,$_POST['jmeno']);
    $prijmeni = mysqli_real_escape_string($db,$_POST['prijmeni']);
    $op = mysqli_real_escape_string($db,$_POST['op']);
    $telefon = mysqli_real_escape_string($db,$_POST['telefon']);
    $email = mysqli_real_escape_string($db,$_POST['email']);
    $ulice = mysqli_real_escape_string($db,$_POST['ulice']);
    $cislo = mysqli_real_escape_string($db,$_POST['cislo']);
    $mesto = mysqli_real_escape_string($db,$_POST['mesto']);
    $psc = mysqli_real_escape_string($db,$_POST['psc']);
    $funkce = mysqli_real_escape_string($db,$_POST['funkce']);
    $heslo = mysqli_real_escape_string($db,$_POST['heslo']);
    $login = mysqli_real_escape_string($db,$_POST['login']);
    if ($heslo == ""){
        $query = "UPDATE zamestnanec SET `jmeno` = '$jmeno', `prijmeni` = '$prijmeni', `cislo_op` = '$op', `telefon` = '$telefon', `email` = '$email', `ulice` = '$ulice', `cislo_popisne` = '$cislo', `mesto` = '$mesto', `psc` = '$psc', `rola` = '$funkce', `login` = '$login' WHERE `id_zamestnanec` = $id";
    } else $query = "UPDATE zamestnanec SET `jmeno` = '$jmeno', `prijmeni` = '$prijmeni', `cislo_op` = '$op', `telefon` = '$telefon', `email` = '$email', `ulice` = '$ulice', `cislo_popisne` = '$cislo', `mesto` = '$mesto', `psc` = '$psc', `rola` = '$funkce', `heslo` = '$heslo', `login` = '$login' WHERE `id_zamestnanec` = $id";

    if ((mysqli_query($db, $query) == 0)) {
        $_SESSION["message"] = "Chyba při vkládání užívatele!";
        $_SESSION["msg_flag"] = 1;
        printf("error: %s\n", mysqli_error($db));
        mysqli_close($db);
        header("location: user_modify.php?id=".$id);
    } else {
        $_SESSION["message"] = "Uživatel byl vložen!";
        $_SESSION["msg_flag"] = 0;
        mysqli_close($db);
        header("location: user_list.php");
    }

    

    die();

?>