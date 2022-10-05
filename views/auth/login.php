<h1 class="nombre-pagina">Login</h1>
<p class="descripcion-pagina">Inicia sesión con tus datos</p>

<?php

include_once __DIR__ . "/../templates/alertas.php";

?>

<form action="/" method="POST" class="formulario">
  <div class="campo">
    <label for="email">Email</label>
    <input 
    autocomplete="email"
      id="email" 
      type="email"
      placeholder="Tu E-Mail"
      name="email"
    />

  </div>

  <div class="campo">
    <label for="password">Password</label>
    <input 
      autocomplete="password"
      type="password" 
      name="password" 
      id="password"
      placeholder="Tu Password"
    />
  </div>

  <input type="submit" type="submit" class="boton" value="Iniciar Sesión">
</form>

<div class="acciones">
  <a href="/crear-cuenta">¿Aún no tienes una cuenta? Crea una</a>
  <a href="/olvide">Olvidaste tu password</a>
</div>