<?php
	include_once "functions.php";

    function save_form_data(){
        $_SESSION['form_jm'] = $_POST['jmeno'];
        $_SESSION ['form_pr'] = $_POST['prijmeni'];
        $_SESSION['form_op'] = $_POST['op'];
        $_SESSION['form_te'] = $_POST['telefon'];
        $_SESSION['form_em'] = $_POST['email'];
        $_SESSION['form_ul'] = $_POST['ulice'];
        $_SESSION['form_ci'] = $_POST['cislo'];
        $_SESSION['form_me'] = $_POST['mesto'];
        $_SESSION['form_ps'] = $_POST['psc'];
        $_SESSION['form_lo'] = $_POST['login'];
        $_SESSION['form_fu'] = $_POST['funkce'];
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
    
    unset($_SESSION['form_jm']);
    unset($_SESSION ['form_pr']);
    unset($_SESSION['form_op']);
    unset($_SESSION['form_te']);
    unset($_SESSION['form_em']);
    unset($_SESSION['form_ul']);
    unset($_SESSION['form_ci']);
    unset($_SESSION['form_me']);
    unset($_SESSION['form_ps']);
    unset($_SESSION['form_lo']);
    unset($_SESSION['form_fu']);

    if (($_POST['heslo']) != ($_POST['heslo_zop'])){
        save_form_data();
        $_SESSION["message"] = "Hesla se neshodují!";
        $_SESSION["msg_flag"] = 1;
        header("location: user_add.php");
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


    
    $query = "SELECT * FROM zamestnanec WHERE hide_zam = '1' AND login = '$login' AND cislo_op = '$op' ";
    if (($result = mysqli_query($db, $query)) && (mysqli_affected_rows($db) == 1)) {
        $query = "";
        while ($row = mysqli_fetch_assoc($result)){
            $id = $row['id_zamestnanec'];
            $query = "UPDATE zamestnanec SET `jmeno` = '$jmeno', `prijmeni` = '$prijmeni', `cislo_op` = '$op', `telefon` = '$telefon', `email` = '$email', `ulice` = '$ulice', `cislo_popisne` = '$cislo', `mesto` = '$mesto', `psc` = '$psc', `rola` = '$funkce', `heslo` = '$heslo', `login` = '$login', hide_zam = '0' WHERE `id_zamestnanec` = $id";
            if ((mysqli_query($db, $query) == 0)) {
                if (mysqli_affected_rows($db) != 1) {
                    $_SESSION["message"] = "Chyba při vkládání užívatele!";
                } else $_SESSION["message"] = "Chyba při vkládání užívatele!";
                $_SESSION["msg_flag"] = 1;
                save_form_data();
                printf("error: %s\n", mysqli_error($db));
                mysqli_close($db);
                header("location: user_add.php");
            } else {
                $_SESSION["message"] = "Uživatel byl vložen!";
                $_SESSION["msg_flag"] = 0;
                mysqli_close($db);
                header("location: user_list.php");
            }
        }
    } else {
        $query = "";
        $query = "INSERT INTO zamestnanec (`jmeno`, `prijmeni`, `cislo_op`, `telefon`, `email`, `ulice`, `cislo_popisne`, `mesto`, `psc`, `rola`, `heslo`, `login`) VALUES ('$jmeno','$prijmeni','$op','$telefon','$email','$ulice','$cislo','$mesto','$psc','$funkce','$heslo','$login')";
        if ((mysqli_query($db, $query) == 0)) {
            if (mysqli_affected_rows($db) != 1) {
                $_SESSION["message"] = "Chyba při vkládání užívatele, uživatel s daným loginem už existuje!";
            } else $_SESSION["message"] = "Chyba při vkládání užívatele!";
            $_SESSION["msg_flag"] = 1;
            save_form_data();
            printf("error: %s\n", mysqli_error($db));
            mysqli_close($db);
            header("location: user_add.php");
        } else {
            $_SESSION["message"] = "Uživatel byl vložen!";
            $_SESSION["msg_flag"] = 0;
            mysqli_close($db);
            header("location: user_list.php");
        }
    }  
    die();
?>