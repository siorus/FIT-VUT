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
?>
<div class="col-md-8 center-block">
    <form class="form-horizontal" role="form" method="post" action="menu_polozka_modif_ingred_add_script.php?id=<?php echo $id; ?>&nazev=<?php echo htmlspecialchars($nazev_pol); ?>">
    <fieldset>
        <?php
        echo "<legend>Přidání ingrediencí k položke ";
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
                    $db = connect_to_serv();
                    $query = "SELECT * FROM ingredience WHERE hide_ingred = '0'";
                    if ($result = mysqli_query($db, $query)) {
                        while ($row_a = mysqli_fetch_assoc($result)){
                            echo "<option value=";
                            echo $row_a['id_ingredience'];
                            echo ">";
                            echo htmlspecialchars($row_a['nazev_ingredience']);
                            echo "</option>";
                        }
                    }
                    mysqli_close($db);

                ?>
            </select>
          </div>
        </div>
        <div class="form-group">

        <div class="form-group">
          <label class="col-md-4 control-label" for="hmotnost"><span class="req_dot">*</span> Hmotnost/Objem</label>  
          <div class="col-md-4">
          <input id="hmotnost" name="hmotnost" type="number" class="form-control input-md" required="true" min="1" oninvalid="this.setCustomValidity('Vyplňte hmotnost/objem')" oninput="setCustomValidity('')">
            
          </div>
        </div>

        <div class="form-group">
          <label class="col-md-4 control-label" for="jednotka"><span class="req_dot">*</span> Jednotka</label>
          <div class="col-md-4">
            <select id="jednotka" name="jednotka" class="form-control">
              <option value="ml">ml</option>
              <option value="g">g</option>
            </select>
          </div>
        </div>
        <div class="col-sm-offset-3 col-sm-3 col-sm-offset-3 center-block btn-toolbar">
        <button type="submit" id="submit" name="submit" class="btn-lg btn-success">Uložit</button>
        <button type='button' class='btn-lg btn-primary' onclick="window.location.href = '/menu_polozka_modify_ingred.php?id=<?php echo $id; ?>&nazev=<?php echo htmlspecialchars($nazev_pol); ?>'">Zpět</button>
      </div>

    </fieldset>
    </form>

<!--<button type='button' class='btn btn-primary' onclick='window.location.href = `menu_polozka.php`'>Uložit</button>-->



</div>
<?php include_once "footer.php"; ?>