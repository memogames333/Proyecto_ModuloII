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
                        <h3 class="text-success"><i class="fa fa-user"></i> Administracion de usuarios</h3>
                    </header>
                    <section>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover datatable" id="editable">
                                <thead class="">
                                    <tr>
                                        <thead>
                                          <tr>
                                            <th>ID</th>
                                            <th>Usuario</th>
                                            <th>Tipo</th>
                                            <th>Accion</th>
                                          </tr>
                                        </thead>
                                    </tr>
                                </thead>
                                <tbody>
                                  <?php
                                    $query = $this->db->get("usuario");
                                    foreach ($query->result() as $arrQuery) {
                                      // code...
                                      //echo $arrQuery->id_reservacion."#";
                                      ?>
                                        <tr>
                                          <td><?=$arrQuery->id_usuario; ?></td>
                                          <td><?=$arrQuery->usuario; ?></td>
                                          <td><?=($arrQuery->tipo==1)?"Administrador":"Cliente"; ?></td>
                                          <td><div class='btn-group'><button data-toggle='dropdown' class='btn btn-success dropdown-toggle' aria-expanded='false'><i class='mdi mdi-menu' aria-haspopup='false'></i> Menu</button><ul class='dropdown-menu dropdown-menu-right' x-placement='bottom-start'>
                                              <li><a role='button' href='' ><i class='mdi mdi-square-edit-outline' ></i> Editar</a></li></ul></div>

                                          </td>
                                        </tr>
                                      <?php
                                    }
                                   ?>
                                </tbody>
                            </table>
                        </div>
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

<script type="text/javascript" src="<?=base_url()?>assets/js/scripts/reservaciones.js">

</script>
