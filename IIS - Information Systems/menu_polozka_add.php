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
$nazev=$popis=$typ=$cena="";
	if(isset($_SESSION['form_na'])) $nazev = $_SESSION['form_na'];
    if(isset($_SESSION ['form_po'])) $popis = $_SESSION['form_po'];
    if(isset($_SESSION['form_ty'])) $typ= $_SESSION['form_ty'];
    if(isset($_SESSION['form_ce'])) $cena = $_SESSION['form_ce'];


    unset($_SESSION['form_na']);
    unset($_SESSION ['form_po']);
    unset($_SESSION['form_ty']);
    unset($_SESSION['form_ce']);
?>

<div class="col-md-8 center-block">
	<form class="form-horizontal" role="form" method="post" action="menu_polozka_add_script.php">
	<fieldset>
		
	<!-- Form Name -->
	<legend>Přidání položky menu</legend>
	<span class="req_dot">* Povinné</span>
	<!-- Text input-->
	<div class="form-group">
	  <label class="col-md-4 control-label" for="nazev"><span class="req_dot">*</span>  Název</label>  
	  <div class="col-md-4">
	  <input id="nazev" name="nazev" type="text" value="<?php if (isset($nazev)){ echo htmlspecialchars($nazev);} ?>" class="form-control input-md" required="true"  maxlength="50" oninvalid="this.setCustomValidity('Vyplňte název s max. délkou 50 znaků')" oninput="setCustomValidity('')">
	    
	  </div>
	</div>

	<!-- Text input-->
	<div class="form-group">
	  <label class="col-md-4 control-label" for="popis"> Popis</label>  
	  <div class="col-md-4">
	  <input id="popis" name="popis" type="text" value="<?php if (isset($popis)){ echo htmlspecialchars($popis);} ?>" class="form-control input-md" maxlength="100" oninvalid="this.setCustomValidity('Vyplňte popis s max. délkou 100 znaků')" oninput="setCustomValidity('')">
	    
	  </div>
	</div>

	<div class="form-group">
	  <label class="col-md-4 control-label" for="typ"><span class="req_dot">*</span> Typ</label>
	  <div class="col-md-4">
	    <select id="typ" name="typ" class="form-control">
	      <option value="J" <?php echo (isset($typ) && $typ == 'J') ? 'selected="selected"' : ''; ?> >Jídlo</option>
	      <option value="P" <?php echo (isset($typ) && $typ == 'P') ? 'selected="selected"' : ''; ?> >Nápoj</option>
	    </select>
	  </div>
	</div>
	<div class="form-group">

			<!-- Text input-->
	<div class="form-group">
	  <label class="col-md-4 control-label" for="cena"><span class="req_dot">*</span> Cena</label>  
	  <div class="col-md-4">
	  <input id="cena" name="cena" type="number" value="<?php if (isset($cena)){ echo htmlspecialchars($cena);} ?>" class="form-control input-md" required="true" min="1" oninvalid="this.setCustomValidity('Vyplňte cenu')" oninput="setCustomValidity('')">
	    
	  </div>
	</div>

<div class="col-sm-offset-3 col-sm-3 col-sm-offset-3 center-block btn-toolbar">
<button type="submit" id="submit" name="submit" class="btn-lg btn-success">Uložit</button>
<button type='button' class='btn-lg btn-primary' onclick=" window.location.href = '/menu_polozka.php'">Zpět</button>
</div>


	</fieldset>
	</form>
</div>


<?php
include_once "footer.php";
?>