<?php
class PrivilegiosUsuario
{
    private static $roles;

    public function __construct() {

    }

    // Metodo para obtener permisos del usuario
    public static function obtenerPorUsuario($username) {
        if (!empty($username)) {
            self::InicializarRoles($username);
           return (self::$roles);

        } else {
            return false;
        }
    }

    // Llenar roles con los permisos que tienen asociados
    protected static function InicializarRoles($usuario) {
        self::$roles = array();
        
        $comando = "SELECT mna.idNivelAutorizacion,mna.descripcion from ms_nivelAutorizacion mna inner join ms_usuario mu on (mu.idUsuario =:idUsuario and mu.idNivelAutorizacion = mna.idNivelAutorizacion) order by mna.idNivelAutorizacion asc";
        $db = getConnection();
		$sentencia = $db->prepare($comando);
		$sentencia->bindParam("idUsuario", $usuario);
		$sentencia->execute();
                $resultado = $sentencia->fetchAll(PDO::FETCH_OBJ);
        foreach($resultado as $rol) {
            self::$roles[$rol->descripcion] = Roles::obtenerPermisosDelRol($rol->idNivelAutorizacion);
        }

        return self::$roles;

    }

    // check if user has a specific privilege
    public function tienePrivilegio($perm) {
        foreach ($this->roles as $role) {
            if (roles::tienePermiso($perm,$role)) {
                return true;
            }
        }
        return false;
    }
}