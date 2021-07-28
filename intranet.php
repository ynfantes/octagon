<?php
include_once './includes/constants.php';

echo $twig->render('intranet.html.twig', array(
            "tabla"     => 'cartelera_publica',
            "titulo"    => 'Cartelera Pública',
            "inmuebles" => null));