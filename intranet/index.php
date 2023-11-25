<?php
include_once '../includes/constants.php';
include_once '../includes/usuario.php';
//usuario::esUsuarioLogueado();

$accion = isset($_GET['accion']) ? $_GET['accion'] : "cartelera-general";

switch ($accion) {
    // <editor-fold defaultstate="collapsed" desc="salir">
    case "salir":
        
        $user_logout = new usuario();
        $user_logout->logout();
        break; // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="lista Cuenta Bancaria">
    case "listarCuentaBancaria" :
        if (isset($_POST['id_inmueble'])) {
            $inmuebles = new inmueble();
            $r = $inmuebles->ver($_POST['id_inmueble']);
            if ($r['suceed'] && count($r['data']) > 0) {
                $jsonTable = json_encode($r['data']);
                echo $jsonTable;
            }
          }
        break; // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="lista propietarios por inmueble">
    case "listarPropietariosPorInmueble" :
        if (isset($_POST['id_inmueble'])) {
            $pro = new propietario();
            $r = $pro->listarPropietariosPorInmueble($_POST['id_inmueble']);
            if ($r['suceed'] && count($r['data']) > 0) {
                $jsonTable = json_encode($r['data']);
                echo $jsonTable;
            }
          }
        break; // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="lista periodos egresos">
    case "listarPeriodosEgresos" :
        if (isset($_POST['id_inmueble'])) {
            $inmuebles = new inmueble();
            $r = $inmuebles->periodosIngresos($_POST['id_inmueble']);
            $jsonTable = json_encode($r);
            echo $jsonTable;
          }
        break; // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="publicar informe">
    case "publicarInforme":
        $data = $_POST;
        $informe = new informes();
        if (isset($_FILES['archivo'])) {
            ini_set('max_execution_time', 20600);
            ini_set('max_input_time', 20600);
            ini_set('upload_max_filesize', '10M');
            ini_set('post_max_size', '10M');
            ini_set('memory_limit', '128M');
            $filename = basename(html_entity_decode(strtolower($_FILES['archivo']['name']), ENT_QUOTES, 'UTF-8'));

            $data['archivo'] = $filename;
              $data['publicado'] = @move_uploaded_file($_FILES['archivo']['tmp_name'], "../informes/".$filename);
        }
        $data['periodo'] = Misc::format_mysql_date($data['periodo']);
        
        $result = $informe->insertar($data);
        //$result['curl_errno'] = $error_no;
        unset($result['query']);
        if ($result['suceed']) {
            $result['mensaje'] = '<button type="button" class="close" data-dismiss="alert">';
            $result['mensaje'].= '<span aria-hidden="true">×</span><span class="sr-only">Close</span></button>';
            $result['mensaje'].= '<strong>Muy Bien! </strong> Publicación Registrada con éxito.';
        } else {
            $result['mensaje'] = '<button type="button" class="close" data-dismiss="alert">
            <span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
            <strong>Ups! Ha ocurrido un error</strong> No se pudo registrar el informe.';
        }
        echo json_encode($result);
        break; 
    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="eliminar informe mensual">
    case "eliminarInformeMensual":
        $data = Array();
        $informe = new informes();
        $inmueble = new inmueble();
        if (is_numeric($_GET['id'])) {
            $data['eliminado'] = -1;
            $id = $_GET['id'];
            $informe->actualizar($id, $data);
        }
        $id = 0;
        $informes = $inmueble->listadoInformesActivosPorInmueble(Array("eliminado" => 0));
        $lista_inm = $inmueble->listar();
        echo $twig->render('intranet/informes.html.twig', array(
            "session" => $session,
            "publicaciones" => $informes['data'],
            "id" => $id,
            "inmuebles" => $lista_inm['data']
        ));
        break; 
    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="estado-de-cuenta-propietario">
    case "estado-de-cuenta":
        $im = '';
        $apto = '';
        $cuenta = array();
        $lista_pro = array();
        $inmueble = new inmueble();

        if ($session['usuario']['directorio'] == 'admin') {
            $lista_inm = $inmueble->listar();
        } else {
            $lista_inm = $inmueble->listarInmueblesAutorizados($session['usuario']['id']);
        }
        //' . $session['usuario']['directorio'] . '/
        echo $twig->render('intranet/estado-de-cuenta.html.twig', array(
            "session" => $session,
            "cuentas" => $cuenta,
            "inm" => $im,
            "apto" => $apto,
            "inmuebles" => $lista_inm['data'],
            "propietarios" => $lista_pro
        ));
        break; // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="guardar-cartelera">
    case "guardar-cartelera":
        $result = Array();
        $data = Array();
        if (strlen($_POST['contenido']) < 15) {
            $result['suceed'] = false;
            $result['mensaje'] = "Debe agregar el contenido a la publicación.";
        } else {
            $data = $_POST;
            $cartelera = new cartelera();
            $cartelera->tabla = $_POST['tabla'];
            unset($data['tabla']);
            $data['fecha_hasta'] = Misc::format_mysql_date($data['fecha_hasta']);
            $data['fecha'] = Misc::format_mysql_date($data['fecha']);
            $result = $cartelera->insertar($data);
            $data['contenido'] = utf8_encode($data['contenido']);
            if ($result['suceed']) {
                $result['mensaje'] = "Publicación registrada con éxito";
            }
        }
        echo json_encode($result);
        break;
    // </editor-fold>
        
    // <editor-fold defaultstate="collapsed" desc="eliminar item cartelera condominio">
    case "eliminar-cartelera":
        $id = isset($_GET['id']) ? $_GET['id'] : "general";
        $data = Array();
        $cartelera = new cartelera();
        $cartelera->tabla = "cartelera_".$id;
        
        if (is_numeric($_GET['registro'])) {
            $id = $_GET['registro'];
            $data['eliminar'] = 1;
            $cartelera->actualizar($id, $data);
        }
       
    // </editor-fold>
    // 
    // <editor-fold defaultstate="collapsed" desc="listar cartelera">
    case "listar-cartelera":
        $id = isset($_GET['id']) ? $_GET['id'] : "general";
        $cartelera = new cartelera();
        switch ($id) {
            case "general":
                $cartelera->tabla = "cartelera_general";
                $publicaciones = $cartelera->listar();
                $titulo = "Cartelera General";
                break;
            case "publica":
                $cartelera->tabla = "cartelera_publica";
                $publicaciones = $cartelera->listar();
                $titulo = "Cartelera Publica";
                break;
            default:
                $cartelera->tabla = "cartelera_inmueble";
                $titulo = "Cartelera Condominio";
                $publicaciones = $cartelera->listar();
                break;
        }
        echo $twig->render('intranet/cartelera-listar.html.twig', array(
            "publicaciones" => $publicaciones['data'],
            "titulo"        => $titulo,
            "tipo"          => $id
        ));
        break;
    // </editor-fold>
    
        
    // <editor-fold defaultstate="collapsed" desc="estado de cuenta">
    case "consultar":
    case "estado-de-cuenta":
        $im         = '';
        $apto       = '';
        $cuenta     = array();
        $lista_pro  = array();
        $inmueble   = new inmueble();
        
        if (isset($_POST['inmueble']) && isset($_POST['apto'])) {
            
            $facturas = new factura();
            
            $propietario = new propietario();
            
            $im   = $_POST['inmueble'];
            $apto = $_POST['apto'];
            $inm = $inmueble->ver($im);
            
            $prop = $propietario->obtenerPropietario($im, $apto);
            
            $factura = $facturas->estadoDeCuenta($im, $apto);
            
            $r = $propietario->listarPropietariosPorInmueble($im);
            if ($r['suceed'] && count($r['data'])>0) {
                $lista_pro = $r['data'];
            }
            if ($factura['suceed'] == true) {

                for ($index = 0; $index < count($factura['data']); $index++) {

                    $filename = "../avisos/" . $factura['data'][$index]['numero_factura'] . ".pdf";
                    $factura['data'][$index]['aviso'] = file_exists($filename);
                }

                $cuenta = Array(
                    "inmueble"      => $im,
                    "apto"          => $apto,
                    "inmueble"      => $inm['data'],
                    "propietario"   =>$prop,
                    "recibos"       => $factura['data']);
            }
        }
        
        if ($session['usuario']['directorio'] == 'admin') {
            $lista_inm = $inmueble->listar();
        } else {
            $lista_inm = $inmueble->listarInmueblesAutorizados($session['usuario']['id']);
        }
        
        $template = $accion=='consultar'? 'index':'estado-de-cuenta';
        
        echo $twig->render('intranet/'.$template.'.html.twig', array(
            "session"       => $session,
            "cuentas"       => $cuenta,
            "inm"           => $im,
            "apto"          => $apto,
            "inmuebles"     => $lista_inm['data'],
            "propietarios"  => $lista_pro
        ));
        break; 
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="historico de pagos">
    case "historico-de-pagos":
        $im         = '';
        $apto       = '';
        $lista_pro  = array();
        $inmuebles = new inmueble();
        $historico = Array();
        $propiedad = Array();
        
        if (isset($_POST['inmueble']) && isset($_POST['apto'])) {
            $propie         = new propiedades();
            $pagos          = new pago();
            $db             = new db();
            $propietario    = new propietario();
            
            $im   = $_POST['inmueble'];
            $apto = $_POST['apto'];
            $propiedad = $db->dame_query("select * from propiedades where id_inmueble='".$im."' and apto='".$apto."'")['row'];
            
            $inmueble = $inmuebles->ver($im);
            $p = $pagos->estadoDeCuenta($im, $apto);
            if ($p['suceed'] == true) {
                $historico[] = Array(
                    "inmueble" => $inmueble['data'][0],
                    "propiedad" => $propiedad,
                    "pagos" => $p['data']
                );
            }
            $r = $propietario->listarPropietariosPorInmueble($im);
            if ($r['suceed'] && count($r['data'])>0) {
                $lista_pro = $r['data'];
            }
        }
        if ($session['usuario']['directorio'] == 'admin') {
            $lista_inm = $inmuebles->listar();
        } else {
            $lista_inm = $inmuebles->listarInmueblesAutorizados($session['usuario']['id']);
        }
        echo $twig->render('intranet/historico-pagos.html.twig', array(
            "session"       => $session,
            "propiedades"   => $propiedad,
            "inmuebles"     => $lista_inm['data'],
            "inm"           => $im,
            "apto"          => $apto,
            "historicos"    => $historico,
            "propietarios"  => $lista_pro
        ));
        break; 
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="cartelera publicar">
    case "cartelera-general":
    case "cartelera-condominio":
    case "cartelera-publica":
    default:
        $lista = null;
        switch ($accion) {
            case "cartelera-general":
                $tabla = "cartelera_general";
                $titulo = "Cartelera General";
                break;
            case "cartelera-publica":
                $tabla = "cartelera_publica";
                $titulo = "Cartelera Publica";
                break;
            default:
                $tabla = "cartelera_inmueble";
                $titulo = "Cartelera Condominio";
                $inmueble = new inmueble();
                $lista_inm = $inmueble->listar();
                $lista = $lista_inm['data'];
                break;
        }
        $cartelera = new cartelera();
        //$id = isset($_GET['id']) ? $_GET['id'] : -1;
        //$publicaciones = $cartelera->listar();
        echo $twig->render('intranet/cartelera.html.twig', array(
            "tabla"     => $tabla,
            "titulo"    => $titulo,
            "inmuebles" => $lista
        ));
        break; 
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="lista cartelera general">
//    case "listar-cartelera-general":
//        $cartelera = new cartelera();
//        $publicaciones = $cartelera->listar();
//        echo $twig->render('intranet/caja/listar-cartelera.general.html.twig', array(
//            "session" => $session,
//            "publicaciones" => $publicaciones['data']
//        ));
//        break;
    // </editor-fold>
}
