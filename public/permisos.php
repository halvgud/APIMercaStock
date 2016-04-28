<?php
class Permisos{
    protected function __construct() {

    }

    public static function Obtener($request,$response,$args){
        $usuario = json_decode($request->getBody());
        $correo = $usuario->usuario;
        $contrasena = $usuario->contrasena;
        try {
            $comando = "select idNivelAutorizacion,descripcion from ms_nivelAutorizacion";
            $db = getConnection();
            $sentencia = $db->prepare($comando);
            $sentencia->execute();
            $roles = $sentencia->fetchObject();
            foreach ($roles as $rol) {
                //var_dump($rol);
                $permisoDelRol = Roles::obtenerPermisosDelRol($rol);
                $comando= "select idOpcion,descripcion,valor as activo from ms_opcion";
                $sentencia=$db->prepare($comando);
                $sentencia->execute();
                $nuevoArreglo=$sentencia->fetchObject();
                $permisos =new stdClass();
                $permisos =$nuevoArreglo;
                foreach ($permisos as &$permiso) {
                    var_dump($permiso);
                    if (roles::tienePermiso($permiso,$permisoDelRol)) {
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



}