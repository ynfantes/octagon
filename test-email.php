<?php
include_once './includes/constants.php';
$mail = new mailto(SMTP);
$r = $mail->enviar_email("Prueba", "Este es un mensaje de prueba", '', "ynfantes@gmail.com", "Edgar Messia");

if ($r=='') {
    echo (".- Mensaje enviado a Ok!");
} else {
    echo (".- Mensaje enviado a Falló");
}
//$hoy = date("Y-m-d H:i:00 ", time());
//echo $hoy;