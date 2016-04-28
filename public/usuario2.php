<?php
class usuario2{
     protected function __construct() {
   
    }
public static function crear()//$datosUsuario
        {
            $post = json_decode(file_get_contents('php://input'),true);
            
            //$idusuario=$post['idUsuario'];
            $claveApi2=$post['claveApi2'];
            $usuario=$post['usuario'];
            $contrasena =$post['contrasena'];
            $contrasenaEncriptada = self::encriptarContrasena($contrasena);
            $nombre = $post['nombre'];
            $apellido = $post['apellido'];
            $sexo=$post['sexo'];
            $contacto=$post['contacto'];
            $idsucursal=$post['idSucursal'];
            //$claveApi = self::generarClaveApi();
            $claveApi=$post['claveApi'];
            $idNivelAutorizacion=$post['idNivelAutorizacion'];
            $idEstado = $post['idEstado'];
            $fechaEstado = $post['fechaEstado'];
            $fechaSesion = $post['fechaSesion'];
            //
            //$idusuario=$datosUsuario->idUsuario;
            //$usuario=$datosUsuario->usuario;
            //$contrasena = $datosUsuario->contrasena;
            //$contrasenaEncriptada = self::encriptarContrasena($contrasena);
            //$nombre = $datosUsuario->nombre;
            //$apellido = $datosUsuario->apellido;
            //$sexo=$datosUsuario->sexo;
            //$contacto=$datosUsuario->contacto;
            //$idsucursal=$datosUsuario->idSucursal;
            ////$claveApi = self::generarClaveApi();
            //$claveApi=$datosUsuario->claveApi;
            //$idNivelAutorizacion=$datosUsuario->idNivelAutorizacion;
            //$idEstado = $datosUsuario->idEstado;
            //$fechaEstado = $datosUsuario->fechaEstado;
            //$fechaSesion = $datosUsuario->fechaSesion;
            
            
            //return var_dump($datosUsuario);
            try {
    
                $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
    
                // Sentencia INSERT
                $comando = "INSERT INTO " . self::NOMBRE_TABLA . " ( " .
                    //self::ID_USUARIO . ",
                self::USUARIO . ",".
                    self::CONTRASENA . "," .
                    self::NOMBRE . "," .
                    self::APELLIDO . "," .
                    self::SEXO . "," .
                    self::CONTACTO . "," .
                    self::ID_SUCURSAL . "," .
                    self::CLAVE_API . ",".
                    self::ID_NIVEL_AUTORIZACION.",".
                    self::ID_ESTADO.",".
                    self::FECHA_ESTADO .",".
                    self::FECHA_SESION
                    .")" .
                    " VALUES(?,?,?,?,?,?,?,?,?,?,now(),now())";
    
                $sentencia = $pdo->prepare($comando);
    
                //$sentencia->bindParam(1, $idusuario);
                $sentencia->bindParam(1, $usuario);
                $sentencia->bindParam(2, $contrasenaEncriptada);
                $sentencia->bindParam(3, $nombre);
                $sentencia->bindParam(4, $apellido);
                $sentencia->bindParam(5, $sexo);
                $sentencia->bindParam(6, $contacto);
                $sentencia->bindParam(7, $idsucursal);
                $sentencia->bindParam(8, $claveApi);
                $sentencia->bindParam(9,$idNivelAutorizacion);
                $sentencia->bindParam(10,$idEstado);
                //$sentencia->bindParam(12,$fechaEstado);
              // $sentencia->bindParam(13,$fechaSesion);
                if(!self::apiregistro($claveApi2)==null){
                $resultado = $sentencia->execute();
                    if ($resultado) {
                        return self::ESTADO_CREACION_EXITOSA;
                    } else {
                        return self::ESTADO_CREACION_FALLIDA;
                    }
                }
                else{
                     throw new ExcepcionApi(self::ESTADO_FALLA_DESCONOCIDA,
                        "Clave Api invalida",401);
                }
                
            } catch (PDOException $e) {
                throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
            }
        }}  
