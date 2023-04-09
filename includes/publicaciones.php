<?php

/**
 * Description of publicaciones
 *
 * @author Edgar
 */
class publicaciones extends db implements crud {
    const tabla = "inmobiliaria_publicacion";
    
    public function actualizar($id, $data) {
        return db::update(self::tabla, $data, ["id" => $id]);
    }

    public function borrar($id) {
        return db::delete(self::tabla, ["id" => $id]);
    }

    public function borrarTodo() {
        return db::delete(self::tabla);
    }

    public function insertar($data) {
        return db::insert(self::tabla, $data);
    }

    public function listar() {
        return db::select("*", self::tabla);
    }

    public function ver($id) {
        // $sql = "select pu.*, c.ciudad, e.estado, t.descripcion as tipo, o.descripcion as operacion, m.simbolo, m.descripcion as moneda  
        //     from inmobiliaria_publicacion pu
        //     join ciudades c on c.id_ciudad = pu.id_ciudad
        //     join estados e on e.id_estado = pu.id_estado 
        //     join inmobiliaria_tipo t on t.id = pu.id_inmobiliaria_tipo
        //     join operaciones o on o.id = pu.id_operacion 
        //     join monedas m on m.id = pu.id_moneda 
        //     where pu.id = $id";
        
        //  return db::query($sql);
        return $this->obtenerPublicaciones(['id' => $id]);

    }
    public function totalPublicaciones() {
        $sql = "select count(*) as total from inmobiliaria_publicacion where inactivo = 0";
        $r = db::query($sql);
        return $r['row']['total'];
        
    }
    public function obtenerPublicaciones($data=[]) {
        $sql = "select pu.* 
            ,c.ciudad
            ,e.estado
            ,t.descripcion as tipo
            ,o.descripcion as operacion
            ,m.simbolo 
            from inmobiliaria_publicacion pu
            join estados e on e.id_estado = pu.id_estado
            join ciudades c on c.id_ciudad = pu.id_ciudad
            join inmobiliaria_tipo t on t.id = pu.id_inmobiliaria_tipo
            join operaciones o on o.id = pu.id_operacion 
            join monedas m on m.id = pu.id_moneda
            where inactivo = 0";
        
        if (isset($data['id_estado'])) {
            $sql.= " and pu.id_estado =".$data['id_estado'];
        }
        if (isset($data['id_ciudad'])) {
            $sql.= " and pu.id_ciudad =".$data['id_ciudad'];
        }
        if (isset($data['id_operacion'])) {
            $sql.= " and pu.id_operacion =".$data['id_operacion'];
        }
        if (isset($data['id_inmobiliaria_tipo']) && $data['id_inmobiliaria_tipo']>1) {
            $sql.= " and pu.id_inmobiliaria_tipo =".$data['id_inmobiliaria_tipo'];
        }
        if (isset($data['habitaciones']) && $data['habitaciones']>1) {
            $sql.= " and pu.habitaciones =".$data['habitaciones'];
        }
        if(isset($data['id']) && $data['id']>0) {
            $sql.= " and pu.id =".$data['id'];
        }
        if (isset($data['sort'])) {
            $sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
        } else {
            $sql .= " ORDER BY id";
        }
        if (isset($data['order'])) {
            $sql .= " ".$data['order'];
        }
        if(isset($data['start']) && isset($data['limit'])) {

            $start = isset($data['start']) ? $data['start'] : 0;
            $limit = isset($data['limit']) ? $data['limit'] : 5;
            $sql .= " LIMIT $start,$limit";
        }

        //echo $sql;
        return db::query($sql);
        
    } 
    
    public function publicacionMasVistas($limit) {
        $sql = "select pu.*, c.descripcion as ciudad, m.descripcion as municipio, t.descripcion as Tipo, o.descripcion as operacion  
            from inmobiliaria_publicacion pu
            join ciudades c on c.id = pu.id_ciudad
            join municipios m on m.id = pu.id_municipio 
            join inmobiliaria_tipo t on t.id = pu.id_inmobiliaria_tipo
            join operaciones o on o.id = pu.id_operacion where inactivo = 0 
            order by pu.visto DESC LIMIT 0, $limit";
        
        return db::query($sql);
    }

    public function obtenerImagenesPorPublicacion($id) {
        $consulta = "select * from inmobiliaria_img where id_inmobiliaria_publicacion=$id";
        return db::query($consulta);
    }
}
