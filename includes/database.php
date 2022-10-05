<?php

$db = mysqli_connect(
  $_ENV['DB_HOST'],
  $_ENV['DB_USER'],
  $_ENV['DB_PASS'], // a veces no funciona asi y es necesario agregarle un placeholder con un string vacio en algunas computadoras
  $_ENV['DB_BD']
);

$db->set_charset('utf8');

if (!$db) {

  echo "Error: No se pudo conectar a MySQL.";
  echo "errno de depuración: " . mysqli_connect_errno();
  echo "error de depuración: " . mysqli_connect_error();
  exit;

} 
