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
$id = $_GET['id'];
$mistnost = $_GET['mistnost'];
$cislo = $_GET['cislo'];


?>
<div class="col-md-7 center-block">
<form class="form-inline" role="form" method="post" action="objednavky_stul_add.php?id=<?php echo $id; ?>&mistnost=<?php echo $mistnost; ?>&cislo=<?php echo $cislo; ?>">

<?php
	if ($mistnost == 'predni zahradka') { echo "<legend>Přední zahrádka č.".$cislo . "</legend>";}
	else if ($mistnost == 'zadni zahradka') { echo "<legend>Zadní zahrádka č.".$cislo . "</legend>";}
	else if ($mistnost == 'salonek') { echo "<legend>Salonek č.".$cislo . "</legend>";}
	else if ($mistnost == 'bar') { echo "<legend>Bar</legend>";}
	else if ($mistnost == 'sala') { echo "<legend>Sála č.".$cislo . "</legend>";}
?>	
<span class="req_dot">* Povinné</span>
<br/>
<div class="form-group">
  <label for="nazev"><span class="req_dot">*</span>  Název</label>  
  <select id="nazev" name="nazev" class="form-control">
                <?php 
                    $db = connect_to_serv();
                    $query = "SELECT id_menu,nazev FROM polozka_menu WHERE hide_polozka = '0'";
                    if ($result = mysqli_query($db, $query)) {
                        while ($row_a = mysqli_fetch_assoc($result)){
                            echo "<option value=";
                            echo $row_a['id_menu'];
                            echo ">";
                            echo htmlspecialchars($row_a['nazev']);
                            echo "</option>";
                        }
                    }

                ?>
            </select>
</div>
<button type="submit" id="submit" name="submit" class="btn btn-success">Přidat</button>
</form>
<br/>
<form class="form-horizontal" role="form" method="post" action="objednavky_stul_uctenka.php?id=<?php echo $id; ?>&mistnost=<?php echo $mistnost; ?>&cislo=<?php echo $cislo; ?>">
<table class="table table-bordered table-striped table-hover">
	<thead>
		<tr>
			<th>Název</th>
			<th>Cena</th>
			<th>Status</th>
			<th>Na účetnku</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php
			$query = "SELECT polozka_menu.nazev, objednana_polozka.cena,status,id_objednavka,id_menu FROM  (stul  NATURAL JOIN objednana_polozka) JOIN polozka_menu USING(id_menu)  WHERE id_stul = '$id' AND status = 'objednano' AND hide_objednana = '0'";
			if ($result = mysqli_query($db, $query)) {
				while ($row = mysqli_fetch_assoc($result)){
					$id_ob = $row['id_objednavka'];
					echo "<tr>";
					echo "<td>" . htmlspecialchars($row['nazev']) ."</td>";
					echo "<td>" . $row['cena'] ."</td>";
					echo "<td>" . $row['status'] ."</td>";
					echo "<td><input type='checkbox' name=" .   $id_ob ." value=" . $id_ob ."></td>";
					echo "<td> <button type='button' class='btn btn-danger center-block' onclick=\" if (confirm('Stlačením OK vymažete položku!')) window.location.href ='objednavky_stul_delete.php?id=$id&mistnost=$mistnost&cislo=$cislo&id_ob=$id_ob';\">Storno</button> </td>";
					echo "</tr>";
				}
			}
			
		?>
	</tbody>
</table>
<button type="submit" id="submit1" name="submit1" class="btn-lg btn-success center-block">Na účtenku</button>
</form>
<br/>
<br/>
<form class="form-horizontal" role="form" method="post" action="objednavky_stul_uctenka_zapl.php?id=<?php echo $id; ?>&mistnost=<?php echo $mistnost; ?>&cislo=<?php echo $cislo; ?>">
 <table class="table table-bordered table-striped table-hover">
 	<thead>
		<tr>
			<th>Číslo účtenky</th>
			<th>Suma</th>
			<th>Status</th>
			<th>Označit zaplacené</th>
		</tr>
	</thead>
	<tbody>
			<?php
			$query = "SELECT id_uctenka,uctenka.datum,suma,uctenka.status FROM uctenka JOIN objednana_polozka USING (id_uctenka) NATURAL JOIN stul WHERE id_stul = '$id' AND uctenka.status = 'vystaveno' GROUP BY id_uctenka";
			if ($result = mysqli_query($db, $query)) {
				while ($row = mysqli_fetch_assoc($result)){
					$id_uctenka = $row['id_uctenka'];
					echo "<tr>";
					echo "<td>" . $row['id_uctenka'] ."</td>";
					echo "<td>" . $row['suma'] ."</td>";
					echo "<td>" . $row['status'] ."</td>";
					echo "<td><input type='checkbox' name=" .   $id_uctenka ." value=" . $id_uctenka ."></td>";
					echo "</tr>";
				}
			}
			mysqli_close($db);
			?>
		
	</tbody>
</table>
<button type="submit" id="submit2" name="submit2" class="btn-lg btn-success center-block">Potvrdit zaplacení</button>
</form>
<br/>
<br/>
<button type='button' class='btn-lg btn-primary center-block' onclick="window.location.href = '/objednavky.php'">Zpět</button>
</div>
<?php
include_once "footer.php";
?>