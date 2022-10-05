<h1 class="nombre-pagina">Panel de Administración</h1>

<?php include_once __DIR__ . '/../templates/barra.php';?>

<h2>Buscar Citas</h2>
<div class="busqueda">
  <form class="formulario">

    <div class="campo">
      <label for="fecha">Fecha</label>
      <input
        type="date"
        id="fecha"
        name="fecha"
        value="<?php echo $fecha ?>"
      >
    </div>

  </form>
</div>

<?php
  if(count($citas) === 0) {
    echo "<h2>No hay citas en esta fecha</h2>";
  }
?>

<div id="citas-admin">
  <ul class="citas">
    <?php 
      $idCita = 0;

      foreach( $citas as $key => $cita ) { 

        if($idCita !== $cita->id) {

          $total = 0; // se inicializa en 0 el total a pagar aqui ya este codigo se ejecuta al verificarse una nueva cita. Si se lo colocara antes del if entonces se inicializaria en cada iteracion que tiene id repetido porque hay un registro de servicio ligado a la misma cita con un mismo id que se repite en la tabla de citasservicios.

    ?>

      <li>
        <p>ID: <span><?php echo $cita->id; ?></p>
        <p>Hora: <span><?php echo $cita->hora; ?></p>
        <p>Cliente: <span><?php echo $cita->cliente; ?></p>
        <p>Email: <span><?php echo $cita->email; ?></p>
        <p>Teléfono: <span><?php echo $cita->telefono; ?></p>

        <h3>Servicios</h3> 

        <?php 
          $idCita = $cita->id; // que es esto? es una comprobacion mediante un if para revisar si el registro ya ha sido importado 1 vez y no se muestre de forma repetida ya que el codigo sql muestra registros repetidos porque son los servicios ligados al id de la cita entonces el id caso contrario se mostraria muchas veces. Se coloca aqui para que al menos se muestre 1 vez y despues se realiza la comprobacion en la siguiente iteracion.
          
        } // fin del if 
          $total += $cita->precio; // esta sumatoria va luego del if porque sino sumaria un solo servicio que es el que esta con un id diferente por ser de otra cita. Osea el primero de cada cita 
        ?> 
        
        <p class="servicio"><?php echo $cita->servicio . " " . $cita->precio; ?></p>
      
      <!-- </li> se borra el cierre del li para que html lo cierre automaticamente y no aparezca el primer servicio de forma asimetrica --> 

        <?php 

          $actual = $cita->id; //cita de la iteracion actual
          $proximo = $citas[$key + 1]->id ?? 0; // proxima iteracion ya que $key muestra el id de la cita + 1, es decir la cita con el id siguiente a la iteracion actual. y se coloca 0 como placeholder para que no marque undefined al tratar de guardar un valor no existente en el arreglo de citas
           
          if(esUltimo($actual, $proximo)) { ?>
          
            <p class="total">Total: <span>$ <?php echo $total ;?></span></p>

            <form action="/api/eliminar" method="POST">
              <input type="hidden" name="id" value="<?php echo $cita->id; ?>">
              <input type="submit" class="boton-eliminar" value="Eliminar">
            </form>
           
    <?php }
      } // fin de foreach ?>
    
  </ul>
  
</div>

<?php 
  $script = "<script src='build/js/buscador.js'></script>";
?>
