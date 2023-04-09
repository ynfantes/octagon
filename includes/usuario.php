<?php
class usuario extends db implements crud  {
    
    const tabla = "usuarios";
    
    public function actualizar($id, $data) {
        
    }

    public function borrar($id) {
        
    }

    public function borrarTodo() {
        
    }

    public function insertar($data) {
        return db::insert(self::tabla ,$data);
    }

    public function listar() {
        
    }

    public function ver($condicion) {
        return db::select("*",self::tabla,$condicion);  
    }   
    
    public function login($usuario,$password,$user='intrantet') {
        if ($usuario!="" && $password!="") {
            $condicion = ["nombre"=>"'".$usuario."'"];
            $result = db::select("*",self::tabla,$condicion);
            
            if ($result['suceed'] && count($result['data']) > 0) {
                
                if ($result['data'][0]['clave'] === $password) {
                    
                    session_start();
                    $_SESSION['usuario'] = $result['data'][0];
                    $_SESSION['status'] = 'logueado';
                    if ($user == 'intrantet') {
                        header("location:".URL_INTRANET."/".$result['data'][0]['directorio']);
                    } else {
                        header("location:".URL_INMOBILIARIA."/?accion=publicaciones");
                    }
                    return $result;
                    
                } else {
                    $result['suceed'] = false;
                    $result['error'] = "Contraseña inválida";
                    return $result;
                }
            } else {
                $result['suceed'] = false;
                $result['error'] = "Usuario no registrado.";
                return $result;
            }
        } else {
            $result['suceed'] = false;
            $result['error'] = "Nombre de usuario y/o contraseña requerida.";
            return $result;
        }
    }

    public static function esUsuarioLogueado($user='intranet') {
        session_start();
        if (!isset($_SESSION['status']) || $_SESSION['status'] != 'logueado' || !isset($_SESSION['usuario']))  {
            header("location:".ROOT.$user.".php");
            die();
        }
    }
    
    public static  function logout($user='intranet') {
        //session_start();
        if (isset($_SESSION['status'])) {
            unset($_SESSION['status']);
            unset($_SESSION['usuario']);
            session_unset();
            session_destroy();
            if (isset($_COOKIE[session_name()]))
                setcookie(session_name(), '', time() - 1000);
            header("location:".ROOT.$user.".php");
        }
    }
}