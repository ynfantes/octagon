<?php
$debug = FALSE;
$sistema = "/octagon/";
$email_error = TRUE;
$mostrar_error = TRUE;
$produccion = FALSE;
if ($_SERVER['SERVER_NAME'] == "www.administradora-octagon.com.ve" | $_SERVER['SERVER_NAME'] == "administradora-octagon.com.ve") {
    $user = "octagon_root";
    $password = "Octagon5231";
    $db = "valoriza2_octagon";
    $email_error = TRUE;
    $mostrar_error = FALSE;
    $debug = FALSE;
    $sistema = "/";
    $produccion = TRUE;
} else {
    $user = "root";
    $password = "";
    $db = "valoriza2_octagon";
}