<?php
$debug          = FALSE;
$sistema        = "/";
$email_error    = TRUE;
$mostrar_error  = TRUE;
$produccion     = FALSE;
if ($_SERVER['SERVER_NAME'] == "www.administradora-octagon.com.ve" | $_SERVER['SERVER_NAME'] == "administradora-octagon.com.ve") {
    $user        = "";
    $password    = "";
    $db          = "";
    $email_error = TRUE;
    $mostrar_error = FALSE;
    $debug       = FALSE;
    $sistema     = "/";
    $produccion  = TRUE;
} else {
    $user        = "root";
    $password    = "";
    $db          = "valoriza2_octagon";
}