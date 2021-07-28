<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of notificacion
 *
 * @author Valoriza 2
 */
class notificacion extends db implements crud {
    const tabla = "notificacion";
    public function actualizar($id, $data) {
        return db::update(self::tabla, $data, array("id" => $id));
    }

    public function borrar($id) {
        return db::delete(self::tabla, array("id" => $id));
    }

    public function borrarTodo() {
        return db::insert(self::tabla, $data);
    }

    public function insertar($data) {
        return db::insert(self::tabla, $data);
    }
    
    public function insertarNotificacionPropietario($data) {
        return db::insert("notificacion_propietario", $data);
    }
    
    public function listar() {
        return db::select("*",self::tabla,array("id"=>$id));
    }

    public function ver($id) {
        return db::delete(self::tabla);
    }
    
    public function obtenerNumeroNuevasNotificacionesPropietario($cedula) {
        $sql = "SELECT count(n.id) as total FROM propiedades p join notificacion n on n.id_inmueble = p.id_inmueble where p.cedula=$cedula and n.id not in (select id_notificacion from notificacion_propietario where cedula=$cedula)";
        $result =db::dame_query($sql);
        return $result['row']['total'];
    }
    
    public function obtenerNumeroNotificacionesPropietario($cedula) {
        $sql = "SELECT count(n.id) as total FROM propiedades p join notificacion n on n.id_inmueble = p.id_inmueble where p.cedula=$cedula ";
        $result =db::dame_query($sql);
        return $result['row']['total'];
    }
    
    public function listarNotificacionesPropietario($cedula) {
        $sql = "select n.*, (select count(id_notificacion) from notificacion_propietario where cedula=$cedula and notificacion_propietario.id_notificacion = n.id) as leido
        from propiedades p join notificacion n on n.id_inmueble = p.id_inmueble
        where p.cedula=$cedula order by fecha_registro desc limit 0, 10";
        return db::dame_query($sql);
    }

}