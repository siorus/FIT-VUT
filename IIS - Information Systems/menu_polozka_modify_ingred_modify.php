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
$nazev_pol = $_GET['nazev'];
$ingr = $_GET['ingr'];
$db = connect_to_serv();
$query2 = "SELECT * FROM `obsahuje` as o NATURAL JOIN `ingredience` as i  WHERE id_menu='$id' AND id_ingredience='$ingr'";
if ($result2 = mysqli_query($db, $query2)) {
  while ($row2 = mysqli_fetch_assoc($result2)){
    $row = $row2;
  }
}
mysqli_close($db);
?>
<div class="col-md-8 center-block">
    <form class="form-horizontal" role="form" method="post" action="menu_polozka_modify_ingred_modify_script.php?id=<?php echo $id; ?>&nazev=<?php echo htmlspecialchars($nazev_pol); ?>&ingr=<?php echo $ingr; ?>">
    <fieldset>
        <?php
        echo "<legend>Modifikace ingredience k položke ";
        echo '"';
        echo htmlspecialchars($nazev_pol);
        echo '"';
        echo "</legend>";
        ?>
        <span class="req_dot">* Povinné</span>
        <div class="form-group">
          <label class="col-md-4 control-label" for="nazev"><span class="req_dot">*</span> Název</label>
          <div class="col-md-4">
            <select id="nazev" name="nazev" class="form-control">
                <?php 
                            echo "<option value=";
                            echo $row['id_ingredience'];
                            echo ">";
                            echo htmlspecialchars($row['nazev_ingredience']);
                            echo "</option>";
                        
                    
                ?>
            </select>
          </div>
        </div>
        <div class="form-group">

        <div class="form-group">
          <label class="col-md-4 control-label" for="hmotnost"><span class="req_dot">*</span> Hmotnost/Objem</label>  
          <div class="col-md-4">
          <input id="hmotnost" name="hmotnost" type="number" class="form-control input-md" value="<?php if (isset($row['mnozstvi'])){ echo htmlspecialchars($row['mnozstvi']);} ?>" required="true" min="1" oninvalid="this.setCustomValidity('Vyplňte hmotnost/objem')" oninput="setCustomValidity('')">
            
          </div>
        </div>

        <div class="form-group">
          <label class="col-md-4 control-label" for="jednotka"><span class="req_dot">*</span> Jednotka</label>
          <div class="col-md-4">
            <select id="jednotka" name="jednotka" class="form-control">
              <option value="ml" <?php echo (isset($row['jednotka']) && $row['jednotka'] == 'ml') ? 'selected="selected"' : ''; ?> >ml</option>
              <option value="g" <?php echo (isset($row['jednotka']) && $row['jednotka'] == 'g') ? 'selected="selected"' : ''; ?>>g</option>
            </select>
          </div>
        </div>
        <div class="col-sm-offset-3 col-sm-3 col-sm-offset-3 center-block btn-toolbar">
        <button type="submit" id="submit" name="submit" class="btn-lg btn-success">Uložit</button>
        <button type='button' class='btn-lg btn-primary' onclick="window.location.href = '/menu_polozka_modify_ingred.php?id=<?php echo $id; ?>&nazev=<?php echo htmlspecialchars($nazev_pol); ?>'">Zpět</button>
      </div>

    </fieldset>
    </form>
</div>
<?php include_once "footer.php"; ?>