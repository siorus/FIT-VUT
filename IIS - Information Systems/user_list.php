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
<div class="col-md-8 center-block">
<button type="button" class="btn btn-primary" onclick="location.href = 'user_add.php'">Přidat</button>
<br/>
<br/>
<table class="table table-bordered table-striped table-hover">
	<thead>
		<tr>
			<th>Login</th>
			<th>Jméno</th>
			<th>Příjmení</th>
			<th>Funkce</th>
			<th></th>
			<th></th>
			<th></th>
<!--			<th>Ulice</th>
			<th>Číslo</th>
			<th>Město</th>
			<th>PSČ</th>
			<th>Telefon</th>
			<th>Email</th>
			<th>Číslo OP</th>
-->
		</tr>
	</thead>
	<tbody>
		<?php
			$db = connect_to_serv();
			$query = "SELECT id_zamestnanec,jmeno,prijmeni,login,rola FROM zamestnanec WHERE hide_zam = '0'";
			if ($result = mysqli_query($db, $query)) {
				while ($row = mysqli_fetch_assoc($result)){
					echo '<tr>';
					echo "<td>".htmlspecialchars($row['login'])."</td>";
					echo "<td>".htmlspecialchars($row['jmeno'])."</td>";
					echo "<td>".htmlspecialchars($row['prijmeni'])."</td>";
					echo "<td>".htmlspecialchars($row['rola'])."</td>";
					$id = $row['id_zamestnanec'];
					echo "<td> <button type='button' class='btn btn-primary center-block' onclick=\"window.location.href = 'user_show.php?id=$id';\">Detaily</button> </td>";
					echo "<td> <button type='button' class='btn btn-success center-block' onclick=\"window.location.href = 'user_modify.php?id=$id'\">Změnit</button> </td>";
					if ($row['rola'] == 'majitel') {
						echo "<td> <button type='button' class='btn btn-default center-block'>Vymazat</button> </td>";
					} else echo "<td> <button type='button' class='btn btn-danger center-block' onclick=\" if (confirm('Stlačením OK vymažete uživatele!')) window.location.href ='user_delete.php?id=$id';\">Vymazat</button> </td>";
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