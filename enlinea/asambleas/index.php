<?php
include_once '../../includes/constants.php';
        
$accion = isset($_GET['accion']) ? $_GET['accion'] : "listar";
$asamblea = new asamblea();

switch ($accion) {
    
    // <editor-fold defaultstate="collapsed" desc="guardar">
    case "guardar":
        $data = $_GET;
        unset($data['accion']);
        $data['fecha'] =  Misc::format_mysql_datatime($data['fecha']. ' '.$data['hora']);
        unset($data['hora']);
        $resultado = $asamblea->insertar($data);
        
        if ($resultado['suceed']) {
            unset($data['archivo_soporte']);
            $data['descripcion']='Asamblea General Extraordinaria';
            $notificacion = new notificacion();
            unset($data['id_asamblea']);
            $resultado= $notificacion->insertar($data);
            echo "0";
        } else {
            echo $resultado['stats']['error'];
        }
        break; 
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="actualizar">
    case "actualizar":
        $data = $_GET;
        unset($data['accion']);
        unset($data['id']);
        $resultado = $asamblea->actualizarAsamblea($_GET['id'], $data);
        if ($resultado['suceed']) {
            echo "0";
        } else {
            echo $resultado['stats']['error'];
        }
        break; // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="listar">
    case "listar":
        $propietario = new propietario();

        $propietario->esPropietarioLogueado();
        $session = $_SESSION;
        $lista = $asamblea->listarAsambleasPorPropietarios($session['usuario']['cedula']);
        $asambleas = null;
        
        if ($lista['suceed'] && count($lista['data']>0)) {
            $asambleas=$lista['data'];
            for ($index = 0; $index < count($asambleas); $index++) {
                $filename = "../../img/asambleas/convocatoria/" . $asambleas[$index]['archivo_soporte'];
                $asambleas[$index]['convocatoria'] = file_exists($filename) && $asambleas[$index]['archivo_soporte']!='';
                $filename = $asambleas[$index]['archivo_acta'];
                $asambleas[$index]['acta'] = file_exists($filename) && $asambleas[$index]['archivo_acta']!='';
            }
        }
        //var_dump($asambleas);
        echo $twig->render('enlinea/asambleas/listar.html.twig', array(
            "session"  => $session,
            "lista" => $asambleas
        ));
        break; 
    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="lsitar json">
    case "listaJSon":
        $propietario = new propietario();
        $propietario->esPropietarioLogueado();
        $session = $_SESSION;
        $lista = $asamblea->listarAsambleasPorPropietariosCalendario($session['usuario']['cedula']);
        $asambleas = null;


        if ($lista['suceed'] && count($lista['data'] > 0)) {
            $asambleas = $lista['data'];
        }
        echo json_encode($asambleas);
        break; 
    // </editor-fold>
    
    case "verActa":
        $url = URL_SISTEMA."/asambleas/".$_GET['id'];
        header("location:$url");
        break;
}

