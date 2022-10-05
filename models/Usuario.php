<?php

namespace Model;

class Usuario extends ActiveRecord {

  // --- Base de datos --- //
  protected static $tabla = 'usuarios';
  protected static $columnasDB = ['id', 'nombre', 'apellido', 'email', 'password', 'telefono', 'admin', 'confirmado', 'token'];

  
  public $id;
  public $nombre;
  public $apellido;
  public $email;
  public $password;
  public $telefono;
  public $admin;
  public $confirmado;
  public $token;

  public function __construct($args = [])
  {
    $this->id = $args['id'] ?? null;
    $this->nombre = $args['nombre'] ?? '';
    $this->apellido = $args['apellido'] ?? '';
    $this->email = $args['email'] ?? '';
    $this->password = $args['password'] ?? '';
    $this->telefono = $args['telefono'] ?? '';
    $this->admin = $args['admin'] ?? '0';
    $this->confirmado = $args['confirmado'] ?? '0';
    $this->token = $args['token'] ?? '';
  }

  // --- Mensajes de validación para la creación de una cuenta --- //
  public function validarNuevaCuenta() {

    if(!$this->nombre) {

      self::$alertas['error'][] = 'El Nombre es Obligatorio';

    }

    if(!$this->apellido) {

      self::$alertas['error'][] = 'El Apellido es Obligatorio';

    }

    if(!$this->email) {

      self::$alertas['error'][] = 'El Email es Obligatorio';

    }

    if(!$this->password) {

      self::$alertas['error'][] = 'El Password del Cliente es Obligatorio';

    }

    if(strlen($this->password) < 6) {
      self::$alertas['error'][] = 'El Password debe contener al menos 6 caracteres';
    }

    return self::$alertas;
  }

  public function validarLogin() {

    if(!$this->email) {
      self::$alertas['error'][] = 'El E-mail es Obligatorio';
    }

    if(!$this->password) {
      self::$alertas['error'][] = 'El Password es Obligatorio';
    }

    return self::$alertas;

  }

  public function validarEmail() {

    if(!$this->email) {
      self::$alertas['error'][] = 'El E-mail es Obligatorio';
    }

    return self::$alertas;
  }

  public function validarPassword() {

    if(!$this->password) {

      self::$alertas['error'][] = 'El Password es Obligatorio';

    }

    if(strlen($this->password) < 6) {

      self::$alertas['error'][] = 'El Password debe tener al menos 6 caracteres';

    }

    return self::$alertas;

  }

  // --- Revisa si el usuario existe --- //
  public function existeUsuario() {

    $query = "SELECT * FROM " . self::$tabla . " WHERE email = '" . $this->email . "' LIMIT 1"; 

    $resultado = self::$db->query($query);

    /*NOTA:
      Al mandarse el query retorna un objeto con num_rows 0 o mas. En caso de ser mas que 0 significa que encontro coincidencias en la base de datos y el email ingresado esta previamente registrado, por lo tanto al retornar resultado se puede colocar en el controlador un codigo para mostrar el arreglo de con errores actualizar y en caso de que no encuentre coincidencias se proceda con el codigo para registrar el usuario
    */
    
    if($resultado->num_rows) {
      self::$alertas['error'][] = 'El Usuario ya esta registrado';
    }

    return $resultado;
  }

  public function hashPassword() {

    $this->password = password_hash($this->password, PASSWORD_BCRYPT);

  }

  public function crearToken() {

    $this->token = uniqid();

  }

  public function comprobarPasswordAndVerificado($password) {

    /*
    NOTA:Aqui lo que sucede es que en el controlador hay 2 instancias diferentes de la clase Usuario, una que se crea por medio de $auth que toma los valores del POST, es decir lo que el usuario escribio en el formulario, y otra que se genera con el metodo estatico where que busca un registro por columna y valor dentro de la tabla correspondiente a la clase desde la cual se lo llama. De tal forma que se compara lo que escribio el usuario que es el parametro que toma el auth y el this->password del metodo estatico(osea si es igual al registrado en la base de datos)
    */
    
    $resultado = password_verify($password, $this->password);

    if(!$resultado || !$this->confirmado) {
      
      self::$alertas['error'][] = 'Password Incorrecto o Tu Cuenta no ha sido Confirmada';

    } else {

      return true;

    }

  }
}