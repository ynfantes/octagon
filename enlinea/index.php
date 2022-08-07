<?php
include_once '../includes/constants.php';

propietario::esPropietarioLogueado();

$fecha_actualizacion = null;

$session = $_SESSION;
// <editor-fold defaultstate="collapsed" desc="cantidad recibos pendientes">
$f = new factura();
$p = new pago();

$cuenta = $f->numeroRecibosPendientesPropitario($session['usuario']['cedula']);
$recibos = 0;
$cancela = 0;
if ($cuenta['suceed']) {
    if (count($cuenta['data']) > 0) {
        $recibos = $cuenta['data'][0]['cantidad'];
    }
}
$pagos = $p->numeroRecibosCanceladosPorPropitario($session['usuario']['cedula']);
if ($pagos['suceed']) {
    if (count($pagos['data']) > 0) {
        $cancela = $pagos['data'][0]['cantidad'];
    }
}
// </editor-fold>

$mensajes = new mensajes();
$m = $mensajes->cantidadMensajesSinLeerPorPropietario($session['usuario']['cedula'],$session['id_sesion']);

$notificacion = new notificacion();
$n = $notificacion->obtenerNumeroNuevasNotificacionesPropietario($session['usuario']['cedula']);

$o = $notificacion->obtenerNumeroNotificacionesPropietario($session['usuario']['cedula']);

$sesiones = propietario::obtenerInfoUltimasSesiones($session['usuario']['cedula'],$session['id_sesion']);
$historico = null;

if ($sesiones['suceed']) {
    $historico = $sesiones['data'];
}

echo $twig->render('enlinea/index.html.twig', array(
    'session'               => $session,
    'fecha_actualizacion'   => $fecha_actualizacion,
    'recibos'               => $recibos,
    'cancelacion'           => $cancela,
    'historico'             => $historico,
    'mensajes'              => $m,
    'notificaciones'        => $n,
    'total_notificaciones'  => $o
));