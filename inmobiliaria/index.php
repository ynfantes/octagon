<?php
include_once '../includes/constants.php';
include_once '../includes/usuario.php';

$accion     = isset($_GET['accion']) ? $_GET['accion']:"";
// acciones donde no se debe validar si el usuario está registrado
$haystack   = ["","ver-propiedad","registro"];
if(!in_array($accion,$haystack)) {
    usuario::esUsuarioLogueado('inmobiliaria');
}

$publicaciones = new publicaciones();

function getContext() {
    $db = new db();
    $estados_act = '21,24,14';
    
    $estados    = $db->dame_query("select * from estados where id_estado in ($estados_act) order by estado");
    $ciudades   = $db->dame_query("select * from ciudades where id_estado in ($estados_act) order by ciudad");
    $monedas    = $db->dame_query("select * from monedas order by descripcion");
    $operacion  = $db->dame_query("select * from operaciones order by descripcion");
    $tipo       = $db->dame_query("select * from inmobiliaria_tipo order by descripcion");
    
    return [
        'ciudades'  => $ciudades['data'],
        'estados'   => $estados['data'],
        'monedas'   => $monedas['data'],
        'operacion' => $operacion['data'],
        'tipo'      => $tipo['data'],
        'session'   => isset($_SESSION) ? $_SESSION: ''
    ];
}

