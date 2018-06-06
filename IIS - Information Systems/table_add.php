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
	$mistnost=$cislo=$kapacita="";
	if(isset($_SESSION['form_mi'])) $mistnost = $_SESSION['form_mi'];
    if(isset($_SESSION ['form_ci'])) $cislo = $_SESSION['form_ci'];
    if(isset($_SESSION['form_ka'])) $kapacita= $_SESSION['form_ka'];


    unset($_SESSION['form_mi']);
    unset($_SESSION ['form_ci']);
    unset($_SESSION['form_ka']);
?>
<div class="col-md-8 center-block">
	<form class="form-horizontal" role="form" method="post" action="table_add_script.php">
	<fieldset>
		
	<legend>Přidání stolu</legend>
	<span class="req_dot">* Povinné</span>

	<div class="form-group">
	  <label class="col-md-4 control-label" for="mistnost"><span class="req_dot">*</span> Místnost</label>
	  <div class="col-md-4">
	    <select id="mistnost" name="mistnost" class="form-control">
	      <option value="bar" <?php echo (isset($mistnost) && $mistnost == 'bar') ? 'selected="selected"' : ''; ?> >Bar</option>
	      <option value="sala" <?php echo (isset($mistnost) && $mistnost == 'sala') ? 'selected="selected"' : ''; ?> >Sála</option>
	      <option value="predni zahradka" <?php echo (isset($mistnost) && $mistnost == 'predni zahradka') ? 'selected="selected"' : ''; ?>>Přední zahrádka</option>
	      <option value="zadni zahradka" <?php echo (isset($mistnost) && $mistnost == 'zadni zahradka') ? 'selected="selected"' : ''; ?>>Zadní zahrádka</option>
	      <option value="salonek" <?php echo (isset($mistnost) && $mistnost == 'salonek') ? 'selected="selected"' : ''; ?>>Salónek</option>
	    </select>
	  </div>
	</div>
	<div class="form-group">

	<div class="form-group">
	  <label class="col-md-4 control-label" for="cislo"><span class="req_dot">*</span>  Číslo stolu v místnosti</label>  
	  <div class="col-md-4">
	  <input id="cislo" name="cislo" type="number" value="<?php if (isset($cislo)){ echo htmlspecialchars($cislo);} ?>" class="form-control input-md" required="true"  min="1" max="999" oninvalid="this.setCustomValidity('Vyplňte číslo s max. délkou 3 znaky')" oninput="setCustomValidity('')">
	    
	  </div>
	</div>

	<div class="form-group">
	  <label class="col-md-4 control-label" for="kapacita"><span class="req_dot">*</span>  Kapacita stolu</label>  
	  <div class="col-md-4">
	  <input id="kapacita" name="kapacita" type="number" value="<?php if (isset($kapacita)){ echo htmlspecialchars($kapacita);} ?>" class="form-control input-md" required="true"  min="1" max="99999999999" oninvalid="this.setCustomValidity('Vyplňte kapacitu s max. délkou 11 znaků')" oninput="setCustomValidity('')">
	    
	  </div>
	</div>
<div class="col-sm-offset-3 col-sm-3 col-sm-offset-3 center-block btn-toolbar">
<button type="submit" id="submit" name="submit" class="btn-lg btn-success">Uložit</button>
<button type='button' class='btn-lg btn-primary' onclick="window.location.href = '/table_list.php'">Zpět</button>
</div>

	</fieldset>
	</form>
</div>
<?php include_once "footer.php";?>