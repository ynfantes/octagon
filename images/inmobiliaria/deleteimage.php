<?php
$resp = [];
if (isset($_GET['name'])) {
    
    $resp['image'] = $_GET['name'];

    $path= getcwd();
    
    if(file_exists($path."/".$resp['image'])) {
        unlink($path."/".$resp['image']);
        $resp['message'] = 'Imagen Eliminada con éxito!';
    } else {
        $resp['message'] = 'No existe la imagen en el servidor';
    }

} else {
    $resp['message'] = 'No se recibió el nombre de la imagen a eliminar';
};
echo json_encode($resp);