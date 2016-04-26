<?php

class Roles
{
    protected $permissions;

    protected function __construct() {
        $this->permissions = array();
    }

    // return a role object with associated permissions
    public static function obtenerPermisosDelRol($role_id) {
        $role = new Roles();
        $db = getConnection();
       $comando = "SELECT mo.valor from ms_permiso mp inner join ms_opcion mo on (mo.idOpcion = mp.idOpcion) where mp.idNivelAutorizacion=:idNivelAutorizacion and mp.idEstado='A' order by mo.idOpcion";
        	$sentencia = $db->prepare($comando);
		$sentencia->bindParam("idNivelAutorizacion", $role_id);
		$sentencia->execute();
                 $permisos = $sentencia->fetchAll(PDO::FETCH_OBJ);
                 //var_dump($permisos);
       // $permisos =  $db->obtenerResultado();
        foreach($permisos as &$permiso) {
            $role->permissions[$permiso->valor] = true;
        }
        return $role;
    }

    // check if a permission is set
    public function tienePermiso($permission) {
        return isset($this->permissions[$permission]);
    }
}