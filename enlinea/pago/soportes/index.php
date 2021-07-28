<?php
die('emtrp');
//include_once '../../includes/constants.php';
$path= getcwd();
$dir = opendir($path);
$factura = new factura();
$e = 0;
$n = 0;
// Leo todos los ficheros de la carpeta
while ($elemento = readdir($dir)){
    // Tratamos los elementos . y .. que tienen todas las carpetas
    if( $elemento != "." && $elemento != ".." && $elemento != "mantenimiento.php" && $elemento != "index.php"){
        // Si es una carpeta
        if(!is_dir($path.$elemento) ){
            echo $elemento.' '.date('M-d-Y', filectime($elemento)).'\n';
//            if ($r==0) {
//                unlink($path."/".$elemento);
//                $n++;
//            } else {
//                $e++;
//            }
        }
    }
}