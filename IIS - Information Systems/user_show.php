<?php
session_start(); 
include_once "header.php";
include_once "functions.php";
if (!isset($_SESSION["login"])){
	header("location: index.php");
	die();
}
if (!(($_SESSION["rola"] == "majitel") || ($_SESSION["rola"] == "provozni"))) {
	header("location: permissions.php");
	die();
}
$id = $_GET['id'];
?>
<div class="col-md-4 center-block">
<table class="table table-bordered table-striped table-hover">
	<tbody>
		<?php
			$db = connect_to_serv();
			$query = "SELECT * FROM zamestnanec WHERE id_zamestnanec='$id'";
			if ($result = mysqli_query($db, $query)) {
				while ($row = mysqli_fetch_assoc($result)){
					echo '<tr>';
					echo "<td><label>Login</label></td>";
					echo "<td>".htmlspecialchars($row['login'])."</td>";
					echo '</tr>';
					echo '<tr>';
					echo "<td><label>Jméno</label></td>";
					echo "<td>".htmlspecialchars($row['jmeno'])."</td>";
					echo '</tr>';
					echo '<tr>';
					echo "<td><label>Příjmení</label></td>";
					echo "<td>".htmlspecialchars($row['prijmeni'])."</td>";
					echo '</tr>';
					echo "<td><label>Funkce</label></td>";
					echo "<td>".htmlspecialchars($row['rola'])."</td>";
					echo '</tr>';
					echo "<td><label>Město</label></td>";
					echo "<td>".htmlspecialchars($row['mesto'])."</td>";
					echo '</tr>';
					echo "<td><label>PSČ</label></td>";
					echo "<td>".htmlspecialchars($row['psc'])."</td>";
					echo '</tr>';
					echo "<td><label>Ulice</label></td>";
					echo "<td>".htmlspecialchars($row['ulice'])."</td>";
					echo '</tr>';
					echo "<td><label>Číslo</label></td>";
					echo "<td>".htmlspecialchars($row['cislo_popisne'])."</td>";
					echo '</tr>';
					echo "<td><label>Telefon</label></td>";
					echo "<td>".htmlspecialchars($row['telefon'])."</td>";
					echo '</tr>';
					echo "<td><label>Email</label></td>";
					echo "<td>".htmlspecialchars($row['email'])."</td>";
					echo '</tr>';
					echo "<td><label>Heslo</label></td>";
					echo "<td>".htmlspecialchars($row['heslo'])."</td>";
					echo '</tr>';
					echo "<td><label>Číslo OP</label></td>";
					echo "<td>".htmlspecialchars($row['cislo_op'])."</td>";
					echo '</tr>';
				}
			}
			mysqli_close($db);
		?>
	</tbody>
</table>
<button type='button' class='btn-lg btn-primary center-block' onclick="window.location.href = '/user_list.php'">ZPĚT</button>
</div>
<?php
include_once "footer.php";
?>