<?php
include_once '../includes/constants.php';
include_once '../includes/mailto.php';

$correo_destinatario = 'condominiosoctagon1@gmail.com';
$cc = 'respuestalegal6@gmail.com';

switch ($_POST['tipo']) {
    case "1":
        
        // solvencia de condominio
        foreach ($_FILES as $key => $value) {
            $archivo  = $_FILES[$key]['name'];
            $tempFile = $_FILES[$key]['tmp_name'];
            $extension = end(explode(".",$_FILES[$key]['name']));
            $mainFile = $key.'_'.$_POST['apartamento'].'.'.$extension;
            move_uploaded_file($tempFile,"soportes/".$mainFile);
            $archivo = realpath("soportes/".$mainFile);
            if (file_exists($archivo)) {
                $files[] = $archivo;
            }
        }
        $ini = parse_ini_file('../includes/emails.ini');
        
        $mensaje = sprintf($ini['SOLICITUD_SOLVENCIA_CONDOMINIO'],
                $_POST['nombre'],
                $_POST['edificio'],
                $_POST['apartamento'],
                $_POST['email'],
                $_POST['celular']);
        
        $mail = new mailto();
        $r = $mail->enviar_email("Solicitud Solvencia Condominio",
                $mensaje, "", 
                $correo_destinatario,"",
                $files);
                            
        if ($r=="") {
           $html = '<div class="alert alert-success">
                    <a data-dismiss="alert" class="close" href="#">×</a>
                    <strong>Muy Bien!</strong> Hemos recibido su solicitud.
                    </div>';
            echo($html);
        } else {
            $html = '<div class="alert alert-danger">
                    <a data-dismiss="alert" class="close" href="#">×</a>
                    <strong>Ups!</strong> No hemos podido procesr su solicitud.
                    </div>';
            echo($html);
        }
        break;
        
    case "2":
        // solvencia hidrocapital
        foreach ($_FILES as $key => $value) {
            $archivo  = $_FILES[$key]['name'];
            $tempFile = $_FILES[$key]['tmp_name'];
            $extension = end(explode(".",$_FILES[$key]['name']));
            $mainFile = $key.'_'.$_POST['apartamento'].'.'.$extension;
            move_uploaded_file($tempFile,"soportes/".$mainFile);
            $archivo = realpath("soportes/".$mainFile);
            if (file_exists($archivo)) {
                $files[] = $archivo;
            }
        }

        $ini = parse_ini_file('../includes/emails.ini');
        $mensaje = sprintf($ini['SOLICITUD_SOLVENCIA_HIDROCAPITAL'],
                $_POST['nombre'],
                $_POST['edificio'],
                $_POST['apartamento'],
                $_POST['email'],
                $_POST['celular']);
        
        $mail = new mailto();
        $r = $mail->enviar_email("Solicitud Solvencia Hidrocapital",
                $mensaje, "", 
                $correo_destinatario,"",
                $files);
                            
        if ($r=="") {
            $html = '<div class="alert alert-success">
                    <a data-dismiss="alert" class="close" href="#">×</a>
                    <strong>Bien!</strong>Hemos recibido su solicitud.
                    </div>';
            echo($html);
        } else {
            $html = '<div class="alert alert-danger">
                    <a data-dismiss="alert" class="close" href="#">×</a>
                    <strong>Ups!</strong> No hemos podido procesr su solicitud.
                    </div>';
            echo($html);
        }
    default:
        // ninguna seleccion
        
        break;
}