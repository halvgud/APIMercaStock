<?php

class Roles
{
    protected $permisos;
    protected $permissions;
    protected function __construct() {
        $this->permisos = array();
        $this->permissions=array();
    }

    public static function obtenerPermisosDelRol1($role_id) {
        $role = new Roles();
        $db = getConnection();
        //$db->abrirConexion();
       // $db->seleccion('relacion_rol_permiso','p.descripcion',' r inner join permisos p on r.id_permiso = p.id_permiso','r.id_rol="'.$role_id.'" and r.estado="A"','p.id_permiso asc',null);
      //  $permisos =  $db->obtenerResultado();
        $comando = "SELECT mo.descripcion from ms_permiso mp inner join ms_opcion mo on (mo.idOpcion = mp.idOpcion) where mp.idNivelAutorizacion=:idNivelAutorizacion and mp.idEstado='A' order by mo.idOpcion";
        $sentencia = $db->prepare($comando);
        $sentencia->bindParam("idNivelAutorizacion", $role_id);
        $sentencia->execute();
        $permisos= $sentencia->fetchAll(PDO::FETCH_ASSOC);
        foreach($permisos as &$permiso) {
            $role->permissions[$permiso['descripcion']] = true;
        }

        return $role;
    }

    // return a role object with associated permissions
    public static function obtenerPermisosDelRol($role_id) {
        $role = new Roles();
        $db = getConnection();
       $comando = "SELECT mo.valor from ms_permiso mp inner join ms_opcion mo on (mo.idOpcion = mp.idOpcion) where mp.idNivelAutorizacion=:idNivelAutorizacion and mp.idEstado='A' order by mo.idOpcion";
         //$comando = "SELECT mp.valor from ms_opcion mp inner join ms_permiso mo on (mo.idOpcion = mp.idOpcion) where mo.idNivelAutorizacion=:idNivelAutorizacion and mp.idEstado='A' order by mo.idOpcion";
        $sentencia = $db->prepare($comando);
		$sentencia->bindParam("idNivelAutorizacion", $role_id);
		$sentencia->execute();
                 $permisos = $sentencia->fetchAll(PDO::FETCH_OBJ);
                 //var_dump($permisos);
       // $permisos =  $db->obtenerResultado();
        foreach($permisos as $permiso) {
            $role->permisos[$permiso->valor] = true;
        }
        return $role->permisos;
    }

    // check if a permission is set
    public function tienePermiso($permisos) {
        return isset($this->permissions[$permisos]);
        //return true;
    }
}