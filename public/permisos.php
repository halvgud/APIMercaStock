<?php
class Permisos{
    protected function __construct() {

    }
    public static function Obtener($request,$response,$args){
        $usuario = json_decode($request->getBody());
        $correo = $usuario->usuario;
        $contrasena = $usuario->contrasena;
        try {
            $comando = "SELECT idNivelAutorizacion,descripcion FROM ms_nivelAutorizacion";
            $db = getConnection();
            $sentencia = $db->prepare($comando);
            $sentencia->execute();
            $roles = $sentencia->fetchObject();
            foreach ($roles as $rol) {
                //var_dump($rol);
                $permisoDelRol = Roles::obtenerPermisosDelRol($rol);
                $comando = "SELECT idOpcion,descripcion,valor AS activo FROM ms_opcion";
                $sentencia = $db->prepare($comando);
                $sentencia->execute();
                $nuevoArreglo = $sentencia->fetchObject();
                $permisos = new stdClass();
                $permisos = $nuevoArreglo;
                foreach ($permisos as &$permiso) {
                    var_dump($permiso);
                    if (roles::tienePermiso($permiso, $permisoDelRol)) {
                        //$permiso['activo'] = "true";
                        var_dump($permiso);
                    } else {
                        //  $permiso['activo'] = "false";
                    }
                }
                $rol['permisos'] = $permisos;
            }
            return ($roles);

            $autenticar=array();
            if($resultado){
                $autenticar= [
                    "estado" => 200,
                    "mensaje"=>"OK",
                    "datos" => $resultado
                ];
            }else{
            }
            if ($autenticar['estado']=='200') {
                $datos = $autenticar['datos'];
                $_SESSION['idUsuario']= $datos->idUsuario;
                $_SESSION['usuario'] =$datos->Usuario;
                $_SESSION['idNivelAutorizacion'] =$datos->idNivelAutorizacion;
                $resArray['success'] = 'Se ha logueado correctamente';
                $codigo=200;
            }else{
                $codigo=401;
            }
            $newResponse = $response->withJson($autenticar,$codigo);
            return $newResponse;
       } catch(PDOException $e) {
    //error_log($e->getMessage(), 3, '/var/tmp/php.log');
echo '{"error":{"text":'. $e->getMessage() .'}}';
}
}



    public static function Obtener1($request,$response,$args){
        try {
            $comando = "select idNivelAutorizacion,descripcion from ms_nivelAutorizacion";
            $db = getConnection();
            $sentencia = $db->prepare($comando);
            $sentencia->execute();
            $roles = $sentencia->fetchAll();
            foreach ($roles as &$rol) {
                $permisoDelRol = Roles::obtenerPermisosDelRol1($rol["idNivelAutorizacion"]);
                $comando = "select idopcion,descripcion from ms_opcion";
                $sentencia = $db->prepare($comando);
                $sentencia->execute();
                $permisos = $sentencia->fetchAll();
                foreach ($permisos as &$permiso) {
                    if ($permisoDelRol->tienePermiso($permiso['descripcion'])) {
                        $permiso['activo'] = true;
                    } else {
                        $permiso['activo'] = false;
                    }
                }
                $rol['permisos'] = $permisos;

            }
            return ($roles);
       } catch(PDOException $e) {
            //error_log($e->getMessage(), 3, '/var/tmp/php.log');
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    public static function Actualizar($request,$response,$args){
        $bandera =false;
        $db = getConnection();
        $data = json_decode($request->getBody());
        if ($db->beginTransaction()) {
            $rol = $data->rol;
            foreach($rol->permisos as &$permiso) {
                $estado = $permiso->activo?'A':'I';
                if ($db->Actualizar('relacion_rol_permiso', 'estado="'.$estado.'"', 'id_rol=' . $rol->id_rol . ' and id_permiso=' . $permiso->id_permiso)) {
                    $bandera=true;
                } else {
                    $bandera=false;
                    break;
                }
            }
        } else {
            mensajeError(1,$db->obtenerResultado());
        }
    }

}

function Actualizar($id) {
    $request = Slim::getInstance()->request();
    $body = $request->getBody();
    $wine = json_decode($body);
    $sql = "UPDATE ms_permiso SET idNivelAutorizacion=:idNivelAutorizacion, idOpcion=:idOpcion, idEstado=:idEstado WHERE idPermiso=:idPermiso";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("idNivelAutorizacion", $wine->name);
        $stmt->bindParam("idOpcion", $wine->grapes);
        $stmt->bindParam("idEstado", $wine->country);
        $stmt->bindParam("idPermiso", $wine->idPermiso);
        $stmt->execute();
        $db = null;
        echo json_encode($wine);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}
