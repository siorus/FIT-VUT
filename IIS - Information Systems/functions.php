<?php
	function flash_message(){
		if ($_SESSION['msg_flag'] == 1) {
			echo '<div class="flash_msg_fail">';
		} else echo '<div class="flash_msg_ok">';
		echo '<p>'.$_SESSION['message'].'<p/>';
		unset($_SESSION['message']);
		echo '</div>';
	}

	function connect_to_serv(){
		define("DB_HOST","localhost");
	    define("DB_USER","root");
	    define("DB_PASS","korcek");
	    define("DB_DB","IIS");

	    $db = mysqli_connect(DB_HOST, DB_USER, DB_PASS,DB_DB);
	    if (!$db) {
	        echo "Chyba";
	        die('Could not connect: ' . mysqli_error());
	    }
	    mysqli_set_charset($db,"utf8");
	    return $db;
	}
?>
