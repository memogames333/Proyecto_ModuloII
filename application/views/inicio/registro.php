<style media="screen">
.register form input[type=text] {
  border: 1px solid #dddddd;
  width: 100%;
  height: 45px;
  -webkit-border-radius: 30px;
  -moz-border-radius: 30px;
  -ms-border-radius: 30px;
  border-radius: 30px;
  padding-left: 15px;
  margin-bottom: 10px;
}
.register form input[type=password] {
  border: 1px solid #dddddd;
  width: 100%;
  height: 45px;
  -webkit-border-radius: 30px;
  -moz-border-radius: 30px;
  -ms-border-radius: 30px;
  border-radius: 30px;
  padding-left: 15px;
  margin-bottom: 10px;
}
.register form input[type=email] {
  border: 1px solid #dddddd;
  width: 100%;
  height: 45px;
  -webkit-border-radius: 30px;
  -moz-border-radius: 30px;
  -ms-border-radius: 30px;
  border-radius: 30px;
  padding-left: 15px;
  margin-bottom: 10px;
}
.sort-box1{
  border: 1px solid #dddddd;
  width: 100%;
  height: 45px;
  -webkit-border-radius: 30px;
  -moz-border-radius: 30px;
  -ms-border-radius: 30px;
  border-radius: 30px;
  padding-left: 15px;
  margin-bottom: 35px;
}

.sort-box1:focus{
  border-color: #5677fc;
}

/* .select2-container {
  border: 1px solid #dddddd;
  width: 100%;
  height: 45px;
  -webkit-border-radius: 30px;
  -moz-border-radius: 30px;
  -ms-border-radius: 30px;
  border-radius: 30px;
  padding-left: 15px;
  margin-bottom: 35px;
} */

.register form input[type=password]:focus {
  border-color: #5677fc;
}

.register form input[type=password].placeholder {
  font-style: italic;
  color: #666666;
}

.register form input[type=password]:-moz-placeholder {
  font-style: italic;
  color: #666666;
}

.register form input[type=password]::-moz-placeholder {
  font-style: italic;
  color: #666666;
}

.register form input[type=password]:-ms-input-placeholder {
  font-style: italic;
  color: #666666;
}

.register form input[type=password]::-webkit-input-placeholder {
  font-style: italic;
  color: #666666;
}
.register form input[type=email]:focus {
  border-color: #5677fc;
}

.register form input[type=email].placeholder {
  font-style: italic;
  color: #666666;
}

.register form input[type=email]:-moz-placeholder {
  font-style: italic;
  color: #666666;
}

.register form input[type=email]::-moz-placeholder {
  font-style: italic;
  color: #666666;
}

.register form input[type=email]:-ms-input-placeholder {
  font-style: italic;
  color: #666666;
}

.register form input[type=email]::-webkit-input-placeholder {
  font-style: italic;
  color: #666666;
}



.register form label {
    font-size: 15px;
    color: #666666;
    font-weight: 0;
}
.error {
    background-color: transparent!important;
    border: 0 dashed #CA0B00;
    /* color: #CA0B00; */
}
label .error{
    /* color: #CA0B00; */
}
</style>
<section class="register">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <form name="formulario1A" id="formulario1A" autocomplete="off">
                    <h5>Crea tu cuenta</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <label> Nombre*</label>
                            <input type="text" name="nombre" id="nombre" placeholder="Su nombre">
                        </div>
                        <div class="col-md-6">
                            <label>Apellido*</label>
                            <input type="text" name="apellido" id="apellido" placeholder="Su apellido">
                        </div>
                        <div class="col-md-12" hidden>
                            <label> Numero de telefono*</label>
                            <input type="text" name="telefono" id="telefono" placeholder="Su numero de telefono">
                        </div>
                        <div class="col-md-12" hidden>
                            <label> Departamento*</label>
                            <!-- <input type="text" name="departamento" id="departamento" placeholder="Departamento"> -->
                            <select class="sort-box1 select" name="departamento" id="departamento">
                              <option value="">Seleccione</option>
                              <?php
                                  foreach ($departamento as $key)
                                  {
                                    $id_dep = $key["id_departamento"];
                                    $nombre = $key["nombre_departamento"];
                                    echo "<option value='".$id_dep."'>".$nombre."</option>";
                                  }
                              ?>
                            </select>
                        </div>
                        <div class="col-md-12" hidden>
                            <label> Municipio*</label>
                            <select class="sort-box1 select" name="municipio" id="municipio">
                              <option value="">Seleccione</option>
                            </select>
                        </div>
                        <div class="col-md-12" hidden>
                            <label> Dirección*</label>
                            <input type="text" name="direccion" id="direccion" placeholder="Dirección">
                        </div>
                        <div class="col-md-12">
                          <label> Correo electronico*</label>
                          <input type="email" name="mail" id="mail" placeholder="Su correo electronico">
                        </div>
                        <div class="col-md-6">
                            <label> Contraseña*</label>
                            <input type="password" name="password" id="password" placeholder="La contraseña debe tener más de 6 caracteres">
                        </div>
                        <div class="col-md-6">
                            <label>Confirmar Contraseña*</label>
                            <input type="password" name="confir_password" id="confir_password" placeholder="Confirme su contraseña">
                        </div>
                        <!-- <div class="col-md-7">
                            <div>
                                <input type="checkbox" name="t-box" id="t-box">
                                <label for="t-box">I have read the terms and condition.</label>
                            </div>
                            <div>
                                <input type="checkbox" name="c-box" id="c-box">
                                <label for="c-box">Subscribe for newsletter</label>
                            </div>
                        </div> -->
                        <div class="col-md-12 text-right">
                            <button type="submit" name="button">Registrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
