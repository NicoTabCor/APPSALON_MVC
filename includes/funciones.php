<?php

function debuguear($variable): string
{
  echo "<pre>";
  var_dump($variable);
  echo "</pre>";
  exit;
}

// Escapa / Sanitizar el HTML
function s($html): string
{
  $s = htmlspecialchars($html);
  return $s;
}

function esUltimo(string $actual, string $proximo): bool {
  if($actual !== $proximo) {
    return true;
  }
  return false; // esta funcion revisa si el id actual corresponde a una misma cita, si el id actual es diferente al proximo entonces significa que el id proximo es otra cita diferente
}

// --- Funcion que revisa que el usuario este autenticado --- //

function isAuth() : void {
  
  if(!isset($_SESSION['login'])) {

    header("Location: /");
    
  }
}

function isAdmin() : void {
  if(!isset($_SESSION['admin'])) {
    header('Location: /');
  }
}
