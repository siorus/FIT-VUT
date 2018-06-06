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
$db = connect_to_serv();
$query = "SELECT * FROM polozka_menu WHERE id_menu='$id'";
if ($result = mysqli_query($db, $query)) {
	while ($row_a = mysqli_fetch_assoc($result)){
		$row=$row_a;
	}
}
mysqli_close($db);
?>

<div class="col-md-8 center-block">
	<form class="form-horizontal" role="form" method="post" action="menu_polozka_modify_script.php?id=<?php echo $id; ?>">
	<fieldset>
		
	<!-- Form Name -->
	<legend>Modifikování položky menu</legend>
	<span class="req_dot">* Povinné</span>
	<!-- Text input-->
	<div class="form-group">
	  <label class="col-md-4 control-label" for="nazev"><span class="req_dot">*</span>  Název</label>  
	  <div class="col-md-4">
	  <input id="nazev" name="nazev" type="text" value="<?php if (isset($row['nazev'])){ echo htmlspecialchars($row['nazev']);} ?>" class="form-control input-md" required="true"  maxlength="50" oninvalid="this.setCustomValidity('Vyplňte název s max. délkou 50 znaků')" oninput="setCustomValidity('')">
	    
	  </div>
	</div>

	<!-- Text input-->
	<div class="form-group">
	  <label class="col-md-4 control-label" for="popis"> Popis</label>  
	  <div class="col-md-4">
	  <input id="popis" name="popis" type="text" value="<?php if (isset($row['popis'])){ echo htmlspecialchars($row['popis']);} ?>" class="form-control input-md" maxlength="100" oninvalid="this.setCustomValidity('Vyplňte popis s max. délkou 100 znaků')" oninput="setCustomValidity('')">
	    
	  </div>
	</div>

	<div class="form-group">
	  <label class="col-md-4 control-label" for="typ"><span class="req_dot">*</span> Typ</label>
	  <div class="col-md-4">
	    <select id="typ" name="typ" class="form-control">
	      <option value="J" <?php echo (isset($row['typ']) && $row['typ'] == 'J') ? 'selected="selected"' : ''; ?> >Jídlo</option>
	      <option value="P" <?php echo (isset($row['typ']) && $row['typ'] == 'P') ? 'selected="selected"' : ''; ?> >Nápoj</option>
	    </select>
	  </div>
	</div>
	<div class="form-group">

			<!-- Text input-->
	<div class="form-group">
	  <label class="col-md-4 control-label" for="cena"><span class="req_dot">*</span> Cena</label>  
	  <div class="col-md-4">
	  <input id="cena" name="cena" type="number" value="<?php if (isset($row['cena'])){ echo htmlspecialchars($row['cena']);} ?>" class="form-control input-md" required="true" min="1" oninvalid="this.setCustomValidity('Vyplňte cenu')" oninput="setCustomValidity('')">
	    
	  </div>
	</div>

<div class="col-sm-offset-3 col-sm-3 col-sm-offset-3 center-block btn-toolbar">
<button type="submit" id="submit" name="submit" class="btn-lg btn-success">Uložit</button>
<button type='button' class='btn-lg btn-primary' onclick="window.location.href = '/menu_polozka.php'">Zpět</button>
</div>

	</fieldset>
	</form>
</div>

<?php
include_once "footer.php";
?>