<?php
defined('BASEPATH') or exit('No direct script access allowed');
//echo md5("123456");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="<?php echo base_url("assets/img/logofav.png"); ?>" rel="icon" type="image/png">
  <title>Taqueria Sosa</title>
  <link href="<?php echo base_url("assets/css/bootstrap.min.css"); ?>" rel="stylesheet">
  <link href="<?php echo base_url("assets/libs/mdi/css/materialdesignicons.min.css"); ?>" rel="stylesheet">

  <link href="<?=base_url("");?>assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet">

  <link href="<?php echo base_url(""); ?>assets/css/main.css" rel="stylesheet">
  <link href="<?php echo base_url(""); ?>assets/css/animate.css" rel="stylesheet">
  <link href="<?php echo base_url(""); ?>assets/css/style.css" rel="stylesheet">

</head>
<body class="gray-bg">
  <!--<div class="loginColumns animated fadeInDown">
    <div class="row">
      <div class="col-md-6 text-center">
        <h2 class="font-bold">CONSOLA DE ADMINISTRACION</h2>
        <div>
          <center>
            <img alt="image" style="width: 80%; margin-top:0%;" src="<?php /*echo base_url("") . "assets/img/logo.png"; */?>" />
          </center>
        </div>
      </div>
      <div class="col-md-6">
        <div class="ibox-content" style="margin-top: 9%;">
          <label>Por favor ingrese sus credenciales.</label>
          <form class="m-t" role="form" method="POST">
            <div class="form-group">
              <input type="text" name="correo" class="form-control" id="correo" placeholder="Usuario" required="">
            </div>
            <div class="form-group">
              <input type="password" class="form-control" id="clave" required="" name="clave" placeholder="Clave">
            </div>
            <div class="row">
              <div class="col-lg-6">
                <button type="button" class="btn btn-primary block full-width m-b" id="btn_ini_sesion">Iniciar Sesi&oacute;n</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="row">
      <hr>
      <p class="text-muted">Neko Systems - Copyright</p>
    </div>
  </div>-->
  <div class="loginColumns animated fadeInDown">
      <div class="row">

          <div class="col-md-6">
              <h1 class="font-bold text-center text-dark">ADMINISTRACION</h1>

              <img class="mx-auto d-block" src="<?=base_url("assets/img/logo.png")?>" width="80%">

          </div>
          <div class="col-md-6">
              <div class="ibox-content">
                  <h4>Ingrese sus credenciales</h4>
                  <form class="m-t" role="form" method="POST">
                      <div class="form-group">
                          <input type="text" name="correo" class="form-control" id="correo" placeholder="Usuario" required="">
                      </div>
                      <div class="form-group">
                          <input type="password" class="form-control" id="clave" required="" name="clave" placeholder="Clave">
                      </div>
                      <div class="row">
                          <div class="col">
                              <button type="button" class="btn btn-success block full-width m-b" id="btn_ini_sesion">Iniciar Sesi&oacute;n</button>
                              <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
                          </div>
                      </div>
                  </form>
              </div>
          </div>
      </div>
      <hr>
      <div class="row">
          <div class="col-md-6">
              Proyecto modulo - Todos los derechos reservados
          </div>
          <div class="col-md-6 text-right">
              <small>© <?=date("Y")?></small>
          </div>
      </div>
  </div>
</body>
<script src="<?php echo base_url("assets/js/jquery-3.1.1.min.js"); ?>"></script>
<script src="<?php echo base_url("assets/js/bootstrap.js"); ?>"></script>
<script src="<?php echo base_url("assets/libs/sweetalert2/sweetalert2.min.js"); ?>"></script>
<script>var base_url = '<?php echo base_url() ?>';</script>
<script src="<?php echo base_url("assets/js/scripts/login.js"); ?>"></script>
</html>
