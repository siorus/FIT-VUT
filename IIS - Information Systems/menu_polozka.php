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
?>
<div class="col-md-10 center-block">
<button type="button" class="btn btn-primary" onclick="location.href = 'menu_polozka_add.php'">Přidat</button>
<br/>
<br/>
<table class="table table-bordered table-striped table-hover">
	<thead>
		<tr>
			<th>Název</th>
			<th>Typ</th>
			<th>Hmotnost/Objem</th>
			<th>Cena</th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php
			$db = connect_to_serv();
			$query = "SELECT id_menu,nazev,cena,typ FROM polozka_menu WHERE hide_polozka = '0'";
			if ($result = mysqli_query($db, $query)) {
				while ($row = mysqli_fetch_assoc($result)){
					$menu = $row['id_menu'];
					$query2 = "SELECT id_menu,mnozstvi,jednotka FROM obsahuje WHERE id_menu = '$menu' AND hide_obsahuje = '0' ";
					$mnozstvi_g = 0;
					$mnozstvi_ml = 0;
					$jednotka = array("","");
					if ($result2 = mysqli_query($db, $query2)) {
						while ($row2 = mysqli_fetch_assoc($result2)){
							if ($row2['jednotka'] == "ml"){
								$mnozstvi_ml = $mnozstvi_ml + doubleval($row2['mnozstvi']);
								$jednotka[0] = $row2['jednotka'];
							} else if ($row2['jednotka'] == "g") { 
							$mnozstvi_g = $mnozstvi_g + doubleval($row2['mnozstvi']);
							$jednotka[1] = $row2['jednotka'];
							}
						}
					}
					echo '<tr>';
					echo "<td>".htmlspecialchars($row['nazev'])."</td>";
					if ($row['typ'] == "J") {
						echo "<td>Jídlo</td>";
					} else echo "<td>Nápoj</td>";
					echo "<td>";
						if ($jednotka[1] != ""){
							echo htmlspecialchars($mnozstvi_g)." ".htmlspecialchars($jednotka[1]);
						}
						if (($jednotka[1] != "") && ($jednotka[0] != "")) echo " + ";
						if ($jednotka[0] != "") {
							echo htmlspecialchars($mnozstvi_ml). " " . htmlspecialchars($jednotka[0]);
						}
					echo "</td>";
					echo "<td>".htmlspecialchars($row['cena'])."</td>";
					$id = $row['id_menu'];
					$nazev =$row['nazev'];
					echo "<td> <button type='button' class='btn btn-primary center-block' onclick=\" window.location.href = 'menu_polozka_show.php?id=$id'; \">Detaily</button> </td>";
					echo "<td> <button type='button' class='btn btn-success center-block' onclick=\" window.location.href = 'menu_polozka_modify.php?id=$id'; \">Změnit</button> </td>";
					$nazev2 = htmlspecialchars($nazev);
					echo "<td> <button type='button' class='btn btn-success center-block' onclick=\" window.location.href = 'menu_polozka_modify_ingred.php?id=$id&nazev=$nazev2';\">Změnit ingredience</button> </td>";
					echo "<td> <button type='button' class='btn btn-danger center-block' onclick=\" if (confirm('Stlačením OK vymažete položku menu!')) window.location.href ='menu_polozka_delete.php?id=$id';\">Vymazat</button> </td>";
					echo '</tr>';	
				}
			}
			mysqli_close($db);
		?>
	</tbody>
</table>
</div>
<?php
include_once "footer.php";
?>