<?php

function redimensionarImagenWeb($source, $destination, $maxWidth = 1600, $maxHeight = 1200, $quality = 80, $fixedHeight = null, $fixedWidth = null) {
    if (!file_exists($source)) {
        return false;
    }

    $info = getimagesize($source);
    if ($info === false) {
        return false;
    }

    list($width, $height, $type) = $info;
    $ratio = min($maxWidth / $width, $maxHeight / $height, 1);
    $newWidth = max(1, (int) round($width * $ratio));
    $newHeight = max(1, (int) round($height * $ratio));

    if ($fixedHeight !== null && $fixedWidth !== null) {
        $newWidth = $fixedWidth;
        $newHeight = $fixedHeight;
    }

    switch ($type) {
        case IMAGETYPE_JPEG:
            $image = imagecreatefromjpeg($source);
            break;
        case IMAGETYPE_PNG:
            $image = imagecreatefrompng($source);
            break;
        case IMAGETYPE_WEBP:
            $image = imagecreatefromwebp($source);
            break;
        default:
            return false;
    }

    if ($image === false) {
        return false;
    }

    $thumb = imagecreatetruecolor($newWidth, $newHeight);

    if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP) {
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
        $transparent = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
        imagefilledrectangle($thumb, 0, 0, $newWidth, $newHeight, $transparent);
    }

    if ($fixedHeight !== null && $fixedWidth !== null) {
        $cropWidth = $width;
        $cropHeight = $height;
        $cropX = 0;
        $cropY = 0;

        if ($width / $height > $fixedWidth / $fixedHeight) {
            $cropWidth = (int) round($height * ($fixedWidth / $fixedHeight));
            $cropX = (int) round(($width - $cropWidth) / 2);
        } else {
            $cropHeight = (int) round($width * ($fixedHeight / $fixedWidth));
            $cropY = (int) round(($height - $cropHeight) / 2);
        }

        imagecopyresampled($thumb, $image, 0, 0, $cropX, $cropY, $newWidth, $newHeight, $cropWidth, $cropHeight);
    } else {
        imagecopyresampled($thumb, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    }

    $extension = strtolower(pathinfo($destination, PATHINFO_EXTENSION));

    if ($extension === 'jpg' || $extension === 'jpeg') {
        imagejpeg($thumb, $destination, $quality);
    } elseif ($extension === 'png') {
        imagepng($thumb, $destination, 9);
    } elseif ($extension === 'webp') {
        imagewebp($thumb, $destination, $quality);
    } else {
        imagedestroy($image);
        imagedestroy($thumb);
        return false;
    }

    imagedestroy($image);
    imagedestroy($thumb);
    return true;
}

if (!empty($_FILES)) {
    if (!isset($_FILES['file']) || !is_array($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'La imagen no se recibió correctamente.'
        ]);
        exit;
    }

    $tempFile = $_FILES['file']['tmp_name'];
    $originalName = $_FILES['file']['name'];
    $cleanBaseName = preg_replace('/[^a-zA-Z0-9._-]/', '', basename(str_replace(',', '', $originalName)));
    $safeName = time() . '_' . $cleanBaseName;
    $destination = __DIR__ . DIRECTORY_SEPARATOR . $safeName;

    if (!is_dir(__DIR__) || !is_writable(__DIR__)) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'La carpeta de imágenes no tiene permisos de escritura.'
        ]);
        exit;
    }

    if (!move_uploaded_file($tempFile, $destination)) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'No se pudo guardar la imagen en el servidor.'
        ]);
        exit;
    }

    $mime = mime_content_type($destination);
    if ($mime === false || strpos($mime, 'image/') === false) {
        @unlink($destination);
        http_response_code(415);
        echo json_encode([
            'success' => false,
            'error' => 'El archivo no es una imagen válida.'
        ]);
        exit;
    }
    
    $extension = strtolower(pathinfo($destination, PATHINFO_EXTENSION));

    if ($extension === 'png' || $extension === 'webp') {
        $jpgDestino = str_replace('.' . $extension, '.jpg', $destination);
        $img = imagecreatefromstring(file_get_contents($destination));
        if ($img !== false) {
            imagealphablending($img, false);
            imagesavealpha($img, true);
            $bg = imagecreatetruecolor(imagesx($img), imagesy($img));
            $white = imagecolorallocate($bg, 255, 255, 255);
            imagefill($bg, 0, 0, $white);
            imagealphablending($bg, true);
            imagecopy($bg, $img, 0, 0, 0, 0, imagesx($img), imagesy($img));
            imagejpeg($bg, $jpgDestino, 80);
            imagedestroy($img);
            imagedestroy($bg);
            @unlink($destination);
            $destination = $jpgDestino;
            $safeName = basename($jpgDestino);
        }
    }

    if (file_exists($destination)) {
        redimensionarImagenWeb($destination, $destination, 1200, 900, 80, 800, 1200);
    }

    echo json_encode([
        'success' => true,
        'file' => $safeName
    ]);
    exit;
} else {
    if (isset($_GET['id']) && $_GET['id'] <> "") {
        include '../../includes/constants.php';
        $publicaciones = new publicaciones();
        $publicacion = $publicaciones->ver($_GET['id']);
        
        if ($publicacion['suceed'] === false || count($publicacion['data']) === 0) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'Publicación no encontrada.'
            ]);
            exit;
        }
        if ($publicacion['row']['imagen']!== "" && $publicacion['row']['imagen']!== null) {
            $files[] = basename($publicacion['row']['imagen']);
        }
        
        $imagenes = $publicaciones->obtenerImagenesPorPublicacion($_GET['id']);

        if ($imagenes['suceed']) {
            foreach ($imagenes['data'] as $imagen) {
                $files[] = basename($imagen['imagen']);
            }
        }
        $result  = [];
        $path = getcwd();

        if (false !== $files) {
            foreach ($files as $file) {
                if ('.' != $file && '..' != $file && file_exists($path . '/' . $file)) {
                    $obj['name'] = $file;
                    $obj['size'] = filesize($file);
                    $result[] = $obj;
                }
            }
        }

        header('Content-type: text/json');
        header('Content-type: application/json');
        echo json_encode($result);
    }
}