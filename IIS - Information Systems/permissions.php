<?php 
session_start();
include_once "header.php";
if (!isset($_SESSION['login'])){
	header("location: index.php");
	die();
}
?>
<h1 id="permission_denied">Nemáte oprávnění pro přístup do sekce!</h1>
<?php include_once "footer.php";
?>