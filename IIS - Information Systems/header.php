<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	<title>Informačný systém reštaurácie</title>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap-theme.css">
	<link rel="stylesheet" type="text/css" href="css/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap-datetimepicker.min.css">
	<link rel="stylesheet" type="text/css" href="css/trojan.css">
	<style>
		.req_dot {color:red; font-weight:bold}
		.center-block {float:none}
		.human {margin: 0 0 0 12px}
	</style>
	<!--<style>
		.objednavky {width: 100px; height: 100px  }
	</style>
	-->
	<script src="js/jquery-3.2.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery-ui.js"></script>
	<script src="js/bootstrap-datetimepicker.min.js"></script>
</head>
<body>
<?php
  echo '<nav class="navbar navbar-inverse">'; echo "\n";
  echo '   <div class="container-fluid">'; echo "\n";
  echo '      <div class="navbar-header">'; echo "\n";
  #echo '<img src="images/374042-200.png" width=45px/>';
  echo '         <a class="navbar-brand" href="#"><p style="color:white; margin-top: -12px;"><img src="images/374042-200.png" width=45px"/> Trojan Horse Pub</p></a>'; echo "\n";
  #echo '    <a class="navbar-brand" href="#">Trojan Horse</a>';
  echo '      </div>'; echo "\n";
  if (isset($_SESSION["login"])){
	  echo '      <ul class="nav navbar-nav">'; echo "\n";
	  echo       "         <li"; if(substr($_SERVER["REQUEST_URI"],1,10) == "objednavky") echo " class='active'"; echo '><a href="objednavky.php">Objednávky</a></li>'; echo "\n";
	  echo       "         <li"; if(substr($_SERVER["REQUEST_URI"],1,9) == "rezervace") echo " class='active'"; echo '><a href="rezervace_list.php">Rezervace</a></li>'; echo "\n";
	  if (($_SESSION['rola'] == "majitel") || ($_SESSION['rola'] == "provozni")){
	    echo '         <li class="dropdown ';
	    if(substr($_SERVER["REQUEST_URI"],1,4) == "menu") echo "active";
	    echo '">'; echo "\n";
	    echo '            <a class="dropdown-toggle" data-toggle="dropdown" href="#">Položka menu';
	    echo '<span class="caret"></span></a>'; echo "\n";
	    echo '            <ul class="dropdown-menu">'; echo "\n";
	    echo 			"               <li"; if(substr($_SERVER["REQUEST_URI"],1,12) == "menu_polozka") echo " class='active'"; echo '><a href="menu_polozka.php">Jídlo/Piti</a></li>'; echo "\n";
	    echo       		"               <li"; if(substr($_SERVER["REQUEST_URI"],1,11) == "menu_ingred") echo " class='active'"; echo '><a href="menu_ingred.php">Ingredience</a></li>'; echo "\n";
	    echo '            </ul>'; echo "\n";
	    echo '         </li>'; echo "\n";
	    echo       "         <li"; if(substr($_SERVER["REQUEST_URI"],1,4) == "user") echo " class='active'"; echo '><a href="user_list.php">Zaměstnanec</a></li>'; echo "\n";
	    echo       "         <li"; if(substr($_SERVER["REQUEST_URI"],1,5) == "table") echo " class='active'"; echo '><a href="table_list.php">Stoly</a></li>'; echo "\n";
	  }

	  if ($_SESSION['rola'] == 'majitel') {
	  	echo       "         <li"; if(substr($_SERVER["REQUEST_URI"],1,5) == "trzby") echo " class='active'"; echo '><a href="trzby.php">Tržby</a></li>'; echo "\n";
	  }

	  echo '      </ul>'; echo "\n";
	  echo '      <ul class="nav navbar-nav navbar-right">'; echo "\n"; 
	  echo '         <li><a href="index.php"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>'; echo "\n";
	  echo '      </ul>'; echo "\n";
	}
  echo '   </div>'; echo "\n";  
  echo '</nav>'; echo "\n"; echo "\n";
?>