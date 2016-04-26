<?php
class PrivilegiosUsuario
{
    private $roles;

    public function __construct() {

    }

    // Metodo para obtener permisos del usuario
    public static function obtenerPorUsuario($username) {
        if (!empty($username)) {
            $privUser = new PrivilegiosUsuario();
            $privUser->InicializarRoles($username);
            return $privUser;
        } else {
            return false;
        }
    }

    // Llenar roles con los permisos que tienen asociados
    protected function InicializarRoles($usuario) {
        $this->roles = array();
        
        $comando = "SELECT mna.idNivelAutorizacion,mna.descripcion from ms_nivelAutorizacion mna inner join ms_usuario mu on (mu.idUsuario =:idUsuario) order by mna.idNivelAutorizacion asc";
        $db = getConnection();
		$sentencia = $db->prepare($comando);
		$sentencia->bindParam("idUsuario", $usuario);
		$sentencia->execute();
                $resultado = $sentencia->fetchAll(PDO::FETCH_OBJ);
                //var_dump($resultado);
      //  $resultado = $db->obtenerResultado();
        foreach($resultado as &$rol) {
            $this->roles[$rol->descripcion] = Roles::obtenerPermisosDelRol($rol->idNivelAutorizacion);
        }
        return $this->roles;
    }

    // check if user has a specific privilege
    public function tienePrivilegio($perm) {
        foreach ($this->roles as $role) {
            if ($role->tienePermiso($perm)) {
                return true;
            }
        }
        return false;
    }
}