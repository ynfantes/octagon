<?php
include_once '../../includes/constants.php';
include_once '../../includes/file.php';


$propietario = new propietario();

$propietario->esPropietarioLogueado();

$inmuebles = new inmueble();
$propiedad = new propiedades();
$bitacora = new bitacora();
$facturas = new factura();

$accion = isset($_GET['accion']) ? $_GET['accion'] : "listar";
$session = $_SESSION;

$cuenta = Array();
$factura = null;
$cobro = null;
$monto = 0;
$total = 0;
$promedio_facturacion = 0;

$monto_c = 0;
$total_c = 0;
$promedio_cobranza = 0;
$i = 0;
$n = 0;
$direccion_facturacion = "right";
$direccion_cobranza = "right";

$propiedades = $propiedad->propiedadesPropietario($_SESSION['usuario']['cedula']);

if ($propiedades['suceed']) {
    
    $facturacion = $inmuebles->movimientoFacturacionMensual($propiedades['data'][0]['id_inmueble']);
    $cobranza = $inmuebles->movimientoCobranzaMensual($propiedades['data'][0]['id_inmueble']);
    
    if ($facturacion['suceed']) {

        foreach ($facturacion['data'] as $r) {
            $i++;
            $direccion_facturacion = $r['facturado'] > $monto ? "up" : "down";
            $monto = $r['facturado'];
            $total += $monto;
            $factura .= $factura != '' ? ',' : '';
            $factura .= (int) $r['facturado'];
        }
        if($i>0) { $promedio_facturacion = (int) ($total / $i); };
    }
    
    if ($cobranza['suceed']) {
        
        foreach ($cobranza['data'] as $c) {
            $n++;
            $direccion_cobranza = $c['monto'] > $monto_c ? "up" : "down";
            $monto_c = $c['monto'];
            $total_c += $monto_c;
            $cobro .= $cobro != '' ? ',' : '';
            $cobro .= (int)$c['monto'];
        }
        if ($n>0) {$promedio_cobranza = (int)($total_c / $n);}
        
    }
    
}

