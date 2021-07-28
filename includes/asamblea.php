<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of asamblea
 *
 * @author Valoriza 2
 */
class asamblea extends db implements crud  {
    
    const tabla = "asambleas";
    public function actualizar($id, $data) {
        return db::update(self::tabla, $data, array("id" => $id));
    }

    public function actualizarAsamblea($id_asamblea,$data) {
        return db::update(self::tabla,$data,array("id_asamblea"=>$id_asamblea));
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

    public function listar() {
        return db::select("*",self::tabla,array("id"=>$id));
    }

    public function ver($id) {
        return db::delete(self::tabla);
    }
    
    public function listarAsambleasPorPropietarios($cedula) {
        $consulta = "select a.id, a.fecha, a.archivo_soporte, a.archivo_acta, i.nombre_inmueble "
                . "from asambleas a join inmueble i on i.id=a.id_inmueble where a.id_inmueble in ("
                . "select id_inmueble from propiedades where cedula="
                . $cedula .")";
        return db::query($consulta);
    }
    
    public function listarAsambleasPorPropietariosCalendario($cedula) {
        $consulta = "select id, fecha as `start`, date_add(fecha, INTERVAL 2 HOUR) as `end`, 'Asamblea de Propietarios' as title, false as `allday`, "
                . "concat('asambleas/soportes/',archivo_soporte) as url, archivo_acta, 'fa-clock-o' as icon from asambleas where id_inmueble in ("
                . "select id_inmueble from propiedades where cedula="
                . $cedula .")";
        return db::query($consulta);
    }
}
