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
	$jmeno=$prijmeni=$op=$telefon=$email=$ulice=$cislo=$mesto=$psc=$funkce=$heslo=$login="";
	if(isset($_SESSION['form_jm'])) $jmeno = $_SESSION['form_jm'];
    if(isset($_SESSION ['form_pr'])) $prijmeni = $_SESSION['form_pr'];
    if(isset($_SESSION['form_op'])) $op= $_SESSION['form_op'];
    if(isset($_SESSION['form_te'])) $telefon = $_SESSION['form_te'];
    if(isset($_SESSION['form_em'])) $email = $_SESSION['form_em'];
    if(isset($_SESSION['form_ul'])) $ulice = $_SESSION['form_ul'];
    if(isset($_SESSION['form_ci'])) $cislo = $_SESSION['form_ci'];
    if(isset($_SESSION['form_me'])) $mesto = $_SESSION['form_me'];
    if(isset($_SESSION['form_ps'])) $psc = $_SESSION['form_ps'];
    if(isset($_SESSION['form_lo'])) $login = $_SESSION['form_lo'];
    if(isset($_SESSION['form_fu'])) $funkce = $_SESSION['form_fu'];

    unset($_SESSION['form_jm']);
    unset($_SESSION ['form_pr']);
    unset($_SESSION['form_op']);
    unset($_SESSION['form_te']);
    unset($_SESSION['form_em']);
    unset($_SESSION['form_ul']);
    unset($_SESSION['form_ci']);
    unset($_SESSION['form_me']);
    unset($_SESSION['form_ps']);
    unset($_SESSION['form_lo']);
    unset($_SESSION['form_fu']);

