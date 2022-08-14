<?php
include_once 'includes/constants.php';
$mantenimiento=false;
$avance=0;
$propietarios = new propietario();
$propiedades = new propiedades();
$facturas = new factura();
$result = $propietarios->listar();
if (!$result['suceed'] || empty($result['data'])) {
    $mantenimiento=TRUE;
} else {
    $result = $propiedades->listar();
    if (!$result['suceed'] || empty($result['data'])) {
        $mantenimiento=TRUE;
        
    } else {
        $result = $facturas->listar();
        if (!$result['suceed'] || empty($result['data'])) {
            $mantenimiento=TRUE;
        }
    }    
}

if ($mantenimiento) {
    $mail = new mailto(SMTP);
    $min = date("i");
    $avance = $min * 100 / 60;
    //enviamos una comunicacion al administrador del sistema
    if (PRODUCCION) {
        $mail->enviar_email(TITULO.' [Mantenimiento]','Sincronice la página web','','ynfantes@gmail.com','Valoriza2');
    }
}
$cartelera = new cartelera();
$cartelera->tabla="cartelera_publica";
$cartelera_publica = $cartelera->listar();

echo $twig->render(
        'index.html.twig',
        Array(
            "mantenimiento" => $mantenimiento,
            "avance"        => $avance,
            "cartelera_publica" => $cartelera_publica['data']
            )
        );