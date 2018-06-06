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
<div class="col-md-5 center-block">
<button type="button" class="btn btn-primary" onclick="location.href = 'table_add.php'">Přidat</button>
<table class="table table-bordered table-striped table-hover">
	<caption><font size="6">Bar</font></caption>
	<thead>
		<tr>
			<th>Číslo stolu</th>
			<th>Kapacita</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php
			$db = connect_to_serv();
			$query = "SELECT * FROM stul WHERE mistnost='bar' AND hide_stul = '0'";
			if ($result = mysqli_query($db, $query)) {
				while ($row = mysqli_fetch_assoc($result)){
					echo '<tr>';
					echo "<td>".$row['cislo']."</td>";
					$id = $row['id_stul'];
					echo '<td><form role="form" class="form-inline" method="post" action="table_mod_script.php?id=' . $id . '"'. '><input id="kapacita" name="kapacita" type="number" method="post" value="'. $row['kapacita']. '"' . ' class="form-control input-md" required="true"  min="1" max="99999999999" oninvalid="this.setCustomValidity("Vyplňte kapacitu s max. délkou 11 znaků")" oninput="setCustomValidity("")"><button style="margin: 0px 0px 0px 5px;" type="submit" class="btn btn-success">Změnit</button></form></td>';
					#echo "<td> <button type='button' class='btn btn-success' onclick='window.location.href = `table_mod_script.php?id=$id`'>Změnit</button> </td>";
					echo "<td> <button type='button' class='btn btn-danger center-block' onclick=\" if (confirm('Stlačením OK vymažete stůl!')) window.location.href ='table_delete.php?id=$id';\">Vymazat</button> </td>";
					echo '</tr>';
				}
			}
			#mysqli_close($db);
		?>
	</tbody>
</table>

<table class="table table-bordered table-striped table-hover">
	<caption><font size="6">Sála</font></caption>
	<thead>
		<tr>
			<th>Číslo stolu</th>
			<th>Kapacita</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php
			#$db = connect_to_serv();
			$query = "SELECT * FROM stul WHERE mistnost='sala' AND hide_stul = '0'";
			if ($result = mysqli_query($db, $query)) {
				while ($row = mysqli_fetch_assoc($result)){
					echo '<tr>';
					echo "<td>".$row['cislo']."</td>";
					$id = $row['id_stul'];
					echo '<td><form role="form" class="form-inline" method="post" action="table_mod_script.php?id=' . $id . '"'. '><input id="kapacita" name="kapacita" type="number" method="post" value="'. $row['kapacita']. '"' . ' class="form-control input-md" required="true"  min="1" max="99999999999" oninvalid="this.setCustomValidity("Vyplňte kapacitu s max. délkou 11 znaků")" oninput="setCustomValidity("")"><button style="margin: 0px 0px 0px 5px;" type="submit" class="btn btn-success">Změnit</button></form></td>';
					#echo "<td> <button type='button' class='btn btn-success' onclick='window.location.href = `table_mod_script.php?id=$id`'>Změnit</button> </td>";
					echo "<td> <button type='button' class='btn btn-danger' onclick=\" if (confirm('Stlačením OK vymažete stůl!')) window.location.href ='table_delete.php?id=$id';\">Vymazat</button> </td>";
					echo '</tr>';
				}
			}
			#mysqli_close($db);
		?>
	</tbody>
</table>