switch ($accion) {
    
    case "junta-condominio": default :
        $junta_condominio = new junta_condominio();
        $propiedades = $propiedad->propiedadesPropietario($_SESSION['usuario']['cedula']);
        
        $miembros = Array();

        if ($propiedades['suceed'] == true) {
            $id_inmueble = "";
            foreach ($propiedades['data'] as $propiedad) {
                
                $bitacora->insertar(Array(
                    "id_sesion"=>$session['id_sesion'],
                    "id_accion"=> 4,
                    "descripcion"=>$propiedad['id_inmueble']." - ".$propiedad['apto'],
                ));
                
                if ($propiedad['id_inmueble'] != $id_inmueble) {
                    $id_inmueble = $propiedad['id_inmueble'];
                    $inmueble = $inmuebles->ver($id_inmueble);
                    $junta = $junta_condominio->listarJuntaPorInmueble($id_inmueble);
                    
                        if (count($junta['data'])>0) {
                        $miembros[] = Array("inmueble" => $inmueble['data'][0],
                            "miembros" => $junta['data']);
                        }
                }
            }
        }
        
        echo $twig->render('enlinea/inmueble/formulario.html.twig', array(
            'session'                => $session,
            'junta'                  => $miembros,
            'movimiento_facturacion' => $factura,
            'promedio_facturacion'   => $promedio_facturacion,
            'direccion_facturacion'  => $direccion_facturacion,
            'movimiento_cobranza'    => $cobro,
            'promedio_cobranza'      => $promedio_cobranza,
            'direccion_cobranza'     => $direccion_cobranza
        ));
        
        break; 

    case "cuenta":
        $propiedades = $propiedad->propiedadesPropietario($_SESSION['usuario']['cedula']);
        $id_inmueble = "";
        if ($propiedades['suceed'] == true) {
            $cuentas = Array();
            
            foreach ($propiedades['data'] as $propiedad) {
                
                $bitacora->insertar(Array(
                    "id_sesion"=>$session['id_sesion'],
                    "id_accion"=>6,
                    "descripcion"=>$propiedad['id_inmueble']." - ".$propiedad['apto'],
                ));
                $legal = false;
                if ($id_inmueble!=$propiedad['id_inmueble']) {
                    if ($propiedad['meses_pendiente'] > MESES_COBRANZA) {
                        $legal = true;
                    }
                    $id_inmueble=$propiedad['id_inmueble'];
                    $inm = $inmuebles->ver($propiedad['id_inmueble']);
                    $cuenta = $inmuebles->estadoDeCuenta($propiedad['id_inmueble']);
                    $cuentas[] = Array("inmueble" => $inm['data'][0], "cuenta" => $cuenta['data'],"legal"=>$legal);
                    
                }
            }
        }


        echo $twig->render('enlinea/inmueble/estado-de-cuenta.html.twig', array("session" => $session,
            'cuentas'                => $cuentas,
            'movimiento_facturacion' => $factura,
            'promedio_facturacion'   => $promedio_facturacion,
            'direccion_facturacion'  => $direccion_facturacion,
            'movimiento_cobranza'    => $cobro,
            'promedio_cobranza'      => $promedio_cobranza,
            'direccion_cobranza'     => $direccion_cobranza
        ));
        break; 

    case "facturacionflot":
        
        $propiedades = $propiedad->inmueblePorPropietario($_SESSION['usuario']['cedula']);
        
        if ($propiedades['suceed']) {
            
                $facturacion = $inmuebles->movimientoFacturacionMensual($_GET['id']);
                $rows = array();
                $table = array();
                if ($facturacion['suceed'] && count($facturacion['data'])>0) {
                    $table['label'] = $facturacion['data'][0]['nombre_inmueble'];
                    $i = 0;
                    foreach ($facturacion['data'] as $r) {
                        $i++;
                        $rows[] = array((string) Misc::date_periodo_format($r['periodo']), $r['facturado'] / 1000);
                    }
                    $table['data'] = $rows;
                }
                $jsonTable = json_encode($table);

                echo $jsonTable;
            
        }
        break; 
    
    case "cobranzaflot":
        $propiedades = $propiedad->inmueblePorPropietario($_SESSION['usuario']['cedula']);
        if ($propiedades['suceed']) {

            $cobranza = $inmuebles->movimientoCobranzaMensual($_GET['id']);
                $rows = array();
                $table = array();
                $i = 1;
                if ($cobranza['suceed'] && count($cobranza['data'])>0) {
                    $table['label'] = $cobranza['data'][0]['nombre_inmueble'];
                    foreach ($cobranza['data'] as $r) {
                        $i++;
                        $rows[] = array((string) Misc::date_periodo_format($r['periodo']), $r['monto'] / 1000);
                    }
                    $table['data'] = $rows;
                }
                $jsonTable = json_encode($table);
                echo $jsonTable;

        }
        break; 

    case "cartelera":
        $cartelera = new cartelera();
        $propiedad = new propiedades();
        $inmuebles = new inmueble();

        $fecha_actualizacion = null;
        
        $bitacora->insertar(Array(

            'id_sesion'     => $session['id_sesion'],
            'id_accion'     => 11,
            'descripcion'   => $fecha_actualizacion

        ));
        $cartelera->tabla="cartelera_general";
        $cartelera_general = $cartelera->listar();
        $propiedades = $propiedad->propiedadesPropietario($_SESSION['usuario']['cedula']);
        $inmueble = $inmuebles->ver($propiedades['data'][0]['id_inmueble']);

        if($inmueble['suceed'] && count($inmueble['data'])>0) {
            $fecha_actualizacion = $inmueble['data'][0]['fecha_actualizacion'];
        }

        if ($propiedades['suceed'] == true && count($propiedades['data'])>0) {
            $cartelera_inmueble = Array();
            $cartelera->tabla="cartelera_inmueble";
            

            foreach ($propiedades['data'] as $propiedad) {
                
                $resultado = $cartelera->listarCarteleraInmueble($propiedad['id_inmueble']);
                array_push($cartelera_inmueble, $resultado['data']);
                $inm = $propiedad['id_inmueble'];
                
            }
        }
        echo $twig->render('enlinea/inmueble/cartelera.html.twig', array(
            'session'                 => $session,
            'propiedades'             => $propiedades['data'],
            'fecha_actualizacion'     => $fecha_actualizacion,
            'movimiento_facturacion'  => $factura,
            'promedio_facturacion'    => $promedio_facturacion,
            'direccion_facturacion'   => $direccion_facturacion,
            'movimiento_cobranza'     => $cobro,
            'promedio_cobranza'       => $promedio_cobranza,
            'direccion_cobranza'      => $direccion_cobranza,
            'inmuebles'               => $propiedades['data'],
            'cartelera_general'       => $cartelera_general['data'],
            'cartelera_inmueble'      => $cartelera_inmueble,
            'inm'                     => $inm
        ));
        break; 
    
    case "listarCuentasDeFondo":
        $fondo = new fondo();
        $fondos = array();
        $m = array();
        $inmueble = new inmueble();
        
        $propiedades = $inmueble->listarInmueblesPorPropietario($_SESSION['usuario']['cedula']);

        if ($propiedades['suceed'] && count($propiedades['data']) > 0) {
            $id_inmueble = $propiedades['data'][0]['id_inmueble'];
            $r = $fondo->listarCuentasDeFondoInmueble($id_inmueble);
            
            if ($r['suceed'] && count($r['data']) > 0) {
                $fondos = $r['data'];
                $codigo_gasto = isset($_GET['id']) ? $_GET['id']:$fondos[0]['codigo_gasto'];
                $cuenta_fondo = $fondo->obtenerIdCuentaFondo($id_inmueble, $codigo_gasto);
                
                if ($cuenta_fondo['suceed'] && count($cuenta_fondo['data'])>0) {
                    $cuenta = $cuenta_fondo['data'][0];
                }
                $movimientos = $fondo->consultaEstadoDeCuentaFondo($id_inmueble, $codigo_gasto);
                if ($movimientos['suceed'] && count($movimientos['data'])>0) {
                    $m = $movimientos['data'];
                }
            }
        }
        
        echo $twig->render('enlinea/inmueble/fondos.html.twig', array(
            'session'     => $session,
            'propiedades' => $propiedades['data'],
            'id_inmueble' => $id_inmueble,
            'fondos'      => $fondos,
            'movimientos' => $m,
            'cuenta'      => $cuenta
        ));
        break; 
    
    case "imprimircuenta":
        break;
}
