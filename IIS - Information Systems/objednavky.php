<?php
session_start(); 
include_once "header.php";
include_once "functions.php";
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
if (isset($_SESSION['message'])){
	flash_message($_SESSION['message']);
	unset($_SESSION['message']);
	unset($_SESSION['msg_flag']);
}
?>
<div id="bar">
	<caption><font size="6">Bar</font></caption>
	<br/>
	<?php
			$db = connect_to_serv();
			$query = "SELECT * FROM stul WHERE mistnost='bar' AND hide_stul = '0'";
			if ($result = mysqli_query($db, $query)) {
				while ($row = mysqli_fetch_assoc($result)){
					$id = $row['id_stul'];
					$mistnost = $row['mistnost'];
					$cislo = $row['cislo'];
					echo "<a href='objednavky_stul.php?id=$id&mistnost=$mistnost&cislo=$cislo'><div class='objednavky'>";
					echo "<div>";
					echo "<p>" . $row['cislo'];
					echo "</p>";
					echo "</div>";
					echo "</div></a>";
				}
			}
			#mysqli_close($db);
	?>
</div>

<div id="predni">
	<caption><font size="6">Přední zahrádka</font></caption>
	<br/>
	<?php
			#$db = connect_to_serv();
			$query = "SELECT * FROM stul WHERE mistnost='predni zahradka' AND hide_stul = '0'";
			if ($result = mysqli_query($db, $query)) {
				while ($row = mysqli_fetch_assoc($result)){
					$id = $row['id_stul'];
					$mistnost = $row['mistnost'];
					$cislo = $row['cislo'];
					echo "<a href='objednavky_stul.php?id=$id&mistnost=$mistnost&cislo=$cislo'><div class='objednavky'>";
					echo "<div>";
					echo "<p>" . $row['cislo'];
					echo "</p>";
					echo "</div>";
					echo "</div></a>";
				}
			}
			#mysqli_close($db);
	?>
</div>

<div id="predni">
	<caption><font size="6">Zadní zahrádka</font></caption>
	<br/>
	<?php
			#$db = connect_to_serv();
			$query = "SELECT * FROM stul WHERE mistnost='zadni zahradka' AND hide_stul = '0'";
			if ($result = mysqli_query($db, $query)) {
				while ($row = mysqli_fetch_assoc($result)){
					$id = $row['id_stul'];
					$mistnost = $row['mistnost'];
					$cislo = $row['cislo'];
					echo "<a href='objednavky_stul.php?id=$id&mistnost=$mistnost&cislo=$cislo'><div class='objednavky'>";
					echo "<div>";
					echo "<p>" . $row['cislo'];
					echo "</p>";
					echo "</div>";
					echo "</div></a>";
				}
			}
			#mysqli_close($db);
	?>
</div>

<div id="sala">
	<caption><font size="6">Sála</font></caption>
	<br/>
	<?php
			#$db = connect_to_serv();
			$query = "SELECT * FROM stul WHERE mistnost='sala' AND hide_stul = '0'";
			if ($result = mysqli_query($db, $query)) {
				while ($row = mysqli_fetch_assoc($result)){
					$id = $row['id_stul'];
					$mistnost = $row['mistnost'];
					$cislo = $row['cislo'];
					echo "<a href='objednavky_stul.php?id=$id&mistnost=$mistnost&cislo=$cislo'><div class='objednavky'>";
					echo "<div>";
					echo "<p>" . $row['cislo'];
					echo "</p>";
					echo "</div>";
					echo "</div></a>";
				}
			}
			#mysqli_close($db);
	?>
</div>

<div id="salonek">
	<caption><font size="6">Salonek</font></caption>
	<br/>
	<?php
			#$db = connect_to_serv();
			$query = "SELECT * FROM stul WHERE mistnost='salonek' AND hide_stul = '0'";
			if ($result = mysqli_query($db, $query)) {
				while ($row = mysqli_fetch_assoc($result)){
					$id = $row['id_stul'];
					$mistnost = $row['mistnost'];
					$cislo = $row['cislo'];
					echo "<a href='objednavky_stul.php?id=$id&mistnost=$mistnost&cislo=$cislo'><div class='objednavky'>";
					echo "<div>";
					echo "<p>" . $row['cislo'];
					echo "</p>";
					echo "</div>";
					echo "</div></a>";
				}
			}
			mysqli_close($db);
	?>
</div>




<?php
include_once "footer.php";
?>