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
if (!(($_SESSION["rola"] == "majitel") || ($_SESSION["rola"] == "provozni"))) {
	header("location: permissions.php");
	die();
}
if (isset($_SESSION['message'])){
	flash_message($_SESSION['message']);
	unset($_SESSION['message']);
	unset($_SESSION['msg_flag']);
}
$id = $_GET['id'];
?>
<div class="col-md-6 center-block">
<table class="table table-bordered table-striped table-hover">
	<tbody>
		<?php
			$db = connect_to_serv();
			$query = "SELECT * FROM polozka_menu WHERE id_menu='$id' AND hide_polozka = '0' ";
			if ($result = mysqli_query($db, $query)) {
				while ($row = mysqli_fetch_assoc($result)){
					echo '<tr>';
					echo "<td><label>Název</label></td>";
					echo "<td>".htmlspecialchars($row['nazev'])."</td>";
					echo '</tr>';
					echo '<tr>';
					echo "<td><label>Popis</label></td>";
					echo "<td>".htmlspecialchars($row['popis'])."</td>";
					echo '</tr>';
					echo '<tr>';
					echo "<td><label>Jídlo/Nápoj</label></td>";
					if ($row['typ'] == 'J'){
						echo "<td>Jídlo</td>";
					} else echo "<td>Nápoj</td>";
					echo '</tr>';
					echo "<td><label>Cena</label></td>";
					echo "<td>".$row['cena']."</td>";
					echo '</tr>';
				}
			}
		?>
	</tbody>
</table>
<table class="table table-bordered table-striped table-hover">
	<thead>
		<tr>
			<th>Obsahuje</th>
			<th>Hmotnost/Objem</th>
			<th>Jednotka</th>
		</tr>
	</thead>
	<tbody>
		<?php
			$query2 = "SELECT * FROM `obsahuje` as o NATURAL JOIN `ingredience` as i  WHERE id_menu='$id' AND hide_obsahuje = '0' AND hide_ingred = '0'";
			if ($result2 = mysqli_query($db, $query2)) {
				while ($row2 = mysqli_fetch_assoc($result2)){
					echo '<tr>';
					echo "<td>".htmlspecialchars($row2['nazev_ingredience'])."</td>";
					echo "<td>".$row2['mnozstvi']."</td>";
					echo "<td>".$row2['jednotka']."</td>";
					echo '</tr>';

					}
			}
			mysqli_close($db);
		?>
	</tbody>
</table>


<button type='button' class='btn-lg btn-primary center-block' onclick="window.location.href = '/menu_polozka.php'">ZPĚT</button>

</div>

<?php
include_once "footer.php";
?>