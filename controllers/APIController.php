<?php

namespace Controllers;

use Model\Cita;
use Model\CitaServicio;
use Model\Servicios;

class APIController {

  public static function index() {

    $servicios = Servicios::all();
    
    echo json_encode($servicios);
  }

  public static function guardar() {

    // --- Almacena la Cita y devuelve el ID insertado en la BD --- //
    $cita = new Cita($_POST);

    $resultado = $cita->guardar();
    
    // --- Almacena en la base de datos los registros de CitaServicios --- //
    $id = $resultado['id'];

    $idServicios = explode(',', $_POST['servicios']);

    foreach($idServicios as $idServicio) {

      $args = [
        'citaId' => $id,
        'servicioId' => $idServicio
      ];

      $citaServicio = new CitaServicio($args);

      $citaServicio->guardar();

    }
    /*NOTA: Se itera por cada servicio id de servicio pasado desde el form data y se genera una instancia de cita servicios cambiando los datos que normalmente traeria del post a un arreglo nuevo con las especificaciones de la tabla de citasServicios*/

    echo json_encode(['resultado' => $resultado]);
    
  }

  public static function eliminar() {
    
    if($_SERVER['REQUEST_METHOD'] === 'POST') {

      $id = $_POST['id'];
      $cita = Cita::find($id);
      $cita->eliminar();
      header('Location:' . $_SERVER['HTTP_REFERER']);

    }
  }
}