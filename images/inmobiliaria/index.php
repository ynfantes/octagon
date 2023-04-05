<?php

if (!empty($_FILES)) {

 $tempFile = $_FILES['file']['tmp_name'];
 // using DIRECTORY_SEPARATOR constant is a good practice, it makes your code portable.
 //$targetPath = dirname( __FILE__ ) . DIRECTORY_SEPARATOR ;
 // Adding timestamp with image's name so that files with same name can be uploaded easily.
 $mainFile = $_FILES['file']['name'];

 move_uploaded_file($tempFile,$mainFile);

} else {
    if (isset($_GET['id']) && $_GET['id']<>"") {
        include '../../includes/constants.php';
        $publicaciones = new publicaciones();
        $publicacion = $publicaciones->ver($_GET['id']);
        
        $files[] = basename($publicacion['data'][0]['imagen']);
        
        $imagenes = $publicaciones->obtenerImagenesPorPublicacion($_GET['id']);
        
        if ($imagenes['suceed']) {
            foreach ($imagenes['data'] as $imagen) {
                $files[] = basename($imagen['imagen']);
            }
        }
        $result  = [];
        $path = getcwd();

        if ( false!==$files ) {
            foreach ( $files as $file ) {
                if ( '.'!=$file && '..'!=$file && file_exists($path.'/'.$file)) {       //2
                    $obj['name'] = $file;
                    $obj['size'] = filesize($file);
                    $result[] = $obj;
                }
            }
        }
        
        header('Content-type: text/json');              //3
        header('Content-type: application/json');
        echo json_encode($result);
    }
}