<table class="table table-bordered table-striped table-hover">
	<caption><font size="6">Přední zahrádka</font></caption>
	<thead>
		<tr>
			<th>Číslo stolu</th>
			<th>Kapacita</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php
			#$db = connect_to_serv();
			$query = "SELECT * FROM stul WHERE mistnost='predni zahradka' AND hide_stul = '0'";
			if ($result = mysqli_query($db, $query)) {
				while ($row = mysqli_fetch_assoc($result)){
					echo '<tr>';
					echo "<td>".$row['cislo']."</td>";
					$id = $row['id_stul'];
					echo '<td><form role="form" class="form-inline" method="post" action="table_mod_script.php?id=' . $id . '"'. '><input id="kapacita" name="kapacita" type="number" method="post" value="'. $row['kapacita']. '"' . ' class="form-control input-md" required="true"  min="1" max="99999999999" oninvalid="this.setCustomValidity("Vyplňte kapacitu s max. délkou 11 znaků")" oninput="setCustomValidity("")"><button style="margin: 0px 0px 0px 5px;" type="submit" class="btn btn-success">Změnit</button></form></td>';
					#echo "<td> <button type='button' class='btn btn-success' onclick='window.location.href = `table_mod_script.php?id=$id`'>Změnit</button> </td>";
					echo "<td> <button type='button' class='btn btn-danger' onclick=\" if (confirm('Stlačením OK vymažete stůl!')) window.location.href ='table_delete.php?id=$id';\">Vymazat</button> </td>";
					echo '</tr>';
				}
			}
			#mysqli_close($db);
		?>
	</tbody>
</table>

<table class="table table-bordered table-striped table-hover">
	<caption><font size="6">Zadní zahrádka</font></caption>
	<thead>
		<tr>
			<th>Číslo stolu</th>
			<th>Kapacita</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php
			#$db = connect_to_serv();
			$query = "SELECT * FROM stul WHERE mistnost='zadni zahradka' AND hide_stul = '0'";
			if ($result = mysqli_query($db, $query)) {
				while ($row = mysqli_fetch_assoc($result)){
					echo '<tr>';
					echo "<td>".$row['cislo']."</td>";
					$id = $row['id_stul'];
					echo '<td><form role="form" class="form-inline" method="post" action="table_mod_script.php?id=' . $id . '"'. '><input id="kapacita" name="kapacita" type="number" method="post" value="'. $row['kapacita']. '"' . ' class="form-control input-md" required="true"  min="1" max="99999999999" oninvalid="this.setCustomValidity("Vyplňte kapacitu s max. délkou 11 znaků")" oninput="setCustomValidity("")"><button style="margin: 0px 0px 0px 5px;" type="submit" class="btn btn-success">Změnit</button></form></td>';
					#echo "<td> <button type='button' class='btn btn-success' onclick='window.location.href = `table_mod_script.php?id=$id`'>Změnit</button> </td>";
					echo "<td> <button type='button' class='btn btn-danger' onclick=\" if (confirm('Stlačením OK vymažete stůl!')) window.location.href ='table_delete.php?id=$id';\">Vymazat</button> </td>";
					echo '</tr>';
				}
			}
			#mysqli_close($db);
		?>
	</tbody>
</table>

<table class="table table-bordered table-striped table-hover">
	<caption><font size="6">Salónek</font></caption>
	<thead>
		<tr>
			<th>Číslo stolu</th>
			<th>Kapacita</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php
			#$db = connect_to_serv();
			$query = "SELECT * FROM stul WHERE mistnost='salonek' AND hide_stul = '0'";
			if ($result = mysqli_query($db, $query)) {
				while ($row = mysqli_fetch_assoc($result)){
					echo '<tr>';
					echo "<td>".$row['cislo']."</td>";
					$id = $row['id_stul'];
					echo '<td><form role="form" class="form-inline" method="post" action="table_mod_script.php?id=' . $id . '"'. '><input id="kapacita" name="kapacita" type="number" method="post" value="'. $row['kapacita']. '"' . ' class="form-control input-md" required="true"  min="1" max="99999999999" oninvalid="this.setCustomValidity("Vyplňte kapacitu s max. délkou 11 znaků")" oninput="setCustomValidity("")"><button style="margin: 0px 0px 0px 5px;" type="submit" class="btn btn-success">Změnit</button></form></td>';
					#echo "<td> <button type='button' class='btn btn-success' onclick='window.location.href = `table_mod_script.php?id=$id`'>Změnit</button> </td>";
					echo "<td> <button type='button' class='btn btn-danger' onclick=\" if (confirm('Stlačením OK vymažete stůl!')) window.location.href ='table_delete.php?id=$id';\">Vymazat</button> </td>";
					echo '</tr>';
				}
			}
			mysqli_close($db);
		?>
	</tbody>
</table>
</div>
<?php include_once "footer.php";?>