<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="wrapper wrapper-content  animated fadeInRight">
    <div class="row container_admin" style="width:100%!important; padding:20px 280px;">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-content">
                    <!--load datables estructure html-->
                    <header>
                        <h3 class="text-success"><i class="fa fa-user"></i> Nuevo Usuario</h3>
                    </header>
                    <section>
                      <form class="" id="form_add" action="" method="post">
                        <div class="row">
                          <div class="col-lg-6">
                            <div class="">
                              <label for="">Usuario</label>
                              <input type="text" name="usuario" class="form-control" placeholder="Ingrese nombre de usuario" id="usuario" value="">
                            </div>
                          </div>
                          <div class="col-lg-6">
                              <label for="">Contraseña</label>
                              <input type="text" name="password" class="form-control" placeholder="Ingrese Contraseña" id="password" value="">
                          </div>
                          <div class="col-lg-6">
                            <br>
                              <label for="">Correo</label>
                              <input type="text" name="correo" class="form-control" placeholder="Ingrese correo" id="password" value="">
                          </div>
                          <div class="col-lg-6">
                            <div class="">
                              <br>
                              <label for="">Tipo de Usuario</label>
                              <select class="form-control" id="tipoUsuario" name="tipoUsuario">
                                <option value="0">Seleccione...</option>
                                <option value="1">Administrador</option>
                                <option value="2">Cliente</option>
                              </select>
                            </div>
                          </div>
                        </div>
                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
                        <a class="btn btn-success" style="color:#fff; margin-top:10px;" id="enviar">Enviar</a>
                      </form>
                        <!--div class='ibox-content'-->
                    </section>
                    <!--Show Modal Popups View & Delete -->
                </div>
                <!--div class='ibox-content'-->
            </div>
            <!--<div class='ibox float-e-margins' -->
        </div>
        <!--div class='col-lg-12'-->
    </div>
    <!--div class='row'-->
</div>

<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
<?php if(isset($proceso)){ ?>
<input type="hidden" value="<?php echo $proceso; ?>" id="proceso">
<?php } ?>


<div class='modal  fade' id='viewModal' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
    <div class='modal-dialog modal-md'>
        <div class='modal-content modal-md'>
		</div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<link rel="stylesheet" href="<?=base_url("assets/css/toastr.css")?>">
<link rel="stylesheet" href="<?=base_url("assets/css/bootstrap.min.css")?>">

<link href="<?= base_url("assets/libs/datapicker/bootstrap-datepicker.min.css"); ?>" rel="stylesheet">
<link href="<?= base_url("assets/libs/dataTables/datatables.css"); ?>" rel="stylesheet">

<script>var base_url = '<?php echo base_url() ?>'</script>
<script src="<?php echo base_url("assets/js/jquery-3.1.1.min.js"); ?>"></script>
<script src="<?=base_url("assets/js/popper.min.js")?>"></script>
<script src="<?php echo base_url("assets/js/bootstrap.js"); ?>"></script>
<script src="<?php echo base_url("assets/libs/sweetalert2/sweetalert2.min.js"); ?>"></script>
<script src="<?=base_url("assets/js/toastr.js")?>"></script>
<script src="<?= base_url("assets/libs/datapicker/bootstrap-datepicker.min.js"); ?>"></script>
<script src="<?= base_url("assets/libs/mask/jquery.mask.min.js"); ?>"></script>
<script src="<?= base_url("assets/libs/numeric/jquery.numeric.js"); ?>"></script>
<script src="<?php echo base_url("assets/libs/dataTables/datatables.js"); ?>"></script>

<script type="text/javascript" src="<?=base_url()?>assets/js/scripts/login.js">

</script>
