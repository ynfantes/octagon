<?php
include_once '../../includes/constants.php';
include_once '../../includes/file.php';

$propietario = new propietario();
$bitacora = new bitacora();

$propietario->esPropietarioLogueado();

$accion = isset($_GET['accion']) ? $_GET['accion'] : "bandeja-entrada";
$session = $_SESSION;

$notifiaciones = new notificacion();

switch ($accion) {
    // <editor-fold defaultstate="collapsed" desc="listar">
    case "listar": default:
        $resultado = null;
        $inbox = $notifiaciones->listarNotificacionesPropietario($session['usuario']['cedula']);
        if ($inbox['suceed']) {
            $resultado = $inbox['data'];
        }

        echo $twig->render('enlinea/mensajes/notificaciones.html.twig', array(
            "session" => $session,
            "lista" => $resultado
        ));
        break; 
    // </editor-fold>
    
    // <editor-fold defaultsta                                                                                                                                                                     te="collapsed" desc="marcar notificacion como leida">
    case "leida":
        $notifiaciones->insertarNotificacionPropietario(Array("id_notificacion" => $_GET['id'], "cedula" => $session['usuario']['cedula']));
        break; // </editor-fold>

}
