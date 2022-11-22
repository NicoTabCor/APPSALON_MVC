<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {

  public static function login(Router $router) {

    $alertas = [];

    $auth = new Usuario();

    if($_SERVER['REQUEST_METHOD'] === 'POST') {

      $auth = new Usuario($_POST);

      $alertas = $auth->validarLogin();

      if(empty($alertas)) {
        // --- Comprobar que exista el Usuario --- //
        $usuario = Usuario::where('email', $auth->email);

        // --- El Usuario existe --- //
        if($usuario) {

          // --- Verificar Password --- //
          if($usuario->comprobarPasswordAndVerificado($auth->password)) {

            // --- Autenticar el Usuario --- //
            session_start();

            $_SESSION['id'] = $usuario->id;
            $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
            $_SESSION['email'] = $usuario->email;
            $_SESSION['login'] = true;

            // --- Redireccionamiento --- //
            if($usuario->admin === "1") {
              
              $_SESSION['admin'] = $usuario->admin ?? null;
              header("Location: /admin");

            } else {
              
              header("Location: /cita");

            }

          }

        } else {
          // --- El Usuario no existe --- //
          Usuario::setAlerta('error', 'Usuario no encontrado');

        }

      }
      
    }

    $alertas = Usuario::getAlertas();

    $router->render('auth/login',[

      'alertas' => $alertas,
      'auth' => $auth

    ]);
    
  }

  public static function logout() {
    
    session_start();
    
    $_SESSION = [];
    
    header("Location: /");

  }

  public static function olvide(Router $router) {

    $alertas = [];

    if($_SERVER['REQUEST_METHOD'] === 'POST') {

      $auth = new Usuario($_POST);
      $alertas = $auth->validarEmail();

      if(empty($alertas)) {

        $usuario = Usuario::where('email', $auth->email);

        // --- Verificar si el usuario existe y esta confirmado --- //
        if($usuario && $usuario->confirmado === '1') {
          
          // --- Generar token para  --- //
          $usuario->crearToken();

          // --- Actualizar el registro del email y le agrega el token para luego poder enviarselo a su email --- //
          $usuario->guardar();

          // --- Enviar el email --- //
          $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
          $email->enviarInstrucciones();


          // --- Alerta de exito --- //
          Usuario::setAlerta('exito', 'Revisa tu E-mail');
          $alertas = Usuario::getAlertas();


        } else {
          Usuario::setAlerta('error', 'El Usuario no existe o no esta confirmado');

          $alertas = Usuario::getAlertas();
        }
      }

    }

    $router->render('auth/olvide-password', [

      'alertas' => $alertas

    ]);
  }

  public static function recuperar(Router $router) {
    
    $alertas = [];
    
    $error = false;

    $token = s($_GET['token']);

    // --- Buscar Usuario por su token --- //
    $usuario = Usuario::where('token', $token);

    if(empty($usuario)) {

      Usuario::setAlerta('error', 'Token no Válido');
      $error = true;

    }

    if($_SERVER['REQUEST_METHOD'] === 'POST') {

      // --- Leer el nuevo password y guardarlo --- //

      // --- Instancia el objeto y se pasan los valores llenados por el usuario --- //
      $password = new Usuario($_POST);

      // --- Valida que no falte el password --- //
      $alertas = $password->validarPassword();

      // --- Si no hay errores se reemplaza el password de la base de datos por el nuevo introducido --- //
      if(empty($alertas)) {

        $usuario->password = null;

        $usuario->password = $password->password;

        // --- Se hashea para seguridad --- //
        $usuario->hashPassword();

        // --- Se elimina el token de la base de datos para que nadie pueda volver a utilizarlo si lograran descubrirlo --- //
        $usuario->token = null;

        // --- Se mandan los valores nuevos a la base de datos --- //
        $resultado = $usuario->guardar();

        if($resultado) {
          header("Location: /");
        }

      }
    }

    $alertas = Usuario::getAlertas();

    $router->render('auth/recuperar-password', [

      'alertas' => $alertas,
      'error' => $error

    ]);
    
  }

  public static function crear(Router $router) {
    
    $usuario = new Usuario($_POST);

    // --- Alertas vacias --- //
    $alertas = [];

    if($_SERVER['REQUEST_METHOD'] === 'POST') {

      $usuario->sincronizar($_POST);
      $alertas = $usuario->validarNuevaCuenta();

      // --- Revisar que alerta este vacio  --- //
      if(empty($alertas)) {

        // --- Verificar que el usuario no este registrado previamente --- //
        $resultado = $usuario->existeUsuario();

        // --- Esta Registrado --- //
        if($resultado->num_rows) {

          $alertas = Usuario::getAlertas();

        } else {
          // --- No esta Registrado --- //
          // --- Hashear el Password --- //
          $usuario->hashPassword();

          // --- Generar un Token único --- //
          $usuario->crearToken();
          
          // --- Instancio y lleno el objeto email con los datos introducidos por el usuario --- //
          $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
          
          // --- Enviar Confirmacion al email del usuario --- //
          $email->enviarConfirmacion();

          // --- Crear el Usuario --- //
          $resultado = $usuario->guardar();

          if($resultado) {

            header("Location: /mensaje");

          }
        }
      }
    }

    $router->render('auth/crear-cuenta', [
      'usuario' => $usuario,
      'alertas' => $alertas
    ]);
  }

  public static function mensaje(Router $router) {

    $router->render('auth/mensaje', []);
  }

  public static function confirmar(Router $router) {

    $alertas = [];

    // --- Leer el token de la url que se crear a partir del click en el enlace de confirmacion que se manda via email --- //
    $token = s($_GET['token']); // Se lo sanitiza

    // --- Se trae el primer registro de la columna del primer argumento con el valor del segundo --- //
    $usuario = Usuario::where('token', $token);

    // --- Si el query marca un error se añade un error al array de alertas --- //
    if(empty($usuario)) {

      // --- Mensaje de error --- //
      Usuario::setAlerta('error', 'El Token no es válido');

    } else {

      // --- Modificar a usuario confirmado --- //
      $usuario->confirmado = "1";
      $usuario->token = null;
      
      $usuario->guardar();
      Usuario::setAlerta('exito', 'Cuenta Comprobada Correctamente');

    }

    // --- Actualizar alertas en la vista --- //
    $alertas = Usuario::getAlertas();
    
    // --- Renderizar la vista --- //
    $router->render('auth/confirmar-cuenta', [

      'alertas' => $alertas
      
    ]);
  }
}
