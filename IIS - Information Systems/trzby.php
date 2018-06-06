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
if (!(($_SESSION["rola"] == "majitel"))) {
	header("location: permissions.php");
	die();
}
if (isset($_SESSION['message'])){
	flash_message($_SESSION['message']);
	unset($_SESSION['message']);
	unset($_SESSION['msg_flag']);
}
?>
<div class="col-md-7 center-block">
<form class="form-inline" role="form" method="post" action="<?php $_SERVER['PHP_SELF'];?>">
<legend>Zobrazení tržeb</legend>
<br/>
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
	if (isset($_POST['dat_od'])) {
		if ($_POST['dat_od'] == ''){
			$crttime = time();
			$od = date('Y-m-d', $crttime) . " " ."00:00:00";
		} else $od = $_POST['dat_od']. ":00";
	}
	if (isset($_POST['dat_do'])) {
		if ($_POST['dat_do'] == ''){
			$crttime = time();
			$do = date('Y-m-d', $crttime) . " " ."23:59:59";
		} else $do = $_POST['dat_do']. ":00";
	}
	if (isset($_POST['dat_do']) && isset($_POST['dat_od']) ) {
		if ($od<=$do){
	    $db = connect_to_serv();
	    $query = "SELECT SUM(suma) as suma FROM uctenka WHERE (datum >= '$od' AND datum <= '$do' AND hide_uctenka = '0' AND status='zaplaceno')";
	    if ($result = mysqli_query($db, $query)) {
	    	while ($row = mysqli_fetch_assoc($result)){
			echo '
			<table class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th>Období od</th>
					<th>Období do</th>
					<th>Suma</th>
				</tr>
			</thead>
			<tbody>';
				echo '<tr>';
				echo "<td>".$od."</td>";
				echo "<td>".$do."</td>";
				echo "<td>".$row['suma']."</td>";
				echo '</tr>';

			echo '</tbody>
			</table>';				    		
	    	}
	    }

	    $query = "";
		$query = "SELECT id_uctenka,datum,suma,jmeno,prijmeni FROM uctenka NATURAL JOIN zamestnanec WHERE (datum >= '$od' AND datum <= '$do' AND hide_uctenka = '0' AND status='zaplaceno') ORDER BY datum";
		if ($result = mysqli_query($db, $query)) {
			echo '
			<table class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th>Číslo účtenky</th>
					<th>Datum vystavení</th>
					<th>Suma</th>
					<th>Vystavil</th>
				</tr>
			</thead>
			<tbody>';
			while ($row = mysqli_fetch_assoc($result)){
				echo '<tr>';
				echo "<td>".$row['id_uctenka']."</td>";
				echo "<td>".$row['datum']."</td>";
				echo "<td>".$row['suma']."</td>";
				echo "<td>".htmlspecialchars($row['jmeno'])." ".htmlspecialchars($row['prijmeni'])."</td>";
				echo '</tr>';
			}
		}
		echo '</tbody>
		</table>';
		mysqli_close($db);
		} else {
			echo "<br/>";
			echo '<div class="flash_msg_fail">';
			echo "<p>Datumy jsou zadané v opačném pořadí!!</p>";
			echo '</div>';
		}
	}
echo "</div>";
?>



<script>
$(function(){
     $("#dat_od").datetimepicker({format: 'yyyy-mm-dd hh:ii',autoclose: "true"});
     $("#dat_do").datetimepicker({format: 'yyyy-mm-dd hh:ii',autoclose: "true"});
});
</script>
<?php
include_once "footer.php";
?>