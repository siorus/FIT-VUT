<?php
	session_start();
  	include_once "functions.php";

  	$db = connect_to_serv();
   
    $query = mysqli_query($db, "SELECT id_zamestnanec,jmeno, prijmeni, rola, heslo, login FROM zamestnanec WHERE login='" . $_POST["login"] . "' and heslo= '". $_POST["heslo"]."'");
    
    if(mysqli_num_rows($query) == 1) {
    	$row = mysqli_fetch_array($query);
		$_SESSION["login"] = $row['login'];
		$_SESSION["id"] = $row['id_zamestnanec'];
		$_SESSION["jmeno"] = $row['jmeno'];
		$_SESSION["prijmeni"] = $row['prijmeni'];
		$_SESSION["rola"] = $row['rola'];
		mysqli_close($db);
	    header("location: objednavky.php");
		die();
	} else {
		$_SESSION["message"] = "Nesprávný login nebo heslo!";
		$_SESSION["msg_flag"] = 1;
		mysqli_close($db);
	    header("location: index.php");
		die();
	}

?>