<h1 class="nombre-pagina">Olvide Password</h1>
<p class="descripcion-pagina">Restablece tu password escribiendo tu E-mail a continuación</p>

<?php

include_once __DIR__ . "/../templates/alertas.php";

?>

<form class="formulario" action="/olvide" method="POST">

  <div class="campo">
    <label for="email">E-mail</label>
    <input 
      type="email"
      name="email" 
      id="email"
      placeholder="Tu E-mail"
    />
  </div>

  <input class="boton" type="submit" value="Enviar Instrucciones"/>

</form>

<div class="acciones">
  <a href="/crear-cuenta">¿Aún no tienes una cuenta? Crea una</a>
  <a href="/">¿Ya tienes una cuenta? Inicia Sesión</a>
</div>