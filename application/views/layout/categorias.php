
                    <div class="col-lg-3 col-md-0">
                        <div class="menu-widget">
                            <p><i class="fa fa-bars"></i>Categorias</p>
                            <ul class="list-unstyled">
                              <?php foreach ($categoriasleft as $cat): ?>
                                <?php if ($cat["subcategorias"]!=null): ?>
                                  <li><a href="cat/<?=$cat["id_categoria"]?>"><img src="<?=base_url()?>assets/images/m-cloth.png" alt=""><?=$cat["nombre"]?><i class="fa fa-angle-right"></i></a>
                                    <div class="mega-menu">
                                        <div class="row">
                                          <div class="col-md-4">
                                          <?php foreach ($cat["subcategorias"] as $sub): ?>
                                            <div class="<?=$sub["nombre"]?>">
                                              <h6><?=$sub["nombre"]?></h6>

                                              <a href="index.html">- Samsung</a>
                                            </div>
                                          <?php endforeach; ?>
                                                </div>

                                        </div>
                                    </div>
                                    </li>
                                <?php else: ?>
                                  <li><a href="cat/<?=$cat["id_categoria"]?>"><img src="<?=base_url()?>assets/images/sm.png" alt=""><?=$cat["nombre"]?></a></li>
                                <?php endif; ?>
                              <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
