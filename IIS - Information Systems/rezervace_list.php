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
<div class="col-md-7 center-block">
<button type="button" class="btn btn-primary" onclick="location.href = 'rezervace_add.php'">Přidat</button>
<br/>
<br/>
<form class="form-inline" role="form" method="post" action="<?php $_SERVER['PHP_SELF'];?>">
	
<legend>Zobrazení rezervací</legend>
<div class="form-group">
  <label for="dat_od">Od</label>  
  <input id="dat_od" name="dat_od" type="text" placeholder="yyyy-mm-dd hh:mm" class="form-control" oninvalid="this.setCustomValidity('Vyplňte datum od')" oninput="setCustomValidity('')">
</div>
<div class="form-group">
  <label for="dat_do">Do</label>  
  <input id="dat_do" name="dat_do" type="text" placeholder="yyyy-mm-dd hh:mm" class="form-control" oninvalid="this.setCustomValidity('Vyplňte datum do')" oninput="setCustomValidity('')">
</div>
<button type="submit" id="submit" name="submit" class="btn btn-primary">Hledat</button>
</form>
<br/>
<?php
	if(isset($_POST['submit'])) {
		unset($_SESSION['query']);
	}
	$od = NULL;
	$do = NULL;
	if (isset($_POST['dat_od'])) {
		if ($_POST['dat_od'] == ''){
			$crttime = time();
			$od = date('Y-m-d', $crttime) . " " ."00:00:00";
		} else $od = $_POST['dat_od']. ":00";
	}
	if (isset($_POST['dat_do'])) {
		if ($_POST['dat_do'] == ''){
			$crttime = time();
			$do = date('Y-m-d', $crttime) . " "."23:59:59";
		} else $do = $_POST['dat_do']. ":00";
	}
	#$do = date_format(date_create_from_format("m/d/Y", $_POST['dat_do']), 'Y-m-d') . " 23:59:59";
	if (isset($od)<=isset($do)){
    $db = connect_to_serv();
    if (!isset($_SESSION['query'])){
	$query = "SELECT id_rezervace,datum_rezervace,jmeno,datum_do FROM rezervace NATURAL JOIN rezervace_stul NATURAL JOIN stul WHERE (datum_rezervace >= '$od' AND datum_do <= '$do' AND hide_rez = '0' AND hide_rezstul = '0') GROUP BY id_rezervace,datum_rezervace,datum_do ORDER BY datum_rezervace";
	$_SESSION['query'] = $query;
	} else $query = $_SESSION['query'];
	
		if ($result = mysqli_query($db, $query)) {
			echo '
			<table class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th>Datum a čas</th>
					<th>Jméno</th>
					<th>Do</th>
					<th></th>
					<th></th>
				</tr>
			</thead>
			<tbody>';
			while ($row = mysqli_fetch_assoc($result)){

				echo '<tr>';
				echo "<td>".$row['datum_rezervace']."</td>";
				echo "<td>".htmlspecialchars($row['jmeno'])."</td>";
				echo "<td>".$row['datum_do']."</td>";
				$id_rez = $row['id_rezervace'];
				echo "<td> <button type='button' class='btn btn-primary center-block' onclick=\" window.location.href = 'rezervace_show.php?id_rez=$id_rez';\">Detaily</button> </td>";
				#echo "<td> <button type='button' class='btn btn-success' onclick='window.location.href = `rezervace_modify.php?id_rez=$id_rez&id_stu=$id_stu`'>Změnit</button> </td>";
				echo "<td> <button type='button' class='btn btn-danger center-block' onclick=\" if (confirm('Stlačením OK vymažete rezervaci!')) window.location.href ='rezervace_delete.php?id_rez=$id_rez';\">Vymazat</button> </td>";
				echo '</tr>';
			}
		echo '</tbody>
		</table>';
		}

	mysqli_close($db);
	} else {
		echo "<br/>";
		echo '<div class="flash_msg_fail">';
		echo "<p>Datumy jsou zadané v opačném pořadí!!</p>";
		echo '</div>';
	}
	echo "</div>";
?>
<script>
			//var isIE = /*@cc_on!@*/false || !!document.documentMode;
			/*var isFirefox = typeof InstallTrigger !== 'undefined';
			if((isFirefox) || (isIE))
			{*/
			    $(function(){
			         $("#dat_od").datetimepicker({format: 'yyyy-mm-dd hh:ii',autoclose: "true"});
			         $("#dat_do").datetimepicker({format: 'yyyy-mm-dd hh:ii',autoclose: "true"});
			  });

			//}
</script>
<?php
include_once "footer.php";
?>