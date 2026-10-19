<?php include "connections/config.php"; ?>
<!--<!DOCTYPE html>-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>MineData-Labs</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="MineData-Labs">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css" />
  <script type="text/javascript" src="js/jquery.min.js"></script>
  <script type="text/javascript" src="js/bootstrap.min.js"></script>
  <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style type="text/css">
    body {
      padding-bottom: 20px;
    }

    .navbar {
      margin-bottom: 10px;
    }

    .bg-blue {
      background-color: #152c52;
    }

    .navbar-dark .navbar-nav .nav-link {
      color: white;
    }

    .navbar-brand {

      height: 80px;
    }

    img {
      max-width: 80%;
    }

    .barra {
      width: 100%;
      padding: 0px;
      height: 50px;
      background-color: #cecece;
      text-align: left;
      color: blue;
    }

    .barra a {
      color: #152c52;
    }

    .nav-item a {
      font-size: 14px;
      font-weight: lighter;
      font-family: tahoma;
    }
    .form-group input[type="checkbox"] {
    display: none;
}

    .form-group input[type="checkbox"] + .btn-group > label span {
        width: 40px;
    }
    
    .form-group input[type="checkbox"] + .btn-group > label span:first-child {
        display: none;
    }
    .form-group input[type="checkbox"] + .btn-group > label span:last-child {
        display: inline-block;   
    }
    
    .form-group input[type="checkbox"]:checked + .btn-group > label span:first-child {
        display: inline-block;
    }
    .form-group input[type="checkbox"]:checked + .btn-group > label span:last-child {
        display: none;   
}
  </style>

</head>

<body>
  <div class="barra">
    <div class="container" class="col-xl-10 col-sm-7 col-md-5 col-ld-5">
      <ul class="nav nav-pills">

        <li class="nav-item">
          <a class="text-muted" href="#" target="_blank">MinaData Labs</a>
        </li>

        <?php if ($_SESSION['LoggedIn'] == 1) { ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#">Hola <?php echo $_SESSION['nombre']?></a>
            <div class="dropdown-menu">
              <a class="dropdown-item" href="logout.php">Cerra sesión</a>
            </div>
          </li>
          
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#">Cambiar de Mina</a>
            <div class="dropdown-menu">
              <?php
              //0 = Todos
              if ($_SESSION['unidad_acc'] == '0') {
                $datos_at = $mysqli->query("SELECT serie, unidad_id, nombre FROM arg_empr_unidades") or die(mysqli_error($mysqli));
                while ($row = $datos_at->fetch_assoc()) { ?> 
                  <a class="dropdown-item" href="app.php?unidad_id=<?php  echo $row['unidad_id']; ?>"><?php echo $row['nombre']; ?></a>
                <?php } ?>  
              <?php } ?>
              <?php
              //1|2|3=Solo una unidad de mina
              if ($_SESSION['unidad_acc'] <> '0' and $_SESSION['unidad_acc'] <> '999') {
                $datos_umina = $mysqli->query("SELECT serie, unidad_id, nombre FROM arg_empr_unidades WHERE unidad_id = " . $_SESSION['unidad_acc']) or die(mysqli_error($mysqli));
                while ($row = $datos_umina->fetch_assoc()) { ?>
                  <a class="dropdown-item" id="unidad" href="app.php?&unidad_id=<?php  echo $row['unidad_id']; ?>"><?php  echo $row['nombre']; ?></a>
                <?php  }
              }
              //999=Varias unidades de mina (No Todas)
              $cadena = strlen($_SESSION['unidades']);
              $i = 0;
              if ($_SESSION['unidad_acc'] == '999') {
                while ($i <= $cadena) {
                  $valor = substr($_SESSION['unidades'], $i, 1);
                  if (is_numeric($valor)) {
                    $datos_umina = $mysqli->query("SELECT serie, unidad_id, nombre FROM arg_empr_unidades WHERE unidad_id = " . $valor) or die(mysqli_error($mysqli));
                    $mina_acce = $datos_umina->fetch_array(MYSQLI_ASSOC);
                    $mina_acc = $mina_acce['unidad_id'];
                    $mina_acc_nombre = $mina_acce['nombre'];?> 
                    <a class="dropdown-item" href="app.php?unidad_id=<?php  echo $mina_acc; ?>"><?php echo $mina_acc_nombre; ?></a>
                  <?php }
                  $i = $i + 1;
                }
              }?>
          </li>
        <?php  } ?>
      </ul>
    </div>
  </div>

  <?php  
    if (isset($_GET["unidad_id"])) {
      $unidad_mina_sel = $_GET['unidad_id'];
    } 
    else {
      $unidad_mina_sel = $_SESSION['unidad_def'];
    }
  ?>

  <br />
  <div class="col-xl-2 col-sm-4 col-md-5 col-ld-5 ">
    <a class="navbar-brand logos" href="seguimiento_ordenes.php?unidad_id=<?php  echo $unidad_mina_sel; ?>">
      <img src="images/minedata_lab.jpg" alt="ArgonautGold Logo">
    </a>
  </div>

  <div class="container" class="col-xl-3 col-sm-3 col-md-5 col-ld-5 ">
    <div class="row">

      <?php 
      $mysqli->set_charset("utf8");
      $datos_menu = $mysqli->query("SELECT DISTINCT
                                    menu_id, `nombre_directiva`, clase 
                                FROM 
                                    `perfiles_privilegios` 
                                WHERE 
                                    directiva_id = 1 AND activo = 1 AND u_id = " .$_SESSION['u_id']."
                                ORDER BY menu_id")
        or die(mysqli_error($mysqli));
      while ($row_menu = $datos_menu->fetch_assoc()) { ?>
        
        <div class="col-xl-3 col-sm-5 col-md-4 col-ld-5 ">
          <div class="card text-white text-xl-center bg-info o-hidden h-80">
            <div class="card-body">
              <div class="card-body-icon big">
                <i class="<?php echo $row_menu['clase'] ?>"></i>
              </div>
            </div>
            <div class="btn-group">
              <button type="button" id="dropdownMenuButton" class="btn btn-light btn-block dropdown-toggle" data-toggle="dropdown">
                <?php echo $row_menu['nombre_directiva'] ?>
              </button>
              <ul class="dropdown-menu">
                <?php  $datos_transacciones = $mysqli->query("SELECT DISTINCT nombre_directiva, valor_directiva, menu_id, accion, orden FROM `perfiles_privilegios` WHERE directiva_id = 2 AND activo = 1 AND menu_id = " . $row_menu['menu_id'] . " AND u_id = " . $_SESSION['u_id'] . " ORDER BY  orden") or die(mysqli_error($mysqli));
                $total_rows =  mysqli_num_rows($datos_transacciones);
                $i = 1;
                while ($row_transaccion = $datos_transacciones->fetch_assoc()) { ?>
                  <li> <a href="<?php  echo $row_transaccion['accion'] . $unidad_mina_sel; ?>">
                      <h5><?php  echo $row_transaccion['nombre_directiva'] ?></h5>
                    </a> </li>
                  <?php  if ($i < $total_rows) { ?>
                    <li class="divider"></li>
                  <?php  } ?>
                  <?php  $i++; ?>
                <?php  } ?>
              </ul>
            </div>
          </div>
        </div>
      <?php  } ?>
    </div>
  </div>
  </div>