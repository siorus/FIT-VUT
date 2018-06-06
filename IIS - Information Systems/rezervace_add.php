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

	$poznamka=$telefon=$jmeno=$kapacita="";
	if(isset($_SESSION['form_po'])) $poznamka = $_SESSION['form_po'];
    if(isset($_SESSION ['form_te'])) $telefon = $_SESSION['form_te'];
    if(isset($_SESSION['form_jm'])) $jmeno = $_SESSION['form_jm'];
    if(isset($_SESSION['form_ka'])) $kapacita = $_SESSION['form_ka'];


    unset($_SESSION['form_po']);
    unset($_SESSION ['form_te']);
    unset($_SESSION['form_jm']);
    unset($_SESSION['form_ka']);
?>
<div class="col-md-8 center-block">
	<form class="form-horizontal" role="form" method="post" action="<?php $_SERVER['PHP_SELF'];?>">
	<fieldset>
		
	<legend>1. Vyhledání volného stolu rezervace</legend>
	<span class="req_dot">* Povinné</span>

	<div class="form-group">
	  <label class="col-md-4 control-label" for="dat_od"><span class="req_dot">*</span>  Od</label> 
	  <div class="col-md-4"> 
	  <input id="dat_od" name="dat_od" type="text" placeholder="yyyy-mm-dd hh:mm" class="form-control input-md" oninvalid="this.setCustomValidity('Vyplňte datum od')" oninput="setCustomValidity('')">
	  </div>
	</div>
	<div class="form-group">
	  <label class="col-md-4 control-label" for="dat_do"><span class="req_dot">*</span>  Do</label>  
	  <div class="col-md-4">
	  <input id="dat_do" name="dat_do" type="text" placeholder="yyyy-mm-dd hh:mm" class="form-control input-md"  oninvalid="this.setCustomValidity('Vyplňte datum do')" oninput="setCustomValidity('')">
	  </div>
	</div>
	<div class="col-sm-offset-3 col-sm-3 col-sm-offset-3 center-block btn-toolbar">
	<button type="submit1" id="submit1" name="submit1" class="btn-lg btn-primary">Hledat</button>
	</div>

	</fieldset>
	</form>
</div>

