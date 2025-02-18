<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

$config = ["Settings" => [
    "displayErrorDetails" => true
]];

$app = new \Slim\App;

function anError($th, $res) {
    $data = ['status'=>'error', 'message'=>$th->getMessage()];
    $newResponse = $res->withJson($data, 500);
    return $newResponse;
}

$app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name");
    
    return $response;
});

$app->get('/status', function (Request $request, Response $response) {
    
    try {
        
        $data = ['status'=>'OK'];
        $newResponse = $response->withJson($data);
        return $newResponse;

    } catch (\Throwable $th) {
        //throw $th;
        return anError($th,$response);
    }
    
});

$app->post('/copyUpdateFile', function(Request $req, Response $res) {

    try {
        
        $data = json_decode($req->getBody(),true);
        //Decode pdf content
        $file_decoded = base64_decode($data['base64']);
        //Write data back to pdf file
        $file = fopen('../../data/'.$data['filename'],'w');
        $data['suceed'] = fwrite($file,$file_decoded);
        $data['content'] = $file_decoded;
        unset($data['base64']);
        //close output file
        fclose($file);
        $newRes = $res->withJson($data);
        return $newRes;

    } catch (\Throwable $th) {
        return anError($th, $res);
    }
    
});
require_once '../src/routes/routes.php';

$app->run();