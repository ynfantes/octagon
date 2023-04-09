<?php
include_once './includes/constants.php';
$context = [
    'tabla'     => 'cartelera_publica',
    'titulo'    => 'Cartelera Pública',
    'inmuebles' => null
];
echo $twig->render('intranet.html.twig', $context);