<?php
include_once 'configuracion.php';

date_default_timezone_set("America/La_Paz");

// acceso a la base de datos
define("HOST", "localhost");
define("USER", $user);
define("PASSWORD", $password);
define("DB", $db);
// configuracion ficheros del sistema
define("SISTEMA", $sistema);
define("EMAIL_ERROR", $email_error);
define("EMAIL_CONTACTO", "ynfantes@gmail.com");
define("EMAIL_TITULO", "error");
define("MOSTRAR_ERROR", $mostrar_error);
define("DEBUG", $debug);
define("PRODUCCION", $produccion);
define("TITULO", "Administradora Octagon");
/**
 * para las urls
 */
define("ROOT", 'http://' . $_SERVER['SERVER_NAME'] . SISTEMA);
define("URL_SISTEMA", ROOT . "enlinea");
define("URL_INTRANET", ROOT . "intranet");
/**
 * para los includes
 */
define("SERVER_ROOT", $_SERVER['DOCUMENT_ROOT'] . SISTEMA);

/*set_include_path(SERVER_ROOT . "/site/");*/
define("TEMPLATE", SERVER_ROOT . "/template/");
/****correo electronico****/
define("mailPHP",0);
define("sendMail",1);
define("SMTP",2);
define("PROGRAMA_CORREO",SMTP);

define("NOMBRE_APLICACION","Octagon en Línea");
define("ACTUALIZ","data/");
define("ARCHIVO_INMUEBLE","INMUEBLE.txt");
define("ARCHIVO_CUENTAS","CUENTAS.txt");
define("ARCHIVO_FACTURA","FACTURA.txt");
define("ARCHIVO_FACTURA_DETALLE","FACTURA_DETALLE.txt");
define("ARCHIVO_JUNTA_CONDOMINIO","JUNTA_CONDOMINIO.txt");
define("ARCHIVO_PROPIEDADES","PROPIEDADES.txt");
define("ARCHIVO_PROPIETARIOS","PROPIETARIOS.txt");
define("ARCHIVO_EDO_CTA_INM","EDO_CUENTA_INMUEBLE.txt");
define("ARCHIVO_CUENTAS_DE_FONDO","CUENTAS_FONDO.txt");
define("ARCHIVO_MOVIMIENTOS_DE_FONDO","MOVIMIENTO_FONDO.txt");
define("ARCHIVO_ACTUALIZACION","ACTUALIZACION.txt");
define("SMTP_SERVER",$email_server);
define("PORT",$email_port);
define("USER_MAIL",$email_user);
define("PASS_MAIL",$email_pass);
define("MESES_COBRANZA",2);
define("GRAFICO_FACTURACION",1);
define("GRAFICO_COBRANZA",1);
define("DEMO",0);
define("MOVIMIENTO_FONDO",1);

// twig
include_once dirname(dirname(dirname(__FILE__))).'/framework/twig/lib/Twig/Autoloader.php';
include_once SERVER_ROOT . 'includes/extensiones.php';
Twig_Autoloader::register();

$loader = new Twig_Loader_Filesystem(SERVER_ROOT . 'template');
$twig = new Twig_Environment($loader, array(
            'debug' => true,
            //'cache' => SERVER_ROOT . 'cache',
            'cache' => false,
            "auto_reload" => true)
);
if (isset($_SESSION))
    $twig->addGlobal("session", $_SESSION);

$twig->addExtension(new extensiones());
$twig->addExtension(new Twig_Extension_Debug());

//autoload
spl_autoload_register( function($class) {
    include_once SERVER_ROOT.'/includes/'.$class.'.php';
});

if (isset($_GET['logout']) && $_GET['logout'] == true) {
    $user_logout = new propietario();
    $user_logout->logout();
}