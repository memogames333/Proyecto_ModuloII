<div id="x-root" class="x-root">


  <div id="top" class="site">



    <link rel="stylesheet" href="<?=base_url()?>assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?=base_url()?>assets/css/style.css">

    <header class="masthead masthead-inline" role="banner">



      <div class="x-navbar-wrap">
        <div class="x-navbar x-navbar-fixed-left" style="width:22%">
          <div class="x-navbar-inner">
            <div class="x-container max width">

              <a href="<?php echo base_url("inicio"); ?>" class="x-brand img">
                <img src="assets/img/logo.png" alt="Restaurant"></a>

              <a href="#" id="x-btn-navbar" class="x-btn-navbar collapsed" data-x-toggle="collapse-b" data-x-toggleable="x-nav-wrap-mobile" aria-expanded="false" aria-controls="x-nav-wrap-mobile" role="button">
                <i class="x-icon-bars" data-x-icon-s="&#xf0c9;"></i>
                <span class="visually-hidden">Navigation</span>
              </a>
              <style media="screen">
              #menu-item-65 {
                  border-width: 3px;
                  border-style: solid;
                  border-color: #d6653b;
                  border-radius: 0.35em;
                  font-size: 1em;
                  background-color: rgba(255, 255, 255, 0);
                  box-shadow: 0em 0.15em 0.65em 0em rgba(0, 0, 0, 0.25);
                  width: 305px;
                }
              </style>
              <nav class="x-nav-wrap desktop" role="navigation">
                <ul id="menu-main-menu" class="x-nav">
                  <li id="menu-item-65" class='<?php if($clave == 0){ echo "menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-89 current_page_item menu-item-97";}else{ echo "menu-item menu-item-type-post_type menu-item-object-page menu-item-96";}?>'>
                    <?php
                    if($this->session->logged_in)
                    {
                      ?>
                      <a href="<?php echo base_url("inicio") ?>" target="_blank"><span><?=$this->session->nombre;?></span></a>
                      <?php
                    }
                    else {
                      ?>
                      <a href="<?php echo base_url("login") ?>" target="_blank"><span>Iniciar Sesion</span></a>
                      <?php
                    }
                     ?>
                  </li>
                  <li id="menu-item-97" class='<?php if($clave == 1){ echo "menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-89 current_page_item menu-item-97";}else{ echo "menu-item menu-item-type-post_type menu-item-object-page menu-item-96";}?>'><a href="<?php echo base_url("inicio") ?>" aria-current="page"><span>Inicio</span></a></li>
                  <li id="menu-item-98" class='<?php if($clave == 8){ echo "menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-89 current_page_item menu-item-97";}else{ echo "menu-item menu-item-type-post_type menu-item-object-page menu-item-96";}?>'><a href="<?php echo base_url("reservaciones") ?>" aria-current="page"><span>Reservaciones</span></a></li>
                  <li id="menu-item-96" class='<?php if($clave == 2){ echo "menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-89 current_page_item menu-item-97";}else{ echo "menu-item menu-item-type-post_type menu-item-object-page menu-item-96";}?>'><a href="<?php echo base_url("gallery")?>"><span>Galeria</span></a></li>
                  <li id="menu-item-95" class='<?php if($clave == 3){ echo "menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-89 current_page_item menu-item-97";}else{ echo "menu-item menu-item-type-post_type menu-item-object-page menu-item-96";}?>'><a href="<?php echo base_url("menu")?>"><span>Menu</span></a></li>
                  <li id="menu-item-95" class='<?php if($clave == 4){ echo "menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-89 current_page_item menu-item-97";}else{ echo "menu-item menu-item-type-post_type menu-item-object-page menu-item-96";}?>'><a href="<?php echo base_url("catering")?>"><span>Platillos</span></a></li>
                  <li id="menu-item-94" class='<?php if($clave == 5){ echo "menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-89 current_page_item menu-item-97";}else{ echo "menu-item menu-item-type-post_type menu-item-object-page menu-item-96";}?>'><a href="<?php echo base_url("location")?>"><span>Ubicacion</span></a></li>
                  <li id="menu-item-93" class='<?php if($clave == 6){ echo "menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-89 current_page_item menu-item-97";}else{ echo "menu-item menu-item-type-post_type menu-item-object-page menu-item-96";}?>'><a href="<?php echo base_url("about")?>"><span>Acerca de Nosotros</span></a></li>
                  <li id="menu-item-92" class='<?php if($clave == 7){ echo "menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-89 current_page_item menu-item-97";}else{ echo "menu-item menu-item-type-post_type menu-item-object-page menu-item-96";}?>'><a href="<?php echo base_url("contact")?>"><span>Contactanos</span></a></li>
                </ul>
              </nav>

              <div id="x-nav-wrap-mobile" class="x-nav-wrap mobile x-collapsed" data-x-toggleable="x-nav-wrap-mobile" data-x-toggle-collapse="1" aria-hidden="true" aria-labelledby="x-btn-navbar">
                <ul id="menu-main-menu-1" class="x-nav">
                  <li class="<?php if($clave == 1){ echo "menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-89 current_page_item menu-item-97";}else{ echo "menu-item menu-item-type-post_type menu-item-object-page menu-item-96";}?>"><a href="<?php echo base_url("inicio") ?>" aria-current="page"><span>Inicio</span></a></li>
                  <li class="<?php if($clave == 2){ echo "menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-89 current_page_item menu-item-97";}else{ echo "menu-item menu-item-type-post_type menu-item-object-page menu-item-96";}?>"><a href="<?php echo base_url("gallery")?>"><span>Galeria</span></a></li>
                  <li class="<?php if($clave == 3){ echo "menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-89 current_page_item menu-item-97";}else{ echo "menu-item menu-item-type-post_type menu-item-object-page menu-item-96";}?>"><a href="<?php echo base_url("menu")?>"><span>Menu</span></a></li>
                  <li class="<?php if($clave == 4){ echo "menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-89 current_page_item menu-item-97";}else{ echo "menu-item menu-item-type-post_type menu-item-object-page menu-item-96";}?>"><a href="<?php echo base_url("catering")?>"><span>Platillos</span></a></li>
                  <li class="<?php if($clave == 5){ echo "menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-89 current_page_item menu-item-97";}else{ echo "menu-item menu-item-type-post_type menu-item-object-page menu-item-96";}?>"><a href="<?php echo base_url("location")?>"><span>Ubicacion</span></a></li>
                  <li class="<?php if($clave == 6){ echo "menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-89 current_page_item menu-item-97";}else{ echo "menu-item menu-item-type-post_type menu-item-object-page menu-item-96";}?>"><a href="<?php echo base_url("about")?>"><span>Acerca de </span></a></li>
                  <li class="<?php if($clave == 7){ echo "menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-89 current_page_item menu-item-97";}else{ echo "menu-item menu-item-type-post_type menu-item-object-page menu-item-96";}?>"><a href="<?php echo base_url("contact")?>"><span>Contactanos</span></a></li>
                </ul>
              </div>

            </div>
          </div>
        </div>
      </div>


    </header>