<div class="col-md-8 center-block">
	<form class="form-horizontal" role="form" method="post" action="rezervace_add_script.php">
	<fieldset>
		
	<legend>2 .Přidání rezervace</legend>
	<span class="req_dot">* Povinné</span>


	<div class="form-group">
          <label class="col-md-4 control-label" for="stul"><span class="req_dot">*</span> Výběr stolu</label>
          <div class="col-md-4">
            <select multiple id="stul" required="true" name="stul[]" class="form-control" size="8" oninvalid="this.setCustomValidity('Vyberte stůl')" oninput="setCustomValidity('')">
	<?php
	if(isset($_POST['submit1'])) {
		if ($_POST['dat_od'] == ''){
			$crttime = time();
			$od = date('Y-m-d H:i', $crttime) . ":00";
		} else $od = $_POST['dat_od']. ":00";
		if ($_POST['dat_do'] == ''){
			$crttime = time()+(60*60*2);
			$do = date('Y-m-d H:i', $crttime) . ":00";
		} else $do = $_POST['dat_do']. ":00";
		#$kapacita = $_POST['kapacita'];
		if ($_POST['dat_od']<=$_POST['dat_do']){
		    $db = connect_to_serv();
			$query ="SELECT mistnost,cislo,kapacita,id_stul FROM `stul` WHERE id_stul NOT IN (SELECT id_stul  FROM  `rezervace_stul` NATURAL JOIN `stul`WHERE (mistnost<>'bar' AND hide_rezstul = '0' AND datum_rezervace BETWEEN '$od' AND '$do') OR  (mistnost<>'bar' AND hide_rezstul = '0' AND datum_do BETWEEN '$od' AND '$do')) AND  hide_stul = '0' ORDER BY mistnost, cislo";
			if ($result = mysqli_query($db, $query)) {
				while ($row = mysqli_fetch_assoc($result)){
					echo "<option value=";
	                echo $row['id_stul'];
	                echo ">";
	                echo $row['mistnost']." c.".$row['cislo']." kapacita: ".$row['kapacita'];
	                echo "</option>";
				}
			}
			mysqli_close($db);
		} else $opacne = 1;
	}
	?>
			</select>
			<?php
				if (isset($opacne) && $opacne == 1){
					echo "<p style='color:red;'>Datumy jsou prohozené!!</p>";
				} else  echo "<p>Při použití IE, použijte klávesu CTRL a levé tlačítko myši.</p>";
			?>
		</div>
	</div>
	<div class="form-group">
	  <label class="col-md-4 control-label" for="datum_od1"> Od</label>  
	  <div class="col-md-4">
	  <input id="datum_od1" name="datum_od1" type="text" value="<?php if (isset($od)){ echo htmlspecialchars($od);} ?>" class="form-control input-md" readonly>
	    
	  </div>
	</div>

	<div class="form-group">
	  <label class="col-md-4 control-label" for="datum_do1"> Do</label>  
	  <div class="col-md-4">
	  <input id="datum_do1" name="datum_do1" type="text" value="<?php if (isset($do)){ echo htmlspecialchars($do);} ?>" class="form-control input-md" readonly>
	    
	  </div>
	</div>

	<div class="form-group">
	  <label class="col-md-4 control-label" for="kapacita1"> Počet osob</label>  
	  <div class="col-md-4">
	  <input id="kapacita1" name="kapacita1" type="number" value="<?php if (isset($kapacita)){ echo htmlspecialchars($kapacita);} ?>" class="form-control input-md" min="1" max="9999" oninvalid="this.setCustomValidity('Vyplňte kapacitu')" oninput="setCustomValidity('')">
	    
	  </div>
	</div>

	<div class="form-group">
	  <label class="col-md-4 control-label" for="jmeno"><span class="req_dot">*</span>  Jméno</label>  
	  <div class="col-md-4">
	  <input id="jmeno" name="jmeno" type="text" value="<?php if (isset($jmeno)){ echo htmlspecialchars($jmeno);} ?>" class="form-control input-md" required="true" maxlength="20" oninvalid="this.setCustomValidity('Vyplňte jméno s max. délkou 20 znaku')" oninput="setCustomValidity('')">
	    
	  </div>
	</div>

	<div class="form-group">
	  <label class="col-md-4 control-label" for="telefon"><span class="req_dot">*</span>  Telefon</label>  
	  <div class="col-md-4">
	  <input id="telefon" name="telefon" type="number" value="<?php if (isset($telefon)){ echo htmlspecialchars($telefon);} ?>" class="form-control input-md" required="true"  min="1" max="9999999999" oninvalid="this.setCustomValidity('Vyplňte telefon s max. délkou 10 znaků')" oninput="setCustomValidity('')">
	    
	  </div>
	</div>

	<div class="form-group">
	  <label class="col-md-4 control-label" for="poznamka">  Poznámka</label>  
	  <div class="col-md-4">
	  <input id="poznamka" name="poznamka" type="text" value="<?php if (isset($poznamka)){ echo htmlspecialchars($poznamka);} ?>" class="form-control input-md" maxlength="100" oninvalid="this.setCustomValidity('Vyplňte poznámku s max. délkou 100 znaků')" oninput="setCustomValidity('')">
	    
	  </div>
	</div>
<div class="col-sm-offset-3 col-sm-3 col-sm-offset-3 center-block btn-toolbar">
<button type="submit" id="submit" name="submit" class="btn-lg btn-success">Uložit</button>
<button type='button' class='btn-lg btn-primary' onclick="window.location.href = '/rezervace_list.php'">Zpět</button>
</div>

	</fieldset>
	</form>
</div>

<script>
  $(function(){
         $("#dat_od").datetimepicker({format: 'yyyy-mm-dd hh:ii',autoclose: "true"});
         $("#dat_do").datetimepicker({format: 'yyyy-mm-dd hh:ii',autoclose: "true"});
  });
</script>
<script>
	jQuery('option').mousedown(function(e) {
    e.preventDefault();
    jQuery(this).toggleClass('selected');
  
    jQuery(this).prop('selected', !jQuery(this).prop('selected'));
    return false;
	});
</script>

<?php include_once "footer.php";?>