?>
<div class="col-md-8 center-block">
	<form class="form-horizontal" role="form" method="post" action="user_add_script.php">
	<fieldset>
		
	<!-- Form Name -->
	<legend>Přidání zaměstnance</legend>
	<span class="req_dot">* Povinné</span>
	<!-- Text input-->
	<div class="form-group">
	  <label class="col-md-4 control-label" for="jmeno"><span class="req_dot">*</span>  Jméno</label>  
	  <div class="col-md-4">
	  <input id="jmeno" name="jmeno" type="text" value="<?php if (isset($jmeno)){ echo htmlspecialchars($jmeno);} ?>" class="form-control input-md" required="true"  maxlength="20" oninvalid="this.setCustomValidity('Vyplňte jméno s max. délkou 20 znaků')" oninput="setCustomValidity('')">
	    
	  </div>
	</div>

	<!-- Text input-->
	<div class="form-group">
	  <label class="col-md-4 control-label" for="prijmeni"><span class="req_dot">*</span> Příjmení</label>  
	  <div class="col-md-4">
	  <input id="prijmeni" name="prijmeni" type="text" value="<?php if (isset($prijmeni)){ echo htmlspecialchars($prijmeni);} ?>" class="form-control input-md" required="true" maxlength="30" oninvalid="this.setCustomValidity('Vyplňte příjmení s max. délkou 20 znaků')" oninput="setCustomValidity('')">
	    
	  </div>
	</div>

			<!-- Text input-->
	<div class="form-group">
	  <label class="col-md-4 control-label" for="mesto"><span class="req_dot">*</span> Město</label>  
	  <div class="col-md-4">
	  <input id="mesto" name="mesto" type="text" value="<?php if (isset($mesto)){ echo htmlspecialchars($mesto);} ?>" class="form-control input-md" required="true" maxlength="25" oninvalid="this.setCustomValidity('Vyplňte město s max. délkou 25 znaků')" oninput="setCustomValidity('')">
	    
	  </div>
	</div>

	<!-- Text input-->
	<div class="form-group">
	  <label class="col-md-4 control-label" for="psc"><span class="req_dot">*</span> PSČ</label>  
	  <div class="col-md-4">
	  <input id="psc" name="psc" type="text" value="<?php if (isset($psc)){ echo htmlspecialchars($psc);} ?>" class="form-control input-md" required="true" maxlength="11" oninvalid="this.setCustomValidity('Zadejte PSČ, max. délka 11 znaků')" oninput="setCustomValidity('')">
	    
	  </div>
	</div>

	<!-- Text input-->
	<div class="form-group">
	  <label class="col-md-4 control-label" for="ulice"><span class="req_dot">*</span> Ulice</label>  
	  <div class="col-md-4">
	  <input id="ulice" name="ulice" type="text" value="<?php if (isset($ulice)){ echo htmlspecialchars($ulice);} ?>" class="form-control input-md" required="true" oninvalid="this.setCustomValidity('Vyplňte ulici, max. délka 30 znaků')" oninput="setCustomValidity('')">
	    
	  </div>
	</div>

	<!-- Text input-->
	<div class="form-group">
	  <label class="col-md-4 control-label" for="cislo"><span class="req_dot">*</span> Číslo popisné</label>  
	  <div class="col-md-4">
	  <input id="cislo" name="cislo" type="number" value="<?php if (isset($cislo)){ echo htmlspecialchars($cislo);} ?>" class="form-control input-md" required="true" min="1" max="99999999999" oninvalid="this.setCustomValidity('Vyplňte číslo popisné, max. délka 11 znaků')" oninput="setCustomValidity('')">
	    
	  </div>
	</div>

			<!-- Text input-->
	<div class="form-group">
	  <label class="col-md-4 control-label" for="telefon">Telefon</label>  
	  <div class="col-md-4">
	  <input id="telefon" name="telefon" type="number" value="<?php if (isset($telefon)){ echo htmlspecialchars($telefon);} ?>" class="form-control input-md" min="1" max="9999999999" oninvalid="this.setCustomValidity('Vyplňte číslo, max. délka 10 znaků')">
	    
	  </div>
	</div>

			<!-- Text input-->
	<div class="form-group">
	  <label class="col-md-4 control-label" for="email">E-Mail</label>  
	  <div class="col-md-4">
	  <input id="email" name="email" type="email" value="<?php if (isset($email)){ echo htmlspecialchars($email);} ?>" class="form-control input-md" maxlength="30" oninvalid="this.setCustomValidity('Vyplňte email, max. délka 30 znaků')">
	    
	  </div>
	</div>

			<!-- Text input-->
	<div class="form-group">
	  <label class="col-md-4 control-label" for="op"><span class="req_dot">*</span> Číslo OP</label>  
	  <div class="col-md-4">
	  <input id="op" name="op" type="text" value="<?php if (isset($op)){ echo htmlspecialchars($op);} ?>" class="form-control input-md" required="true" maxlength="9" oninvalid="this.setCustomValidity('Vyplňte číslo OP, max. délka 9 znaků')" oninput="setCustomValidity('')">
	    
	  </div>
	</div>

	<div class="form-group">
	  <label class="col-md-4 control-label" for="login"><span class="req_dot">*</span> Login</label>  
	  <div class="col-md-4">
	  <input id="login" name="login" type="text" value="<?php if (isset($login)){ echo htmlspecialchars($login);} ?>" class="form-control input-md" required="true" maxlength="30" oninvalid="this.setCustomValidity('Vyplňte login, max. délka 30 znaků')" oninput="setCustomValidity('')">
	  </div>
	</div>

	<div class="form-group">
	  <label class="col-md-4 control-label" for="heslo"><span class="req_dot">*</span> Heslo</label>  
	  <div class="col-md-4">
	  <input id="heslo" name="heslo" type="password" placeholder="" class="form-control input-md" required="true" maxlength="30" oninvalid="this.setCustomValidity('Vyplňte heslo, max. délka 30 znaků')" oninput="setCustomValidity('')">
	    
	  </div>
	</div>

	<div class="form-group">
	  <label class="col-md-4 control-label" for="heslo_zop"><span class="req_dot">*</span> Zopakování hesla</label>  
	  <div class="col-md-4">
	  <input id="heslo_zop" name="heslo_zop" type="password" placeholder="" class="form-control input-md" required="true" maxlength="30" oninvalid="this.setCustomValidity('Vyplňte heslo, max. délka 30 znaků')" oninput="setCustomValidity('')">
	    
	  </div>
	</div>

		<!-- Select Basic -->
	<div class="form-group">
	  <label class="col-md-4 control-label" for="funkce"><span class="req_dot">*</span> Funkce</label>
	  <div class="col-md-4">
	    <select id="funkce" name="funkce" class="form-control">
	      <option value="kuchar" <?php echo (isset($funkce) && $funkce == 'kuchar') ? 'selected="selected"' : ''; ?> >kuchař</option>
	      <option value="cisnik" <?php echo (isset($funkce) && $funkce == 'cisnik') ? 'selected="selected"' : ''; ?> >číšník</option>
	      <option value="provozni" <?php echo (isset($funkce) && $funkce == 'provozni') ? 'selected="selected"' : ''; ?>>provozní</option>
	      <option value="majitel" <?php echo (isset($funkce) && $funkce == 'majitel') ? 'selected="selected"' : ''; ?>>majitel</option>
	    </select>
	  </div>
	</div>
	<div class="form-group">

<div class="col-sm-offset-3 col-sm-3 col-sm-offset-3 center-block btn-toolbar">
<button type="submit" id="submit" name="submit" class="btn-lg btn-success ">Uložit</button>
<button type='button' class='btn-lg btn-primary' onclick="window.location.href = '/user_list.php'">Zpět</button>
</div>

	</fieldset>
	</form>
</div>
<?php include_once "footer.php";?>