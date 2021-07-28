<?php
include_once './includes/constants.php';

$result = array();
$password = '';
$nombre = '';

if (isset($_POST['nombre']) && isset($_POST['password'])) {
    $usuario = new usuario();
    
    $result = $usuario->login($_POST['nombre'],$_POST['password']);
    
    if ($result['suceed']=='true') {
        
        if ($_SESSION['status'] == 'logueado') {
            header("location:".ROOT."intranet/index.php");
        }
        die();
    }
}

echo $twig->render('intranet.html.twig', array("mensaje" => $result,"usuario"=>$nombre,"password"=>$password));