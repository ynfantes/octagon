<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

include_once '../../includes/constants.php';



$app->post('/facturas/insert', function(Request $req, Response $res) {
    try {
        
        $factura = new factura();    
        $data = json_decode($req->getBody(),true);
    
        foreach ($data as $index => $fact) {
            unset($fact['id'],$fact['fidea']);
            $result = $factura->insertarActualizar($fact);
            $data[$index]['suceed'] = $result['suceed'];
        }
        $newRes = $res->withJson($data);
        return $newRes;

    } catch (\Throwable $th) {
        return anError($th, $res);
    }
    
});

$app->delete('/facturas/delete', function(Request $req, Response $res) {
    try {
        
        $factura = new factura();
        $data = json_decode($req->getBody(),true);
        
        foreach ($data as $index => $fact) {
            unset($fact['id']);
            $result = $factura->borrarFactura($fact);
            $data[$index]['suceed'] = $result['suceed'];
        }
        $newRes = $res->withJson($data);
        return $newRes;

    } catch (\Throwable $th) {
        return anError($th, $res);
    }

});

$app->post('/avisos/strToPDF', function(Request $req, Response $res) {

    try {
        
        $data = json_decode($req->getBody(),true);
        //Decode pdf content
        $pdf_decoded = base64_decode($data['base64']);
        //Write data back to pdf file
        $pdf = fopen('../../enlinea/avisos/'.$data['filename'],'w');
        $data['suceed'] = fwrite($pdf,$pdf_decoded);
        unset($data['base64']);
        //close output file
        fclose($pdf);
        $newRes = $res->withJson($data);
        return $newRes;

    } catch (\Throwable $th) {
        return anError($th, $res);
    }

});