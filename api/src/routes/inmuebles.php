<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

include_once '../../includes/constants.php';

$app->post('/inmuebles/insert', function(Request $req, Response $res) {
    try {
        
        $inmueble = new inmueble();    
        $data = json_decode($req->getBody(),true);
        
        foreach ($data as $index => $inm) {
            unset($inm['fecha_actualizacion']);
            $result = $inmueble->insertarActualizar($inm);
            $data[$index]['suceed'] = $result['suceed'];
            $data[$index]['stats']  = $result['stats'];
        }
        $newRes = $res->withJson($data);
        return $newRes;

    } catch (\Throwable $th) {
        return anError($th, $res);
    }
});


$app->put('/cuentas', function(Request $req, Response $res) {
    try {
        
        $inmueble = new inmueble();
        $data = json_decode($req->getBody(),true);
        $act = $data;
        unset($act['IDCuenta'],$act['id']);
        $result = $inmueble->actualizar($data['id'],$act);
        $data['suceed'] = $result['suceed'];
        $data['stats'] = $result['stats'];
        $newRes = $res->withJson($data);
        return $newRes;

    } catch (\Throwable $th) {
        return anError($th, $res);
    }
});


$app->post('/cuentas', function(Request $req, Response $res) {
    try {
        
        $inmueble = new inmueble();
        $data = json_decode($req->getBody(),true);
        foreach ($data as $index => $cuenta) {
            unset($cuenta['IDCuenta']);
            $result = $inmueble->agregarCuentaInmueble($cuenta);
            $data[$index]['suceed']  = $result['suceed'];
            $data[$index]['stats']   = $result['stats'];
        }
        $newRes = $res->withJson($data);
        return $newRes;

    } catch (\Throwable $th) {
        return anError($th, $res);
    }
});


$app->delete('/cuentas', function(Request $req, Response $res) {
    try {
        
        $inmueble = new inmueble();
        $data = json_decode($req->getBody(),true);
        foreach ($data as $index => $cuenta) {
            $result = $inmueble->borrarCuentaBancaria($cuenta['id_inmueble'],$cuenta['numero_cuenta']);
            $data[$index]['suceed'] = $result['suceed'];
            $data[$index]['stats']  = $result['stats'];
        }
        $newRes = $res->withJson($data);
        return $newRes;

    } catch (\Throwable $th) {
        return anError($th, $res);
    }
});

$app->put('/cobranza/mensual', function(Request $req, Response $res) {
    try {
        
        $inmueble = new inmueble();
        $data = json_decode($req->getBody(),true);
        foreach ($data as $index => $cobranza) {
            $result = $inmueble->insertarCobranzaMensual($cobranza);
            $data[$index]['suceed'] = $result['suceed'];
            $data[$index]['stats']   = $result['stats'];
        }
        $newRes = $res->withJson($data);
        return $newRes;

    } catch (\Throwable $th) {
        return anError($th, $res);
    }
});

$app->put('/facturacion/mensual', function(Request $req, Response $res) {
    try {
        
        $inmueble = new inmueble();
        $data = json_decode($req->getBody(),true);
        foreach ($data as $index => $facturacion) {
            $result = $inmueble->insertarFacturacionMensual($facturacion);
            $data[$index]['suceed'] = $result['suceed'];
            $data[$index]['stats']   = $result['stats'];
        }
        $newRes = $res->withJson($data);
        return $newRes;

    } catch (\Throwable $th) {
        return anError($th, $res);
    }
});

$app->put('/propietarios/grupo', function(Request $req, Response $res) {
    try {
        
        $inmueble = new inmueble();
        $result = json_decode($req->getBody(),true);
        $codinm = '';
        foreach ($result as $item => $prop) {
    
            if ($codinm !== $prop['id_inmueble']) {
                $codinm = $prop['id_inmueble'];
                $condicion = array_filter($prop, function($key) {
                    return $key === 'id_inmueble';
                },ARRAY_FILTER_USE_KEY);
                $r = $inmueble->borrarGrupo($condicion);
            }
    
            $r = $inmueble->insertarGrupo($prop);
            $result[$item]['suceed'] = $r['suceed'];
            $result[$item]['stats'] = $r['stats'];
            if ($r['stats']['error']) $result[$item]['query'] = $r['query'];
            
        }
        $newRes = $res->withJson($result);
        return $newRes;

    } catch (\Throwable $th) {
        return anError($th, $res);
    }

});

$app->post('/propietarios/grupo', function(Request $req, Response $res) {
    try {
        $inmueble = new inmueble();
        $result = json_decode($req->getBody(),true);
        $codinm = '';
        $grupo  = '';
        foreach ($result as $item => $prop) {
    
            $r = $inmueble->insertarGrupoPropietario($prop);
            $result[$item]['suceed'] = $r['suceed'];
            $result[$item]['stats'] = $r['stats'];
            if ($r['stats']['error']) $result[$item]['query'] = $r['query'];
            
        }
        $newRes = $res->withJson($result);
        return $newRes;
    } catch (\Throwable $th) {
        return anError($th, $res);        
    }

});