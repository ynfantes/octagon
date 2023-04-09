
<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of administrador
 *
 * @author Edgar
 */
class administrador extends db implements crud {
    const tabla = "administrador";

    public function actualizar($id, $data) {
        
        return db::update(self::tabla, $data, Array("id"=>$id));
    }

    public function borrar($id) {
        return db::delete(self::tabla, Array("id"=>$id));
    }

    public function borrarTodo() {
        return db::delete(self::tabla);
    }

    public function insertar($data) {
        return db::insert(self::tabla,$data);
    }

    public function listar() {
        return db::select("*", self::tabla);
    }

    public function ver($id) {
        return db::select("*",self::tabla,Array("id"=>$id));
    }
    
    public function cambioDeClave($id,$password) {
        return db::update(self::tabla, Array("password"=>$password),Array("id"=>$id));
    }
    
    public function login($usuario,$password) {
        
        if ($usuario!="" && $password!="") {
            
            $result = db::select("*",self::tabla,Array("usuario"=>$usuario,"password"=>$password));
            
            if ($result['suceed'] == "true" && count($result['data']) > 0) {
                session_start();
                $_SESSION['usuario'] = $result['data'];
                $_SESSION['status'] = 'logueado';
                
                header("location:".URL_INMOBILIARIA."/accion=publicaciones");
                
            } else {
                $result['error'] = "Nombre usuario y/o password incorrecto.";
            }
            
        } else {
            $result['suceed'] = false;
            $result['error'] = "Nombre usuario y/o password requerído.";
            
        }
        return $result;
    }
}
