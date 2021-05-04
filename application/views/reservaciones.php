<form class="" id="form_add" action="" method="post">
  <div class="row">
    <div class="col-lg-2">

    </div>
    <div class="col-lg-8">
      <label for="" class="label_reservaciones margin_top">Formulario de Reservaciones</label>

      <div class="form-group">
        <label for="" class="label_reservaciones">Nombre Completo</label>
        <input type="text" class="form-control" name="nombre" placeholder="Ingrese su nombre completo" value="">
      </div>
      <div class="row">
        <div class="col-lg-4">
          <div class="form-group">
            <label for="" class="label_reservaciones">Telefono</label>
            <input type="text" class="form-control tel" name="telefono" placeholder="Ingrese telefono" value="">
          </div>
        </div>
        <div class="col-lg-4">
          <div class="form-group">
            <label for="" class="label_reservaciones">Fecha Reservacion</label>
            <input type="text" class="form-control fecha" name="fecha" id="fecha" placeholder="yyyy-mm-dd" value="">
          </div>
        </div>
        <div class="col-lg-4">
          <div class="form-group">
            <label for="" class="label_reservaciones">Numero de Mesas</label>
            <input type="text" class="form-control numeric" name="numero_mesas" placeholder="Ingrese numero" value="">
          </div>
        </div>
        <div class="col-lg-9"></div>
        <div class="col-lg-3">
          <a class="btn btn-primary btn_res" id="enviar">Enviar Reservacion</a>
          <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
        </div>
      </div>
    </div>
  </div>
</form>
<link rel="stylesheet" href="<?=base_url("assets/css/toastr.css")?>">
<link href="<?= base_url("assets/libs/datapicker/bootstrap-datepicker.min.css"); ?>" rel="stylesheet">

<script>var base_url = '<?php echo base_url() ?>'</script>
<script src="<?php echo base_url("assets/js/jquery-3.1.1.min.js"); ?>"></script>
<script src="<?php echo base_url("assets/js/bootstrap.js"); ?>"></script>
<script src="<?php echo base_url("assets/libs/sweetalert2/sweetalert2.min.js"); ?>"></script>
<script src="<?=base_url("assets/js/toastr.js")?>"></script>
<script src="<?= base_url("assets/libs/datapicker/bootstrap-datepicker.min.js"); ?>"></script>
<script src="<?= base_url("assets/libs/mask/jquery.mask.min.js"); ?>"></script>
<script src="<?= base_url("assets/libs/numeric/jquery.numeric.js"); ?>"></script>

<script type="text/javascript" src="<?=base_url()?>assets/js/scripts/reservaciones.js">

</script>
