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
$nazev = $_GET['nazev'];
?>
<div class="col-md-7 center-block">
<button type="button" class="btn btn-primary" onclick="location.href = 'menu_polozka_modify_ingred_add.php?id=<?php echo $id; ?>&nazev=<?php echo htmlspecialchars($nazev); ?>'">Přidat</button>

<table class="table table-bordered table-striped table-hover">
	<caption><font size="4">Modifikace ingrediencí k položce <?php echo '"'. htmlspecialchars($nazev). '"'?></font></caption>
	<thead>
		<tr>
			<th>Obsahuje</th>
			<th>Hmotnost/Objem</th>
			<th>Jednotka</th>
			<th></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php
			$query2 = "SELECT * FROM `obsahuje` as o NATURAL JOIN `ingredience` as i  WHERE id_menu='$id' AND hide_obsahuje = '0' AND hide_ingred = '0'";
			$db = connect_to_serv();
			if ($result2 = mysqli_query($db, $query2)) {
				while ($row2 = mysqli_fetch_assoc($result2)){
					echo '<tr>';
					echo "<td>".htmlspecialchars($row2['nazev_ingredience'])."</td>";
					echo "<td>".$row2['mnozstvi']."</td>";
					echo "<td>".$row2['jednotka']."</td>";
					$ingr = $row2['id_ingredience'];
					$nazev2 = htmlspecialchars($nazev);
					echo "<td> <button type='button' class='btn btn-success center-block' onclick= \"window.location.href = 'menu_polozka_modify_ingred_modify.php?id=$id&nazev=$nazev2&ingr=$ingr';\">Změnit</button> </td>";
					echo "<td> <button type='button' class='btn btn-danger center-block' onclick=\" if (confirm('Stlačením OK vymažete ingredienci k menu!')) window.location.href ='menu_polozka_modify_ingred_delete.php?id=$id&ingr=$ingr&nazev=$nazev2'\">Vymazat</button> </td>";
					echo '</tr>';

					}
			}
			mysqli_close($db);
		?>
	</tbody>
</table>
<button type="button" class="btn-lg btn-primary center-block" onclick=" window.location.href = '/menu_polozka.php'">Zpět</button>
</div>
<?php
include_once "footer.php";
?>