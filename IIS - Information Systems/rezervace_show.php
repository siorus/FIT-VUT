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
$id_rez = $_GET['id_rez'];
?>
<div class="col-md-5 center-block">
<table class="table table-bordered table-striped table-hover">
	<tbody>
		<?php
			$db = connect_to_serv();
			$query = "SELECT datum_rezervace,datum_do,jmeno,telefon,poznamka,pocet_osob FROM rezervace NATURAL JOIN rezervace_stul WHERE id_rezervace='$id_rez' GROUP BY datum_rezervace,datum_do,jmeno,telefon,poznamka,pocet_osob";
			if ($result = mysqli_query($db, $query)) {
				while ($row = mysqli_fetch_assoc($result)){
					echo '<tr>';
					echo "<td><label>Rezevováno od</label></td>";
					echo "<td>".$row['datum_rezervace']."</td>";
					echo '</tr>';
					echo '<tr>';
					echo "<td><label>Rezevováno do</label></td>";
					echo "<td>".$row['datum_do']."</td>";
					echo '</tr>';
					echo '<tr>';
					echo "<td><label>Jméno</label></td>";
					echo "<td>".htmlspecialchars($row['jmeno'])."</td>";
					echo '</tr>';
					echo '<tr>';
					echo "<td><label>Telefon</label></td>";
					echo "<td>".htmlspecialchars($row['telefon'])."</td>";
					echo '</tr>';
					echo '<tr>';
					echo "<td><label>Počet osob</label></td>";
					echo "<td>".htmlspecialchars($row['pocet_osob'])."</td>";
					echo '</tr>';
					echo '<tr>';
					echo "<td><label>Poznámka</label></td>";
					echo "<td>".htmlspecialchars($row['poznamka'])."</td>";
					echo '</tr>';
				}
			}
		?>
	</tbody>
</table>

<table class="table table-bordered table-striped table-hover">
	<thead>
		<tr>
			<th>Místnost</th>
			<th>Číslo</th>
			<th>Kapacita</th>
		</tr>
	</thead>
	<tbody>
		<?php
			$query2 = "SELECT mistnost,cislo,kapacita FROM rezervace_stul NATURAL JOIN stul WHERE id_rezervace='$id_rez'";
			if ($result2 = mysqli_query($db, $query2)) {
				while ($row2 = mysqli_fetch_assoc($result2)){
					echo '<tr>';
					echo "<td>".$row2['mistnost']."</td>";
					echo "<td>".$row2['cislo']."</td>";
					echo "<td>".$row2['kapacita']."</td>";
					echo '</tr>';

					}
			}
			mysqli_close($db);
		?>
	</tbody>
</table>


<button type='button' class='btn-lg btn-primary center-block' onclick="window.location.href = '/rezervace_list.php'">ZPĚT</button>


</div>
<?php
include_once "footer.php";
?>