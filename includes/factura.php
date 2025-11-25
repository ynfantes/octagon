<?php
/**
 * Clase que mantiene la tabla factura
 *
 * @autor   Edgar Messia
 * @static  
 * @package     Valoriza2.Framework
 * @subpackage	FileSystem
 * @since	1.0
 */
class factura extends db implements crud {
    
    const tabla = "facturas";
    
    public function actualizar($id, $data){
        return db::update(self::tabla, $data, array("id" => $id));
    }

    public function borrar($id){
        return db::delete(self::tabla, array("id" => $id));
    }

    /**
     * Inserta un regristro en la tabla factura
     *
     * @param	Array	$data	Arreglo con la data
     * 
     * @return	Array	Retorna arreglo con parámetos del resultado
     * @since	1.0
     */
    public function insertar($data){
        return db::insert(self::tabla, $data);
    }
    public function insertarActualizar($data){
        $update = $data;
        unset($update['id_inmueble'],$update['apto'],$update['periodo']);
        return db::insertUpdate(self::tabla, $data,$update);
    }
    public function insertar_detalle_factura($data) {
        return db::insert("factura_detalle",$data);
    }
    
    public function listar(){
       return db::select("*", self::tabla);
    }
    
    public function ver($id){
        return db::select("*",self::tabla,array("id"=>$id));
    }

    public function borrarTodo() {
        return db::delete(self::tabla);
    }
    public function borrarFactura($data) {
        return db::delete(self::tabla,$data);
    }
    public static function estadoDeCuenta($inmueble, $apto) {
        $consulta = "select * from ".self::tabla.
                " where id_inmueble='".$inmueble."' and apto='".$apto.
                "' order by periodo ASC";
        return db::query($consulta);
    }
    
    public function facturaPerteneceACliente($factura,$cedula) {
        
        $query = "select propiedades.* from propiedades join 
                facturas on facturas.apto = propiedades.apto and 
                facturas.id_inmueble = propiedades.id_inmueble 
                where facturas.numero_factura='".$factura."'";
        $result = $this->dame_query($query);
        if ($result['suceed']==true) {
            return $result['data'][0]['cedula']==$cedula;
        } else {
            return false;
        }
    }
    
    public function avisoExisteEnBaseDeDatos($aviso) {
        $aviso = str_replace(".pdf","",$aviso);
        $query = "
            SELECT numero_factura 
            FROM facturas 
            WHERE numero_factura='{$aviso}'
        ";
        // En caso de que exista la tabla historico_avisos_cobro
        // UNION 
        // SELECT numero_factura 
        // FROM historico_avisos_cobro 
        // WHERE numero_factura='{$aviso}'
        // Fin de la adición
        $result = $this->dame_query($query);
        
        return ($result['suceed'] && isset($result['data']) && count($result['data']) > 0) ? 1 : 0;
    }
    
    public static function numeroRecibosPendientesPropitario($cedula) {
        $sql = "SELECT count(f.numero_factura) as cantidad 
            FROM propiedades as p 
            JOIN facturas as f on f.id_inmueble = p.id_inmueble and f.apto = p.apto 
            WHERE p.cedula=".$cedula;
        $result = db::query($sql);
        return $result;
    }

    public function listarFacturasConNumFact() {
        
        $sql = 'SELECT numero_factura, id_inmueble, apto  
            FROM facturas 
            WHERE numero_factura is not null and numero_factura <> ""
            ORDER BY id_inmueble, apto, periodo';

        return db::query($sql);
    }

    public function listarDiferenciasRecibosPendientesPorPropietario() {
        
        $sql = "select p.codinm, p.apto, p.recibos, f.f_recibos as meses_pendiente , p.clave
            from propietarios p 
            inner join (
                SELECT COUNT(apto) as f_recibos, id_inmueble, apto 
                FROM `facturas` 
                GROUP BY id_inmueble, apto
            ) f on p.codinm=f.id_inmueble 
            and p.apto = f.apto 
            where p.recibos <> f.f_recibos
            order by p.codinm, p.apto";
        
        return db::query($sql);
    }
}