switch ($accion) {
    
    case "lista-ciudades-por-estado":
        $db = new db();
        $ciudades = [];

        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            $result = $db->dame_query("select * from ciudades where id_estado=$id");
            if ($result['suceed']) {
                $ciudades = $result['data'];
            }
        }
        echo json_encode($ciudades);
        break;

    case "login":
        $administrador = Array();
        $login = new administrador();
        if (isset($_POST['usuario']) && isset($_POST['password'])) {
            $administrador = $login->login($_POST['usuario'], $_POST['password']);
        }
        echo $twig->render('inmobiliaria/index.html.twig', Array("administrador" => $administrador));
        break; 

    case "inactivar":
        if (isset($_GET['id'])) {
            $result = $publicaciones->actualizar($_GET['id'], Array("inactivo" => 1));
            if ($result['suceed']) {
                $result['mensaje'] = "(".$result['stats']['affected_rows'].") Publicación inactivada) con éxito.";
            } else {
                $result['mensaje'] = "Ups! ocurrió un error al tratar de inactivar la publicación.";
            }
            
        }
        $listado = $publicaciones->obtenerPublicaciones();
        echo $twig->render('inmobiliaria/lista-publicaciones.html.twig', Array(
            "listado" => $listado['data'],"resultado"=>$result
        ));
        break;
     
    case "publicaciones":
        $name                = 'inmobiliaria/lista-publicaciones.html.twig';
        $list                = $publicaciones->obtenerPublicaciones();
        $num_rows            = $list['stats']['affected_rows'];
        $rows_per_page       =  defined('ROWS_PER_PAGE_LIST_PUB') ? ROWS_PER_PAGE_LIST_PUB : 2;
        $total_page          = ceil($num_rows / $rows_per_page);
        $current_page        = isset($_GET['page']) ? $_GET['page'] : 1;
        $start               = $rows_per_page * ($current_page - 1);
        $limit               = $rows_per_page;
        $data                = [ 'start' => $start, 'limit' => $limit ];
        $list                = $publicaciones->obtenerPublicaciones($data);
        $listado['data']     = $list['suceed'] ? $list['data'] : [];
        $listado['rows']     = $list['stats']['affected_rows'];
        $listado['start']    = $start + 1;
        $listado['end']      = $start + $listado['rows'];
        $listado['page']     = $current_page;
        $listado['num_rows'] = $num_rows;
        $listado['pages']    = $total_page;

        $context = [
            "listado" => $listado,
            "session" => $_SESSION,
        ];

        echo $twig->render($name, $context);
        break; 

    case "publicar":
        $context = getContext();
        if (isset($_GET['id'])) {
            $propiedad = $publicaciones->ver($_GET['id']);
            if ($propiedad['suceed'] && $propiedad['stats']['affected_rows']>0) {
                $context['propiedad'] = $propiedad['row'];
            }
        }
        echo $twig->render('inmobiliaria/registrar-propiedad.html.twig', $context);
        break; 

    case "guardar":
        $db     = new db();
        $dir    = "images/inmobiliaria/";
        $result = [];
        $propiedad = [];
        $data   = $_POST;
        
        if(!empty($_FILES)){
            echo 'vienen files';
            $route = $dir . basename($FILES['file']['name']);
            die($route);
            move_uploaded_file($_FILES['file']['tmp'],$route);
        }
        
        if (isset($data['imagen'])) {
            $imagenes = $data['imagen'];
            unset($data['imagen']);
        }
        // editar
        if (isset($data['id']) && $data['id'] > 0) {

            $id = $data['id'];
            unset($data['id']);
            $result = $publicaciones->actualizar($id, $data);
            if ($result['suceed']) {
                $result['mensaje'] = 'Publicación actualizada con éxito!';
            } else {
                $result['mensaje'] = 'Ups! Ocurrió un error durante el proceso. No se puedo actualizar la información';
            }
            $db->delete("inmobiliaria_img", ["id_inmobiliaria_publicacion" => $id]);

        } else {
            // guardar
            $result = $publicaciones->insertar($data);
            if ($result['suceed']) {
                $id = $result['insert_id'];
                $result['mensaje'] = 'Publicación guardada con éxito';
            } else {
                $result['mensaje'] = 'Ups! Ocurrió un error durante el proceso. No se puedo guardar la información';
            }
        }
        
        if ($result['suceed']) {
            
            if (isset($imagenes)) {
                $i = 0;
                
                foreach ($imagenes as $imagen) {
                    if ($i == 0) {
                        
                        $datos = ["imagen" => $dir.$imagen];
                        $res_img = $publicaciones->actualizar($id, $datos);

                    } else {
                        
                        $datos = [
                            "id_inmobiliaria_publicacion" => $id, 
                            "imagen"                      => $dir.$imagen
                        ];
                        $db->insert("inmobiliaria_img", $datos);
                    }
                    $i++;
                }
                $propiedad = $publicaciones->ver($id);
            }
        }
    
        $context = getContext();
        $context['resultado'] = $result;
        $context['propiedad'] = $propiedad;
        echo json_encode($context);
        break; 
    
    case "editar":
        $db = new db();
        
        $municipios = $db->dame_query("select * from municipios where id > 1 order by descripcion ");
        $tipo       = $db->dame_query("select * from inmobiliaria_tipo where id > 1 order by descripcion");
        $operacion  = $db->dame_query("select * from operaciones where id > 1 order by descripcion");
        $propiedad  = $publicaciones->ver($_GET['id']);

        if ($propiedad['suceed'] && count($propiedad['data']) > 0) {
            for ($index = 0; $index < count($propiedad['data']); $index++) {
                $imagenes = $publicaciones->obtenerImagenesPorPublicacion($propiedad['data'][$index]['id']);
                $propiedad['data'][$index]['imagenes'] = $imagenes['data'];
            }
            $publicaciones->actualizar($_GET['id'], ["visto" => $propiedad['data'][0]['visto'] + 1]);
        }
        $opciones = [
            'municipios'  => $municipios['data'],
            'tipo'        => $tipo['data'],
            'operacion'   => $operacion['data'],
            'propiedad'   => $propiedad['data'],
            'accion'      => "editar"
        ];
        echo $twig->render('inmobiliaria/registrar-propiedad.html.twig', $opciones);
        break; 

    case "salir":
    
        $user_logout = new usuario();
        $user_logout->logout('inmobiliaria');
        break;
    case "configuracion":
        $db     = new db();
        $tabla  = "inmobiliaria_configuracion";

        if ($_POST) {
            $data = $_POST;
            if (isset($data['id']) && $data['id']<>"") {
                $id  = $data['id'];
                unset($data['id']);
                $res = $db->update($tabla,$data,['id' => $id]);
                $res['id'] = $id;
            } else {
                $res = $db->insert($tabla,$data);
            }
            $res['mensaje'] = $res['suceed'] ? 'Configuación actualizada con éxito': 'No se ha podido actualizar la configuración. Intente nuevamente';
            unset($res['query']);
            echo json_encode($res);

        } else {
            $name = 'inmobiliaria/configuracion.html.twig';
            $data = $db->select("*", $tabla);
            $conf = $data['suceed'] ? $data['row']:[];
            $context = [
                'config'  => $conf,
                'session' => $_SESSION,
            ];
            echo $twig->render($name, $context);
        }
        break;
    case "ver-propiedad":
        $db = new db();
        $propiedad = $publicaciones->ver($_GET['id']);
        
        if ($propiedad['suceed'] && !empty($propiedad['row'])) {
            
            $imagenes = $publicaciones->obtenerImagenesPorPublicacion($propiedad['row']['id']);
            $propiedad['row']['imagenes'] = $imagenes['data'];
            $publicaciones->actualizar($_GET['id'], ["visto" => $propiedad['row']['visto'] + 1]);
        }
        $res = $db->select('*','inmobiliaria_configuracion');
        $config = $res['suceed'] ? $res['row'] : [];
        $context = [
            'propiedad' => $propiedad['row'], 
            'config'    => $config,
        ];
        
        echo $twig->render('inmobiliaria/propiedad.html.twig', $context);
        break; 

    case "registro":
        if ($_POST) {
            $user = new usuario();
            $nombre = $_POST['nombre'];
            $result = $user->ver([ 'nombre' => "'".$nombre."'" ]);
            
            if ($result['suceed'] && $result['row']==[]) {
                
                $result = $user->insertar($_POST);
                if ($result['suceed']) {
                    $result['mensaje'] = 'Usuario registrado con éxito.<br>Ya puede inciciar sesión.';
                } else {
                    $result['mensaje'] = 'Ha ocurrido un error durante el proceso.<br>No se ha podido registrar el usuario. Inténtelo nuevamente.';
                }
                
            } else {
                $result['mensaje'] = "Ya existe un usuario registrado con el nombre <strong>$nombre</strong>";
                $result['suceed']  = false;
                $result['exists']  = true;
            }
            unset($result['query'], $result['row'], $result['data']);
            
            echo json_encode($result);
        } else {

            $context = [];
            echo $twig->render('inmobiliaria/registro.html.twig', $context);
        }
        break;
    default :
        $name    = 'inmobiliaria/index.html.twig';
        $context = getContext();
        $data    = $_GET;
        foreach ($data as $key => $value) {
            if ($value=="") unset($data[$key]);
        }
        
        $list    = $publicaciones->obtenerPublicaciones($data);
        
        $listado = $list['suceed'] ? $list['data'] : [];
        $context["publicaciones"] = $listado;
        $context["filter"] = $data;
        echo $twig->render($name, $context);

        break;
}
        


