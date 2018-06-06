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
if(isset($_SESSION['nazev'])) $nazev= $_SESSION['nazev'];
unset($_SESSION['nazev']);
if ((($_SESSION["rola"] == "majitel") || ($_SESSION["rola"] == "provozni")) &&(time () >= (int) $_SESSION ['timeout'])) {
	$_SESSION['message'] = "Byl jste automaticky odhlášen";
	$_SESSION['msg_flag'] = 1;
	header("location: index.php");
	die();
}
?>
<div class="col-md-6 center-block">
<form class="form-inline" role="form" method="post" accept-charset="utf-8" action="menu_ingred_add_script.php">
	
<legend>Přidání ingredience</legend>
<span class="req_dot">* Povinné</span>
<br/>
<div class="form-group">
  <label for="nazev"><span class="req_dot">*</span>  Název</label>  
  <input id="nazev" name="nazev" type="text" value="<?php if (isset($nazev)){ echo htmlspecialchars($nazev);} ?>" class="form-control" required="true" maxlength="30" oninvalid="this.setCustomValidity('Vyplňte název s max. délkou 30 znaků')" oninput="setCustomValidity('')">
</div>
<button type="submit" id="submit" name="submit" class="btn btn-success">Uložit</button>
</form>
<br/>
<table class="table table-bordered table-striped table-hover">
	<thead>
		<tr>
			<th>Číslo ingredience</th>
			<th>Název</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php
			$db = connect_to_serv();
			$query = "SELECT * FROM ingredience WHERE hide_ingred = '0'";
			if ($result = mysqli_query($db, $query)) {
				while ($row = mysqli_fetch_assoc($result)){
					echo '<tr>';
					echo "<td>".$row['id_ingredience']."</td>";
					$id = $row['id_ingredience'];
					echo '<td><form class="form-inline" accept-charset="utf-8" role="form" method="post" action="menu_ingred_mod_script.php?id=' . $id . '"'. '><input id="nazev" name="nazev" type="text" method="post" value="'. htmlspecialchars($row['nazev_ingredience']). '"' . ' class="form-control input-md" required="true" maxlength="30" oninvalid="this.setCustomValidity("Vyplňte název s max. délkou 30 znaků")" oninput="setCustomValidity("")"><button style="margin: 0px 0px 0px 5px;" type="submit" class="btn btn-success">Změnit</button></form></td>';
					#echo "<td> <button type='button' class='btn btn-success' onclick='window.location.href = `table_mod_script.php?id=$id`'>Změnit</button> </td>";
					echo "<td> <button type='button' class='btn btn-danger center-block' onclick=\" if (confirm('Stlačením OK vymažete ingredienci!')) window.location.href ='menu_ingred_delete.php?id=$id';\">Vymazat</button> </td>";
					echo '</tr>';
				}
			}
			mysqli_close($db);
		?>
	</tbody>
</table>
</div>

<?php include_once "footer.php";?>