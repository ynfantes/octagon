<?php
include_once './includes/constants.php';

$result = [];
$password = '';
$nombre = '';
$user = 'intranet';

if (isset($_POST['nombre']) && isset($_POST['password']) && isset($_POST['user'])) {
    
    $usuario = new usuario();
    $user    = $_POST['user'];
    $result  = $usuario->login($_POST['nombre'],$_POST['password']);
    
    if ($result['suceed']=='true') {
        
        if ($_SESSION['status'] == 'logueado') {
            header("location:".ROOT."$user/index.php");
        }
        die();
    }
}
$options = [
    'mensaje'  => $result,
    'usuario'  => $nombre,
    'password' => $password
];
echo $twig->render('intranet.html.twig', $options);