<?php include_once "header.php";
include_once "functions.php";
session_start();
if (isset($_SESSION['message'])){
	flash_message($_SESSION['message']);
	unset($_SESSION['message']);
	unset($_SESSION['msg_flag']);
}
session_destroy();
session_unset();
?>
<div class="col-md-7 center-block vertical-center" style="vertical-align: middle; text-align: center; margin: auto; ">
	<form class="form-horizontal" role="login" method="post" action="login_script.php">
	<fieldset>
	<!--<div class="error-message"><?php if(isset($message)) { echo $message; } ?></div>	
	 Form Name -->
	<!-- Text input-->
	<div class="form-group">
	  <label class="col-md-4 control-label" for="login"></label>  
	  <div class="col-md-4">
	  <input id="login" name="login" autofocus="autofocus" type="text" placeholder="Login" class="form-control input-md" required="true"  oninvalid="this.setCustomValidity('Vyplňte login')" oninput="setCustomValidity('')">
	  </div>
	</div>

	<div class="form-group">
	  <label class="col-md-4 control-label" for="heslo"></label>  
	  <div class="col-md-4">
	  <input id="heslo" name="heslo" type="password" placeholder="Heslo" class="form-control input-md" required="true"  oninvalid="this.setCustomValidity('Vyplňte heslo')" oninput="setCustomValidity('')">
	  </div>
	</div>


<div class="col-sm-offset-3 col-sm-3 col-sm-offset-3 center-block btn-toolbar">
<button type="submit" id="submit" name="submit" class="btn-lg btn-primary">Přihlásit</button>
</div>

	</fieldset>
	</form>
</div>
<?php include_once "footer.php" ?>