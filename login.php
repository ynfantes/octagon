<?php

include_once 'includes/constants.php';

$result = array();
$password = '';
$apto = '';
$propietario = new propietario(); 
if (isset($_POST['apto']) && isset($_POST['password'])) {
    $apto = $_POST['apto'];
    $password = $_POST['password'];
    $result = $propietario->login($apto,$password, 0);
    if ($result['suceed']=='true') {
        
        if ($_SESSION['status'] == 'logueado') {
            header("location:" . URL_SISTEMA );
        }
        die();
    }
} else {
    if (isset($_POST['email'])) {
        $result = $propietario->recuperarContraSena($_POST['email']);
    }
}
echo $twig->render('index.html.twig', array("mensaje" => $result,"apto"=>$apto,"password"=>$password));